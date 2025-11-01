<?php
/**
 * MySQL Query Service
 *
 * Handles execution of custom MySQL queries safely.
 *
 * @package ATablesCharts\Database
 * @since 1.0.0
 */

namespace ATablesCharts\Database;

use A_Tables_Charts\Database\DatabaseHelpers;

/**
 * MySQLQueryService Class
 *
 * Responsibilities:
 * - Execute custom MySQL queries safely
 * - Validate queries for security
 * - Convert results to table format
 */
class MySQLQueryService {

	/**
	 * Execute a MySQL query and return results
	 *
	 * SECURITY: This method implements multiple layers of protection:
	 * - Strict SELECT-only enforcement
	 * - Table whitelist validation
	 * - Query complexity limits
	 * - Pattern-based SQL injection prevention
	 * - Execution time limits
	 * - Result size limits
	 *
	 * @param string $query SQL query to execute.
	 * @return array Result with headers and data.
	 */
	public function execute_query( $query ) {
		global $wpdb;

		// Validate query
		$validation = $this->validate_query( $query );
		if ( ! $validation['valid'] ) {
			// Log security violation
			if ( class_exists( 'ATablesCharts\Shared\Utils\Logger' ) ) {
				$logger = new \ATablesCharts\Shared\Utils\Logger();
				$logger->warning( 'MySQL query validation failed', array(
					'errors' => $validation['errors'],
					'user_id' => get_current_user_id(),
				) );
			}

			return array(
				'success' => false,
				'message' => $validation['errors'][0],
				'errors'  => $validation['errors'],
			);
		}

		// Execute query with safety limits
		try {
			// Set maximum execution time to prevent resource exhaustion
			@set_time_limit( 30 );

			// Use wpdb to execute the query
			// Note: We've already validated this is a SELECT-only query
			$results = $wpdb->get_results( $query, ARRAY_A );

			// Check for errors
			if ( $wpdb->last_error ) {
				// Log database error
				if ( class_exists( 'ATablesCharts\Shared\Utils\Logger' ) ) {
					$logger = new \ATablesCharts\Shared\Utils\Logger();
					$logger->error( 'MySQL query execution error', array(
						'error' => $wpdb->last_error,
						'user_id' => get_current_user_id(),
					) );
				}

				return array(
					'success' => false,
					'message' => __( 'Database error: ', 'a-tables-charts' ) . esc_html( $wpdb->last_error ),
				);
			}

			// Check if results are empty
			if ( empty( $results ) ) {
				return array(
					'success' => false,
					'message' => __( 'Query returned no results.', 'a-tables-charts' ),
				);
			}

			// Enforce maximum result size to prevent memory exhaustion
			$max_rows = apply_filters( 'atables_mysql_query_max_rows', 10000 );
			if ( count( $results ) > $max_rows ) {
				return array(
					'success' => false,
					'message' => sprintf(
						/* translators: %d: maximum number of rows */
						__( 'Query returned too many rows. Maximum allowed: %d. Please add a LIMIT clause.', 'a-tables-charts' ),
						$max_rows
					),
				);
			}

			// Extract headers from first row
			$headers = array_keys( $results[0] );

			// Validate column count
			$max_columns = apply_filters( 'atables_mysql_query_max_columns', 100 );
			if ( count( $headers ) > $max_columns ) {
				return array(
					'success' => false,
					'message' => sprintf(
						/* translators: %d: maximum number of columns */
						__( 'Query returned too many columns. Maximum allowed: %d', 'a-tables-charts' ),
						$max_columns
					),
				);
			}

			// Convert to table data format
			$data = array();
			foreach ( $results as $row ) {
				$data[] = array_values( $row );
			}

			// Log successful query execution
			if ( class_exists( 'ATablesCharts\Shared\Utils\Logger' ) ) {
				$logger = new \ATablesCharts\Shared\Utils\Logger();
				$logger->info( 'MySQL query executed successfully', array(
					'rows' => count( $data ),
					'columns' => count( $headers ),
					'user_id' => get_current_user_id(),
				) );
			}

			return array(
				'success' => true,
				'headers' => $headers,
				'data'    => $data,
				'rows'    => count( $data ),
				'columns' => count( $headers ),
			);

		} catch ( \Exception $e ) {
			// Log exception
			if ( class_exists( 'ATablesCharts\Shared\Utils\Logger' ) ) {
				$logger = new \ATablesCharts\Shared\Utils\Logger();
				$logger->error( 'MySQL query exception', array(
					'error' => $e->getMessage(),
					'user_id' => get_current_user_id(),
				) );
			}

			return array(
				'success' => false,
				'message' => __( 'Error executing query: ', 'a-tables-charts' ) . esc_html( $e->getMessage() ),
			);
		}
	}

	/**
	 * Validate MySQL query for security
	 *
	 * SECURITY: Implements comprehensive validation:
	 * - SELECT-only enforcement with strict parsing
	 * - Dangerous keyword detection (case-insensitive, comment-aware)
	 * - SQL injection pattern detection
	 * - Table whitelist enforcement
	 * - Query complexity limits (prevents expensive operations)
	 * - Comment stripping (prevents comment-based bypasses)
	 * - Nested query depth limits
	 *
	 * @param string $query SQL query to validate.
	 * @return array Validation result with 'valid' boolean and 'errors' array.
	 */
	public function validate_query( $query ) {
		$errors = array();

		// Check if query is empty
		if ( empty( trim( $query ) ) ) {
			$errors[] = __( 'Query cannot be empty.', 'a-tables-charts' );
			return array(
				'valid'  => false,
				'errors' => $errors,
			);
		}

		// Normalize whitespace and remove excess spacing
		$query_normalized = preg_replace( '/\s+/', ' ', trim( $query ) );

		// Strip SQL comments to prevent comment-based bypass attempts
		$query_no_comments = $this->strip_sql_comments( $query_normalized );

		// Convert to uppercase for checking (but preserve original for table validation)
		$query_upper = strtoupper( $query_no_comments );

		// 1. STRICT SELECT-ONLY ENFORCEMENT
		// Must start with SELECT (after trimming)
		if ( ! preg_match( '/^\s*SELECT\s+/i', $query_no_comments ) ) {
			$errors[] = __( 'Only SELECT queries are allowed for security reasons.', 'a-tables-charts' );
		}

		// Ensure no semicolon-separated multiple statements
		if ( substr_count( $query_no_comments, ';' ) > 1 ||
		     ( substr_count( $query_no_comments, ';' ) === 1 && rtrim( $query_no_comments, '; ' ) !== rtrim( $query_no_comments, ' ' ) ) ) {
			$errors[] = __( 'Multiple statements are not allowed. Only single SELECT queries permitted.', 'a-tables-charts' );
		}

		// 2. DANGEROUS KEYWORD DETECTION (comprehensive list)
		$dangerous_keywords = array(
			// Data manipulation
			'INSERT', 'UPDATE', 'DELETE', 'REPLACE', 'TRUNCATE',
			// Schema manipulation
			'DROP', 'CREATE', 'ALTER', 'RENAME',
			// Permission/security
			'GRANT', 'REVOKE', 'SET',
			// Execution
			'EXECUTE', 'EXEC', 'CALL', 'DO',
			// File operations
			'LOAD_FILE', 'LOAD DATA', 'OUTFILE', 'DUMPFILE', 'INTO OUTFILE', 'INTO DUMPFILE',
			// Information disclosure
			'INFORMATION_SCHEMA', 'MYSQL.USER', 'SHOW GRANTS',
			// Other dangerous operations
			'BENCHMARK', 'SLEEP', 'WAITFOR',
			// Prepared statements (can be used maliciously)
			'PREPARE', 'DEALLOCATE',
			// Transaction control that could be abused
			'START TRANSACTION', 'COMMIT', 'ROLLBACK',
		);

		foreach ( $dangerous_keywords as $keyword ) {
			// Use word boundary for accurate matching, check in cleaned query
			if ( preg_match( '/\b' . preg_quote( $keyword, '/' ) . '\b/i', $query_no_comments ) ) {
				$errors[] = sprintf(
					/* translators: %s: dangerous keyword */
					__( 'Query contains forbidden keyword: %s', 'a-tables-charts' ),
					$keyword
				);
			}
		}

		// 3. SQL INJECTION PATTERN DETECTION
		$injection_patterns = array(
			// Stacked queries
			'/;\s*(SELECT|INSERT|UPDATE|DELETE|DROP|CREATE|ALTER)/i' => __( 'Stacked queries detected', 'a-tables-charts' ),
			// UNION-based injection (multiple variations)
			'/UNION\s+(ALL\s+)?SELECT/i' => __( 'UNION SELECT not allowed', 'a-tables-charts' ),
			// Hex encoding attempts
			'/0x[0-9a-f]+/i' => __( 'Hexadecimal values not allowed', 'a-tables-charts' ),
			// Blind SQL injection time-based
			'/BENCHMARK\s*\(/i' => __( 'BENCHMARK function not allowed', 'a-tables-charts' ),
			'/SLEEP\s*\(/i' => __( 'SLEEP function not allowed', 'a-tables-charts' ),
			// Information schema queries (can leak database structure)
			'/INFORMATION_SCHEMA/i' => __( 'INFORMATION_SCHEMA access not allowed', 'a-tables-charts' ),
			// MySQL user table
			'/mysql\.user/i' => __( 'MySQL user table access not allowed', 'a-tables-charts' ),
			// Subquery depth (limit to reasonable nesting)
			'/(\(.*){5,}/i' => __( 'Query too complex: excessive nesting detected', 'a-tables-charts' ),
		);

		foreach ( $injection_patterns as $pattern => $error_message ) {
			if ( preg_match( $pattern, $query_no_comments ) ) {
				$errors[] = $error_message;
			}
		}

		// 4. TABLE WHITELIST VALIDATION
		$table_validation = $this->validate_query_tables( $query_no_comments );
		if ( ! $table_validation['valid'] ) {
			$errors = array_merge( $errors, $table_validation['errors'] );
		}

		// 5. QUERY COMPLEXITY LIMITS
		$complexity_check = $this->check_query_complexity( $query_no_comments );
		if ( ! $complexity_check['valid'] ) {
			$errors = array_merge( $errors, $complexity_check['errors'] );
		}

		// 6. CHARACTER SET VALIDATION (prevent encoding-based attacks)
		if ( preg_match( '/[^\x20-\x7E\r\n\t]/', $query ) ) {
			$errors[] = __( 'Query contains invalid characters. Only ASCII characters are allowed.', 'a-tables-charts' );
		}

		return array(
			'valid'  => empty( $errors ),
			'errors' => $errors,
		);
	}

	/**
	 * Strip SQL comments from query to prevent comment-based bypasses
	 *
	 * @param string $query SQL query.
	 * @return string Query with comments removed.
	 */
	private function strip_sql_comments( $query ) {
		// Remove C-style comments /* */
		$query = preg_replace( '/\/\*.*?\*\//s', ' ', $query );
		// Remove MySQL-specific comments # and --
		$query = preg_replace( '/#.*?(\n|$)/', ' ', $query );
		$query = preg_replace( '/--.*?(\n|$)/', ' ', $query );
		// Normalize whitespace after comment removal
		$query = preg_replace( '/\s+/', ' ', $query );
		return trim( $query );
	}

	/**
	 * Validate that query only accesses whitelisted tables
	 *
	 * @param string $query SQL query.
	 * @return array Validation result.
	 */
	private function validate_query_tables( $query ) {
		global $wpdb;
		$errors = array();

		// Get all WordPress core tables as whitelist
		$allowed_tables = array(
			$wpdb->posts,
			$wpdb->postmeta,
			$wpdb->users,
			$wpdb->usermeta,
			$wpdb->comments,
			$wpdb->commentmeta,
			$wpdb->terms,
			$wpdb->term_taxonomy,
			$wpdb->term_relationships,
			$wpdb->options,
			$wpdb->links,
		);

		// Add plugin tables
		$allowed_tables[] = $wpdb->prefix . 'atables_tables';
		$allowed_tables[] = $wpdb->prefix . 'atables_rows';

		// Allow developers to extend the whitelist
		$allowed_tables = apply_filters( 'atables_mysql_query_allowed_tables', $allowed_tables );

		// Extract table names from FROM and JOIN clauses
		// Pattern matches: FROM tablename, JOIN tablename
		preg_match_all( '/(?:FROM|JOIN)\s+`?(\w+)`?/i', $query, $matches );

		if ( ! empty( $matches[1] ) ) {
			foreach ( $matches[1] as $table ) {
				// Check if table has wp_ prefix, if not add it
				$full_table = $table;
				if ( strpos( $table, $wpdb->prefix ) !== 0 ) {
					$full_table = $wpdb->prefix . $table;
				}

				// Check against whitelist
				if ( ! in_array( $full_table, $allowed_tables, true ) && ! in_array( $table, $allowed_tables, true ) ) {
					$errors[] = sprintf(
						/* translators: %s: table name */
						__( 'Table "%s" is not in the allowed whitelist. Contact administrator to enable access.', 'a-tables-charts' ),
						esc_html( $table )
					);
				}
			}
		}

		return array(
			'valid'  => empty( $errors ),
			'errors' => $errors,
		);
	}

	/**
	 * Check query complexity to prevent resource-intensive operations
	 *
	 * @param string $query SQL query.
	 * @return array Validation result.
	 */
	private function check_query_complexity( $query ) {
		$errors = array();

		// Count JOINs (limit to prevent expensive operations)
		$join_count = preg_match_all( '/\sJOIN\s/i', $query );
		$max_joins = apply_filters( 'atables_mysql_query_max_joins', 3 );
		if ( $join_count > $max_joins ) {
			$errors[] = sprintf(
				/* translators: 1: number of joins, 2: maximum allowed */
				__( 'Query has too many JOINs (%1$d). Maximum allowed: %2$d', 'a-tables-charts' ),
				$join_count,
				$max_joins
			);
		}

		// Count subqueries (limit nesting depth)
		$subquery_depth = 0;
		$temp_query = $query;
		while ( preg_match( '/\(\s*SELECT/i', $temp_query ) ) {
			$subquery_depth++;
			$temp_query = preg_replace( '/\(\s*SELECT.*?\)/i', '', $temp_query, 1 );
		}
		$max_subquery_depth = apply_filters( 'atables_mysql_query_max_subquery_depth', 2 );
		if ( $subquery_depth > $max_subquery_depth ) {
			$errors[] = sprintf(
				/* translators: 1: subquery depth, 2: maximum allowed */
				__( 'Query has too many nested subqueries (%1$d). Maximum allowed: %2$d', 'a-tables-charts' ),
				$subquery_depth,
				$max_subquery_depth
			);
		}

		// Check for LIMIT clause (recommended for large result sets)
		if ( ! preg_match( '/\sLIMIT\s+\d+/i', $query ) ) {
			// This is a warning, not an error - query can still proceed
			// Uncomment to make LIMIT mandatory:
			// $errors[] = __( 'Query should include a LIMIT clause to prevent excessive memory usage.', 'a-tables-charts' );
		}

		// Prevent SELECT * from very large tables without LIMIT
		if ( preg_match( '/SELECT\s+\*/i', $query ) && ! preg_match( '/\sLIMIT\s/i', $query ) ) {
			$errors[] = __( 'SELECT * queries must include a LIMIT clause to prevent memory exhaustion.', 'a-tables-charts' );
		}

		return array(
			'valid'  => empty( $errors ),
			'errors' => $errors,
		);
	}

	/**
	 * Test query connection and return sample results
	 *
	 * @param string $query SQL query to test.
	 * @param int    $limit Number of rows to return (default 5).
	 * @return array Test result.
	 */
	public function test_query( $query, $limit = 5 ) {
		// Add LIMIT if not present
		if ( stripos( $query, 'LIMIT' ) === false ) {
			$query = rtrim( $query, '; ' ) . ' LIMIT ' . (int) $limit;
		}

		return $this->execute_query( $query );
	}

	/**
	 * Get list of available tables in database
	 *
	 * @return array List of table names.
	 */
	public function get_available_tables() {
		global $wpdb;

		$tables = $wpdb->get_results( 'SHOW TABLES', ARRAY_N );
		$table_list = array();

		foreach ( $tables as $table ) {
			$table_list[] = $table[0];
		}

		return $table_list;
	}

	/**
	 * Get columns for a specific table
	 *
	 * @param string $table_name Table name.
	 * @return array List of columns with details.
	 */
	public function get_table_columns( $table_name ) {
		global $wpdb;

		// Sanitize table name
		$table_name = preg_replace( '/[^a-zA-Z0-9_]/', '', $table_name );

		$columns = $wpdb->get_results(
			"SHOW COLUMNS FROM `{$table_name}`",
			ARRAY_A
		);

		return $columns;
	}

	/**
	 * Build a simple SELECT query
	 *
	 * @param string $table   Table name.
	 * @param array  $columns Columns to select.
	 * @param string $where   WHERE clause (optional).
	 * @param string $order   ORDER BY clause (optional) - format: "column_name" or "column_name DESC".
	 * @param int    $limit   LIMIT (optional).
	 * @return string Generated SQL query.
	 */
	public function build_query( $table, $columns = array(), $where = '', $order = '', $limit = 0 ) {
		// Sanitize table name
		$table = preg_replace( '/[^a-zA-Z0-9_]/', '', $table );

		// Build SELECT clause
		if ( empty( $columns ) ) {
			$select = '*';
		} else {
			$select = '`' . implode( '`, `', array_map( 'sanitize_text_field', $columns ) ) . '`';
		}

		$query = "SELECT {$select} FROM `{$table}`";

		// Add WHERE clause
		if ( ! empty( $where ) ) {
			$query .= " WHERE {$where}";
		}

		// Add ORDER BY clause with validation to prevent SQL injection
		if ( ! empty( $order ) ) {
			// Parse order parameter (expected format: "column" or "column ASC/DESC")
			$order_parts = preg_split( '/\s+/', trim( $order ), 2 );
			$order_column = sanitize_key( $order_parts[0] );
			$order_direction = isset( $order_parts[1] ) ? strtoupper( trim( $order_parts[1] ) ) : 'ASC';

			// Validate direction
			$safe_direction = in_array( $order_direction, array( 'ASC', 'DESC' ), true ) ? $order_direction : 'ASC';

			// Only add ORDER BY if column name is valid (contains only alphanumeric and underscore)
			if ( ! empty( $order_column ) && preg_match( '/^[a-z0-9_]+$/i', $order_column ) ) {
				$query .= " ORDER BY `{$order_column}` {$safe_direction}";
			}
		}

		// Add LIMIT
		if ( $limit > 0 ) {
			$query .= " LIMIT " . (int) $limit;
		}

		return $query;
	}

	/**
	 * Get sample query templates
	 *
	 * @return array Array of sample queries.
	 */
	public function get_sample_queries() {
		global $wpdb;

		return array(
			array(
				'name'        => __( 'All Posts', 'a-tables-charts' ),
				'description' => __( 'Get all published posts with title and date', 'a-tables-charts' ),
				'query'       => "SELECT ID, post_title, post_date, post_author FROM {$wpdb->posts} WHERE post_status = 'publish' AND post_type = 'post' ORDER BY post_date DESC LIMIT 100",
			),
			array(
				'name'        => __( 'All Users', 'a-tables-charts' ),
				'description' => __( 'Get all users with email and registration date', 'a-tables-charts' ),
				'query'       => "SELECT ID, user_login, user_email, user_registered FROM {$wpdb->users} ORDER BY user_registered DESC",
			),
			array(
				'name'        => __( 'Post Count by Author', 'a-tables-charts' ),
				'description' => __( 'Count posts grouped by author', 'a-tables-charts' ),
				'query'       => "SELECT u.display_name as Author, COUNT(p.ID) as Posts FROM {$wpdb->users} u LEFT JOIN {$wpdb->posts} p ON u.ID = p.post_author WHERE p.post_status = 'publish' GROUP BY u.ID ORDER BY Posts DESC",
			),
			array(
				'name'        => __( 'Recent Comments', 'a-tables-charts' ),
				'description' => __( 'Get recent approved comments', 'a-tables-charts' ),
				'query'       => "SELECT comment_author, comment_content, comment_date FROM {$wpdb->comments} WHERE comment_approved = '1' ORDER BY comment_date DESC LIMIT 50",
			),
			array(
				'name'        => __( 'Custom Query', 'a-tables-charts' ),
				'description' => __( 'Write your own SELECT query', 'a-tables-charts' ),
				'query'       => "SELECT * FROM {$wpdb->posts} WHERE post_status = 'publish' LIMIT 10",
			),
		);
	}
}

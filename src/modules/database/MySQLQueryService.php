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
	 * @param string $query SQL query to execute.
	 * @return array Result with headers and data.
	 */
	public function execute_query( $query ) {
		global $wpdb;

		// Validate query
		$validation = $this->validate_query( $query );
		if ( ! $validation['valid'] ) {
			return array(
				'success' => false,
				'message' => $validation['errors'][0],
				'errors'  => $validation['errors'],
			);
		}

		// Execute query
		try {
			// Use wpdb to execute the query
			$results = $wpdb->get_results( $query, ARRAY_A );

			// Check for errors
			if ( $wpdb->last_error ) {
				return array(
					'success' => false,
					'message' => __( 'Database error: ', 'a-tables-charts' ) . $wpdb->last_error,
				);
			}

			// Check if results are empty
			if ( empty( $results ) ) {
				return array(
					'success' => false,
					'message' => __( 'Query returned no results.', 'a-tables-charts' ),
				);
			}

			// Extract headers from first row
			$headers = array_keys( $results[0] );

			// Convert to table data format
			$data = array();
			foreach ( $results as $row ) {
				$data[] = array_values( $row );
			}

			return array(
				'success' => true,
				'headers' => $headers,
				'data'    => $data,
				'rows'    => count( $data ),
				'columns' => count( $headers ),
			);

		} catch ( \Exception $e ) {
			return array(
				'success' => false,
				'message' => __( 'Error executing query: ', 'a-tables-charts' ) . $e->getMessage(),
			);
		}
	}

	/**
	 * Validate MySQL query for security
	 *
	 * @param string $query SQL query to validate.
	 * @return array Validation result.
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

		// Convert to uppercase for checking
		$query_upper = strtoupper( trim( $query ) );

		// Only allow SELECT queries
		if ( strpos( $query_upper, 'SELECT' ) !== 0 ) {
			$errors[] = __( 'Only SELECT queries are allowed for security reasons.', 'a-tables-charts' );
		}

		// Prevent dangerous keywords
		$dangerous_keywords = array(
			'DROP',
			'DELETE',
			'TRUNCATE',
			'ALTER',
			'CREATE',
			'INSERT',
			'UPDATE',
			'REPLACE',
			'GRANT',
			'REVOKE',
			'EXECUTE',
			'CALL',
			'LOAD_FILE',
			'OUTFILE',
			'DUMPFILE',
		);

		foreach ( $dangerous_keywords as $keyword ) {
			if ( preg_match( '/\b' . $keyword . '\b/i', $query ) ) {
				$errors[] = sprintf(
					/* translators: %s: dangerous keyword */
					__( 'Query contains dangerous keyword: %s', 'a-tables-charts' ),
					$keyword
				);
			}
		}

		// Check for SQL injection attempts
		$injection_patterns = array(
			'/;\s*(DROP|DELETE|TRUNCATE)/i',
			'/UNION\s+SELECT/i',
			'/--/',
			'/\/\*.*\*\//s',
		);

		foreach ( $injection_patterns as $pattern ) {
			if ( preg_match( $pattern, $query ) ) {
				$errors[] = __( 'Query contains potentially dangerous patterns.', 'a-tables-charts' );
				break;
			}
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
	 * @param string $order   ORDER BY clause (optional).
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

		// Add ORDER BY clause
		if ( ! empty( $order ) ) {
			$query .= " ORDER BY {$order}";
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

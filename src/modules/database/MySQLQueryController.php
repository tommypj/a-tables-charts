<?php
/**
 * MySQL Query Controller
 *
 * Handles AJAX requests for MySQL query operations.
 *
 * @package ATablesCharts\Database
 * @since 1.0.0
 */

namespace ATablesCharts\Database;

use ATablesCharts\Tables\Services\TableService;
use ATablesCharts\Shared\Utils\Logger;

/**
 * MySQLQueryController Class
 *
 * Handles HTTP requests for MySQL queries.
 */
class MySQLQueryController {

	/**
	 * MySQL Query Service
	 *
	 * @var MySQLQueryService
	 */
	private $query_service;

	/**
	 * Table Service
	 *
	 * @var TableService
	 */
	private $table_service;

	/**
	 * Logger
	 *
	 * @var Logger
	 */
	private $logger;

	/**
	 * Rate limit transient key prefix
	 *
	 * @var string
	 */
	private $rate_limit_prefix = 'atables_mysql_rate_limit_';

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->query_service = new MySQLQueryService();
		$this->table_service = new TableService();
		$this->logger = new Logger();
	}

	/**
	 * Check rate limit for MySQL queries
	 *
	 * Prevents abuse by limiting number of queries per user per time period.
	 *
	 * @return array Array with 'allowed' boolean and 'message' string.
	 */
	private function check_rate_limit() {
		$user_id = get_current_user_id();
		$transient_key = $this->rate_limit_prefix . $user_id;

		// Get current request count
		$requests = get_transient( $transient_key );

		// Rate limit: 10 queries per minute
		$max_requests = apply_filters( 'atables_mysql_query_rate_limit', 10 );
		$time_window = apply_filters( 'atables_mysql_query_rate_window', 60 ); // seconds

		if ( false === $requests ) {
			// First request in this time window
			set_transient( $transient_key, 1, $time_window );
			return array(
				'allowed' => true,
				'remaining' => $max_requests - 1,
			);
		}

		if ( $requests >= $max_requests ) {
			// Rate limit exceeded
			$this->logger->warning( 'MySQL query rate limit exceeded', array(
				'user_id' => $user_id,
				'requests' => $requests,
			) );

			return array(
				'allowed' => false,
				'message' => sprintf(
					/* translators: 1: number of requests, 2: time window in seconds */
					__( 'Rate limit exceeded. Maximum %1$d queries allowed per %2$d seconds. Please try again later.', 'a-tables-charts' ),
					$max_requests,
					$time_window
				),
			);
		}

		// Increment request count
		set_transient( $transient_key, $requests + 1, $time_window );

		return array(
			'allowed' => true,
			'remaining' => $max_requests - ( $requests + 1 ),
		);
	}

	/**
	 * Test MySQL query
	 *
	 * SECURITY: Multiple layers of protection:
	 * - Nonce verification
	 * - Capability check (manage_options)
	 * - Rate limiting
	 * - Query validation (SELECT-only, whitelist, complexity limits)
	 * - Audit logging
	 *
	 * @return void Sends JSON response.
	 */
	public function test_query() {
		// Verify nonce
		if ( ! check_ajax_referer( 'atables_admin_nonce', 'nonce', false ) ) {
			$this->logger->warning( 'Nonce verification failed for test_query' );
			wp_send_json_error( array(
				'message' => __( 'Security check failed.', 'a-tables-charts' ),
			), 403 );
		}

		// Check permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			$this->logger->warning( 'Permission denied for test_query', array(
				'user_id' => get_current_user_id(),
			) );
			wp_send_json_error( array(
				'message' => __( 'You do not have permission to execute queries.', 'a-tables-charts' ),
			), 403 );
		}

		// Check rate limit
		$rate_limit = $this->check_rate_limit();
		if ( ! $rate_limit['allowed'] ) {
			wp_send_json_error( array(
				'message' => $rate_limit['message'],
			), 429 );
		}

		// Get and sanitize query
		// Note: stripslashes only removes added slashes, query is still validated by service
		$query = isset( $_POST['query'] ) ? stripslashes( $_POST['query'] ) : '';

		if ( empty( $query ) ) {
			wp_send_json_error( array(
				'message' => __( 'Query is required.', 'a-tables-charts' ),
			) );
		}

		// Log query attempt
		$this->logger->info( 'MySQL test query attempt', array(
			'user_id' => get_current_user_id(),
			'query_length' => strlen( $query ),
		) );

		// Test query (validation happens in service)
		$result = $this->query_service->test_query( $query, 5 );

		if ( ! $result['success'] ) {
			wp_send_json_error( array(
				'message' => $result['message'],
			) );
		}

		wp_send_json_success( array(
			'message' => __( 'Query executed successfully!', 'a-tables-charts' ),
			'headers' => $result['headers'],
			'data'    => $result['data'],
			'rows'    => $result['rows'],
			'columns' => $result['columns'],
			'rate_limit_remaining' => $rate_limit['remaining'],
		) );
	}

	/**
	 * Create table from MySQL query
	 *
	 * SECURITY: Multiple layers of protection:
	 * - Nonce verification
	 * - Capability check (manage_options)
	 * - Rate limiting
	 * - Query validation (SELECT-only, whitelist, complexity limits)
	 * - Input sanitization
	 * - Audit logging
	 *
	 * @return void Sends JSON response.
	 */
	public function create_table_from_query() {
		// Verify nonce
		if ( ! check_ajax_referer( 'atables_admin_nonce', 'nonce', false ) ) {
			$this->logger->error( 'Nonce verification failed for create_table_from_query' );
			wp_send_json_error( array(
				'message' => __( 'Security check failed.', 'a-tables-charts' ),
			), 403 );
		}

		// Check permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			$this->logger->error( 'Permission denied for create_table_from_query' );
			wp_send_json_error( array(
				'message' => __( 'You do not have permission to create tables.', 'a-tables-charts' ),
			), 403 );
		}

		// Check rate limit
		$rate_limit = $this->check_rate_limit();
		if ( ! $rate_limit['allowed'] ) {
			wp_send_json_error( array(
				'message' => $rate_limit['message'],
			), 429 );
		}

		// Get data
		$title = isset( $_POST['title'] ) ? sanitize_text_field( $_POST['title'] ) : '';
		$description = isset( $_POST['description'] ) ? sanitize_textarea_field( $_POST['description'] ) : '';
		$query = isset( $_POST['query'] ) ? stripslashes( $_POST['query'] ) : '';

		$this->logger->info( 'Create table from query request', array(
			'title' => $title,
			'has_query' => ! empty( $query ),
		) );

		if ( empty( $title ) ) {
			$this->logger->error( 'Title missing in create_table_from_query' );
			wp_send_json_error( array(
				'message' => __( 'Table title is required.', 'a-tables-charts' ),
			) );
		}

		if ( empty( $query ) ) {
			$this->logger->error( 'Query missing in create_table_from_query' );
			wp_send_json_error( array(
				'message' => __( 'Query is required.', 'a-tables-charts' ),
			) );
		}

		// Execute query to get data
		$result = $this->query_service->execute_query( $query );

		if ( ! $result['success'] ) {
			$this->logger->error( 'Query execution failed', array(
				'error' => $result['message'],
			) );
			wp_send_json_error( array(
				'message' => $result['message'],
			) );
		}

		$this->logger->info( 'Query executed successfully', array(
			'rows' => $result['rows'],
			'columns' => $result['columns'],
		) );

		// Create mock ImportResult object
		$import_result = (object) array(
			'headers'      => $result['headers'],
			'data'         => $result['data'],
			'row_count'    => $result['rows'],
			'column_count' => $result['columns'],
		);

		// Create table
		$table_result = $this->table_service->create_from_import(
			$title,
			$import_result,
			'mysql',
			$description
		);

		if ( ! $table_result['success'] ) {
			$this->logger->error( 'Table creation failed', array(
				'error' => $table_result['message'],
			) );
			wp_send_json_error( array(
				'message' => $table_result['message'],
			) );
		}

		// Store the query with the table for reference
		update_post_meta( $table_result['table_id'], '_atables_source_query', $query );

		$this->logger->info( 'Table created from MySQL query', array(
			'table_id' => $table_result['table_id'],
			'title'    => $title,
		) );

		wp_send_json_success( array(
			'message'  => __( 'Table created successfully from MySQL query!', 'a-tables-charts' ),
			'table_id' => $table_result['table_id'],
		) );
	}

	/**
	 * Get available tables
	 *
	 * @return void Sends JSON response.
	 */
	public function get_tables() {
		// Verify nonce
		if ( ! check_ajax_referer( 'atables_admin_nonce', 'nonce', false ) ) {
			wp_send_json_error( array(
				'message' => __( 'Security check failed.', 'a-tables-charts' ),
			), 403 );
		}

		// Check permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array(
				'message' => __( 'You do not have permission to view tables.', 'a-tables-charts' ),
			), 403 );
		}

		$tables = $this->query_service->get_available_tables();

		wp_send_json_success( array(
			'tables' => $tables,
		) );
	}

	/**
	 * Get table columns
	 *
	 * @return void Sends JSON response.
	 */
	public function get_table_columns() {
		// Verify nonce
		if ( ! check_ajax_referer( 'atables_admin_nonce', 'nonce', false ) ) {
			wp_send_json_error( array(
				'message' => __( 'Security check failed.', 'a-tables-charts' ),
			), 403 );
		}

		// Check permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array(
				'message' => __( 'You do not have permission to view tables.', 'a-tables-charts' ),
			), 403 );
		}

		$table_name = isset( $_POST['table'] ) ? sanitize_text_field( $_POST['table'] ) : '';

		if ( empty( $table_name ) ) {
			wp_send_json_error( array(
				'message' => __( 'Table name is required.', 'a-tables-charts' ),
			) );
		}

		$columns = $this->query_service->get_table_columns( $table_name );

		wp_send_json_success( array(
			'columns' => $columns,
		) );
	}

	/**
	 * Get sample queries
	 *
	 * @return void Sends JSON response.
	 */
	public function get_sample_queries() {
		// Verify nonce
		if ( ! check_ajax_referer( 'atables_admin_nonce', 'nonce', false ) ) {
			wp_send_json_error( array(
				'message' => __( 'Security check failed.', 'a-tables-charts' ),
			), 403 );
		}

		$samples = $this->query_service->get_sample_queries();

		wp_send_json_success( array(
			'queries' => $samples,
		) );
	}
}

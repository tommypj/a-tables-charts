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
	 * Constructor
	 */
	public function __construct() {
		$this->query_service = new MySQLQueryService();
		$this->table_service = new TableService();
		$this->logger = new Logger();
	}

	/**
	 * Test MySQL query
	 *
	 * @return void Sends JSON response.
	 */
	public function test_query() {
		// Verify nonce
		if ( ! check_ajax_referer( 'atables_admin_nonce', 'nonce', false ) ) {
			wp_send_json_error( array(
				'message' => __( 'Security check failed.', 'a-tables-charts' ),
			), 403 );
		}

		// Check permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array(
				'message' => __( 'You do not have permission to execute queries.', 'a-tables-charts' ),
			), 403 );
		}

		// Get query
		$query = isset( $_POST['query'] ) ? stripslashes( $_POST['query'] ) : '';

		if ( empty( $query ) ) {
			wp_send_json_error( array(
				'message' => __( 'Query is required.', 'a-tables-charts' ),
			) );
		}

		// Test query
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
		) );
	}

	/**
	 * Create table from MySQL query
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

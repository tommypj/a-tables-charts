<?php
/**
 * Filter Controller
 *
 * Handles AJAX requests for filter operations.
 *
 * @package ATablesCharts\Filters\Controllers
 * @since 1.0.5
 */

namespace ATablesCharts\Filters\Controllers;

use ATablesCharts\Filters\Services\FilterService;
use ATablesCharts\Filters\Services\FilterPresetService;
use ATablesCharts\Filters\Types\Filter;
use ATablesCharts\Filters\Types\FilterPreset;
use ATablesCharts\Tables\Repositories\TableRepository;

/**
 * FilterController Class
 *
 * Responsibilities:
 * - Handle AJAX requests for filter operations
 * - Validate user permissions
 * - Format API responses
 * - Handle errors gracefully
 */
class FilterController {

	/**
	 * Filter preset service
	 *
	 * @var FilterPresetService
	 */
	private $preset_service;

	/**
	 * Filter service
	 *
	 * @var FilterService
	 */
	private $filter_service;

	/**
	 * Table repository
	 *
	 * @var TableRepository
	 */
	private $table_repository;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->preset_service   = new FilterPresetService();
		$this->filter_service   = new FilterService();
		$this->table_repository = new TableRepository();
	}

	/**
	 * Register AJAX hooks
	 *
	 * @return void
	 */
	public function register_hooks() {
		// Preset CRUD operations
		add_action( 'wp_ajax_atables_create_filter_preset', array( $this, 'create_preset' ) );
		add_action( 'wp_ajax_atables_update_filter_preset', array( $this, 'update_preset' ) );
		add_action( 'wp_ajax_atables_delete_filter_preset', array( $this, 'delete_preset' ) );
		add_action( 'wp_ajax_atables_get_filter_preset', array( $this, 'get_preset' ) );
		add_action( 'wp_ajax_atables_get_table_presets', array( $this, 'get_table_presets' ) );

		// New action names for v2 filter builder
		add_action( 'wp_ajax_atables_save_filter_preset', array( $this, 'create_preset' ) );
		add_action( 'wp_ajax_atables_load_filter_preset', array( $this, 'get_preset' ) );
		add_action( 'wp_ajax_atables_get_filter_presets', array( $this, 'get_table_presets' ) );

		// Preset management
		add_action( 'wp_ajax_atables_set_default_preset', array( $this, 'set_default_preset' ) );
		add_action( 'wp_ajax_atables_duplicate_preset', array( $this, 'duplicate_preset' ) );

		// Filter operations
		add_action( 'wp_ajax_atables_apply_filters', array( $this, 'apply_filters' ) );
		add_action( 'wp_ajax_atables_test_filter', array( $this, 'test_filter' ) );

		// Column analysis
		add_action( 'wp_ajax_atables_analyze_column', array( $this, 'analyze_column' ) );
		add_action( 'wp_ajax_atables_get_column_values', array( $this, 'get_column_values' ) );
	}

	/**
	 * Create a new filter preset
	 *
	 * @return void
	 */
	public function create_preset() {
		// Verify nonce
		if ( ! $this->verify_nonce() ) {
			$this->send_error( __( 'Security check failed.', 'a-tables-charts' ), 403 );
		}

		// Check permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			$this->send_error( __( 'Insufficient permissions.', 'a-tables-charts' ), 403 );
		}

		// Get request data
		$table_id    = isset( $_POST['table_id'] ) ? intval( $_POST['table_id'] ) : 0;
		$name        = isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : '';
		$description = isset( $_POST['description'] ) ? sanitize_textarea_field( $_POST['description'] ) : '';
		$filters     = isset( $_POST['filters'] ) ? json_decode( stripslashes( $_POST['filters'] ), true ) : array();
		$is_default  = isset( $_POST['is_default'] ) ? (bool) $_POST['is_default'] : false;

		// Validate required fields
		if ( empty( $table_id ) ) {
			$this->send_error( __( 'Table ID is required.', 'a-tables-charts' ), 400 );
		}

		if ( empty( $name ) ) {
			$this->send_error( __( 'Preset name is required.', 'a-tables-charts' ), 400 );
		}

		if ( empty( $filters ) ) {
			$this->send_error( __( 'At least one filter is required.', 'a-tables-charts' ), 400 );
		}

		// Verify table exists
		$table = $this->table_repository->find_by_id( $table_id );
		if ( ! $table ) {
			$this->send_error( __( 'Table not found.', 'a-tables-charts' ), 404 );
		}

		// Create preset
		$result = $this->preset_service->create_preset(
			array(
				'table_id'    => $table_id,
				'name'        => $name,
				'description' => $description,
				'filters'     => $filters,
				'is_default'  => $is_default,
			)
		);

		if ( ! $result['success'] ) {
			$this->send_error( implode( ' ', $result['errors'] ), 400 );
		}

		$this->send_success( $result['data'], __( 'Filter preset created successfully.', 'a-tables-charts' ) );
	}

	/**
	 * Update a filter preset
	 *
	 * @return void
	 */
	public function update_preset() {
		// Verify nonce
		if ( ! $this->verify_nonce() ) {
			$this->send_error( __( 'Security check failed.', 'a-tables-charts' ), 403 );
		}

		// Check permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			$this->send_error( __( 'Insufficient permissions.', 'a-tables-charts' ), 403 );
		}

		// Get request data
		$preset_id   = isset( $_POST['preset_id'] ) ? intval( $_POST['preset_id'] ) : 0;
		$name        = isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : '';
		$description = isset( $_POST['description'] ) ? sanitize_textarea_field( $_POST['description'] ) : '';
		$filters     = isset( $_POST['filters'] ) ? json_decode( stripslashes( $_POST['filters'] ), true ) : null;
		$is_default  = isset( $_POST['is_default'] ) ? (bool) $_POST['is_default'] : null;

		// Validate required fields
		if ( empty( $preset_id ) ) {
			$this->send_error( __( 'Preset ID is required.', 'a-tables-charts' ), 400 );
		}

		// Build update data (only include provided fields)
		$update_data = array();

		if ( ! empty( $name ) ) {
			$update_data['name'] = $name;
		}

		if ( isset( $description ) ) {
			$update_data['description'] = $description;
		}

		if ( null !== $filters ) {
			$update_data['filters'] = $filters;
		}

		if ( null !== $is_default ) {
			$update_data['is_default'] = $is_default;
		}

		// Update preset
		$result = $this->preset_service->update_preset( $preset_id, $update_data );

		if ( ! $result['success'] ) {
			$this->send_error( implode( ' ', $result['errors'] ), 400 );
		}

		$this->send_success( $result['data'], __( 'Filter preset updated successfully.', 'a-tables-charts' ) );
	}

	/**
	 * Delete a filter preset
	 *
	 * @return void
	 */
	public function delete_preset() {
		// Verify nonce
		if ( ! $this->verify_nonce() ) {
			$this->send_error( __( 'Security check failed.', 'a-tables-charts' ), 403 );
		}

		// Check permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			$this->send_error( __( 'Insufficient permissions.', 'a-tables-charts' ), 403 );
		}

		// Get request data
		$preset_id = isset( $_POST['preset_id'] ) ? intval( $_POST['preset_id'] ) : 0;

		// Validate required fields
		if ( empty( $preset_id ) ) {
			$this->send_error( __( 'Preset ID is required.', 'a-tables-charts' ), 400 );
		}

		// Delete preset
		$result = $this->preset_service->delete_preset( $preset_id );

		if ( ! $result['success'] ) {
			$this->send_error( implode( ' ', $result['errors'] ), 400 );
		}

		$this->send_success( null, __( 'Filter preset deleted successfully.', 'a-tables-charts' ) );
	}

	/**
	 * Get a single filter preset
	 *
	 * @return void
	 */
	public function get_preset() {
		// Verify nonce
		if ( ! $this->verify_nonce() ) {
			$this->send_error( __( 'Security check failed.', 'a-tables-charts' ), 403 );
		}

		// Check permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			$this->send_error( __( 'Insufficient permissions.', 'a-tables-charts' ), 403 );
		}

		// Get request data (support both GET and POST)
		$preset_id = isset( $_POST['preset_id'] ) ? intval( $_POST['preset_id'] ) : ( isset( $_GET['preset_id'] ) ? intval( $_GET['preset_id'] ) : 0 );

		// Validate required fields
		if ( empty( $preset_id ) ) {
			$this->send_error( __( 'Preset ID is required.', 'a-tables-charts' ), 400 );
		}

		// Get preset
		$result = $this->preset_service->get_preset( $preset_id );

		if ( ! $result['success'] ) {
			$this->send_error( implode( ' ', $result['errors'] ), 404 );
		}

		$this->send_success( $result['data'] );
	}

	/**
	 * Get all presets for a table
	 *
	 * @return void
	 */
	public function get_table_presets() {
		// Verify nonce
		if ( ! $this->verify_nonce() ) {
			$this->send_error( __( 'Security check failed.', 'a-tables-charts' ), 403 );
		}

		// Check permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			$this->send_error( __( 'Insufficient permissions.', 'a-tables-charts' ), 403 );
		}

		// Get request data (support both GET and POST)
		$table_id = isset( $_POST['table_id'] ) ? intval( $_POST['table_id'] ) : ( isset( $_GET['table_id'] ) ? intval( $_GET['table_id'] ) : 0 );

		// Validate required fields
		if ( empty( $table_id ) ) {
			$this->send_error( __( 'Table ID is required.', 'a-tables-charts' ), 400 );
		}

		// Get presets
		$result = $this->preset_service->get_table_presets( $table_id );

		$this->send_success( $result['data'] );
	}

	/**
	 * Set preset as default
	 *
	 * @return void
	 */
	public function set_default_preset() {
		// Verify nonce
		if ( ! $this->verify_nonce() ) {
			$this->send_error( __( 'Security check failed.', 'a-tables-charts' ), 403 );
		}

		// Check permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			$this->send_error( __( 'Insufficient permissions.', 'a-tables-charts' ), 403 );
		}

		// Get request data
		$preset_id = isset( $_POST['preset_id'] ) ? intval( $_POST['preset_id'] ) : 0;
		$table_id  = isset( $_POST['table_id'] ) ? intval( $_POST['table_id'] ) : 0;

		// Validate required fields
		if ( empty( $preset_id ) || empty( $table_id ) ) {
			$this->send_error( __( 'Preset ID and Table ID are required.', 'a-tables-charts' ), 400 );
		}

		// Set default
		$result = $this->preset_service->set_default( $preset_id, $table_id );

		if ( ! $result['success'] ) {
			$this->send_error( implode( ' ', $result['errors'] ), 400 );
		}

		$this->send_success( null, __( 'Default preset set successfully.', 'a-tables-charts' ) );
	}

	/**
	 * Duplicate a filter preset
	 *
	 * @return void
	 */
	public function duplicate_preset() {
		// Verify nonce
		if ( ! $this->verify_nonce() ) {
			$this->send_error( __( 'Security check failed.', 'a-tables-charts' ), 403 );
		}

		// Check permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			$this->send_error( __( 'Insufficient permissions.', 'a-tables-charts' ), 403 );
		}

		// Get request data
		$preset_id = isset( $_POST['preset_id'] ) ? intval( $_POST['preset_id'] ) : 0;
		$new_name  = isset( $_POST['new_name'] ) ? sanitize_text_field( $_POST['new_name'] ) : null;

		// Validate required fields
		if ( empty( $preset_id ) ) {
			$this->send_error( __( 'Preset ID is required.', 'a-tables-charts' ), 400 );
		}

		// Duplicate preset
		$result = $this->preset_service->duplicate_preset( $preset_id, $new_name );

		if ( ! $result['success'] ) {
			$this->send_error( implode( ' ', $result['errors'] ), 400 );
		}

		$this->send_success( $result['data'], __( 'Filter preset duplicated successfully.', 'a-tables-charts' ) );
	}

	/**
	 * Apply filters to table data
	 *
	 * @return void
	 */
	public function apply_filters() {
		// Verify nonce
		if ( ! $this->verify_nonce() ) {
			$this->send_error( __( 'Security check failed.', 'a-tables-charts' ), 403 );
		}

		// Check permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			$this->send_error( __( 'Insufficient permissions.', 'a-tables-charts' ), 403 );
		}

		// Get request data
		$table_id  = isset( $_POST['table_id'] ) ? intval( $_POST['table_id'] ) : 0;
		$preset_id = isset( $_POST['preset_id'] ) ? intval( $_POST['preset_id'] ) : null;
		$filters   = isset( $_POST['filters'] ) ? json_decode( stripslashes( $_POST['filters'] ), true ) : null;

		// Validate required fields
		if ( empty( $table_id ) ) {
			$this->send_error( __( 'Table ID is required.', 'a-tables-charts' ), 400 );
		}

		if ( empty( $preset_id ) && empty( $filters ) ) {
			$this->send_error( __( 'Either preset_id or filters must be provided.', 'a-tables-charts' ), 400 );
		}

		// Get table data
		$table = $this->table_repository->find_by_id( $table_id );
		if ( ! $table ) {
			$this->send_error( __( 'Table not found.', 'a-tables-charts' ), 404 );
		}

		// Get table data
		$table_data = $this->table_repository->get_table_data( $table_id );

		// Map data with headers
		$headers = $table->get_headers();
		$mapped_data = array();
		foreach ( $table_data as $row ) {
			$mapped_row = array();
			foreach ( $row as $index => $value ) {
				if ( isset( $headers[ $index ] ) ) {
					$mapped_row[ $headers[ $index ] ] = $value;
				}
			}
			$mapped_data[] = $mapped_row;
		}

		// Apply filters
		if ( ! empty( $preset_id ) ) {
			// Apply using preset
			$result = $this->preset_service->apply_preset_to_data( $preset_id, $mapped_data );
			
			if ( ! $result['success'] ) {
				$this->send_error( implode( ' ', $result['errors'] ), 400 );
			}

			$this->send_success( $result['data'] );
		} else {
			// Apply using filters array
			$filter_objects = array();
			foreach ( $filters as $filter_data ) {
				$filter_objects[] = new Filter( $filter_data );
			}

			$filtered_data = $this->filter_service->apply_filters( $mapped_data, $filter_objects );

			$this->send_success(
				array(
					'filtered_data'  => $filtered_data,
					'original_count' => count( $mapped_data ),
					'filtered_count' => count( $filtered_data ),
				)
			);
		}
	}

	/**
	 * Test a filter before applying
	 *
	 * @return void
	 */
	public function test_filter() {
		// Verify nonce
		if ( ! $this->verify_nonce() ) {
			$this->send_error( __( 'Security check failed.', 'a-tables-charts' ), 403 );
		}

		// Check permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			$this->send_error( __( 'Insufficient permissions.', 'a-tables-charts' ), 403 );
		}

		// Get request data
		$table_id    = isset( $_POST['table_id'] ) ? intval( $_POST['table_id'] ) : 0;
		$filter_data = isset( $_POST['filter'] ) ? json_decode( stripslashes( $_POST['filter'] ), true ) : array();

		// Validate required fields
		if ( empty( $table_id ) || empty( $filter_data ) ) {
			$this->send_error( __( 'Table ID and filter data are required.', 'a-tables-charts' ), 400 );
		}

		// Get table data
		$table = $this->table_repository->find_by_id( $table_id );
		if ( ! $table ) {
			$this->send_error( __( 'Table not found.', 'a-tables-charts' ), 404 );
		}

		// Get and map table data
		$table_data = $this->table_repository->get_table_data( $table_id );
		$headers = $table->get_headers();
		$mapped_data = array();
		foreach ( $table_data as $row ) {
			$mapped_row = array();
			foreach ( $row as $index => $value ) {
				if ( isset( $headers[ $index ] ) ) {
					$mapped_row[ $headers[ $index ] ] = $value;
				}
			}
			$mapped_data[] = $mapped_row;
		}

		// Create filter object
		$filter = new Filter( $filter_data );

		// Test filter
		$result = $this->filter_service->test_filter( $mapped_data, $filter );

		$this->send_success( $result );
	}

	/**
	 * Analyze column data
	 *
	 * @return void
	 */
	public function analyze_column() {
		// Verify nonce
		if ( ! $this->verify_nonce() ) {
			$this->send_error( __( 'Security check failed.', 'a-tables-charts' ), 403 );
		}

		// Check permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			$this->send_error( __( 'Insufficient permissions.', 'a-tables-charts' ), 403 );
		}

		// Get request data
		$table_id = isset( $_GET['table_id'] ) ? intval( $_GET['table_id'] ) : 0;
		$column   = isset( $_GET['column'] ) ? sanitize_text_field( $_GET['column'] ) : '';

		// Validate required fields
		if ( empty( $table_id ) || empty( $column ) ) {
			$this->send_error( __( 'Table ID and column name are required.', 'a-tables-charts' ), 400 );
		}

		// Get table data
		$table = $this->table_repository->find_by_id( $table_id );
		if ( ! $table ) {
			$this->send_error( __( 'Table not found.', 'a-tables-charts' ), 404 );
		}

		// Get and map table data
		$table_data = $this->table_repository->get_table_data( $table_id );
		$headers = $table->get_headers();
		$mapped_data = array();
		foreach ( $table_data as $row ) {
			$mapped_row = array();
			foreach ( $row as $index => $value ) {
				if ( isset( $headers[ $index ] ) ) {
					$mapped_row[ $headers[ $index ] ] = $value;
				}
			}
			$mapped_data[] = $mapped_row;
		}

		// Analyze column
		$suggestion = $this->filter_service->suggest_filter_for_column( $mapped_data, $column );

		$this->send_success( $suggestion );
	}

	/**
	 * Get unique values for a column
	 *
	 * @return void
	 */
	public function get_column_values() {
		// Verify nonce
		if ( ! $this->verify_nonce() ) {
			$this->send_error( __( 'Security check failed.', 'a-tables-charts' ), 403 );
		}

		// Check permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			$this->send_error( __( 'Insufficient permissions.', 'a-tables-charts' ), 403 );
		}

		// Get request data
		$table_id = isset( $_GET['table_id'] ) ? intval( $_GET['table_id'] ) : 0;
		$column   = isset( $_GET['column'] ) ? sanitize_text_field( $_GET['column'] ) : '';

		// Validate required fields
		if ( empty( $table_id ) || empty( $column ) ) {
			$this->send_error( __( 'Table ID and column name are required.', 'a-tables-charts' ), 400 );
		}

		// Get table data
		$table = $this->table_repository->find_by_id( $table_id );
		if ( ! $table ) {
			$this->send_error( __( 'Table not found.', 'a-tables-charts' ), 404 );
		}

		// Get and map table data
		$table_data = $this->table_repository->get_table_data( $table_id );
		$headers = $table->get_headers();
		$mapped_data = array();
		foreach ( $table_data as $row ) {
			$mapped_row = array();
			foreach ( $row as $index => $value ) {
				if ( isset( $headers[ $index ] ) ) {
					$mapped_row[ $headers[ $index ] ] = $value;
				}
			}
			$mapped_data[] = $mapped_row;
		}

		// Get unique values
		$values = $this->filter_service->get_column_unique_values( $mapped_data, $column );

		$this->send_success(
			array(
				'column' => $column,
				'values' => $values,
				'count'  => count( $values ),
			)
		);
	}

	/**
	 * Verify AJAX nonce
	 *
	 * @return bool True if nonce is valid, false otherwise
	 */
	private function verify_nonce() {
		$nonce = isset( $_REQUEST['nonce'] ) ? $_REQUEST['nonce'] : '';
		return wp_verify_nonce( $nonce, 'atables_admin_nonce' );
	}

	/**
	 * Send success response
	 *
	 * @param mixed  $data    Response data.
	 * @param string $message Success message.
	 * @return void
	 */
	private function send_success( $data = null, $message = '' ) {
		wp_send_json_success(
			array(
				'data'    => $data,
				'message' => $message,
			)
		);
	}

	/**
	 * Send error response
	 *
	 * @param string $message Error message.
	 * @param int    $code    HTTP status code.
	 * @return void
	 */
	private function send_error( $message, $code = 400 ) {
		wp_send_json_error(
			array(
				'message' => $message,
			),
			$code
		);
	}
}

<?php
/**
 * Table Controller
 *
 * Handles AJAX requests for table operations.
 *
 * @package ATablesCharts\Tables\Controllers
 * @since 1.0.0
 */

namespace ATablesCharts\Tables\Controllers;

use ATablesCharts\Tables\Services\TableService;
use ATablesCharts\Shared\Utils\Validator;
use ATablesCharts\Shared\Utils\Sanitizer;
use ATablesCharts\Shared\Utils\Helpers;
use ATablesCharts\Shared\Utils\Logger;
use ATablesCharts\Cache\Services\CacheService;

/**
 * TableController Class
 *
 * Responsibilities:
 * - Handle AJAX table operations
 * - Validate requests
 * - Return JSON responses
 * - Handle errors gracefully
 */
class TableController {

	/**
	 * Table service
	 *
	 * @var TableService
	 */
	private $service;

	/**
	 * Logger instance
	 *
	 * @var Logger
	 */
	private $logger;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->service = new TableService();
		$this->logger  = new Logger();
	}

	/**
	 * Register AJAX hooks
	 */
	public function register_hooks() {
		// Table CRUD operations.
		add_action( 'wp_ajax_atables_save_table', array( $this, 'handle_save_table' ) );
		add_action( 'wp_ajax_atables_get_table', array( $this, 'handle_get_table' ) );
		add_action( 'wp_ajax_atables_get_tables', array( $this, 'handle_get_tables' ) );
		add_action( 'wp_ajax_atables_update_table', array( $this, 'handle_update_table' ) );
		add_action( 'wp_ajax_atables_delete_table', array( $this, 'handle_delete_table' ) );
		add_action( 'wp_ajax_atables_duplicate_table', array( $this, 'handle_duplicate_table' ) );
		add_action( 'wp_ajax_atables_save_manual_table', array( $this, 'handle_save_manual_table' ) );
	}

	/**
	 * Handle save table AJAX request
	 *
	 * Saves imported data as a new table.
	 */
	public function handle_save_table() {
		// Verify nonce.
		if ( ! $this->verify_nonce() ) {
			$this->send_error( __( 'Security check failed.', 'a-tables-charts' ), 403 );
			return;
		}

		// Check permissions.
		if ( ! current_user_can( 'manage_options' ) ) {
			$this->send_error( __( 'You do not have permission to perform this action.', 'a-tables-charts' ), 403 );
			return;
		}

		// Get parameters.
		$title       = isset( $_POST['title'] ) ? Sanitizer::text( $_POST['title'] ) : '';
		$description = isset( $_POST['description'] ) ? Sanitizer::textarea( $_POST['description'] ) : '';
		$source_type = isset( $_POST['source_type'] ) ? Sanitizer::text( $_POST['source_type'] ) : 'csv';
		
		// Get import data.
		$import_data = isset( $_POST['import_data'] ) ? $_POST['import_data'] : null;

		if ( empty( $title ) ) {
			$this->send_error( __( 'Table title is required.', 'a-tables-charts' ), 400 );
			return;
		}

		if ( empty( $import_data ) ) {
			$this->send_error( __( 'No data to save.', 'a-tables-charts' ), 400 );
			return;
		}

		// Parse import data.
		if ( is_string( $import_data ) ) {
			$import_data = json_decode( stripslashes( $import_data ), true );
		}

		// Validate import data structure.
		if ( ! isset( $import_data['headers'] ) || ! isset( $import_data['data'] ) ) {
			$this->send_error( __( 'Invalid import data format.', 'a-tables-charts' ), 400 );
			return;
		}

		// Create a mock ImportResult object.
		$import_result = (object) array(
			'success'      => true,
			'headers'      => $import_data['headers'],
			'data'         => $import_data['data'],
			'row_count'    => isset( $import_data['row_count'] ) ? $import_data['row_count'] : count( $import_data['data'] ),
			'column_count' => isset( $import_data['column_count'] ) ? $import_data['column_count'] : count( $import_data['headers'] ),
		);

		$this->logger->info( 'Save table request', array(
			'title'       => $title,
			'source_type' => $source_type,
			'rows'        => $import_result->row_count,
		) );

		// Create table.
		$result = $this->service->create_from_import( $title, $import_result, $source_type, $description );

		if ( $result['success'] ) {
			$this->send_success( array( 'table_id' => $result['table_id'] ), $result['message'] );
		} else {
			$this->send_error( $result['message'], 400 );
		}
	}

	/**
	 * Handle get table AJAX request
	 */
	public function handle_get_table() {
		// Verify nonce.
		if ( ! $this->verify_nonce() ) {
			$this->send_error( __( 'Security check failed.', 'a-tables-charts' ), 403 );
			return;
		}

		// Get table ID.
		$table_id = isset( $_POST['table_id'] ) ? (int) $_POST['table_id'] : 0;

		if ( empty( $table_id ) ) {
			$this->send_error( __( 'Table ID is required.', 'a-tables-charts' ), 400 );
			return;
		}

		// Get table.
		$table = $this->service->get_table( $table_id );

		if ( ! $table ) {
			$this->send_error( __( 'Table not found.', 'a-tables-charts' ), 404 );
			return;
		}

		$this->send_success( $table->to_array() );
	}

	/**
	 * Handle get tables AJAX request
	 */
	public function handle_get_tables() {
		// Verify nonce.
		if ( ! $this->verify_nonce() ) {
			$this->send_error( __( 'Security check failed.', 'a-tables-charts' ), 403 );
			return;
		}

		// Get parameters.
		$page     = isset( $_POST['page'] ) ? (int) $_POST['page'] : 1;
		$per_page = isset( $_POST['per_page'] ) ? (int) $_POST['per_page'] : 20;
		$status   = isset( $_POST['status'] ) ? Sanitizer::text( $_POST['status'] ) : 'active';

		$args = array(
			'page'     => $page,
			'per_page' => $per_page,
			'status'   => $status,
		);

		// Get tables.
		$result = $this->service->get_all_tables( $args );

		// Convert tables to arrays.
		$tables_array = array_map(
			function( $table ) {
				return $table->to_array();
			},
			$result['tables']
		);

		$this->send_success(
			array(
				'tables'   => $tables_array,
				'total'    => $result['total'],
				'page'     => $result['page'],
				'per_page' => $result['per_page'],
			)
		);
	}

	/**
	* Handle update table AJAX request
	*/
	public function handle_update_table() {
	// Start output buffering to catch any unexpected output
	ob_start();
	
	try {
	 // Verify nonce.
			if ( ! $this->verify_nonce() ) {
	  ob_end_clean();
	  $this->send_error( __( 'Security check failed.', 'a-tables-charts' ), 403 );
	 return;
	}

			// Check permissions.
	 if ( ! current_user_can( 'manage_options' ) ) {
	 ob_end_clean();
				$this->send_error( __( 'You do not have permission to perform this action.', 'a-tables-charts' ), 403 );
	  return;
	}

	 // Get parameters.
			$table_id = isset( $_POST['table_id'] ) ? (int) $_POST['table_id'] : 0;

			if ( empty( $table_id ) ) {
				ob_end_clean();
				$this->send_error( __( 'Table ID is required.', 'a-tables-charts' ), 400 );
				return;
			}

		// Get update data.
		$data = array();

		if ( isset( $_POST['title'] ) ) {
			$data['title'] = Sanitizer::text( $_POST['title'] );
		}

		if ( isset( $_POST['description'] ) ) {
			$data['description'] = Sanitizer::textarea( $_POST['description'] );
		}

		if ( isset( $_POST['status'] ) ) {
			$data['status'] = Sanitizer::text( $_POST['status'] );
		}
		
		// Handle headers and data update.
		if ( isset( $_POST['headers'] ) && isset( $_POST['data'] ) ) {
			$headers = isset( $_POST['headers'] ) ? $_POST['headers'] : array();
			$table_data = isset( $_POST['data'] ) ? $_POST['data'] : array();
			
			// Sanitize headers.
			$headers = array_map( array( 'ATablesCharts\Shared\Utils\Sanitizer', 'text' ), $headers );
			
			// Sanitize data.
			$sanitized_data = array();
			foreach ( $table_data as $row ) {
				$sanitized_row = array();
				foreach ( $row as $cell ) {
					$sanitized_row[] = Sanitizer::text( $cell );
				}
				$sanitized_data[] = $sanitized_row;
			}
			
			$data['source_data'] = array(
				'headers' => $headers,
				'data'    => $sanitized_data,
			);
			$data['row_count'] = count( $sanitized_data );
			$data['column_count'] = count( $headers );
		}

		// Handle display settings update.
		if ( isset( $_POST['display_settings'] ) ) {
			$display_settings_input = $_POST['display_settings'];
			
			// Parse if JSON string.
			if ( is_string( $display_settings_input ) ) {
				$display_settings_input = json_decode( stripslashes( $display_settings_input ), true );
			}
			
			if ( is_array( $display_settings_input ) ) {
				$display_settings = $this->sanitize_display_settings( $display_settings_input );
				$data['display_settings'] = $display_settings;
			}
		}

			// Update table.
			$result = $this->service->update_table( $table_id, $data );
			
			// Clean output buffer before sending response
			ob_end_clean();

			if ( $result['success'] ) {
				// Clear cache for this table
				$this->clear_table_cache( $table_id );
				
				$this->send_success( null, $result['message'] );
			} else {
				$this->send_error( $result['message'], 400 );
			}
		} catch ( \Exception $e ) {
			// Clean buffer and log error
			ob_end_clean();
			$this->logger->error( 'Update table exception', array(
				'error' => $e->getMessage(),
				'trace' => $e->getTraceAsString(),
			) );
			$this->send_error( __( 'An error occurred while updating the table.', 'a-tables-charts' ), 500 );
		}
	}

	/**
	 * Handle delete table AJAX request
	 */
	public function handle_delete_table() {
		// Verify nonce.
		if ( ! $this->verify_nonce() ) {
			$this->send_error( __( 'Security check failed.', 'a-tables-charts' ), 403 );
			return;
		}

		// Check permissions.
		if ( ! current_user_can( 'manage_options' ) ) {
			$this->send_error( __( 'You do not have permission to perform this action.', 'a-tables-charts' ), 403 );
			return;
		}

		// Get table ID.
		$table_id = isset( $_POST['table_id'] ) ? (int) $_POST['table_id'] : 0;

		if ( empty( $table_id ) ) {
			$this->send_error( __( 'Table ID is required.', 'a-tables-charts' ), 400 );
			return;
		}

		// Delete table.
		$result = $this->service->delete_table( $table_id );

		if ( $result['success'] ) {
			// Clear cache for this table
			$this->clear_table_cache( $table_id );
			
			$this->send_success( null, $result['message'] );
		} else {
			$this->send_error( $result['message'], 400 );
		}
	}

	/**
	 * Handle duplicate table AJAX request
	 */
	public function handle_duplicate_table() {
		// Verify nonce.
		if ( ! $this->verify_nonce() ) {
			$this->send_error( __( 'Security check failed.', 'a-tables-charts' ), 403 );
			return;
		}

		// Check permissions.
		if ( ! current_user_can( 'manage_options' ) ) {
			$this->send_error( __( 'You do not have permission to perform this action.', 'a-tables-charts' ), 403 );
			return;
		}

		// Get parameters.
		$table_id  = isset( $_POST['table_id'] ) ? (int) $_POST['table_id'] : 0;
		$new_title = isset( $_POST['new_title'] ) ? Sanitizer::text( $_POST['new_title'] ) : '';

		if ( empty( $table_id ) ) {
			$this->send_error( __( 'Table ID is required.', 'a-tables-charts' ), 400 );
			return;
		}

		// Duplicate table.
		$result = $this->service->duplicate_table( $table_id, $new_title );

		if ( $result['success'] ) {
			$this->send_success( array( 'table_id' => $result['table_id'] ), $result['message'] );
		} else {
			$this->send_error( $result['message'], 400 );
		}
	}

	/**
	 * Handle save manual table AJAX request
	 *
	 * Saves manually created table data.
	 */
	public function handle_save_manual_table() {
		// Verify nonce.
		if ( ! $this->verify_nonce() ) {
			$this->send_error( __( 'Security check failed.', 'a-tables-charts' ), 403 );
			return;
		}

		// Check permissions.
		if ( ! current_user_can( 'manage_options' ) ) {
			$this->send_error( __( 'You do not have permission to perform this action.', 'a-tables-charts' ), 403 );
			return;
		}

		// Get and sanitize parameters.
		$title       = isset( $_POST['title'] ) ? Sanitizer::text( $_POST['title'] ) : '';
		$description = isset( $_POST['description'] ) ? Sanitizer::textarea( $_POST['description'] ) : '';
		$headers     = isset( $_POST['headers'] ) ? $_POST['headers'] : array();
		$data        = isset( $_POST['data'] ) ? $_POST['data'] : array();

		// Validate required fields.
		if ( empty( $title ) ) {
			$this->send_error( __( 'Table title is required.', 'a-tables-charts' ), 400 );
			return;
		}

		if ( empty( $headers ) || ! is_array( $headers ) ) {
			$this->send_error( __( 'Table headers are required.', 'a-tables-charts' ), 400 );
			return;
		}

		if ( ! is_array( $data ) ) {
			$this->send_error( __( 'Invalid table data format.', 'a-tables-charts' ), 400 );
			return;
		}

		// Sanitize headers.
		$sanitized_headers = array();
		foreach ( $headers as $header ) {
			$sanitized_headers[] = Sanitizer::text( $header );
		}

		// Sanitize data rows.
		$sanitized_data = array();
		foreach ( $data as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}

			$sanitized_row = array();
			foreach ( $row as $cell ) {
				$sanitized_row[] = Sanitizer::text( $cell );
			}
			$sanitized_data[] = $sanitized_row;
		}

		// Ensure all rows have the same number of columns as headers.
		$header_count = count( $sanitized_headers );
		foreach ( $sanitized_data as &$row ) {
			// Pad rows with empty strings if needed.
			while ( count( $row ) < $header_count ) {
				$row[] = '';
			}
			// Trim rows if they have more columns than headers.
			if ( count( $row ) > $header_count ) {
				$row = array_slice( $row, 0, $header_count );
			}
		}

		$this->logger->info( 'Save manual table request', array(
			'title'        => $title,
			'rows'         => count( $sanitized_data ),
			'columns'      => $header_count,
		) );

		// Create a mock ImportResult object.
		$import_result = (object) array(
			'success'      => true,
			'headers'      => $sanitized_headers,
			'data'         => $sanitized_data,
			'row_count'    => count( $sanitized_data ),
			'column_count' => $header_count,
		);

		// Create table with source_type='manual'.
		$result = $this->service->create_from_import( $title, $import_result, 'manual', $description );

		if ( $result['success'] ) {
			$this->send_success(
				array( 'table_id' => $result['table_id'] ),
				$result['message']
			);
		} else {
			$this->send_error( $result['message'], 400 );
		}
	}

	/**
	 * Sanitize display settings
	 *
	 * @param array $input Raw display settings.
	 * @return array Sanitized display settings
	 */
	private function sanitize_display_settings( $input ) {
		$sanitized = array();
		
		// Rows per page (1-100).
		if ( isset( $input['rows_per_page'] ) ) {
			$rows = intval( $input['rows_per_page'] );
			$sanitized['rows_per_page'] = max( 1, min( 100, $rows ) );
		}
		
		// Table style (whitelist).
		if ( isset( $input['table_style'] ) ) {
			$allowed_styles = array( 'default', 'striped', 'bordered', 'hover' );
			$style = Sanitizer::text( $input['table_style'] );
			if ( in_array( $style, $allowed_styles, true ) ) {
				$sanitized['table_style'] = $style;
			}
		}
		
		// Boolean settings.
		$boolean_keys = array( 'enable_search', 'enable_sorting', 'enable_pagination' );
		foreach ( $boolean_keys as $key ) {
			if ( isset( $input[ $key ] ) ) {
				// Handle string "false" and "true" from JavaScript
				if ( $input[ $key ] === 'false' || $input[ $key ] === '0' || $input[ $key ] === 0 || $input[ $key ] === false ) {
					$sanitized[ $key ] = false;
				} elseif ( $input[ $key ] === 'true' || $input[ $key ] === '1' || $input[ $key ] === 1 || $input[ $key ] === true ) {
					$sanitized[ $key ] = true;
				} else {
					// Default to boolean cast for other values
					$sanitized[ $key ] = (bool) $input[ $key ];
				}
			}
		}
		
		$this->logger->info( 'Display settings sanitized', array(
			'input'     => $input,
			'sanitized' => $sanitized,
		) );
		
		return $sanitized;
	}

	/**
	 * Verify nonce
	 *
	 * @return bool True if valid
	 */
	private function verify_nonce() {
		$nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : '';
		return Validator::nonce( $nonce, 'atables_admin_nonce' );
	}

	/**
	 * Send success JSON response
	 *
	 * @param mixed  $data    Response data.
	 * @param string $message Success message.
	 */
	private function send_success( $data = null, $message = '' ) {
		Helpers::send_json( true, $data, $message, 200 );
	}

	/**
	 * Send error JSON response
	 *
	 * @param string $message Error message.
	 * @param int    $code    HTTP status code.
	 */
	private function send_error( $message, $code = 400 ) {
		$this->logger->error( 'Table request failed', array(
			'error' => $message,
			'code'  => $code,
		) );

		Helpers::send_json( false, null, $message, $code );
	}
	
	/**
	 * Clear cache for a specific table
	 *
	 * @param int $table_id Table ID.
	 */
	private function clear_table_cache( $table_id ) {
		try {
			$cache_service = new CacheService();

			// Clear both cache keys for this table
			$cache_service->delete( 'table_all_data_' . $table_id );
			$cache_service->delete( 'table_' . $table_id );
			
			$this->logger->info( 'Cache cleared for table', array( 'table_id' => $table_id ) );
		} catch ( \Exception $e ) {
			$this->logger->error( 'Failed to clear table cache', array(
				'table_id' => $table_id,
				'error'    => $e->getMessage(),
			) );
		}
	}
}

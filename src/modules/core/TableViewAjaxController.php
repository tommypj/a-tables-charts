<?php
/**
 * Table View AJAX Controller
 *
 * Handles AJAX requests for table viewing functionality
 *
 * @package ATablesCharts\Core
 * @since 1.0.0
 */

namespace ATablesCharts\Core;

use ATablesCharts\Tables\Services\TableService;
use ATablesCharts\Tables\Repositories\TableRepository;
use ATablesCharts\Cache\Services\CacheService;
use ATablesCharts\Filters\Types\Filter;
use ATablesCharts\Filters\Services\FilterService;

/**
 * Table View AJAX Controller
 */
class TableViewAjaxController {

	/**
	 * Register AJAX hooks
	 */
	public function register_hooks() {
		add_action( 'wp_ajax_atables_load_table_data', array( $this, 'load_table_data' ) );
		add_action( 'wp_ajax_atables_load_all_table_data', array( $this, 'load_all_table_data' ) );
		add_action( 'wp_ajax_atables_filter_table_data', array( $this, 'filter_table_data' ) );
	}

	/**
	 * Load table data via AJAX
	 */
	public function load_table_data() {
		// Verify nonce
		check_ajax_referer( 'atables_admin_nonce', 'nonce' );

		// Check user permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'Insufficient permissions.' );
		}

		// Get parameters
		$table_id    = isset( $_POST['table_id'] ) ? (int) $_POST['table_id'] : 0;
		$per_page    = isset( $_POST['per_page'] ) ? (int) $_POST['per_page'] : 10;
		$page        = isset( $_POST['paged'] ) ? max( 1, (int) $_POST['paged'] ) : 1;
		$search      = isset( $_POST['search'] ) ? sanitize_text_field( $_POST['search'] ) : '';
		$sort_column = isset( $_POST['sort_column'] ) ? sanitize_text_field( $_POST['sort_column'] ) : '';
		$sort_order  = isset( $_POST['sort_order'] ) && in_array( strtolower( $_POST['sort_order'] ), array( 'asc', 'desc' ), true ) 
			? strtolower( $_POST['sort_order'] ) 
			: 'asc';

		// Validate table ID
		if ( empty( $table_id ) ) {
			wp_send_json_error( 'Invalid table ID.' );
		}

		$table_service = new TableService();
		$table = $table_service->get_table( $table_id );

		if ( ! $table ) {
			wp_send_json_error( 'Table not found.' );
		}

		$headers = $table->get_headers();

		// Get filtered and paginated data
		$table_repository = new \ATablesCharts\Tables\Repositories\TableRepository();
		$result = $table_repository->get_table_data_filtered( $table_id, array(
			'per_page'    => $per_page,
			'page'        => $page,
			'search'      => $search,
			'sort_column' => $sort_column,
			'sort_order'  => $sort_order,
		) );

		$data = $result['data'];
		$filtered_total = $result['total'];

		// Convert associative arrays to indexed arrays for consistency
		$rows_as_arrays = array();
		foreach ( $data as $row ) {
			$row_array = array();
			foreach ( $headers as $header ) {
				$row_array[] = isset( $row[ $header ] ) ? $row[ $header ] : '';
			}
			$rows_as_arrays[] = $row_array;
		}

		// Calculate pagination info
		$total_rows = $table->row_count;
		$total_pages = ceil( $filtered_total / $per_page );
		$start_row = $filtered_total > 0 ? ( ( $page - 1 ) * $per_page ) + 1 : 0;
		$end_row = min( $page * $per_page, $filtered_total );

		// Prepare response
		$response = array(
			'headers'   => $headers,
			'rows'      => $rows_as_arrays,
			'is_search' => ! empty( $search ),
			'pagination' => array(
				'current_page'   => $page,
				'per_page'       => $per_page,
				'total_pages'    => $total_pages,
				'total_rows'     => $total_rows,
				'filtered_total' => $filtered_total,
				'start_row'      => $start_row,
				'end_row'        => $end_row,
			),
		);

		wp_send_json_success( $response );
	}
	
	/**
	 * Filter table data with advanced filters via AJAX
	 */
	public function filter_table_data() {
		// Verify nonce
		check_ajax_referer( 'atables_admin_nonce', 'nonce' );

		// Check user permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'Insufficient permissions.' );
		}

		// Get parameters
		$table_id = isset( $_POST['table_id'] ) ? (int) $_POST['table_id'] : 0;
		$per_page = isset( $_POST['per_page'] ) ? (int) $_POST['per_page'] : 10;
		$page     = isset( $_POST['paged'] ) ? max( 1, (int) $_POST['paged'] ) : 1;
		$filters  = isset( $_POST['filters'] ) ? json_decode( stripslashes( $_POST['filters'] ), true ) : array();

		// Validate table ID
		if ( empty( $table_id ) ) {
			wp_send_json_error( 'Invalid table ID.' );
		}

		$table_service = new TableService();
		$table = $table_service->get_table( $table_id );

		if ( ! $table ) {
			wp_send_json_error( 'Table not found.' );
		}

		$headers = $table->get_headers();

		// Get all table data
		$table_repository = new TableRepository();
		$all_data = $table_repository->get_table_data( $table_id );

		// Map data to headers
		$mapped_data = array();
		foreach ( $all_data as $row ) {
			$mapped_row = array();
			foreach ( $row as $index => $value ) {
				if ( isset( $headers[ $index ] ) ) {
					$mapped_row[ $headers[ $index ] ] = $value;
				}
			}
			$mapped_data[] = $mapped_row;
		}

		// Apply filters if provided
		if ( ! empty( $filters ) ) {
			// Convert filter arrays to Filter objects
			$filter_objects = array();
			foreach ( $filters as $filter_data ) {
				if ( isset( $filter_data['column'] ) && isset( $filter_data['operator'] ) ) {
					// Create Filter object with array data
					$filter_objects[] = new Filter( array(
						'column'   => $filter_data['column'],
						'operator' => $filter_data['operator'],
						'value'    => isset( $filter_data['value'] ) ? $filter_data['value'] : '',
					) );
				}
			}

			// Apply filters
			if ( ! empty( $filter_objects ) ) {
				$filter_service = new FilterService();
				$mapped_data = $filter_service->apply_filters( $mapped_data, $filter_objects );
			}
		}

		// Get total after filtering
		$filtered_total = count( $mapped_data );

		// Apply pagination
		$offset = ( $page - 1 ) * $per_page;
		$paginated_data = array_slice( $mapped_data, $offset, $per_page );

		// Convert associative arrays back to indexed arrays for consistency
		$rows_as_arrays = array();
		foreach ( $paginated_data as $row ) {
			$row_array = array();
			foreach ( $headers as $header ) {
				$row_array[] = isset( $row[ $header ] ) ? $row[ $header ] : '';
			}
			$rows_as_arrays[] = $row_array;
		}

		// Calculate pagination info
		$total_rows = $table->row_count;
		$total_pages = ceil( $filtered_total / $per_page );
		$start_row = $filtered_total > 0 ? $offset + 1 : 0;
		$end_row = min( $offset + $per_page, $filtered_total );

		// Prepare response
		$response = array(
			'headers'   => $headers,
			'rows'      => $rows_as_arrays,
			'is_filtered' => ! empty( $filters ),
			'pagination' => array(
				'current_page'   => $page,
				'per_page'       => $per_page,
				'total_pages'    => $total_pages,
				'total_rows'     => $total_rows,
				'filtered_total' => $filtered_total,
				'start_row'      => $start_row,
				'end_row'        => $end_row,
			),
		);

		wp_send_json_success( $response );
	}
	
	/**
	 * Load ALL table data (for client-side filtering)
	 */
	public function load_all_table_data() {
		// Verify nonce
		check_ajax_referer( 'atables_admin_nonce', 'nonce' );

		// Check user permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'Insufficient permissions.' );
		}

		// Get parameters
		$table_id = isset( $_POST['table_id'] ) ? (int) $_POST['table_id'] : 0;

		// Validate table ID
		if ( empty( $table_id ) ) {
			wp_send_json_error( 'Invalid table ID.' );
		}

		// Check cache first
		$cache_service = new CacheService();
		$cache_key = 'table_all_data_' . $table_id;
		$cached_data = $cache_service->get( $cache_key );

		if ( false !== $cached_data ) {
			// Return cached data
			wp_send_json_success( $cached_data );
			return;
		}

		$table_service = new TableService();
		$table = $table_service->get_table( $table_id );

		if ( ! $table ) {
			wp_send_json_error( 'Table not found.' );
		}

		$headers = $table->get_headers();

		// Get ALL data (no pagination)
		$table_repository = new \ATablesCharts\Tables\Repositories\TableRepository();
		$data = $table_repository->get_table_data( $table_id );

		// Prepare response
		$response = array(
			'headers' => $headers,
			'rows'    => $data,
			'total'   => count( $data ),
		);

		// Cache the data
		$cache_service->set( $cache_key, $response );

		wp_send_json_success( $response );
	}
}

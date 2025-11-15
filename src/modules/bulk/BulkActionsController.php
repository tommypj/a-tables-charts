<?php
/**
 * Bulk Actions Controller
 *
 * Handles bulk operations on table rows
 *
 * @package ATablesCharts\Bulk\Controllers
 * @since 1.0.0
 */

namespace ATablesCharts\Bulk\Controllers;

use ATablesCharts\Tables\Repositories\TableRepository;
use ATablesCharts\Bulk\Services\BulkActionsService;

/**
 * BulkActionsController Class
 *
 * Responsibilities:
 * - Handle AJAX requests for bulk operations
 * - Validate input and permissions
 * - Delegate business logic to BulkActionsService
 * - Send JSON responses
 */
class BulkActionsController {

	/**
	 * Table repository
	 *
	 * @var TableRepository
	 */
	private $repository;

	/**
	 * Bulk actions service
	 *
	 * @var BulkActionsService
	 */
	private $service;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->repository = new TableRepository();
		$this->service = new BulkActionsService();
	}

	/**
	 * Register AJAX hooks
	 */
	public function register_hooks() {
		add_action( 'wp_ajax_atables_bulk_action', array( $this, 'handle_bulk_action' ) );
	}

	/**
	 * Handle bulk action AJAX request
	 */
	public function handle_bulk_action() {
		// Verify nonce
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'atables_admin_nonce' ) ) {
			wp_send_json_error( array(
				'message' => __( 'Security check failed.', 'a-tables-charts' ),
			) );
		}

		// Check permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array(
				'message' => __( 'You do not have permission to perform this action.', 'a-tables-charts' ),
			) );
		}

		// Get parameters
		$table_id = isset( $_POST['table_id'] ) ? intval( $_POST['table_id'] ) : 0;
		$action = isset( $_POST['bulk_action'] ) ? sanitize_text_field( $_POST['bulk_action'] ) : '';
		$data = isset( $_POST['data'] ) ? $_POST['data'] : array();

		// Validate table ID
		if ( ! $table_id ) {
			wp_send_json_error( array(
				'message' => __( 'Invalid table ID.', 'a-tables-charts' ),
			) );
		}

		// Get table
		$table = $this->repository->get_by_id( $table_id );
		if ( ! $table ) {
			wp_send_json_error( array(
				'message' => __( 'Table not found.', 'a-tables-charts' ),
			) );
		}

		// Perform action
		switch ( $action ) {
			case 'delete':
				$result = $this->bulk_delete( $table, $data );
				break;

			case 'duplicate':
				$result = $this->bulk_duplicate( $table, $data );
				break;

			case 'edit':
				$result = $this->bulk_edit( $table, $data );
				break;

			default:
				wp_send_json_error( array(
					'message' => __( 'Invalid action.', 'a-tables-charts' ),
				) );
		}

		if ( $result['success'] ) {
			wp_send_json_success( $result );
		} else {
			wp_send_json_error( $result );
		}
	}

	/**
	 * Bulk delete rows
	 *
	 * @param object $table Table object.
	 * @param array  $data  Action data.
	 * @return array Result
	 */
	private function bulk_delete( $table, $data ) {
		$row_indices = isset( $data['rows'] ) ? array_map( 'intval', $data['rows'] ) : array();

		// Get current table data
		$table_data = $table->get_data();

		// Use service to perform bulk delete
		$result = $this->service->bulk_delete( $table_data, $row_indices );

		if ( ! $result['success'] ) {
			return $result;
		}

		// Update table with new data
		$table->source_data['data'] = $result['data'];
		$table->row_count = count( $result['data'] );

		$updated = $this->repository->update( $table );

		if ( $updated ) {
			return array(
				'success' => true,
				'message' => $result['message'],
			);
		}

		return array(
			'success' => false,
			'message' => __( 'Failed to delete rows.', 'a-tables-charts' ),
		);
	}

	/**
	 * Bulk duplicate rows
	 *
	 * @param object $table Table object.
	 * @param array  $data  Action data.
	 * @return array Result
	 */
	private function bulk_duplicate( $table, $data ) {
		$row_indices = isset( $data['rows'] ) ? array_map( 'intval', $data['rows'] ) : array();

		// Get current table data
		$table_data = $table->get_data();

		// Use service to perform bulk duplicate
		$result = $this->service->bulk_duplicate( $table_data, $row_indices );

		if ( ! $result['success'] ) {
			return $result;
		}

		// Update table with new data
		$table->source_data['data'] = $result['data'];
		$table->row_count = count( $result['data'] );

		$updated = $this->repository->update( $table );

		if ( $updated ) {
			return array(
				'success' => true,
				'message' => $result['message'],
			);
		}

		return array(
			'success' => false,
			'message' => __( 'Failed to duplicate rows.', 'a-tables-charts' ),
		);
	}

	/**
	 * Bulk edit rows
	 *
	 * @param object $table Table object.
	 * @param array  $data  Action data.
	 * @return array Result
	 */
	private function bulk_edit( $table, $data ) {
		$row_indices = isset( $data['rows'] ) ? array_map( 'intval', $data['rows'] ) : array();
		$column = isset( $data['column'] ) ? sanitize_text_field( $data['column'] ) : '';
		$value = isset( $data['value'] ) ? sanitize_text_field( $data['value'] ) : '';

		// Get current table data and headers
		$table_data = $table->get_data();
		$headers = $table->get_headers();

		// Use service to perform bulk edit
		$result = $this->service->bulk_edit( $table_data, $headers, $row_indices, $column, $value );

		if ( ! $result['success'] ) {
			return $result;
		}

		// Update table with new data
		$table->source_data['data'] = $result['data'];

		$updated = $this->repository->update( $table );

		if ( $updated ) {
			return array(
				'success' => true,
				'message' => $result['message'],
			);
		}

		return array(
			'success' => false,
			'message' => __( 'Failed to update rows.', 'a-tables-charts' ),
		);
	}
}

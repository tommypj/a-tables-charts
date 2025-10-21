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

/**
 * BulkActionsController Class
 *
 * Responsibilities:
 * - Handle bulk delete
 * - Handle bulk duplicate
 * - Handle bulk edit
 * - Validate bulk operations
 */
class BulkActionsController {

	/**
	 * Table repository
	 *
	 * @var TableRepository
	 */
	private $repository;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->repository = new TableRepository();
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

		if ( empty( $row_indices ) ) {
			return array(
				'success' => false,
				'message' => __( 'No rows selected.', 'a-tables-charts' ),
			);
		}

		// Get current table data
		$table_data = $table->get_data();

		// Sort indices in descending order to avoid index shifting
		rsort( $row_indices );

		// Remove rows
		$deleted_count = 0;
		foreach ( $row_indices as $index ) {
			if ( isset( $table_data[ $index ] ) ) {
				unset( $table_data[ $index ] );
				$deleted_count++;
			}
		}

		// Reindex array
		$table_data = array_values( $table_data );

		// Update table
		$table->source_data['data'] = $table_data;
		$table->row_count = count( $table_data );

		$updated = $this->repository->update( $table );

		if ( $updated ) {
			return array(
				'success' => true,
				'message' => sprintf(
					_n(
						'%d row deleted successfully.',
						'%d rows deleted successfully.',
						$deleted_count,
						'a-tables-charts'
					),
					$deleted_count
				),
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

		if ( empty( $row_indices ) ) {
			return array(
				'success' => false,
				'message' => __( 'No rows selected.', 'a-tables-charts' ),
			);
		}

		// Get current table data
		$table_data = $table->get_data();

		// Duplicate rows (add copies at the end)
		$duplicated_count = 0;
		foreach ( $row_indices as $index ) {
			if ( isset( $table_data[ $index ] ) ) {
				$table_data[] = $table_data[ $index ];
				$duplicated_count++;
			}
		}

		// Update table
		$table->source_data['data'] = $table_data;
		$table->row_count = count( $table_data );

		$updated = $this->repository->update( $table );

		if ( $updated ) {
			return array(
				'success' => true,
				'message' => sprintf(
					_n(
						'%d row duplicated successfully.',
						'%d rows duplicated successfully.',
						$duplicated_count,
						'a-tables-charts'
					),
					$duplicated_count
				),
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

		if ( empty( $row_indices ) ) {
			return array(
				'success' => false,
				'message' => __( 'No rows selected.', 'a-tables-charts' ),
			);
		}

		if ( empty( $column ) ) {
			return array(
				'success' => false,
				'message' => __( 'No column selected.', 'a-tables-charts' ),
			);
		}

		// Get current table data
		$table_data = $table->get_data();
		$headers = $table->get_headers();

		// Verify column exists
		if ( ! in_array( $column, $headers, true ) ) {
			return array(
				'success' => false,
				'message' => __( 'Invalid column selected.', 'a-tables-charts' ),
			);
		}

		// Update rows
		$updated_count = 0;
		foreach ( $row_indices as $index ) {
			if ( isset( $table_data[ $index ] ) ) {
				$table_data[ $index ][ $column ] = $value;
				$updated_count++;
			}
		}

		// Update table
		$table->source_data['data'] = $table_data;

		$updated = $this->repository->update( $table );

		if ( $updated ) {
			return array(
				'success' => true,
				'message' => sprintf(
					_n(
						'%d row updated successfully.',
						'%d rows updated successfully.',
						$updated_count,
						'a-tables-charts'
					),
					$updated_count
				),
			);
		}

		return array(
			'success' => false,
			'message' => __( 'Failed to update rows.', 'a-tables-charts' ),
		);
	}
}

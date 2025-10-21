<?php
/**
 * Cell Merging Controller
 *
 * Handles cell merging operations via AJAX
 *
 * @package ATablesCharts\CellMerging\Controllers
 * @since 1.0.0
 */

namespace ATablesCharts\CellMerging\Controllers;

use ATablesCharts\CellMerging\Services\CellMergingService;
use ATablesCharts\Tables\Repositories\TableRepository;

/**
 * CellMergingController Class
 */
class CellMergingController {

	/**
	 * Cell merging service
	 *
	 * @var CellMergingService
	 */
	private $service;

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
		$this->service = new CellMergingService();
		$this->repository = new TableRepository();
	}

	/**
	 * Register AJAX hooks
	 */
	public function register_hooks() {
		add_action( 'wp_ajax_atables_save_cell_merges', array( $this, 'save_cell_merges' ) );
		add_action( 'wp_ajax_atables_get_cell_merges', array( $this, 'get_cell_merges' ) );
		add_action( 'wp_ajax_atables_auto_merge_cells', array( $this, 'auto_merge_cells' ) );
	}

	/**
	 * Save cell merge configuration
	 */
	public function save_cell_merges() {
		// Verify nonce
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'atables_admin_nonce' ) ) {
			wp_send_json_error( array(
				'message' => __( 'Security check failed.', 'a-tables-charts' ),
			) );
		}

		// Check permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array(
				'message' => __( 'Permission denied.', 'a-tables-charts' ),
			) );
		}

		$table_id = isset( $_POST['table_id'] ) ? intval( $_POST['table_id'] ) : 0;
		$merges = isset( $_POST['merges'] ) ? json_decode( stripslashes( $_POST['merges'] ), true ) : array();

		if ( ! $table_id ) {
			wp_send_json_error( array(
				'message' => __( 'Invalid table ID.', 'a-tables-charts' ),
			) );
		}

		// Save merges to table display settings
		$success = $this->repository->update_display_setting( $table_id, 'cell_merges', $merges );

		if ( $success ) {
			wp_send_json_success( array(
				'message' => __( 'Cell merges saved successfully!', 'a-tables-charts' ),
			) );
		} else {
			wp_send_json_error( array(
				'message' => __( 'Failed to save cell merges.', 'a-tables-charts' ),
			) );
		}
	}

	/**
	 * Get cell merge configuration
	 */
	public function get_cell_merges() {
		// Verify nonce
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'atables_admin_nonce' ) ) {
			wp_send_json_error( array(
				'message' => __( 'Security check failed.', 'a-tables-charts' ),
			) );
		}

		// Check permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array(
				'message' => __( 'Permission denied.', 'a-tables-charts' ),
			) );
		}

		$table_id = isset( $_POST['table_id'] ) ? intval( $_POST['table_id'] ) : 0;

		if ( ! $table_id ) {
			wp_send_json_error( array(
				'message' => __( 'Invalid table ID.', 'a-tables-charts' ),
			) );
		}

		$settings = $this->repository->get_display_settings( $table_id );
		$merges = isset( $settings['cell_merges'] ) ? $settings['cell_merges'] : array();

		wp_send_json_success( array(
			'merges' => $merges,
		) );
	}

	/**
	 * Auto-merge identical adjacent cells
	 */
	public function auto_merge_cells() {
		// Verify nonce
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'atables_admin_nonce' ) ) {
			wp_send_json_error( array(
				'message' => __( 'Security check failed.', 'a-tables-charts' ),
			) );
		}

		// Check permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array(
				'message' => __( 'Permission denied.', 'a-tables-charts' ),
			) );
		}

		$table_id = isset( $_POST['table_id'] ) ? intval( $_POST['table_id'] ) : 0;
		$column_config = isset( $_POST['column_config'] ) ? json_decode( stripslashes( $_POST['column_config'] ), true ) : array();

		if ( ! $table_id ) {
			wp_send_json_error( array(
				'message' => __( 'Invalid table ID.', 'a-tables-charts' ),
			) );
		}

		// Get table data
		$table = $this->repository->find_by_id( $table_id );
		if ( ! $table ) {
			wp_send_json_error( array(
				'message' => __( 'Table not found.', 'a-tables-charts' ),
			) );
		}

		$data = $table->get_data();

		// Auto-merge
		$merges = $this->service->auto_merge_identical( $data, $column_config );

		wp_send_json_success( array(
			'message' => sprintf(
				__( '%d cell merges created.', 'a-tables-charts' ),
				count( $merges )
			),
			'merges'  => $merges,
		) );
	}
}

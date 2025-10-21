<?php
/**
 * Formula Controller
 *
 * Handles formula operations via AJAX
 *
 * @package ATablesCharts\Formulas\Controllers
 * @since 1.0.0
 */

namespace ATablesCharts\Formulas\Controllers;

use ATablesCharts\Formulas\Services\FormulaService;
use ATablesCharts\Tables\Repositories\TableRepository;

/**
 * FormulaController Class
 */
class FormulaController {

	/**
	 * Formula service
	 *
	 * @var FormulaService
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
		$this->service = new FormulaService();
		$this->repository = new TableRepository();
	}

	/**
	 * Register AJAX hooks
	 */
	public function register_hooks() {
		add_action( 'wp_ajax_atables_calculate_formula', array( $this, 'calculate_formula' ) );
		add_action( 'wp_ajax_atables_save_formulas', array( $this, 'save_formulas' ) );
		add_action( 'wp_ajax_atables_get_formulas', array( $this, 'get_formulas' ) );
		add_action( 'wp_ajax_atables_validate_formula', array( $this, 'validate_formula' ) );
	}

	/**
	 * Calculate formula via AJAX
	 */
	public function calculate_formula() {
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
		$formula = isset( $_POST['formula'] ) ? sanitize_text_field( $_POST['formula'] ) : '';

		if ( ! $table_id ) {
			wp_send_json_error( array(
				'message' => __( 'Invalid table ID.', 'a-tables-charts' ),
			) );
		}

		if ( empty( $formula ) ) {
			wp_send_json_error( array(
				'message' => __( 'Formula is required.', 'a-tables-charts' ),
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
		$headers = $table->get_headers();

		// Calculate formula
		$result = $this->service->calculate_formula( $formula, $data, $headers );

		wp_send_json_success( array(
			'result' => $result,
		) );
	}

	/**
	 * Save formulas for table
	 */
	public function save_formulas() {
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
		$formulas = isset( $_POST['formulas'] ) ? json_decode( stripslashes( $_POST['formulas'] ), true ) : array();

		if ( ! $table_id ) {
			wp_send_json_error( array(
				'message' => __( 'Invalid table ID.', 'a-tables-charts' ),
			) );
		}

		// Save formulas to table display settings
		$success = $this->repository->update_display_setting( $table_id, 'formulas', $formulas );

		if ( $success ) {
			wp_send_json_success( array(
				'message' => __( 'Formulas saved successfully!', 'a-tables-charts' ),
			) );
		} else {
			wp_send_json_error( array(
				'message' => __( 'Failed to save formulas.', 'a-tables-charts' ),
			) );
		}
	}

	/**
	 * Get formulas for table
	 */
	public function get_formulas() {
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
		$formulas = isset( $settings['formulas'] ) ? $settings['formulas'] : array();

		wp_send_json_success( array(
			'formulas' => $formulas,
		) );
	}

	/**
	 * Validate formula
	 */
	public function validate_formula() {
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

		$formula = isset( $_POST['formula'] ) ? sanitize_text_field( $_POST['formula'] ) : '';

		if ( empty( $formula ) ) {
			wp_send_json_error( array(
				'message' => __( 'Formula is required.', 'a-tables-charts' ),
			) );
		}

		$result = $this->service->validate_formula( $formula );

		if ( $result['valid'] ) {
			wp_send_json_success( array(
				'message' => __( 'Formula is valid!', 'a-tables-charts' ),
			) );
		} else {
			wp_send_json_error( array(
				'message' => implode( ' ', $result['errors'] ),
				'errors'  => $result['errors'],
			) );
		}
	}
}

<?php
/**
 * Validation Controller
 *
 * Handles validation operations via AJAX with independent save functionality.
 *
 * @package ATablesCharts\Validation\Controllers
 * @since 1.0.0
 */

namespace ATablesCharts\Validation\Controllers;

use ATablesCharts\Validation\Services\ValidationService;
use ATablesCharts\Validation\Repositories\ValidationRepository;
use ATablesCharts\Tables\Repositories\TableRepository;

/**
 * ValidationController Class
 */
class ValidationController {

	/**
	 * Validation service
	 *
	 * @var ValidationService
	 */
	private $service;

	/**
	 * Validation repository (new modular storage)
	 *
	 * @var ValidationRepository
	 */
	private $validation_repository;

	/**
	 * Table repository (for backward compatibility)
	 *
	 * @var TableRepository
	 */
	private $table_repository;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->service = new ValidationService();
		$this->validation_repository = new ValidationRepository();
		$this->table_repository = new TableRepository();
	}

	/**
	 * Register AJAX hooks
	 */
	public function register_hooks() {
		add_action( 'wp_ajax_atables_validate_data', array( $this, 'validate_data' ) );
		add_action( 'wp_ajax_atables_save_validation_rules', array( $this, 'save_validation_rules' ) );
		add_action( 'wp_ajax_atables_get_validation_rules', array( $this, 'get_validation_rules' ) );
	}

	/**
	 * Validate table data via AJAX
	 */
	public function validate_data() {
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
		$rules = isset( $_POST['rules'] ) ? json_decode( stripslashes( $_POST['rules'] ), true ) : array();

		if ( ! $table_id ) {
			wp_send_json_error( array(
				'message' => __( 'Invalid table ID.', 'a-tables-charts' ),
			) );
		}

		// Get table data
		$table = $this->table_repository->find_by_id( $table_id );
		if ( ! $table ) {
			wp_send_json_error( array(
				'message' => __( 'Table not found.', 'a-tables-charts' ),
			) );
		}

		$data = $table->get_data();

		// Validate data
		$result = $this->service->validate( $data, $rules );

		if ( $result['valid'] ) {
			wp_send_json_success( array(
				'message' => __( 'All data is valid!', 'a-tables-charts' ),
				'errors'  => array(),
			) );
		} else {
			wp_send_json_error( array(
				'message' => sprintf(
					_n(
						'%d validation error found.',
						'%d validation errors found.',
						count( $result['errors'] ),
						'a-tables-charts'
					),
					count( $result['errors'] )
				),
				'errors'  => $result['errors'],
			) );
		}
	}

	/**
	 * Save validation rules for a table
	 */
	public function save_validation_rules() {
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
		$rules = isset( $_POST['rules'] ) ? json_decode( stripslashes( $_POST['rules'] ), true ) : array();

		if ( ! $table_id ) {
			wp_send_json_error( array(
				'message' => __( 'Invalid table ID.', 'a-tables-charts' ),
			) );
		}

		// Use new modular repository if table exists, otherwise fall back to old method
		if ( $this->validation_repository->table_exists() ) {
			$success = $this->validation_repository->save_rules( $table_id, $rules );
		} else {
			// Fallback to old method for backward compatibility
			$success = $this->table_repository->update_display_setting( $table_id, 'validation_rules', $rules );
		}

		if ( $success ) {
			wp_send_json_success( array(
				'message' => __( 'Validation rules saved successfully!', 'a-tables-charts' ),
			) );
		} else {
			wp_send_json_error( array(
				'message' => __( 'Failed to save validation rules.', 'a-tables-charts' ),
			) );
		}
	}

	/**
	 * Get validation rules for a table
	 */
	public function get_validation_rules() {
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

		// Use new modular repository if table exists, otherwise fall back to old method
		if ( $this->validation_repository->table_exists() ) {
			$rules = $this->validation_repository->get_rules( $table_id );
		} else {
			// Fallback to old method for backward compatibility
			$settings = $this->table_repository->get_display_settings( $table_id );
			$rules = isset( $settings['validation_rules'] ) ? $settings['validation_rules'] : array();
		}

		wp_send_json_success( array(
			'rules' => $rules,
		) );
	}
}

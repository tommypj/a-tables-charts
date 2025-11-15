<?php
/**
 * Enhanced Table Controller
 *
 * @package ATablesCharts
 */

namespace ATablesCharts\Core\Controllers;

use ATablesCharts\Tables\Repositories\TableRepository;
use ATablesCharts\Shared\Utils\Logger;

class EnhancedTableController {

	private $repository;

	public function __construct() {
		$this->repository = new TableRepository();
	}

	public function register_hooks() {
		add_action( 'wp_ajax_atables_save_enhanced_table', array( $this, 'save_enhanced_table' ) );
		add_action( 'wp_ajax_atables_apply_template', array( $this, 'apply_template' ) );
		add_action( 'wp_ajax_atables_get_table_settings', array( $this, 'get_table_settings' ) );
		add_action( 'wp_ajax_atables_save_table_settings', array( $this, 'save_table_settings' ) );
	}

	public function save_enhanced_table() {
		try {
			// Verify nonce
			check_ajax_referer( 'atables_admin_nonce', 'nonce' );
			
			// Validate required fields
			if ( ! isset( $_POST['table_id'] ) ) {
				wp_send_json_error( array( 'message' => 'Table ID is required' ) );
			}
			
			$table_id = intval( $_POST['table_id'] );
			$title = isset( $_POST['title'] ) ? sanitize_text_field( $_POST['title'] ) : '';
			$description = isset( $_POST['description'] ) ? sanitize_textarea_field( $_POST['description'] ) : '';
			$headers = isset( $_POST['headers'] ) && is_array( $_POST['headers'] ) ? array_map( 'sanitize_text_field', $_POST['headers'] ) : array();
			$data = isset( $_POST['data'] ) && is_array( $_POST['data'] ) ? $_POST['data'] : array();
			$settings_json = isset( $_POST['display_settings'] ) ? stripslashes( $_POST['display_settings'] ) : '{}';
			$settings = json_decode( $settings_json, true );
			
			if ( ! is_array( $settings ) ) {
				$settings = array();
			}

			// Sanitize data rows
			$sanitized_data = array();
			foreach ( $data as $row ) {
				if ( is_array( $row ) ) {
					$sanitized_data[] = array_map( 'sanitize_text_field', $row );
				}
			}

			// Update table metadata
			global $wpdb;
			$updated = $wpdb->update(
				$wpdb->prefix . 'atables_tables',
				array(
					'title' => $title,
					'description' => $description,
					'row_count' => count( $sanitized_data ),
					'column_count' => count( $headers ),
					'updated_at' => current_time( 'mysql' ),
				),
				array( 'id' => $table_id ),
				array( '%s', '%s', '%d', '%d', '%s' ),
				array( '%d' )
			);

			// Update table data
			$this->repository->update_table_data( $table_id, $headers, $sanitized_data );
			
			// Save display settings
			$this->repository->save_display_settings( $table_id, $settings );

			wp_send_json_success( array( 'message' => 'All changes saved successfully!' ) );
			
		} catch ( \Exception $e ) {
			Logger::log_error( 'Enhanced table save error', array( 'error' => $e->getMessage() ) );
			wp_send_json_error( array( 'message' => 'Error: ' . $e->getMessage() ) );
		}
	}

	public function apply_template() {
		try {
			check_ajax_referer( 'atables_admin_nonce', 'nonce' );

			$table_id = intval( $_POST['table_id'] );
			$template_id = sanitize_text_field( $_POST['template_id'] );

			require_once \ATABLES_PLUGIN_DIR . 'src/modules/templates/TemplateService.php';
			$service = new \ATablesCharts\Templates\Services\TemplateService();
			$templates = $service->get_templates();

			if ( isset( $templates[ $template_id ] ) ) {
				$this->repository->save_display_settings( $table_id, $templates[ $template_id ]['config'] );
				wp_send_json_success( array( 'message' => 'Template applied successfully!' ) );
			}

			wp_send_json_error( array( 'message' => 'Template not found' ) );

		} catch ( \Exception $e ) {
			Logger::log_error( 'Template apply error', array( 'error' => $e->getMessage() ) );
			wp_send_json_error( array( 'message' => 'Error: ' . $e->getMessage() ) );
		}
	}

	public function get_table_settings() {
		try {
			// Verify nonce
			check_ajax_referer( 'atables_admin_nonce', 'nonce' );

			// Validate required fields
			if ( ! isset( $_POST['table_id'] ) ) {
				wp_send_json_error( array( 'message' => 'Table ID is required' ) );
			}

			$table_id = intval( $_POST['table_id'] );

			// Get table display settings
			$settings = $this->repository->get_display_settings( $table_id );

			if ( ! $settings ) {
				$settings = array();
			}

			wp_send_json_success( $settings );

		} catch ( \Exception $e ) {
			Logger::log_error( 'Get table settings error', array( 'error' => $e->getMessage() ) );
			wp_send_json_error( array( 'message' => 'Error: ' . $e->getMessage() ) );
		}
	}

	public function save_table_settings() {
		try {
			// Verify nonce
			check_ajax_referer( 'atables_admin_nonce', 'nonce' );

			// Check permissions
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error( array( 'message' => 'Permission denied' ) );
			}

			// Validate required fields
			if ( ! isset( $_POST['table_id'] ) ) {
				wp_send_json_error( array( 'message' => 'Table ID is required' ) );
			}

			$table_id = intval( $_POST['table_id'] );
			$settings_json = isset( $_POST['settings'] ) ? stripslashes( $_POST['settings'] ) : '{}';
			$new_settings = json_decode( $settings_json, true );

			if ( ! is_array( $new_settings ) ) {
				wp_send_json_error( array( 'message' => 'Invalid settings format' ) );
			}

			// Get existing settings
			$existing_settings = $this->repository->get_display_settings( $table_id );
			if ( ! is_array( $existing_settings ) ) {
				$existing_settings = array();
			}

			// Merge new settings with existing ones
			$merged_settings = array_merge( $existing_settings, $new_settings );

			// Save merged settings
			$success = $this->repository->save_display_settings( $table_id, $merged_settings );

			if ( $success ) {
				wp_send_json_success( array( 'message' => 'Settings saved successfully!' ) );
			} else {
				wp_send_json_error( array( 'message' => 'Failed to save settings' ) );
			}

		} catch ( \Exception $e ) {
			Logger::log_error( 'Save table settings error', array( 'error' => $e->getMessage() ) );
			wp_send_json_error( array( 'message' => 'Error: ' . $e->getMessage() ) );
		}
	}
}

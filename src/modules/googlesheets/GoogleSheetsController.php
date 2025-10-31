<?php
/**
 * Google Sheets Import Controller
 *
 * Handles AJAX requests for Google Sheets import
 *
 * @package ATablesCharts\GoogleSheets\Controllers
 * @since 1.0.0
 */

namespace ATablesCharts\GoogleSheets\Controllers;

use ATablesCharts\GoogleSheets\Services\GoogleSheetsService;
use ATablesCharts\Tables\Types\Table;
use ATablesCharts\Tables\Repositories\TableRepository;

/**
 * GoogleSheetsController Class
 */
class GoogleSheetsController {

	/**
	 * Google Sheets service
	 *
	 * @var GoogleSheetsService
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
		$this->service = new GoogleSheetsService();
		$this->repository = new TableRepository();
	}

	/**
	 * Register AJAX hooks
	 */
	public function register_hooks() {
		add_action( 'wp_ajax_atables_test_google_sheets', array( $this, 'test_google_sheets' ) );
		add_action( 'wp_ajax_atables_import_google_sheets', array( $this, 'import_google_sheets' ) );
		add_action( 'wp_ajax_atables_sync_google_sheets', array( $this, 'sync_google_sheets' ) );
	}

	/**
	 * Test Google Sheets access
	 */
	public function test_google_sheets() {
		// Verify nonce
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'atables_import_nonce' ) ) {
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

		$url = isset( $_POST['url'] ) ? esc_url_raw( $_POST['url'] ) : '';

		if ( empty( $url ) ) {
			wp_send_json_error( array(
				'message' => __( 'Please provide a Google Sheets URL.', 'a-tables-charts' ),
			) );
		}

		$result = $this->service->test_access( $url );

		if ( $result['success'] ) {
			wp_send_json_success( $result );
		} else {
			wp_send_json_error( $result );
		}
	}

	/**
	 * Import from Google Sheets
	 */
	public function import_google_sheets() {
		// Verify nonce
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'atables_import_nonce' ) ) {
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

		$url = isset( $_POST['url'] ) ? esc_url_raw( $_POST['url'] ) : '';
		$title = isset( $_POST['title'] ) ? sanitize_text_field( $_POST['title'] ) : '';
		$auto_sync = isset( $_POST['auto_sync'] ) ? (bool) $_POST['auto_sync'] : false;

		if ( empty( $url ) ) {
			wp_send_json_error( array(
				'message' => __( 'Please provide a Google Sheets URL.', 'a-tables-charts' ),
			) );
		}

		// Import data
		$result = $this->service->import_from_url( $url );

		if ( ! $result['success'] ) {
			wp_send_json_error( array(
				'message' => $result['message'],
			) );
		}

		// Create table
		if ( empty( $title ) ) {
			$title = 'Google Sheets Import - ' . date( 'Y-m-d H:i' );
		}

		$table = new Table( array(
			'title'       => $title,
			'source_type' => 'google_sheets',
			'source_data' => array(
				'headers'    => $result['headers'],
				'data'       => $result['data'],
				'sheet_id'   => $result['sheet_id'],
				'source_url' => $result['source_url'],
				'auto_sync'  => $auto_sync,
				'last_sync'  => current_time( 'mysql' ),
			),
			'row_count'    => $result['row_count'],
			'column_count' => $result['column_count'],
			'created_by'   => get_current_user_id(),
		) );

		$table_id = $this->repository->create( $table );

		if ( $table_id ) {
			wp_send_json_success( array(
				'message'  => __( 'Google Sheet imported successfully!', 'a-tables-charts' ),
				'table_id' => $table_id,
				'redirect' => admin_url( 'admin.php?page=a-tables-charts-view&table_id=' . $table_id ),
			) );
		} else {
			wp_send_json_error( array(
				'message' => __( 'Failed to create table.', 'a-tables-charts' ),
			) );
		}
	}

	/**
	 * Sync Google Sheets data
	 */
	public function sync_google_sheets() {
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

		// Get table
		$table = $this->repository->get_by_id( $table_id );

		if ( ! $table ) {
			wp_send_json_error( array(
				'message' => __( 'Table not found.', 'a-tables-charts' ),
			) );
		}

		// Check if table is from Google Sheets
		if ( $table->source_type !== 'google_sheets' ) {
			wp_send_json_error( array(
				'message' => __( 'This table is not linked to Google Sheets.', 'a-tables-charts' ),
			) );
		}

		// Get source URL
		$source_url = isset( $table->source_data['source_url'] ) ? $table->source_data['source_url'] : '';

		if ( empty( $source_url ) ) {
			wp_send_json_error( array(
				'message' => __( 'Source URL not found.', 'a-tables-charts' ),
			) );
		}

		// Re-import data
		$result = $this->service->import_from_url( $source_url );

		if ( ! $result['success'] ) {
			wp_send_json_error( array(
				'message' => $result['message'],
			) );
		}

		// Update table data
		$table->source_data['headers'] = $result['headers'];
		$table->source_data['data'] = $result['data'];
		$table->source_data['last_sync'] = current_time( 'mysql' );
		$table->row_count = $result['row_count'];
		$table->column_count = $result['column_count'];

		$updated = $this->repository->update( $table );

		if ( $updated ) {
			wp_send_json_success( array(
				'message'   => __( 'Table synced successfully!', 'a-tables-charts' ),
				'last_sync' => current_time( 'mysql' ),
				'row_count' => $result['row_count'],
			) );
		} else {
			wp_send_json_error( array(
				'message' => __( 'Failed to update table.', 'a-tables-charts' ),
			) );
		}
	}
}

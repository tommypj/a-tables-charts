<?php
/**
 * Import Controller
 *
 * Handles import-related HTTP requests.
 *
 * @package ATablesCharts\Import\Controllers
 * @since 1.0.0
 */

namespace ATablesCharts\Import\Controllers;

use ATablesCharts\Import\Services\ExcelImportService;
use ATablesCharts\Import\Services\XmlImportService;
use ATablesCharts\Shared\Utils\Logger;
use ATablesCharts\Tables\Services\TableService;

/**
 * ImportController Class
 *
 * Handles file upload and import requests.
 */
class ImportController {

	/**
	 * Excel Import Service
	 *
	 * @var ExcelImportService
	 */
	private $excel_service;

	/**
	 * XML Import Service
	 *
	 * @var XmlImportService
	 */
	private $xml_service;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->excel_service = new ExcelImportService();
		$this->xml_service   = new XmlImportService();
	}

	/**
	 * Handle Excel file upload and preview
	 *
	 * @return void Outputs JSON response
	 */
	public function preview_excel() {
		// Verify nonce
		if ( ! check_ajax_referer( 'atables_import_nonce', 'nonce', false ) ) {
			wp_send_json_error(
				array( 'message' => __( 'Security check failed.', 'a-tables-charts' ) ),
				403
			);
		}

		// Check permissions
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error(
				array( 'message' => __( 'You do not have permission to import files.', 'a-tables-charts' ) ),
				403
			);
		}

		// Check if file was uploaded
		if ( empty( $_FILES['file'] ) ) {
			wp_send_json_error(
				array( 'message' => __( 'No file uploaded.', 'a-tables-charts' ) ),
				400
			);
		}

		$file = $_FILES['file'];

		// Check for upload errors
		if ( $file['error'] !== UPLOAD_ERR_OK ) {
			wp_send_json_error(
				array( 'message' => __( 'File upload failed.', 'a-tables-charts' ) ),
				400
			);
		}

		// Validate file type - check extension first, then MIME type
		$extension = strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) );
		
		if ( ! in_array( $extension, array( 'xlsx', 'xls' ) ) ) {
			wp_send_json_error(
				array( 'message' => __( 'Invalid file extension. Please upload an Excel file (.xlsx or .xls).', 'a-tables-charts' ) ),
				400
			);
		}
		
		// Optional: Check MIME type if available, but don't fail if it's not recognized
		$file_type = mime_content_type( $file['tmp_name'] );
		$allowed_types = array(
			'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			'application/vnd.ms-excel',
			'application/octet-stream', // Sometimes Excel files are detected as this
			'application/zip' // .xlsx files are actually zip files
		);
		
		// Log for debugging
		Logger::log_debug( 'Excel upload', array( 'file' => $file['name'], 'mime_type' => $file_type ) );

		// Get options
		$options = array(
			'has_headers' => isset( $_POST['has_headers'] ) && $_POST['has_headers'] === 'true',
			'sheet_index' => isset( $_POST['sheet_index'] ) ? (int) $_POST['sheet_index'] : 0,
		);

		// Get preview
		$result = $this->excel_service->preview( $file['tmp_name'], $options );

		if ( $result['success'] ) {
			// Store file temporarily for import
			$temp_file = $this->store_temp_file( $file );
			
			if ( $temp_file ) {
				$result['temp_file'] = basename( $temp_file );
			}
			
			wp_send_json_success( $result );
		} else {
			wp_send_json_error(
				array( 'message' => $result['message'] ),
				400
			);
		}
	}

	/**
	 * Import Excel file and create table
	 *
	 * @return void Outputs JSON response
	 */
	public function import_excel() {
		// Verify nonce
		if ( ! check_ajax_referer( 'atables_import_nonce', 'nonce', false ) ) {
			wp_send_json_error(
				array( 'message' => __( 'Security check failed.', 'a-tables-charts' ) ),
				403
			);
		}

		// Check permissions
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error(
				array( 'message' => __( 'You do not have permission to import files.', 'a-tables-charts' ) ),
				403
			);
		}

		// Get temp file
		$temp_file = isset( $_POST['temp_file'] ) ? sanitize_file_name( $_POST['temp_file'] ) : '';
		
		if ( empty( $temp_file ) ) {
			wp_send_json_error(
				array( 'message' => __( 'Temp file not found.', 'a-tables-charts' ) ),
				400
			);
		}

		$file_path = $this->get_temp_file_path( $temp_file );

		if ( ! file_exists( $file_path ) ) {
			wp_send_json_error(
				array( 'message' => __( 'Uploaded file not found. Please try again.', 'a-tables-charts' ) ),
				400
			);
		}

		// Get import options
		$options = array(
			'title'       => isset( $_POST['title'] ) ? sanitize_text_field( $_POST['title'] ) : '',
			'description' => isset( $_POST['description'] ) ? sanitize_textarea_field( $_POST['description'] ) : '',
			'has_headers' => isset( $_POST['has_headers'] ) && $_POST['has_headers'] === 'true',
			'sheet_index' => isset( $_POST['sheet_index'] ) ? (int) $_POST['sheet_index'] : 0,
		);

		// Import file
		$result = $this->excel_service->import( $file_path, $options );

		// Clean up temp file
		@unlink( $file_path );

		if ( $result['success'] ) {
			wp_send_json_success( $result );
		} else {
			wp_send_json_error(
				array( 'message' => $result['message'] ),
				400
			);
		}
	}

	/**
	 * Get available sheets from Excel file
	 *
	 * @return void Outputs JSON response
	 */
	public function get_excel_sheets() {
		// Verify nonce
		if ( ! check_ajax_referer( 'atables_import_nonce', 'nonce', false ) ) {
			wp_send_json_error(
				array( 'message' => __( 'Security check failed.', 'a-tables-charts' ) ),
				403
			);
		}

		// Check permissions
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error(
				array( 'message' => __( 'You do not have permission to import files.', 'a-tables-charts' ) ),
				403
			);
		}

		// Get temp file
		$temp_file = isset( $_POST['temp_file'] ) ? sanitize_file_name( $_POST['temp_file'] ) : '';
		
		if ( empty( $temp_file ) ) {
			wp_send_json_error(
				array( 'message' => __( 'Temp file not found.', 'a-tables-charts' ) ),
				400
			);
		}

		$file_path = $this->get_temp_file_path( $temp_file );

		if ( ! file_exists( $file_path ) ) {
			wp_send_json_error(
				array( 'message' => __( 'File not found.', 'a-tables-charts' ) ),
				400
			);
		}

		// Get sheets
		$result = $this->excel_service->get_sheets( $file_path );

		if ( $result['success'] ) {
			wp_send_json_success( $result );
		} else {
			wp_send_json_error(
				array( 'message' => $result['message'] ),
				400
			);
		}
	}

	/**
	 * Store uploaded file temporarily
	 *
	 * @param array $file Uploaded file array.
	 * @return string|false Temp file path or false on failure
	 */
	private function store_temp_file( $file ) {
		$upload_dir = wp_upload_dir();
		$temp_dir = $upload_dir['basedir'] . '/atables-temp';

		// Create temp directory if it doesn't exist
		if ( ! file_exists( $temp_dir ) ) {
			wp_mkdir_p( $temp_dir );
		}

		// Generate unique filename
		$filename = 'import_' . uniqid() . '_' . sanitize_file_name( $file['name'] );
		$temp_file = $temp_dir . '/' . $filename;

		// Move uploaded file to temp location
		if ( move_uploaded_file( $file['tmp_name'], $temp_file ) ) {
			return $temp_file;
		}

		return false;
	}

	/**
	 * Get temp file path
	 *
	 * @param string $filename Filename.
	 * @return string Full path to temp file
	 */
	private function get_temp_file_path( $filename ) {
		$upload_dir = wp_upload_dir();
		return $upload_dir['basedir'] . '/atables-temp/' . $filename;
	}

	/**
	 * Handle XML file upload and preview
	 *
	 * @return void Outputs JSON response
	 */
	public function preview_xml() {
		// Verify nonce.
		if ( ! check_ajax_referer( 'atables_import_nonce', 'nonce', false ) ) {
			wp_send_json_error(
				array( 'message' => __( 'Security check failed.', 'a-tables-charts' ) ),
				403
			);
		}

		// Check permissions.
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error(
				array( 'message' => __( 'You do not have permission to import files.', 'a-tables-charts' ) ),
				403
			);
		}

		// Check if file was uploaded.
		if ( empty( $_FILES['file'] ) ) {
			wp_send_json_error(
				array( 'message' => __( 'No file uploaded.', 'a-tables-charts' ) ),
				400
			);
		}

		$file = $_FILES['file'];

		// Check for upload errors.
		if ( $file['error'] !== UPLOAD_ERR_OK ) {
			wp_send_json_error(
				array( 'message' => __( 'File upload failed.', 'a-tables-charts' ) ),
				400
			);
		}

		// Validate file type.
		$extension = strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) );
		
		if ( 'xml' !== $extension ) {
			wp_send_json_error(
				array( 'message' => __( 'Invalid file extension. Please upload an XML file.', 'a-tables-charts' ) ),
				400
			);
		}

		// Get preview.
		$result = $this->xml_service->preview( $file['tmp_name'], 10 );

		if ( $result['success'] ) {
			// Store file temporarily for import.
			$temp_file = $this->store_temp_file( $file );
			
			if ( $temp_file ) {
				$result['temp_file'] = basename( $temp_file );
			}
			
			wp_send_json_success( $result );
		} else {
			wp_send_json_error(
				array( 'message' => $result['error'] ),
				400
			);
		}
	}

	/**
	 * Import XML file and create table
	 *
	 * @return void Outputs JSON response
	 */
	public function import_xml() {
		// Verify nonce.
		if ( ! check_ajax_referer( 'atables_import_nonce', 'nonce', false ) ) {
			wp_send_json_error(
				array( 'message' => __( 'Security check failed.', 'a-tables-charts' ) ),
				403
			);
		}

		// Check permissions.
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error(
				array( 'message' => __( 'You do not have permission to import files.', 'a-tables-charts' ) ),
				403
			);
		}

		// Get temp file.
		$temp_file = isset( $_POST['temp_file'] ) ? sanitize_file_name( $_POST['temp_file'] ) : '';
		
		if ( empty( $temp_file ) ) {
			wp_send_json_error(
				array( 'message' => __( 'Temp file not found.', 'a-tables-charts' ) ),
				400
			);
		}

		$file_path = $this->get_temp_file_path( $temp_file );

		if ( ! file_exists( $file_path ) ) {
			wp_send_json_error(
				array( 'message' => __( 'Uploaded file not found. Please try again.', 'a-tables-charts' ) ),
				400
			);
		}

		// Get table title.
		$title = isset( $_POST['title'] ) ? sanitize_text_field( $_POST['title'] ) : '';
		
		if ( empty( $title ) ) {
			$title = 'XML Import ' . date( 'Y-m-d H:i:s' );
		}

		try {
			// Import file.
			$import_result = $this->xml_service->import( $file_path );

			/**
			 * Action triggered before creating table from import
			 *
			 * @since 1.0.0
			 * @param string $title         Table title
			 * @param array  $import_result Import result data
			 * @param string $source_type   Source type (xml, excel, csv, etc.)
			 */
			do_action( 'atables_before_table_create_from_import', $title, $import_result, 'xml' );

			// Create the table.
			$table_service = new TableService();

			// Create table from import result.
			$result = $table_service->create_from_import( $title, $import_result, 'xml' );

			// Clean up temp file.
			@unlink( $file_path );

			if ( $result['success'] ) {
				/**
				 * Action triggered after table creation from import
				 *
				 * @since 1.0.0
				 * @param int    $table_id      Created table ID
				 * @param array  $import_result Import result data
				 * @param string $source_type   Source type (xml, excel, csv, etc.)
				 */
				do_action( 'atables_after_table_create_from_import', $result['table_id'], $import_result, 'xml' );

				wp_send_json_success(
					array(
						'table_id' => $result['table_id'],
						'message'  => $result['message'],
					)
				);
			} else {
				wp_send_json_error(
					array( 'message' => $result['message'] ),
					400
				);
			}
		} catch ( \Exception $e ) {
			// Clean up temp file.
			@unlink( $file_path );

			wp_send_json_error(
				array( 'message' => $e->getMessage() ),
				400
			);
		}
	}

	/**
	 * Get XML structure
	 *
	 * @return void Outputs JSON response
	 */
	public function get_xml_structure() {
		// Verify nonce.
		if ( ! check_ajax_referer( 'atables_import_nonce', 'nonce', false ) ) {
			wp_send_json_error(
				array( 'message' => __( 'Security check failed.', 'a-tables-charts' ) ),
				403
			);
		}

		// Check permissions.
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error(
				array( 'message' => __( 'You do not have permission to import files.', 'a-tables-charts' ) ),
				403
			);
		}

		// Get temp file.
		$temp_file = isset( $_POST['temp_file'] ) ? sanitize_file_name( $_POST['temp_file'] ) : '';
		
		if ( empty( $temp_file ) ) {
			wp_send_json_error(
				array( 'message' => __( 'Temp file not found.', 'a-tables-charts' ) ),
				400
			);
		}

		$file_path = $this->get_temp_file_path( $temp_file );

		if ( ! file_exists( $file_path ) ) {
			wp_send_json_error(
				array( 'message' => __( 'File not found.', 'a-tables-charts' ) ),
				400
			);
		}

		// Get structure.
		$result = $this->xml_service->get_structure( $file_path );

		wp_send_json_success( $result );
	}
}

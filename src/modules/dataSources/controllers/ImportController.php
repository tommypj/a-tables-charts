<?php
/**
 * Import Controller
 *
 * Handles AJAX requests for data import operations.
 *
 * @package ATablesCharts\DataSources\Controllers
 * @since 1.0.0
 */

namespace ATablesCharts\DataSources\Controllers;

use ATablesCharts\DataSources\Services\ImportService;
use ATablesCharts\Shared\Utils\Validator;
use ATablesCharts\Shared\Utils\Sanitizer;
use ATablesCharts\Shared\Utils\Helpers;
use ATablesCharts\Shared\Utils\Logger;

/**
 * ImportController Class
 *
 * Responsibilities:
 * - Handle file upload AJAX requests
 * - Validate requests and permissions
 * - Return JSON responses
 * - Handle errors gracefully
 */
class ImportController {

	/**
	 * Import service instance
	 *
	 * @var ImportService
	 */
	private $import_service;

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
		$this->import_service = new ImportService();
		$this->logger         = new Logger();
	}

	/**
	 * Register AJAX hooks
	 */
	public function register_hooks() {
		// File upload.
		add_action( 'wp_ajax_atables_import_file', array( $this, 'handle_file_import' ) );

		// URL import.
		add_action( 'wp_ajax_atables_import_url', array( $this, 'handle_url_import' ) );

		// Preview import.
		add_action( 'wp_ajax_atables_preview_import', array( $this, 'handle_preview_import' ) );
	}

	/**
	 * Handle file import AJAX request
	 */
	public function handle_file_import() {
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

		// Check if file was uploaded.
		if ( empty( $_FILES['file'] ) ) {
			$this->send_error( __( 'No file uploaded.', 'a-tables-charts' ), 400 );
			return;
		}

		$file = $_FILES['file'];

		// Get options.
		$options = $this->get_import_options();

		$this->logger->info( 'File import request', array(
			'filename' => $file['name'],
			'size'     => $file['size'],
			'user_id'  => get_current_user_id(),
		) );

		// Import file.
		$result = $this->import_service->import_from_upload( $file, $options );

		// Send response.
		if ( $result->success ) {
			$this->send_success( $result->to_array(), $result->message );
		} else {
			$this->send_error( $result->message, 400 );
		}
	}

	/**
	 * Handle URL import AJAX request
	 */
	public function handle_url_import() {
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

		// Get URL.
		$url = isset( $_POST['url'] ) ? Sanitizer::url( $_POST['url'] ) : '';

		if ( empty( $url ) ) {
			$this->send_error( __( 'URL is required.', 'a-tables-charts' ), 400 );
			return;
		}

		// Validate URL.
		if ( ! Validator::url( $url ) ) {
			$this->send_error( __( 'Invalid URL provided.', 'a-tables-charts' ), 400 );
			return;
		}

		// Get options.
		$options = $this->get_import_options();

		$this->logger->info( 'URL import request', array(
			'url'     => $url,
			'user_id' => get_current_user_id(),
		) );

		// Import from URL.
		$result = $this->import_service->import_from_url( $url, $options );

		// Send response.
		if ( $result->success ) {
			$this->send_success( $result->to_array(), $result->message );
		} else {
			$this->send_error( $result->message, 400 );
		}
	}

	/**
	 * Handle preview import AJAX request
	 *
	 * Returns only the first few rows for preview
	 */
	public function handle_preview_import() {
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

		// Check if file was uploaded.
		if ( empty( $_FILES['file'] ) ) {
			$this->send_error( __( 'No file uploaded.', 'a-tables-charts' ), 400 );
			return;
		}

		$file = $_FILES['file'];

		// Get options with max rows limit for preview.
		$options = $this->get_import_options();
		$options['max_rows'] = 10; // Preview only first 10 rows.

		$this->logger->info( 'Preview import request', array(
			'filename' => $file['name'],
		) );

		// Import file.
		$result = $this->import_service->import_from_upload( $file, $options );

		// Send response.
		if ( $result->success ) {
			// Add preview flag.
			$data = $result->to_array();
			$data['is_preview'] = true;
			$data['message']    = __( 'Preview of first 10 rows', 'a-tables-charts' );

			$this->send_success( $data );
		} else {
			$this->send_error( $result->message, 400 );
		}
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
	 * Get import options from request
	 *
	 * @return array Import options
	 */
	private function get_import_options() {
		$options = array();

		// CSV options.
		if ( isset( $_POST['delimiter'] ) ) {
			$options['delimiter'] = Sanitizer::text( $_POST['delimiter'] );
		}

		if ( isset( $_POST['has_header'] ) ) {
			$options['has_header'] = Sanitizer::boolean( $_POST['has_header'] );
		}

		if ( isset( $_POST['encoding'] ) ) {
			$options['encoding'] = Sanitizer::text( $_POST['encoding'] );
		}

		// JSON options.
		if ( isset( $_POST['array_key'] ) ) {
			$options['array_key'] = Sanitizer::text( $_POST['array_key'] );
		}

		if ( isset( $_POST['flatten_nested'] ) ) {
			$options['flatten_nested'] = Sanitizer::boolean( $_POST['flatten_nested'] );
		}

		return $options;
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
		$this->logger->error( 'Import request failed', array(
			'error' => $message,
			'code'  => $code,
		) );

		Helpers::send_json( false, null, $message, $code );
	}

	/**
	 * Get supported file types for display
	 *
	 * @return array File types info
	 */
	public function get_supported_file_types() {
		return array(
			'extensions'    => $this->import_service->get_supported_extensions(),
			'max_file_size' => $this->import_service->get_max_file_size_formatted(),
		);
	}
}

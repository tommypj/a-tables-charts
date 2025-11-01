<?php
/**
 * Export Controller
 *
 * Handles export-related HTTP requests.
 *
 * @package ATablesCharts\Export\Controllers
 * @since 1.0.0
 */

namespace ATablesCharts\Export\Controllers;

use ATablesCharts\Export\Services\CSVExportService;
use ATablesCharts\Export\Services\ExcelExportService;
use ATablesCharts\Export\Services\PdfExportService;
use ATablesCharts\Tables\Repositories\TableRepository;

/**
 * ExportController Class
 *
 * Responsibilities:
 * - Handle export requests
 * - Validate permissions
 * - Coordinate export process
 */
class ExportController {

	/**
	 * CSV Export Service
	 *
	 * @var CSVExportService
	 */
	private $csv_export_service;

	/**
	 * Excel Export Service
	 *
	 * @var ExcelExportService
	 */
	private $excel_export_service;

	/**
	 * PDF Export Service
	 *
	 * @var PdfExportService
	 */
	private $pdf_export_service;

	/**
	 * Table Repository
	 *
	 * @var TableRepository
	 */
	private $table_repository;

	/**
	 * Rate limit transient key prefix
	 *
	 * @var string
	 */
	private $rate_limit_prefix = 'atables_export_rate_limit_';

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->csv_export_service = new CSVExportService();
		$this->excel_export_service = new ExcelExportService();
		$this->pdf_export_service = new PdfExportService();
		$this->table_repository = new TableRepository();
	}

	/**
	 * Check rate limit for exports
	 *
	 * SECURITY: Prevents DoS attacks from mass exports
	 *
	 * @param int $table_id Table ID being exported.
	 * @return array Array with 'allowed' boolean and optional 'message' string.
	 */
	private function check_export_rate_limit( $table_id ) {
		// Use IP for unauthenticated, user ID for authenticated
		$identifier = is_user_logged_in() ? get_current_user_id() : $this->get_client_ip();
		$transient_key = $this->rate_limit_prefix . md5( $identifier . '_' . $table_id );

		// Get current export count
		$exports = get_transient( $transient_key );

		// Rate limit: 5 exports per hour per table for unauthenticated users
		// 20 exports per hour for authenticated users
		$max_exports = is_user_logged_in() ?
			apply_filters( 'atables_export_rate_limit_authenticated', 20 ) :
			apply_filters( 'atables_export_rate_limit_unauthenticated', 5 );

		$time_window = apply_filters( 'atables_export_rate_window', 3600 ); // 1 hour

		if ( false === $exports ) {
			// First export in this time window
			set_transient( $transient_key, 1, $time_window );
			return array(
				'allowed' => true,
				'remaining' => $max_exports - 1,
			);
		}

		if ( $exports >= $max_exports ) {
			// Rate limit exceeded
			if ( class_exists( 'ATablesCharts\Shared\Utils\Logger' ) ) {
				$logger = new \ATablesCharts\Shared\Utils\Logger();
				$logger->warning( 'Export rate limit exceeded', array(
					'identifier' => $identifier,
					'table_id' => $table_id,
					'exports' => $exports,
					'is_logged_in' => is_user_logged_in(),
				) );
			}

			return array(
				'allowed' => false,
				'message' => sprintf(
					/* translators: 1: number of exports, 2: time window in minutes */
					__( 'Export limit exceeded. Maximum %1$d exports allowed per %2$d minutes. Please try again later.', 'a-tables-charts' ),
					$max_exports,
					$time_window / 60
				),
			);
		}

		// Increment export count
		set_transient( $transient_key, $exports + 1, $time_window );

		return array(
			'allowed' => true,
			'remaining' => $max_exports - ( $exports + 1 ),
		);
	}

	/**
	 * Get client IP address
	 *
	 * @return string Client IP address.
	 */
	private function get_client_ip() {
		$ip = '';

		if ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$ip = sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) );
			$ip = explode( ',', $ip );
			$ip = trim( $ip[0] );
		} elseif ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
			$ip = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
		}

		return $ip;
	}

	/**
	 * Verify export permissions and nonce
	 *
	 * SECURITY: Ensures only authorized exports occur
	 * - Table-specific nonce validation
	 * - Public-only access for unauthenticated users
	 * - Rate limiting
	 * - Audit logging
	 *
	 * @param int $table_id Table ID.
	 * @param string $nonce Nonce to verify.
	 * @return array Array with 'allowed' boolean and optional 'message' string.
	 */
	private function verify_export_permissions( $table_id, $nonce ) {
		// Verify table-specific nonce
		$nonce_action = 'atables_export_table_' . $table_id;
		if ( ! wp_verify_nonce( $nonce, $nonce_action ) ) {
			// Log security violation
			if ( class_exists( 'ATablesCharts\Shared\Utils\Logger' ) ) {
				$logger = new \ATablesCharts\Shared\Utils\Logger();
				$logger->warning( 'Export nonce verification failed', array(
					'table_id' => $table_id,
					'ip' => $this->get_client_ip(),
					'user_id' => get_current_user_id(),
				) );
			}

			return array(
				'allowed' => false,
				'message' => __( 'Security check failed. Please refresh the page and try again.', 'a-tables-charts' ),
			);
		}

		// Get table to check if it exists and is public
		$table = $this->table_repository->find_by_id( $table_id );

		if ( ! $table ) {
			return array(
				'allowed' => false,
				'message' => __( 'Table not found.', 'a-tables-charts' ),
			);
		}

		// For unauthenticated users, only allow export of public (active) tables
		if ( ! is_user_logged_in() ) {
			if ( $table->status !== 'active' ) {
				// Log unauthorized access attempt
				if ( class_exists( 'ATablesCharts\Shared\Utils\Logger' ) ) {
					$logger = new \ATablesCharts\Shared\Utils\Logger();
					$logger->warning( 'Unauthenticated export attempt on non-public table', array(
						'table_id' => $table_id,
						'table_status' => $table->status,
						'ip' => $this->get_client_ip(),
					) );
				}

				return array(
					'allowed' => false,
					'message' => __( 'This table is not available for export.', 'a-tables-charts' ),
				);
			}
		}

		// Check rate limit
		$rate_limit = $this->check_export_rate_limit( $table_id );
		if ( ! $rate_limit['allowed'] ) {
			return array(
				'allowed' => false,
				'message' => $rate_limit['message'],
			);
		}

		// Log successful export authorization
		if ( class_exists( 'ATablesCharts\Shared\Utils\Logger' ) ) {
			$logger = new \ATablesCharts\Shared\Utils\Logger();
			$logger->info( 'Export authorized', array(
				'table_id' => $table_id,
				'user_id' => get_current_user_id(),
				'is_logged_in' => is_user_logged_in(),
				'rate_limit_remaining' => $rate_limit['remaining'],
			) );
		}

		return array(
			'allowed' => true,
			'table' => $table,
			'rate_limit_remaining' => $rate_limit['remaining'],
		);
	}

	/**
	 * Main export handler - routes to correct format
	 *
	 * @return void Outputs file or JSON error
	 */
	public function export_table() {
		// Get parameters from GET or POST
		$format = $this->get_param('format', 'csv');
		
		// Route to correct export method
		switch ($format) {
			case 'xlsx':
			case 'excel':
				$this->export_table_excel();
				break;
			case 'pdf':
				$this->export_table_pdf();
				break;
			case 'csv':
			default:
				$this->export_table_csv();
				break;
		}
	}
	
	/**
	 * Get parameter from GET or POST
	 *
	 * @param string $key Parameter key.
	 * @param mixed  $default Default value.
	 * @return mixed Parameter value
	 */
	private function get_param($key, $default = '') {
		if (isset($_GET[$key])) {
			return sanitize_text_field($_GET[$key]);
		}
		if (isset($_POST[$key])) {
			return sanitize_text_field($_POST[$key]);
		}
		return $default;
	}
	
	/**
	 * Export table to CSV
	 *
	 * SECURITY: Enhanced security with table-specific nonce, rate limiting, and public-only access
	 *
	 * @return void Outputs CSV file or error
	 */
	private function export_table_csv() {
		// Get table ID and nonce
		$table_id = (int) $this->get_param( 'table_id', 0 );
		$nonce = $this->get_param( 'nonce' );

		if ( empty( $table_id ) ) {
			wp_die(
				esc_html__( 'Table ID is required.', 'a-tables-charts' ),
				esc_html__( 'Invalid Request', 'a-tables-charts' ),
				array( 'response' => 400 )
			);
		}

		// Verify export permissions (nonce, rate limit, public access)
		$permission_check = $this->verify_export_permissions( $table_id, $nonce );

		if ( ! $permission_check['allowed'] ) {
			wp_die(
				esc_html( $permission_check['message'] ),
				esc_html__( 'Export Not Allowed', 'a-tables-charts' ),
				array( 'response' => 403 )
			);
		}

		$table = $permission_check['table'];

		// Get filter parameters.
		$search = $this->get_param('search', '');
		$sort_column = $this->get_param('sort_column', '');
		$sort_order = $this->get_param('sort_order', 'asc');

		// Get filtered data (all rows, no pagination).
		$result = $this->table_repository->get_table_data_filtered($table_id, array(
			'per_page'    => 0, // Get all rows
			'page'        => 1,
			'search'      => $search,
			'sort_column' => $sort_column,
			'sort_order'  => $sort_order,
		));

		$headers = $table->get_headers();
		$data = $result['data'];

		// Validate export data.
		$validation = $this->csv_export_service->validate($headers, $data);
		if (!$validation['valid']) {
			wp_die(implode(' ', $validation['errors']), 'Export Error', array('response' => 400));
		}

		// Generate filename.
		$filename = $this->csv_export_service->get_safe_filename($table->title);

		// Export to CSV (this will exit).
		$this->csv_export_service->export($headers, $data, $filename);
	}
	
	/**
	 * Export table to Excel
	 *
	 * SECURITY: Enhanced security with table-specific nonce, rate limiting, and public-only access
	 *
	 * @return void Outputs Excel file or error
	 */
	private function export_table_excel() {
		// Get table ID and nonce
		$table_id = (int) $this->get_param( 'table_id', 0 );
		$nonce = $this->get_param( 'nonce' );

		if ( empty( $table_id ) ) {
			wp_die(
				esc_html__( 'Table ID is required.', 'a-tables-charts' ),
				esc_html__( 'Invalid Request', 'a-tables-charts' ),
				array( 'response' => 400 )
			);
		}

		// Verify export permissions (nonce, rate limit, public access)
		$permission_check = $this->verify_export_permissions( $table_id, $nonce );

		if ( ! $permission_check['allowed'] ) {
			wp_die(
				esc_html( $permission_check['message'] ),
				esc_html__( 'Export Not Allowed', 'a-tables-charts' ),
				array( 'response' => 403 )
			);
		}

		$table = $permission_check['table'];

		// Get filter parameters.
		$search = $this->get_param('search', '');
		$sort_column = $this->get_param('sort_column', '');
		$sort_order = $this->get_param('sort_order', 'asc');

		// Get filtered data.
		$result = $this->table_repository->get_table_data_filtered($table_id, array(
			'per_page'    => 0,
			'page'        => 1,
			'search'      => $search,
			'sort_column' => $sort_column,
			'sort_order'  => $sort_order,
		));

		$data = $result['data'];

		// Export to Excel.
		$file_path = $this->excel_export_service->export($table, $data);

		if (!$file_path || !file_exists($file_path)) {
			wp_die(__('Failed to generate Excel file.', 'a-tables-charts'), 'Export Error', array('response' => 500));
		}

		// Send file.
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
		header('Content-Length: ' . filesize($file_path));
		header('Cache-Control: max-age=0');

		readfile($file_path);

		// Clean up.
		@unlink($file_path);
		exit;
	}
	
	/**
	 * Export table to PDF
	 *
	 * SECURITY: Enhanced security with table-specific nonce, rate limiting, and public-only access
	 *
	 * @return void Outputs PDF file or error
	 */
	private function export_table_pdf() {
		// Get table ID and nonce
		$table_id = (int) $this->get_param( 'table_id', 0 );
		$nonce = $this->get_param( 'nonce' );

		if ( empty( $table_id ) ) {
			wp_die(
				esc_html__( 'Table ID is required.', 'a-tables-charts' ),
				esc_html__( 'Invalid Request', 'a-tables-charts' ),
				array( 'response' => 400 )
			);
		}

		// Verify export permissions (nonce, rate limit, public access)
		$permission_check = $this->verify_export_permissions( $table_id, $nonce );

		if ( ! $permission_check['allowed'] ) {
			wp_die(
				esc_html( $permission_check['message'] ),
				esc_html__( 'Export Not Allowed', 'a-tables-charts' ),
				array( 'response' => 403 )
			);
		}

		$table = $permission_check['table'];

		// Get filter parameters
		$search = $this->get_param('search', '');
		$sort_column = $this->get_param('sort_column', '');
		$sort_order = $this->get_param('sort_order', 'asc');

		// Get filtered data (all rows, no pagination)
		$result = $this->table_repository->get_table_data_filtered($table_id, array(
			'per_page'    => 0, // Get all rows
			'page'        => 1,
			'search'      => $search,
			'sort_column' => $sort_column,
			'sort_order'  => $sort_order,
		));

		$headers = $table->get_headers();
		$data = $result['data'];

		// Validate export data
		$validation = $this->pdf_export_service->validate($headers, $data);
		if (!$validation['valid']) {
			wp_die(
				implode(' ', $validation['errors']),
				'Export Error',
				array('response' => 400)
			);
		}

		// Generate filename
		$filename = $this->pdf_export_service->get_safe_filename($table->title);

		// Get orientation preference
		$orientation = $this->get_param('orientation', 'auto');

		// Prepare export options
		$options = array(
			'orientation' => $orientation,
			'title'       => $table->title,
		);

		// Export to PDF (this will exit)
		$this->pdf_export_service->export($headers, $data, $filename, $options);
	}

	/**
	 * Get export URL for a table
	 *
	 * SECURITY: Generates table-specific nonce for enhanced security
	 *
	 * @param int    $table_id Table ID.
	 * @param string $format Export format (csv, xlsx, pdf).
	 * @param array  $filters Optional filters.
	 * @return string Export URL with table-specific nonce
	 */
	public function get_export_url( $table_id, $format = 'csv', $filters = array() ) {
		// Create table-specific nonce
		$nonce_action = 'atables_export_table_' . $table_id;

		$params = array(
			'action'   => 'atables_export_table',
			'table_id' => $table_id,
			'format'   => $format,
			'nonce'    => wp_create_nonce( $nonce_action ),
		);

		if ( ! empty( $filters['search'] ) ) {
			$params['search'] = $filters['search'];
		}

		if ( ! empty( $filters['sort_column'] ) ) {
			$params['sort_column'] = $filters['sort_column'];
			$params['sort_order'] = $filters['sort_order'] ?? 'asc';
		}

		return add_query_arg( $params, admin_url( 'admin-ajax.php' ) );
	}
}

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
	 * Constructor
	 */
	public function __construct() {
		$this->csv_export_service = new CSVExportService();
		$this->excel_export_service = new ExcelExportService();
		$this->pdf_export_service = new PdfExportService();
		$this->table_repository = new TableRepository();
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
	 * @return void Outputs CSV file or JSON error
	 */
	private function export_table_csv() {
		// Verify nonce.
		$nonce = $this->get_param('nonce');
		if (!wp_verify_nonce($nonce, 'atables_export_nonce')) {
			wp_die(__('Security check failed.', 'a-tables-charts'), 'Security Error', array('response' => 403));
		}

		// Get table ID.
		$table_id = (int) $this->get_param('table_id', 0);

		if (empty($table_id)) {
			wp_die(__('Table ID is required.', 'a-tables-charts'), 'Invalid Request', array('response' => 400));
		}

		// Get the table.
		$table = $this->table_repository->find_by_id($table_id);

		if (!$table) {
			wp_die(__('Table not found.', 'a-tables-charts'), 'Not Found', array('response' => 404));
		}

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
	 * @return void Outputs Excel file or JSON error
	 */
	private function export_table_excel() {
		// Verify nonce.
		$nonce = $this->get_param('nonce');
		if (!wp_verify_nonce($nonce, 'atables_export_nonce')) {
			wp_die(__('Security check failed.', 'a-tables-charts'), 'Security Error', array('response' => 403));
		}

		// Get table ID.
		$table_id = (int) $this->get_param('table_id', 0);

		if (empty($table_id)) {
			wp_die(__('Table ID is required.', 'a-tables-charts'), 'Invalid Request', array('response' => 400));
		}

		// Get the table.
		$table = $this->table_repository->find_by_id($table_id);

		if (!$table) {
			wp_die(__('Table not found.', 'a-tables-charts'), 'Not Found', array('response' => 404));
		}

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
	 * @return void Outputs PDF file or error
	 */
	private function export_table_pdf() {
		// Verify nonce
		$nonce = $this->get_param('nonce');
		if (!wp_verify_nonce($nonce, 'atables_export_nonce')) {
			wp_die(
				__('Security check failed.', 'a-tables-charts'),
				'Security Error',
				array('response' => 403)
			);
		}

		// Get table ID
		$table_id = (int) $this->get_param('table_id', 0);
		if (empty($table_id)) {
			wp_die(
				__('Table ID is required.', 'a-tables-charts'),
				'Invalid Request',
				array('response' => 400)
			);
		}

		// Get the table
		$table = $this->table_repository->find_by_id($table_id);
		if (!$table) {
			wp_die(
				__('Table not found.', 'a-tables-charts'),
				'Not Found',
				array('response' => 404)
			);
		}

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
	 * @param int    $table_id Table ID.
	 * @param string $format Export format (csv, xlsx, pdf).
	 * @param array  $filters Optional filters.
	 * @return string Export URL
	 */
	public function get_export_url($table_id, $format = 'csv', $filters = array()) {
		$params = array(
			'action'   => 'atables_export_table',
			'table_id' => $table_id,
			'format'   => $format,
			'nonce'    => wp_create_nonce('atables_export_nonce'),
		);

		if (!empty($filters['search'])) {
			$params['search'] = $filters['search'];
		}

		if (!empty($filters['sort_column'])) {
			$params['sort_column'] = $filters['sort_column'];
			$params['sort_order'] = $filters['sort_order'] ?? 'asc';
		}

		return add_query_arg($params, admin_url('admin-ajax.php'));
	}
}

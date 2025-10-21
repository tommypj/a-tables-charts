<?php
/**
 * Excel Export Service
 *
 * Business logic for Excel export operations.
 *
 * @package ATablesCharts\Export\Services
 * @since 1.0.0
 */

namespace ATablesCharts\Export\Services;

use ATablesCharts\Export\Exporters\ExcelExporter;
use ATablesCharts\Tables\Types\Table;

/**
 * ExcelExportService Class
 *
 * Responsibilities:
 * - Validate export data
 * - Generate Excel files
 * - Handle file output
 */
class ExcelExportService {

	/**
	 * Excel Exporter
	 *
	 * @var ExcelExporter
	 */
	private $exporter;

	/**
	 * Constructor
	 */
	public function __construct() {
		// Load exporter only if PhpSpreadsheet is available
		if ( file_exists( ATABLES_PLUGIN_DIR . 'vendor/autoload.php' ) ) {
			require_once ATABLES_PLUGIN_DIR . 'vendor/autoload.php';
		}
		
		$this->exporter = new ExcelExporter();
	}

	/**
	 * Export table data to Excel format
	 *
	 * @param Table $table Table object.
	 * @param array $data  Table data.
	 * @return string|false Path to generated file or false on failure
	 */
	public function export( $table, $data ) {
		$headers = $table->get_headers();
		$filename = $this->get_safe_filename( $table->title ) . '.xlsx';

		// Export directly to browser
		$this->exporter->export( $headers, $data, $filename );
		
		// Note: export() method exits, so this line won't be reached
		return false;
	}

	/**
	 * Generate safe filename from table title
	 *
	 * @param string $title Table title.
	 * @return string Safe filename
	 */
	public function get_safe_filename( $title ) {
		// Remove special characters.
		$filename = preg_replace( '/[^a-zA-Z0-9-_ ]/', '', $title );
		
		// Replace spaces with hyphens.
		$filename = str_replace( ' ', '-', $filename );
		
		// Convert to lowercase.
		$filename = strtolower( $filename );
		
		// Limit length.
		$filename = substr( $filename, 0, 50 );
		
		// Add timestamp.
		$filename .= '-' . date( 'Y-m-d' );
		
		return $filename;
	}

	/**
	 * Validate export data
	 *
	 * @param array $headers Column headers.
	 * @param array $data    Table data.
	 * @return array Validation result
	 */
	public function validate( $headers, $data ) {
		$errors = array();

		if ( empty( $headers ) ) {
			$errors[] = __( 'Headers are required for export.', 'a-tables-charts' );
		}

		if ( ! is_array( $headers ) ) {
			$errors[] = __( 'Headers must be an array.', 'a-tables-charts' );
		}

		if ( ! is_array( $data ) ) {
			$errors[] = __( 'Data must be an array.', 'a-tables-charts' );
		}

		return array(
			'valid'  => empty( $errors ),
			'errors' => $errors,
		);
	}
}

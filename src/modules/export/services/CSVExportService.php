<?php
/**
 * CSV Export Service
 *
 * Business logic for CSV export operations.
 *
 * @package ATablesCharts\Export\Services
 * @since 1.0.0
 */

namespace ATablesCharts\Export\Services;

use ATablesCharts\Export\Exporters\CSVExporter;

/**
 * CSVExportService Class
 *
 * Responsibilities:
 * - Validate export data
 * - Generate CSV files
 * - Handle file output
 */
class CSVExportService {

	/**
	 * CSV Exporter
	 *
	 * @var CSVExporter
	 */
	private $exporter;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->exporter = new CSVExporter();
	}

	/**
	 * Export data to CSV format
	 *
	 * @param array  $headers  Column headers.
	 * @param array  $data     Table data.
	 * @param string $filename Filename for export.
	 * @return void Outputs CSV file
	 */
	public function export( $headers, $data, $filename ) {
		$filename = $this->ensure_csv_extension( $filename );
		$this->exporter->export( $headers, $data, $filename );
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

	/**
	 * Generate safe filename from table title
	 *
	 * @param string $title Table title.
	 * @return string Safe filename with .csv extension
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
		
		// Ensure .csv extension.
		return $this->ensure_csv_extension( $filename );
	}

	/**
	 * Ensure filename has .csv extension
	 *
	 * @param string $filename Filename.
	 * @return string Filename with .csv extension
	 */
	private function ensure_csv_extension( $filename ) {
		if ( substr( $filename, -4 ) !== '.csv' ) {
			$filename .= '.csv';
		}
		return $filename;
	}
}

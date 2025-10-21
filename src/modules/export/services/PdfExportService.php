<?php
/**
 * PDF Export Service
 *
 * Business logic for PDF export operations.
 *
 * @package ATablesCharts\Export\Services
 * @since 1.0.0
 */

namespace ATablesCharts\Export\Services;

use ATablesCharts\Export\Exporters\PdfExporter;

/**
 * PdfExportService Class
 *
 * Responsibilities:
 * - Validate export data
 * - Generate PDF files
 * - Handle file output
 * - Apply business rules
 */
class PdfExportService {

	/**
	 * PDF Exporter
	 *
	 * @var PdfExporter
	 */
	private $exporter;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->exporter = new PdfExporter();
	}

	/**
	 * Export data to PDF format
	 *
	 * @param array  $headers  Column headers.
	 * @param array  $data     Table data.
	 * @param string $filename Filename for export.
	 * @param array  $options  Export options (orientation, title, font_size).
	 * @return void Outputs PDF file
	 */
	public function export( $headers, $data, $filename, $options = array() ) {
		// Ensure .pdf extension
		$filename = $this->ensure_pdf_extension( $filename );

		// Get settings
		$settings = $this->get_export_settings();

		// Merge options with settings
		$export_options = array_merge(
			array(
				'orientation' => $settings['orientation'],
				'font_size'   => $settings['font_size'],
				'title'       => $options['title'] ?? 'Table Export',
			),
			$options
		);

		// Add headers to options for orientation detection
		$export_options['headers'] = $headers;

		// Perform export
		$this->exporter->export( $headers, $data, $filename, $export_options );
	}

	/**
	 * Validate export data
	 *
	 * @param array $headers Column headers.
	 * @param array $data    Table data.
	 * @return array Validation result with 'valid' and 'errors' keys
	 */
	public function validate( $headers, $data ) {
		$errors = array();

		// Check headers
		if ( empty( $headers ) ) {
			$errors[] = __( 'Headers are required for export.', 'a-tables-charts' );
		}

		if ( ! is_array( $headers ) ) {
			$errors[] = __( 'Headers must be an array.', 'a-tables-charts' );
		}

		// Check data
		if ( ! is_array( $data ) ) {
			$errors[] = __( 'Data must be an array.', 'a-tables-charts' );
		}

		// Check row count limit
		$max_rows = $this->get_max_rows();
		if ( count( $data ) > $max_rows ) {
			$errors[] = sprintf(
				/* translators: %d: maximum number of rows allowed */
				__( 'Table is too large for PDF export (maximum %d rows). Please use Excel export for large datasets.', 'a-tables-charts' ),
				$max_rows
			);
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
	 * @return string Safe filename with .pdf extension
	 */
	public function get_safe_filename( $title ) {
		// Remove special characters
		$filename = preg_replace( '/[^a-zA-Z0-9-_ ]/', '', $title );

		// Replace spaces with hyphens
		$filename = str_replace( ' ', '-', $filename );

		// Convert to lowercase
		$filename = strtolower( $filename );

		// Limit length
		$filename = substr( $filename, 0, 50 );

		// Add timestamp
		$filename .= '-' . date( 'Y-m-d' );

		// Ensure .pdf extension
		return $this->ensure_pdf_extension( $filename );
	}

	/**
	 * Ensure filename has .pdf extension
	 *
	 * @param string $filename Filename.
	 * @return string Filename with .pdf extension
	 */
	private function ensure_pdf_extension( $filename ) {
		if ( substr( $filename, -4 ) !== '.pdf' ) {
			$filename .= '.pdf';
		}
		return $filename;
	}

	/**
	 * Get PDF export settings
	 *
	 * @return array Export settings
	 */
	private function get_export_settings() {
		// Get settings from Settings Service
		$settings = get_option( 'atables_settings', array() );
		$orientation = isset( $settings['pdf_page_orientation'] ) ? $settings['pdf_page_orientation'] : 'auto';
		$font_size   = isset( $settings['pdf_font_size'] ) ? $settings['pdf_font_size'] : 9;

		return array(
			'orientation' => $orientation,
			'font_size'   => (int) $font_size,
		);
	}

	/**
	 * Get maximum rows allowed for PDF export
	 *
	 * @return int Maximum rows
	 */
	private function get_max_rows() {
		$settings = get_option( 'atables_settings', array() );
		$max_rows = isset( $settings['pdf_max_rows'] ) ? $settings['pdf_max_rows'] : 5000;
		return (int) $max_rows;
	}

	/**
	 * Estimate PDF file size
	 *
	 * Rough estimation based on data size.
	 *
	 * @param array $data Table data.
	 * @return int Estimated size in bytes
	 */
	public function estimate_size( $data ) {
		// Very rough estimation: ~500 bytes per row
		$row_count = count( $data );
		$estimated_size = $row_count * 500;

		// Add base PDF overhead (~50KB)
		$estimated_size += 50000;

		return $estimated_size;
	}

	/**
	 * Check if export is considered large
	 *
	 * @param int $row_count Number of rows.
	 * @return bool True if large export
	 */
	public function is_large_export( $row_count ) {
		// Consider exports over 1000 rows as "large"
		return $row_count > 1000;
	}

	/**
	 * Prepare data for export
	 *
	 * Sanitizes and formats data before export.
	 *
	 * @param array $data    Table data.
	 * @param array $headers Column headers.
	 * @return array Prepared data
	 */
	private function prepare_data( $data, $headers ) {
		$prepared = array();

		foreach ( $data as $row ) {
			$prepared_row = array();

			foreach ( $headers as $header ) {
				// Get value or empty string
				$value = isset( $row[ $header ] ) ? $row[ $header ] : '';

				// Convert to string
				$value = (string) $value;

				// Trim whitespace
				$value = trim( $value );

				$prepared_row[ $header ] = $value;
			}

			$prepared[] = $prepared_row;
		}

		return $prepared;
	}
}

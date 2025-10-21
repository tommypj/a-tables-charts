<?php
/**
 * CSV Exporter
 *
 * Handles CSV file export.
 *
 * @package ATablesCharts\Export\Exporters
 * @since 1.0.0
 */

namespace ATablesCharts\Export\Exporters;

/**
 * CSVExporter Class
 *
 * Exports table data to CSV format.
 */
class CSVExporter {

	/**
	 * Export data to CSV format
	 *
	 * @param array  $headers Column headers.
	 * @param array  $data    Table data.
	 * @param string $filename Export filename.
	 * @return void Outputs CSV file
	 */
	public function export( $headers, $data, $filename = 'export.csv' ) {
		// Clean output buffer
		if ( ob_get_level() ) {
			ob_end_clean();
		}

		// Set headers for download
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
		header( 'Pragma: no-cache' );
		header( 'Expires: 0' );

		// Open output stream
		$output = fopen( 'php://output', 'w' );

		// Add BOM for Excel UTF-8 support
		fprintf( $output, chr(0xEF) . chr(0xBB) . chr(0xBF) );

		// Write headers
		fputcsv( $output, $headers );

		// Write data rows
		foreach ( $data as $row ) {
			$csv_row = array();
			foreach ( $headers as $header ) {
				$csv_row[] = isset( $row[ $header ] ) ? $row[ $header ] : '';
			}
			fputcsv( $output, $csv_row );
		}

		// Close output stream
		fclose( $output );

		exit;
	}
}

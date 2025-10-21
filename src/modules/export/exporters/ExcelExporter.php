<?php
/**
 * Excel Exporter
 *
 * Handles Excel (.xlsx) file export using PhpSpreadsheet.
 *
 * @package ATablesCharts\Export\Exporters
 * @since 1.0.0
 */

namespace ATablesCharts\Export\Exporters;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

/**
 * ExcelExporter Class
 *
 * Exports table data to Excel format with styling.
 */
class ExcelExporter {

	/**
	 * Export data to Excel format
	 *
	 * @param array  $headers Column headers.
	 * @param array  $data    Table data.
	 * @param string $filename Export filename.
	 * @return void Outputs Excel file
	 */
	public function export( $headers, $data, $filename = 'export.xlsx' ) {
		// Check if PhpSpreadsheet is available
		if ( ! class_exists( 'PhpOffice\PhpSpreadsheet\Spreadsheet' ) ) {
			wp_die( 
				'PhpSpreadsheet library is not installed. Please run "composer install" in the plugin directory.',
				'Excel Export Error',
				array( 'response' => 500 )
			);
		}

		try {
			// Create new spreadsheet
			$spreadsheet = new Spreadsheet();
			$sheet = $spreadsheet->getActiveSheet();

			// Set document properties
			$spreadsheet->getProperties()
				->setCreator( 'A-Tables & Charts' )
				->setTitle( $filename )
				->setSubject( 'Table Export' )
				->setDescription( 'Exported table data from WordPress' );

			// Add headers
			$col = 'A';
			foreach ( $headers as $header ) {
				$sheet->setCellValue( $col . '1', $header );
				$col++;
			}

			// Style headers
			$header_range = 'A1:' . $this->get_column_letter( count( $headers ) - 1 ) . '1';
			$sheet->getStyle( $header_range )->applyFromArray([
				'font' => [
					'bold' => true,
					'color' => ['rgb' => 'FFFFFF'],
					'size' => 12,
				],
				'fill' => [
					'fillType' => Fill::FILL_SOLID,
					'startColor' => ['rgb' => '2271B1'],
				],
				'alignment' => [
					'horizontal' => Alignment::HORIZONTAL_LEFT,
					'vertical' => Alignment::VERTICAL_CENTER,
				],
				'borders' => [
					'allBorders' => [
						'borderStyle' => Border::BORDER_THIN,
						'color' => ['rgb' => 'CCCCCC'],
					],
				],
			]);

			// Add data rows
			$row = 2;
			foreach ( $data as $data_row ) {
				$col = 'A';
				foreach ( $headers as $header ) {
					$value = isset( $data_row[ $header ] ) ? $data_row[ $header ] : '';
					
					// Auto-detect and set data type
					if ( is_numeric( $value ) ) {
						$sheet->setCellValue( $col . $row, (float) $value );
					} else {
						$sheet->setCellValue( $col . $row, $value );
					}
					
					$col++;
				}
				$row++;
			}

			// Auto-size columns
			foreach ( range( 'A', $this->get_column_letter( count( $headers ) - 1 ) ) as $col ) {
				$sheet->getColumnDimension( $col )->setAutoSize( true );
			}

			// Add borders to data
			if ( $row > 2 ) {
				$data_range = 'A2:' . $this->get_column_letter( count( $headers ) - 1 ) . ( $row - 1 );
				$sheet->getStyle( $data_range )->applyFromArray([
					'borders' => [
						'allBorders' => [
							'borderStyle' => Border::BORDER_THIN,
							'color' => ['rgb' => 'DDDDDD'],
						],
					],
				]);
			}

			// Freeze header row
			$sheet->freezePane( 'A2' );

			// Set active cell to A1
			$sheet->setSelectedCell( 'A1' );

			// Output file
			$this->output_file( $spreadsheet, $filename );

		} catch ( \Exception $e ) {
			wp_die(
				'Excel export failed: ' . esc_html( $e->getMessage() ),
				'Excel Export Error',
				array( 'response' => 500 )
			);
		}
	}

	/**
	 * Get column letter from index
	 *
	 * @param int $index Column index (0-based).
	 * @return string Column letter (A, B, C, etc.)
	 */
	private function get_column_letter( $index ) {
		$letter = '';
		while ( $index >= 0 ) {
			$letter = chr( $index % 26 + 65 ) . $letter;
			$index = floor( $index / 26 ) - 1;
		}
		return $letter;
	}

	/**
	 * Output Excel file to browser
	 *
	 * @param Spreadsheet $spreadsheet Spreadsheet object.
	 * @param string      $filename    Filename for download.
	 * @return void
	 */
	private function output_file( $spreadsheet, $filename ) {
		// Clean output buffer
		if ( ob_get_level() ) {
			ob_end_clean();
		}

		// Set headers for download
		header( 'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' );
		header( 'Content-Disposition: attachment;filename="' . $filename . '"' );
		header( 'Cache-Control: max-age=0' );
		header( 'Cache-Control: max-age=1' );
		header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
		header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
		header( 'Cache-Control: cache, must-revalidate' );
		header( 'Pragma: public' );

		// Create writer and output
		$writer = new Xlsx( $spreadsheet );
		$writer->save( 'php://output' );

		exit;
	}
}

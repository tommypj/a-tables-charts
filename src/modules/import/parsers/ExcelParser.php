<?php
/**
 * Excel Parser
 *
 * Parses Excel (.xlsx, .xls) files using PhpSpreadsheet.
 *
 * @package ATablesCharts\Import\Parsers
 * @since 1.0.0
 */

namespace ATablesCharts\Import\Parsers;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

/**
 * ExcelParser Class
 *
 * Parses Excel files and extracts data.
 */
class ExcelParser {

	/**
	 * Parse Excel file
	 *
	 * @param string $file_path Path to Excel file.
	 * @param array  $options   Parser options.
	 * @return array Parsed data with headers and rows
	 */
	public function parse( $file_path, $options = array() ) {
		// Default options
		$defaults = array(
			'sheet_index'    => 0,           // Which sheet to import (0 = first sheet)
			'has_headers'    => true,        // First row contains headers
			'skip_empty'     => true,        // Skip empty rows
			'max_rows'       => 10000,       // Maximum rows to import
			'trim_values'    => true,        // Trim whitespace from values
		);

		$options = array_merge( $defaults, $options );

		// Validate file exists
		if ( ! file_exists( $file_path ) ) {
			throw new \Exception( __( 'Excel file not found.', 'a-tables-charts' ) );
		}

		// Check if PhpSpreadsheet is available
		if ( ! class_exists( 'PhpOffice\PhpSpreadsheet\IOFactory' ) ) {
			throw new \Exception( __( 'PhpSpreadsheet library is not installed.', 'a-tables-charts' ) );
		}

		try {
			// Load the spreadsheet
			$spreadsheet = IOFactory::load( $file_path );
			
			// Get the specified sheet
			$sheet = $spreadsheet->getSheet( $options['sheet_index'] );
			
			// Get all data from sheet
			$sheet_data = $sheet->toArray( null, true, true, true );
			
			// Remove empty rows if needed
			if ( $options['skip_empty'] ) {
				$sheet_data = array_filter( $sheet_data, function( $row ) {
					return ! empty( array_filter( $row ) );
				});
			}

			// Limit rows
			if ( count( $sheet_data ) > $options['max_rows'] ) {
				$sheet_data = array_slice( $sheet_data, 0, $options['max_rows'] );
			}

			// Extract headers and data
			if ( $options['has_headers'] ) {
				$headers = array_shift( $sheet_data );
				$headers = array_values( $headers ); // Re-index
				
				// Clean headers
				$headers = array_map( function( $header ) use ( $options ) {
					if ( $options['trim_values'] ) {
						$header = trim( $header );
					}
					return empty( $header ) ? 'Column' : $header;
				}, $headers );
				
				// Make headers unique
				$headers = $this->make_unique_headers( $headers );
			} else {
				// Generate headers
				$first_row = reset( $sheet_data );
				$column_count = count( $first_row );
				$headers = array();
				for ( $i = 1; $i <= $column_count; $i++ ) {
					$headers[] = 'Column ' . $i;
				}
			}

			// Process data rows
			$data = array();
			foreach ( $sheet_data as $row ) {
				$row = array_values( $row ); // Re-index
				
				// Trim values if needed
				if ( $options['trim_values'] ) {
					$row = array_map( 'trim', $row );
				}
				
				// Ensure row has same number of columns as headers
				while ( count( $row ) < count( $headers ) ) {
					$row[] = '';
				}
				
				// Trim excess columns
				$row = array_slice( $row, 0, count( $headers ) );
				
				$data[] = $row;
			}

			return array(
				'headers'      => $headers,
				'data'         => $data,
				'row_count'    => count( $data ),
				'column_count' => count( $headers ),
				'sheet_name'   => $sheet->getTitle(),
				'sheet_count'  => $spreadsheet->getSheetCount(),
			);

		} catch ( \Exception $e ) {
			throw new \Exception(
				sprintf(
					/* translators: %s: error message */
					__( 'Failed to parse Excel file: %s', 'a-tables-charts' ),
					$e->getMessage()
				)
			);
		}
	}

	/**
	 * Get available sheets from Excel file
	 *
	 * @param string $file_path Path to Excel file.
	 * @return array List of sheet names
	 */
	public function get_sheets( $file_path ) {
		if ( ! file_exists( $file_path ) ) {
			throw new \Exception( __( 'Excel file not found.', 'a-tables-charts' ) );
		}

		try {
			$spreadsheet = IOFactory::load( $file_path );
			$sheets = array();
			
			foreach ( $spreadsheet->getAllSheets() as $index => $sheet ) {
				$sheets[] = array(
					'index' => $index,
					'name'  => $sheet->getTitle(),
					'rows'  => $sheet->getHighestRow(),
				);
			}
			
			return $sheets;
			
		} catch ( \Exception $e ) {
			throw new \Exception(
				sprintf(
					/* translators: %s: error message */
					__( 'Failed to read Excel file: %s', 'a-tables-charts' ),
					$e->getMessage()
				)
			);
		}
	}

	/**
	 * Make headers unique by adding numbers to duplicates
	 *
	 * @param array $headers Array of headers.
	 * @return array Unique headers
	 */
	private function make_unique_headers( $headers ) {
		$unique = array();
		$counts = array();
		
		foreach ( $headers as $header ) {
			$original = $header;
			
			if ( ! isset( $counts[ $header ] ) ) {
				$counts[ $header ] = 1;
			} else {
				$counts[ $header ]++;
				$header = $original . ' ' . $counts[ $original ];
			}
			
			$unique[] = $header;
		}
		
		return $unique;
	}

	/**
	 * Validate Excel file
	 *
	 * @param string $file_path Path to file.
	 * @return array Validation result
	 */
	public function validate( $file_path ) {
		$errors = array();

		if ( ! file_exists( $file_path ) ) {
			$errors[] = __( 'File does not exist.', 'a-tables-charts' );
			return array(
				'valid'  => false,
				'errors' => $errors,
			);
		}

		// Check file extension
		$extension = strtolower( pathinfo( $file_path, PATHINFO_EXTENSION ) );
		if ( ! in_array( $extension, array( 'xlsx', 'xls' ) ) ) {
			$errors[] = __( 'File must be .xlsx or .xls format.', 'a-tables-charts' );
		}

		// Check file size (max 10MB)
		$max_size = 10 * 1024 * 1024; // 10MB in bytes
		if ( filesize( $file_path ) > $max_size ) {
			$errors[] = __( 'File size exceeds 10MB limit.', 'a-tables-charts' );
		}

		// Try to load the file
		try {
			if ( class_exists( 'PhpOffice\PhpSpreadsheet\IOFactory' ) ) {
				$spreadsheet = IOFactory::load( $file_path );
				
				// Check if file has any data
				$sheet = $spreadsheet->getActiveSheet();
				if ( $sheet->getHighestRow() < 2 ) {
					$errors[] = __( 'Excel file appears to be empty.', 'a-tables-charts' );
				}
			}
		} catch ( \Exception $e ) {
			$errors[] = sprintf(
				/* translators: %s: error message */
				__( 'Invalid Excel file: %s', 'a-tables-charts' ),
				$e->getMessage()
			);
		}

		return array(
			'valid'  => empty( $errors ),
			'errors' => $errors,
		);
	}
}

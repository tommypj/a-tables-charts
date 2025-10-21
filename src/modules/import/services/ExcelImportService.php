<?php
/**
 * Excel Import Service
 *
 * Business logic for Excel import operations.
 *
 * @package ATablesCharts\Import\Services
 * @since 1.0.0
 */

namespace ATablesCharts\Import\Services;

use ATablesCharts\Import\Parsers\ExcelParser;
use ATablesCharts\Tables\Services\TableService;

/**
 * ExcelImportService Class
 *
 * Handles Excel file imports and table creation.
 */
class ExcelImportService {

	/**
	 * Excel Parser
	 *
	 * @var ExcelParser
	 */
	private $parser;

	/**
	 * Table Service
	 *
	 * @var TableService
	 */
	private $table_service;

	/**
	 * Constructor
	 */
	public function __construct() {
		// Load parser
		if ( file_exists( ATABLES_PLUGIN_DIR . 'vendor/autoload.php' ) ) {
			require_once ATABLES_PLUGIN_DIR . 'vendor/autoload.php';
		}
		
		$this->parser = new ExcelParser();
		$this->table_service = new TableService();
	}

	/**
	 * Import Excel file and create table
	 *
	 * @param string $file_path Path to uploaded Excel file.
	 * @param array  $options   Import options.
	 * @return array Result with success status and table_id or errors
	 */
	public function import( $file_path, $options = array() ) {
		// Skip validation for temp files - already validated in controller
		// Just check if file exists
		if ( ! file_exists( $file_path ) ) {
			return array(
				'success' => false,
				'message' => __( 'File not found.', 'a-tables-charts' ),
			);
		}

		try {
			// Parse Excel file
			$parsed_data = $this->parser->parse( $file_path, $options );

			// Extract data
			$headers = $parsed_data['headers'];
			$data = $parsed_data['data'];
			$sheet_name = $parsed_data['sheet_name'];

			// Generate table title
			$title = ! empty( $options['title'] ) 
				? $options['title'] 
				: $this->generate_title_from_filename( $file_path, $sheet_name );

			// Create table using the same format as CSV/JSON imports
			// We need to create a simple object that mimics ImportResult
			$import_result = (object) array(
				'headers'      => $headers,
				'data'         => $data,
				'row_count'    => $parsed_data['row_count'],
				'column_count' => $parsed_data['column_count'],
			);
			
			$result = $this->table_service->create_from_import(
				$title,
				$import_result,
				'excel',
				! empty( $options['description'] ) ? $options['description'] : ''
			);

			if ( $result['success'] ) {
				return array(
					'success'  => true,
					'message'  => sprintf(
						/* translators: 1: row count, 2: column count */
						__( 'Successfully imported %1$d rows and %2$d columns.', 'a-tables-charts' ),
						$parsed_data['row_count'],
						$parsed_data['column_count']
					),
					'table_id' => $result['table_id'],
					'stats'    => array(
						'rows'    => $parsed_data['row_count'],
						'columns' => $parsed_data['column_count'],
						'sheet'   => $sheet_name,
					),
				);
			} else {
				return array(
					'success' => false,
					'message' => $result['message'],
				);
			}

		} catch ( \Exception $e ) {
			return array(
				'success' => false,
				'message' => $e->getMessage(),
			);
		}
	}

	/**
	 * Get preview of Excel file data
	 *
	 * @param string $file_path Path to Excel file.
	 * @param array  $options   Parser options.
	 * @return array Preview data or error
	 */
	public function preview( $file_path, $options = array() ) {
		// Skip validation for temp files - already validated in controller
		// Just check if file exists
		if ( ! file_exists( $file_path ) ) {
			return array(
				'success' => false,
				'message' => __( 'File not found.', 'a-tables-charts' ),
			);
		}

		try {
			// Parse with limit for preview
			$preview_options = array_merge( $options, array(
				'max_rows' => 10, // Only preview first 10 rows
			) );

			$parsed_data = $this->parser->parse( $file_path, $preview_options );

			return array(
				'success'      => true,
				'headers'      => $parsed_data['headers'],
				'data'         => $parsed_data['data'],
				'row_count'    => $parsed_data['row_count'],
				'column_count' => $parsed_data['column_count'],
				'sheet_name'   => $parsed_data['sheet_name'],
				'sheet_count'  => $parsed_data['sheet_count'],
			);

		} catch ( \Exception $e ) {
			return array(
				'success' => false,
				'message' => $e->getMessage(),
			);
		}
	}

	/**
	 * Get list of available sheets in Excel file
	 *
	 * @param string $file_path Path to Excel file.
	 * @return array List of sheets or error
	 */
	public function get_sheets( $file_path ) {
		try {
			$sheets = $this->parser->get_sheets( $file_path );
			
			return array(
				'success' => true,
				'sheets'  => $sheets,
			);
			
		} catch ( \Exception $e ) {
			return array(
				'success' => false,
				'message' => $e->getMessage(),
			);
		}
	}

	/**
	 * Generate table title from filename
	 *
	 * @param string $file_path  Path to file.
	 * @param string $sheet_name Sheet name.
	 * @return string Generated title
	 */
	private function generate_title_from_filename( $file_path, $sheet_name = '' ) {
		$filename = basename( $file_path, '.xlsx' );
		$filename = basename( $filename, '.xls' );
		
		// Clean filename
		$title = str_replace( array( '-', '_' ), ' ', $filename );
		$title = ucwords( $title );
		
		// Add sheet name if available and different from filename
		if ( ! empty( $sheet_name ) && strtolower( $sheet_name ) !== strtolower( $filename ) ) {
			$title .= ' - ' . $sheet_name;
		}
		
		return $title;
	}
}

<?php
/**
 * WP-CLI Export/Import Commands
 *
 * Manages table exports and imports via WP-CLI.
 *
 * @package ATablesCharts\CLI
 * @since 1.0.0
 */

namespace ATablesCharts\CLI\Commands;

use ATablesCharts\Export\Services\ExportService;
use ATablesCharts\Tables\Services\TableService;
use ATablesCharts\Import\Services\ImportService;
use ATablesCharts\Shared\Utils\Logger;

/**
 * Export and import tables.
 */
class ExportCommand {

	/**
	 * Export service instance
	 *
	 * @var ExportService
	 */
	private $export_service;

	/**
	 * Table service instance
	 *
	 * @var TableService
	 */
	private $table_service;

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
		require_once ATABLES_PLUGIN_DIR . 'src/modules/export/index.php';
		require_once ATABLES_PLUGIN_DIR . 'src/modules/tables/index.php';
		require_once ATABLES_PLUGIN_DIR . 'src/modules/import/index.php';
		$this->export_service = new ExportService();
		$this->table_service = new TableService();
		$this->import_service = new ImportService();
		$this->logger = new Logger();
	}

	/**
	 * Exports a table to a file.
	 *
	 * ## OPTIONS
	 *
	 * <id>
	 * : The table ID to export.
	 *
	 * [--format=<format>]
	 * : Export format (csv, json, xlsx, xml).
	 * ---
	 * default: csv
	 * options:
	 *   - csv
	 *   - json
	 *   - xlsx
	 *   - xml
	 * ---
	 *
	 * [--file=<file>]
	 * : Output file path. If not specified, outputs to stdout.
	 *
	 * [--columns=<columns>]
	 * : Comma-separated list of column indices to export (0-based).
	 *
	 * ## EXAMPLES
	 *
	 *     # Export table to CSV
	 *     $ wp atables export 123 --format=csv --file=/path/to/export.csv
	 *
	 *     # Export to JSON
	 *     $ wp atables export 123 --format=json --file=/path/to/export.json
	 *
	 *     # Export specific columns
	 *     $ wp atables export 123 --columns=0,1,3 --file=/path/to/export.csv
	 *
	 *     # Export to stdout
	 *     $ wp atables export 123 --format=csv
	 *
	 * @when after_wp_load
	 */
	public function __invoke( $args, $assoc_args ) {
		list( $table_id ) = $args;
		$format = isset( $assoc_args['format'] ) ? $assoc_args['format'] : 'csv';
		$file = isset( $assoc_args['file'] ) ? $assoc_args['file'] : null;

		$table = $this->table_service->get_table( $table_id );

		if ( ! $table ) {
			\WP_CLI::error( "Table #{$table_id} not found." );
		}

		// Get table data
		$data = $table->source_data;

		// Filter columns if specified
		if ( isset( $assoc_args['columns'] ) ) {
			$columns = array_map( 'intval', explode( ',', $assoc_args['columns'] ) );
			$data = $this->filter_columns( $data, $columns );
		}

		// Export based on format
		switch ( $format ) {
			case 'csv':
				$content = $this->export_to_csv( $data );
				break;
			case 'json':
				$content = $this->export_to_json( $data );
				break;
			case 'xml':
				$content = $this->export_to_xml( $data, $table->title );
				break;
			case 'xlsx':
				if ( ! $file ) {
					\WP_CLI::error( 'XLSX export requires --file parameter.' );
				}
				$this->export_to_xlsx( $data, $file );
				\WP_CLI::success( "Table exported to {$file}" );
				return;
			default:
				\WP_CLI::error( "Unsupported format: {$format}" );
		}

		// Output to file or stdout
		if ( $file ) {
			$result = file_put_contents( $file, $content );
			if ( $result === false ) {
				\WP_CLI::error( "Failed to write to {$file}" );
			}
			\WP_CLI::success( "Table exported to {$file}" );
		} else {
			echo $content;
		}
	}

	/**
	 * Imports a file to create or update a table.
	 *
	 * ## OPTIONS
	 *
	 * <file>
	 * : Path to the file to import.
	 *
	 * [--title=<title>]
	 * : Title for the new table. Required for new tables.
	 *
	 * [--table-id=<table-id>]
	 * : Update existing table instead of creating new one.
	 *
	 * [--format=<format>]
	 * : File format (csv, json, xlsx, xml). Auto-detected from extension if not specified.
	 *
	 * [--has-headers]
	 * : First row contains headers (for CSV).
	 *
	 * [--delimiter=<delimiter>]
	 * : CSV delimiter character.
	 * ---
	 * default: ,
	 * ---
	 *
	 * [--porcelain]
	 * : Output just the table ID.
	 *
	 * ## EXAMPLES
	 *
	 *     # Import CSV file
	 *     $ wp atables import /path/to/data.csv --title="Imported Data" --has-headers
	 *
	 *     # Import JSON file
	 *     $ wp atables import /path/to/data.json --title="JSON Data"
	 *
	 *     # Update existing table
	 *     $ wp atables import /path/to/data.csv --table-id=123
	 *
	 *     # Import with custom delimiter
	 *     $ wp atables import /path/to/data.tsv --delimiter=$'\t' --title="TSV Data"
	 *
	 * @when after_wp_load
	 * @subcommand import
	 */
	public function import( $args, $assoc_args ) {
		list( $file_path ) = $args;

		if ( ! file_exists( $file_path ) ) {
			\WP_CLI::error( "File not found: {$file_path}" );
		}

		// Determine format
		$format = isset( $assoc_args['format'] )
			? $assoc_args['format']
			: $this->detect_format( $file_path );

		// Parse file
		$parse_options = array(
			'has_header' => isset( $assoc_args['has-headers'] ),
			'delimiter' => isset( $assoc_args['delimiter'] ) ? $assoc_args['delimiter'] : ',',
		);

		$file_content = file_get_contents( $file_path );
		$import_result = $this->import_service->import_from_string( $file_content, $format, $parse_options );

		if ( ! $import_result ) {
			\WP_CLI::error( 'Failed to parse file.' );
		}

		// Create or update table
		if ( isset( $assoc_args['table-id'] ) ) {
			// Update existing table
			$table_id = $assoc_args['table-id'];
			$table = $this->table_service->get_table( $table_id );

			if ( ! $table ) {
				\WP_CLI::error( "Table #{$table_id} not found." );
			}

			$result = $this->table_service->update_table_data(
				$table_id,
				$import_result->data,
				$import_result->headers
			);

			if ( ! $result['success'] ) {
				\WP_CLI::error( $result['message'] );
			}

			if ( isset( $assoc_args['porcelain'] ) ) {
				\WP_CLI::line( $table_id );
			} else {
				\WP_CLI::success( "Updated table #{$table_id}" );
			}
		} else {
			// Create new table
			if ( ! isset( $assoc_args['title'] ) ) {
				\WP_CLI::error( 'Title is required for new tables. Use --title parameter.' );
			}

			$table_data = array(
				'title' => $assoc_args['title'],
				'description' => isset( $assoc_args['description'] ) ? $assoc_args['description'] : '',
				'status' => 'active',
				'source_data' => array(
					'headers' => $import_result->headers,
					'data' => $import_result->data,
				),
				'row_count' => count( $import_result->data ),
				'column_count' => count( $import_result->headers ),
			);

			$result = $this->table_service->create_table( $table_data );

			if ( ! $result['success'] ) {
				\WP_CLI::error( $result['message'] );
			}

			if ( isset( $assoc_args['porcelain'] ) ) {
				\WP_CLI::line( $result['table_id'] );
			} else {
				\WP_CLI::success( "Created table #{$result['table_id']}: {$assoc_args['title']}" );
			}
		}
	}

	/**
	 * Filter columns from table data
	 *
	 * @param array $data Table data.
	 * @param array $columns Column indices to keep.
	 * @return array Filtered data.
	 */
	private function filter_columns( $data, $columns ) {
		$filtered = array(
			'headers' => array(),
			'data' => array(),
		);

		// Filter headers
		foreach ( $columns as $col_index ) {
			if ( isset( $data['headers'][ $col_index ] ) ) {
				$filtered['headers'][] = $data['headers'][ $col_index ];
			}
		}

		// Filter data rows
		foreach ( $data['data'] as $row ) {
			$filtered_row = array();
			foreach ( $columns as $col_index ) {
				if ( isset( $row[ $col_index ] ) ) {
					$filtered_row[] = $row[ $col_index ];
				}
			}
			$filtered['data'][] = $filtered_row;
		}

		return $filtered;
	}

	/**
	 * Export data to CSV format
	 *
	 * @param array $data Table data.
	 * @return string CSV content.
	 */
	private function export_to_csv( $data ) {
		$output = fopen( 'php://temp', 'r+' );

		// Write headers
		fputcsv( $output, $data['headers'] );

		// Write data rows
		foreach ( $data['data'] as $row ) {
			fputcsv( $output, $row );
		}

		rewind( $output );
		$csv = stream_get_contents( $output );
		fclose( $output );

		return $csv;
	}

	/**
	 * Export data to JSON format
	 *
	 * @param array $data Table data.
	 * @return string JSON content.
	 */
	private function export_to_json( $data ) {
		$rows = array();

		foreach ( $data['data'] as $row_data ) {
			$row_obj = array();
			foreach ( $data['headers'] as $index => $header ) {
				$row_obj[ $header ] = isset( $row_data[ $index ] ) ? $row_data[ $index ] : '';
			}
			$rows[] = $row_obj;
		}

		return json_encode( $rows, JSON_PRETTY_PRINT );
	}

	/**
	 * Export data to XML format
	 *
	 * @param array  $data Table data.
	 * @param string $title Table title.
	 * @return string XML content.
	 */
	private function export_to_xml( $data, $title ) {
		$xml = new \SimpleXMLElement( '<?xml version="1.0" encoding="UTF-8"?><table></table>' );
		$xml->addAttribute( 'title', $title );

		$headers_element = $xml->addChild( 'headers' );
		foreach ( $data['headers'] as $header ) {
			$headers_element->addChild( 'header', htmlspecialchars( $header ) );
		}

		$rows_element = $xml->addChild( 'rows' );
		foreach ( $data['data'] as $row_data ) {
			$row_element = $rows_element->addChild( 'row' );
			foreach ( $data['headers'] as $index => $header ) {
				$value = isset( $row_data[ $index ] ) ? $row_data[ $index ] : '';
				$row_element->addChild( 'cell', htmlspecialchars( $value ) );
			}
		}

		return $xml->asXML();
	}

	/**
	 * Export data to XLSX format
	 *
	 * @param array  $data Table data.
	 * @param string $file_path Output file path.
	 */
	private function export_to_xlsx( $data, $file_path ) {
		// Use the export service to create XLSX
		$result = $this->export_service->export_to_excel( $data, $file_path );

		if ( ! $result ) {
			\WP_CLI::error( 'Failed to create XLSX file.' );
		}
	}

	/**
	 * Detect file format from extension
	 *
	 * @param string $file_path File path.
	 * @return string Format (csv, json, xlsx, xml).
	 */
	private function detect_format( $file_path ) {
		$extension = strtolower( pathinfo( $file_path, PATHINFO_EXTENSION ) );

		$format_map = array(
			'csv' => 'csv',
			'json' => 'json',
			'xlsx' => 'xlsx',
			'xls' => 'xlsx',
			'xml' => 'xml',
			'tsv' => 'csv',
		);

		return isset( $format_map[ $extension ] ) ? $format_map[ $extension ] : 'csv';
	}
}

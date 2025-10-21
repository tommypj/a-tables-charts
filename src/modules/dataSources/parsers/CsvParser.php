<?php
/**
 * CSV Parser
 *
 * Parses CSV (Comma-Separated Values) files.
 *
 * @package ATablesCharts\DataSources\Parsers
 * @since 1.0.0
 */

namespace ATablesCharts\DataSources\Parsers;

use ATablesCharts\DataSources\Types\ImportResult;
use ATablesCharts\Shared\Utils\Validator;
use ATablesCharts\Shared\Utils\Sanitizer;

/**
 * CsvParser Class
 *
 * Responsibilities:
 * - Parse CSV files
 * - Handle various delimiters and encodings
 * - Detect and handle headers
 * - Handle large files efficiently
 */
class CsvParser implements ParserInterface {

	/**
	 * Default options for CSV parsing
	 *
	 * @var array
	 */
	private $default_options = array(
		'delimiter'        => ',',
		'enclosure'        => '"',
		'escape'           => '\\',
		'has_header'       => true,
		'skip_empty_lines' => true,
		'detect_delimiter' => true,
		'encoding'         => 'UTF-8',
		'max_rows'         => 0, // 0 = no limit
	);

	/**
	 * Parse data from a CSV file
	 *
	 * @param string $file_path Path to the CSV file.
	 * @param array  $options   Optional parsing options.
	 * @return ImportResult Result of the parsing operation
	 */
	public function parse_file( $file_path, $options = array() ) {
		// Validate file exists.
		if ( ! file_exists( $file_path ) ) {
			return ImportResult::error( __( 'File not found.', 'a-tables-charts' ) );
		}

		// Validate file can be parsed.
		if ( ! $this->can_parse( $file_path ) ) {
			return ImportResult::error( __( 'Invalid CSV file.', 'a-tables-charts' ) );
		}

		// Merge options with defaults.
		$options = array_merge( $this->default_options, $options );

		// Read file content.
		$content = file_get_contents( $file_path );

		if ( false === $content ) {
			return ImportResult::error( __( 'Failed to read file.', 'a-tables-charts' ) );
		}

		return $this->parse_string( $content, $options );
	}

	/**
	 * Parse data from a CSV string
	 *
	 * @param string $content CSV content.
	 * @param array  $options Optional parsing options.
	 * @return ImportResult Result of the parsing operation
	 */
	public function parse_string( $content, $options = array() ) {
		// Merge options with defaults.
		$options = array_merge( $this->default_options, $options );

		// Convert encoding if needed.
		$content = $this->convert_encoding( $content, $options['encoding'] );

		// Detect delimiter if requested.
		if ( $options['detect_delimiter'] ) {
			$options['delimiter'] = $this->detect_delimiter( $content );
		}

		// Parse CSV.
		$rows = $this->parse_csv_content( $content, $options );

		if ( empty( $rows ) ) {
			return ImportResult::error( __( 'No data found in CSV file.', 'a-tables-charts' ) );
		}

		// Extract headers.
		$headers = array();
		if ( $options['has_header'] ) {
			$headers = array_shift( $rows );
			$headers = $this->sanitize_headers( $headers );
		} else {
			// Generate column names (Column 1, Column 2, etc.).
			$column_count = ! empty( $rows ) ? count( $rows[0] ) : 0;
			for ( $i = 1; $i <= $column_count; $i++ ) {
				$headers[] = sprintf( __( 'Column %d', 'a-tables-charts' ), $i );
			}
		}

		// Sanitize data.
		$rows = $this->sanitize_data( $rows );

		// Apply max rows limit.
		if ( $options['max_rows'] > 0 && count( $rows ) > $options['max_rows'] ) {
			$rows = array_slice( $rows, 0, $options['max_rows'] );
		}

		// Create success result.
		$result = ImportResult::success( $rows, $headers );

		// Add metadata.
		$result->add_metadata( 'delimiter', $options['delimiter'] );
		$result->add_metadata( 'encoding', $options['encoding'] );

		return $result;
	}

	/**
	 * Validate that a file can be parsed
	 *
	 * @param string $file_path Path to the file.
	 * @return bool True if file can be parsed
	 */
	public function can_parse( $file_path ) {
		$extension = strtolower( pathinfo( $file_path, PATHINFO_EXTENSION ) );
		return in_array( $extension, $this->get_supported_extensions(), true );
	}

	/**
	 * Get supported file extensions
	 *
	 * @return array Array of supported extensions
	 */
	public function get_supported_extensions() {
		return array( 'csv', 'txt' );
	}

	/**
	 * Get supported MIME types
	 *
	 * @return array Array of supported MIME types
	 */
	public function get_supported_mime_types() {
		return array(
			'text/csv',
			'text/plain',
			'application/csv',
			'text/comma-separated-values',
			'application/vnd.ms-excel',
		);
	}

	/**
	 * Parse CSV content into rows
	 *
	 * @param string $content CSV content.
	 * @param array  $options Parsing options.
	 * @return array Array of rows
	 */
	private function parse_csv_content( $content, $options ) {
		$rows = array();

		// Split content into lines.
		$lines = preg_split( "/\r\n|\n|\r/", $content );

		foreach ( $lines as $line ) {
			// Skip empty lines if requested.
			if ( $options['skip_empty_lines'] && empty( trim( $line ) ) ) {
				continue;
			}

			// Parse line.
			$row = str_getcsv( $line, $options['delimiter'], $options['enclosure'], $options['escape'] );

			// Skip if no data.
			if ( empty( $row ) || ( count( $row ) === 1 && empty( trim( $row[0] ) ) ) ) {
				continue;
			}

			$rows[] = $row;
		}

		return $rows;
	}

	/**
	 * Detect the delimiter used in CSV content
	 *
	 * @param string $content CSV content.
	 * @return string Detected delimiter
	 */
	private function detect_delimiter( $content ) {
		// Get first few lines for detection.
		$lines         = preg_split( "/\r\n|\n|\r/", $content );
		$sample_lines  = array_slice( $lines, 0, 5 );
		$sample_content = implode( "\n", $sample_lines );

		// Try common delimiters.
		$delimiters = array( ',', ';', "\t", '|', ':' );
		$max_count  = 0;
		$best_delimiter = ',';

		foreach ( $delimiters as $delimiter ) {
			$count = substr_count( $sample_content, $delimiter );
			if ( $count > $max_count ) {
				$max_count      = $count;
				$best_delimiter = $delimiter;
			}
		}

		return $best_delimiter;
	}

	/**
	 * Convert content encoding
	 *
	 * @param string $content CSV content.
	 * @param string $target_encoding Target encoding.
	 * @return string Converted content
	 */
	private function convert_encoding( $content, $target_encoding = 'UTF-8' ) {
		// Detect current encoding.
		$current_encoding = mb_detect_encoding( $content, mb_detect_order(), true );

		if ( false === $current_encoding ) {
			$current_encoding = 'UTF-8';
		}

		// Convert if needed.
		if ( $current_encoding !== $target_encoding ) {
			$content = mb_convert_encoding( $content, $target_encoding, $current_encoding );
		}

		return $content;
	}

	/**
	 * Sanitize column headers
	 *
	 * @param array $headers Raw headers.
	 * @return array Sanitized headers
	 */
	private function sanitize_headers( $headers ) {
		$sanitized = array();

		foreach ( $headers as $header ) {
			// Remove extra whitespace.
			$header = trim( $header );

			// Sanitize.
			$header = Sanitizer::text( $header );

			// Ensure not empty.
			if ( empty( $header ) ) {
				$header = 'Column ' . ( count( $sanitized ) + 1 );
			}

			$sanitized[] = $header;
		}

		return $sanitized;
	}

	/**
	 * Sanitize data rows
	 *
	 * @param array $rows Raw data rows.
	 * @return array Sanitized rows
	 */
	private function sanitize_data( $rows ) {
		$sanitized = array();

		foreach ( $rows as $row ) {
			$sanitized_row = array();

			foreach ( $row as $cell ) {
				// Trim whitespace.
				$cell = trim( $cell );

				// Sanitize text.
				$cell = Sanitizer::text( $cell );

				$sanitized_row[] = $cell;
			}

			$sanitized[] = $sanitized_row;
		}

		return $sanitized;
	}
}

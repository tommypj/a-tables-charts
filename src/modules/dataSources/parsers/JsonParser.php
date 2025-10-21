<?php
/**
 * JSON Parser
 *
 * Parses JSON (JavaScript Object Notation) data.
 *
 * @package ATablesCharts\DataSources\Parsers
 * @since 1.0.0
 */

namespace ATablesCharts\DataSources\Parsers;

use ATablesCharts\DataSources\Types\ImportResult;
use ATablesCharts\Shared\Utils\Sanitizer;

/**
 * JsonParser Class
 *
 * Responsibilities:
 * - Parse JSON files and strings
 * - Handle nested JSON structures
 * - Flatten complex data
 * - Extract tabular data from JSON
 */
class JsonParser implements ParserInterface {

	/**
	 * Default options for JSON parsing
	 *
	 * @var array
	 */
	private $default_options = array(
		'flatten_nested' => true,
		'max_depth'      => 10,
		'array_key'      => null, // Key to extract array from (if JSON is object with nested array)
	);

	/**
	 * Parse data from a JSON file
	 *
	 * @param string $file_path Path to the JSON file.
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
			return ImportResult::error( __( 'Invalid JSON file.', 'a-tables-charts' ) );
		}

		// Read file content.
		$content = file_get_contents( $file_path );

		if ( false === $content ) {
			return ImportResult::error( __( 'Failed to read file.', 'a-tables-charts' ) );
		}

		return $this->parse_string( $content, $options );
	}

	/**
	 * Parse data from a JSON string
	 *
	 * @param string $content JSON content.
	 * @param array  $options Optional parsing options.
	 * @return ImportResult Result of the parsing operation
	 */
	public function parse_string( $content, $options = array() ) {
		// Merge options with defaults.
		$options = array_merge( $this->default_options, $options );

		// Decode JSON.
		$data = json_decode( $content, true );

		// Check for JSON errors.
		if ( json_last_error() !== JSON_ERROR_NONE ) {
			return ImportResult::error(
				sprintf(
					/* translators: %s: JSON error message */
					__( 'JSON parsing error: %s', 'a-tables-charts' ),
					json_last_error_msg()
				)
			);
		}

		// Handle empty data.
		if ( empty( $data ) ) {
			return ImportResult::error( __( 'No data found in JSON.', 'a-tables-charts' ) );
		}

		// Extract array from nested structure if key specified.
		if ( ! is_null( $options['array_key'] ) && isset( $data[ $options['array_key'] ] ) ) {
			$data = $data[ $options['array_key'] ];
		}

		// Ensure data is an array.
		if ( ! is_array( $data ) ) {
			return ImportResult::error( __( 'JSON must contain an array of records.', 'a-tables-charts' ) );
		}

		// Convert to tabular format.
		$result = $this->convert_to_table( $data, $options );

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
		return array( 'json' );
	}

	/**
	 * Get supported MIME types
	 *
	 * @return array Array of supported MIME types
	 */
	public function get_supported_mime_types() {
		return array(
			'application/json',
			'text/json',
		);
	}

	/**
	 * Convert JSON data to table format
	 *
	 * @param array $data    JSON data.
	 * @param array $options Parsing options.
	 * @return ImportResult Result with tabular data
	 */
	private function convert_to_table( $data, $options ) {
		// Handle array of objects (most common case).
		if ( $this->is_array_of_objects( $data ) ) {
			return $this->parse_array_of_objects( $data, $options );
		}

		// Handle array of arrays.
		if ( $this->is_array_of_arrays( $data ) ) {
			return $this->parse_array_of_arrays( $data );
		}

		// Handle single object.
		if ( $this->is_object( $data ) ) {
			return $this->parse_single_object( $data, $options );
		}

		return ImportResult::error( __( 'Unsupported JSON structure.', 'a-tables-charts' ) );
	}

	/**
	 * Check if data is an array of objects
	 *
	 * @param array $data Data to check.
	 * @return bool True if array of objects
	 */
	private function is_array_of_objects( $data ) {
		if ( empty( $data ) || ! is_array( $data ) ) {
			return false;
		}

		// Check first item.
		$first = reset( $data );
		return is_array( $first ) && $this->is_object( $first );
	}

	/**
	 * Check if data is an array of arrays
	 *
	 * @param array $data Data to check.
	 * @return bool True if array of arrays
	 */
	private function is_array_of_arrays( $data ) {
		if ( empty( $data ) || ! is_array( $data ) ) {
			return false;
		}

		// Check first item.
		$first = reset( $data );
		return is_array( $first ) && ! $this->is_object( $first );
	}

	/**
	 * Check if data is an object (associative array)
	 *
	 * @param array $data Data to check.
	 * @return bool True if object
	 */
	private function is_object( $data ) {
		if ( ! is_array( $data ) ) {
			return false;
		}

		// Check if array has string keys.
		return array_keys( $data ) !== range( 0, count( $data ) - 1 );
	}

	/**
	 * Parse array of objects
	 *
	 * @param array $data    Data to parse.
	 * @param array $options Parsing options.
	 * @return ImportResult Result
	 */
	private function parse_array_of_objects( $data, $options ) {
		// Collect all unique keys as headers.
		$headers = array();
		foreach ( $data as $item ) {
			if ( $options['flatten_nested'] ) {
				$flattened = $this->flatten_array( $item );
				$headers   = array_merge( $headers, array_keys( $flattened ) );
			} else {
				$headers = array_merge( $headers, array_keys( $item ) );
			}
		}

		$headers = array_unique( $headers );
		$headers = array_values( $headers );

		// Convert each object to a row.
		$rows = array();
		foreach ( $data as $item ) {
			if ( $options['flatten_nested'] ) {
				$item = $this->flatten_array( $item );
			}

			$row = array();
			foreach ( $headers as $header ) {
				$row[] = isset( $item[ $header ] ) ? $this->format_value( $item[ $header ] ) : '';
			}
			$rows[] = $row;
		}

		// Sanitize.
		$headers = $this->sanitize_headers( $headers );
		$rows    = $this->sanitize_data( $rows );

		return ImportResult::success( $rows, $headers );
	}

	/**
	 * Parse array of arrays
	 *
	 * @param array $data Data to parse.
	 * @return ImportResult Result
	 */
	private function parse_array_of_arrays( $data ) {
		// Use first row as headers or generate them.
		$first_row = reset( $data );
		$col_count = count( $first_row );

		// Generate headers.
		$headers = array();
		for ( $i = 1; $i <= $col_count; $i++ ) {
			$headers[] = sprintf( __( 'Column %d', 'a-tables-charts' ), $i );
		}

		// Sanitize.
		$rows = $this->sanitize_data( $data );

		return ImportResult::success( $rows, $headers );
	}

	/**
	 * Parse single object
	 *
	 * @param array $data    Data to parse.
	 * @param array $options Parsing options.
	 * @return ImportResult Result
	 */
	private function parse_single_object( $data, $options ) {
		if ( $options['flatten_nested'] ) {
			$data = $this->flatten_array( $data );
		}

		$headers = array_keys( $data );
		$rows    = array( array_values( $data ) );

		// Sanitize.
		$headers = $this->sanitize_headers( $headers );
		$rows    = $this->sanitize_data( $rows );

		return ImportResult::success( $rows, $headers );
	}

	/**
	 * Flatten nested array using dot notation
	 *
	 * @param array  $array Array to flatten.
	 * @param string $prefix Prefix for keys.
	 * @return array Flattened array
	 */
	private function flatten_array( $array, $prefix = '' ) {
		$result = array();

		foreach ( $array as $key => $value ) {
			$new_key = $prefix . $key;

			if ( is_array( $value ) && ! empty( $value ) ) {
				// Check if it's an object or array.
				if ( $this->is_object( $value ) ) {
					$result = array_merge( $result, $this->flatten_array( $value, $new_key . '.' ) );
				} else {
					// Convert array to JSON string.
					$result[ $new_key ] = wp_json_encode( $value );
				}
			} else {
				$result[ $new_key ] = $value;
			}
		}

		return $result;
	}

	/**
	 * Format a value for display
	 *
	 * @param mixed $value Value to format.
	 * @return string Formatted value
	 */
	private function format_value( $value ) {
		if ( is_bool( $value ) ) {
			return $value ? 'true' : 'false';
		}

		if ( is_null( $value ) ) {
			return '';
		}

		if ( is_array( $value ) || is_object( $value ) ) {
			return wp_json_encode( $value );
		}

		return (string) $value;
	}

	/**
	 * Sanitize column headers
	 *
	 * @param array $headers Raw headers.
	 * @return array Sanitized headers
	 */
	private function sanitize_headers( $headers ) {
		return array_map( array( Sanitizer::class, 'text' ), $headers );
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
			$sanitized_row = array_map( array( Sanitizer::class, 'text' ), $row );
			$sanitized[]   = $sanitized_row;
		}

		return $sanitized;
	}
}

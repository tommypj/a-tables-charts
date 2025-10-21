<?php
/**
 * PHP Array Parser
 *
 * Parses PHP arrays into table data.
 *
 * @package ATablesCharts\DataSources\Parsers
 * @since 1.0.0
 */

namespace ATablesCharts\DataSources\Parsers;

/**
 * PHPArrayParser Class
 *
 * Converts PHP arrays to table format.
 * Supports both indexed and associative arrays.
 */
class PHPArrayParser {

	/**
	 * Parse PHP array data
	 *
	 * @param array $data PHP array data.
	 * @param array $options Parser options.
	 * @return array Parsed data with headers and rows.
	 */
	public function parse( $data, $options = array() ) {
		// Validate input
		if ( ! is_array( $data ) || empty( $data ) ) {
			throw new \Exception( __( 'Invalid array data provided.', 'a-tables-charts' ) );
		}

		$defaults = array(
			'has_headers'    => true,  // First row contains headers
			'auto_headers'   => true,  // Auto-detect headers from associative array keys
			'header_prefix'  => 'Column ', // Prefix for auto-generated headers
		);
		$options = array_merge( $defaults, $options );

		// Detect array type
		$is_associative = $this->is_associative( $data );

		if ( $is_associative ) {
			return $this->parse_associative( $data, $options );
		} else {
			return $this->parse_indexed( $data, $options );
		}
	}

	/**
	 * Parse associative array (array of objects/associative arrays)
	 *
	 * Example:
	 * [
	 *   ['name' => 'John', 'age' => 30],
	 *   ['name' => 'Jane', 'age' => 25]
	 * ]
	 *
	 * @param array $data Associative array data.
	 * @param array $options Parser options.
	 * @return array Parsed data.
	 */
	private function parse_associative( $data, $options ) {
		$headers = array();
		$rows = array();

		// Get headers from first row keys
		$first_row = reset( $data );
		if ( is_array( $first_row ) || is_object( $first_row ) ) {
			$first_row = (array) $first_row;
			$headers = array_keys( $first_row );
		}

		// Convert all rows to indexed arrays
		foreach ( $data as $row ) {
			$row = (array) $row;
			$row_data = array();
			
			foreach ( $headers as $header ) {
				$row_data[] = isset( $row[ $header ] ) ? $this->format_value( $row[ $header ] ) : '';
			}
			
			$rows[] = $row_data;
		}

		return array(
			'headers' => $headers,
			'data'    => $rows,
		);
	}

	/**
	 * Parse indexed array (2D array)
	 *
	 * Example:
	 * [
	 *   ['Name', 'Age', 'City'],
	 *   ['John', 30, 'NYC'],
	 *   ['Jane', 25, 'LA']
	 * ]
	 *
	 * @param array $data Indexed array data.
	 * @param array $options Parser options.
	 * @return array Parsed data.
	 */
	private function parse_indexed( $data, $options ) {
		$headers = array();
		$rows = array();

		// Check if first row should be treated as headers
		if ( $options['has_headers'] && ! empty( $data ) ) {
			$headers = array_shift( $data );
			$headers = array_map( array( $this, 'format_value' ), $headers );
		} else {
			// Generate headers
			$first_row = reset( $data );
			$column_count = is_array( $first_row ) ? count( $first_row ) : 0;
			
			for ( $i = 0; $i < $column_count; $i++ ) {
				$headers[] = $options['header_prefix'] . ( $i + 1 );
			}
		}

		// Process data rows
		foreach ( $data as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}

			$row_data = array_map( array( $this, 'format_value' ), $row );
			
			// Ensure row has same number of columns as headers
			$row_data = array_pad( $row_data, count( $headers ), '' );
			$row_data = array_slice( $row_data, 0, count( $headers ) );
			
			$rows[] = $row_data;
		}

		return array(
			'headers' => $headers,
			'data'    => $rows,
		);
	}

	/**
	 * Check if array is associative
	 *
	 * @param array $array Array to check.
	 * @return bool True if associative.
	 */
	private function is_associative( $array ) {
		if ( ! is_array( $array ) || empty( $array ) ) {
			return false;
		}

		// Check first element
		$first = reset( $array );
		return is_array( $first ) && $this->array_is_associative( $first );
	}

	/**
	 * Check if a single array is associative
	 *
	 * @param array $array Array to check.
	 * @return bool True if associative.
	 */
	private function array_is_associative( $array ) {
		if ( ! is_array( $array ) ) {
			return false;
		}

		return array_keys( $array ) !== range( 0, count( $array ) - 1 );
	}

	/**
	 * Format value for display
	 *
	 * @param mixed $value Value to format.
	 * @return string Formatted value.
	 */
	private function format_value( $value ) {
		if ( is_array( $value ) || is_object( $value ) ) {
			return wp_json_encode( $value );
		}

		if ( is_bool( $value ) ) {
			return $value ? 'true' : 'false';
		}

		if ( is_null( $value ) ) {
			return '';
		}

		return (string) $value;
	}

	/**
	 * Validate array structure
	 *
	 * @param array $data Array data to validate.
	 * @return array Validation result.
	 */
	public function validate( $data ) {
		$errors = array();

		// Check if data is an array
		if ( ! is_array( $data ) ) {
			$errors[] = __( 'Data must be an array.', 'a-tables-charts' );
			return array(
				'valid'  => false,
				'errors' => $errors,
			);
		}

		// Check if array is empty
		if ( empty( $data ) ) {
			$errors[] = __( 'Array cannot be empty.', 'a-tables-charts' );
			return array(
				'valid'  => false,
				'errors' => $errors,
			);
		}

		// Check if all rows have consistent structure
		$first_row = reset( $data );
		$is_associative = is_array( $first_row ) && $this->array_is_associative( $first_row );

		if ( $is_associative ) {
			// For associative arrays, check if all rows have the same keys
			$expected_keys = array_keys( (array) $first_row );
			
			foreach ( $data as $index => $row ) {
				$row = (array) $row;
				$row_keys = array_keys( $row );
				
				if ( $row_keys !== $expected_keys ) {
					$errors[] = sprintf(
						/* translators: %d: row index */
						__( 'Row %d has inconsistent keys.', 'a-tables-charts' ),
						$index
					);
				}
			}
		} else {
			// For indexed arrays, check if all rows have the same column count
			$expected_count = is_array( $first_row ) ? count( $first_row ) : 1;
			
			foreach ( $data as $index => $row ) {
				if ( ! is_array( $row ) ) {
					continue;
				}
				
				if ( count( $row ) !== $expected_count ) {
					$errors[] = sprintf(
						/* translators: 1: row index, 2: expected count, 3: actual count */
						__( 'Row %1$d has %3$d columns, expected %2$d.', 'a-tables-charts' ),
						$index,
						$expected_count,
						count( $row )
					);
				}
			}
		}

		return array(
			'valid'  => empty( $errors ),
			'errors' => $errors,
		);
	}
}

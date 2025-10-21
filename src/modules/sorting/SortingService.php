<?php
/**
 * Advanced Sorting Service
 *
 * Handles advanced sorting operations for tables
 *
 * @package ATablesCharts\Sorting\Services
 * @since 1.0.0
 */

namespace ATablesCharts\Sorting\Services;

/**
 * SortingService Class
 *
 * Responsibilities:
 * - Multi-column sorting
 * - Natural sorting (alphanumeric)
 * - Custom sort orders
 * - Type-aware sorting
 */
class SortingService {

	/**
	 * Sort data by multiple columns
	 *
	 * @param array $data        Data to sort.
	 * @param array $sort_config Sort configuration.
	 * @return array Sorted data
	 */
	public function multi_sort( $data, $sort_config ) {
		if ( empty( $data ) || empty( $sort_config ) ) {
			return $data;
		}

		// Create a copy to avoid modifying original
		$sorted_data = $data;

		usort( $sorted_data, function( $a, $b ) use ( $sort_config ) {
			foreach ( $sort_config as $sort ) {
				$column = $sort['column'];
				$direction = isset( $sort['direction'] ) ? $sort['direction'] : 'asc';
				$type = isset( $sort['type'] ) ? $sort['type'] : 'auto';

				// Get values
				$val_a = isset( $a[ $column ] ) ? $a[ $column ] : '';
				$val_b = isset( $b[ $column ] ) ? $b[ $column ] : '';

				// Compare based on type
				$comparison = $this->compare_values( $val_a, $val_b, $type );

				// If values are equal, continue to next sort column
				if ( $comparison === 0 ) {
					continue;
				}

				// Apply direction
				return $direction === 'desc' ? -$comparison : $comparison;
			}

			return 0;
		} );

		return $sorted_data;
	}

	/**
	 * Compare two values based on type
	 *
	 * @param mixed  $a    First value.
	 * @param mixed  $b    Second value.
	 * @param string $type Comparison type.
	 * @return int Comparison result (-1, 0, 1)
	 */
	private function compare_values( $a, $b, $type = 'auto' ) {
		// Auto-detect type if needed
		if ( $type === 'auto' ) {
			$type = $this->detect_value_type( $a, $b );
		}

		switch ( $type ) {
			case 'numeric':
				return $this->compare_numeric( $a, $b );

			case 'date':
				return $this->compare_dates( $a, $b );

			case 'natural':
				return $this->compare_natural( $a, $b );

			case 'string':
			default:
				return $this->compare_strings( $a, $b );
		}
	}

	/**
	 * Detect value type for sorting
	 *
	 * @param mixed $a First value.
	 * @param mixed $b Second value.
	 * @return string Detected type
	 */
	private function detect_value_type( $a, $b ) {
		// Check if both values are numeric
		if ( is_numeric( $a ) && is_numeric( $b ) ) {
			return 'numeric';
		}

		// Check if both values look like dates
		if ( $this->is_date( $a ) && $this->is_date( $b ) ) {
			return 'date';
		}

		// Check if values contain numbers (for natural sorting)
		if ( preg_match( '/\d+/', $a ) || preg_match( '/\d+/', $b ) ) {
			return 'natural';
		}

		return 'string';
	}

	/**
	 * Check if value is a date
	 *
	 * @param mixed $value Value to check.
	 * @return bool True if date
	 */
	private function is_date( $value ) {
		if ( empty( $value ) || ! is_string( $value ) ) {
			return false;
		}

		$timestamp = strtotime( $value );
		return $timestamp !== false;
	}

	/**
	 * Compare numeric values
	 *
	 * @param mixed $a First value.
	 * @param mixed $b Second value.
	 * @return int Comparison result
	 */
	private function compare_numeric( $a, $b ) {
		$num_a = floatval( $a );
		$num_b = floatval( $b );

		if ( $num_a < $num_b ) {
			return -1;
		} elseif ( $num_a > $num_b ) {
			return 1;
		}
		return 0;
	}

	/**
	 * Compare date values
	 *
	 * @param mixed $a First value.
	 * @param mixed $b Second value.
	 * @return int Comparison result
	 */
	private function compare_dates( $a, $b ) {
		$time_a = strtotime( $a );
		$time_b = strtotime( $b );

		if ( $time_a === false ) {
			$time_a = 0;
		}
		if ( $time_b === false ) {
			$time_b = 0;
		}

		if ( $time_a < $time_b ) {
			return -1;
		} elseif ( $time_a > $time_b ) {
			return 1;
		}
		return 0;
	}

	/**
	 * Compare strings naturally (alphanumeric)
	 *
	 * Natural sorting: Item1, Item2, Item10 (not Item1, Item10, Item2)
	 *
	 * @param mixed $a First value.
	 * @param mixed $b Second value.
	 * @return int Comparison result
	 */
	private function compare_natural( $a, $b ) {
		return strnatcasecmp( (string) $a, (string) $b );
	}

	/**
	 * Compare string values
	 *
	 * @param mixed $a First value.
	 * @param mixed $b Second value.
	 * @return int Comparison result
	 */
	private function compare_strings( $a, $b ) {
		return strcasecmp( (string) $a, (string) $b );
	}

	/**
	 * Sort data by single column (convenience method)
	 *
	 * @param array  $data      Data to sort.
	 * @param string $column    Column name.
	 * @param string $direction Sort direction (asc/desc).
	 * @param string $type      Sort type (auto/numeric/date/natural/string).
	 * @return array Sorted data
	 */
	public function sort_by_column( $data, $column, $direction = 'asc', $type = 'auto' ) {
		return $this->multi_sort( $data, array(
			array(
				'column'    => $column,
				'direction' => $direction,
				'type'      => $type,
			),
		) );
	}

	/**
	 * Get sort configuration from request
	 *
	 * @param array $request Request data.
	 * @return array Sort configuration
	 */
	public function parse_sort_request( $request ) {
		$sort_config = array();

		// Single column sort
		if ( isset( $request['sort_column'] ) && ! empty( $request['sort_column'] ) ) {
			$sort_config[] = array(
				'column'    => sanitize_text_field( $request['sort_column'] ),
				'direction' => isset( $request['sort_direction'] ) ? sanitize_text_field( $request['sort_direction'] ) : 'asc',
				'type'      => isset( $request['sort_type'] ) ? sanitize_text_field( $request['sort_type'] ) : 'auto',
			);
		}

		// Multi-column sort
		if ( isset( $request['sort_columns'] ) && is_array( $request['sort_columns'] ) ) {
			foreach ( $request['sort_columns'] as $sort ) {
				if ( isset( $sort['column'] ) && ! empty( $sort['column'] ) ) {
					$sort_config[] = array(
						'column'    => sanitize_text_field( $sort['column'] ),
						'direction' => isset( $sort['direction'] ) ? sanitize_text_field( $sort['direction'] ) : 'asc',
						'type'      => isset( $sort['type'] ) ? sanitize_text_field( $sort['type'] ) : 'auto',
					);
				}
			}
		}

		return $sort_config;
	}

	/**
	 * Sort with custom order
	 *
	 * @param array  $data         Data to sort.
	 * @param string $column       Column to sort by.
	 * @param array  $custom_order Custom order array.
	 * @return array Sorted data
	 */
	public function sort_by_custom_order( $data, $column, $custom_order ) {
		if ( empty( $data ) || empty( $custom_order ) ) {
			return $data;
		}

		// Create order map
		$order_map = array_flip( $custom_order );

		usort( $data, function( $a, $b ) use ( $column, $order_map, $custom_order ) {
			$val_a = isset( $a[ $column ] ) ? $a[ $column ] : '';
			$val_b = isset( $b[ $column ] ) ? $b[ $column ] : '';

			// Get positions in custom order
			$pos_a = isset( $order_map[ $val_a ] ) ? $order_map[ $val_a ] : 999999;
			$pos_b = isset( $order_map[ $val_b ] ) ? $order_map[ $val_b ] : 999999;

			// If both not in custom order, sort alphabetically
			if ( $pos_a === 999999 && $pos_b === 999999 ) {
				return strcasecmp( $val_a, $val_b );
			}

			return $pos_a - $pos_b;
		} );

		return $data;
	}

	/**
	 * Get predefined custom orders
	 *
	 * @return array Custom order presets
	 */
	public static function get_custom_order_presets() {
		return array(
			'days_of_week' => array(
				'label' => __( 'Days of Week', 'a-tables-charts' ),
				'order' => array( 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday' ),
			),
			'months' => array(
				'label' => __( 'Months', 'a-tables-charts' ),
				'order' => array( 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December' ),
			),
			'status' => array(
				'label' => __( 'Status (Active First)', 'a-tables-charts' ),
				'order' => array( 'Active', 'Pending', 'Inactive', 'Archived' ),
			),
			'priority' => array(
				'label' => __( 'Priority (High to Low)', 'a-tables-charts' ),
				'order' => array( 'Critical', 'High', 'Medium', 'Low' ),
			),
			'size' => array(
				'label' => __( 'Size (Small to Large)', 'a-tables-charts' ),
				'order' => array( 'XS', 'S', 'M', 'L', 'XL', 'XXL' ),
			),
		);
	}

	/**
	 * Apply saved sort configuration to data
	 *
	 * @param array $data        Data to sort.
	 * @param array $sort_config Saved sort configuration.
	 * @return array Sorted data
	 */
	public function apply_saved_sort( $data, $sort_config ) {
		if ( empty( $sort_config ) || ! is_array( $sort_config ) ) {
			return $data;
		}

		// Check if it's a custom order sort
		if ( isset( $sort_config['type'] ) && $sort_config['type'] === 'custom_order' ) {
			return $this->sort_by_custom_order(
				$data,
				$sort_config['column'],
				$sort_config['custom_order']
			);
		}

		// Regular multi-column sort
		return $this->multi_sort( $data, $sort_config );
	}

	/**
	 * Validate sort configuration
	 *
	 * @param array $sort_config Sort configuration.
	 * @param array $headers     Available column headers.
	 * @return array Validation result
	 */
	public function validate_sort_config( $sort_config, $headers ) {
		$errors = array();

		if ( empty( $sort_config ) ) {
			$errors[] = __( 'Sort configuration is empty.', 'a-tables-charts' );
			return array(
				'valid'  => false,
				'errors' => $errors,
			);
		}

		foreach ( $sort_config as $index => $sort ) {
			if ( ! isset( $sort['column'] ) || empty( $sort['column'] ) ) {
				$errors[] = sprintf(
					__( 'Sort rule %d: Column is required.', 'a-tables-charts' ),
					$index + 1
				);
			} elseif ( ! in_array( $sort['column'], $headers, true ) ) {
				$errors[] = sprintf(
					__( 'Sort rule %d: Column "%s" does not exist.', 'a-tables-charts' ),
					$index + 1,
					$sort['column']
				);
			}

			$valid_directions = array( 'asc', 'desc' );
			if ( isset( $sort['direction'] ) && ! in_array( $sort['direction'], $valid_directions, true ) ) {
				$errors[] = sprintf(
					__( 'Sort rule %d: Invalid direction. Must be "asc" or "desc".', 'a-tables-charts' ),
					$index + 1
				);
			}

			$valid_types = array( 'auto', 'numeric', 'date', 'natural', 'string' );
			if ( isset( $sort['type'] ) && ! in_array( $sort['type'], $valid_types, true ) ) {
				$errors[] = sprintf(
					__( 'Sort rule %d: Invalid type.', 'a-tables-charts' ),
					$index + 1
				);
			}
		}

		return array(
			'valid'  => empty( $errors ),
			'errors' => $errors,
		);
	}
}

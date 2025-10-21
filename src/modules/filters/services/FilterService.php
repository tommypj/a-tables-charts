<?php
/**
 * Filter Service
 *
 * Handles filter application and data filtering logic.
 *
 * @package ATablesCharts\Filters\Services
 * @since 1.0.5
 */

namespace ATablesCharts\Filters\Services;

use ATablesCharts\Filters\Types\Filter;
use ATablesCharts\Filters\Types\FilterPreset;

/**
 * FilterService Class
 *
 * Responsibilities:
 * - Apply filters to data sets
 * - Validate filter rules
 * - Handle filter matching logic
 * - Process filter combinations
 */
class FilterService {

	/**
	 * Apply filters to a data set
	 *
	 * @param array $data    Data array to filter.
	 * @param array $filters Array of Filter objects.
	 * @return array Filtered data
	 */
	public function apply_filters( array $data, array $filters ) {
		if ( empty( $filters ) ) {
			return $data;
		}

		$filtered_data = $data;

		foreach ( $filters as $filter ) {
			if ( ! $filter instanceof Filter ) {
				continue;
			}

			$filtered_data = $this->apply_single_filter( $filtered_data, $filter );
		}

		return $filtered_data;
	}

	/**
	 * Apply a single filter to data
	 *
	 * @param array  $data   Data array to filter.
	 * @param Filter $filter Filter object.
	 * @return array Filtered data
	 */
	private function apply_single_filter( array $data, Filter $filter ) {
		return array_filter(
			$data,
			function( $row ) use ( $filter ) {
				return $filter->matches( $row );
			}
		);
	}

	/**
	 * Apply a filter preset to data
	 *
	 * @param array        $data   Data array to filter.
	 * @param FilterPreset $preset FilterPreset object.
	 * @return array Filtered data
	 */
	public function apply_preset( array $data, FilterPreset $preset ) {
		return $this->apply_filters( $data, $preset->filters );
	}

	/**
	 * Validate filter against data structure
	 *
	 * Checks if the filter column exists in the data.
	 *
	 * @param Filter $filter Filter object.
	 * @param array  $data   Sample data row.
	 * @return array Validation result with 'valid' and 'errors' keys
	 */
	public function validate_filter_against_data( Filter $filter, array $data ) {
		$errors = array();

		// Check if column exists
		if ( ! isset( $data[ $filter->column ] ) ) {
			$errors[] = sprintf(
				/* translators: %s: column name */
				__( 'Column "%s" does not exist in data.', 'a-tables-charts' ),
				$filter->column
			);
		}

		// Validate filter itself
		$filter_validation = $filter->validate();
		if ( ! $filter_validation['valid'] ) {
			$errors = array_merge( $errors, $filter_validation['errors'] );
		}

		return array(
			'valid'  => empty( $errors ),
			'errors' => $errors,
		);
	}

	/**
	 * Get filter statistics for a data set
	 *
	 * @param array  $data   Original data.
	 * @param Filter $filter Filter to analyze.
	 * @return array Statistics array
	 */
	public function get_filter_statistics( array $data, Filter $filter ) {
		$original_count = count( $data );
		$filtered_data = $this->apply_single_filter( $data, $filter );
		$filtered_count = count( $filtered_data );

		return array(
			'original_count'  => $original_count,
			'filtered_count'  => $filtered_count,
			'removed_count'   => $original_count - $filtered_count,
			'removal_percent' => $original_count > 0 
				? round( ( ( $original_count - $filtered_count ) / $original_count ) * 100, 2 ) 
				: 0,
		);
	}

	/**
	 * Get available columns from data
	 *
	 * @param array $data Data array (should have at least one row).
	 * @return array Array of column names
	 */
	public function get_available_columns( array $data ) {
		if ( empty( $data ) ) {
			return array();
		}

		$first_row = reset( $data );

		if ( ! is_array( $first_row ) ) {
			return array();
		}

		return array_keys( $first_row );
	}

	/**
	 * Detect data type for a column
	 *
	 * @param array  $data   Data array.
	 * @param string $column Column name.
	 * @return string Data type (text|number|date|select)
	 */
	public function detect_column_type( array $data, string $column ) {
		if ( empty( $data ) ) {
			return 'text';
		}

		$sample_size = min( 100, count( $data ) );
		$samples = array_slice( $data, 0, $sample_size );

		$numeric_count = 0;
		$date_count = 0;
		$unique_values = array();

		foreach ( $samples as $row ) {
			if ( ! isset( $row[ $column ] ) ) {
				continue;
			}

			$value = $row[ $column ];

			// Check if numeric
			if ( is_numeric( $value ) ) {
				$numeric_count++;
			}

			// Check if date
			if ( $this->is_date_value( $value ) ) {
				$date_count++;
			}

			// Collect unique values
			if ( ! in_array( $value, $unique_values, true ) ) {
				$unique_values[] = $value;
			}
		}

		// If more than 80% are numeric, it's a number column
		if ( $numeric_count / $sample_size > 0.8 ) {
			return 'number';
		}

		// If more than 80% are dates, it's a date column
		if ( $date_count / $sample_size > 0.8 ) {
			return 'date';
		}

		// If unique values are less than 20 and less than 20% of sample, it's a select
		if ( count( $unique_values ) < 20 && count( $unique_values ) / $sample_size < 0.2 ) {
			return 'select';
		}

		// Default to text
		return 'text';
	}

	/**
	 * Check if value is a date
	 *
	 * @param mixed $value Value to check.
	 * @return bool True if date, false otherwise
	 */
	private function is_date_value( $value ) {
		if ( ! is_string( $value ) ) {
			return false;
		}

		$timestamp = strtotime( $value );

		// Check if it's a valid date and not just a number
		return $timestamp !== false && ! is_numeric( $value );
	}

	/**
	 * Get unique values for a column (for select filters)
	 *
	 * @param array  $data   Data array.
	 * @param string $column Column name.
	 * @return array Array of unique values
	 */
	public function get_column_unique_values( array $data, string $column ) {
		$values = array();

		foreach ( $data as $row ) {
			if ( isset( $row[ $column ] ) && ! in_array( $row[ $column ], $values, true ) ) {
				$values[] = $row[ $column ];
			}
		}

		sort( $values );

		return $values;
	}

	/**
	 * Build filter suggestion based on column data
	 *
	 * @param array  $data   Data array.
	 * @param string $column Column name.
	 * @return array Filter suggestion
	 */
	public function suggest_filter_for_column( array $data, string $column ) {
		$data_type = $this->detect_column_type( $data, $column );
		$unique_values = $this->get_column_unique_values( $data, $column );

		$suggestion = array(
			'column'        => $column,
			'data_type'     => $data_type,
			'unique_count'  => count( $unique_values ),
		);

		switch ( $data_type ) {
			case 'number':
				$suggestion['suggested_operators'] = array( 'equals', 'greater_than', 'less_than', 'between' );
				break;

			case 'date':
				$suggestion['suggested_operators'] = array( 'date_equals', 'date_after', 'date_before', 'date_between' );
				break;

			case 'select':
				$suggestion['suggested_operators'] = array( 'equals', 'not_equals', 'in', 'not_in' );
				$suggestion['options'] = $unique_values;
				break;

			default:
				$suggestion['suggested_operators'] = array( 'contains', 'equals', 'starts_with', 'ends_with' );
				break;
		}

		return $suggestion;
	}

	/**
	 * Combine multiple filter presets (OR logic)
	 *
	 * @param array $data    Data array to filter.
	 * @param array $presets Array of FilterPreset objects.
	 * @return array Filtered data (union of all preset results)
	 */
	public function apply_presets_with_or( array $data, array $presets ) {
		if ( empty( $presets ) ) {
			return $data;
		}

		$all_results = array();

		foreach ( $presets as $preset ) {
			if ( ! $preset instanceof FilterPreset ) {
				continue;
			}

			$preset_results = $this->apply_preset( $data, $preset );

			// Merge results (union)
			foreach ( $preset_results as $row ) {
				$row_hash = md5( wp_json_encode( $row ) );
				$all_results[ $row_hash ] = $row;
			}
		}

		return array_values( $all_results );
	}

	/**
	 * Test filter before applying
	 *
	 * Returns preview of how many rows would be filtered.
	 *
	 * @param array  $data   Data array.
	 * @param Filter $filter Filter to test.
	 * @return array Test results
	 */
	public function test_filter( array $data, Filter $filter ) {
		$filtered_data = $this->apply_single_filter( $data, $filter );

		return array(
			'original_count' => count( $data ),
			'filtered_count' => count( $filtered_data ),
			'sample_filtered' => array_slice( $filtered_data, 0, 5 ),
			'filter_valid'   => true,
		);
	}
}

<?php
/**
 * Filter Entity
 *
 * Represents a single filter rule.
 *
 * @package ATablesCharts\Filters\Types
 * @since 1.0.5
 */

namespace ATablesCharts\Filters\Types;

/**
 * Filter Class
 *
 * Represents a single filter condition to be applied to table data.
 */
class Filter {

	/**
	 * Column name to filter
	 *
	 * @var string
	 */
	public $column;

	/**
	 * Filter operator
	 *
	 * @var string
	 */
	public $operator;

	/**
	 * Filter value(s)
	 *
	 * @var mixed Can be string, number, array (for multi-select/between)
	 */
	public $value;

	/**
	 * Data type of the column
	 *
	 * @var string text, number, date, select
	 */
	public $data_type;

	/**
	 * Whether this filter is case-sensitive (for text filters)
	 *
	 * @var bool
	 */
	public $case_sensitive;

	/**
	 * Constructor
	 *
	 * @param array $data Filter data.
	 */
	public function __construct( $data = array() ) {
		$this->column         = isset( $data['column'] ) ? $data['column'] : '';
		$this->operator       = isset( $data['operator'] ) ? $data['operator'] : FilterOperator::CONTAINS;
		$this->value          = isset( $data['value'] ) ? $data['value'] : '';
		$this->data_type      = isset( $data['data_type'] ) ? $data['data_type'] : 'text';
		$this->case_sensitive = isset( $data['case_sensitive'] ) ? (bool) $data['case_sensitive'] : false;
	}

	/**
	 * Validate filter
	 *
	 * @return array Validation result with 'valid' and 'errors' keys
	 */
	public function validate() {
		$errors = array();

		// Column is required
		if ( empty( $this->column ) ) {
			$errors[] = __( 'Filter column is required.', 'a-tables-charts' );
		}

		// Operator is required
		if ( empty( $this->operator ) ) {
			$errors[] = __( 'Filter operator is required.', 'a-tables-charts' );
		}

		// Validate operator
		if ( ! FilterOperator::is_valid( $this->operator ) ) {
			$errors[] = __( 'Invalid filter operator.', 'a-tables-charts' );
		}

		// Value is required for most operators
		if ( FilterOperator::requires_value( $this->operator ) ) {
			if ( $this->value === '' || $this->value === null ) {
				$errors[] = __( 'Filter value is required for this operator.', 'a-tables-charts' );
			}
		}

		// Validate "between" requires array with 2 values
		if ( FilterOperator::requires_two_values( $this->operator ) ) {
			if ( ! is_array( $this->value ) || count( $this->value ) !== 2 ) {
				$errors[] = __( 'Between operator requires two values.', 'a-tables-charts' );
			}
		}

		// Validate IN/NOT_IN requires array
		if ( in_array( $this->operator, array( FilterOperator::IN, FilterOperator::NOT_IN ), true ) ) {
			if ( ! is_array( $this->value ) ) {
				$errors[] = __( 'Multi-select operators require an array of values.', 'a-tables-charts' );
			}
		}

		return array(
			'valid'  => empty( $errors ),
			'errors' => $errors,
		);
	}

	/**
	 * Convert filter to array
	 *
	 * @return array Filter data as array
	 */
	public function to_array() {
		return array(
			'column'         => $this->column,
			'operator'       => $this->operator,
			'value'          => $this->value,
			'data_type'      => $this->data_type,
			'case_sensitive' => $this->case_sensitive,
		);
	}

	/**
	 * Create filter from array
	 *
	 * @param array $data Filter data array.
	 * @return Filter Filter instance
	 */
	public static function from_array( $data ) {
		return new self( $data );
	}

	/**
	 * Get filter display label
	 *
	 * @return string Human-readable filter description
	 */
	public function get_label() {
		$operator_label = FilterOperator::get_label( $this->operator );
		$value_label = $this->get_value_label();

		return sprintf(
			'%s %s %s',
			$this->column,
			$operator_label,
			$value_label
		);
	}

	/**
	 * Get value display label
	 *
	 * @return string Human-readable value
	 */
	private function get_value_label() {
		// No value needed for these operators
		if ( ! FilterOperator::requires_value( $this->operator ) ) {
			return '';
		}

		// Handle array values
		if ( is_array( $this->value ) ) {
			if ( FilterOperator::requires_two_values( $this->operator ) ) {
				return implode( ' and ', $this->value );
			} else {
				return implode( ', ', $this->value );
			}
		}

		return (string) $this->value;
	}

	/**
	 * Check if filter matches a row
	 *
	 * @param array $row Row data.
	 * @return bool True if row matches filter, false otherwise
	 */
	public function matches( $row ) {
		// Get the column value from row
		if ( ! isset( $row[ $this->column ] ) ) {
			// Column doesn't exist in row
			return $this->operator === FilterOperator::IS_EMPTY;
		}

		$cell_value = $row[ $this->column ];

		// Handle empty checks
		if ( $this->operator === FilterOperator::IS_EMPTY ) {
			return empty( $cell_value );
		}

		if ( $this->operator === FilterOperator::IS_NOT_EMPTY ) {
			return ! empty( $cell_value );
		}

		// Apply type-specific matching
		switch ( $this->data_type ) {
			case 'number':
				return $this->matches_number( $cell_value );
			case 'date':
				return $this->matches_date( $cell_value );
			case 'select':
				return $this->matches_select( $cell_value );
			case 'text':
			default:
				return $this->matches_text( $cell_value );
		}
	}

	/**
	 * Match text value
	 *
	 * @param mixed $cell_value Cell value.
	 * @return bool True if matches
	 */
	private function matches_text( $cell_value ) {
		$cell_value = (string) $cell_value;
		$filter_value = (string) $this->value;

		// Case sensitivity handling
		if ( ! $this->case_sensitive ) {
			$cell_value = strtolower( $cell_value );
			$filter_value = strtolower( $filter_value );
		}

		switch ( $this->operator ) {
			case FilterOperator::CONTAINS:
				return strpos( $cell_value, $filter_value ) !== false;

			case FilterOperator::NOT_CONTAINS:
				return strpos( $cell_value, $filter_value ) === false;

			case FilterOperator::EQUALS:
				return $cell_value === $filter_value;

			case FilterOperator::NOT_EQUALS:
				return $cell_value !== $filter_value;

			case FilterOperator::STARTS_WITH:
				return strpos( $cell_value, $filter_value ) === 0;

			case FilterOperator::ENDS_WITH:
				return substr( $cell_value, -strlen( $filter_value ) ) === $filter_value;

			default:
				return false;
		}
	}

	/**
	 * Match number value
	 *
	 * @param mixed $cell_value Cell value.
	 * @return bool True if matches
	 */
	private function matches_number( $cell_value ) {
		$cell_value = is_numeric( $cell_value ) ? (float) $cell_value : 0;
		$filter_value = is_numeric( $this->value ) ? (float) $this->value : 0;

		switch ( $this->operator ) {
			case FilterOperator::EQUALS:
				return $cell_value === $filter_value;

			case FilterOperator::NOT_EQUALS:
				return $cell_value !== $filter_value;

			case FilterOperator::GREATER_THAN:
				return $cell_value > $filter_value;

			case FilterOperator::LESS_THAN:
				return $cell_value < $filter_value;

			case FilterOperator::GREATER_THAN_OR_EQUAL:
				return $cell_value >= $filter_value;

			case FilterOperator::LESS_THAN_OR_EQUAL:
				return $cell_value <= $filter_value;

			case FilterOperator::BETWEEN:
				if ( is_array( $this->value ) && count( $this->value ) === 2 ) {
					$min = (float) $this->value[0];
					$max = (float) $this->value[1];
					return $cell_value >= $min && $cell_value <= $max;
				}
				return false;

			default:
				return false;
		}
	}

	/**
	 * Match date value
	 *
	 * @param mixed $cell_value Cell value.
	 * @return bool True if matches
	 */
	private function matches_date( $cell_value ) {
		$cell_timestamp = strtotime( $cell_value );
		$filter_timestamp = strtotime( $this->value );

		if ( false === $cell_timestamp || false === $filter_timestamp ) {
			return false;
		}

		switch ( $this->operator ) {
			case FilterOperator::DATE_EQUALS:
				return date( 'Y-m-d', $cell_timestamp ) === date( 'Y-m-d', $filter_timestamp );

			case FilterOperator::DATE_BEFORE:
				return $cell_timestamp < $filter_timestamp;

			case FilterOperator::DATE_AFTER:
				return $cell_timestamp > $filter_timestamp;

			case FilterOperator::DATE_BETWEEN:
				if ( is_array( $this->value ) && count( $this->value ) === 2 ) {
					$start = strtotime( $this->value[0] );
					$end = strtotime( $this->value[1] );
					return $cell_timestamp >= $start && $cell_timestamp <= $end;
				}
				return false;

			default:
				return false;
		}
	}

	/**
	 * Match select value
	 *
	 * @param mixed $cell_value Cell value.
	 * @return bool True if matches
	 */
	private function matches_select( $cell_value ) {
		$cell_value = (string) $cell_value;

		switch ( $this->operator ) {
			case FilterOperator::IN:
				return in_array( $cell_value, (array) $this->value, true );

			case FilterOperator::NOT_IN:
				return ! in_array( $cell_value, (array) $this->value, true );

			default:
				return false;
		}
	}
}

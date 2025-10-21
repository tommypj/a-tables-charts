<?php
/**
 * Filter Operator
 *
 * Defines all available filter operators and their properties.
 *
 * @package ATablesCharts\Filters\Types
 * @since 1.0.5
 */

namespace ATablesCharts\Filters\Types;

/**
 * FilterOperator Class
 *
 * Enum-style class defining filter operators and their behavior.
 */
class FilterOperator {

	/**
	 * Text operators
	 */
	const CONTAINS = 'contains';
	const NOT_CONTAINS = 'not_contains';
	const EQUALS = 'equals';
	const NOT_EQUALS = 'not_equals';
	const STARTS_WITH = 'starts_with';
	const ENDS_WITH = 'ends_with';
	const IS_EMPTY = 'is_empty';
	const IS_NOT_EMPTY = 'is_not_empty';

	/**
	 * Number operators
	 */
	const GREATER_THAN = 'greater_than';
	const LESS_THAN = 'less_than';
	const GREATER_THAN_OR_EQUAL = 'greater_than_or_equal';
	const LESS_THAN_OR_EQUAL = 'less_than_or_equal';
	const BETWEEN = 'between';

	/**
	 * Date operators
	 */
	const DATE_EQUALS = 'date_equals';
	const DATE_BEFORE = 'date_before';
	const DATE_AFTER = 'date_after';
	const DATE_BETWEEN = 'date_between';

	/**
	 * Multi-select operators
	 */
	const IN = 'in';
	const NOT_IN = 'not_in';

	/**
	 * Get all operators for a data type
	 *
	 * @param string $data_type Data type (text, number, date, select).
	 * @return array Array of operators
	 */
	public static function get_operators_for_type( $data_type ) {
		$operators = array();

		switch ( $data_type ) {
			case 'text':
				$operators = array(
					self::CONTAINS        => __( 'Contains', 'a-tables-charts' ),
					self::NOT_CONTAINS    => __( 'Does not contain', 'a-tables-charts' ),
					self::EQUALS          => __( 'Equals', 'a-tables-charts' ),
					self::NOT_EQUALS      => __( 'Does not equal', 'a-tables-charts' ),
					self::STARTS_WITH     => __( 'Starts with', 'a-tables-charts' ),
					self::ENDS_WITH       => __( 'Ends with', 'a-tables-charts' ),
					self::IS_EMPTY        => __( 'Is empty', 'a-tables-charts' ),
					self::IS_NOT_EMPTY    => __( 'Is not empty', 'a-tables-charts' ),
				);
				break;

			case 'number':
				$operators = array(
					self::EQUALS                => __( 'Equals', 'a-tables-charts' ),
					self::NOT_EQUALS            => __( 'Does not equal', 'a-tables-charts' ),
					self::GREATER_THAN          => __( 'Greater than', 'a-tables-charts' ),
					self::LESS_THAN             => __( 'Less than', 'a-tables-charts' ),
					self::GREATER_THAN_OR_EQUAL => __( 'Greater than or equal', 'a-tables-charts' ),
					self::LESS_THAN_OR_EQUAL    => __( 'Less than or equal', 'a-tables-charts' ),
					self::BETWEEN               => __( 'Between', 'a-tables-charts' ),
					self::IS_EMPTY              => __( 'Is empty', 'a-tables-charts' ),
					self::IS_NOT_EMPTY          => __( 'Is not empty', 'a-tables-charts' ),
				);
				break;

			case 'date':
				$operators = array(
					self::DATE_EQUALS  => __( 'Equals', 'a-tables-charts' ),
					self::DATE_BEFORE  => __( 'Before', 'a-tables-charts' ),
					self::DATE_AFTER   => __( 'After', 'a-tables-charts' ),
					self::DATE_BETWEEN => __( 'Between', 'a-tables-charts' ),
					self::IS_EMPTY     => __( 'Is empty', 'a-tables-charts' ),
					self::IS_NOT_EMPTY => __( 'Is not empty', 'a-tables-charts' ),
				);
				break;

			case 'select':
				$operators = array(
					self::IN           => __( 'Is one of', 'a-tables-charts' ),
					self::NOT_IN       => __( 'Is not one of', 'a-tables-charts' ),
					self::IS_EMPTY     => __( 'Is empty', 'a-tables-charts' ),
					self::IS_NOT_EMPTY => __( 'Is not empty', 'a-tables-charts' ),
				);
				break;

			default:
				// Default to text operators
				$operators = self::get_operators_for_type( 'text' );
				break;
		}

		return $operators;
	}

	/**
	 * Check if operator requires a value
	 *
	 * @param string $operator Operator constant.
	 * @return bool True if requires value, false otherwise
	 */
	public static function requires_value( $operator ) {
		$no_value_operators = array(
			self::IS_EMPTY,
			self::IS_NOT_EMPTY,
		);

		return ! in_array( $operator, $no_value_operators, true );
	}

	/**
	 * Check if operator requires two values (range)
	 *
	 * @param string $operator Operator constant.
	 * @return bool True if requires two values, false otherwise
	 */
	public static function requires_two_values( $operator ) {
		$two_value_operators = array(
			self::BETWEEN,
			self::DATE_BETWEEN,
		);

		return in_array( $two_value_operators, $two_value_operators, true );
	}

	/**
	 * Get all available operators
	 *
	 * @return array All operators across all types
	 */
	public static function get_all_operators() {
		return array(
			self::CONTAINS,
			self::NOT_CONTAINS,
			self::EQUALS,
			self::NOT_EQUALS,
			self::STARTS_WITH,
			self::ENDS_WITH,
			self::GREATER_THAN,
			self::LESS_THAN,
			self::GREATER_THAN_OR_EQUAL,
			self::LESS_THAN_OR_EQUAL,
			self::BETWEEN,
			self::DATE_EQUALS,
			self::DATE_BEFORE,
			self::DATE_AFTER,
			self::DATE_BETWEEN,
			self::IN,
			self::NOT_IN,
			self::IS_EMPTY,
			self::IS_NOT_EMPTY,
		);
	}

	/**
	 * Validate operator
	 *
	 * @param string $operator Operator to validate.
	 * @return bool True if valid, false otherwise
	 */
	public static function is_valid( $operator ) {
		return in_array( $operator, self::get_all_operators(), true );
	}

	/**
	 * Get operator label
	 *
	 * @param string $operator Operator constant.
	 * @return string Operator label
	 */
	public static function get_label( $operator ) {
		$labels = array(
			self::CONTAINS              => __( 'Contains', 'a-tables-charts' ),
			self::NOT_CONTAINS          => __( 'Does not contain', 'a-tables-charts' ),
			self::EQUALS                => __( 'Equals', 'a-tables-charts' ),
			self::NOT_EQUALS            => __( 'Does not equal', 'a-tables-charts' ),
			self::STARTS_WITH           => __( 'Starts with', 'a-tables-charts' ),
			self::ENDS_WITH             => __( 'Ends with', 'a-tables-charts' ),
			self::GREATER_THAN          => __( 'Greater than', 'a-tables-charts' ),
			self::LESS_THAN             => __( 'Less than', 'a-tables-charts' ),
			self::GREATER_THAN_OR_EQUAL => __( 'Greater than or equal', 'a-tables-charts' ),
			self::LESS_THAN_OR_EQUAL    => __( 'Less than or equal', 'a-tables-charts' ),
			self::BETWEEN               => __( 'Between', 'a-tables-charts' ),
			self::DATE_EQUALS           => __( 'Equals', 'a-tables-charts' ),
			self::DATE_BEFORE           => __( 'Before', 'a-tables-charts' ),
			self::DATE_AFTER            => __( 'After', 'a-tables-charts' ),
			self::DATE_BETWEEN          => __( 'Between', 'a-tables-charts' ),
			self::IN                    => __( 'Is one of', 'a-tables-charts' ),
			self::NOT_IN                => __( 'Is not one of', 'a-tables-charts' ),
			self::IS_EMPTY              => __( 'Is empty', 'a-tables-charts' ),
			self::IS_NOT_EMPTY          => __( 'Is not empty', 'a-tables-charts' ),
		);

		return isset( $labels[ $operator ] ) ? $labels[ $operator ] : $operator;
	}
}

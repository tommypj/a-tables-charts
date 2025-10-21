<?php
/**
 * Conditional Formatter
 *
 * Apply conditional formatting to table cells based on rules
 *
 * @package ATablesCharts\Shared\Utils
 * @since 1.0.0
 */

namespace ATablesCharts\Shared\Utils;

/**
 * ConditionalFormatter Class
 *
 * Responsibilities:
 * - Evaluate conditional rules
 * - Apply formatting based on conditions
 * - Support multiple rule types
 * - Handle numeric and text comparisons
 */
class ConditionalFormatter {

	/**
	 * Apply conditional formatting to a value
	 *
	 * @param mixed $value Cell value.
	 * @param array $rules Array of conditional rules.
	 * @param array $row_data Complete row data (for row-level conditions).
	 * @return array CSS classes and inline styles to apply
	 */
	public static function apply( $value, $rules = array(), $row_data = array() ) {
		if ( empty( $rules ) ) {
			return array(
				'classes' => '',
				'styles'  => '',
			);
		}

		$classes = array();
		$styles = array();

		foreach ( $rules as $rule ) {
			if ( self::evaluate_rule( $value, $rule, $row_data ) ) {
				// Apply formatting from this rule
				if ( ! empty( $rule['css_class'] ) ) {
					$classes[] = sanitize_html_class( $rule['css_class'] );
				}

				if ( ! empty( $rule['background_color'] ) ) {
					$styles[] = 'background-color: ' . esc_attr( $rule['background_color'] );
				}

				if ( ! empty( $rule['text_color'] ) ) {
					$styles[] = 'color: ' . esc_attr( $rule['text_color'] );
				}

				if ( ! empty( $rule['font_weight'] ) ) {
					$styles[] = 'font-weight: ' . esc_attr( $rule['font_weight'] );
				}

				if ( ! empty( $rule['icon'] ) ) {
					// Icons will be handled separately
				}

				// If stop_if_true is set, don't evaluate further rules
				if ( ! empty( $rule['stop_if_true'] ) && $rule['stop_if_true'] === true ) {
					break;
				}
			}
		}

		return array(
			'classes' => implode( ' ', $classes ),
			'styles'  => implode( '; ', $styles ),
		);
	}

	/**
	 * Evaluate a conditional rule
	 *
	 * @param mixed $value    Cell value.
	 * @param array $rule     Rule definition.
	 * @param array $row_data Complete row data.
	 * @return bool True if condition is met
	 */
	public static function evaluate_rule( $value, $rule, $row_data = array() ) {
		if ( empty( $rule['condition'] ) ) {
			return false;
		}

		$condition = $rule['condition'];
		$operator = isset( $rule['operator'] ) ? $rule['operator'] : 'equals';
		$compare_value = isset( $rule['value'] ) ? $rule['value'] : '';

		// Get the actual value to compare
		$actual_value = $value;

		// If condition references another column, get that value
		if ( isset( $rule['column'] ) && isset( $row_data[ $rule['column'] ] ) ) {
			$actual_value = $row_data[ $rule['column'] ];
		}

		return self::compare( $actual_value, $operator, $compare_value );
	}

	/**
	 * Compare values based on operator
	 *
	 * @param mixed  $value1   First value.
	 * @param string $operator Comparison operator.
	 * @param mixed  $value2   Second value.
	 * @return bool Comparison result
	 */
	public static function compare( $value1, $operator, $value2 ) {
		// Convert to appropriate types for comparison
		$is_numeric = is_numeric( $value1 ) && is_numeric( $value2 );

		if ( $is_numeric ) {
			$value1 = floatval( $value1 );
			$value2 = floatval( $value2 );
		} else {
			$value1 = (string) $value1;
			$value2 = (string) $value2;
		}

		switch ( $operator ) {
			case 'equals':
			case '==':
			case '=':
				return $value1 == $value2;

			case 'not_equals':
			case '!=':
			case '<>':
				return $value1 != $value2;

			case 'greater_than':
			case '>':
				return $is_numeric && $value1 > $value2;

			case 'greater_than_or_equal':
			case '>=':
				return $is_numeric && $value1 >= $value2;

			case 'less_than':
			case '<':
				return $is_numeric && $value1 < $value2;

			case 'less_than_or_equal':
			case '<=':
				return $is_numeric && $value1 <= $value2;

			case 'contains':
				return stripos( $value1, $value2 ) !== false;

			case 'not_contains':
				return stripos( $value1, $value2 ) === false;

			case 'starts_with':
				return stripos( $value1, $value2 ) === 0;

			case 'ends_with':
				$length = strlen( $value2 );
				return substr( $value1, -$length ) === $value2;

			case 'is_empty':
				return empty( $value1 );

			case 'is_not_empty':
				return ! empty( $value1 );

			case 'between':
				if ( ! $is_numeric ) {
					return false;
				}
				$range = explode( ',', $value2 );
				if ( count( $range ) !== 2 ) {
					return false;
				}
				$min = floatval( trim( $range[0] ) );
				$max = floatval( trim( $range[1] ) );
				return $value1 >= $min && $value1 <= $max;

			case 'in':
				$list = array_map( 'trim', explode( ',', $value2 ) );
				return in_array( $value1, $list, false );

			case 'not_in':
				$list = array_map( 'trim', explode( ',', $value2 ) );
				return ! in_array( $value1, $list, false );

			default:
				return false;
		}
	}

	/**
	 * Get predefined formatting presets
	 *
	 * @return array Preset configurations
	 */
	public static function get_presets() {
		return array(
			'positive_negative' => array(
				'name'        => 'Positive/Negative',
				'description' => 'Highlight positive values in green, negative in red',
				'rules'       => array(
					array(
						'operator'         => '>',
						'value'            => 0,
						'background_color' => '#d1e7dd',
						'text_color'       => '#0f5132',
						'font_weight'      => '600',
					),
					array(
						'operator'         => '<',
						'value'            => 0,
						'background_color' => '#f8d7da',
						'text_color'       => '#842029',
						'font_weight'      => '600',
					),
				),
			),
			'traffic_light' => array(
				'name'        => 'Traffic Light (Red/Yellow/Green)',
				'description' => 'Color-code values based on thresholds',
				'rules'       => array(
					array(
						'operator'         => '<',
						'value'            => 33,
						'background_color' => '#f8d7da',
						'text_color'       => '#842029',
					),
					array(
						'operator'         => 'between',
						'value'            => '33,66',
						'background_color' => '#fff3cd',
						'text_color'       => '#664d03',
					),
					array(
						'operator'         => '>',
						'value'            => 66,
						'background_color' => '#d1e7dd',
						'text_color'       => '#0f5132',
					),
				),
			),
			'status_badges' => array(
				'name'        => 'Status Badges',
				'description' => 'Color-code common status values',
				'rules'       => array(
					array(
						'operator'  => 'in',
						'value'     => 'active,approved,completed,success,yes',
						'css_class' => 'atables-badge atables-badge-success',
					),
					array(
						'operator'  => 'in',
						'value'     => 'pending,in progress,processing',
						'css_class' => 'atables-badge atables-badge-warning',
					),
					array(
						'operator'  => 'in',
						'value'     => 'inactive,rejected,failed,error,no',
						'css_class' => 'atables-badge atables-badge-danger',
					),
				),
			),
			'high_low' => array(
				'name'        => 'Highlight High/Low Values',
				'description' => 'Emphasize extreme values',
				'rules'       => array(
					array(
						'operator'         => '>',
						'value'            => 1000,
						'background_color' => '#0d6efd',
						'text_color'       => '#ffffff',
						'font_weight'      => 'bold',
					),
					array(
						'operator'         => '<',
						'value'            => 100,
						'background_color' => '#dc3545',
						'text_color'       => '#ffffff',
						'font_weight'      => 'bold',
					),
				),
			),
			'priority' => array(
				'name'        => 'Priority Levels',
				'description' => 'Color-code priority values',
				'rules'       => array(
					array(
						'operator'         => 'in',
						'value'            => 'critical,urgent,high',
						'background_color' => '#dc3545',
						'text_color'       => '#ffffff',
						'font_weight'      => 'bold',
					),
					array(
						'operator'         => 'equals',
						'value'            => 'medium',
						'background_color' => '#ffc107',
						'text_color'       => '#000000',
					),
					array(
						'operator'         => 'in',
						'value'            => 'low,minor',
						'background_color' => '#0dcaf0',
						'text_color'       => '#000000',
					),
				),
			),
			'percentage_scale' => array(
				'name'        => 'Percentage Scale',
				'description' => 'Progressive coloring from 0-100%',
				'rules'       => array(
					array(
						'operator'         => '<',
						'value'            => 25,
						'background_color' => '#dc3545',
						'text_color'       => '#ffffff',
					),
					array(
						'operator'         => 'between',
						'value'            => '25,50',
						'background_color' => '#ffc107',
						'text_color'       => '#000000',
					),
					array(
						'operator'         => 'between',
						'value'            => '50,75',
						'background_color' => '#0dcaf0',
						'text_color'       => '#000000',
					),
					array(
						'operator'         => '>=',
						'value'            => 75,
						'background_color' => '#198754',
						'text_color'       => '#ffffff',
					),
				),
			),
		);
	}

	/**
	 * Apply a preset to a value
	 *
	 * @param mixed  $value       Cell value.
	 * @param string $preset_name Preset name.
	 * @param array  $row_data    Complete row data.
	 * @return array CSS classes and styles
	 */
	public static function apply_preset( $value, $preset_name, $row_data = array() ) {
		$presets = self::get_presets();

		if ( ! isset( $presets[ $preset_name ] ) ) {
			return array(
				'classes' => '',
				'styles'  => '',
			);
		}

		$preset = $presets[ $preset_name ];
		return self::apply( $value, $preset['rules'], $row_data );
	}

	/**
	 * Create a simple rule
	 *
	 * Helper method to create a rule with minimal configuration
	 *
	 * @param string $operator        Comparison operator.
	 * @param mixed  $value           Value to compare against.
	 * @param string $background      Background color.
	 * @param string $text_color      Text color (optional).
	 * @return array Rule definition
	 */
	public static function create_rule( $operator, $value, $background, $text_color = null ) {
		$rule = array(
			'operator'         => $operator,
			'value'            => $value,
			'background_color' => $background,
		);

		if ( $text_color ) {
			$rule['text_color'] = $text_color;
		}

		return $rule;
	}

	/**
	 * Get supported operators
	 *
	 * @return array Operator definitions
	 */
	public static function get_operators() {
		return array(
			'equals'                  => 'Equals (=)',
			'not_equals'              => 'Not Equals (≠)',
			'greater_than'            => 'Greater Than (>)',
			'greater_than_or_equal'   => 'Greater Than or Equal (≥)',
			'less_than'               => 'Less Than (<)',
			'less_than_or_equal'      => 'Less Than or Equal (≤)',
			'between'                 => 'Between',
			'contains'                => 'Contains',
			'not_contains'            => 'Does Not Contain',
			'starts_with'             => 'Starts With',
			'ends_with'               => 'Ends With',
			'is_empty'                => 'Is Empty',
			'is_not_empty'            => 'Is Not Empty',
			'in'                      => 'In List',
			'not_in'                  => 'Not In List',
		);
	}
}

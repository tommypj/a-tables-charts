<?php
/**
 * Conditional Formatting Service
 *
 * @package ATablesCharts\Conditional\Services
 */

namespace ATablesCharts\Conditional\Services;

class ConditionalFormattingService {

	/**
	 * Get available operators
	 *
	 * @return array Operators
	 */
	public function get_available_operators() {
		return array(
			'equals' => __( 'Equals', 'a-tables-charts' ),
			'not_equals' => __( 'Not Equals', 'a-tables-charts' ),
			'contains' => __( 'Contains', 'a-tables-charts' ),
			'not_contains' => __( 'Does Not Contain', 'a-tables-charts' ),
			'starts_with' => __( 'Starts With', 'a-tables-charts' ),
			'ends_with' => __( 'Ends With', 'a-tables-charts' ),
			'greater_than' => __( 'Greater Than', 'a-tables-charts' ),
			'less_than' => __( 'Less Than', 'a-tables-charts' ),
			'greater_equal' => __( 'Greater Than or Equal', 'a-tables-charts' ),
			'less_equal' => __( 'Less Than or Equal', 'a-tables-charts' ),
			'between' => __( 'Between', 'a-tables-charts' ),
			'is_empty' => __( 'Is Empty', 'a-tables-charts' ),
			'is_not_empty' => __( 'Is Not Empty', 'a-tables-charts' ),
			'is_number' => __( 'Is Number', 'a-tables-charts' ),
			'is_text' => __( 'Is Text', 'a-tables-charts' ),
		);
	}

	/**
	 * Get preset rules
	 *
	 * @return array Presets
	 */
	public function get_presets() {
		return array(
			'highlight_positive' => array(
				'label' => __( 'Highlight Positive Numbers', 'a-tables-charts' ),
				'rules' => array(
					array(
						'operator' => 'greater_than',
						'value' => 0,
						'background_color' => '#d5f4e6',
						'text_color' => '#0a7340',
					),
				),
			),
			'highlight_negative' => array(
				'label' => __( 'Highlight Negative Numbers', 'a-tables-charts' ),
				'rules' => array(
					array(
						'operator' => 'less_than',
						'value' => 0,
						'background_color' => '#fce8e8',
						'text_color' => '#a72a2c',
					),
				),
			),
			'highlight_empty' => array(
				'label' => __( 'Highlight Empty Cells', 'a-tables-charts' ),
				'rules' => array(
					array(
						'operator' => 'is_empty',
						'background_color' => '#fff3cd',
						'text_color' => '#856404',
					),
				),
			),
			'status_colors' => array(
				'label' => __( 'Status Colors', 'a-tables-charts' ),
				'rules' => array(
					array(
						'operator' => 'equals',
						'value' => 'Active',
						'background_color' => '#d5f4e6',
						'text_color' => '#0a7340',
					),
					array(
						'operator' => 'equals',
						'value' => 'Inactive',
						'background_color' => '#f0f0f1',
						'text_color' => '#646970',
					),
				),
			),
		);
	}

	/**
	 * Apply conditional formatting to data
	 *
	 * @param array $data  Table data.
	 * @param array $rules Formatting rules.
	 * @return array Formatted data
	 */
	public function apply_formatting( $data, $rules ) {
		if ( empty( $rules ) ) {
			return $data;
		}

		$formatted = array();
		foreach ( $data as $row_index => $row ) {
			$formatted_row = $row;
			foreach ( $rules as $rule ) {
				if ( isset( $rule['column'] ) && isset( $row[ $rule['column'] ] ) ) {
					$cell_value = $row[ $rule['column'] ];
					if ( $this->check_condition( $cell_value, $rule ) ) {
						$formatted_row[ $rule['column'] . '_style' ] = array(
							'background_color' => $rule['background_color'] ?? '',
							'text_color' => $rule['text_color'] ?? '',
							'font_weight' => $rule['font_weight'] ?? 'normal',
						);
					}
				}
			}
			$formatted[] = $formatted_row;
		}

		return $formatted;
	}

	/**
	 * Check if condition is met
	 *
	 * @param mixed $value Cell value.
	 * @param array $rule  Rule to check.
	 * @return bool Condition met
	 */
	private function check_condition( $value, $rule ) {
		$operator = $rule['operator'] ?? 'equals';
		$compare = $rule['value'] ?? '';

		switch ( $operator ) {
			case 'equals':
				return $value == $compare;
			case 'not_equals':
				return $value != $compare;
			case 'contains':
				return stripos( $value, $compare ) !== false;
			case 'not_contains':
				return stripos( $value, $compare ) === false;
			case 'starts_with':
				return stripos( $value, $compare ) === 0;
			case 'ends_with':
				return substr( $value, -strlen( $compare ) ) === $compare;
			case 'greater_than':
				return is_numeric( $value ) && $value > $compare;
			case 'less_than':
				return is_numeric( $value ) && $value < $compare;
			case 'greater_equal':
				return is_numeric( $value ) && $value >= $compare;
			case 'less_equal':
				return is_numeric( $value ) && $value <= $compare;
			case 'is_empty':
				return empty( $value );
			case 'is_not_empty':
				return ! empty( $value );
			case 'is_number':
				return is_numeric( $value );
			case 'is_text':
				return ! is_numeric( $value );
			default:
				return false;
		}
	}
}

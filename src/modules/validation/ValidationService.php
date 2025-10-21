<?php
/**
 * Data Validation Service
 *
 * Handles validation rules for table data
 *
 * @package ATablesCharts\Validation\Services
 * @since 1.0.0
 */

namespace ATablesCharts\Validation\Services;

/**
 * ValidationService Class
 *
 * Responsibilities:
 * - Validate data against rules
 * - Field-level validation
 * - Row-level validation
 * - Custom validation rules
 */
class ValidationService {

	/**
	 * Validate data against rules
	 *
	 * @param array $data            Data to validate.
	 * @param array $validation_rules Validation rules.
	 * @return array Validation result
	 */
	public function validate( $data, $validation_rules ) {
		$errors = array();
		$warnings = array();

		if ( empty( $validation_rules ) ) {
			return array(
				'valid'    => true,
				'errors'   => array(),
				'warnings' => array(),
			);
		}

		foreach ( $data as $row_index => $row ) {
			$row_errors = $this->validate_row( $row, $validation_rules, $row_index );
			
			if ( ! empty( $row_errors ) ) {
				$errors = array_merge( $errors, $row_errors );
			}
		}

		return array(
			'valid'    => empty( $errors ),
			'errors'   => $errors,
			'warnings' => $warnings,
		);
	}

	/**
	 * Validate a single row
	 *
	 * @param array $row              Row data.
	 * @param array $validation_rules Validation rules.
	 * @param int   $row_index        Row index.
	 * @return array Validation errors
	 */
	public function validate_row( $row, $validation_rules, $row_index = 0 ) {
		$errors = array();

		foreach ( $validation_rules as $column => $rules ) {
			$value = isset( $row[ $column ] ) ? $row[ $column ] : '';
			
			$field_errors = $this->validate_field( $value, $rules, $column, $row_index );
			
			if ( ! empty( $field_errors ) ) {
				$errors = array_merge( $errors, $field_errors );
			}
		}

		return $errors;
	}

	/**
	 * Validate a single field
	 *
	 * @param mixed  $value     Field value.
	 * @param array  $rules     Validation rules for field.
	 * @param string $column    Column name.
	 * @param int    $row_index Row index.
	 * @return array Validation errors
	 */
	public function validate_field( $value, $rules, $column, $row_index = 0 ) {
		$errors = array();

		foreach ( $rules as $rule => $config ) {
			$error = $this->apply_rule( $value, $rule, $config, $column, $row_index );
			
			if ( $error ) {
				$errors[] = $error;
			}
		}

		return $errors;
	}

	/**
	 * Apply a single validation rule
	 *
	 * @param mixed  $value     Field value.
	 * @param string $rule      Rule name.
	 * @param mixed  $config    Rule configuration.
	 * @param string $column    Column name.
	 * @param int    $row_index Row index.
	 * @return string|null Error message or null
	 */
	private function apply_rule( $value, $rule, $config, $column, $row_index ) {
		switch ( $rule ) {
			case 'required':
				return $this->validate_required( $value, $config, $column, $row_index );

			case 'type':
				return $this->validate_type( $value, $config, $column, $row_index );

			case 'min':
				return $this->validate_min( $value, $config, $column, $row_index );

			case 'max':
				return $this->validate_max( $value, $config, $column, $row_index );

			case 'min_length':
				return $this->validate_min_length( $value, $config, $column, $row_index );

			case 'max_length':
				return $this->validate_max_length( $value, $config, $column, $row_index );

			case 'pattern':
				return $this->validate_pattern( $value, $config, $column, $row_index );

			case 'email':
				return $this->validate_email( $value, $config, $column, $row_index );

			case 'url':
				return $this->validate_url( $value, $config, $column, $row_index );

			case 'date':
				return $this->validate_date( $value, $config, $column, $row_index );

			case 'enum':
				return $this->validate_enum( $value, $config, $column, $row_index );

			case 'unique':
				return $this->validate_unique( $value, $config, $column, $row_index );

			case 'custom':
				return $this->validate_custom( $value, $config, $column, $row_index );

			default:
				return null;
		}
	}

	/**
	 * Validate required field
	 *
	 * @param mixed  $value     Field value.
	 * @param mixed  $config    Rule config.
	 * @param string $column    Column name.
	 * @param int    $row_index Row index.
	 * @return string|null Error message or null
	 */
	private function validate_required( $value, $config, $column, $row_index ) {
		if ( ! $config ) {
			return null;
		}

		if ( empty( $value ) && $value !== '0' && $value !== 0 ) {
			return sprintf(
				__( 'Row %d: %s is required.', 'a-tables-charts' ),
				$row_index + 1,
				$column
			);
		}

		return null;
	}

	/**
	 * Validate data type
	 *
	 * @param mixed  $value     Field value.
	 * @param string $config    Expected type.
	 * @param string $column    Column name.
	 * @param int    $row_index Row index.
	 * @return string|null Error message or null
	 */
	private function validate_type( $value, $config, $column, $row_index ) {
		if ( empty( $value ) ) {
			return null; // Skip type validation for empty values
		}

		$valid = false;

		switch ( $config ) {
			case 'string':
				$valid = is_string( $value );
				break;

			case 'number':
			case 'numeric':
				$valid = is_numeric( $value );
				break;

			case 'integer':
				$valid = filter_var( $value, FILTER_VALIDATE_INT ) !== false;
				break;

			case 'float':
			case 'decimal':
				$valid = filter_var( $value, FILTER_VALIDATE_FLOAT ) !== false;
				break;

			case 'boolean':
				$valid = filter_var( $value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE ) !== null;
				break;

			case 'date':
				$valid = strtotime( $value ) !== false;
				break;

			case 'email':
				$valid = filter_var( $value, FILTER_VALIDATE_EMAIL ) !== false;
				break;

			case 'url':
				$valid = filter_var( $value, FILTER_VALIDATE_URL ) !== false;
				break;
		}

		if ( ! $valid ) {
			return sprintf(
				__( 'Row %d: %s must be of type %s.', 'a-tables-charts' ),
				$row_index + 1,
				$column,
				$config
			);
		}

		return null;
	}

	/**
	 * Validate minimum value
	 *
	 * @param mixed  $value     Field value.
	 * @param mixed  $config    Minimum value.
	 * @param string $column    Column name.
	 * @param int    $row_index Row index.
	 * @return string|null Error message or null
	 */
	private function validate_min( $value, $config, $column, $row_index ) {
		if ( empty( $value ) || ! is_numeric( $value ) ) {
			return null;
		}

		if ( floatval( $value ) < floatval( $config ) ) {
			return sprintf(
				__( 'Row %d: %s must be at least %s.', 'a-tables-charts' ),
				$row_index + 1,
				$column,
				$config
			);
		}

		return null;
	}

	/**
	 * Validate maximum value
	 *
	 * @param mixed  $value     Field value.
	 * @param mixed  $config    Maximum value.
	 * @param string $column    Column name.
	 * @param int    $row_index Row index.
	 * @return string|null Error message or null
	 */
	private function validate_max( $value, $config, $column, $row_index ) {
		if ( empty( $value ) || ! is_numeric( $value ) ) {
			return null;
		}

		if ( floatval( $value ) > floatval( $config ) ) {
			return sprintf(
				__( 'Row %d: %s must be no more than %s.', 'a-tables-charts' ),
				$row_index + 1,
				$column,
				$config
			);
		}

		return null;
	}

	/**
	 * Validate minimum length
	 *
	 * @param mixed  $value     Field value.
	 * @param int    $config    Minimum length.
	 * @param string $column    Column name.
	 * @param int    $row_index Row index.
	 * @return string|null Error message or null
	 */
	private function validate_min_length( $value, $config, $column, $row_index ) {
		if ( empty( $value ) ) {
			return null;
		}

		if ( strlen( $value ) < intval( $config ) ) {
			return sprintf(
				__( 'Row %d: %s must be at least %d characters.', 'a-tables-charts' ),
				$row_index + 1,
				$column,
				$config
			);
		}

		return null;
	}

	/**
	 * Validate maximum length
	 *
	 * @param mixed  $value     Field value.
	 * @param int    $config    Maximum length.
	 * @param string $column    Column name.
	 * @param int    $row_index Row index.
	 * @return string|null Error message or null
	 */
	private function validate_max_length( $value, $config, $column, $row_index ) {
		if ( empty( $value ) ) {
			return null;
		}

		if ( strlen( $value ) > intval( $config ) ) {
			return sprintf(
				__( 'Row %d: %s must be no more than %d characters.', 'a-tables-charts' ),
				$row_index + 1,
				$column,
				$config
			);
		}

		return null;
	}

	/**
	 * Validate pattern (regex)
	 *
	 * @param mixed  $value     Field value.
	 * @param string $config    Regex pattern.
	 * @param string $column    Column name.
	 * @param int    $row_index Row index.
	 * @return string|null Error message or null
	 */
	private function validate_pattern( $value, $config, $column, $row_index ) {
		if ( empty( $value ) ) {
			return null;
		}

		if ( ! preg_match( $config, $value ) ) {
			return sprintf(
				__( 'Row %d: %s format is invalid.', 'a-tables-charts' ),
				$row_index + 1,
				$column
			);
		}

		return null;
	}

	/**
	 * Validate email
	 *
	 * @param mixed  $value     Field value.
	 * @param mixed  $config    Rule config.
	 * @param string $column    Column name.
	 * @param int    $row_index Row index.
	 * @return string|null Error message or null
	 */
	private function validate_email( $value, $config, $column, $row_index ) {
		if ( empty( $value ) ) {
			return null;
		}

		if ( ! filter_var( $value, FILTER_VALIDATE_EMAIL ) ) {
			return sprintf(
				__( 'Row %d: %s must be a valid email address.', 'a-tables-charts' ),
				$row_index + 1,
				$column
			);
		}

		return null;
	}

	/**
	 * Validate URL
	 *
	 * @param mixed  $value     Field value.
	 * @param mixed  $config    Rule config.
	 * @param string $column    Column name.
	 * @param int    $row_index Row index.
	 * @return string|null Error message or null
	 */
	private function validate_url( $value, $config, $column, $row_index ) {
		if ( empty( $value ) ) {
			return null;
		}

		if ( ! filter_var( $value, FILTER_VALIDATE_URL ) ) {
			return sprintf(
				__( 'Row %d: %s must be a valid URL.', 'a-tables-charts' ),
				$row_index + 1,
				$column
			);
		}

		return null;
	}

	/**
	 * Validate date
	 *
	 * @param mixed  $value     Field value.
	 * @param mixed  $config    Date format or true.
	 * @param string $column    Column name.
	 * @param int    $row_index Row index.
	 * @return string|null Error message or null
	 */
	private function validate_date( $value, $config, $column, $row_index ) {
		if ( empty( $value ) ) {
			return null;
		}

		if ( strtotime( $value ) === false ) {
			return sprintf(
				__( 'Row %d: %s must be a valid date.', 'a-tables-charts' ),
				$row_index + 1,
				$column
			);
		}

		return null;
	}

	/**
	 * Validate enum (allowed values)
	 *
	 * @param mixed  $value     Field value.
	 * @param array  $config    Allowed values.
	 * @param string $column    Column name.
	 * @param int    $row_index Row index.
	 * @return string|null Error message or null
	 */
	private function validate_enum( $value, $config, $column, $row_index ) {
		if ( empty( $value ) ) {
			return null;
		}

		if ( ! in_array( $value, $config, true ) ) {
			return sprintf(
				__( 'Row %d: %s must be one of: %s.', 'a-tables-charts' ),
				$row_index + 1,
				$column,
				implode( ', ', $config )
			);
		}

		return null;
	}

	/**
	 * Validate unique (placeholder - requires full dataset)
	 *
	 * @param mixed  $value     Field value.
	 * @param mixed  $config    Rule config.
	 * @param string $column    Column name.
	 * @param int    $row_index Row index.
	 * @return string|null Error message or null
	 */
	private function validate_unique( $value, $config, $column, $row_index ) {
		// Unique validation requires checking against all rows
		// This is handled in the main validate() method
		return null;
	}

	/**
	 * Validate custom rule
	 *
	 * @param mixed  $value     Field value.
	 * @param mixed  $config    Custom validation function.
	 * @param string $column    Column name.
	 * @param int    $row_index Row index.
	 * @return string|null Error message or null
	 */
	private function validate_custom( $value, $config, $column, $row_index ) {
		if ( is_callable( $config ) ) {
			$result = call_user_func( $config, $value, $column, $row_index );
			
			if ( $result !== true ) {
				return is_string( $result ) ? $result : sprintf(
					__( 'Row %d: %s validation failed.', 'a-tables-charts' ),
					$row_index + 1,
					$column
				);
			}
		}

		return null;
	}

	/**
	 * Get validation rule presets
	 *
	 * @return array Validation rule presets
	 */
	public static function get_rule_presets() {
		return array(
			'required_field' => array(
				'label'       => __( 'Required Field', 'a-tables-charts' ),
				'description' => __( 'Field must not be empty', 'a-tables-charts' ),
				'rules'       => array(
					'required' => true,
				),
			),
			'email_address' => array(
				'label'       => __( 'Email Address', 'a-tables-charts' ),
				'description' => __( 'Valid email format required', 'a-tables-charts' ),
				'rules'       => array(
					'type'  => 'email',
					'email' => true,
				),
			),
			'website_url' => array(
				'label'       => __( 'Website URL', 'a-tables-charts' ),
				'description' => __( 'Valid URL format required', 'a-tables-charts' ),
				'rules'       => array(
					'type' => 'url',
					'url'  => true,
				),
			),
			'positive_number' => array(
				'label'       => __( 'Positive Number', 'a-tables-charts' ),
				'description' => __( 'Must be a number greater than 0', 'a-tables-charts' ),
				'rules'       => array(
					'type' => 'numeric',
					'min'  => 0.01,
				),
			),
			'integer_only' => array(
				'label'       => __( 'Integer Only', 'a-tables-charts' ),
				'description' => __( 'Whole numbers only', 'a-tables-charts' ),
				'rules'       => array(
					'type' => 'integer',
				),
			),
			'percentage' => array(
				'label'       => __( 'Percentage (0-100)', 'a-tables-charts' ),
				'description' => __( 'Number between 0 and 100', 'a-tables-charts' ),
				'rules'       => array(
					'type' => 'numeric',
					'min'  => 0,
					'max'  => 100,
				),
			),
			'phone_number' => array(
				'label'       => __( 'Phone Number', 'a-tables-charts' ),
				'description' => __( 'Valid phone format', 'a-tables-charts' ),
				'rules'       => array(
					'pattern' => '/^[\d\s\-\+\(\)]+$/',
				),
			),
			'zip_code' => array(
				'label'       => __( 'ZIP Code (US)', 'a-tables-charts' ),
				'description' => __( '5 or 9 digit ZIP code', 'a-tables-charts' ),
				'rules'       => array(
					'pattern' => '/^\d{5}(-\d{4})?$/',
				),
			),
			'date_field' => array(
				'label'       => __( 'Date Field', 'a-tables-charts' ),
				'description' => __( 'Valid date required', 'a-tables-charts' ),
				'rules'       => array(
					'type' => 'date',
					'date' => true,
				),
			),
			'short_text' => array(
				'label'       => __( 'Short Text (max 100)', 'a-tables-charts' ),
				'description' => __( 'Text limited to 100 characters', 'a-tables-charts' ),
				'rules'       => array(
					'type'       => 'string',
					'max_length' => 100,
				),
			),
		);
	}

	/**
	 * Get presets (alias for get_rule_presets)
	 *
	 * @return array Validation rule presets
	 */
	public function get_presets() {
		return self::get_rule_presets();
	}

	/**
	 * Get available validation rules
	 *
	 * @return array Available rules with descriptions
	 */
	public static function get_available_rules() {
		return array(
			'required'   => __( 'Required - Field must not be empty', 'a-tables-charts' ),
			'type'       => __( 'Type - Validate data type (string, number, email, url, date)', 'a-tables-charts' ),
			'min'        => __( 'Min - Minimum numeric value', 'a-tables-charts' ),
			'max'        => __( 'Max - Maximum numeric value', 'a-tables-charts' ),
			'min_length' => __( 'Min Length - Minimum text length', 'a-tables-charts' ),
			'max_length' => __( 'Max Length - Maximum text length', 'a-tables-charts' ),
			'pattern'    => __( 'Pattern - Regular expression match', 'a-tables-charts' ),
			'email'      => __( 'Email - Valid email address', 'a-tables-charts' ),
			'url'        => __( 'URL - Valid URL format', 'a-tables-charts' ),
			'date'       => __( 'Date - Valid date format', 'a-tables-charts' ),
			'enum'       => __( 'Enum - Must be one of allowed values', 'a-tables-charts' ),
			'unique'     => __( 'Unique - No duplicate values', 'a-tables-charts' ),
			'custom'     => __( 'Custom - Custom validation function', 'a-tables-charts' ),
		);
	}
}

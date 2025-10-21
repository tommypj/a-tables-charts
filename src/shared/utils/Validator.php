<?php
/**
 * Validator Utility
 *
 * Provides comprehensive validation functions for various data types.
 * Enhanced with detailed error messages and validation results.
 *
 * @package ATablesCharts\Shared\Utils
 * @since 1.0.0
 */

namespace ATablesCharts\Shared\Utils;

/**
 * Validator Class
 *
 * Responsibilities:
 * - Validate emails, URLs, numbers, etc.
 * - Validate file uploads
 * - Validate data structures
 * - Return validation results with detailed error messages
 * - Provide multiple validation methods (simple bool or detailed array)
 */
class Validator {

	/**
	 * Validation errors collected during validation
	 *
	 * @var array
	 */
	private static $errors = array();

	/**
	 * Clear all validation errors
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function clear_errors() {
		self::$errors = array();
	}

	/**
	 * Get all validation errors
	 *
	 * @since 1.0.0
	 * @return array Validation errors
	 */
	public static function get_errors() {
		return self::$errors;
	}

	/**
	 * Add validation error
	 *
	 * @since 1.0.0
	 * @param string $field Field name.
	 * @param string $message Error message.
	 * @return void
	 */
	private static function add_error( $field, $message ) {
		if ( ! isset( self::$errors[ $field ] ) ) {
			self::$errors[ $field ] = array();
		}
		self::$errors[ $field ][] = $message;
	}

	/**
	 * Validate email address
	 *
	 * @since 1.0.0
	 * @param string $email Email address to validate.
	 * @param string $field_name Field name for error messages (optional).
	 * @return bool True if valid, false otherwise
	 */
	public static function email( $email, $field_name = 'email' ) {
		if ( empty( $email ) ) {
			self::add_error( $field_name, __( 'Email address is required.', 'a-tables-charts' ) );
			return false;
		}

		if ( ! is_email( $email ) ) {
			self::add_error( $field_name, __( 'Please provide a valid email address.', 'a-tables-charts' ) );
			return false;
		}

		return true;
	}

	/**
	 * Validate URL
	 *
	 * @since 1.0.0
	 * @param string $url URL to validate.
	 * @param string $field_name Field name for error messages (optional).
	 * @return bool True if valid, false otherwise
	 */
	public static function url( $url, $field_name = 'url' ) {
		if ( empty( $url ) ) {
			self::add_error( $field_name, __( 'URL is required.', 'a-tables-charts' ) );
			return false;
		}

		if ( filter_var( $url, FILTER_VALIDATE_URL ) === false ) {
			self::add_error( $field_name, __( 'Please provide a valid URL.', 'a-tables-charts' ) );
			return false;
		}

		return true;
	}

	/**
	 * Validate integer
	 *
	 * @since 1.0.0
	 * @param mixed  $value Value to validate.
	 * @param int    $min Minimum value (optional).
	 * @param int    $max Maximum value (optional).
	 * @param string $field_name Field name for error messages (optional).
	 * @return bool True if valid, false otherwise
	 */
	public static function integer( $value, $min = null, $max = null, $field_name = 'value' ) {
		if ( ! is_numeric( $value ) || (int) $value != $value ) {
			self::add_error( $field_name, __( 'Value must be a valid integer.', 'a-tables-charts' ) );
			return false;
		}

		$int_value = (int) $value;

		if ( null !== $min && $int_value < $min ) {
			self::add_error(
				$field_name,
				sprintf(
					/* translators: %d: minimum value */
					__( 'Value must be at least %d.', 'a-tables-charts' ),
					$min
				)
			);
			return false;
		}

		if ( null !== $max && $int_value > $max ) {
			self::add_error(
				$field_name,
				sprintf(
					/* translators: %d: maximum value */
					__( 'Value must not exceed %d.', 'a-tables-charts' ),
					$max
				)
			);
			return false;
		}

		return true;
	}

	/**
	 * Validate float/decimal
	 *
	 * @since 1.0.0
	 * @param mixed  $value Value to validate.
	 * @param float  $min Minimum value (optional).
	 * @param float  $max Maximum value (optional).
	 * @param string $field_name Field name for error messages (optional).
	 * @return bool True if valid, false otherwise
	 */
	public static function float( $value, $min = null, $max = null, $field_name = 'value' ) {
		if ( ! is_numeric( $value ) ) {
			self::add_error( $field_name, __( 'Value must be a valid number.', 'a-tables-charts' ) );
			return false;
		}

		$float_value = (float) $value;

		if ( null !== $min && $float_value < $min ) {
			self::add_error(
				$field_name,
				sprintf(
					/* translators: %s: minimum value */
					__( 'Value must be at least %s.', 'a-tables-charts' ),
					number_format( $min, 2 )
				)
			);
			return false;
		}

		if ( null !== $max && $float_value > $max ) {
			self::add_error(
				$field_name,
				sprintf(
					/* translators: %s: maximum value */
					__( 'Value must not exceed %s.', 'a-tables-charts' ),
					number_format( $max, 2 )
				)
			);
			return false;
		}

		return true;
	}

	/**
	 * Validate required field
	 *
	 * @since 1.0.0
	 * @param mixed  $value Value to validate.
	 * @param string $field_name Field name for error messages (optional).
	 * @return bool True if not empty, false otherwise
	 */
	public static function required( $value, $field_name = 'field' ) {
		$is_valid = true;

		if ( is_array( $value ) ) {
			$is_valid = ! empty( $value );
		} else {
			$is_valid = '' !== trim( (string) $value );
		}

		if ( ! $is_valid ) {
			self::add_error(
				$field_name,
				sprintf(
					/* translators: %s: field name */
					__( '%s is required.', 'a-tables-charts' ),
					ucfirst( str_replace( '_', ' ', $field_name ) )
				)
			);
		}

		return $is_valid;
	}

	/**
	 * Validate string length
	 *
	 * @since 1.0.0
	 * @param string $value Value to validate.
	 * @param int    $min Minimum length (optional).
	 * @param int    $max Maximum length (optional).
	 * @param string $field_name Field name for error messages (optional).
	 * @return bool True if valid, false otherwise
	 */
	public static function string_length( $value, $min = null, $max = null, $field_name = 'field' ) {
		$length = mb_strlen( (string) $value );

		if ( null !== $min && $length < $min ) {
			self::add_error(
				$field_name,
				sprintf(
					/* translators: 1: field name, 2: minimum length */
					__( '%1$s must be at least %2$d characters long.', 'a-tables-charts' ),
					ucfirst( str_replace( '_', ' ', $field_name ) ),
					$min
				)
			);
			return false;
		}

		if ( null !== $max && $length > $max ) {
			self::add_error(
				$field_name,
				sprintf(
					/* translators: 1: field name, 2: maximum length */
					__( '%1$s must not exceed %2$d characters.', 'a-tables-charts' ),
					ucfirst( str_replace( '_', ' ', $field_name ) ),
					$max
				)
			);
			return false;
		}

		return true;
	}

	/**
	 * Validate alphanumeric string
	 *
	 * @since 1.0.0
	 * @param string $value Value to validate.
	 * @param string $field_name Field name for error messages (optional).
	 * @return bool True if valid, false otherwise
	 */
	public static function alphanumeric( $value, $field_name = 'field' ) {
		if ( ! preg_match( '/^[a-zA-Z0-9]+$/', $value ) ) {
			self::add_error(
				$field_name,
				sprintf(
					/* translators: %s: field name */
					__( '%s must contain only letters and numbers.', 'a-tables-charts' ),
					ucfirst( str_replace( '_', ' ', $field_name ) )
				)
			);
			return false;
		}

		return true;
	}

	/**
	 * Validate slug format (letters, numbers, hyphens, underscores)
	 *
	 * @since 1.0.0
	 * @param string $value Value to validate.
	 * @param string $field_name Field name for error messages (optional).
	 * @return bool True if valid, false otherwise
	 */
	public static function slug( $value, $field_name = 'slug' ) {
		if ( ! preg_match( '/^[a-z0-9-_]+$/', $value ) ) {
			self::add_error(
				$field_name,
				__( 'Slug must contain only lowercase letters, numbers, hyphens, and underscores.', 'a-tables-charts' )
			);
			return false;
		}

		return true;
	}

	/**
	 * Validate enum/choice value
	 *
	 * @since 1.0.0
	 * @param mixed  $value Value to validate.
	 * @param array  $allowed_values Allowed values.
	 * @param string $field_name Field name for error messages (optional).
	 * @return bool True if valid, false otherwise
	 */
	public static function in_array( $value, $allowed_values, $field_name = 'field' ) {
		if ( ! in_array( $value, $allowed_values, true ) ) {
			self::add_error(
				$field_name,
				sprintf(
					/* translators: 1: field name, 2: allowed values */
					__( '%1$s must be one of: %2$s', 'a-tables-charts' ),
					ucfirst( str_replace( '_', ' ', $field_name ) ),
					implode( ', ', $allowed_values )
				)
			);
			return false;
		}

		return true;
	}

	/**
	 * Validate table title
	 *
	 * @since 1.0.0
	 * @param string $title Table title to validate.
	 * @return array Validation result with 'valid' and 'errors' keys
	 */
	public static function table_title( $title ) {
		self::clear_errors();

		$valid = true;

		// Check required.
		if ( ! self::required( $title, 'title' ) ) {
			$valid = false;
		}

		// Check length.
		if ( ! self::string_length( $title, 3, 200, 'title' ) ) {
			$valid = false;
		}

		return array(
			'valid'  => $valid,
			'errors' => self::get_errors(),
		);
	}

	/**
	 * Validate table data
	 *
	 * @since 1.0.0
	 * @param array $data Table data to validate.
	 * @return array Validation result with 'valid' and 'errors' keys
	 */
	public static function table_data( $data ) {
		self::clear_errors();

		$valid = true;

		// Check if data is array.
		if ( ! is_array( $data ) ) {
			self::add_error( 'data', __( 'Table data must be an array.', 'a-tables-charts' ) );
			$valid = false;
		}

		// Check if data is not empty.
		if ( empty( $data ) ) {
			self::add_error( 'data', __( 'Table data cannot be empty.', 'a-tables-charts' ) );
			$valid = false;
		}

		// Check if all rows have same number of columns.
		if ( $valid && is_array( $data ) ) {
			$column_count = null;
			foreach ( $data as $index => $row ) {
				if ( ! is_array( $row ) ) {
					self::add_error( 'data', sprintf(
						/* translators: %d: row number */
						__( 'Row %d must be an array.', 'a-tables-charts' ),
						$index + 1
					) );
					$valid = false;
					continue;
				}

				$current_count = count( $row );
				if ( null === $column_count ) {
					$column_count = $current_count;
				} elseif ( $current_count !== $column_count ) {
					self::add_error( 'data', sprintf(
						/* translators: 1: row number, 2: expected columns, 3: actual columns */
						__( 'Row %1$d has %3$d columns, expected %2$d.', 'a-tables-charts' ),
						$index + 1,
						$column_count,
						$current_count
					) );
					$valid = false;
				}
			}
		}

		return array(
			'valid'  => $valid,
			'errors' => self::get_errors(),
		);
	}

	/**
	 * Validate file upload
	 *
	 * @since 1.0.0
	 * @param array $file File from $_FILES array.
	 * @param array $allowed_types Allowed MIME types.
	 * @param int   $max_size Maximum file size in bytes.
	 * @return array Validation result with 'valid' and 'error' keys
	 */
	public static function file_upload( $file, $allowed_types = array(), $max_size = 0 ) {
		$result = array(
			'valid' => true,
			'error' => '',
		);

		// Check if file was uploaded.
		if ( ! isset( $file['tmp_name'] ) || ! is_uploaded_file( $file['tmp_name'] ) ) {
			$result['valid'] = false;
			$result['error'] = __( 'No file was uploaded.', 'a-tables-charts' );
			return $result;
		}

		// Check for upload errors.
		if ( isset( $file['error'] ) && UPLOAD_ERR_OK !== $file['error'] ) {
			$result['valid'] = false;
			$result['error'] = self::get_upload_error_message( $file['error'] );
			return $result;
		}

		// Check file size.
		if ( $max_size > 0 && $file['size'] > $max_size ) {
			$result['valid'] = false;
			$result['error'] = sprintf(
				/* translators: %s: Maximum file size */
				__( 'File size exceeds maximum allowed size of %s.', 'a-tables-charts' ),
				size_format( $max_size )
			);
			return $result;
		}

		// Check file type.
		if ( ! empty( $allowed_types ) ) {
			$file_type = wp_check_filetype( $file['name'] );
			
			if ( ! in_array( $file_type['type'], $allowed_types, true ) ) {
				$result['valid'] = false;
				$result['error'] = sprintf(
					/* translators: %s: allowed file types */
					__( 'File type is not allowed. Allowed types: %s', 'a-tables-charts' ),
					implode( ', ', $allowed_types )
				);
				return $result;
			}
		}

		// Check for malicious file names.
		if ( ! self::safe_filename( $file['name'] ) ) {
			$result['valid'] = false;
			$result['error'] = __( 'File name contains invalid characters.', 'a-tables-charts' );
			return $result;
		}

		return $result;
	}

	/**
	 * Validate filename for security
	 *
	 * @since 1.0.0
	 * @param string $filename Filename to validate.
	 * @return bool True if safe, false otherwise
	 */
	public static function safe_filename( $filename ) {
		// Check for directory traversal attempts.
		if ( strpos( $filename, '..' ) !== false || strpos( $filename, '/' ) !== false ) {
			return false;
		}

		// Check for null bytes.
		if ( strpos( $filename, "\0" ) !== false ) {
			return false;
		}

		return true;
	}

	/**
	 * Get upload error message
	 *
	 * @since 1.0.0
	 * @param int $error_code PHP upload error code.
	 * @return string Error message
	 */
	private static function get_upload_error_message( $error_code ) {
		$errors = array(
			UPLOAD_ERR_INI_SIZE   => __( 'File exceeds upload_max_filesize directive in php.ini.', 'a-tables-charts' ),
			UPLOAD_ERR_FORM_SIZE  => __( 'File exceeds MAX_FILE_SIZE directive in HTML form.', 'a-tables-charts' ),
			UPLOAD_ERR_PARTIAL    => __( 'File was only partially uploaded.', 'a-tables-charts' ),
			UPLOAD_ERR_NO_FILE    => __( 'No file was uploaded.', 'a-tables-charts' ),
			UPLOAD_ERR_NO_TMP_DIR => __( 'Missing temporary folder.', 'a-tables-charts' ),
			UPLOAD_ERR_CANT_WRITE => __( 'Failed to write file to disk.', 'a-tables-charts' ),
			UPLOAD_ERR_EXTENSION  => __( 'File upload stopped by extension.', 'a-tables-charts' ),
		);

		return isset( $errors[ $error_code ] ) ? $errors[ $error_code ] : __( 'Unknown upload error.', 'a-tables-charts' );
	}

	/**
	 * Validate JSON string
	 *
	 * @since 1.0.0
	 * @param string $json JSON string to validate.
	 * @param string $field_name Field name for error messages (optional).
	 * @return bool True if valid JSON, false otherwise
	 */
	public static function json( $json, $field_name = 'json' ) {
		if ( ! is_string( $json ) ) {
			self::add_error( $field_name, __( 'Value must be a string.', 'a-tables-charts' ) );
			return false;
		}

		json_decode( $json );
		if ( json_last_error() !== JSON_ERROR_NONE ) {
			self::add_error( $field_name, sprintf(
				/* translators: %s: JSON error message */
				__( 'Invalid JSON: %s', 'a-tables-charts' ),
				json_last_error_msg()
			) );
			return false;
		}

		return true;
	}

	/**
	 * Validate date format
	 *
	 * @since 1.0.0
	 * @param string $date Date string to validate.
	 * @param string $format Expected date format (default: Y-m-d).
	 * @param string $field_name Field name for error messages (optional).
	 * @return bool True if valid date, false otherwise
	 */
	public static function date( $date, $format = 'Y-m-d', $field_name = 'date' ) {
		$d = \DateTime::createFromFormat( $format, $date );
		$is_valid = $d && $d->format( $format ) === $date;

		if ( ! $is_valid ) {
			self::add_error( $field_name, sprintf(
				/* translators: %s: expected format */
				__( 'Date must be in format: %s', 'a-tables-charts' ),
				$format
			) );
		}

		return $is_valid;
	}

	/**
	 * Validate array structure
	 *
	 * @since 1.0.0
	 * @param array $data Data to validate.
	 * @param array $required Required keys.
	 * @return array Validation result with 'valid' and 'missing' keys
	 */
	public static function array_structure( $data, $required ) {
		$result = array(
			'valid'   => true,
			'missing' => array(),
		);

		if ( ! is_array( $data ) ) {
			$result['valid']   = false;
			$result['missing'] = $required;
			return $result;
		}

		foreach ( $required as $key ) {
			if ( ! array_key_exists( $key, $data ) ) {
				$result['valid']     = false;
				$result['missing'][] = $key;
			}
		}

		return $result;
	}

	/**
	 * Sanitize and validate nonce
	 *
	 * @since 1.0.0
	 * @param string $nonce Nonce value.
	 * @param string $action Nonce action.
	 * @return bool True if valid, false otherwise
	 */
	public static function nonce( $nonce, $action ) {
		return wp_verify_nonce( $nonce, $action ) !== false;
	}

	/**
	 * Validate multiple fields at once
	 *
	 * @since 1.0.0
	 * @param array $data Data to validate.
	 * @param array $rules Validation rules.
	 * @return array Validation result with 'valid' and 'errors' keys
	 * 
	 * @example
	 * $rules = array(
	 *     'title' => array('required', 'string_length:3:200'),
	 *     'email' => array('required', 'email'),
	 *     'age' => array('integer:18:100'),
	 * );
	 */
	public static function validate_fields( $data, $rules ) {
		self::clear_errors();

		$valid = true;

		foreach ( $rules as $field => $field_rules ) {
			$value = isset( $data[ $field ] ) ? $data[ $field ] : null;

			foreach ( $field_rules as $rule ) {
				$rule_parts = explode( ':', $rule );
				$rule_name = $rule_parts[0];
				$rule_params = array_slice( $rule_parts, 1 );

				$method_params = array_merge( array( $value ), $rule_params, array( $field ) );

				if ( method_exists( __CLASS__, $rule_name ) ) {
					$result = call_user_func_array( array( __CLASS__, $rule_name ), $method_params );
					if ( ! $result ) {
						$valid = false;
					}
				}
			}
		}

		return array(
			'valid'  => $valid,
			'errors' => self::get_errors(),
		);
	}
}

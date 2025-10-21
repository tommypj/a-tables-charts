<?php
/**
 * Column Type Formatter
 *
 * Automatically formats column values based on detected type
 *
 * @package ATablesCharts\Shared\Utils
 * @since 1.0.0
 */

namespace ATablesCharts\Shared\Utils;

/**
 * ColumnTypeFormatter Class
 *
 * Responsibilities:
 * - Detect column data types
 * - Auto-format currency values
 * - Auto-format dates
 * - Auto-format numbers
 * - Auto-format percentages
 * - Auto-format phone numbers
 */
class ColumnTypeFormatter {

	/**
	 * Detect and format column value
	 *
	 * @param mixed $value    Cell value.
	 * @param array $options  Formatting options.
	 * @return string Formatted value
	 */
	public static function format( $value, $options = array() ) {
		// Skip empty values
		if ( empty( $value ) && $value !== 0 && $value !== '0' ) {
			return '';
		}

		$defaults = array(
			'type'               => 'auto',  // auto, currency, date, number, percentage, phone
			'currency_symbol'    => '$',
			'currency_position'  => 'before', // before, after
			'decimal_separator'  => '.',
			'thousands_separator' => ',',
			'decimal_places'     => 2,
			'date_format'        => 'M d, Y',  // PHP date format
			'percentage_decimal' => 1,
		);

		$options = wp_parse_args( $options, $defaults );

		// Auto-detect type if set to auto
		if ( $options['type'] === 'auto' ) {
			$options['type'] = self::detect_type( $value );
		}

		// Format based on type
		switch ( $options['type'] ) {
			case 'currency':
				return self::format_currency( $value, $options );
			
			case 'date':
				return self::format_date( $value, $options );
			
			case 'number':
				return self::format_number( $value, $options );
			
			case 'percentage':
				return self::format_percentage( $value, $options );
			
			case 'phone':
				return self::format_phone( $value );
			
			default:
				return esc_html( $value );
		}
	}

	/**
	 * Auto-detect column type
	 *
	 * @param mixed $value Value to analyze.
	 * @return string Detected type
	 */
	public static function detect_type( $value ) {
		$value_str = (string) $value;
		$value_str = trim( $value_str );

		// Check for currency
		if ( self::is_currency( $value_str ) ) {
			return 'currency';
		}

		// Check for percentage
		if ( self::is_percentage( $value_str ) ) {
			return 'percentage';
		}

		// Check for date
		if ( self::is_date( $value_str ) ) {
			return 'date';
		}

		// Check for phone
		if ( self::is_phone( $value_str ) ) {
			return 'phone';
		}

		// Check for number
		if ( self::is_number( $value_str ) ) {
			return 'number';
		}

		return 'text';
	}

	/**
	 * Check if value is currency
	 *
	 * @param string $value Value to check.
	 * @return bool True if currency
	 */
	public static function is_currency( $value ) {
		// Match: $123, $1,234.56, €123, £123.45, etc.
		$pattern = '/^[\$€£¥]?\s?[\d,]+\.?\d*$|^[\d,]+\.?\d*\s?[\$€£¥]$/';
		return preg_match( $pattern, trim( $value ) ) === 1;
	}

	/**
	 * Check if value is percentage
	 *
	 * @param string $value Value to check.
	 * @return bool True if percentage
	 */
	public static function is_percentage( $value ) {
		// Match: 45%, 12.5%, etc.
		return preg_match( '/^\d+\.?\d*\s?%$/', trim( $value ) ) === 1;
	}

	/**
	 * Check if value is date
	 *
	 * @param string $value Value to check.
	 * @return bool True if date
	 */
	public static function is_date( $value ) {
		// Try to parse as date
		$timestamp = strtotime( $value );
		
		// Check if it's a valid date and not just a number
		if ( $timestamp === false || is_numeric( $value ) ) {
			return false;
		}

		// Check if value contains date-like patterns
		$date_patterns = array(
			'/\d{4}[-\/]\d{1,2}[-\/]\d{1,2}/',     // 2024-01-15 or 2024/01/15
			'/\d{1,2}[-\/]\d{1,2}[-\/]\d{4}/',     // 01-15-2024 or 01/15/2024
			'/\d{1,2}\s\w+\s\d{4}/',               // 15 January 2024
			'/\w+\s\d{1,2},?\s\d{4}/',             // January 15, 2024
		);

		foreach ( $date_patterns as $pattern ) {
			if ( preg_match( $pattern, $value ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Check if value is number
	 *
	 * @param string $value Value to check.
	 * @return bool True if number
	 */
	public static function is_number( $value ) {
		// Remove common thousand separators
		$cleaned = str_replace( array( ',', ' ' ), '', $value );
		return is_numeric( $cleaned );
	}

	/**
	 * Check if value is phone number
	 *
	 * @param string $value Value to check.
	 * @return bool True if phone
	 */
	public static function is_phone( $value ) {
		// Remove spaces, dashes, parentheses
		$cleaned = preg_replace( '/[^0-9+]/', '', $value );
		
		// Check if it's 10-15 digits (typical phone number range)
		$length = strlen( $cleaned );
		
		// Must contain phone-like characters
		$has_phone_chars = preg_match( '/[\(\)\-\s]/', $value );
		
		return ( $length >= 10 && $length <= 15 && $has_phone_chars );
	}

	/**
	 * Format currency value
	 *
	 * @param mixed $value   Value to format.
	 * @param array $options Formatting options.
	 * @return string Formatted currency
	 */
	public static function format_currency( $value, $options ) {
		// Extract numeric value
		$numeric = preg_replace( '/[^0-9.-]/', '', $value );
		$numeric = floatval( $numeric );

		// Format number
		$formatted = number_format(
			$numeric,
			$options['decimal_places'],
			$options['decimal_separator'],
			$options['thousands_separator']
		);

		// Add currency symbol
		if ( $options['currency_position'] === 'before' ) {
			return '<span class="atables-currency-value">' . esc_html( $options['currency_symbol'] . $formatted ) . '</span>';
		} else {
			return '<span class="atables-currency-value">' . esc_html( $formatted . $options['currency_symbol'] ) . '</span>';
		}
	}

	/**
	 * Format date value
	 *
	 * @param mixed $value   Value to format.
	 * @param array $options Formatting options.
	 * @return string Formatted date
	 */
	public static function format_date( $value, $options ) {
		$timestamp = strtotime( $value );
		
		if ( $timestamp === false ) {
			return esc_html( $value );
		}

		$formatted = date( $options['date_format'], $timestamp );
		return '<span class="atables-date-value">' . esc_html( $formatted ) . '</span>';
	}

	/**
	 * Format number value
	 *
	 * @param mixed $value   Value to format.
	 * @param array $options Formatting options.
	 * @return string Formatted number
	 */
	public static function format_number( $value, $options ) {
		// Clean the value
		$cleaned = str_replace( array( ',', ' ' ), '', $value );
		$numeric = floatval( $cleaned );

		// Format with thousands separator
		$formatted = number_format(
			$numeric,
			$options['decimal_places'],
			$options['decimal_separator'],
			$options['thousands_separator']
		);

		return '<span class="atables-number-value">' . esc_html( $formatted ) . '</span>';
	}

	/**
	 * Format percentage value
	 *
	 * @param mixed $value   Value to format.
	 * @param array $options Formatting options.
	 * @return string Formatted percentage
	 */
	public static function format_percentage( $value, $options ) {
		// Extract numeric value
		$numeric = preg_replace( '/[^0-9.-]/', '', $value );
		$numeric = floatval( $numeric );

		// Format number
		$formatted = number_format(
			$numeric,
			$options['percentage_decimal'],
			$options['decimal_separator'],
			$options['thousands_separator']
		);

		return '<span class="atables-percentage-value">' . esc_html( $formatted . '%' ) . '</span>';
	}

	/**
	 * Format phone number
	 *
	 * @param string $value Phone number.
	 * @return string Formatted phone
	 */
	public static function format_phone( $value ) {
		// Extract digits
		$digits = preg_replace( '/[^0-9]/', '', $value );
		
		// Format based on length
		if ( strlen( $digits ) === 10 ) {
			// US format: (555) 123-4567
			$formatted = sprintf(
				'(%s) %s-%s',
				substr( $digits, 0, 3 ),
				substr( $digits, 3, 3 ),
				substr( $digits, 6, 4 )
			);
		} elseif ( strlen( $digits ) === 11 && $digits[0] === '1' ) {
			// US with country code: +1 (555) 123-4567
			$formatted = sprintf(
				'+1 (%s) %s-%s',
				substr( $digits, 1, 3 ),
				substr( $digits, 4, 3 ),
				substr( $digits, 7, 4 )
			);
		} else {
			// Keep original format
			$formatted = $value;
		}

		return '<a href="tel:' . esc_attr( $digits ) . '" class="atables-phone-value">' . esc_html( $formatted ) . '</a>';
	}

	/**
	 * Analyze column to determine best format
	 *
	 * Analyzes all values in a column to determine the most common type
	 *
	 * @param array $column_values All values from the column.
	 * @return string Detected column type
	 */
	public static function analyze_column( $column_values ) {
		$types = array();

		// Sample up to 100 values for performance
		$sample = array_slice( $column_values, 0, 100 );

		foreach ( $sample as $value ) {
			if ( empty( $value ) ) {
				continue;
			}

			$type = self::detect_type( $value );
			if ( ! isset( $types[ $type ] ) ) {
				$types[ $type ] = 0;
			}
			$types[ $type ]++;
		}

		// Return most common type
		if ( empty( $types ) ) {
			return 'text';
		}

		arsort( $types );
		return key( $types );
	}

	/**
	 * Get formatting suggestions for a column
	 *
	 * @param array $column_values Column values.
	 * @return array Suggested formatting options
	 */
	public static function get_suggestions( $column_values ) {
		$type = self::analyze_column( $column_values );

		$suggestions = array(
			'type' => $type,
			'confidence' => 'high',
		);

		switch ( $type ) {
			case 'currency':
				// Detect currency symbol from first value
				$first_value = reset( $column_values );
				if ( preg_match( '/^([\$€£¥])/', $first_value, $matches ) ) {
					$suggestions['currency_symbol'] = $matches[1];
				}
				break;

			case 'date':
				// Detect date format from first value
				$first_value = reset( $column_values );
				$suggestions['date_format'] = self::detect_date_format( $first_value );
				break;
		}

		return $suggestions;
	}

	/**
	 * Detect date format from value
	 *
	 * @param string $value Date string.
	 * @return string PHP date format
	 */
	private static function detect_date_format( $value ) {
		// Try to detect format
		if ( preg_match( '/^\d{4}[-\/]\d{1,2}[-\/]\d{1,2}$/', $value ) ) {
			return 'Y-m-d'; // 2024-01-15
		} elseif ( preg_match( '/^\d{1,2}[-\/]\d{1,2}[-\/]\d{4}$/', $value ) ) {
			return 'm/d/Y'; // 01/15/2024
		} elseif ( preg_match( '/^\w+\s\d{1,2},?\s\d{4}$/', $value ) ) {
			return 'F j, Y'; // January 15, 2024
		}

		return 'M d, Y'; // Default
	}
}

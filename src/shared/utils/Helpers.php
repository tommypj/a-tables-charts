<?php
/**
 * Helper Utility Functions
 *
 * Provides common helper functions used throughout the plugin.
 *
 * @package ATablesCharts\Shared\Utils
 * @since 1.0.0
 */

namespace ATablesCharts\Shared\Utils;

/**
 * Helpers Class
 *
 * Responsibilities:
 * - Format data for display
 * - Convert data types
 * - Generate unique IDs
 * - Date/time formatting
 * - Array manipulation
 */
class Helpers {

	/**
	 * Format bytes to human-readable size
	 *
	 * @since 1.0.0
	 * @param int $bytes Bytes to format.
	 * @return string Formatted size
	 */
	public static function format_bytes( $bytes ) {
		return size_format( $bytes );
	}

	/**
	 * Format number with thousands separator
	 *
	 * @since 1.0.0
	 * @param float  $number   Number to format.
	 * @param int    $decimals Number of decimal places.
	 * @return string Formatted number
	 */
	public static function format_number( $number, $decimals = 2 ) {
		$settings = get_option( 'atables_settings', array() );
		
		$decimal_sep   = isset( $settings['decimal_separator'] ) ? $settings['decimal_separator'] : '.';
		$thousands_sep = isset( $settings['thousands_separator'] ) ? $settings['thousands_separator'] : ',';

		return number_format( $number, $decimals, $decimal_sep, $thousands_sep );
	}

	/**
	 * Format date
	 *
	 * @since 1.0.0
	 * @param string $date   Date to format.
	 * @param string $format Format string (optional).
	 * @return string Formatted date
	 */
	public static function format_date( $date, $format = null ) {
		if ( null === $format ) {
			$settings = get_option( 'atables_settings', array() );
			$format   = isset( $settings['date_format'] ) ? $settings['date_format'] : 'Y-m-d';
		}

		return date_i18n( $format, strtotime( $date ) );
	}

	/**
	 * Format time
	 *
	 * @since 1.0.0
	 * @param string $time   Time to format.
	 * @param string $format Format string (optional).
	 * @return string Formatted time
	 */
	public static function format_time( $time, $format = null ) {
		if ( null === $format ) {
			$settings = get_option( 'atables_settings', array() );
			$format   = isset( $settings['time_format'] ) ? $settings['time_format'] : 'H:i:s';
		}

		return date_i18n( $format, strtotime( $time ) );
	}

	/**
	 * Generate unique ID
	 *
	 * @since 1.0.0
	 * @param string $prefix Prefix for the ID.
	 * @return string Unique ID
	 */
	public static function generate_id( $prefix = '' ) {
		return uniqid( $prefix, true );
	}

	/**
	 * Convert array to CSV string
	 *
	 * @since 1.0.0
	 * @param array $array Array to convert.
	 * @return string CSV string
	 */
	public static function array_to_csv( $array ) {
		if ( empty( $array ) ) {
			return '';
		}

		$output = fopen( 'php://temp', 'r+' );
		
		foreach ( $array as $row ) {
			fputcsv( $output, $row );
		}
		
		rewind( $output );
		$csv = stream_get_contents( $output );
		fclose( $output );

		return $csv;
	}

	/**
	 * Truncate string
	 *
	 * @since 1.0.0
	 * @param string $text   Text to truncate.
	 * @param int    $length Maximum length.
	 * @param string $suffix Suffix to append.
	 * @return string Truncated text
	 */
	public static function truncate( $text, $length = 100, $suffix = '...' ) {
		if ( mb_strlen( $text ) <= $length ) {
			return $text;
		}

		return mb_substr( $text, 0, $length ) . $suffix;
	}

	/**
	 * Get value from array by path
	 *
	 * @since 1.0.0
	 * @param array  $array   Array to search.
	 * @param string $path    Dot-notation path.
	 * @param mixed  $default Default value if not found.
	 * @return mixed Value or default
	 */
	public static function array_get( $array, $path, $default = null ) {
		$keys = explode( '.', $path );

		foreach ( $keys as $key ) {
			if ( ! is_array( $array ) || ! array_key_exists( $key, $array ) ) {
				return $default;
			}
			$array = $array[ $key ];
		}

		return $array;
	}

	/**
	 * Set value in array by path
	 *
	 * @since 1.0.0
	 * @param array  $array Array to modify.
	 * @param string $path  Dot-notation path.
	 * @param mixed  $value Value to set.
	 * @return array Modified array
	 */
	public static function array_set( &$array, $path, $value ) {
		$keys = explode( '.', $path );
		$last = array_pop( $keys );

		foreach ( $keys as $key ) {
			if ( ! isset( $array[ $key ] ) || ! is_array( $array[ $key ] ) ) {
				$array[ $key ] = array();
			}
			$array = &$array[ $key ];
		}

		$array[ $last ] = $value;
	}

	/**
	 * Check if value exists in multidimensional array
	 *
	 * @since 1.0.0
	 * @param mixed $needle   Value to search for.
	 * @param array $haystack Array to search in.
	 * @return bool True if found, false otherwise
	 */
	public static function in_array_recursive( $needle, $haystack ) {
		foreach ( $haystack as $item ) {
			if ( $item === $needle || ( is_array( $item ) && self::in_array_recursive( $needle, $item ) ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Flatten multidimensional array
	 *
	 * @since 1.0.0
	 * @param array $array Array to flatten.
	 * @return array Flattened array
	 */
	public static function array_flatten( $array ) {
		$result = array();

		array_walk_recursive(
			$array,
			function( $value ) use ( &$result ) {
				$result[] = $value;
			}
		);

		return $result;
	}

	/**
	 * Convert string to slug
	 *
	 * @since 1.0.0
	 * @param string $text Text to convert.
	 * @return string Slug
	 */
	public static function slugify( $text ) {
		return sanitize_title( $text );
	}

	/**
	 * Check if current request is AJAX
	 *
	 * @since 1.0.0
	 * @return bool True if AJAX request, false otherwise
	 */
	public static function is_ajax() {
		return wp_doing_ajax();
	}

	/**
	 * Check if current user has capability
	 *
	 * @since 1.0.0
	 * @param string $capability Capability to check.
	 * @return bool True if user has capability, false otherwise
	 */
	public static function current_user_can( $capability ) {
		return current_user_can( $capability );
	}

	/**
	 * Get current user ID
	 *
	 * @since 1.0.0
	 * @return int User ID or 0 if not logged in
	 */
	public static function get_current_user_id() {
		return get_current_user_id();
	}

	/**
	 * Send JSON response
	 *
	 * @since 1.0.0
	 * @param bool   $success Success status.
	 * @param mixed  $data    Response data.
	 * @param string $message Response message.
	 * @param int    $code    HTTP status code.
	 */
	public static function send_json( $success, $data = null, $message = '', $code = 200 ) {
		// Clean any output buffers to prevent corruption
		while ( ob_get_level() > 0 ) {
			ob_end_clean();
		}
		
		$response = array(
			'success' => $success,
			'message' => $message,
		);

		if ( null !== $data ) {
			$response['data'] = $data;
		}

		wp_send_json( $response, $code );
	}

	/**
	 * Get plugin option
	 *
	 * @since 1.0.0
	 * @param string $key     Option key.
	 * @param mixed  $default Default value.
	 * @return mixed Option value
	 */
	public static function get_option( $key, $default = null ) {
		$settings = get_option( 'atables_settings', array() );
		return isset( $settings[ $key ] ) ? $settings[ $key ] : $default;
	}

	/**
	 * Update plugin option
	 *
	 * @since 1.0.0
	 * @param string $key   Option key.
	 * @param mixed  $value Option value.
	 * @return bool True on success, false on failure
	 */
	public static function update_option( $key, $value ) {
		$settings         = get_option( 'atables_settings', array() );
		$settings[ $key ] = $value;
		return update_option( 'atables_settings', $settings );
	}
}

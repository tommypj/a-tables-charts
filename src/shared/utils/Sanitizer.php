<?php
/**
 * Sanitizer Utility
 *
 * Provides sanitization functions for various data types.
 *
 * @package ATablesCharts\Shared\Utils
 * @since 1.0.0
 */

namespace ATablesCharts\Shared\Utils;

/**
 * Sanitizer Class
 *
 * Responsibilities:
 * - Sanitize user inputs
 * - Clean data before database storage
 * - Prevent XSS and SQL injection
 * - Format data consistently
 */
class Sanitizer {

	/**
	 * Sanitize text field
	 *
	 * @since 1.0.0
	 * @param string $text Text to sanitize.
	 * @return string Sanitized text
	 */
	public static function text( $text ) {
		return sanitize_text_field( $text );
	}

	/**
	 * Sanitize textarea field
	 *
	 * @since 1.0.0
	 * @param string $textarea Textarea content to sanitize.
	 * @return string Sanitized textarea
	 */
	public static function textarea( $textarea ) {
		return sanitize_textarea_field( $textarea );
	}

	/**
	 * Sanitize email
	 *
	 * @since 1.0.0
	 * @param string $email Email to sanitize.
	 * @return string Sanitized email
	 */
	public static function email( $email ) {
		return sanitize_email( $email );
	}

	/**
	 * Sanitize URL
	 *
	 * @since 1.0.0
	 * @param string $url URL to sanitize.
	 * @return string Sanitized URL
	 */
	public static function url( $url ) {
		return esc_url_raw( $url );
	}

	/**
	 * Sanitize integer
	 *
	 * @since 1.0.0
	 * @param mixed $number Number to sanitize.
	 * @return int Sanitized integer
	 */
	public static function integer( $number ) {
		return intval( $number );
	}

	/**
	 * Sanitize float
	 *
	 * @since 1.0.0
	 * @param mixed $number Number to sanitize.
	 * @return float Sanitized float
	 */
	public static function float( $number ) {
		return floatval( $number );
	}

	/**
	 * Sanitize boolean
	 *
	 * @since 1.0.0
	 * @param mixed $value Value to convert to boolean.
	 * @return bool Boolean value
	 */
	public static function boolean( $value ) {
		return filter_var( $value, FILTER_VALIDATE_BOOLEAN );
	}

	/**
	 * Sanitize array of text fields
	 *
	 * @since 1.0.0
	 * @param array $array Array to sanitize.
	 * @return array Sanitized array
	 */
	public static function array_text( $array ) {
		if ( ! is_array( $array ) ) {
			return array();
		}

		return array_map( 'sanitize_text_field', $array );
	}

	/**
	 * Sanitize array recursively
	 *
	 * @since 1.0.0
	 * @param array $array Array to sanitize.
	 * @return array Sanitized array
	 */
	public static function array_recursive( $array ) {
		if ( ! is_array( $array ) ) {
			return sanitize_text_field( $array );
		}

		foreach ( $array as $key => $value ) {
			$array[ $key ] = self::array_recursive( $value );
		}

		return $array;
	}

	/**
	 * Sanitize HTML content
	 *
	 * @since 1.0.0
	 * @param string $html     HTML content to sanitize.
	 * @param array  $allowed  Allowed HTML tags (optional).
	 * @return string Sanitized HTML
	 */
	public static function html( $html, $allowed = null ) {
		if ( null === $allowed ) {
			$allowed = wp_kses_allowed_html( 'post' );
		}

		return wp_kses( $html, $allowed );
	}

	/**
	 * Sanitize SQL order by clause
	 *
	 * @since 1.0.0
	 * @param string $orderby   Order by field.
	 * @param array  $allowed   Allowed fields.
	 * @param string $default   Default field.
	 * @return string Sanitized order by
	 */
	public static function sql_orderby( $orderby, $allowed, $default = 'id' ) {
		$orderby = strtolower( trim( $orderby ) );

		if ( in_array( $orderby, $allowed, true ) ) {
			return $orderby;
		}

		return $default;
	}

	/**
	 * Sanitize SQL order direction
	 *
	 * @since 1.0.0
	 * @param string $order   Order direction (ASC/DESC).
	 * @param string $default Default direction.
	 * @return string Sanitized order direction
	 */
	public static function sql_order( $order, $default = 'ASC' ) {
		$order = strtoupper( trim( $order ) );

		if ( in_array( $order, array( 'ASC', 'DESC' ), true ) ) {
			return $order;
		}

		return $default;
	}

	/**
	 * Sanitize file name
	 *
	 * @since 1.0.0
	 * @param string $filename File name to sanitize.
	 * @return string Sanitized file name
	 */
	public static function filename( $filename ) {
		return sanitize_file_name( $filename );
	}

	/**
	 * Sanitize key (alphanumeric with dashes and underscores)
	 *
	 * @since 1.0.0
	 * @param string $key Key to sanitize.
	 * @return string Sanitized key
	 */
	public static function key( $key ) {
		return sanitize_key( $key );
	}

	/**
	 * Sanitize slug
	 *
	 * @since 1.0.0
	 * @param string $slug Slug to sanitize.
	 * @return string Sanitized slug
	 */
	public static function slug( $slug ) {
		return sanitize_title( $slug );
	}

	/**
	 * Sanitize hex color
	 *
	 * @since 1.0.0
	 * @param string $color Hex color to sanitize.
	 * @return string Sanitized hex color
	 */
	public static function hex_color( $color ) {
		return sanitize_hex_color( $color );
	}

	/**
	 * Sanitize comma-separated list
	 *
	 * @since 1.0.0
	 * @param string $list Comma-separated list.
	 * @return string Sanitized list
	 */
	public static function comma_list( $list ) {
		$items = array_map( 'trim', explode( ',', $list ) );
		$items = array_map( 'sanitize_text_field', $items );
		$items = array_filter( $items ); // Remove empty items.

		return implode( ', ', $items );
	}

	/**
	 * Deep clean array for database storage
	 *
	 * @since 1.0.0
	 * @param mixed $data Data to clean.
	 * @return mixed Cleaned data
	 */
	public static function deep_clean( $data ) {
		if ( is_array( $data ) ) {
			return array_map( array( __CLASS__, 'deep_clean' ), $data );
		}

		if ( is_string( $data ) ) {
			return wp_kses_post( $data );
		}

		return $data;
	}

	/**
	 * Strip all tags except specified
	 *
	 * @since 1.0.0
	 * @param string $content       Content to strip.
	 * @param string $allowed_tags  Allowed tags.
	 * @return string Stripped content
	 */
	public static function strip_tags( $content, $allowed_tags = '' ) {
		return wp_strip_all_tags( $content, false );
	}
}

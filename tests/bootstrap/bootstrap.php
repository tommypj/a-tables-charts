<?php
/**
 * PHPUnit Bootstrap File
 *
 * Sets up the testing environment for WordPress plugin tests.
 *
 * @package ATablesCharts\Tests
 * @since 1.0.0
 */

// Define plugin constants for testing.
define( 'ATABLES_VERSION', '1.0.0' );
define( 'ATABLES_SLUG', 'a-tables-charts' );
define( 'ATABLES_PLUGIN_DIR', dirname( dirname( __DIR__ ) ) . '/' );
define( 'ATABLES_PLUGIN_URL', 'http://example.com/wp-content/plugins/a-tables-charts/' );
define( 'ATABLES_PLUGIN_BASENAME', 'a-tables-charts/a-tables-charts.php' );

// Define WP_DEBUG for testing (can be overridden in individual tests).
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', true );
}
if ( ! defined( 'WP_DEBUG_LOG' ) ) {
	define( 'WP_DEBUG_LOG', true );
}

// Load Composer autoloader if available.
if ( file_exists( dirname( dirname( __DIR__ ) ) . '/vendor/autoload.php' ) ) {
	require_once dirname( dirname( __DIR__ ) ) . '/vendor/autoload.php';
}

// Custom autoloader for ATablesCharts namespace to handle case-insensitive directories.
spl_autoload_register( function ( $class ) {
	// Only handle ATablesCharts namespace.
	$prefix = 'ATablesCharts\\';
	$base_dir = dirname( dirname( __DIR__ ) ) . '/src/';

	// Check if the class uses the namespace prefix.
	$len = strlen( $prefix );
	if ( strncmp( $prefix, $class, $len ) !== 0 ) {
		return; // Not our namespace.
	}

	// Get the relative class name.
	$relative_class = substr( $class, $len );

	// Convert namespace separators to directory separators.
	$relative_class_path = str_replace( '\\', '/', $relative_class );

	// Handle the specific directory structure:
	// - First level: lcfirst (Export -> export, Tables -> tables, DataSources -> dataSources)
	// - Second level: lowercase (Services -> services, Types -> types, etc.)
	// - Class file: as-is (CSVExportService.php, Table.php, etc.)

	// Common directory name mappings.
	$dir_mappings = array(
		'/Services/'      => '/services/',
		'/Types/'         => '/types/',
		'/Repositories/'  => '/repositories/',
		'/Controllers/'   => '/controllers/',
		'/Parsers/'       => '/parsers/',
		'/Renderers/'     => '/renderers/',
		'/Utils/'         => '/utils/',
		'/Interfaces/'    => '/interfaces/',
		'/Exceptions/'    => '/exceptions/',
		'/Middleware/'    => '/middleware/',
		'/Validators/'    => '/validators/',
		'/Migrations/'    => '/migrations/',
	);

	// Try different path strategies.
	$possible_paths = array();

	// Strategy 1: modules/ + lcfirst first segment + lowercase middle segments.
	$normalized_path = $relative_class_path;
	foreach ( $dir_mappings as $upper => $lower ) {
		$normalized_path = str_replace( $upper, $lower, $normalized_path );
	}
	$parts = explode( '/', $normalized_path );
	if ( count( $parts ) > 0 ) {
		$parts[0] = lcfirst( $parts[0] );
		$possible_paths[] = $base_dir . 'modules/' . implode( '/', $parts ) . '.php';
	}

	// Strategy 2: shared/ + lowercase segments (for shared utilities).
	$normalized_shared = $relative_class_path;
	foreach ( $dir_mappings as $upper => $lower ) {
		$normalized_shared = str_replace( $upper, $lower, $normalized_shared );
	}
	$possible_paths[] = $base_dir . $normalized_shared . '.php';
	$possible_paths[] = $base_dir . strtolower( $normalized_shared ) . '.php';

	// Strategy 3: lowercase directories but preserve class filename case.
	$parts_shared = explode( '/', $normalized_shared );
	if ( count( $parts_shared ) > 1 ) {
		$class_name = array_pop( $parts_shared );
		$dirs = array_map( 'strtolower', $parts_shared );
		$possible_paths[] = $base_dir . implode( '/', $dirs ) . '/' . $class_name . '.php';
	}

	// Try each possible path.
	foreach ( $possible_paths as $file ) {
		if ( file_exists( $file ) ) {
			require $file;
			return;
		}
	}
} );

// Mock WordPress functions for unit tests that don't need full WordPress.
if ( ! function_exists( 'is_email' ) ) {
	/**
	 * Mock WordPress is_email function
	 *
	 * @param string $email Email to validate.
	 * @return bool|string
	 */
	function is_email( $email ) {
		return filter_var( $email, FILTER_VALIDATE_EMAIL ) !== false ? $email : false;
	}
}

if ( ! function_exists( '__' ) ) {
	/**
	 * Mock WordPress translation function
	 *
	 * @param string $text Text to translate.
	 * @param string $domain Text domain.
	 * @return string
	 */
	function __( $text, $domain = 'default' ) {
		return $text;
	}
}

if ( ! function_exists( 'esc_html__' ) ) {
	/**
	 * Mock WordPress escaped translation function
	 *
	 * @param string $text Text to translate.
	 * @param string $domain Text domain.
	 * @return string
	 */
	function esc_html__( $text, $domain = 'default' ) {
		return htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' );
	}
}

if ( ! function_exists( 'wp_check_filetype' ) ) {
	/**
	 * Mock WordPress file type check
	 *
	 * @param string $filename Filename.
	 * @param array  $mimes Optional. Array of mime types.
	 * @return array
	 */
	function wp_check_filetype( $filename, $mimes = null ) {
		$ext = strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) );
		$type = '';
		
		$mime_types = array(
			'csv'  => 'text/csv',
			'xml'  => 'application/xml',
			'json' => 'application/json',
			'txt'  => 'text/plain',
		);
		
		if ( isset( $mime_types[ $ext ] ) ) {
			$type = $mime_types[ $ext ];
		}
		
		return array(
			'ext'  => $ext,
			'type' => $type,
		);
	}
}

if ( ! function_exists( 'size_format' ) ) {
	/**
	 * Mock WordPress size format function
	 *
	 * @param int $bytes Number of bytes.
	 * @return string
	 */
	function size_format( $bytes ) {
		$units = array( 'B', 'KB', 'MB', 'GB', 'TB' );
		
		$bytes = max( $bytes, 0 );
		$pow = floor( ( $bytes ? log( $bytes ) : 0 ) / log( 1024 ) );
		$pow = min( $pow, count( $units ) - 1 );
		
		$bytes /= pow( 1024, $pow );
		
		return round( $bytes, 2 ) . ' ' . $units[ $pow ];
	}
}

if ( ! function_exists( 'wp_verify_nonce' ) ) {
	/**
	 * Mock WordPress nonce verification
	 *
	 * @param string $nonce Nonce value.
	 * @param string $action Nonce action.
	 * @return int|false
	 */
	function wp_verify_nonce( $nonce, $action ) {
		// For testing purposes, accept any non-empty nonce.
		return ! empty( $nonce ) ? 1 : false;
	}
}

if ( ! function_exists( 'current_time' ) ) {
	/**
	 * Mock WordPress current_time function
	 *
	 * @param string $type Type of time to retrieve (mysql, timestamp, or PHP date format).
	 * @param int    $gmt  Optional. Whether to use GMT timezone. Default false.
	 * @return int|string
	 */
	function current_time( $type, $gmt = 0 ) {
		if ( 'mysql' === $type ) {
			return gmdate( 'Y-m-d H:i:s' );
		}
		if ( 'timestamp' === $type || 'U' === $type ) {
			return time();
		}
		return gmdate( $type );
	}
}

if ( ! function_exists( 'get_current_user_id' ) ) {
	/**
	 * Mock WordPress get_current_user_id function
	 *
	 * @return int
	 */
	function get_current_user_id() {
		// For testing purposes, return a test user ID.
		return 1;
	}
}

if ( ! function_exists( 'wp_unslash' ) ) {
	/**
	 * Mock WordPress wp_unslash function
	 *
	 * @param string|array $value String or array to remove slashes from.
	 * @return string|array
	 */
	function wp_unslash( $value ) {
		return is_string( $value ) ? stripslashes( $value ) : $value;
	}
}

if ( ! function_exists( 'esc_sql' ) ) {
	/**
	 * Mock WordPress esc_sql function
	 *
	 * @param string $data Data to escape.
	 * @return string
	 */
	function esc_sql( $data ) {
		global $wpdb;
		if ( isset( $wpdb ) && method_exists( $wpdb, '_real_escape' ) ) {
			return $wpdb->_real_escape( $data );
		}
		return addslashes( $data );
	}
}

if ( ! function_exists( 'sanitize_text_field' ) ) {
	/**
	 * Mock WordPress sanitize_text_field function
	 *
	 * @param string $str String to sanitize.
	 * @return string
	 */
	function sanitize_text_field( $str ) {
		$filtered = wp_check_invalid_utf8( $str );
		// Remove script/style tags AND their content (security)
		$filtered = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $filtered );
		// Now strip remaining tags but keep their content
		$filtered = strip_tags( $filtered );
		$filtered = trim( $filtered );
		return $filtered;
	}
}

if ( ! function_exists( 'wp_check_invalid_utf8' ) ) {
	/**
	 * Mock WordPress wp_check_invalid_utf8 function
	 *
	 * @param string $string String to check.
	 * @param bool   $strip  Whether to strip invalid UTF8.
	 * @return string
	 */
	function wp_check_invalid_utf8( $string, $strip = false ) {
		// Simple UTF-8 validation.
		if ( ! preg_match( '//u', $string ) ) {
			return '';
		}
		return $string;
	}
}

if ( ! function_exists( 'sanitize_key' ) ) {
	/**
	 * Mock WordPress sanitize_key function
	 *
	 * @param string $key String key.
	 * @return string
	 */
	function sanitize_key( $key ) {
		$key = strtolower( $key );
		$key = preg_replace( '/[^a-z0-9_\-]/', '', $key );
		return $key;
	}
}

if ( ! function_exists( 'sanitize_textarea_field' ) ) {
	/**
	 * Mock WordPress sanitize_textarea_field function
	 *
	 * @param string $str String to sanitize.
	 * @return string
	 */
	function sanitize_textarea_field( $str ) {
		$filtered = wp_check_invalid_utf8( $str );
		$filtered = str_replace( "\r", '', $filtered );
		return $filtered;
	}
}

if ( ! function_exists( 'sanitize_email' ) ) {
	/**
	 * Mock WordPress sanitize_email function
	 *
	 * @param string $email Email to sanitize.
	 * @return string
	 */
	function sanitize_email( $email ) {
		return strtolower( trim( $email ) );
	}
}

if ( ! function_exists( 'esc_url_raw' ) ) {
	/**
	 * Mock WordPress esc_url_raw function
	 *
	 * @param string $url URL to escape.
	 * @return string
	 */
	function esc_url_raw( $url ) {
		// Remove dangerous protocols
		$dangerous_protocols = array( 'javascript:', 'data:', 'vbscript:', 'file:' );
		foreach ( $dangerous_protocols as $protocol ) {
			if ( stripos( $url, $protocol ) === 0 ) {
				return '';
			}
		}
		return filter_var( $url, FILTER_SANITIZE_URL );
	}
}

if ( ! function_exists( 'esc_url' ) ) {
	/**
	 * Mock WordPress esc_url function
	 *
	 * @param string $url URL to escape.
	 * @return string
	 */
	function esc_url( $url ) {
		return esc_url_raw( $url );
	}
}

if ( ! function_exists( 'sanitize_file_name' ) ) {
	/**
	 * Mock WordPress sanitize_file_name function
	 *
	 * @param string $filename Filename to sanitize.
	 * @return string
	 */
	function sanitize_file_name( $filename ) {
		$filename = preg_replace( '/[^a-zA-Z0-9._\-]/', '', $filename );
		return $filename;
	}
}

if ( ! function_exists( 'sanitize_title' ) ) {
	/**
	 * Mock WordPress sanitize_title function
	 *
	 * @param string $title Title to sanitize.
	 * @return string
	 */
	function sanitize_title( $title ) {
		$title = strtolower( $title );
		$title = preg_replace( '/[^a-z0-9\-]/', '-', $title );
		$title = preg_replace( '/-+/', '-', $title );
		$title = trim( $title, '-' );
		return $title;
	}
}

if ( ! function_exists( 'sanitize_hex_color' ) ) {
	/**
	 * Mock WordPress sanitize_hex_color function
	 *
	 * @param string $color Hex color to sanitize.
	 * @return string|null
	 */
	function sanitize_hex_color( $color ) {
		if ( '' === $color ) {
			return '';
		}

		if ( preg_match( '/^#[a-f0-9]{6}$/i', $color ) ) {
			return $color;
		}

		if ( preg_match( '/^#[a-f0-9]{3}$/i', $color ) ) {
			return $color;
		}

		return null;
	}
}

if ( ! function_exists( 'wp_kses_allowed_html' ) ) {
	/**
	 * Mock WordPress wp_kses_allowed_html function
	 *
	 * @param string $context Context.
	 * @return array
	 */
	function wp_kses_allowed_html( $context = 'post' ) {
		return array(
			'a'      => array( 'href' => true, 'title' => true ),
			'b'      => array(),
			'strong' => array(),
			'em'     => array(),
			'i'      => array(),
			'p'      => array(),
			'br'     => array(),
		);
	}
}

if ( ! function_exists( 'wp_kses' ) ) {
	/**
	 * Mock WordPress wp_kses function
	 *
	 * @param string $string HTML string.
	 * @param array  $allowed_html Allowed HTML.
	 * @return string
	 */
	function wp_kses( $string, $allowed_html ) {
		// Remove script tags and their content
		$string = preg_replace( '@<script[^>]*?>.*?</script>@si', '', $string );

		// Remove dangerous event handlers
		$string = preg_replace( '/\s*on\w+\s*=\s*["\'][^"\']*["\']/i', '', $string );
		$string = preg_replace( '/\s*on\w+\s*=\s*[^\s>]*/i', '', $string );

		// Strip all tags except allowed
		$allowed_tags = '<' . implode( '><', array_keys( $allowed_html ) ) . '>';
		return strip_tags( $string, $allowed_tags );
	}
}

if ( ! function_exists( 'wp_kses_post' ) ) {
	/**
	 * Mock WordPress wp_kses_post function
	 *
	 * @param string $data Data to sanitize.
	 * @return string
	 */
	function wp_kses_post( $data ) {
		return wp_kses( $data, wp_kses_allowed_html( 'post' ) );
	}
}

if ( ! function_exists( 'wp_strip_all_tags' ) ) {
	/**
	 * Mock WordPress wp_strip_all_tags function
	 *
	 * @param string $string String to strip.
	 * @param bool   $remove_breaks Whether to remove breaks.
	 * @return string
	 */
	function wp_strip_all_tags( $string, $remove_breaks = false ) {
		$string = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $string );
		$string = strip_tags( $string );

		if ( $remove_breaks ) {
			$string = preg_replace( '/[\r\n\t ]+/', ' ', $string );
		}

		return trim( $string );
	}
}

if ( ! function_exists( 'wp_upload_dir' ) ) {
	/**
	 * Mock WordPress wp_upload_dir function
	 *
	 * @return array
	 */
	function wp_upload_dir() {
		$upload_path = sys_get_temp_dir() . '/atables-test-uploads';
		return array(
			'path'    => $upload_path,
			'url'     => 'http://example.com/wp-content/uploads',
			'subdir'  => '',
			'basedir' => $upload_path,
			'baseurl' => 'http://example.com/wp-content/uploads',
			'error'   => false,
		);
	}
}

if ( ! function_exists( 'wp_json_encode' ) ) {
	/**
	 * Mock WordPress wp_json_encode function
	 *
	 * @param mixed $data Data to encode.
	 * @param int   $options JSON options.
	 * @param int   $depth Maximum depth.
	 * @return string|false
	 */
	function wp_json_encode( $data, $options = 0, $depth = 512 ) {
		return json_encode( $data, $options, $depth );
	}
}

if ( ! function_exists( 'wp_mkdir_p' ) ) {
	/**
	 * Mock WordPress wp_mkdir_p function
	 *
	 * @param string $target Directory path.
	 * @return bool
	 */
	function wp_mkdir_p( $target ) {
		if ( file_exists( $target ) ) {
			return is_dir( $target );
		}
		return @mkdir( $target, 0755, true );
	}
}

if ( ! function_exists( 'wp_delete_file' ) ) {
	/**
	 * Mock WordPress wp_delete_file function
	 *
	 * @param string $file File path.
	 * @return bool
	 */
	function wp_delete_file( $file ) {
		if ( file_exists( $file ) ) {
			return @unlink( $file );
		}
		return true;
	}
}

// Load test helpers.
if ( file_exists( __DIR__ . '/TestCase.php' ) ) {
	require __DIR__ . '/TestCase.php';
}

// Optional: Check if WordPress test library is available for integration tests.
$_tests_dir = getenv( 'WP_TESTS_DIR' );

if ( $_tests_dir && file_exists( $_tests_dir . '/includes/functions.php' ) ) {
	// WordPress test library is available - load it for integration tests.
	require_once $_tests_dir . '/includes/functions.php';
	
	/**
	 * Manually load the plugin for testing
	 */
	function _manually_load_plugin() {
		require dirname( dirname( __DIR__ ) ) . '/a-tables-charts.php';
	}
	
	tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );
	require $_tests_dir . '/includes/bootstrap.php';
} else {
	// WordPress test library not available - running pure unit tests only.
	// This is fine for testing utility classes that don't depend on WordPress.
	echo "Running unit tests without WordPress (pure PHP tests)\n";
}

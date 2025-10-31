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

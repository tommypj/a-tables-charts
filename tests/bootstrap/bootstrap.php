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

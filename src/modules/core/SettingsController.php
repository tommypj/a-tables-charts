<?php
/**
 * Settings Controller
 *
 * Handles settings page logic and AJAX operations
 *
 * @package ATablesCharts\Core
 * @since 1.0.0
 */

namespace ATablesCharts\Core;

/**
 * Settings Controller Class
 */
class SettingsController {

	/**
	 * Register hooks
	 */
	public function register_hooks() {
		// Register settings
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		
		// AJAX handlers
		add_action( 'wp_ajax_atables_clear_cache', array( $this, 'clear_cache' ) );
		add_action( 'wp_ajax_atables_reset_cache_stats', array( $this, 'reset_cache_stats' ) );
	}

	/**
	 * Register plugin settings
	 */
	public function register_settings() {
		register_setting(
			'atables_settings',
			'atables_settings',
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize_settings' ),
				'default'           => array(),
			)
		);
	}

	/**
	 * Sanitize settings before saving
	 *
	 * @param array $input Raw input data.
	 * @return array Sanitized settings.
	 */
	public function sanitize_settings( $input ) {
		$sanitized = array();

		// General Settings
		if ( isset( $input['rows_per_page'] ) ) {
			$sanitized['rows_per_page'] = absint( $input['rows_per_page'] );
			$sanitized['rows_per_page'] = max( 1, min( 100, $sanitized['rows_per_page'] ) );
		}

		if ( isset( $input['default_table_style'] ) ) {
			$allowed_styles = array( 'default', 'striped', 'bordered', 'hover' );
			$sanitized['default_table_style'] = in_array( $input['default_table_style'], $allowed_styles, true )
				? $input['default_table_style']
				: 'default';
		}

		// Boolean checkboxes
		$boolean_fields = array(
			'enable_responsive',
			'enable_search',
			'enable_sorting',
			'enable_pagination',
			'enable_export',
			'cache_enabled',
			'lazy_load_tables',
			'async_loading',
			'google_charts_enabled',
			'chartjs_enabled',
			'sanitize_html',
		);

		foreach ( $boolean_fields as $field ) {
			// Explicitly set to false if not present (checkbox unchecked)
			$sanitized[ $field ] = isset( $input[ $field ] ) && $input[ $field ] == '1';
		}
		
		// Special handling for mysql_query_enabled - save as '1' or '0' string instead of boolean
		if ( isset( $input['mysql_query_enabled'] ) && $input['mysql_query_enabled'] == '1' ) {
			$sanitized['mysql_query_enabled'] = '1';
		} else {
			$sanitized['mysql_query_enabled'] = '0';
		}
		
		// Also save it with the old key name for backwards compatibility
		$sanitized['enable_mysql_query'] = $sanitized['mysql_query_enabled'];

		// Data Formatting
		if ( isset( $input['date_format'] ) ) {
			$sanitized['date_format'] = sanitize_text_field( $input['date_format'] );
		}

		if ( isset( $input['time_format'] ) ) {
			$sanitized['time_format'] = sanitize_text_field( $input['time_format'] );
		}

		if ( isset( $input['decimal_separator'] ) ) {
			$sanitized['decimal_separator'] = substr( sanitize_text_field( $input['decimal_separator'] ), 0, 1 );
		}

		if ( isset( $input['thousands_separator'] ) ) {
			$sanitized['thousands_separator'] = substr( sanitize_text_field( $input['thousands_separator'] ), 0, 1 );
		}

		// Import Settings
		if ( isset( $input['max_import_size'] ) ) {
			$max_mb = absint( $input['max_import_size'] );
			$max_mb = max( 1, min( 100, $max_mb ) );
			$sanitized['max_import_size'] = $max_mb * 1048576; // Convert MB to bytes
		}

		if ( isset( $input['csv_delimiter'] ) ) {
			$sanitized['csv_delimiter'] = substr( sanitize_text_field( $input['csv_delimiter'] ), 0, 1 );
		}

		if ( isset( $input['csv_enclosure'] ) ) {
			$sanitized['csv_enclosure'] = substr( sanitize_text_field( $input['csv_enclosure'] ), 0, 1 );
		}

		if ( isset( $input['csv_escape'] ) ) {
			$sanitized['csv_escape'] = substr( sanitize_text_field( $input['csv_escape'] ), 0, 1 );
		}

		// Export Settings
		if ( isset( $input['export_filename'] ) ) {
			$sanitized['export_filename'] = sanitize_file_name( $input['export_filename'] );
		}

		if ( isset( $input['export_date_format'] ) ) {
			$sanitized['export_date_format'] = sanitize_text_field( $input['export_date_format'] );
		}

		if ( isset( $input['export_encoding'] ) ) {
			$allowed_encodings = array( 'UTF-8', 'ISO-8859-1', 'Windows-1252' );
			$sanitized['export_encoding'] = in_array( $input['export_encoding'], $allowed_encodings, true )
				? $input['export_encoding']
				: 'UTF-8';
		}

		// Performance & Cache
		if ( isset( $input['cache_expiration'] ) ) {
			$sanitized['cache_expiration'] = absint( $input['cache_expiration'] );
		}

		// Chart Settings
		if ( isset( $input['default_chart_library'] ) ) {
			$allowed_libraries = array( 'chartjs', 'google' );
			$sanitized['default_chart_library'] = in_array( $input['default_chart_library'], $allowed_libraries, true )
				? $input['default_chart_library']
				: 'chartjs';
		}

		// Security Settings
		if ( isset( $input['allowed_file_types'] ) && is_array( $input['allowed_file_types'] ) ) {
			$allowed_types = array( 'csv', 'json', 'xlsx', 'xls', 'xml' );
			$sanitized['allowed_file_types'] = array_intersect( $input['allowed_file_types'], $allowed_types );
			
			// Ensure at least CSV is allowed
			if ( empty( $sanitized['allowed_file_types'] ) ) {
				$sanitized['allowed_file_types'] = array( 'csv' );
				}
				}

		
		return $sanitized;
	}

	/**
	 * Clear all cache via AJAX
	 */
	public function clear_cache() {
		// Verify nonce
		check_ajax_referer( 'atables_admin_nonce', 'nonce' );

		// Check permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array(
				'message' => __( 'Insufficient permissions.', 'a-tables-charts' ),
			) );
		}

		try {
			// Load cache module
			require_once ATABLES_PLUGIN_DIR . 'src/modules/cache/index.php';
			$cache_service = new \ATablesCharts\Cache\Services\CacheService();

			// Clear cache
			$cleared_count = $cache_service->clear_all();

			wp_send_json_success( array(
				'message' => sprintf(
					/* translators: %d: number of cache items cleared */
					__( 'Successfully cleared %d cache items!', 'a-tables-charts' ),
					$cleared_count
				),
			) );
		} catch ( \Exception $e ) {
			wp_send_json_error( array(
				'message' => __( 'Failed to clear cache: ', 'a-tables-charts' ) . $e->getMessage(),
			) );
		}
	}

	/**
	 * Reset cache statistics via AJAX
	 */
	public function reset_cache_stats() {
		// Verify nonce
		check_ajax_referer( 'atables_admin_nonce', 'nonce' );

		// Check permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array(
				'message' => __( 'Insufficient permissions.', 'a-tables-charts' ),
			) );
		}

		try {
			// Load cache module
			require_once ATABLES_PLUGIN_DIR . 'src/modules/cache/index.php';
			$cache_service = new \ATablesCharts\Cache\Services\CacheService();

			// Reset stats
			$cache_service->reset_stats();

			wp_send_json_success( array(
				'message' => __( 'Cache statistics reset successfully!', 'a-tables-charts' ),
			) );
		} catch ( \Exception $e ) {
			wp_send_json_error( array(
				'message' => __( 'Failed to reset statistics: ', 'a-tables-charts' ) . $e->getMessage(),
			) );
		}
	}

	/**
	 * Get default settings
	 *
	 * @return array Default settings.
	 */
	public static function get_defaults() {
		return array(
		// General
		'rows_per_page'          => 10,
		'default_table_style'    => 'default',
		'enable_responsive'      => true,
		'enable_search'          => true,
		'enable_sorting'         => true,
		'enable_pagination'      => true,
		'enable_export'          => true,
		// Data Formatting
		'date_format'            => 'Y-m-d',
		'time_format'            => 'H:i:s',
		'decimal_separator'      => '.',
		'thousands_separator'    => ',',
		// Import Settings
		'max_import_size'        => 10485760, // 10MB
		'csv_delimiter'          => ',',
		'csv_enclosure'          => '"',
		'csv_escape'             => '\\',
		// Export Settings
		'export_filename'        => 'table-export',
		'export_date_format'     => 'Y-m-d',
		'export_encoding'        => 'UTF-8',
		// Performance & Cache
		'cache_enabled'          => true,
		'cache_expiration'       => 3600,
		'lazy_load_tables'       => false,
		'async_loading'          => false,
		// Charts
		'google_charts_enabled'  => true,
		'chartjs_enabled'        => true,
		'default_chart_library'  => 'chartjs',
		// Security
		'allowed_file_types'     => array( 'csv', 'json', 'xlsx', 'xls', 'xml' ),
		'sanitize_html'          => true,
		 'enable_mysql_query'     => true,
			);
	}

	/**
	 * Get a specific setting value
	 *
	 * @param string $key     Setting key.
	 * @param mixed  $default Default value if setting doesn't exist.
	 * @return mixed Setting value.
	 */
	public static function get_setting( $key, $default = null ) {
		$settings = get_option( 'atables_settings', array() );
		$defaults = self::get_defaults();
		$settings = wp_parse_args( $settings, $defaults );

		if ( isset( $settings[ $key ] ) ) {
			return $settings[ $key ];
		}

		return $default !== null ? $default : ( $defaults[ $key ] ?? null );
	}

	/**
	 * Update a specific setting
	 *
	 * @param string $key   Setting key.
	 * @param mixed  $value Setting value.
	 * @return bool True on success, false on failure.
	 */
	public static function update_setting( $key, $value ) {
		$settings = get_option( 'atables_settings', array() );
		$settings[ $key ] = $value;
		return update_option( 'atables_settings', $settings );
	}
}

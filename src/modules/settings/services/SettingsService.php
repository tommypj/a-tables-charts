<?php
/**
 * Settings Service
 *
 * Handles plugin settings operations.
 *
 * @package ATablesCharts\Settings\Services
 * @since 1.0.0
 */

namespace ATablesCharts\Settings\Services;

/**
 * SettingsService Class
 *
 * Responsibilities:
 * - Load and save settings
 * - Provide default values
 * - Validate settings
 */
class SettingsService {

	/**
	 * Option name in WordPress
	 *
	 * @var string
	 */
	private $option_name = 'atables_settings';

	/**
	 * Default settings
	 *
	 * @var array
	 */
	private $defaults = array(
		// General
		'rows_per_page'         => 10,
		'default_table_style'   => 'default',
		'enable_responsive'     => true,
		'enable_search'         => true,
		'enable_sorting'        => true,
		'enable_pagination'     => true,
		'enable_export'         => true,
		
		// Data Formatting
		'date_format'           => 'Y-m-d',
		'time_format'           => 'H:i:s',
		'decimal_separator'     => '.',
		'thousands_separator'   => ',',
		'decimal_places'        => 2,
		
		// Import Settings
		'max_import_size'       => 10485760, // 10MB in bytes
		'csv_delimiter'         => ',',
		'csv_enclosure'         => '"',
		'csv_escape'            => '\\',
		
		// Export Settings
		'export_filename'       => 'table-export',
		'export_date_format'    => 'Y-m-d',
		'export_encoding'       => 'UTF-8',
		
		// PDF Export Settings
		'pdf_page_orientation'  => 'auto',
		'pdf_font_size'         => 9,
		'pdf_max_rows'          => 5000,
		
		// Cache Settings
		'cache_enabled'         => true,
		'cache_expiration'      => 3600, // 1 hour
		
		// Chart Settings
		'google_charts_enabled' => true,
		'chartjs_enabled'       => true,
		'default_chart_type'    => 'bar',
		'chart_animation'       => true,
		
		// Performance
		'lazy_load_tables'      => false,
		'async_loading'         => false,
		
		// Security
		'allowed_file_types'    => array( 'csv', 'json', 'xlsx', 'xls', 'xml' ),
		'sanitize_html'         => true,
	);

	/**
	 * Get all settings
	 *
	 * @return array Settings array
	 */
	public function get_settings() {
		$settings = get_option( $this->option_name, array() );
		return wp_parse_args( $settings, $this->defaults );
	}

	/**
	 * Get a specific setting
	 *
	 * @param string $key     Setting key.
	 * @param mixed  $default Default value if setting doesn't exist.
	 * @return mixed Setting value
	 */
	public function get( $key, $default = null ) {
		$settings = $this->get_settings();
		
		if ( isset( $settings[ $key ] ) ) {
			return $settings[ $key ];
		}
		
		return $default !== null ? $default : ( isset( $this->defaults[ $key ] ) ? $this->defaults[ $key ] : null );
	}

	/**
	 * Update settings
	 *
	 * @param array $new_settings New settings to save.
	 * @return bool True on success, false on failure
	 */
	public function update_settings( $new_settings ) {
		$current_settings = $this->get_settings();
		$merged_settings  = array_merge( $current_settings, $new_settings );
		
		// Validate settings before saving
		$validated_settings = $this->validate_settings( $merged_settings );
		
		return update_option( $this->option_name, $validated_settings );
	}

	/**
	 * Update a single setting
	 *
	 * @param string $key   Setting key.
	 * @param mixed  $value Setting value.
	 * @return bool True on success, false on failure
	 */
	public function set( $key, $value ) {
		$settings = $this->get_settings();
		$settings[ $key ] = $value;
		
		return update_option( $this->option_name, $settings );
	}

	/**
	 * Delete all settings
	 *
	 * @return bool True on success, false on failure
	 */
	public function delete_settings() {
		return delete_option( $this->option_name );
	}

	/**
	 * Reset to default settings
	 *
	 * @return bool True on success, false on failure
	 */
	public function reset_to_defaults() {
		return update_option( $this->option_name, $this->defaults );
	}

	/**
	 * Validate settings
	 *
	 * @param array $settings Settings to validate.
	 * @return array Validated settings
	 */
	private function validate_settings( $settings ) {
		$validated = array();

		// Rows per page (1-100)
		if ( isset( $settings['rows_per_page'] ) ) {
			$validated['rows_per_page'] = max( 1, min( 100, intval( $settings['rows_per_page'] ) ) );
		}

		// Table style
		if ( isset( $settings['default_table_style'] ) ) {
			$allowed_styles = array( 'default', 'striped', 'bordered', 'hover' );
			$validated['default_table_style'] = in_array( $settings['default_table_style'], $allowed_styles, true ) 
				? $settings['default_table_style'] 
				: 'default';
		}

		// Boolean settings
		$boolean_settings = array(
			'enable_responsive',
			'enable_search',
			'enable_sorting',
			'enable_pagination',
			'enable_export',
			'cache_enabled',
			'google_charts_enabled',
			'chartjs_enabled',
			'chart_animation',
			'lazy_load_tables',
			'async_loading',
			'sanitize_html',
		);

		foreach ( $boolean_settings as $key ) {
			if ( isset( $settings[ $key ] ) ) {
				$validated[ $key ] = (bool) $settings[ $key ];
			}
		}

		// Text settings (sanitize)
		$text_settings = array(
			'date_format',
			'time_format',
			'decimal_separator',
			'thousands_separator',
			'csv_delimiter',
			'csv_enclosure',
			'csv_escape',
			'export_filename',
			'export_date_format',
			'export_encoding',
			'default_chart_type',
			'pdf_page_orientation',
		);

		foreach ( $text_settings as $key ) {
			if ( isset( $settings[ $key ] ) ) {
				$validated[ $key ] = sanitize_text_field( $settings[ $key ] );
			}
		}

		// Numeric settings
		if ( isset( $settings['decimal_places'] ) ) {
			$validated['decimal_places'] = max( 0, min( 10, intval( $settings['decimal_places'] ) ) );
		}

		if ( isset( $settings['cache_expiration'] ) ) {
			$validated['cache_expiration'] = max( 0, intval( $settings['cache_expiration'] ) );
		}

		if ( isset( $settings['max_import_size'] ) ) {
			$validated['max_import_size'] = max( 1048576, intval( $settings['max_import_size'] ) ); // Min 1MB
		}

		// PDF settings
		if ( isset( $settings['pdf_font_size'] ) ) {
			$validated['pdf_font_size'] = max( 6, min( 14, intval( $settings['pdf_font_size'] ) ) );
		}

		if ( isset( $settings['pdf_max_rows'] ) ) {
			$validated['pdf_max_rows'] = max( 100, min( 10000, intval( $settings['pdf_max_rows'] ) ) );
		}

		// Array settings
		if ( isset( $settings['allowed_file_types'] ) && is_array( $settings['allowed_file_types'] ) ) {
			$validated['allowed_file_types'] = array_map( 'sanitize_text_field', $settings['allowed_file_types'] );
		}

		return $validated;
	}

	/**
	 * Get default settings
	 *
	 * @return array Default settings
	 */
	public function get_defaults() {
		return $this->defaults;
	}

	/**
	 * Export settings as JSON
	 *
	 * @return string JSON string
	 */
	public function export_settings() {
		$settings = $this->get_settings();
		return wp_json_encode( $settings, JSON_PRETTY_PRINT );
	}

	/**
	 * Import settings from JSON
	 *
	 * @param string $json JSON string.
	 * @return bool|WP_Error True on success, WP_Error on failure
	 */
	public function import_settings( $json ) {
		$settings = json_decode( $json, true );

		if ( json_last_error() !== JSON_ERROR_NONE ) {
			return new \WP_Error( 'invalid_json', __( 'Invalid JSON format.', 'a-tables-charts' ) );
		}

		if ( ! is_array( $settings ) ) {
			return new \WP_Error( 'invalid_data', __( 'Invalid settings data.', 'a-tables-charts' ) );
		}

		return $this->update_settings( $settings );
	}
}

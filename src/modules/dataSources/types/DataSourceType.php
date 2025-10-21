<?php
/**
 * Data Source Type Enum
 *
 * Defines all available data source types.
 *
 * @package ATablesCharts\DataSources\Types
 * @since 1.0.0
 */

namespace ATablesCharts\DataSources\Types;

/**
 * DataSourceType Class
 *
 * Enum-like class for data source types.
 */
class DataSourceType {

	/**
	 * Manual table - user enters data manually
	 */
	const MANUAL = 'manual';

	/**
	 * Excel file upload (.xlsx, .xls)
	 */
	const EXCEL = 'excel';

	/**
	 * CSV file upload
	 */
	const CSV = 'csv';

	/**
	 * JSON data source
	 */
	const JSON = 'json';

	/**
	 * XML data source
	 */
	const XML = 'xml';

	/**
	 * MySQL query
	 */
	const MYSQL = 'mysql';

	/**
	 * Google Sheets (Premium)
	 */
	const GOOGLE_SHEETS = 'google_sheets';

	/**
	 * WordPress posts
	 */
	const WP_POSTS = 'wp_posts';

	/**
	 * WooCommerce products (Premium)
	 */
	const WOOCOMMERCE = 'woocommerce';

	/**
	 * REST API endpoint
	 */
	const API = 'api';

	/**
	 * Get all available data source types
	 *
	 * @return array Array of data source types
	 */
	public static function get_all() {
		return array(
			self::MANUAL,
			self::EXCEL,
			self::CSV,
			self::JSON,
			self::XML,
			self::MYSQL,
			self::GOOGLE_SHEETS,
			self::WP_POSTS,
			self::WOOCOMMERCE,
			self::API,
		);
	}

	/**
	 * Get free (lite) data source types
	 *
	 * @return array Array of free data source types
	 */
	public static function get_free() {
		return array(
			self::MANUAL,
			self::EXCEL,
			self::CSV,
			self::JSON,
			self::XML,
			self::MYSQL,
		);
	}

	/**
	 * Get premium data source types
	 *
	 * @return array Array of premium data source types
	 */
	public static function get_premium() {
		return array(
			self::GOOGLE_SHEETS,
			self::WOOCOMMERCE,
			self::API,
		);
	}

	/**
	 * Check if a data source type is valid
	 *
	 * @param string $type Data source type to validate.
	 * @return bool True if valid, false otherwise
	 */
	public static function is_valid( $type ) {
		return in_array( $type, self::get_all(), true );
	}

	/**
	 * Check if a data source type requires premium
	 *
	 * @param string $type Data source type to check.
	 * @return bool True if premium required, false otherwise
	 */
	public static function is_premium( $type ) {
		return in_array( $type, self::get_premium(), true );
	}

	/**
	 * Get display name for a data source type
	 *
	 * @param string $type Data source type.
	 * @return string Display name
	 */
	public static function get_label( $type ) {
		$labels = array(
			self::MANUAL        => __( 'Manual Table', 'a-tables-charts' ),
			self::EXCEL         => __( 'Excel Import', 'a-tables-charts' ),
			self::CSV           => __( 'CSV Import', 'a-tables-charts' ),
			self::JSON          => __( 'JSON Import', 'a-tables-charts' ),
			self::XML           => __( 'XML Import', 'a-tables-charts' ),
			self::MYSQL         => __( 'MySQL Query', 'a-tables-charts' ),
			self::GOOGLE_SHEETS => __( 'Google Sheets', 'a-tables-charts' ),
			self::WP_POSTS      => __( 'WordPress Posts', 'a-tables-charts' ),
			self::WOOCOMMERCE   => __( 'WooCommerce Products', 'a-tables-charts' ),
			self::API           => __( 'REST API', 'a-tables-charts' ),
		);

		return isset( $labels[ $type ] ) ? $labels[ $type ] : $type;
	}

	/**
	 * Get description for a data source type
	 *
	 * @param string $type Data source type.
	 * @return string Description
	 */
	public static function get_description( $type ) {
		$descriptions = array(
			self::MANUAL        => __( 'Create a table from scratch and enter data manually.', 'a-tables-charts' ),
			self::EXCEL         => __( 'Import data from .xlsx or .xls files.', 'a-tables-charts' ),
			self::CSV           => __( 'Import data from CSV files.', 'a-tables-charts' ),
			self::JSON          => __( 'Import data from JSON format.', 'a-tables-charts' ),
			self::XML           => __( 'Import data from XML files.', 'a-tables-charts' ),
			self::MYSQL         => __( 'Create table from MySQL query results.', 'a-tables-charts' ),
			self::GOOGLE_SHEETS => __( 'Connect to Google Sheets as data source.', 'a-tables-charts' ),
			self::WP_POSTS      => __( 'Display WordPress posts in a table.', 'a-tables-charts' ),
			self::WOOCOMMERCE   => __( 'Display WooCommerce products with pricing.', 'a-tables-charts' ),
			self::API           => __( 'Fetch data from a REST API endpoint.', 'a-tables-charts' ),
		);

		return isset( $descriptions[ $type ] ) ? $descriptions[ $type ] : '';
	}

	/**
	 * Get icon for a data source type
	 *
	 * @param string $type Data source type.
	 * @return string Icon (emoji or dashicon class)
	 */
	public static function get_icon( $type ) {
		$icons = array(
			self::MANUAL        => '✏️',
			self::EXCEL         => '📊',
			self::CSV           => '📄',
			self::JSON          => '{ }',
			self::XML           => '< >',
			self::MYSQL         => '🗄️',
			self::GOOGLE_SHEETS => '📗',
			self::WP_POSTS      => '📝',
			self::WOOCOMMERCE   => '🛒',
			self::API           => '🔌',
		);

		return isset( $icons[ $type ] ) ? $icons[ $type ] : '📦';
	}
}

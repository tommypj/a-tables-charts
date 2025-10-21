<?php
/**
 * Database Schema
 *
 * Defines database table structures for the Tables module.
 *
 * @package ATablesCharts\Tables\Schemas
 * @since 1.0.0
 */

namespace ATablesCharts\Tables\Schemas;

/**
 * DatabaseSchema Class
 *
 * Responsibilities:
 * - Define database table structures
 * - Create tables on plugin activation
 * - Handle schema migrations
 * - Provide schema versioning
 */
class DatabaseSchema {

	/**
	 * Current schema version
	 *
	 * @var string
	 */
	const SCHEMA_VERSION = '1.0.0';

	/**
	 * WordPress database object
	 *
	 * @var \wpdb
	 */
	private $wpdb;

	/**
	 * Table prefix
	 *
	 * @var string
	 */
	private $prefix;

	/**
	 * Constructor
	 */
	public function __construct() {
		global $wpdb;
		$this->wpdb   = $wpdb;
		$this->prefix = $wpdb->prefix . 'atables_';
	}

	/**
	 * Create all database tables
	 *
	 * @return bool True on success, false on failure
	 */
	public function create_tables() {
		$charset_collate = $this->wpdb->get_charset_collate();

		// Create tables table.
		$tables_table = $this->create_tables_table( $charset_collate );

		// Create table_data table.
		$data_table = $this->create_table_data_table( $charset_collate );

		// Create table_meta table.
		$meta_table = $this->create_table_meta_table( $charset_collate );

		// Update schema version.
		if ( $tables_table && $data_table && $meta_table ) {
			update_option( 'atables_schema_version', self::SCHEMA_VERSION );
			return true;
		}

		return false;
	}

	/**
	 * Create main tables table
	 *
	 * Stores table metadata and configuration.
	 *
	 * @param string $charset_collate Database charset collation.
	 * @return bool True on success
	 */
	private function create_tables_table( $charset_collate ) {
		$table_name = $this->prefix . 'tables';

		$sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
			id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			title varchar(255) NOT NULL,
			description text,
			data_source_type varchar(50) NOT NULL,
			data_source_config longtext,
			row_count int(11) DEFAULT 0,
			column_count int(11) DEFAULT 0,
			status varchar(20) DEFAULT 'active',
			created_by bigint(20) UNSIGNED NOT NULL,
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			KEY status (status),
			KEY created_by (created_by),
			KEY created_at (created_at)
		) {$charset_collate};";

		return $this->execute_sql( $sql );
	}

	/**
	 * Create table_data table
	 *
	 * Stores the actual table data (rows and columns).
	 *
	 * @param string $charset_collate Database charset collation.
	 * @return bool True on success
	 */
	private function create_table_data_table( $charset_collate ) {
		$table_name = $this->prefix . 'table_data';

		$sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
			id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			table_id bigint(20) UNSIGNED NOT NULL,
			row_index int(11) NOT NULL,
			row_data longtext NOT NULL,
			PRIMARY KEY (id),
			KEY table_id (table_id),
			KEY row_index (row_index)
		) {$charset_collate};";

		return $this->execute_sql( $sql );
	}

	/**
	 * Create table_meta table
	 *
	 * Stores additional metadata for tables (settings, filters, etc.).
	 *
	 * @param string $charset_collate Database charset collation.
	 * @return bool True on success
	 */
	private function create_table_meta_table( $charset_collate ) {
		$table_name = $this->prefix . 'table_meta';

		$sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
			id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			table_id bigint(20) UNSIGNED NOT NULL,
			meta_key varchar(255) NOT NULL,
			meta_value longtext,
			PRIMARY KEY (id),
			KEY table_id (table_id),
			KEY meta_key (meta_key)
		) {$charset_collate};";

		return $this->execute_sql( $sql );
	}

	/**
	 * Execute SQL query
	 *
	 * @param string $sql SQL query.
	 * @return bool True on success
	 */
	private function execute_sql( $sql ) {
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );

		// Check if table was created.
		$table_name = $this->extract_table_name( $sql );
		return $this->table_exists( $table_name );
	}

	/**
	 * Extract table name from CREATE TABLE SQL
	 *
	 * @param string $sql SQL query.
	 * @return string Table name
	 */
	private function extract_table_name( $sql ) {
		preg_match( '/CREATE TABLE IF NOT EXISTS\s+`?(\w+)`?/i', $sql, $matches );
		return isset( $matches[1] ) ? $matches[1] : '';
	}

	/**
	 * Check if table exists
	 *
	 * @param string $table_name Table name.
	 * @return bool True if exists
	 */
	private function table_exists( $table_name ) {
		$query = $this->wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name );
		return $this->wpdb->get_var( $query ) === $table_name;
	}

	/**
	 * Drop all database tables
	 *
	 * WARNING: This will delete all data!
	 *
	 * @return bool True on success
	 */
	public function drop_tables() {
		$tables = array(
			$this->prefix . 'table_meta',
			$this->prefix . 'table_data',
			$this->prefix . 'tables',
		);

		foreach ( $tables as $table ) {
			$sql = "DROP TABLE IF EXISTS {$table}";
			$this->wpdb->query( $sql );
		}

		delete_option( 'atables_schema_version' );
		return true;
	}

	/**
	 * Get table names
	 *
	 * @return array Array of table names
	 */
	public function get_table_names() {
		return array(
			'tables'     => $this->prefix . 'tables',
			'table_data' => $this->prefix . 'table_data',
			'table_meta' => $this->prefix . 'table_meta',
		);
	}

	/**
	 * Get current schema version
	 *
	 * @return string Schema version
	 */
	public function get_schema_version() {
		return get_option( 'atables_schema_version', '0.0.0' );
	}

	/**
	 * Check if schema needs update
	 *
	 * @return bool True if update needed
	 */
	public function needs_update() {
		return version_compare( $this->get_schema_version(), self::SCHEMA_VERSION, '<' );
	}
}

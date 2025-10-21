<?php
/**
 * Add Filter Presets Table Migration
 *
 * Creates the table for storing user-defined filter presets.
 *
 * @package ATablesCharts\Core\Migrations
 * @since 1.0.5
 */

namespace ATablesCharts\Core\Migrations;

/**
 * AddFilterPresetsTable Migration
 *
 * Creates wp_atables_filter_presets table for storing filter configurations.
 */
class AddFilterPresetsTable {

	/**
	 * Run the migration
	 *
	 * @return bool True on success, false on failure
	 */
	public static function up() {
		global $wpdb;

		$table_name = $wpdb->prefix . 'atables_filter_presets';
		$tables_table = $wpdb->prefix . 'atables_tables';
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
			id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
			table_id BIGINT UNSIGNED NOT NULL,
			name VARCHAR(255) NOT NULL,
			description TEXT,
			filters JSON NOT NULL,
			is_default TINYINT(1) DEFAULT 0,
			created_by BIGINT UNSIGNED NOT NULL,
			created_at DATETIME NOT NULL,
			updated_at DATETIME NOT NULL,
			INDEX idx_table_id (table_id),
			INDEX idx_created_by (created_by),
			INDEX idx_is_default (is_default),
			CONSTRAINT fk_filter_preset_table 
				FOREIGN KEY (table_id) 
				REFERENCES {$tables_table}(id) 
				ON DELETE CASCADE
		) {$charset_collate};";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		
		$result = dbDelta( $sql );

		// Log migration result
		if ( ! empty( $result ) ) {
			error_log( 'Filter presets table migration completed: ' . print_r( $result, true ) );
			return true;
		}

		return false;
	}

	/**
	 * Reverse the migration
	 *
	 * @return bool True on success, false on failure
	 */
	public static function down() {
		global $wpdb;

		$table_name = $wpdb->prefix . 'atables_filter_presets';

		// Drop the table
		$result = $wpdb->query( "DROP TABLE IF EXISTS {$table_name}" );

		error_log( 'Filter presets table dropped: ' . ( $result !== false ? 'success' : 'failed' ) );

		return $result !== false;
	}

	/**
	 * Check if migration has been run
	 *
	 * @return bool True if table exists, false otherwise
	 */
	public static function is_migrated() {
		global $wpdb;

		$table_name = $wpdb->prefix . 'atables_filter_presets';

		$query = $wpdb->prepare(
			'SHOW TABLES LIKE %s',
			$wpdb->esc_like( $table_name )
		);

		return $wpdb->get_var( $query ) === $table_name;
	}
}

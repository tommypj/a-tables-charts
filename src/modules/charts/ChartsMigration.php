<?php
/**
 * Charts Database Migration
 *
 * Creates the charts table in the database.
 *
 * @package ATablesCharts\Charts
 * @since 1.0.0
 */

namespace ATablesCharts\Charts;

/**
 * ChartsMigration Class
 *
 * Handles database schema for charts.
 */
class ChartsMigration {

	/**
	 * Run the migration
	 */
	public static function up() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();
		$table_name      = $wpdb->prefix . 'atables_charts';

		$sql = "CREATE TABLE IF NOT EXISTS $table_name (
			id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			table_id bigint(20) UNSIGNED NOT NULL,
			title varchar(255) NOT NULL,
			type varchar(50) NOT NULL DEFAULT 'bar',
			config longtext NOT NULL,
			status varchar(20) NOT NULL DEFAULT 'active',
			created_by bigint(20) UNSIGNED NOT NULL,
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			KEY table_id (table_id),
			KEY status (status),
			KEY created_by (created_by)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	/**
	 * Rollback the migration
	 */
	public static function down() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'atables_charts';
		$wpdb->query( "DROP TABLE IF EXISTS $table_name" );
	}
}

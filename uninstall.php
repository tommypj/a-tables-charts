<?php
/**
 * Uninstall Script
 *
 * Fired when the plugin is uninstalled.
 * Cleans up all plugin data from the database.
 *
 * @package ATablesCharts
 * @since 1.0.0
 */

// If uninstall not called from WordPress, exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/**
 * Remove plugin data
 *
 * This function removes all plugin data including:
 * - Database tables
 * - Plugin options
 * - Upload directories
 * - Transients
 */
function atables_uninstall() {
	global $wpdb;

	// Delete database tables.
	$table_prefix = $wpdb->prefix . 'atables_';
	$tables       = array(
		$table_prefix . 'tables',
		$table_prefix . 'charts',
		$table_prefix . 'cache',
		$table_prefix . 'rows',
	);

	foreach ( $tables as $table ) {
		$wpdb->query( "DROP TABLE IF EXISTS {$table}" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}

	// Delete plugin options.
	delete_option( 'atables_settings' );
	delete_option( 'ATABLES_VERSION' );
	delete_option( 'atables_db_version' );
	delete_option( 'atables_activated_at' );
	delete_option( 'atables_deactivated_at' );

	// Delete all transients.
	$wpdb->query(
		$wpdb->prepare(
			"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
			$wpdb->esc_like( '_transient_A-Tables & Charts_' ) . '%'
		)
	);

	$wpdb->query(
		$wpdb->prepare(
			"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
			$wpdb->esc_like( '_transient_timeout_A-Tables & Charts_' ) . '%'
		)
	);

	// Delete upload directory (optional - commented out by default).
	// Uncomment if you want to delete uploaded files on uninstall.
	/*
	$upload_dir  = wp_upload_dir();
	$tables_dir = $upload_dir['basedir'] . '/atables';
	
	if ( file_exists( $tables_dir ) ) {
		atables_delete_directory( $tables_dir );
	}
	*/

	// Clear any cached data.
	wp_cache_flush();
}

/**
 * Recursively delete a directory
 *
 * @param string $dir Directory path to delete.
 * @return bool True on success, false on failure
 */
function atables_delete_directory( $dir ) {
	if ( ! file_exists( $dir ) ) {
		return true;
	}

	if ( ! is_dir( $dir ) ) {
		return unlink( $dir );
	}

	foreach ( scandir( $dir ) as $item ) {
		if ( '.' === $item || '..' === $item ) {
			continue;
		}

		if ( ! atables_delete_directory( $dir . DIRECTORY_SEPARATOR . $item ) ) {
			return false;
		}
	}

	return rmdir( $dir );
}

// Run the uninstall.
atables_uninstall();

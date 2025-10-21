<?php
/**
 * Plugin Deactivator
 *
 * Handles plugin deactivation tasks.
 * Fired during plugin deactivation.
 *
 * @package ATablesCharts\Core
 * @since 1.0.0
 */

namespace ATablesCharts\Core;

/**
 * Deactivator Class
 *
 * Responsibilities:
 * - Clean up temporary data
 * - Flush rewrite rules
 * - Log deactivation
 * - Preserve user data (DO NOT delete tables on deactivation)
 */
class Deactivator {

	/**
	 * Deactivate the plugin
	 *
	 * This method is called when the plugin is deactivated.
	 * It performs cleanup tasks but DOES NOT delete user data.
	 * Data deletion only happens on uninstall (via uninstall.php).
	 *
	 * @since 1.0.0
	 */
	public static function deactivate() {
		// Clean up temporary files.
		self::cleanup_temp_files();

		// Clear scheduled events.
		self::clear_scheduled_events();

		// Flush rewrite rules.
		flush_rewrite_rules();

		// Set deactivation timestamp.
		update_option( 'atables_deactivated_at', current_time( 'timestamp' ) );

		// Log deactivation.
		error_log( 'A-Tables and Charts plugin deactivated' );
	}

	/**
	 * Clean up temporary files
	 *
	 * Removes temporary files from the uploads directory.
	 *
	 * @since 1.0.0
	 */
	private static function cleanup_temp_files() {
		$upload_dir = wp_upload_dir();
		$temp_dir   = $upload_dir['basedir'] . '/atables/temp';

		// Check if temp directory exists.
		if ( ! file_exists( $temp_dir ) ) {
			return;
		}

		// Get all files in temp directory.
		$files = glob( $temp_dir . '/*' );

		if ( ! empty( $files ) ) {
			foreach ( $files as $file ) {
				// Only delete files, not directories.
				if ( is_file( $file ) ) {
					wp_delete_file( $file );
				}
			}
		}
	}

	/**
	 * Clear scheduled events
	 *
	 * Removes any cron jobs scheduled by the plugin.
	 *
	 * @since 1.0.0
	 */
	private static function clear_scheduled_events() {
		// Clear cache cleanup event if scheduled.
		$timestamp = wp_next_scheduled( 'A-Tables & Charts_cache_cleanup' );
		if ( $timestamp ) {
			wp_unschedule_event( $timestamp, 'A-Tables & Charts_cache_cleanup' );
		}

		// Clear any other scheduled events.
		$events = array(
			'A-Tables & Charts_auto_sync',
			'A-Tables & Charts_data_refresh',
		);

		foreach ( $events as $event ) {
			$timestamp = wp_next_scheduled( $event );
			if ( $timestamp ) {
				wp_unschedule_event( $timestamp, $event );
			}
		}
	}

	/**
	 * Clear transients
	 *
	 * Removes all plugin-specific transients.
	 *
	 * @since 1.0.0
	 */
	private static function clear_transients() {
		global $wpdb;

		// Delete all transients that start with 'A-Tables & Charts_'.
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
				$wpdb->esc_like( '_transient_A-Tables & Charts_' ) . '%'
			)
		);

		// Delete transient timeout options.
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
				$wpdb->esc_like( '_transient_timeout_A-Tables & Charts_' ) . '%'
			)
		);
	}

	/**
	 * Get deactivation timestamp
	 *
	 * @since 1.0.0
	 * @return int|false Timestamp or false if not set
	 */
	public static function get_deactivation_time() {
		return get_option( 'atables_deactivated_at', false );
	}

	/**
	 * Check if plugin was deactivated within given hours
	 *
	 * Useful for showing "Welcome Back" messages or similar.
	 *
	 * @since 1.0.0
	 * @param int $hours Number of hours to check.
	 * @return bool True if deactivated within hours, false otherwise
	 */
	public static function was_deactivated_within( $hours = 24 ) {
		$deactivation_time = self::get_deactivation_time();
		
		if ( false === $deactivation_time ) {
			return false;
		}

		$time_diff = current_time( 'timestamp' ) - $deactivation_time;
		return $time_diff < ( $hours * HOUR_IN_SECONDS );
	}
}

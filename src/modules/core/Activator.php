<?php
/**
 * Plugin Activator
 *
 * Handles plugin activation tasks.
 * Fired during plugin activation.
 *
 * @package ATablesCharts\Core
 * @since 1.0.0
 */

namespace ATablesCharts\Core;

/**
 * Activator Class
 *
 * Responsibilities:
 * - Create database tables
 * - Set default options
 * - Check system requirements
 * - Create necessary directories
 * - Set up initial configuration
 */
class Activator {

	/**
	 * Activate the plugin
	 *
	 * This method is called when the plugin is activated.
	 * It sets up the database tables, default options, and
	 * performs any other necessary setup tasks.
	 *
	 * @since 1.0.0
	 */
	public static function activate() {
		// Check requirements first.
		self::check_requirements();

		// Create database tables.
		self::create_tables();

		// Set default options.
		self::set_default_options();

		// Create upload directory.
		self::create_upload_directory();

		// Set activation timestamp.
		update_option( 'atables_activated_at', current_time( 'timestamp' ) );
		update_option( 'ATABLES_VERSION', ATABLES_VERSION );

		// Flush rewrite rules.
		flush_rewrite_rules();

		// Log activation.
		error_log( 'A-Tables and Charts plugin activated successfully' );
	}

	/**
	 * Check system requirements
	 *
	 * Verifies that the server meets minimum requirements.
	 *
	 * @since 1.0.0
	 * @throws \Exception If requirements are not met.
	 */
	private static function check_requirements() {
		global $wp_version;

		$errors = array();

		// Check PHP version.
		if ( version_compare( PHP_VERSION, ATABLES_MIN_PHP_VERSION, '<' ) ) {
			$errors[] = sprintf(
				'PHP version %s or higher is required. You are running version %s.',
				ATABLES_MIN_PHP_VERSION,
				PHP_VERSION
			);
		}

		// Check WordPress version.
		if ( version_compare( $wp_version, ATABLES_MIN_WP_VERSION, '<' ) ) {
			$errors[] = sprintf(
				'WordPress version %s or higher is required. You are running version %s.',
				ATABLES_MIN_WP_VERSION,
				$wp_version
			);
		}

		// Check if required PHP extensions are loaded.
		$required_extensions = array( 'mbstring', 'json' );
		foreach ( $required_extensions as $extension ) {
			if ( ! extension_loaded( $extension ) ) {
				$errors[] = sprintf(
					'Required PHP extension "%s" is not installed.',
					$extension
				);
			}
		}

		// If there are errors, deactivate and show error.
		if ( ! empty( $errors ) ) {
			deactivate_plugins( ATABLES_PLUGIN_BASENAME );
			wp_die(
				'<h1>Plugin Activation Failed</h1>' .
				'<p>' . implode( '</p><p>', $errors ) . '</p>' .
				'<p><a href="' . admin_url( 'plugins.php' ) . '">&larr; Return to Plugins</a></p>'
			);
		}
	}

	/**
	 * Create database tables
	 *
	 * Creates all necessary database tables for the plugin.
	 *
	 * @since 1.0.0
	 */
	private static function create_tables() {
		// Load DatabaseSchema class.
		require_once ATABLES_PLUGIN_DIR . 'src/modules/tables/schemas/DatabaseSchema.php';
		
		$schema = new \ATablesCharts\Tables\Schemas\DatabaseSchema();
		$schema->create_tables();
		
		// Create charts table.
		require_once ATABLES_PLUGIN_DIR . 'src/modules/charts/ChartsMigration.php';
		\ATablesCharts\Charts\ChartsMigration::up();
		
		// Store database version.
		update_option( 'atables_db_version', ATABLES_VERSION );
	}

	/**
	 * Set default plugin options
	 *
	 * Creates default configuration options.
	 *
	 * @since 1.0.0
	 */
	private static function set_default_options() {
		$default_options = array(
			'cache_enabled'         => true,
			'cache_expiration'      => 3600, // 1 hour
			'rows_per_page'         => 10,
			'enable_responsive'     => true,
			'enable_search'         => true,
			'enable_sorting'        => true,
			'enable_pagination'     => true,
			'date_format'           => 'Y-m-d',
			'time_format'           => 'H:i:s',
			'decimal_separator'     => '.',
			'thousands_separator'   => ',',
			'enable_export'         => true,
			'export_formats'        => array( 'csv', 'excel', 'pdf' ),
			'google_charts_enabled' => true,
			'chartjs_enabled'       => true,
		);

		// Only set if not already set.
		if ( false === get_option( 'atables_settings' ) ) {
			update_option( 'atables_settings', $default_options );
		}
	}

	/**
	 * Create upload directory
	 *
	 * Creates a directory for storing uploaded files.
	 *
	 * @since 1.0.0
	 */
	private static function create_upload_directory() {
		$upload_dir = wp_upload_dir();
		$tables_dir = $upload_dir['basedir'] . '/atables';

		// Create directory if it doesn't exist.
		if ( ! file_exists( $tables_dir ) ) {
			wp_mkdir_p( $tables_dir );

			// Create .htaccess to protect uploaded files.
			$htaccess_content = 'Options -Indexes' . PHP_EOL;
			$htaccess_content .= 'deny from all' . PHP_EOL;
			file_put_contents( $tables_dir . '/.htaccess', $htaccess_content );

			// Create index.php to prevent directory listing.
			file_put_contents( $tables_dir . '/index.php', '<?php // Silence is golden' );
		}

		// Create subdirectories.
		$subdirs = array( 'temp', 'imports', 'exports' );
		foreach ( $subdirs as $subdir ) {
			$subdir_path = $tables_dir . '/' . $subdir;
			if ( ! file_exists( $subdir_path ) ) {
				wp_mkdir_p( $subdir_path );
				file_put_contents( $subdir_path . '/index.php', '<?php // Silence is golden' );
			}
		}
	}

	/**
	 * Get activation timestamp
	 *
	 * @since 1.0.0
	 * @return int|false Timestamp or false if not set
	 */
	public static function get_activation_time() {
		return get_option( 'atables_activated_at', false );
	}

	/**
	 * Check if this is a fresh installation
	 *
	 * @since 1.0.0
	 * @return bool True if fresh install, false otherwise
	 */
	public static function is_fresh_install() {
		return false === get_option( 'ATABLES_VERSION' );
	}
}

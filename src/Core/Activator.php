<?php
/**
 * Plugin Activator
 *
 * @package ATables\Core
 * @since 2.0.0
 */

namespace ATables\Core;

/**
 * Activator Class
 *
 * Handles plugin activation and deactivation.
 */
class Activator {

    /**
     * Activate plugin
     */
    public static function activate() {
        // Create database tables
        Database::create_tables();

        // Set default options
        self::set_default_options();

        // Check for old version and migrate
        if ( self::needs_migration() ) {
            self::run_migration();
        }

        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Deactivate plugin
     */
    public static function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();

        // Note: We don't delete data on deactivation
        // Data is only deleted on uninstall
    }

    /**
     * Set default options
     */
    private static function set_default_options() {
        // Plugin version
        update_option( 'atables_version', ATABLES_VERSION );

        // Default settings
        $defaults = array(
            'default_theme'       => 'default',
            'enable_pagination'   => true,
            'default_rows_per_page' => 10,
            'enable_search'       => true,
            'enable_sorting'      => true,
        );

        foreach ( $defaults as $key => $value ) {
            if ( false === get_option( "atables_{$key}" ) ) {
                add_option( "atables_{$key}", $value );
            }
        }
    }

    /**
     * Check if migration is needed
     *
     * @return bool
     */
    private static function needs_migration() {
        global $wpdb;

        // Check if old tables exist
        $old_table = $wpdb->prefix . 'atables_tables';
        $old_exists = $wpdb->get_var( "SHOW TABLES LIKE '{$old_table}'" ) === $old_table;

        // Check if old table has old structure (display_settings JSON column)
        if ( $old_exists ) {
            $columns = $wpdb->get_results( "SHOW COLUMNS FROM {$old_table}" );
            foreach ( $columns as $column ) {
                if ( $column->Field === 'display_settings' ) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Run migration from old version
     */
    private static function run_migration() {
        // Run migration in background to avoid timeout
        update_option( 'atables_needs_migration', true );

        // Set admin notice
        set_transient( 'atables_migration_notice', true, 30 );
    }

    /**
     * Check if this is first install
     *
     * @return bool
     */
    public static function is_first_install() {
        return false === get_option( 'atables_version' );
    }
}

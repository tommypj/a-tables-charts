<?php
/**
 * Plugin Activator
 *
 * @package ATables\Core
 * @since 3.0.0
 */

namespace ATables\Core;

/**
 * Activator Class
 *
 * Fired during plugin activation
 */
class Activator {

    /**
     * Activate the plugin
     */
    public static function activate() {
        // Create database tables
        Database::create_tables();

        // Flush rewrite rules
        flush_rewrite_rules();
    }
}

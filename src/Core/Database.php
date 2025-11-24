<?php
/**
 * Database Schema Manager
 *
 * @package ATables\Core
 * @since 2.0.0
 */

namespace ATables\Core;

/**
 * Database Class
 *
 * Manages database schema creation and updates.
 */
class Database {

    /**
     * Create all database tables
     */
    public static function create_tables() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        // Core tables (always created)
        self::create_tables_table( $charset_collate );
        self::create_columns_table( $charset_collate );
        self::create_rows_table( $charset_collate );
        self::create_display_settings_table( $charset_collate );

        // Pro tables (always created, but features require license)
        self::create_validation_rules_table( $charset_collate );
        self::create_conditional_formatting_table( $charset_collate );
        self::create_charts_table( $charset_collate );
        self::create_licenses_table( $charset_collate );

        // Update database version
        update_option( 'atables_db_version', ATABLES_VERSION );
    }

    /**
     * Create tables table
     */
    private static function create_tables_table( $charset_collate ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'atables_tables';

        $sql = "CREATE TABLE {$table_name} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            source_type VARCHAR(50) NOT NULL DEFAULT 'upload',
            row_count INT(11) DEFAULT 0,
            column_count INT(11) DEFAULT 0,
            status VARCHAR(20) DEFAULT 'active',
            created_by BIGINT(20) UNSIGNED NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY idx_status (status),
            KEY idx_created_by (created_by),
            KEY idx_created_at (created_at)
        ) {$charset_collate};";

        dbDelta( $sql );
    }

    /**
     * Create columns table
     */
    private static function create_columns_table( $charset_collate ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'atables_columns';

        $sql = "CREATE TABLE {$table_name} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            table_id BIGINT(20) UNSIGNED NOT NULL,
            column_name VARCHAR(255) NOT NULL,
            column_type VARCHAR(50) DEFAULT 'text',
            column_order INT(11) NOT NULL,
            is_visible TINYINT(1) DEFAULT 1,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY idx_table_id (table_id),
            KEY idx_column_order (column_order)
        ) {$charset_collate};";

        dbDelta( $sql );
    }

    /**
     * Create rows table
     */
    private static function create_rows_table( $charset_collate ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'atables_rows';

        $sql = "CREATE TABLE {$table_name} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            table_id BIGINT(20) UNSIGNED NOT NULL,
            row_order INT(11) NOT NULL,
            row_data LONGTEXT NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY idx_table_id (table_id),
            KEY idx_row_order (row_order)
        ) {$charset_collate};";

        dbDelta( $sql );
    }

    /**
     * Create display settings table
     */
    private static function create_display_settings_table( $charset_collate ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'atables_display_settings';

        $sql = "CREATE TABLE {$table_name} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            table_id BIGINT(20) UNSIGNED NOT NULL,
            theme VARCHAR(50) DEFAULT 'default',
            enable_search TINYINT(1) DEFAULT 1,
            enable_sorting TINYINT(1) DEFAULT 1,
            enable_pagination TINYINT(1) DEFAULT 1,
            rows_per_page INT(11) DEFAULT 10,
            custom_css LONGTEXT,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY unique_table (table_id)
        ) {$charset_collate};";

        dbDelta( $sql );
    }

    /**
     * Create validation rules table (PRO)
     */
    private static function create_validation_rules_table( $charset_collate ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'atables_validation_rules';

        $sql = "CREATE TABLE {$table_name} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            table_id BIGINT(20) UNSIGNED NOT NULL,
            column_name VARCHAR(255) NOT NULL,
            rule_type VARCHAR(50) NOT NULL,
            rule_config LONGTEXT,
            error_message VARCHAR(500),
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY idx_table_id (table_id),
            KEY idx_column_name (column_name)
        ) {$charset_collate};";

        dbDelta( $sql );
    }

    /**
     * Create conditional formatting table (PRO)
     */
    private static function create_conditional_formatting_table( $charset_collate ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'atables_conditional_formatting';

        $sql = "CREATE TABLE {$table_name} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            table_id BIGINT(20) UNSIGNED NOT NULL,
            rule_name VARCHAR(255),
            column_name VARCHAR(255) NOT NULL,
            condition_type VARCHAR(50) NOT NULL,
            condition_value VARCHAR(255),
            style_config LONGTEXT,
            priority INT(11) DEFAULT 0,
            is_active TINYINT(1) DEFAULT 1,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY idx_table_id (table_id),
            KEY idx_column_name (column_name),
            KEY idx_is_active (is_active)
        ) {$charset_collate};";

        dbDelta( $sql );
    }

    /**
     * Create charts table (PRO)
     */
    private static function create_charts_table( $charset_collate ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'atables_charts';

        $sql = "CREATE TABLE {$table_name} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            table_id BIGINT(20) UNSIGNED NOT NULL,
            title VARCHAR(255) NOT NULL,
            chart_type VARCHAR(50) NOT NULL,
            chart_config LONGTEXT NOT NULL,
            status VARCHAR(20) DEFAULT 'active',
            created_by BIGINT(20) UNSIGNED NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY idx_table_id (table_id),
            KEY idx_status (status)
        ) {$charset_collate};";

        dbDelta( $sql );
    }

    /**
     * Create licenses table
     */
    private static function create_licenses_table( $charset_collate ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'atables_licenses';

        $sql = "CREATE TABLE {$table_name} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            license_key VARCHAR(255) NOT NULL,
            purchase_code VARCHAR(255),
            license_type VARCHAR(50) NOT NULL,
            status VARCHAR(20) DEFAULT 'active',
            activated_at DATETIME,
            expires_at DATETIME,
            last_checked DATETIME,
            site_url VARCHAR(255),
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY unique_license (license_key),
            KEY idx_status (status)
        ) {$charset_collate};";

        dbDelta( $sql );
    }

    /**
     * Drop all tables (for uninstall)
     */
    public static function drop_tables() {
        global $wpdb;

        $tables = array(
            $wpdb->prefix . 'atables_licenses',
            $wpdb->prefix . 'atables_charts',
            $wpdb->prefix . 'atables_conditional_formatting',
            $wpdb->prefix . 'atables_validation_rules',
            $wpdb->prefix . 'atables_display_settings',
            $wpdb->prefix . 'atables_rows',
            $wpdb->prefix . 'atables_columns',
            $wpdb->prefix . 'atables_tables',
        );

        foreach ( $tables as $table ) {
            $wpdb->query( "DROP TABLE IF EXISTS {$table}" );
        }

        // Delete options
        delete_option( 'atables_version' );
        delete_option( 'atables_db_version' );
    }
}

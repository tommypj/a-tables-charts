<?php
/**
 * Database Schema
 *
 * @package ATables\Core
 * @since 3.0.0
 */

namespace ATables\Core;

/**
 * Database Class
 *
 * Creates and manages database tables
 */
class Database {

    /**
     * Create all database tables
     */
    public static function create_tables() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        // Tables table
        $tables_sql = "CREATE TABLE {$wpdb->prefix}atables_tables (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            title varchar(255) NOT NULL,
            description text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";

        // Columns table
        $columns_sql = "CREATE TABLE {$wpdb->prefix}atables_columns (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            table_id bigint(20) UNSIGNED NOT NULL,
            column_name varchar(255) NOT NULL,
            column_order int NOT NULL DEFAULT 0,
            PRIMARY KEY (id),
            KEY table_id (table_id)
        ) $charset_collate;";

        // Rows table
        $rows_sql = "CREATE TABLE {$wpdb->prefix}atables_rows (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            table_id bigint(20) UNSIGNED NOT NULL,
            row_data longtext NOT NULL,
            row_order int NOT NULL DEFAULT 0,
            PRIMARY KEY (id),
            KEY table_id (table_id)
        ) $charset_collate;";

        // Features table (for enable/disable toggles)
        $features_sql = "CREATE TABLE {$wpdb->prefix}atables_features (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            feature_key varchar(100) NOT NULL,
            enabled tinyint(1) NOT NULL DEFAULT 1,
            settings longtext,
            PRIMARY KEY (id),
            UNIQUE KEY feature_key (feature_key)
        ) $charset_collate;";

        dbDelta( $tables_sql );
        dbDelta( $columns_sql );
        dbDelta( $rows_sql );
        dbDelta( $features_sql );

        // Insert default features (all disabled initially)
        self::initialize_features();

        update_option( 'atables_db_version', ATABLES_VERSION );
    }

    /**
     * Initialize feature toggles
     */
    private static function initialize_features() {
        global $wpdb;

        $features = array(
            'themes' => array(
                'title' => 'Table Themes',
                'tier' => 'free',
            ),
            'search' => array(
                'title' => 'Search',
                'tier' => 'free',
            ),
            'sorting' => array(
                'title' => 'Column Sorting',
                'tier' => 'free',
            ),
            'pagination' => array(
                'title' => 'Pagination',
                'tier' => 'free',
            ),
        );

        foreach ( $features as $key => $data ) {
            $exists = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT COUNT(*) FROM {$wpdb->prefix}atables_features WHERE feature_key = %s",
                    $key
                )
            );

            if ( ! $exists ) {
                $wpdb->insert(
                    $wpdb->prefix . 'atables_features',
                    array(
                        'feature_key' => $key,
                        'enabled' => 0, // All disabled by default
                        'settings' => wp_json_encode( $data ),
                    ),
                    array( '%s', '%d', '%s' )
                );
            }
        }
    }
}

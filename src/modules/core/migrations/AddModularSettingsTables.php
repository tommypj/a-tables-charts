<?php
/**
 * Database Migration: Add Modular Settings Tables
 *
 * Creates separate tables for validation rules, conditional formatting,
 * formulas, and cell merges for better performance and maintainability.
 *
 * @package ATablesCharts\Core\Migrations
 * @since 1.1.0
 */

namespace ATablesCharts\Core\Migrations;

class AddModularSettingsTables {

    /**
     * Run the migration
     *
     * @return array Result with success status and message
     */
    public static function up() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();
        $results = array();

        // Create validation_rules table
        $results[] = self::create_validation_rules_table( $charset_collate );

        // Create conditional_formatting table
        $results[] = self::create_conditional_formatting_table( $charset_collate );

        // Create formulas table
        $results[] = self::create_formulas_table( $charset_collate );

        // Create cell_merges table
        $results[] = self::create_cell_merges_table( $charset_collate );

        // Create display_settings table
        $results[] = self::create_display_settings_table( $charset_collate );

        // Check for any failures
        $failures = array_filter( $results, function( $result ) {
            return ! $result['success'];
        });

        if ( ! empty( $failures ) ) {
            $messages = array_map( function( $result ) {
                return $result['message'];
            }, $failures );

            return array(
                'success' => false,
                'message' => 'Failed to create tables: ' . implode( ', ', $messages )
            );
        }

        return array(
            'success' => true,
            'message' => 'Modular settings tables created successfully'
        );
    }

    /**
     * Create validation_rules table
     */
    private static function create_validation_rules_table( $charset_collate ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'atables_validation_rules';

        // Check if table already exists
        if ( self::table_exists( $table_name ) ) {
            return array(
                'success' => true,
                'message' => 'validation_rules table already exists'
            );
        }

        $sql = "CREATE TABLE {$table_name} (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            table_id bigint(20) UNSIGNED NOT NULL,
            column_name varchar(255) NOT NULL,
            rule_type varchar(50) NOT NULL,
            rule_config longtext,
            error_message varchar(500),
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY table_id (table_id),
            KEY column_name (column_name)
        ) {$charset_collate};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );

        if ( ! self::table_exists( $table_name ) ) {
            return array(
                'success' => false,
                'message' => 'Failed to create validation_rules table'
            );
        }

        return array(
            'success' => true,
            'message' => 'validation_rules table created'
        );
    }

    /**
     * Create conditional_formatting table
     */
    private static function create_conditional_formatting_table( $charset_collate ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'atables_conditional_formatting';

        if ( self::table_exists( $table_name ) ) {
            return array(
                'success' => true,
                'message' => 'conditional_formatting table already exists'
            );
        }

        $sql = "CREATE TABLE {$table_name} (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            table_id bigint(20) UNSIGNED NOT NULL,
            rule_name varchar(255),
            column_name varchar(255) NOT NULL,
            condition_type varchar(50) NOT NULL,
            condition_value varchar(255),
            style_config longtext,
            priority int(11) DEFAULT 0,
            is_active tinyint(1) DEFAULT 1,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY table_id (table_id),
            KEY column_name (column_name),
            KEY is_active (is_active)
        ) {$charset_collate};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );

        if ( ! self::table_exists( $table_name ) ) {
            return array(
                'success' => false,
                'message' => 'Failed to create conditional_formatting table'
            );
        }

        return array(
            'success' => true,
            'message' => 'conditional_formatting table created'
        );
    }

    /**
     * Create formulas table
     */
    private static function create_formulas_table( $charset_collate ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'atables_formulas';

        if ( self::table_exists( $table_name ) ) {
            return array(
                'success' => true,
                'message' => 'formulas table already exists'
            );
        }

        $sql = "CREATE TABLE {$table_name} (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            table_id bigint(20) UNSIGNED NOT NULL,
            target_column varchar(255) NOT NULL,
            formula_expression longtext NOT NULL,
            formula_type varchar(50) DEFAULT 'custom',
            is_active tinyint(1) DEFAULT 1,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY table_id (table_id),
            KEY target_column (target_column),
            KEY is_active (is_active)
        ) {$charset_collate};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );

        if ( ! self::table_exists( $table_name ) ) {
            return array(
                'success' => false,
                'message' => 'Failed to create formulas table'
            );
        }

        return array(
            'success' => true,
            'message' => 'formulas table created'
        );
    }

    /**
     * Create cell_merges table
     */
    private static function create_cell_merges_table( $charset_collate ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'atables_cell_merges';

        if ( self::table_exists( $table_name ) ) {
            return array(
                'success' => true,
                'message' => 'cell_merges table already exists'
            );
        }

        $sql = "CREATE TABLE {$table_name} (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            table_id bigint(20) UNSIGNED NOT NULL,
            start_row int(11) NOT NULL,
            start_col int(11) NOT NULL,
            end_row int(11) NOT NULL,
            end_col int(11) NOT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY table_id (table_id)
        ) {$charset_collate};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );

        if ( ! self::table_exists( $table_name ) ) {
            return array(
                'success' => false,
                'message' => 'Failed to create cell_merges table'
            );
        }

        return array(
            'success' => true,
            'message' => 'cell_merges table created'
        );
    }

    /**
     * Create display_settings table
     */
    private static function create_display_settings_table( $charset_collate ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'atables_display_settings';

        if ( self::table_exists( $table_name ) ) {
            return array(
                'success' => true,
                'message' => 'display_settings table already exists'
            );
        }

        $sql = "CREATE TABLE {$table_name} (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            table_id bigint(20) UNSIGNED NOT NULL,
            theme varchar(50) DEFAULT 'default',
            responsive_mode varchar(50) DEFAULT 'scroll',
            enable_search tinyint(1) DEFAULT 1,
            enable_sorting tinyint(1) DEFAULT 1,
            enable_pagination tinyint(1) DEFAULT 1,
            rows_per_page int(11) DEFAULT 10,
            custom_css longtext,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY table_id (table_id)
        ) {$charset_collate};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );

        if ( ! self::table_exists( $table_name ) ) {
            return array(
                'success' => false,
                'message' => 'Failed to create display_settings table'
            );
        }

        return array(
            'success' => true,
            'message' => 'display_settings table created'
        );
    }

    /**
     * Check if table exists
     */
    private static function table_exists( $table_name ) {
        global $wpdb;

        $result = $wpdb->get_var(
            $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name )
        );

        return $result === $table_name;
    }

    /**
     * Rollback the migration
     *
     * @return array Result with success status and message
     */
    public static function down() {
        global $wpdb;

        $tables = array(
            $wpdb->prefix . 'atables_validation_rules',
            $wpdb->prefix . 'atables_conditional_formatting',
            $wpdb->prefix . 'atables_formulas',
            $wpdb->prefix . 'atables_cell_merges',
            $wpdb->prefix . 'atables_display_settings',
        );

        foreach ( $tables as $table ) {
            $wpdb->query( "DROP TABLE IF EXISTS {$table}" );
        }

        return array(
            'success' => true,
            'message' => 'Modular settings tables removed successfully'
        );
    }

    /**
     * Check if migration has been run
     *
     * @return bool
     */
    public static function has_run() {
        global $wpdb;

        // Check if all tables exist
        $tables = array(
            $wpdb->prefix . 'atables_validation_rules',
            $wpdb->prefix . 'atables_conditional_formatting',
            $wpdb->prefix . 'atables_formulas',
            $wpdb->prefix . 'atables_cell_merges',
            $wpdb->prefix . 'atables_display_settings',
        );

        foreach ( $tables as $table ) {
            if ( ! self::table_exists( $table ) ) {
                return false;
            }
        }

        return true;
    }

    /**
     * Migrate existing data from display_settings column to new tables
     *
     * @return array Result with success status and message
     */
    public static function migrate_data() {
        global $wpdb;

        $tables_table = $wpdb->prefix . 'atables_tables';

        // Get all tables with display_settings
        $tables = $wpdb->get_results(
            "SELECT id, display_settings FROM {$tables_table} WHERE display_settings IS NOT NULL AND display_settings != ''",
            ARRAY_A
        );

        if ( empty( $tables ) ) {
            return array(
                'success' => true,
                'message' => 'No data to migrate'
            );
        }

        $migrated = 0;

        foreach ( $tables as $table ) {
            $table_id = $table['id'];
            $settings = json_decode( $table['display_settings'], true );

            if ( ! is_array( $settings ) ) {
                continue;
            }

            // Migrate validation rules
            if ( ! empty( $settings['validation_rules'] ) ) {
                self::migrate_validation_rules( $table_id, $settings['validation_rules'] );
            }

            // Migrate conditional formatting
            if ( ! empty( $settings['conditional_formatting'] ) ) {
                self::migrate_conditional_formatting( $table_id, $settings['conditional_formatting'] );
            }

            // Migrate formulas
            if ( ! empty( $settings['formulas'] ) ) {
                self::migrate_formulas( $table_id, $settings['formulas'] );
            }

            // Migrate cell merges
            if ( ! empty( $settings['cell_merges'] ) ) {
                self::migrate_cell_merges( $table_id, $settings['cell_merges'] );
            }

            // Migrate display settings
            self::migrate_display_settings( $table_id, $settings );

            $migrated++;
        }

        return array(
            'success' => true,
            'message' => "Migrated data for {$migrated} tables"
        );
    }

    /**
     * Migrate validation rules for a table
     */
    private static function migrate_validation_rules( $table_id, $rules ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'atables_validation_rules';

        foreach ( $rules as $column => $column_rules ) {
            if ( ! is_array( $column_rules ) ) {
                continue;
            }

            foreach ( $column_rules as $rule ) {
                $wpdb->insert(
                    $table_name,
                    array(
                        'table_id' => $table_id,
                        'column_name' => $column,
                        'rule_type' => isset( $rule['type'] ) ? $rule['type'] : 'custom',
                        'rule_config' => wp_json_encode( $rule ),
                        'error_message' => isset( $rule['message'] ) ? $rule['message'] : '',
                    ),
                    array( '%d', '%s', '%s', '%s', '%s' )
                );
            }
        }
    }

    /**
     * Migrate conditional formatting for a table
     */
    private static function migrate_conditional_formatting( $table_id, $rules ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'atables_conditional_formatting';

        foreach ( $rules as $index => $rule ) {
            $wpdb->insert(
                $table_name,
                array(
                    'table_id' => $table_id,
                    'rule_name' => isset( $rule['name'] ) ? $rule['name'] : 'Rule ' . ( $index + 1 ),
                    'column_name' => isset( $rule['column'] ) ? $rule['column'] : '',
                    'condition_type' => isset( $rule['condition'] ) ? $rule['condition'] : '',
                    'condition_value' => isset( $rule['value'] ) ? $rule['value'] : '',
                    'style_config' => wp_json_encode( isset( $rule['style'] ) ? $rule['style'] : array() ),
                    'priority' => $index,
                    'is_active' => 1,
                ),
                array( '%d', '%s', '%s', '%s', '%s', '%s', '%d', '%d' )
            );
        }
    }

    /**
     * Migrate formulas for a table
     */
    private static function migrate_formulas( $table_id, $formulas ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'atables_formulas';

        foreach ( $formulas as $formula ) {
            $wpdb->insert(
                $table_name,
                array(
                    'table_id' => $table_id,
                    'target_column' => isset( $formula['column'] ) ? $formula['column'] : '',
                    'formula_expression' => isset( $formula['formula'] ) ? $formula['formula'] : '',
                    'formula_type' => isset( $formula['type'] ) ? $formula['type'] : 'custom',
                    'is_active' => 1,
                ),
                array( '%d', '%s', '%s', '%s', '%d' )
            );
        }
    }

    /**
     * Migrate cell merges for a table
     */
    private static function migrate_cell_merges( $table_id, $merges ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'atables_cell_merges';

        foreach ( $merges as $merge ) {
            $wpdb->insert(
                $table_name,
                array(
                    'table_id' => $table_id,
                    'start_row' => isset( $merge['startRow'] ) ? $merge['startRow'] : 0,
                    'start_col' => isset( $merge['startCol'] ) ? $merge['startCol'] : 0,
                    'end_row' => isset( $merge['endRow'] ) ? $merge['endRow'] : 0,
                    'end_col' => isset( $merge['endCol'] ) ? $merge['endCol'] : 0,
                ),
                array( '%d', '%d', '%d', '%d', '%d' )
            );
        }
    }

    /**
     * Migrate display settings for a table
     */
    private static function migrate_display_settings( $table_id, $settings ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'atables_display_settings';

        // Check if settings already exist for this table
        $exists = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT id FROM {$table_name} WHERE table_id = %d",
                $table_id
            )
        );

        $data = array(
            'table_id' => $table_id,
            'theme' => isset( $settings['theme'] ) ? $settings['theme'] : 'default',
            'responsive_mode' => isset( $settings['responsive_mode'] ) ? $settings['responsive_mode'] : 'scroll',
            'enable_search' => isset( $settings['enable_search'] ) ? ( $settings['enable_search'] ? 1 : 0 ) : 1,
            'enable_sorting' => isset( $settings['enable_sorting'] ) ? ( $settings['enable_sorting'] ? 1 : 0 ) : 1,
            'enable_pagination' => isset( $settings['enable_pagination'] ) ? ( $settings['enable_pagination'] ? 1 : 0 ) : 1,
            'rows_per_page' => isset( $settings['rows_per_page'] ) ? intval( $settings['rows_per_page'] ) : 10,
            'custom_css' => isset( $settings['custom_css'] ) ? $settings['custom_css'] : '',
        );

        if ( $exists ) {
            $wpdb->update(
                $table_name,
                $data,
                array( 'table_id' => $table_id ),
                array( '%d', '%s', '%s', '%d', '%d', '%d', '%d', '%s' ),
                array( '%d' )
            );
        } else {
            $wpdb->insert(
                $table_name,
                $data,
                array( '%d', '%s', '%s', '%d', '%d', '%d', '%d', '%s' )
            );
        }
    }
}

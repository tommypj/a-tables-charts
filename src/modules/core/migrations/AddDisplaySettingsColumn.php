<?php
/**
 * Database Migration: Add display_settings column
 * 
 * Adds display_settings column to wp_atables_tables table for per-table display configuration.
 * 
 * @package ATablesCharts\Core\Migrations
 * @since 1.0.1
 */

namespace ATablesCharts\Core\Migrations;

class AddDisplaySettingsColumn {
    
    /**
     * Run the migration
     * 
     * @return array Result with success status and message
     */
    public static function up() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'atables_tables';
        
        // Check if column already exists
        $column_exists = $wpdb->get_results(
            $wpdb->prepare(
                "SHOW COLUMNS FROM {$table_name} LIKE %s",
                'display_settings'
            )
        );
        
        if (!empty($column_exists)) {
            return array(
                'success' => true,
                'message' => 'display_settings column already exists'
            );
        }
        
        // Add the column
        $sql = "ALTER TABLE {$table_name}
                ADD COLUMN display_settings LONGTEXT DEFAULT NULL
                AFTER description";
        
        $result = $wpdb->query($sql);
        
        if ($result === false) {
            return array(
                'success' => false,
                'message' => 'Failed to add display_settings column: ' . $wpdb->last_error
            );
        }
        
        return array(
            'success' => true,
            'message' => 'display_settings column added successfully'
        );
    }
    
    /**
     * Rollback the migration
     * 
     * @return array Result with success status and message
     */
    public static function down() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'atables_tables';
        
        // Remove the column
        $sql = "ALTER TABLE {$table_name} DROP COLUMN display_settings";
        
        $result = $wpdb->query($sql);
        
        if ($result === false) {
            return array(
                'success' => false,
                'message' => 'Failed to remove display_settings column: ' . $wpdb->last_error
            );
        }
        
        return array(
            'success' => true,
            'message' => 'display_settings column removed successfully'
        );
    }
    
    /**
     * Check if migration has been run
     * 
     * @return bool
     */
    public static function has_run() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'atables_tables';
        
        $column_exists = $wpdb->get_results(
            $wpdb->prepare(
                "SHOW COLUMNS FROM {$table_name} LIKE %s",
                'display_settings'
            )
        );
        
        return !empty($column_exists);
    }
}

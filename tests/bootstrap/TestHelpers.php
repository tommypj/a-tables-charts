<?php
/**
 * Test Helper Functions
 *
 * Provides utility functions for creating test data and cleaning up.
 *
 * @package ATablesCharts\Tests\Bootstrap
 * @since 1.0.0
 */

namespace ATablesCharts\Tests\Bootstrap;

class TestHelpers {
    
    /**
     * Create a test table in the database
     *
     * @param string $title Table title
     * @return int Table ID
     */
    public static function create_test_table($title = 'Test Table') {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'atables_tables';
        
        $wpdb->insert(
            $table_name,
            [
                'title' => $title,
                'description' => 'Test description',
                'data_source_type' => 'manual',
                'data_source_config' => json_encode([
                    'headers' => ['Column A', 'Column B', 'Column C'],
                    'data' => [
                        ['Value 1', 'Value 2', 'Value 3'],
                        ['Value 4', 'Value 5', 'Value 6'],
                    ]
                ]),
                'row_count' => 2,
                'column_count' => 3,
                'status' => 'active',
                'created_by' => 1,
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql'),
            ],
            ['%s', '%s', '%s', '%s', '%d', '%d', '%s', '%d', '%s', '%s']
        );
        
        return $wpdb->insert_id;
    }
    
    /**
     * Delete all test tables
     */
    public static function cleanup_test_tables() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'atables_tables';
        $wpdb->query("DELETE FROM {$table_name} WHERE title LIKE 'Test%'");
    }
    
    /**
     * Create sample CSV data
     *
     * @return string CSV content
     */
    public static function get_sample_csv_data() {
        return "Name,Age,City\nJohn,30,New York\nJane,25,Los Angeles\nBob,35,Chicago";
    }
    
    /**
     * Create sample JSON data
     *
     * @return string JSON content
     */
    public static function get_sample_json_data() {
        return json_encode([
            ['name' => 'John', 'age' => 30, 'city' => 'New York'],
            ['name' => 'Jane', 'age' => 25, 'city' => 'Los Angeles'],
            ['name' => 'Bob', 'age' => 35, 'city' => 'Chicago'],
        ]);
    }
    
    /**
     * Create sample array data
     *
     * @return array
     */
    public static function get_sample_array_data() {
        return [
            ['name' => 'John', 'age' => 30, 'city' => 'New York'],
            ['name' => 'Jane', 'age' => 25, 'city' => 'Los Angeles'],
            ['name' => 'Bob', 'age' => 35, 'city' => 'Chicago'],
        ];
    }
    
    /**
     * Get sample headers
     *
     * @return array
     */
    public static function get_sample_headers() {
        return ['Name', 'Age', 'City'];
    }
}

<?php
/**
 * Debug Table 18
 * Check if table exists and what data it has
 */

require_once('../../../../wp-load.php');

global $wpdb;
$table_name = $wpdb->prefix . 'atables_tables';

echo "<h1>Debug Table 18</h1>";

// Check if table 18 exists
$table18 = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", 18));

if ($table18) {
    echo "<h2 style='color:green;'>✓ Table 18 EXISTS</h2>";
    echo "<pre>";
    print_r($table18);
    echo "</pre>";
    
    echo "<h3>Source Data:</h3>";
    $source_data = json_decode($table18->data_source_config, true);
    echo "<pre>";
    print_r($source_data);
    echo "</pre>";
    
    echo "<h3>Display Settings:</h3>";
    echo "<pre>";
    $display_settings = json_decode($table18->display_settings, true);
    print_r($display_settings);
    echo "</pre>";
    
    // Check table data
    $data_table = $wpdb->prefix . 'atables_table_data';
    $data_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $data_table WHERE table_id = %d", 18));
    echo "<h3>Data Rows in table_data: $data_count</h3>";
    
    if ($data_count > 0) {
        echo "<h4>Sample Data (first 3 rows):</h4>";
        $sample = $wpdb->get_results($wpdb->prepare("SELECT * FROM $data_table WHERE table_id = %d LIMIT 3", 18));
        echo "<pre>";
        foreach ($sample as $row) {
            echo "Row {$row->row_index}: " . $row->row_data . "\n";
        }
        echo "</pre>";
    }
    
} else {
    echo "<h2 style='color:red;'>✗ Table 18 DOES NOT EXIST!</h2>";
    
    // Show all tables
    echo "<h3>All Tables in Database:</h3>";
    $all_tables = $wpdb->get_results("SELECT id, title, status FROM $table_name ORDER BY id DESC LIMIT 10");
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Title</th><th>Status</th></tr>";
    foreach ($all_tables as $t) {
        echo "<tr><td>{$t->id}</td><td>{$t->title}</td><td>{$t->status}</td></tr>";
    }
    echo "</table>";
}

echo "<hr>";
echo "<h3>What shortcode are you using?</h3>";
echo "<p>Make sure you're using: <code>[atable id=\"18\"]</code></p>";

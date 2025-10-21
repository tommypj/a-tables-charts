<?php
/**
 * Debug Table Existence
 * 
 * Check if table 17 exists and what data it has
 */

require_once('../../../../../wp-load.php');

global $wpdb;
$table_name = $wpdb->prefix . 'atables_tables';

// Check table 14
$table14 = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", 14));
echo "<h2>Table 14:</h2>";
echo "<pre>";
print_r($table14);
echo "</pre>";

// Check table 17
$table17 = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", 17));
echo "<h2>Table 17:</h2>";
if ($table17) {
    echo "<pre>";
    print_r($table17);
    echo "</pre>";
    
    // Check display settings
    echo "<h3>Display Settings for Table 17:</h3>";
    echo "<pre>";
    $settings = json_decode($table17->display_settings, true);
    print_r($settings);
    echo "</pre>";
} else {
    echo "<p style='color:red;'><strong>TABLE 17 DOES NOT EXIST!</strong></p>";
}

// List all tables
echo "<h2>All Tables:</h2>";
$all_tables = $wpdb->get_results("SELECT id, title FROM $table_name ORDER BY id");
echo "<ul>";
foreach ($all_tables as $t) {
    echo "<li>ID: {$t->id} - {$t->title}</li>";
}
echo "</ul>";

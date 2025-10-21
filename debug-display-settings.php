<?php
/**
 * Debug Display Settings
 * 
 * Temporary file to check what's in the database
 * Run this from WordPress admin or command line
 */

// Include WordPress
require_once('../../../../../wp-load.php');

// Get table ID from URL
$table_id = isset($_GET['table_id']) ? (int)$_GET['table_id'] : 0;

if (!$table_id) {
    die('Usage: debug-display-settings.php?table_id=123');
}

global $wpdb;
$table_name = $wpdb->prefix . 'atables_tables';

$table = $wpdb->get_row($wpdb->prepare(
    "SELECT id, title, display_settings FROM $table_name WHERE id = %d",
    $table_id
));

if (!$table) {
    die('Table not found!');
}

echo "<h2>Table: {$table->title} (ID: {$table->id})</h2>";
echo "<h3>Raw display_settings from database:</h3>";
echo "<pre>";
var_dump($table->display_settings);
echo "</pre>";

echo "<h3>Decoded display_settings:</h3>";
echo "<pre>";
$decoded = json_decode($table->display_settings, true);
var_dump($decoded);
echo "</pre>";

echo "<h3>Expected values:</h3>";
echo "<ul>";
echo "<li><strong>enable_search:</strong> " . ($decoded['enable_search'] ?? 'NOT SET') . "</li>";
echo "<li><strong>enable_sorting:</strong> " . ($decoded['enable_sorting'] ?? 'NOT SET') . "</li>";
echo "<li><strong>enable_pagination:</strong> " . ($decoded['enable_pagination'] ?? 'NOT SET') . "</li>";
echo "<li><strong>rows_per_page:</strong> " . ($decoded['rows_per_page'] ?? 'NOT SET') . "</li>";
echo "<li><strong>table_style:</strong> " . ($decoded['table_style'] ?? 'NOT SET') . "</li>";
echo "</ul>";

echo "<h3>Global Settings:</h3>";
echo "<pre>";
$global = get_option('atables_settings', array());
print_r($global);
echo "</pre>";

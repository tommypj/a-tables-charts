<?php
/**
 * Debug Script - Check Settings
 * 
 * Place this file in your plugin root and access it via browser to see what's saved
 */

// Load WordPress
require_once('../../../wp-load.php');

// Get settings
$settings = get_option('atables_settings', array());

echo "<h1>A-Tables Settings Debug</h1>";
echo "<h2>Current Settings in Database:</h2>";
echo "<pre>";
print_r($settings);
echo "</pre>";

echo "<h2>Specific Check:</h2>";
echo "<p><strong>enable_mysql_query:</strong> ";
if (isset($settings['enable_mysql_query'])) {
    echo "SET to: ";
    var_dump($settings['enable_mysql_query']);
} else {
    echo "NOT SET (missing from array)";
}
echo "</p>";

echo "<h2>All Boolean Fields:</h2>";
$boolean_fields = array(
    'enable_responsive',
    'enable_search',
    'enable_sorting',
    'enable_pagination',
    'enable_export',
    'cache_enabled',
    'lazy_load_tables',
    'async_loading',
    'google_charts_enabled',
    'chartjs_enabled',
    'sanitize_html',
    'enable_mysql_query',
);

echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Field</th><th>Value</th><th>Type</th></tr>";
foreach ($boolean_fields as $field) {
    echo "<tr>";
    echo "<td>$field</td>";
    echo "<td>";
    if (isset($settings[$field])) {
        var_dump($settings[$field]);
    } else {
        echo "NOT SET";
    }
    echo "</td>";
    echo "<td>" . (isset($settings[$field]) ? gettype($settings[$field]) : 'undefined') . "</td>";
    echo "</tr>";
}
echo "</table>";

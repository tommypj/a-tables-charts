<?php
/**
 * Check Table Settings
 * 
 * View what settings are saved for a table
 * Access via: /wp-content/plugins/a-tables-charts/check-table-settings.php?table_id=7
 * 
 * IMPORTANT: Delete this file after debugging!
 */

// Load WordPress
require_once(__DIR__ . '/../../../wp-load.php');

// Security check
if (!current_user_can('manage_options')) {
    die('Permission denied');
}

$table_id = isset($_GET['table_id']) ? (int)$_GET['table_id'] : 0;

if (empty($table_id)) {
    die('Please provide table_id in URL: ?table_id=7');
}

echo '<h1>Table Settings Debug</h1>';
echo '<p>Checking settings for Table ID: ' . $table_id . '</p>';
echo '<hr>';

// Load table
require_once(__DIR__ . '/src/modules/tables/index.php');
$repository = new \ATablesCharts\Tables\Repositories\TableRepository();
$table = $repository->find_by_id($table_id);

if (!$table) {
    die('<p style="color: red;">Table not found!</p>');
}

echo '<h2>1. Raw Database Column Value:</h2>';
global $wpdb;
$raw = $wpdb->get_var($wpdb->prepare(
    "SELECT display_settings FROM {$wpdb->prefix}atables_tables WHERE id = %d",
    $table_id
));
echo '<pre>';
echo 'Type: ' . gettype($raw) . "\n";
echo 'Value: ';
var_dump($raw);
echo '</pre>';

echo '<hr>';
echo '<h2>2. Table Model get_display_settings():</h2>';
$settings = $table->get_display_settings();
echo '<pre>';
print_r($settings);
echo '</pre>';

echo '<hr>';
echo '<h2>3. Global Settings:</h2>';
$global = get_option('atables_settings', array());
echo '<pre>';
print_r($global);
echo '</pre>';

echo '<hr>';
echo '<h2>4. Expected Behavior:</h2>';
if (isset($settings['enable_search'])) {
    if ($settings['enable_search'] === false) {
        echo '<p style="color: green;">✓ Search is DISABLED for this table (custom setting)</p>';
    } else {
        echo '<p style="color: blue;">ℹ Search is ENABLED for this table (custom setting)</p>';
    }
} else {
    echo '<p style="color: orange;">⚠ Search uses GLOBAL setting (no custom override)</p>';
    if (isset($global['enable_search']) && $global['enable_search']) {
        echo '<p>Global setting: ENABLED</p>';
    } else {
        echo '<p>Global setting: DISABLED</p>';
    }
}

echo '<hr>';
echo '<p><strong>Delete this file after debugging!</strong></p>';
?>

<?php
/**
 * Diagnostic Script - Check Filter Presets Table
 * 
 * Run this via: http://my-wordpress-site.local/wp-content/plugins/a-tables-charts/check-migration.php
 */

// Load WordPress
require_once('../../../wp-load.php');

// Check if user is admin
if (!current_user_can('manage_options')) {
    die('Access denied. Must be admin.');
}

global $wpdb;

$table_name = $wpdb->prefix . 'atables_filter_presets';

echo '<h1>🔍 Migration Diagnostic</h1>';
echo '<hr>';

// Check if table exists
$query = $wpdb->prepare(
    'SHOW TABLES LIKE %s',
    $wpdb->esc_like($table_name)
);
$table_exists = $wpdb->get_var($query) === $table_name;

echo '<h2>Table Status</h2>';
echo '<p><strong>Table Name:</strong> ' . esc_html($table_name) . '</p>';
echo '<p><strong>Table Exists:</strong> ' . ($table_exists ? '✅ YES' : '❌ NO') . '</p>';

if ($table_exists) {
    echo '<p style="color: green;">✅ The migration has already been run!</p>';
    
    // Show table structure
    $columns = $wpdb->get_results("DESCRIBE {$table_name}");
    echo '<h3>Table Structure:</h3>';
    echo '<table border="1" cellpadding="5">';
    echo '<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>';
    foreach ($columns as $column) {
        echo '<tr>';
        echo '<td>' . esc_html($column->Field) . '</td>';
        echo '<td>' . esc_html($column->Type) . '</td>';
        echo '<td>' . esc_html($column->Null) . '</td>';
        echo '<td>' . esc_html($column->Key) . '</td>';
        echo '<td>' . esc_html($column->Default) . '</td>';
        echo '</tr>';
    }
    echo '</table>';
    
    // Count rows
    $count = $wpdb->get_var("SELECT COUNT(*) FROM {$table_name}");
    echo '<p><strong>Row Count:</strong> ' . esc_html($count) . '</p>';
    
} else {
    echo '<p style="color: orange;">⚠️ The table does not exist yet.</p>';
    echo '<p><a href="' . admin_url('admin.php?page=a-tables-charts&action=run-migrations') . '" class="button button-primary">Run Migration Now</a></p>';
}

echo '<hr>';
echo '<h2>Migration Runner Status</h2>';

// Load migration runner
require_once(ATABLES_PLUGIN_DIR . 'src/modules/core/MigrationRunner.php');

$has_pending = \ATablesCharts\Core\MigrationRunner::has_pending();
echo '<p><strong>Has Pending Migrations:</strong> ' . ($has_pending ? '✅ YES' : '❌ NO') . '</p>';

if ($has_pending) {
    echo '<p style="color: orange;">⚠️ There are pending migrations that need to run.</p>';
    echo '<p><a href="' . admin_url('admin.php?page=a-tables-charts&action=run-migrations') . '" class="button button-primary">Run All Migrations</a></p>';
} else {
    echo '<p style="color: green;">✅ All migrations are up to date!</p>';
}

echo '<hr>';
echo '<p><a href="' . admin_url('admin.php?page=a-tables-charts') . '">&larr; Back to A-Tables Dashboard</a></p>';

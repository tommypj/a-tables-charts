<?php
/**
 * Debug Helper - Check Feature Status
 *
 * Visit: /wp-content/plugins/a-tables-charts/debug-features.php
 */

require_once('../../../wp-load.php');

if (!current_user_can('manage_options')) {
    die('Access denied');
}

global $wpdb;

echo '<h1>Feature Status Debug</h1>';

echo '<h2>Database Features:</h2>';
$features = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}atables_features", ARRAY_A);
echo '<pre>';
print_r($features);
echo '</pre>';

echo '<h2>FeatureManager Check:</h2>';
foreach ($features as $feature) {
    $enabled = \ATables\Features\FeatureManager::is_enabled($feature['feature_key']);
    echo '<p><strong>' . $feature['feature_key'] . ':</strong> ' . ($enabled ? 'ENABLED' : 'DISABLED') . '</p>';
}

echo '<h2>Theme Module Hooks Registered:</h2>';
global $wp_filter;
echo '<p>atables_table_classes filter: ';
echo isset($wp_filter['atables_table_classes']) ? 'YES' : 'NO';
echo '</p>';
echo '<p>wp_enqueue_scripts action (themes): ';
if (isset($wp_filter['wp_enqueue_scripts'])) {
    $found = false;
    foreach ($wp_filter['wp_enqueue_scripts']->callbacks as $priority => $callbacks) {
        foreach ($callbacks as $callback) {
            if (is_array($callback['function']) &&
                $callback['function'][0] === 'ATables\Features\Themes\ThemeModule') {
                $found = true;
                break 2;
            }
        }
    }
    echo $found ? 'YES' : 'NO';
} else {
    echo 'NO';
}
echo '</p>';

echo '<h2>Sample Table Theme:</h2>';
$table_1_theme = get_option('atables_theme_1', 'not set');
echo '<p>Table 1 theme: ' . $table_1_theme . '</p>';

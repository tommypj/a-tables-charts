<?php
/**
 * Manual Migration Runner
 * 
 * Run this file directly to execute the display_settings migration.
 * Access via: /wp-content/plugins/a-tables-charts/run-migration-manually.php
 * 
 * IMPORTANT: Delete this file after running!
 */

// Load WordPress
require_once(__DIR__ . '/../../../wp-load.php');

// Security check
if (!current_user_can('manage_options')) {
    die('Permission denied');
}

// Load migration
require_once(__DIR__ . '/src/modules/core/migrations/AddDisplaySettingsColumn.php');

echo '<h1>Manual Migration Runner</h1>';
echo '<p>Running AddDisplaySettingsColumn migration...</p>';

// Check if already run
if (\ATablesCharts\Core\Migrations\AddDisplaySettingsColumn::has_run()) {
    echo '<p style="color: green;"><strong>✓ Migration already completed!</strong></p>';
    echo '<p>The display_settings column already exists in the database.</p>';
} else {
    // Run migration
    $result = \ATablesCharts\Core\Migrations\AddDisplaySettingsColumn::up();
    
    if ($result['success']) {
        echo '<p style="color: green;"><strong>✓ SUCCESS!</strong></p>';
        echo '<p>' . esc_html($result['message']) . '</p>';
        echo '<p>The display_settings column has been added to your database.</p>';
        echo '<p><strong>Now try saving your table settings again!</strong></p>';
    } else {
        echo '<p style="color: red;"><strong>✗ FAILED!</strong></p>';
        echo '<p>' . esc_html($result['message']) . '</p>';
        echo '<p>Please check your database permissions and try again.</p>';
    }
}

echo '<hr>';
echo '<p><strong>IMPORTANT:</strong> Delete this file (run-migration-manually.php) after running!</p>';
echo '<p>File location: ' . __FILE__ . '</p>';
?>

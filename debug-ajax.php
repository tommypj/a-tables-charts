<?php
/**
 * AJAX Debug Script
 * 
 * This simulates the AJAX call to see the actual error
 * Access via: /wp-content/plugins/a-tables-charts/debug-ajax.php
 * 
 * IMPORTANT: Delete this file after debugging!
 */

// Load WordPress
require_once(__DIR__ . '/../../../wp-load.php');

// Security check
if (!current_user_can('manage_options')) {
    die('Permission denied');
}

echo '<h1>AJAX Debug Script</h1>';
echo '<p>Testing the update_table AJAX handler...</p>';
echo '<hr>';

// Simulate the AJAX request
$_POST['action'] = 'atables_update_table';
$_POST['nonce'] = wp_create_nonce('atables_admin_nonce');
$_POST['table_id'] = 7;
$_POST['title'] = 'Test Table';
$_POST['description'] = 'Test Description';
$_POST['headers'] = array('Header 1', 'Header 2');
$_POST['data'] = array(
    array('Row 1 Col 1', 'Row 1 Col 2'),
    array('Row 2 Col 1', 'Row 2 Col 2')
);
$_POST['display_settings'] = array(
    'enable_search' => false
);

echo '<h2>POST Data:</h2>';
echo '<pre>';
print_r($_POST);
echo '</pre>';

echo '<hr>';
echo '<h2>Response:</h2>';

// Capture output
ob_start();

// Load the controller
require_once(__DIR__ . '/src/modules/tables/index.php');
$controller = new \ATablesCharts\Tables\Controllers\TableController();

// Call the handler
$controller->handle_update_table();

// Get the output
$output = ob_get_clean();

echo '<pre style="background: #f0f0f0; padding: 10px; border: 1px solid #ccc;">';
echo htmlspecialchars($output);
echo '</pre>';

echo '<hr>';
echo '<p><strong>If you see HTML/PHP errors above (like warnings or notices), that\'s the problem!</strong></p>';
echo '<p>The error is being output before the JSON response, causing the parse error.</p>';
?>

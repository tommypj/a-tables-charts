<?php
/**
 * Debug Test Script
 * 
 * Run this file directly to test if all classes load properly
 * Access via: http://your-site.local/wp-content/plugins/a-tables-charts/debug-test.php
 */

// Load WordPress
require_once('../../../wp-load.php');

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>A-Tables & Charts - Debug Test</h1>";
echo "<pre>";

// Test 1: Check if constants are defined
echo "\n=== TEST 1: Constants ===\n";
echo "ATABLES_PLUGIN_DIR: " . (defined('ATABLES_PLUGIN_DIR') ? "✓ " . ATABLES_PLUGIN_DIR : "✗ Not defined") . "\n";
echo "ATABLES_VERSION: " . (defined('ATABLES_VERSION') ? "✓ " . ATABLES_VERSION : "✗ Not defined") . "\n";

// Test 2: Check if shared utilities load
echo "\n=== TEST 2: Shared Utilities ===\n";
try {
    require_once ATABLES_PLUGIN_DIR . 'src/shared/utils/Validator.php';
    echo "Validator: ✓ Loaded\n";
} catch (Exception $e) {
    echo "Validator: ✗ Error - " . $e->getMessage() . "\n";
}

try {
    require_once ATABLES_PLUGIN_DIR . 'src/shared/utils/Sanitizer.php';
    echo "Sanitizer: ✓ Loaded\n";
} catch (Exception $e) {
    echo "Sanitizer: ✗ Error - " . $e->getMessage() . "\n";
}

try {
    require_once ATABLES_PLUGIN_DIR . 'src/shared/utils/Helpers.php';
    echo "Helpers: ✓ Loaded\n";
} catch (Exception $e) {
    echo "Helpers: ✗ Error - " . $e->getMessage() . "\n";
}

try {
    require_once ATABLES_PLUGIN_DIR . 'src/shared/utils/Logger.php';
    echo "Logger: ✓ Loaded\n";
} catch (Exception $e) {
    echo "Logger: ✗ Error - " . $e->getMessage() . "\n";
}

// Test 3: Check if Data Sources module loads
echo "\n=== TEST 3: Data Sources Module ===\n";
try {
    require_once ATABLES_PLUGIN_DIR . 'src/modules/dataSources/index.php';
    echo "Data Sources Module: ✓ Loaded\n";
} catch (Exception $e) {
    echo "Data Sources Module: ✗ Error - " . $e->getMessage() . "\n";
}

// Test 4: Test ImportController instantiation
echo "\n=== TEST 4: ImportController ===\n";
try {
    $controller = new \ATablesCharts\DataSources\Controllers\ImportController();
    echo "ImportController: ✓ Instantiated successfully\n";
} catch (Exception $e) {
    echo "ImportController: ✗ Error - " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

// Test 5: Check AJAX hooks
echo "\n=== TEST 5: AJAX Hooks ===\n";
$hooks = array(
    'wp_ajax_atables_import_file',
    'wp_ajax_atables_import_url',
    'wp_ajax_atables_preview_import'
);

foreach ($hooks as $hook) {
    global $wp_filter;
    $registered = isset($wp_filter[$hook]) && !empty($wp_filter[$hook]);
    echo "$hook: " . ($registered ? "✓ Registered" : "✗ Not registered") . "\n";
}

// Test 6: Simulate CSV upload
echo "\n=== TEST 6: CSV Parser Test ===\n";
try {
    $parser = new \ATablesCharts\DataSources\Parsers\CsvParser();
    echo "CSV Parser: ✓ Instantiated\n";
    echo "Supported extensions: " . implode(', ', $parser->get_supported_extensions()) . "\n";
} catch (Exception $e) {
    echo "CSV Parser: ✗ Error - " . $e->getMessage() . "\n";
}

// Test 7: Check upload directory permissions
echo "\n=== TEST 7: Upload Directory ===\n";
$upload_dir = wp_upload_dir();
echo "Upload path: " . $upload_dir['path'] . "\n";
echo "Writable: " . (is_writable($upload_dir['path']) ? "✓ Yes" : "✗ No") . "\n";
echo "Max upload size: " . size_format(wp_max_upload_size()) . "\n";

echo "\n=== All Tests Complete ===\n";
echo "</pre>";

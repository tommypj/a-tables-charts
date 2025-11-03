<?php
/**
 * Scheduled Refresh Integration Test
 *
 * Tests complete workflow of scheduled refresh functionality.
 *
 * @package ATablesCharts\Tests
 */

// Bootstrap WordPress
define( 'ABSPATH', dirname( __FILE__, 3 ) . '/../../../' );
define( 'ATABLES_PLUGIN_DIR', dirname( __FILE__, 3 ) . '/' );
define( 'ATABLES_VERSION', '1.0.0' );
define( 'ATABLES_SLUG', 'a-tables-charts' );

require_once ABSPATH . 'wp-load.php';

require_once ATABLES_PLUGIN_DIR . 'src/modules/cron/index.php';
require_once ATABLES_PLUGIN_DIR . 'src/modules/tables/index.php';

use ATablesCharts\Cron\Services\ScheduleService;
use ATablesCharts\Cron\Services\RefreshService;
use ATablesCharts\Tables\Services\TableService;

echo "\n";
echo "====================================\n";
echo "Scheduled Refresh Integration Test\n";
echo "====================================\n\n";

$schedule_service = new ScheduleService();
$refresh_service = new RefreshService();
$table_service = new TableService();

$test_passed = 0;
$test_failed = 0;

// Test 1: Create test table
echo "Test 1: Creating test table...";
$table_result = $table_service->create_table( array(
	'title' => 'Integration Test Table',
	'description' => 'Created for integration testing',
	'status' => 'active',
	'source_data' => array(
		'headers' => array( 'ID', 'Name', 'Value' ),
		'data' => array(
			array( '1', 'Original', '100' ),
		),
	),
	'row_count' => 1,
	'column_count' => 3,
) );

if ( $table_result['success'] ) {
	echo " ✓ PASS\n";
	$test_passed++;
	$table_id = $table_result['table_id'];
	echo "  Table ID: $table_id\n";
} else {
	echo " ✗ FAIL: " . $table_result['message'] . "\n";
	$test_failed++;
	exit( 1 );
}

// Test 2: Create schedule (simulating CSV refresh)
echo "\nTest 2: Creating refresh schedule...";
$schedule_id = $schedule_service->create_schedule( array(
	'table_id' => $table_id,
	'source_type' => 'mysql',
	'frequency' => 'daily',
	'config' => array(
		'query' => "SELECT 1 as ID, 'Refreshed' as Name, '200' as Value",
	),
	'active' => true,
) );

if ( $schedule_id > 0 ) {
	echo " ✓ PASS\n";
	$test_passed++;
	echo "  Schedule ID: $schedule_id\n";
} else {
	echo " ✗ FAIL\n";
	$test_failed++;
}

// Test 3: Get schedule
echo "\nTest 3: Retrieving schedule...";
$schedule = $schedule_service->get_schedule( $schedule_id );

if ( $schedule && $schedule['table_id'] === $table_id ) {
	echo " ✓ PASS\n";
	$test_passed++;
	echo "  Schedule retrieved successfully\n";
} else {
	echo " ✗ FAIL\n";
	$test_failed++;
}

// Test 4: Trigger refresh
echo "\nTest 4: Triggering scheduled refresh...";
$refresh_result = $refresh_service->refresh_table( $schedule );

if ( $refresh_result['success'] ) {
	echo " ✓ PASS\n";
	$test_passed++;
	echo "  " . $refresh_result['message'] . "\n";
} else {
	echo " ✗ FAIL: " . ( $refresh_result['message'] ?? 'Unknown error' ) . "\n";
	$test_failed++;
}

// Test 5: Verify table updated
echo "\nTest 5: Verifying table data updated...";
$updated_table = $table_service->get_table( $table_id );

if ( $updated_table && isset( $updated_table->source_data['data'][0] ) ) {
	$first_row = $updated_table->source_data['data'][0];
	if ( isset( $first_row[1] ) && $first_row[1] === 'Refreshed' ) {
		echo " ✓ PASS\n";
		$test_passed++;
		echo "  Table data successfully updated\n";
	} else {
		echo " ✗ FAIL: Data not updated correctly\n";
		$test_failed++;
	}
} else {
	echo " ✗ FAIL: Could not retrieve updated table\n";
	$test_failed++;
}

// Test 6: Update last run
echo "\nTest 6: Updating last run status...";
$update_result = $schedule_service->update_last_run(
	$schedule_id,
	time(),
	'success',
	'Integration test successful'
);

if ( $update_result ) {
	echo " ✓ PASS\n";
	$test_passed++;
} else {
	echo " ✗ FAIL\n";
	$test_failed++;
}

// Test 7: Deactivate schedule
echo "\nTest 7: Deactivating schedule...";
$deactivate_result = $schedule_service->toggle_active( $schedule_id, false );

if ( $deactivate_result ) {
	$schedule = $schedule_service->get_schedule( $schedule_id );
	if ( ! $schedule['active'] ) {
		echo " ✓ PASS\n";
		$test_passed++;
	} else {
		echo " ✗ FAIL: Schedule still active\n";
		$test_failed++;
	}
} else {
	echo " ✗ FAIL\n";
	$test_failed++;
}

// Cleanup
echo "\nCleaning up test data...";
$schedule_service->delete_schedule( $schedule_id );
$table_service->delete_table( $table_id );
echo " Done\n";

// Summary
echo "\n====================================\n";
echo "Test Summary\n";
echo "====================================\n";
echo "Total Tests:  " . ( $test_passed + $test_failed ) . "\n";
echo "Passed:       \033[0;32m$test_passed\033[0m\n";
echo "Failed:       \033[0;31m$test_failed\033[0m\n";
echo "====================================\n\n";

if ( $test_failed === 0 ) {
	echo "\033[0;32m✓ All integration tests passed!\033[0m\n\n";
	exit( 0 );
} else {
	echo "\033[0;31m✗ Some integration tests failed!\033[0m\n\n";
	exit( 1 );
}

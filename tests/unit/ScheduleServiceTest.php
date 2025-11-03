<?php
/**
 * Schedule Service Unit Tests
 *
 * @package ATablesCharts\Tests
 */

namespace ATablesCharts\Tests\Unit;

/**
 * Test Schedule Service
 */
class ScheduleServiceTest {

	/**
	 * Schedule service instance
	 *
	 * @var \ATablesCharts\Cron\Services\ScheduleService
	 */
	private $service;

	/**
	 * Test results
	 *
	 * @var array
	 */
	private $results = array();

	/**
	 * Setup before tests
	 */
	public function setUp() {
		require_once ATABLES_PLUGIN_DIR . 'src/modules/cron/index.php';
		$this->service = new \ATablesCharts\Cron\Services\ScheduleService();

		// Clear all schedules
		$schedules = $this->service->get_all_schedules();
		foreach ( $schedules as $schedule ) {
			$this->service->delete_schedule( $schedule['id'] );
		}
	}

	/**
	 * Run all tests
	 *
	 * @return array Test results.
	 */
	public function run_all_tests() {
		$this->setUp();

		echo "\n=== Schedule Service Unit Tests ===\n\n";

		$this->test_create_schedule();
		$this->test_get_schedule();
		$this->test_get_all_schedules();
		$this->test_update_schedule();
		$this->test_delete_schedule();
		$this->test_toggle_active();
		$this->test_update_last_run();
		$this->test_get_statistics();

		return $this->results;
	}

	/**
	 * Test: Create schedule
	 */
	private function test_create_schedule() {
		$test_name = 'Create Schedule';

		try {
			$schedule_data = array(
				'table_id' => 1,
				'source_type' => 'mysql',
				'frequency' => 'daily',
				'config' => array(
					'query' => 'SELECT * FROM wp_posts',
				),
				'active' => true,
			);

			$schedule_id = $this->service->create_schedule( $schedule_data );

			$this->assert_true( $schedule_id > 0, 'Schedule ID should be positive' );

			// Verify schedule was created
			$schedule = $this->service->get_schedule( $schedule_id );
			$this->assert_true( ! empty( $schedule ), 'Schedule should exist' );
			$this->assert_equals( 1, $schedule['table_id'], 'Table ID should match' );
			$this->assert_equals( 'mysql', $schedule['source_type'], 'Source type should match' );
			$this->assert_equals( 'daily', $schedule['frequency'], 'Frequency should match' );
			$this->assert_true( $schedule['active'], 'Should be active' );

			$this->pass_test( $test_name, "Schedule ID: $schedule_id created successfully" );
		} catch ( \Exception $e ) {
			$this->fail_test( $test_name, $e->getMessage() );
		}
	}

	/**
	 * Test: Get schedule
	 */
	private function test_get_schedule() {
		$test_name = 'Get Schedule';

		try {
			// Create a test schedule
			$schedule_id = $this->service->create_schedule( array(
				'table_id' => 2,
				'source_type' => 'google_sheets',
				'frequency' => 'hourly',
				'config' => array(
					'sheet_id' => 'test123',
					'api_key' => 'key123',
				),
				'active' => true,
			) );

			// Get the schedule
			$schedule = $this->service->get_schedule( $schedule_id );

			$this->assert_true( ! empty( $schedule ), 'Schedule should be retrieved' );
			$this->assert_equals( $schedule_id, $schedule['id'], 'Schedule ID should match' );
			$this->assert_equals( 'google_sheets', $schedule['source_type'], 'Source type should match' );
			$this->assert_true( isset( $schedule['config']['sheet_id'] ), 'Config should be intact' );

			// Try to get non-existent schedule
			$non_existent = $this->service->get_schedule( 99999 );
			$this->assert_true( $non_existent === null, 'Non-existent schedule should return null' );

			$this->pass_test( $test_name, 'Schedule retrieved correctly' );
		} catch ( \Exception $e ) {
			$this->fail_test( $test_name, $e->getMessage() );
		}
	}

	/**
	 * Test: Get all schedules
	 */
	private function test_get_all_schedules() {
		$test_name = 'Get All Schedules';

		try {
			// Create multiple schedules
			$this->service->create_schedule( array(
				'table_id' => 3,
				'source_type' => 'csv_url',
				'frequency' => 'daily',
				'config' => array( 'url' => 'http://example.com/data.csv' ),
				'active' => true,
			) );

			$this->service->create_schedule( array(
				'table_id' => 4,
				'source_type' => 'rest_api',
				'frequency' => 'hourly',
				'config' => array( 'url' => 'http://api.example.com/data' ),
				'active' => false,
			) );

			$schedules = $this->service->get_all_schedules();

			$this->assert_true( is_array( $schedules ), 'Should return array' );
			$this->assert_true( count( $schedules ) >= 2, 'Should have at least 2 schedules' );

			$this->pass_test( $test_name, count( $schedules ) . ' schedules retrieved' );
		} catch ( \Exception $e ) {
			$this->fail_test( $test_name, $e->getMessage() );
		}
	}

	/**
	 * Test: Update schedule
	 */
	private function test_update_schedule() {
		$test_name = 'Update Schedule';

		try {
			// Create schedule
			$schedule_id = $this->service->create_schedule( array(
				'table_id' => 5,
				'source_type' => 'mysql',
				'frequency' => 'daily',
				'config' => array( 'query' => 'SELECT * FROM test' ),
				'active' => true,
			) );

			// Update schedule
			$result = $this->service->update_schedule( $schedule_id, array(
				'frequency' => 'hourly',
				'active' => false,
			) );

			$this->assert_true( $result, 'Update should return true' );

			// Verify updates
			$schedule = $this->service->get_schedule( $schedule_id );
			$this->assert_equals( 'hourly', $schedule['frequency'], 'Frequency should be updated' );
			$this->assert_true( ! $schedule['active'], 'Should be inactive' );

			$this->pass_test( $test_name, 'Schedule updated successfully' );
		} catch ( \Exception $e ) {
			$this->fail_test( $test_name, $e->getMessage() );
		}
	}

	/**
	 * Test: Delete schedule
	 */
	private function test_delete_schedule() {
		$test_name = 'Delete Schedule';

		try {
			// Create schedule
			$schedule_id = $this->service->create_schedule( array(
				'table_id' => 6,
				'source_type' => 'mysql',
				'frequency' => 'daily',
				'config' => array(),
				'active' => true,
			) );

			// Delete schedule
			$result = $this->service->delete_schedule( $schedule_id );

			$this->assert_true( $result, 'Delete should return true' );

			// Verify deleted
			$schedule = $this->service->get_schedule( $schedule_id );
			$this->assert_true( $schedule === null, 'Schedule should not exist after deletion' );

			$this->pass_test( $test_name, 'Schedule deleted successfully' );
		} catch ( \Exception $e ) {
			$this->fail_test( $test_name, $e->getMessage() );
		}
	}

	/**
	 * Test: Toggle active status
	 */
	private function test_toggle_active() {
		$test_name = 'Toggle Active';

		try {
			// Create active schedule
			$schedule_id = $this->service->create_schedule( array(
				'table_id' => 7,
				'source_type' => 'mysql',
				'frequency' => 'daily',
				'config' => array(),
				'active' => true,
			) );

			// Deactivate
			$result = $this->service->toggle_active( $schedule_id, false );
			$this->assert_true( $result, 'Deactivate should return true' );

			$schedule = $this->service->get_schedule( $schedule_id );
			$this->assert_true( ! $schedule['active'], 'Should be inactive' );

			// Reactivate
			$result = $this->service->toggle_active( $schedule_id, true );
			$this->assert_true( $result, 'Reactivate should return true' );

			$schedule = $this->service->get_schedule( $schedule_id );
			$this->assert_true( $schedule['active'], 'Should be active again' );

			$this->pass_test( $test_name, 'Active status toggled correctly' );
		} catch ( \Exception $e ) {
			$this->fail_test( $test_name, $e->getMessage() );
		}
	}

	/**
	 * Test: Update last run
	 */
	private function test_update_last_run() {
		$test_name = 'Update Last Run';

		try {
			// Create schedule
			$schedule_id = $this->service->create_schedule( array(
				'table_id' => 8,
				'source_type' => 'mysql',
				'frequency' => 'daily',
				'config' => array(),
				'active' => true,
			) );

			// Update last run
			$timestamp = time();
			$result = $this->service->update_last_run(
				$schedule_id,
				$timestamp,
				'success',
				'Refreshed 100 rows'
			);

			$this->assert_true( $result, 'Update last run should return true' );

			// Verify update
			$schedule = $this->service->get_schedule( $schedule_id );
			$this->assert_equals( $timestamp, $schedule['last_run'], 'Last run timestamp should match' );
			$this->assert_equals( 'success', $schedule['last_status'], 'Last status should match' );
			$this->assert_equals( 'Refreshed 100 rows', $schedule['last_message'], 'Last message should match' );

			$this->pass_test( $test_name, 'Last run updated successfully' );
		} catch ( \Exception $e ) {
			$this->fail_test( $test_name, $e->getMessage() );
		}
	}

	/**
	 * Test: Get statistics
	 */
	private function test_get_statistics() {
		$test_name = 'Get Statistics';

		try {
			// Clear all schedules first
			$schedules = $this->service->get_all_schedules();
			foreach ( $schedules as $schedule ) {
				$this->service->delete_schedule( $schedule['id'] );
			}

			// Create test schedules
			$this->service->create_schedule( array(
				'table_id' => 9,
				'source_type' => 'mysql',
				'frequency' => 'daily',
				'config' => array(),
				'active' => true,
			) );

			$this->service->create_schedule( array(
				'table_id' => 10,
				'source_type' => 'mysql',
				'frequency' => 'daily',
				'config' => array(),
				'active' => true,
			) );

			$this->service->create_schedule( array(
				'table_id' => 11,
				'source_type' => 'mysql',
				'frequency' => 'daily',
				'config' => array(),
				'active' => false,
			) );

			// Get statistics
			$stats = $this->service->get_statistics();

			$this->assert_equals( 3, $stats['total'], 'Total should be 3' );
			$this->assert_equals( 2, $stats['active'], 'Active should be 2' );
			$this->assert_equals( 1, $stats['inactive'], 'Inactive should be 1' );

			$this->pass_test( $test_name, 'Statistics calculated correctly' );
		} catch ( \Exception $e ) {
			$this->fail_test( $test_name, $e->getMessage() );
		}
	}

	/**
	 * Assert true
	 *
	 * @param bool   $condition Condition to check.
	 * @param string $message   Error message.
	 */
	private function assert_true( $condition, $message ) {
		if ( ! $condition ) {
			throw new \Exception( $message );
		}
	}

	/**
	 * Assert equals
	 *
	 * @param mixed  $expected Expected value.
	 * @param mixed  $actual   Actual value.
	 * @param string $message  Error message.
	 */
	private function assert_equals( $expected, $actual, $message ) {
		if ( $expected != $actual ) {
			throw new \Exception( "$message (Expected: $expected, Actual: $actual)" );
		}
	}

	/**
	 * Mark test as passed
	 *
	 * @param string $test_name Test name.
	 * @param string $notes     Notes.
	 */
	private function pass_test( $test_name, $notes = '' ) {
		$this->results[] = array(
			'test' => $test_name,
			'status' => 'PASS',
			'notes' => $notes,
		);
		echo "✓ PASS: $test_name - $notes\n";
	}

	/**
	 * Mark test as failed
	 *
	 * @param string $test_name Test name.
	 * @param string $error     Error message.
	 */
	private function fail_test( $test_name, $error ) {
		$this->results[] = array(
			'test' => $test_name,
			'status' => 'FAIL',
			'notes' => $error,
		);
		echo "✗ FAIL: $test_name - $error\n";
	}
}

<?php
/**
 * Performance Monitor Unit Tests
 *
 * @package ATablesCharts\Tests
 */

namespace ATablesCharts\Tests\Unit;

/**
 * Test Performance Monitor Service
 */
class PerformanceMonitorTest {

	/**
	 * Performance monitor instance
	 *
	 * @var \ATablesCharts\Performance\Services\PerformanceMonitor
	 */
	private $monitor;

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
		require_once ATABLES_PLUGIN_DIR . 'src/modules/performance/index.php';
		$this->monitor = new \ATablesCharts\Performance\Services\PerformanceMonitor();

		// Clear existing metrics
		$this->monitor->clear_metrics();
	}

	/**
	 * Run all tests
	 *
	 * @return array Test results.
	 */
	public function run_all_tests() {
		$this->setUp();

		echo "\n=== Performance Monitor Unit Tests ===\n\n";

		$this->test_start_stop_timer();
		$this->test_record_metric();
		$this->test_slow_operation_detection();
		$this->test_statistics_generation();
		$this->test_recommendations();
		$this->test_clear_metrics();
		$this->test_operations_by_type();

		return $this->results;
	}

	/**
	 * Test: Start and stop timer
	 */
	private function test_start_stop_timer() {
		$test_name = 'Start/Stop Timer';

		try {
			// Start timer
			$timer_id = $this->monitor->start_timer( 'test_operation', array(
				'test_context' => 'value',
			) );

			$this->assert_true( ! empty( $timer_id ), 'Timer ID should not be empty' );

			// Simulate work
			usleep( 100000 ); // 100ms

			// Stop timer
			$metrics = $this->monitor->stop_timer( $timer_id );

			$this->assert_true( is_array( $metrics ), 'Metrics should be array' );
			$this->assert_true( isset( $metrics['operation'] ), 'Should have operation' );
			$this->assert_true( isset( $metrics['duration'] ), 'Should have duration' );
			$this->assert_true( isset( $metrics['memory'] ), 'Should have memory' );
			$this->assert_true( isset( $metrics['queries'] ), 'Should have queries' );
			$this->assert_true( $metrics['duration'] >= 100, 'Duration should be >= 100ms' );

			$this->pass_test( $test_name, 'Timer tracked operation correctly' );
		} catch ( \Exception $e ) {
			$this->fail_test( $test_name, $e->getMessage() );
		}
	}

	/**
	 * Test: Record metric directly
	 */
	private function test_record_metric() {
		$test_name = 'Record Metric';

		try {
			$this->monitor->record_metric( 'direct_operation', 250.50, array(
				'table_id' => 123,
			) );

			$stats = $this->monitor->get_statistics( array( 'operation' => 'direct_operation' ) );

			$this->assert_true( $stats['count'] > 0, 'Should have recorded metric' );
			$this->assert_equals( 250.50, $stats['avg_duration'], 'Duration should match' );

			$this->pass_test( $test_name, 'Metric recorded correctly' );
		} catch ( \Exception $e ) {
			$this->fail_test( $test_name, $e->getMessage() );
		}
	}

	/**
	 * Test: Slow operation detection
	 */
	private function test_slow_operation_detection() {
		$test_name = 'Slow Operation Detection';

		try {
			// Record slow operation (>1000ms)
			$this->monitor->record_metric( 'slow_op', 1500, array() );

			// Record fast operation
			$this->monitor->record_metric( 'fast_op', 100, array() );

			$slow_ops = $this->monitor->get_slow_operations( 10 );

			$this->assert_true( count( $slow_ops ) > 0, 'Should detect slow operations' );

			$found_slow = false;
			foreach ( $slow_ops as $op ) {
				if ( $op['operation'] === 'slow_op' && $op['is_slow'] === true ) {
					$found_slow = true;
					break;
				}
			}

			$this->assert_true( $found_slow, 'Slow operation should be marked as slow' );

			$this->pass_test( $test_name, 'Slow operations detected correctly' );
		} catch ( \Exception $e ) {
			$this->fail_test( $test_name, $e->getMessage() );
		}
	}

	/**
	 * Test: Statistics generation
	 */
	private function test_statistics_generation() {
		$test_name = 'Statistics Generation';

		try {
			// Clear previous metrics
			$this->monitor->clear_metrics();

			// Record multiple operations
			$durations = array( 100, 200, 300, 400, 500 );
			foreach ( $durations as $duration ) {
				$this->monitor->record_metric( 'stats_test', $duration, array() );
			}

			$stats = $this->monitor->get_statistics( array( 'operation' => 'stats_test' ) );

			$this->assert_equals( 5, $stats['count'], 'Should have 5 operations' );
			$this->assert_equals( 300, $stats['avg_duration'], 'Average should be 300' );
			$this->assert_equals( 500, $stats['max_duration'], 'Max should be 500' );
			$this->assert_equals( 100, $stats['min_duration'], 'Min should be 100' );

			$this->pass_test( $test_name, 'Statistics calculated correctly' );
		} catch ( \Exception $e ) {
			$this->fail_test( $test_name, $e->getMessage() );
		}
	}

	/**
	 * Test: Recommendations generation
	 */
	private function test_recommendations() {
		$test_name = 'Recommendations Generation';

		try {
			// Clear metrics
			$this->monitor->clear_metrics();

			// Record operations to trigger recommendations
			// High average time
			for ( $i = 0; $i < 10; $i++ ) {
				$this->monitor->record_metric( 'high_avg_op', 600, array() );
			}

			// High slow percentage
			for ( $i = 0; $i < 10; $i++ ) {
				$this->monitor->record_metric( 'slow_ops', 1500, array() );
			}

			$recommendations = $this->monitor->get_recommendations();

			$this->assert_true( is_array( $recommendations ), 'Should return array' );
			$this->assert_true( count( $recommendations ) > 0, 'Should have recommendations' );

			// Check for specific recommendations
			$has_slow_ops_warning = false;
			foreach ( $recommendations as $rec ) {
				if ( strpos( $rec['title'], 'Slow' ) !== false ) {
					$has_slow_ops_warning = true;
					break;
				}
			}

			$this->assert_true( $has_slow_ops_warning, 'Should warn about slow operations' );

			$this->pass_test( $test_name, 'Recommendations generated correctly' );
		} catch ( \Exception $e ) {
			$this->fail_test( $test_name, $e->getMessage() );
		}
	}

	/**
	 * Test: Clear metrics
	 */
	private function test_clear_metrics() {
		$test_name = 'Clear Metrics';

		try {
			// Record some metrics
			$this->monitor->record_metric( 'test_op', 100, array() );

			// Clear metrics
			$result = $this->monitor->clear_metrics();

			$this->assert_true( $result, 'Clear should return true' );

			// Verify metrics cleared
			$stats = $this->monitor->get_statistics();
			$this->assert_equals( 0, $stats['count'], 'Count should be 0 after clear' );

			$this->pass_test( $test_name, 'Metrics cleared successfully' );
		} catch ( \Exception $e ) {
			$this->fail_test( $test_name, $e->getMessage() );
		}
	}

	/**
	 * Test: Operations by type
	 */
	private function test_operations_by_type() {
		$test_name = 'Operations By Type';

		try {
			// Clear metrics
			$this->monitor->clear_metrics();

			// Record different operation types
			$this->monitor->record_metric( 'table_render', 200, array() );
			$this->monitor->record_metric( 'table_render', 300, array() );
			$this->monitor->record_metric( 'export', 500, array() );
			$this->monitor->record_metric( 'export', 600, array() );
			$this->monitor->record_metric( 'export', 700, array() );

			$operations = $this->monitor->get_operations_by_type();

			$this->assert_true( isset( $operations['table_render'] ), 'Should have table_render' );
			$this->assert_true( isset( $operations['export'] ), 'Should have export' );
			$this->assert_equals( 2, $operations['table_render']['count'], 'table_render count should be 2' );
			$this->assert_equals( 3, $operations['export']['count'], 'export count should be 3' );
			$this->assert_equals( 250, $operations['table_render']['avg_duration'], 'table_render avg should be 250' );
			$this->assert_equals( 600, $operations['export']['avg_duration'], 'export avg should be 600' );

			$this->pass_test( $test_name, 'Operations grouped correctly by type' );
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

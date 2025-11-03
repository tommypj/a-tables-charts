<?php
/**
 * Master Test Runner
 *
 * Executes all test suites and generates comprehensive report.
 *
 * Usage: php tests/run-all-tests.php
 *
 * @package ATablesCharts\Tests
 */

// Bootstrap WordPress
define( 'ABSPATH', dirname( __FILE__, 2 ) . '/../../../' );
define( 'ATABLES_PLUGIN_DIR', dirname( __FILE__, 2 ) . '/' );
define( 'ATABLES_VERSION', '1.0.0' );
define( 'ATABLES_SLUG', 'a-tables-charts' );

// Check if running in WordPress context
if ( ! file_exists( ABSPATH . 'wp-load.php' ) ) {
	echo "Error: WordPress not found. Please run this from WordPress root or adjust ABSPATH.\n";
	exit( 1 );
}

require_once ABSPATH . 'wp-load.php';
require_once ATABLES_PLUGIN_DIR . 'src/modules/core/Plugin.php';

// Import test classes
require_once __DIR__ . '/unit/PerformanceMonitorTest.php';
require_once __DIR__ . '/unit/ScheduleServiceTest.php';

use ATablesCharts\Tests\Unit\PerformanceMonitorTest;
use ATablesCharts\Tests\Unit\ScheduleServiceTest;

/**
 * Master Test Runner Class
 */
class MasterTestRunner {

	/**
	 * All test results
	 *
	 * @var array
	 */
	private $all_results = array();

	/**
	 * Start time
	 *
	 * @var float
	 */
	private $start_time;

	/**
	 * Run all tests
	 */
	public function run() {
		$this->start_time = microtime( true );

		echo "\n";
		echo "╔════════════════════════════════════════════════════════════╗\n";
		echo "║  A-Tables & Charts - Comprehensive Test Suite             ║\n";
		echo "╚════════════════════════════════════════════════════════════╝\n";
		echo "\n";
		echo "Date: " . date( 'Y-m-d H:i:s' ) . "\n";
		echo "Environment: WordPress " . get_bloginfo( 'version' ) . " | PHP " . PHP_VERSION . "\n";
		echo "\n";

		// Run unit tests
		$this->run_unit_tests();

		// Generate report
		$this->generate_report();

		// Save results to file
		$this->save_results();
	}

	/**
	 * Run unit tests
	 */
	private function run_unit_tests() {
		echo "┌────────────────────────────────────────────────────────────┐\n";
		echo "│  UNIT TESTS                                                │\n";
		echo "└────────────────────────────────────────────────────────────┘\n";

		// Performance Monitor Tests
		$perf_test = new PerformanceMonitorTest();
		$perf_results = $perf_test->run_all_tests();
		$this->all_results['unit']['PerformanceMonitor'] = $perf_results;

		echo "\n";

		// Schedule Service Tests
		$schedule_test = new ScheduleServiceTest();
		$schedule_results = $schedule_test->run_all_tests();
		$this->all_results['unit']['ScheduleService'] = $schedule_results;

		echo "\n";
	}

	/**
	 * Generate comprehensive report
	 */
	private function generate_report() {
		$total_tests = 0;
		$passed_tests = 0;
		$failed_tests = 0;
		$execution_time = microtime( true ) - $this->start_time;

		echo "\n";
		echo "╔════════════════════════════════════════════════════════════╗\n";
		echo "║  TEST EXECUTION SUMMARY                                    ║\n";
		echo "╚════════════════════════════════════════════════════════════╝\n";
		echo "\n";

		// Count results
		foreach ( $this->all_results as $category => $suites ) {
			echo "Category: " . strtoupper( $category ) . "\n";
			echo str_repeat( '-', 60 ) . "\n";

			foreach ( $suites as $suite_name => $results ) {
				echo "\n  Suite: $suite_name\n";

				foreach ( $results as $result ) {
					$total_tests++;

					$status_symbol = $result['status'] === 'PASS' ? '✓' : '✗';
					$status_color = $result['status'] === 'PASS' ? "\033[0;32m" : "\033[0;31m";
					$reset_color = "\033[0m";

					echo "    {$status_color}{$status_symbol}{$reset_color} {$result['test']}: {$result['notes']}\n";

					if ( $result['status'] === 'PASS' ) {
						$passed_tests++;
					} else {
						$failed_tests++;
					}
				}
			}

			echo "\n";
		}

		// Overall statistics
		$pass_rate = $total_tests > 0 ? ( $passed_tests / $total_tests ) * 100 : 0;

		echo "╔════════════════════════════════════════════════════════════╗\n";
		echo "║  OVERALL STATISTICS                                        ║\n";
		echo "╚════════════════════════════════════════════════════════════╝\n";
		echo "\n";
		echo "  Total Tests:       $total_tests\n";
		echo "  Passed:            \033[0;32m$passed_tests\033[0m (" . number_format( $pass_rate, 1 ) . "%)\n";
		echo "  Failed:            \033[0;31m$failed_tests\033[0m\n";
		echo "  Execution Time:    " . number_format( $execution_time, 2 ) . " seconds\n";
		echo "\n";

		if ( $failed_tests === 0 ) {
			echo "\033[0;32m";
			echo "╔════════════════════════════════════════════════════════════╗\n";
			echo "║  ✓ ALL TESTS PASSED!                                       ║\n";
			echo "╚════════════════════════════════════════════════════════════╝\n";
			echo "\033[0m";
		} else {
			echo "\033[0;31m";
			echo "╔════════════════════════════════════════════════════════════╗\n";
			echo "║  ✗ SOME TESTS FAILED                                       ║\n";
			echo "╚════════════════════════════════════════════════════════════╝\n";
			echo "\033[0m";
		}

		echo "\n";
	}

	/**
	 * Save results to file
	 */
	private function save_results() {
		$results_dir = __DIR__ . '/results';
		if ( ! file_exists( $results_dir ) ) {
			mkdir( $results_dir, 0755, true );
		}

		$timestamp = date( 'Y-m-d_H-i-s' );
		$results_file = $results_dir . "/test-results-{$timestamp}.json";

		$report = array(
			'timestamp' => time(),
			'date' => date( 'Y-m-d H:i:s' ),
			'environment' => array(
				'wordpress' => get_bloginfo( 'version' ),
				'php' => PHP_VERSION,
				'plugin_version' => ATABLES_VERSION,
			),
			'results' => $this->all_results,
			'summary' => $this->calculate_summary(),
		);

		file_put_contents( $results_file, json_encode( $report, JSON_PRETTY_PRINT ) );

		echo "Results saved to: $results_file\n\n";
	}

	/**
	 * Calculate summary statistics
	 *
	 * @return array Summary.
	 */
	private function calculate_summary() {
		$total = 0;
		$passed = 0;
		$failed = 0;

		foreach ( $this->all_results as $category => $suites ) {
			foreach ( $suites as $suite_name => $results ) {
				foreach ( $results as $result ) {
					$total++;
					if ( $result['status'] === 'PASS' ) {
						$passed++;
					} else {
						$failed++;
					}
				}
			}
		}

		return array(
			'total' => $total,
			'passed' => $passed,
			'failed' => $failed,
			'pass_rate' => $total > 0 ? ( $passed / $total ) * 100 : 0,
		);
	}
}

// Run tests
$runner = new MasterTestRunner();
$runner->run();

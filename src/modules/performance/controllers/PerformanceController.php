<?php
/**
 * Performance Controller
 *
 * Handles AJAX requests for performance monitoring.
 *
 * @package ATablesCharts\Performance
 * @since 1.0.0
 */

namespace ATablesCharts\Performance\Controllers;

use ATablesCharts\Performance\Services\PerformanceMonitor;
use ATablesCharts\Shared\Utils\Logger;

/**
 * Performance Controller
 */
class PerformanceController {

	/**
	 * Performance monitor instance
	 *
	 * @var PerformanceMonitor
	 */
	private $monitor;

	/**
	 * Logger instance
	 *
	 * @var Logger
	 */
	private $logger;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->monitor = new PerformanceMonitor();
		$this->logger = new Logger();
	}

	/**
	 * Register hooks
	 */
	public function register_hooks() {
		// AJAX endpoints
		add_action( 'wp_ajax_atables_get_performance_stats', array( $this, 'ajax_get_stats' ) );
		add_action( 'wp_ajax_atables_get_slow_operations', array( $this, 'ajax_get_slow_operations' ) );
		add_action( 'wp_ajax_atables_get_performance_recommendations', array( $this, 'ajax_get_recommendations' ) );
		add_action( 'wp_ajax_atables_clear_performance_metrics', array( $this, 'ajax_clear_metrics' ) );

		// Hook into table operations to track performance
		add_action( 'atables_before_table_render', array( $this, 'start_table_render_timer' ), 10, 1 );
		add_action( 'atables_after_table_render', array( $this, 'stop_table_render_timer' ), 10, 2 );

		add_action( 'atables_before_table_export', array( $this, 'start_export_timer' ), 10, 2 );
		add_action( 'atables_after_table_export', array( $this, 'stop_export_timer' ), 10, 3 );

		add_action( 'atables_before_scheduled_refresh', array( $this, 'start_refresh_timer' ), 10, 2 );
		add_action( 'atables_after_scheduled_refresh', array( $this, 'stop_refresh_timer' ), 10, 2 );
	}

	/**
	 * Get performance statistics
	 */
	public function ajax_get_stats() {
		check_ajax_referer( 'atables_admin_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => 'Unauthorized' ) );
		}

		$period = isset( $_POST['period'] ) ? sanitize_text_field( $_POST['period'] ) : 'day';
		$operation = isset( $_POST['operation'] ) ? sanitize_text_field( $_POST['operation'] ) : null;

		$stats = $this->monitor->get_statistics( array(
			'period' => $period,
			'operation' => $operation,
		) );

		$operations_by_type = $this->monitor->get_operations_by_type();

		wp_send_json_success( array(
			'stats' => $stats,
			'operations' => $operations_by_type,
		) );
	}

	/**
	 * Get slow operations
	 */
	public function ajax_get_slow_operations() {
		check_ajax_referer( 'atables_admin_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => 'Unauthorized' ) );
		}

		$limit = isset( $_POST['limit'] ) ? intval( $_POST['limit'] ) : 10;

		$slow_operations = $this->monitor->get_slow_operations( $limit );

		wp_send_json_success( array(
			'operations' => $slow_operations,
		) );
	}

	/**
	 * Get performance recommendations
	 */
	public function ajax_get_recommendations() {
		check_ajax_referer( 'atables_admin_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => 'Unauthorized' ) );
		}

		$recommendations = $this->monitor->get_recommendations();

		wp_send_json_success( array(
			'recommendations' => $recommendations,
		) );
	}

	/**
	 * Clear performance metrics
	 */
	public function ajax_clear_metrics() {
		check_ajax_referer( 'atables_admin_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => 'Unauthorized' ) );
		}

		$result = $this->monitor->clear_metrics();

		if ( $result ) {
			wp_send_json_success( array( 'message' => 'Performance metrics cleared successfully.' ) );
		} else {
			wp_send_json_error( array( 'message' => 'Failed to clear performance metrics.' ) );
		}
	}

	/**
	 * Start table render timer
	 *
	 * @param int $table_id Table ID.
	 */
	public function start_table_render_timer( $table_id ) {
		$timer_id = $this->monitor->start_timer( 'table_render', array(
			'table_id' => $table_id,
		) );

		// Store timer ID globally for later retrieval
		$GLOBALS['atables_render_timer_' . $table_id] = $timer_id;
	}

	/**
	 * Stop table render timer
	 *
	 * @param int   $table_id Table ID.
	 * @param array $result Render result.
	 */
	public function stop_table_render_timer( $table_id, $result ) {
		$timer_id = isset( $GLOBALS['atables_render_timer_' . $table_id] )
			? $GLOBALS['atables_render_timer_' . $table_id]
			: null;

		if ( $timer_id ) {
			$this->monitor->stop_timer( $timer_id );
			unset( $GLOBALS['atables_render_timer_' . $table_id] );
		}
	}

	/**
	 * Start export timer
	 *
	 * @param int    $table_id Table ID.
	 * @param string $format Export format.
	 */
	public function start_export_timer( $table_id, $format ) {
		$timer_id = $this->monitor->start_timer( 'export', array(
			'table_id' => $table_id,
			'format' => $format,
		) );

		$GLOBALS['atables_export_timer_' . $table_id] = $timer_id;
	}

	/**
	 * Stop export timer
	 *
	 * @param int    $table_id Table ID.
	 * @param string $format Export format.
	 * @param array  $result Export result.
	 */
	public function stop_export_timer( $table_id, $format, $result ) {
		$timer_id = isset( $GLOBALS['atables_export_timer_' . $table_id] )
			? $GLOBALS['atables_export_timer_' . $table_id]
			: null;

		if ( $timer_id ) {
			$this->monitor->stop_timer( $timer_id );
			unset( $GLOBALS['atables_export_timer_' . $table_id] );
		}
	}

	/**
	 * Start scheduled refresh timer
	 *
	 * @param int   $table_id Table ID.
	 * @param array $schedule Schedule data.
	 */
	public function start_refresh_timer( $table_id, $schedule ) {
		$timer_id = $this->monitor->start_timer( 'scheduled_refresh', array(
			'table_id' => $table_id,
			'schedule_id' => $schedule['id'],
			'source_type' => $schedule['source_type'],
		) );

		$GLOBALS['atables_refresh_timer_' . $schedule['id']] = $timer_id;
	}

	/**
	 * Stop scheduled refresh timer
	 *
	 * @param int   $table_id Table ID.
	 * @param array $result Refresh result.
	 */
	public function stop_refresh_timer( $table_id, $result ) {
		// Find timer ID (we don't have schedule ID here, so search)
		foreach ( $GLOBALS as $key => $value ) {
			if ( strpos( $key, 'atables_refresh_timer_' ) === 0 && is_string( $value ) ) {
				$this->monitor->stop_timer( $value );
				unset( $GLOBALS[ $key ] );
				break;
			}
		}
	}
}

<?php
/**
 * Cron Controller
 *
 * Manages scheduled data refresh tasks using WP-Cron.
 * Supports automatic table updates from MySQL, Google Sheets, CSV URLs, and other sources.
 *
 * @package ATablesCharts\Cron\Controllers
 * @since 1.0.0
 */

namespace ATablesCharts\Cron\Controllers;

use ATablesCharts\Cron\Services\ScheduleService;
use ATablesCharts\Cron\Services\RefreshService;
use ATablesCharts\Shared\Utils\Logger;

/**
 * CronController Class
 *
 * Responsibilities:
 * - Register WP-Cron events
 * - Schedule/unschedule refresh tasks
 * - Execute scheduled refreshes
 * - Handle cron errors and logging
 */
class CronController {

	/**
	 * Schedule service instance
	 *
	 * @var ScheduleService
	 */
	private $schedule_service;

	/**
	 * Refresh service instance
	 *
	 * @var RefreshService
	 */
	private $refresh_service;

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
		$this->schedule_service = new ScheduleService();
		$this->refresh_service  = new RefreshService();
		$this->logger           = new Logger();
	}

	/**
	 * Register hooks
	 */
	public function register_hooks() {
		// Register custom cron schedules
		add_filter( 'cron_schedules', array( $this, 'add_custom_schedules' ) );

		// Register cron event handler
		add_action( 'atables_scheduled_refresh', array( $this, 'execute_scheduled_refresh' ), 10, 1 );

		// Admin AJAX endpoints
		add_action( 'wp_ajax_atables_create_schedule', array( $this, 'ajax_create_schedule' ) );
		add_action( 'wp_ajax_atables_update_schedule', array( $this, 'ajax_update_schedule' ) );
		add_action( 'wp_ajax_atables_delete_schedule', array( $this, 'ajax_delete_schedule' ) );
		add_action( 'wp_ajax_atables_trigger_refresh', array( $this, 'ajax_trigger_refresh' ) );
		add_action( 'wp_ajax_atables_get_schedules', array( $this, 'ajax_get_schedules' ) );

		// Cleanup on plugin deactivation
		register_deactivation_hook( \ATABLES_PLUGIN_FILE, array( $this, 'clear_all_schedules' ) );
	}

	/**
	 * Add custom cron schedules
	 *
	 * @param array $schedules Existing schedules.
	 * @return array Modified schedules
	 */
	public function add_custom_schedules( $schedules ) {
		// Every 15 minutes
		$schedules['every_15_minutes'] = array(
			'interval' => 15 * MINUTE_IN_SECONDS,
			'display'  => __( 'Every 15 Minutes', 'a-tables-charts' ),
		);

		// Every 30 minutes
		$schedules['every_30_minutes'] = array(
			'interval' => 30 * MINUTE_IN_SECONDS,
			'display'  => __( 'Every 30 Minutes', 'a-tables-charts' ),
		);

		// Every 2 hours
		$schedules['every_2_hours'] = array(
			'interval' => 2 * HOUR_IN_SECONDS,
			'display'  => __( 'Every 2 Hours', 'a-tables-charts' ),
		);

		// Every 6 hours
		$schedules['every_6_hours'] = array(
			'interval' => 6 * HOUR_IN_SECONDS,
			'display'  => __( 'Every 6 Hours', 'a-tables-charts' ),
		);

		// Every 12 hours
		$schedules['every_12_hours'] = array(
			'interval' => 12 * HOUR_IN_SECONDS,
			'display'  => __( 'Every 12 Hours', 'a-tables-charts' ),
		);

		return $schedules;
	}

	/**
	 * Execute scheduled refresh
	 *
	 * @param int $schedule_id Schedule ID.
	 */
	public function execute_scheduled_refresh( $schedule_id ) {
		$this->logger->info( 'Starting scheduled refresh', array( 'schedule_id' => $schedule_id ) );

		try {
			// Get schedule details
			$schedule = $this->schedule_service->get_schedule( $schedule_id );

			if ( ! $schedule ) {
				throw new \Exception( 'Schedule not found: ' . $schedule_id );
			}

			// Check if schedule is active
			if ( ! $schedule['active'] ) {
				$this->logger->info( 'Schedule is not active', array( 'schedule_id' => $schedule_id ) );
				return;
			}

			// Execute refresh
			$result = $this->refresh_service->refresh_table( $schedule );

			// Log result
			if ( $result['success'] ) {
				$this->logger->info( 'Scheduled refresh completed successfully', array(
					'schedule_id' => $schedule_id,
					'table_id'    => $schedule['table_id'],
					'rows'        => $result['rows'] ?? 0,
				) );

				// Update last run timestamp
				$this->schedule_service->update_last_run( $schedule_id, time(), 'success' );

				/**
				 * Action triggered after successful scheduled refresh
				 *
				 * @since 1.0.0
				 * @param int   $schedule_id Schedule ID
				 * @param array $result      Refresh result
				 */
				do_action( 'atables_after_scheduled_refresh_success', $schedule_id, $result );
			} else {
				$this->logger->error( 'Scheduled refresh failed', array(
					'schedule_id' => $schedule_id,
					'error'       => $result['message'] ?? 'Unknown error',
				) );

				// Update last run with error
				$this->schedule_service->update_last_run( $schedule_id, time(), 'error', $result['message'] ?? '' );

				/**
				 * Action triggered after failed scheduled refresh
				 *
				 * @since 1.0.0
				 * @param int   $schedule_id Schedule ID
				 * @param array $result      Refresh result with error
				 */
				do_action( 'atables_after_scheduled_refresh_failure', $schedule_id, $result );
			}

		} catch ( \Exception $e ) {
			$this->logger->error( 'Scheduled refresh exception', array(
				'schedule_id' => $schedule_id,
				'exception'   => $e->getMessage(),
			) );

			$this->schedule_service->update_last_run( $schedule_id, time(), 'error', $e->getMessage() );
		}
	}

	/**
	 * AJAX: Create new schedule
	 */
	public function ajax_create_schedule() {
		// Verify nonce
		if ( ! check_ajax_referer( 'atables_admin_nonce', 'nonce', false ) ) {
			wp_send_json_error( array( 'message' => __( 'Security check failed.', 'a-tables-charts' ) ), 403 );
		}

		// Check permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'a-tables-charts' ) ), 403 );
		}

		// Get and validate input
		$table_id   = isset( $_POST['table_id'] ) ? intval( $_POST['table_id'] ) : 0;
		$source_type = isset( $_POST['source_type'] ) ? sanitize_text_field( $_POST['source_type'] ) : '';
		$frequency  = isset( $_POST['frequency'] ) ? sanitize_text_field( $_POST['frequency'] ) : '';
		$config     = isset( $_POST['config'] ) ? $_POST['config'] : array();

		// Validate required fields
		if ( empty( $table_id ) || empty( $source_type ) || empty( $frequency ) ) {
			wp_send_json_error( array( 'message' => __( 'Missing required fields.', 'a-tables-charts' ) ), 400 );
		}

		// Sanitize config
		$config = $this->sanitize_config( $config, $source_type );

		// Create schedule
		$schedule_id = $this->schedule_service->create_schedule( array(
			'table_id'    => $table_id,
			'source_type' => $source_type,
			'frequency'   => $frequency,
			'config'      => $config,
			'active'      => true,
		) );

		if ( $schedule_id ) {
			// Schedule first cron event
			$this->schedule_cron_event( $schedule_id, $frequency );

			wp_send_json_success( array(
				'schedule_id' => $schedule_id,
				'message'     => __( 'Schedule created successfully.', 'a-tables-charts' ),
			) );
		} else {
			wp_send_json_error( array( 'message' => __( 'Failed to create schedule.', 'a-tables-charts' ) ), 500 );
		}
	}

	/**
	 * AJAX: Update existing schedule
	 */
	public function ajax_update_schedule() {
		// Verify nonce
		if ( ! check_ajax_referer( 'atables_admin_nonce', 'nonce', false ) ) {
			wp_send_json_error( array( 'message' => __( 'Security check failed.', 'a-tables-charts' ) ), 403 );
		}

		// Check permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'a-tables-charts' ) ), 403 );
		}

		// Get schedule ID
		$schedule_id = isset( $_POST['schedule_id'] ) ? intval( $_POST['schedule_id'] ) : 0;

		if ( empty( $schedule_id ) ) {
			wp_send_json_error( array( 'message' => __( 'Schedule ID required.', 'a-tables-charts' ) ), 400 );
		}

		// Get update data
		$updates = array();

		if ( isset( $_POST['frequency'] ) ) {
			$updates['frequency'] = sanitize_text_field( $_POST['frequency'] );

			// Reschedule cron event with new frequency
			$this->unschedule_cron_event( $schedule_id );
			$this->schedule_cron_event( $schedule_id, $updates['frequency'] );
		}

		if ( isset( $_POST['active'] ) ) {
			$updates['active'] = (bool) $_POST['active'];

			// If deactivating, unschedule cron
			if ( ! $updates['active'] ) {
				$this->unschedule_cron_event( $schedule_id );
			} else {
				// If activating, schedule cron
				$schedule = $this->schedule_service->get_schedule( $schedule_id );
				$this->schedule_cron_event( $schedule_id, $schedule['frequency'] );
			}
		}

		if ( isset( $_POST['config'] ) ) {
			$schedule = $this->schedule_service->get_schedule( $schedule_id );
			$updates['config'] = $this->sanitize_config( $_POST['config'], $schedule['source_type'] );
		}

		// Update schedule
		$result = $this->schedule_service->update_schedule( $schedule_id, $updates );

		if ( $result ) {
			wp_send_json_success( array(
				'message' => __( 'Schedule updated successfully.', 'a-tables-charts' ),
			) );
		} else {
			wp_send_json_error( array( 'message' => __( 'Failed to update schedule.', 'a-tables-charts' ) ), 500 );
		}
	}

	/**
	 * AJAX: Delete schedule
	 */
	public function ajax_delete_schedule() {
		// Verify nonce
		if ( ! check_ajax_referer( 'atables_admin_nonce', 'nonce', false ) ) {
			wp_send_json_error( array( 'message' => __( 'Security check failed.', 'a-tables-charts' ) ), 403 );
		}

		// Check permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'a-tables-charts' ) ), 403 );
		}

		// Get schedule ID
		$schedule_id = isset( $_POST['schedule_id'] ) ? intval( $_POST['schedule_id'] ) : 0;

		if ( empty( $schedule_id ) ) {
			wp_send_json_error( array( 'message' => __( 'Schedule ID required.', 'a-tables-charts' ) ), 400 );
		}

		// Unschedule cron event
		$this->unschedule_cron_event( $schedule_id );

		// Delete schedule
		$result = $this->schedule_service->delete_schedule( $schedule_id );

		if ( $result ) {
			wp_send_json_success( array(
				'message' => __( 'Schedule deleted successfully.', 'a-tables-charts' ),
			) );
		} else {
			wp_send_json_error( array( 'message' => __( 'Failed to delete schedule.', 'a-tables-charts' ) ), 500 );
		}
	}

	/**
	 * AJAX: Manually trigger refresh (for testing)
	 */
	public function ajax_trigger_refresh() {
		// Verify nonce
		if ( ! check_ajax_referer( 'atables_admin_nonce', 'nonce', false ) ) {
			wp_send_json_error( array( 'message' => __( 'Security check failed.', 'a-tables-charts' ) ), 403 );
		}

		// Check permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'a-tables-charts' ) ), 403 );
		}

		// Get schedule ID
		$schedule_id = isset( $_POST['schedule_id'] ) ? intval( $_POST['schedule_id'] ) : 0;

		if ( empty( $schedule_id ) ) {
			wp_send_json_error( array( 'message' => __( 'Schedule ID required.', 'a-tables-charts' ) ), 400 );
		}

		// Trigger refresh immediately
		$this->execute_scheduled_refresh( $schedule_id );

		// Get latest status
		$schedule = $this->schedule_service->get_schedule( $schedule_id );

		wp_send_json_success( array(
			'message'      => __( 'Refresh triggered successfully.', 'a-tables-charts' ),
			'last_run'     => $schedule['last_run'] ?? null,
			'last_status'  => $schedule['last_status'] ?? 'unknown',
			'last_message' => $schedule['last_message'] ?? '',
		) );
	}

	/**
	 * AJAX: Get all schedules
	 */
	public function ajax_get_schedules() {
		// Verify nonce
		if ( ! check_ajax_referer( 'atables_admin_nonce', 'nonce', false ) ) {
			wp_send_json_error( array( 'message' => __( 'Security check failed.', 'a-tables-charts' ) ), 403 );
		}

		// Check permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'a-tables-charts' ) ), 403 );
		}

		// Get all schedules
		$schedules = $this->schedule_service->get_all_schedules();

		// Add next run time for each
		foreach ( $schedules as &$schedule ) {
			$schedule['next_run'] = $this->get_next_scheduled_time( $schedule['id'] );
		}

		wp_send_json_success( array(
			'schedules' => $schedules,
		) );
	}

	/**
	 * Schedule cron event
	 *
	 * @param int    $schedule_id Schedule ID.
	 * @param string $frequency   Frequency (hourly, daily, etc.).
	 */
	private function schedule_cron_event( $schedule_id, $frequency ) {
		$hook = 'atables_scheduled_refresh';

		// Unschedule any existing events first
		$this->unschedule_cron_event( $schedule_id );

		// Schedule new event
		$timestamp = time();
		wp_schedule_event( $timestamp, $frequency, $hook, array( $schedule_id ) );

		$this->logger->info( 'Cron event scheduled', array(
			'schedule_id' => $schedule_id,
			'frequency'   => $frequency,
			'next_run'    => date( 'Y-m-d H:i:s', $timestamp ),
		) );
	}

	/**
	 * Unschedule cron event
	 *
	 * @param int $schedule_id Schedule ID.
	 */
	private function unschedule_cron_event( $schedule_id ) {
		$hook = 'atables_scheduled_refresh';

		// Get all scheduled events for this hook
		$timestamp = wp_next_scheduled( $hook, array( $schedule_id ) );

		if ( $timestamp ) {
			wp_unschedule_event( $timestamp, $hook, array( $schedule_id ) );

			$this->logger->info( 'Cron event unscheduled', array(
				'schedule_id' => $schedule_id,
			) );
		}
	}

	/**
	 * Get next scheduled time for a schedule
	 *
	 * @param int $schedule_id Schedule ID.
	 * @return int|null Timestamp or null if not scheduled
	 */
	private function get_next_scheduled_time( $schedule_id ) {
		$hook = 'atables_scheduled_refresh';
		return wp_next_scheduled( $hook, array( $schedule_id ) );
	}

	/**
	 * Clear all schedules (on deactivation)
	 */
	public function clear_all_schedules() {
		$schedules = $this->schedule_service->get_all_schedules();

		foreach ( $schedules as $schedule ) {
			$this->unschedule_cron_event( $schedule['id'] );
		}

		$this->logger->info( 'All cron schedules cleared' );
	}

	/**
	 * Sanitize configuration based on source type
	 *
	 * @param array  $config      Raw configuration.
	 * @param string $source_type Source type.
	 * @return array Sanitized configuration
	 */
	private function sanitize_config( $config, $source_type ) {
		$sanitized = array();

		switch ( $source_type ) {
			case 'mysql':
				$sanitized['query'] = isset( $config['query'] ) ? sanitize_textarea_field( $config['query'] ) : '';
				break;

			case 'google_sheets':
				$sanitized['sheet_id'] = isset( $config['sheet_id'] ) ? sanitize_text_field( $config['sheet_id'] ) : '';
				$sanitized['range']    = isset( $config['range'] ) ? sanitize_text_field( $config['range'] ) : '';
				$sanitized['api_key']  = isset( $config['api_key'] ) ? sanitize_text_field( $config['api_key'] ) : '';
				break;

			case 'csv_url':
				$sanitized['url']         = isset( $config['url'] ) ? esc_url_raw( $config['url'] ) : '';
				$sanitized['has_headers'] = isset( $config['has_headers'] ) ? (bool) $config['has_headers'] : true;
				$sanitized['delimiter']   = isset( $config['delimiter'] ) ? sanitize_text_field( $config['delimiter'] ) : ',';
				break;

			case 'rest_api':
				$sanitized['url']     = isset( $config['url'] ) ? esc_url_raw( $config['url'] ) : '';
				$sanitized['method']  = isset( $config['method'] ) ? sanitize_text_field( $config['method'] ) : 'GET';
				$sanitized['headers'] = isset( $config['headers'] ) ? array_map( 'sanitize_text_field', (array) $config['headers'] ) : array();
				break;

			default:
				/**
				 * Filter to allow custom config sanitization
				 *
				 * @since 1.0.0
				 * @param array  $sanitized   Sanitized config
				 * @param array  $config      Raw config
				 * @param string $source_type Source type
				 */
				$sanitized = apply_filters( 'atables_sanitize_cron_config', $sanitized, $config, $source_type );
				break;
		}

		return $sanitized;
	}
}

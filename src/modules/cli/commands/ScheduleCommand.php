<?php
/**
 * WP-CLI Schedule Commands
 *
 * Manages scheduled data refreshes via WP-CLI.
 *
 * @package ATablesCharts\CLI
 * @since 1.0.0
 */

namespace ATablesCharts\CLI\Commands;

use ATablesCharts\Cron\Services\ScheduleService;
use ATablesCharts\Cron\Services\RefreshService;
use ATablesCharts\Shared\Utils\Logger;

/**
 * Manage scheduled data refreshes.
 */
class ScheduleCommand {

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
		require_once ATABLES_PLUGIN_DIR . 'src/modules/cron/index.php';
		$this->schedule_service = new ScheduleService();
		$this->refresh_service = new RefreshService();
		$this->logger = new Logger();
	}

	/**
	 * Lists all scheduled refreshes.
	 *
	 * ## OPTIONS
	 *
	 * [--format=<format>]
	 * : Output format (table, json, csv, yaml, ids, count).
	 * ---
	 * default: table
	 * options:
	 *   - table
	 *   - json
	 *   - csv
	 *   - yaml
	 *   - ids
	 *   - count
	 * ---
	 *
	 * [--active]
	 * : Show only active schedules.
	 *
	 * [--inactive]
	 * : Show only inactive schedules.
	 *
	 * ## EXAMPLES
	 *
	 *     # List all schedules
	 *     $ wp atables schedule list
	 *
	 *     # List only active schedules
	 *     $ wp atables schedule list --active
	 *
	 *     # Get schedule IDs only
	 *     $ wp atables schedule list --format=ids
	 *
	 *     # Count schedules
	 *     $ wp atables schedule list --format=count
	 *
	 * @when after_wp_load
	 */
	public function list( $args, $assoc_args ) {
		$format = isset( $assoc_args['format'] ) ? $assoc_args['format'] : 'table';
		$schedules = $this->schedule_service->get_all_schedules();

		// Filter by active/inactive
		if ( isset( $assoc_args['active'] ) ) {
			$schedules = array_filter( $schedules, function( $schedule ) {
				return $schedule['active'];
			} );
		} elseif ( isset( $assoc_args['inactive'] ) ) {
			$schedules = array_filter( $schedules, function( $schedule ) {
				return ! $schedule['active'];
			} );
		}

		if ( $format === 'ids' ) {
			$ids = array_map( function( $schedule ) {
				return $schedule['id'];
			}, $schedules );
			echo implode( ' ', $ids );
			return;
		}

		if ( $format === 'count' ) {
			\WP_CLI::line( count( $schedules ) );
			return;
		}

		// Format data for display
		$items = array_map( function( $schedule ) {
			require_once ATABLES_PLUGIN_DIR . 'src/modules/tables/index.php';
			$table_service = new \ATablesCharts\Tables\Services\TableService();
			$table = $table_service->get_table( $schedule['table_id'] );

			$hook_name = 'atables_scheduled_refresh_' . $schedule['id'];
			$next_run = wp_next_scheduled( $hook_name );

			return array(
				'ID' => $schedule['id'],
				'Table ID' => $schedule['table_id'],
				'Table' => $table ? $table->title : 'Unknown',
				'Source' => $schedule['source_type'],
				'Frequency' => $schedule['frequency'],
				'Active' => $schedule['active'] ? 'Yes' : 'No',
				'Last Run' => $schedule['last_run'] ? date( 'Y-m-d H:i:s', $schedule['last_run'] ) : 'Never',
				'Status' => $schedule['last_status'] ?? 'N/A',
				'Next Run' => $next_run ? date( 'Y-m-d H:i:s', $next_run ) : 'N/A',
			);
		}, $schedules );

		\WP_CLI\Utils\format_items( $format, $items, array( 'ID', 'Table ID', 'Table', 'Source', 'Frequency', 'Active', 'Last Run', 'Status', 'Next Run' ) );
	}

	/**
	 * Gets details about a specific schedule.
	 *
	 * ## OPTIONS
	 *
	 * <id>
	 * : The schedule ID.
	 *
	 * [--format=<format>]
	 * : Output format (table, json, yaml).
	 * ---
	 * default: table
	 * options:
	 *   - table
	 *   - json
	 *   - yaml
	 * ---
	 *
	 * ## EXAMPLES
	 *
	 *     # Get schedule details
	 *     $ wp atables schedule get 1
	 *
	 *     # Output as JSON
	 *     $ wp atables schedule get 1 --format=json
	 *
	 * @when after_wp_load
	 */
	public function get( $args, $assoc_args ) {
		list( $schedule_id ) = $args;
		$format = isset( $assoc_args['format'] ) ? $assoc_args['format'] : 'table';

		$schedule = $this->schedule_service->get_schedule( $schedule_id );

		if ( ! $schedule ) {
			\WP_CLI::error( "Schedule #{$schedule_id} not found." );
		}

		$hook_name = 'atables_scheduled_refresh_' . $schedule_id;
		$next_run = wp_next_scheduled( $hook_name );

		$data = array(
			'ID' => $schedule['id'],
			'Table ID' => $schedule['table_id'],
			'Source Type' => $schedule['source_type'],
			'Frequency' => $schedule['frequency'],
			'Active' => $schedule['active'] ? 'Yes' : 'No',
			'Created' => $schedule['created_at'],
			'Last Run' => $schedule['last_run'] ? date( 'Y-m-d H:i:s', $schedule['last_run'] ) : 'Never',
			'Last Status' => $schedule['last_status'] ?? 'N/A',
			'Last Message' => $schedule['last_message'] ?? 'N/A',
			'Next Run' => $next_run ? date( 'Y-m-d H:i:s', $next_run ) : 'N/A',
		);

		\WP_CLI\Utils\format_items( $format, array( $data ), array_keys( $data ) );

		if ( $format === 'table' && ! empty( $schedule['config'] ) ) {
			\WP_CLI::line( "\nConfiguration:" );
			foreach ( $schedule['config'] as $key => $value ) {
				if ( is_array( $value ) ) {
					$value = json_encode( $value );
				}
				\WP_CLI::line( "  {$key}: {$value}" );
			}
		}
	}

	/**
	 * Activates a schedule.
	 *
	 * ## OPTIONS
	 *
	 * <id>...
	 * : One or more schedule IDs to activate.
	 *
	 * ## EXAMPLES
	 *
	 *     # Activate a schedule
	 *     $ wp atables schedule activate 1
	 *
	 *     # Activate multiple schedules
	 *     $ wp atables schedule activate 1 2 3
	 *
	 * @when after_wp_load
	 */
	public function activate( $args, $assoc_args ) {
		$activated = 0;

		foreach ( $args as $schedule_id ) {
			$result = $this->schedule_service->toggle_active( $schedule_id, true );

			if ( $result ) {
				$activated++;
				\WP_CLI::log( "Activated schedule #{$schedule_id}." );
			} else {
				\WP_CLI::warning( "Failed to activate schedule #{$schedule_id}." );
			}
		}

		\WP_CLI::success( "Activated {$activated} of " . count( $args ) . ' schedule(s).' );
	}

	/**
	 * Deactivates a schedule.
	 *
	 * ## OPTIONS
	 *
	 * <id>...
	 * : One or more schedule IDs to deactivate.
	 *
	 * ## EXAMPLES
	 *
	 *     # Deactivate a schedule
	 *     $ wp atables schedule deactivate 1
	 *
	 *     # Deactivate multiple schedules
	 *     $ wp atables schedule deactivate 1 2 3
	 *
	 * @when after_wp_load
	 */
	public function deactivate( $args, $assoc_args ) {
		$deactivated = 0;

		foreach ( $args as $schedule_id ) {
			$result = $this->schedule_service->toggle_active( $schedule_id, false );

			if ( $result ) {
				$deactivated++;
				\WP_CLI::log( "Deactivated schedule #{$schedule_id}." );
			} else {
				\WP_CLI::warning( "Failed to deactivate schedule #{$schedule_id}." );
			}
		}

		\WP_CLI::success( "Deactivated {$deactivated} of " . count( $args ) . ' schedule(s).' );
	}

	/**
	 * Triggers a scheduled refresh immediately.
	 *
	 * ## OPTIONS
	 *
	 * <id>
	 * : The schedule ID to trigger.
	 *
	 * [--verbose]
	 * : Show detailed output.
	 *
	 * ## EXAMPLES
	 *
	 *     # Trigger a refresh
	 *     $ wp atables schedule run 1
	 *
	 *     # Trigger with detailed output
	 *     $ wp atables schedule run 1 --verbose
	 *
	 * @when after_wp_load
	 */
	public function run( $args, $assoc_args ) {
		list( $schedule_id ) = $args;
		$verbose = isset( $assoc_args['verbose'] );

		$schedule = $this->schedule_service->get_schedule( $schedule_id );

		if ( ! $schedule ) {
			\WP_CLI::error( "Schedule #{$schedule_id} not found." );
		}

		if ( $verbose ) {
			\WP_CLI::line( "Running scheduled refresh #{$schedule_id}..." );
			\WP_CLI::line( "  Table ID: {$schedule['table_id']}" );
			\WP_CLI::line( "  Source: {$schedule['source_type']}" );
			\WP_CLI::line( "  Frequency: {$schedule['frequency']}" );
		}

		$result = $this->refresh_service->refresh_table( $schedule );

		if ( $result['success'] ) {
			// Update last run info
			$this->schedule_service->update_last_run(
				$schedule_id,
				time(),
				'success',
				$result['message'] ?? ''
			);

			if ( $verbose ) {
				\WP_CLI::line( "  Result: {$result['message']}" );
				if ( isset( $result['rows'] ) ) {
					\WP_CLI::line( "  Rows refreshed: {$result['rows']}" );
				}
			}

			\WP_CLI::success( "Successfully refreshed table from schedule #{$schedule_id}." );
		} else {
			// Update last run info
			$this->schedule_service->update_last_run(
				$schedule_id,
				time(),
				'error',
				$result['message'] ?? 'Unknown error'
			);

			\WP_CLI::error( "Failed to refresh: {$result['message']}" );
		}
	}

	/**
	 * Deletes one or more schedules.
	 *
	 * ## OPTIONS
	 *
	 * <id>...
	 * : One or more schedule IDs to delete.
	 *
	 * [--yes]
	 * : Skip confirmation prompt.
	 *
	 * ## EXAMPLES
	 *
	 *     # Delete a schedule
	 *     $ wp atables schedule delete 1
	 *
	 *     # Delete multiple schedules
	 *     $ wp atables schedule delete 1 2 3
	 *
	 *     # Delete without confirmation
	 *     $ wp atables schedule delete 1 --yes
	 *
	 * @when after_wp_load
	 */
	public function delete( $args, $assoc_args ) {
		if ( ! isset( $assoc_args['yes'] ) ) {
			\WP_CLI::confirm( sprintf(
				'Are you sure you want to delete %d schedule(s)?',
				count( $args )
			) );
		}

		$deleted = 0;

		foreach ( $args as $schedule_id ) {
			$result = $this->schedule_service->delete_schedule( $schedule_id );

			if ( $result ) {
				// Unschedule the cron event
				$hook_name = 'atables_scheduled_refresh_' . $schedule_id;
				wp_unschedule_event( wp_next_scheduled( $hook_name ), $hook_name, array( $schedule_id ) );

				$deleted++;
				\WP_CLI::log( "Deleted schedule #{$schedule_id}." );
			} else {
				\WP_CLI::warning( "Failed to delete schedule #{$schedule_id}." );
			}
		}

		\WP_CLI::success( "Deleted {$deleted} of " . count( $args ) . ' schedule(s).' );
	}

	/**
	 * Shows statistics about scheduled refreshes.
	 *
	 * ## OPTIONS
	 *
	 * [--format=<format>]
	 * : Output format (table, json, yaml).
	 * ---
	 * default: table
	 * options:
	 *   - table
	 *   - json
	 *   - yaml
	 * ---
	 *
	 * ## EXAMPLES
	 *
	 *     # Show schedule statistics
	 *     $ wp atables schedule stats
	 *
	 *     # Output as JSON
	 *     $ wp atables schedule stats --format=json
	 *
	 * @when after_wp_load
	 */
	public function stats( $args, $assoc_args ) {
		$format = isset( $assoc_args['format'] ) ? $assoc_args['format'] : 'table';
		$stats = $this->schedule_service->get_statistics();

		$data = array(
			'Total Schedules' => $stats['total'],
			'Active' => $stats['active'],
			'Inactive' => $stats['inactive'],
		);

		\WP_CLI\Utils\format_items( $format, array( $data ), array_keys( $data ) );
	}
}

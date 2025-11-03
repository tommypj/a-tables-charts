<?php
/**
 * Schedule Service
 *
 * Manages scheduled refresh configurations in the database.
 *
 * @package ATablesCharts\Cron\Services
 * @since 1.0.0
 */

namespace ATablesCharts\Cron\Services;

/**
 * ScheduleService Class
 *
 * Handles CRUD operations for refresh schedules.
 */
class ScheduleService {

	/**
	 * Option key for schedules
	 *
	 * @var string
	 */
	private $option_key = 'atables_refresh_schedules';

	/**
	 * Get all schedules
	 *
	 * @return array Array of schedules
	 */
	public function get_all_schedules() {
		$schedules = get_option( $this->option_key, array() );
		return is_array( $schedules ) ? $schedules : array();
	}

	/**
	 * Get schedule by ID
	 *
	 * @param int $schedule_id Schedule ID.
	 * @return array|null Schedule data or null if not found
	 */
	public function get_schedule( $schedule_id ) {
		$schedules = $this->get_all_schedules();

		foreach ( $schedules as $schedule ) {
			if ( isset( $schedule['id'] ) && $schedule['id'] === $schedule_id ) {
				return $schedule;
			}
		}

		return null;
	}

	/**
	 * Get schedules for a specific table
	 *
	 * @param int $table_id Table ID.
	 * @return array Array of schedules for the table
	 */
	public function get_schedules_for_table( $table_id ) {
		$schedules = $this->get_all_schedules();
		$result    = array();

		foreach ( $schedules as $schedule ) {
			if ( isset( $schedule['table_id'] ) && $schedule['table_id'] === $table_id ) {
				$result[] = $schedule;
			}
		}

		return $result;
	}

	/**
	 * Create new schedule
	 *
	 * @param array $data Schedule data.
	 * @return int|false Schedule ID or false on failure
	 */
	public function create_schedule( $data ) {
		$schedules = $this->get_all_schedules();

		// Generate new ID
		$max_id     = 0;
		foreach ( $schedules as $schedule ) {
			if ( isset( $schedule['id'] ) && $schedule['id'] > $max_id ) {
				$max_id = $schedule['id'];
			}
		}
		$new_id = $max_id + 1;

		// Prepare schedule data
		$schedule = array(
			'id'           => $new_id,
			'table_id'     => isset( $data['table_id'] ) ? intval( $data['table_id'] ) : 0,
			'source_type'  => isset( $data['source_type'] ) ? sanitize_text_field( $data['source_type'] ) : '',
			'frequency'    => isset( $data['frequency'] ) ? sanitize_text_field( $data['frequency'] ) : 'daily',
			'config'       => isset( $data['config'] ) ? $data['config'] : array(),
			'active'       => isset( $data['active'] ) ? (bool) $data['active'] : true,
			'created_at'   => current_time( 'mysql' ),
			'last_run'     => null,
			'last_status'  => null,
			'last_message' => null,
		);

		// Add to schedules array
		$schedules[] = $schedule;

		// Save to database
		if ( update_option( $this->option_key, $schedules ) ) {
			return $new_id;
		}

		return false;
	}

	/**
	 * Update schedule
	 *
	 * @param int   $schedule_id Schedule ID.
	 * @param array $updates     Fields to update.
	 * @return bool True on success, false on failure
	 */
	public function update_schedule( $schedule_id, $updates ) {
		$schedules = $this->get_all_schedules();
		$found     = false;

		foreach ( $schedules as $index => $schedule ) {
			if ( isset( $schedule['id'] ) && $schedule['id'] === $schedule_id ) {
				// Update fields
				foreach ( $updates as $key => $value ) {
					$schedules[ $index ][ $key ] = $value;
				}

				$schedules[ $index ]['updated_at'] = current_time( 'mysql' );
				$found = true;
				break;
			}
		}

		if ( $found ) {
			return update_option( $this->option_key, $schedules );
		}

		return false;
	}

	/**
	 * Delete schedule
	 *
	 * @param int $schedule_id Schedule ID.
	 * @return bool True on success, false on failure
	 */
	public function delete_schedule( $schedule_id ) {
		$schedules = $this->get_all_schedules();
		$new_schedules = array();

		foreach ( $schedules as $schedule ) {
			if ( ! isset( $schedule['id'] ) || $schedule['id'] !== $schedule_id ) {
				$new_schedules[] = $schedule;
			}
		}

		return update_option( $this->option_key, $new_schedules );
	}

	/**
	 * Update last run information
	 *
	 * @param int    $schedule_id Schedule ID.
	 * @param int    $timestamp   Run timestamp.
	 * @param string $status      Status (success, error).
	 * @param string $message     Optional message.
	 * @return bool True on success, false on failure
	 */
	public function update_last_run( $schedule_id, $timestamp, $status, $message = '' ) {
		return $this->update_schedule( $schedule_id, array(
			'last_run'     => $timestamp,
			'last_status'  => $status,
			'last_message' => $message,
		) );
	}

	/**
	 * Toggle schedule active status
	 *
	 * @param int  $schedule_id Schedule ID.
	 * @param bool $active      Active status.
	 * @return bool True on success, false on failure
	 */
	public function toggle_active( $schedule_id, $active ) {
		return $this->update_schedule( $schedule_id, array(
			'active' => (bool) $active,
		) );
	}

	/**
	 * Get schedule statistics
	 *
	 * @return array Statistics
	 */
	public function get_statistics() {
		$schedules = $this->get_all_schedules();

		$stats = array(
			'total'           => count( $schedules ),
			'active'          => 0,
			'inactive'        => 0,
			'last_24h'        => 0,
			'successful'      => 0,
			'failed'          => 0,
			'by_source_type'  => array(),
			'by_frequency'    => array(),
		);

		$day_ago = time() - ( 24 * HOUR_IN_SECONDS );

		foreach ( $schedules as $schedule ) {
			// Count active/inactive
			if ( ! empty( $schedule['active'] ) ) {
				$stats['active']++;
			} else {
				$stats['inactive']++;
			}

			// Count recent runs
			if ( isset( $schedule['last_run'] ) && $schedule['last_run'] > $day_ago ) {
				$stats['last_24h']++;

				// Count success/failure
				if ( isset( $schedule['last_status'] ) ) {
					if ( $schedule['last_status'] === 'success' ) {
						$stats['successful']++;
					} elseif ( $schedule['last_status'] === 'error' ) {
						$stats['failed']++;
					}
				}
			}

			// Count by source type
			$source_type = $schedule['source_type'] ?? 'unknown';
			if ( ! isset( $stats['by_source_type'][ $source_type ] ) ) {
				$stats['by_source_type'][ $source_type ] = 0;
			}
			$stats['by_source_type'][ $source_type ]++;

			// Count by frequency
			$frequency = $schedule['frequency'] ?? 'unknown';
			if ( ! isset( $stats['by_frequency'][ $frequency ] ) ) {
				$stats['by_frequency'][ $frequency ] = 0;
			}
			$stats['by_frequency'][ $frequency ]++;
		}

		return $stats;
	}
}

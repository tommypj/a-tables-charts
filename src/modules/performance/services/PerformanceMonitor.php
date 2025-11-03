<?php
/**
 * Performance Monitor Service
 *
 * Tracks and analyzes plugin performance metrics.
 *
 * @package ATablesCharts\Performance
 * @since 1.0.0
 */

namespace ATablesCharts\Performance\Services;

use ATablesCharts\Shared\Utils\Logger;

/**
 * Performance Monitor
 *
 * Collects and analyzes performance data for tables, queries, and rendering.
 */
class PerformanceMonitor {

	/**
	 * Logger instance
	 *
	 * @var Logger
	 */
	private $logger;

	/**
	 * Metrics storage (transient)
	 *
	 * @var array
	 */
	private $metrics = array();

	/**
	 * Current session metrics
	 *
	 * @var array
	 */
	private $session_metrics = array();

	/**
	 * Performance thresholds (in milliseconds)
	 *
	 * @var array
	 */
	private $thresholds = array(
		'query_execution' => 1000,  // 1 second
		'table_render' => 2000,     // 2 seconds
		'export' => 5000,           // 5 seconds
		'import' => 10000,          // 10 seconds
	);

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->logger = new Logger();
		$this->load_metrics();
	}

	/**
	 * Start timing an operation
	 *
	 * @param string $operation Operation name.
	 * @param array  $context Additional context.
	 * @return string Timer ID.
	 */
	public function start_timer( $operation, $context = array() ) {
		$timer_id = uniqid( $operation . '_', true );

		$this->session_metrics[ $timer_id ] = array(
			'operation' => $operation,
			'context' => $context,
			'start_time' => microtime( true ),
			'start_memory' => memory_get_usage( true ),
			'queries_before' => $this->get_query_count(),
		);

		return $timer_id;
	}

	/**
	 * Stop timing an operation
	 *
	 * @param string $timer_id Timer ID from start_timer().
	 * @return array|false Performance metrics or false if timer not found.
	 */
	public function stop_timer( $timer_id ) {
		if ( ! isset( $this->session_metrics[ $timer_id ] ) ) {
			return false;
		}

		$timer = $this->session_metrics[ $timer_id ];
		$end_time = microtime( true );
		$end_memory = memory_get_usage( true );
		$queries_after = $this->get_query_count();

		$duration = ( $end_time - $timer['start_time'] ) * 1000; // Convert to milliseconds
		$memory_used = $end_memory - $timer['start_memory'];
		$queries_used = $queries_after - $timer['queries_before'];

		$metrics = array(
			'operation' => $timer['operation'],
			'context' => $timer['context'],
			'duration' => round( $duration, 2 ),
			'memory' => $memory_used,
			'queries' => $queries_used,
			'timestamp' => time(),
			'is_slow' => $duration > $this->get_threshold( $timer['operation'] ),
		);

		// Log slow operations
		if ( $metrics['is_slow'] ) {
			$this->logger->warning( 'Slow operation detected', $metrics );
		}

		// Store metric
		$this->store_metric( $metrics );

		// Clean up session
		unset( $this->session_metrics[ $timer_id ] );

		return $metrics;
	}

	/**
	 * Record a metric directly
	 *
	 * @param string $operation Operation name.
	 * @param float  $duration Duration in milliseconds.
	 * @param array  $context Additional context.
	 */
	public function record_metric( $operation, $duration, $context = array() ) {
		$metrics = array(
			'operation' => $operation,
			'context' => $context,
			'duration' => round( $duration, 2 ),
			'memory' => 0,
			'queries' => 0,
			'timestamp' => time(),
			'is_slow' => $duration > $this->get_threshold( $operation ),
		);

		$this->store_metric( $metrics );
	}

	/**
	 * Get performance statistics
	 *
	 * @param array $args Query arguments.
	 * @return array Statistics.
	 */
	public function get_statistics( $args = array() ) {
		$defaults = array(
			'operation' => null,
			'period' => 'day', // hour, day, week, month
			'limit' => 100,
		);

		$args = wp_parse_args( $args, $defaults );

		// Get metrics from transient
		$all_metrics = get_transient( 'atables_performance_metrics' );
		if ( ! $all_metrics ) {
			$all_metrics = array();
		}

		// Filter by operation
		if ( $args['operation'] ) {
			$all_metrics = array_filter( $all_metrics, function( $metric ) use ( $args ) {
				return $metric['operation'] === $args['operation'];
			} );
		}

		// Filter by period
		$since = $this->get_period_timestamp( $args['period'] );
		$all_metrics = array_filter( $all_metrics, function( $metric ) use ( $since ) {
			return $metric['timestamp'] >= $since;
		} );

		// Limit results
		$all_metrics = array_slice( $all_metrics, -$args['limit'] );

		if ( empty( $all_metrics ) ) {
			return array(
				'count' => 0,
				'avg_duration' => 0,
				'max_duration' => 0,
				'min_duration' => 0,
				'avg_memory' => 0,
				'avg_queries' => 0,
				'slow_operations' => 0,
			);
		}

		// Calculate statistics
		$durations = array_column( $all_metrics, 'duration' );
		$memory = array_column( $all_metrics, 'memory' );
		$queries = array_column( $all_metrics, 'queries' );
		$slow_count = count( array_filter( $all_metrics, function( $m ) {
			return $m['is_slow'];
		} ) );

		return array(
			'count' => count( $all_metrics ),
			'avg_duration' => round( array_sum( $durations ) / count( $durations ), 2 ),
			'max_duration' => round( max( $durations ), 2 ),
			'min_duration' => round( min( $durations ), 2 ),
			'avg_memory' => round( array_sum( $memory ) / count( $memory ) ),
			'avg_queries' => round( array_sum( $queries ) / count( $queries ), 1 ),
			'slow_operations' => $slow_count,
			'slow_percentage' => round( ( $slow_count / count( $all_metrics ) ) * 100, 1 ),
		);
	}

	/**
	 * Get slow operations
	 *
	 * @param int $limit Number of results to return.
	 * @return array Slow operations.
	 */
	public function get_slow_operations( $limit = 10 ) {
		$all_metrics = get_transient( 'atables_performance_metrics' );
		if ( ! $all_metrics ) {
			return array();
		}

		// Filter slow operations
		$slow = array_filter( $all_metrics, function( $metric ) {
			return $metric['is_slow'];
		} );

		// Sort by duration (slowest first)
		usort( $slow, function( $a, $b ) {
			return $b['duration'] - $a['duration'];
		} );

		return array_slice( $slow, 0, $limit );
	}

	/**
	 * Get operations by type
	 *
	 * @return array Operation counts grouped by type.
	 */
	public function get_operations_by_type() {
		$all_metrics = get_transient( 'atables_performance_metrics' );
		if ( ! $all_metrics ) {
			return array();
		}

		$operations = array();

		foreach ( $all_metrics as $metric ) {
			$op = $metric['operation'];
			if ( ! isset( $operations[ $op ] ) ) {
				$operations[ $op ] = array(
					'count' => 0,
					'total_duration' => 0,
					'slow_count' => 0,
				);
			}

			$operations[ $op ]['count']++;
			$operations[ $op ]['total_duration'] += $metric['duration'];

			if ( $metric['is_slow'] ) {
				$operations[ $op ]['slow_count']++;
			}
		}

		// Calculate averages
		foreach ( $operations as $op => &$data ) {
			$data['avg_duration'] = round( $data['total_duration'] / $data['count'], 2 );
		}

		return $operations;
	}

	/**
	 * Get optimization recommendations
	 *
	 * @return array Recommendations.
	 */
	public function get_recommendations() {
		$recommendations = array();
		$stats = $this->get_statistics( array( 'period' => 'day' ) );
		$operations = $this->get_operations_by_type();

		// Check overall performance
		if ( $stats['avg_duration'] > 500 ) {
			$recommendations[] = array(
				'severity' => 'warning',
				'title' => 'High Average Response Time',
				'description' => sprintf(
					'Average operation time is %.2f ms. Consider optimizing slow queries and enabling caching.',
					$stats['avg_duration']
				),
			);
		}

		// Check slow operations percentage
		if ( $stats['slow_percentage'] > 10 ) {
			$recommendations[] = array(
				'severity' => 'error',
				'title' => 'Too Many Slow Operations',
				'description' => sprintf(
					'%d%% of operations are slow. Review slow operations and optimize table structure.',
					$stats['slow_percentage']
				),
			);
		}

		// Check cache effectiveness
		if ( isset( $operations['cache_miss'] ) && isset( $operations['cache_hit'] ) ) {
			$cache_total = $operations['cache_miss']['count'] + $operations['cache_hit']['count'];
			$cache_hit_rate = ( $operations['cache_hit']['count'] / $cache_total ) * 100;

			if ( $cache_hit_rate < 50 ) {
				$recommendations[] = array(
					'severity' => 'warning',
					'title' => 'Low Cache Hit Rate',
					'description' => sprintf(
						'Cache hit rate is %.1f%%. Increase cache duration or pre-warm cache.',
						$cache_hit_rate
					),
				);
			}
		}

		// Check query count
		if ( $stats['avg_queries'] > 10 ) {
			$recommendations[] = array(
				'severity' => 'warning',
				'title' => 'High Database Query Count',
				'description' => sprintf(
					'Average %.1f queries per operation. Enable query caching and optimize database structure.',
					$stats['avg_queries']
				),
			);
		}

		// Check memory usage
		if ( $stats['avg_memory'] > 10485760 ) { // 10 MB
			$recommendations[] = array(
				'severity' => 'warning',
				'title' => 'High Memory Usage',
				'description' => sprintf(
					'Average memory usage is %s. Consider pagination for large tables.',
					$this->format_bytes( $stats['avg_memory'] )
				),
			);
		}

		if ( empty( $recommendations ) ) {
			$recommendations[] = array(
				'severity' => 'success',
				'title' => 'Performance Looks Good',
				'description' => 'No performance issues detected. Keep monitoring for changes.',
			);
		}

		return $recommendations;
	}

	/**
	 * Clear all metrics
	 *
	 * @return bool Success.
	 */
	public function clear_metrics() {
		return delete_transient( 'atables_performance_metrics' );
	}

	/**
	 * Store metric in transient
	 *
	 * @param array $metric Metric data.
	 */
	private function store_metric( $metric ) {
		$metrics = get_transient( 'atables_performance_metrics' );
		if ( ! $metrics ) {
			$metrics = array();
		}

		$metrics[] = $metric;

		// Keep only last 1000 metrics
		if ( count( $metrics ) > 1000 ) {
			$metrics = array_slice( $metrics, -1000 );
		}

		set_transient( 'atables_performance_metrics', $metrics, WEEK_IN_SECONDS );
	}

	/**
	 * Load metrics from transient
	 */
	private function load_metrics() {
		$this->metrics = get_transient( 'atables_performance_metrics' );
		if ( ! $this->metrics ) {
			$this->metrics = array();
		}
	}

	/**
	 * Get performance threshold for operation
	 *
	 * @param string $operation Operation name.
	 * @return int Threshold in milliseconds.
	 */
	private function get_threshold( $operation ) {
		// Check for exact match
		if ( isset( $this->thresholds[ $operation ] ) ) {
			return $this->thresholds[ $operation ];
		}

		// Check for partial match
		foreach ( $this->thresholds as $key => $threshold ) {
			if ( strpos( $operation, $key ) !== false ) {
				return $threshold;
			}
		}

		// Default threshold
		return 1000;
	}

	/**
	 * Get timestamp for period
	 *
	 * @param string $period Period (hour, day, week, month).
	 * @return int Timestamp.
	 */
	private function get_period_timestamp( $period ) {
		$periods = array(
			'hour' => HOUR_IN_SECONDS,
			'day' => DAY_IN_SECONDS,
			'week' => WEEK_IN_SECONDS,
			'month' => MONTH_IN_SECONDS,
		);

		$seconds = isset( $periods[ $period ] ) ? $periods[ $period ] : DAY_IN_SECONDS;

		return time() - $seconds;
	}

	/**
	 * Get database query count
	 *
	 * @return int Query count.
	 */
	private function get_query_count() {
		global $wpdb;
		return $wpdb->num_queries;
	}

	/**
	 * Format bytes to human-readable format
	 *
	 * @param int $bytes Bytes.
	 * @return string Formatted string.
	 */
	private function format_bytes( $bytes ) {
		$units = array( 'B', 'KB', 'MB', 'GB' );
		$bytes = max( $bytes, 0 );
		$pow = floor( ( $bytes ? log( $bytes ) : 0 ) / log( 1024 ) );
		$pow = min( $pow, count( $units ) - 1 );
		$bytes /= pow( 1024, $pow );

		return round( $bytes, 2 ) . ' ' . $units[ $pow ];
	}
}

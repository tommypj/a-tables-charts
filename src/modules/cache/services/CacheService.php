<?php
/**
 * Cache Service
 *
 * Handles caching of table data for performance optimization.
 * Uses WordPress transients for temporary storage.
 *
 * @package ATablesCharts\Cache
 * @since 1.0.0
 */

namespace ATablesCharts\Cache\Services;

/**
 * Cache Service Class
 *
 * Responsibilities:
 * - Cache table data from external sources
 * - Manage cache expiration
 * - Clear cache when needed
 * - Track cache statistics
 */
class CacheService {

	/**
	 * Cache key prefix
	 *
	 * @var string
	 */
	private const CACHE_PREFIX = 'atables_cache_';

	/**
	 * Cache statistics key
	 *
	 * @var string
	 */
	private const STATS_KEY = 'atables_cache_stats';

	/**
	 * Default cache expiration (in seconds)
	 * Default: 1 hour
	 *
	 * @var int
	 */
	private $default_expiration;

	/**
	 * Constructor
	 */
	public function __construct() {
		// Get cache expiration from settings (default 1 hour = 3600 seconds).
		$settings = get_option( 'atables_settings', array() );
		$this->default_expiration = isset( $settings['cache_expiration'] ) 
			? (int) $settings['cache_expiration'] 
			: 3600;
	}

	/**
	 * Get cached data
	 *
	 * @param string $key Cache key.
	 * @return mixed|false Cached data or false if not found.
	 */
	public function get( $key ) {
		$cache_key = $this->get_cache_key( $key );
		$cached_data = get_transient( $cache_key );

		if ( false !== $cached_data ) {
			$this->update_stats( 'hits' );
			return $cached_data;
		}

		$this->update_stats( 'misses' );
		return false;
	}

	/**
	 * Set cached data
	 *
	 * @param string $key Cache key.
	 * @param mixed  $data Data to cache.
	 * @param int    $expiration Cache expiration in seconds (optional).
	 * @return bool True on success, false on failure.
	 */
	public function set( $key, $data, $expiration = null ) {
		$cache_key = $this->get_cache_key( $key );
		$expiration = $expiration ?? $this->default_expiration;

		$result = set_transient( $cache_key, $data, $expiration );

		if ( $result ) {
			$this->update_stats( 'sets' );
			$this->track_cache_key( $key, $expiration );
		}

		return $result;
	}

	/**
	 * Delete cached data
	 *
	 * @param string $key Cache key.
	 * @return bool True on success, false on failure.
	 */
	public function delete( $key ) {
		$cache_key = $this->get_cache_key( $key );
		$result = delete_transient( $cache_key );

		if ( $result ) {
			$this->update_stats( 'deletes' );
			$this->remove_cache_key( $key );
		}

		return $result;
	}

	/**
	 * Check if cache exists
	 *
	 * @param string $key Cache key.
	 * @return bool True if exists, false otherwise.
	 */
	public function exists( $key ) {
		$cache_key = $this->get_cache_key( $key );
		return false !== get_transient( $cache_key );
	}

	/**
	 * Clear all plugin caches
	 *
	 * @return int Number of caches cleared.
	 */
	public function clear_all() {
		global $wpdb;

		// Get all cache keys we've tracked.
		$tracked_keys = $this->get_tracked_keys();
		$cleared = 0;

		foreach ( $tracked_keys as $key ) {
			if ( $this->delete( $key ) ) {
				$cleared++;
			}
		}

		// Also clear any orphaned transients (fallback).
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->options} 
				WHERE option_name LIKE %s 
				OR option_name LIKE %s",
				'_transient_' . self::CACHE_PREFIX . '%',
				'_transient_timeout_' . self::CACHE_PREFIX . '%'
			)
		);

		// Reset statistics.
		$this->reset_stats();

		return $cleared;
	}

	/**
	 * Clear cache for a specific table
	 *
	 * @param int $table_id Table ID.
	 * @return bool True on success.
	 */
	public function clear_table_cache( $table_id ) {
		$key = 'table_' . $table_id;
		return $this->delete( $key );
	}

	/**
	 * Get cache for a table
	 *
	 * @param int $table_id Table ID.
	 * @return mixed|false Cached data or false.
	 */
	public function get_table_cache( $table_id ) {
		$key = 'table_' . $table_id;
		return $this->get( $key );
	}

	/**
	 * Set cache for a table
	 *
	 * @param int   $table_id Table ID.
	 * @param mixed $data Data to cache.
	 * @param int   $expiration Cache expiration (optional).
	 * @return bool True on success.
	 */
	public function set_table_cache( $table_id, $data, $expiration = null ) {
		$key = 'table_' . $table_id;
		return $this->set( $key, $data, $expiration );
	}

	/**
	 * Get cache statistics
	 *
	 * @return array Cache statistics.
	 */
	public function get_stats() {
		$stats = get_option( self::STATS_KEY, array(
			'hits'    => 0,
			'misses'  => 0,
			'sets'    => 0,
			'deletes' => 0,
		) );

		// Calculate hit rate.
		$total_requests = $stats['hits'] + $stats['misses'];
		$stats['hit_rate'] = $total_requests > 0 
			? round( ( $stats['hits'] / $total_requests ) * 100, 2 ) 
			: 0;

		return $stats;
	}

	/**
	 * Reset cache statistics
	 *
	 * @return bool True on success.
	 */
	public function reset_stats() {
		return update_option( self::STATS_KEY, array(
			'hits'    => 0,
			'misses'  => 0,
			'sets'    => 0,
			'deletes' => 0,
		) );
	}

	/**
	 * Get cache info for all tables
	 *
	 * @return array Array of cache info.
	 */
	public function get_all_cache_info() {
		$tracked_keys = $this->get_tracked_keys();
		$cache_info = array();

		foreach ( $tracked_keys as $key ) {
			$cache_key = $this->get_cache_key( $key );
			$timeout = get_option( '_transient_timeout_' . $cache_key );
			
			if ( false !== $timeout ) {
				$cache_info[] = array(
					'key'        => $key,
					'expires_at' => $timeout,
					'expires_in' => max( 0, $timeout - time() ),
					'is_expired' => $timeout < time(),
				);
			}
		}

		return $cache_info;
	}

	/**
	 * Get cache size estimate
	 *
	 * @return array Size information.
	 */
	public function get_cache_size() {
		global $wpdb;

		$result = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT SUM(LENGTH(option_value)) 
				FROM {$wpdb->options} 
				WHERE option_name LIKE %s",
				'_transient_' . self::CACHE_PREFIX . '%'
			)
		);

		$bytes = $result ? (int) $result : 0;

		return array(
			'bytes'     => $bytes,
			'kilobytes' => round( $bytes / 1024, 2 ),
			'megabytes' => round( $bytes / 1024 / 1024, 2 ),
		);
	}

	/**
	 * Generate cache key
	 *
	 * @param string $key Original key.
	 * @return string Full cache key.
	 */
	private function get_cache_key( $key ) {
		return self::CACHE_PREFIX . md5( $key );
	}

	/**
	 * Track a cache key for management
	 *
	 * @param string $key Cache key.
	 * @param int    $expiration Expiration time.
	 * @return bool True on success.
	 */
	private function track_cache_key( $key, $expiration ) {
		$tracked = get_option( self::CACHE_PREFIX . 'tracked_keys', array() );
		
		if ( ! in_array( $key, $tracked, true ) ) {
			$tracked[] = $key;
			return update_option( self::CACHE_PREFIX . 'tracked_keys', $tracked );
		}

		return true;
	}

	/**
	 * Remove a cache key from tracking
	 *
	 * @param string $key Cache key.
	 * @return bool True on success.
	 */
	private function remove_cache_key( $key ) {
		$tracked = get_option( self::CACHE_PREFIX . 'tracked_keys', array() );
		$index = array_search( $key, $tracked, true );

		if ( false !== $index ) {
			unset( $tracked[ $index ] );
			return update_option( self::CACHE_PREFIX . 'tracked_keys', array_values( $tracked ) );
		}

		return true;
	}

	/**
	 * Get all tracked cache keys
	 *
	 * @return array Array of cache keys.
	 */
	private function get_tracked_keys() {
		return get_option( self::CACHE_PREFIX . 'tracked_keys', array() );
	}

	/**
	 * Update cache statistics
	 *
	 * @param string $type Stat type (hits, misses, sets, deletes).
	 * @return bool True on success.
	 */
	private function update_stats( $type ) {
		$stats = get_option( self::STATS_KEY, array(
			'hits'    => 0,
			'misses'  => 0,
			'sets'    => 0,
			'deletes' => 0,
		) );

		if ( isset( $stats[ $type ] ) ) {
			$stats[ $type ]++;
			return update_option( self::STATS_KEY, $stats );
		}

		return false;
	}

	/**
	 * Warm cache for a table
	 * Proactively load and cache table data
	 *
	 * @param int $table_id Table ID.
	 * @return bool True on success.
	 */
	public function warm_cache( $table_id ) {
		// Load Tables module.
		require_once ATABLES_PLUGIN_DIR . 'src/modules/tables/index.php';
		
		$table_service = new \ATablesCharts\Tables\Services\TableService();
		$table = $table_service->get_table( $table_id );

		if ( ! $table ) {
			return false;
		}

		// Get fresh data.
		$table_repository = new \ATablesCharts\Tables\Repositories\TableRepository();
		$data = $table_repository->get_table_data( $table_id );

		// Cache it.
		return $this->set_table_cache( $table_id, array(
			'headers' => $table->get_headers(),
			'data'    => $data,
			'cached_at' => current_time( 'mysql' ),
		) );
	}
}

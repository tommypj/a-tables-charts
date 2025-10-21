<?php
/**
 * Cache Controller
 *
 * Handles AJAX requests for cache management.
 *
 * @package ATablesCharts\Cache
 * @since 1.0.0
 */

namespace ATablesCharts\Cache\Controllers;

use ATablesCharts\Cache\Services\CacheService;

/**
 * Cache Controller Class
 */
class CacheController {

	/**
	 * Cache Service instance
	 *
	 * @var CacheService
	 */
	private $cache_service;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->cache_service = new CacheService();
	}

	/**
	 * Register AJAX hooks
	 */
	public function register_hooks() {
		add_action( 'wp_ajax_atables_clear_cache', array( $this, 'clear_cache' ) );
		add_action( 'wp_ajax_atables_reset_cache_stats', array( $this, 'reset_cache_stats' ) );
	}

	/**
	 * Clear all cache (AJAX)
	 */
	public function clear_cache() {
		// Verify nonce.
		check_ajax_referer( 'atables_admin_nonce', 'nonce' );

		// Check permissions.
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array(
				'message' => __( 'You do not have permission to perform this action.', 'a-tables-charts' ),
			) );
		}

		try {
			$cleared = $this->cache_service->clear_all();

			wp_send_json_success( array(
				'message' => sprintf(
					/* translators: %d: number of caches cleared */
					__( 'Successfully cleared %d cached items.', 'a-tables-charts' ),
					$cleared
				),
				'cleared' => $cleared,
			) );
		} catch ( \Exception $e ) {
			wp_send_json_error( array(
				'message' => __( 'Failed to clear cache: ', 'a-tables-charts' ) . $e->getMessage(),
			) );
		}
	}

	/**
	 * Reset cache statistics (AJAX)
	 */
	public function reset_cache_stats() {
		// Verify nonce.
		check_ajax_referer( 'atables_admin_nonce', 'nonce' );

		// Check permissions.
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array(
				'message' => __( 'You do not have permission to perform this action.', 'a-tables-charts' ),
			) );
		}

		try {
			$this->cache_service->reset_stats();

			wp_send_json_success( array(
				'message' => __( 'Cache statistics reset successfully.', 'a-tables-charts' ),
			) );
		} catch ( \Exception $e ) {
			wp_send_json_error( array(
				'message' => __( 'Failed to reset statistics: ', 'a-tables-charts' ) . $e->getMessage(),
			) );
		}
	}
}

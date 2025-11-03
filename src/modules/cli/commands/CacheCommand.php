<?php
/**
 * WP-CLI Cache Commands
 *
 * Manages cache via WP-CLI.
 *
 * @package ATablesCharts\CLI
 * @since 1.0.0
 */

namespace ATablesCharts\CLI\Commands;

use ATablesCharts\Cache\Services\CacheService;
use ATablesCharts\Tables\Services\TableService;
use ATablesCharts\Shared\Utils\Logger;

/**
 * Manage A-Tables & Charts cache.
 */
class CacheCommand {

	/**
	 * Cache service instance
	 *
	 * @var CacheService
	 */
	private $cache_service;

	/**
	 * Table service instance
	 *
	 * @var TableService
	 */
	private $table_service;

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
		require_once ATABLES_PLUGIN_DIR . 'src/modules/cache/index.php';
		require_once ATABLES_PLUGIN_DIR . 'src/modules/tables/index.php';
		$this->cache_service = new CacheService();
		$this->table_service = new TableService();
		$this->logger = new Logger();
	}

	/**
	 * Clears all cache.
	 *
	 * ## OPTIONS
	 *
	 * [--yes]
	 * : Skip confirmation prompt.
	 *
	 * ## EXAMPLES
	 *
	 *     # Clear all cache
	 *     $ wp atables cache clear
	 *
	 *     # Clear without confirmation
	 *     $ wp atables cache clear --yes
	 *
	 * @when after_wp_load
	 */
	public function clear( $args, $assoc_args ) {
		if ( ! isset( $assoc_args['yes'] ) ) {
			\WP_CLI::confirm( 'Are you sure you want to clear all cache?' );
		}

		$result = $this->cache_service->clear_all_cache();

		if ( $result['success'] ) {
			\WP_CLI::success( 'All cache cleared successfully.' );
		} else {
			\WP_CLI::error( $result['message'] ?? 'Failed to clear cache.' );
		}
	}

	/**
	 * Clears cache for a specific table.
	 *
	 * ## OPTIONS
	 *
	 * <id>...
	 * : One or more table IDs.
	 *
	 * ## EXAMPLES
	 *
	 *     # Clear cache for one table
	 *     $ wp atables cache clear-table 123
	 *
	 *     # Clear cache for multiple tables
	 *     $ wp atables cache clear-table 123 124 125
	 *
	 * @when after_wp_load
	 * @subcommand clear-table
	 */
	public function clear_table( $args, $assoc_args ) {
		$cleared = 0;

		foreach ( $args as $table_id ) {
			$result = $this->cache_service->clear_table_cache( $table_id );

			if ( $result['success'] ) {
				$cleared++;
				\WP_CLI::log( "Cleared cache for table #{$table_id}." );
			} else {
				\WP_CLI::warning( "Failed to clear cache for table #{$table_id}: {$result['message']}" );
			}
		}

		\WP_CLI::success( "Cleared cache for {$cleared} of " . count( $args ) . ' table(s).' );
	}

	/**
	 * Shows cache statistics.
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
	 *     # Show cache stats
	 *     $ wp atables cache stats
	 *
	 *     # Output as JSON
	 *     $ wp atables cache stats --format=json
	 *
	 * @when after_wp_load
	 */
	public function stats( $args, $assoc_args ) {
		$format = isset( $assoc_args['format'] ) ? $assoc_args['format'] : 'table';
		$stats = $this->cache_service->get_cache_stats();

		$data = array();

		if ( isset( $stats['total_entries'] ) ) {
			$data['Total Entries'] = $stats['total_entries'];
		}

		if ( isset( $stats['total_size'] ) ) {
			$data['Total Size'] = $this->format_bytes( $stats['total_size'] );
		}

		if ( isset( $stats['oldest_entry'] ) ) {
			$data['Oldest Entry'] = $stats['oldest_entry'] ? date( 'Y-m-d H:i:s', $stats['oldest_entry'] ) : 'N/A';
		}

		if ( isset( $stats['newest_entry'] ) ) {
			$data['Newest Entry'] = $stats['newest_entry'] ? date( 'Y-m-d H:i:s', $stats['newest_entry'] ) : 'N/A';
		}

		if ( empty( $data ) ) {
			$data = array(
				'Status' => 'No cache data available',
			);
		}

		\WP_CLI\Utils\format_items( $format, array( $data ), array_keys( $data ) );
	}

	/**
	 * Lists cached tables.
	 *
	 * ## OPTIONS
	 *
	 * [--format=<format>]
	 * : Output format (table, json, csv, yaml).
	 * ---
	 * default: table
	 * options:
	 *   - table
	 *   - json
	 *   - csv
	 *   - yaml
	 * ---
	 *
	 * ## EXAMPLES
	 *
	 *     # List cached tables
	 *     $ wp atables cache list
	 *
	 *     # Output as JSON
	 *     $ wp atables cache list --format=json
	 *
	 * @when after_wp_load
	 */
	public function list( $args, $assoc_args ) {
		$format = isset( $assoc_args['format'] ) ? $assoc_args['format'] : 'table';

		// Get all cached keys
		global $wpdb;
		$cache_keys = $wpdb->get_col(
			"SELECT option_name FROM {$wpdb->options}
			WHERE option_name LIKE '_transient_atables_table_%'"
		);

		$items = array();

		foreach ( $cache_keys as $key ) {
			// Extract table ID from key: _transient_atables_table_{id}_{hash}
			if ( preg_match( '/_transient_atables_table_(\d+)_/', $key, $matches ) ) {
				$table_id = $matches[1];
				$cache_data = get_transient( str_replace( '_transient_', '', $key ) );

				if ( $cache_data ) {
					$table = $this->table_service->get_table( $table_id );

					$items[] = array(
						'Table ID' => $table_id,
						'Title' => $table ? $table->title : 'Unknown',
						'Cached' => date( 'Y-m-d H:i:s', time() ),
						'Size' => $this->format_bytes( strlen( serialize( $cache_data ) ) ),
					);
				}
			}
		}

		if ( empty( $items ) ) {
			\WP_CLI::line( 'No cached tables found.' );
			return;
		}

		\WP_CLI\Utils\format_items( $format, $items, array( 'Table ID', 'Title', 'Cached', 'Size' ) );
	}

	/**
	 * Warms up cache for all tables.
	 *
	 * ## OPTIONS
	 *
	 * [--limit=<limit>]
	 * : Limit the number of tables to warm up.
	 *
	 * [--verbose]
	 * : Show detailed output.
	 *
	 * ## EXAMPLES
	 *
	 *     # Warm up all tables
	 *     $ wp atables cache warmup
	 *
	 *     # Warm up first 10 tables
	 *     $ wp atables cache warmup --limit=10
	 *
	 *     # Warm up with detailed output
	 *     $ wp atables cache warmup --verbose
	 *
	 * @when after_wp_load
	 */
	public function warmup( $args, $assoc_args ) {
		$limit = isset( $assoc_args['limit'] ) ? intval( $assoc_args['limit'] ) : 999;
		$verbose = isset( $assoc_args['verbose'] );

		$result = $this->table_service->get_all_tables( array(
			'status' => 'active',
			'per_page' => $limit,
		) );

		$tables = $result['tables'];

		if ( empty( $tables ) ) {
			\WP_CLI::line( 'No active tables found.' );
			return;
		}

		$warmed = 0;
		$total = count( $tables );

		if ( $verbose ) {
			\WP_CLI::line( "Warming up cache for {$total} table(s)..." );
		}

		$progress = \WP_CLI\Utils\make_progress_bar( 'Warming cache', $total );

		foreach ( $tables as $table ) {
			// Generate cache by retrieving table data
			$this->cache_service->get_cached_table_data( $table->id );

			$warmed++;

			if ( $verbose ) {
				\WP_CLI::log( "  Warmed cache for table #{$table->id}: {$table->title}" );
			}

			$progress->tick();
		}

		$progress->finish();

		\WP_CLI::success( "Cache warmed for {$warmed} table(s)." );
	}

	/**
	 * Formats bytes to human-readable format
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

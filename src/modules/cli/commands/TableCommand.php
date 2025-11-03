<?php
/**
 * WP-CLI Table Commands
 *
 * Manages tables via WP-CLI.
 *
 * @package ATablesCharts\CLI
 * @since 1.0.0
 */

namespace ATablesCharts\CLI\Commands;

use ATablesCharts\Tables\Services\TableService;
use ATablesCharts\Shared\Utils\Logger;

/**
 * Manage A-Tables & Charts tables.
 */
class TableCommand {

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
		require_once ATABLES_PLUGIN_DIR . 'src/modules/tables/index.php';
		$this->table_service = new TableService();
		$this->logger = new Logger();
	}

	/**
	 * Lists all tables.
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
	 * [--status=<status>]
	 * : Filter by status (active, inactive).
	 *
	 * [--search=<search>]
	 * : Search tables by title.
	 *
	 * ## EXAMPLES
	 *
	 *     # List all tables
	 *     $ wp atables table list
	 *
	 *     # List only active tables
	 *     $ wp atables table list --status=active
	 *
	 *     # Get table IDs only
	 *     $ wp atables table list --format=ids
	 *
	 *     # Count tables
	 *     $ wp atables table list --format=count
	 *
	 * @when after_wp_load
	 */
	public function list( $args, $assoc_args ) {
		$format = isset( $assoc_args['format'] ) ? $assoc_args['format'] : 'table';

		$query_args = array( 'per_page' => 999 );

		if ( isset( $assoc_args['status'] ) ) {
			$query_args['status'] = $assoc_args['status'];
		}

		if ( isset( $assoc_args['search'] ) ) {
			$query_args['search'] = $assoc_args['search'];
		}

		$result = $this->table_service->get_all_tables( $query_args );
		$tables = $result['tables'];

		if ( $format === 'ids' ) {
			$ids = array_map( function( $table ) {
				return $table->id;
			}, $tables );
			echo implode( ' ', $ids );
			return;
		}

		if ( $format === 'count' ) {
			\WP_CLI::line( count( $tables ) );
			return;
		}

		// Format data for display
		$items = array_map( function( $table ) {
			return array(
				'ID' => $table->id,
				'Title' => $table->title,
				'Rows' => $table->row_count,
				'Columns' => $table->column_count,
				'Status' => $table->status,
				'Created' => $table->created_at,
			);
		}, $tables );

		\WP_CLI\Utils\format_items( $format, $items, array( 'ID', 'Title', 'Rows', 'Columns', 'Status', 'Created' ) );
	}

	/**
	 * Gets details about a specific table.
	 *
	 * ## OPTIONS
	 *
	 * <id>
	 * : The table ID.
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
	 * [--fields=<fields>]
	 * : Limit output to specific fields.
	 *
	 * ## EXAMPLES
	 *
	 *     # Get table details
	 *     $ wp atables table get 123
	 *
	 *     # Get specific fields
	 *     $ wp atables table get 123 --fields=id,title,status
	 *
	 *     # Output as JSON
	 *     $ wp atables table get 123 --format=json
	 *
	 * @when after_wp_load
	 */
	public function get( $args, $assoc_args ) {
		list( $table_id ) = $args;
		$format = isset( $assoc_args['format'] ) ? $assoc_args['format'] : 'table';

		$table = $this->table_service->get_table( $table_id );

		if ( ! $table ) {
			\WP_CLI::error( "Table #{$table_id} not found." );
		}

		$data = array(
			'ID' => $table->id,
			'Title' => $table->title,
			'Description' => $table->description,
			'Rows' => $table->row_count,
			'Columns' => $table->column_count,
			'Status' => $table->status,
			'Created' => $table->created_at,
			'Modified' => $table->updated_at,
		);

		if ( isset( $assoc_args['fields'] ) ) {
			$fields = explode( ',', $assoc_args['fields'] );
			$data = array_intersect_key( $data, array_flip( $fields ) );
		}

		\WP_CLI\Utils\format_items( $format, array( $data ), array_keys( $data ) );
	}

	/**
	 * Creates a new table.
	 *
	 * ## OPTIONS
	 *
	 * <title>
	 * : The table title.
	 *
	 * [--description=<description>]
	 * : Table description.
	 *
	 * [--status=<status>]
	 * : Table status (active or inactive).
	 * ---
	 * default: active
	 * ---
	 *
	 * [--porcelain]
	 * : Output just the new table ID.
	 *
	 * ## EXAMPLES
	 *
	 *     # Create a table
	 *     $ wp atables table create "My Table"
	 *
	 *     # Create with description
	 *     $ wp atables table create "Products" --description="Product catalog"
	 *
	 *     # Create and get ID only
	 *     $ wp atables table create "Sales Data" --porcelain
	 *
	 * @when after_wp_load
	 */
	public function create( $args, $assoc_args ) {
		list( $title ) = $args;

		$data = array(
			'title' => $title,
			'description' => isset( $assoc_args['description'] ) ? $assoc_args['description'] : '',
			'status' => isset( $assoc_args['status'] ) ? $assoc_args['status'] : 'active',
			'source_data' => array(
				'headers' => array( 'Column 1' ),
				'data' => array( array( '' ) ),
			),
			'row_count' => 1,
			'column_count' => 1,
		);

		$result = $this->table_service->create_table( $data );

		if ( ! $result['success'] ) {
			\WP_CLI::error( $result['message'] );
		}

		if ( isset( $assoc_args['porcelain'] ) ) {
			\WP_CLI::line( $result['table_id'] );
		} else {
			\WP_CLI::success( "Created table #{$result['table_id']}: {$title}" );
		}
	}

	/**
	 * Updates a table.
	 *
	 * ## OPTIONS
	 *
	 * <id>
	 * : The table ID.
	 *
	 * [--title=<title>]
	 * : New table title.
	 *
	 * [--description=<description>]
	 * : New table description.
	 *
	 * [--status=<status>]
	 * : New table status (active or inactive).
	 *
	 * ## EXAMPLES
	 *
	 *     # Update title
	 *     $ wp atables table update 123 --title="New Title"
	 *
	 *     # Update status
	 *     $ wp atables table update 123 --status=inactive
	 *
	 *     # Update multiple fields
	 *     $ wp atables table update 123 --title="Updated" --description="New desc"
	 *
	 * @when after_wp_load
	 */
	public function update( $args, $assoc_args ) {
		list( $table_id ) = $args;

		$table = $this->table_service->get_table( $table_id );

		if ( ! $table ) {
			\WP_CLI::error( "Table #{$table_id} not found." );
		}

		$updates = array();

		if ( isset( $assoc_args['title'] ) ) {
			$updates['title'] = $assoc_args['title'];
		}

		if ( isset( $assoc_args['description'] ) ) {
			$updates['description'] = $assoc_args['description'];
		}

		if ( isset( $assoc_args['status'] ) ) {
			$updates['status'] = $assoc_args['status'];
		}

		if ( empty( $updates ) ) {
			\WP_CLI::error( 'Nothing to update. Please specify at least one field to update.' );
		}

		$result = $this->table_service->update_table( $table_id, $updates );

		if ( ! $result['success'] ) {
			\WP_CLI::error( $result['message'] );
		}

		\WP_CLI::success( "Updated table #{$table_id}." );
	}

	/**
	 * Deletes one or more tables.
	 *
	 * ## OPTIONS
	 *
	 * <id>...
	 * : One or more table IDs to delete.
	 *
	 * [--yes]
	 * : Skip confirmation prompt.
	 *
	 * ## EXAMPLES
	 *
	 *     # Delete a single table
	 *     $ wp atables table delete 123
	 *
	 *     # Delete multiple tables
	 *     $ wp atables table delete 123 124 125
	 *
	 *     # Delete without confirmation
	 *     $ wp atables table delete 123 --yes
	 *
	 * @when after_wp_load
	 */
	public function delete( $args, $assoc_args ) {
		$table_ids = $args;

		if ( ! isset( $assoc_args['yes'] ) ) {
			\WP_CLI::confirm( sprintf(
				'Are you sure you want to delete %d table(s)?',
				count( $table_ids )
			) );
		}

		$deleted = 0;

		foreach ( $table_ids as $table_id ) {
			$result = $this->table_service->delete_table( $table_id );

			if ( $result['success'] ) {
				$deleted++;
				\WP_CLI::log( "Deleted table #{$table_id}." );
			} else {
				\WP_CLI::warning( "Failed to delete table #{$table_id}: {$result['message']}" );
			}
		}

		\WP_CLI::success( "Deleted {$deleted} of " . count( $table_ids ) . ' table(s).' );
	}

	/**
	 * Duplicates a table.
	 *
	 * ## OPTIONS
	 *
	 * <id>
	 * : The table ID to duplicate.
	 *
	 * [--title=<title>]
	 * : Title for the new table. If not provided, will append "Copy" to original title.
	 *
	 * [--porcelain]
	 * : Output just the new table ID.
	 *
	 * ## EXAMPLES
	 *
	 *     # Duplicate a table
	 *     $ wp atables table duplicate 123
	 *
	 *     # Duplicate with custom title
	 *     $ wp atables table duplicate 123 --title="Backup Table"
	 *
	 *     # Duplicate and get new ID
	 *     $ wp atables table duplicate 123 --porcelain
	 *
	 * @when after_wp_load
	 */
	public function duplicate( $args, $assoc_args ) {
		list( $table_id ) = $args;

		$table = $this->table_service->get_table( $table_id );

		if ( ! $table ) {
			\WP_CLI::error( "Table #{$table_id} not found." );
		}

		$new_title = isset( $assoc_args['title'] )
			? $assoc_args['title']
			: $table->title . ' (Copy)';

		$new_data = array(
			'title' => $new_title,
			'description' => $table->description,
			'status' => $table->status,
			'source_data' => $table->source_data,
			'row_count' => $table->row_count,
			'column_count' => $table->column_count,
		);

		// Copy additional settings if they exist
		if ( ! empty( $table->settings ) ) {
			$new_data['settings'] = $table->settings;
		}

		$result = $this->table_service->create_table( $new_data );

		if ( ! $result['success'] ) {
			\WP_CLI::error( $result['message'] );
		}

		if ( isset( $assoc_args['porcelain'] ) ) {
			\WP_CLI::line( $result['table_id'] );
		} else {
			\WP_CLI::success( "Duplicated table #{$table_id} to #{$result['table_id']}: {$new_title}" );
		}
	}
}

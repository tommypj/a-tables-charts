<?php
/**
 * Filter Preset Repository
 *
 * Handles all database operations for filter presets.
 *
 * @package ATablesCharts\Filters\Repositories
 * @since 1.0.5
 */

namespace ATablesCharts\Filters\Repositories;

use ATablesCharts\Filters\Types\FilterPreset;
use ATablesCharts\Filters\Types\Filter;
use A_Tables_Charts\Database\DatabaseHelpers;

/**
 * FilterPresetRepository Class
 *
 * Responsibilities:
 * - CRUD operations for filter presets
 * - Query presets with filters
 * - Handle preset data persistence
 * - Manage preset relationships with tables
 */
class FilterPresetRepository {

	/**
	 * WordPress database object
	 *
	 * @var \wpdb
	 */
	private $wpdb;

	/**
	 * Filter presets table name
	 *
	 * @var string
	 */
	private $table_name;

	/**
	 * Constructor
	 */
	public function __construct() {
		global $wpdb;
		$this->wpdb       = $wpdb;
		$this->table_name = $wpdb->prefix . 'atables_filter_presets';
	}

	/**
	 * Create a new filter preset
	 *
	 * @param FilterPreset $preset Filter preset object.
	 * @return int|false Preset ID on success, false on failure
	 */
	public function create( FilterPreset $preset ) {
		// Validate preset
		$validation = $preset->validate();
		if ( ! $validation['valid'] ) {
			error_log( 'Filter preset validation failed: ' . print_r( $validation['errors'], true ) );
			return false;
		}

		// Prepare data for insertion
		$data = $preset->to_database();

		// Debug log
		error_log( 'Creating filter preset: ' . print_r( $data, true ) );

		// Insert into database
		$result = $this->wpdb->insert(
			$this->table_name,
			$data,
			array(
				'%d', // table_id
				'%s', // name
				'%s', // description
				'%s', // filters (JSON)
				'%d', // is_default
				'%d', // created_by
				'%s', // created_at
				'%s', // updated_at
			)
		);

		if ( false === $result ) {
			error_log( 'Database insert failed. Last error: ' . $this->wpdb->last_error );
			error_log( 'Last query: ' . $this->wpdb->last_query );
			return false;
		}

		$preset_id = $this->wpdb->insert_id;
		error_log( 'Filter preset created successfully with ID: ' . $preset_id );

		return $preset_id;
	}

	/**
	 * Get preset by ID
	 *
	 * @param int $id Preset ID.
	 * @return FilterPreset|null FilterPreset object or null if not found
	 */
	public function find_by_id( $id ) {
		$query = $this->wpdb->prepare(
			"SELECT * FROM {$this->table_name} WHERE id = %d",
			$id
		);

		$row = $this->wpdb->get_row( $query, ARRAY_A );

		if ( ! $row ) {
			return null;
		}

		return FilterPreset::from_database( $row );
	}

	/**
	 * Get all presets for a table
	 *
	 * @param int   $table_id Table ID.
	 * @param array $args     Query arguments.
	 * @return array Array of FilterPreset objects
	 */
	public function find_by_table_id( $table_id, $args = array() ) {
		$defaults = array(
			'orderby' => 'created_at',
			'order'   => 'DESC',
		);

		$args = wp_parse_args( $args, $defaults );

		// Validate ORDER BY parameters to prevent SQL injection
		$allowed_columns = array( 'id', 'table_id', 'name', 'created_at', 'updated_at', 'is_default' );
		$safe_orderby = DatabaseHelpers::prepare_order_by( $args['orderby'], $allowed_columns, 'created_at' );
		$safe_order = DatabaseHelpers::prepare_order_direction( $args['order'], 'DESC' );

		$query = $this->wpdb->prepare(
			"SELECT * FROM {$this->table_name}
			WHERE table_id = %d
			ORDER BY {$safe_orderby} {$safe_order}",
			$table_id
		);

		$results = $this->wpdb->get_results( $query, ARRAY_A );

		if ( ! $results ) {
			return array();
		}

		return array_map(
			function( $row ) {
				return FilterPreset::from_database( $row );
			},
			$results
		);
	}

	/**
	 * Get default preset for a table
	 *
	 * @param int $table_id Table ID.
	 * @return FilterPreset|null FilterPreset object or null if no default
	 */
	public function find_default_by_table_id( $table_id ) {
		$query = $this->wpdb->prepare(
			"SELECT * FROM {$this->table_name} 
			WHERE table_id = %d AND is_default = 1 
			LIMIT 1",
			$table_id
		);

		$row = $this->wpdb->get_row( $query, ARRAY_A );

		if ( ! $row ) {
			return null;
		}

		return FilterPreset::from_database( $row );
	}

	/**
	 * Get presets created by user
	 *
	 * @param int   $user_id User ID.
	 * @param array $args    Query arguments.
	 * @return array Array of FilterPreset objects
	 */
	public function find_by_user_id( $user_id, $args = array() ) {
		$defaults = array(
			'per_page' => 20,
			'page'     => 1,
			'orderby'  => 'created_at',
			'order'    => 'DESC',
		);

		$args = wp_parse_args( $args, $defaults );

		// Validate ORDER BY parameters to prevent SQL injection
		$allowed_columns = array( 'id', 'table_id', 'name', 'created_at', 'updated_at', 'is_default' );
		$safe_orderby = DatabaseHelpers::prepare_order_by( $args['orderby'], $allowed_columns, 'created_at' );
		$safe_order = DatabaseHelpers::prepare_order_direction( $args['order'], 'DESC' );

		$offset = ( $args['page'] - 1 ) * $args['per_page'];

		$query = "SELECT * FROM {$this->table_name}
				  WHERE created_by = %d
				  ORDER BY {$safe_orderby} {$safe_order}
				  LIMIT %d OFFSET %d";

		$results = $this->wpdb->get_results(
			$this->wpdb->prepare( $query, $user_id, $args['per_page'], $offset ),
			ARRAY_A
		);

		if ( ! $results ) {
			return array();
		}

		return array_map(
			function( $row ) {
				return FilterPreset::from_database( $row );
			},
			$results
		);
	}

	/**
	 * Update preset
	 *
	 * @param int          $id     Preset ID.
	 * @param FilterPreset $preset FilterPreset object with updated data.
	 * @return bool True on success, false on failure
	 */
	public function update( $id, FilterPreset $preset ) {
		// Validate preset
		$validation = $preset->validate();
		if ( ! $validation['valid'] ) {
			error_log( 'Filter preset validation failed: ' . print_r( $validation['errors'], true ) );
			return false;
		}

		$data = $preset->to_database();

		$result = $this->wpdb->update(
			$this->table_name,
			$data,
			array( 'id' => $id ),
			array(
				'%d', // table_id
				'%s', // name
				'%s', // description
				'%s', // filters (JSON)
				'%d', // is_default
				'%d', // created_by
				'%s', // created_at
				'%s', // updated_at
			),
			array( '%d' )
		);

		if ( false === $result ) {
			error_log( 'Filter preset update failed. Last error: ' . $this->wpdb->last_error );
			return false;
		}

		return true;
	}

	/**
	 * Delete preset
	 *
	 * @param int $id Preset ID.
	 * @return bool True on success, false on failure
	 */
	public function delete( $id ) {
		$result = $this->wpdb->delete(
			$this->table_name,
			array( 'id' => $id ),
			array( '%d' )
		);

		if ( false === $result ) {
			error_log( 'Filter preset deletion failed. Last error: ' . $this->wpdb->last_error );
			return false;
		}

		return true;
	}

	/**
	 * Set preset as default for table
	 *
	 * Unsets any existing default preset for the table first.
	 *
	 * @param int $preset_id Preset ID.
	 * @param int $table_id  Table ID.
	 * @return bool True on success, false on failure
	 */
	public function set_as_default( $preset_id, $table_id ) {
		// Start transaction
		$this->wpdb->query( 'START TRANSACTION' );

		try {
			// Unset all existing defaults for this table
			$unset_result = $this->wpdb->update(
				$this->table_name,
				array(
					'is_default' => 0,
					'updated_at' => current_time( 'mysql' ),
				),
				array( 'table_id' => $table_id ),
				array( '%d', '%s' ),
				array( '%d' )
			);

			if ( false === $unset_result ) {
				throw new \Exception( 'Failed to unset existing defaults' );
			}

			// Set new default
			$set_result = $this->wpdb->update(
				$this->table_name,
				array(
					'is_default' => 1,
					'updated_at' => current_time( 'mysql' ),
				),
				array( 'id' => $preset_id ),
				array( '%d', '%s' ),
				array( '%d' )
			);

			if ( false === $set_result ) {
				throw new \Exception( 'Failed to set new default' );
			}

			// Commit transaction
			$this->wpdb->query( 'COMMIT' );
			return true;

		} catch ( \Exception $e ) {
			// Rollback on error
			$this->wpdb->query( 'ROLLBACK' );
			error_log( 'Failed to set default preset: ' . $e->getMessage() );
			return false;
		}
	}

	/**
	 * Unset default preset for table
	 *
	 * @param int $table_id Table ID.
	 * @return bool True on success, false on failure
	 */
	public function unset_default( $table_id ) {
		$result = $this->wpdb->update(
			$this->table_name,
			array(
				'is_default' => 0,
				'updated_at' => current_time( 'mysql' ),
			),
			array( 'table_id' => $table_id ),
			array( '%d', '%s' ),
			array( '%d' )
		);

		return false !== $result;
	}

	/**
	 * Search presets by name
	 *
	 * @param string $search_term Search term.
	 * @param array  $args        Additional query arguments.
	 * @return array Array of FilterPreset objects
	 */
	public function search( $search_term, $args = array() ) {
		$defaults = array(
			'per_page' => 20,
			'page'     => 1,
			'orderby'  => 'created_at',
			'order'    => 'DESC',
		);

		$args = wp_parse_args( $args, $defaults );

		// Validate ORDER BY parameters to prevent SQL injection
		$allowed_columns = array( 'id', 'table_id', 'name', 'created_at', 'updated_at', 'is_default' );
		$safe_orderby = DatabaseHelpers::prepare_order_by( $args['orderby'], $allowed_columns, 'created_at' );
		$safe_order = DatabaseHelpers::prepare_order_direction( $args['order'], 'DESC' );

		$offset = ( $args['page'] - 1 ) * $args['per_page'];

		$query = "SELECT * FROM {$this->table_name}
				  WHERE name LIKE %s OR description LIKE %s
				  ORDER BY {$safe_orderby} {$safe_order}
				  LIMIT %d OFFSET %d";

		$results = $this->wpdb->get_results(
			$this->wpdb->prepare(
				$query,
				'%' . $this->wpdb->esc_like( $search_term ) . '%',
				'%' . $this->wpdb->esc_like( $search_term ) . '%',
				$args['per_page'],
				$offset
			),
			ARRAY_A
		);

		if ( ! $results ) {
			return array();
		}

		return array_map(
			function( $row ) {
				return FilterPreset::from_database( $row );
			},
			$results
		);
	}

	/**
	 * Count presets for a table
	 *
	 * @param int   $table_id Table ID.
	 * @param array $args     Query arguments.
	 * @return int Total count
	 */
	public function count_by_table_id( $table_id, $args = array() ) {
		$where = $this->wpdb->prepare( 'WHERE table_id = %d', $table_id );

		if ( isset( $args['is_default'] ) ) {
			$where .= $this->wpdb->prepare( ' AND is_default = %d', $args['is_default'] ? 1 : 0 );
		}

		$query = "SELECT COUNT(*) FROM {$this->table_name} {$where}";

		return (int) $this->wpdb->get_var( $query );
	}

	/**
	 * Check if preset exists
	 *
	 * @param int $id Preset ID.
	 * @return bool True if exists, false otherwise
	 */
	public function exists( $id ) {
		$query = $this->wpdb->prepare(
			"SELECT COUNT(*) FROM {$this->table_name} WHERE id = %d",
			$id
		);

		return (int) $this->wpdb->get_var( $query ) > 0;
	}

	/**
	 * Check if preset name exists for table
	 *
	 * @param string $name     Preset name.
	 * @param int    $table_id Table ID.
	 * @param int    $exclude_id Optional preset ID to exclude (for updates).
	 * @return bool True if exists, false otherwise
	 */
	public function name_exists( $name, $table_id, $exclude_id = null ) {
		$query = "SELECT COUNT(*) FROM {$this->table_name} 
				  WHERE table_id = %d AND name = %s";

		$params = array( $table_id, $name );

		if ( null !== $exclude_id ) {
			$query .= " AND id != %d";
			$params[] = $exclude_id;
		}

		$count = (int) $this->wpdb->get_var( $this->wpdb->prepare( $query, $params ) );

		return $count > 0;
	}

	/**
	 * Duplicate preset
	 *
	 * @param int    $preset_id Preset ID to duplicate.
	 * @param string $new_name  Optional new name for duplicate.
	 * @return int|false New preset ID on success, false on failure
	 */
	public function duplicate( $preset_id, $new_name = null ) {
		// Get original preset
		$original = $this->find_by_id( $preset_id );

		if ( ! $original ) {
			return false;
		}

		// Create new preset with modified data
		$duplicate = new FilterPreset(
			array(
				'table_id'    => $original->table_id,
				'name'        => $new_name ?? $original->name . ' (Copy)',
				'description' => $original->description,
				'filters'     => $original->filters,
				'is_default'  => false, // Duplicates are never default
				'created_by'  => get_current_user_id(),
			)
		);

		return $this->create( $duplicate );
	}

	/**
	 * Delete all presets for a table
	 *
	 * Used when deleting a table (cascade).
	 *
	 * @param int $table_id Table ID.
	 * @return bool True on success, false on failure
	 */
	public function delete_by_table_id( $table_id ) {
		$result = $this->wpdb->delete(
			$this->table_name,
			array( 'table_id' => $table_id ),
			array( '%d' )
		);

		return false !== $result;
	}

	/**
	 * Get statistics for presets
	 *
	 * @return array Statistics array
	 */
	public function get_statistics() {
		$stats = array(
			'total_presets'   => 0,
			'default_presets' => 0,
			'tables_with_presets' => 0,
			'avg_filters_per_preset' => 0,
		);

		// Total presets
		$stats['total_presets'] = (int) $this->wpdb->get_var(
			"SELECT COUNT(*) FROM {$this->table_name}"
		);

		// Default presets count
		$stats['default_presets'] = (int) $this->wpdb->get_var(
			"SELECT COUNT(*) FROM {$this->table_name} WHERE is_default = 1"
		);

		// Tables with presets
		$stats['tables_with_presets'] = (int) $this->wpdb->get_var(
			"SELECT COUNT(DISTINCT table_id) FROM {$this->table_name}"
		);

		// Average filters per preset (would require parsing JSON)
		// For now, return 0 or implement if needed

		return $stats;
	}
}

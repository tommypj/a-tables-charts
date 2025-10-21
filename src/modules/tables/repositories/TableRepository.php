<?php
/**
 * Table Repository
 *
 * Handles all database operations for tables.
 *
 * @package ATablesCharts\Tables\Repositories
 * @since 1.0.0
 */

namespace ATablesCharts\Tables\Repositories;

use ATablesCharts\Tables\Types\Table;

/**
 * TableRepository Class
 *
 * Responsibilities:
 * - CRUD operations for tables
 * - Query tables with filters
 * - Handle table data persistence
 * - Manage table relationships
 */
class TableRepository {

	/**
	 * WordPress database object
	 *
	 * @var \wpdb
	 */
	private $wpdb;

	/**
	 * Tables table name
	 *
	 * @var string
	 */
	private $table_name;

	/**
	 * Table data table name
	 *
	 * @var string
	 */
	private $data_table_name;

	/**
	 * Table meta table name
	 *
	 * @var string
	 */
	private $meta_table_name;

	/**
	 * Constructor
	 */
	public function __construct() {
		global $wpdb;
		$this->wpdb             = $wpdb;
		$this->table_name       = $wpdb->prefix . 'atables_tables';
		$this->data_table_name  = $wpdb->prefix . 'atables_table_data';
		$this->meta_table_name  = $wpdb->prefix . 'atables_table_meta';
	}

	/**
	 * Create a new table
	 *
	 * @param Table $table Table object.
	 * @return int|false Table ID on success, false on failure
	 */
	public function create( Table $table ) {
		// Validate table.
		$validation = $table->validate();
		if ( ! $validation['valid'] ) {
			error_log( 'Table validation failed: ' . print_r( $validation['errors'], true ) );
			return false;
		}

		// Prepare data for insertion.
		$data = $table->to_database();
		
		// Debug log the data being inserted.
		error_log( 'Attempting to insert table with data: ' . print_r( $data, true ) );

		// Insert into database.
		$result = $this->wpdb->insert(
			$this->table_name,
			$data,
			array(
				'%s', // title
				'%s', // description
				'%s', // source_type
				'%s', // source_data
				'%d', // row_count
				'%d', // column_count
				'%s', // status
				'%d', // created_by
			)
		);

		if ( false === $result ) {
			// Log the actual database error.
			error_log( 'Database insert failed. Last error: ' . $this->wpdb->last_error );
			error_log( 'Last query: ' . $this->wpdb->last_query );
			return false;
		}

		$table_id = $this->wpdb->insert_id;
		error_log( 'Table created successfully with ID: ' . $table_id );

		// Store individual rows in table_data for better performance.
		$this->store_table_rows( $table_id, $table->get_data() );

		return $table_id;
	}

	/**
	 * Store table rows in table_data table
	 *
	 * @param int   $table_id Table ID.
	 * @param array $rows     Data rows.
	 * @return bool True on success
	 */
	private function store_table_rows( $table_id, $rows ) {
		if ( empty( $rows ) ) {
			return true;
		}

		// Prepare batch insert.
		$values = array();
		$placeholders = array();

		foreach ( $rows as $index => $row ) {
			$values[] = $table_id;
			$values[] = $index;
			$values[] = wp_json_encode( $row );
			$placeholders[] = '(%d, %d, %s)';
		}

		// Batch insert for performance.
		$query = "INSERT INTO {$this->data_table_name} (table_id, row_index, row_data) VALUES " . implode( ', ', $placeholders );

		return false !== $this->wpdb->query( $this->wpdb->prepare( $query, $values ) );
	}

	/**
	 * Get table by ID
	 *
	 * @param int $id Table ID.
	 * @return Table|null Table object or null if not found
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

		return $this->row_to_table( $row );
	}

	/**
	 * Get all tables
	 *
	 * @param array $args Query arguments.
	 * @return array Array of Table objects
	 */
	public function find_all( $args = array() ) {
		$defaults = array(
			'status'   => 'active',
			'per_page' => 20,
			'page'     => 1,
			'orderby'  => 'created_at',
			'order'    => 'DESC',
		);

		$args = wp_parse_args( $args, $defaults );

		// Build query.
		$where = "WHERE 1=1";

		if ( ! empty( $args['status'] ) ) {
			$where .= $this->wpdb->prepare( ' AND status = %s', $args['status'] );
		}

		$offset = ( $args['page'] - 1 ) * $args['per_page'];

		$query = "SELECT * FROM {$this->table_name} 
				  {$where} 
				  ORDER BY {$args['orderby']} {$args['order']} 
				  LIMIT %d OFFSET %d";

		$results = $this->wpdb->get_results(
			$this->wpdb->prepare( $query, $args['per_page'], $offset ),
			ARRAY_A
		);

		if ( ! $results ) {
			return array();
		}

		return array_map( array( $this, 'row_to_table' ), $results );
	}

	/**
	 * Update table
	 *
	 * @param int   $id   Table ID.
	 * @param Table $table Table object with updated data.
	 * @return bool True on success, false on failure
	 */
	public function update( $id, Table $table ) {
		// Validate table.
		$validation = $table->validate();
		if ( ! $validation['valid'] ) {
			return false;
		}

		$data = $table->to_database();

		$result = $this->wpdb->update(
			$this->table_name,
			$data,
			array( 'id' => $id ),
			array( '%s', '%s', '%s', '%s', '%d', '%d', '%s', '%d', '%s' ),
			array( '%d' )
		);

		// Update table rows if data changed.
		if ( false !== $result ) {
			// Delete old rows.
			$this->wpdb->delete(
				$this->data_table_name,
				array( 'table_id' => $id ),
				array( '%d' )
			);

			// Insert new rows.
			$this->store_table_rows( $id, $table->get_data() );
		}

		return false !== $result;
	}

	/**
	 * Delete table
	 *
	 * @param int $id Table ID.
	 * @return bool True on success, false on failure
	 */
	public function delete( $id ) {
		// Delete table rows (cascade should handle this, but let's be explicit).
		$this->wpdb->delete(
			$this->data_table_name,
			array( 'table_id' => $id ),
			array( '%d' )
		);

		// Delete table meta.
		$this->wpdb->delete(
			$this->meta_table_name,
			array( 'table_id' => $id ),
			array( '%d' )
		);

		// Delete table.
		return false !== $this->wpdb->delete(
			$this->table_name,
			array( 'id' => $id ),
			array( '%d' )
		);
	}

	/**
	 * Get total count of tables
	 *
	 * @param array $args Query arguments.
	 * @return int Total count
	 */
	public function count( $args = array() ) {
		$where = "WHERE 1=1";

		if ( isset( $args['status'] ) && ! empty( $args['status'] ) ) {
			$where .= $this->wpdb->prepare( ' AND status = %s', $args['status'] );
		}

		$query = "SELECT COUNT(*) FROM {$this->table_name} {$where}";

		return (int) $this->wpdb->get_var( $query );
	}

	/**
	 * Search tables by title
	 *
	 * @param string $search_term Search term.
	 * @param array  $args        Additional query arguments.
	 * @return array Array of Table objects
	 */
	public function search( $search_term, $args = array() ) {
		$defaults = array(
			'per_page' => 20,
			'page'     => 1,
			'orderby'  => 'created_at',
			'order'    => 'DESC',
		);

		$args = wp_parse_args( $args, $defaults );

		$offset = ( $args['page'] - 1 ) * $args['per_page'];

		$query = "SELECT * FROM {$this->table_name} 
				  WHERE title LIKE %s OR description LIKE %s
				  ORDER BY {$args['orderby']} {$args['order']} 
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

		return array_map( array( $this, 'row_to_table' ), $results );
	}

	/**
	 * Convert database row to Table object
	 *
	 * @param array $row Database row.
	 * @return Table Table object
	 */
	private function row_to_table( $row ) {
		// Map database column names to entity properties.
		if ( isset( $row['data_source_type'] ) ) {
			$row['source_type'] = $row['data_source_type'];
		}
		
		if ( isset( $row['data_source_config'] ) ) {
			$row['source_data'] = $row['data_source_config'];
		}
		
		// Decode source_data if it's JSON string.
		if ( isset( $row['source_data'] ) && is_string( $row['source_data'] ) ) {
			$row['source_data'] = json_decode( $row['source_data'], true );
		}

		return new Table( $row );
	}

	/**
	 * Get table data rows with search, filter, and sort
	 *
	 * @param int   $table_id Table ID.
	 * @param array $args     Query arguments.
	 * @return array Array containing data and total count
	 */
	public function get_table_data_filtered( $table_id, $args = array() ) {
		$defaults = array(
			'per_page'     => 10,
			'page'         => 1,
			'search'       => '',
			'sort_column'  => '',
			'sort_order'   => 'asc',
		);

		$args = wp_parse_args( $args, $defaults );

		// Get the table to access headers
		$table = $this->find_by_id( $table_id );
		if ( ! $table ) {
			return array(
				'data'  => array(),
				'total' => 0,
			);
		}

		$headers = $table->get_headers();

		// Get all data first (we'll filter in PHP since data is JSON)
		$query = "SELECT row_data FROM {$this->data_table_name} 
				  WHERE table_id = %d 
				  ORDER BY row_index ASC";

		$results = $this->wpdb->get_results(
			$this->wpdb->prepare( $query, $table_id ),
			ARRAY_A
		);

		if ( ! $results ) {
			return array(
				'data'  => array(),
				'total' => 0,
			);
		}

		// Decode JSON data and map headers to values
		$all_data = array();
		foreach ( $results as $row ) {
			$row_data = json_decode( $row['row_data'], true );
			
			// Map numeric indices to header names
			$mapped_row = array();
			foreach ( $row_data as $index => $value ) {
				if ( isset( $headers[ $index ] ) ) {
					$mapped_row[ $headers[ $index ] ] = $value;
				}
			}
			
			$all_data[] = $mapped_row;
		}

		// Apply search filter
		if ( ! empty( $args['search'] ) ) {
			$search_term = strtolower( $args['search'] );
			$all_data = array_filter(
				$all_data,
				function( $row ) use ( $search_term ) {
					// Search across all columns
					foreach ( $row as $value ) {
						if ( stripos( (string) $value, $search_term ) !== false ) {
							return true;
						}
					}
					return false;
				}
			);
			// Re-index array after filter
			$all_data = array_values( $all_data );
		}

		// Apply sorting
		if ( ! empty( $args['sort_column'] ) && ! empty( $all_data ) ) {
			$sort_column = $args['sort_column'];
			$sort_order = strtolower( $args['sort_order'] ) === 'desc' ? 'desc' : 'asc';

			// Check if column exists in data
			if ( isset( $all_data[0][ $sort_column ] ) ) {
				usort(
					$all_data,
					function( $a, $b ) use ( $sort_column, $sort_order ) {
						// Check if both rows have the column
						if ( ! isset( $a[ $sort_column ] ) || ! isset( $b[ $sort_column ] ) ) {
							return 0;
						}

						$val_a = $a[ $sort_column ];
						$val_b = $b[ $sort_column ];

						// Try numeric comparison first
						if ( is_numeric( $val_a ) && is_numeric( $val_b ) ) {
							$result = $val_a <=> $val_b;
						} else {
							// String comparison
							$result = strcasecmp( (string) $val_a, (string) $val_b );
						}

						return $sort_order === 'desc' ? -$result : $result;
					}
				);
			}
		}

		// Get total count after filtering
		$total = count( $all_data );

		// Apply pagination
		if ( $args['per_page'] > 0 ) {
			$offset = ( $args['page'] - 1 ) * $args['per_page'];
			$all_data = array_slice( $all_data, $offset, $args['per_page'] );
		}

		return array(
			'data'  => $all_data,
			'total' => $total,
		);
	}

	/**
	 * Update table display settings
	 *
	 * @param int   $table_id Table ID.
	 * @param array $settings Display settings array.
	 * @return bool True on success, false on failure
	 */
	public function update_display_settings( $table_id, $settings ) {
		$result = $this->wpdb->update(
			$this->table_name,
			array(
				'display_settings' => wp_json_encode( $settings ),
				'updated_at'       => current_time( 'mysql' ),
			),
			array( 'id' => $table_id ),
			array( '%s', '%s' ),
			array( '%d' )
		);

		return false !== $result;
	}

	/**
	 * Get table display settings
	 *
	 * @param int $table_id Table ID.
	 * @return array Display settings array
	 */
	public function get_display_settings( $table_id ) {
		$query = $this->wpdb->prepare(
			"SELECT display_settings FROM {$this->table_name} WHERE id = %d",
			$table_id
		);

		$result = $this->wpdb->get_var( $query );

		if ( empty( $result ) ) {
			return array();
		}

		$settings = json_decode( $result, true );
		return is_array( $settings ) ? $settings : array();
	}

	/**
	 * Clear table display settings
	 *
	 * @param int $table_id Table ID.
	 * @return bool True on success, false on failure
	 */
	public function clear_display_settings( $table_id ) {
		$result = $this->wpdb->update(
			$this->table_name,
			array(
				'display_settings' => null,
				'updated_at'       => current_time( 'mysql' ),
			),
			array( 'id' => $table_id ),
			array( '%s', '%s' ),
			array( '%d' )
		);

		return false !== $result;
	}

	/**
	 * Update a single display setting
	 *
	 * @param int    $table_id Table ID.
	 * @param string $key      Setting key.
	 * @param mixed  $value    Setting value.
	 * @return bool True on success, false on failure
	 */
	public function update_display_setting( $table_id, $key, $value ) {
		$settings = $this->get_display_settings( $table_id );
		$settings[ $key ] = $value;
		return $this->update_display_settings( $table_id, $settings );
	}

	/**
	 * Remove a single display setting
	 *
	 * @param int    $table_id Table ID.
	 * @param string $key      Setting key.
	 * @return bool True on success, false on failure
	 */
	public function remove_display_setting( $table_id, $key ) {
		$settings = $this->get_display_settings( $table_id );
		
		if ( ! isset( $settings[ $key ] ) ) {
			return true; // Setting doesn't exist, nothing to remove
		}
		
		unset( $settings[ $key ] );
		return $this->update_display_settings( $table_id, $settings );
	}

	/**
	 * Get table data with advanced sorting
	 *
	 * @param int   $table_id    Table ID.
	 * @param array $sort_config Sort configuration.
	 * @return array Sorted data
	 */
	public function get_table_data_sorted( $table_id, $sort_config = array() ) {
		// Get table
		$table = $this->find_by_id( $table_id );
		if ( ! $table ) {
			return array();
		}

		// Get all data
		$data = $this->get_table_data( $table_id );
		
		if ( empty( $data ) || empty( $sort_config ) ) {
			return $data;
		}

		// Load sorting service
		if ( ! class_exists( 'ATablesCharts\\Sorting\\Services\\SortingService' ) ) {
			require_once ATABLES_PLUGIN_DIR . 'src/modules/sorting/SortingService.php';
		}

		$sorting_service = new \ATablesCharts\Sorting\Services\SortingService();
		
		// Map numeric indices to header names for data
		$headers = $table->get_headers();
		$mapped_data = array();
		
		foreach ( $data as $row ) {
			$mapped_row = array();
			foreach ( $row as $index => $value ) {
				if ( isset( $headers[ $index ] ) ) {
					$mapped_row[ $headers[ $index ] ] = $value;
				}
			}
			$mapped_data[] = $mapped_row;
		}

		// Apply sorting
		return $sorting_service->apply_saved_sort( $mapped_data, $sort_config );
	}
	
	/**
	 * Get table data rows (backward compatibility)
	 *
	 * @param int   $table_id Table ID.
	 * @param array $args     Query arguments.
	 * @return array Array of data rows
	 */
	public function get_table_data( $table_id, $args = array() ) {
		$defaults = array(
			'per_page' => 0,
			'page'     => 1,
		);

		$args = wp_parse_args( $args, $defaults );

		$query = "SELECT row_data FROM {$this->data_table_name} 
				  WHERE table_id = %d 
				  ORDER BY row_index ASC";

		if ( $args['per_page'] > 0 ) {
			$offset = ( $args['page'] - 1 ) * $args['per_page'];
			$query .= " LIMIT %d OFFSET %d";
			$results = $this->wpdb->get_results(
				$this->wpdb->prepare( $query, $table_id, $args['per_page'], $offset ),
				ARRAY_A
			);
		} else {
			$results = $this->wpdb->get_results(
				$this->wpdb->prepare( $query, $table_id ),
				ARRAY_A
			);
		}

		if ( ! $results ) {
			return array();
		}

		// Decode JSON row data.
		return array_map(
			function( $row ) {
				return json_decode( $row['row_data'], true );
			},
			$results
		);
	}

	/**
	 * Update table data (headers and rows)
	 *
	 * @param int   $table_id Table ID.
	 * @param array $headers  Column headers.
	 * @param array $data     Data rows.
	 * @return bool True on success, false on failure
	 */
	public function update_table_data( $table_id, $headers, $data ) {
		// Delete old rows
		$this->wpdb->delete(
			$this->data_table_name,
			array( 'table_id' => $table_id ),
			array( '%d' )
		);

		// Store new rows
		$this->store_table_rows( $table_id, $data );

		// Update table metadata with headers
		$source_data = array(
			'headers' => $headers,
		);

		$result = $this->wpdb->update(
			$this->table_name,
			array(
				'source_data' => wp_json_encode( $source_data ),
				'updated_at' => current_time( 'mysql' ),
			),
			array( 'id' => $table_id ),
			array( '%s', '%s' ),
			array( '%d' )
		);

		return false !== $result;
	}

	/**
	 * Save display settings (alias for update_display_settings)
	 *
	 * @param int   $table_id Table ID.
	 * @param array $settings Display settings array.
	 * @return bool True on success, false on failure
	 */
	public function save_display_settings( $table_id, $settings ) {
		return $this->update_display_settings( $table_id, $settings );
	}
}

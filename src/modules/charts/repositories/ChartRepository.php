<?php
/**
 * Chart Repository
 *
 * Handles all database operations for charts.
 *
 * @package ATablesCharts\Charts\Repositories
 * @since 1.0.0
 */

namespace ATablesCharts\Charts\Repositories;

use ATablesCharts\Charts\Types\Chart;

/**
 * ChartRepository Class
 *
 * Responsibilities:
 * - CRUD operations for charts
 * - Query charts with filters
 * - Link charts to tables
 */
class ChartRepository {

	/**
	 * WordPress database object
	 *
	 * @var \wpdb
	 */
	private $wpdb;

	/**
	 * Charts table name
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
		$this->table_name = $wpdb->prefix . 'atables_charts';
	}

	/**
	 * Create a new chart
	 *
	 * @param Chart $chart Chart object.
	 * @return int|false Chart ID on success, false on failure
	 */
	public function create( Chart $chart ) {
		$validation = $chart->validate();
		if ( ! $validation['valid'] ) {
			return false;
		}

		$data = $chart->to_database();

		$result = $this->wpdb->insert(
			$this->table_name,
			$data,
			array( '%d', '%s', '%s', '%s', '%s', '%d' )
		);

		return false !== $result ? $this->wpdb->insert_id : false;
	}

	/**
	 * Get chart by ID
	 *
	 * @param int $id Chart ID.
	 * @return Chart|null Chart object or null if not found
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

		return new Chart( $row );
	}

	/**
	 * Get all charts
	 *
	 * @param array $args Query arguments.
	 * @return array Array of Chart objects
	 */
	public function find_all( $args = array() ) {
		$defaults = array(
			'table_id' => null,
			'status'   => 'active',
			'per_page' => 20,
			'page'     => 1,
			'orderby'  => 'created_at',
			'order'    => 'DESC',
		);

		$args = wp_parse_args( $args, $defaults );

		$where = "WHERE 1=1";

		if ( ! empty( $args['table_id'] ) ) {
			$where .= $this->wpdb->prepare( ' AND table_id = %d', $args['table_id'] );
		}

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

		return array_map(
			function( $row ) {
				return new Chart( $row );
			},
			$results
		);
	}

	/**
	 * Update chart
	 *
	 * @param int   $id    Chart ID.
	 * @param Chart $chart Chart object with updated data.
	 * @return bool True on success, false on failure
	 */
	public function update( $id, Chart $chart ) {
		$validation = $chart->validate();
		if ( ! $validation['valid'] ) {
			return false;
		}

		$data = $chart->to_database();

		$result = $this->wpdb->update(
			$this->table_name,
			$data,
			array( 'id' => $id ),
			array( '%d', '%s', '%s', '%s', '%s', '%d' ),
			array( '%d' )
		);

		return false !== $result;
	}

	/**
	 * Delete chart
	 *
	 * @param int $id Chart ID.
	 * @return bool True on success, false on failure
	 */
	public function delete( $id ) {
		return false !== $this->wpdb->delete(
			$this->table_name,
			array( 'id' => $id ),
			array( '%d' )
		);
	}

	/**
	 * Get total count of charts
	 *
	 * @param array $args Query arguments.
	 * @return int Total count
	 */
	public function count( $args = array() ) {
		$where = "WHERE 1=1";

		if ( isset( $args['table_id'] ) && ! empty( $args['table_id'] ) ) {
			$where .= $this->wpdb->prepare( ' AND table_id = %d', $args['table_id'] );
		}

		if ( isset( $args['status'] ) && ! empty( $args['status'] ) ) {
			$where .= $this->wpdb->prepare( ' AND status = %s', $args['status'] );
		}

		$query = "SELECT COUNT(*) FROM {$this->table_name} {$where}";

		return (int) $this->wpdb->get_var( $query );
	}

	/**
	 * Get charts by table ID
	 *
	 * @param int $table_id Table ID.
	 * @return array Array of Chart objects
	 */
	public function find_by_table_id( $table_id ) {
		return $this->find_all( array( 'table_id' => $table_id ) );
	}
}

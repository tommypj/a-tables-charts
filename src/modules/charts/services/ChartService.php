<?php
/**
 * Chart Service
 *
 * Business logic for chart operations.
 *
 * @package ATablesCharts\Charts\Services
 * @since 1.0.0
 */

namespace ATablesCharts\Charts\Services;

use ATablesCharts\Charts\Types\Chart;
use ATablesCharts\Charts\Repositories\ChartRepository;
use ATablesCharts\Tables\Repositories\TableRepository;

/**
 * ChartService Class
 *
 * Responsibilities:
 * - Handle chart business logic
 * - Validate chart data
 * - Generate chart data from tables
 */
class ChartService {

	/**
	 * Chart repository
	 *
	 * @var ChartRepository
	 */
	private $chart_repository;

	/**
	 * Table repository
	 *
	 * @var TableRepository
	 */
	private $table_repository;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->chart_repository = new ChartRepository();
		$this->table_repository = new TableRepository();
	}

	/**
	 * Create a new chart
	 *
	 * @param array $data Chart data.
	 * @return array Result with success status and message
	 */
	public function create_chart( $data ) {
		// Validate table exists.
		$table = $this->table_repository->find_by_id( $data['table_id'] );
		if ( ! $table ) {
			return array(
				'success' => false,
				'message' => __( 'Table not found.', 'a-tables-charts' ),
			);
		}

		// Create chart object.
		$chart = new Chart( $data );

		// Validate chart.
		$validation = $chart->validate();
		if ( ! $validation['valid'] ) {
			return array(
				'success' => false,
				'message' => implode( ' ', $validation['errors'] ),
			);
		}

		// Save chart.
		$chart_id = $this->chart_repository->create( $chart );

		if ( ! $chart_id ) {
			return array(
				'success' => false,
				'message' => __( 'Failed to create chart.', 'a-tables-charts' ),
			);
		}

		return array(
			'success'  => true,
			'message'  => __( 'Chart created successfully.', 'a-tables-charts' ),
			'chart_id' => $chart_id,
		);
	}

	/**
	 * Get chart by ID
	 *
	 * @param int $chart_id Chart ID.
	 * @return Chart|null Chart object or null
	 */
	public function get_chart( $chart_id ) {
		return $this->chart_repository->find_by_id( $chart_id );
	}

	/**
	 * Get all charts
	 *
	 * @param array $args Query arguments.
	 * @return array Result with charts and pagination info
	 */
	public function get_all_charts( $args = array() ) {
		$charts = $this->chart_repository->find_all( $args );
		$total  = $this->chart_repository->count( $args );

		return array(
			'charts'   => $charts,
			'total'    => $total,
			'page'     => $args['page'] ?? 1,
			'per_page' => $args['per_page'] ?? 20,
		);
	}

	/**
	 * Update chart
	 *
	 * @param int   $chart_id Chart ID.
	 * @param array $data     Updated chart data.
	 * @return array Result with success status and message
	 */
	public function update_chart( $chart_id, $data ) {
		// Get existing chart.
		$existing_chart = $this->chart_repository->find_by_id( $chart_id );
		if ( ! $existing_chart ) {
			return array(
				'success' => false,
				'message' => __( 'Chart not found.', 'a-tables-charts' ),
			);
		}

		// Merge with existing data.
		$chart_data = array_merge( $existing_chart->to_array(), $data );
		$chart      = new Chart( $chart_data );

		// Validate.
		$validation = $chart->validate();
		if ( ! $validation['valid'] ) {
			return array(
				'success' => false,
				'message' => implode( ' ', $validation['errors'] ),
			);
		}

		// Update.
		$result = $this->chart_repository->update( $chart_id, $chart );

		if ( ! $result ) {
			return array(
				'success' => false,
				'message' => __( 'Failed to update chart.', 'a-tables-charts' ),
			);
		}

		return array(
			'success' => true,
			'message' => __( 'Chart updated successfully.', 'a-tables-charts' ),
		);
	}

	/**
	 * Delete chart
	 *
	 * @param int $chart_id Chart ID.
	 * @return array Result with success status and message
	 */
	public function delete_chart( $chart_id ) {
		$result = $this->chart_repository->delete( $chart_id );

		if ( ! $result ) {
			return array(
				'success' => false,
				'message' => __( 'Failed to delete chart.', 'a-tables-charts' ),
			);
		}

		return array(
			'success' => true,
			'message' => __( 'Chart deleted successfully.', 'a-tables-charts' ),
		);
	}

	/**
	 * Get chart data for rendering
	 *
	 * Processes table data into format suitable for Chart.js.
	 *
	 * @param int $chart_id Chart ID.
	 * @return array|false Chart data or false on error
	 */
	public function get_chart_data( $chart_id ) {
		$chart = $this->chart_repository->find_by_id( $chart_id );
		if ( ! $chart ) {
			return false;
		}

		// Get table data.
		$table = $this->table_repository->find_by_id( $chart->table_id );
		if ( ! $table ) {
			return false;
		}

		$table_data = $this->table_repository->get_table_data( $chart->table_id );

		// Process data based on chart configuration.
		$config       = $chart->config;
		$label_column = $config['label_column'] ?? null;
		$data_columns = $config['data_columns'] ?? array();

		if ( empty( $label_column ) || empty( $data_columns ) ) {
			return false;
		}

		$headers = $table->get_headers();
		$labels  = array();
		$datasets = array();

		// Initialize datasets.
		foreach ( $data_columns as $column ) {
			$datasets[ $column ] = array(
				'label' => $column,
				'data'  => array(),
			);
		}

		// Process table data.
		foreach ( $table_data as $row ) {
			$label_index = array_search( $label_column, $headers, true );
			if ( $label_index !== false && isset( $row[ $label_index ] ) ) {
				$labels[] = $row[ $label_index ];
			}

			foreach ( $data_columns as $column ) {
				$column_index = array_search( $column, $headers, true );
				if ( $column_index !== false && isset( $row[ $column_index ] ) ) {
					$value = is_numeric( $row[ $column_index ] ) ? (float) $row[ $column_index ] : 0;
					$datasets[ $column ]['data'][] = $value;
				}
			}
		}

		return array(
			'labels'   => $labels,
			'datasets' => array_values( $datasets ),
			'type'     => $chart->type,
			'title'    => $chart->title,
			'config'   => $config,
		);
	}

	/**
	 * Get charts by table ID
	 *
	 * @param int $table_id Table ID.
	 * @return array Array of charts
	 */
	public function get_charts_by_table( $table_id ) {
		return $this->chart_repository->find_by_table_id( $table_id );
	}
}

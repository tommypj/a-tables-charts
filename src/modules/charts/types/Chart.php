<?php
/**
 * Chart Entity
 *
 * Represents a chart configuration.
 *
 * @package ATablesCharts\Charts\Types
 * @since 1.0.0
 */

namespace ATablesCharts\Charts\Types;

/**
 * Chart Class
 *
 * Represents a chart with its configuration.
 */
class Chart {

	/**
	 * Chart ID
	 *
	 * @var int
	 */
	public $id;

	/**
	 * Table ID this chart is based on
	 *
	 * @var int
	 */
	public $table_id;

	/**
	 * Chart title
	 *
	 * @var string
	 */
	public $title;

	/**
	 * Chart type (bar, line, pie, doughnut, column, area, scatter, radar)
	 *
	 * @var string
	 */
	public $type;

	/**
	 * Chart configuration (JSON)
	 *
	 * @var array
	 */
	public $config;

	/**
	 * Status (active, inactive)
	 *
	 * @var string
	 */
	public $status;

	/**
	 * Created by user ID
	 *
	 * @var int
	 */
	public $created_by;

	/**
	 * Created at timestamp
	 *
	 * @var string
	 */
	public $created_at;

	/**
	 * Updated at timestamp
	 *
	 * @var string
	 */
	public $updated_at;

	/**
	 * Constructor
	 *
	 * @param array $data Chart data.
	 */
	public function __construct( $data = array() ) {
		$this->id         = $data['id'] ?? 0;
		$this->table_id   = $data['table_id'] ?? 0;
		$this->title      = $data['title'] ?? '';
		$this->type       = $data['type'] ?? 'bar';
		$this->config     = $data['config'] ?? array();
		$this->status     = $data['status'] ?? 'active';
		$this->created_by = $data['created_by'] ?? get_current_user_id();
		$this->created_at = $data['created_at'] ?? current_time( 'mysql' );
		$this->updated_at = $data['updated_at'] ?? current_time( 'mysql' );

		// Decode config if it's a JSON string.
		if ( is_string( $this->config ) ) {
			$this->config = json_decode( $this->config, true ) ?? array();
		}
	}

	/**
	 * Convert to array for database storage
	 *
	 * @return array Chart data
	 */
	public function to_database() {
		return array(
			'table_id'   => $this->table_id,
			'title'      => $this->title,
			'type'       => $this->type,
			'config'     => wp_json_encode( $this->config ),
			'status'     => $this->status,
			'created_by' => $this->created_by,
		);
	}

	/**
	 * Convert to array
	 *
	 * @return array Chart data
	 */
	public function to_array() {
		return array(
			'id'         => $this->id,
			'table_id'   => $this->table_id,
			'title'      => $this->title,
			'type'       => $this->type,
			'config'     => $this->config,
			'status'     => $this->status,
			'created_by' => $this->created_by,
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
		);
	}

	/**
	 * Validate chart data
	 *
	 * @return array Validation result
	 */
	public function validate() {
		$errors = array();

		if ( empty( $this->table_id ) ) {
			$errors[] = __( 'Table ID is required.', 'a-tables-charts' );
		}

		if ( empty( $this->title ) ) {
			$errors[] = __( 'Chart title is required.', 'a-tables-charts' );
		}

		$allowed_types = array( 'bar', 'line', 'pie', 'doughnut', 'column', 'area', 'scatter', 'radar' );
		if ( ! in_array( $this->type, $allowed_types, true ) ) {
			$errors[] = __( 'Invalid chart type.', 'a-tables-charts' );
		}

		return array(
			'valid'  => empty( $errors ),
			'errors' => $errors,
		);
	}
}

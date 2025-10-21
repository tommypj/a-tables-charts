<?php
/**
 * Table Entity
 *
 * Represents a table in the system.
 *
 * @package ATablesCharts\Tables\Types
 * @since 1.0.0
 */

namespace ATablesCharts\Tables\Types;

/**
 * Table Class
 *
 * Responsibilities:
 * - Represent table data structure
 * - Validate table data
 * - Convert to/from array
 * - Type safety for table operations
 */
class Table {

	/**
	 * Table ID
	 *
	 * @var int|null
	 */
	public $id;

	/**
	 * Table title
	 *
	 * @var string
	 */
	public $title;

	/**
	 * Table description
	 *
	 * @var string
	 */
	public $description;

	/**
	 * Source type (csv, json, excel, manual, etc.)
	 *
	 * @var string
	 */
	public $source_type;

	/**
	 * Source data (headers and rows)
	 *
	 * @var array
	 */
	public $source_data;

	/**
	 * Number of rows
	 *
	 * @var int
	 */
	public $row_count;

	/**
	 * Number of columns
	 *
	 * @var int
	 */
	public $column_count;

	/**
	 * Table status (active, draft, archived)
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
	 * Created timestamp
	 *
	 * @var string
	 */
	public $created_at;

	/**
	 * Updated timestamp
	 *
	 * @var string
	 */
	public $updated_at;

	/**
	 * Display settings (per-table overrides)
	 *
	 * @var string|null JSON string of display settings
	 */
	public $display_settings;

	/**
	 * Constructor
	 *
	 * @param array $data Table data.
	 */
	public function __construct( $data = array() ) {
		$this->id            = isset( $data['id'] ) ? (int) $data['id'] : null;
		$this->title         = isset( $data['title'] ) ? $data['title'] : '';
		$this->description   = isset( $data['description'] ) ? $data['description'] : '';
		$this->source_type   = isset( $data['source_type'] ) ? $data['source_type'] : 'manual';
		$this->source_data   = isset( $data['source_data'] ) ? $data['source_data'] : array();
		$this->row_count     = isset( $data['row_count'] ) ? (int) $data['row_count'] : 0;
		$this->column_count  = isset( $data['column_count'] ) ? (int) $data['column_count'] : 0;
		$this->status        = isset( $data['status'] ) ? $data['status'] : 'active';
		$this->created_by       = isset( $data['created_by'] ) ? (int) $data['created_by'] : 0;
		$this->created_at       = isset( $data['created_at'] ) ? $data['created_at'] : current_time( 'mysql' );
		$this->updated_at       = isset( $data['updated_at'] ) ? $data['updated_at'] : current_time( 'mysql' );
		$this->display_settings = isset( $data['display_settings'] ) ? $data['display_settings'] : null;
	}

	/**
	 * Create from import result
	 *
	 * @param string $title        Table title.
	 * @param object $import_result ImportResult object.
	 * @param string $source_type   Source type.
	 * @return Table Table instance
	 */
	public static function from_import_result( $title, $import_result, $source_type = 'csv' ) {
		return new self(
			array(
				'title'         => $title,
				'source_type'   => $source_type,
				'source_data'   => array(
					'headers' => $import_result->headers,
					'data'    => $import_result->data,
				),
				'row_count'     => $import_result->row_count,
				'column_count'  => $import_result->column_count,
				'created_by'    => get_current_user_id(),
			)
		);
	}

	/**
	 * Convert to array
	 *
	 * @return array Table data as array
	 */
	public function to_array() {
		return array(
			'id'            => $this->id,
			'title'         => $this->title,
			'description'   => $this->description,
			'source_type'   => $this->source_type,
			'source_data'   => $this->source_data,
			'row_count'     => $this->row_count,
			'column_count'  => $this->column_count,
			'status'        => $this->status,
			'created_by'    => $this->created_by,
			'created_at'    => $this->created_at,
			'updated_at'    => $this->updated_at,
		);
	}

	/**
	 * Convert to database format
	 *
	 * @return array Data ready for database insertion
	 */
	public function to_database() {
		return array(
			'title'              => $this->title,
			'description'        => $this->description,
			'data_source_type'   => $this->source_type,
			'data_source_config' => wp_json_encode( $this->source_data ),
			'row_count'          => $this->row_count,
			'column_count'       => $this->column_count,
			'status'             => $this->status,
			'created_by'         => $this->created_by,
			'display_settings'   => $this->display_settings,
		);
	}

	/**
	 * Get headers
	 *
	 * @return array Column headers
	 */
	public function get_headers() {
		return isset( $this->source_data['headers'] ) ? $this->source_data['headers'] : array();
	}

	/**
	 * Get data rows
	 *
	 * @return array Data rows
	 */
	public function get_data() {
		return isset( $this->source_data['data'] ) ? $this->source_data['data'] : array();
	}

	/**
	 * Validate table data
	 *
	 * @return array Validation result with 'valid' and 'errors' keys
	 */
	public function validate() {
		$errors = array();

		// Title is required.
		if ( empty( $this->title ) ) {
			$errors[] = __( 'Table title is required.', 'a-tables-charts' );
		}

		// Title length.
		if ( strlen( $this->title ) > 255 ) {
			$errors[] = __( 'Table title must be less than 255 characters.', 'a-tables-charts' );
		}

		// Source type is required.
		if ( empty( $this->source_type ) ) {
			$errors[] = __( 'Source type is required.', 'a-tables-charts' );
		}

		// Valid source types.
		$valid_types = array( 'csv', 'json', 'excel', 'xml', 'manual', 'mysql', 'api' );
		if ( ! in_array( $this->source_type, $valid_types, true ) ) {
			$errors[] = __( 'Invalid source type.', 'a-tables-charts' );
		}

		// Source data should have headers and data.
		if ( empty( $this->source_data['headers'] ) ) {
			$errors[] = __( 'Table must have column headers.', 'a-tables-charts' );
		}

		// Valid status.
		$valid_statuses = array( 'active', 'draft', 'archived' );
		if ( ! in_array( $this->status, $valid_statuses, true ) ) {
			$errors[] = __( 'Invalid status.', 'a-tables-charts' );
		}

		return array(
			'valid'  => empty( $errors ),
			'errors' => $errors,
		);
	}

	/**
	 * Check if table is empty
	 *
	 * @return bool True if empty
	 */
	public function is_empty() {
		return $this->row_count === 0 || empty( $this->get_data() );
	}

	/**
	 * Get table size in bytes
	 *
	 * @return int Size in bytes
	 */
	public function get_size() {
		return strlen( wp_json_encode( $this->source_data ) );
	}

	/**
	 * Get display settings as array
	 *
	 * @return array Display settings
	 */
	public function get_display_settings() {
		if ( empty( $this->display_settings ) ) {
			return array();
		}
		
		$settings = json_decode( $this->display_settings, true );
		return is_array( $settings ) ? $settings : array();
	}

	/**
	 * Set display settings from array
	 *
	 * @param array $settings Display settings
	 */
	public function set_display_settings( $settings ) {
		$this->display_settings = wp_json_encode( $settings );
	}

	/**
	 * Get a specific display setting
	 *
	 * @param string $key     Setting key
	 * @param mixed  $default Default value if not set
	 * @return mixed Setting value
	 */
	public function get_display_setting( $key, $default = null ) {
		$settings = $this->get_display_settings();
		return isset( $settings[ $key ] ) ? $settings[ $key ] : $default;
	}

	/**
	 * Check if table has custom display settings
	 *
	 * @return bool True if has custom settings
	 */
	public function has_display_settings() {
		return ! empty( $this->display_settings );
	}
}

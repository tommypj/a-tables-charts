<?php
/**
 * Filter Preset Entity
 *
 * Represents a saved filter configuration.
 *
 * @package ATablesCharts\Filters\Types
 * @since 1.0.5
 */

namespace ATablesCharts\Filters\Types;

/**
 * FilterPreset Class
 *
 * Represents a saved collection of filters that can be reused.
 */
class FilterPreset {

	/**
	 * Preset ID
	 *
	 * @var int|null
	 */
	public $id;

	/**
	 * Table ID this preset belongs to
	 *
	 * @var int
	 */
	public $table_id;

	/**
	 * Preset name
	 *
	 * @var string
	 */
	public $name;

	/**
	 * Preset description
	 *
	 * @var string
	 */
	public $description;

	/**
	 * Array of Filter objects
	 *
	 * @var array
	 */
	public $filters;

	/**
	 * Whether this is the default preset for the table
	 *
	 * @var bool
	 */
	public $is_default;

	/**
	 * User ID who created the preset
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
	 * Constructor
	 *
	 * @param array $data Preset data.
	 */
	public function __construct( $data = array() ) {
		$this->id          = isset( $data['id'] ) ? (int) $data['id'] : null;
		$this->table_id    = isset( $data['table_id'] ) ? (int) $data['table_id'] : 0;
		$this->name        = isset( $data['name'] ) ? $data['name'] : '';
		$this->description = isset( $data['description'] ) ? $data['description'] : '';
		$this->filters     = isset( $data['filters'] ) ? $data['filters'] : array();
		$this->is_default  = isset( $data['is_default'] ) ? (bool) $data['is_default'] : false;
		$this->created_by  = isset( $data['created_by'] ) ? (int) $data['created_by'] : get_current_user_id();
		$this->created_at  = isset( $data['created_at'] ) ? $data['created_at'] : current_time( 'mysql' );
		$this->updated_at  = isset( $data['updated_at'] ) ? $data['updated_at'] : current_time( 'mysql' );

		// Convert filter arrays to Filter objects if needed
		if ( ! empty( $this->filters ) && is_array( $this->filters ) ) {
			$this->filters = array_map(
				function( $filter ) {
					return $filter instanceof Filter ? $filter : new Filter( $filter );
				},
				$this->filters
			);
		}
	}

	/**
	 * Validate preset
	 *
	 * @return array Validation result with 'valid' and 'errors' keys
	 */
	public function validate() {
		$errors = array();

		// Table ID is required
		if ( empty( $this->table_id ) ) {
			$errors[] = __( 'Table ID is required.', 'a-tables-charts' );
		}

		// Name is required
		if ( empty( $this->name ) ) {
			$errors[] = __( 'Preset name is required.', 'a-tables-charts' );
		}

		// Name length validation
		if ( strlen( $this->name ) > 255 ) {
			$errors[] = __( 'Preset name must be less than 255 characters.', 'a-tables-charts' );
		}

		// At least one filter is required
		if ( empty( $this->filters ) ) {
			$errors[] = __( 'At least one filter is required.', 'a-tables-charts' );
		}

		// Validate each filter
		if ( ! empty( $this->filters ) ) {
			foreach ( $this->filters as $index => $filter ) {
				if ( ! $filter instanceof Filter ) {
					$errors[] = sprintf(
						/* translators: %d: filter index */
						__( 'Filter %d is not a valid Filter object.', 'a-tables-charts' ),
						$index + 1
					);
					continue;
				}

				$filter_validation = $filter->validate();
				if ( ! $filter_validation['valid'] ) {
					$errors = array_merge( $errors, $filter_validation['errors'] );
				}
			}
		}

		return array(
			'valid'  => empty( $errors ),
			'errors' => $errors,
		);
	}

	/**
	 * Convert preset to array
	 *
	 * @return array Preset data as array
	 */
	public function to_array() {
		return array(
			'id'          => $this->id,
			'table_id'    => $this->table_id,
			'name'        => $this->name,
			'description' => $this->description,
			'filters'     => array_map(
				function( $filter ) {
					return $filter->to_array();
				},
				$this->filters
			),
			'is_default'  => $this->is_default,
			'created_by'  => $this->created_by,
			'created_at'  => $this->created_at,
			'updated_at'  => $this->updated_at,
		);
	}

	/**
	 * Convert preset to database format
	 *
	 * @return array Data ready for database insertion
	 */
	public function to_database() {
		$filters_json = wp_json_encode(
			array_map(
				function( $filter ) {
					return $filter->to_array();
				},
				$this->filters
			)
		);

		return array(
			'table_id'    => $this->table_id,
			'name'        => $this->name,
			'description' => $this->description,
			'filters'     => $filters_json,
			'is_default'  => $this->is_default ? 1 : 0,
			'created_by'  => $this->created_by,
			'created_at'  => $this->created_at,
			'updated_at'  => current_time( 'mysql' ),
		);
	}

	/**
	 * Create preset from database row
	 *
	 * @param array $row Database row.
	 * @return FilterPreset FilterPreset instance
	 */
	public static function from_database( $row ) {
		// Decode filters JSON
		if ( isset( $row['filters'] ) && is_string( $row['filters'] ) ) {
			$row['filters'] = json_decode( $row['filters'], true );
		}

		// Convert is_default to boolean
		if ( isset( $row['is_default'] ) ) {
			$row['is_default'] = (bool) $row['is_default'];
		}

		return new self( $row );
	}

	/**
	 * Get filter count
	 *
	 * @return int Number of filters in preset
	 */
	public function get_filter_count() {
		return count( $this->filters );
	}

	/**
	 * Add filter to preset
	 *
	 * @param Filter $filter Filter to add.
	 * @return void
	 */
	public function add_filter( Filter $filter ) {
		$this->filters[] = $filter;
		$this->updated_at = current_time( 'mysql' );
	}

	/**
	 * Remove filter from preset
	 *
	 * @param int $index Filter index.
	 * @return bool True if removed, false if index doesn't exist
	 */
	public function remove_filter( $index ) {
		if ( ! isset( $this->filters[ $index ] ) ) {
			return false;
		}

		unset( $this->filters[ $index ] );
		$this->filters = array_values( $this->filters ); // Re-index array
		$this->updated_at = current_time( 'mysql' );

		return true;
	}

	/**
	 * Clear all filters
	 *
	 * @return void
	 */
	public function clear_filters() {
		$this->filters = array();
		$this->updated_at = current_time( 'mysql' );
	}
}

<?php
/**
 * Filter Preset Service
 *
 * Handles business logic for filter presets.
 *
 * @package ATablesCharts\Filters\Services
 * @since 1.0.5
 */

namespace ATablesCharts\Filters\Services;

use ATablesCharts\Filters\Types\FilterPreset;
use ATablesCharts\Filters\Types\Filter;
use ATablesCharts\Filters\Repositories\FilterPresetRepository;
use ATablesCharts\Shared\Utils\Logger;

/**
 * FilterPresetService Class
 *
 * Responsibilities:
 * - Manage filter preset CRUD operations
 * - Handle preset validation
 * - Manage default presets
 * - Business logic for presets
 */
class FilterPresetService {

	/**
	 * Filter preset repository
	 *
	 * @var FilterPresetRepository
	 */
	private $repository;

	/**
	 * Filter service
	 *
	 * @var FilterService
	 */
	private $filter_service;

	/**
	 * Constructor
	 *
	 * @param FilterPresetRepository $repository     Filter preset repository.
	 * @param FilterService          $filter_service Filter service.
	 */
	public function __construct( FilterPresetRepository $repository = null, FilterService $filter_service = null ) {
		$this->repository     = $repository ?? new FilterPresetRepository();
		$this->filter_service = $filter_service ?? new FilterService();
	}

	/**
	 * Create a new filter preset
	 *
	 * @param array $data Preset data.
	 * @return array Result with 'success', 'data', and 'errors' keys
	 */
	public function create_preset( array $data ) {
		try {
			// Create preset object
			$preset = new FilterPreset( $data );

			// Validate preset
			$validation = $preset->validate();
			if ( ! $validation['valid'] ) {
				return array(
					'success' => false,
					'errors'  => $validation['errors'],
				);
			}

			// Check if name already exists for this table
			if ( $this->repository->name_exists( $preset->name, $preset->table_id ) ) {
				return array(
					'success' => false,
					'errors'  => array(
						sprintf(
							/* translators: %s: preset name */
							__( 'A preset with the name "%s" already exists for this table.', 'a-tables-charts' ),
							$preset->name
						),
					),
				);
			}

			// Create preset
			$preset_id = $this->repository->create( $preset );

			if ( false === $preset_id ) {
				return array(
					'success' => false,
					'errors'  => array( __( 'Failed to create filter preset.', 'a-tables-charts' ) ),
				);
			}

			// Set as default if requested
			if ( $preset->is_default ) {
				$this->repository->set_as_default( $preset_id, $preset->table_id );
			}

			// Get created preset
			$created_preset = $this->repository->find_by_id( $preset_id );

			return array(
				'success' => true,
				'data'    => $created_preset->to_array(),
			);

		} catch ( \Exception $e ) {
			Logger::log_error( 'Filter preset creation error', array( 'error' => $e->getMessage() ) );
			return array(
				'success' => false,
				'errors'  => array( __( 'An unexpected error occurred.', 'a-tables-charts' ) ),
			);
		}
	}

	/**
	 * Update a filter preset
	 *
	 * @param int   $preset_id Preset ID.
	 * @param array $data      Updated preset data.
	 * @return array Result with 'success', 'data', and 'errors' keys
	 */
	public function update_preset( int $preset_id, array $data ) {
		try {
			// Check if preset exists
			$existing = $this->repository->find_by_id( $preset_id );
			if ( ! $existing ) {
				return array(
					'success' => false,
					'errors'  => array( __( 'Filter preset not found.', 'a-tables-charts' ) ),
				);
			}

			// Merge with existing data
			$data['id'] = $preset_id;
			$data['table_id'] = $existing->table_id;
			$data['created_by'] = $existing->created_by;
			$data['created_at'] = $existing->created_at;

			// Create updated preset object
			$preset = new FilterPreset( $data );

			// Validate preset
			$validation = $preset->validate();
			if ( ! $validation['valid'] ) {
				return array(
					'success' => false,
					'errors'  => $validation['errors'],
				);
			}

			// Check if name already exists (excluding current preset)
			if ( $this->repository->name_exists( $preset->name, $preset->table_id, $preset_id ) ) {
				return array(
					'success' => false,
					'errors'  => array(
						sprintf(
							/* translators: %s: preset name */
							__( 'A preset with the name "%s" already exists for this table.', 'a-tables-charts' ),
							$preset->name
						),
					),
				);
			}

			// Update preset
			$result = $this->repository->update( $preset_id, $preset );

			if ( ! $result ) {
				return array(
					'success' => false,
					'errors'  => array( __( 'Failed to update filter preset.', 'a-tables-charts' ) ),
				);
			}

			// Handle default status change
			if ( $preset->is_default && ! $existing->is_default ) {
				$this->repository->set_as_default( $preset_id, $preset->table_id );
			} elseif ( ! $preset->is_default && $existing->is_default ) {
				$this->repository->unset_default( $preset->table_id );
			}

			// Get updated preset
			$updated_preset = $this->repository->find_by_id( $preset_id );

			return array(
				'success' => true,
				'data'    => $updated_preset->to_array(),
			);

		} catch ( \Exception $e ) {
			Logger::log_error( 'Filter preset update error', array( 'error' => $e->getMessage() ) );
			return array(
				'success' => false,
				'errors'  => array( __( 'An unexpected error occurred.', 'a-tables-charts' ) ),
			);
		}
	}

	/**
	 * Delete a filter preset
	 *
	 * @param int $preset_id Preset ID.
	 * @return array Result with 'success' and 'errors' keys
	 */
	public function delete_preset( int $preset_id ) {
		try {
			// Check if preset exists
			if ( ! $this->repository->exists( $preset_id ) ) {
				return array(
					'success' => false,
					'errors'  => array( __( 'Filter preset not found.', 'a-tables-charts' ) ),
				);
			}

			// Delete preset
			$result = $this->repository->delete( $preset_id );

			if ( ! $result ) {
				return array(
					'success' => false,
					'errors'  => array( __( 'Failed to delete filter preset.', 'a-tables-charts' ) ),
				);
			}

			return array(
				'success' => true,
			);

		} catch ( \Exception $e ) {
			Logger::log_error( 'Filter preset deletion error', array( 'error' => $e->getMessage() ) );
			return array(
				'success' => false,
				'errors'  => array( __( 'An unexpected error occurred.', 'a-tables-charts' ) ),
			);
		}
	}

	/**
	 * Get preset by ID
	 *
	 * @param int $preset_id Preset ID.
	 * @return array Result with 'success', 'data', and 'errors' keys
	 */
	public function get_preset( int $preset_id ) {
		$preset = $this->repository->find_by_id( $preset_id );

		if ( ! $preset ) {
			return array(
				'success' => false,
				'errors'  => array( __( 'Filter preset not found.', 'a-tables-charts' ) ),
			);
		}

		return array(
			'success' => true,
			'data'    => $preset->to_array(),
		);
	}

	/**
	 * Get all presets for a table
	 *
	 * @param int   $table_id Table ID.
	 * @param array $args     Query arguments.
	 * @return array Result with 'success' and 'data' keys
	 */
	public function get_table_presets( int $table_id, array $args = array() ) {
		$presets = $this->repository->find_by_table_id( $table_id, $args );

		return array(
			'success' => true,
			'data'    => array_map(
				function( $preset ) {
					return $preset->to_array();
				},
				$presets
			),
		);
	}

	/**
	 * Get default preset for a table
	 *
	 * @param int $table_id Table ID.
	 * @return array Result with 'success' and 'data' keys
	 */
	public function get_default_preset( int $table_id ) {
		$preset = $this->repository->find_default_by_table_id( $table_id );

		if ( ! $preset ) {
			return array(
				'success' => false,
				'data'    => null,
			);
		}

		return array(
			'success' => true,
			'data'    => $preset->to_array(),
		);
	}

	/**
	 * Set preset as default
	 *
	 * @param int $preset_id Preset ID.
	 * @param int $table_id  Table ID.
	 * @return array Result with 'success' and 'errors' keys
	 */
	public function set_default( int $preset_id, int $table_id ) {
		try {
			// Check if preset exists and belongs to table
			$preset = $this->repository->find_by_id( $preset_id );

			if ( ! $preset ) {
				return array(
					'success' => false,
					'errors'  => array( __( 'Filter preset not found.', 'a-tables-charts' ) ),
				);
			}

			if ( $preset->table_id !== $table_id ) {
				return array(
					'success' => false,
					'errors'  => array( __( 'Preset does not belong to the specified table.', 'a-tables-charts' ) ),
				);
			}

			// Set as default
			$result = $this->repository->set_as_default( $preset_id, $table_id );

			if ( ! $result ) {
				return array(
					'success' => false,
					'errors'  => array( __( 'Failed to set default preset.', 'a-tables-charts' ) ),
				);
			}

			return array(
				'success' => true,
			);

		} catch ( \Exception $e ) {
			Logger::log_error( 'Set default preset error', array( 'error' => $e->getMessage() ) );
			return array(
				'success' => false,
				'errors'  => array( __( 'An unexpected error occurred.', 'a-tables-charts' ) ),
			);
		}
	}

	/**
	 * Duplicate a preset
	 *
	 * @param int    $preset_id Preset ID to duplicate.
	 * @param string $new_name  Optional new name for duplicate.
	 * @return array Result with 'success', 'data', and 'errors' keys
	 */
	public function duplicate_preset( int $preset_id, string $new_name = null ) {
		try {
			$new_preset_id = $this->repository->duplicate( $preset_id, $new_name );

			if ( false === $new_preset_id ) {
				return array(
					'success' => false,
					'errors'  => array( __( 'Failed to duplicate filter preset.', 'a-tables-charts' ) ),
				);
			}

			$new_preset = $this->repository->find_by_id( $new_preset_id );

			return array(
				'success' => true,
				'data'    => $new_preset->to_array(),
			);

		} catch ( \Exception $e ) {
			Logger::log_error( 'Duplicate preset error', array( 'error' => $e->getMessage() ) );
			return array(
				'success' => false,
				'errors'  => array( __( 'An unexpected error occurred.', 'a-tables-charts' ) ),
			);
		}
	}

	/**
	 * Apply preset to table data
	 *
	 * @param int   $preset_id Preset ID.
	 * @param array $data      Table data to filter.
	 * @return array Result with 'success', 'data', and 'errors' keys
	 */
	public function apply_preset_to_data( int $preset_id, array $data ) {
		try {
			$preset = $this->repository->find_by_id( $preset_id );

			if ( ! $preset ) {
				return array(
					'success' => false,
					'errors'  => array( __( 'Filter preset not found.', 'a-tables-charts' ) ),
				);
			}

			$filtered_data = $this->filter_service->apply_preset( $data, $preset );

			return array(
				'success' => true,
				'data'    => array(
					'filtered_data'  => $filtered_data,
					'original_count' => count( $data ),
					'filtered_count' => count( $filtered_data ),
				),
			);

		} catch ( \Exception $e ) {
			Logger::log_error( 'Apply preset error', array( 'error' => $e->getMessage() ) );
			return array(
				'success' => false,
				'errors'  => array( __( 'An unexpected error occurred while applying filters.', 'a-tables-charts' ) ),
			);
		}
	}

	/**
	 * Get preset statistics
	 *
	 * @return array Statistics array
	 */
	public function get_statistics() {
		return $this->repository->get_statistics();
	}
}

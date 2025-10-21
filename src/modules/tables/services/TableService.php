<?php
/**
 * Table Service
 *
 * Business logic for table operations.
 *
 * @package ATablesCharts\Tables\Services
 * @since 1.0.0
 */

namespace ATablesCharts\Tables\Services;

use ATablesCharts\Tables\Types\Table;
use ATablesCharts\Tables\Repositories\TableRepository;
use ATablesCharts\Shared\Utils\Logger;
use ATablesCharts\Shared\Utils\Sanitizer;
use ATablesCharts\Shared\Utils\Validator;

/**
 * TableService Class
 *
 * Responsibilities:
 * - Coordinate table operations
 * - Apply business rules
 * - Handle validation
 * - Log operations
 * - Cache management
 */
class TableService {

	/**
	 * Table repository
	 *
	 * @var TableRepository
	 */
	private $repository;

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
		$this->repository = new TableRepository();
		$this->logger     = new Logger();
	}

	/**
	 * Create a new table from import data
	 *
	 * @param string $title         Table title.
	 * @param object $import_result ImportResult object.
	 * @param string $source_type   Source type (csv, json, etc.).
	 * @param string $description   Table description (optional).
	 * @return array Result with 'success', 'table_id', and 'message'
	 */
	public function create_from_import( $title, $import_result, $source_type = 'csv', $description = '' ) {
		// Validate title using enhanced validator.
		$title_validation = Validator::table_title( $title );
		
		if ( ! $title_validation['valid'] ) {
			$error_messages = array();
			foreach ( $title_validation['errors'] as $field => $errors ) {
				$error_messages = array_merge( $error_messages, $errors );
			}
			
			return array(
				'success' => false,
				'message' => implode( ' ', $error_messages ),
				'errors'  => $title_validation['errors'],
			);
		}
		
		// Sanitize after validation.
		$title = Sanitizer::text( $title );

		// Create Table object.
		$table = Table::from_import_result( $title, $import_result, $source_type );
		$table->description = Sanitizer::textarea( $description );

		// Log operation.
		$this->logger->info( 'Creating table from import', array(
			'title'       => $title,
			'source_type' => $source_type,
			'rows'        => $import_result->row_count,
			'columns'     => $import_result->column_count,
		) );

		// Create table in database.
		$table_id = $this->repository->create( $table );

		if ( false === $table_id ) {
			$this->logger->error( 'Failed to create table', array(
				'title' => $title,
			) );

			return array(
				'success' => false,
				'message' => __( 'Failed to create table. Please try again.', 'a-tables-charts' ),
			);
		}

		$this->logger->info( 'Table created successfully', array(
			'table_id' => $table_id,
			'title'    => $title,
		) );

		return array(
			'success'  => true,
			'table_id' => $table_id,
			'message'  => __( 'Table created successfully!', 'a-tables-charts' ),
		);
	}

	/**
	 * Get table by ID
	 *
	 * @param int $id Table ID.
	 * @return Table|null Table object or null
	 */
	public function get_table( $id ) {
		return $this->repository->find_by_id( $id );
	}

	/**
	 * Get all tables
	 *
	 * @param array $args Query arguments.
	 * @return array Array of tables with metadata
	 */
	public function get_all_tables( $args = array() ) {
		$tables = $this->repository->find_all( $args );
		$total  = $this->repository->count( $args );

		return array(
			'tables' => $tables,
			'total'  => $total,
			'page'   => isset( $args['page'] ) ? $args['page'] : 1,
			'per_page' => isset( $args['per_page'] ) ? $args['per_page'] : 20,
		);
	}

	/**
	 * Update table
	 *
	 * @param int   $id   Table ID.
	 * @param array $data Updated data.
	 * @return array Result with 'success' and 'message'
	 */
	public function update_table( $id, $data ) {
		// Get existing table.
		$table = $this->repository->find_by_id( $id );

		if ( ! $table ) {
			return array(
				'success' => false,
				'message' => __( 'Table not found.', 'a-tables-charts' ),
			);
		}

		// Update fields.
		if ( isset( $data['title'] ) ) {
			$table->title = Sanitizer::text( $data['title'] );
		}

		if ( isset( $data['description'] ) ) {
			$table->description = Sanitizer::textarea( $data['description'] );
		}

		if ( isset( $data['status'] ) ) {
			$table->status = Sanitizer::text( $data['status'] );
		}
		
		// Update source data (headers and data).
		if ( isset( $data['source_data'] ) ) {
			$table->source_data = $data['source_data'];
		}
		
		// Update row and column counts.
		if ( isset( $data['row_count'] ) ) {
			$table->row_count = (int) $data['row_count'];
		}
		
		if ( isset( $data['column_count'] ) ) {
			$table->column_count = (int) $data['column_count'];
		}
		
		// Update display settings if provided
		if ( isset( $data['display_settings'] ) ) {
			if ( is_array( $data['display_settings'] ) && ! empty( $data['display_settings'] ) ) {
				// Set display settings on the table object
				$table->set_display_settings( $data['display_settings'] );
			} else {
				// Empty array means clear settings
				$table->display_settings = null;
			}
		}
		
		// Update timestamp.
		$table->updated_at = current_time( 'mysql' );

		// Update in database.
		// Note: The repository's update method automatically handles updating table data rows
		$result = $this->repository->update( $id, $table );

		if ( ! $result ) {
			$this->logger->error( 'Failed to update table', array( 'table_id' => $id ) );
			return array(
				'success' => false,
				'message' => __( 'Failed to update table.', 'a-tables-charts' ),
			);
		}

		$this->logger->info( 'Table updated', array( 'table_id' => $id ) );

		return array(
			'success' => true,
			'message' => __( 'Table updated successfully!', 'a-tables-charts' ),
		);
	}

	/**
	 * Delete table
	 *
	 * @param int $id Table ID.
	 * @return array Result with 'success' and 'message'
	 */
	public function delete_table( $id ) {
		// Check if table exists.
		$table = $this->repository->find_by_id( $id );

		if ( ! $table ) {
			return array(
				'success' => false,
				'message' => __( 'Table not found.', 'a-tables-charts' ),
			);
		}

		// Delete table.
		$result = $this->repository->delete( $id );

		if ( ! $result ) {
			return array(
				'success' => false,
				'message' => __( 'Failed to delete table.', 'a-tables-charts' ),
			);
		}

		$this->logger->info( 'Table deleted', array(
			'table_id' => $id,
			'title'    => $table->title,
		) );

		return array(
			'success' => true,
			'message' => __( 'Table deleted successfully!', 'a-tables-charts' ),
		);
	}

	/**
	 * Duplicate table
	 *
	 * @param int    $id        Table ID to duplicate.
	 * @param string $new_title New table title (optional).
	 * @return array Result with 'success', 'table_id', and 'message'
	 */
	public function duplicate_table( $id, $new_title = '' ) {
		// Get original table.
		$original = $this->repository->find_by_id( $id );

		if ( ! $original ) {
			return array(
				'success' => false,
				'message' => __( 'Table not found.', 'a-tables-charts' ),
			);
		}

		// Create new title.
		if ( empty( $new_title ) ) {
			$new_title = $original->title . ' (Copy)';
		}

		// Create duplicate.
		$duplicate = new Table( $original->to_array() );
		$duplicate->id = null; // Reset ID for new table.
		$duplicate->title = $new_title;
		$duplicate->created_at = current_time( 'mysql' );
		$duplicate->updated_at = current_time( 'mysql' );

		$new_id = $this->repository->create( $duplicate );

		if ( false === $new_id ) {
			return array(
				'success' => false,
				'message' => __( 'Failed to duplicate table.', 'a-tables-charts' ),
			);
		}

		return array(
			'success'  => true,
			'table_id' => $new_id,
			'message'  => __( 'Table duplicated successfully!', 'a-tables-charts' ),
		);
	}

	/**
	 * Search tables
	 *
	 * @param string $search_term Search term.
	 * @param array  $args        Query arguments.
	 * @return array Search results
	 */
	public function search_tables( $search_term, $args = array() ) {
		$tables = $this->repository->search( $search_term, $args );

		return array(
			'tables' => $tables,
			'total'  => count( $tables ),
			'search' => $search_term,
		);
	}

	/**
	 * Get table data with pagination
	 *
	 * @param int   $table_id Table ID.
	 * @param array $args     Query arguments.
	 * @return array Table data with pagination info
	 */
	public function get_table_data( $table_id, $args = array() ) {
		$table = $this->repository->find_by_id( $table_id );

		if ( ! $table ) {
			return array(
				'success' => false,
				'message' => __( 'Table not found.', 'a-tables-charts' ),
			);
		}

		$data = $this->repository->get_table_data( $table_id, $args );

		return array(
			'success' => true,
			'headers' => $table->get_headers(),
			'data'    => $data,
			'total_rows' => $table->row_count,
		);
	}
}

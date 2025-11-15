<?php
/**
 * Bulk Actions Service
 *
 * Handles business logic for bulk operations on table data
 *
 * @package ATablesCharts\Bulk\Services
 * @since 1.0.0
 */

namespace ATablesCharts\Bulk\Services;

/**
 * BulkActionsService Class
 *
 * Responsibilities:
 * - Process bulk delete operations
 * - Process bulk duplicate operations
 * - Process bulk edit operations
 * - Validate bulk operation data
 * - Return processed data and operation results
 */
class BulkActionsService {

	/**
	 * Perform bulk delete on table data
	 *
	 * @param array $table_data  Current table data.
	 * @param array $row_indices Row indices to delete.
	 * @return array Result with success status, message, and updated data
	 */
	public function bulk_delete( $table_data, $row_indices ) {
		// Validate inputs
		if ( empty( $row_indices ) ) {
			return array(
				'success' => false,
				'message' => __( 'No rows selected.', 'a-tables-charts' ),
				'data'    => $table_data,
			);
		}

		if ( ! is_array( $table_data ) ) {
			return array(
				'success' => false,
				'message' => __( 'Invalid table data.', 'a-tables-charts' ),
				'data'    => array(),
			);
		}

		// Ensure indices are integers
		$row_indices = array_map( 'intval', $row_indices );

		// Sort indices in descending order to avoid index shifting
		rsort( $row_indices );

		// Remove rows
		$deleted_count = 0;
		foreach ( $row_indices as $index ) {
			if ( isset( $table_data[ $index ] ) ) {
				unset( $table_data[ $index ] );
				$deleted_count++;
			}
		}

		// Reindex array to maintain sequential indices
		$table_data = array_values( $table_data );

		return array(
			'success' => true,
			'message' => sprintf(
				_n(
					'%d row deleted successfully.',
					'%d rows deleted successfully.',
					$deleted_count,
					'a-tables-charts'
				),
				$deleted_count
			),
			'data'    => $table_data,
			'count'   => $deleted_count,
		);
	}

	/**
	 * Perform bulk duplicate on table data
	 *
	 * @param array $table_data  Current table data.
	 * @param array $row_indices Row indices to duplicate.
	 * @return array Result with success status, message, and updated data
	 */
	public function bulk_duplicate( $table_data, $row_indices ) {
		// Validate inputs
		if ( empty( $row_indices ) ) {
			return array(
				'success' => false,
				'message' => __( 'No rows selected.', 'a-tables-charts' ),
				'data'    => $table_data,
			);
		}

		if ( ! is_array( $table_data ) ) {
			return array(
				'success' => false,
				'message' => __( 'Invalid table data.', 'a-tables-charts' ),
				'data'    => array(),
			);
		}

		// Ensure indices are integers
		$row_indices = array_map( 'intval', $row_indices );

		// Duplicate rows (add copies at the end)
		$duplicated_count = 0;
		foreach ( $row_indices as $index ) {
			if ( isset( $table_data[ $index ] ) ) {
				$table_data[] = $table_data[ $index ];
				$duplicated_count++;
			}
		}

		return array(
			'success' => true,
			'message' => sprintf(
				_n(
					'%d row duplicated successfully.',
					'%d rows duplicated successfully.',
					$duplicated_count,
					'a-tables-charts'
				),
				$duplicated_count
			),
			'data'    => $table_data,
			'count'   => $duplicated_count,
		);
	}

	/**
	 * Perform bulk edit on table data
	 *
	 * @param array  $table_data  Current table data.
	 * @param array  $headers     Table headers.
	 * @param array  $row_indices Row indices to edit.
	 * @param string $column      Column name to edit.
	 * @param string $value       New value for the column.
	 * @return array Result with success status, message, and updated data
	 */
	public function bulk_edit( $table_data, $headers, $row_indices, $column, $value ) {
		// Validate inputs
		if ( empty( $row_indices ) ) {
			return array(
				'success' => false,
				'message' => __( 'No rows selected.', 'a-tables-charts' ),
				'data'    => $table_data,
			);
		}

		if ( empty( $column ) ) {
			return array(
				'success' => false,
				'message' => __( 'No column selected.', 'a-tables-charts' ),
				'data'    => $table_data,
			);
		}

		if ( ! is_array( $table_data ) ) {
			return array(
				'success' => false,
				'message' => __( 'Invalid table data.', 'a-tables-charts' ),
				'data'    => array(),
			);
		}

		if ( ! is_array( $headers ) ) {
			return array(
				'success' => false,
				'message' => __( 'Invalid headers.', 'a-tables-charts' ),
				'data'    => $table_data,
			);
		}

		// Verify column exists
		if ( ! in_array( $column, $headers, true ) ) {
			return array(
				'success' => false,
				'message' => __( 'Invalid column selected.', 'a-tables-charts' ),
				'data'    => $table_data,
			);
		}

		// Ensure indices are integers
		$row_indices = array_map( 'intval', $row_indices );

		// Update rows
		$updated_count = 0;
		foreach ( $row_indices as $index ) {
			if ( isset( $table_data[ $index ] ) ) {
				$table_data[ $index ][ $column ] = $value;
				$updated_count++;
			}
		}

		return array(
			'success' => true,
			'message' => sprintf(
				_n(
					'%d row updated successfully.',
					'%d rows updated successfully.',
					$updated_count,
					'a-tables-charts'
				),
				$updated_count
			),
			'data'    => $table_data,
			'count'   => $updated_count,
		);
	}

	/**
	 * Validate bulk operation data
	 *
	 * @param array  $table_data  Table data.
	 * @param array  $row_indices Row indices.
	 * @param string $operation   Operation type (delete, duplicate, edit).
	 * @return array Validation result
	 */
	public function validate_bulk_operation( $table_data, $row_indices, $operation ) {
		$errors = array();

		// Check table data
		if ( ! is_array( $table_data ) || empty( $table_data ) ) {
			$errors[] = __( 'Table data is required.', 'a-tables-charts' );
		}

		// Check row indices
		if ( ! is_array( $row_indices ) || empty( $row_indices ) ) {
			$errors[] = __( 'At least one row must be selected.', 'a-tables-charts' );
		}

		// Check operation type
		$valid_operations = array( 'delete', 'duplicate', 'edit' );
		if ( ! in_array( $operation, $valid_operations, true ) ) {
			$errors[] = __( 'Invalid operation type.', 'a-tables-charts' );
		}

		// Validate row indices exist in data
		if ( is_array( $table_data ) && is_array( $row_indices ) ) {
			foreach ( $row_indices as $index ) {
				if ( ! isset( $table_data[ intval( $index ) ] ) ) {
					$errors[] = sprintf(
						/* translators: %d: row index */
						__( 'Row index %d does not exist.', 'a-tables-charts' ),
						$index
					);
				}
			}
		}

		return array(
			'valid'  => empty( $errors ),
			'errors' => $errors,
		);
	}

	/**
	 * Get statistics about bulk operation
	 *
	 * @param array  $original_data Original table data.
	 * @param array  $updated_data  Updated table data.
	 * @param string $operation     Operation type.
	 * @return array Statistics
	 */
	public function get_operation_stats( $original_data, $updated_data, $operation ) {
		$original_count = is_array( $original_data ) ? count( $original_data ) : 0;
		$updated_count = is_array( $updated_data ) ? count( $updated_data ) : 0;

		$stats = array(
			'operation'       => $operation,
			'original_rows'   => $original_count,
			'updated_rows'    => $updated_count,
			'rows_changed'    => abs( $updated_count - $original_count ),
		);

		switch ( $operation ) {
			case 'delete':
				$stats['rows_deleted'] = $original_count - $updated_count;
				break;

			case 'duplicate':
				$stats['rows_added'] = $updated_count - $original_count;
				break;

			case 'edit':
				$stats['rows_modified'] = $updated_count; // Same count, but values changed
				break;
		}

		return $stats;
	}

	/**
	 * Preview bulk operation without applying it
	 *
	 * @param array  $table_data  Current table data.
	 * @param array  $row_indices Row indices.
	 * @param string $operation   Operation type.
	 * @param array  $options     Additional options (column, value for edit).
	 * @return array Preview result
	 */
	public function preview_operation( $table_data, $row_indices, $operation, $options = array() ) {
		$preview = array(
			'operation'      => $operation,
			'affected_rows'  => count( $row_indices ),
			'current_count'  => count( $table_data ),
		);

		switch ( $operation ) {
			case 'delete':
				$preview['new_count'] = count( $table_data ) - count( $row_indices );
				$preview['action'] = sprintf(
					_n(
						'Will delete %d row',
						'Will delete %d rows',
						count( $row_indices ),
						'a-tables-charts'
					),
					count( $row_indices )
				);
				break;

			case 'duplicate':
				$preview['new_count'] = count( $table_data ) + count( $row_indices );
				$preview['action'] = sprintf(
					_n(
						'Will duplicate %d row',
						'Will duplicate %d rows',
						count( $row_indices ),
						'a-tables-charts'
					),
					count( $row_indices )
				);
				break;

			case 'edit':
				$preview['new_count'] = count( $table_data );
				$column = isset( $options['column'] ) ? $options['column'] : '';
				$value = isset( $options['value'] ) ? $options['value'] : '';
				$preview['action'] = sprintf(
					/* translators: 1: number of rows, 2: column name, 3: new value */
					_n(
						'Will update %1$d row: set %2$s = "%3$s"',
						'Will update %1$d rows: set %2$s = "%3$s"',
						count( $row_indices ),
						'a-tables-charts'
					),
					count( $row_indices ),
					$column,
					$value
				);
				break;
		}

		return $preview;
	}
}

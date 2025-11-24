<?php
/**
 * Table Service
 *
 * @package ATables\Features\Tables
 * @since 2.0.0
 */

namespace ATables\Features\Tables;

/**
 * TableService Class
 *
 * Business logic for table operations.
 */
class TableService {

    /**
     * Repository instance
     *
     * @var TableRepository
     */
    private $repository;

    /**
     * Constructor
     */
    public function __construct() {
        $this->repository = new TableRepository();
    }

    /**
     * Get all tables with pagination
     *
     * @param array $args Query arguments.
     * @return array
     */
    public function get_tables( $args = array() ) {
        $tables = $this->repository->get_all( $args );
        $total = $this->repository->count( $args );

        return array(
            'tables' => $tables,
            'total'  => $total,
        );
    }

    /**
     * Get table with columns and rows
     *
     * @param int   $id   Table ID.
     * @param array $args Query arguments for rows.
     * @return array|null
     */
    public function get_table( $id, $args = array() ) {
        $table = $this->repository->get_by_id( $id );

        if ( ! $table ) {
            return null;
        }

        $table['columns'] = $this->repository->get_columns( $id );
        $table['rows'] = $this->repository->get_rows( $id, $args );
        $table['total_rows'] = $this->repository->count_rows( $id, $args );

        return $table;
    }

    /**
     * Create table from uploaded file
     *
     * @param string $title       Table title.
     * @param array  $columns     Columns data.
     * @param array  $rows        Rows data.
     * @param string $description Table description.
     * @return int|false Table ID on success, false on failure.
     */
    public function create_from_data( $title, $columns, $rows, $description = '' ) {
        // Create table
        $table_id = $this->repository->create( array(
            'title'       => sanitize_text_field( $title ),
            'description' => sanitize_textarea_field( $description ),
            'source_type' => 'upload',
        ) );

        if ( ! $table_id ) {
            return false;
        }

        // Save columns
        $columns_formatted = array();
        foreach ( $columns as $column_name ) {
            $columns_formatted[] = array(
                'name'    => sanitize_text_field( $column_name ),
                'type'    => $this->detect_column_type( $rows, $column_name ),
                'visible' => 1,
            );
        }

        $this->repository->save_columns( $table_id, $columns_formatted );

        // Save rows
        $rows_sanitized = array();
        foreach ( $rows as $row ) {
            $sanitized_row = array();
            foreach ( $row as $key => $value ) {
                $sanitized_row[ $key ] = sanitize_text_field( $value );
            }
            $rows_sanitized[] = $sanitized_row;
        }

        $this->repository->save_rows( $table_id, $rows_sanitized );

        return $table_id;
    }

    /**
     * Update table
     *
     * @param int   $id   Table ID.
     * @param array $data Table data.
     * @return bool
     */
    public function update_table( $id, $data ) {
        $update_data = array();

        if ( isset( $data['title'] ) ) {
            $update_data['title'] = sanitize_text_field( $data['title'] );
        }

        if ( isset( $data['description'] ) ) {
            $update_data['description'] = sanitize_textarea_field( $data['description'] );
        }

        if ( isset( $data['status'] ) ) {
            $update_data['status'] = sanitize_text_field( $data['status'] );
        }

        return $this->repository->update( $id, $update_data );
    }

    /**
     * Delete table
     *
     * @param int $id Table ID.
     * @return bool
     */
    public function delete_table( $id ) {
        return $this->repository->delete( $id );
    }

    /**
     * Detect column type from data
     *
     * @param array  $rows        Rows data.
     * @param string $column_name Column name.
     * @return string
     */
    private function detect_column_type( $rows, $column_name ) {
        // Sample first 10 rows
        $sample = array_slice( $rows, 0, 10 );

        $is_numeric = true;
        $is_date = true;

        foreach ( $sample as $row ) {
            if ( ! isset( $row[ $column_name ] ) ) {
                continue;
            }

            $value = $row[ $column_name ];

            // Check if numeric
            if ( ! is_numeric( $value ) ) {
                $is_numeric = false;
            }

            // Check if date
            if ( ! strtotime( $value ) ) {
                $is_date = false;
            }
        }

        if ( $is_numeric ) {
            return 'number';
        }

        if ( $is_date ) {
            return 'date';
        }

        return 'text';
    }
}

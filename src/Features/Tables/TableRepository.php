<?php
/**
 * Table Repository
 *
 * @package ATables\Features\Tables
 * @since 2.0.0
 */

namespace ATables\Features\Tables;

/**
 * TableRepository Class
 *
 * Handles all database operations for tables.
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
    private $tables_table;

    /**
     * Columns table name
     *
     * @var string
     */
    private $columns_table;

    /**
     * Rows table name
     *
     * @var string
     */
    private $rows_table;

    /**
     * Constructor
     */
    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->tables_table = $wpdb->prefix . 'atables_tables';
        $this->columns_table = $wpdb->prefix . 'atables_columns';
        $this->rows_table = $wpdb->prefix . 'atables_rows';
    }

    /**
     * Get all tables
     *
     * @param array $args Query arguments.
     * @return array
     */
    public function get_all( $args = array() ) {
        $defaults = array(
            'status'   => 'active',
            'per_page' => 20,
            'page'     => 1,
            'orderby'  => 'created_at',
            'order'    => 'DESC',
            'search'   => '',
        );

        $args = wp_parse_args( $args, $defaults );

        $where = "WHERE 1=1";

        if ( ! empty( $args['status'] ) ) {
            $where .= $this->wpdb->prepare( ' AND status = %s', $args['status'] );
        }

        if ( ! empty( $args['search'] ) ) {
            $search = '%' . $this->wpdb->esc_like( $args['search'] ) . '%';
            $where .= $this->wpdb->prepare( ' AND (title LIKE %s OR description LIKE %s)', $search, $search );
        }

        $offset = ( $args['page'] - 1 ) * $args['per_page'];
        $orderby = sanitize_sql_orderby( $args['orderby'] . ' ' . $args['order'] );

        $query = "SELECT * FROM {$this->tables_table} {$where} ORDER BY {$orderby} LIMIT %d OFFSET %d";

        $results = $this->wpdb->get_results(
            $this->wpdb->prepare( $query, $args['per_page'], $offset ),
            ARRAY_A
        );

        return $results ? $results : array();
    }

    /**
     * Get table by ID
     *
     * @param int $id Table ID.
     * @return array|null
     */
    public function get_by_id( $id ) {
        $query = $this->wpdb->prepare(
            "SELECT * FROM {$this->tables_table} WHERE id = %d",
            $id
        );

        return $this->wpdb->get_row( $query, ARRAY_A );
    }

    /**
     * Count tables
     *
     * @param array $args Query arguments.
     * @return int
     */
    public function count( $args = array() ) {
        $where = "WHERE 1=1";

        if ( ! empty( $args['status'] ) ) {
            $where .= $this->wpdb->prepare( ' AND status = %s', $args['status'] );
        }

        if ( ! empty( $args['search'] ) ) {
            $search = '%' . $this->wpdb->esc_like( $args['search'] ) . '%';
            $where .= $this->wpdb->prepare( ' AND (title LIKE %s OR description LIKE %s)', $search, $search );
        }

        $query = "SELECT COUNT(*) FROM {$this->tables_table} {$where}";

        return (int) $this->wpdb->get_var( $query );
    }

    /**
     * Create table
     *
     * @param array $data Table data.
     * @return int|false Table ID on success, false on failure.
     */
    public function create( $data ) {
        $defaults = array(
            'title'       => '',
            'description' => '',
            'source_type' => 'upload',
            'status'      => 'active',
            'created_by'  => get_current_user_id(),
        );

        $data = wp_parse_args( $data, $defaults );

        $result = $this->wpdb->insert(
            $this->tables_table,
            $data,
            array( '%s', '%s', '%s', '%s', '%d' )
        );

        if ( false === $result ) {
            return false;
        }

        return $this->wpdb->insert_id;
    }

    /**
     * Update table
     *
     * @param int   $id   Table ID.
     * @param array $data Table data.
     * @return bool
     */
    public function update( $id, $data ) {
        $result = $this->wpdb->update(
            $this->tables_table,
            $data,
            array( 'id' => $id ),
            array( '%s', '%s', '%s', '%s', '%d', '%d' ),
            array( '%d' )
        );

        return false !== $result;
    }

    /**
     * Delete table
     *
     * @param int $id Table ID.
     * @return bool
     */
    public function delete( $id ) {
        // Delete rows
        $this->wpdb->delete( $this->rows_table, array( 'table_id' => $id ), array( '%d' ) );

        // Delete columns
        $this->wpdb->delete( $this->columns_table, array( 'table_id' => $id ), array( '%d' ) );

        // Delete table
        $result = $this->wpdb->delete( $this->tables_table, array( 'id' => $id ), array( '%d' ) );

        return false !== $result;
    }

    /**
     * Get table columns
     *
     * @param int $table_id Table ID.
     * @return array
     */
    public function get_columns( $table_id ) {
        $query = $this->wpdb->prepare(
            "SELECT * FROM {$this->columns_table} WHERE table_id = %d ORDER BY column_order ASC",
            $table_id
        );

        $results = $this->wpdb->get_results( $query, ARRAY_A );

        return $results ? $results : array();
    }

    /**
     * Save columns
     *
     * @param int   $table_id Table ID.
     * @param array $columns  Columns data.
     * @return bool
     */
    public function save_columns( $table_id, $columns ) {
        // Delete existing columns
        $this->wpdb->delete( $this->columns_table, array( 'table_id' => $table_id ), array( '%d' ) );

        // Insert new columns
        foreach ( $columns as $index => $column ) {
            $result = $this->wpdb->insert(
                $this->columns_table,
                array(
                    'table_id'     => $table_id,
                    'column_name'  => $column['name'],
                    'column_type'  => $column['type'] ?? 'text',
                    'column_order' => $index,
                    'is_visible'   => $column['visible'] ?? 1,
                ),
                array( '%d', '%s', '%s', '%d', '%d' )
            );

            if ( false === $result ) {
                return false;
            }
        }

        // Update column count
        $this->wpdb->update(
            $this->tables_table,
            array( 'column_count' => count( $columns ) ),
            array( 'id' => $table_id ),
            array( '%d' ),
            array( '%d' )
        );

        return true;
    }

    /**
     * Get table rows
     *
     * @param int   $table_id Table ID.
     * @param array $args     Query arguments.
     * @return array
     */
    public function get_rows( $table_id, $args = array() ) {
        $defaults = array(
            'per_page' => 0,
            'page'     => 1,
            'search'   => '',
        );

        $args = wp_parse_args( $args, $defaults );

        $where = "WHERE table_id = %d";
        $params = array( $table_id );

        if ( ! empty( $args['search'] ) ) {
            $search = '%' . $this->wpdb->esc_like( $args['search'] ) . '%';
            $where .= " AND row_data LIKE %s";
            $params[] = $search;
        }

        $query = "SELECT * FROM {$this->rows_table} {$where} ORDER BY row_order ASC";

        if ( $args['per_page'] > 0 ) {
            $offset = ( $args['page'] - 1 ) * $args['per_page'];
            $query .= " LIMIT %d OFFSET %d";
            $params[] = $args['per_page'];
            $params[] = $offset;
        }

        $results = $this->wpdb->get_results(
            $this->wpdb->prepare( $query, $params ),
            ARRAY_A
        );

        if ( ! $results ) {
            return array();
        }

        // Decode JSON data
        return array_map( function( $row ) {
            $row['row_data'] = json_decode( $row['row_data'], true );
            return $row;
        }, $results );
    }

    /**
     * Count rows
     *
     * @param int   $table_id Table ID.
     * @param array $args     Query arguments.
     * @return int
     */
    public function count_rows( $table_id, $args = array() ) {
        $where = "WHERE table_id = %d";
        $params = array( $table_id );

        if ( ! empty( $args['search'] ) ) {
            $search = '%' . $this->wpdb->esc_like( $args['search'] ) . '%';
            $where .= " AND row_data LIKE %s";
            $params[] = $search;
        }

        $query = "SELECT COUNT(*) FROM {$this->rows_table} {$where}";

        return (int) $this->wpdb->get_var( $this->wpdb->prepare( $query, $params ) );
    }

    /**
     * Save rows
     *
     * @param int   $table_id Table ID.
     * @param array $rows     Rows data.
     * @return bool
     */
    public function save_rows( $table_id, $rows ) {
        // Delete existing rows
        $this->wpdb->delete( $this->rows_table, array( 'table_id' => $table_id ), array( '%d' ) );

        // Insert new rows
        foreach ( $rows as $index => $row ) {
            $result = $this->wpdb->insert(
                $this->rows_table,
                array(
                    'table_id'  => $table_id,
                    'row_order' => $index,
                    'row_data'  => wp_json_encode( $row ),
                ),
                array( '%d', '%d', '%s' )
            );

            if ( false === $result ) {
                return false;
            }
        }

        // Update row count
        $this->wpdb->update(
            $this->tables_table,
            array( 'row_count' => count( $rows ) ),
            array( 'id' => $table_id ),
            array( '%d' ),
            array( '%d' )
        );

        return true;
    }
}

<?php
/**
 * Table Controller
 *
 * @package ATables\Features\Tables
 * @since 2.0.0
 */

namespace ATables\Features\Tables;

/**
 * TableController Class
 *
 * Handles AJAX requests for table operations.
 */
class TableController {

    /**
     * Service instance
     *
     * @var TableService
     */
    private $service;

    /**
     * Constructor
     */
    public function __construct() {
        $this->service = new TableService();
    }

    /**
     * Get tables via AJAX
     */
    public function get_tables() {
        check_ajax_referer( 'atables_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'a-tables-charts' ) ) );
        }

        $args = array(
            'per_page' => isset( $_POST['per_page'] ) ? intval( $_POST['per_page'] ) : 20,
            'page'     => isset( $_POST['page'] ) ? intval( $_POST['page'] ) : 1,
            'search'   => isset( $_POST['search'] ) ? sanitize_text_field( $_POST['search'] ) : '',
            'status'   => isset( $_POST['status'] ) ? sanitize_text_field( $_POST['status'] ) : 'active',
        );

        $result = $this->service->get_tables( $args );

        wp_send_json_success( $result );
    }

    /**
     * Save table via AJAX
     */
    public function save_table() {
        check_ajax_referer( 'atables_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'a-tables-charts' ) ) );
        }

        $table_id = isset( $_POST['table_id'] ) ? intval( $_POST['table_id'] ) : 0;
        $data = isset( $_POST['data'] ) ? $_POST['data'] : array();

        if ( $table_id ) {
            // Update existing table
            $success = $this->service->update_table( $table_id, $data );

            if ( $success ) {
                wp_send_json_success( array(
                    'message' => __( 'Table updated successfully!', 'a-tables-charts' ),
                ) );
            } else {
                wp_send_json_error( array(
                    'message' => __( 'Failed to update table.', 'a-tables-charts' ),
                ) );
            }
        } else {
            wp_send_json_error( array(
                'message' => __( 'Invalid table ID.', 'a-tables-charts' ),
            ) );
        }
    }

    /**
     * Delete table via AJAX
     */
    public function delete_table() {
        check_ajax_referer( 'atables_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'a-tables-charts' ) ) );
        }

        $table_id = isset( $_POST['table_id'] ) ? intval( $_POST['table_id'] ) : 0;

        if ( ! $table_id ) {
            wp_send_json_error( array(
                'message' => __( 'Invalid table ID.', 'a-tables-charts' ),
            ) );
        }

        $success = $this->service->delete_table( $table_id );

        if ( $success ) {
            wp_send_json_success( array(
                'message' => __( 'Table deleted successfully!', 'a-tables-charts' ),
            ) );
        } else {
            wp_send_json_error( array(
                'message' => __( 'Failed to delete table.', 'a-tables-charts' ),
            ) );
        }
    }

    /**
     * Save table data (columns and rows) via AJAX
     */
    public function save_table_data() {
        check_ajax_referer( 'atables_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'a-tables-charts' ) ) );
        }

        $table_id = isset( $_POST['table_id'] ) ? intval( $_POST['table_id'] ) : 0;
        $columns = isset( $_POST['columns'] ) ? $_POST['columns'] : array();
        $rows = isset( $_POST['rows'] ) ? $_POST['rows'] : array();

        if ( ! $table_id ) {
            wp_send_json_error( array(
                'message' => __( 'Invalid table ID.', 'a-tables-charts' ),
            ) );
        }

        // Verify table exists
        $table = $this->service->get_table( $table_id );
        if ( ! $table ) {
            wp_send_json_error( array(
                'message' => __( 'Table not found.', 'a-tables-charts' ),
            ) );
        }

        global $wpdb;

        // Start transaction
        $wpdb->query( 'START TRANSACTION' );

        try {
            $columns_table = $wpdb->prefix . 'atables_columns';
            $rows_table = $wpdb->prefix . 'atables_rows';

            // Update columns
            $updated_columns = array();
            foreach ( $columns as $column_data ) {
                $column_id = isset( $column_data['id'] ) ? $column_data['id'] : 0;
                $column_name = isset( $column_data['name'] ) ? sanitize_text_field( $column_data['name'] ) : '';

                if ( empty( $column_name ) ) {
                    continue;
                }

                // If it's a new column (id starts with 'new-')
                if ( strpos( $column_id, 'new-' ) === 0 ) {
                    $wpdb->insert(
                        $columns_table,
                        array(
                            'table_id' => $table_id,
                            'column_name' => $column_name,
                            'column_type' => 'text',
                            'column_order' => count( $updated_columns ),
                            'is_visible' => 1,
                        ),
                        array( '%d', '%s', '%s', '%d', '%d' )
                    );
                    $column_id = $wpdb->insert_id;
                } else {
                    // Update existing column
                    $wpdb->update(
                        $columns_table,
                        array(
                            'column_name' => $column_name,
                            'column_order' => count( $updated_columns ),
                        ),
                        array( 'id' => intval( $column_id ) ),
                        array( '%s', '%d' ),
                        array( '%d' )
                    );
                }

                $updated_columns[] = array(
                    'id' => $column_id,
                    'name' => $column_name,
                );
            }

            // Delete columns that are no longer in the list
            $existing_columns = $wpdb->get_col(
                $wpdb->prepare(
                    "SELECT id FROM {$columns_table} WHERE table_id = %d",
                    $table_id
                )
            );

            $updated_column_ids = array_map( function( $col ) {
                return intval( $col['id'] );
            }, $updated_columns );

            foreach ( $existing_columns as $existing_id ) {
                if ( ! in_array( intval( $existing_id ), $updated_column_ids ) ) {
                    $wpdb->delete(
                        $columns_table,
                        array( 'id' => intval( $existing_id ) ),
                        array( '%d' )
                    );
                }
            }

            // Update rows
            $updated_row_ids = array();
            foreach ( $rows as $row_data ) {
                $row_id = isset( $row_data['id'] ) ? $row_data['id'] : 0;
                $row_content = isset( $row_data['data'] ) ? $row_data['data'] : array();

                // Sanitize row data
                $sanitized_row = array();
                foreach ( $row_content as $col_name => $value ) {
                    $sanitized_row[ sanitize_text_field( $col_name ) ] = sanitize_text_field( $value );
                }

                // If it's a new row (id starts with 'new-')
                if ( strpos( $row_id, 'new-' ) === 0 ) {
                    $wpdb->insert(
                        $rows_table,
                        array(
                            'table_id' => $table_id,
                            'row_data' => wp_json_encode( $sanitized_row ),
                            'row_order' => count( $updated_row_ids ),
                        ),
                        array( '%d', '%s', '%d' )
                    );
                    $row_id = $wpdb->insert_id;
                } else {
                    // Update existing row
                    $wpdb->update(
                        $rows_table,
                        array(
                            'row_data' => wp_json_encode( $sanitized_row ),
                            'row_order' => count( $updated_row_ids ),
                        ),
                        array( 'id' => intval( $row_id ) ),
                        array( '%s', '%d' ),
                        array( '%d' )
                    );
                }

                $updated_row_ids[] = intval( $row_id );
            }

            // Delete rows that are no longer in the list
            $existing_rows = $wpdb->get_col(
                $wpdb->prepare(
                    "SELECT id FROM {$rows_table} WHERE table_id = %d",
                    $table_id
                )
            );

            foreach ( $existing_rows as $existing_id ) {
                if ( ! in_array( intval( $existing_id ), $updated_row_ids ) ) {
                    $wpdb->delete(
                        $rows_table,
                        array( 'id' => intval( $existing_id ) ),
                        array( '%d' )
                    );
                }
            }

            // Commit transaction
            $wpdb->query( 'COMMIT' );

            wp_send_json_success( array(
                'message' => __( 'Table data saved successfully!', 'a-tables-charts' ),
            ) );

        } catch ( Exception $e ) {
            // Rollback on error
            $wpdb->query( 'ROLLBACK' );

            wp_send_json_error( array(
                'message' => __( 'Failed to save table data: ', 'a-tables-charts' ) . $e->getMessage(),
            ) );
        }
    }
}

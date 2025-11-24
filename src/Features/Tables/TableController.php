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
}

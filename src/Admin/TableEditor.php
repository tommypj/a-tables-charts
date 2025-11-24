<?php
/**
 * Table Editor
 *
 * @package ATables\Admin
 * @since 3.0.0
 */

namespace ATables\Admin;

/**
 * TableEditor Class
 */
class TableEditor {

    /**
     * Render new table page
     */
    public static function render_new() {
        $table = null;
        include ATABLES_PLUGIN_DIR . 'templates/admin/edit.php';
    }

    /**
     * Render edit table page
     */
    public static function render_edit() {
        $table_id = isset( $_GET['id'] ) ? intval( $_GET['id'] ) : 0;

        if ( ! $table_id ) {
            wp_die( 'Invalid table ID' );
        }

        global $wpdb;

        // Get table
        $table = $wpdb->get_row(
            $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}atables_tables WHERE id = %d", $table_id ),
            ARRAY_A
        );

        if ( ! $table ) {
            wp_die( 'Table not found' );
        }

        // Get columns
        $table['columns'] = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}atables_columns WHERE table_id = %d ORDER BY column_order ASC",
                $table_id
            ),
            ARRAY_A
        );

        // Get rows
        $table['rows'] = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}atables_rows WHERE table_id = %d ORDER BY row_order ASC",
                $table_id
            ),
            ARRAY_A
        );

        // Parse row data
        foreach ( $table['rows'] as &$row ) {
            $row['data'] = json_decode( $row['row_data'], true );
        }

        include ATABLES_PLUGIN_DIR . 'templates/admin/edit.php';
    }

    /**
     * Save table via AJAX
     */
    public static function save_table() {
        check_ajax_referer( 'atables_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Permission denied' ) );
        }

        $table_id = isset( $_POST['table_id'] ) ? intval( $_POST['table_id'] ) : 0;
        $title = isset( $_POST['title'] ) ? sanitize_text_field( $_POST['title'] ) : '';
        $description = isset( $_POST['description'] ) ? sanitize_textarea_field( $_POST['description'] ) : '';
        $columns = isset( $_POST['columns'] ) ? $_POST['columns'] : array();
        $rows = isset( $_POST['rows'] ) ? $_POST['rows'] : array();

        if ( empty( $title ) ) {
            wp_send_json_error( array( 'message' => 'Table title is required' ) );
        }

        global $wpdb;

        // Create or update table
        if ( $table_id ) {
            $wpdb->update(
                $wpdb->prefix . 'atables_tables',
                array( 'title' => $title, 'description' => $description ),
                array( 'id' => $table_id ),
                array( '%s', '%s' ),
                array( '%d' )
            );
        } else {
            $wpdb->insert(
                $wpdb->prefix . 'atables_tables',
                array( 'title' => $title, 'description' => $description ),
                array( '%s', '%s' )
            );
            $table_id = $wpdb->insert_id;
        }

        // Delete existing columns and rows
        $wpdb->delete( $wpdb->prefix . 'atables_columns', array( 'table_id' => $table_id ), array( '%d' ) );
        $wpdb->delete( $wpdb->prefix . 'atables_rows', array( 'table_id' => $table_id ), array( '%d' ) );

        // Save columns
        foreach ( $columns as $index => $column_name ) {
            $wpdb->insert(
                $wpdb->prefix . 'atables_columns',
                array(
                    'table_id' => $table_id,
                    'column_name' => sanitize_text_field( $column_name ),
                    'column_order' => $index,
                ),
                array( '%d', '%s', '%d' )
            );
        }

        // Save rows
        foreach ( $rows as $index => $row_data ) {
            $wpdb->insert(
                $wpdb->prefix . 'atables_rows',
                array(
                    'table_id' => $table_id,
                    'row_data' => wp_json_encode( $row_data ),
                    'row_order' => $index,
                ),
                array( '%d', '%s', '%d' )
            );
        }

        wp_send_json_success( array(
            'message' => 'Table saved successfully',
            'table_id' => $table_id,
        ) );
    }
}

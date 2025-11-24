<?php
/**
 * Table List Admin Page
 *
 * @package ATables\Admin
 * @since 3.0.0
 */

namespace ATables\Admin;

/**
 * TableList Class
 */
class TableList {

    /**
     * Render the list page
     */
    public static function render() {
        global $wpdb;

        $tables = $wpdb->get_results(
            "SELECT t.*,
                    (SELECT COUNT(*) FROM {$wpdb->prefix}atables_rows WHERE table_id = t.id) as row_count,
                    (SELECT COUNT(*) FROM {$wpdb->prefix}atables_columns WHERE table_id = t.id) as column_count
             FROM {$wpdb->prefix}atables_tables t
             ORDER BY t.created_at DESC",
            ARRAY_A
        );

        include ATABLES_PLUGIN_DIR . 'templates/admin/list.php';
    }

    /**
     * Delete table via AJAX
     */
    public static function delete_table() {
        check_ajax_referer( 'atables_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Permission denied' ) );
        }

        $table_id = isset( $_POST['table_id'] ) ? intval( $_POST['table_id'] ) : 0;

        if ( ! $table_id ) {
            wp_send_json_error( array( 'message' => 'Invalid table ID' ) );
        }

        global $wpdb;

        // Delete table and related data
        $wpdb->delete( $wpdb->prefix . 'atables_rows', array( 'table_id' => $table_id ), array( '%d' ) );
        $wpdb->delete( $wpdb->prefix . 'atables_columns', array( 'table_id' => $table_id ), array( '%d' ) );
        $wpdb->delete( $wpdb->prefix . 'atables_tables', array( 'id' => $table_id ), array( '%d' ) );

        wp_send_json_success( array( 'message' => 'Table deleted successfully' ) );
    }
}

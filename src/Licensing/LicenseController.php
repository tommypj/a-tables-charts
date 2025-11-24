<?php
/**
 * License Controller
 *
 * @package ATables\Licensing
 * @since 2.0.0
 */

namespace ATables\Licensing;

/**
 * LicenseController Class
 *
 * Handles AJAX requests for license operations.
 */
class LicenseController {

    /**
     * Activate license via AJAX
     */
    public static function activate_license() {
        check_ajax_referer( 'atables_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array(
                'message' => __( 'Permission denied.', 'a-tables-charts' ),
            ) );
        }

        $license_key = isset( $_POST['license_key'] ) ? sanitize_text_field( $_POST['license_key'] ) : '';
        $purchase_code = isset( $_POST['purchase_code'] ) ? sanitize_text_field( $_POST['purchase_code'] ) : '';

        if ( empty( $license_key ) ) {
            wp_send_json_error( array(
                'message' => __( 'License key is required.', 'a-tables-charts' ),
            ) );
        }

        // Activate license
        $result = LicenseManager::activate_license( $license_key, $purchase_code );

        if ( $result ) {
            // Clear license cache
            delete_transient( 'atables_license_status' );

            wp_send_json_success( array(
                'message' => __( 'License activated successfully! PRO features are now available.', 'a-tables-charts' ),
            ) );
        } else {
            wp_send_json_error( array(
                'message' => __( 'License activation failed. Please check your license key and try again.', 'a-tables-charts' ),
            ) );
        }
    }

    /**
     * Deactivate license via AJAX
     */
    public static function deactivate_license() {
        check_ajax_referer( 'atables_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array(
                'message' => __( 'Permission denied.', 'a-tables-charts' ),
            ) );
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'atables_licenses';

        // Deactivate license
        $result = $wpdb->update(
            $table_name,
            array(
                'status' => 'inactive',
                'updated_at' => current_time( 'mysql' ),
            ),
            array( 'id' => 1 ),
            array( '%s', '%s' ),
            array( '%d' )
        );

        if ( false !== $result ) {
            // Clear license cache
            delete_transient( 'atables_license_status' );

            wp_send_json_success( array(
                'message' => __( 'License deactivated successfully.', 'a-tables-charts' ),
            ) );
        } else {
            wp_send_json_error( array(
                'message' => __( 'Failed to deactivate license.', 'a-tables-charts' ),
            ) );
        }
    }

    /**
     * Refresh license status via AJAX
     */
    public static function refresh_license() {
        check_ajax_referer( 'atables_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array(
                'message' => __( 'Permission denied.', 'a-tables-charts' ),
            ) );
        }

        // Clear license cache to force re-check
        delete_transient( 'atables_license_status' );

        // Get fresh license status
        $is_active = LicenseManager::is_pro_active();

        if ( $is_active ) {
            wp_send_json_success( array(
                'message' => __( 'License status refreshed. Your license is active.', 'a-tables-charts' ),
            ) );
        } else {
            wp_send_json_error( array(
                'message' => __( 'License is not active. Please activate or renew your license.', 'a-tables-charts' ),
            ) );
        }
    }
}

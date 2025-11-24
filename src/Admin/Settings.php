<?php
/**
 * Settings Page
 *
 * @package ATables\Admin
 * @since 3.0.0
 */

namespace ATables\Admin;

use ATables\Features\FeatureManager;

/**
 * Settings Class
 */
class Settings {

    /**
     * Render settings page
     */
    public static function render() {
        $features = FeatureManager::get_all();
        include ATABLES_PLUGIN_DIR . 'templates/admin/settings.php';
    }

    /**
     * Toggle feature via AJAX
     */
    public static function toggle_feature() {
        check_ajax_referer( 'atables_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Permission denied' ) );
        }

        $feature_key = isset( $_POST['feature_key'] ) ? sanitize_text_field( $_POST['feature_key'] ) : '';
        $enabled = isset( $_POST['enabled'] ) ? (bool) $_POST['enabled'] : false;

        if ( empty( $feature_key ) ) {
            wp_send_json_error( array( 'message' => 'Invalid feature key' ) );
        }

        if ( $enabled ) {
            FeatureManager::enable( $feature_key );
        } else {
            FeatureManager::disable( $feature_key );
        }

        wp_send_json_success( array( 'message' => 'Feature updated successfully' ) );
    }
}

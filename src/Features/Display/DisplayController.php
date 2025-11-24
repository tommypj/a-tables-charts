<?php
/**
 * Display Settings Controller
 *
 * @package ATables\Features\Display
 * @since 2.0.0
 */

namespace ATables\Features\Display;

/**
 * DisplayController Class
 *
 * Handles AJAX requests for display settings.
 */
class DisplayController {

    /**
     * Save display settings
     */
    public static function save_display_settings() {
        // Verify nonce
        check_ajax_referer( 'atables_nonce', 'nonce' );

        // Check permissions
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array(
                'message' => __( 'You do not have permission to perform this action.', 'a-tables-charts' ),
            ) );
        }

        // Get data
        $table_id = isset( $_POST['table_id'] ) ? intval( $_POST['table_id'] ) : 0;
        $settings = isset( $_POST['settings'] ) ? $_POST['settings'] : array();

        if ( ! $table_id ) {
            wp_send_json_error( array(
                'message' => __( 'Invalid table ID.', 'a-tables-charts' ),
            ) );
        }

        // Sanitize settings
        $sanitized_settings = array(
            'enable_search' => isset( $settings['enable_search'] ) ? intval( $settings['enable_search'] ) : 0,
            'enable_sorting' => isset( $settings['enable_sorting'] ) ? intval( $settings['enable_sorting'] ) : 0,
            'enable_pagination' => isset( $settings['enable_pagination'] ) ? intval( $settings['enable_pagination'] ) : 0,
            'rows_per_page' => isset( $settings['rows_per_page'] ) ? intval( $settings['rows_per_page'] ) : 10,
            'theme' => isset( $settings['theme'] ) ? sanitize_text_field( $settings['theme'] ) : 'default',
            'responsive_mode' => isset( $settings['responsive_mode'] ) ? sanitize_text_field( $settings['responsive_mode'] ) : 'scroll',
            'custom_css' => isset( $settings['custom_css'] ) ? sanitize_textarea_field( $settings['custom_css'] ) : '',
        );

        // Save to database
        global $wpdb;
        $table_name = $wpdb->prefix . 'atables_display_settings';

        // Check if settings exist
        $exists = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$table_name} WHERE table_id = %d",
                $table_id
            )
        );

        if ( $exists ) {
            // Update existing settings
            $result = $wpdb->update(
                $table_name,
                $sanitized_settings,
                array( 'table_id' => $table_id ),
                array( '%d', '%d', '%d', '%d', '%s', '%s', '%s' ),
                array( '%d' )
            );
        } else {
            // Insert new settings
            $sanitized_settings['table_id'] = $table_id;
            $result = $wpdb->insert(
                $table_name,
                $sanitized_settings,
                array( '%d', '%d', '%d', '%d', '%d', '%s', '%s', '%s' )
            );
        }

        if ( false === $result ) {
            wp_send_json_error( array(
                'message' => __( 'Failed to save display settings.', 'a-tables-charts' ),
            ) );
        }

        wp_send_json_success( array(
            'message' => __( 'Display settings saved successfully!', 'a-tables-charts' ),
        ) );
    }
}

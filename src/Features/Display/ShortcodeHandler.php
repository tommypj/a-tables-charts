<?php
/**
 * Shortcode Handler
 *
 * @package ATables\Features\Display
 * @since 2.0.0
 */

namespace ATables\Features\Display;

use ATables\Features\Tables\TableService;

/**
 * ShortcodeHandler Class
 *
 * Handles [atables] shortcode rendering.
 */
class ShortcodeHandler {

    /**
     * Render shortcode
     *
     * @param array $atts Shortcode attributes.
     * @return string
     */
    public static function render( $atts ) {
        $atts = shortcode_atts( array(
            'id'       => 0,
            'search'   => 'true',
            'sort'     => 'true',
            'paginate' => 'true',
            'per_page' => 10,
        ), $atts );

        $table_id = intval( $atts['id'] );

        if ( ! $table_id ) {
            return '<p>' . __( 'Please provide a table ID.', 'a-tables-charts' ) . '</p>';
        }

        $service = new TableService();
        $table = $service->get_table( $table_id, array(
            'per_page' => 0, // Get all rows for frontend
        ) );

        if ( ! $table ) {
            return '<p>' . __( 'Table not found.', 'a-tables-charts' ) . '</p>';
        }

        // Get display settings
        global $wpdb;
        $settings_table = $wpdb->prefix . 'atables_display_settings';
        $settings = $wpdb->get_row(
            $wpdb->prepare( "SELECT * FROM {$settings_table} WHERE table_id = %d", $table_id ),
            ARRAY_A
        );

        // Use shortcode attributes or default settings
        $enable_search = filter_var( $atts['search'], FILTER_VALIDATE_BOOLEAN );
        $enable_sorting = filter_var( $atts['sort'], FILTER_VALIDATE_BOOLEAN );
        $enable_pagination = filter_var( $atts['paginate'], FILTER_VALIDATE_BOOLEAN );
        $rows_per_page = intval( $atts['per_page'] );

        // Override with database settings if available
        if ( $settings ) {
            $enable_search = $settings['enable_search'];
            $enable_sorting = $settings['enable_sorting'];
            $enable_pagination = $settings['enable_pagination'];
            $rows_per_page = $settings['rows_per_page'];
        }

        ob_start();
        include ATABLES_PLUGIN_DIR . 'templates/frontend/table-display.php';
        return ob_get_clean();
    }
}

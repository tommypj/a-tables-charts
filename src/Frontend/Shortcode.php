<?php
/**
 * Shortcode Handler
 *
 * @package ATables\Frontend
 * @since 3.0.0
 */

namespace ATables\Frontend;

/**
 * Shortcode Class
 */
class Shortcode {

    /**
     * Render shortcode
     *
     * @param array $atts Shortcode attributes
     * @return string
     */
    public static function render( $atts ) {
        $atts = shortcode_atts( array(
            'id' => 0,
        ), $atts );

        $table_id = intval( $atts['id'] );

        if ( ! $table_id ) {
            return '';
        }

        global $wpdb;

        // Get table
        $table = $wpdb->get_row(
            $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}atables_tables WHERE id = %d", $table_id ),
            ARRAY_A
        );

        if ( ! $table ) {
            return '';
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

        ob_start();
        include ATABLES_PLUGIN_DIR . 'templates/frontend/table.php';
        return ob_get_clean();
    }
}

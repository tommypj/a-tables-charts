<?php
/**
 * Theme Feature Module
 *
 * @package ATables\Features\Themes
 * @since 3.0.0
 */

namespace ATables\Features\Themes;

use ATables\Features\FeatureManager;

/**
 * ThemeModule Class
 *
 * Independent theme feature - only loads if enabled
 */
class ThemeModule {

    /**
     * Initialize the module
     */
    public static function init() {
        // Only load if feature is enabled
        if ( ! FeatureManager::is_enabled( 'themes' ) ) {
            return;
        }

        // Add hooks
        add_filter( 'atables_table_classes', array( __CLASS__, 'add_theme_class' ), 10, 2 );
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_theme_styles' ) );
        add_action( 'atables_after_table_meta', array( __CLASS__, 'render_theme_selector' ), 10, 1 );
        add_action( 'wp_ajax_atables_save_theme', array( __CLASS__, 'save_theme' ) );
    }

    /**
     * Add theme class to table wrapper
     *
     * @param array $classes Existing classes
     * @param int   $table_id Table ID
     * @return array Modified classes
     */
    public static function add_theme_class( $classes, $table_id ) {
        $theme = self::get_table_theme( $table_id );
        $classes[] = 'atables-theme-' . $theme;
        return $classes;
    }

    /**
     * Enqueue theme styles
     */
    public static function enqueue_theme_styles() {
        wp_enqueue_style(
            'atables-themes',
            ATABLES_PLUGIN_URL . 'assets/css/themes.css',
            array( 'atables-frontend' ),
            ATABLES_VERSION
        );
    }

    /**
     * Render theme selector in admin
     *
     * @param array $table Table data
     */
    public static function render_theme_selector( $table ) {
        if ( ! $table ) {
            return;
        }

        $current_theme = self::get_table_theme( $table['id'] );
        ?>
        <tr>
            <th><label for="table-theme"><?php esc_html_e( 'Theme', 'a-tables-charts' ); ?></label></th>
            <td>
                <select id="table-theme" name="theme" class="regular-text">
                    <option value="default" <?php selected( $current_theme, 'default' ); ?>>
                        <?php esc_html_e( 'Default', 'a-tables-charts' ); ?>
                    </option>
                    <option value="minimal" <?php selected( $current_theme, 'minimal' ); ?>>
                        <?php esc_html_e( 'Minimal', 'a-tables-charts' ); ?>
                    </option>
                    <option value="dark" <?php selected( $current_theme, 'dark' ); ?>>
                        <?php esc_html_e( 'Dark', 'a-tables-charts' ); ?>
                    </option>
                    <option value="striped" <?php selected( $current_theme, 'striped' ); ?>>
                        <?php esc_html_e( 'Striped', 'a-tables-charts' ); ?>
                    </option>
                </select>
                <p class="description"><?php esc_html_e( 'Choose a visual theme for your table.', 'a-tables-charts' ); ?></p>
                <button type="button" class="button" id="save-theme-btn">
                    <?php esc_html_e( 'Save Theme', 'a-tables-charts' ); ?>
                </button>
                <span class="theme-save-result"></span>
            </td>
        </tr>

        <script>
        jQuery(document).ready(function($) {
            $('#save-theme-btn').on('click', function() {
                const button = $(this);
                const result = $('.theme-save-result');

                button.prop('disabled', true).text('<?php esc_html_e( 'Saving...', 'a-tables-charts' ); ?>');

                $.ajax({
                    url: atablesAdmin.ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'atables_save_theme',
                        nonce: atablesAdmin.nonce,
                        table_id: <?php echo intval( $table['id'] ); ?>,
                        theme: $('#table-theme').val()
                    },
                    success: function(response) {
                        if (response.success) {
                            result.html('<span style="color: #46b450;">✓ ' + response.data.message + '</span>');
                        } else {
                            result.html('<span style="color: #dc3232;">✗ ' + response.data.message + '</span>');
                        }
                        button.prop('disabled', false).text('<?php esc_html_e( 'Save Theme', 'a-tables-charts' ); ?>');

                        setTimeout(function() {
                            result.fadeOut(function() {
                                $(this).html('').show();
                            });
                        }, 3000);
                    }
                });
            });
        });
        </script>
        <?php
    }

    /**
     * Save theme via AJAX
     */
    public static function save_theme() {
        check_ajax_referer( 'atables_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Permission denied' ) );
        }

        $table_id = isset( $_POST['table_id'] ) ? intval( $_POST['table_id'] ) : 0;
        $theme = isset( $_POST['theme'] ) ? sanitize_text_field( $_POST['theme'] ) : 'default';

        if ( ! $table_id ) {
            wp_send_json_error( array( 'message' => 'Invalid table ID' ) );
        }

        // Validate theme
        $allowed_themes = array( 'default', 'minimal', 'dark', 'striped' );
        if ( ! in_array( $theme, $allowed_themes ) ) {
            $theme = 'default';
        }

        // Save to options (using table-specific option)
        update_option( 'atables_theme_' . $table_id, $theme );

        wp_send_json_success( array( 'message' => 'Theme saved successfully!' ) );
    }

    /**
     * Get table theme
     *
     * @param int $table_id Table ID
     * @return string Theme name
     */
    public static function get_table_theme( $table_id ) {
        $theme = get_option( 'atables_theme_' . $table_id, 'default' );

        // Validate
        $allowed_themes = array( 'default', 'minimal', 'dark', 'striped' );
        if ( ! in_array( $theme, $allowed_themes ) ) {
            $theme = 'default';
        }

        return $theme;
    }
}

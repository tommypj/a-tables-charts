<?php
/**
 * Feature Manager
 *
 * @package ATables\Features
 * @since 3.0.0
 */

namespace ATables\Features;

/**
 * FeatureManager Class
 *
 * Manages feature toggles and checks
 */
class FeatureManager {

    /**
     * Check if a feature is enabled
     *
     * @param string $feature_key Feature key to check
     * @return bool
     */
    public static function is_enabled( $feature_key ) {
        global $wpdb;

        $enabled = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT enabled FROM {$wpdb->prefix}atables_features WHERE feature_key = %s",
                $feature_key
            )
        );

        return (bool) $enabled;
    }

    /**
     * Enable a feature
     *
     * @param string $feature_key Feature key
     * @return bool
     */
    public static function enable( $feature_key ) {
        global $wpdb;

        return (bool) $wpdb->update(
            $wpdb->prefix . 'atables_features',
            array( 'enabled' => 1 ),
            array( 'feature_key' => $feature_key ),
            array( '%d' ),
            array( '%s' )
        );
    }

    /**
     * Disable a feature
     *
     * @param string $feature_key Feature key
     * @return bool
     */
    public static function disable( $feature_key ) {
        global $wpdb;

        return (bool) $wpdb->update(
            $wpdb->prefix . 'atables_features',
            array( 'enabled' => 0 ),
            array( 'feature_key' => $feature_key ),
            array( '%d' ),
            array( '%s' )
        );
    }

    /**
     * Get all features with their status
     *
     * @return array
     */
    public static function get_all() {
        global $wpdb;

        $features = $wpdb->get_results(
            "SELECT * FROM {$wpdb->prefix}atables_features ORDER BY feature_key ASC",
            ARRAY_A
        );

        $formatted = array();
        foreach ( $features as $feature ) {
            $settings = json_decode( $feature['settings'], true );
            $formatted[ $feature['feature_key'] ] = array(
                'enabled' => (bool) $feature['enabled'],
                'title' => $settings['title'] ?? $feature['feature_key'],
                'tier' => $settings['tier'] ?? 'free',
            );
        }

        return $formatted;
    }
}

<?php
/**
 * License Manager
 *
 * @package ATables\Licensing
 * @since 2.0.0
 */

namespace ATables\Licensing;

/**
 * LicenseManager Class
 *
 * Manages license validation and pro feature access.
 */
class LicenseManager {

    /**
     * License API URL (change to your actual API)
     */
    const API_URL = 'https://yoursite.com/wp-json/atables-license/v1';

    /**
     * Cache duration (24 hours)
     */
    const CACHE_DURATION = DAY_IN_SECONDS;

    /**
     * Grace period (7 days)
     */
    const GRACE_PERIOD = 7 * DAY_IN_SECONDS;

    /**
     * Check if this is the pro version
     *
     * @return bool
     */
    public static function is_pro_version() {
        // Check if pro constant is defined
        return defined( 'ATABLES_PRO' ) && ATABLES_PRO === true;
    }

    /**
     * Check if pro features are active
     *
     * @return bool
     */
    public static function is_pro_active() {
        // If not pro version, return false
        if ( ! self::is_pro_version() ) {
            return false;
        }

        // Get license status from cache
        $license_status = get_transient( 'atables_license_status' );

        // If cache exists and is valid
        if ( false !== $license_status ) {
            return 'active' === $license_status;
        }

        // Validate license
        return self::validate_license();
    }

    /**
     * Activate license
     *
     * @param string $license_key License key.
     * @param string $purchase_code Envato purchase code (optional).
     * @return array Result with success status and message.
     */
    public static function activate_license( $license_key, $purchase_code = '' ) {
        // Validate format
        if ( empty( $license_key ) ) {
            return array(
                'success' => false,
                'message' => __( 'Please enter a license key.', 'a-tables-charts' ),
            );
        }

        // Call API to validate
        $response = wp_remote_post(
            self::API_URL . '/activate',
            array(
                'body' => array(
                    'license_key'    => $license_key,
                    'purchase_code'  => $purchase_code,
                    'site_url'       => home_url(),
                    'plugin_version' => ATABLES_VERSION,
                ),
                'timeout' => 15,
            )
        );

        // Check for errors
        if ( is_wp_error( $response ) ) {
            return array(
                'success' => false,
                'message' => __( 'Could not connect to license server. Please try again later.', 'a-tables-charts' ),
            );
        }

        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );

        if ( ! isset( $data['success'] ) ) {
            return array(
                'success' => false,
                'message' => __( 'Invalid response from license server.', 'a-tables-charts' ),
            );
        }

        // If activation successful
        if ( $data['success'] ) {
            // Store license data
            self::store_license_data( $license_key, $data );

            // Set cache
            set_transient( 'atables_license_status', 'active', self::CACHE_DURATION );

            return array(
                'success' => true,
                'message' => __( 'License activated successfully!', 'a-tables-charts' ),
            );
        }

        return array(
            'success' => false,
            'message' => $data['message'] ?? __( 'License activation failed.', 'a-tables-charts' ),
        );
    }

    /**
     * Deactivate license
     *
     * @return array Result with success status and message.
     */
    public static function deactivate_license() {
        $license_key = get_option( 'atables_license_key' );

        if ( empty( $license_key ) ) {
            return array(
                'success' => false,
                'message' => __( 'No license found.', 'a-tables-charts' ),
            );
        }

        // Call API to deactivate
        $response = wp_remote_post(
            self::API_URL . '/deactivate',
            array(
                'body' => array(
                    'license_key' => $license_key,
                    'site_url'    => home_url(),
                ),
                'timeout' => 15,
            )
        );

        // Clear local data
        delete_option( 'atables_license_key' );
        delete_option( 'atables_license_data' );
        delete_transient( 'atables_license_status' );

        return array(
            'success' => true,
            'message' => __( 'License deactivated successfully.', 'a-tables-charts' ),
        );
    }

    /**
     * Validate license
     *
     * @return bool
     */
    private static function validate_license() {
        $license_key = get_option( 'atables_license_key' );

        if ( empty( $license_key ) ) {
            return false;
        }

        // Call API to validate
        $response = wp_remote_post(
            self::API_URL . '/validate',
            array(
                'body' => array(
                    'license_key' => $license_key,
                    'site_url'    => home_url(),
                ),
                'timeout' => 10,
            )
        );

        // If API is down, check grace period
        if ( is_wp_error( $response ) ) {
            return self::check_grace_period();
        }

        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );

        $is_valid = isset( $data['valid'] ) && $data['valid'] === true;

        // Update cache
        $status = $is_valid ? 'active' : 'invalid';
        set_transient( 'atables_license_status', $status, self::CACHE_DURATION );

        // Update last checked
        update_option( 'atables_license_last_checked', time() );

        return $is_valid;
    }

    /**
     * Check grace period
     *
     * If API is down, allow grace period before disabling features.
     *
     * @return bool
     */
    private static function check_grace_period() {
        $last_checked = get_option( 'atables_license_last_checked', 0 );
        $time_since = time() - $last_checked;

        // If within grace period, consider valid
        if ( $time_since < self::GRACE_PERIOD ) {
            return true;
        }

        return false;
    }

    /**
     * Store license data
     *
     * @param string $license_key License key.
     * @param array  $data        License data from API.
     */
    private static function store_license_data( $license_key, $data ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'atables_licenses';

        // Check if license exists
        $exists = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT id FROM {$table_name} WHERE license_key = %s",
                $license_key
            )
        );

        $license_data = array(
            'license_key'   => $license_key,
            'purchase_code' => $data['purchase_code'] ?? '',
            'license_type'  => $data['license_type'] ?? 'regular',
            'status'        => 'active',
            'activated_at'  => current_time( 'mysql' ),
            'expires_at'    => isset( $data['expires_at'] ) ? $data['expires_at'] : null,
            'last_checked'  => current_time( 'mysql' ),
            'site_url'      => home_url(),
        );

        if ( $exists ) {
            // Update existing
            $wpdb->update(
                $table_name,
                $license_data,
                array( 'license_key' => $license_key ),
                array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' ),
                array( '%s' )
            );
        } else {
            // Insert new
            $wpdb->insert(
                $table_name,
                $license_data,
                array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
            );
        }

        // Store in options for quick access
        update_option( 'atables_license_key', $license_key );
        update_option( 'atables_license_data', $data );
    }

    /**
     * Get license data
     *
     * @return array|null
     */
    public static function get_license_data() {
        return get_option( 'atables_license_data', null );
    }

    /**
     * Check if feature is available
     *
     * @param string $feature Feature name.
     * @return bool
     */
    public static function can_use_feature( $feature ) {
        // List of pro features
        $pro_features = array(
            'validation',
            'conditional_formatting',
            'charts',
            'formulas',
            'advanced_filtering',
            'export',
            'api_access',
        );

        // If feature is not pro, allow
        if ( ! in_array( $feature, $pro_features, true ) ) {
            return true;
        }

        // Check if pro is active
        return self::is_pro_active();
    }
}

<?php
/**
 * Features Helper Class
 * 
 * Manages feature availability and upgrade detection for FREE vs PRO versions.
 * This helper will be used to determine which features are available and provide
 * upgrade URLs when needed.
 *
 * @package ATablesCharts
 * @subpackage Shared\Utils
 * @since 1.0.0
 */

namespace ATablesCharts\Shared\Utils;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Features Class
 * 
 * Provides methods to check feature availability and manage upgrade prompts.
 */
class Features {
    
    /**
     * Free version features list
     * 
     * @var array
     */
    private static $free_features = array(
        'csv_import',
        'basic_export',
        'table_editing',
        'frontend_display',
        'basic_filters',
        'search',
        'sorting',
        'pagination',
        'copy_clipboard',
        'print_table',
    );
    
    /**
     * PRO features with metadata
     * 
     * @var array
     */
    private static $pro_features = array(
        'excel_import' => array(
            'title' => 'Excel Import',
            'description' => 'Import XLS and XLSX files',
            'icon' => '📊',
            'category' => 'import',
        ),
        'json_import' => array(
            'title' => 'JSON Import',
            'description' => 'Import JSON data files',
            'icon' => '{ }',
            'category' => 'import',
        ),
        'xml_import' => array(
            'title' => 'XML Import',
            'description' => 'Import XML data files',
            'icon' => '</>',
            'category' => 'import',
        ),
        'charts' => array(
            'title' => 'Interactive Charts',
            'description' => 'Create beautiful charts from your data',
            'icon' => '📈',
            'category' => 'visualization',
        ),
        'excel_export' => array(
            'title' => 'Excel Export',
            'description' => 'Export tables to Excel format',
            'icon' => '💾',
            'category' => 'export',
        ),
        'pdf_export' => array(
            'title' => 'PDF Export',
            'description' => 'Export tables to PDF documents',
            'icon' => '📄',
            'category' => 'export',
        ),
        'google_sheets' => array(
            'title' => 'Google Sheets Integration',
            'description' => 'Connect to Google Sheets',
            'icon' => '📋',
            'category' => 'integration',
        ),
        'database_connect' => array(
            'title' => 'Database Connections',
            'description' => 'Connect to MySQL databases',
            'icon' => '🔗',
            'category' => 'integration',
        ),
        'user_roles' => array(
            'title' => 'User Role Management',
            'description' => 'Control access by user roles',
            'icon' => '👥',
            'category' => 'security',
        ),
        'white_label' => array(
            'title' => 'White Label Options',
            'description' => 'Customize branding',
            'icon' => '🎨',
            'category' => 'customization',
        ),
        'priority_support' => array(
            'title' => 'Priority Support',
            'description' => 'Get help when you need it',
            'icon' => '💬',
            'category' => 'support',
        ),
    );
    
    /**
     * Check if feature is available in current version
     *
     * @param string $feature Feature identifier
     * @return bool True if available, false otherwise
     */
    public static function is_available( $feature ) {
        // If PRO version, all features available
        if ( self::is_pro() ) {
            return true;
        }
        
        // Check if feature is in free list
        return in_array( $feature, self::$free_features, true );
    }
    
    /**
     * Check if this is the PRO version
     *
     * @return bool True if PRO version
     */
    public static function is_pro() {
        // This will be different in lite version
        // PRO version: return true
        // LITE version: return false
        return true; // PRO version
    }
    
    /**
     * Get upgrade URL
     *
     * @param string $source Optional source identifier for tracking
     * @return string Upgrade URL
     */
    public static function get_upgrade_url( $source = '' ) {
        $url = 'https://a-tables-charts.com/pricing/';
        
        if ( ! empty( $source ) ) {
            $url = add_query_arg( 'ref', $source, $url );
        }
        
        return $url;
    }
    
    /**
     * Get all PRO features
     *
     * @return array PRO features with metadata
     */
    public static function get_pro_features() {
        return self::$pro_features;
    }
    
    /**
     * Get PRO features by category
     *
     * @param string $category Category identifier
     * @return array PRO features in category
     */
    public static function get_pro_features_by_category( $category ) {
        return array_filter(
            self::$pro_features,
            function( $feature ) use ( $category ) {
                return isset( $feature['category'] ) && $feature['category'] === $category;
            }
        );
    }
    
    /**
     * Get feature information
     *
     * @param string $feature_key Feature identifier
     * @return array|null Feature data or null if not found
     */
    public static function get_feature_info( $feature_key ) {
        return isset( self::$pro_features[ $feature_key ] ) 
            ? self::$pro_features[ $feature_key ] 
            : null;
    }
    
    /**
     * Check if feature requires PRO
     *
     * @param string $feature Feature identifier
     * @return bool True if requires PRO
     */
    public static function requires_pro( $feature ) {
        return ! self::is_available( $feature );
    }
    
    /**
     * Get upgrade call-to-action text
     *
     * @param string $context Context where CTA is displayed
     * @return string CTA text
     */
    public static function get_upgrade_cta( $context = 'general' ) {
        $ctas = array(
            'general' => __( 'Upgrade to PRO', 'a-tables-charts' ),
            'feature' => __( 'Unlock This Feature', 'a-tables-charts' ),
            'import' => __( 'Upgrade to Import', 'a-tables-charts' ),
            'export' => __( 'Upgrade to Export', 'a-tables-charts' ),
            'chart' => __( 'Create Charts with PRO', 'a-tables-charts' ),
        );
        
        return isset( $ctas[ $context ] ) ? $ctas[ $context ] : $ctas['general'];
    }
    
    /**
     * Get pricing plans
     *
     * @return array Pricing plans information
     */
    public static function get_pricing_plans() {
        return array(
            'personal' => array(
                'name' => __( 'Personal', 'a-tables-charts' ),
                'price' => '$79',
                'period' => __( '/year', 'a-tables-charts' ),
                'description' => __( 'Perfect for personal projects', 'a-tables-charts' ),
                'features' => array(
                    __( '1 Site License', 'a-tables-charts' ),
                    __( 'All PRO Features', 'a-tables-charts' ),
                    __( 'Priority Support', 'a-tables-charts' ),
                    __( '1 Year Updates', 'a-tables-charts' ),
                ),
                'url' => self::get_upgrade_url( 'personal-plan' ),
                'recommended' => false,
            ),
            'business' => array(
                'name' => __( 'Business', 'a-tables-charts' ),
                'price' => '$149',
                'period' => __( '/year', 'a-tables-charts' ),
                'description' => __( 'Best for agencies and businesses', 'a-tables-charts' ),
                'features' => array(
                    __( '5 Site Licenses', 'a-tables-charts' ),
                    __( 'All PRO Features', 'a-tables-charts' ),
                    __( 'Priority Support', 'a-tables-charts' ),
                    __( '1 Year Updates', 'a-tables-charts' ),
                ),
                'url' => self::get_upgrade_url( 'business-plan' ),
                'recommended' => true,
            ),
            'agency' => array(
                'name' => __( 'Agency', 'a-tables-charts' ),
                'price' => '$299',
                'period' => __( '/year', 'a-tables-charts' ),
                'description' => __( 'Unlimited sites for agencies', 'a-tables-charts' ),
                'features' => array(
                    __( 'Unlimited Sites', 'a-tables-charts' ),
                    __( 'All PRO Features', 'a-tables-charts' ),
                    __( 'Priority Support', 'a-tables-charts' ),
                    __( '1 Year Updates', 'a-tables-charts' ),
                ),
                'url' => self::get_upgrade_url( 'agency-plan' ),
                'recommended' => false,
            ),
        );
    }
}

<?php
/**
 * Core Plugin Class
 *
 * @package ATables\Core
 * @since 2.0.0
 */

namespace ATables\Core;

/**
 * Main Plugin Class
 */
class Plugin {

    /**
     * Plugin instance
     *
     * @var Plugin
     */
    private static $instance = null;

    /**
     * Loader instance
     *
     * @var Loader
     */
    private $loader;

    /**
     * Get plugin instance
     *
     * @return Plugin
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        $this->loader = new Loader();
        $this->load_dependencies();
        $this->register_hooks();
    }

    /**
     * Load required dependencies
     */
    private function load_dependencies() {
        // Core classes are autoloaded
    }

    /**
     * Register hooks
     */
    private function register_hooks() {
        // Admin hooks
        if ( is_admin() ) {
            $this->loader->add_action( 'admin_menu', $this, 'register_admin_menu' );
            $this->loader->add_action( 'admin_enqueue_scripts', $this, 'enqueue_admin_assets' );
        }

        // Frontend hooks
        $this->loader->add_action( 'wp_enqueue_scripts', $this, 'enqueue_frontend_assets' );
        $this->loader->add_action( 'init', $this, 'register_shortcodes' );

        // AJAX hooks
        $this->register_ajax_hooks();
    }

    /**
     * Register AJAX hooks
     */
    private function register_ajax_hooks() {
        // Table operations
        $this->loader->add_action( 'wp_ajax_atables_get_tables', 'ATables\Features\Tables\TableController', 'get_tables' );
        $this->loader->add_action( 'wp_ajax_atables_save_table', 'ATables\Features\Tables\TableController', 'save_table' );
        $this->loader->add_action( 'wp_ajax_atables_delete_table', 'ATables\Features\Tables\TableController', 'delete_table' );
        $this->loader->add_action( 'wp_ajax_atables_save_table_data', 'ATables\Features\Tables\TableController', 'save_table_data' );
        $this->loader->add_action( 'wp_ajax_atables_create_manual_table', 'ATables\Features\Tables\TableController', 'create_manual_table' );

        // Upload operations
        $this->loader->add_action( 'wp_ajax_atables_upload_file', 'ATables\Features\Upload\UploadController', 'upload_file' );

        // Display settings
        $this->loader->add_action( 'wp_ajax_atables_save_display_settings', 'ATables\Features\Display\DisplayController', 'save_display_settings' );
    }

    /**
     * Register admin menu
     */
    public function register_admin_menu() {
        add_menu_page(
            __( 'A-Tables & Charts', 'a-tables-charts' ),
            __( 'Tables & Charts', 'a-tables-charts' ),
            'manage_options',
            'a-tables-charts',
            array( $this, 'render_admin_page' ),
            'dashicons-grid-view',
            30
        );

        // Submenu: All Tables
        add_submenu_page(
            'a-tables-charts',
            __( 'All Tables', 'a-tables-charts' ),
            __( 'All Tables', 'a-tables-charts' ),
            'manage_options',
            'a-tables-charts',
            array( $this, 'render_admin_page' )
        );

        // Submenu: Add New
        add_submenu_page(
            'a-tables-charts',
            __( 'Add New Table', 'a-tables-charts' ),
            __( 'Add New', 'a-tables-charts' ),
            'manage_options',
            'a-tables-charts-new',
            array( $this, 'render_new_table_page' )
        );

        // Submenu: Edit (hidden from menu, accessible via URL)
        add_submenu_page(
            null, // No parent = hidden from menu
            __( 'Edit Table', 'a-tables-charts' ),
            __( 'Edit Table', 'a-tables-charts' ),
            'manage_options',
            'a-tables-charts-edit',
            array( $this, 'render_edit_table_page' )
        );

        // Submenu: Create Manual (hidden from menu, accessible via URL)
        add_submenu_page(
            null, // No parent = hidden from menu
            __( 'Create Table Manually', 'a-tables-charts' ),
            __( 'Create Manually', 'a-tables-charts' ),
            'manage_options',
            'a-tables-charts-create-manual',
            array( $this, 'render_create_manual_page' )
        );

        // Submenu: License (Pro version)
        if ( \ATables\Licensing\LicenseManager::is_pro_version() ) {
            add_submenu_page(
                'a-tables-charts',
                __( 'License', 'a-tables-charts' ),
                __( 'License', 'a-tables-charts' ),
                'manage_options',
                'a-tables-charts-license',
                array( $this, 'render_license_page' )
            );
        }
    }

    /**
     * Render admin page
     */
    public function render_admin_page() {
        require_once ATABLES_PLUGIN_DIR . 'templates/admin/tables-list.php';
    }

    /**
     * Render new table page
     */
    public function render_new_table_page() {
        require_once ATABLES_PLUGIN_DIR . 'templates/admin/table-new.php';
    }

    /**
     * Render edit table page
     */
    public function render_edit_table_page() {
        require_once ATABLES_PLUGIN_DIR . 'templates/admin/table-edit-enhanced.php';
    }

    /**
     * Render create manual page
     */
    public function render_create_manual_page() {
        require_once ATABLES_PLUGIN_DIR . 'templates/admin/table-create-manual.php';
    }

    /**
     * Render license page
     */
    public function render_license_page() {
        require_once ATABLES_PLUGIN_DIR . 'templates/admin/license.php';
    }

    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets( $hook ) {
        // Only load on our plugin pages
        if ( strpos( $hook, 'a-tables-charts' ) === false ) {
            return;
        }

        // CSS
        wp_enqueue_style(
            'atables-admin',
            ATABLES_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            ATABLES_VERSION
        );

        // JS
        wp_enqueue_script(
            'atables-admin',
            ATABLES_PLUGIN_URL . 'assets/js/admin.js',
            array( 'jquery' ),
            ATABLES_VERSION,
            true
        );

        // Localize script
        wp_localize_script(
            'atables-admin',
            'atablesAdmin',
            array(
                'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                'nonce'   => wp_create_nonce( 'atables_nonce' ),
                'isPro'   => \ATables\Licensing\LicenseManager::is_pro_active(),
            )
        );
    }

    /**
     * Enqueue frontend assets
     */
    public function enqueue_frontend_assets() {
        // CSS
        wp_enqueue_style(
            'atables-frontend',
            ATABLES_PLUGIN_URL . 'assets/css/frontend.css',
            array(),
            ATABLES_VERSION
        );

        // JS
        wp_enqueue_script(
            'atables-frontend',
            ATABLES_PLUGIN_URL . 'assets/js/frontend.js',
            array( 'jquery' ),
            ATABLES_VERSION,
            true
        );
    }

    /**
     * Register shortcodes
     */
    public function register_shortcodes() {
        add_shortcode( 'atables', array( 'ATables\Features\Display\ShortcodeHandler', 'render' ) );
        add_shortcode( 'atables_chart', array( 'ATables\Features\Charts\ChartShortcode', 'render' ) );
    }

    /**
     * Run the plugin
     */
    public function run() {
        $this->loader->run();
    }
}

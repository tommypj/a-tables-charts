<?php
/**
 * Core Plugin Class
 *
 * @package ATables\Core
 * @since 3.0.0
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
        $this->init_feature_modules();
        $this->register_hooks();
    }

    /**
     * Initialize feature modules
     * Each module checks if it's enabled before loading
     */
    private function init_feature_modules() {
        // Theme Module
        \ATables\Features\Themes\ThemeModule::init();

        // Future modules will be added here:
        // \ATables\Features\Search\SearchModule::init();
        // \ATables\Features\Sorting\SortingModule::init();
        // \ATables\Features\Pagination\PaginationModule::init();
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
        $this->loader->add_action( 'init', $this, 'register_shortcode' );

        // AJAX hooks
        $this->loader->add_action( 'wp_ajax_atables_save_table', 'ATables\Admin\TableEditor', 'save_table' );
        $this->loader->add_action( 'wp_ajax_atables_delete_table', 'ATables\Admin\TableList', 'delete_table' );
        $this->loader->add_action( 'wp_ajax_atables_toggle_feature', 'ATables\Admin\Settings', 'toggle_feature' );
    }

    /**
     * Register admin menu
     */
    public function register_admin_menu() {
        add_menu_page(
            __( 'Tables & Charts', 'a-tables-charts' ),
            __( 'Tables', 'a-tables-charts' ),
            'manage_options',
            'atables',
            array( 'ATables\Admin\TableList', 'render' ),
            'dashicons-grid-view',
            30
        );

        add_submenu_page(
            'atables',
            __( 'All Tables', 'a-tables-charts' ),
            __( 'All Tables', 'a-tables-charts' ),
            'manage_options',
            'atables',
            array( 'ATables\Admin\TableList', 'render' )
        );

        add_submenu_page(
            'atables',
            __( 'Add New', 'a-tables-charts' ),
            __( 'Add New', 'a-tables-charts' ),
            'manage_options',
            'atables-new',
            array( 'ATables\Admin\TableEditor', 'render_new' )
        );

        add_submenu_page(
            null,
            __( 'Edit Table', 'a-tables-charts' ),
            __( 'Edit Table', 'a-tables-charts' ),
            'manage_options',
            'atables-edit',
            array( 'ATables\Admin\TableEditor', 'render_edit' )
        );

        add_submenu_page(
            'atables',
            __( 'Features', 'a-tables-charts' ),
            __( 'Features', 'a-tables-charts' ),
            'manage_options',
            'atables-features',
            array( 'ATables\Admin\Settings', 'render' )
        );
    }

    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets( $hook ) {
        if ( strpos( $hook, 'atables' ) === false ) {
            return;
        }

        wp_enqueue_style(
            'atables-admin',
            ATABLES_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            ATABLES_VERSION
        );

        wp_enqueue_script(
            'atables-admin',
            ATABLES_PLUGIN_URL . 'assets/js/admin.js',
            array( 'jquery' ),
            ATABLES_VERSION,
            true
        );

        wp_localize_script(
            'atables-admin',
            'atablesAdmin',
            array(
                'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                'nonce' => wp_create_nonce( 'atables_nonce' ),
            )
        );
    }

    /**
     * Enqueue frontend assets
     */
    public function enqueue_frontend_assets() {
        wp_enqueue_style(
            'atables-frontend',
            ATABLES_PLUGIN_URL . 'assets/css/frontend.css',
            array(),
            ATABLES_VERSION
        );

        wp_enqueue_script(
            'atables-frontend',
            ATABLES_PLUGIN_URL . 'assets/js/frontend.js',
            array( 'jquery' ),
            ATABLES_VERSION,
            true
        );
    }

    /**
     * Register shortcode
     */
    public function register_shortcode() {
        add_shortcode( 'atables', array( 'ATables\Frontend\Shortcode', 'render' ) );
    }

    /**
     * Run the plugin
     */
    public function run() {
        $this->loader->run();
    }
}

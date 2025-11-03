<?php
/**
 * Core Plugin Class
 *
 * The main plugin orchestrator that coordinates all modules.
 * Implements singleton pattern to ensure only one instance exists.
 *
 * @package ATablesCharts\Core
 * @since 1.0.0
 */

namespace ATablesCharts\Core;

use ATablesCharts\Shared\Utils\Logger;

/**
 * Main Plugin Class
 *
 * Responsibilities:
 * - Initialize all plugin modules
 * - Register hooks and filters
 * - Manage plugin lifecycle
 * - Coordinate dependencies between modules
 */
class Plugin {

	/**
	 * Single instance of the plugin
	 *
	 * @var Plugin|null
	 */
	private static $instance = null;

	/**
	 * Plugin loader instance
	 *
	 * @var Loader
	 */
	private $loader;

	/**
	 * Plugin version
	 *
	 * @var string
	 */
	private $version;

	/**
	 * Plugin slug
	 *
	 * @var string
	 */
	private $plugin_slug;

	/**
	 * Logger instance
	 *
	 * @var Logger
	 */
	private $logger;

	/**
	 * Private constructor to enforce singleton pattern
	 */
	private function __construct() {
		$this->version     = ATABLES_VERSION;
		$this->plugin_slug = ATABLES_SLUG;
		
		$this->load_dependencies();
		$this->set_locale();
		$this->register_hooks();
	}

	/**
	 * Get singleton instance
	 *
	 * @return Plugin Single instance of the plugin
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Prevent cloning of the instance
	 */
	private function __clone() {}

	/**
	 * Prevent unserializing of the instance
	 */
	public function __wakeup() {
		throw new \Exception( 'Cannot unserialize singleton' );
	}

	/**
	 * Load required dependencies
	 *
	 * @since 1.0.0
	 */
	private function load_dependencies() {
		// Load the loader class.
		require_once ATABLES_PLUGIN_DIR . 'src/modules/core/Loader.php';
		$this->loader = new Loader();

		// Load shared utilities FIRST (required by all modules).
		$this->load_shared_utilities();

		// Load logger.
		require_once ATABLES_PLUGIN_DIR . 'src/shared/utils/Logger.php';
		$this->logger = new Logger();

		// Log plugin initialization.
		$this->logger->info( 'A-Tables and Charts plugin initialized', array(
			'version' => $this->version,
		) );

		// Load WP-CLI commands if WP-CLI is available.
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			require_once ATABLES_PLUGIN_DIR . 'src/modules/cli/index.php';
		}
	}

	/**
	 * Load shared utility classes
	 *
	 * @since 1.0.0
	 */
	private function load_shared_utilities() {
		// Load validators and sanitizers first (no dependencies).
		require_once ATABLES_PLUGIN_DIR . 'src/shared/utils/Validator.php';
		require_once ATABLES_PLUGIN_DIR . 'src/shared/utils/Sanitizer.php';
		require_once ATABLES_PLUGIN_DIR . 'src/shared/utils/Helpers.php';
		require_once ATABLES_PLUGIN_DIR . 'src/shared/utils/ContentFormatter.php';
	}

	/**
	 * Define the locale for internationalization
	 *
	 * @since 1.0.0
	 */
	private function set_locale() {
		$this->loader->add_action(
			'plugins_loaded',
			$this,
			'load_plugin_textdomain'
		);
	}

	/**
	 * Load plugin text domain for translations
	 *
	 * @since 1.0.0
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			'a-tables-charts',
			false,
			dirname( ATABLES_PLUGIN_BASENAME ) . '/languages/'
		);
	}

	/**
	 * Register all hooks with WordPress
	 *
	 * @since 1.0.0
	 */
	private function register_hooks() {
		// Register admin hooks.
		$this->register_admin_hooks();

		// Register public hooks.
		$this->register_public_hooks();

		// Register AJAX hooks.
		$this->register_ajax_hooks();

		// Register shortcode hooks.
		$this->register_shortcode_hooks();

		// Register Gutenberg blocks.
		$this->register_gutenberg_hooks();
	}

	/**
	 * Register admin-specific hooks
	 *
	 * @since 1.0.0
	 */
	private function register_admin_hooks() {
		// Load Database Updater.
		require_once ATABLES_PLUGIN_DIR . 'src/modules/core/DatabaseUpdater.php';
		
		// Check for database updates.
		add_action( 'admin_notices', array( 'ATablesCharts\Core\DatabaseUpdater', 'admin_notice' ) );
		add_action( 'admin_init', array( 'ATablesCharts\Core\DatabaseUpdater', 'handle_update_request' ) );
		add_action( 'admin_notices', array( 'ATablesCharts\Core\DatabaseUpdater', 'display_messages' ) );
		
		// Load Migration Runner.
		require_once ATABLES_PLUGIN_DIR . 'src/modules/core/MigrationRunner.php';
		
		// Check for pending migrations.
		add_action( 'admin_notices', array( 'ATablesCharts\Core\MigrationRunner', 'admin_notice' ) );
		add_action( 'admin_init', array( 'ATablesCharts\Core\MigrationRunner', 'handle_migration_request' ) );
		add_action( 'admin_notices', array( 'ATablesCharts\Core\MigrationRunner', 'display_messages' ) );
		
		// Register settings.
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		
		// Enqueue admin styles.
		$this->loader->add_action(
			'admin_enqueue_scripts',
			$this,
			'enqueue_admin_styles'
		);

		// Enqueue admin scripts.
		$this->loader->add_action(
			'admin_enqueue_scripts',
			$this,
			'enqueue_admin_scripts'
		);

		// Register admin menu.
		$this->loader->add_action(
			'admin_menu',
			$this,
			'register_admin_menu'
		);
	}

	/**
	 * Register public-facing hooks
	 *
	 * @since 1.0.0
	 */
	private function register_public_hooks() {
		// Enqueue public styles.
		$this->loader->add_action(
			'wp_enqueue_scripts',
			$this,
			'enqueue_public_styles'
		);

		// Enqueue public scripts.
		$this->loader->add_action(
			'wp_enqueue_scripts',
			$this,
			'enqueue_public_scripts'
		);
	}

	/**
	 * Register AJAX hooks
	 *
	 * @since 1.0.0
	 */
	private function register_ajax_hooks() {
		// Load Data Sources module.
		require_once ATABLES_PLUGIN_DIR . 'src/modules/dataSources/index.php';
		
		// Register Import Controller.
		$import_controller = new \ATablesCharts\DataSources\Controllers\ImportController();
		$import_controller->register_hooks();
		
		// Load Tables module.
		require_once ATABLES_PLUGIN_DIR . 'src/modules/tables/index.php';
		
		// Register Table Controller.
		$table_controller = new \ATablesCharts\Tables\Controllers\TableController();
		$table_controller->register_hooks();
		
		// Register Table View AJAX Controller.
		require_once ATABLES_PLUGIN_DIR . 'src/modules/core/TableViewAjaxController.php';
		$table_view_controller = new \ATablesCharts\Core\TableViewAjaxController();
		$table_view_controller->register_hooks();
		
		// Register Settings Controller.
		require_once ATABLES_PLUGIN_DIR . 'src/modules/core/SettingsController.php';
		$settings_controller = new \ATablesCharts\Core\SettingsController();
		$settings_controller->register_hooks();
		
		// Load Export module.
		require_once ATABLES_PLUGIN_DIR . 'src/modules/export/index.php';
		
		// Register Export Controller.
		$export_controller = new \ATablesCharts\Export\Controllers\ExportController();

		// Register export AJAX actions (for logged-in users only).
		// SECURITY: Unauthenticated exports completely disabled to prevent:
		// - Resource exhaustion (DoS attacks)
		// - Unauthorized data access
		// - Mass data scraping
		// Users must be logged in to export tables.
		add_action( 'wp_ajax_atables_export_table', array( $export_controller, 'export_table' ) );

		// Load Database module.
		require_once ATABLES_PLUGIN_DIR . 'src/modules/database/index.php';
		
		// Register MySQL Query Controller.
		$mysql_controller = new \ATablesCharts\Database\MySQLQueryController();
		
		// Register MySQL query AJAX actions.
		add_action( 'wp_ajax_atables_test_query', array( $mysql_controller, 'test_query' ) );
		add_action( 'wp_ajax_atables_create_table_from_query', array( $mysql_controller, 'create_table_from_query' ) );
		add_action( 'wp_ajax_atables_get_db_tables', array( $mysql_controller, 'get_tables' ) );
		add_action( 'wp_ajax_atables_get_table_columns', array( $mysql_controller, 'get_table_columns' ) );
		add_action( 'wp_ajax_atables_get_sample_queries', array( $mysql_controller, 'get_sample_queries' ) );
		
		// Load Import module.
		require_once ATABLES_PLUGIN_DIR . 'src/modules/import/index.php';
		
		// Register Import Controller (Excel).
		$excel_import_controller = new \ATablesCharts\Import\Controllers\ImportController();
		
		// Register import AJAX actions (Excel).
		add_action( 'wp_ajax_atables_preview_excel', array( $excel_import_controller, 'preview_excel' ) );
		add_action( 'wp_ajax_atables_import_excel', array( $excel_import_controller, 'import_excel' ) );
		add_action( 'wp_ajax_atables_get_excel_sheets', array( $excel_import_controller, 'get_excel_sheets' ) );
		
		// Register import AJAX actions (XML).
		add_action( 'wp_ajax_atables_preview_xml', array( $excel_import_controller, 'preview_xml' ) );
		add_action( 'wp_ajax_atables_import_xml', array( $excel_import_controller, 'import_xml' ) );
		add_action( 'wp_ajax_atables_get_xml_structure', array( $excel_import_controller, 'get_xml_structure' ) );
		
		// Load Charts module.
		require_once ATABLES_PLUGIN_DIR . 'src/modules/charts/index.php';
		
		// Register Chart Controller.
		$chart_controller = new \ATablesCharts\Charts\Controllers\ChartController();
		$chart_controller->register_hooks();
		
		// Load Cache module.
		require_once ATABLES_PLUGIN_DIR . 'src/modules/cache/index.php';
		
		// Register Cache Controller.
		$cache_controller = new \ATablesCharts\Cache\Controllers\CacheController();
		$cache_controller->register_hooks();
		
		// Load Filters module.
		require_once ATABLES_PLUGIN_DIR . 'src/modules/filters/index.php';
		
		// Register Filter Controller.
		$filter_controller = new \ATablesCharts\Filters\Controllers\FilterController();
		$filter_controller->register_hooks();
		
		// Load Bulk Actions module.
		require_once ATABLES_PLUGIN_DIR . 'src/modules/bulk/BulkActionsController.php';
		
		// Register Bulk Actions Controller.
		$bulk_controller = new \ATablesCharts\Bulk\Controllers\BulkActionsController();
		$bulk_controller->register_hooks();
		
		// Load Google Sheets module.
		require_once ATABLES_PLUGIN_DIR . 'src/modules/googlesheets/GoogleSheetsService.php';
		require_once ATABLES_PLUGIN_DIR . 'src/modules/googlesheets/GoogleSheetsController.php';
		
		// Register Google Sheets Controller.
		$googlesheets_controller = new \ATablesCharts\GoogleSheets\Controllers\GoogleSheetsController();
		$googlesheets_controller->register_hooks();
		
		// Load Validation module.
		require_once ATABLES_PLUGIN_DIR . 'src/modules/validation/ValidationService.php';
		require_once ATABLES_PLUGIN_DIR . 'src/modules/validation/ValidationController.php';
		
		// Register Validation Controller.
		$validation_controller = new \ATablesCharts\Validation\Controllers\ValidationController();
		$validation_controller->register_hooks();
		
		// Load Cell Merging module.
		require_once ATABLES_PLUGIN_DIR . 'src/modules/cellmerging/CellMergingService.php';
		require_once ATABLES_PLUGIN_DIR . 'src/modules/cellmerging/CellMergingController.php';
		
		// Register Cell Merging Controller.
		$cellmerging_controller = new \ATablesCharts\CellMerging\Controllers\CellMergingController();
		$cellmerging_controller->register_hooks();
		
		// Load Formulas module.
		require_once ATABLES_PLUGIN_DIR . 'src/modules/formulas/FormulaService.php';
		require_once ATABLES_PLUGIN_DIR . 'src/modules/formulas/FormulaController.php';
		
		// Register Formula Controller.
		$formula_controller = new \ATablesCharts\Formulas\Controllers\FormulaController();
		$formula_controller->register_hooks();
		
		// Load Enhanced Table Controller (for tabbed edit interface).
		require_once ATABLES_PLUGIN_DIR . 'src/modules/core/controllers/EnhancedTableController.php';
		$enhanced_controller = new \ATablesCharts\Core\Controllers\EnhancedTableController();
		$enhanced_controller->register_hooks();

		// Load Cron module (scheduled data refresh).
		require_once ATABLES_PLUGIN_DIR . 'src/modules/cron/index.php';

		// Register Cron Controller.
		$cron_controller = new \ATablesCharts\Cron\Controllers\CronController();
		$cron_controller->register_hooks();

		$this->logger->info( 'AJAX hooks registered', array(
			'controllers' => array( 'ImportController', 'TableController', 'ExportController', 'ExcelImportController', 'ChartController', 'CacheController', 'FilterController', 'BulkActionsController', 'GoogleSheetsController', 'ValidationController', 'CellMergingController', 'FormulaController', 'EnhancedTableController', 'CronController' ),
		) );
	}

	/**
	 * Register shortcode hooks
	 *
	 * @since 1.0.0
	 */
	private function register_shortcode_hooks() {
		// Load Frontend module (simplified version).
		require_once ATABLES_PLUGIN_DIR . 'src/modules/frontend/index.php';
		
		// Register Table Shortcode.
		$table_shortcode = new \ATablesCharts\Frontend\Shortcodes\TableShortcode();
		$table_shortcode->register();
		
		// Register Cell Shortcode.
		$cell_shortcode = new \ATablesCharts\Frontend\Shortcodes\CellShortcode();
		$cell_shortcode->register();
		
		// Register Chart Shortcode.
		$chart_shortcode = new \ATablesCharts\Frontend\Shortcodes\ChartShortcode();
		$chart_shortcode->register();
		
		$this->logger->info( 'Shortcode hooks registered (simple version)', array(
			'shortcodes' => array( 'atable', 'atable_cell', 'achart' ),
		) );
	}

	/**
	 * Register Gutenberg blocks
	 *
	 * @since 1.0.0
	 */
	private function register_gutenberg_hooks() {
		// Load Gutenberg module.
		require_once ATABLES_PLUGIN_DIR . 'src/modules/gutenberg/index.php';

		// Register Gutenberg Controller.
		$gutenberg_controller = new \ATablesCharts\Gutenberg\GutenbergController();
		$gutenberg_controller->register_hooks();

		$this->logger->info( 'Gutenberg blocks registered', array(
			'blocks' => array( 'atables/table', 'atables/chart' ),
		) );
	}

	/**
	 * Enqueue admin styles
	 *
	 * @since 1.0.0
	 * @param string $hook_suffix Current admin page hook suffix.
	 */
	public function enqueue_admin_styles( $hook_suffix ) {
		// Only load on our plugin pages.
		if ( ! $this->is_plugin_page( $hook_suffix ) ) {
			return;
		}

		// Always load global styles.
		wp_enqueue_style(
			$this->plugin_slug . '-global',
			ATABLES_PLUGIN_URL . 'assets/css/admin-global.css',
			array(),
			$this->version,
			'all'
		);
		
		// Always load notification styles.
		wp_enqueue_style(
			$this->plugin_slug . '-notifications',
			ATABLES_PLUGIN_URL . 'assets/css/notifications.css',
			array( $this->plugin_slug . '-global' ),
			$this->version,
			'all'
		);
		
		// Always load modal styles.
		wp_enqueue_style(
			$this->plugin_slug . '-modals',
			ATABLES_PLUGIN_URL . 'assets/css/admin-modals.css',
			array( $this->plugin_slug . '-global' ),
			$this->version,
			'all'
		);

		// Load page-specific styles.
		$page_styles = $this->get_page_specific_styles( $hook_suffix );
		if ( ! empty( $page_styles ) ) {
			foreach ( $page_styles as $handle => $file ) {
				wp_enqueue_style(
					$this->plugin_slug . '-' . $handle,
					ATABLES_PLUGIN_URL . 'assets/css/' . $file,
					array( $this->plugin_slug . '-global' ),
					$this->version,
					'all'
				);
			}
		}
	}

	/**
	 * Get page-specific CSS files
	 *
	 * @since 1.0.0
	 * @param string $hook_suffix Current admin page hook suffix.
	 * @return array Array of CSS files to load.
	 */
	private function get_page_specific_styles( $hook_suffix ) {
		$styles = array();

		// Dashboard page.
		if ( $hook_suffix === 'toplevel_page_' . $this->plugin_slug ) {
			$styles['dashboard'] = 'admin-dashboard.css';
		}

		// Create table page.
		if ( $hook_suffix === $this->plugin_slug . '_page_' . $this->plugin_slug . '-create' ) {
			$styles['wizard'] = 'admin-wizard.css';
		}
		
		// Manual table page.
		if ( $hook_suffix === 'admin_page_' . $this->plugin_slug . '-manual' ) {
			$styles['table-edit'] = 'admin-table-edit.css';
		}
		
		// MySQL Query page.
		if ( $hook_suffix === 'admin_page_' . $this->plugin_slug . '-mysql' ) {
			$styles['mysql-query'] = 'admin-mysql-query.css';
		}
		
		// Charts pages.
		if ( $hook_suffix === $this->plugin_slug . '_page_' . $this->plugin_slug . '-charts' ||
		     $hook_suffix === $this->plugin_slug . '_page_' . $this->plugin_slug . '-create-chart' ) {
			$styles['charts'] = 'admin-charts.css';
		}

		// View table page.
		if ( $hook_suffix === 'admin_page_' . $this->plugin_slug . '-view' ) {
			$styles['table-view'] = 'admin-table-view.css';
			$styles['filters'] = 'admin-filters.css';
		}
		
		// Edit table page.
		if ( $hook_suffix === 'admin_page_' . $this->plugin_slug . '-edit' ) {
			$styles['table-edit'] = 'admin-table-edit.css';
			$styles['edit-tabs'] = 'admin-edit-tabs.css';
		}

		// Settings page.
		if ( $hook_suffix === $this->plugin_slug . '_page_' . $this->plugin_slug . '-settings' ) {
			$styles['settings'] = 'admin-settings.css';
		}

		return $styles;
	}

	/**
	 * Enqueue admin scripts
	 *
	 * @since 1.0.0
	 * @param string $hook_suffix Current admin page hook suffix.
	 */
	public function enqueue_admin_scripts( $hook_suffix ) {
		// Only load on our plugin pages.
		if ( ! $this->is_plugin_page( $hook_suffix ) ) {
			return;
		}
		
		// Enqueue notification system first (no dependencies, used everywhere).
		wp_enqueue_script(
			$this->plugin_slug . '-notifications',
			ATABLES_PLUGIN_URL . 'assets/js/notifications.js',
			array( 'jquery' ),
			$this->version,
			true
		);
		
		// Enqueue modal system (dependency for admin-main).
		wp_enqueue_script(
			$this->plugin_slug . '-modals',
			ATABLES_PLUGIN_URL . 'assets/js/admin-modals.js',
			array( 'jquery' ),
			$this->version,
			true
		);
		
		// Enqueue delete functionality (depends on modals).
		wp_enqueue_script(
			$this->plugin_slug . '-delete',
			ATABLES_PLUGIN_URL . 'assets/js/admin-delete.js',
			array( 'jquery', $this->plugin_slug . '-modals' ),
			$this->version,
			true
		);

		wp_enqueue_script(
			$this->plugin_slug . '-admin',
			ATABLES_PLUGIN_URL . 'assets/js/admin-main.js',
			array( 'jquery', $this->plugin_slug . '-modals' ),
			$this->version,
			true
		);
		
		// Load enhanced edit page scripts
		if ( $hook_suffix === 'admin_page_' . $this->plugin_slug . '-edit' ) {
			// Color picker for conditional formatting
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );
			
			// Save handler (coordinates all tabs)
			wp_enqueue_script(
				$this->plugin_slug . '-save-handler',
				ATABLES_PLUGIN_URL . 'assets/js/admin-save-handler.js',
				array( 'jquery', 'wp-color-picker' ),
				$this->version,
				true
			);
		}
		
		// Load table view AJAX script on view table page
		if ( $hook_suffix === 'admin_page_' . $this->plugin_slug . '-view' ) {
			// AJAX table view (pagination, search, sort)
			wp_enqueue_script(
				$this->plugin_slug . '-table-view',
				ATABLES_PLUGIN_URL . 'assets/js/admin-table-view.js',
				array( 'jquery' ),
				$this->version,
				true
			);
			
			// Simplified server-side filter builder (v2)
			wp_enqueue_script(
				$this->plugin_slug . '-filter-builder-v2',
				ATABLES_PLUGIN_URL . 'assets/js/admin-filter-builder-v2.js',
				array( 'jquery' ),
				$this->version,
				true
			);
			
			// Bulk edit functionality
			wp_enqueue_script(
				$this->plugin_slug . '-bulk-edit',
				ATABLES_PLUGIN_URL . 'assets/js/admin-bulk-edit.js',
				array( 'jquery' ),
				$this->version,
				true
			);
			
			// Bulk edit styles
			wp_enqueue_style(
				$this->plugin_slug . '-bulk-edit',
				ATABLES_PLUGIN_URL . 'assets/css/admin-bulk-edit.css',
				array(),
				$this->version,
				'all'
			);
		}

		// Localize script with AJAX URL and nonce.
		wp_localize_script(
			$this->plugin_slug . '-admin',
			'aTablesAdmin',
			array(
				'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
				'nonce'        => wp_create_nonce( 'atables_admin_nonce' ),
				'exportNonce'  => wp_create_nonce( 'atables_export_nonce' ),
				'importNonce'  => wp_create_nonce( 'atables_import_nonce' ),
				'pluginUrl'    => ATABLES_PLUGIN_URL,
				'i18n'         => array(
					'error'   => __( 'An error occurred', 'a-tables-charts' ),
					'success' => __( 'Success', 'a-tables-charts' ),
					'loading' => __( 'Loading...', 'a-tables-charts' ),
				),
			)
		);
	}

	/**
	 * Enqueue public styles
	 *
	 * @since 1.0.0
	 */
	public function enqueue_public_styles() {
		// Enqueue Dashicons for frontend buttons
		wp_enqueue_style( 'dashicons' );
		
		// DataTables CSS from CDN.
		wp_enqueue_style(
			'datatables-css',
			'https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css',
			array(),
			'1.13.7',
			'all'
		);
		
		// Table themes (must load before table styles).
		wp_enqueue_style(
			$this->plugin_slug . '-table-themes',
			ATABLES_PLUGIN_URL . 'assets/css/table-themes.css',
			array( 'datatables-css' ),
			$this->version,
			'all'
		);
		
		// Column formatting styles.
		wp_enqueue_style(
			$this->plugin_slug . '-column-formatting',
			ATABLES_PLUGIN_URL . 'assets/css/column-formatting.css',
			array( $this->plugin_slug . '-table-themes' ),
			$this->version,
			'all'
		);
		
		// Conditional formatting styles.
		wp_enqueue_style(
			$this->plugin_slug . '-conditional-formatting',
			ATABLES_PLUGIN_URL . 'assets/css/conditional-formatting.css',
			array( $this->plugin_slug . '-table-themes' ),
			$this->version,
			'all'
		);
		
		// Table styles (for shortcode).
		wp_enqueue_style(
			$this->plugin_slug . '-tables',
			ATABLES_PLUGIN_URL . 'assets/css/public-tables.css',
			array( 'datatables-css', $this->plugin_slug . '-table-themes', $this->plugin_slug . '-column-formatting' ),
			$this->version,
			'all'
		);
		
		// Chart styles (for shortcode).
		wp_enqueue_style(
			$this->plugin_slug . '-charts',
			ATABLES_PLUGIN_URL . 'assets/css/public-charts.css',
			array(),
			$this->version,
			'all'
		);
	}

	/**
	 * Enqueue public scripts
	 *
	 * @since 1.0.0
	 */
	public function enqueue_public_scripts() {
		// Check if Google Charts is enabled
		$settings = get_option( 'atables_settings', array() );
		$google_charts_enabled = isset( $settings['google_charts_enabled'] ) ? $settings['google_charts_enabled'] : true;
		
		// Enqueue Google Charts if enabled
		if ( $google_charts_enabled ) {
			wp_enqueue_script(
				'google-charts',
				'https://www.gstatic.com/charts/loader.js',
				array(),
				null,
				false
			);
		}
		
		// Enqueue Chart.js for chart shortcode from CDN with fallback.
		wp_enqueue_script(
			'chartjs',
			'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js',
			array(),
			'4.4.0',
			false // Load in head for charts
		);
		
		// Add integrity and crossorigin for CDN security
		add_filter('script_loader_tag', function($tag, $handle) {
			if ('chartjs' === $handle) {
				$tag = str_replace(' src=', ' crossorigin="anonymous" src=', $tag);
			}
			return $tag;
		}, 10, 2);
		
		// Enqueue chart renderer (depends on Chart.js).
		wp_enqueue_script(
			$this->plugin_slug . '-charts',
			ATABLES_PLUGIN_URL . 'assets/js/public-charts.js',
			array( 'chartjs' ),
			$this->version,
			true
		);
		
		// Enqueue DataTables for interactive tables.
		wp_enqueue_script(
			'datatables-js',
			'https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js',
			array( 'jquery' ),
			'1.13.7',
			true
		);
		
		// Enqueue tables script (depends on DataTables).
		wp_enqueue_script(
			$this->plugin_slug . '-public-tables',
			ATABLES_PLUGIN_URL . 'assets/js/public-tables.js',
			array( 'jquery', 'datatables-js' ),
			$this->version,
			true
		);
	}

	/**
	 * Register admin menu
	 *
	 * @since 1.0.0
	 */
	public function register_admin_menu() {
		add_menu_page(
			__( 'a-tables-charts', 'a-tables-charts' ),
			__( 'a-tables-charts', 'a-tables-charts' ),
			'manage_options',
			$this->plugin_slug,
			array( $this, 'render_admin_dashboard' ),
			'dashicons-grid-view',
			30
		);

		add_submenu_page(
			$this->plugin_slug,
			__( 'All Tables', 'a-tables-charts' ),
			__( 'All Tables', 'a-tables-charts' ),
			'manage_options',
			$this->plugin_slug,
			array( $this, 'render_admin_dashboard' )
		);

		add_submenu_page(
			$this->plugin_slug,
			__( 'Create Table', 'a-tables-charts' ),
			__( 'Create Table', 'a-tables-charts' ),
			'manage_options',
			$this->plugin_slug . '-create',
			array( $this, 'render_create_table' )
		);
		
		// Hidden submenu for manual table creation.
		add_submenu_page(
			null, // No parent menu (hidden from sidebar).
			__( 'Create Manual Table', 'a-tables-charts' ),
			__( 'Create Manual Table', 'a-tables-charts' ),
			'manage_options',
			$this->plugin_slug . '-manual',
			array( $this, 'render_manual_table' )
		);
		
		// Hidden submenu for MySQL query import.
		add_submenu_page(
			null, // No parent menu (hidden from sidebar).
			__( 'MySQL Query', 'a-tables-charts' ),
			__( 'MySQL Query', 'a-tables-charts' ),
			'manage_options',
			$this->plugin_slug . '-mysql',
			array( $this, 'render_mysql_query' )
		);
		
		// Hidden submenu for Google Sheets import.
		add_submenu_page(
			null, // No parent menu (hidden from sidebar).
			__( 'Google Sheets Import', 'a-tables-charts' ),
			__( 'Google Sheets Import', 'a-tables-charts' ),
			'manage_options',
			$this->plugin_slug . '-google-sheets',
			array( $this, 'render_google_sheets' )
		);
		
		add_submenu_page(
			$this->plugin_slug,
			__( 'Charts', 'a-tables-charts' ),
			__( 'Charts', 'a-tables-charts' ),
			'manage_options',
			$this->plugin_slug . '-charts',
			array( $this, 'render_charts' )
		);
		
		add_submenu_page(
			$this->plugin_slug,
			__( 'Create Chart', 'a-tables-charts' ),
			__( 'Create Chart', 'a-tables-charts' ),
			'manage_options',
			$this->plugin_slug . '-create-chart',
			array( $this, 'render_create_chart' )
		);

		add_submenu_page(
			$this->plugin_slug,
			__( 'Scheduled Refresh', 'a-tables-charts' ),
			__( 'Scheduled Refresh', 'a-tables-charts' ),
			'manage_options',
			$this->plugin_slug . '-scheduled-refresh',
			array( $this, 'render_scheduled_refresh' )
		);
		
		// Hidden submenu for viewing single table.
		add_submenu_page(
			null, // No parent menu (hidden from sidebar).
			__( 'View Table', 'a-tables-charts' ),
			__( 'View Table', 'a-tables-charts' ),
			'manage_options',
			$this->plugin_slug . '-view',
			array( $this, 'render_view_table' )
		);
		
		// Hidden submenu for editing single table.
		add_submenu_page(
			null, // No parent menu (hidden from sidebar).
			__( 'Edit Table', 'a-tables-charts' ),
			__( 'Edit Table', 'a-tables-charts' ),
			'manage_options',
			$this->plugin_slug . '-edit',
			array( $this, 'render_edit_table' )
		);

		add_submenu_page(
			$this->plugin_slug,
			__( 'Settings', 'a-tables-charts' ),
			__( 'Settings', 'a-tables-charts' ),
			'manage_options',
			$this->plugin_slug . '-settings',
			array( $this, 'render_settings' )
		);
	}

	/**
	 * Render admin dashboard page
	 *
	 * @since 1.0.0
	 */
	public function render_admin_dashboard() {
		include ATABLES_PLUGIN_DIR . 'src/modules/core/views/dashboard.php';
	}

	/**
	 * Render create table page
	 *
	 * @since 1.0.0
	 */
	public function render_create_table() {
		include ATABLES_PLUGIN_DIR . 'src/modules/core/views/create-table.php';
	}
	
	/**
	 * Render manual table page
	 *
	 * @since 1.0.0
	 */
	public function render_manual_table() {
		include ATABLES_PLUGIN_DIR . 'src/modules/core/views/manual-table.php';
	}
	
	/**
	 * Render MySQL query page
	 *
	 * @since 1.0.0
	 */
	public function render_mysql_query() {
		include ATABLES_PLUGIN_DIR . 'src/modules/core/views/mysql-query.php';
	}
	
	/**
	 * Render Google Sheets import page
	 *
	 * @since 1.0.0
	 */
	public function render_google_sheets() {
		include ATABLES_PLUGIN_DIR . 'src/modules/core/views/google-sheets.php';
	}
	
	/**
	 * Render charts page
	 *
	 * @since 1.0.0
	 */
	public function render_charts() {
		include ATABLES_PLUGIN_DIR . 'src/modules/core/views/charts.php';
	}
	
	/**
	 * Render create chart page
	 *
	 * @since 1.0.0
	 */
	public function render_create_chart() {
		include ATABLES_PLUGIN_DIR . 'src/modules/core/views/create-chart.php';
	}
	
	/**
	 * Render view table page
	 *
	 * @since 1.0.0
	 */
	public function render_view_table() {
		include ATABLES_PLUGIN_DIR . 'src/modules/core/views/view-table.php';
	}
	
	/**
	 * Render edit table page
	 *
	 * @since 1.0.0
	 */
	public function render_edit_table() {
		// Use enhanced tabbed interface
		include ATABLES_PLUGIN_DIR . 'src/modules/core/views/edit-table-enhanced.php';
	}

	/**
	 * Render scheduled refresh page
	 *
	 * @since 1.0.0
	 */
	public function render_scheduled_refresh() {
		include ATABLES_PLUGIN_DIR . 'src/modules/core/views/scheduled-refresh.php';
	}

	/**
	 * Render settings page
	 *
	 * @since 1.0.0
	 */
	public function render_settings() {
		include ATABLES_PLUGIN_DIR . 'src/modules/core/views/settings.php';
	}

	/**
	 * Check if current page is a plugin page
	 *
	 * @since 1.0.0
	 * @param string $hook_suffix Current admin page hook suffix.
	 * @return bool True if plugin page, false otherwise.
	 */
	private function is_plugin_page( $hook_suffix ) {
		$plugin_pages = array(
			'toplevel_page_' . $this->plugin_slug,
			$this->plugin_slug . '_page_' . $this->plugin_slug . '-create',
			$this->plugin_slug . '_page_' . $this->plugin_slug . '-charts',
			$this->plugin_slug . '_page_' . $this->plugin_slug . '-create-chart',
			$this->plugin_slug . '_page_' . $this->plugin_slug . '-scheduled-refresh',
			'admin_page_' . $this->plugin_slug . '-view',
			'admin_page_' . $this->plugin_slug . '-edit',
			'admin_page_' . $this->plugin_slug . '-manual',
			'admin_page_' . $this->plugin_slug . '-mysql',
			'admin_page_' . $this->plugin_slug . '-google-sheets',
			$this->plugin_slug . '_page_' . $this->plugin_slug . '-settings',
		);

		return in_array( $hook_suffix, $plugin_pages, true );
	}

	/**
	 * Run the plugin
	 *
	 * Executes the loader to run all hooks with WordPress.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * Get plugin version
	 *
	 * @since 1.0.0
	 * @return string Plugin version
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Get plugin slug
	 *
	 * @since 1.0.0
	 * @return string Plugin slug
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Get loader instance
	 *
	 * @since 1.0.0
	 * @return Loader Loader instance
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Register plugin settings
	 *
	 * @since 1.0.0
	 */
	public function register_settings() {
		// Register settings option.
		register_setting(
			'atables_settings',
			'atables_settings',
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize_settings' ),
				'default'           => array(),
			)
		);
	}

	/**
	 * Sanitize settings
	 *
	 * @param array $input Raw input data.
	 * @return array Sanitized data
	 */
	public function sanitize_settings( $input ) {
		// Load settings service.
		require_once ATABLES_PLUGIN_DIR . 'src/modules/settings/index.php';
		$settings_service = new \ATablesCharts\Settings\Services\SettingsService();
		
		// Use the service to validate settings.
		$validated = array();
		
		// Rows per page.
		if ( isset( $input['rows_per_page'] ) ) {
			$validated['rows_per_page'] = max( 1, min( 100, intval( $input['rows_per_page'] ) ) );
		}
		
		// Default table style.
		if ( isset( $input['default_table_style'] ) ) {
			$allowed_styles = array( 'default', 'striped', 'bordered', 'hover' );
			$validated['default_table_style'] = in_array( $input['default_table_style'], $allowed_styles, true )
				? $input['default_table_style']
				: 'default';
		}
		
		// Boolean settings.
		$boolean_keys = array(
			'enable_responsive',
			'enable_search',
			'enable_sorting',
			'enable_pagination',
			'enable_export',
			'cache_enabled',
			'google_charts_enabled',
			'chartjs_enabled',
			'lazy_load_tables',
			'async_loading',
			'sanitize_html',
		);
		
		foreach ( $boolean_keys as $key ) {
			$validated[ $key ] = isset( $input[ $key ] ) ? (bool) $input[ $key ] : false;
		}
		
		// Chart library selection
		if ( isset( $input['default_chart_library'] ) ) {
			$allowed_libraries = array( 'chartjs', 'google' );
			$validated['default_chart_library'] = in_array( $input['default_chart_library'], $allowed_libraries, true )
				? $input['default_chart_library']
				: 'chartjs';
		}
		
		// Text fields.
		$text_keys = array(
			'date_format',
			'time_format',
			'decimal_separator',
			'thousands_separator',
			'csv_delimiter',
			'csv_enclosure',
			'csv_escape',
			'export_filename',
			'export_date_format',
			'export_encoding',
		);
		
		foreach ( $text_keys as $key ) {
			if ( isset( $input[ $key ] ) ) {
				$validated[ $key ] = sanitize_text_field( $input[ $key ] );
			}
		}
		
		// Numeric fields.
		if ( isset( $input['cache_expiration'] ) ) {
			$validated['cache_expiration'] = max( 0, intval( $input['cache_expiration'] ) );
		}
		
		// Max import size (convert MB to bytes).
		if ( isset( $input['max_import_size'] ) ) {
			$size_mb = max( 1, min( 100, intval( $input['max_import_size'] ) ) );
			$validated['max_import_size'] = $size_mb * 1048576; // Convert to bytes
		}
		
		// Array fields (allowed file types).
		if ( isset( $input['allowed_file_types'] ) && is_array( $input['allowed_file_types'] ) ) {
			$allowed_extensions = array( 'csv', 'json', 'xlsx', 'xls', 'xml' );
			$validated['allowed_file_types'] = array_intersect( $input['allowed_file_types'], $allowed_extensions );
			// Ensure at least one type is allowed
			if ( empty( $validated['allowed_file_types'] ) ) {
				$validated['allowed_file_types'] = array( 'csv' );
			}
		} else {
			// If no types selected, default to CSV only
			$validated['allowed_file_types'] = array( 'csv' );
		}
		
		return $validated;
	}
}

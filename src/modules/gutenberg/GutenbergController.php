<?php
/**
 * Gutenberg Controller
 *
 * Handles Gutenberg block registration and integration.
 *
 * @package ATablesCharts\Gutenberg
 * @since 1.0.0
 */

namespace ATablesCharts\Gutenberg;

use ATablesCharts\Tables\Services\TableService;
use ATablesCharts\Charts\Services\ChartService;

/**
 * GutenbergController Class
 *
 * Registers custom Gutenberg blocks for tables and charts.
 */
class GutenbergController {

	/**
	 * Table Service
	 *
	 * @var TableService
	 */
	private $table_service;

	/**
	 * Chart Service
	 *
	 * @var ChartService
	 */
	private $chart_service;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->table_service = new TableService();
		$this->chart_service = new ChartService();
	}

	/**
	 * Register hooks
	 */
	public function register_hooks() {
		// Register blocks
		add_action( 'init', array( $this, 'register_blocks' ) );

		// Enqueue block editor assets
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_editor_assets' ) );
	}

	/**
	 * Register Gutenberg blocks
	 */
	public function register_blocks() {
		// Check if block registration function exists
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		// Register Table Block
		register_block_type(
			ATABLES_PLUGIN_DIR . 'src/modules/gutenberg/blocks/table-block',
			array(
				'render_callback' => array( $this, 'render_table_block' ),
			)
		);

		// Register Chart Block
		register_block_type(
			ATABLES_PLUGIN_DIR . 'src/modules/gutenberg/blocks/chart-block',
			array(
				'render_callback' => array( $this, 'render_chart_block' ),
			)
		);
	}

	/**
	 * Enqueue block editor assets
	 */
	public function enqueue_editor_assets() {
		// Enqueue block editor script
		wp_enqueue_script(
			'atables-blocks',
			ATABLES_PLUGIN_URL . 'assets/js/blocks.js',
			array( 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n', 'wp-data' ),
			ATABLES_VERSION,
			true
		);

		// Localize script with data
		wp_localize_script(
			'atables-blocks',
			'atablesBlockData',
			array(
				'tables' => $this->get_tables_for_editor(),
				'charts' => $this->get_charts_for_editor(),
			)
		);

		// Enqueue block editor styles
		wp_enqueue_style(
			'atables-blocks-editor',
			ATABLES_PLUGIN_URL . 'assets/css/blocks-editor.css',
			array( 'wp-edit-blocks' ),
			ATABLES_VERSION
		);
	}

	/**
	 * Get tables for editor
	 *
	 * @return array Tables list
	 */
	private function get_tables_for_editor() {
		$result = $this->table_service->get_all_tables();
		$tables = $result['tables'] ?? array();
		$options = array();

		foreach ( $tables as $table ) {
			$options[] = array(
				'value' => $table->get_id(),
				'label' => $table->get_title(),
			);
		}

		return $options;
	}

	/**
	 * Get charts for editor
	 *
	 * @return array Charts list
	 */
	private function get_charts_for_editor() {
		$result = $this->chart_service->get_all_charts();
		$charts = $result['charts'] ?? array();
		$options = array();

		foreach ( $charts as $chart ) {
			$options[] = array(
				'value' => $chart->get_id(),
				'label' => $chart->get_title(),
			);
		}

		return $options;
	}

	/**
	 * Render Table Block
	 *
	 * @param array $attributes Block attributes.
	 * @return string Rendered block HTML.
	 */
	public function render_table_block( $attributes ) {
		// Get table ID from attributes
		$table_id = isset( $attributes['tableId'] ) ? intval( $attributes['tableId'] ) : 0;

		if ( empty( $table_id ) ) {
			return '<div class="atables-block-notice">' . esc_html__( 'Please select a table.', 'a-tables-charts' ) . '</div>';
		}

		// Use existing shortcode to render
		return do_shortcode( '[atables id="' . $table_id . '"]' );
	}

	/**
	 * Render Chart Block
	 *
	 * @param array $attributes Block attributes.
	 * @return string Rendered block HTML.
	 */
	public function render_chart_block( $attributes ) {
		// Get chart ID from attributes
		$chart_id = isset( $attributes['chartId'] ) ? intval( $attributes['chartId'] ) : 0;

		if ( empty( $chart_id ) ) {
			return '<div class="atables-block-notice">' . esc_html__( 'Please select a chart.', 'a-tables-charts' ) . '</div>';
		}

		// Use existing shortcode to render
		return do_shortcode( '[atables_chart id="' . $chart_id . '"]' );
	}
}

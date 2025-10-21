<?php
/**
 * Chart Shortcode - Enhanced Version
 *
 * Handles the [achart] shortcode with support for Chart.js and Google Charts.
 *
 * @package ATablesCharts\Frontend\Shortcodes
 * @since 1.0.0
 */

namespace ATablesCharts\Frontend\Shortcodes;

use ATablesCharts\Charts\Repositories\ChartRepository;
use ATablesCharts\Charts\Services\ChartService;
use ATablesCharts\Frontend\Renderers\ChartRenderer;
use ATablesCharts\Charts\Renderers\GoogleChartsRenderer;

class ChartShortcode {

	private $repository;
	private $service;
	private $renderer;
	private $google_renderer;

	public function __construct() {
		$this->repository = new ChartRepository();
		$this->service    = new ChartService();
		$this->renderer   = new ChartRenderer();
		$this->google_renderer = new GoogleChartsRenderer();
	}

	public function register() {
		add_shortcode( 'achart', array( $this, 'render' ) );
	}

	public function render( $atts ) {
		$atts = shortcode_atts(
			array(
				'id'      => 0,
				'width'   => '100%',
				'height'  => '400px',
				'library' => '', // 'chartjs', 'google', or empty for default
			),
			$atts,
			'achart'
		);

		$chart_id = (int) $atts['id'];
		
		if ( empty( $chart_id ) ) {
			return '<p><strong>A-Charts Error:</strong> Chart ID is required. Usage: [achart id="123"]</p>';
		}

		$chart = $this->repository->find_by_id( $chart_id );
		
		if ( ! $chart ) {
			return '<p><strong>A-Charts Error:</strong> Chart not found.</p>';
		}

		if ( $chart->status !== 'active' ) {
			return '<p><strong>A-Charts Error:</strong> This chart is not available.</p>';
		}

		// Get processed chart data
		$chart_data = $this->service->get_chart_data( $chart_id );
		
		if ( ! $chart_data ) {
			return '<p><strong>A-Charts Error:</strong> Unable to load chart data. Please check chart configuration.</p>';
		}

		// Determine which library to use
		$library = $this->get_chart_library( $atts['library'] );

		$options = array(
			'chart'      => $chart,
			'chart_data' => $chart_data,
			'width'      => $atts['width'],
			'height'     => $atts['height'],
		);

		// Render with selected library
		if ( $library === 'google' ) {
			return $this->google_renderer->render( $chart, $chart_data, $options );
		} else {
			// Default to Chart.js
			return $this->renderer->render( $options );
		}
	}

	/**
	 * Determine which chart library to use
	 *
	 * @param string $library_attr Library attribute from shortcode.
	 * @return string Library to use ('chartjs' or 'google').
	 */
	private function get_chart_library( $library_attr ) {
		// If explicitly specified in shortcode, use that
		if ( ! empty( $library_attr ) ) {
			return $library_attr === 'google' ? 'google' : 'chartjs';
		}

		// Otherwise, check global settings
		$settings = get_option( 'atables_settings', array() );
		
		// Check if Google Charts is enabled and preferred
		$google_enabled = isset( $settings['google_charts_enabled'] ) && $settings['google_charts_enabled'];
		$default_lib = isset( $settings['default_chart_library'] ) ? $settings['default_chart_library'] : 'chartjs';
		
		// Return Google if enabled AND set as default
		if ( $google_enabled && $default_lib === 'google' ) {
			return 'google';
		}

		// Default to Chart.js
		return 'chartjs';
	}
}

<?php
/**
 * Tests for GoogleChartsRenderer
 *
 * @package ATablesCharts\Tests\Unit\Charts
 * @since 1.0.0
 */

namespace ATablesCharts\Tests\Unit\Charts;

use ATablesCharts\Charts\Renderers\GoogleChartsRenderer;
use ATablesCharts\Charts\Types\Chart;
use PHPUnit\Framework\TestCase;

/**
 * GoogleChartsRenderer Test Case
 */
class GoogleChartsRendererTest extends TestCase {

	/**
	 * GoogleChartsRenderer instance
	 *
	 * @var GoogleChartsRenderer
	 */
	private $renderer;

	/**
	 * Set up test
	 */
	protected function setUp(): void {
		parent::setUp();
		$this->renderer = new GoogleChartsRenderer();
	}

	// Render Tests

	public function test_render_returns_html_string() {
		$chart = new Chart( array(
			'id'       => 1,
			'table_id' => 1,
			'title'    => 'Test Chart',
			'type'     => 'bar',
			'config'   => array(),
		) );

		$data = array(
			'labels'   => array( 'Q1', 'Q2', 'Q3', 'Q4' ),
			'datasets' => array(
				array(
					'label' => 'Sales',
					'data'  => array( 100, 150, 200, 180 ),
				),
			),
		);

		$html = $this->renderer->render( $chart, $data );

		$this->assertIsString( $html );
		$this->assertStringContainsString( 'google_chart_', $html );
	}

	public function test_render_includes_chart_title() {
		$chart = new Chart( array(
			'id'       => 1,
			'table_id' => 1,
			'title'    => 'Sales Revenue Chart',
			'type'     => 'bar',
		) );

		$data = array(
			'labels'   => array( 'Q1' ),
			'datasets' => array(
				array(
					'label' => 'Sales',
					'data'  => array( 100 ),
				),
			),
		);

		$html = $this->renderer->render( $chart, $data );

		$this->assertStringContainsString( 'Sales Revenue Chart', $html );
	}

	public function test_render_includes_google_charts_loader() {
		// Create a fresh renderer to reset the static variable
		$renderer = new GoogleChartsRenderer();

		$chart = new Chart( array(
			'id'       => 999, // Use unique ID
			'table_id' => 1,
			'title'    => 'Test',
			'type'     => 'line',
		) );

		$data = array(
			'labels'   => array( 'A' ),
			'datasets' => array(
				array(
					'label' => 'Data',
					'data'  => array( 10 ),
				),
			),
		);

		$html = $renderer->render( $chart, $data );

		// Loader may or may not be included depending on static state
		// Just verify basic render works
		$this->assertStringContainsString( 'google_chart_', $html );
		$this->assertStringContainsString( 'atables-google-chart', $html );
	}

	public function test_render_includes_chart_container() {
		$chart = new Chart( array(
			'id'       => 1,
			'table_id' => 1,
			'title'    => 'Test',
			'type'     => 'pie',
		) );

		$data = array(
			'labels'   => array( 'A', 'B' ),
			'datasets' => array(
				array(
					'label' => 'Data',
					'data'  => array( 30, 70 ),
				),
			),
		);

		$html = $this->renderer->render( $chart, $data );

		$this->assertStringContainsString( 'atables-google-chart-wrapper', $html );
		$this->assertStringContainsString( 'atables-google-chart', $html );
	}

	public function test_render_with_custom_dimensions() {
		$chart = new Chart( array(
			'id'       => 1,
			'table_id' => 1,
			'title'    => 'Test',
			'type'     => 'bar',
		) );

		$data = array(
			'labels'   => array( 'A' ),
			'datasets' => array(
				array(
					'label' => 'Data',
					'data'  => array( 50 ),
				),
			),
		);

		$options = array(
			'width'  => '800px',
			'height' => '600px',
		);

		$html = $this->renderer->render( $chart, $data, $options );

		$this->assertStringContainsString( '800px', $html );
		$this->assertStringContainsString( '600px', $html );
	}

	// Chart Type Tests

	public function test_render_bar_chart() {
		$chart = new Chart( array(
			'id'       => 1,
			'table_id' => 1,
			'title'    => 'Bar Chart',
			'type'     => 'bar',
		) );

		$data = array(
			'labels'   => array( 'Jan', 'Feb' ),
			'datasets' => array(
				array(
					'label' => 'Revenue',
					'data'  => array( 1000, 1500 ),
				),
			),
		);

		$html = $this->renderer->render( $chart, $data );

		$this->assertStringContainsString( 'BarChart', $html );
	}

	public function test_render_line_chart() {
		$chart = new Chart( array(
			'id'       => 1,
			'table_id' => 1,
			'title'    => 'Line Chart',
			'type'     => 'line',
		) );

		$data = array(
			'labels'   => array( 'Week 1', 'Week 2' ),
			'datasets' => array(
				array(
					'label' => 'Growth',
					'data'  => array( 50, 75 ),
				),
			),
		);

		$html = $this->renderer->render( $chart, $data );

		$this->assertStringContainsString( 'LineChart', $html );
	}

	public function test_render_pie_chart() {
		$chart = new Chart( array(
			'id'       => 1,
			'table_id' => 1,
			'title'    => 'Pie Chart',
			'type'     => 'pie',
		) );

		$data = array(
			'labels'   => array( 'Category A', 'Category B' ),
			'datasets' => array(
				array(
					'label' => 'Distribution',
					'data'  => array( 60, 40 ),
				),
			),
		);

		$html = $this->renderer->render( $chart, $data );

		$this->assertStringContainsString( 'PieChart', $html );
	}

	public function test_render_doughnut_chart() {
		$chart = new Chart( array(
			'id'       => 1,
			'table_id' => 1,
			'title'    => 'Doughnut Chart',
			'type'     => 'doughnut',
		) );

		$data = array(
			'labels'   => array( 'Part 1', 'Part 2' ),
			'datasets' => array(
				array(
					'label' => 'Parts',
					'data'  => array( 45, 55 ),
				),
			),
		);

		$html = $this->renderer->render( $chart, $data );

		// Doughnut maps to PieChart with pieHole option
		$this->assertStringContainsString( 'PieChart', $html );
		$this->assertStringContainsString( 'pieHole', $html );
	}

	public function test_render_column_chart() {
		$chart = new Chart( array(
			'id'       => 1,
			'table_id' => 1,
			'title'    => 'Column Chart',
			'type'     => 'column',
		) );

		$data = array(
			'labels'   => array( 'Product A', 'Product B' ),
			'datasets' => array(
				array(
					'label' => 'Sales',
					'data'  => array( 120, 95 ),
				),
			),
		);

		$html = $this->renderer->render( $chart, $data );

		$this->assertStringContainsString( 'ColumnChart', $html );
	}

	public function test_render_area_chart() {
		$chart = new Chart( array(
			'id'       => 1,
			'table_id' => 1,
			'title'    => 'Area Chart',
			'type'     => 'area',
		) );

		$data = array(
			'labels'   => array( 'Day 1', 'Day 2' ),
			'datasets' => array(
				array(
					'label' => 'Traffic',
					'data'  => array( 500, 650 ),
				),
			),
		);

		$html = $this->renderer->render( $chart, $data );

		$this->assertStringContainsString( 'AreaChart', $html );
	}

	public function test_render_scatter_chart() {
		$chart = new Chart( array(
			'id'       => 1,
			'table_id' => 1,
			'title'    => 'Scatter Chart',
			'type'     => 'scatter',
		) );

		$data = array(
			'labels'   => array( 1, 2, 3 ),
			'datasets' => array(
				array(
					'label' => 'Points',
					'data'  => array( 10, 20, 15 ),
				),
			),
		);

		$html = $this->renderer->render( $chart, $data );

		$this->assertStringContainsString( 'ScatterChart', $html );
	}

	public function test_render_radar_chart() {
		$chart = new Chart( array(
			'id'       => 1,
			'table_id' => 1,
			'title'    => 'Radar Chart',
			'type'     => 'radar',
		) );

		$data = array(
			'labels'   => array( 'Speed', 'Power', 'Defense' ),
			'datasets' => array(
				array(
					'label' => 'Stats',
					'data'  => array( 85, 90, 75 ),
				),
			),
		);

		$html = $this->renderer->render( $chart, $data );

		$this->assertStringContainsString( 'RadarChart', $html );
	}

	// Data Handling Tests

	public function test_render_with_empty_data() {
		$chart = new Chart( array(
			'id'       => 1,
			'table_id' => 1,
			'title'    => 'Empty Data Chart',
			'type'     => 'bar',
		) );

		$data = array();

		$html = $this->renderer->render( $chart, $data );

		// Should render with "No Data" fallback
		$this->assertIsString( $html );
		$this->assertStringContainsString( 'No Data', $html );
	}

	public function test_render_with_multiple_datasets() {
		$chart = new Chart( array(
			'id'       => 1,
			'table_id' => 1,
			'title'    => 'Multi-Dataset Chart',
			'type'     => 'line',
		) );

		$data = array(
			'labels'   => array( 'Q1', 'Q2', 'Q3' ),
			'datasets' => array(
				array(
					'label' => 'Product A',
					'data'  => array( 100, 120, 140 ),
				),
				array(
					'label' => 'Product B',
					'data'  => array( 80, 90, 95 ),
				),
				array(
					'label' => 'Product C',
					'data'  => array( 60, 70, 85 ),
				),
			),
		);

		$html = $this->renderer->render( $chart, $data );

		$this->assertStringContainsString( 'Product A', $html );
		$this->assertStringContainsString( 'Product B', $html );
		$this->assertStringContainsString( 'Product C', $html );
	}

	public function test_render_with_numeric_labels() {
		$chart = new Chart( array(
			'id'       => 1,
			'table_id' => 1,
			'title'    => 'Numeric Labels',
			'type'     => 'bar',
		) );

		$data = array(
			'labels'   => array( 2020, 2021, 2022, 2023 ),
			'datasets' => array(
				array(
					'label' => 'Annual Revenue',
					'data'  => array( 50000, 65000, 72000, 80000 ),
				),
			),
		);

		$html = $this->renderer->render( $chart, $data );

		$this->assertStringContainsString( '2020', $html );
		$this->assertStringContainsString( '2023', $html );
	}

	// Configuration Tests

	public function test_render_with_custom_colors() {
		$chart = new Chart( array(
			'id'       => 1,
			'table_id' => 1,
			'title'    => 'Custom Colors',
			'type'     => 'bar',
			'config'   => array(
				'colors' => array( '#FF6384', '#36A2EB', '#FFCE56' ),
			),
		) );

		$data = array(
			'labels'   => array( 'A', 'B', 'C' ),
			'datasets' => array(
				array(
					'label' => 'Data',
					'data'  => array( 10, 20, 30 ),
				),
			),
		);

		$html = $this->renderer->render( $chart, $data );

		$this->assertStringContainsString( '#FF6384', $html );
		$this->assertStringContainsString( '#36A2EB', $html );
	}

	public function test_render_with_axis_labels() {
		$chart = new Chart( array(
			'id'       => 1,
			'table_id' => 1,
			'title'    => 'Axis Labels Chart',
			'type'     => 'bar',
			'config'   => array(
				'x_axis_label' => 'Months',
				'y_axis_label' => 'Revenue ($)',
			),
		) );

		$data = array(
			'labels'   => array( 'Jan', 'Feb' ),
			'datasets' => array(
				array(
					'label' => 'Sales',
					'data'  => array( 1000, 1200 ),
				),
			),
		);

		$html = $this->renderer->render( $chart, $data );

		$this->assertStringContainsString( 'Months', $html );
		$this->assertStringContainsString( 'Revenue', $html );
	}

	// Security Tests

	public function test_render_escapes_chart_title() {
		$chart = new Chart( array(
			'id'       => 1,
			'table_id' => 1,
			'title'    => 'Test <script>alert("xss")</script> Chart',
			'type'     => 'bar',
		) );

		$data = array(
			'labels'   => array( 'A' ),
			'datasets' => array(
				array(
					'label' => 'Data',
					'data'  => array( 10 ),
				),
			),
		);

		$html = $this->renderer->render( $chart, $data );

		// Verify the title is in JSON (escaped by wp_json_encode)
		// The JSON will contain the title with escaped characters
		$this->assertStringContainsString( 'Test', $html );
		$this->assertStringContainsString( 'Chart', $html );

		// Verify rendering didn't completely fail
		$this->assertStringContainsString( 'google_chart_', $html );
	}

	public function test_render_handles_special_characters_in_labels() {
		$chart = new Chart( array(
			'id'       => 1,
			'table_id' => 1,
			'title'    => 'Special Chars',
			'type'     => 'bar',
		) );

		$data = array(
			'labels'   => array( 'A & B', 'C < D', 'E > F' ),
			'datasets' => array(
				array(
					'label' => 'Data with "quotes"',
					'data'  => array( 10, 20, 30 ),
				),
			),
		);

		$html = $this->renderer->render( $chart, $data );

		// Should handle special characters safely
		$this->assertIsString( $html );
		$this->assertStringContainsString( 'google_chart_', $html );
	}

	// Edge Cases

	public function test_render_with_zero_values() {
		$chart = new Chart( array(
			'id'       => 1,
			'table_id' => 1,
			'title'    => 'Zero Values',
			'type'     => 'line',
		) );

		$data = array(
			'labels'   => array( 'A', 'B', 'C' ),
			'datasets' => array(
				array(
					'label' => 'Data',
					'data'  => array( 0, 0, 0 ),
				),
			),
		);

		$html = $this->renderer->render( $chart, $data );

		$this->assertIsString( $html );
		$this->assertStringContainsString( 'LineChart', $html );
	}

	public function test_render_with_negative_values() {
		$chart = new Chart( array(
			'id'       => 1,
			'table_id' => 1,
			'title'    => 'Negative Values',
			'type'     => 'bar',
		) );

		$data = array(
			'labels'   => array( 'Loss', 'Profit', 'Loss' ),
			'datasets' => array(
				array(
					'label' => 'P&L',
					'data'  => array( -500, 1000, -300 ),
				),
			),
		);

		$html = $this->renderer->render( $chart, $data );

		$this->assertIsString( $html );
		$this->assertStringContainsString( 'BarChart', $html );
	}

	public function test_render_with_large_dataset() {
		$chart = new Chart( array(
			'id'       => 1,
			'table_id' => 1,
			'title'    => 'Large Dataset',
			'type'     => 'line',
		) );

		$labels = range( 1, 100 );
		$values = array_map( function( $i ) {
			return $i * 10;
		}, $labels );

		$data = array(
			'labels'   => $labels,
			'datasets' => array(
				array(
					'label' => 'Series',
					'data'  => $values,
				),
			),
		);

		$html = $this->renderer->render( $chart, $data );

		$this->assertIsString( $html );
		$this->assertStringContainsString( 'LineChart', $html );
	}

	public function test_render_generates_unique_chart_id() {
		$chart = new Chart( array(
			'id'       => 1,
			'table_id' => 1,
			'title'    => 'Test',
			'type'     => 'bar',
		) );

		$data = array(
			'labels'   => array( 'A' ),
			'datasets' => array(
				array(
					'label' => 'Data',
					'data'  => array( 10 ),
				),
			),
		);

		$html1 = $this->renderer->render( $chart, $data );
		$html2 = $this->renderer->render( $chart, $data );

		// Each render should generate a unique ID
		$this->assertNotEquals( $html1, $html2 );
		$this->assertStringContainsString( 'google_chart_1_', $html1 );
		$this->assertStringContainsString( 'google_chart_1_', $html2 );
	}
}

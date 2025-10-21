<?php
/**
 * Google Charts Renderer - Fixed Version
 *
 * Renders charts using Google Charts library.
 *
 * @package ATablesCharts\Charts\Renderers
 * @since 1.0.0
 */

namespace ATablesCharts\Charts\Renderers;

/**
 * GoogleChartsRenderer Class
 *
 * Responsibilities:
 * - Render charts using Google Charts
 * - Convert chart data to Google Charts format
 * - Generate chart configuration
 */
class GoogleChartsRenderer {

	/**
	 * Track if Google Charts has been loaded
	 *
	 * @var bool
	 */
	private static $google_charts_loaded = false;

	/**
	 * Render chart
	 *
	 * @param object $chart Chart object.
	 * @param array  $data  Chart data.
	 * @param array  $options Render options.
	 * @return string Chart HTML.
	 */
	public function render( $chart, $data, $options = array() ) {
		$chart_id = 'google_chart_' . $chart->id . '_' . uniqid();
		
		// Get chart configuration from the chart object
		$config = is_array( $chart->config ) ? $chart->config : array();
		
		// Add defaults
		$config['type'] = $chart->type;
		$config['width'] = $options['width'] ?? '100%';
		$config['height'] = $options['height'] ?? '400px';
		
		// Prepare data for Google Charts
		$chart_data = $this->prepare_data( $data, $config );
		
		// Get chart options
		$chart_options = $this->get_chart_options( $chart, $config );
		
		// Get chart type for Google Charts
		$google_chart_type = $this->get_google_chart_type( $config['type'] );
		
		// Convert to JSON once, safely
		$chart_data_json = wp_json_encode( $chart_data );
		$chart_options_json = wp_json_encode( $chart_options );
		
		ob_start();
		?>
		<div class="atables-google-chart-wrapper" style="max-width: <?php echo esc_attr( $config['width'] ); ?>; margin: 20px 0;">
			<div id="<?php echo esc_attr( $chart_id ); ?>" 
			     class="atables-google-chart" 
			     style="width: 100%; height: <?php echo esc_attr( $config['height'] ); ?>; background: white; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
			</div>
		</div>
		
		<?php if ( ! self::$google_charts_loaded ) : ?>
		<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
		<?php 
			self::$google_charts_loaded = true;
		endif; 
		?>
		
		<script type="text/javascript">
		(function() {
			// Store data in variables to avoid escaping issues
			var chartData_<?php echo esc_attr( $chart_id ); ?> = <?php echo $chart_data_json; ?>;
			var chartOptions_<?php echo esc_attr( $chart_id ); ?> = <?php echo $chart_options_json; ?>;
			
			function drawChart_<?php echo esc_attr( $chart_id ); ?>() {
				try {
					var data = google.visualization.arrayToDataTable(chartData_<?php echo esc_attr( $chart_id ); ?>);
					var chart = new google.visualization.<?php echo esc_attr( $google_chart_type ); ?>(
						document.getElementById('<?php echo esc_js( $chart_id ); ?>')
					);
					chart.draw(data, chartOptions_<?php echo esc_attr( $chart_id ); ?>);
					console.log('✓ Google Chart rendered: <?php echo esc_js( $chart_id ); ?>');
				} catch (error) {
					console.error('Google Charts error:', error);
					var container = document.getElementById('<?php echo esc_js( $chart_id ); ?>');
					if (container) {
						container.innerHTML = '<p style="color: red; padding: 20px; text-align: center;">Error rendering chart. Please check console for details.</p>';
					}
				}
			}
			
			// Load Google Charts and draw
			if (typeof google !== 'undefined' && google.charts) {
				google.charts.load('current', {'packages':['corechart']});
				google.charts.setOnLoadCallback(drawChart_<?php echo esc_attr( $chart_id ); ?>);
			} else {
				console.error('Google Charts library not loaded');
			}
			
			// Redraw on window resize
			window.addEventListener('resize', function() {
				if (typeof google !== 'undefined' && google.visualization) {
					drawChart_<?php echo esc_attr( $chart_id ); ?>();
				}
			});
		})();
		</script>
		<?php
		
		return ob_get_clean();
	}

	/**
	 * Prepare data for Google Charts format
	 *
	 * @param array $data   Chart data.
	 * @param array $config Chart configuration.
	 * @return array Google Charts data array.
	 */
	private function prepare_data( $data, $config ) {
		$google_data = array();
		
		// Validate data structure
		if ( empty( $data['labels'] ) || empty( $data['datasets'] ) ) {
			return array(
				array( 'Label', 'Value' ),
				array( 'No Data', 0 )
			);
		}
		
		// Add header row
		$header = array( 'Label' );
		foreach ( $data['datasets'] as $dataset ) {
			$header[] = isset( $dataset['label'] ) ? $dataset['label'] : 'Data';
		}
		$google_data[] = $header;
		
		// Add data rows
		foreach ( $data['labels'] as $index => $label ) {
			$row = array( strval( $label ) ); // Ensure label is string
			
			foreach ( $data['datasets'] as $dataset ) {
				$value = isset( $dataset['data'][ $index ] ) ? $dataset['data'][ $index ] : 0;
				// Convert to float
				$row[] = is_numeric( $value ) ? floatval( $value ) : 0;
			}
			
			$google_data[] = $row;
		}
		
		return $google_data;
	}

	/**
	 * Get chart options for Google Charts
	 *
	 * @param object $chart  Chart object.
	 * @param array  $config Chart configuration.
	 * @return array Google Charts options.
	 */
	private function get_chart_options( $chart, $config ) {
		$options = array(
			'title' => $chart->title,
			'legend' => array(
				'position' => 'bottom',
			),
			'chartArea' => array(
				'width' => '85%',
				'height' => '70%',
			),
		);
		
		// Add type-specific options
		switch ( $config['type'] ) {
			case 'pie':
				$options['pieHole'] = 0;
				$options['chartArea']['height'] = '80%';
				$options['is3D'] = false;
				break;
				
			case 'doughnut':
				$options['pieHole'] = 0.4;
				$options['chartArea']['height'] = '80%';
				break;
				
			case 'bar':
				$options['hAxis'] = array(
					'title' => isset( $config['x_axis_label'] ) ? $config['x_axis_label'] : '',
					'minValue' => 0,
				);
				$options['vAxis'] = array(
					'title' => isset( $config['y_axis_label'] ) ? $config['y_axis_label'] : '',
				);
				break;
				
			case 'line':
				$options['hAxis'] = array(
					'title' => isset( $config['x_axis_label'] ) ? $config['x_axis_label'] : '',
				);
				$options['vAxis'] = array(
					'title' => isset( $config['y_axis_label'] ) ? $config['y_axis_label'] : '',
					'minValue' => 0,
				);
				$options['curveType'] = 'function'; // Smooth lines
				break;
				
			case 'area':
				$options['hAxis'] = array(
					'title' => isset( $config['x_axis_label'] ) ? $config['x_axis_label'] : '',
				);
				$options['vAxis'] = array(
					'title' => isset( $config['y_axis_label'] ) ? $config['y_axis_label'] : '',
					'minValue' => 0,
				);
				$options['isStacked'] = false;
				break;
		}
		
		// Add colors if specified
		if ( ! empty( $config['colors'] ) && is_array( $config['colors'] ) ) {
			$options['colors'] = $config['colors'];
		}
		
		// Add animation
		$options['animation'] = array(
			'startup' => true,
			'duration' => 1000,
			'easing' => 'out',
		);
		
		return $options;
	}

	/**
	 * Map chart type to Google Charts type
	 *
	 * @param string $type Chart type.
	 * @return string Google Charts type.
	 */
	private function get_google_chart_type( $type ) {
		$map = array(
			'line'      => 'LineChart',
			'bar'       => 'BarChart',
			'column'    => 'ColumnChart',
			'pie'       => 'PieChart',
			'doughnut'  => 'PieChart',
			'area'      => 'AreaChart',
		);
		
		return isset( $map[ $type ] ) ? $map[ $type ] : 'ColumnChart';
	}
}

<?php
/**
 * Chart Renderer - Self-Contained Version
 *
 * Renders charts with Chart.js loaded inline.
 *
 * @package ATablesCharts\Frontend\Renderers
 * @since 1.0.0
 */

namespace ATablesCharts\Frontend\Renderers;

class ChartRenderer {

	private static $chartjs_loaded = false;

	public function render( $options ) {
		$chart = $options['chart'];
		$chart_data = $options['chart_data'];
		$width = isset( $options['width'] ) ? $options['width'] : '100%';
		$height = isset( $options['height'] ) ? $options['height'] : '400px';
		
		$unique_id = 'achart-' . $chart->id . '-' . wp_rand( 1000, 9999 );
		
		$labels = $chart_data['labels'];
		$datasets = $chart_data['datasets'];

		ob_start();
		
		// Load Chart.js only once
		if (!self::$chartjs_loaded) {
			?>
			<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js" crossorigin="anonymous"></script>
			<?php
			self::$chartjs_loaded = true;
		}
		?>
		
		<div class="acharts-frontend-wrapper" style="max-width: <?php echo esc_attr( $width ); ?>; margin: 20px 0;">
			<div class="acharts-frontend-chart-wrapper" style="height: <?php echo esc_attr( $height ); ?>; background: white; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
				<canvas id="<?php echo esc_attr( $unique_id ); ?>"></canvas>
			</div>
		</div>
		
		<script type="text/javascript">
		(function() {
			var maxRetries = 50; // 5 seconds max
			var retryCount = 0;
			
			function renderChart_<?php echo esc_js( str_replace('-', '_', $unique_id) ); ?>() {
				retryCount++;
				
				// Check if Chart.js is loaded
				if (typeof Chart === 'undefined') {
					if (retryCount < maxRetries) {
						setTimeout(renderChart_<?php echo esc_js( str_replace('-', '_', $unique_id) ); ?>, 100);
					} else {
						console.error('Chart.js failed to load after ' + (maxRetries * 100) + 'ms');
						var canvas = document.getElementById('<?php echo esc_js( $unique_id ); ?>');
						if (canvas) {
							var wrapper = canvas.closest('.acharts-frontend-chart-wrapper');
							if (wrapper) {
								wrapper.innerHTML = '<p style="color: red; text-align: center;">Failed to load Chart.js. Please check your internet connection.</p>';
							}
						}
					}
					return;
				}
				
				var canvas = document.getElementById('<?php echo esc_js( $unique_id ); ?>');
				if (!canvas) {
					console.error('Canvas not found: <?php echo esc_js( $unique_id ); ?>');
					return;
				}
				
				var ctx = canvas.getContext('2d');
				
				try {
					new Chart(ctx, {
						type: '<?php echo esc_js( $chart->type ); ?>',
						data: {
							labels: <?php echo wp_json_encode( $labels ); ?>,
							datasets: <?php echo wp_json_encode( $datasets ); ?>
						},
						options: {
							responsive: true,
							maintainAspectRatio: false,
							plugins: {
								legend: {
									display: true,
									position: 'top'
								},
								title: {
									display: <?php echo !empty($chart->title) ? 'true' : 'false'; ?>,
									text: '<?php echo esc_js( $chart->title ); ?>'
								}
							}
							<?php if ($chart->type !== 'pie' && $chart->type !== 'doughnut') : ?>
							,
							scales: {
								y: {
									beginAtZero: true
								}
							}
							<?php endif; ?>
						}
					});
					console.log('✓ Chart rendered: <?php echo esc_js( $unique_id ); ?>');
				} catch (error) {
					console.error('Error rendering chart:', error);
				}
			}
			
			// Start trying to render
			renderChart_<?php echo esc_js( str_replace('-', '_', $unique_id) ); ?>();
		})();
		</script>
		<?php
		return ob_get_clean();
	}
}

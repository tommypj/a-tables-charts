/**
 * A-Tables & Charts - Public Chart Renderer
 *
 * Handles chart rendering on the frontend.
 *
 * @package ATablesCharts
 * @since 1.0.0
 */

(function() {
	'use strict';

	/**
	 * Initialize charts when DOM is ready
	 */
	function initCharts() {
		// Wait for Chart.js to be available
		if (typeof Chart === 'undefined') {
			console.error('Chart.js is not loaded. Charts cannot be rendered.');
			return;
		}

		// Find all chart canvases
		const chartElements = document.querySelectorAll('canvas[data-chart]');
		
		if (chartElements.length === 0) {
			return;
		}

		// Render each chart
		chartElements.forEach(function(canvas) {
			renderChart(canvas);
		});
	}

	/**
	 * Render a single chart
	 */
	function renderChart(canvas) {
		try {
			// Get chart data from data attribute
			const chartDataStr = canvas.getAttribute('data-chart');
			if (!chartDataStr) {
				console.error('No chart data found for canvas:', canvas.id);
				return;
			}

			const chartData = JSON.parse(chartDataStr);
			
			// Validate chart data
			if (!chartData.config || !chartData.config.labels || !chartData.config.datasets) {
				console.error('Invalid chart data structure:', chartData);
				return;
			}

			// Get canvas context
			const ctx = canvas.getContext('2d');
			
			// Create the chart
			new Chart(ctx, {
				type: chartData.type || 'bar',
				data: {
					labels: chartData.config.labels,
					datasets: chartData.config.datasets
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
							display: chartData.title ? true : false,
							text: chartData.title || ''
						}
					},
					scales: getScalesConfig(chartData.type)
				}
			});

			console.log('Chart rendered successfully:', chartData.id);

		} catch (error) {
			console.error('Error rendering chart:', error);
		}
	}

	/**
	 * Get scales configuration based on chart type
	 */
	function getScalesConfig(chartType) {
		// Pie and Doughnut charts don't use scales
		if (chartType === 'pie' || chartType === 'doughnut') {
			return {};
		}

		// Bar and Line charts use scales
		return {
			y: {
				beginAtZero: true
			}
		};
	}

	/**
	 * Initialize when DOM is ready
	 */
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initCharts);
	} else {
		// DOM is already ready
		initCharts();
	}

	// Also try after window load (backup)
	window.addEventListener('load', function() {
		// Check if charts were already initialized
		const uninitializedCharts = document.querySelectorAll('canvas[data-chart]:not([data-chart-initialized])');
		if (uninitializedCharts.length > 0) {
			initCharts();
		}
	});

})();

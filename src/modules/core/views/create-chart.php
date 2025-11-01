<?php
/**
 * Create Chart Page
 *
 * Allows users to create charts from table data.
 *
 * @package ATablesCharts\Core
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load Tables module.
require_once ATABLES_PLUGIN_DIR . 'src/modules/tables/index.php';

$table_service = new \ATablesCharts\Tables\Services\TableService();
$tables_result = $table_service->get_all_tables( array(
	'status'   => 'active',
	'per_page' => 100,
) );

$tables = $tables_result['tables'];
?>

<div class="wrap atables-create-chart-page">
	<h1><?php esc_html_e( 'Create Chart', 'a-tables-charts' ); ?></h1>
	
	<div class="atables-chart-wizard">
		<!-- Step 1: Select Table -->
		<div class="atables-wizard-step atables-step-1 active">
			<h2><?php esc_html_e( 'Step 1: Select Table', 'a-tables-charts' ); ?></h2>
			<p><?php esc_html_e( 'Choose the table you want to create a chart from.', 'a-tables-charts' ); ?></p>
			
			<?php if ( ! empty( $tables ) ) : ?>
				<div class="atables-table-selector">
					<?php foreach ( $tables as $table ) : ?>
						<div class="atables-table-card" data-table-id="<?php echo esc_attr( $table->id ); ?>">
							<h3><?php echo esc_html( $table->title ); ?></h3>
							<p class="atables-table-stats">
								<span><?php echo esc_html( number_format( $table->row_count ) ); ?> rows</span>
								<span><?php echo esc_html( $table->column_count ); ?> columns</span>
							</p>
							<button class="button button-primary atables-select-table">
								<?php esc_html_e( 'Select', 'a-tables-charts' ); ?>
							</button>
						</div>
					<?php endforeach; ?>
				</div>
			<?php else : ?>
				<p><?php esc_html_e( 'No tables available. Please create a table first.', 'a-tables-charts' ); ?></p>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=a-tables-charts-create' ) ); ?>" class="button button-primary">
					<?php esc_html_e( 'Create Table', 'a-tables-charts' ); ?>
				</a>
			<?php endif; ?>
		</div>
		
		<!-- Step 2: Configure Chart -->
		<div class="atables-wizard-step atables-step-2">
			<h2><?php esc_html_e( 'Step 2: Configure Chart', 'a-tables-charts' ); ?></h2>
			
			<div class="atables-chart-config">
				<div class="atables-config-left">
					<div class="atables-form-group">
						<label for="chart-title"><?php esc_html_e( 'Chart Title', 'a-tables-charts' ); ?></label>
						<input type="text" id="chart-title" class="regular-text" placeholder="Enter chart title">
					</div>
					
					<div class="atables-form-group">
						<label for="chart-type"><?php esc_html_e( 'Chart Type', 'a-tables-charts' ); ?></label>
						<select id="chart-type">
							<option value="bar"><?php esc_html_e( 'Bar Chart', 'a-tables-charts' ); ?></option>
							<option value="line"><?php esc_html_e( 'Line Chart', 'a-tables-charts' ); ?></option>
							<option value="pie"><?php esc_html_e( 'Pie Chart', 'a-tables-charts' ); ?></option>
							<option value="doughnut"><?php esc_html_e( 'Doughnut Chart', 'a-tables-charts' ); ?></option>
							<option value="column"><?php esc_html_e( 'Column Chart', 'a-tables-charts' ); ?></option>
							<option value="area"><?php esc_html_e( 'Area Chart', 'a-tables-charts' ); ?></option>
							<option value="scatter"><?php esc_html_e( 'Scatter Chart', 'a-tables-charts' ); ?></option>
							<option value="radar"><?php esc_html_e( 'Radar Chart', 'a-tables-charts' ); ?></option>
						</select>
					</div>
					
					<div class="atables-form-group">
						<label for="label-column"><?php esc_html_e( 'Label Column (X-Axis)', 'a-tables-charts' ); ?></label>
						<select id="label-column">
							<option value=""><?php esc_html_e( 'Select column...', 'a-tables-charts' ); ?></option>
						</select>
					</div>
					
					<div class="atables-form-group">
						<label><?php esc_html_e( 'Data Columns (Y-Axis)', 'a-tables-charts' ); ?></label>
						<div id="data-columns-list"></div>
					</div>
					
					<div class="atables-form-actions">
						<button class="button atables-btn-back"><?php esc_html_e( 'Back', 'a-tables-charts' ); ?></button>
						<button class="button button-primary atables-btn-preview"><?php esc_html_e( 'Preview Chart', 'a-tables-charts' ); ?></button>
					</div>
				</div>
				
				<div class="atables-config-right">
					<h3><?php esc_html_e( 'Chart Preview', 'a-tables-charts' ); ?></h3>
					<div class="atables-chart-preview-container">
						<canvas id="chart-preview"></canvas>
					</div>
				</div>
			</div>
		</div>
		
		<!-- Step 3: Save Chart -->
		<div class="atables-wizard-step atables-step-3">
			<h2><?php esc_html_e( 'Step 3: Save Chart', 'a-tables-charts' ); ?></h2>
			
			<div class="atables-save-section">
				<div class="atables-final-preview">
					<canvas id="chart-final-preview"></canvas>
				</div>
				
				<div class="atables-form-actions">
					<button class="button atables-btn-back"><?php esc_html_e( 'Back', 'a-tables-charts' ); ?></button>
					<button class="button button-primary atables-btn-save"><?php esc_html_e( 'Save Chart', 'a-tables-charts' ); ?></button>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Load Chart.js from CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
jQuery(document).ready(function($) {
	let selectedTableId = null;
	let selectedTableHeaders = [];
	let chartInstance = null;
	let finalChartInstance = null;
	let currentChartConfig = null; // Store current configuration
	
	// Step 1: Select table
	$('.atables-select-table').on('click', function() {
		const $card = $(this).closest('.atables-table-card');
		selectedTableId = $card.data('table-id');
		
		// Load table columns
		loadTableColumns(selectedTableId);
		
		// Go to step 2
		gotoStep(2);
	});
	
	// Load table columns
	function loadTableColumns(tableId) {
		$.ajax({
			url: aTablesAdmin.ajaxUrl,
			type: 'POST',
			data: {
				action: 'atables_get_table',
				nonce: aTablesAdmin.nonce,
				table_id: tableId
			},
			success: function(response) {
				if (response.success && response.data) {
					const headers = response.data.source_data.headers || [];
					selectedTableHeaders = headers;
					
					// Populate label column dropdown
					$('#label-column').empty().append('<option value="">Select column...</option>');
					headers.forEach(function(header) {
						$('#label-column').append('<option value="' + header + '">' + header + '</option>');
					});
					
					// Populate data columns checkboxes
					$('#data-columns-list').empty();
					headers.forEach(function(header) {
						$('#data-columns-list').append(
							'<label><input type="checkbox" class="data-column-checkbox" value="' + header + '"> ' + header + '</label>'
						);
					});
				}
			}
		});
	}
	
	// Preview chart
	$('.atables-btn-preview').on('click', async function() {
		const title = $('#chart-title').val();
		const type = $('#chart-type').val();
		const labelColumn = $('#label-column').val();
		const dataColumns = $('.data-column-checkbox:checked').map(function() {
			return $(this).val();
		}).get();
		
		if (!title || !labelColumn || dataColumns.length === 0) {
			await ATablesModal.alert({
				title: 'Missing Information',
				message: 'Please fill in all required fields: chart title, label column, and at least one data column.',
				type: 'warning',
				icon: '⚠️'
			});
			return;
		}
		
		// Store configuration
		currentChartConfig = {
			title: title,
			type: type,
			labelColumn: labelColumn,
			dataColumns: dataColumns
		};
		
		// Generate preview and move to step 3
		generateChartPreview(type, title, labelColumn, dataColumns);
	});
	
	// Generate chart preview
	function generateChartPreview(type, title, labelColumn, dataColumns) {
		// Show loading state
		$('.atables-btn-preview').prop('disabled', true).text('Loading...');
		
		// Get table data for preview
		$.ajax({
			url: aTablesAdmin.ajaxUrl,
			type: 'POST',
			data: {
				action: 'atables_get_table',
				nonce: aTablesAdmin.nonce,
				table_id: selectedTableId
			},
			success: function(response) {
				if (response.success && response.data) {
					const tableData = response.data.source_data.data || [];
					const headers = response.data.source_data.headers || [];
					
					// Process data for chart
					const chartData = processChartData(tableData, headers, labelColumn, dataColumns);
					
					// Render chart in step 2 preview
					renderChart('chart-preview', type, title, chartData);
					
					// Render chart in step 3 final preview
					renderChart('chart-final-preview', type, title, chartData);
					
					// Move to step 3 after a short delay to show the preview
					setTimeout(function() {
						gotoStep(3);
						$('.atables-btn-preview').prop('disabled', false).text('Preview Chart');
					}, 500);
				}
			},
			error: async function() {
				await ATablesModal.error('Failed to load table data. Please try again.');
				$('.atables-btn-preview').prop('disabled', false).text('Preview Chart');
			}
		});
	}
	
	// Process chart data
	function processChartData(tableData, headers, labelColumn, dataColumns) {
		const labels = [];
		const datasets = [];
		
		// Get label column index
		const labelIndex = headers.indexOf(labelColumn);
		
		// Initialize datasets
		dataColumns.forEach(function(column, idx) {
			datasets.push({
				label: column,
				data: [],
				backgroundColor: getChartColor(idx),
				borderColor: getChartColor(idx),
				borderWidth: 2
			});
		});
		
		// Process rows
		tableData.forEach(function(row) {
			if (row[labelIndex]) {
				labels.push(row[labelIndex]);
			}
			
			dataColumns.forEach(function(column, idx) {
				const columnIndex = headers.indexOf(column);
				const value = parseFloat(row[columnIndex]) || 0;
				datasets[idx].data.push(value);
			});
		});
		
		return { labels, datasets };
	}
	
	// Render chart
	function renderChart(canvasId, type, title, data) {
		const ctx = document.getElementById(canvasId);
		
		// Destroy existing chart
		if (canvasId === 'chart-preview' && chartInstance) {
			chartInstance.destroy();
		}
		if (canvasId === 'chart-final-preview' && finalChartInstance) {
			finalChartInstance.destroy();
		}
		
		const config = {
			type: type,
			data: data,
			options: {
				responsive: true,
				maintainAspectRatio: true,
				plugins: {
					legend: {
						display: true,
						position: 'top'
					},
					title: {
						display: true,
						text: title
					}
				}
			}
		};
		
		const newChart = new Chart(ctx, config);
		
		if (canvasId === 'chart-preview') {
			chartInstance = newChart;
		} else {
			finalChartInstance = newChart;
		}
	}
	
	// Get chart colors
	function getChartColor(index) {
		const colors = [
			'rgba(54, 162, 235, 0.8)',
			'rgba(255, 99, 132, 0.8)',
			'rgba(255, 206, 86, 0.8)',
			'rgba(75, 192, 192, 0.8)',
			'rgba(153, 102, 255, 0.8)',
			'rgba(255, 159, 64, 0.8)'
		];
		return colors[index % colors.length];
	}
	
	// Save chart
	$('.atables-btn-save').on('click', async function() {
		const $btn = $(this);
		const originalText = $btn.text();
		$btn.prop('disabled', true).text('Saving...');
		
		// Use stored configuration
		if (!currentChartConfig) {
			await ATablesModal.error({
				title: 'Configuration Missing',
				message: 'Chart configuration is missing. Please go back and configure the chart again.'
			});
			$btn.prop('disabled', false).text(originalText);
			return;
		}
		
		$.ajax({
			url: aTablesAdmin.ajaxUrl,
			type: 'POST',
			data: {
				action: 'atables_create_chart',
				nonce: aTablesAdmin.nonce,
				table_id: selectedTableId,
				title: currentChartConfig.title,
				type: currentChartConfig.type,
				label_column: currentChartConfig.labelColumn,
				data_columns: currentChartConfig.dataColumns
			},
			success: async function(response) {
				if (response.success) {
					const viewCharts = await ATablesModal.confirm({
						title: 'Chart Created Successfully!',
						message: 'Your chart has been created. Would you like to view all charts now?',
						type: 'success',
						icon: '✅',
						confirmText: 'View All Charts',
						cancelText: 'Create Another Chart'
					});
					
					if (viewCharts) {
						window.location.href = 'admin.php?page=a-tables-charts-charts';
					} else {
						window.location.reload();
					}
				} else {
					await ATablesModal.error('Error: ' + (response.data || 'Unknown error'));
					$btn.prop('disabled', false).text(originalText);
				}
			},
			error: async function(xhr, status, error) {
				console.error('Save error:', xhr, status, error);
				await ATablesModal.error('Failed to save chart. Please try again.');
				$btn.prop('disabled', false).text(originalText);
			}
		});
	});
	
	// Navigation
	$('.atables-btn-back').on('click', function() {
		const currentStep = $('.atables-wizard-step.active').index() + 1;
		if (currentStep > 1) {
			gotoStep(currentStep - 1);
		}
	});
	
	function gotoStep(step) {
		$('.atables-wizard-step').removeClass('active');
		$('.atables-step-' + step).addClass('active');
	}
});
</script>

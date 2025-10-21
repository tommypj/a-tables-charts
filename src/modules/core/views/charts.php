<?php
/**
 * Charts Page
 *
 * Displays all charts.
 *
 * @package ATablesCharts\Core
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load Charts module.
require_once ATABLES_PLUGIN_DIR . 'src/modules/charts/index.php';

$chart_service = new \ATablesCharts\Charts\Services\ChartService();
$result = $chart_service->get_all_charts( array(
	'status'   => 'active',
	'per_page' => 20,
	'page'     => 1,
) );

$charts = $result['charts'];
$total_charts = $result['total'];
?>

<div class="wrap atables-charts-page">
	<h1><?php esc_html_e( 'Charts', 'a-tables-charts' ); ?></h1>
	
	<a href="<?php echo esc_url( admin_url( 'admin.php?page=a-tables-charts-create-chart' ) ); ?>" class="page-title-action">
		<?php esc_html_e( 'Create New Chart', 'a-tables-charts' ); ?>
	</a>
	
	<div class="atables-charts-grid">
		<?php if ( ! empty( $charts ) ) : ?>
			<?php foreach ( $charts as $chart ) : ?>
				<div class="atables-chart-card" data-chart-id="<?php echo esc_attr( $chart->id ); ?>" data-chart-title="<?php echo esc_attr( $chart->title ); ?>">
					<div class="atables-chart-preview">
						<canvas class="chart-canvas" id="chart-<?php echo esc_attr( $chart->id ); ?>"></canvas>
					</div>
					<div class="atables-chart-info">
						<h3><?php echo esc_html( $chart->title ); ?></h3>
						<p class="atables-chart-meta">
							<span class="atables-badge"><?php echo esc_html( strtoupper( $chart->type ) ); ?></span>
							<span><?php echo esc_html( human_time_diff( strtotime( $chart->created_at ) ) . ' ago' ); ?></span>
						</p>
						<div class="atables-chart-actions">
							<button class="button button-small atables-copy-chart-shortcode" data-chart-id="<?php echo esc_attr( $chart->id ); ?>" title="<?php esc_attr_e( 'Copy Shortcode', 'a-tables-charts' ); ?>">
								<?php esc_html_e( 'Shortcode', 'a-tables-charts' ); ?>
							</button>
							<button class="button button-small atables-delete-chart" data-chart-id="<?php echo esc_attr( $chart->id ); ?>">
								<?php esc_html_e( 'Delete', 'a-tables-charts' ); ?>
							</button>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		<?php else : ?>
			<div class="atables-empty-state">
				<div class="atables-empty-icon">📊</div>
				<h3><?php esc_html_e( 'No Charts Yet', 'a-tables-charts' ); ?></h3>
				<p><?php esc_html_e( 'Create your first chart to visualize your table data.', 'a-tables-charts' ); ?></p>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=a-tables-charts-create-chart' ) ); ?>" class="button button-primary button-large">
					<?php esc_html_e( 'Create Your First Chart', 'a-tables-charts' ); ?>
				</a>
			</div>
		<?php endif; ?>
	</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
jQuery(document).ready(function($) {
	// Render all charts
	<?php foreach ( $charts as $chart ) : ?>
	renderChart(<?php echo $chart->id; ?>);
	<?php endforeach; ?>
	
	function renderChart(chartId) {
		$.ajax({
			url: aTablesAdmin.ajaxUrl,
			type: 'POST',
			data: {
				action: 'atables_get_chart_data',
				nonce: aTablesAdmin.nonce,
				chart_id: chartId
			},
			success: function(response) {
				if (response.success && response.data) {
					const data = response.data;
					const ctx = document.getElementById('chart-' + chartId);
					
					new Chart(ctx, {
						type: data.type,
						data: {
							labels: data.labels,
							datasets: data.datasets.map((ds, idx) => ({
								...ds,
								backgroundColor: getChartColor(idx),
								borderColor: getChartColor(idx),
								borderWidth: 2
							}))
						},
						options: {
							responsive: true,
							maintainAspectRatio: true,
							plugins: {
								legend: { display: false },
								title: { display: false }
							}
						}
					});
				}
			}
		});
	}
	
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
	
	// Delete chart
	$('.atables-delete-chart').on('click', async function() {
		const chartId = $(this).data('chart-id');
		const $card = $(this).closest('.atables-chart-card');
		const chartTitle = $card.data('chart-title');
		
		const confirmed = await ATablesModal.confirm({
			title: 'Delete Chart?',
			message: `You are about to permanently delete the chart <strong>"${chartTitle}"</strong>. This action cannot be undone.`,
			type: 'danger',
			icon: '🗑️',
			confirmText: 'Delete Chart',
			cancelText: 'Cancel',
			confirmClass: 'danger',
			requireConfirmation: true,
			confirmationText: chartTitle,
			confirmationPlaceholder: 'Type chart name to confirm deletion...'
		});
		
		if (!confirmed) return;
		
		$.ajax({
			url: aTablesAdmin.ajaxUrl,
			type: 'POST',
			data: {
				action: 'atables_delete_chart',
				nonce: aTablesAdmin.nonce,
				chart_id: chartId
			},
			success: async function(response) {
				if (response.success) {
					$card.fadeOut(300, function() {
						$(this).remove();
					});
					await ATablesModal.success('Chart deleted successfully!');
				} else {
					await ATablesModal.error('Failed to delete chart. Please try again.');
				}
			},
			error: async function() {
				await ATablesModal.error('An error occurred while deleting the chart.');
			}
		});
	});
	
	// Copy chart shortcode
	$('.atables-copy-chart-shortcode').on('click', async function() {
		const chartId = $(this).data('chart-id');
		const shortcode = '[achart id="' + chartId + '"]';
		
		// Copy to clipboard
		if (navigator.clipboard && navigator.clipboard.writeText) {
			try {
				await navigator.clipboard.writeText(shortcode);
				await ATablesModal.success({
					title: 'Shortcode Copied!',
					message: `Chart shortcode copied to clipboard:<br><code style="background:#f6f7f7;padding:4px 8px;border-radius:4px;font-family:monospace;">${shortcode}</code>`
				});
			} catch (err) {
				fallbackCopyTextToClipboard(shortcode);
			}
		} else {
			fallbackCopyTextToClipboard(shortcode);
		}
	});
	
	async function fallbackCopyTextToClipboard(text) {
		const textArea = document.createElement('textarea');
		textArea.value = text;
		textArea.style.position = 'fixed';
		textArea.style.top = '0';
		textArea.style.left = '0';
		textArea.style.opacity = '0';
		document.body.appendChild(textArea);
		textArea.focus();
		textArea.select();
		
		try {
			const successful = document.execCommand('copy');
			if (successful) {
				await ATablesModal.success({
					title: 'Shortcode Copied!',
					message: `Chart shortcode copied:<br><code style="background:#f6f7f7;padding:4px 8px;border-radius:4px;font-family:monospace;">${text}</code>`
				});
			} else {
				await ATablesModal.alert({
					title: 'Copy Manually',
					message: `Please copy this shortcode manually:<br><code style="background:#f6f7f7;padding:8px 12px;border-radius:4px;font-family:monospace;display:block;margin-top:8px;">${text}</code>`,
					type: 'info',
					icon: '📋'
				});
			}
		} catch (err) {
			await ATablesModal.alert({
				title: 'Copy Manually',
				message: `Please copy this shortcode manually:<br><code style="background:#f6f7f7;padding:8px 12px;border-radius:4px;font-family:monospace;display:block;margin-top:8px;">${text}</code>`,
				type: 'info',
				icon: '📋'
			});
		}
		
		document.body.removeChild(textArea);
	}
});
</script>

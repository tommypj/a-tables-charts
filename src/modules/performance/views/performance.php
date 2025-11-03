<?php
/**
 * Performance Monitor Page
 *
 * Displays performance metrics and recommendations.
 *
 * @package ATablesCharts\Performance
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load Performance module.
require_once ATABLES_PLUGIN_DIR . 'src/modules/performance/index.php';

$monitor = new \ATablesCharts\Performance\Services\PerformanceMonitor();
$stats = $monitor->get_statistics( array( 'period' => 'day' ) );
$slow_operations = $monitor->get_slow_operations( 5 );
$recommendations = $monitor->get_recommendations();
?>

<div class="wrap atables-performance-page">
	<h1>
		<?php esc_html_e( 'Performance Monitor', 'a-tables-charts' ); ?>
		<button class="page-title-action atables-refresh-stats-btn">
			<span class="dashicons dashicons-update"></span>
			<?php esc_html_e( 'Refresh', 'a-tables-charts' ); ?>
		</button>
		<button class="page-title-action atables-clear-metrics-btn">
			<span class="dashicons dashicons-trash"></span>
			<?php esc_html_e( 'Clear Metrics', 'a-tables-charts' ); ?>
		</button>
	</h1>

	<p class="description">
		<?php esc_html_e( 'Monitor plugin performance and identify optimization opportunities.', 'a-tables-charts' ); ?>
	</p>

	<!-- Period Selector -->
	<div class="atables-period-selector">
		<label for="performance-period"><?php esc_html_e( 'Time Period:', 'a-tables-charts' ); ?></label>
		<select id="performance-period">
			<option value="hour"><?php esc_html_e( 'Last Hour', 'a-tables-charts' ); ?></option>
			<option value="day" selected><?php esc_html_e( 'Last 24 Hours', 'a-tables-charts' ); ?></option>
			<option value="week"><?php esc_html_e( 'Last Week', 'a-tables-charts' ); ?></option>
			<option value="month"><?php esc_html_e( 'Last Month', 'a-tables-charts' ); ?></option>
		</select>
	</div>

	<!-- Statistics Cards -->
	<div class="atables-stats-grid">
		<div class="atables-stat-card">
			<div class="atables-stat-icon">
				<span class="dashicons dashicons-clock"></span>
			</div>
			<div class="atables-stat-content">
				<div class="atables-stat-value" id="stat-avg-duration">
					<?php echo esc_html( number_format( $stats['avg_duration'], 2 ) ); ?> ms
				</div>
				<div class="atables-stat-label"><?php esc_html_e( 'Avg Response Time', 'a-tables-charts' ); ?></div>
			</div>
		</div>

		<div class="atables-stat-card">
			<div class="atables-stat-icon">
				<span class="dashicons dashicons-performance"></span>
			</div>
			<div class="atables-stat-content">
				<div class="atables-stat-value" id="stat-operations">
					<?php echo esc_html( number_format( $stats['count'] ) ); ?>
				</div>
				<div class="atables-stat-label"><?php esc_html_e( 'Total Operations', 'a-tables-charts' ); ?></div>
			</div>
		</div>

		<div class="atables-stat-card">
			<div class="atables-stat-icon">
				<span class="dashicons dashicons-warning"></span>
			</div>
			<div class="atables-stat-content">
				<div class="atables-stat-value atables-stat-warning" id="stat-slow-ops">
					<?php echo esc_html( $stats['slow_operations'] ); ?>
					<span class="atables-stat-small">(<?php echo esc_html( $stats['slow_percentage'] ); ?>%)</span>
				</div>
				<div class="atables-stat-label"><?php esc_html_e( 'Slow Operations', 'a-tables-charts' ); ?></div>
			</div>
		</div>

		<div class="atables-stat-card">
			<div class="atables-stat-icon">
				<span class="dashicons dashicons-database"></span>
			</div>
			<div class="atables-stat-content">
				<div class="atables-stat-value" id="stat-queries">
					<?php echo esc_html( number_format( $stats['avg_queries'], 1 ) ); ?>
				</div>
				<div class="atables-stat-label"><?php esc_html_e( 'Avg DB Queries', 'a-tables-charts' ); ?></div>
			</div>
		</div>
	</div>

	<!-- Recommendations -->
	<div class="atables-section">
		<h2><?php esc_html_e( 'Optimization Recommendations', 'a-tables-charts' ); ?></h2>
		<div id="recommendations-container">
			<?php if ( ! empty( $recommendations ) ) : ?>
				<?php foreach ( $recommendations as $rec ) : ?>
					<div class="atables-recommendation atables-recommendation-<?php echo esc_attr( $rec['severity'] ); ?>">
						<div class="atables-recommendation-icon">
							<?php if ( $rec['severity'] === 'error' ) : ?>
								<span class="dashicons dashicons-dismiss"></span>
							<?php elseif ( $rec['severity'] === 'warning' ) : ?>
								<span class="dashicons dashicons-warning"></span>
							<?php else : ?>
								<span class="dashicons dashicons-yes-alt"></span>
							<?php endif; ?>
						</div>
						<div class="atables-recommendation-content">
							<h3><?php echo esc_html( $rec['title'] ); ?></h3>
							<p><?php echo esc_html( $rec['description'] ); ?></p>
						</div>
					</div>
				<?php endforeach; ?>
			<?php else : ?>
				<div class="atables-empty-state">
					<p><?php esc_html_e( 'No recommendations available.', 'a-tables-charts' ); ?></p>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<!-- Slow Operations Table -->
	<div class="atables-section">
		<h2><?php esc_html_e( 'Slowest Operations', 'a-tables-charts' ); ?></h2>
		<div id="slow-operations-container">
			<?php if ( ! empty( $slow_operations ) ) : ?>
				<table class="wp-list-table widefat fixed striped">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Operation', 'a-tables-charts' ); ?></th>
							<th><?php esc_html_e( 'Duration', 'a-tables-charts' ); ?></th>
							<th><?php esc_html_e( 'Memory', 'a-tables-charts' ); ?></th>
							<th><?php esc_html_e( 'Queries', 'a-tables-charts' ); ?></th>
							<th><?php esc_html_e( 'Context', 'a-tables-charts' ); ?></th>
							<th><?php esc_html_e( 'Time', 'a-tables-charts' ); ?></th>
						</tr>
					</thead>
					<tbody id="slow-operations-tbody">
						<?php foreach ( $slow_operations as $op ) : ?>
							<tr>
								<td><strong><?php echo esc_html( $op['operation'] ); ?></strong></td>
								<td><span class="atables-duration-slow"><?php echo esc_html( number_format( $op['duration'], 2 ) ); ?> ms</span></td>
								<td><?php echo esc_html( size_format( $op['memory'], 2 ) ); ?></td>
								<td><?php echo esc_html( $op['queries'] ); ?></td>
								<td>
									<?php if ( ! empty( $op['context'] ) ) : ?>
										<code><?php echo esc_html( json_encode( $op['context'] ) ); ?></code>
									<?php else : ?>
										—
									<?php endif; ?>
								</td>
								<td><?php echo esc_html( human_time_diff( $op['timestamp'] ) . ' ago' ); ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php else : ?>
				<div class="atables-empty-state">
					<p><?php esc_html_e( 'No slow operations detected.', 'a-tables-charts' ); ?></p>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<!-- Operations by Type -->
	<div class="atables-section">
		<h2><?php esc_html_e( 'Operations by Type', 'a-tables-charts' ); ?></h2>
		<div id="operations-by-type-container">
			<table class="wp-list-table widefat fixed striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Operation Type', 'a-tables-charts' ); ?></th>
						<th><?php esc_html_e( 'Count', 'a-tables-charts' ); ?></th>
						<th><?php esc_html_e( 'Avg Duration', 'a-tables-charts' ); ?></th>
						<th><?php esc_html_e( 'Slow Count', 'a-tables-charts' ); ?></th>
						<th><?php esc_html_e( 'Slow %', 'a-tables-charts' ); ?></th>
					</tr>
				</thead>
				<tbody id="operations-by-type-tbody">
					<!-- Populated via JavaScript -->
				</tbody>
			</table>
		</div>
	</div>
</div>

<style>
.atables-performance-page {
	padding: 20px;
}

.atables-period-selector {
	margin: 20px 0;
}

.atables-period-selector label {
	margin-right: 10px;
	font-weight: 600;
}

.atables-stats-grid {
	display: grid;
	grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
	gap: 20px;
	margin: 20px 0;
}

.atables-stat-card {
	background: #fff;
	border: 1px solid #ddd;
	border-radius: 4px;
	padding: 20px;
	display: flex;
	align-items: center;
	gap: 15px;
}

.atables-stat-icon {
	font-size: 40px;
	color: #2271b1;
	display: flex;
	align-items: center;
}

.atables-stat-icon .dashicons {
	width: 40px;
	height: 40px;
	font-size: 40px;
}

.atables-stat-content {
	flex: 1;
}

.atables-stat-value {
	font-size: 32px;
	font-weight: bold;
	color: #2271b1;
	margin-bottom: 5px;
}

.atables-stat-value.atables-stat-warning {
	color: #d63638;
}

.atables-stat-small {
	font-size: 16px;
	color: #666;
}

.atables-stat-label {
	color: #666;
	font-size: 14px;
	font-weight: 600;
}

.atables-section {
	background: #fff;
	border: 1px solid #ddd;
	border-radius: 4px;
	padding: 20px;
	margin: 20px 0;
}

.atables-section h2 {
	margin-top: 0;
	border-bottom: 1px solid #ddd;
	padding-bottom: 10px;
}

.atables-recommendation {
	display: flex;
	align-items: flex-start;
	gap: 15px;
	padding: 15px;
	margin-bottom: 15px;
	border-radius: 4px;
	border-left: 4px solid;
}

.atables-recommendation-error {
	background: #fff0f0;
	border-left-color: #d63638;
}

.atables-recommendation-warning {
	background: #fff8e6;
	border-left-color: #dba617;
}

.atables-recommendation-success {
	background: #f0f9ff;
	border-left-color: #46b450;
}

.atables-recommendation-icon {
	font-size: 24px;
}

.atables-recommendation-icon .dashicons {
	width: 24px;
	height: 24px;
	font-size: 24px;
}

.atables-recommendation-error .dashicons {
	color: #d63638;
}

.atables-recommendation-warning .dashicons {
	color: #dba617;
}

.atables-recommendation-success .dashicons {
	color: #46b450;
}

.atables-recommendation-content h3 {
	margin: 0 0 5px 0;
	font-size: 14px;
	font-weight: 600;
}

.atables-recommendation-content p {
	margin: 0;
	color: #666;
}

.atables-duration-slow {
	color: #d63638;
	font-weight: 600;
}

.atables-empty-state {
	text-align: center;
	padding: 40px 20px;
	color: #666;
}
</style>

<script>
jQuery(document).ready(function($) {
	let currentPeriod = 'day';

	// Load initial data
	loadStats();

	// Period change
	$('#performance-period').on('change', function() {
		currentPeriod = $(this).val();
		loadStats();
	});

	// Refresh button
	$('.atables-refresh-stats-btn').on('click', function() {
		loadStats();
	});

	// Clear metrics button
	$('.atables-clear-metrics-btn').on('click', function() {
		if (!confirm('<?php esc_html_e( 'Are you sure you want to clear all performance metrics?', 'a-tables-charts' ); ?>')) {
			return;
		}

		const $btn = $(this);
		$btn.prop('disabled', true);

		$.ajax({
			url: aTablesAdmin.ajaxUrl,
			type: 'POST',
			data: {
				action: 'atables_clear_performance_metrics',
				nonce: aTablesAdmin.nonce
			},
			success: function(response) {
				if (response.success) {
					alert(response.data.message);
					location.reload();
				} else {
					alert(response.data.message);
				}
			},
			error: function() {
				alert('<?php esc_html_e( 'An error occurred', 'a-tables-charts' ); ?>');
			},
			complete: function() {
				$btn.prop('disabled', false);
			}
		});
	});

	function loadStats() {
		// Update stats
		$.ajax({
			url: aTablesAdmin.ajaxUrl,
			type: 'POST',
			data: {
				action: 'atables_get_performance_stats',
				nonce: aTablesAdmin.nonce,
				period: currentPeriod
			},
			success: function(response) {
				if (response.success) {
					updateStats(response.data.stats);
					updateOperationsByType(response.data.operations);
				}
			}
		});

		// Update slow operations
		$.ajax({
			url: aTablesAdmin.ajaxUrl,
			type: 'POST',
			data: {
				action: 'atables_get_slow_operations',
				nonce: aTablesAdmin.nonce,
				limit: 10
			},
			success: function(response) {
				if (response.success) {
					updateSlowOperations(response.data.operations);
				}
			}
		});

		// Update recommendations
		$.ajax({
			url: aTablesAdmin.ajaxUrl,
			type: 'POST',
			data: {
				action: 'atables_get_performance_recommendations',
				nonce: aTablesAdmin.nonce
			},
			success: function(response) {
				if (response.success) {
					updateRecommendations(response.data.recommendations);
				}
			}
		});
	}

	function updateStats(stats) {
		$('#stat-avg-duration').text(formatNumber(stats.avg_duration, 2) + ' ms');
		$('#stat-operations').text(formatNumber(stats.count, 0));
		$('#stat-slow-ops').html(
			stats.slow_operations +
			' <span class="atables-stat-small">(' + stats.slow_percentage + '%)</span>'
		);
		$('#stat-queries').text(formatNumber(stats.avg_queries, 1));
	}

	function updateOperationsByType(operations) {
		const $tbody = $('#operations-by-type-tbody');
		$tbody.empty();

		if (Object.keys(operations).length === 0) {
			$tbody.append('<tr><td colspan="5" style="text-align: center;">No operations recorded.</td></tr>');
			return;
		}

		Object.keys(operations).forEach(function(op) {
			const data = operations[op];
			const slowPercentage = ((data.slow_count / data.count) * 100).toFixed(1);

			$tbody.append(
				'<tr>' +
					'<td><strong>' + escapeHtml(op) + '</strong></td>' +
					'<td>' + formatNumber(data.count, 0) + '</td>' +
					'<td>' + formatNumber(data.avg_duration, 2) + ' ms</td>' +
					'<td>' + data.slow_count + '</td>' +
					'<td>' + slowPercentage + '%</td>' +
				'</tr>'
			);
		});
	}

	function updateSlowOperations(operations) {
		const $tbody = $('#slow-operations-tbody');
		$tbody.empty();

		if (operations.length === 0) {
			$tbody.append('<tr><td colspan="6" style="text-align: center;">No slow operations detected.</td></tr>');
			return;
		}

		operations.forEach(function(op) {
			const context = op.context && Object.keys(op.context).length > 0
				? '<code>' + escapeHtml(JSON.stringify(op.context)) + '</code>'
				: '—';

			const timeAgo = new Date(op.timestamp * 1000);

			$tbody.append(
				'<tr>' +
					'<td><strong>' + escapeHtml(op.operation) + '</strong></td>' +
					'<td><span class="atables-duration-slow">' + formatNumber(op.duration, 2) + ' ms</span></td>' +
					'<td>' + formatBytes(op.memory) + '</td>' +
					'<td>' + op.queries + '</td>' +
					'<td>' + context + '</td>' +
					'<td>' + formatTimeAgo(timeAgo) + '</td>' +
				'</tr>'
			);
		});
	}

	function updateRecommendations(recommendations) {
		const $container = $('#recommendations-container');
		$container.empty();

		if (recommendations.length === 0) {
			$container.append('<div class="atables-empty-state"><p>No recommendations available.</p></div>');
			return;
		}

		recommendations.forEach(function(rec) {
			const iconClass = rec.severity === 'error' ? 'dashicons-dismiss'
				: rec.severity === 'warning' ? 'dashicons-warning'
				: 'dashicons-yes-alt';

			$container.append(
				'<div class="atables-recommendation atables-recommendation-' + rec.severity + '">' +
					'<div class="atables-recommendation-icon">' +
						'<span class="dashicons ' + iconClass + '"></span>' +
					'</div>' +
					'<div class="atables-recommendation-content">' +
						'<h3>' + escapeHtml(rec.title) + '</h3>' +
						'<p>' + escapeHtml(rec.description) + '</p>' +
					'</div>' +
				'</div>'
			);
		});
	}

	function formatNumber(num, decimals) {
		return parseFloat(num).toFixed(decimals).replace(/\.?0+$/, '');
	}

	function formatBytes(bytes) {
		const units = ['B', 'KB', 'MB', 'GB'];
		let size = Math.max(bytes, 0);
		let pow = Math.floor((size ? Math.log(size) : 0) / Math.log(1024));
		pow = Math.min(pow, units.length - 1);
		size /= Math.pow(1024, pow);
		return size.toFixed(2) + ' ' + units[pow];
	}

	function formatTimeAgo(date) {
		const seconds = Math.floor((new Date() - date) / 1000);
		if (seconds < 60) return seconds + ' sec ago';
		const minutes = Math.floor(seconds / 60);
		if (minutes < 60) return minutes + ' min ago';
		const hours = Math.floor(minutes / 60);
		if (hours < 24) return hours + ' hr ago';
		const days = Math.floor(hours / 24);
		return days + ' day' + (days > 1 ? 's' : '') + ' ago';
	}

	function escapeHtml(text) {
		const map = {
			'&': '&amp;',
			'<': '&lt;',
			'>': '&gt;',
			'"': '&quot;',
			"'": '&#039;'
		};
		return text.replace(/[&<>"']/g, function(m) { return map[m]; });
	}
});
</script>

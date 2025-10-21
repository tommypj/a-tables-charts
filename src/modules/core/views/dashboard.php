<?php
/**
 * Dashboard View
 *
 * Main plugin dashboard showing tables and charts overview.
 *
 * @package ATablesCharts\Core
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load Tables module to get table data.
require_once ATABLES_PLUGIN_DIR . 'src/modules/tables/index.php';

// Get settings for default rows per page.
$settings = get_option( 'atables_settings', array() );
$rows_per_page = isset( $settings['rows_per_page'] ) ? (int) $settings['rows_per_page'] : 10;

$table_service = new \ATablesCharts\Tables\Services\TableService();
$result = $table_service->get_all_tables( array(
	'status'   => 'active',
	'per_page' => $rows_per_page,
	'page'     => 1,
) );

$tables = $result['tables'];
$total_tables = $result['total'];

// Load Charts module to get chart count.
require_once ATABLES_PLUGIN_DIR . 'src/modules/charts/index.php';

$chart_service = new \ATablesCharts\Charts\Services\ChartService();
$charts_result = $chart_service->get_all_charts( array(
	'status'   => 'active',
	'per_page' => 1,
	'page'     => 1,
) );

$total_charts = $charts_result['total'];
?>

<div class="wrap atables-dashboard">
	<h1><?php esc_html_e( 'a-tables-charts', 'a-tables-charts' ); ?></h1>
	
	<a href="<?php echo esc_url( admin_url( 'admin.php?page=a-tables-charts-create' ) ); ?>" class="page-title-action">
		<?php esc_html_e( 'Create New Table', 'a-tables-charts' ); ?>
	</a>
	<a href="<?php echo esc_url( admin_url( 'admin.php?page=a-tables-charts-create-chart' ) ); ?>" class="page-title-action">
		<?php esc_html_e( 'Create New Chart', 'a-tables-charts' ); ?>
	</a>
	
	<!-- Stats Cards -->
	<div class="atables-stats-grid">
		<div class="atables-stat-card">
			<h2><?php echo esc_html( $total_tables ); ?></h2>
			<p><?php esc_html_e( 'Total Tables', 'a-tables-charts' ); ?></p>
		</div>
		
		<div class="atables-stat-card">
			<h2><?php echo esc_html( $total_charts ); ?></h2>
			<p><?php esc_html_e( 'Total Charts', 'a-tables-charts' ); ?></p>
		</div>
		
		<div class="atables-stat-card">
			<h2><?php echo esc_html( ATABLES_VERSION ); ?></h2>
			<p><?php esc_html_e( 'Plugin Version', 'a-tables-charts' ); ?></p>
		</div>
	</div>
	
	<!-- Recent Tables -->
	<div class="atables-recent-section">
		<h2><?php esc_html_e( 'Recent Tables', 'a-tables-charts' ); ?></h2>
		
		<?php if ( ! empty( $tables ) ) : ?>
			<div class="atables-bulk-actions">
				<select id="atables-bulk-action">
					<option value=""><?php esc_html_e( 'Bulk Actions', 'a-tables-charts' ); ?></option>
					<option value="delete"><?php esc_html_e( 'Delete', 'a-tables-charts' ); ?></option>
				</select>
				<button type="button" class="button" id="atables-apply-bulk"><?php esc_html_e( 'Apply', 'a-tables-charts' ); ?></button>
			</div>
			
			<div class="atables-table-list">
				<table class="wp-list-table widefat fixed striped">
					<thead>
						<tr>
							<th class="check-column"><input type="checkbox" id="atables-select-all"></th>
							<th><?php esc_html_e( 'Title', 'a-tables-charts' ); ?></th>
							<th><?php esc_html_e( 'Source', 'a-tables-charts' ); ?></th>
							<th><?php esc_html_e( 'Rows', 'a-tables-charts' ); ?></th>
							<th><?php esc_html_e( 'Columns', 'a-tables-charts' ); ?></th>
							<th><?php esc_html_e( 'Created', 'a-tables-charts' ); ?></th>
							<th><?php esc_html_e( 'Actions', 'a-tables-charts' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $tables as $table ) : ?>
							<tr>
								<th class="check-column"><input type="checkbox" class="atables-table-checkbox" value="<?php echo esc_attr( $table->id ); ?>"></th>
								<td>
									<strong><?php echo esc_html( $table->title ); ?></strong>
									<?php if ( ! empty( $table->description ) ) : ?>
										<br><small><?php echo esc_html( $table->description ); ?></small>
									<?php endif; ?>
								</td>
								<td>
									<span class="atables-badge"><?php echo esc_html( strtoupper( $table->source_type ) ); ?></span>
								</td>
								<td><?php echo esc_html( number_format( $table->row_count ) ); ?></td>
								<td><?php echo esc_html( $table->column_count ); ?></td>
								<td><?php echo esc_html( human_time_diff( strtotime( $table->created_at ) ) . ' ago' ); ?></td>
								<td>
								<a href="<?php echo esc_url( admin_url( 'admin.php?page=a-tables-charts-view&table_id=' . $table->id ) ); ?>" class="button button-small">
								<?php esc_html_e( 'View', 'a-tables-charts' ); ?>
								</a>
								<a href="<?php echo esc_url( admin_url( 'admin.php?page=a-tables-charts-edit&table_id=' . $table->id ) ); ?>" class="button button-small">
								<?php esc_html_e( 'Edit', 'a-tables-charts' ); ?>
								</a>
								<button class="button button-small atables-duplicate-btn" data-table-id="<?php echo esc_attr( $table->id ); ?>" data-table-title="<?php echo esc_attr( $table->title ); ?>" title="<?php esc_attr_e( 'Duplicate Table', 'a-tables-charts' ); ?>">
								<?php esc_html_e( 'Duplicate', 'a-tables-charts' ); ?>
								</button>
								<button class="button button-small atables-copy-shortcode" data-table-id="<?php echo esc_attr( $table->id ); ?>" title="<?php esc_attr_e( 'Copy Shortcode', 'a-tables-charts' ); ?>">
								<?php esc_html_e( 'Shortcode', 'a-tables-charts' ); ?>
								</button>
								<button class="button button-small atables-delete-btn" data-table-id="<?php echo esc_attr( $table->id ); ?>" data-table-title="<?php echo esc_attr( $table->title ); ?>">
									<?php esc_html_e( 'Delete', 'a-tables-charts' ); ?>
								</button>
							</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		<?php else : ?>
			<div class="atables-empty-state">
				<div class="atables-empty-icon">📊</div>
				<h3><?php esc_html_e( 'No Tables Yet', 'a-tables-charts' ); ?></h3>
				<p><?php esc_html_e( 'Create your first table to get started with A-Tables & Charts.', 'a-tables-charts' ); ?></p>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=a-tables-charts-create' ) ); ?>" class="button button-primary button-large">
					<?php esc_html_e( 'Create Your First Table', 'a-tables-charts' ); ?>
				</a>
			</div>
		<?php endif; ?>
	</div>
	
	<!-- Getting Started Guide -->
	<div class="atables-getting-started">
		<h2><?php esc_html_e( 'Getting Started', 'a-tables-charts' ); ?></h2>
		
		<div class="atables-guide-grid">
			<div class="atables-guide-card">
				<h3>1. <?php esc_html_e( 'Create a Table', 'a-tables-charts' ); ?></h3>
				<p><?php esc_html_e( 'Import data from Excel, CSV, JSON, XML, or create a manual table.', 'a-tables-charts' ); ?></p>
			</div>
			
			<div class="atables-guide-card">
				<h3>2. <?php esc_html_e( 'Configure Display', 'a-tables-charts' ); ?></h3>
				<p><?php esc_html_e( 'Set up columns, filters, sorting, and pagination options.', 'a-tables-charts' ); ?></p>
			</div>
		</div>
	</div>
</div>

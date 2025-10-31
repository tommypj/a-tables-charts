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
$default_per_page = isset( $settings['rows_per_page'] ) ? (int) $settings['rows_per_page'] : 10;

// Get filter parameters from URL.
$current_page = isset( $_GET['paged'] ) ? max( 1, (int) $_GET['paged'] ) : 1;
$per_page = isset( $_GET['per_page'] ) ? (int) $_GET['per_page'] : $default_per_page;
$search = isset( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : '';
$source_filter = isset( $_GET['source'] ) ? sanitize_text_field( $_GET['source'] ) : '';
$sort_by = isset( $_GET['sort'] ) ? sanitize_text_field( $_GET['sort'] ) : 'created_at';
$sort_order = isset( $_GET['order'] ) && in_array( strtolower( $_GET['order'] ), array( 'asc', 'desc' ), true ) ? strtolower( $_GET['order'] ) : 'desc';

// Validate per_page values.
$allowed_per_page = array( 10, 25, 50, 100 );
if ( ! in_array( $per_page, $allowed_per_page, true ) ) {
	$per_page = $default_per_page;
}

// Build query parameters.
$query_params = array(
	'status'   => 'active',
	'per_page' => $per_page,
	'page'     => $current_page,
	'sort_by'  => $sort_by,
	'order'    => $sort_order,
);

// Add search if provided.
if ( ! empty( $search ) ) {
	$query_params['search'] = $search;
}

// Add source filter if provided.
if ( ! empty( $source_filter ) ) {
	$query_params['source_type'] = $source_filter;
}

$table_service = new \ATablesCharts\Tables\Services\TableService();
$result = $table_service->get_all_tables( $query_params );

$tables = $result['tables'];
$total_tables = $result['total'];
$total_pages = ceil( $total_tables / $per_page );

// Helper function to generate URL with current parameters.
function atables_get_dashboard_url( $new_params = array() ) {
	$current_params = array(
		'page'     => 'a-tables-charts',
		'per_page' => $_GET['per_page'] ?? 10,
		'paged'    => $_GET['paged'] ?? 1,
		's'        => $_GET['s'] ?? '',
		'source'   => $_GET['source'] ?? '',
		'sort'     => $_GET['sort'] ?? 'created_at',
		'order'    => $_GET['order'] ?? 'desc',
	);
	
	$params = array_merge( $current_params, $new_params );
	
	// Remove empty parameters.
	$params = array_filter( $params, function( $value ) {
		return $value !== '' && $value !== null;
	} );
	
	return add_query_arg( $params, admin_url( 'admin.php' ) );
}

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
		<div class="atables-section-header">
			<h2><?php esc_html_e( 'All Tables', 'a-tables-charts' ); ?></h2>
		</div>
		
		<!-- Filter Controls Bar -->
		<div class="atables-filters-bar">
			<div class="atables-filters-row">
				<!-- Search Box -->
				<div class="atables-filter-group atables-search-group">
					<label for="atables-search-input" class="atables-filter-label">
						<span class="dashicons dashicons-search"></span>
						<?php esc_html_e( 'Search', 'a-tables-charts' ); ?>
					</label>
					<div class="atables-search-wrapper">
						<input type="search" 
							   id="atables-search-input" 
							   class="atables-input" 
							   placeholder="<?php esc_attr_e( 'Search tables...', 'a-tables-charts' ); ?>" 
							   value="<?php echo esc_attr( $search ); ?>">
						<button type="button" id="atables-search-btn" class="button button-primary">
							<?php esc_html_e( 'Search', 'a-tables-charts' ); ?>
						</button>
					</div>
				</div>
				
				<!-- Source Filter -->
				<div class="atables-filter-group">
					<label for="atables-source-filter" class="atables-filter-label">
						<span class="dashicons dashicons-filter"></span>
						<?php esc_html_e( 'Source Type', 'a-tables-charts' ); ?>
					</label>
					<select id="atables-source-filter" class="atables-select">
						<option value="" <?php selected( $source_filter, '' ); ?>><?php esc_html_e( 'All Sources', 'a-tables-charts' ); ?></option>
						<option value="CSV" <?php selected( $source_filter, 'CSV' ); ?>><?php esc_html_e( 'CSV', 'a-tables-charts' ); ?></option>
						<option value="JSON" <?php selected( $source_filter, 'JSON' ); ?>><?php esc_html_e( 'JSON', 'a-tables-charts' ); ?></option>
						<option value="EXCEL" <?php selected( $source_filter, 'EXCEL' ); ?>><?php esc_html_e( 'Excel', 'a-tables-charts' ); ?></option>
						<option value="XML" <?php selected( $source_filter, 'XML' ); ?>><?php esc_html_e( 'XML', 'a-tables-charts' ); ?></option>
						<option value="MANUAL" <?php selected( $source_filter, 'MANUAL' ); ?>><?php esc_html_e( 'Manual', 'a-tables-charts' ); ?></option>
						<option value="MYSQL" <?php selected( $source_filter, 'MYSQL' ); ?>><?php esc_html_e( 'MySQL', 'a-tables-charts' ); ?></option>
						<option value="GOOGLE_SHEETS" <?php selected( $source_filter, 'GOOGLE_SHEETS' ); ?>><?php esc_html_e( 'Google Sheets', 'a-tables-charts' ); ?></option>
					</select>
				</div>
				
				<!-- Rows Per Page -->
				<div class="atables-filter-group">
					<label for="atables-per-page-select" class="atables-filter-label">
						<span class="dashicons dashicons-list-view"></span>
						<?php esc_html_e( 'Show', 'a-tables-charts' ); ?>
					</label>
					<select id="atables-per-page-select" class="atables-select">
						<option value="10" <?php selected( $per_page, 10 ); ?>>10</option>
						<option value="25" <?php selected( $per_page, 25 ); ?>>25</option>
						<option value="50" <?php selected( $per_page, 50 ); ?>>50</option>
						<option value="100" <?php selected( $per_page, 100 ); ?>>100</option>
					</select>
				</div>
				
				<!-- Reset Filters Button -->
				<?php if ( ! empty( $search ) || ! empty( $source_filter ) || $per_page != $default_per_page || $sort_by != 'created_at' || $sort_order != 'desc' ) : ?>
					<div class="atables-filter-group atables-reset-group">
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=a-tables-charts' ) ); ?>" 
						   class="button atables-reset-btn" 
						   title="<?php esc_attr_e( 'Clear all filters and reset to defaults', 'a-tables-charts' ); ?>">
							<span class="dashicons dashicons-image-rotate"></span>
							<?php esc_html_e( 'Reset Filters', 'a-tables-charts' ); ?>
						</a>
					</div>
				<?php endif; ?>
			</div>
		</div>
		
		<!-- Results Info -->
		<?php if ( ! empty( $search ) || ! empty( $source_filter ) ) : ?>
			<div class="atables-results-info">
				<p>
					<?php
					if ( $total_tables > 0 ) {
						printf(
							/* translators: %s: number of tables found */
							esc_html( _n( 'Found %s table', 'Found %s tables', $total_tables, 'a-tables-charts' ) ),
							'<strong>' . esc_html( number_format( $total_tables ) ) . '</strong>'
						);
					} else {
						esc_html_e( 'No tables found matching your criteria.', 'a-tables-charts' );
					}
					?>
				</p>
			</div>
		<?php endif; ?>
		
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
							<th class="atables-sortable <?php echo ( $sort_by === 'title' ) ? 'sorted sorted-' . esc_attr( $sort_order ) : ''; ?>">
								<a href="<?php echo esc_url( atables_get_dashboard_url( array( 'sort' => 'title', 'order' => ( $sort_by === 'title' && $sort_order === 'asc' ) ? 'desc' : 'asc', 'paged' => 1 ) ) ); ?>">
									<?php esc_html_e( 'Title', 'a-tables-charts' ); ?>
									<?php if ( $sort_by === 'title' ) : ?>
										<span class="dashicons dashicons-arrow-<?php echo $sort_order === 'asc' ? 'up' : 'down'; ?>-alt2"></span>
									<?php else : ?>
										<span class="dashicons dashicons-sort"></span>
									<?php endif; ?>
								</a>
							</th>
							<th><?php esc_html_e( 'Source', 'a-tables-charts' ); ?></th>
							<th class="atables-sortable <?php echo ( $sort_by === 'row_count' ) ? 'sorted sorted-' . esc_attr( $sort_order ) : ''; ?>">
								<a href="<?php echo esc_url( atables_get_dashboard_url( array( 'sort' => 'row_count', 'order' => ( $sort_by === 'row_count' && $sort_order === 'asc' ) ? 'desc' : 'asc', 'paged' => 1 ) ) ); ?>">
									<?php esc_html_e( 'Rows', 'a-tables-charts' ); ?>
									<?php if ( $sort_by === 'row_count' ) : ?>
										<span class="dashicons dashicons-arrow-<?php echo $sort_order === 'asc' ? 'up' : 'down'; ?>-alt2"></span>
									<?php else : ?>
										<span class="dashicons dashicons-sort"></span>
									<?php endif; ?>
								</a>
							</th>
							<th class="atables-sortable <?php echo ( $sort_by === 'column_count' ) ? 'sorted sorted-' . esc_attr( $sort_order ) : ''; ?>">
								<a href="<?php echo esc_url( atables_get_dashboard_url( array( 'sort' => 'column_count', 'order' => ( $sort_by === 'column_count' && $sort_order === 'asc' ) ? 'desc' : 'asc', 'paged' => 1 ) ) ); ?>">
									<?php esc_html_e( 'Columns', 'a-tables-charts' ); ?>
									<?php if ( $sort_by === 'column_count' ) : ?>
										<span class="dashicons dashicons-arrow-<?php echo $sort_order === 'asc' ? 'up' : 'down'; ?>-alt2"></span>
									<?php else : ?>
										<span class="dashicons dashicons-sort"></span>
									<?php endif; ?>
								</a>
							</th>
							<th class="atables-sortable <?php echo ( $sort_by === 'created_at' ) ? 'sorted sorted-' . esc_attr( $sort_order ) : ''; ?>">
								<a href="<?php echo esc_url( atables_get_dashboard_url( array( 'sort' => 'created_at', 'order' => ( $sort_by === 'created_at' && $sort_order === 'asc' ) ? 'desc' : 'asc', 'paged' => 1 ) ) ); ?>">
									<?php esc_html_e( 'Created', 'a-tables-charts' ); ?>
									<?php if ( $sort_by === 'created_at' ) : ?>
										<span class="dashicons dashicons-arrow-<?php echo $sort_order === 'asc' ? 'up' : 'down'; ?>-alt2"></span>
									<?php else : ?>
										<span class="dashicons dashicons-sort"></span>
									<?php endif; ?>
								</a>
							</th>
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
			
			<!-- Pagination Controls -->
			<?php if ( $total_pages > 1 ) : ?>
				<div class="atables-pagination">
					<div class="atables-pagination-info">
						<p>
							<?php
							$start_row = ( ( $current_page - 1 ) * $per_page ) + 1;
							$end_row = min( $current_page * $per_page, $total_tables );
							printf(
								/* translators: 1: start row, 2: end row, 3: total tables */
								esc_html__( 'Showing %1$s to %2$s of %3$s tables', 'a-tables-charts' ),
								'<strong>' . esc_html( number_format( $start_row ) ) . '</strong>',
								'<strong>' . esc_html( number_format( $end_row ) ) . '</strong>',
								'<strong>' . esc_html( number_format( $total_tables ) ) . '</strong>'
							);
							?>
						</p>
					</div>
					
					<div class="atables-pagination-controls">
						<!-- First Page -->
						<?php if ( $current_page > 1 ) : ?>
							<a href="<?php echo esc_url( atables_get_dashboard_url( array( 'paged' => 1 ) ) ); ?>" 
							   class="button" 
							   title="<?php esc_attr_e( 'First Page', 'a-tables-charts' ); ?>">
								<span class="dashicons dashicons-controls-skipback"></span>
							</a>
							<a href="<?php echo esc_url( atables_get_dashboard_url( array( 'paged' => max( 1, $current_page - 1 ) ) ) ); ?>" 
							   class="button" 
							   title="<?php esc_attr_e( 'Previous Page', 'a-tables-charts' ); ?>">
								<span class="dashicons dashicons-arrow-left-alt2"></span>
							</a>
						<?php else : ?>
							<button class="button" disabled>
								<span class="dashicons dashicons-controls-skipback"></span>
							</button>
							<button class="button" disabled>
								<span class="dashicons dashicons-arrow-left-alt2"></span>
							</button>
						<?php endif; ?>
						
						<!-- Page Numbers -->
						<span class="atables-page-numbers">
							<?php
							// Calculate page range to display.
							$range = 2; // Show 2 pages on each side of current page.
							$start = max( 1, $current_page - $range );
							$end = min( $total_pages, $current_page + $range );
							
							// Show first page and ellipsis if needed.
							if ( $start > 1 ) {
								?>
								<a href="<?php echo esc_url( atables_get_dashboard_url( array( 'paged' => 1 ) ) ); ?>" 
								   class="button">1</a>
								<?php if ( $start > 2 ) : ?>
									<span class="atables-page-ellipsis">...</span>
								<?php endif; ?>
								<?php
							}
							
							// Show page numbers in range.
							for ( $i = $start; $i <= $end; $i++ ) {
								if ( $i === $current_page ) {
									?>
									<button class="button button-primary" disabled><?php echo esc_html( $i ); ?></button>
									<?php
								} else {
									?>
									<a href="<?php echo esc_url( atables_get_dashboard_url( array( 'paged' => $i ) ) ); ?>" 
									   class="button"><?php echo esc_html( $i ); ?></a>
									<?php
								}
							}
							
							// Show last page and ellipsis if needed.
							if ( $end < $total_pages ) {
								if ( $end < $total_pages - 1 ) {
									?>
									<span class="atables-page-ellipsis">...</span>
									<?php
								}
								?>
								<a href="<?php echo esc_url( atables_get_dashboard_url( array( 'paged' => $total_pages ) ) ); ?>" 
								   class="button"><?php echo esc_html( $total_pages ); ?></a>
								<?php
							}
							?>
						</span>
						
						<!-- Next/Last Page -->
						<?php if ( $current_page < $total_pages ) : ?>
							<a href="<?php echo esc_url( atables_get_dashboard_url( array( 'paged' => min( $total_pages, $current_page + 1 ) ) ) ); ?>" 
							   class="button" 
							   title="<?php esc_attr_e( 'Next Page', 'a-tables-charts' ); ?>">
								<span class="dashicons dashicons-arrow-right-alt2"></span>
							</a>
							<a href="<?php echo esc_url( atables_get_dashboard_url( array( 'paged' => $total_pages ) ) ); ?>" 
							   class="button" 
							   title="<?php esc_attr_e( 'Last Page', 'a-tables-charts' ); ?>">
								<span class="dashicons dashicons-controls-skipforward"></span>
							</a>
						<?php else : ?>
							<button class="button" disabled>
								<span class="dashicons dashicons-arrow-right-alt2"></span>
							</button>
							<button class="button" disabled>
								<span class="dashicons dashicons-controls-skipforward"></span>
							</button>
						<?php endif; ?>
					</div>
				</div>
			<?php endif; ?>
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

<style>
/* ============================================
   FILTER BAR STYLES
   ============================================ */

.atables-filters-bar {
	background: #fff;
	border: 1px solid #dcdcde;
	border-radius: 4px;
	padding: 20px;
	margin-bottom: 20px;
	box-shadow: 0 1px 1px rgba(0,0,0,.04);
}

.atables-filters-row {
	display: flex;
	flex-wrap: wrap;
	gap: 20px;
	align-items: flex-end;
}

.atables-filter-group {
	display: flex;
	flex-direction: column;
	gap: 8px;
	min-width: 0;
}

.atables-filter-group.atables-search-group {
	flex: 1;
	min-width: 300px;
}

.atables-filter-label {
	display: flex;
	align-items: center;
	gap: 6px;
	font-weight: 600;
	font-size: 13px;
	color: #1d2327;
	margin: 0;
}

.atables-filter-label .dashicons {
	font-size: 16px;
	width: 16px;
	height: 16px;
	color: #2271b1;
}

.atables-search-wrapper {
	display: flex;
	gap: 8px;
}

.atables-input {
	flex: 1;
	height: 36px;
	padding: 0 12px;
	border: 1px solid #8c8f94;
	border-radius: 4px;
	font-size: 14px;
	line-height: 2;
	min-width: 0;
	transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.atables-input:focus {
	border-color: #2271b1;
	box-shadow: 0 0 0 1px #2271b1;
	outline: 2px solid transparent;
}

.atables-select {
	height: 36px;
	padding: 0 32px 0 12px;
	border: 1px solid #8c8f94;
	border-radius: 4px;
	font-size: 14px;
	line-height: 2;
	background: #fff url(data:image/svg+xml;charset=US-ASCII,%3Csvg%20width%3D%2220%22%20height%3D%2220%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cpath%20d%3D%22M5%206l5%205%205-5%202%201-7%207-7-7%202-1z%22%20fill%3D%22%23555%22%2F%3E%3C%2Fsvg%3E) no-repeat right 8px top 50%;
	background-size: 16px 16px;
	appearance: none;
	-webkit-appearance: none;
	-moz-appearance: none;
	cursor: pointer;
	min-width: 180px;
	transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.atables-select:focus {
	border-color: #2271b1;
	box-shadow: 0 0 0 1px #2271b1;
	outline: 2px solid transparent;
}

.atables-reset-group {
	margin-left: auto;
}

.atables-reset-btn {
	height: 36px !important;
	line-height: 34px !important;
	padding: 0 16px !important;
	background: #f6f7f7 !important;
	border-color: #dcdcde !important;
	color: #2c3338 !important;
	font-weight: 500 !important;
	display: inline-flex !important;
	align-items: center !important;
	gap: 6px !important;
	transition: all 0.15s ease-in-out !important;
}

.atables-reset-btn:hover {
	background: #f0f0f1 !important;
	border-color: #8c8f94 !important;
	color: #1d2327 !important;
}

.atables-reset-btn .dashicons {
	font-size: 18px;
	width: 18px;
	height: 18px;
}

#atables-search-btn {
	height: 36px !important;
	line-height: 34px !important;
	padding: 0 20px !important;
	white-space: nowrap;
}

/* Results Info */
.atables-results-info {
	background: #e7f5fe;
	border-left: 4px solid #2271b1;
	padding: 12px 16px;
	margin-bottom: 16px;
	border-radius: 0 4px 4px 0;
}

.atables-results-info p {
	margin: 0;
	font-size: 14px;
	color: #1d2327;
}

.atables-results-info strong {
	color: #2271b1;
	font-weight: 600;
}

/* Responsive Design */
@media screen and (max-width: 1200px) {
	.atables-filters-row {
		gap: 16px;
	}
	
	.atables-filter-group.atables-search-group {
		min-width: 250px;
	}
	
	.atables-select {
		min-width: 150px;
	}
}

@media screen and (max-width: 782px) {
	.atables-filters-bar {
		padding: 16px;
	}
	
	.atables-filters-row {
		flex-direction: column;
		gap: 16px;
	}
	
	.atables-filter-group,
	.atables-filter-group.atables-search-group {
		width: 100%;
		min-width: 0;
	}
	
	.atables-reset-group {
		margin-left: 0;
		width: 100%;
	}
	
	.atables-reset-btn {
		width: 100%;
		justify-content: center;
	}
	
	.atables-search-wrapper {
		flex-direction: column;
	}
	
	#atables-search-btn {
		width: 100%;
	}
	
	.atables-select {
		width: 100%;
		min-width: 0;
	}
}

/* Section Header */
.atables-section-header {
	margin-bottom: 20px;
}

.atables-section-header h2 {
	margin: 0;
	font-size: 23px;
	font-weight: 400;
	line-height: 1.3;
}
</style>

<script>
jQuery(document).ready(function($) {
	// Handle search button click
	$('#atables-search-btn').on('click', function() {
		var searchTerm = $('#atables-search-input').val();
		var currentUrl = new URL(window.location.href);
		currentUrl.searchParams.set('s', searchTerm);
		currentUrl.searchParams.set('paged', 1); // Reset to first page
		window.location.href = currentUrl.toString();
	});
	
	// Handle Enter key in search box
	$('#atables-search-input').on('keypress', function(e) {
		if (e.which === 13) {
			e.preventDefault();
			$('#atables-search-btn').click();
		}
	});
	
	// Handle source filter change
	$('#atables-source-filter').on('change', function() {
		var sourceType = $(this).val();
		var currentUrl = new URL(window.location.href);
		if (sourceType) {
			currentUrl.searchParams.set('source', sourceType);
		} else {
			currentUrl.searchParams.delete('source');
		}
		currentUrl.searchParams.set('paged', 1); // Reset to first page
		window.location.href = currentUrl.toString();
	});
	
	// Handle per-page change
	$('#atables-per-page-select').on('change', function() {
		var perPage = $(this).val();
		var currentUrl = new URL(window.location.href);
		currentUrl.searchParams.set('per_page', perPage);
		currentUrl.searchParams.set('paged', 1); // Reset to first page
		window.location.href = currentUrl.toString();
	});
});
</script>

<?php
/**
 * View Table Page
 *
 * Displays a single table with search, filter, sort, and pagination.
 *
 * @package ATablesCharts\Core
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get table ID from URL.
$table_id = isset( $_GET['table_id'] ) ? (int) $_GET['table_id'] : 0;

if ( empty( $table_id ) ) {
	wp_die( esc_html__( 'Invalid table ID.', 'a-tables-charts' ) );
}

// Get filter parameters.
$current_page = isset( $_GET['paged'] ) ? max( 1, (int) $_GET['paged'] ) : 1;
$per_page = isset( $_GET['per_page'] ) ? (int) $_GET['per_page'] : 10;
$search = isset( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : '';
$sort_column = isset( $_GET['sort'] ) ? sanitize_text_field( $_GET['sort'] ) : '';
$sort_order = isset( $_GET['order'] ) && in_array( strtolower( $_GET['order'] ), array( 'asc', 'desc' ), true ) ? strtolower( $_GET['order'] ) : 'asc';

// Validate per_page values.
$allowed_per_page = array( 10, 25, 50, 100 );
if ( ! in_array( $per_page, $allowed_per_page, true ) ) {
	$per_page = 10;
}

// Load Tables module.
require_once ATABLES_PLUGIN_DIR . 'src/modules/tables/index.php';

$table_service = new \ATablesCharts\Tables\Services\TableService();
$table = $table_service->get_table( $table_id );

if ( ! $table ) {
	wp_die( esc_html__( 'Table not found.', 'a-tables-charts' ) );
}

$headers = $table->get_headers();

// Get filtered and paginated data.
$table_repository = new \ATablesCharts\Tables\Repositories\TableRepository();
$result = $table_repository->get_table_data_filtered( $table_id, array(
	'per_page'    => $per_page,
	'page'        => $current_page,
	'search'      => $search,
	'sort_column' => $sort_column,
	'sort_order'  => $sort_order,
) );

$data = $result['data'];
$filtered_total = $result['total'];

// Calculate pagination info.
$total_rows = $table->row_count;
$total_pages = ceil( $filtered_total / $per_page );
$start_row = $filtered_total > 0 ? ( ( $current_page - 1 ) * $per_page ) + 1 : 0;
$end_row = min( $current_page * $per_page, $filtered_total );

// Helper function to generate URL with current parameters.
function get_url_with_params( $new_params = array() ) {
	$current_params = array(
		'page' => 'a-tables-charts-view',
		'table_id' => $_GET['table_id'],
		'per_page' => $_GET['per_page'] ?? 10,
		'paged' => $_GET['paged'] ?? 1,
		's' => $_GET['s'] ?? '',
		'sort' => $_GET['sort'] ?? '',
		'order' => $_GET['order'] ?? '',
	);
	
	$params = array_merge( $current_params, $new_params );
	
	// Remove empty parameters.
	$params = array_filter( $params, function( $value ) {
		return $value !== '' && $value !== null;
	} );
	
	return add_query_arg( $params, admin_url( 'admin.php' ) );
}
?>

<div class="wrap atables-view-page">
	<!-- Page Header -->
	<div class="atables-page-header">
		<h1 class="atables-page-title"><?php echo esc_html( $table->title ); ?></h1>
		
		<div class="atables-header-actions">
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=a-tables-charts' ) ); ?>" class="button">
				<span class="dashicons dashicons-arrow-left-alt2"></span>
				<?php esc_html_e( 'Back to All Tables', 'a-tables-charts' ); ?>
			</a>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=a-tables-charts-edit&table_id=' . $table->id ) ); ?>" class="button button-primary">
				<span class="dashicons dashicons-edit"></span>
				<?php esc_html_e( 'Edit Table', 'a-tables-charts' ); ?>
			</a>
			<button class="button button-secondary atables-export-btn" data-format="csv">
				<span class="dashicons dashicons-media-spreadsheet"></span>
				<?php esc_html_e( 'Export CSV', 'a-tables-charts' ); ?>
			</button>
			<button class="button button-secondary atables-export-btn" data-format="excel">
				<span class="dashicons dashicons-download"></span>
				<?php esc_html_e( 'Export Excel', 'a-tables-charts' ); ?>
			</button>
			<button class="button button-secondary atables-export-btn" data-format="pdf">
				<span class="dashicons dashicons-media-document"></span>
				<?php esc_html_e( 'Export PDF', 'a-tables-charts' ); ?>
			</button>
			<button class="button atables-delete-btn" data-table-id="<?php echo esc_attr( $table->id ); ?>" data-table-title="<?php echo esc_attr( $table->title ); ?>">
				<span class="dashicons dashicons-trash"></span>
				<?php esc_html_e( 'Delete', 'a-tables-charts' ); ?>
			</button>
		</div>
	</div>
	
	<!-- Table Info Card -->
	<div class="atables-info-card">
		<?php if ( ! empty( $table->description ) ) : ?>
			<p class="atables-table-description"><?php echo esc_html( $table->description ); ?></p>
		<?php endif; ?>
		
		<div class="atables-info-stats">
			<div class="atables-info-stat">
				<span class="atables-info-label"><?php esc_html_e( 'Source Type', 'a-tables-charts' ); ?></span>
				<span class="atables-info-value">
					<span class="atables-badge atables-badge-info"><?php echo esc_html( strtoupper( $table->source_type ) ); ?></span>
				</span>
			</div>
			
			<div class="atables-info-stat">
				<span class="atables-info-label"><?php esc_html_e( 'Total Rows', 'a-tables-charts' ); ?></span>
				<span class="atables-info-value"><?php echo esc_html( number_format( $total_rows ) ); ?></span>
			</div>
			
			<div class="atables-info-stat">
				<span class="atables-info-label"><?php esc_html_e( 'Total Columns', 'a-tables-charts' ); ?></span>
				<span class="atables-info-value"><?php echo esc_html( $table->column_count ); ?></span>
			</div>
			
			<div class="atables-info-stat">
				<span class="atables-info-label"><?php esc_html_e( 'Created', 'a-tables-charts' ); ?></span>
				<span class="atables-info-value"><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $table->created_at ) ) ); ?></span>
			</div>
		</div>
	</div>
	
	<!-- Shortcodes Helper -->
	<div class="atables-shortcode-card">
		<h3>
			<span class="dashicons dashicons-shortcode"></span>
			<?php esc_html_e( 'Shortcodes', 'a-tables-charts' ); ?>
		</h3>
		<p class="atables-help-text"><?php esc_html_e( 'Use these shortcodes to display this table or individual cells on your posts and pages.', 'a-tables-charts' ); ?></p>
		
		<div class="atables-shortcode-section">
			<h4><?php esc_html_e( 'Full Table', 'a-tables-charts' ); ?></h4>
			<div class="atables-shortcode-box">
				<code class="atables-shortcode-code">[atable id="<?php echo esc_attr( $table_id ); ?>"]</code>
				<button class="button atables-copy-shortcode" data-shortcode='[atable id="<?php echo esc_attr( $table_id ); ?>"]'>
					<span class="dashicons dashicons-admin-page"></span>
					<?php esc_html_e( 'Copy', 'a-tables-charts' ); ?>
				</button>
			</div>
		</div>
		
		<div class="atables-shortcode-section">
			<h4><?php esc_html_e( 'Single Cell Value', 'a-tables-charts' ); ?></h4>
			<p class="atables-help-text-small"><?php esc_html_e( 'Display a single cell value from this table. Select row and column:', 'a-tables-charts' ); ?></p>
			
			<div class="atables-cell-shortcode-builder">
				<div class="atables-form-row">
					<label><?php esc_html_e( 'Row Number', 'a-tables-charts' ); ?></label>
					<input type="number" id="cell-row" class="small-text" value="1" min="1" max="<?php echo esc_attr( $total_rows ); ?>">
					<span class="atables-help-text-small"><?php printf( esc_html__( '(1-%d)', 'a-tables-charts' ), $total_rows ); ?></span>
				</div>
				
				<div class="atables-form-row">
					<label><?php esc_html_e( 'Column', 'a-tables-charts' ); ?></label>
					<select id="cell-column" class="atables-select">
						<?php foreach ( $headers as $header ) : ?>
							<option value="<?php echo esc_attr( $header ); ?>"><?php echo esc_html( $header ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				
				<div class="atables-form-row">
					<label><?php esc_html_e( 'Format', 'a-tables-charts' ); ?></label>
					<select id="cell-format" class="atables-select">
						<option value="text"><?php esc_html_e( 'Text (default)', 'a-tables-charts' ); ?></option>
						<option value="number"><?php esc_html_e( 'Number (1,234.56)', 'a-tables-charts' ); ?></option>
						<option value="currency"><?php esc_html_e( 'Currency ($1,234.56)', 'a-tables-charts' ); ?></option>
					</select>
				</div>
				
				<div class="atables-shortcode-box">
					<code class="atables-shortcode-code" id="cell-shortcode-preview">[atable_cell table_id="<?php echo esc_attr( $table_id ); ?>" row="1" column="<?php echo esc_attr( $headers[0] ?? 'Column1' ); ?>" format="text"]</code>
					<button class="button atables-copy-shortcode" id="copy-cell-shortcode">
						<span class="dashicons dashicons-admin-page"></span>
						<?php esc_html_e( 'Copy', 'a-tables-charts' ); ?>
					</button>
				</div>
				
				<p class="atables-help-text-small">
					<strong><?php esc_html_e( 'Advanced Options:', 'a-tables-charts' ); ?></strong><br>
					<code>prefix="$"</code> - Add text before value<br>
					<code>suffix="%"</code> - Add text after value<br>
					<code>default="N/A"</code> - Default if cell is empty
				</p>
			</div>
		</div>
	</div>
	
	<!-- Filter Panel -->
	<?php
	// Pass variables to filter panel
	$columns = $headers; // Table column names
	// Include filter panel
	require ATABLES_PLUGIN_DIR . 'src/modules/filters/views/filter-panel.php';
	?>
	
	<!-- Data Table Card -->
	<div class="atables-data-card">
		<div class="atables-data-header">
			<div class="atables-data-title">
				<h2><?php esc_html_e( 'Table Data', 'a-tables-charts' ); ?></h2>
			</div>
			<div class="atables-data-actions">
				<!-- Rows per page selector -->
				<div class="atables-per-page-selector">
					<label for="per-page-select"><?php esc_html_e( 'Show', 'a-tables-charts' ); ?></label>
					<select id="per-page-select" class="atables-per-page-select">
						<option value="10" <?php selected( $per_page, 10 ); ?>>10</option>
						<option value="25" <?php selected( $per_page, 25 ); ?>>25</option>
						<option value="50" <?php selected( $per_page, 50 ); ?>>50</option>
						<option value="100" <?php selected( $per_page, 100 ); ?>>100</option>
					</select>
					<label for="per-page-select"><?php esc_html_e( 'rows', 'a-tables-charts' ); ?></label>
				</div>
			</div>
		</div>
		
		<div class="atables-data-wrapper">
			<table class="atables-modern-table atables-sortable-table">
				<thead>
					<tr>
						<?php foreach ( $headers as $header ) : ?>
							<?php
							$is_sorted = ( $sort_column === $header );
							$next_order = $is_sorted && $sort_order === 'asc' ? 'desc' : 'asc';
							$sort_url = get_url_with_params( array(
								'sort' => $header,
								'order' => $next_order,
								'paged' => 1, // Reset to first page when sorting
							) );
							?>
							<th class="atables-sortable <?php echo $is_sorted ? 'sorted sorted-' . esc_attr( $sort_order ) : ''; ?>">
								<a href="<?php echo esc_url( $sort_url ); ?>" class="atables-sort-link">
									<?php echo esc_html( $header ); ?>
									<span class="atables-sort-icon">
										<?php if ( $is_sorted ) : ?>
											<span class="dashicons dashicons-arrow-<?php echo $sort_order === 'asc' ? 'up' : 'down'; ?>-alt2"></span>
										<?php else : ?>
											<span class="dashicons dashicons-sort"></span>
										<?php endif; ?>
									</span>
								</a>
							</th>
						<?php endforeach; ?>
					</tr>
				</thead>
				<tbody>
					<?php if ( ! empty( $data ) ) : ?>
						<?php foreach ( $data as $row ) : ?>
							<tr>
								<?php foreach ( $row as $cell ) : ?>
									<td><?php echo esc_html( $cell ); ?></td>
								<?php endforeach; ?>
							</tr>
						<?php endforeach; ?>
					<?php else : ?>
						<tr>
							<td colspan="<?php echo esc_attr( count( $headers ) ); ?>" class="atables-no-data">
								<span class="dashicons dashicons-info"></span>
								<?php
								if ( ! empty( $search ) ) {
									esc_html_e( 'No results found for your search.', 'a-tables-charts' );
								} else {
									esc_html_e( 'No data available in this table.', 'a-tables-charts' );
								}
								?>
							</td>
						</tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
		
		<!-- Table Footer with Pagination -->
		<div class="atables-data-footer">
			<div class="atables-pagination-info">
				<p class="atables-data-count">
					<?php
					if ( $filtered_total > 0 ) {
						if ( $filtered_total < $total_rows ) {
							printf(
								/* translators: 1: start row, 2: end row, 3: filtered total, 4: total rows */
								esc_html__( 'Showing %1$s to %2$s of %3$s rows (filtered from %4$s total)', 'a-tables-charts' ),
								'<strong>' . esc_html( number_format( $start_row ) ) . '</strong>',
								'<strong>' . esc_html( number_format( $end_row ) ) . '</strong>',
								'<strong>' . esc_html( number_format( $filtered_total ) ) . '</strong>',
								'<strong>' . esc_html( number_format( $total_rows ) ) . '</strong>'
							);
						} else {
							printf(
								/* translators: 1: start row, 2: end row, 3: total rows */
								esc_html__( 'Showing %1$s to %2$s of %3$s rows', 'a-tables-charts' ),
								'<strong>' . esc_html( number_format( $start_row ) ) . '</strong>',
								'<strong>' . esc_html( number_format( $end_row ) ) . '</strong>',
								'<strong>' . esc_html( number_format( $filtered_total ) ) . '</strong>'
							);
						}
					} else {
						esc_html_e( 'No rows to display', 'a-tables-charts' );
					}
					?>
				</p>
			</div>
			
			<?php if ( $total_pages > 1 ) : ?>
				<div class="atables-pagination-controls">
					<!-- First Page -->
					<?php if ( $current_page > 1 ) : ?>
						<a href="<?php echo esc_url( get_url_with_params( array( 'paged' => 1 ) ) ); ?>" 
						   class="atables-page-btn atables-page-first" 
						   title="<?php esc_attr_e( 'First Page', 'a-tables-charts' ); ?>">
							<span class="dashicons dashicons-controls-skipback"></span>
						</a>
						<a href="<?php echo esc_url( get_url_with_params( array( 'paged' => max( 1, $current_page - 1 ) ) ) ); ?>" 
						   class="atables-page-btn atables-page-prev" 
						   title="<?php esc_attr_e( 'Previous Page', 'a-tables-charts' ); ?>">
							<span class="dashicons dashicons-arrow-left-alt2"></span>
						</a>
					<?php else : ?>
						<span class="atables-page-btn atables-page-first disabled">
							<span class="dashicons dashicons-controls-skipback"></span>
						</span>
						<span class="atables-page-btn atables-page-prev disabled">
							<span class="dashicons dashicons-arrow-left-alt2"></span>
						</span>
					<?php endif; ?>
					
					<!-- Page Numbers -->
					<div class="atables-page-numbers">
						<?php
						// Calculate page range to display.
						$range = 2; // Show 2 pages on each side of current page.
						$start = max( 1, $current_page - $range );
						$end = min( $total_pages, $current_page + $range );
						
						// Show first page and ellipsis if needed.
						if ( $start > 1 ) {
							?>
							<a href="<?php echo esc_url( get_url_with_params( array( 'paged' => 1 ) ) ); ?>" 
							   class="atables-page-num">1</a>
							<?php if ( $start > 2 ) : ?>
								<span class="atables-page-ellipsis">...</span>
							<?php endif; ?>
							<?php
						}
						
						// Show page numbers in range.
						for ( $i = $start; $i <= $end; $i++ ) {
							if ( $i === $current_page ) {
								?>
								<span class="atables-page-num active"><?php echo esc_html( $i ); ?></span>
								<?php
							} else {
								?>
								<a href="<?php echo esc_url( get_url_with_params( array( 'paged' => $i ) ) ); ?>" 
								   class="atables-page-num"><?php echo esc_html( $i ); ?></a>
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
							<a href="<?php echo esc_url( get_url_with_params( array( 'paged' => $total_pages ) ) ); ?>" 
							   class="atables-page-num"><?php echo esc_html( $total_pages ); ?></a>
							<?php
						}
						?>
					</div>
					
					<!-- Next/Last Page -->
					<?php if ( $current_page < $total_pages ) : ?>
						<a href="<?php echo esc_url( get_url_with_params( array( 'paged' => min( $total_pages, $current_page + 1 ) ) ) ); ?>" 
						   class="atables-page-btn atables-page-next" 
						   title="<?php esc_attr_e( 'Next Page', 'a-tables-charts' ); ?>">
							<span class="dashicons dashicons-arrow-right-alt2"></span>
						</a>
						<a href="<?php echo esc_url( get_url_with_params( array( 'paged' => $total_pages ) ) ); ?>" 
						   class="atables-page-btn atables-page-last" 
						   title="<?php esc_attr_e( 'Last Page', 'a-tables-charts' ); ?>">
							<span class="dashicons dashicons-controls-skipforward"></span>
						</a>
					<?php else : ?>
						<span class="atables-page-btn atables-page-next disabled">
							<span class="dashicons dashicons-arrow-right-alt2"></span>
						</span>
						<span class="atables-page-btn atables-page-last disabled">
							<span class="dashicons dashicons-controls-skipforward"></span>
						</span>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>

<script>
jQuery(document).ready(function($) {
	// Handle per-page change
	$('#per-page-select').on('change', function() {
		var perPage = $(this).val();
		var currentUrl = new URL(window.location.href);
		currentUrl.searchParams.set('per_page', perPage);
		currentUrl.searchParams.set('paged', 1); // Reset to first page
		window.location.href = currentUrl.toString();
	});
	
	// Copy shortcode buttons
	$('.atables-copy-shortcode').on('click', function() {
		var shortcode = $(this).data('shortcode');
		
		// For cell shortcode, get from preview
		if ($(this).attr('id') === 'copy-cell-shortcode') {
			shortcode = $('#cell-shortcode-preview').text();
		}
		
		// Copy to clipboard
		if (navigator.clipboard) {
			navigator.clipboard.writeText(shortcode).then(function() {
				showNotification('Shortcode copied to clipboard!', 'success');
			}).catch(function() {
				fallbackCopy(shortcode);
			});
		} else {
			fallbackCopy(shortcode);
		}
	});
	
	// Update cell shortcode preview
	function updateCellShortcode() {
		var tableId = <?php echo esc_js( $table_id ); ?>;
		var row = parseInt($('#cell-row').val()); // Keep as 1-based (user-friendly)
		var column = $('#cell-column').val();
		var format = $('#cell-format').val();
		
		var shortcode = '[atable_cell table_id="' + tableId + '" row="' + row + '" column="' + column + '"';
		
		if (format !== 'text') {
			shortcode += ' format="' + format + '"';
		}
		
		shortcode += ']';
		
		$('#cell-shortcode-preview').text(shortcode);
	}
	
	// Update on input change
	$('#cell-row, #cell-column, #cell-format').on('change', updateCellShortcode);
	
	// Fallback copy method
	function fallbackCopy(text) {
		var $temp = $('<textarea>').val(text).appendTo('body').select();
		try {
			document.execCommand('copy');
			showNotification('Shortcode copied to clipboard!', 'success');
		} catch (err) {
			showNotification('Failed to copy shortcode', 'error');
		}
		$temp.remove();
	}
	
	// Show notification
	function showNotification(message, type) {
		var $notification = $('<div>', {
			class: 'atables-notification atables-notification-' + type,
			text: message
		}).appendTo('body');
		
		setTimeout(function() {
			$notification.addClass('show');
		}, 10);
		
		setTimeout(function() {
			$notification.removeClass('show');
			setTimeout(function() {
				$notification.remove();
			}, 300);
		}, 3000);
	}
});
</script>

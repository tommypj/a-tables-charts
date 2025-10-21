<?php
/**
 * Edit Table Page
 *
 * Allows users to edit table metadata and data.
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

// Load Tables module.
require_once ATABLES_PLUGIN_DIR . 'src/modules/tables/index.php';

$table_service = new \ATablesCharts\Tables\Services\TableService();
$table = $table_service->get_table( $table_id );

if ( ! $table ) {
	wp_die( esc_html__( 'Table not found.', 'a-tables-charts' ) );
}

$headers = $table->get_headers();
$table_repository = new \ATablesCharts\Tables\Repositories\TableRepository();
$data = $table_repository->get_table_data( $table_id );

// Get display settings for this table
$table_display_settings = $table->get_display_settings();

// Get global settings for comparison
$global_settings = get_option( 'atables_settings', array() );
$global_defaults = array(
	'rows_per_page'      => 10,
	'default_table_style' => 'default',
	'enable_search'      => true,
	'enable_sorting'     => true,
	'enable_pagination'  => true,
);
$global_settings = wp_parse_args( $global_settings, $global_defaults );
?>

<div class="wrap atables-edit-page">
	<!-- Page Header -->
	<div class="atables-page-header">
		<h1 class="atables-page-title">
			<span class="dashicons dashicons-edit"></span>
			<?php esc_html_e( 'Edit Table', 'a-tables-charts' ); ?>
		</h1>
		
		<div class="atables-header-actions">
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=a-tables-charts' ) ); ?>" class="button">
				<span class="dashicons dashicons-arrow-left-alt2"></span>
				<?php esc_html_e( 'Back to All Tables', 'a-tables-charts' ); ?>
			</a>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=a-tables-charts-view&table_id=' . $table->id ) ); ?>" class="button">
				<span class="dashicons dashicons-visibility"></span>
				<?php esc_html_e( 'View Table', 'a-tables-charts' ); ?>
			</a>
			<button type="button" class="button atables-delete-table-btn" data-table-id="<?php echo esc_attr( $table->id ); ?>" data-table-title="<?php echo esc_attr( $table->title ); ?>">
				<span class="dashicons dashicons-trash"></span>
				<?php esc_html_e( 'Delete Table', 'a-tables-charts' ); ?>
			</button>
			<button type="button" class="button button-primary atables-save-btn" id="atables-save-table">
				<span class="dashicons dashicons-yes"></span>
				<?php esc_html_e( 'Save Changes', 'a-tables-charts' ); ?>
			</button>
		</div>
	</div>
	
	<!-- Success/Error Messages -->
	<div class="atables-notice atables-notice-success" style="display:none;">
		<p></p>
	</div>
	<div class="atables-notice atables-notice-error" style="display:none;">
		<p></p>
	</div>
	
	<!-- Two Column Layout -->
	<div class="atables-two-column-layout">
		<!-- Left Column: Table Info -->
		<div class="atables-column atables-column-left">
			<div class="atables-edit-section atables-info-section">
				<h2><?php esc_html_e( 'Table Information', 'a-tables-charts' ); ?></h2>
				
				<div class="atables-form-row">
					<label for="table-title">
						<?php esc_html_e( 'Table Title', 'a-tables-charts' ); ?>
						<span class="required">*</span>
					</label>
					<input type="text" 
						   id="table-title" 
						   class="regular-text" 
						   value="<?php echo esc_attr( $table->title ); ?>" 
						   required>
				</div>
				
				<div class="atables-form-row">
					<label for="table-description">
						<?php esc_html_e( 'Description', 'a-tables-charts' ); ?>
					</label>
					<textarea id="table-description" 
							  class="large-text" 
							  rows="4"><?php echo esc_textarea( $table->description ); ?></textarea>
				</div>
				
				<div class="atables-form-row atables-meta-info">
					<div class="atables-meta-item">
						<span class="atables-meta-label"><?php esc_html_e( 'Source Type:', 'a-tables-charts' ); ?></span>
						<span class="atables-badge atables-badge-info"><?php echo esc_html( strtoupper( $table->source_type ) ); ?></span>
					</div>
					<div class="atables-meta-item">
						<span class="atables-meta-label"><?php esc_html_e( 'Created:', 'a-tables-charts' ); ?></span>
						<span><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $table->created_at ) ) ); ?></span>
					</div>
					<div class="atables-meta-item">
						<span class="atables-meta-label"><?php esc_html_e( 'Rows:', 'a-tables-charts' ); ?></span>
						<span class="atables-badge"><?php echo esc_html( $table->row_count ); ?></span>
					</div>
					<div class="atables-meta-item">
						<span class="atables-meta-label"><?php esc_html_e( 'Columns:', 'a-tables-charts' ); ?></span>
						<span class="atables-badge"><?php echo esc_html( $table->column_count ); ?></span>
					</div>
				</div>
			</div>
		</div>
		
		<!-- Right Column: Display Settings -->
		<div class="atables-column atables-column-right">
			<div class="atables-edit-section atables-display-settings-section">
				<h2>
					<span class="dashicons dashicons-admin-appearance"></span>
					<?php esc_html_e( 'Display Settings', 'a-tables-charts' ); ?>
				</h2>
				
				<div class="atables-settings-compact">
					<!-- Rows per Page -->
					<div class="atables-setting-compact">
						<label class="atables-setting-label">
							<?php esc_html_e( 'Rows per Page', 'a-tables-charts' ); ?>
						</label>
						<input type="number" 
							   name="display_settings[rows_per_page]" 
							   class="small-text"
							   value="<?php echo esc_attr( $table_display_settings['rows_per_page'] ?? $global_settings['rows_per_page'] ); ?>"
							   min="1" 
							   max="100">
					</div>
					
					<!-- Table Style -->
					<div class="atables-setting-compact">
						<label class="atables-setting-label">
							<?php esc_html_e( 'Table Style', 'a-tables-charts' ); ?>
						</label>
						<select name="display_settings[table_style]" class="atables-select">
							<option value="default" <?php selected( $table_display_settings['table_style'] ?? $global_settings['default_table_style'], 'default' ); ?>>
								<?php esc_html_e( 'Default', 'a-tables-charts' ); ?>
							</option>
							<option value="striped" <?php selected( $table_display_settings['table_style'] ?? $global_settings['default_table_style'], 'striped' ); ?>>
								<?php esc_html_e( 'Striped', 'a-tables-charts' ); ?>
							</option>
							<option value="bordered" <?php selected( $table_display_settings['table_style'] ?? $global_settings['default_table_style'], 'bordered' ); ?>>
								<?php esc_html_e( 'Bordered', 'a-tables-charts' ); ?>
							</option>
							<option value="hover" <?php selected( $table_display_settings['table_style'] ?? $global_settings['default_table_style'], 'hover' ); ?>>
								<?php esc_html_e( 'Hover', 'a-tables-charts' ); ?>
							</option>
						</select>
					</div>
					
					<!-- Feature Toggles -->
					<div class="atables-setting-compact">
						<label class="atables-setting-label">
							<?php esc_html_e( 'Features', 'a-tables-charts' ); ?>
						</label>
						<div class="atables-feature-toggles">
							<!-- Search -->
							<?php
							$search_enabled = isset( $table_display_settings['enable_search'] ) 
								? $table_display_settings['enable_search'] 
								: $global_settings['enable_search'];
							?>
							<label class="atables-toggle">
								<span><?php esc_html_e( 'Search', 'a-tables-charts' ); ?></span>
								<div class="atables-toggle-switch">
									<input type="radio" 
										   name="display_settings[enable_search]" 
										   value="1" 
										   id="search-on"
										   <?php checked( $search_enabled === true || $search_enabled === 1 ); ?>>
									<label for="search-on"><?php esc_html_e( 'On', 'a-tables-charts' ); ?></label>
									<input type="radio" 
										   name="display_settings[enable_search]" 
										   value="0"
										   id="search-off" 
										   <?php checked( $search_enabled === false || $search_enabled === 0 ); ?>>
									<label for="search-off"><?php esc_html_e( 'Off', 'a-tables-charts' ); ?></label>
								</div>
							</label>
							
							<!-- Sorting -->
							<?php
							$sorting_enabled = isset( $table_display_settings['enable_sorting'] ) 
								? $table_display_settings['enable_sorting'] 
								: $global_settings['enable_sorting'];
							?>
							<label class="atables-toggle">
								<span><?php esc_html_e( 'Sorting', 'a-tables-charts' ); ?></span>
								<div class="atables-toggle-switch">
									<input type="radio" 
										   name="display_settings[enable_sorting]" 
										   value="1"
										   id="sorting-on" 
										   <?php checked( $sorting_enabled === true || $sorting_enabled === 1 ); ?>>
									<label for="sorting-on"><?php esc_html_e( 'On', 'a-tables-charts' ); ?></label>
									<input type="radio" 
										   name="display_settings[enable_sorting]" 
										   value="0"
										   id="sorting-off" 
										   <?php checked( $sorting_enabled === false || $sorting_enabled === 0 ); ?>>
									<label for="sorting-off"><?php esc_html_e( 'Off', 'a-tables-charts' ); ?></label>
								</div>
							</label>
							
							<!-- Pagination -->
							<?php
							$pagination_enabled = isset( $table_display_settings['enable_pagination'] ) 
								? $table_display_settings['enable_pagination'] 
								: $global_settings['enable_pagination'];
							?>
							<label class="atables-toggle">
								<span><?php esc_html_e( 'Pagination', 'a-tables-charts' ); ?></span>
								<div class="atables-toggle-switch">
									<input type="radio" 
										   name="display_settings[enable_pagination]" 
										   value="1"
										   id="pagination-on" 
										   <?php checked( $pagination_enabled === true || $pagination_enabled === 1 ); ?>>
									<label for="pagination-on"><?php esc_html_e( 'On', 'a-tables-charts' ); ?></label>
									<input type="radio" 
										   name="display_settings[enable_pagination]" 
										   value="0"
										   id="pagination-off" 
										   <?php checked( $pagination_enabled === false || $pagination_enabled === 0 ); ?>>
									<label for="pagination-off"><?php esc_html_e( 'Off', 'a-tables-charts' ); ?></label>
								</div>
							</label>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<!-- Table Data Section -->
	<div class="atables-edit-section atables-data-section">
		<div class="atables-section-header">
			<h2><?php esc_html_e( 'Table Data', 'a-tables-charts' ); ?></h2>
			<div class="atables-data-actions">
				<button type="button" class="button" id="atables-add-row">
					<span class="dashicons dashicons-plus-alt"></span>
					<?php esc_html_e( 'Add Row', 'a-tables-charts' ); ?>
				</button>
				<button type="button" class="button" id="atables-add-column">
					<span class="dashicons dashicons-plus-alt"></span>
					<?php esc_html_e( 'Add Column', 'a-tables-charts' ); ?>
				</button>
			</div>
		</div>
		
		<div class="atables-table-wrapper">
			<table class="atables-edit-table" id="atables-editable-table">
				<thead>
					<tr>
						<th class="atables-row-number">#</th>
						<?php foreach ( $headers as $index => $header ) : ?>
							<th data-column-index="<?php echo esc_attr( $index ); ?>">
								<input type="text" 
									   class="atables-header-input" 
									   value="<?php echo esc_attr( $header ); ?>" 
									   data-original="<?php echo esc_attr( $header ); ?>">
								<button type="button" class="atables-delete-column" title="<?php esc_attr_e( 'Delete Column', 'a-tables-charts' ); ?>">
									<span class="dashicons dashicons-no"></span>
								</button>
							</th>
						<?php endforeach; ?>
					</tr>
				</thead>
				<tbody>
					<?php if ( ! empty( $data ) ) : ?>
						<?php foreach ( $data as $row_index => $row ) : ?>
							<tr data-row-index="<?php echo esc_attr( $row_index ); ?>">
								<td class="atables-row-number">
									<?php echo esc_html( $row_index + 1 ); ?>
									<button type="button" class="atables-delete-row" title="<?php esc_attr_e( 'Delete Row', 'a-tables-charts' ); ?>">
										<span class="dashicons dashicons-trash"></span>
									</button>
								</td>
								<?php foreach ( $row as $cell ) : ?>
									<td>
										<input type="text" 
											   class="atables-cell-input" 
											   value="<?php echo esc_attr( $cell ); ?>">
									</td>
								<?php endforeach; ?>
							</tr>
						<?php endforeach; ?>
					<?php else : ?>
						<tr class="atables-no-data">
							<td colspan="<?php echo esc_attr( count( $headers ) + 1 ); ?>">
								<p><?php esc_html_e( 'No data available. Add rows to get started.', 'a-tables-charts' ); ?></p>
							</td>
						</tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
		
		<div class="atables-table-stats">
			<span id="atables-row-count"><?php echo esc_html( count( $data ) ); ?></span> 
			<?php esc_html_e( 'rows', 'a-tables-charts' ); ?> × 
			<span id="atables-column-count"><?php echo esc_html( count( $headers ) ); ?></span> 
			<?php esc_html_e( 'columns', 'a-tables-charts' ); ?>
		</div>
	</div>
	
	<!-- Hidden field for table ID -->
	<input type="hidden" id="atables-table-id" value="<?php echo esc_attr( $table_id ); ?>">
</div>

<script>
jQuery(document).ready(function($) {
	'use strict';
	
	let rowCounter = <?php echo count( $data ); ?>;
	
	// Save table
	$('#atables-save-table').on('click', function() {
		saveTable();
	});
	
	// Add row
	$('#atables-add-row').on('click', function() {
		addRow();
	});
	
	// Add column
	$('#atables-add-column').on('click', function() {
		addColumn();
	});
	
	// Delete row
	$(document).on('click', '.atables-delete-row', async function() {
		const confirmed = await ATablesModal.confirm({
			title: 'Delete Row?',
			message: 'Are you sure you want to delete this row?',
			type: 'warning',
			icon: '⚠️',
			confirmText: 'Delete Row',
			cancelText: 'Cancel'
		});
		
		if (confirmed) {
			$(this).closest('tr').fadeOut(300, function() {
				$(this).remove();
				updateRowNumbers();
				updateStats();
			});
		}
	});
	
	// Delete column
	$(document).on('click', '.atables-delete-column', async function() {
		const confirmed = await ATablesModal.confirm({
			title: 'Delete Column?',
			message: 'Are you sure you want to delete this column?',
			type: 'warning',
			icon: '⚠️',
			confirmText: 'Delete Column',
			cancelText: 'Cancel'
		});
		
		if (confirmed) {
			const columnIndex = $(this).closest('th').data('column-index');
			deleteColumn(columnIndex);
		}
	});
	
	/**
	 * Add new row
	 */
	function addRow() {
		const $table = $('#atables-editable-table tbody');
		const columnCount = $('#atables-editable-table thead th').length - 1; // Exclude row number column
		
		// Remove "no data" message if present
		$table.find('.atables-no-data').remove();
		
		rowCounter++;
		let html = '<tr data-row-index="' + rowCounter + '">';
		html += '<td class="atables-row-number">' + rowCounter + '<button type="button" class="atables-delete-row" title="Delete Row"><span class="dashicons dashicons-trash"></span></button></td>';
		
		for (let i = 0; i < columnCount; i++) {
			html += '<td><input type="text" class="atables-cell-input" value=""></td>';
		}
		
		html += '</tr>';
		
		$table.append(html);
		updateStats();
	}
	
	/**
	 * Add new column
	 */
	function addColumn() {
		const columnName = prompt('<?php esc_html_e( 'Enter column name:', 'a-tables-charts' ); ?>', 'Column ' + ($('#atables-editable-table thead th').length));
		
		if (!columnName) return;
		
		const columnIndex = $('#atables-editable-table thead th').length - 1;
		
		// Add header
		const headerHtml = '<th data-column-index="' + columnIndex + '">' +
			'<input type="text" class="atables-header-input" value="' + escapeHtml(columnName) + '" data-original="' + escapeHtml(columnName) + '">' +
			'<button type="button" class="atables-delete-column" title="Delete Column"><span class="dashicons dashicons-no"></span></button>' +
			'</th>';
		$('#atables-editable-table thead tr').append(headerHtml);
		
		// Add cells to existing rows
		$('#atables-editable-table tbody tr').each(function() {
			if (!$(this).hasClass('atables-no-data')) {
				$(this).append('<td><input type="text" class="atables-cell-input" value=""></td>');
			}
		});
		
		updateStats();
	}
	
	/**
	 * Delete column
	 */
	function deleteColumn(columnIndex) {
		// Delete header (add 1 for row number column)
		$('#atables-editable-table thead th').eq(columnIndex + 1).remove();
		
		// Delete cells in all rows
		$('#atables-editable-table tbody tr').each(function() {
			$(this).find('td').eq(columnIndex + 1).remove();
		});
		
		updateStats();
	}
	
	/**
	 * Update row numbers
	 */
	function updateRowNumbers() {
		$('#atables-editable-table tbody tr').each(function(index) {
			$(this).find('.atables-row-number').first().text(index + 1);
			$(this).attr('data-row-index', index);
		});
		rowCounter = $('#atables-editable-table tbody tr').length;
	}
	
	/**
	 * Update table statistics
	 */
	function updateStats() {
		const rowCount = $('#atables-editable-table tbody tr:not(.atables-no-data)').length;
		const columnCount = $('#atables-editable-table thead th').length - 1; // Exclude row number column
		
		$('#atables-row-count').text(rowCount);
		$('#atables-column-count').text(columnCount);
	}
	
	/**
	 * Save table
	 */
	function saveTable() {
		const $btn = $('#atables-save-table');
		const originalText = $btn.html();
		
		// Disable button
		$btn.prop('disabled', true).html('<span class="dashicons dashicons-update dashicons-spin"></span> Saving...');
		
		// Collect data
		const tableId = $('#atables-table-id').val();
		const title = $('#table-title').val().trim();
		const description = $('#table-description').val().trim();
		
		if (!title) {
			showNotice('error', 'Table title is required.');
			$btn.prop('disabled', false).html(originalText);
			return;
		}
		
		// Collect headers
		const headers = [];
		$('#atables-editable-table thead .atables-header-input').each(function() {
			headers.push($(this).val().trim());
		});
		
		// Collect data
		const data = [];
		$('#atables-editable-table tbody tr:not(.atables-no-data)').each(function() {
			const row = [];
			$(this).find('.atables-cell-input').each(function() {
				row.push($(this).val());
			});
			if (row.length > 0) {
				data.push(row);
			}
		});
		
		// Collect display settings
		const displaySettings = {};
		
		// Rows per page - always save
		const rowsValue = $('input[name="display_settings[rows_per_page]"]').val();
		if (rowsValue) {
			displaySettings.rows_per_page = parseInt(rowsValue);
		}
		
		// Table style - always save
		const styleValue = $('select[name="display_settings[table_style]"]').val();
		if (styleValue) {
			displaySettings.table_style = styleValue;
		}
		
		// Features (search, sorting, pagination) - always save the selected value
		const searchValue = $('input[name="display_settings[enable_search]"]:checked').val();
		displaySettings.enable_search = searchValue === '1';
		
		const sortingValue = $('input[name="display_settings[enable_sorting]"]:checked').val();
		displaySettings.enable_sorting = sortingValue === '1';
		
		const paginationValue = $('input[name="display_settings[enable_pagination]"]:checked').val();
		displaySettings.enable_pagination = paginationValue === '1';
		
		// Send AJAX request
		$.ajax({
			url: aTablesAdmin.ajaxUrl,
			type: 'POST',
			data: {
				action: 'atables_update_table',
				nonce: aTablesAdmin.nonce,
				table_id: tableId,
				title: title,
				description: description,
				headers: headers,
				data: data,
			display_settings: displaySettings
			},
			success: function(response) {
				if (response.success) {
					// Handle both response.message and response.data.message structures
					const successMessage = response.message || (response.data && response.data.message) || 'Table updated successfully!';
					showNotice('success', successMessage);
					
					// Update row counter
					rowCounter = data.length;
				} else {
					// Handle both response.message and response.data.message structures
					const errorMessage = response.message || (response.data && response.data.message) || 'Failed to update table.';
					showNotice('error', errorMessage);
				}
			},
			error: function(xhr, status, error) {
				console.error('Save error:', xhr, status, error);
				showNotice('error', 'Failed to save table: ' + error);
			},
			complete: function() {
				$btn.prop('disabled', false).html(originalText);
			}
		});
	}
	
	/**
	 * Show notice
	 */
	function showNotice(type, message) {
		const $notice = $('.atables-notice-' + type);
		$notice.find('p').text(message);
		$notice.fadeIn();
		
		setTimeout(function() {
			$notice.fadeOut();
		}, 5000);
	}
	
	/**
	 * Escape HTML
	 */
	function escapeHtml(text) {
		const div = document.createElement('div');
		div.textContent = text;
		return div.innerHTML;
	}
});
</script>

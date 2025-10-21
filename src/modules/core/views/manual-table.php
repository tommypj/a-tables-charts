<?php
/**
 * Manual Table Creation View
 *
 * Allows users to create tables from scratch.
 *
 * @package ATablesCharts\Core
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="wrap atables-manual-table">
	<div class="atables-page-header">
		<h1 class="atables-page-title">
			<span class="dashicons dashicons-edit"></span>
			<?php esc_html_e( 'Create Table Manually', 'a-tables-charts' ); ?>
		</h1>
		
		<div class="atables-header-actions">
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=a-tables-charts-create' ) ); ?>" class="button">
				<span class="dashicons dashicons-arrow-left-alt2"></span>
				<?php esc_html_e( 'Back', 'a-tables-charts' ); ?>
			</a>
			<button type="button" class="button button-primary atables-save-manual-table">
				<span class="dashicons dashicons-yes"></span>
				<?php esc_html_e( 'Save Table', 'a-tables-charts' ); ?>
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
	
	<!-- Table Info Section -->
	<div class="atables-edit-section atables-info-section">
		<h2><?php esc_html_e( 'Table Information', 'a-tables-charts' ); ?></h2>
		
		<div class="atables-form-row">
			<label for="manual-table-title">
				<?php esc_html_e( 'Table Title', 'a-tables-charts' ); ?>
				<span class="required">*</span>
			</label>
			<input type="text" 
				   id="manual-table-title" 
				   class="regular-text" 
				   placeholder="<?php esc_attr_e( 'Enter table title...', 'a-tables-charts' ); ?>"
				   required>
		</div>
		
		<div class="atables-form-row">
			<label for="manual-table-description">
				<?php esc_html_e( 'Description', 'a-tables-charts' ); ?>
			</label>
			<textarea id="manual-table-description" 
					  class="large-text" 
					  rows="3"
					  placeholder="<?php esc_attr_e( 'Optional description...', 'a-tables-charts' ); ?>"></textarea>
		</div>
	</div>
	
	<!-- Table Builder Section -->
	<div class="atables-edit-section atables-builder-section">
		<div class="atables-section-header">
			<h2><?php esc_html_e( 'Table Builder', 'a-tables-charts' ); ?></h2>
			<div class="atables-builder-actions">
				<button type="button" class="button" id="atables-manual-add-row">
					<span class="dashicons dashicons-plus-alt"></span>
					<?php esc_html_e( 'Add Row', 'a-tables-charts' ); ?>
				</button>
				<button type="button" class="button" id="atables-manual-add-column">
					<span class="dashicons dashicons-plus-alt"></span>
					<?php esc_html_e( 'Add Column', 'a-tables-charts' ); ?>
				</button>
			</div>
		</div>
		
		<div class="atables-table-wrapper">
			<table class="atables-manual-builder" id="atables-manual-table">
				<thead>
					<tr>
						<th class="atables-row-number">#</th>
						<th data-column-index="0">
							<input type="text" 
								   class="atables-header-input" 
								   value="Column 1" 
								   placeholder="<?php esc_attr_e( 'Column Name', 'a-tables-charts' ); ?>">
							<button type="button" class="atables-delete-column" title="<?php esc_attr_e( 'Delete Column', 'a-tables-charts' ); ?>">
								<span class="dashicons dashicons-no"></span>
							</button>
						</th>
						<th data-column-index="1">
							<input type="text" 
								   class="atables-header-input" 
								   value="Column 2" 
								   placeholder="<?php esc_attr_e( 'Column Name', 'a-tables-charts' ); ?>">
							<button type="button" class="atables-delete-column" title="<?php esc_attr_e( 'Delete Column', 'a-tables-charts' ); ?>">
								<span class="dashicons dashicons-no"></span>
							</button>
						</th>
						<th data-column-index="2">
							<input type="text" 
								   class="atables-header-input" 
								   value="Column 3" 
								   placeholder="<?php esc_attr_e( 'Column Name', 'a-tables-charts' ); ?>">
							<button type="button" class="atables-delete-column" title="<?php esc_attr_e( 'Delete Column', 'a-tables-charts' ); ?>">
								<span class="dashicons dashicons-no"></span>
							</button>
						</th>
					</tr>
				</thead>
				<tbody>
					<tr data-row-index="0">
						<td class="atables-row-number">
							1
							<button type="button" class="atables-delete-row" title="<?php esc_attr_e( 'Delete Row', 'a-tables-charts' ); ?>">
								<span class="dashicons dashicons-trash"></span>
							</button>
						</td>
						<td>
							<input type="text" class="atables-cell-input" placeholder="<?php esc_attr_e( 'Enter data...', 'a-tables-charts' ); ?>">
						</td>
						<td>
							<input type="text" class="atables-cell-input" placeholder="<?php esc_attr_e( 'Enter data...', 'a-tables-charts' ); ?>">
						</td>
						<td>
							<input type="text" class="atables-cell-input" placeholder="<?php esc_attr_e( 'Enter data...', 'a-tables-charts' ); ?>">
						</td>
					</tr>
					<tr data-row-index="1">
						<td class="atables-row-number">
							2
							<button type="button" class="atables-delete-row" title="<?php esc_attr_e( 'Delete Row', 'a-tables-charts' ); ?>">
								<span class="dashicons dashicons-trash"></span>
							</button>
						</td>
						<td>
							<input type="text" class="atables-cell-input" placeholder="<?php esc_attr_e( 'Enter data...', 'a-tables-charts' ); ?>">
						</td>
						<td>
							<input type="text" class="atables-cell-input" placeholder="<?php esc_attr_e( 'Enter data...', 'a-tables-charts' ); ?>">
						</td>
						<td>
							<input type="text" class="atables-cell-input" placeholder="<?php esc_attr_e( 'Enter data...', 'a-tables-charts' ); ?>">
						</td>
					</tr>
					<tr data-row-index="2">
						<td class="atables-row-number">
							3
							<button type="button" class="atables-delete-row" title="<?php esc_attr_e( 'Delete Row', 'a-tables-charts' ); ?>">
								<span class="dashicons dashicons-trash"></span>
							</button>
						</td>
						<td>
							<input type="text" class="atables-cell-input" placeholder="<?php esc_attr_e( 'Enter data...', 'a-tables-charts' ); ?>">
						</td>
						<td>
							<input type="text" class="atables-cell-input" placeholder="<?php esc_attr_e( 'Enter data...', 'a-tables-charts' ); ?>">
						</td>
						<td>
							<input type="text" class="atables-cell-input" placeholder="<?php esc_attr_e( 'Enter data...', 'a-tables-charts' ); ?>">
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		
		<div class="atables-table-stats">
			<span id="atables-manual-row-count">3</span> 
			<?php esc_html_e( 'rows', 'a-tables-charts' ); ?> × 
			<span id="atables-manual-column-count">3</span> 
			<?php esc_html_e( 'columns', 'a-tables-charts' ); ?>
		</div>
		
		<div class="atables-help-text">
			<p>
				<span class="dashicons dashicons-info"></span>
				<?php esc_html_e( 'Tip: Click on column headers to rename them. Use the buttons above to add more rows or columns.', 'a-tables-charts' ); ?>
			</p>
		</div>
	</div>
</div>

<script>
jQuery(document).ready(function($) {
	'use strict';
	
	let rowCounter = 3; // Starting with 3 rows
	
	// Add row
	$('#atables-manual-add-row').on('click', function() {
		addRow();
	});
	
	// Add column
	$('#atables-manual-add-column').on('click', function() {
		addColumn();
	});
	
	// Delete row
	$(document).on('click', '.atables-delete-row', function() {
		const rowCount = $('#atables-manual-table tbody tr').length;
		
		if (rowCount <= 1) {
			showNotice('error', '<?php esc_html_e( 'Table must have at least one row.', 'a-tables-charts' ); ?>');
			return;
		}
		
		if (confirm('<?php esc_html_e( 'Are you sure you want to delete this row?', 'a-tables-charts' ); ?>')) {
			$(this).closest('tr').fadeOut(300, function() {
				$(this).remove();
				updateRowNumbers();
				updateStats();
			});
		}
	});
	
	// Delete column
	$(document).on('click', '.atables-delete-column', function() {
		const columnCount = $('#atables-manual-table thead th').length - 1; // Exclude row number column
		
		if (columnCount <= 1) {
			showNotice('error', '<?php esc_html_e( 'Table must have at least one column.', 'a-tables-charts' ); ?>');
			return;
		}
		
		if (confirm('<?php esc_html_e( 'Are you sure you want to delete this column?', 'a-tables-charts' ); ?>')) {
			const columnIndex = $(this).closest('th').data('column-index');
			deleteColumn(columnIndex);
		}
	});
	
	// Save table
	$('.atables-save-manual-table').on('click', function() {
		saveManualTable();
	});
	
	/**
	 * Add new row
	 */
	function addRow() {
		const $table = $('#atables-manual-table tbody');
		const columnCount = $('#atables-manual-table thead th').length - 1; // Exclude row number column
		
		rowCounter++;
		let html = '<tr data-row-index="' + rowCounter + '">';
		html += '<td class="atables-row-number">' + rowCounter + '<button type="button" class="atables-delete-row" title="<?php esc_attr_e( 'Delete Row', 'a-tables-charts' ); ?>"><span class="dashicons dashicons-trash"></span></button></td>';
		
		for (let i = 0; i < columnCount; i++) {
			html += '<td><input type="text" class="atables-cell-input" placeholder="<?php esc_attr_e( 'Enter data...', 'a-tables-charts' ); ?>"></td>';
		}
		
		html += '</tr>';
		
		$table.append(html);
		updateStats();
		
		// Focus first cell of new row
		$table.find('tr:last-child input:first').focus();
	}
	
	/**
	 * Add new column
	 */
	function addColumn() {
		const columnIndex = $('#atables-manual-table thead th').length - 1;
		const columnName = 'Column ' + (columnIndex + 1);
		
		// Add header
		const headerHtml = '<th data-column-index="' + columnIndex + '">' +
			'<input type="text" class="atables-header-input" value="' + escapeHtml(columnName) + '" placeholder="<?php esc_attr_e( 'Column Name', 'a-tables-charts' ); ?>">' +
			'<button type="button" class="atables-delete-column" title="<?php esc_attr_e( 'Delete Column', 'a-tables-charts' ); ?>"><span class="dashicons dashicons-no"></span></button>' +
			'</th>';
		$('#atables-manual-table thead tr').append(headerHtml);
		
		// Add cells to existing rows
		$('#atables-manual-table tbody tr').each(function() {
			$(this).append('<td><input type="text" class="atables-cell-input" placeholder="<?php esc_attr_e( 'Enter data...', 'a-tables-charts' ); ?>"></td>');
		});
		
		updateStats();
		
		// Focus new header input
		$('#atables-manual-table thead th:last-child input').focus().select();
	}
	
	/**
	 * Delete column
	 */
	function deleteColumn(columnIndex) {
		// Delete header (add 1 for row number column)
		$('#atables-manual-table thead th').eq(columnIndex + 1).remove();
		
		// Delete cells in all rows
		$('#atables-manual-table tbody tr').each(function() {
			$(this).find('td').eq(columnIndex + 1).remove();
		});
		
		// Update column indices
		$('#atables-manual-table thead th').each(function(index) {
			if (index > 0) { // Skip row number column
				$(this).attr('data-column-index', index - 1);
			}
		});
		
		updateStats();
	}
	
	/**
	 * Update row numbers
	 */
	function updateRowNumbers() {
		$('#atables-manual-table tbody tr').each(function(index) {
			$(this).find('.atables-row-number').first().contents().filter(function() {
				return this.nodeType === 3; // Text node
			}).first().replaceWith(index + 1);
			$(this).attr('data-row-index', index);
		});
		rowCounter = $('#atables-manual-table tbody tr').length;
	}
	
	/**
	 * Update table statistics
	 */
	function updateStats() {
		const rowCount = $('#atables-manual-table tbody tr').length;
		const columnCount = $('#atables-manual-table thead th').length - 1; // Exclude row number column
		
		$('#atables-manual-row-count').text(rowCount);
		$('#atables-manual-column-count').text(columnCount);
	}
	
	/**
	 * Save manual table
	 */
	function saveManualTable() {
		const $btn = $('.atables-save-manual-table');
		const originalText = $btn.html();
		
		// Get table info
		const title = $('#manual-table-title').val().trim();
		const description = $('#manual-table-description').val().trim();
		
		if (!title) {
			showNotice('error', '<?php esc_html_e( 'Please enter a table title.', 'a-tables-charts' ); ?>');
			$('#manual-table-title').focus();
			return;
		}
		
		// Collect headers
		const headers = [];
		$('#atables-manual-table thead .atables-header-input').each(function() {
			const headerValue = $(this).val().trim();
			headers.push(headerValue || 'Column');
		});
		
		// Validate headers
		if (headers.length === 0) {
			showNotice('error', '<?php esc_html_e( 'Table must have at least one column.', 'a-tables-charts' ); ?>');
			return;
		}
		
		// Collect data
		const data = [];
		let hasData = false;
		
		$('#atables-manual-table tbody tr').each(function() {
			const row = [];
			$(this).find('.atables-cell-input').each(function() {
				const cellValue = $(this).val();
				row.push(cellValue);
				if (cellValue.trim()) {
					hasData = true;
				}
			});
			data.push(row);
		});
		
		if (!hasData) {
			if (!confirm('<?php esc_html_e( 'Your table is empty. Do you want to save it anyway?', 'a-tables-charts' ); ?>')) {
				return;
			}
		}
		
		// Disable button
		$btn.prop('disabled', true).html('<span class="dashicons dashicons-update dashicons-spin"></span> <?php esc_html_e( 'Saving...', 'a-tables-charts' ); ?>');
		
		// Prepare data
		const postData = {
			action: 'atables_save_manual_table',
			nonce: aTablesAdmin.nonce,
			title: title,
			description: description,
			headers: headers,
			data: data
		};
		
		// Send AJAX request
		$.ajax({
			url: aTablesAdmin.ajaxUrl,
			type: 'POST',
			data: postData,
			success: function(response) {
				if (response.success) {
					showNotice('success', response.message || '<?php esc_html_e( 'Table created successfully!', 'a-tables-charts' ); ?>');
					
					// Redirect after short delay
					setTimeout(function() {
						if (confirm('<?php esc_html_e( 'Table created! Would you like to view all tables?', 'a-tables-charts' ); ?>')) {
							window.location.href = 'admin.php?page=a-tables-charts';
						} else {
							// Reset form
							window.location.reload();
						}
					}, 1000);
				} else {
					showNotice('error', response.message || '<?php esc_html_e( 'Failed to create table.', 'a-tables-charts' ); ?>');
					$btn.prop('disabled', false).html(originalText);
				}
			},
			error: function(xhr, status, error) {
				console.error('Save error:', xhr, status, error);
				showNotice('error', '<?php esc_html_e( 'Failed to save table:', 'a-tables-charts' ); ?> ' + error);
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
		
		// Scroll to top to show notice
		$('html, body').animate({ scrollTop: 0 }, 300);
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

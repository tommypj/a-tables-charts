<?php
/**
 * Data Tab
 *
 * @package ATablesCharts
 */

if ( ! defined( 'ABSPATH' ) ) exit;
?>

<div class="atables-tab-header">
	<h3><?php esc_html_e( 'Table Data', 'a-tables-charts' ); ?></h3>
	<p><?php esc_html_e( 'Edit your table data directly. Add, remove, or modify rows and columns.', 'a-tables-charts' ); ?></p>
</div>

<div class="atables-data-toolbar">
	<button type="button" class="button" id="atables-add-row">
		<span class="dashicons dashicons-plus-alt"></span>
		<?php esc_html_e( 'Add Row', 'a-tables-charts' ); ?>
	</button>
	<button type="button" class="button" id="atables-add-column">
		<span class="dashicons dashicons-plus-alt"></span>
		<?php esc_html_e( 'Add Column', 'a-tables-charts' ); ?>
	</button>
	<button type="button" class="button" id="atables-import-data">
		<span class="dashicons dashicons-upload"></span>
		<?php esc_html_e( 'Import Data', 'a-tables-charts' ); ?>
	</button>
	<button type="button" class="button" id="atables-export-data">
		<span class="dashicons dashicons-download"></span>
		<?php esc_html_e( 'Export Data', 'a-tables-charts' ); ?>
	</button>
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
							   data-original="<?php echo esc_attr( $header ); ?>"
							   placeholder="Column <?php echo esc_attr( $index + 1 ); ?>">
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
							<span class="row-num"><?php echo esc_html( $row_index + 1 ); ?></span>
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
						<div class="atables-empty-state">
							<p><?php esc_html_e( 'No data available.', 'a-tables-charts' ); ?></p>
							<button type="button" class="button button-primary" id="atables-add-first-row">
								<span class="dashicons dashicons-plus-alt"></span>
								<?php esc_html_e( 'Add First Row', 'a-tables-charts' ); ?>
							</button>
						</div>
					</td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>
</div>

<div class="atables-table-stats">
	<span class="atables-stat-item">
		<strong><?php esc_html_e( 'Rows:', 'a-tables-charts' ); ?></strong>
		<span id="atables-row-count"><?php echo esc_html( count( $data ) ); ?></span>
	</span>
	<span class="atables-stat-item">
		<strong><?php esc_html_e( 'Columns:', 'a-tables-charts' ); ?></strong>
		<span id="atables-column-count"><?php echo esc_html( count( $headers ) ); ?></span>
	</span>
	<span class="atables-stat-item">
		<strong><?php esc_html_e( 'Total Cells:', 'a-tables-charts' ); ?></strong>
		<span id="atables-cell-count"><?php echo esc_html( count( $data ) * count( $headers ) ); ?></span>
	</span>
</div>

<style>
.atables-data-toolbar {
	display: flex;
	gap: 10px;
	margin-bottom: 20px;
	padding: 15px;
	background: #f9f9f9;
	border: 1px solid #dcdcde;
	border-radius: 4px;
}

.atables-table-wrapper {
	overflow-x: auto;
	border: 1px solid #dcdcde;
	border-radius: 4px;
	margin-bottom: 15px;
}

.atables-edit-table {
	width: 100%;
	border-collapse: collapse;
	background: #fff;
}

.atables-edit-table th,
.atables-edit-table td {
	border: 1px solid #dcdcde;
	padding: 8px;
	min-width: 150px;
}

.atables-edit-table th {
	background: #f6f7f7;
	font-weight: 600;
	position: relative;
}

.atables-row-number {
	min-width: 60px !important;
	text-align: center;
	background: #f0f0f1 !important;
	font-weight: 600;
	position: relative;
}

.atables-header-input {
	width: 100%;
	border: 1px solid transparent;
	padding: 6px 8px;
	background: transparent;
	font-weight: 600;
}

.atables-header-input:focus {
	border-color: #2271b1;
	background: #fff;
	outline: none;
}

.atables-delete-column {
	position: absolute;
	top: 2px;
	right: 2px;
	background: #d63638;
	color: #fff;
	border: none;
	border-radius: 3px;
	padding: 2px 4px;
	cursor: pointer;
	opacity: 0;
	transition: opacity 0.2s;
}

.atables-edit-table th:hover .atables-delete-column {
	opacity: 1;
}

.atables-delete-column:hover {
	background: #a72a2c;
}

.atables-delete-row {
	background: #d63638;
	color: #fff;
	border: none;
	border-radius: 3px;
	padding: 2px 6px;
	cursor: pointer;
	margin-left: 5px;
	opacity: 0;
	transition: opacity 0.2s;
}

.atables-edit-table tr:hover .atables-delete-row {
	opacity: 1;
}

.atables-delete-row:hover {
	background: #a72a2c;
}

.atables-cell-input {
	width: 100%;
	border: 1px solid transparent;
	padding: 6px 8px;
	background: transparent;
}

.atables-cell-input:focus {
	border-color: #2271b1;
	background: #fff;
	outline: none;
}

.atables-no-data td {
	text-align: center;
	padding: 40px 20px !important;
}

.atables-empty-state p {
	margin-bottom: 15px;
	color: #646970;
}

.atables-table-stats {
	display: flex;
	gap: 30px;
	padding: 12px 15px;
	background: #f9f9f9;
	border: 1px solid #dcdcde;
	border-radius: 4px;
	font-size: 13px;
}

.atables-stat-item strong {
	color: #1d2327;
	margin-right: 5px;
}

.atables-stat-item span {
	color: #2271b1;
	font-weight: 600;
}
</style>

<script>
jQuery(document).ready(function($) {
	let rowCounter = <?php echo count( $data ); ?>;
	
	// Add row
	$('#atables-add-row, #atables-add-first-row').on('click', function() {
		addRow();
	});
	
	// Add column
	$('#atables-add-column').on('click', function() {
		addColumn();
	});
	
	// Delete row
	$(document).on('click', '.atables-delete-row', function() {
		if (confirm('<?php esc_html_e( 'Delete this row?', 'a-tables-charts' ); ?>')) {
			$(this).closest('tr').fadeOut(300, function() {
				$(this).remove();
				updateRowNumbers();
				updateStats();
			});
		}
	});
	
	// Delete column
	$(document).on('click', '.atables-delete-column', function() {
		if (confirm('<?php esc_html_e( 'Delete this column?', 'a-tables-charts' ); ?>')) {
			const columnIndex = $(this).closest('th').data('column-index');
			deleteColumn(columnIndex);
		}
	});
	
	function addRow() {
		const $table = $('#atables-editable-table tbody');
		const columnCount = $('#atables-editable-table thead th').length - 1;
		
		$table.find('.atables-no-data').remove();
		
		rowCounter++;
		let html = '<tr data-row-index="' + rowCounter + '">';
		html += '<td class="atables-row-number">';
		html += '<span class="row-num">' + rowCounter + '</span>';
		html += '<button type="button" class="atables-delete-row" title="Delete Row"><span class="dashicons dashicons-trash"></span></button>';
		html += '</td>';
		
		for (let i = 0; i < columnCount; i++) {
			html += '<td><input type="text" class="atables-cell-input" value=""></td>';
		}
		html += '</tr>';
		
		$table.append(html);
		updateStats();
	}
	
	function addColumn() {
		const columnName = prompt('<?php esc_html_e( 'Enter column name:', 'a-tables-charts' ); ?>', '');
		if (!columnName) return;
		
		const columnIndex = $('#atables-editable-table thead th').length - 1;
		
		const headerHtml = '<th data-column-index="' + columnIndex + '">' +
			'<input type="text" class="atables-header-input" value="' + escapeHtml(columnName) + '" data-original="' + escapeHtml(columnName) + '">' +
			'<button type="button" class="atables-delete-column" title="Delete Column"><span class="dashicons dashicons-no"></span></button>' +
			'</th>';
		$('#atables-editable-table thead tr').append(headerHtml);
		
		$('#atables-editable-table tbody tr').each(function() {
			if (!$(this).hasClass('atables-no-data')) {
				$(this).append('<td><input type="text" class="atables-cell-input" value=""></td>');
			}
		});
		
		updateStats();
	}
	
	function deleteColumn(columnIndex) {
		$('#atables-editable-table thead th').eq(columnIndex + 1).remove();
		$('#atables-editable-table tbody tr').each(function() {
			$(this).find('td').eq(columnIndex + 1).remove();
		});
		updateStats();
	}
	
	function updateRowNumbers() {
		$('#atables-editable-table tbody tr').each(function(index) {
			$(this).find('.row-num').text(index + 1);
			$(this).attr('data-row-index', index);
		});
		rowCounter = $('#atables-editable-table tbody tr').length;
	}
	
	function updateStats() {
		const rowCount = $('#atables-editable-table tbody tr:not(.atables-no-data)').length;
		const columnCount = $('#atables-editable-table thead th').length - 1;
		
		$('#atables-row-count').text(rowCount);
		$('#atables-column-count').text(columnCount);
		$('#atables-cell-count').text(rowCount * columnCount);
	}
	
	function escapeHtml(text) {
		const div = document.createElement('div');
		div.textContent = text;
		return div.innerHTML;
	}
});
</script>

<?php
/**
 * Cell Merging Tab
 *
 * @package ATablesCharts
 */

if ( ! defined( 'ABSPATH' ) ) exit;
?>

<div class="atables-tab-header">
	<h3><?php esc_html_e( 'Cell Merging', 'a-tables-charts' ); ?></h3>
	<p><?php esc_html_e( 'Merge cells to create complex table layouts.', 'a-tables-charts' ); ?></p>
</div>

<div class="atables-merge-toolbar">
	<button type="button" class="button button-primary" id="atables-add-merge">
		<span class="dashicons dashicons-plus-alt"></span>
		<?php esc_html_e( 'Add Cell Merge', 'a-tables-charts' ); ?>
	</button>
	
	<button type="button" class="button" id="atables-auto-merge">
		<span class="dashicons dashicons-image-flip-horizontal"></span>
		<?php esc_html_e( 'Auto-Merge Identical', 'a-tables-charts' ); ?>
	</button>
</div>

<!-- Cell Merges List -->
<div id="cell-merges-list" class="atables-merges-container">
	<?php if ( ! empty( $cell_merges ) ) : ?>
		<?php foreach ( $cell_merges as $index => $merge ) : ?>
		<div class="atables-merge-item" data-index="<?php echo esc_attr( $index ); ?>" data-merge='<?php echo esc_attr( wp_json_encode( $merge ) ); ?>'>
			<div class="atables-merge-info">
				<div class="atables-merge-icon">🔗</div>
				<div class="atables-merge-details">
					<div class="atables-merge-range">
						<strong><?php esc_html_e( 'Start:', 'a-tables-charts' ); ?></strong>
						<?php printf( esc_html__( 'Row %d, Column %d', 'a-tables-charts' ), $merge['start_row'], $merge['start_col'] ); ?>
					</div>
					<div class="atables-merge-span">
						<strong><?php esc_html_e( 'Span:', 'a-tables-charts' ); ?></strong>
						<?php printf( esc_html__( '%d rows × %d columns', 'a-tables-charts' ), $merge['row_span'], $merge['col_span'] ); ?>
					</div>
				</div>
			</div>
			<div class="atables-merge-actions">
				<button type="button" class="button button-small atables-delete-merge" data-index="<?php echo esc_attr( $index ); ?>">
					<span class="dashicons dashicons-trash"></span>
					<?php esc_html_e( 'Delete', 'a-tables-charts' ); ?>
				</button>
			</div>
		</div>
		<?php endforeach; ?>
	<?php else : ?>
		<div class="atables-empty-state">
			<p><?php esc_html_e( 'No cell merges yet.', 'a-tables-charts' ); ?></p>
			<p class="description"><?php esc_html_e( 'Click "Add Cell Merge" to combine cells.', 'a-tables-charts' ); ?></p>
		</div>
	<?php endif; ?>
</div>

<!-- Merge Modal -->
<div id="atables-merge-modal" class="atables-modal" style="display:none;">
	<div class="atables-modal-overlay"></div>
	<div class="atables-modal-content">
		<div class="atables-modal-header">
			<h3><?php esc_html_e( 'Add Cell Merge', 'a-tables-charts' ); ?></h3>
			<button type="button" class="atables-modal-close">&times;</button>
		</div>
		
		<div class="atables-modal-body">
			<form id="atables-merge-form">
				<div class="atables-form-row">
					<label><?php esc_html_e( 'Start Position', 'a-tables-charts' ); ?> <span class="required">*</span></label>
					<div class="atables-merge-inputs">
						<div class="atables-input-group">
							<label for="merge-start-row"><?php esc_html_e( 'Row', 'a-tables-charts' ); ?></label>
							<input type="number" id="merge-start-row" min="0" required placeholder="0">
						</div>
						<div class="atables-input-group">
							<label for="merge-start-col"><?php esc_html_e( 'Column', 'a-tables-charts' ); ?></label>
							<input type="number" id="merge-start-col" min="0" required placeholder="0">
						</div>
					</div>
					<p class="description"><?php esc_html_e( 'Zero-based indices. Row 0 = first row, Column 0 = first column.', 'a-tables-charts' ); ?></p>
				</div>
				
				<div class="atables-form-row">
					<label><?php esc_html_e( 'Merge Span', 'a-tables-charts' ); ?> <span class="required">*</span></label>
					<div class="atables-merge-inputs">
						<div class="atables-input-group">
							<label for="merge-row-span"><?php esc_html_e( 'Row Span', 'a-tables-charts' ); ?></label>
							<input type="number" id="merge-row-span" min="1" required placeholder="1" value="1">
						</div>
						<div class="atables-input-group">
							<label for="merge-col-span"><?php esc_html_e( 'Column Span', 'a-tables-charts' ); ?></label>
							<input type="number" id="merge-col-span" min="1" required placeholder="1" value="1">
						</div>
					</div>
					<p class="description"><?php esc_html_e( 'How many rows/columns to merge. Minimum 1 for each.', 'a-tables-charts' ); ?></p>
				</div>
			</form>
		</div>
		
		<div class="atables-modal-footer">
			<button type="button" class="button" id="atables-merge-cancel"><?php esc_html_e( 'Cancel', 'a-tables-charts' ); ?></button>
			<button type="button" class="button button-primary" id="atables-merge-save"><?php esc_html_e( 'Add Merge', 'a-tables-charts' ); ?></button>
		</div>
	</div>
</div>

<style>
.atables-merge-toolbar {
	display: flex;
	gap: 10px;
	margin-bottom: 20px;
	padding: 15px;
	background: #f9f9f9;
	border: 1px solid #dcdcde;
	border-radius: 4px;
}

.atables-merges-container {
	min-height: 200px;
}

.atables-merge-item {
	background: #fff;
	border: 1px solid #dcdcde;
	border-radius: 4px;
	padding: 15px;
	margin-bottom: 10px;
	display: flex;
	align-items: center;
	justify-content: space-between;
	transition: all 0.2s ease;
}

.atables-merge-item:hover {
	box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
	border-color: #2271b1;
}

.atables-merge-info {
	flex: 1;
	display: flex;
	align-items: center;
	gap: 15px;
}

.atables-merge-icon {
	font-size: 28px;
}

.atables-merge-details {
	flex: 1;
}

.atables-merge-range,
.atables-merge-span {
	font-size: 13px;
	margin-bottom: 4px;
}

.atables-merge-range strong,
.atables-merge-span strong {
	color: #2271b1;
}

.atables-merge-actions {
	display: flex;
	gap: 6px;
}

.atables-merge-inputs {
	display: flex;
	gap: 15px;
}

.atables-input-group {
	flex: 1;
}

.atables-input-group label {
	font-size: 12px;
	font-weight: 600;
	margin-bottom: 5px;
	display: block;
}
</style>

<script>
jQuery(document).ready(function($) {
	let cellMerges = <?php echo wp_json_encode( $cell_merges ); ?> || [];
	
	// Add Merge
	$('#atables-add-merge').on('click', function() {
		openMergeModal();
	});
	
	// Delete Merge
	$(document).on('click', '.atables-delete-merge', function() {
		if (confirm('<?php esc_html_e( 'Delete this merge?', 'a-tables-charts' ); ?>')) {
			const index = $(this).data('index');
			cellMerges.splice(index, 1);
			renderMerges();
		}
	});
	
	// Save Merge
	$('#atables-merge-save').on('click', function() {
		saveMerge();
	});
	
	// Cancel
	$('#atables-merge-cancel, .atables-modal-close').on('click', function() {
		closeMergeModal();
	});
	
	// Auto-merge
	$('#atables-auto-merge').on('click', function() {
		if (confirm('<?php esc_html_e( 'Auto-merge identical adjacent cells?', 'a-tables-charts' ); ?>')) {
			autoMerge();
		}
	});
	
	function openMergeModal() {
		$('#atables-merge-form')[0].reset();
		$('#atables-merge-modal').fadeIn(200);
	}
	
	function closeMergeModal() {
		$('#atables-merge-modal').fadeOut(200);
	}
	
	function saveMerge() {
		const startRow = parseInt($('#merge-start-row').val());
		const startCol = parseInt($('#merge-start-col').val());
		const rowSpan = parseInt($('#merge-row-span').val());
		const colSpan = parseInt($('#merge-col-span').val());
		
		if (isNaN(startRow) || isNaN(startCol) || isNaN(rowSpan) || isNaN(colSpan)) {
			alert('<?php esc_html_e( 'Please fill in all fields with valid numbers.', 'a-tables-charts' ); ?>');
			return;
		}
		
		if (rowSpan < 1 || colSpan < 1) {
			alert('<?php esc_html_e( 'Span must be at least 1.', 'a-tables-charts' ); ?>');
			return;
		}
		
		const merge = {
			start_row: startRow,
			start_col: startCol,
			row_span: rowSpan,
			col_span: colSpan
		};
		
		cellMerges.push(merge);
		renderMerges();
		closeMergeModal();
		
		if (window.ATablesNotifications) {
			window.ATablesNotifications.show('Merge added! Don\'t forget to save the table.', 'success');
		}
	}
	
	function renderMerges() {
		const $container = $('#cell-merges-list');
		$container.empty();
		
		if (cellMerges.length === 0) {
			$container.html('<div class="atables-empty-state"><p><?php esc_html_e( 'No cell merges yet.', 'a-tables-charts' ); ?></p><p class="description"><?php esc_html_e( 'Click "Add Cell Merge" to combine cells.', 'a-tables-charts' ); ?></p></div>');
			return;
		}
		
		cellMerges.forEach((merge, index) => {
			const html = `
				<div class="atables-merge-item" data-index="${index}">
					<div class="atables-merge-info">
						<div class="atables-merge-icon">🔗</div>
						<div class="atables-merge-details">
							<div class="atables-merge-range">
								<strong><?php esc_html_e( 'Start:', 'a-tables-charts' ); ?></strong>
								Row ${merge.start_row}, Column ${merge.start_col}
							</div>
							<div class="atables-merge-span">
								<strong><?php esc_html_e( 'Span:', 'a-tables-charts' ); ?></strong>
								${merge.row_span} rows × ${merge.col_span} columns
							</div>
						</div>
					</div>
					<div class="atables-merge-actions">
						<button type="button" class="button button-small atables-delete-merge" data-index="${index}">
							<span class="dashicons dashicons-trash"></span> <?php esc_html_e( 'Delete', 'a-tables-charts' ); ?>
						</button>
					</div>
				</div>
			`;
			$container.append(html);
		});
	}
	
	function autoMerge() {
		alert('<?php esc_html_e( 'Auto-merge feature coming soon!', 'a-tables-charts' ); ?>');
	}
	
	// Expose merges for saving
	$(document).on('atables:saveAll', function() {
		$(document).trigger('atables:merging:getMerges', [cellMerges]);
	});
});
</script>

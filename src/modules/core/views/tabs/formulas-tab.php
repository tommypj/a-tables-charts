<?php
/**
 * Formulas Tab
 *
 * @package ATablesCharts
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Get available functions
require_once ATABLES_PLUGIN_DIR . 'src/modules/formulas/FormulaService.php';
$formula_service = new \ATablesCharts\Formulas\Services\FormulaService();
$functions = $formula_service->get_available_functions();
$presets = $formula_service->get_formula_presets();
?>

<div class="atables-tab-header">
	<h3><?php esc_html_e( 'Formulas', 'a-tables-charts' ); ?></h3>
	<p><?php esc_html_e( 'Add spreadsheet-like calculations to your table cells.', 'a-tables-charts' ); ?></p>
</div>

<div class="atables-formula-toolbar">
	<button type="button" class="button button-primary" id="atables-add-formula">
		<span class="dashicons dashicons-plus-alt"></span>
		<?php esc_html_e( 'Add Formula', 'a-tables-charts' ); ?>
	</button>
	
	<div class="atables-formula-help">
		<button type="button" class="button button-small" id="atables-show-functions">
			<span class="dashicons dashicons-info"></span>
			<?php esc_html_e( 'Function Reference', 'a-tables-charts' ); ?>
		</button>
		<button type="button" class="button button-small" id="atables-show-cell-ref">
			<span class="dashicons dashicons-editor-help"></span>
			<?php esc_html_e( 'Cell References', 'a-tables-charts' ); ?>
		</button>
	</div>
</div>

<!-- Function Reference Card (Collapsible) -->
<div id="function-reference-card" class="atables-info-card" style="display:none;">
	<h4><?php esc_html_e( 'Available Functions', 'a-tables-charts' ); ?></h4>
	<div class="atables-function-list">
		<?php foreach ( $functions as $func_id => $func ) : ?>
		<div class="atables-function-item">
			<div class="atables-function-name"><?php echo esc_html( $func['label'] ); ?></div>
			<div class="atables-function-syntax"><?php echo esc_html( $func['syntax'] ); ?></div>
			<div class="atables-function-desc"><?php echo esc_html( $func['description'] ); ?></div>
			<div class="atables-function-example"><code><?php echo esc_html( $func['example'] ); ?></code></div>
		</div>
		<?php endforeach; ?>
	</div>
</div>

<!-- Cell Reference Guide (Collapsible) -->
<div id="cell-reference-card" class="atables-info-card" style="display:none;">
	<h4><?php esc_html_e( 'Cell Reference Guide', 'a-tables-charts' ); ?></h4>
	<div class="atables-cell-ref-examples">
		<div class="atables-ref-item">
			<code>A1</code>
			<span><?php esc_html_e( 'Single cell (Column A, Row 1)', 'a-tables-charts' ); ?></span>
		</div>
		<div class="atables-ref-item">
			<code>A1:A10</code>
			<span><?php esc_html_e( 'Cell range (Column A, Rows 1-10)', 'a-tables-charts' ); ?></span>
		</div>
		<div class="atables-ref-item">
			<code>A:A</code>
			<span><?php esc_html_e( 'Entire column A', 'a-tables-charts' ); ?></span>
		</div>
		<div class="atables-ref-item">
			<code>A1:C1</code>
			<span><?php esc_html_e( 'Row range (Columns A-C, Row 1)', 'a-tables-charts' ); ?></span>
		</div>
	</div>
	<p class="description"><?php esc_html_e( 'Columns: A, B, C... | Rows: 1, 2, 3... (1-based)', 'a-tables-charts' ); ?></p>
</div>

<!-- Formulas List -->
<div id="formulas-list" class="atables-formulas-container">
	<?php if ( ! empty( $formulas ) ) : ?>
		<?php foreach ( $formulas as $index => $formula ) : ?>
		<div class="atables-formula-item" data-index="<?php echo esc_attr( $index ); ?>" data-formula='<?php echo esc_attr( wp_json_encode( $formula ) ); ?>'>
			<div class="atables-formula-content">
				<div class="atables-formula-target">
					<span class="dashicons dashicons-location"></span>
					<strong><?php esc_html_e( 'Target:', 'a-tables-charts' ); ?></strong>
					Row <?php echo esc_html( $formula['target_row'] ); ?>, 
					Col: <?php echo esc_html( $formula['target_col'] ); ?>
				</div>
				<code class="atables-formula-code"><?php echo esc_html( $formula['formula'] ); ?></code>
			</div>
			<div class="atables-formula-actions">
				<button type="button" class="button button-small atables-test-formula" data-index="<?php echo esc_attr( $index ); ?>">
					<span class="dashicons dashicons-yes-alt"></span>
					<?php esc_html_e( 'Test', 'a-tables-charts' ); ?>
				</button>
				<button type="button" class="button button-small atables-edit-formula" data-index="<?php echo esc_attr( $index ); ?>">
					<span class="dashicons dashicons-edit"></span>
					<?php esc_html_e( 'Edit', 'a-tables-charts' ); ?>
				</button>
				<button type="button" class="button button-small atables-delete-formula" data-index="<?php echo esc_attr( $index ); ?>">
					<span class="dashicons dashicons-trash"></span>
					<?php esc_html_e( 'Delete', 'a-tables-charts' ); ?>
				</button>
			</div>
		</div>
		<?php endforeach; ?>
	<?php else : ?>
		<div class="atables-empty-state">
			<p><?php esc_html_e( 'No formulas yet.', 'a-tables-charts' ); ?></p>
			<p class="description"><?php esc_html_e( 'Click "Add Formula" to create your first calculation.', 'a-tables-charts' ); ?></p>
		</div>
	<?php endif; ?>
</div>

<!-- Formula Modal -->
<div id="atables-formula-modal" class="atables-modal" style="display:none;">
	<div class="atables-modal-overlay"></div>
	<div class="atables-modal-content atables-modal-large">
		<div class="atables-modal-header">
			<h3 id="atables-formula-modal-title"><?php esc_html_e( 'Add Formula', 'a-tables-charts' ); ?></h3>
			<button type="button" class="atables-modal-close">&times;</button>
		</div>
		
		<div class="atables-modal-body">
			<div class="atables-formula-builder">
				<div class="atables-formula-main">
					<form id="atables-formula-form">
						<input type="hidden" id="formula-index" value="">
						
						<div class="atables-form-row">
							<label><?php esc_html_e( 'Target Cell', 'a-tables-charts' ); ?> <span class="required">*</span></label>
							<div class="atables-cell-selector">
								<div class="atables-input-group">
									<label for="formula-row"><?php esc_html_e( 'Row', 'a-tables-charts' ); ?></label>
									<input type="number" id="formula-row" min="-1" placeholder="-1" required>
									<span class="description"><?php esc_html_e( '-1 = new row', 'a-tables-charts' ); ?></span>
								</div>
								<div class="atables-input-group">
									<label for="formula-col"><?php esc_html_e( 'Column', 'a-tables-charts' ); ?></label>
									<select id="formula-col" required>
										<option value=""><?php esc_html_e( '-- Select --', 'a-tables-charts' ); ?></option>
										<?php foreach ( $headers as $header ) : ?>
										<option value="<?php echo esc_attr( $header ); ?>"><?php echo esc_html( $header ); ?></option>
										<?php endforeach; ?>
									</select>
								</div>
							</div>
						</div>
						
						<div class="atables-form-row">
							<label for="formula-input"><?php esc_html_e( 'Formula', 'a-tables-charts' ); ?> <span class="required">*</span></label>
							<textarea id="formula-input" rows="4" placeholder="=SUM(A1:A10)" required></textarea>
							<p class="description"><?php esc_html_e( 'Start with = sign. Use cell references and functions.', 'a-tables-charts' ); ?></p>
						</div>
						
						<div class="atables-form-row">
							<label><?php esc_html_e( 'Quick Presets', 'a-tables-charts' ); ?></label>
							<div class="atables-preset-buttons">
								<?php foreach ( $presets as $preset_id => $preset ) : ?>
								<button type="button" class="button button-small atables-formula-preset" data-formula="<?php echo esc_attr( $preset['formula'] ); ?>">
									<?php echo esc_html( $preset['label'] ); ?>
								</button>
								<?php endforeach; ?>
							</div>
						</div>
						
						<div class="atables-form-row">
							<button type="button" class="button" id="formula-test-btn">
								<span class="dashicons dashicons-yes-alt"></span>
								<?php esc_html_e( 'Test Formula', 'a-tables-charts' ); ?>
							</button>
							<div id="formula-test-result" style="display:none;"></div>
						</div>
					</form>
				</div>
				
				<div class="atables-formula-sidebar">
					<h4><?php esc_html_e( 'Quick Reference', 'a-tables-charts' ); ?></h4>
					
					<div class="atables-sidebar-section">
						<h5><?php esc_html_e( 'Functions', 'a-tables-charts' ); ?></h5>
						<?php foreach ( array_slice( $functions, 0, 5 ) as $func ) : ?>
						<button type="button" class="atables-insert-function" data-function="<?php echo esc_attr( $func['label'] ); ?>">
							<code><?php echo esc_html( $func['label'] ); ?></code>
						</button>
						<?php endforeach; ?>
					</div>
					
					<div class="atables-sidebar-section">
						<h5><?php esc_html_e( 'Examples', 'a-tables-charts' ); ?></h5>
						<div class="atables-example-list">
							<div class="atables-example-item">
								<code>=SUM(A1:A10)</code>
								<span><?php esc_html_e( 'Sum range', 'a-tables-charts' ); ?></span>
							</div>
							<div class="atables-example-item">
								<code>=A1+B1</code>
								<span><?php esc_html_e( 'Add cells', 'a-tables-charts' ); ?></span>
							</div>
							<div class="atables-example-item">
								<code>=A1*1.10</code>
								<span><?php esc_html_e( 'Calculate %', 'a-tables-charts' ); ?></span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<div class="atables-modal-footer">
			<button type="button" class="button" id="atables-formula-cancel"><?php esc_html_e( 'Cancel', 'a-tables-charts' ); ?></button>
			<button type="button" class="button button-primary" id="atables-formula-save"><?php esc_html_e( 'Save Formula', 'a-tables-charts' ); ?></button>
		</div>
	</div>
</div>

<style>
.atables-formula-toolbar {
	display: flex;
	justify-content: space-between;
	align-items: center;
	margin-bottom: 20px;
	padding: 15px;
	background: #f9f9f9;
	border: 1px solid #dcdcde;
	border-radius: 4px;
}

.atables-formula-help {
	display: flex;
	gap: 8px;
}

.atables-info-card {
	background: #e7f3ff;
	border: 1px solid #2271b1;
	border-radius: 4px;
	padding: 20px;
	margin-bottom: 20px;
}

.atables-info-card h4 {
	margin: 0 0 15px 0;
	color: #2271b1;
}

.atables-function-list {
	display: grid;
	grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
	gap: 15px;
}

.atables-function-item {
	background: #fff;
	border: 1px solid #dcdcde;
	border-radius: 4px;
	padding: 15px;
}

.atables-function-name {
	font-weight: 600;
	color: #2271b1;
	font-family: monospace;
	font-size: 14px;
	margin-bottom: 5px;
}

.atables-function-syntax {
	font-size: 11px;
	color: #646970;
	font-family: monospace;
	margin-bottom: 8px;
}

.atables-function-desc {
	font-size: 12px;
	color: #1d2327;
	margin-bottom: 8px;
}

.atables-function-example {
	font-size: 12px;
}

.atables-function-example code {
	background: #f9f9f9;
	padding: 4px 8px;
	border-radius: 3px;
}

.atables-cell-ref-examples {
	display: flex;
	flex-direction: column;
	gap: 10px;
}

.atables-ref-item {
	display: flex;
	align-items: center;
	gap: 15px;
	padding: 10px;
	background: #fff;
	border: 1px solid #dcdcde;
	border-radius: 4px;
}

.atables-ref-item code {
	background: #2d2d2d;
	color: #f8f8f2;
	padding: 6px 12px;
	border-radius: 4px;
	font-weight: 600;
	min-width: 80px;
}

.atables-formulas-container {
	min-height: 200px;
}

.atables-formula-item {
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

.atables-formula-item:hover {
	box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
	border-color: #2271b1;
}

.atables-formula-content {
	flex: 1;
}

.atables-formula-target {
	display: flex;
	align-items: center;
	gap: 8px;
	margin-bottom: 8px;
	font-size: 13px;
	color: #646970;
}

.atables-formula-code {
	background: #2d2d2d;
	color: #f8f8f2;
	padding: 8px 12px;
	border-radius: 4px;
	font-family: 'Courier New', monospace;
	font-size: 14px;
	display: block;
}

.atables-formula-actions {
	display: flex;
	gap: 6px;
}

.atables-modal-large {
	max-width: 900px;
}

.atables-formula-builder {
	display: grid;
	grid-template-columns: 1fr 300px;
	gap: 25px;
}

.atables-cell-selector {
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
}

.atables-input-group input,
.atables-input-group select {
	width: 100%;
}

.atables-input-group .description {
	font-size: 11px;
	color: #646970;
	margin-top: 4px;
}

#formula-input {
	font-family: 'Courier New', monospace;
	font-size: 14px;
	background: #2d2d2d;
	color: #f8f8f2;
	border: 1px solid #8c8f94;
	border-radius: 4px;
	padding: 10px;
	width: 100%;
}

#formula-input:focus {
	border-color: #2271b1;
	outline: none;
	box-shadow: 0 0 0 1px #2271b1;
}

.atables-preset-buttons {
	display: flex;
	flex-wrap: wrap;
	gap: 8px;
}

#formula-test-result {
	margin-top: 10px;
	padding: 12px;
	border-radius: 4px;
	font-family: monospace;
	font-size: 14px;
}

#formula-test-result.success {
	background: #d5f4e6;
	color: #0a7340;
	border: 1px solid #0a7340;
}

#formula-test-result.error {
	background: #fce8e8;
	color: #a72a2c;
	border: 1px solid #a72a2c;
}

.atables-formula-sidebar {
	background: #f9f9f9;
	padding: 20px;
	border-radius: 4px;
	border: 1px solid #dcdcde;
}

.atables-formula-sidebar h4 {
	margin: 0 0 15px 0;
	font-size: 16px;
}

.atables-sidebar-section {
	margin-bottom: 20px;
}

.atables-sidebar-section h5 {
	margin: 0 0 10px 0;
	font-size: 13px;
	color: #646970;
	text-transform: uppercase;
	letter-spacing: 0.5px;
}

.atables-insert-function {
	display: block;
	width: 100%;
	background: #fff;
	border: 1px solid #dcdcde;
	padding: 8px 10px;
	margin-bottom: 6px;
	border-radius: 4px;
	text-align: left;
	cursor: pointer;
	transition: all 0.2s;
}

.atables-insert-function:hover {
	background: #2271b1;
	color: #fff;
	border-color: #2271b1;
}

.atables-insert-function code {
	background: transparent;
	color: inherit;
}

.atables-example-list {
	display: flex;
	flex-direction: column;
	gap: 8px;
}

.atables-example-item {
	background: #fff;
	border: 1px solid #dcdcde;
	padding: 10px;
	border-radius: 4px;
	font-size: 12px;
}

.atables-example-item code {
	display: block;
	background: #2d2d2d;
	color: #f8f8f2;
	padding: 6px 8px;
	border-radius: 3px;
	margin-bottom: 5px;
	font-size: 13px;
}

.atables-example-item span {
	color: #646970;
}

@media screen and (max-width: 782px) {
	.atables-formula-builder {
		grid-template-columns: 1fr;
	}
	
	.atables-cell-selector {
		flex-direction: column;
	}
}
</style>

<script>
jQuery(document).ready(function($) {
	let formulas = <?php echo wp_json_encode( $formulas ); ?>;
	
	// Show/Hide Reference Cards
	$('#atables-show-functions').on('click', function() {
		$('#function-reference-card').slideToggle();
	});
	
	$('#atables-show-cell-ref').on('click', function() {
		$('#cell-reference-card').slideToggle();
	});
	
	// Add Formula
	$('#atables-add-formula').on('click', function() {
		openFormulaModal();
	});
	
	// Edit Formula
	$(document).on('click', '.atables-edit-formula', function() {
		const index = $(this).data('index');
		const formula = formulas[index];
		openFormulaModal(formula, index);
	});
	
	// Delete Formula
	$(document).on('click', '.atables-delete-formula', function() {
		if (confirm('<?php esc_html_e( 'Delete this formula?', 'a-tables-charts' ); ?>')) {
			const index = $(this).data('index');
			formulas.splice(index, 1);
			renderFormulas();
		}
	});
	
	// Test Formula
	$(document).on('click', '.atables-test-formula, #formula-test-btn', function() {
		testFormula();
	});
	
	// Save Formula
	$('#atables-formula-save').on('click', function() {
		saveFormula();
	});
	
	// Cancel
	$('#atables-formula-cancel, .atables-modal-close').on('click', function() {
		closeFormulaModal();
	});
	
	// Apply Preset
	$('.atables-formula-preset').on('click', function() {
		const formula = $(this).data('formula');
		$('#formula-input').val(formula);
	});
	
	// Insert Function
	$('.atables-insert-function').on('click', function() {
		const func = $(this).data('function');
		const current = $('#formula-input').val();
		$('#formula-input').val(current + func + '()');
		$('#formula-input').focus();
	});
	
	function openFormulaModal(formula = null, index = null) {
		$('#atables-formula-form')[0].reset();
		$('#formula-index').val(index !== null ? index : '');
		$('#formula-test-result').hide();
		
		if (formula) {
			$('#atables-formula-modal-title').text('<?php esc_html_e( 'Edit Formula', 'a-tables-charts' ); ?>');
			$('#formula-row').val(formula.target_row);
			$('#formula-col').val(formula.target_col);
			$('#formula-input').val(formula.formula);
		} else {
			$('#atables-formula-modal-title').text('<?php esc_html_e( 'Add Formula', 'a-tables-charts' ); ?>');
			$('#formula-row').val(-1);
		}
		
		$('#atables-formula-modal').fadeIn(200);
	}
	
	function closeFormulaModal() {
		$('#atables-formula-modal').fadeOut(200);
	}
	
	function saveFormula() {
		const row = $('#formula-row').val();
		const col = $('#formula-col').val();
		const formulaText = $('#formula-input').val();
		const index = $('#formula-index').val();
		
		if (!col || !formulaText) {
			alert('<?php esc_html_e( 'Please fill in all required fields.', 'a-tables-charts' ); ?>');
			return;
		}
		
		const formula = {
			target_row: parseInt(row),
			target_col: col,
			formula: formulaText
		};
		
		if (index !== '') {
			formulas[parseInt(index)] = formula;
		} else {
			formulas.push(formula);
		}
		
		renderFormulas();
		closeFormulaModal();
		
		if (window.ATablesNotifications) {
			window.ATablesNotifications.show('Formula saved! Don\'t forget to save the table.', 'success');
		}
	}
	
	function testFormula() {
		const formulaText = $('#formula-input').val();
		const tableId = $('#atables-table-id').val();
		
		if (!formulaText) {
			alert('<?php esc_html_e( 'Please enter a formula.', 'a-tables-charts' ); ?>');
			return;
		}
		
		const $result = $('#formula-test-result');
		$result.removeClass('success error').html('<span class="dashicons dashicons-update dashicons-spin"></span> Testing...').show();
		
		$.ajax({
			url: aTablesAdmin.ajaxUrl,
			type: 'POST',
			data: {
				action: 'atables_calculate_formula',
				nonce: aTablesAdmin.nonce,
				table_id: tableId,
				formula: formulaText
			},
			success: function(response) {
				if (response.success) {
					$result.addClass('success').html('✓ Result: ' + response.data.result);
				} else {
					$result.addClass('error').html('✗ ' + (response.data.message || 'Error'));
				}
			},
			error: function() {
				$result.addClass('error').html('✗ Failed to test formula');
			}
		});
	}
	
	function renderFormulas() {
		const $container = $('#formulas-list');
		$container.empty();
		
		if (formulas.length === 0) {
			$container.html('<div class="atables-empty-state"><p><?php esc_html_e( 'No formulas yet.', 'a-tables-charts' ); ?></p><p class="description"><?php esc_html_e( 'Click "Add Formula" to create your first calculation.', 'a-tables-charts' ); ?></p></div>');
			return;
		}
		
		formulas.forEach((formula, index) => {
			const html = `
				<div class="atables-formula-item" data-index="${index}">
					<div class="atables-formula-content">
						<div class="atables-formula-target">
							<span class="dashicons dashicons-location"></span>
							<strong><?php esc_html_e( 'Target:', 'a-tables-charts' ); ?></strong>
							Row ${formula.target_row}, Col: ${formula.target_col}
						</div>
						<code class="atables-formula-code">${formula.formula}</code>
					</div>
					<div class="atables-formula-actions">
						<button type="button" class="button button-small atables-test-formula" data-index="${index}">
							<span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( 'Test', 'a-tables-charts' ); ?>
						</button>
						<button type="button" class="button button-small atables-edit-formula" data-index="${index}">
							<span class="dashicons dashicons-edit"></span> <?php esc_html_e( 'Edit', 'a-tables-charts' ); ?>
						</button>
						<button type="button" class="button button-small atables-delete-formula" data-index="${index}">
							<span class="dashicons dashicons-trash"></span> <?php esc_html_e( 'Delete', 'a-tables-charts' ); ?>
						</button>
					</div>
				</div>
			`;
			$container.append(html);
		});
	}
	
	// Expose formulas for saving
	$(document).on('atables:saveAll', function() {
		$(document).trigger('atables:formulas:getFormulas', [formulas]);
	});
});
</script>

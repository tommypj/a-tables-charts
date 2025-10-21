<?php
/**
 * Conditional Formatting Tab
 *
 * @package ATablesCharts
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Get operators
require_once ATABLES_PLUGIN_DIR . 'src/modules/conditional/ConditionalFormattingService.php';
$cf_service = new \ATablesCharts\Conditional\Services\ConditionalFormattingService();
$operators = $cf_service->get_available_operators();
$presets = $cf_service->get_presets();
?>

<div class="atables-tab-header">
	<h3><?php esc_html_e( 'Conditional Formatting', 'a-tables-charts' ); ?></h3>
	<p><?php esc_html_e( 'Apply visual styles to cells based on their values.', 'a-tables-charts' ); ?></p>
</div>

<div class="atables-cf-toolbar">
	<button type="button" class="button button-primary" id="atables-add-cf-rule">
		<span class="dashicons dashicons-plus-alt"></span>
		<?php esc_html_e( 'Add Rule', 'a-tables-charts' ); ?>
	</button>
	
	<div class="atables-cf-presets">
		<label><?php esc_html_e( 'Quick Presets:', 'a-tables-charts' ); ?></label>
		<?php foreach ( array_slice( $presets, 0, 4 ) as $preset_id => $preset ) : ?>
		<button type="button" class="button button-small atables-apply-preset" data-preset="<?php echo esc_attr( $preset_id ); ?>">
			<?php echo esc_html( $preset['label'] ); ?>
		</button>
		<?php endforeach; ?>
	</div>
</div>

<div id="conditional-rules-list" class="atables-rules-container">
	<?php if ( ! empty( $conditional_rules ) ) : ?>
		<?php foreach ( $conditional_rules as $index => $rule ) : ?>
		<div class="atables-rule-item" data-index="<?php echo esc_attr( $index ); ?>" data-rule='<?php echo esc_attr( wp_json_encode( $rule ) ); ?>'>
			<div class="atables-rule-summary">
				<span class="atables-rule-column"><?php echo esc_html( $rule['column'] ?? 'Unknown' ); ?></span>
				<span class="atables-rule-operator"><?php echo esc_html( $rule['operator'] ?? 'equals' ); ?></span>
				<span class="atables-rule-value"><?php echo esc_html( $rule['value'] ?? '' ); ?></span>
				<span class="atables-rule-style-preview" style="background-color: <?php echo esc_attr( $rule['background_color'] ?? 'transparent' ); ?>; color: <?php echo esc_attr( $rule['text_color'] ?? 'inherit' ); ?>; font-weight: <?php echo esc_attr( $rule['font_weight'] ?? 'normal' ); ?>;">
					<?php esc_html_e( 'Preview', 'a-tables-charts' ); ?>
				</span>
			</div>
			<div class="atables-rule-actions">
				<button type="button" class="button button-small atables-edit-cf-rule" data-index="<?php echo esc_attr( $index ); ?>">
					<span class="dashicons dashicons-edit"></span>
					<?php esc_html_e( 'Edit', 'a-tables-charts' ); ?>
				</button>
				<button type="button" class="button button-small atables-delete-cf-rule" data-index="<?php echo esc_attr( $index ); ?>">
					<span class="dashicons dashicons-trash"></span>
					<?php esc_html_e( 'Delete', 'a-tables-charts' ); ?>
				</button>
			</div>
		</div>
		<?php endforeach; ?>
	<?php else : ?>
		<div class="atables-empty-state">
			<p><?php esc_html_e( 'No conditional formatting rules yet.', 'a-tables-charts' ); ?></p>
			<p class="description"><?php esc_html_e( 'Click "Add Rule" to create your first formatting rule.', 'a-tables-charts' ); ?></p>
		</div>
	<?php endif; ?>
</div>

<!-- Conditional Formatting Modal -->
<div id="atables-cf-modal" class="atables-modal" style="display:none;">
	<div class="atables-modal-overlay"></div>
	<div class="atables-modal-content">
		<div class="atables-modal-header">
			<h3 id="atables-cf-modal-title"><?php esc_html_e( 'Add Conditional Formatting Rule', 'a-tables-charts' ); ?></h3>
			<button type="button" class="atables-modal-close">&times;</button>
		</div>
		
		<div class="atables-modal-body">
			<form id="atables-cf-form">
				<input type="hidden" id="cf-rule-index" value="">
				
				<div class="atables-form-row">
					<label for="cf-column">
						<?php esc_html_e( 'Column', 'a-tables-charts' ); ?>
						<span class="required">*</span>
					</label>
					<select id="cf-column" required>
						<option value=""><?php esc_html_e( '-- Select Column --', 'a-tables-charts' ); ?></option>
						<?php foreach ( $headers as $header ) : ?>
						<option value="<?php echo esc_attr( $header ); ?>"><?php echo esc_html( $header ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				
				<div class="atables-form-row">
					<label for="cf-operator">
						<?php esc_html_e( 'Condition', 'a-tables-charts' ); ?>
						<span class="required">*</span>
					</label>
					<select id="cf-operator" required>
						<?php foreach ( $operators as $op_value => $op_label ) : ?>
						<option value="<?php echo esc_attr( $op_value ); ?>"><?php echo esc_html( $op_label ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				
				<div class="atables-form-row">
					<label for="cf-value">
						<?php esc_html_e( 'Value', 'a-tables-charts' ); ?>
					</label>
					<input type="text" id="cf-value" placeholder="<?php esc_attr_e( 'Comparison value', 'a-tables-charts' ); ?>">
					<p class="description"><?php esc_html_e( 'Leave empty for operators like "is_empty" or "is_not_empty".', 'a-tables-charts' ); ?></p>
				</div>
				
				<div class="atables-form-row">
					<label><?php esc_html_e( 'Styling', 'a-tables-charts' ); ?></label>
					
					<div class="atables-color-picker-group">
						<div class="atables-color-input-wrapper">
							<label for="cf-bg-color"><?php esc_html_e( 'Background', 'a-tables-charts' ); ?></label>
							<input type="text" id="cf-bg-color" class="atables-color-picker" value="#ffeb3b">
						</div>
						
						<div class="atables-color-input-wrapper">
							<label for="cf-text-color"><?php esc_html_e( 'Text Color', 'a-tables-charts' ); ?></label>
							<input type="text" id="cf-text-color" class="atables-color-picker" value="#000000">
						</div>
						
						<div class="atables-color-input-wrapper">
							<label for="cf-font-weight"><?php esc_html_e( 'Font Weight', 'a-tables-charts' ); ?></label>
							<select id="cf-font-weight">
								<option value="normal"><?php esc_html_e( 'Normal', 'a-tables-charts' ); ?></option>
								<option value="bold"><?php esc_html_e( 'Bold', 'a-tables-charts' ); ?></option>
							</select>
						</div>
					</div>
				</div>
				
				<div class="atables-form-row">
					<label><?php esc_html_e( 'Preview', 'a-tables-charts' ); ?></label>
					<div id="cf-preview" class="atables-cf-preview">
						<?php esc_html_e( 'Sample Text', 'a-tables-charts' ); ?>
					</div>
				</div>
			</form>
		</div>
		
		<div class="atables-modal-footer">
			<button type="button" class="button" id="atables-cf-cancel"><?php esc_html_e( 'Cancel', 'a-tables-charts' ); ?></button>
			<button type="button" class="button button-primary" id="atables-cf-save"><?php esc_html_e( 'Save Rule', 'a-tables-charts' ); ?></button>
		</div>
	</div>
</div>

<style>
.atables-cf-toolbar {
	display: flex;
	justify-content: space-between;
	align-items: center;
	margin-bottom: 20px;
	padding: 15px;
	background: #f9f9f9;
	border: 1px solid #dcdcde;
	border-radius: 4px;
}

.atables-cf-presets {
	display: flex;
	align-items: center;
	gap: 8px;
}

.atables-cf-presets label {
	font-weight: 600;
	margin: 0;
}

.atables-rules-container {
	min-height: 200px;
}

.atables-rule-item {
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

.atables-rule-item:hover {
	box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
	border-color: #2271b1;
}

.atables-rule-summary {
	flex: 1;
	display: flex;
	align-items: center;
	gap: 12px;
	flex-wrap: wrap;
}

.atables-rule-column {
	font-weight: 600;
	color: #2271b1;
	font-size: 14px;
}

.atables-rule-operator {
	background: #e7f3ff;
	color: #2271b1;
	padding: 4px 10px;
	border-radius: 3px;
	font-size: 12px;
	font-weight: 500;
}

.atables-rule-value {
	background: #f9f9f9;
	border: 1px solid #dcdcde;
	padding: 4px 10px;
	border-radius: 3px;
	font-family: monospace;
	font-size: 13px;
}

.atables-rule-style-preview {
	padding: 6px 15px;
	border-radius: 3px;
	font-size: 13px;
	border: 1px solid rgba(0,0,0,0.1);
}

.atables-rule-actions {
	display: flex;
	gap: 6px;
}

/* Modal Styles */
.atables-modal {
	position: fixed;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;
	z-index: 100000;
}

.atables-modal-overlay {
	position: absolute;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;
	background: rgba(0, 0, 0, 0.7);
}

.atables-modal-content {
	position: relative;
	background: #fff;
	width: 90%;
	max-width: 600px;
	margin: 50px auto;
	border-radius: 8px;
	box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
	max-height: 90vh;
	overflow-y: auto;
}

.atables-modal-header {
	display: flex;
	justify-content: space-between;
	align-items: center;
	padding: 20px 25px;
	border-bottom: 1px solid #dcdcde;
}

.atables-modal-header h3 {
	margin: 0;
	font-size: 18px;
}

.atables-modal-close {
	background: transparent;
	border: none;
	font-size: 28px;
	cursor: pointer;
	color: #646970;
	padding: 0;
	width: 30px;
	height: 30px;
	display: flex;
	align-items: center;
	justify-content: center;
}

.atables-modal-close:hover {
	color: #1d2327;
}

.atables-modal-body {
	padding: 25px;
}

.atables-modal-footer {
	padding: 15px 25px;
	border-top: 1px solid #dcdcde;
	display: flex;
	justify-content: flex-end;
	gap: 10px;
}

.atables-form-row {
	margin-bottom: 20px;
}

.atables-form-row label {
	display: block;
	font-weight: 600;
	margin-bottom: 8px;
	color: #1d2327;
}

.atables-form-row input[type="text"],
.atables-form-row select {
	width: 100%;
	padding: 8px 12px;
	border: 1px solid #8c8f94;
	border-radius: 4px;
	font-size: 14px;
}

.atables-form-row input[type="text"]:focus,
.atables-form-row select:focus {
	border-color: #2271b1;
	outline: none;
	box-shadow: 0 0 0 1px #2271b1;
}

.atables-color-picker-group {
	display: flex;
	gap: 20px;
	flex-wrap: wrap;
}

.atables-color-input-wrapper {
	flex: 1;
	min-width: 150px;
}

.atables-color-input-wrapper label {
	font-size: 13px;
	margin-bottom: 6px;
}

.atables-cf-preview {
	padding: 15px 20px;
	border: 2px dashed #dcdcde;
	border-radius: 4px;
	text-align: center;
	font-size: 16px;
	transition: all 0.3s ease;
}

.required {
	color: #d63638;
}
</style>

<script>
jQuery(document).ready(function($) {
	let conditionalRules = <?php echo wp_json_encode( $conditional_rules ); ?>;
	
	// Initialize color pickers
	if ($.fn.wpColorPicker) {
		$('.atables-color-picker').wpColorPicker({
			change: updatePreview,
			clear: updatePreview
		});
	}
	
	// Add Rule
	$('#atables-add-cf-rule').on('click', function() {
		openCFModal();
	});
	
	// Edit Rule
	$(document).on('click', '.atables-edit-cf-rule', function() {
		const index = $(this).data('index');
		const rule = conditionalRules[index];
		openCFModal(rule, index);
	});
	
	// Delete Rule
	$(document).on('click', '.atables-delete-cf-rule', function() {
		if (confirm('<?php esc_html_e( 'Delete this rule?', 'a-tables-charts' ); ?>')) {
			const index = $(this).data('index');
			conditionalRules.splice(index, 1);
			renderRules();
		}
	});
	
	// Save Rule
	$('#atables-cf-save').on('click', function() {
		saveCFRule();
	});
	
	// Cancel
	$('#atables-cf-cancel, .atables-modal-close').on('click', function() {
		closeCFModal();
	});
	
	// Close on overlay click
	$('.atables-modal-overlay').on('click', function() {
		closeCFModal();
	});
	
	// Update preview on input change
	$('#cf-bg-color, #cf-text-color, #cf-font-weight').on('change', updatePreview);
	
	// Apply Preset
	$('.atables-apply-preset').on('click', function() {
		const presetId = $(this).data('preset');
		applyPreset(presetId);
	});
	
	function openCFModal(rule = null, index = null) {
		// Reset form
		$('#atables-cf-form')[0].reset();
		$('#cf-rule-index').val(index !== null ? index : '');
		
		if (rule) {
			$('#atables-cf-modal-title').text('<?php esc_html_e( 'Edit Conditional Formatting Rule', 'a-tables-charts' ); ?>');
			$('#cf-column').val(rule.column);
			$('#cf-operator').val(rule.operator);
			$('#cf-value').val(rule.value || '');
			$('#cf-bg-color').val(rule.background_color || '#ffeb3b').trigger('change');
			$('#cf-text-color').val(rule.text_color || '#000000').trigger('change');
			$('#cf-font-weight').val(rule.font_weight || 'normal');
		} else {
			$('#atables-cf-modal-title').text('<?php esc_html_e( 'Add Conditional Formatting Rule', 'a-tables-charts' ); ?>');
			$('#cf-bg-color').val('#ffeb3b').trigger('change');
			$('#cf-text-color').val('#000000').trigger('change');
		}
		
		updatePreview();
		$('#atables-cf-modal').fadeIn(200);
	}
	
	function closeCFModal() {
		$('#atables-cf-modal').fadeOut(200);
	}
	
	function saveCFRule() {
		const column = $('#cf-column').val();
		const operator = $('#cf-operator').val();
		const value = $('#cf-value').val();
		const bgColor = $('#cf-bg-color').val();
		const textColor = $('#cf-text-color').val();
		const fontWeight = $('#cf-font-weight').val();
		const index = $('#cf-rule-index').val();
		
		if (!column || !operator) {
			alert('<?php esc_html_e( 'Please fill in all required fields.', 'a-tables-charts' ); ?>');
			return;
		}
		
		const rule = {
			column: column,
			operator: operator,
			value: value,
			background_color: bgColor,
			text_color: textColor,
			font_weight: fontWeight
		};
		
		if (index !== '') {
			// Edit existing rule
			conditionalRules[parseInt(index)] = rule;
		} else {
			// Add new rule
			conditionalRules.push(rule);
		}
		
		renderRules();
		closeCFModal();
		
		if (window.ATablesNotifications) {
			window.ATablesNotifications.show('Rule saved! Don\'t forget to save the table.', 'success');
		}
	}
	
	function updatePreview() {
		const bgColor = $('#cf-bg-color').val();
		const textColor = $('#cf-text-color').val();
		const fontWeight = $('#cf-font-weight').val();
		
		$('#cf-preview').css({
			'background-color': bgColor,
			'color': textColor,
			'font-weight': fontWeight
		});
	}
	
	function renderRules() {
		const $container = $('#conditional-rules-list');
		$container.empty();
		
		if (conditionalRules.length === 0) {
			$container.html('<div class="atables-empty-state"><p><?php esc_html_e( 'No conditional formatting rules yet.', 'a-tables-charts' ); ?></p><p class="description"><?php esc_html_e( 'Click "Add Rule" to create your first formatting rule.', 'a-tables-charts' ); ?></p></div>');
			return;
		}
		
		conditionalRules.forEach((rule, index) => {
			const html = `
				<div class="atables-rule-item" data-index="${index}">
					<div class="atables-rule-summary">
						<span class="atables-rule-column">${rule.column}</span>
						<span class="atables-rule-operator">${rule.operator}</span>
						<span class="atables-rule-value">${rule.value || ''}</span>
						<span class="atables-rule-style-preview" style="background-color: ${rule.background_color}; color: ${rule.text_color}; font-weight: ${rule.font_weight};">
							<?php esc_html_e( 'Preview', 'a-tables-charts' ); ?>
						</span>
					</div>
					<div class="atables-rule-actions">
						<button type="button" class="button button-small atables-edit-cf-rule" data-index="${index}">
							<span class="dashicons dashicons-edit"></span> <?php esc_html_e( 'Edit', 'a-tables-charts' ); ?>
						</button>
						<button type="button" class="button button-small atables-delete-cf-rule" data-index="${index}">
							<span class="dashicons dashicons-trash"></span> <?php esc_html_e( 'Delete', 'a-tables-charts' ); ?>
						</button>
					</div>
				</div>
			`;
			$container.append(html);
		});
	}
	
	function applyPreset(presetId) {
		// This would load preset configuration from server
		alert('Preset: ' + presetId + ' - This will apply pre-configured rules');
	}
	
	// Expose rules for saving
	$(document).on('atables:saveAll', function() {
		$(document).trigger('atables:cf:getRules', [conditionalRules]);
	});
});
</script>

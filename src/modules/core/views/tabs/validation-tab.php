<?php
/**
 * Validation Tab
 *
 * @package ATablesCharts
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Get validation presets
require_once ATABLES_PLUGIN_DIR . 'src/modules/validation/ValidationService.php';
$validation_service = new \ATablesCharts\Validation\Services\ValidationService();
$rule_types = $validation_service->get_available_rules();
$presets = $validation_service->get_presets();
?>

<div class="atables-tab-header">
	<h3><?php esc_html_e( 'Data Validation', 'a-tables-charts' ); ?></h3>
	<p><?php esc_html_e( 'Set rules to ensure data quality and consistency.', 'a-tables-charts' ); ?></p>
</div>

<div class="atables-validation-toolbar">
	<button type="button" class="button button-primary" id="atables-add-validation">
		<span class="dashicons dashicons-plus-alt"></span>
		<?php esc_html_e( 'Add Validation Rule', 'a-tables-charts' ); ?>
	</button>
	
	<div class="atables-validation-presets">
		<label><?php esc_html_e( 'Quick Presets:', 'a-tables-charts' ); ?></label>
		<?php foreach ( array_slice( $presets, 0, 3 ) as $preset_id => $preset ) : ?>
		<button type="button" class="button button-small atables-apply-validation-preset" data-preset="<?php echo esc_attr( $preset_id ); ?>">
			<?php echo esc_html( $preset['label'] ); ?>
		</button>
		<?php endforeach; ?>
	</div>
</div>

<!-- Validation Rules List -->
<div id="validation-rules-list" class="atables-validation-container">
	<?php if ( ! empty( $validation_rules ) && is_array( $validation_rules ) ) : ?>
		<?php foreach ( $validation_rules as $column => $rules ) : ?>
		<div class="atables-validation-item" data-column="<?php echo esc_attr( $column ); ?>" data-rules='<?php echo esc_attr( wp_json_encode( $rules ) ); ?>'>
			<div class="atables-validation-summary">
				<div class="atables-validation-column">
					<span class="dashicons dashicons-yes-alt"></span>
					<strong><?php echo esc_html( $column ); ?></strong>
				</div>
				<div class="atables-validation-rules-display">
					<?php
					$rule_texts = array();
					foreach ( $rules as $rule_type => $rule_value ) {
						if ( $rule_value === true ) {
							$rule_texts[] = ucfirst( str_replace( '_', ' ', $rule_type ) );
						} elseif ( is_array( $rule_value ) ) {
							$rule_texts[] = ucfirst( str_replace( '_', ' ', $rule_type ) ) . ': ' . implode( ', ', $rule_value );
						} else {
							$rule_texts[] = ucfirst( str_replace( '_', ' ', $rule_type ) ) . ': ' . $rule_value;
						}
					}
					echo esc_html( implode( ' | ', $rule_texts ) );
					?>
				</div>
			</div>
			<div class="atables-validation-actions">
				<button type="button" class="button button-small atables-edit-validation" data-column="<?php echo esc_attr( $column ); ?>">
					<span class="dashicons dashicons-edit"></span>
					<?php esc_html_e( 'Edit', 'a-tables-charts' ); ?>
				</button>
				<button type="button" class="button button-small atables-delete-validation" data-column="<?php echo esc_attr( $column ); ?>">
					<span class="dashicons dashicons-trash"></span>
					<?php esc_html_e( 'Delete', 'a-tables-charts' ); ?>
				</button>
			</div>
		</div>
		<?php endforeach; ?>
	<?php else : ?>
		<div class="atables-empty-state">
			<p><?php esc_html_e( 'No validation rules yet.', 'a-tables-charts' ); ?></p>
			<p class="description"><?php esc_html_e( 'Click "Add Validation Rule" to ensure data quality.', 'a-tables-charts' ); ?></p>
		</div>
	<?php endif; ?>
</div>

<!-- Validation Modal -->
<div id="atables-validation-modal" class="atables-modal" style="display:none;">
	<div class="atables-modal-overlay"></div>
	<div class="atables-modal-content">
		<div class="atables-modal-header">
			<h3 id="atables-validation-modal-title"><?php esc_html_e( 'Add Validation Rule', 'a-tables-charts' ); ?></h3>
			<button type="button" class="atables-modal-close">&times;</button>
		</div>
		
		<div class="atables-modal-body">
			<form id="atables-validation-form">
				<input type="hidden" id="validation-original-column" value="">
				
				<div class="atables-form-row">
					<label for="validation-column">
						<?php esc_html_e( 'Column', 'a-tables-charts' ); ?>
						<span class="required">*</span>
					</label>
					<select id="validation-column" required>
						<option value=""><?php esc_html_e( '-- Select Column --', 'a-tables-charts' ); ?></option>
						<?php foreach ( $headers as $header ) : ?>
						<option value="<?php echo esc_attr( $header ); ?>"><?php echo esc_html( $header ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				
				<div class="atables-form-row">
					<label><?php esc_html_e( 'Validation Rules', 'a-tables-charts' ); ?></label>
					
					<div class="atables-validation-rules-builder">
						<label class="atables-validation-rule-option">
							<input type="checkbox" name="required" id="rule-required">
							<span><?php esc_html_e( 'Required (cannot be empty)', 'a-tables-charts' ); ?></span>
						</label>
						
						<label class="atables-validation-rule-option">
							<input type="checkbox" name="type" id="rule-type">
							<span><?php esc_html_e( 'Type Validation', 'a-tables-charts' ); ?></span>
						</label>
						<div id="type-config" class="atables-rule-config" style="display:none;">
							<select name="type_value" id="type-value">
								<option value="email"><?php esc_html_e( 'Email', 'a-tables-charts' ); ?></option>
								<option value="url"><?php esc_html_e( 'URL', 'a-tables-charts' ); ?></option>
								<option value="number"><?php esc_html_e( 'Number', 'a-tables-charts' ); ?></option>
								<option value="integer"><?php esc_html_e( 'Integer', 'a-tables-charts' ); ?></option>
								<option value="alpha"><?php esc_html_e( 'Alphabetic', 'a-tables-charts' ); ?></option>
								<option value="alphanumeric"><?php esc_html_e( 'Alphanumeric', 'a-tables-charts' ); ?></option>
							</select>
						</div>
						
						<label class="atables-validation-rule-option">
							<input type="checkbox" name="min" id="rule-min">
							<span><?php esc_html_e( 'Minimum Value', 'a-tables-charts' ); ?></span>
						</label>
						<div id="min-config" class="atables-rule-config" style="display:none;">
							<input type="number" name="min_value" id="min-value" placeholder="<?php esc_attr_e( 'Minimum value', 'a-tables-charts' ); ?>">
						</div>
						
						<label class="atables-validation-rule-option">
							<input type="checkbox" name="max" id="rule-max">
							<span><?php esc_html_e( 'Maximum Value', 'a-tables-charts' ); ?></span>
						</label>
						<div id="max-config" class="atables-rule-config" style="display:none;">
							<input type="number" name="max_value" id="max-value" placeholder="<?php esc_attr_e( 'Maximum value', 'a-tables-charts' ); ?>">
						</div>
						
						<label class="atables-validation-rule-option">
							<input type="checkbox" name="min_length" id="rule-min-length">
							<span><?php esc_html_e( 'Minimum Length', 'a-tables-charts' ); ?></span>
						</label>
						<div id="min-length-config" class="atables-rule-config" style="display:none;">
							<input type="number" name="min_length_value" id="min-length-value" placeholder="<?php esc_attr_e( 'Minimum characters', 'a-tables-charts' ); ?>">
						</div>
						
						<label class="atables-validation-rule-option">
							<input type="checkbox" name="max_length" id="rule-max-length">
							<span><?php esc_html_e( 'Maximum Length', 'a-tables-charts' ); ?></span>
						</label>
						<div id="max-length-config" class="atables-rule-config" style="display:none;">
							<input type="number" name="max_length_value" id="max-length-value" placeholder="<?php esc_attr_e( 'Maximum characters', 'a-tables-charts' ); ?>">
						</div>
						
						<label class="atables-validation-rule-option">
							<input type="checkbox" name="unique" id="rule-unique">
							<span><?php esc_html_e( 'Unique (no duplicates)', 'a-tables-charts' ); ?></span>
						</label>
					</div>
				</div>
				
				<div class="atables-form-row">
					<label><?php esc_html_e( 'Quick Presets', 'a-tables-charts' ); ?></label>
					<div class="atables-preset-buttons">
						<?php foreach ( $presets as $preset_id => $preset ) : ?>
						<button type="button" class="button button-small atables-validation-preset-btn" data-preset="<?php echo esc_attr( $preset_id ); ?>">
							<?php echo esc_html( $preset['label'] ); ?>
						</button>
						<?php endforeach; ?>
					</div>
				</div>
			</form>
		</div>
		
		<div class="atables-modal-footer">
			<button type="button" class="button" id="atables-validation-cancel"><?php esc_html_e( 'Cancel', 'a-tables-charts' ); ?></button>
			<button type="button" class="button button-primary" id="atables-validation-save"><?php esc_html_e( 'Save Rule', 'a-tables-charts' ); ?></button>
		</div>
	</div>
</div>

<style>
.atables-validation-toolbar {
	display: flex;
	justify-content: space-between;
	align-items: center;
	margin-bottom: 20px;
	padding: 15px;
	background: #f9f9f9;
	border: 1px solid #dcdcde;
	border-radius: 4px;
}

.atables-validation-presets {
	display: flex;
	align-items: center;
	gap: 8px;
}

.atables-validation-presets label {
	font-weight: 600;
	margin: 0;
}

.atables-validation-container {
	min-height: 200px;
}

.atables-validation-item {
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

.atables-validation-item:hover {
	box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
	border-color: #2271b1;
}

.atables-validation-summary {
	flex: 1;
}

.atables-validation-column {
	display: flex;
	align-items: center;
	gap: 8px;
	margin-bottom: 8px;
}

.atables-validation-column strong {
	color: #2271b1;
	font-size: 15px;
}

.atables-validation-rules-display {
	color: #646970;
	font-size: 13px;
}

.atables-validation-actions {
	display: flex;
	gap: 6px;
}

.atables-validation-rules-builder {
	display: flex;
	flex-direction: column;
	gap: 12px;
	background: #f9f9f9;
	padding: 15px;
	border: 1px solid #dcdcde;
	border-radius: 4px;
}

.atables-validation-rule-option {
	display: flex;
	align-items: center;
	gap: 10px;
	cursor: pointer;
	font-weight: 500;
}

.atables-validation-rule-option input[type="checkbox"] {
	margin: 0;
}

.atables-rule-config {
	margin-left: 30px;
	margin-top: -8px;
}

.atables-rule-config input,
.atables-rule-config select {
	width: 100%;
	max-width: 300px;
}

.atables-preset-buttons {
	display: flex;
	flex-wrap: wrap;
	gap: 8px;
}
</style>

<script>
jQuery(document).ready(function($) {
	let validationRules = <?php echo wp_json_encode( $validation_rules ); ?> || {};
	
	// Show/hide rule configs
	$('.atables-validation-rule-option input[type="checkbox"]').on('change', function() {
		const ruleName = $(this).attr('name');
		const configDiv = $('#' + ruleName + '-config');
		if ($(this).is(':checked')) {
			configDiv.slideDown();
		} else {
			configDiv.slideUp();
		}
	});
	
	// Add Validation
	$('#atables-add-validation').on('click', function() {
		openValidationModal();
	});
	
	// Edit Validation
	$(document).on('click', '.atables-edit-validation', function() {
		const column = $(this).data('column');
		const rules = validationRules[column];
		openValidationModal(column, rules);
	});
	
	// Delete Validation
	$(document).on('click', '.atables-delete-validation', function() {
		if (confirm('<?php esc_html_e( 'Delete validation rules for this column?', 'a-tables-charts' ); ?>')) {
			const column = $(this).data('column');
			delete validationRules[column];
			renderValidation();
		}
	});
	
	// Save Validation
	$('#atables-validation-save').on('click', function() {
		saveValidation();
	});
	
	// Cancel
	$('#atables-validation-cancel, .atables-modal-close').on('click', function() {
		closeValidationModal();
	});
	
	// Apply Preset
	$('.atables-validation-preset-btn').on('click', function() {
		const presetId = $(this).data('preset');
		applyValidationPreset(presetId);
	});
	
	function openValidationModal(column = null, rules = null) {
		// Reset form
		$('#atables-validation-form')[0].reset();
		$('.atables-rule-config').hide();
		$('#validation-original-column').val(column || '');
		
		if (column && rules) {
			$('#atables-validation-modal-title').text('<?php esc_html_e( 'Edit Validation Rule', 'a-tables-charts' ); ?>');
			$('#validation-column').val(column);
			
			// Populate rules
			Object.keys(rules).forEach(ruleType => {
				$('#rule-' + ruleType).prop('checked', true).trigger('change');
				
				if (typeof rules[ruleType] !== 'boolean') {
					$('#' + ruleType + '-value').val(rules[ruleType]);
				}
			});
		} else {
			$('#atables-validation-modal-title').text('<?php esc_html_e( 'Add Validation Rule', 'a-tables-charts' ); ?>');
		}
		
		$('#atables-validation-modal').fadeIn(200);
	}
	
	function closeValidationModal() {
		$('#atables-validation-modal').fadeOut(200);
	}
	
	function saveValidation() {
		const column = $('#validation-column').val();
		const originalColumn = $('#validation-original-column').val();
		
		if (!column) {
			alert('<?php esc_html_e( 'Please select a column.', 'a-tables-charts' ); ?>');
			return;
		}
		
		const rules = {};
		
		// Collect checked rules
		$('.atables-validation-rule-option input[type="checkbox"]:checked').each(function() {
			const ruleName = $(this).attr('name');
			const configInput = $('#' + ruleName + '-value');
			
			if (configInput.length && configInput.val()) {
				rules[ruleName] = configInput.val();
			} else {
				rules[ruleName] = true;
			}
		});
		
		if (Object.keys(rules).length === 0) {
			alert('<?php esc_html_e( 'Please select at least one validation rule.', 'a-tables-charts' ); ?>');
			return;
		}
		
		// Delete old entry if column changed
		if (originalColumn && originalColumn !== column) {
			delete validationRules[originalColumn];
		}
		
		validationRules[column] = rules;
		renderValidation();
		closeValidationModal();
		
		if (window.ATablesNotifications) {
			window.ATablesNotifications.show('Validation rules saved! Don\'t forget to save the table.', 'success');
		}
	}
	
	function renderValidation() {
		const $container = $('#validation-rules-list');
		$container.empty();
		
		if (Object.keys(validationRules).length === 0) {
			$container.html('<div class="atables-empty-state"><p><?php esc_html_e( 'No validation rules yet.', 'a-tables-charts' ); ?></p><p class="description"><?php esc_html_e( 'Click "Add Validation Rule" to ensure data quality.', 'a-tables-charts' ); ?></p></div>');
			return;
		}
		
		Object.keys(validationRules).forEach(column => {
			const rules = validationRules[column];
			const ruleTexts = [];
			
			Object.keys(rules).forEach(ruleType => {
				if (rules[ruleType] === true) {
					ruleTexts.push(ruleType.replace(/_/g, ' '));
				} else {
					ruleTexts.push(ruleType.replace(/_/g, ' ') + ': ' + rules[ruleType]);
				}
			});
			
			const html = `
				<div class="atables-validation-item" data-column="${column}">
					<div class="atables-validation-summary">
						<div class="atables-validation-column">
							<span class="dashicons dashicons-yes-alt"></span>
							<strong>${column}</strong>
						</div>
						<div class="atables-validation-rules-display">
							${ruleTexts.join(' | ')}
						</div>
					</div>
					<div class="atables-validation-actions">
						<button type="button" class="button button-small atables-edit-validation" data-column="${column}">
							<span class="dashicons dashicons-edit"></span> <?php esc_html_e( 'Edit', 'a-tables-charts' ); ?>
						</button>
						<button type="button" class="button button-small atables-delete-validation" data-column="${column}">
							<span class="dashicons dashicons-trash"></span> <?php esc_html_e( 'Delete', 'a-tables-charts' ); ?>
						</button>
					</div>
				</div>
			`;
			$container.append(html);
		});
	}
	
	function applyValidationPreset(presetId) {
		alert('Applying preset: ' + presetId);
		// This would load preset configuration
	}
	
	// Expose rules for saving
	$(document).on('atables:saveAll', function() {
		$(document).trigger('atables:validation:getRules', [validationRules]);
	});
});
</script>

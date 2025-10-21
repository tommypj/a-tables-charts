<?php
/**
 * Advanced Tab
 *
 * @package ATablesCharts
 */

if ( ! defined( 'ABSPATH' ) ) exit;
?>

<div class="atables-tab-header">
	<h3><?php esc_html_e( 'Advanced Settings', 'a-tables-charts' ); ?></h3>
	<p><?php esc_html_e( 'Advanced options and power user features.', 'a-tables-charts' ); ?></p>
</div>

<!-- Sorting Configuration -->
<div class="atables-section">
	<h4>
		<span class="dashicons dashicons-sort"></span>
		<?php esc_html_e( 'Advanced Sorting', 'a-tables-charts' ); ?>
	</h4>
	<p class="description"><?php esc_html_e( 'Configure multi-column sorting and custom sort orders.', 'a-tables-charts' ); ?></p>
	
	<div class="atables-sorting-config">
		<button type="button" class="button" id="atables-config-sorting">
			<span class="dashicons dashicons-admin-settings"></span>
			<?php esc_html_e( 'Configure Sorting', 'a-tables-charts' ); ?>
		</button>
		<p class="description"><?php esc_html_e( 'Set default sort column, direction, and type.', 'a-tables-charts' ); ?></p>
	</div>
</div>

<hr>

<!-- JSON Editor -->
<div class="atables-section">
	<h4>
		<span class="dashicons dashicons-media-code"></span>
		<?php esc_html_e( 'JSON Configuration', 'a-tables-charts' ); ?>
	</h4>
	<p class="description"><?php esc_html_e( 'Advanced users: Edit raw JSON configuration.', 'a-tables-charts' ); ?></p>
	
	<div class="atables-json-editor-wrapper">
		<textarea id="atables-json-editor" rows="15" class="large-text code"></textarea>
		<div class="atables-json-actions">
			<button type="button" class="button" id="atables-format-json">
				<span class="dashicons dashicons-editor-alignleft"></span>
				<?php esc_html_e( 'Format JSON', 'a-tables-charts' ); ?>
			</button>
			<button type="button" class="button" id="atables-validate-json">
				<span class="dashicons dashicons-yes-alt"></span>
				<?php esc_html_e( 'Validate JSON', 'a-tables-charts' ); ?>
			</button>
			<button type="button" class="button button-primary" id="atables-apply-json">
				<span class="dashicons dashicons-admin-settings"></span>
				<?php esc_html_e( 'Apply Configuration', 'a-tables-charts' ); ?>
			</button>
		</div>
		<div id="json-validation-result"></div>
	</div>
</div>

<hr>

<!-- Import/Export Settings -->
<div class="atables-section">
	<h4>
		<span class="dashicons dashicons-migrate"></span>
		<?php esc_html_e( 'Import/Export Settings', 'a-tables-charts' ); ?>
	</h4>
	<p class="description"><?php esc_html_e( 'Save and restore table settings across installations.', 'a-tables-charts' ); ?></p>
	
	<div class="atables-import-export">
		<button type="button" class="button" id="atables-export-settings">
			<span class="dashicons dashicons-download"></span>
			<?php esc_html_e( 'Export Settings', 'a-tables-charts' ); ?>
		</button>
		<button type="button" class="button" id="atables-import-settings">
			<span class="dashicons dashicons-upload"></span>
			<?php esc_html_e( 'Import Settings', 'a-tables-charts' ); ?>
		</button>
		<input type="file" id="import-file-input" accept=".json" style="display:none;">
	</div>
</div>

<hr>

<!-- Reset to Defaults -->
<div class="atables-section">
	<h4>
		<span class="dashicons dashicons-image-rotate"></span>
		<?php esc_html_e( 'Reset Settings', 'a-tables-charts' ); ?>
	</h4>
	<p class="description"><?php esc_html_e( 'Reset all display and formatting settings to defaults.', 'a-tables-charts' ); ?></p>
	
	<button type="button" class="button" id="atables-reset-settings">
		<span class="dashicons dashicons-image-rotate"></span>
		<?php esc_html_e( 'Reset to Defaults', 'a-tables-charts' ); ?>
	</button>
</div>

<hr>

<!-- Danger Zone -->
<div class="atables-section atables-danger-zone">
	<h4>
		<span class="dashicons dashicons-warning"></span>
		<?php esc_html_e( 'Danger Zone', 'a-tables-charts' ); ?>
	</h4>
	<p class="description"><?php esc_html_e( 'Irreversible actions. Use with caution!', 'a-tables-charts' ); ?></p>
	
	<div class="atables-danger-actions">
		<button type="button" class="button button-secondary" id="atables-clear-cache">
			<span class="dashicons dashicons-trash"></span>
			<?php esc_html_e( 'Clear Table Cache', 'a-tables-charts' ); ?>
		</button>
		<button type="button" class="button button-secondary" id="atables-delete-table-advanced">
			<span class="dashicons dashicons-trash"></span>
			<?php esc_html_e( 'Delete This Table', 'a-tables-charts' ); ?>
		</button>
	</div>
</div>

<!-- Table Info -->
<div class="atables-table-debug-info">
	<h4><?php esc_html_e( 'Table Information', 'a-tables-charts' ); ?></h4>
	<table class="widefat">
		<tr>
			<td><strong><?php esc_html_e( 'Table ID:', 'a-tables-charts' ); ?></strong></td>
			<td><?php echo esc_html( $table->id ); ?></td>
		</tr>
		<tr>
			<td><strong><?php esc_html_e( 'Source Type:', 'a-tables-charts' ); ?></strong></td>
			<td><?php echo esc_html( $table->source_type ); ?></td>
		</tr>
		<tr>
			<td><strong><?php esc_html_e( 'Created:', 'a-tables-charts' ); ?></strong></td>
			<td><?php echo esc_html( $table->created_at ); ?></td>
		</tr>
		<tr>
			<td><strong><?php esc_html_e( 'Last Modified:', 'a-tables-charts' ); ?></strong></td>
			<td><?php echo esc_html( $table->updated_at ); ?></td>
		</tr>
		<tr>
			<td><strong><?php esc_html_e( 'Rows:', 'a-tables-charts' ); ?></strong></td>
			<td><?php echo esc_html( $table->row_count ); ?></td>
		</tr>
		<tr>
			<td><strong><?php esc_html_e( 'Columns:', 'a-tables-charts' ); ?></strong></td>
			<td><?php echo esc_html( $table->column_count ); ?></td>
		</tr>
	</table>
</div>

<style>
.atables-section {
	margin-bottom: 30px;
}

.atables-section h4 {
	display: flex;
	align-items: center;
	gap: 8px;
	margin-bottom: 8px;
	font-size: 16px;
}

.atables-section hr {
	margin: 30px 0;
	border: none;
	border-top: 1px solid #dcdcde;
}

.atables-sorting-config,
.atables-import-export {
	display: flex;
	gap: 10px;
	flex-wrap: wrap;
	align-items: center;
}

.atables-json-editor-wrapper {
	margin-top: 15px;
}

#atables-json-editor {
	font-family: 'Courier New', monospace;
	font-size: 13px;
	background: #2d2d2d;
	color: #f8f8f2;
	border: 1px solid #8c8f94;
	border-radius: 4px;
	padding: 15px;
	width: 100%;
	resize: vertical;
}

#atables-json-editor:focus {
	border-color: #2271b1;
	outline: none;
}

.atables-json-actions {
	display: flex;
	gap: 10px;
	margin-top: 10px;
}

#json-validation-result {
	margin-top: 10px;
	padding: 12px;
	border-radius: 4px;
	display: none;
}

#json-validation-result.success {
	background: #d5f4e6;
	color: #0a7340;
	border: 1px solid #0a7340;
}

#json-validation-result.error {
	background: #fce8e8;
	color: #a72a2c;
	border: 1px solid #a72a2c;
}

.atables-danger-zone {
	background: #fff8e5;
	border: 2px solid #f0b849;
	border-radius: 8px;
	padding: 20px;
}

.atables-danger-zone h4 {
	color: #d63638;
}

.atables-danger-actions {
	display: flex;
	gap: 10px;
	flex-wrap: wrap;
}

.atables-danger-actions .button {
	border-color: #d63638;
	color: #d63638;
}

.atables-danger-actions .button:hover {
	background: #d63638;
	color: #fff;
	border-color: #d63638;
}

.atables-table-debug-info {
	margin-top: 30px;
	background: #f9f9f9;
	border: 1px solid #dcdcde;
	border-radius: 4px;
	padding: 20px;
}

.atables-table-debug-info h4 {
	margin: 0 0 15px 0;
}

.atables-table-debug-info table {
	background: #fff;
}

.atables-table-debug-info td {
	padding: 10px;
}
</style>

<script>
jQuery(document).ready(function($) {
	const tableId = $('#atables-table-id').val();
	
	// Load current settings into JSON editor
	loadSettingsToJSON();
	
	// Format JSON
	$('#atables-format-json').on('click', function() {
		try {
			const json = JSON.parse($('#atables-json-editor').val());
			$('#atables-json-editor').val(JSON.stringify(json, null, 2));
			showValidationResult('JSON formatted successfully!', 'success');
		} catch (e) {
			showValidationResult('Invalid JSON: ' + e.message, 'error');
		}
	});
	
	// Validate JSON
	$('#atables-validate-json').on('click', function() {
		try {
			JSON.parse($('#atables-json-editor').val());
			showValidationResult('✓ JSON is valid!', 'success');
		} catch (e) {
			showValidationResult('✗ Invalid JSON: ' + e.message, 'error');
		}
	});
	
	// Apply JSON Configuration
	$('#atables-apply-json').on('click', function() {
		try {
			const config = JSON.parse($('#atables-json-editor').val());
			
			if (confirm('<?php esc_html_e( 'Apply this JSON configuration?', 'a-tables-charts' ); ?>')) {
				// Trigger event to apply configuration
				$(document).trigger('atables:applyJSONConfig', [config]);
				showValidationResult('✓ Configuration will be applied when you save the table.', 'success');
			}
		} catch (e) {
			showValidationResult('✗ Invalid JSON: ' + e.message, 'error');
		}
	});
	
	// Export Settings
	$('#atables-export-settings').on('click', function() {
		const settings = collectAllSettings();
		const blob = new Blob([JSON.stringify(settings, null, 2)], { type: 'application/json' });
		const url = URL.createObjectURL(blob);
		const a = document.createElement('a');
		a.href = url;
		a.download = 'table-' + tableId + '-settings.json';
		a.click();
		URL.revokeObjectURL(url);
		
		if (window.ATablesNotifications) {
			window.ATablesNotifications.show('Settings exported successfully!', 'success');
		}
	});
	
	// Import Settings
	$('#atables-import-settings').on('click', function() {
		$('#import-file-input').click();
	});
	
	$('#import-file-input').on('change', function(e) {
		const file = e.target.files[0];
		if (!file) return;
		
		const reader = new FileReader();
		reader.onload = function(e) {
			try {
				const settings = JSON.parse(e.target.result);
				$('#atables-json-editor').val(JSON.stringify(settings, null, 2));
				showValidationResult('✓ Settings imported! Click "Apply Configuration" to use them.', 'success');
			} catch (error) {
				showValidationResult('✗ Invalid settings file: ' + error.message, 'error');
			}
		};
		reader.readAsText(file);
	});
	
	// Reset to Defaults
	$('#atables-reset-settings').on('click', function() {
		if (confirm('<?php esc_html_e( 'Reset all settings to defaults? This cannot be undone.', 'a-tables-charts' ); ?>')) {
			$.ajax({
				url: aTablesAdmin.ajaxUrl,
				type: 'POST',
				data: {
					action: 'atables_reset_table_settings',
					nonce: aTablesAdmin.nonce,
					table_id: tableId
				},
				success: function(response) {
					if (response.success) {
						if (window.ATablesNotifications) {
							window.ATablesNotifications.show('Settings reset successfully!', 'success');
						}
						setTimeout(function() {
							location.reload();
						}, 1000);
					} else {
						alert(response.data.message || 'Failed to reset settings.');
					}
				}
			});
		}
	});
	
	// Clear Cache
	$('#atables-clear-cache').on('click', function() {
		if (confirm('<?php esc_html_e( 'Clear cache for this table?', 'a-tables-charts' ); ?>')) {
			$.ajax({
				url: aTablesAdmin.ajaxUrl,
				type: 'POST',
				data: {
					action: 'atables_clear_cache',
					nonce: aTablesAdmin.nonce,
					table_id: tableId
				},
				success: function(response) {
					if (response.success) {
						if (window.ATablesNotifications) {
							window.ATablesNotifications.show('Cache cleared successfully!', 'success');
						}
					}
				}
			});
		}
	});
	
	// Delete Table
	$('#atables-delete-table-advanced').on('click', function() {
		const tableName = $('#current-table-title').text();
		if (confirm('<?php esc_html_e( 'DELETE this table permanently? This CANNOT be undone!', 'a-tables-charts' ); ?>\n\n' + tableName)) {
			if (confirm('<?php esc_html_e( 'Are you ABSOLUTELY SURE? All data will be lost forever!', 'a-tables-charts' ); ?>')) {
				$.ajax({
					url: aTablesAdmin.ajaxUrl,
					type: 'POST',
					data: {
						action: 'atables_delete_table',
						nonce: aTablesAdmin.nonce,
						table_id: tableId
					},
					success: function(response) {
						if (response.success) {
							if (window.ATablesNotifications) {
								window.ATablesNotifications.show('Table deleted.', 'success');
							}
							setTimeout(function() {
								window.location.href = '<?php echo admin_url( 'admin.php?page=a-tables-charts' ); ?>';
							}, 1000);
						}
					}
				});
			}
		}
	});
	
	function loadSettingsToJSON() {
		const settings = collectAllSettings();
		$('#atables-json-editor').val(JSON.stringify(settings, null, 2));
	}
	
	function collectAllSettings() {
		// Collect all settings from the page
		return {
			display: {
				theme: $('input[name="display_theme"]:checked').val(),
				responsive_mode: $('input[name="display_responsive"]:checked').val(),
				enable_search: $('input[name="display_enable_search"]').is(':checked'),
				enable_sorting: $('input[name="display_enable_sorting"]').is(':checked'),
				enable_pagination: $('input[name="display_enable_pagination"]').is(':checked'),
				rows_per_page: $('input[name="display_rows_per_page"]').val()
			},
			// Add other settings here as they're implemented
			conditional_formatting: [],
			formulas: [],
			validation_rules: {},
			cell_merges: []
		};
	}
	
	function showValidationResult(message, type) {
		const $result = $('#json-validation-result');
		$result.removeClass('success error')
			   .addClass(type)
			   .html(message)
			   .show();
		
		setTimeout(function() {
			$result.fadeOut();
		}, 5000);
	}
});
</script>

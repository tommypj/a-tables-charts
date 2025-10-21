<?php
/**
 * Display Tab
 *
 * @package ATablesCharts
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Get available templates
require_once ATABLES_PLUGIN_DIR . 'src/modules/templates/TemplateService.php';
$template_service = new \ATablesCharts\Templates\Services\TemplateService();
$templates = $template_service->get_templates();

// Get current settings
$current_theme = isset( $display_settings['theme'] ) ? $display_settings['theme'] : 'default';
$current_responsive = isset( $display_settings['responsive_mode'] ) ? $display_settings['responsive_mode'] : 'scroll';
$rows_per_page = isset( $display_settings['rows_per_page'] ) ? $display_settings['rows_per_page'] : 10;
$enable_search = isset( $display_settings['enable_search'] ) ? $display_settings['enable_search'] : true;
$enable_sorting = isset( $display_settings['enable_sorting'] ) ? $display_settings['enable_sorting'] : true;
$enable_pagination = isset( $display_settings['enable_pagination'] ) ? $display_settings['enable_pagination'] : true;
?>

<div class="atables-tab-header">
	<h3><?php esc_html_e( 'Display Settings', 'a-tables-charts' ); ?></h3>
	<p><?php esc_html_e( 'Customize how your table looks and behaves.', 'a-tables-charts' ); ?></p>
</div>

<!-- Templates Section -->
<div class="atables-section">
	<h4>
		<span class="dashicons dashicons-admin-customizer"></span>
		<?php esc_html_e( 'Quick Templates', 'a-tables-charts' ); ?>
	</h4>
	<p class="description"><?php esc_html_e( 'Apply a pre-designed template to quickly style your table.', 'a-tables-charts' ); ?></p>
	
	<div class="atables-template-grid" id="template-grid">
		<?php foreach ( $templates as $template_id => $template ) : ?>
		<div class="atables-template-card" data-template="<?php echo esc_attr( $template_id ); ?>">
			<div class="atables-template-icon"><?php echo esc_html( $template['icon'] ); ?></div>
			<div class="atables-template-name"><?php echo esc_html( $template['label'] ); ?></div>
			<div class="atables-template-desc"><?php echo esc_html( $template['description'] ); ?></div>
			<button type="button" class="button button-small atables-apply-template" data-template="<?php echo esc_attr( $template_id ); ?>">
				<?php esc_html_e( 'Apply', 'a-tables-charts' ); ?>
			</button>
		</div>
		<?php endforeach; ?>
	</div>
</div>

<hr>

<!-- Theme Selection -->
<div class="atables-section">
	<h4>
		<span class="dashicons dashicons-art"></span>
		<?php esc_html_e( 'Table Theme', 'a-tables-charts' ); ?>
	</h4>
	<p class="description"><?php esc_html_e( 'Choose a Bootstrap theme for your table.', 'a-tables-charts' ); ?></p>
	
	<div class="atables-theme-selector">
		<label class="atables-theme-option <?php echo $current_theme === 'default' ? 'selected' : ''; ?>">
			<input type="radio" name="display_theme" value="default" <?php checked( $current_theme, 'default' ); ?>>
			<div class="atables-theme-preview">
				<div class="atables-theme-name"><?php esc_html_e( 'Default', 'a-tables-charts' ); ?></div>
				<div class="atables-theme-sample default-theme"></div>
			</div>
		</label>

		<label class="atables-theme-option <?php echo $current_theme === 'striped' ? 'selected' : ''; ?>">
			<input type="radio" name="display_theme" value="striped" <?php checked( $current_theme, 'striped' ); ?>>
			<div class="atables-theme-preview">
				<div class="atables-theme-name"><?php esc_html_e( 'Striped', 'a-tables-charts' ); ?></div>
				<div class="atables-theme-sample striped-theme"></div>
			</div>
		</label>

		<label class="atables-theme-option <?php echo $current_theme === 'bordered' ? 'selected' : ''; ?>">
			<input type="radio" name="display_theme" value="bordered" <?php checked( $current_theme, 'bordered' ); ?>>
			<div class="atables-theme-preview">
				<div class="atables-theme-name"><?php esc_html_e( 'Bordered', 'a-tables-charts' ); ?></div>
				<div class="atables-theme-sample bordered-theme"></div>
			</div>
		</label>

		<label class="atables-theme-option <?php echo $current_theme === 'hover' ? 'selected' : ''; ?>">
			<input type="radio" name="display_theme" value="hover" <?php checked( $current_theme, 'hover' ); ?>>
			<div class="atables-theme-preview">
				<div class="atables-theme-name"><?php esc_html_e( 'Hover', 'a-tables-charts' ); ?></div>
				<div class="atables-theme-sample hover-theme"></div>
			</div>
		</label>

		<label class="atables-theme-option <?php echo $current_theme === 'dark' ? 'selected' : ''; ?>">
			<input type="radio" name="display_theme" value="dark" <?php checked( $current_theme, 'dark' ); ?>>
			<div class="atables-theme-preview">
				<div class="atables-theme-name"><?php esc_html_e( 'Dark', 'a-tables-charts' ); ?></div>
				<div class="atables-theme-sample dark-theme"></div>
			</div>
		</label>

		<label class="atables-theme-option <?php echo $current_theme === 'minimal' ? 'selected' : ''; ?>">
			<input type="radio" name="display_theme" value="minimal" <?php checked( $current_theme, 'minimal' ); ?>>
			<div class="atables-theme-preview">
				<div class="atables-theme-name"><?php esc_html_e( 'Minimal', 'a-tables-charts' ); ?></div>
				<div class="atables-theme-sample minimal-theme"></div>
			</div>
		</label>
	</div>
</div>

<hr>

<!-- Responsive Mode -->
<div class="atables-section">
	<h4>
		<span class="dashicons dashicons-smartphone"></span>
		<?php esc_html_e( 'Responsive Mode', 'a-tables-charts' ); ?>
	</h4>
	<p class="description"><?php esc_html_e( 'Choose how your table adapts to mobile devices.', 'a-tables-charts' ); ?></p>
	
	<div class="atables-responsive-selector">
		<label class="atables-responsive-option <?php echo $current_responsive === 'scroll' ? 'selected' : ''; ?>">
			<input type="radio" name="display_responsive" value="scroll" <?php checked( $current_responsive, 'scroll' ); ?>>
			<div class="atables-responsive-preview">
				<div class="atables-responsive-icon">📱➡️</div>
				<div class="atables-responsive-name"><?php esc_html_e( 'Horizontal Scroll', 'a-tables-charts' ); ?></div>
				<div class="atables-responsive-desc"><?php esc_html_e( 'Scroll horizontally on small screens', 'a-tables-charts' ); ?></div>
			</div>
		</label>

		<label class="atables-responsive-option <?php echo $current_responsive === 'stack' ? 'selected' : ''; ?>">
			<input type="radio" name="display_responsive" value="stack" <?php checked( $current_responsive, 'stack' ); ?>>
			<div class="atables-responsive-preview">
				<div class="atables-responsive-icon">📚</div>
				<div class="atables-responsive-name"><?php esc_html_e( 'Stack Columns', 'a-tables-charts' ); ?></div>
				<div class="atables-responsive-desc"><?php esc_html_e( 'Stack columns vertically', 'a-tables-charts' ); ?></div>
			</div>
		</label>

		<label class="atables-responsive-option <?php echo $current_responsive === 'cards' ? 'selected' : ''; ?>">
			<input type="radio" name="display_responsive" value="cards" <?php checked( $current_responsive, 'cards' ); ?>>
			<div class="atables-responsive-preview">
				<div class="atables-responsive-icon">🃏</div>
				<div class="atables-responsive-name"><?php esc_html_e( 'Card Layout', 'a-tables-charts' ); ?></div>
				<div class="atables-responsive-desc"><?php esc_html_e( 'Display as cards on mobile', 'a-tables-charts' ); ?></div>
			</div>
		</label>
	</div>
</div>

<hr>

<!-- Feature Controls -->
<div class="atables-section">
	<h4>
		<span class="dashicons dashicons-admin-settings"></span>
		<?php esc_html_e( 'Features', 'a-tables-charts' ); ?>
	</h4>
	<p class="description"><?php esc_html_e( 'Enable or disable table features.', 'a-tables-charts' ); ?></p>
	
	<div class="atables-feature-controls">
		<div class="atables-feature-row">
			<div class="atables-feature-info">
				<strong><?php esc_html_e( 'Search', 'a-tables-charts' ); ?></strong>
				<span class="description"><?php esc_html_e( 'Allow users to search table content', 'a-tables-charts' ); ?></span>
			</div>
			<label class="atables-switch">
				<input type="checkbox" name="display_enable_search" <?php checked( $enable_search ); ?>>
				<span class="atables-switch-slider"></span>
			</label>
		</div>

		<div class="atables-feature-row">
			<div class="atables-feature-info">
				<strong><?php esc_html_e( 'Sorting', 'a-tables-charts' ); ?></strong>
				<span class="description"><?php esc_html_e( 'Enable column sorting', 'a-tables-charts' ); ?></span>
			</div>
			<label class="atables-switch">
				<input type="checkbox" name="display_enable_sorting" <?php checked( $enable_sorting ); ?>>
				<span class="atables-switch-slider"></span>
			</label>
		</div>

		<div class="atables-feature-row">
			<div class="atables-feature-info">
				<strong><?php esc_html_e( 'Pagination', 'a-tables-charts' ); ?></strong>
				<span class="description"><?php esc_html_e( 'Split table into pages', 'a-tables-charts' ); ?></span>
			</div>
			<label class="atables-switch">
				<input type="checkbox" name="display_enable_pagination" <?php checked( $enable_pagination ); ?>>
				<span class="atables-switch-slider"></span>
			</label>
		</div>

		<div class="atables-feature-row">
			<div class="atables-feature-info">
				<strong><?php esc_html_e( 'Rows per Page', 'a-tables-charts' ); ?></strong>
				<span class="description"><?php esc_html_e( 'Number of rows to show per page', 'a-tables-charts' ); ?></span>
			</div>
			<input type="number" name="display_rows_per_page" value="<?php echo esc_attr( $rows_per_page ); ?>" min="5" max="100" class="small-text">
		</div>
	</div>
</div>

<style>
/* Template Grid */
.atables-template-grid {
	display: grid;
	grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
	gap: 15px;
	margin-top: 20px;
}

.atables-template-card {
	background: #fff;
	border: 2px solid #dcdcde;
	border-radius: 8px;
	padding: 20px;
	text-align: center;
	transition: all 0.3s ease;
	cursor: pointer;
}

.atables-template-card:hover {
	border-color: #2271b1;
	box-shadow: 0 4px 12px rgba(34, 113, 177, 0.15);
	transform: translateY(-2px);
}

.atables-template-icon {
	font-size: 40px;
	margin-bottom: 10px;
}

.atables-template-name {
	font-weight: 600;
	color: #1d2327;
	margin-bottom: 5px;
	font-size: 15px;
}

.atables-template-desc {
	font-size: 12px;
	color: #646970;
	margin-bottom: 12px;
}

/* Theme Selector */
.atables-theme-selector {
	display: grid;
	grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
	gap: 15px;
	margin-top: 20px;
}

.atables-theme-option {
	cursor: pointer;
	position: relative;
}

.atables-theme-option input[type="radio"] {
	position: absolute;
	opacity: 0;
}

.atables-theme-preview {
	border: 2px solid #dcdcde;
	border-radius: 8px;
	padding: 15px;
	text-align: center;
	transition: all 0.3s ease;
	background: #fff;
}

.atables-theme-option:hover .atables-theme-preview {
	border-color: #2271b1;
	box-shadow: 0 2px 8px rgba(34, 113, 177, 0.15);
}

.atables-theme-option.selected .atables-theme-preview,
.atables-theme-option input:checked + .atables-theme-preview {
	border-color: #2271b1;
	background: #e7f3ff;
}

.atables-theme-name {
	font-weight: 600;
	margin-bottom: 10px;
	font-size: 13px;
}

.atables-theme-sample {
	height: 60px;
	border-radius: 4px;
	background: linear-gradient(to bottom, #f0f0f1 30%, #fff 30%);
	border: 1px solid #dcdcde;
}

.striped-theme {
	background: repeating-linear-gradient(
		to bottom,
		#f0f0f1 0px,
		#f0f0f1 20px,
		#fff 20px,
		#fff 40px
	);
}

.bordered-theme {
	border: 2px solid #2271b1;
	background: #fff;
}

.dark-theme {
	background: linear-gradient(to bottom, #1d2327 30%, #2c3338 30%);
}

.minimal-theme {
	background: #fff;
	border: none;
	border-bottom: 2px solid #dcdcde;
}

/* Responsive Selector */
.atables-responsive-selector {
	display: grid;
	grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
	gap: 15px;
	margin-top: 20px;
}

.atables-responsive-option {
	cursor: pointer;
	position: relative;
}

.atables-responsive-option input[type="radio"] {
	position: absolute;
	opacity: 0;
}

.atables-responsive-preview {
	border: 2px solid #dcdcde;
	border-radius: 8px;
	padding: 20px;
	text-align: center;
	transition: all 0.3s ease;
	background: #fff;
}

.atables-responsive-option:hover .atables-responsive-preview {
	border-color: #2271b1;
	box-shadow: 0 2px 8px rgba(34, 113, 177, 0.15);
}

.atables-responsive-option.selected .atables-responsive-preview,
.atables-responsive-option input:checked + .atables-responsive-preview {
	border-color: #2271b1;
	background: #e7f3ff;
}

.atables-responsive-icon {
	font-size: 32px;
	margin-bottom: 10px;
}

.atables-responsive-name {
	font-weight: 600;
	margin-bottom: 5px;
	font-size: 14px;
}

.atables-responsive-desc {
	font-size: 12px;
	color: #646970;
}

/* Feature Controls */
.atables-feature-controls {
	margin-top: 20px;
}

.atables-feature-row {
	display: flex;
	justify-content: space-between;
	align-items: center;
	padding: 15px;
	background: #f9f9f9;
	border: 1px solid #dcdcde;
	border-radius: 4px;
	margin-bottom: 10px;
}

.atables-feature-info {
	flex: 1;
}

.atables-feature-info strong {
	display: block;
	margin-bottom: 4px;
	color: #1d2327;
}

.atables-feature-info .description {
	font-size: 12px;
	color: #646970;
}

/* Toggle Switch */
.atables-switch {
	position: relative;
	display: inline-block;
	width: 50px;
	height: 26px;
}

.atables-switch input {
	opacity: 0;
	width: 0;
	height: 0;
}

.atables-switch-slider {
	position: absolute;
	cursor: pointer;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;
	background-color: #ccc;
	transition: 0.3s;
	border-radius: 26px;
}

.atables-switch-slider:before {
	position: absolute;
	content: "";
	height: 18px;
	width: 18px;
	left: 4px;
	bottom: 4px;
	background-color: white;
	transition: 0.3s;
	border-radius: 50%;
}

.atables-switch input:checked + .atables-switch-slider {
	background-color: #2271b1;
}

.atables-switch input:checked + .atables-switch-slider:before {
	transform: translateX(24px);
}

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
</style>

<script>
jQuery(document).ready(function($) {
	// Apply Template
	$('.atables-apply-template').on('click', function() {
		const templateId = $(this).data('template');
		const tableId = $('#atables-table-id').val();
		
		if (!confirm('Apply this template? This will override your current display settings.')) {
			return;
		}
		
		$.ajax({
			url: aTablesAdmin.ajaxUrl,
			type: 'POST',
			data: {
				action: 'atables_apply_template',
				nonce: aTablesAdmin.nonce,
				table_id: tableId,
				template_id: templateId
			},
			success: function(response) {
				if (response.success) {
					if (window.ATablesNotifications) {
						window.ATablesNotifications.show('Template applied successfully!', 'success');
					}
					setTimeout(function() {
						location.reload();
					}, 1000);
				} else {
					alert(response.data.message || 'Failed to apply template.');
				}
			},
			error: function() {
				alert('Failed to apply template. Please try again.');
			}
		});
	});
	
	// Theme selection visual feedback
	$('.atables-theme-option').on('click', function() {
		$('.atables-theme-option').removeClass('selected');
		$(this).addClass('selected');
	});
	
	// Responsive option visual feedback
	$('.atables-responsive-option').on('click', function() {
		$('.atables-responsive-option').removeClass('selected');
		$(this).addClass('selected');
	});
});
</script>

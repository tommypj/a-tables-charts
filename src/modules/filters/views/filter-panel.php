<?php
/**
 * Filter Panel View
 *
 * Renders the filter panel UI for tables
 *
 * @package ATablesCharts\Filters\Views
 * @since 1.0.5
 *
 * @var int   $table_id Table ID
 * @var array $columns  Table column names
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="atables-filter-panel" data-table-id="<?php echo esc_attr( $table_id ); ?>" data-columns='<?php echo esc_attr( wp_json_encode( $columns ) ); ?>'>
	
	<!-- Panel Header -->
	<div class="atables-filter-panel-header">
		<h3 class="atables-filter-panel-title">
			<span class="dashicons dashicons-filter"></span>
			<?php esc_html_e( 'Advanced Filters', 'a-tables-charts' ); ?>
		</h3>
		<div class="atables-filter-panel-actions">
			<button class="atables-filter-add-rule button">
				<span class="dashicons dashicons-plus-alt"></span>
				<?php esc_html_e( 'Add Filter', 'a-tables-charts' ); ?>
			</button>
		</div>
	</div>

	<!-- Preset Selector -->
	<div class="atables-preset-selector" style="display: none;">
		<label for="atables-preset-select">
			<?php esc_html_e( 'Load Preset:', 'a-tables-charts' ); ?>
		</label>
		<select id="atables-preset-select" class="atables-preset-select">
			<option value=""><?php esc_html_e( '-- Select a preset --', 'a-tables-charts' ); ?></option>
		</select>
		<div class="atables-preset-actions">
			<button class="atables-preset-load button button-primary">
				<?php esc_html_e( 'Load', 'a-tables-charts' ); ?>
			</button>
			<button class="atables-preset-duplicate button">
				<?php esc_html_e( 'Duplicate', 'a-tables-charts' ); ?>
			</button>
			<button class="atables-preset-delete button">
				<?php esc_html_e( 'Delete', 'a-tables-charts' ); ?>
			</button>
		</div>
	</div>

	<!-- Filter Builder -->
	<div class="atables-filter-builder">
		<div class="atables-filter-rules">
			<!-- Filter rules will be rendered here by JavaScript -->
			<div class="atables-filter-empty">
				<div class="atables-filter-empty-icon">
					<span class="dashicons dashicons-filter"></span>
				</div>
				<div class="atables-filter-empty-text">
					<?php esc_html_e( 'No filters added yet', 'a-tables-charts' ); ?>
				</div>
				<div class="atables-filter-empty-hint">
					<?php esc_html_e( 'Click "Add Filter" to create your first filter rule', 'a-tables-charts' ); ?>
				</div>
			</div>
		</div>
	</div>

	<!-- Filter Actions -->
	<div class="atables-filter-actions">
		<button class="atables-filter-apply button button-primary" disabled>
			<span class="dashicons dashicons-yes"></span>
			<?php esc_html_e( 'Apply Filters', 'a-tables-charts' ); ?>
		</button>
		<button class="atables-filter-clear button" disabled>
			<span class="dashicons dashicons-no-alt"></span>
			<?php esc_html_e( 'Clear Filters', 'a-tables-charts' ); ?>
		</button>
		<button class="atables-filter-save button" disabled>
			<span class="dashicons dashicons-saved"></span>
			<?php esc_html_e( 'Save as Preset', 'a-tables-charts' ); ?>
		</button>
	</div>

</div>

<!-- Save Preset Modal -->
<div class="atables-preset-modal">
	<div class="atables-preset-modal-content">
		<div class="atables-preset-modal-header">
			<h2 class="atables-preset-modal-title">
				<?php esc_html_e( 'Save Filter Preset', 'a-tables-charts' ); ?>
			</h2>
		</div>

		<div class="atables-preset-modal-body">
			<div class="atables-preset-form-field">
				<label for="atables-preset-name">
					<?php esc_html_e( 'Preset Name', 'a-tables-charts' ); ?>
					<span class="required">*</span>
				</label>
				<input 
					type="text" 
					id="atables-preset-name" 
					placeholder="<?php esc_attr_e( 'e.g., Active Users', 'a-tables-charts' ); ?>"
					required
				>
			</div>

			<div class="atables-preset-form-field">
				<label for="atables-preset-description">
					<?php esc_html_e( 'Description (Optional)', 'a-tables-charts' ); ?>
				</label>
				<textarea 
					id="atables-preset-description" 
					placeholder="<?php esc_attr_e( 'Describe what this filter preset does...', 'a-tables-charts' ); ?>"
				></textarea>
			</div>

			<div class="atables-preset-form-field">
				<div class="atables-preset-checkbox-field">
					<input type="checkbox" id="atables-preset-default">
					<label for="atables-preset-default">
						<?php esc_html_e( 'Set as default preset for this table', 'a-tables-charts' ); ?>
					</label>
				</div>
			</div>
		</div>

		<div class="atables-preset-modal-footer">
			<button class="atables-preset-modal-cancel button">
				<?php esc_html_e( 'Cancel', 'a-tables-charts' ); ?>
			</button>
			<button class="atables-preset-modal-save button button-primary">
				<?php esc_html_e( 'Save Preset', 'a-tables-charts' ); ?>
			</button>
		</div>
	</div>
</div>

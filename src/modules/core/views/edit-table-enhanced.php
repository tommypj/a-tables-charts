<?php
/**
 * Enhanced Edit Table Page with Tabbed Interface
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

// Get display settings
$display_settings = $table_repository->get_display_settings( $table_id );
$global_settings = get_option( 'atables_settings', array() );

// Get conditional formatting rules
$conditional_rules = isset( $display_settings['conditional_formatting'] ) ? $display_settings['conditional_formatting'] : array();

// Get formulas
$formulas = isset( $display_settings['formulas'] ) ? $display_settings['formulas'] : array();

// Get validation rules
$validation_rules = isset( $display_settings['validation_rules'] ) ? $display_settings['validation_rules'] : array();

// Get cell merges
$cell_merges = isset( $display_settings['cell_merges'] ) ? $display_settings['cell_merges'] : array();
?>

<div class="wrap atables-edit-page atables-edit-page-tabs">
	<!-- Page Header -->
	<div class="atables-page-header">
		<h1 class="atables-page-title">
			<span class="dashicons dashicons-edit"></span>
			<?php esc_html_e( 'Edit Table', 'a-tables-charts' ); ?>: 
			<span id="current-table-title"><?php echo esc_html( $table->title ); ?></span>
		</h1>
		
		<div class="atables-header-actions">
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=a-tables-charts' ) ); ?>" class="button">
				<span class="dashicons dashicons-arrow-left-alt2"></span>
				<?php esc_html_e( 'Back to Tables', 'a-tables-charts' ); ?>
			</a>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=a-tables-charts-view&table_id=' . $table->id ) ); ?>" class="button" target="_blank">
				<span class="dashicons dashicons-visibility"></span>
				<?php esc_html_e( 'Preview Table', 'a-tables-charts' ); ?>
			</a>
			<button type="button" class="button button-primary" id="atables-save-all-btn">
				<span class="dashicons dashicons-yes"></span>
				<?php esc_html_e( 'Save All Changes', 'a-tables-charts' ); ?>
			</button>
		</div>
	</div>

	<!-- Success/Error Messages -->
	<div id="atables-message-container"></div>

	<!-- Tab Navigation -->
	<nav class="atables-tab-navigation">
		<div class="atables-tab-nav">
			<button type="button" class="atables-tab-button active" data-tab="basic">
				<span class="dashicons dashicons-admin-generic"></span>
				<?php esc_html_e( 'Basic Info', 'a-tables-charts' ); ?>
			</button>
			<button type="button" class="atables-tab-button" data-tab="display">
				<span class="dashicons dashicons-admin-appearance"></span>
				<?php esc_html_e( 'Display', 'a-tables-charts' ); ?>
			</button>
			<button type="button" class="atables-tab-button" data-tab="data">
				<span class="dashicons dashicons-list-view"></span>
				<?php esc_html_e( 'Table Data', 'a-tables-charts' ); ?>
			</button>
			<button type="button" class="atables-tab-button" data-tab="conditional">
				<span class="dashicons dashicons-art"></span>
				<?php esc_html_e( 'Conditional Formatting', 'a-tables-charts' ); ?>
			</button>
			<button type="button" class="atables-tab-button" data-tab="formulas">
				<span class="dashicons dashicons-calculator"></span>
				<?php esc_html_e( 'Formulas', 'a-tables-charts' ); ?>
			</button>
			<button type="button" class="atables-tab-button" data-tab="validation">
				<span class="dashicons dashicons-yes-alt"></span>
				<?php esc_html_e( 'Validation', 'a-tables-charts' ); ?>
			</button>
			<button type="button" class="atables-tab-button" data-tab="merging">
				<span class="dashicons dashicons-table-col-after"></span>
				<?php esc_html_e( 'Cell Merging', 'a-tables-charts' ); ?>
			</button>
			<button type="button" class="atables-tab-button" data-tab="advanced">
				<span class="dashicons dashicons-admin-tools"></span>
				<?php esc_html_e( 'Advanced', 'a-tables-charts' ); ?>
			</button>
		</div>
	</nav>

	<!-- Tab Panels -->
	<div class="atables-tab-panels">
		
		<!-- BASIC INFO TAB -->
		<div id="atables-tab-basic" class="atables-tab-content active">
			<?php include ATABLES_PLUGIN_DIR . 'src/modules/core/views/tabs/basic-info-tab.php'; ?>
		</div>

		<!-- DISPLAY TAB -->
		<div id="atables-tab-display" class="atables-tab-content">
			<?php include ATABLES_PLUGIN_DIR . 'src/modules/core/views/tabs/display-tab.php'; ?>
		</div>

		<!-- TABLE DATA TAB -->
		<div id="atables-tab-data" class="atables-tab-content">
			<?php include ATABLES_PLUGIN_DIR . 'src/modules/core/views/tabs/data-tab.php'; ?>
		</div>

		<!-- CONDITIONAL FORMATTING TAB -->
		<div id="atables-tab-conditional" class="atables-tab-content">
			<?php include ATABLES_PLUGIN_DIR . 'src/modules/core/views/tabs/conditional-tab.php'; ?>
		</div>

		<!-- FORMULAS TAB -->
		<div id="atables-tab-formulas" class="atables-tab-content">
			<?php include ATABLES_PLUGIN_DIR . 'src/modules/core/views/tabs/formulas-tab.php'; ?>
		</div>

		<!-- VALIDATION TAB -->
		<div id="atables-tab-validation" class="atables-tab-content">
			<?php include ATABLES_PLUGIN_DIR . 'src/modules/core/views/tabs/validation-tab.php'; ?>
		</div>

		<!-- CELL MERGING TAB -->
		<div id="atables-tab-merging" class="atables-tab-content">
			<?php include ATABLES_PLUGIN_DIR . 'src/modules/core/views/tabs/merging-tab.php'; ?>
		</div>

		<!-- ADVANCED TAB -->
		<div id="atables-tab-advanced" class="atables-tab-content">
			<?php include ATABLES_PLUGIN_DIR . 'src/modules/core/views/tabs/advanced-tab.php'; ?>
		</div>

	</div>

	<!-- Hidden Fields -->
	<input type="hidden" id="atables-table-id" value="<?php echo esc_attr( $table_id ); ?>">
	<input type="hidden" id="atables-table-headers" value="<?php echo esc_attr( wp_json_encode( $headers ) ); ?>">
</div>

<style>
/* Quick inline styles for tab navigation */
.atables-tab-navigation {
	background: #fff;
	border-bottom: 2px solid #dcdcde;
	margin: 20px 0 0 0;
	overflow-x: auto;
}

.atables-tab-nav {
	display: flex;
	gap: 0;
	padding: 0;
	margin: 0;
}

.atables-tab-button {
	background: transparent;
	border: none;
	border-bottom: 3px solid transparent;
	color: #50575e;
	cursor: pointer;
	font-size: 14px;
	font-weight: 500;
	padding: 15px 20px;
	transition: all 0.2s ease;
	display: flex;
	align-items: center;
	gap: 8px;
	white-space: nowrap;
}

.atables-tab-button:hover {
	background: #f6f7f7;
	color: #2271b1;
}

.atables-tab-button.active {
	border-bottom-color: #2271b1;
	color: #2271b1;
	background: #f6f7f7;
}

.atables-tab-panels {
	background: #fff;
	border: 1px solid #dcdcde;
	border-top: none;
	padding: 30px;
	min-height: 400px;
}

.atables-tab-content {
	display: none;
}

.atables-tab-content.active {
	display: block;
	animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
	from { opacity: 0; transform: translateY(10px); }
	to { opacity: 1; transform: translateY(0); }
}
</style>

<script>
// Vanilla JavaScript version (no jQuery dependency)
console.log('Enhanced edit page script loaded - VANILLA JS');

// Wait for DOM to be ready
if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', initTabs);
} else {
	initTabs();
}

function initTabs() {
	console.log('Initializing tabs...');
	const tabButtons = document.querySelectorAll('.atables-tab-button');
	const tabContents = document.querySelectorAll('.atables-tab-content');
	console.log('Found', tabButtons.length, 'tab buttons');
	
	// Add click handlers to all tab buttons
	tabButtons.forEach(function(button) {
		button.addEventListener('click', function() {
			const tab = this.getAttribute('data-tab');
			console.log('Tab clicked:', tab);
			
			// Remove active class from all buttons
			tabButtons.forEach(function(btn) {
				btn.classList.remove('active');
			});
			
			// Add active class to clicked button
			this.classList.add('active');
			
			// Hide all tab contents
			tabContents.forEach(function(content) {
				content.classList.remove('active');
			});
			
			// Show selected tab content
			const selectedTab = document.getElementById('atables-tab-' + tab);
			if (selectedTab) {
				selectedTab.classList.add('active');
				console.log('Switched to tab:', tab);
			}
		});
	});
	
	// Save button handler
	const saveBtn = document.getElementById('atables-save-all-btn');
	if (saveBtn) {
		saveBtn.addEventListener('click', function() {
			console.log('Save button clicked');
			// Trigger save event for jQuery handlers if they exist
			if (typeof jQuery !== 'undefined') {
				jQuery(document).trigger('atables:saveAll');
			}
		});
	}
}
</script>

<script>
// jQuery version as backup
if (typeof jQuery !== 'undefined') {
	console.log('jQuery version also loading...');
	jQuery(document).ready(function($) {
		'use strict';
		console.log('jQuery ready, tabs:', $('.atables-tab-button').length);
	
	// Tab switching
	$('.atables-tab-button').on('click', function() {
		console.log('Tab clicked:', $(this).data('tab'));
		const tab = $(this).data('tab');
		
		// Update buttons
		$('.atables-tab-button').removeClass('active');
		$(this).addClass('active');
		
		// Update content
		$('.atables-tab-content').removeClass('active');
		$('#atables-tab-' + tab).addClass('active');
		
		// Trigger custom event for tab change
		$(document).trigger('atables:tabChanged', [tab]);
	});
	
	// Save all changes
	$('#atables-save-all-btn').on('click', function() {
		$(document).trigger('atables:saveAll');
	});
	});
}
</script>

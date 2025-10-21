<?php
/**
 * MySQL Query Import Page
 *
 * Create tables from MySQL queries.
 *
 * @package ATablesCharts\Core
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="wrap atables-mysql-page">
	<div class="atables-page-header">
		<h1 class="atables-page-title">
			<span class="dashicons dashicons-database"></span>
			<?php esc_html_e( 'Create Table from MySQL Query', 'a-tables-charts' ); ?>
		</h1>
		
		<div class="atables-header-actions">
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=a-tables-charts' ) ); ?>" class="button">
				<span class="dashicons dashicons-arrow-left-alt2"></span>
				<?php esc_html_e( 'Back to Tables', 'a-tables-charts' ); ?>
			</a>
		</div>
	</div>
	
	<!-- Info Card -->
	<div class="atables-info-card atables-info-warning">
		<div class="atables-info-icon">
			<span class="dashicons dashicons-info"></span>
		</div>
		<div class="atables-info-content">
			<h3><?php esc_html_e( 'Important Security Information', 'a-tables-charts' ); ?></h3>
			<p><?php esc_html_e( 'For security reasons, only SELECT queries are allowed. Queries containing DROP, DELETE, UPDATE, or other modifying statements will be rejected.', 'a-tables-charts' ); ?></p>
			<p><?php esc_html_e( 'The data will be imported as a snapshot. If the database changes, you will need to re-import to update the table.', 'a-tables-charts' ); ?></p>
		</div>
	</div>
	
	<!-- Sample Queries -->
	<div class="atables-card">
		<div class="atables-card-header">
			<h2><?php esc_html_e( 'Quick Start: Sample Queries', 'a-tables-charts' ); ?></h2>
		</div>
		<div class="atables-card-body">
			<p class="atables-help-text"><?php esc_html_e( 'Click on any sample query below to load it into the query editor:', 'a-tables-charts' ); ?></p>
			<div id="atables-sample-queries" class="atables-sample-queries">
				<div class="atables-loading"><?php esc_html_e( 'Loading sample queries...', 'a-tables-charts' ); ?></div>
			</div>
		</div>
	</div>
	
	<!-- Query Builder -->
	<div class="atables-card">
		<div class="atables-card-header">
			<h2><?php esc_html_e( 'SQL Query', 'a-tables-charts' ); ?></h2>
		</div>
		<div class="atables-card-body">
			<div class="atables-form-group">
				<label for="mysql-query" class="atables-label">
					<?php esc_html_e( 'Enter your SELECT query', 'a-tables-charts' ); ?>
					<span class="required">*</span>
				</label>
				<textarea id="mysql-query" 
						  class="atables-sql-editor" 
						  rows="10" 
						  placeholder="SELECT * FROM wp_posts WHERE post_status = 'publish' LIMIT 100"
						  required></textarea>
				<p class="atables-help-text">
					<?php esc_html_e( 'Write a SELECT query to fetch data from your WordPress database. You can use table names like wp_posts, wp_users, etc.', 'a-tables-charts' ); ?>
				</p>
			</div>
			
			<div class="atables-button-group">
				<button type="button" id="test-query-btn" class="button button-secondary">
					<span class="dashicons dashicons-yes-alt"></span>
					<?php esc_html_e( 'Test Query', 'a-tables-charts' ); ?>
				</button>
			</div>
		</div>
	</div>
	
	<!-- Query Results Preview -->
	<div id="query-results" class="atables-card" style="display: none;">
		<div class="atables-card-header">
			<h2><?php esc_html_e( 'Query Results Preview', 'a-tables-charts' ); ?></h2>
			<span id="results-count" class="atables-badge"></span>
		</div>
		<div class="atables-card-body">
			<div id="results-table-wrapper" class="atables-results-wrapper"></div>
		</div>
	</div>
	
	<!-- Table Details -->
	<div id="table-details" class="atables-card" style="display: none;">
		<div class="atables-card-header">
			<h2><?php esc_html_e( 'Table Details', 'a-tables-charts' ); ?></h2>
		</div>
		<div class="atables-card-body">
			<div class="atables-form-group">
				<label for="table-title" class="atables-label">
					<?php esc_html_e( 'Table Title', 'a-tables-charts' ); ?>
					<span class="required">*</span>
				</label>
				<input type="text" 
					   id="table-title" 
					   class="atables-input" 
					   placeholder="<?php esc_attr_e( 'e.g., Published Posts', 'a-tables-charts' ); ?>"
					   required>
			</div>
			
			<div class="atables-form-group">
				<label for="table-description" class="atables-label">
					<?php esc_html_e( 'Description (Optional)', 'a-tables-charts' ); ?>
				</label>
				<textarea id="table-description" 
						  class="atables-textarea" 
						  rows="3"
						  placeholder="<?php esc_attr_e( 'Brief description of this table...', 'a-tables-charts' ); ?>"></textarea>
			</div>
			
			<div class="atables-button-group">
				<button type="button" id="create-table-btn" class="button button-primary button-hero">
					<span class="dashicons dashicons-saved"></span>
					<?php esc_html_e( 'Create Table', 'a-tables-charts' ); ?>
				</button>
			</div>
		</div>
	</div>
</div>

<script>
jQuery(document).ready(function($) {
	'use strict';
	
	let queryResults = null;
	
	// Load sample queries
	loadSampleQueries();
	
	// Test query button
	$('#test-query-btn').on('click', function() {
		testQuery();
	});
	
	// Create table button
	$('#create-table-btn').on('click', function() {
		createTable();
	});
	
	/**
	 * Load sample queries
	 */
	function loadSampleQueries() {
		$.ajax({
			url: aTablesAdmin.ajaxUrl,
			type: 'POST',
			data: {
				action: 'atables_get_sample_queries',
				nonce: aTablesAdmin.nonce
			},
			success: function(response) {
				if (response.success && response.data.queries) {
					displaySampleQueries(response.data.queries);
				}
			}
		});
	}
	
	/**
	 * Display sample queries
	 */
	function displaySampleQueries(queries) {
		const $container = $('#atables-sample-queries');
		$container.empty();
		
		queries.forEach(function(query) {
			const $card = $('<div>', {
				class: 'atables-sample-query-card',
				click: function() {
					$('#mysql-query').val(query.query);
					$(this).addClass('active').siblings().removeClass('active');
				}
			}).append(
				$('<h4>').text(query.name),
				$('<p>').text(query.description)
			);
			
			$container.append($card);
		});
	}
	
	/**
	 * Test query
	 */
	function testQuery() {
		const query = $('#mysql-query').val().trim();
		
		if (!query) {
			alert('<?php esc_html_e( 'Please enter a query.', 'a-tables-charts' ); ?>');
			return;
		}
		
		const $btn = $('#test-query-btn');
		const originalText = $btn.html();
		
		$btn.prop('disabled', true).html('<span class="dashicons dashicons-update dashicons-spin"></span> <?php esc_html_e( 'Testing...', 'a-tables-charts' ); ?>');
		
		$.ajax({
			url: aTablesAdmin.ajaxUrl,
			type: 'POST',
			data: {
				action: 'atables_test_query',
				nonce: aTablesAdmin.nonce,
				query: query
			},
			success: function(response) {
				if (response.success) {
					queryResults = response.data;
					displayResults(response.data);
					$('#query-results').slideDown();
					$('#table-details').slideDown();
				} else {
					alert('<?php esc_html_e( 'Query Error: ', 'a-tables-charts' ); ?>' + response.data.message);
					$('#query-results').slideUp();
					$('#table-details').slideUp();
				}
			},
			error: function() {
				alert('<?php esc_html_e( 'Failed to test query. Please try again.', 'a-tables-charts' ); ?>');
			},
			complete: function() {
				$btn.prop('disabled', false).html(originalText);
			}
		});
	}
	
	/**
	 * Display query results
	 */
	function displayResults(data) {
		$('#results-count').text(data.rows + ' rows × ' + data.columns + ' columns');
		
		let html = '<table class="atables-preview-table"><thead><tr>';
		
		// Headers
		data.headers.forEach(function(header) {
			html += '<th>' + escapeHtml(header) + '</th>';
		});
		html += '</tr></thead><tbody>';
		
		// Data rows
		data.data.forEach(function(row) {
			html += '<tr>';
			row.forEach(function(cell) {
				html += '<td>' + escapeHtml(cell) + '</td>';
			});
			html += '</tr>';
		});
		
		html += '</tbody></table>';
		html += '<p class="atables-help-text" style="margin-top: 16px;"><?php esc_html_e( 'Showing first 5 rows. All data will be imported when you create the table.', 'a-tables-charts' ); ?></p>';
		
		$('#results-table-wrapper').html(html);
	}
	
	/**
	 * Create table
	 */
	function createTable() {
		const title = $('#table-title').val().trim();
		const description = $('#table-description').val().trim();
		const query = $('#mysql-query').val().trim();
		
		if (!title) {
			alert('<?php esc_html_e( 'Please enter a table title.', 'a-tables-charts' ); ?>');
			return;
		}
		
		if (!query) {
			alert('<?php esc_html_e( 'Please enter a query.', 'a-tables-charts' ); ?>');
			return;
		}
		
		const $btn = $('#create-table-btn');
		const originalText = $btn.html();
		
		$btn.prop('disabled', true).html('<span class="dashicons dashicons-update dashicons-spin"></span> <?php esc_html_e( 'Creating...', 'a-tables-charts' ); ?>');
		
		$.ajax({
			url: aTablesAdmin.ajaxUrl,
			type: 'POST',
			data: {
				action: 'atables_create_table_from_query',
				nonce: aTablesAdmin.nonce,
				title: title,
				description: description,
				query: query
			},
			success: function(response) {
				console.log('Create table response:', response);
				if (response.success) {
					if (response.data && response.data.table_id) {
						console.log('Redirecting to table ID:', response.data.table_id);
						// Build URL properly
						var redirectUrl = '<?php echo admin_url( 'admin.php' ); ?>' + 
							'?page=a-tables-charts-view&table_id=' + response.data.table_id;
						console.log('Redirect URL:', redirectUrl);
						window.location.href = redirectUrl;
					} else {
						console.error('No table_id in response:', response);
						alert('<?php esc_html_e( 'Table created but ID not returned. Please check the tables list.', 'a-tables-charts' ); ?>');
						window.location.href = '<?php echo esc_js( admin_url( 'admin.php?page=a-tables-charts' ) ); ?>';
					}
				} else {
					console.error('Create table failed:', response);
					alert('<?php esc_html_e( 'Error: ', 'a-tables-charts' ); ?>' + (response.data ? response.data.message : 'Unknown error'));
					$btn.prop('disabled', false).html(originalText);
				}
			},
			error: function(xhr, status, error) {
				console.error('AJAX error:', xhr, status, error);
				alert('<?php esc_html_e( 'Failed to create table. Please try again.', 'a-tables-charts' ); ?>');
				$btn.prop('disabled', false).html(originalText);
			}
		});
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

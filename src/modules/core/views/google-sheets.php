<?php
/**
 * Google Sheets Import Page
 *
 * @package ATablesCharts
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load Google Sheets service for instructions
require_once ATABLES_PLUGIN_DIR . 'src/modules/googlesheets/GoogleSheetsService.php';
?>

<div class="wrap atables-admin-page">
	<h1 class="wp-heading-inline">
		<?php esc_html_e( 'Import from Google Sheets', 'a-tables-charts' ); ?>
	</h1>
	<a href="<?php echo esc_url( admin_url( 'admin.php?page=a-tables-charts' ) ); ?>" class="page-title-action">
		<?php esc_html_e( 'Back to Tables', 'a-tables-charts' ); ?>
	</a>
	<hr class="wp-header-end">

	<div class="atables-wizard-container">
		<!-- Instructions -->
		<div class="atables-card" style="margin-bottom: 30px;">
			<?php echo \ATablesCharts\GoogleSheets\Services\GoogleSheetsService::get_sharing_instructions(); ?>
		</div>

		<!-- Import Form -->
		<div class="atables-card">
			<form id="atables-google-sheets-form" method="post">
				<table class="form-table">
					<tr>
						<th scope="row">
							<label for="google-sheets-url">
								<?php esc_html_e( 'Google Sheets URL', 'a-tables-charts' ); ?>
								<span class="required">*</span>
							</label>
						</th>
						<td>
							<input 
								type="url" 
								name="url" 
								id="google-sheets-url" 
								class="regular-text" 
								placeholder="https://docs.google.com/spreadsheets/d/..."
								required
							>
							<p class="description">
								<?php esc_html_e( 'Paste the full URL of your Google Sheet.', 'a-tables-charts' ); ?>
							</p>
							<button type="button" id="test-google-sheets-btn" class="button" style="margin-top: 10px;">
								<?php esc_html_e( 'Test Connection', 'a-tables-charts' ); ?>
							</button>
							<span id="test-result" style="margin-left: 10px;"></span>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="table-title">
								<?php esc_html_e( 'Table Title', 'a-tables-charts' ); ?>
							</label>
						</th>
						<td>
							<input 
								type="text" 
								name="title" 
								id="table-title" 
								class="regular-text"
								placeholder="<?php esc_attr_e( 'My Google Sheet Table', 'a-tables-charts' ); ?>"
							>
							<p class="description">
								<?php esc_html_e( 'Optional: Give your table a custom name. If left empty, a default name will be used.', 'a-tables-charts' ); ?>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php esc_html_e( 'Auto-Sync', 'a-tables-charts' ); ?>
						</th>
						<td>
							<label>
								<input type="checkbox" name="auto_sync" id="auto-sync-checkbox" value="1">
								<?php esc_html_e( 'Keep this table synced with Google Sheets', 'a-tables-charts' ); ?>
							</label>
							<p class="description">
								<?php esc_html_e( 'When enabled, you can manually sync data from Google Sheets to update the table.', 'a-tables-charts' ); ?>
							</p>
						</td>
					</tr>
				</table>

				<p class="submit">
					<button type="submit" class="button button-primary button-large" id="import-google-sheets-btn">
						<?php esc_html_e( 'Import from Google Sheets', 'a-tables-charts' ); ?>
					</button>
				</p>
			</form>
		</div>

		<!-- Supported URL Formats -->
		<div class="atables-card" style="margin-top: 30px; background: #f9f9f9;">
			<h3><?php esc_html_e( 'Supported URL Formats:', 'a-tables-charts' ); ?></h3>
			<ul style="list-style: disc; margin-left: 20px;">
				<?php foreach ( \ATablesCharts\GoogleSheets\Services\GoogleSheetsService::get_url_formats() as $name => $example ) : ?>
					<li>
						<strong><?php echo esc_html( $name ); ?>:</strong><br>
						<code style="font-size: 12px; color: #666;"><?php echo esc_html( $example ); ?></code>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</div>

<script>
jQuery(document).ready(function($) {
	
	// Test Google Sheets connection
	$('#test-google-sheets-btn').on('click', function() {
		const url = $('#google-sheets-url').val();
		const $btn = $(this);
		const $result = $('#test-result');
		
		if (!url) {
			$result.html('<span style="color: #dc3545;">Please enter a URL first.</span>');
			return;
		}
		
		$btn.prop('disabled', true).text('Testing...');
		$result.html('');
		
		$.ajax({
			url: aTablesAdmin.ajaxUrl,
			type: 'POST',
			data: {
				action: 'atables_test_google_sheets',
				nonce: aTablesAdmin.importNonce,
				url: url
			},
			success: function(response) {
				if (response.success) {
					$result.html('<span style="color: #198754;">✓ ' + response.data.message + '</span>');
				} else {
					$result.html('<span style="color: #dc3545;">✗ ' + response.data.message + '</span>');
				}
			},
			error: function() {
				$result.html('<span style="color: #dc3545;">Connection test failed.</span>');
			},
			complete: function() {
				$btn.prop('disabled', false).text('Test Connection');
			}
		});
	});
	
	// Import from Google Sheets
	$('#atables-google-sheets-form').on('submit', function(e) {
		e.preventDefault();
		
		const url = $('#google-sheets-url').val();
		const title = $('#table-title').val();
		const autoSync = $('#auto-sync-checkbox').is(':checked');
		const $btn = $('#import-google-sheets-btn');
		
		if (!url) {
			alert('Please enter a Google Sheets URL.');
			return;
		}
		
		$btn.prop('disabled', true).html('<span class="spinner is-active" style="float:none;"></span> Importing...');
		
		$.ajax({
			url: aTablesAdmin.ajaxUrl,
			type: 'POST',
			data: {
				action: 'atables_import_google_sheets',
				nonce: aTablesAdmin.importNonce,
				url: url,
				title: title,
				auto_sync: autoSync
			},
			success: function(response) {
				if (response.success) {
					if (window.ATablesNotifications) {
						window.ATablesNotifications.show(response.data.message, 'success');
					}
					
					// Redirect to table view
					if (response.data.redirect) {
						setTimeout(function() {
							window.location.href = response.data.redirect;
						}, 1000);
					}
				} else {
					alert(response.data.message);
					$btn.prop('disabled', false).text('Import from Google Sheets');
				}
			},
			error: function() {
				alert('Import failed. Please try again.');
				$btn.prop('disabled', false).text('Import from Google Sheets');
			}
		});
	});
});
</script>

<style>
.atables-instructions {
	background: #e7f3ff;
	border-left: 4px solid #2271b1;
	padding: 20px;
	border-radius: 4px;
}

.atables-instructions h4 {
	margin-top: 0;
	color: #2271b1;
}

.atables-instructions ol {
	margin-left: 20px;
}

.atables-instructions ol li {
	margin-bottom: 8px;
}

.required {
	color: #dc3545;
}

#test-result {
	font-weight: 600;
	animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
	from { opacity: 0; }
	to { opacity: 1; }
}
</style>

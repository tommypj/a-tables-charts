<?php
/**
 * Basic Info Tab
 *
 * @package ATablesCharts
 */

if ( ! defined( 'ABSPATH' ) ) exit;
?>

<div class="atables-tab-header">
	<h3><?php esc_html_e( 'Table Information', 'a-tables-charts' ); ?></h3>
	<p><?php esc_html_e( 'Basic information about your table.', 'a-tables-charts' ); ?></p>
</div>

<div class="atables-form-section">
	<div class="atables-form-row">
		<label for="table-title">
			<?php esc_html_e( 'Table Title', 'a-tables-charts' ); ?>
			<span class="required">*</span>
		</label>
		<input type="text" 
			   id="table-title" 
			   class="regular-text" 
			   value="<?php echo esc_attr( $table->title ); ?>" 
			   required>
		<p class="description"><?php esc_html_e( 'Give your table a descriptive name.', 'a-tables-charts' ); ?></p>
	</div>

	<div class="atables-form-row">
		<label for="table-description">
			<?php esc_html_e( 'Description', 'a-tables-charts' ); ?>
		</label>
		<textarea id="table-description" 
				  class="large-text" 
				  rows="4"><?php echo esc_textarea( $table->description ); ?></textarea>
		<p class="description"><?php esc_html_e( 'Optional description for internal reference.', 'a-tables-charts' ); ?></p>
	</div>

	<div class="atables-info-grid">
		<div class="atables-info-card">
			<div class="atables-info-icon">📊</div>
			<div class="atables-info-content">
				<div class="atables-info-label"><?php esc_html_e( 'Source Type', 'a-tables-charts' ); ?></div>
				<div class="atables-info-value"><?php echo esc_html( strtoupper( $table->source_type ) ); ?></div>
			</div>
		</div>

		<div class="atables-info-card">
			<div class="atables-info-icon">📅</div>
			<div class="atables-info-content">
				<div class="atables-info-label"><?php esc_html_e( 'Created', 'a-tables-charts' ); ?></div>
				<div class="atables-info-value"><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $table->created_at ) ) ); ?></div>
			</div>
		</div>

		<div class="atables-info-card">
			<div class="atables-info-icon">📋</div>
			<div class="atables-info-content">
				<div class="atables-info-label"><?php esc_html_e( 'Rows', 'a-tables-charts' ); ?></div>
				<div class="atables-info-value"><?php echo esc_html( number_format( $table->row_count ) ); ?></div>
			</div>
		</div>

		<div class="atables-info-card">
			<div class="atables-info-icon">📑</div>
			<div class="atables-info-content">
				<div class="atables-info-label"><?php esc_html_e( 'Columns', 'a-tables-charts' ); ?></div>
				<div class="atables-info-value"><?php echo esc_html( $table->column_count ); ?></div>
			</div>
		</div>
	</div>

	<?php if ( $table->source_type === 'google_sheets' && isset( $table->source_data['source_url'] ) ) : ?>
	<div class="atables-google-sheets-info">
		<h4><?php esc_html_e( 'Google Sheets Connection', 'a-tables-charts' ); ?></h4>
		<div class="atables-form-row">
			<label><?php esc_html_e( 'Source URL', 'a-tables-charts' ); ?></label>
			<input type="text" class="regular-text" value="<?php echo esc_attr( $table->source_data['source_url'] ); ?>" readonly>
			<p class="description">
				<?php 
				if ( isset( $table->source_data['last_sync'] ) ) {
					printf( 
						esc_html__( 'Last synced: %s', 'a-tables-charts' ), 
						esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $table->source_data['last_sync'] ) ) )
					);
				}
				?>
			</p>
		</div>
		<button type="button" class="button" id="atables-sync-google-sheets">
			<span class="dashicons dashicons-update"></span>
			<?php esc_html_e( 'Sync from Google Sheets', 'a-tables-charts' ); ?>
		</button>
	</div>
	<?php endif; ?>

	<div class="atables-form-row">
		<label for="table-shortcode">
			<?php esc_html_e( 'Shortcode', 'a-tables-charts' ); ?>
		</label>
		<div class="atables-shortcode-box">
			<code id="table-shortcode">[atable id="<?php echo esc_attr( $table->id ); ?>"]</code>
			<button type="button" class="button button-small atables-copy-shortcode" data-shortcode='[atable id="<?php echo esc_attr( $table->id ); ?>"]'>
				<span class="dashicons dashicons-clipboard"></span>
				<?php esc_html_e( 'Copy', 'a-tables-charts' ); ?>
			</button>
		</div>
		<p class="description"><?php esc_html_e( 'Use this shortcode to display your table on any page or post.', 'a-tables-charts' ); ?></p>
	</div>
</div>

<style>
.atables-info-grid {
	display: grid;
	grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
	gap: 15px;
	margin: 30px 0;
}

.atables-info-card {
	background: #f9f9f9;
	border: 1px solid #dcdcde;
	border-radius: 8px;
	padding: 20px;
	display: flex;
	align-items: center;
	gap: 15px;
}

.atables-info-icon {
	font-size: 32px;
	line-height: 1;
}

.atables-info-label {
	font-size: 12px;
	color: #646970;
	text-transform: uppercase;
	letter-spacing: 0.5px;
	margin-bottom: 5px;
}

.atables-info-value {
	font-size: 20px;
	font-weight: 600;
	color: #1d2327;
}

.atables-shortcode-box {
	display: flex;
	align-items: center;
	gap: 10px;
	background: #f6f7f7;
	padding: 12px 15px;
	border: 1px solid #dcdcde;
	border-radius: 4px;
}

.atables-shortcode-box code {
	flex: 1;
	background: transparent;
	padding: 0;
	font-size: 14px;
}

.atables-google-sheets-info {
	background: #e7f3ff;
	border: 1px solid #2271b1;
	border-radius: 4px;
	padding: 20px;
	margin: 20px 0;
}

.atables-google-sheets-info h4 {
	margin: 0 0 15px 0;
	color: #2271b1;
}
</style>

<script>
jQuery(document).ready(function($) {
	// Copy shortcode
	$('.atables-copy-shortcode').on('click', function() {
		const shortcode = $(this).data('shortcode');
		navigator.clipboard.writeText(shortcode).then(function() {
			if (window.ATablesNotifications) {
				window.ATablesNotifications.show('Shortcode copied to clipboard!', 'success');
			} else {
				alert('Shortcode copied!');
			}
		});
	});

	// Sync Google Sheets
	$('#atables-sync-google-sheets').on('click', function() {
		const $btn = $(this);
		const tableId = $('#atables-table-id').val();
		const originalText = $btn.html();
		
		$btn.prop('disabled', true).html('<span class="dashicons dashicons-update dashicons-spin"></span> Syncing...');
		
		$.ajax({
			url: aTablesAdmin.ajaxUrl,
			type: 'POST',
			data: {
				action: 'atables_sync_google_sheets',
				nonce: aTablesAdmin.nonce,
				table_id: tableId
			},
			success: function(response) {
				if (response.success) {
					if (window.ATablesNotifications) {
						window.ATablesNotifications.show(response.data.message, 'success');
					}
					setTimeout(function() {
						location.reload();
					}, 1500);
				} else {
					alert(response.data.message || 'Sync failed.');
				}
			},
			error: function() {
				alert('Sync failed. Please try again.');
			},
			complete: function() {
				$btn.prop('disabled', false).html(originalText);
			}
		});
	});
});
</script>

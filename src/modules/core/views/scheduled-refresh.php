<?php
/**
 * Scheduled Refresh Page
 *
 * Displays and manages scheduled data refreshes for tables.
 *
 * @package ATablesCharts\Core
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load Cron module.
require_once ATABLES_PLUGIN_DIR . 'src/modules/cron/index.php';

// Load Tables module for table selection.
require_once ATABLES_PLUGIN_DIR . 'src/modules/tables/index.php';

$schedule_service = new \ATablesCharts\Cron\Services\ScheduleService();
$table_service = new \ATablesCharts\Tables\Services\TableService();

// Get all schedules.
$schedules = $schedule_service->get_all_schedules();

// Get all tables for dropdown.
$tables_result = $table_service->get_all_tables( array( 'per_page' => 999 ) );
$tables = $tables_result['tables'];

// Get statistics.
$stats = $schedule_service->get_statistics();
?>

<div class="wrap atables-scheduled-refresh-page">
	<h1>
		<?php esc_html_e( 'Scheduled Refresh', 'a-tables-charts' ); ?>
		<button class="page-title-action atables-create-schedule-btn">
			<?php esc_html_e( 'Create New Schedule', 'a-tables-charts' ); ?>
		</button>
	</h1>

	<p class="description">
		<?php esc_html_e( 'Automatically refresh table data from external sources on a regular schedule.', 'a-tables-charts' ); ?>
	</p>

	<!-- Statistics Cards -->
	<div class="atables-stats-row">
		<div class="atables-stat-card">
			<div class="atables-stat-value"><?php echo esc_html( $stats['total'] ); ?></div>
			<div class="atables-stat-label"><?php esc_html_e( 'Total Schedules', 'a-tables-charts' ); ?></div>
		</div>
		<div class="atables-stat-card">
			<div class="atables-stat-value"><?php echo esc_html( $stats['active'] ); ?></div>
			<div class="atables-stat-label"><?php esc_html_e( 'Active', 'a-tables-charts' ); ?></div>
		</div>
		<div class="atables-stat-card">
			<div class="atables-stat-value"><?php echo esc_html( $stats['inactive'] ); ?></div>
			<div class="atables-stat-label"><?php esc_html_e( 'Inactive', 'a-tables-charts' ); ?></div>
		</div>
	</div>

	<!-- Schedules Table -->
	<?php if ( ! empty( $schedules ) ) : ?>
		<table class="wp-list-table widefat fixed striped atables-schedules-table">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Table', 'a-tables-charts' ); ?></th>
					<th><?php esc_html_e( 'Source', 'a-tables-charts' ); ?></th>
					<th><?php esc_html_e( 'Frequency', 'a-tables-charts' ); ?></th>
					<th><?php esc_html_e( 'Status', 'a-tables-charts' ); ?></th>
					<th><?php esc_html_e( 'Last Run', 'a-tables-charts' ); ?></th>
					<th><?php esc_html_e( 'Next Run', 'a-tables-charts' ); ?></th>
					<th><?php esc_html_e( 'Actions', 'a-tables-charts' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $schedules as $schedule ) : ?>
					<?php
					$table = $table_service->get_table( $schedule['table_id'] );
					$table_title = $table ? $table->title : __( 'Unknown Table', 'a-tables-charts' );

					// Get next run time.
					$hook_name = 'atables_scheduled_refresh_' . $schedule['id'];
					$next_run = wp_next_scheduled( $hook_name );

					// Format source type.
					$source_labels = array(
						'mysql' => __( 'MySQL Query', 'a-tables-charts' ),
						'google_sheets' => __( 'Google Sheets', 'a-tables-charts' ),
						'csv_url' => __( 'CSV URL', 'a-tables-charts' ),
						'rest_api' => __( 'REST API', 'a-tables-charts' ),
					);
					$source_label = isset( $source_labels[ $schedule['source_type'] ] )
						? $source_labels[ $schedule['source_type'] ]
						: $schedule['source_type'];

					// Format frequency.
					$frequency_labels = array(
						'hourly' => __( 'Hourly', 'a-tables-charts' ),
						'twicedaily' => __( 'Twice Daily', 'a-tables-charts' ),
						'daily' => __( 'Daily', 'a-tables-charts' ),
						'every_15_minutes' => __( 'Every 15 Minutes', 'a-tables-charts' ),
						'every_30_minutes' => __( 'Every 30 Minutes', 'a-tables-charts' ),
						'every_2_hours' => __( 'Every 2 Hours', 'a-tables-charts' ),
						'every_6_hours' => __( 'Every 6 Hours', 'a-tables-charts' ),
						'every_12_hours' => __( 'Every 12 Hours', 'a-tables-charts' ),
					);
					$frequency_label = isset( $frequency_labels[ $schedule['frequency'] ] )
						? $frequency_labels[ $schedule['frequency'] ]
						: $schedule['frequency'];
					?>
					<tr data-schedule-id="<?php echo esc_attr( $schedule['id'] ); ?>">
						<td>
							<strong><?php echo esc_html( $table_title ); ?></strong>
							<div class="row-actions">
								<span>
									<a href="<?php echo esc_url( admin_url( 'admin.php?page=a-tables-charts-view&id=' . $schedule['table_id'] ) ); ?>">
										<?php esc_html_e( 'View Table', 'a-tables-charts' ); ?>
									</a>
								</span>
							</div>
						</td>
						<td><?php echo esc_html( $source_label ); ?></td>
						<td><?php echo esc_html( $frequency_label ); ?></td>
						<td>
							<?php if ( $schedule['active'] ) : ?>
								<span class="atables-badge atables-badge-success">
									<?php esc_html_e( 'Active', 'a-tables-charts' ); ?>
								</span>
							<?php else : ?>
								<span class="atables-badge atables-badge-inactive">
									<?php esc_html_e( 'Inactive', 'a-tables-charts' ); ?>
								</span>
							<?php endif; ?>
						</td>
						<td>
							<?php if ( $schedule['last_run'] ) : ?>
								<div class="atables-last-run">
									<span><?php echo esc_html( human_time_diff( $schedule['last_run'] ) . ' ago' ); ?></span>
									<?php if ( $schedule['last_status'] === 'success' ) : ?>
										<span class="dashicons dashicons-yes-alt" style="color: #46b450;" title="<?php echo esc_attr( $schedule['last_message'] ); ?>"></span>
									<?php elseif ( $schedule['last_status'] === 'error' ) : ?>
										<span class="dashicons dashicons-warning" style="color: #dc3232;" title="<?php echo esc_attr( $schedule['last_message'] ); ?>"></span>
									<?php endif; ?>
								</div>
							<?php else : ?>
								<span class="atables-text-muted"><?php esc_html_e( 'Never run', 'a-tables-charts' ); ?></span>
							<?php endif; ?>
						</td>
						<td>
							<?php if ( $schedule['active'] && $next_run ) : ?>
								<?php echo esc_html( human_time_diff( $next_run, current_time( 'timestamp' ) ) ); ?>
							<?php else : ?>
								<span class="atables-text-muted">—</span>
							<?php endif; ?>
						</td>
						<td class="atables-actions">
							<button class="button button-small atables-trigger-refresh-btn"
									data-schedule-id="<?php echo esc_attr( $schedule['id'] ); ?>"
									title="<?php esc_attr_e( 'Trigger Refresh Now', 'a-tables-charts' ); ?>">
								<span class="dashicons dashicons-update"></span>
							</button>
							<button class="button button-small atables-edit-schedule-btn"
									data-schedule-id="<?php echo esc_attr( $schedule['id'] ); ?>"
									title="<?php esc_attr_e( 'Edit Schedule', 'a-tables-charts' ); ?>">
								<span class="dashicons dashicons-edit"></span>
							</button>
							<button class="button button-small atables-delete-schedule-btn"
									data-schedule-id="<?php echo esc_attr( $schedule['id'] ); ?>"
									title="<?php esc_attr_e( 'Delete Schedule', 'a-tables-charts' ); ?>">
								<span class="dashicons dashicons-trash"></span>
							</button>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php else : ?>
		<div class="atables-empty-state">
			<div class="atables-empty-icon">🔄</div>
			<h3><?php esc_html_e( 'No Scheduled Refreshes Yet', 'a-tables-charts' ); ?></h3>
			<p><?php esc_html_e( 'Automatically update your tables with fresh data from external sources.', 'a-tables-charts' ); ?></p>
			<button class="button button-primary button-large atables-create-schedule-btn">
				<?php esc_html_e( 'Create Your First Schedule', 'a-tables-charts' ); ?>
			</button>
		</div>
	<?php endif; ?>
</div>

<!-- Create/Edit Schedule Modal -->
<div id="atables-schedule-modal" class="atables-modal" style="display: none;">
	<div class="atables-modal-content">
		<div class="atables-modal-header">
			<h2 id="atables-schedule-modal-title"><?php esc_html_e( 'Create Schedule', 'a-tables-charts' ); ?></h2>
			<button class="atables-modal-close">&times;</button>
		</div>
		<div class="atables-modal-body">
			<form id="atables-schedule-form">
				<input type="hidden" id="schedule-id" name="schedule_id" value="">

				<!-- Table Selection -->
				<div class="atables-form-group">
					<label for="schedule-table-id">
						<?php esc_html_e( 'Table', 'a-tables-charts' ); ?>
						<span class="required">*</span>
					</label>
					<select id="schedule-table-id" name="table_id" required>
						<option value=""><?php esc_html_e( 'Select a table...', 'a-tables-charts' ); ?></option>
						<?php foreach ( $tables as $table ) : ?>
							<option value="<?php echo esc_attr( $table->id ); ?>">
								<?php echo esc_html( $table->title ); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>

				<!-- Source Type -->
				<div class="atables-form-group">
					<label for="schedule-source-type">
						<?php esc_html_e( 'Data Source', 'a-tables-charts' ); ?>
						<span class="required">*</span>
					</label>
					<select id="schedule-source-type" name="source_type" required>
						<option value=""><?php esc_html_e( 'Select a source...', 'a-tables-charts' ); ?></option>
						<option value="mysql"><?php esc_html_e( 'MySQL Query', 'a-tables-charts' ); ?></option>
						<option value="google_sheets"><?php esc_html_e( 'Google Sheets', 'a-tables-charts' ); ?></option>
						<option value="csv_url"><?php esc_html_e( 'CSV URL', 'a-tables-charts' ); ?></option>
						<option value="rest_api"><?php esc_html_e( 'REST API', 'a-tables-charts' ); ?></option>
					</select>
				</div>

				<!-- Frequency -->
				<div class="atables-form-group">
					<label for="schedule-frequency">
						<?php esc_html_e( 'Frequency', 'a-tables-charts' ); ?>
						<span class="required">*</span>
					</label>
					<select id="schedule-frequency" name="frequency" required>
						<option value="every_15_minutes"><?php esc_html_e( 'Every 15 Minutes', 'a-tables-charts' ); ?></option>
						<option value="every_30_minutes"><?php esc_html_e( 'Every 30 Minutes', 'a-tables-charts' ); ?></option>
						<option value="hourly"><?php esc_html_e( 'Hourly', 'a-tables-charts' ); ?></option>
						<option value="every_2_hours"><?php esc_html_e( 'Every 2 Hours', 'a-tables-charts' ); ?></option>
						<option value="every_6_hours"><?php esc_html_e( 'Every 6 Hours', 'a-tables-charts' ); ?></option>
						<option value="every_12_hours"><?php esc_html_e( 'Every 12 Hours', 'a-tables-charts' ); ?></option>
						<option value="twicedaily"><?php esc_html_e( 'Twice Daily', 'a-tables-charts' ); ?></option>
						<option value="daily" selected><?php esc_html_e( 'Daily', 'a-tables-charts' ); ?></option>
					</select>
				</div>

				<!-- Source-specific configurations (shown/hidden based on source type) -->

				<!-- MySQL Config -->
				<div id="config-mysql" class="atables-source-config" style="display: none;">
					<div class="atables-form-group">
						<label for="config-mysql-query">
							<?php esc_html_e( 'MySQL Query', 'a-tables-charts' ); ?>
							<span class="required">*</span>
						</label>
						<textarea id="config-mysql-query" name="config[query]" rows="5"
								  placeholder="SELECT * FROM wp_posts WHERE post_status = 'publish'"></textarea>
						<p class="description">
							<?php esc_html_e( 'Enter a SELECT query to fetch data. Query will be validated before scheduling.', 'a-tables-charts' ); ?>
						</p>
					</div>
				</div>

				<!-- Google Sheets Config -->
				<div id="config-google_sheets" class="atables-source-config" style="display: none;">
					<div class="atables-form-group">
						<label for="config-sheet-id">
							<?php esc_html_e( 'Sheet ID', 'a-tables-charts' ); ?>
							<span class="required">*</span>
						</label>
						<input type="text" id="config-sheet-id" name="config[sheet_id]"
							   placeholder="1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgvE2upms">
						<p class="description">
							<?php esc_html_e( 'The unique ID from your Google Sheets URL.', 'a-tables-charts' ); ?>
						</p>
					</div>
					<div class="atables-form-group">
						<label for="config-api-key">
							<?php esc_html_e( 'API Key', 'a-tables-charts' ); ?>
							<span class="required">*</span>
						</label>
						<input type="text" id="config-api-key" name="config[api_key]"
							   placeholder="AIzaSy...">
						<p class="description">
							<?php esc_html_e( 'Your Google Sheets API key.', 'a-tables-charts' ); ?>
						</p>
					</div>
					<div class="atables-form-group">
						<label for="config-range">
							<?php esc_html_e( 'Range (Optional)', 'a-tables-charts' ); ?>
						</label>
						<input type="text" id="config-range" name="config[range]"
							   placeholder="Sheet1" value="Sheet1">
						<p class="description">
							<?php esc_html_e( 'Sheet name or range (e.g., "Sheet1" or "Sheet1!A1:D10").', 'a-tables-charts' ); ?>
						</p>
					</div>
				</div>

				<!-- CSV URL Config -->
				<div id="config-csv_url" class="atables-source-config" style="display: none;">
					<div class="atables-form-group">
						<label for="config-csv-url">
							<?php esc_html_e( 'CSV URL', 'a-tables-charts' ); ?>
							<span class="required">*</span>
						</label>
						<input type="url" id="config-csv-url" name="config[url]"
							   placeholder="https://example.com/data.csv">
						<p class="description">
							<?php esc_html_e( 'Direct URL to a CSV file.', 'a-tables-charts' ); ?>
						</p>
					</div>
					<div class="atables-form-group">
						<label>
							<input type="checkbox" id="config-has-headers" name="config[has_headers]" checked>
							<?php esc_html_e( 'First row contains headers', 'a-tables-charts' ); ?>
						</label>
					</div>
					<div class="atables-form-group">
						<label for="config-delimiter">
							<?php esc_html_e( 'Delimiter', 'a-tables-charts' ); ?>
						</label>
						<input type="text" id="config-delimiter" name="config[delimiter]"
							   value="," maxlength="1" style="width: 60px;">
						<p class="description">
							<?php esc_html_e( 'Field delimiter (usually comma).', 'a-tables-charts' ); ?>
						</p>
					</div>
				</div>

				<!-- REST API Config -->
				<div id="config-rest_api" class="atables-source-config" style="display: none;">
					<div class="atables-form-group">
						<label for="config-api-url">
							<?php esc_html_e( 'API URL', 'a-tables-charts' ); ?>
							<span class="required">*</span>
						</label>
						<input type="url" id="config-api-url" name="config[url]"
							   placeholder="https://api.example.com/data">
						<p class="description">
							<?php esc_html_e( 'REST API endpoint that returns JSON data.', 'a-tables-charts' ); ?>
						</p>
					</div>
					<div class="atables-form-group">
						<label for="config-method">
							<?php esc_html_e( 'HTTP Method', 'a-tables-charts' ); ?>
						</label>
						<select id="config-method" name="config[method]">
							<option value="GET" selected><?php esc_html_e( 'GET', 'a-tables-charts' ); ?></option>
							<option value="POST"><?php esc_html_e( 'POST', 'a-tables-charts' ); ?></option>
						</select>
					</div>
					<div class="atables-form-group">
						<label for="config-headers">
							<?php esc_html_e( 'Headers (Optional)', 'a-tables-charts' ); ?>
						</label>
						<textarea id="config-headers" name="config[headers]" rows="3"
								  placeholder='{"Authorization": "Bearer token"}'></textarea>
						<p class="description">
							<?php esc_html_e( 'JSON object with HTTP headers.', 'a-tables-charts' ); ?>
						</p>
					</div>
				</div>

				<!-- Active Status -->
				<div class="atables-form-group">
					<label>
						<input type="checkbox" id="schedule-active" name="active" checked>
						<?php esc_html_e( 'Active (schedule will run)', 'a-tables-charts' ); ?>
					</label>
				</div>
			</form>
		</div>
		<div class="atables-modal-footer">
			<button type="button" class="button button-secondary atables-modal-close">
				<?php esc_html_e( 'Cancel', 'a-tables-charts' ); ?>
			</button>
			<button type="button" class="button button-primary" id="atables-save-schedule-btn">
				<?php esc_html_e( 'Save Schedule', 'a-tables-charts' ); ?>
			</button>
		</div>
	</div>
</div>

<style>
.atables-stats-row {
	display: grid;
	grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
	gap: 20px;
	margin: 20px 0;
}

.atables-stat-card {
	background: #fff;
	border: 1px solid #ddd;
	border-radius: 4px;
	padding: 20px;
	text-align: center;
}

.atables-stat-value {
	font-size: 32px;
	font-weight: bold;
	color: #2271b1;
	margin-bottom: 5px;
}

.atables-stat-label {
	color: #666;
	font-size: 14px;
}

.atables-badge {
	display: inline-block;
	padding: 3px 8px;
	border-radius: 3px;
	font-size: 12px;
	font-weight: 600;
}

.atables-badge-success {
	background: #d4edda;
	color: #155724;
}

.atables-badge-inactive {
	background: #f8f9fa;
	color: #6c757d;
}

.atables-text-muted {
	color: #666;
}

.atables-last-run {
	display: flex;
	align-items: center;
	gap: 5px;
}

.atables-actions {
	white-space: nowrap;
}

.atables-actions .button {
	margin-right: 2px;
}

.atables-actions .dashicons {
	font-size: 16px;
	width: 16px;
	height: 16px;
}

.atables-source-config {
	border-left: 3px solid #2271b1;
	padding-left: 15px;
	margin-left: 5px;
}

.atables-form-group {
	margin-bottom: 15px;
}

.atables-form-group label {
	display: block;
	margin-bottom: 5px;
	font-weight: 600;
}

.atables-form-group input[type="text"],
.atables-form-group input[type="url"],
.atables-form-group select,
.atables-form-group textarea {
	width: 100%;
	max-width: 500px;
}

.required {
	color: #dc3232;
}
</style>

<script>
jQuery(document).ready(function($) {
	let isEditMode = false;
	let currentScheduleId = null;

	// Open create modal
	$('.atables-create-schedule-btn').on('click', function() {
		isEditMode = false;
		currentScheduleId = null;
		$('#atables-schedule-modal-title').text('<?php esc_html_e( 'Create Schedule', 'a-tables-charts' ); ?>');
		$('#atables-schedule-form')[0].reset();
		$('#schedule-id').val('');
		$('.atables-source-config').hide();
		$('#atables-schedule-modal').fadeIn(200);
	});

	// Close modal
	$('.atables-modal-close').on('click', function() {
		$('#atables-schedule-modal').fadeOut(200);
	});

	// Show/hide source-specific config
	$('#schedule-source-type').on('change', function() {
		const sourceType = $(this).val();
		$('.atables-source-config').hide();
		if (sourceType) {
			$('#config-' + sourceType).show();
		}
	});

	// Save schedule
	$('#atables-save-schedule-btn').on('click', function() {
		const $btn = $(this);
		const formData = new FormData($('#atables-schedule-form')[0]);

		// Convert FormData to object
		const data = {
			action: isEditMode ? 'atables_update_schedule' : 'atables_create_schedule',
			nonce: aTablesAdmin.nonce
		};

		for (let [key, value] of formData.entries()) {
			if (key.startsWith('config[')) {
				const configKey = key.match(/config\[([^\]]+)\]/)[1];
				if (!data.config) data.config = {};
				data.config[configKey] = value;
			} else {
				data[key] = value;
			}
		}

		// Convert active checkbox
		data.active = $('#schedule-active').is(':checked');

		// Parse headers JSON if REST API
		if (data.source_type === 'rest_api' && data.config && data.config.headers) {
			try {
				data.config.headers = JSON.parse(data.config.headers);
			} catch (e) {
				alert('<?php esc_html_e( 'Invalid JSON in headers field', 'a-tables-charts' ); ?>');
				return;
			}
		}

		$btn.prop('disabled', true).text('<?php esc_html_e( 'Saving...', 'a-tables-charts' ); ?>');

		$.ajax({
			url: aTablesAdmin.ajaxUrl,
			type: 'POST',
			data: data,
			success: function(response) {
				if (response.success) {
					alert(response.data.message);
					location.reload();
				} else {
					alert(response.data.message || '<?php esc_html_e( 'An error occurred', 'a-tables-charts' ); ?>');
				}
			},
			error: function() {
				alert('<?php esc_html_e( 'An error occurred', 'a-tables-charts' ); ?>');
			},
			complete: function() {
				$btn.prop('disabled', false).text('<?php esc_html_e( 'Save Schedule', 'a-tables-charts' ); ?>');
			}
		});
	});

	// Edit schedule
	$('.atables-edit-schedule-btn').on('click', function() {
		const scheduleId = $(this).data('schedule-id');

		// Fetch schedule data
		$.ajax({
			url: aTablesAdmin.ajaxUrl,
			type: 'POST',
			data: {
				action: 'atables_get_schedules',
				nonce: aTablesAdmin.nonce
			},
			success: function(response) {
				if (response.success) {
					const schedule = response.data.schedules.find(s => s.id == scheduleId);
					if (schedule) {
						isEditMode = true;
						currentScheduleId = scheduleId;

						$('#atables-schedule-modal-title').text('<?php esc_html_e( 'Edit Schedule', 'a-tables-charts' ); ?>');
						$('#schedule-id').val(schedule.id);
						$('#schedule-table-id').val(schedule.table_id);
						$('#schedule-source-type').val(schedule.source_type).trigger('change');
						$('#schedule-frequency').val(schedule.frequency);
						$('#schedule-active').prop('checked', schedule.active);

						// Populate config fields
						if (schedule.config) {
							Object.keys(schedule.config).forEach(key => {
								const $field = $('[name="config[' + key + ']"]');
								if ($field.is(':checkbox')) {
									$field.prop('checked', schedule.config[key]);
								} else if (typeof schedule.config[key] === 'object') {
									$field.val(JSON.stringify(schedule.config[key], null, 2));
								} else {
									$field.val(schedule.config[key]);
								}
							});
						}

						$('#atables-schedule-modal').fadeIn(200);
					}
				}
			}
		});
	});

	// Delete schedule
	$('.atables-delete-schedule-btn').on('click', function() {
		if (!confirm('<?php esc_html_e( 'Are you sure you want to delete this schedule?', 'a-tables-charts' ); ?>')) {
			return;
		}

		const scheduleId = $(this).data('schedule-id');
		const $btn = $(this);

		$btn.prop('disabled', true);

		$.ajax({
			url: aTablesAdmin.ajaxUrl,
			type: 'POST',
			data: {
				action: 'atables_delete_schedule',
				nonce: aTablesAdmin.nonce,
				schedule_id: scheduleId
			},
			success: function(response) {
				if (response.success) {
					location.reload();
				} else {
					alert(response.data.message || '<?php esc_html_e( 'An error occurred', 'a-tables-charts' ); ?>');
					$btn.prop('disabled', false);
				}
			},
			error: function() {
				alert('<?php esc_html_e( 'An error occurred', 'a-tables-charts' ); ?>');
				$btn.prop('disabled', false);
			}
		});
	});

	// Trigger refresh
	$('.atables-trigger-refresh-btn').on('click', function() {
		const scheduleId = $(this).data('schedule-id');
		const $btn = $(this);
		const originalHtml = $btn.html();

		$btn.prop('disabled', true).html('<span class="dashicons dashicons-update spin"></span>');

		$.ajax({
			url: aTablesAdmin.ajaxUrl,
			type: 'POST',
			data: {
				action: 'atables_trigger_refresh',
				nonce: aTablesAdmin.nonce,
				schedule_id: scheduleId
			},
			success: function(response) {
				if (response.success) {
					alert(response.data.message);
					location.reload();
				} else {
					alert(response.data.message || '<?php esc_html_e( 'An error occurred', 'a-tables-charts' ); ?>');
				}
			},
			error: function() {
				alert('<?php esc_html_e( 'An error occurred', 'a-tables-charts' ); ?>');
			},
			complete: function() {
				$btn.prop('disabled', false).html(originalHtml);
			}
		});
	});
});
</script>

<style>
@keyframes spin {
	0% { transform: rotate(0deg); }
	100% { transform: rotate(360deg); }
}

.dashicons.spin {
	animation: spin 1s linear infinite;
}
</style>

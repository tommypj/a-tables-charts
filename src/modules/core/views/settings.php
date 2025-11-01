<?php
/**
 * Settings View
 *
 * Plugin settings page with comprehensive options.
 *
 * @package ATablesCharts\Core
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get current settings with defaults.
$settings = get_option( 'atables_settings', array() );
$defaults = array(
	// General
	'rows_per_page'          => 10,
	'default_table_style'    => 'default',
	'enable_responsive'      => true,
	'enable_search'          => true,
	'enable_sorting'         => true,
	'enable_pagination'      => true,
	'enable_export'          => true,
	// Data Formatting
	'date_format'            => 'Y-m-d',
	'time_format'            => 'H:i:s',
	'decimal_separator'      => '.',
	'thousands_separator'    => ',',
	// Import Settings
	'max_import_size'        => 10485760, // 10MB
	'csv_delimiter'          => ',',
	'csv_enclosure'          => '"',
	'csv_escape'             => '\\',
	// Export Settings
	'export_filename'        => 'table-export',
	'export_date_format'     => 'Y-m-d',
	'export_encoding'        => 'UTF-8',
	// Performance & Cache
	'cache_enabled'          => true,
	'cache_expiration'       => 3600,
	'lazy_load_tables'       => false,
	'async_loading'          => false,
	// Charts
	'google_charts_enabled'  => true,
	'chartjs_enabled'        => true,
	'default_chart_library'  => 'chartjs',
	// Security
	'allowed_file_types'     => array( 'csv', 'json', 'xlsx', 'xls', 'xml' ),
	'sanitize_html'          => true,
	'enable_mysql_query'     => '1',
	'mysql_query_enabled'    => '1',
);
$settings = wp_parse_args( $settings, $defaults );

// Handle settings saved message.
$settings_updated = isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] === 'true';
?>

<div class="wrap atables-settings-page">
	<!-- Page Header -->
	<div class="atables-page-header">
		<h1 class="atables-page-title">
			<span class="dashicons dashicons-admin-settings"></span>
			<?php esc_html_e( 'A-Tables & Charts Settings', 'a-tables-charts' ); ?>
		</h1>
		<p class="atables-page-description">
			<?php esc_html_e( 'Configure default behavior and preferences for tables and charts.', 'a-tables-charts' ); ?>
		</p>
	</div>
	
	<?php if ( $settings_updated ) : ?>
		<script>
			document.addEventListener('DOMContentLoaded', function() {
				if (typeof ATablesToast !== 'undefined') {
					ATablesToast.success('<?php esc_html_e( 'Settings saved successfully!', 'a-tables-charts' ); ?>');
				}
			});
		</script>
	<?php endif; ?>
	
	<form method="post" action="options.php" class="atables-settings-form">
		<?php settings_fields( 'atables_settings' ); ?>
		
		<div class="atables-settings-grid">
			<!-- Left Column - Main Settings -->
			<div class="atables-settings-main">
				
				<!-- General Settings -->
				<div class="atables-settings-card">
					<div class="atables-card-header">
						<h2><span class="dashicons dashicons-admin-generic"></span> <?php esc_html_e( 'General Settings', 'a-tables-charts' ); ?></h2>
					</div>
					<div class="atables-card-body">
						<div class="atables-form-group">
							<label for="rows_per_page" class="atables-label">
								<?php esc_html_e( 'Default Rows per Page', 'a-tables-charts' ); ?>
							</label>
							<input type="number" 
								   id="rows_per_page" 
								   name="atables_settings[rows_per_page]" 
								   value="<?php echo esc_attr( $settings['rows_per_page'] ); ?>" 
								   min="1" 
								   max="100" 
								   class="atables-input-small">
							<p class="atables-help-text">
								<?php esc_html_e( 'Number of rows to display per page in tables (1-100).', 'a-tables-charts' ); ?>
							</p>
						</div>
						
						<div class="atables-form-group">
							<label for="default_table_style" class="atables-label">
								<?php esc_html_e( 'Default Table Style', 'a-tables-charts' ); ?>
							</label>
							<select id="default_table_style" 
									name="atables_settings[default_table_style]" 
									class="atables-input">
								<option value="default" <?php selected( $settings['default_table_style'], 'default' ); ?>>
									<?php esc_html_e( 'Default', 'a-tables-charts' ); ?>
								</option>
								<option value="striped" <?php selected( $settings['default_table_style'], 'striped' ); ?>>
									<?php esc_html_e( 'Striped Rows', 'a-tables-charts' ); ?>
								</option>
								<option value="bordered" <?php selected( $settings['default_table_style'], 'bordered' ); ?>>
									<?php esc_html_e( 'Bordered', 'a-tables-charts' ); ?>
								</option>
								<option value="hover" <?php selected( $settings['default_table_style'], 'hover' ); ?>>
									<?php esc_html_e( 'Hover Effect', 'a-tables-charts' ); ?>
								</option>
							</select>
							<p class="atables-help-text">
								<?php esc_html_e( 'Default visual style for new tables.', 'a-tables-charts' ); ?>
							</p>
						</div>
						
						<div class="atables-form-group">
							<label class="atables-label">
								<?php esc_html_e( 'Frontend Features', 'a-tables-charts' ); ?>
							</label>
							<p class="atables-help-text" style="margin-top: 0;">
								<?php esc_html_e( 'Enable or disable features for frontend table display.', 'a-tables-charts' ); ?>
							</p>
							<div class="atables-checkbox-group">
								<label class="atables-checkbox-label">
									<input type="checkbox" 
										   name="atables_settings[enable_responsive]" 
										   value="1" 
										   <?php checked( $settings['enable_responsive'] ); ?>>
									<span><?php esc_html_e( 'Responsive Tables', 'a-tables-charts' ); ?></span>
									<span class="atables-badge atables-badge-info"><?php esc_html_e( 'Recommended', 'a-tables-charts' ); ?></span>
								</label>
								<label class="atables-checkbox-label">
									<input type="checkbox" 
										   name="atables_settings[enable_search]" 
										   value="1" 
										   <?php checked( $settings['enable_search'] ); ?>>
									<span><?php esc_html_e( 'Search Functionality', 'a-tables-charts' ); ?></span>
								</label>
								<label class="atables-checkbox-label">
									<input type="checkbox" 
										   name="atables_settings[enable_sorting]" 
										   value="1" 
										   <?php checked( $settings['enable_sorting'] ); ?>>
									<span><?php esc_html_e( 'Column Sorting', 'a-tables-charts' ); ?></span>
								</label>
								<label class="atables-checkbox-label">
									<input type="checkbox" 
										   name="atables_settings[enable_pagination]" 
										   value="1" 
										   <?php checked( $settings['enable_pagination'] ); ?>>
									<span><?php esc_html_e( 'Pagination', 'a-tables-charts' ); ?></span>
								</label>
								<label class="atables-checkbox-label">
									<input type="checkbox" 
										   name="atables_settings[enable_export]" 
										   value="1" 
										   <?php checked( $settings['enable_export'] ); ?>>
									<span><?php esc_html_e( 'Export Options', 'a-tables-charts' ); ?></span>
								</label>
							</div>
						</div>
					</div>
				</div>
				
				<!-- Data Formatting -->
				<div class="atables-settings-card">
					<div class="atables-card-header">
						<h2><span class="dashicons dashicons-editor-alignleft"></span> <?php esc_html_e( 'Data Formatting', 'a-tables-charts' ); ?></h2>
					</div>
					<div class="atables-card-body">
						<div class="atables-form-row">
							<div class="atables-form-group">
								<label for="date_format" class="atables-label">
									<?php esc_html_e( 'Date Format', 'a-tables-charts' ); ?>
								</label>
								<input type="text" 
									   id="date_format" 
									   name="atables_settings[date_format]" 
									   value="<?php echo esc_attr( $settings['date_format'] ); ?>" 
									   class="atables-input"
									   placeholder="Y-m-d">
								<p class="atables-help-text">
									<?php
									printf(
										/* translators: %s: PHP date function documentation URL */
										esc_html__( 'Format for dates. See %s for reference.', 'a-tables-charts' ),
										'<a href="https://www.php.net/manual/en/datetime.format.php" target="_blank">PHP date()</a>'
									);
									?>
									<br>
									<strong><?php esc_html_e( 'Example:', 'a-tables-charts' ); ?></strong> 
									<?php echo esc_html( date( $settings['date_format'] ) ); ?>
								</p>
							</div>
							
							<div class="atables-form-group">
								<label for="time_format" class="atables-label">
									<?php esc_html_e( 'Time Format', 'a-tables-charts' ); ?>
								</label>
								<input type="text" 
									   id="time_format" 
									   name="atables_settings[time_format]" 
									   value="<?php echo esc_attr( $settings['time_format'] ); ?>" 
									   class="atables-input"
									   placeholder="H:i:s">
								<p class="atables-help-text">
									<strong><?php esc_html_e( 'Example:', 'a-tables-charts' ); ?></strong> 
									<?php echo esc_html( date( $settings['time_format'] ) ); ?>
								</p>
							</div>
						</div>
						
						<div class="atables-form-row">
							<div class="atables-form-group">
								<label for="decimal_separator" class="atables-label">
									<?php esc_html_e( 'Decimal Separator', 'a-tables-charts' ); ?>
								</label>
								<input type="text" 
									   id="decimal_separator" 
									   name="atables_settings[decimal_separator]" 
									   value="<?php echo esc_attr( $settings['decimal_separator'] ); ?>" 
									   class="atables-input-small"
									   maxlength="1"
									   placeholder=".">
								<p class="atables-help-text">
									<strong><?php esc_html_e( 'Example:', 'a-tables-charts' ); ?></strong> 
									123<?php echo esc_html( $settings['decimal_separator'] ); ?>45
								</p>
							</div>
							
							<div class="atables-form-group">
								<label for="thousands_separator" class="atables-label">
									<?php esc_html_e( 'Thousands Separator', 'a-tables-charts' ); ?>
								</label>
								<input type="text" 
									   id="thousands_separator" 
									   name="atables_settings[thousands_separator]" 
									   value="<?php echo esc_attr( $settings['thousands_separator'] ); ?>" 
									   class="atables-input-small"
									   maxlength="1"
									   placeholder=",">
								<p class="atables-help-text">
									<strong><?php esc_html_e( 'Example:', 'a-tables-charts' ); ?></strong> 
									1<?php echo esc_html( $settings['thousands_separator'] ); ?>234<?php echo esc_html( $settings['thousands_separator'] ); ?>567
								</p>
							</div>
						</div>
					</div>
				</div>
				
				<!-- Import Settings -->
				<div class="atables-settings-card">
					<div class="atables-card-header">
						<h2><span class="dashicons dashicons-upload"></span> <?php esc_html_e( 'Import Settings', 'a-tables-charts' ); ?></h2>
					</div>
					<div class="atables-card-body">
						<div class="atables-form-group">
							<label for="max_import_size" class="atables-label">
								<?php esc_html_e( 'Maximum Import File Size', 'a-tables-charts' ); ?>
							</label>
							<div class="atables-input-group">
								<input type="number" 
									   id="max_import_size" 
									   name="atables_settings[max_import_size]" 
									   value="<?php echo esc_attr( floor( ( $settings['max_import_size'] ?? 10485760 ) / 1048576 ) ); ?>" 
									   min="1" 
									   max="100"
									   class="atables-input-small">
								<span class="atables-input-addon"><?php esc_html_e( 'MB', 'a-tables-charts' ); ?></span>
							</div>
							<p class="atables-help-text">
								<?php esc_html_e( 'Maximum file size allowed for imports (in megabytes). Server limit:', 'a-tables-charts' ); ?>
								<?php echo esc_html( size_format( wp_max_upload_size() ) ); ?>
							</p>
						</div>
						
						<div class="atables-form-row">
							<div class="atables-form-group">
								<label for="csv_delimiter" class="atables-label">
									<?php esc_html_e( 'CSV Delimiter', 'a-tables-charts' ); ?>
								</label>
								<input type="text" 
									   id="csv_delimiter" 
									   name="atables_settings[csv_delimiter]" 
									   value="<?php echo esc_attr( $settings['csv_delimiter'] ?? ',' ); ?>" 
									   class="atables-input-small"
									   maxlength="1"
									   placeholder=",">
								<p class="atables-help-text">
									<?php esc_html_e( 'Character used to separate values in CSV files. Common: comma (,) or semicolon (;)', 'a-tables-charts' ); ?>
								</p>
							</div>
							
							<div class="atables-form-group">
								<label for="csv_enclosure" class="atables-label">
									<?php esc_html_e( 'CSV Enclosure', 'a-tables-charts' ); ?>
								</label>
								<input type="text" 
									   id="csv_enclosure" 
									   name="atables_settings[csv_enclosure]" 
									   value="<?php echo esc_attr( $settings['csv_enclosure'] ?? '"' ); ?>" 
									   class="atables-input-small"
									   maxlength="1"
									   placeholder='"'>
								<p class="atables-help-text">
									<?php esc_html_e( 'Character used to enclose text values. Usually double quote (")', 'a-tables-charts' ); ?>
								</p>
							</div>
						</div>
						
						<div class="atables-form-group">
							<label for="csv_escape" class="atables-label">
								<?php esc_html_e( 'CSV Escape Character', 'a-tables-charts' ); ?>
							</label>
							<input type="text" 
								   id="csv_escape" 
								   name="atables_settings[csv_escape]" 
								   value="<?php echo esc_attr( $settings['csv_escape'] ?? '\\' ); ?>" 
								   class="atables-input-small"
								   maxlength="1"
								   placeholder="\\">
							<p class="atables-help-text">
								<?php esc_html_e( 'Character used to escape special characters. Usually backslash (\\)', 'a-tables-charts' ); ?>
							</p>
						</div>
					</div>
				</div>
				
				<!-- Export Settings -->
				<div class="atables-settings-card">
					<div class="atables-card-header">
						<h2><span class="dashicons dashicons-download"></span> <?php esc_html_e( 'Export Settings', 'a-tables-charts' ); ?></h2>
					</div>
					<div class="atables-card-body">
						<div class="atables-form-group">
							<label for="export_filename" class="atables-label">
								<?php esc_html_e( 'Default Export Filename', 'a-tables-charts' ); ?>
							</label>
							<input type="text" 
								   id="export_filename" 
								   name="atables_settings[export_filename]" 
								   value="<?php echo esc_attr( $settings['export_filename'] ?? 'table-export' ); ?>" 
								   class="atables-input"
								   placeholder="table-export">
							<p class="atables-help-text">
								<?php esc_html_e( 'Default filename for exported files (without extension). A timestamp will be appended automatically.', 'a-tables-charts' ); ?>
								<br>
								<strong><?php esc_html_e( 'Example:', 'a-tables-charts' ); ?></strong>
								<?php echo esc_html( ( $settings['export_filename'] ?? 'table-export' ) . '-' . date( 'Y-m-d' ) . '.csv' ); ?>
							</p>
						</div>
						
						<div class="atables-form-row">
							<div class="atables-form-group">
								<label for="export_date_format" class="atables-label">
									<?php esc_html_e( 'Export Date Format', 'a-tables-charts' ); ?>
								</label>
								<input type="text" 
									   id="export_date_format" 
									   name="atables_settings[export_date_format]" 
									   value="<?php echo esc_attr( $settings['export_date_format'] ?? 'Y-m-d' ); ?>" 
									   class="atables-input"
									   placeholder="Y-m-d">
								<p class="atables-help-text">
									<?php esc_html_e( 'Date format used in export filenames.', 'a-tables-charts' ); ?>
								</p>
							</div>
							
							<div class="atables-form-group">
								<label for="export_encoding" class="atables-label">
									<?php esc_html_e( 'Export File Encoding', 'a-tables-charts' ); ?>
								</label>
								<select id="export_encoding" 
										name="atables_settings[export_encoding]" 
										class="atables-input">
									<option value="UTF-8" <?php selected( $settings['export_encoding'] ?? 'UTF-8', 'UTF-8' ); ?>>
										<?php esc_html_e( 'UTF-8 (Recommended)', 'a-tables-charts' ); ?>
									</option>
									<option value="ISO-8859-1" <?php selected( $settings['export_encoding'] ?? 'UTF-8', 'ISO-8859-1' ); ?>>
										<?php esc_html_e( 'ISO-8859-1 (Latin-1)', 'a-tables-charts' ); ?>
									</option>
									<option value="Windows-1252" <?php selected( $settings['export_encoding'] ?? 'UTF-8', 'Windows-1252' ); ?>>
										<?php esc_html_e( 'Windows-1252', 'a-tables-charts' ); ?>
									</option>
								</select>
								<p class="atables-help-text">
									<?php esc_html_e( 'Character encoding for exported files.', 'a-tables-charts' ); ?>
								</p>
							</div>
						</div>
					</div>
				</div>
				
				<!-- PDF Export Settings -->
				<div class="atables-settings-card">
					<div class="atables-card-header">
						<h2><span class="dashicons dashicons-media-document"></span> <?php esc_html_e( 'PDF Export Settings', 'a-tables-charts' ); ?></h2>
					</div>
					<div class="atables-card-body">
						<div class="atables-form-group">
							<label for="pdf_page_orientation" class="atables-label">
								<?php esc_html_e( 'Default Page Orientation', 'a-tables-charts' ); ?>
							</label>
							<select id="pdf_page_orientation" 
									name="atables_settings[pdf_page_orientation]" 
									class="atables-input">
								<option value="auto" <?php selected( $settings['pdf_page_orientation'] ?? 'auto', 'auto' ); ?>>
									<?php esc_html_e( 'Auto Detect (Recommended)', 'a-tables-charts' ); ?>
								</option>
								<option value="portrait" <?php selected( $settings['pdf_page_orientation'] ?? 'auto', 'portrait' ); ?>>
									<?php esc_html_e( 'Portrait', 'a-tables-charts' ); ?>
								</option>
								<option value="landscape" <?php selected( $settings['pdf_page_orientation'] ?? 'auto', 'landscape' ); ?>>
									<?php esc_html_e( 'Landscape', 'a-tables-charts' ); ?>
								</option>
							</select>
							<p class="atables-help-text">
								<?php esc_html_e( 'Page orientation for PDF exports. Auto detect will use landscape for tables with more than 6 columns.', 'a-tables-charts' ); ?>
							</p>
						</div>
						
						<div class="atables-form-row">
							<div class="atables-form-group">
								<label for="pdf_font_size" class="atables-label">
									<?php esc_html_e( 'Font Size', 'a-tables-charts' ); ?>
								</label>
								<div class="atables-input-group">
									<input type="number" 
										   id="pdf_font_size" 
										   name="atables_settings[pdf_font_size]" 
										   value="<?php echo esc_attr( $settings['pdf_font_size'] ?? 9 ); ?>" 
										   min="6" 
										   max="14"
										   step="1"
										   class="atables-input-small">
									<span class="atables-input-addon"><?php esc_html_e( 'points', 'a-tables-charts' ); ?></span>
								</div>
								<p class="atables-help-text">
									<?php esc_html_e( 'Font size for table content in PDF (6-14 points). Smaller fonts fit more data per page.', 'a-tables-charts' ); ?>
								</p>
							</div>
							
							<div class="atables-form-group">
								<label for="pdf_max_rows" class="atables-label">
									<?php esc_html_e( 'Maximum Rows', 'a-tables-charts' ); ?>
								</label>
								<input type="number" 
									   id="pdf_max_rows" 
									   name="atables_settings[pdf_max_rows]" 
									   value="<?php echo esc_attr( $settings['pdf_max_rows'] ?? 5000 ); ?>" 
									   min="100" 
									   max="10000"
									   step="100"
									   class="atables-input-small">
								<p class="atables-help-text">
									<?php esc_html_e( 'Maximum number of rows allowed for PDF export (100-10,000). Use Excel export for larger datasets.', 'a-tables-charts' ); ?>
								</p>
							</div>
						</div>
						
						<div class="atables-info-box atables-info-box-info">
							<span class="dashicons dashicons-info"></span>
							<div>
								<strong><?php esc_html_e( 'PDF Export Features:', 'a-tables-charts' ); ?></strong>
								<ul style="margin: 8px 0 0 0; padding-left: 20px;">
									<li><?php esc_html_e( 'Professional table styling with WordPress branding', 'a-tables-charts' ); ?></li>
									<li><?php esc_html_e( 'Automatic page breaks for large tables', 'a-tables-charts' ); ?></li>
									<li><?php esc_html_e( 'UTF-8 support for international characters', 'a-tables-charts' ); ?></li>
									<li><?php esc_html_e( 'Respects current table filters and sorting', 'a-tables-charts' ); ?></li>
								</ul>
							</div>
						</div>
					</div>
				</div>
				
				<!-- Performance & Cache -->
				<div class="atables-settings-card">
					<div class="atables-card-header">
						<h2><span class="dashicons dashicons-performance"></span> <?php esc_html_e( 'Performance & Cache', 'a-tables-charts' ); ?></h2>
					</div>
					<div class="atables-card-body">
						<div class="atables-form-group">
							<label class="atables-checkbox-label">
								<input type="checkbox" 
									   name="atables_settings[cache_enabled]" 
									   value="1" 
									   <?php checked( $settings['cache_enabled'] ); ?>>
								<span><?php esc_html_e( 'Enable data caching', 'a-tables-charts' ); ?></span>
								<span class="atables-badge atables-badge-success"><?php esc_html_e( 'Recommended', 'a-tables-charts' ); ?></span>
							</label>
							<p class="atables-help-text">
								<?php esc_html_e( 'Cache table data for faster loading. Improves performance for large tables.', 'a-tables-charts' ); ?>
							</p>
						</div>
						
						<div class="atables-form-group">
							<label for="cache_expiration" class="atables-label">
								<?php esc_html_e( 'Cache Duration', 'a-tables-charts' ); ?>
							</label>
							<div class="atables-input-group">
								<input type="number" 
									   id="cache_expiration" 
									   name="atables_settings[cache_expiration]" 
									   value="<?php echo esc_attr( $settings['cache_expiration'] ); ?>" 
									   min="0" 
									   step="60"
									   class="atables-input-small">
								<span class="atables-input-addon"><?php esc_html_e( 'seconds', 'a-tables-charts' ); ?></span>
							</div>
							<p class="atables-help-text">
								<?php esc_html_e( 'How long to cache data before refreshing. Set to 0 to disable caching.', 'a-tables-charts' ); ?>
								<br>
								<strong><?php esc_html_e( 'Recommended:', 'a-tables-charts' ); ?></strong>
								<?php esc_html_e( '3600 (1 hour) for most sites', 'a-tables-charts' ); ?>
							</p>
						</div>
						
						<div class="atables-form-group">
							<label class="atables-label">
								<?php esc_html_e( 'Advanced Performance Options', 'a-tables-charts' ); ?>
							</label>
							<p class="atables-help-text" style="margin-top: 0;">
								<?php esc_html_e( 'Experimental features to improve performance for large datasets.', 'a-tables-charts' ); ?>
							</p>
							<div class="atables-checkbox-group">
								<label class="atables-checkbox-label">
									<input type="checkbox" 
										   name="atables_settings[lazy_load_tables]" 
										   value="1" 
										   <?php checked( $settings['lazy_load_tables'] ?? false ); ?>>
									<span><?php esc_html_e( 'Lazy Load Tables', 'a-tables-charts' ); ?></span>
									<span class="atables-badge atables-badge-warning"><?php esc_html_e( 'Experimental', 'a-tables-charts' ); ?></span>
								</label>
								<label class="atables-checkbox-label">
									<input type="checkbox" 
										   name="atables_settings[async_loading]" 
										   value="1" 
										   <?php checked( $settings['async_loading'] ?? false ); ?>>
									<span><?php esc_html_e( 'Asynchronous Data Loading', 'a-tables-charts' ); ?></span>
									<span class="atables-badge atables-badge-warning"><?php esc_html_e( 'Experimental', 'a-tables-charts' ); ?></span>
								</label>
							</div>
						</div>
						
						<?php
						// Load cache module.
						require_once ATABLES_PLUGIN_DIR . 'src/modules/cache/index.php';
						$cache_service = new \ATablesCharts\Cache\Services\CacheService();
						$cache_stats = $cache_service->get_stats();
						$cache_size = $cache_service->get_cache_size();
						?>
						
						<div class="atables-cache-stats">
							<h4><?php esc_html_e( 'Cache Statistics', 'a-tables-charts' ); ?></h4>
							<div class="atables-stats-grid">
								<div class="atables-stat-item">
									<span class="atables-stat-label"><?php esc_html_e( 'Cache Hits', 'a-tables-charts' ); ?></span>
									<span class="atables-stat-value"><?php echo number_format( $cache_stats['hits'] ); ?></span>
								</div>
								<div class="atables-stat-item">
									<span class="atables-stat-label"><?php esc_html_e( 'Cache Misses', 'a-tables-charts' ); ?></span>
									<span class="atables-stat-value"><?php echo number_format( $cache_stats['misses'] ); ?></span>
								</div>
								<div class="atables-stat-item">
									<span class="atables-stat-label"><?php esc_html_e( 'Hit Rate', 'a-tables-charts' ); ?></span>
									<span class="atables-stat-value"><?php echo esc_html( $cache_stats['hit_rate'] ); ?>%</span>
								</div>
								<div class="atables-stat-item">
									<span class="atables-stat-label"><?php esc_html_e( 'Cache Size', 'a-tables-charts' ); ?></span>
									<span class="atables-stat-value"><?php echo esc_html( $cache_size['kilobytes'] ); ?> KB</span>
								</div>
							</div>
							<div class="atables-cache-actions">
								<button type="button" class="button" id="atables-clear-cache">
									<span class="dashicons dashicons-trash"></span>
									<?php esc_html_e( 'Clear All Cache', 'a-tables-charts' ); ?>
								</button>
								<button type="button" class="button" id="atables-reset-cache-stats">
									<span class="dashicons dashicons-image-rotate"></span>
									<?php esc_html_e( 'Reset Statistics', 'a-tables-charts' ); ?>
								</button>
							</div>
						</div>
					</div>
				</div>
				
				<!-- Chart Settings -->
				<div class="atables-settings-card">
					<div class="atables-card-header">
						<h2><span class="dashicons dashicons-chart-bar"></span> <?php esc_html_e( 'Chart Settings', 'a-tables-charts' ); ?></h2>
					</div>
					<div class="atables-card-body">
						<div class="atables-form-group">
						<label class="atables-label">
						<?php esc_html_e( 'Chart Libraries', 'a-tables-charts' ); ?>
						</label>
						<p class="atables-help-text" style="margin-top: 0;">
						<?php esc_html_e( 'Enable chart rendering libraries.', 'a-tables-charts' ); ?>
						</p>
						<div class="atables-checkbox-group">
						<label class="atables-checkbox-label">
						<input type="checkbox" 
						name="atables_settings[chartjs_enabled]" 
						value="1" 
						<?php checked( $settings['chartjs_enabled'] ); ?>>
						<span><?php esc_html_e( 'Chart.js', 'a-tables-charts' ); ?></span>
						<span class="atables-badge atables-badge-primary"><?php esc_html_e( 'Recommended', 'a-tables-charts' ); ?></span>
						</label>
						<label class="atables-checkbox-label">
						<input type="checkbox" 
						name="atables_settings[google_charts_enabled]" 
						value="1" 
						<?php checked( $settings['google_charts_enabled'] ); ?>>
						<span><?php esc_html_e( 'Google Charts', 'a-tables-charts' ); ?></span>
						<span class="atables-badge atables-badge-success"><?php esc_html_e( 'Available', 'a-tables-charts' ); ?></span>
						</label>
						</div>
						</div>
						
						<div class="atables-form-group">
						 <label for="default_chart_library" class="atables-label">
						<?php esc_html_e( 'Default Chart Library', 'a-tables-charts' ); ?>
					</label>
					<select id="default_chart_library" 
							name="atables_settings[default_chart_library]" 
							class="atables-input">
						<option value="chartjs" <?php selected( $settings['default_chart_library'] ?? 'chartjs', 'chartjs' ); ?>>
							<?php esc_html_e( 'Chart.js (Modern, Lightweight)', 'a-tables-charts' ); ?>
						</option>
						<option value="google" <?php selected( $settings['default_chart_library'] ?? 'chartjs', 'google' ); ?>>
							<?php esc_html_e( 'Google Charts (Classic, Powerful)', 'a-tables-charts' ); ?>
						</option>
					</select>
					<p class="atables-help-text">
						<?php esc_html_e( 'Choose which library to use by default. You can override this in individual shortcodes.', 'a-tables-charts' ); ?>
					</p>
				</div>
					</div>
				</div>
				
				<!-- Security Settings -->
				<div class="atables-settings-card">
					<div class="atables-card-header">
						<h2><span class="dashicons dashicons-shield"></span> <?php esc_html_e( 'Security Settings', 'a-tables-charts' ); ?></h2>
					</div>
					<div class="atables-card-body">
						<div class="atables-form-group">
							<label class="atables-label">
								<?php esc_html_e( 'Allowed Import File Types', 'a-tables-charts' ); ?>
							</label>
							<p class="atables-help-text" style="margin-top: 0;">
								<?php esc_html_e( 'Select which file types and data sources users can import. Restricting file types improves security.', 'a-tables-charts' ); ?>
							</p>
							<?php
							$allowed_types = $settings['allowed_file_types'] ?? array( 'csv', 'json', 'xlsx', 'xls', 'xml' );
							$file_types = array(
							'csv'  => __( 'CSV Files (.csv)', 'a-tables-charts' ),
							'json' => __( 'JSON Files (.json)', 'a-tables-charts' ),
							'xlsx' => __( 'Excel Files (.xlsx)', 'a-tables-charts' ),
							'xls'  => __( 'Legacy Excel Files (.xls)', 'a-tables-charts' ),
							'xml'  => __( 'XML Files (.xml)', 'a-tables-charts' ),
							);
							
						// Add MySQL Query to data sources
						$data_sources = array(
							'enable_mysql_query' => __( 'MySQL Query Builder', 'a-tables-charts' ),
						);
						?>
							<div class="atables-checkbox-group">
								<?php foreach ( $file_types as $type => $label ) : ?>
									<label class="atables-checkbox-label">
										<input type="checkbox" 
											   name="atables_settings[allowed_file_types][]" 
											   value="<?php echo esc_attr( $type ); ?>" 
											   <?php checked( in_array( $type, $allowed_types, true ) ); ?>>
										<span><?php echo esc_html( $label ); ?></span>
										<?php if ( $type === 'csv' || $type === 'xlsx' ) : ?>
											<span class="atables-badge atables-badge-success"><?php esc_html_e( 'Recommended', 'a-tables-charts' ); ?></span>
										<?php endif; ?>
									</label>
								<?php endforeach; ?>
							</div>
						</div>
						
						<div class="atables-form-group">
							<label class="atables-label">
								<?php esc_html_e( 'Data Sanitization', 'a-tables-charts' ); ?>
							</label>
							<div class="atables-checkbox-group">
								<label class="atables-checkbox-label">
									<input type="checkbox" 
										   name="atables_settings[sanitize_html]" 
										   value="1" 
										   <?php checked( $settings['sanitize_html'] ?? true ); ?>>
									<span><?php esc_html_e( 'Sanitize HTML in Table Data', 'a-tables-charts' ); ?></span>
									<span class="atables-badge atables-badge-success"><?php esc_html_e( 'Recommended', 'a-tables-charts' ); ?></span>
								</label>
							</div>
							<p class="atables-help-text">
								<?php esc_html_e( 'Remove potentially dangerous HTML/JavaScript from imported data. Keeps your site safe from XSS attacks.', 'a-tables-charts' ); ?>
							</p>
						</div>
					</div>
				</div>
				
				<!-- Save Button -->
				<div class="atables-settings-actions">
					<?php submit_button( __( 'Save All Settings', 'a-tables-charts' ), 'primary large', 'submit', false ); ?>
					<button type="button" class="button button-secondary" id="atables-reset-settings">
						<?php esc_html_e( 'Reset to Defaults', 'a-tables-charts' ); ?>
					</button>
				</div>
			</div>
			
			<!-- Right Column - System Info & Help -->
			<div class="atables-settings-sidebar">
				<!-- System Information -->
				<div class="atables-settings-card">
					<div class="atables-card-header">
						<h2><span class="dashicons dashicons-info"></span> <?php esc_html_e( 'System Information', 'a-tables-charts' ); ?></h2>
					</div>
					<div class="atables-card-body">
						<div class="atables-system-info">
							<div class="atables-info-row">
								<span class="atables-info-label"><?php esc_html_e( 'Plugin Version', 'a-tables-charts' ); ?></span>
								<span class="atables-info-value"><?php echo esc_html( ATABLES_VERSION ); ?></span>
							</div>
							<div class="atables-info-row">
								<span class="atables-info-label"><?php esc_html_e( 'WordPress', 'a-tables-charts' ); ?></span>
								<span class="atables-info-value"><?php echo esc_html( get_bloginfo( 'version' ) ); ?></span>
							</div>
							<div class="atables-info-row">
								<span class="atables-info-label"><?php esc_html_e( 'PHP Version', 'a-tables-charts' ); ?></span>
								<span class="atables-info-value"><?php echo esc_html( phpversion() ); ?></span>
							</div>
							<div class="atables-info-row">
								<span class="atables-info-label"><?php esc_html_e( 'MySQL', 'a-tables-charts' ); ?></span>
								<span class="atables-info-value"><?php echo esc_html( $GLOBALS['wpdb']->db_version() ); ?></span>
							</div>
							<div class="atables-info-row">
								<span class="atables-info-label"><?php esc_html_e( 'Upload Max', 'a-tables-charts' ); ?></span>
								<span class="atables-info-value"><?php echo esc_html( size_format( wp_max_upload_size() ) ); ?></span>
							</div>
							<div class="atables-info-row">
								<span class="atables-info-label"><?php esc_html_e( 'Memory Limit', 'a-tables-charts' ); ?></span>
								<span class="atables-info-value"><?php echo esc_html( WP_MEMORY_LIMIT ); ?></span>
							</div>
						</div>
					</div>
				</div>
				
				<!-- Quick Help -->
				<div class="atables-settings-card">
					<div class="atables-card-header">
						<h2><span class="dashicons dashicons-sos"></span> <?php esc_html_e( 'Need Help?', 'a-tables-charts' ); ?></h2>
					</div>
					<div class="atables-card-body">
						<p><?php esc_html_e( 'Having trouble with settings?', 'a-tables-charts' ); ?></p>
						<ul class="atables-help-list">
							<li>
								<span class="dashicons dashicons-book"></span>
								<a href="#" target="_blank"><?php esc_html_e( 'Documentation', 'a-tables-charts' ); ?></a>
							</li>
							<li>
								<span class="dashicons dashicons-video-alt3"></span>
								<a href="#" target="_blank"><?php esc_html_e( 'Video Tutorials', 'a-tables-charts' ); ?></a>
							</li>
							<li>
								<span class="dashicons dashicons-groups"></span>
								<a href="#" target="_blank"><?php esc_html_e( 'Support Forum', 'a-tables-charts' ); ?></a>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>

<script>
jQuery(document).ready(function($) {
	'use strict';
	
	// Reset to defaults confirmation.
	$('#atables-reset-settings').on('click', function(e) {
		e.preventDefault();
		
		if (confirm('<?php esc_html_e( 'Are you sure you want to reset all settings to default values? This cannot be undone.', 'a-tables-charts' ); ?>')) {
			// Reset all form fields to defaults.
			$('#rows_per_page').val('10');
			$('#default_table_style').val('default');
			$('input[name="atables_settings[enable_responsive]"]').prop('checked', true);
			$('input[name="atables_settings[enable_search]"]').prop('checked', true);
			$('input[name="atables_settings[enable_sorting]"]').prop('checked', true);
			$('input[name="atables_settings[enable_pagination]"]').prop('checked', true);
			$('input[name="atables_settings[enable_export]"]').prop('checked', true);
			$('#date_format').val('Y-m-d');
			$('#time_format').val('H:i:s');
			$('#decimal_separator').val('.');
			$('#thousands_separator').val(',');
			$('input[name="atables_settings[cache_enabled]"]').prop('checked', true);
			$('#cache_expiration').val('3600');
			$('input[name="atables_settings[chartjs_enabled]"]').prop('checked', true);
			$('input[name="atables_settings[google_charts_enabled]"]').prop('checked', true);
			
			// Submit form.
			$('.atables-settings-form').submit();
		}
	});
	
	// Clear cache.
	$('#atables-clear-cache').on('click', function() {
		if (!confirm('<?php esc_html_e( 'Are you sure you want to clear all cached data?', 'a-tables-charts' ); ?>')) {
			return;
		}
		
		const $btn = $(this);
		const originalText = $btn.html();
		
		$btn.prop('disabled', true).html('<span class="dashicons dashicons-update dashicons-spin"></span> Clearing...');
		
		$.ajax({
			url: aTablesAdmin.ajaxUrl,
			type: 'POST',
			data: {
				action: 'atables_clear_cache',
				nonce: aTablesAdmin.nonce
			},
				success: function(response) {
					if (response.success) {
						if (typeof ATablesToast !== 'undefined') {
							ATablesToast.success(response.data.message || 'Cache cleared successfully!');
						}
						setTimeout(() => location.reload(), 1000);
					} else {
						if (typeof ATablesToast !== 'undefined') {
							ATablesToast.error(response.data.message || 'Failed to clear cache.');
						} else {
							alert(response.data.message || 'Failed to clear cache.');
						}
					}
				},
				error: function() {
					if (typeof ATablesToast !== 'undefined') {
						ATablesToast.error('Failed to clear cache.');
					} else {
						alert('Failed to clear cache.');
					}
				},
			complete: function() {
				$btn.prop('disabled', false).html(originalText);
			}
		});
	});
	
	// Reset cache stats.
	$('#atables-reset-cache-stats').on('click', function() {
		if (!confirm('<?php esc_html_e( 'Are you sure you want to reset cache statistics?', 'a-tables-charts' ); ?>')) {
			return;
		}
		
		const $btn = $(this);
		const originalText = $btn.html();
		
		$btn.prop('disabled', true).html('<span class="dashicons dashicons-update dashicons-spin"></span> Resetting...');
		
		$.ajax({
			url: aTablesAdmin.ajaxUrl,
			type: 'POST',
			data: {
				action: 'atables_reset_cache_stats',
				nonce: aTablesAdmin.nonce
			},
				success: function(response) {
					if (response.success) {
						if (typeof ATablesToast !== 'undefined') {
							ATablesToast.success(response.data.message || 'Statistics reset successfully!');
						}
						setTimeout(() => location.reload(), 1000);
					} else {
						if (typeof ATablesToast !== 'undefined') {
							ATablesToast.error(response.data.message || 'Failed to reset statistics.');
						} else {
							alert(response.data.message || 'Failed to reset statistics.');
						}
					}
				},
				error: function() {
					if (typeof ATablesToast !== 'undefined') {
						ATablesToast.error('Failed to reset statistics.');
					} else {
						alert('Failed to reset statistics.');
					}
				},
			complete: function() {
				$btn.prop('disabled', false).html(originalText);
			}
		});
	});
});
</script>

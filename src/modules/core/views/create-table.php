<?php
/**
 * Create Table View
 *
 * Advanced table creation wizard with file upload.
 *
 * @package ATablesCharts\Core
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get settings to check allowed file types
$settings = get_option( 'atables_settings', array() );
$allowed_types = isset( $settings['allowed_file_types'] ) && is_array( $settings['allowed_file_types'] )
	? $settings['allowed_file_types']
	: array( 'csv', 'json', 'xlsx', 'xls', 'xml' );

// Map of import sources to their file types
$source_types = array(
	'csv'   => array( 'csv' ),
	'json'  => array( 'json' ),
	'excel' => array( 'xlsx', 'xls' ),
	'xml'   => array( 'xml' ),
);

// Check which sources are enabled
$enabled_sources = array();
foreach ( $source_types as $source => $types ) {
	foreach ( $types as $type ) {
		if ( in_array( $type, $allowed_types, true ) ) {
			$enabled_sources[] = $source;
			break;
		}
	}
}

// Build accept attribute for file input
$accept_extensions = array();
if ( in_array( 'csv', $allowed_types, true ) ) {
	$accept_extensions[] = '.csv';
	$accept_extensions[] = '.txt';
}
if ( in_array( 'json', $allowed_types, true ) ) {
	$accept_extensions[] = '.json';
}
if ( in_array( 'xlsx', $allowed_types, true ) ) {
	$accept_extensions[] = '.xlsx';
}
if ( in_array( 'xls', $allowed_types, true ) ) {
	$accept_extensions[] = '.xls';
}
if ( in_array( 'xml', $allowed_types, true ) ) {
	$accept_extensions[] = '.xml';
}
$accept_attr = implode( ',', $accept_extensions );

// Build supported formats message
$format_names = array();
if ( in_array( 'csv', $allowed_types, true ) ) {
	$format_names[] = 'CSV';
}
if ( in_array( 'json', $allowed_types, true ) ) {
	$format_names[] = 'JSON';
}
if ( in_array( 'xlsx', $allowed_types, true ) || in_array( 'xls', $allowed_types, true ) ) {
	$format_names[] = 'Excel';
}
if ( in_array( 'xml', $allowed_types, true ) ) {
	$format_names[] = 'XML';
}
$formats_message = implode( ', ', $format_names );
?>

<div class="wrap atables-create-table">
	<h1><?php esc_html_e( 'Create New Table', 'a-tables-charts' ); ?></h1>
	
	<div class="atables-wizard">
		
		<!-- Step 1: Choose Data Source -->
		<div class="atables-step atables-step-1 active">
			<div class="atables-step-header">
				<h2><?php esc_html_e( 'Step 1: Choose Data Source', 'a-tables-charts' ); ?></h2>
				<p><?php esc_html_e( 'Select where your data will come from.', 'a-tables-charts' ); ?></p>
			</div>
			
			<div class="atables-data-sources">
				<?php if ( in_array( 'csv', $enabled_sources, true ) ) : ?>
				<div class="atables-source-card" data-source="csv">
					<div class="atables-source-icon">📄</div>
					<h3><?php esc_html_e( 'CSV Import', 'a-tables-charts' ); ?></h3>
					<p><?php esc_html_e( 'Import from CSV files', 'a-tables-charts' ); ?></p>
				</div>
				<?php endif; ?>
				
				<?php if ( in_array( 'json', $enabled_sources, true ) ) : ?>
				<div class="atables-source-card" data-source="json">
					<div class="atables-source-icon">{ }</div>
					<h3><?php esc_html_e( 'JSON Import', 'a-tables-charts' ); ?></h3>
					<p><?php esc_html_e( 'Import from JSON files', 'a-tables-charts' ); ?></p>
				</div>
				<?php endif; ?>
				
				<?php if ( in_array( 'excel', $enabled_sources, true ) ) : ?>
				<div class="atables-source-card" data-source="excel">
					<div class="atables-source-icon">📊</div>
					<h3><?php esc_html_e( 'Excel Import', 'a-tables-charts' ); ?></h3>
					<p><?php esc_html_e( 'Import from .xlsx files', 'a-tables-charts' ); ?></p>
				</div>
				<?php endif; ?>
				
				<?php if ( in_array( 'xml', $enabled_sources, true ) ) : ?>
				<div class="atables-source-card" data-source="xml">
					<div class="atables-source-icon">🏷️</div>
					<h3><?php esc_html_e( 'XML Import', 'a-tables-charts' ); ?></h3>
					<p><?php esc_html_e( 'Import from XML files', 'a-tables-charts' ); ?></p>
				</div>
				<?php endif; ?>
				
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=a-tables-charts-manual' ) ); ?>" class="atables-source-card" data-source="manual">
					<div class="atables-source-icon">✏️</div>
					<h3><?php esc_html_e( 'Manual Table', 'a-tables-charts' ); ?></h3>
					<p><?php esc_html_e( 'Create from scratch', 'a-tables-charts' ); ?></p>
				</a>
				
				<?php if ( true ) : // Always show MySQL Query option ?>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=a-tables-charts-mysql' ) ); ?>" class="atables-source-card" data-source="mysql">
					<div class="atables-source-icon">🗄️</div>
					<h3><?php esc_html_e( 'MySQL Query', 'a-tables-charts' ); ?></h3>
					<p><?php esc_html_e( 'From SQL database', 'a-tables-charts' ); ?></p>
				</a>
				<?php endif; ?>
				
				<?php if ( true ) : // Always show Google Sheets option ?>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=a-tables-charts-google-sheets' ) ); ?>" class="atables-source-card" data-source="googlesheets">
					<div class="atables-source-icon">📊</div>
					<h3><?php esc_html_e( 'Google Sheets', 'a-tables-charts' ); ?></h3>
					<p><?php esc_html_e( 'Import from Google', 'a-tables-charts' ); ?></p>
				</a>
				<?php endif; ?>
			</div>
			
			<div class="atables-step-footer">
				<button class="button button-primary button-large atables-btn-continue" disabled>
					<?php esc_html_e( 'Continue', 'a-tables-charts' ); ?> →
				</button>
			</div>
		</div>
		
		<!-- Step 2: Upload File -->
		<div class="atables-step atables-step-2">
			<div class="atables-step-header">
				<h2 class="atables-step-title"><?php esc_html_e( 'Step 2: Upload File', 'a-tables-charts' ); ?></h2>
				<p class="atables-step-description"></p>
			</div>
			
			<!-- File Upload Area -->
			<div class="atables-upload-container">
				<div class="atables-upload-area" id="atables-drop-zone">
					<div class="atables-upload-icon">📤</div>
					<h3><?php esc_html_e( 'Drag & Drop your file here', 'a-tables-charts' ); ?></h3>
					<p><?php esc_html_e( 'or click to browse', 'a-tables-charts' ); ?></p>
					<input type="file" id="atables-file-input" accept="<?php echo esc_attr( $accept_attr ); ?>" style="display: none;">
					<button type="button" class="button button-secondary" id="atables-browse-btn">
						<?php esc_html_e( 'Browse Files', 'a-tables-charts' ); ?>
					</button>
					<p class="atables-upload-info">
						<?php
						printf(
							/* translators: 1: Supported formats, 2: Max file size */
							esc_html__( 'Supported formats: %1$s | Max size: %2$s', 'a-tables-charts' ),
							esc_html( $formats_message ),
							esc_html( size_format( $settings['max_import_size'] ?? 10485760 ) )
						);
						?>
					</p>
				</div>
				
				<!-- Selected File Info -->
				<div class="atables-file-info" style="display: none;">
					<div class="atables-file-details">
						<span class="atables-file-icon">📄</span>
						<div class="atables-file-meta">
							<strong class="atables-file-name"></strong>
							<span class="atables-file-size"></span>
						</div>
						<button type="button" class="atables-file-remove" title="<?php esc_attr_e( 'Remove file', 'a-tables-charts' ); ?>">×</button>
					</div>
				</div>
				
				<!-- Import Options -->
				<div class="atables-import-options" style="display: none;">
					<h3><?php esc_html_e( 'Import Options', 'a-tables-charts' ); ?></h3>
					
					<!-- CSV Options -->
					<div class="atables-csv-options" style="display: none;">
						<div class="atables-option-group">
							<label>
								<input type="checkbox" id="atables-has-header" checked>
								<?php esc_html_e( 'First row contains column headers', 'a-tables-charts' ); ?>
							</label>
						</div>
						<div class="atables-option-group">
							<label for="atables-delimiter"><?php esc_html_e( 'Delimiter:', 'a-tables-charts' ); ?></label>
							<select id="atables-delimiter">
								<option value="auto"><?php esc_html_e( 'Auto-detect', 'a-tables-charts' ); ?></option>
								<option value=","><?php esc_html_e( 'Comma (,)', 'a-tables-charts' ); ?></option>
								<option value=";"><?php esc_html_e( 'Semicolon (;)', 'a-tables-charts' ); ?></option>
								<option value="\t"><?php esc_html_e( 'Tab', 'a-tables-charts' ); ?></option>
								<option value="|"><?php esc_html_e( 'Pipe (|)', 'a-tables-charts' ); ?></option>
							</select>
						</div>
						<div class="atables-option-group">
							<label for="atables-encoding"><?php esc_html_e( 'Encoding:', 'a-tables-charts' ); ?></label>
							<select id="atables-encoding">
								<option value="UTF-8">UTF-8</option>
								<option value="ISO-8859-1">ISO-8859-1</option>
								<option value="Windows-1252">Windows-1252</option>
							</select>
						</div>
					</div>
					
					<!-- JSON Options -->
					<div class="atables-json-options" style="display: none;">
						<div class="atables-option-group">
							<label>
								<input type="checkbox" id="atables-flatten-nested" checked>
								<?php esc_html_e( 'Flatten nested structures', 'a-tables-charts' ); ?>
							</label>
						</div>
						<div class="atables-option-group">
							<label for="atables-array-key"><?php esc_html_e( 'Array key (if nested):', 'a-tables-charts' ); ?></label>
							<input type="text" id="atables-array-key" placeholder="e.g., data, items, users" class="regular-text">
							<p class="description"><?php esc_html_e( 'Leave empty if JSON is already an array', 'a-tables-charts' ); ?></p>
						</div>
					</div>
					
					<!-- Excel Options -->
					<div class="atables-excel-options" style="display: none;">
						<div class="atables-option-group">
							<label>
								<input type="checkbox" id="atables-excel-has-header" checked>
								<?php esc_html_e( 'First row contains column headers', 'a-tables-charts' ); ?>
							</label>
						</div>
						<div class="atables-option-group">
							<label for="atables-sheet-select"><?php esc_html_e( 'Select Sheet:', 'a-tables-charts' ); ?></label>
							<select id="atables-sheet-select" class="regular-text">
								<option value="0"><?php esc_html_e( 'First Sheet', 'a-tables-charts' ); ?></option>
							</select>
							<p class="description"><?php esc_html_e( 'Select which sheet to import', 'a-tables-charts' ); ?></p>
						</div>
					</div>
					
					<!-- XML Options -->
					<div class="atables-xml-options" style="display: none;">
						<div class="atables-option-group">
							<label>
								<input type="checkbox" id="atables-xml-flatten" checked>
								<?php esc_html_e( 'Flatten nested elements', 'a-tables-charts' ); ?>
							</label>
							<p class="description"><?php esc_html_e( 'Convert nested XML elements to flat table structure', 'a-tables-charts' ); ?></p>
						</div>
					</div>
				</div>
				
				<!-- Progress Bar -->
				<div class="atables-progress" style="display: none;">
					<div class="atables-progress-bar">
						<div class="atables-progress-fill"></div>
					</div>
					<p class="atables-progress-text"></p>
				</div>
			</div>
			
			<div class="atables-step-footer">
				<button class="button button-secondary atables-btn-back">
					← <?php esc_html_e( 'Back', 'a-tables-charts' ); ?>
				</button>
				<button class="button button-primary button-large atables-btn-import" disabled>
					<?php esc_html_e( 'Import & Preview', 'a-tables-charts' ); ?>
				</button>
			</div>
		</div>
		
		<!-- Step 3: Preview & Configure -->
		<div class="atables-step atables-step-3">
			<div class="atables-step-header">
				<h2><?php esc_html_e( 'Step 3: Preview & Configure', 'a-tables-charts' ); ?></h2>
				<p><?php esc_html_e( 'Review your data and configure table settings', 'a-tables-charts' ); ?></p>
			</div>
			
			<!-- Import Summary -->
			<div class="atables-import-summary">
				<div class="atables-summary-stats">
					<div class="atables-stat">
						<span class="atables-stat-label"><?php esc_html_e( 'Rows:', 'a-tables-charts' ); ?></span>
						<span class="atables-stat-value" id="atables-row-count">0</span>
					</div>
					<div class="atables-stat">
						<span class="atables-stat-label"><?php esc_html_e( 'Columns:', 'a-tables-charts' ); ?></span>
						<span class="atables-stat-value" id="atables-column-count">0</span>
					</div>
				</div>
			</div>
			
			<!-- Table Name -->
			<div class="atables-table-name-section">
				<label for="atables-table-title">
					<strong><?php esc_html_e( 'Table Name:', 'a-tables-charts' ); ?></strong>
				</label>
				<input type="text" id="atables-table-title" class="widefat" placeholder="<?php esc_attr_e( 'Enter table name...', 'a-tables-charts' ); ?>">
			</div>
			
			<!-- Data Preview -->
			<div class="atables-preview-container">
				<h3><?php esc_html_e( 'Data Preview', 'a-tables-charts' ); ?></h3>
				<div class="atables-preview-wrapper">
					<div id="atables-data-preview"></div>
				</div>
				<p class="atables-preview-note">
					<?php esc_html_e( 'Showing first 10 rows', 'a-tables-charts' ); ?>
				</p>
			</div>
			
			<div class="atables-step-footer">
				<button class="button button-secondary atables-btn-back">
					← <?php esc_html_e( 'Back', 'a-tables-charts' ); ?>
				</button>
				<button class="button button-primary button-large atables-btn-save">
					<?php esc_html_e( 'Save Table', 'a-tables-charts' ); ?>
				</button>
			</div>
		</div>
		
	</div>
</div>

<!-- Success/Error Messages -->
<div class="atables-notice atables-notice-success" style="display: none;">
	<p></p>
</div>
<div class="atables-notice atables-notice-error" style="display: none;">
	<p></p>
</div>

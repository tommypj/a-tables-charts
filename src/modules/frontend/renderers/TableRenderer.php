<?php
/**
 * Table Renderer - Simple Version
 *
 * Renders tables for frontend display.
 *
 * @package ATablesCharts\Frontend\Renderers
 * @since 1.0.0
 */

namespace ATablesCharts\Frontend\Renderers;

use ATablesCharts\Shared\Utils\ContentFormatter;
use ATablesCharts\Shared\Utils\ColumnFormatter;

class TableRenderer {

	public function render( $options ) {
	$table = $options['table'];
	$data = $options['data'];
	$headers = $table->get_headers();
	$width = isset( $options['width'] ) ? $options['width'] : '100%';
	$style = isset( $options['style'] ) ? $options['style'] : 'default';
	$responsive_mode = isset( $options['responsive_mode'] ) ? $options['responsive_mode'] : 'scroll';
	
	// Interactive features configuration
	$table_id    = isset( $options['table_id'] ) ? $options['table_id'] : 0;
	$search      = isset( $options['search'] ) ? $options['search'] : 'true';
	$pagination  = isset( $options['pagination'] ) ? $options['pagination'] : 'true';
	$page_length = isset( $options['page_length'] ) ? $options['page_length'] : '10';
	$sorting     = isset( $options['sorting'] ) ? $options['sorting'] : 'true';
	$info        = isset( $options['info'] ) ? $options['info'] : 'true';
	
	// Map style to theme class
	$theme_map = array(
	'classic'  => 'atables-theme-classic',
	'modern'   => 'atables-theme-modern',
	'dark'     => 'atables-theme-dark',
	'minimal'  => 'atables-theme-minimal',
	'striped'  => 'atables-theme-striped',
	'material' => 'atables-theme-material',
	 'default'  => 'atables-theme-classic', // Default to classic
	);
	
	// Map responsive mode to wrapper class
		$responsive_class_map = array(
			'scroll' => 'atables-responsive-scroll',
			'stack'  => 'atables-responsive-stack',
			'cards'  => 'atables-responsive-cards',
		);
		
		$theme_class = isset( $theme_map[ $style ] ) ? $theme_map[ $style ] : 'atables-theme-classic';
		$responsive_class = isset( $responsive_class_map[ $responsive_mode ] ) ? $responsive_class_map[ $responsive_mode ] : 'atables-responsive-scroll';
		$table_classes = array( 'atables-table', $theme_class );

		ob_start();
		?>
		<div class="atables-frontend-wrapper <?php echo esc_attr( $responsive_class ); ?>" style="max-width: <?php echo esc_attr( $width ); ?>;">
			<!-- Skip link for keyboard users -->
			<a href="#atables-table-<?php echo esc_attr( $table_id ); ?>" class="atables-skip-link">
				<?php esc_html_e( 'Skip to table content', 'a-tables-charts' ); ?>
			</a>

			<!-- Toolbar with Export Buttons -->
			<div class="atables-toolbar" role="toolbar" aria-label="<?php esc_attr_e( 'Table actions', 'a-tables-charts' ); ?>">
				<div class="atables-toolbar-left">
					<h3 class="atables-table-title"><?php echo esc_html( $table->title ); ?></h3>
					<?php if ( ! empty( $table->description ) ) : ?>
						<p class="atables-table-description" id="table-desc-<?php echo esc_attr( $table_id ); ?>">
							<?php echo esc_html( $table->description ); ?>
						</p>
					<?php endif; ?>
				</div>
				<div class="atables-toolbar-right">
					<div class="atables-export-buttons">
						<button type="button"
						        class="atables-export-btn atables-copy-btn"
						        data-action="copy"
						        data-table-id="<?php echo esc_attr( $table_id ); ?>"
						        aria-label="<?php esc_attr_e( 'Copy table to clipboard', 'a-tables-charts' ); ?>">
							<span class="dashicons dashicons-admin-page" aria-hidden="true"></span>
							<span class="atables-btn-text"><?php esc_html_e( 'Copy', 'a-tables-charts' ); ?></span>
						</button>
						<button type="button"
						        class="atables-export-btn atables-print-btn"
						        data-action="print"
						        data-table-id="<?php echo esc_attr( $table_id ); ?>"
						        aria-label="<?php esc_attr_e( 'Print table', 'a-tables-charts' ); ?>">
							<span class="dashicons dashicons-printer" aria-hidden="true"></span>
							<span class="atables-btn-text"><?php esc_html_e( 'Print', 'a-tables-charts' ); ?></span>
						</button>
						<?php
						// Get export links
						$export_base_url = admin_url( 'admin-ajax.php' );
						$export_nonce = wp_create_nonce( 'atables_export_nonce' );
						
						// Excel export
						$excel_url = add_query_arg( array(
							'action'   => 'atables_export_table',
							'table_id' => $table_id,
							'format'   => 'xlsx',
							'nonce'    => $export_nonce,
						), $export_base_url );
						
						// CSV export
						$csv_url = add_query_arg( array(
							'action'   => 'atables_export_table',
							'table_id' => $table_id,
							'format'   => 'csv',
							'nonce'    => $export_nonce,
						), $export_base_url );
						
						// PDF export
						$pdf_url = add_query_arg( array(
							'action'   => 'atables_export_table',
							'table_id' => $table_id,
							'format'   => 'pdf',
							'nonce'    => $export_nonce,
						), $export_base_url );
						?>
						<a href="<?php echo esc_url( $excel_url ); ?>"
						   class="atables-export-btn"
						   download
						   aria-label="<?php esc_attr_e( 'Download table as Excel file (.xlsx)', 'a-tables-charts' ); ?>">
							<span class="dashicons dashicons-media-spreadsheet" aria-hidden="true"></span>
							<span class="atables-btn-text"><?php esc_html_e( 'Excel', 'a-tables-charts' ); ?></span>
						</a>
						<a href="<?php echo esc_url( $csv_url ); ?>"
						   class="atables-export-btn"
						   download
						   aria-label="<?php esc_attr_e( 'Download table as CSV file (.csv)', 'a-tables-charts' ); ?>">
							<span class="dashicons dashicons-database-export" aria-hidden="true"></span>
							<span class="atables-btn-text"><?php esc_html_e( 'CSV', 'a-tables-charts' ); ?></span>
						</a>
						<a href="<?php echo esc_url( $pdf_url ); ?>"
						   class="atables-export-btn"
						   download
						   aria-label="<?php esc_attr_e( 'Download table as PDF file (.pdf)', 'a-tables-charts' ); ?>">
							<span class="dashicons dashicons-pdf" aria-hidden="true"></span>
							<span class="atables-btn-text"><?php esc_html_e( 'PDF', 'a-tables-charts' ); ?></span>
						</a>
					</div>
				</div>
			</div>

			<div class="atables-frontend-table-wrapper">
				<!-- ARIA live region for table status updates -->
				<div aria-live="polite"
				     aria-atomic="false"
				     class="atables-sr-only"
				     id="table-status-<?php echo esc_attr( $table_id ); ?>">
				</div>

				<table id="atables-table-<?php echo esc_attr( $table_id ); ?>"
				       class="<?php echo esc_attr( implode( ' ', $table_classes ) ); ?> atables-interactive"
				       <?php if ( ! empty( $table->description ) ) : ?>
				       aria-describedby="table-desc-<?php echo esc_attr( $table_id ); ?>"
				       <?php endif; ?>
				       data-search="<?php echo esc_attr( $search ); ?>"
				       data-pagination="<?php echo esc_attr( $pagination ); ?>"
				       data-page-length="<?php echo esc_attr( $page_length ); ?>"
				       data-sorting="<?php echo esc_attr( $sorting ); ?>"
				       data-info="<?php echo esc_attr( $info ); ?>">
					<caption class="atables-sr-only">
						<?php echo esc_html( $table->title ); ?>
						<?php if ( ! empty( $table->description ) ) : ?>
							- <?php echo esc_html( $table->description ); ?>
						<?php endif; ?>
					</caption>
					<thead>
						<tr>
							<?php foreach ( $headers as $header ) : ?>
								<th scope="col"><?php echo esc_html( $header ); ?></th>
							<?php endforeach; ?>
						</tr>
					</thead>
					<tbody>
						<?php if ( ! empty( $data ) ) : ?>
							<?php foreach ( $data as $row ) : ?>
								<tr>
									<?php foreach ( $headers as $header ) : ?>
										<?php
										$cell_value = isset( $row[ $header ] ) ? $row[ $header ] : '';
										// Auto-format URLs, images, emails
										$formatted_value = ContentFormatter::format( $cell_value, array(
											'auto_link'  => true,
											'auto_image' => true,
											'auto_email' => true,
											'image_size' => 'medium',
										) );
										?>
										<td data-label="<?php echo esc_attr( $header ); ?>"><?php echo $formatted_value; // Already escaped in formatter ?></td>
									<?php endforeach; ?>
								</tr>
							<?php endforeach; ?>
						<?php else : ?>
							<tr>
								<td colspan="<?php echo count( $headers ); ?>">
									<?php esc_html_e( 'No data available.', 'a-tables-charts' ); ?>
								</td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
}

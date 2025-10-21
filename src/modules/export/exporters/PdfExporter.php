<?php
/**
 * PDF Exporter
 *
 * Handles PDF file export using TCPDF library.
 *
 * @package ATablesCharts\Export\Exporters
 * @since 1.0.0
 */

namespace ATablesCharts\Export\Exporters;

use TCPDF;

/**
 * PdfExporter Class
 *
 * Exports table data to PDF format with professional styling.
 * Handles automatic page breaks, column width calculation,
 * and proper text wrapping for large datasets.
 */
class PdfExporter {

	/**
	 * Page orientation (L=Landscape, P=Portrait)
	 *
	 * @var string
	 */
	private $page_orientation = 'L';

	/**
	 * Page format (A4, Letter, Legal, etc.)
	 *
	 * @var string
	 */
	private $page_format = 'A4';

	/**
	 * Page margins
	 *
	 * @var int
	 */
	private $margin_left = 15;
	private $margin_right = 15;
	private $margin_top = 20;
	private $margin_bottom = 20;

	/**
	 * Header styling
	 *
	 * @var array
	 */
	private $header_bg_color = array( 34, 113, 177 ); // #2271B1 WordPress blue
	private $header_text_color = array( 255, 255, 255 ); // White
	private $header_font_size = 11;

	/**
	 * Body styling
	 *
	 * @var int
	 */
	private $body_font_size = 9;

	/**
	 * Border styling
	 *
	 * @var array
	 */
	private $border_color = array( 200, 200, 200 );

	/**
	 * Export data to PDF format
	 *
	 * @param array  $headers Column headers.
	 * @param array  $data    Table data.
	 * @param string $filename Export filename.
	 * @param array  $options Export options (orientation, title, etc.).
	 * @return void Outputs PDF file
	 */
	public function export( $headers, $data, $filename = 'export.pdf', $options = array() ) {
		// Check if TCPDF is available
		if ( ! class_exists( 'TCPDF' ) ) {
			wp_die(
				'TCPDF library is not installed. Please run "composer require tecnickcom/tcpdf" in the plugin directory.',
				'PDF Export Error',
				array( 'response' => 500 )
			);
		}

		try {
			// Parse options
			$this->parse_options( $options, $headers );

			// Initialize PDF document
			$pdf = $this->initialize_pdf( $options['title'] ?? 'Table Export' );

			// Calculate column widths
			$column_widths = $this->calculate_column_widths( $headers, $pdf );

			// Add first page
			$pdf->AddPage();

			// Render table header
			$this->render_header( $pdf, $headers, $column_widths );

			// Render table rows
			$this->render_rows( $pdf, $data, $headers, $column_widths );

			// Output file
			$this->output_file( $pdf, $filename );

		} catch ( \Exception $e ) {
			wp_die(
				'PDF export failed: ' . esc_html( $e->getMessage() ),
				'PDF Export Error',
				array( 'response' => 500 )
			);
		}
	}

	/**
	 * Parse export options and set properties
	 *
	 * @param array $options Export options.
	 * @param array $headers Column headers for auto-detection.
	 * @return void
	 */
	private function parse_options( $options, $headers ) {
		// Set orientation
		if ( isset( $options['orientation'] ) ) {
			if ( $options['orientation'] === 'auto' ) {
				// Auto-detect based on column count
				$this->page_orientation = $this->detect_orientation( count( $headers ) );
			} elseif ( $options['orientation'] === 'portrait' ) {
				$this->page_orientation = 'P';
			} elseif ( $options['orientation'] === 'landscape' ) {
				$this->page_orientation = 'L';
			}
		} else {
			// Default: auto-detect
			$this->page_orientation = $this->detect_orientation( count( $headers ) );
		}

		// Set font size from settings
		if ( isset( $options['font_size'] ) ) {
			$this->body_font_size = (int) $options['font_size'];
		}
	}

	/**
	 * Initialize PDF document with settings
	 *
	 * @param string $title Document title.
	 * @return TCPDF PDF instance
	 */
	private function initialize_pdf( $title ) {
		// Create new PDF document
		$pdf = new TCPDF(
			$this->page_orientation,
			'mm',
			$this->page_format,
			true,
			'UTF-8',
			false
		);

		// Set document information
		$pdf->SetCreator( 'A-Tables & Charts WordPress Plugin' );
		$pdf->SetAuthor( get_bloginfo( 'name' ) );
		$pdf->SetTitle( $title );
		$pdf->SetSubject( 'Table Export' );

		// Set margins
		$pdf->SetMargins(
			$this->margin_left,
			$this->margin_top,
			$this->margin_right
		);

		// Set auto page breaks
		$pdf->SetAutoPageBreak( true, $this->margin_bottom );

		// Set font
		$pdf->SetFont( 'helvetica', '', $this->body_font_size );

		// Remove default header/footer
		$pdf->setPrintHeader( false );
		$pdf->setPrintFooter( false );

		return $pdf;
	}

	/**
	 * Calculate column widths based on available page width
	 *
	 * @param array $headers Column headers.
	 * @param TCPDF $pdf     PDF instance.
	 * @return array Column widths
	 */
	private function calculate_column_widths( $headers, $pdf ) {
		$page_width = $pdf->getPageWidth() - $this->margin_left - $this->margin_right;
		$column_count = count( $headers );

		// Simple equal distribution for now
		// TODO: Could be enhanced to calculate based on content length
		$column_width = $page_width / $column_count;

		return array_fill( 0, $column_count, $column_width );
	}

	/**
	 * Render table header with styling
	 *
	 * @param TCPDF $pdf           PDF instance.
	 * @param array $headers       Column headers.
	 * @param array $column_widths Column widths.
	 * @return void
	 */
	private function render_header( $pdf, $headers, $column_widths ) {
		// Set header styling
		$pdf->SetFillColor(
			$this->header_bg_color[0],
			$this->header_bg_color[1],
			$this->header_bg_color[2]
		);
		$pdf->SetTextColor(
			$this->header_text_color[0],
			$this->header_text_color[1],
			$this->header_text_color[2]
		);
		$pdf->SetDrawColor(
			$this->border_color[0],
			$this->border_color[1],
			$this->border_color[2]
		);
		$pdf->SetLineWidth( 0.3 );
		$pdf->SetFont( '', 'B', $this->header_font_size );

		// Calculate row height
		$row_height = 8;

		// Render each header cell
		foreach ( $headers as $index => $header ) {
			$pdf->MultiCell(
				$column_widths[ $index ],
				$row_height,
				$this->sanitize_text( $header ),
				1,
				'C',
				true,
				0,
				'',
				'',
				true,
				0,
				false,
				true,
				$row_height,
				'M'
			);
		}

		// Move to next line
		$pdf->Ln();

		// Reset text color for body
		$pdf->SetTextColor( 0, 0, 0 );
		$pdf->SetFont( '', '', $this->body_font_size );
	}

	/**
	 * Render table data rows
	 *
	 * @param TCPDF $pdf           PDF instance.
	 * @param array $data          Table data.
	 * @param array $headers       Column headers.
	 * @param array $column_widths Column widths.
	 * @return void
	 */
	private function render_rows( $pdf, $data, $headers, $column_widths ) {
		$row_height = 6;

		// Set fill color for alternating rows
		$fill = false;

		foreach ( $data as $row_index => $row_data ) {
			// Check if we need a new page
			if ( $pdf->GetY() > $pdf->getPageHeight() - $this->margin_bottom - 20 ) {
				$pdf->AddPage();
				// Re-render header on new page
				$this->render_header( $pdf, $headers, $column_widths );
			}

			// Set fill color for alternating rows
			if ( $fill ) {
				$pdf->SetFillColor( 245, 245, 245 );
			} else {
				$pdf->SetFillColor( 255, 255, 255 );
			}

			// Store current Y position
			$start_y = $pdf->GetY();
			$max_height = 0;

			// First pass: determine maximum cell height in this row
			foreach ( $headers as $index => $header ) {
				$value = isset( $row_data[ $header ] ) ? $row_data[ $header ] : '';
				$cell_height = $pdf->getStringHeight(
					$column_widths[ $index ],
					$this->sanitize_text( $value )
				);
				$max_height = max( $max_height, $cell_height );
			}

			// Second pass: render cells with uniform height
			foreach ( $headers as $index => $header ) {
				$value = isset( $row_data[ $header ] ) ? $row_data[ $header ] : '';

				$pdf->MultiCell(
					$column_widths[ $index ],
					$max_height,
					$this->sanitize_text( $value ),
					1,
					'L',
					$fill,
					0,
					'',
					'',
					true,
					0,
					false,
					true,
					$max_height,
					'T'
				);
			}

			// Move to next row
			$pdf->Ln();

			// Toggle fill
			$fill = ! $fill;
		}
	}

	/**
	 * Output PDF file to browser
	 *
	 * @param TCPDF  $pdf      PDF instance.
	 * @param string $filename Filename for download.
	 * @return void
	 */
	private function output_file( $pdf, $filename ) {
		// Clean output buffer
		if ( ob_get_level() ) {
			ob_end_clean();
		}

		// Output PDF to browser
		$pdf->Output( $filename, 'D' ); // 'D' = Force download

		exit;
	}

	/**
	 * Sanitize text for PDF output
	 *
	 * Removes HTML tags, decodes entities, and handles special characters.
	 *
	 * @param string $text Text to sanitize.
	 * @return string Sanitized text
	 */
	private function sanitize_text( $text ) {
		// Convert to string if not already
		$text = (string) $text;

		// Decode HTML entities
		$text = html_entity_decode( $text, ENT_QUOTES | ENT_HTML5, 'UTF-8' );

		// Strip HTML tags
		$text = wp_strip_all_tags( $text );

		// Remove extra whitespace
		$text = trim( preg_replace( '/\s+/', ' ', $text ) );

		return $text;
	}

	/**
	 * Auto-detect best orientation based on column count
	 *
	 * @param int $column_count Number of columns.
	 * @return string 'L' for landscape, 'P' for portrait
	 */
	private function detect_orientation( $column_count ) {
		// Use landscape for tables with many columns
		return ( $column_count > 6 ) ? 'L' : 'P';
	}
}

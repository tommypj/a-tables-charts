<?php
/**
 * Cell Shortcode
 *
 * Handles the [atable_cell] shortcode for displaying a single cell value.
 *
 * @package ATablesCharts\Frontend\Shortcodes
 * @since 1.0.0
 */

namespace ATablesCharts\Frontend\Shortcodes;

use ATablesCharts\Tables\Repositories\TableRepository;

/**
 * Cell Shortcode Class
 *
 * Allows embedding single cell values anywhere on the site.
 * Usage: [atable_cell table_id="1" row="0" column="Name"]
 */
class CellShortcode {

	/**
	 * Table Repository
	 *
	 * @var TableRepository
	 */
	private $repository;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->repository = new TableRepository();
	}

	/**
	 * Register shortcode
	 */
	public function register() {
		add_shortcode( 'atable_cell', array( $this, 'render' ) );
	}

	/**
	 * Render single cell value
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string Cell value or error message.
	 */
	public function render( $atts ) {
		// Parse attributes
		$atts = shortcode_atts(
			array(
				'table_id' => 0,
				'row'      => 1, // User-friendly: 1-based (first row = 1)
				'column'   => '',
				'default'  => '', // Default value if cell is empty
				'format'   => 'text', // text, number, currency
				'prefix'   => '', // Prefix (e.g., $, €)
				'suffix'   => '', // Suffix (e.g., %, kg)
			),
			$atts,
			'atable_cell'
		);

		// Validate table ID
		$table_id = (int) $atts['table_id'];
		if ( empty( $table_id ) ) {
			return $this->error( __( 'Table ID is required.', 'a-tables-charts' ) );
		}

		// Get table
		$table = $this->repository->find_by_id( $table_id );
		if ( ! $table ) {
			return $this->error( __( 'Table not found.', 'a-tables-charts' ) );
		}

		// Check if table is active
		if ( $table->status !== 'active' ) {
			return $this->error( __( 'Table is not available.', 'a-tables-charts' ) );
		}

		// Get table data
		$data = $this->repository->get_table_data( $table_id );
		if ( empty( $data ) ) {
			return $this->error( __( 'Table has no data.', 'a-tables-charts' ) );
		}

		// Convert 1-based row number to 0-based index
		$row_number = (int) $atts['row'];
		$row_index = $row_number - 1; // Convert to 0-based index
		
		if ( $row_index < 0 || $row_index >= count( $data ) ) {
			return $this->error( 
				sprintf(
					/* translators: 1: provided row number, 2: max row number */
					__( 'Row %1$d is out of range. This table has %2$d rows.', 'a-tables-charts' ),
					$row_number,
					count( $data )
				)
			);
		}

		// Get column index
		$column = $atts['column'];
		$headers = $table->get_headers();
		
		// Column can be specified by name or index
		if ( is_numeric( $column ) ) {
			$column_index = (int) $column;
		} else {
			// Find column by name
			$column_index = array_search( $column, $headers, true );
			if ( $column_index === false ) {
				return $this->error( 
					sprintf(
						/* translators: %s: column name */
						__( 'Column "%s" not found.', 'a-tables-charts' ),
						esc_html( $column )
					)
				);
			}
		}

		// Validate column index
		if ( $column_index < 0 || $column_index >= count( $headers ) ) {
			return $this->error( __( 'Column index out of range.', 'a-tables-charts' ) );
		}

		// Get cell value
		$row = $data[ $row_index ];
		$cell_value = isset( $row[ $column_index ] ) ? $row[ $column_index ] : '';

		// Use default if cell is empty
		if ( empty( $cell_value ) && ! empty( $atts['default'] ) ) {
			$cell_value = $atts['default'];
		}

		// Format the value
		$formatted_value = $this->format_value( $cell_value, $atts );

		// Return formatted value
		return '<span class="atable-cell-value" data-table-id="' . esc_attr( $table_id ) . '" data-row="' . esc_attr( $row_number ) . '" data-column="' . esc_attr( $column_index ) . '">' . $formatted_value . '</span>';
	}

	/**
	 * Format cell value
	 *
	 * @param mixed $value Cell value.
	 * @param array $atts  Shortcode attributes.
	 * @return string Formatted value.
	 */
	private function format_value( $value, $atts ) {
		$format = $atts['format'];
		$prefix = $atts['prefix'];
		$suffix = $atts['suffix'];

		// Apply formatting based on type
		switch ( $format ) {
			case 'number':
				// Format as number with thousands separator
				if ( is_numeric( $value ) ) {
					$settings = get_option( 'atables_settings', array() );
					$decimal_separator = $settings['decimal_separator'] ?? '.';
					$thousands_separator = $settings['thousands_separator'] ?? ',';
					
					$value = number_format(
						(float) $value,
						$this->get_decimal_places( $value ),
						$decimal_separator,
						$thousands_separator
					);
				}
				break;

			case 'currency':
				// Format as currency
				if ( is_numeric( $value ) ) {
					$settings = get_option( 'atables_settings', array() );
					$decimal_separator = $settings['decimal_separator'] ?? '.';
					$thousands_separator = $settings['thousands_separator'] ?? ',';
					
					$value = number_format(
						(float) $value,
						2,
						$decimal_separator,
						$thousands_separator
					);
					
					// Add currency symbol if not already prefixed
					if ( empty( $prefix ) ) {
						$prefix = '$';
					}
				}
				break;

			case 'text':
			default:
				// Keep as text
				$value = esc_html( $value );
				break;
		}

		// Add prefix and suffix
		return $prefix . $value . $suffix;
	}

	/**
	 * Get number of decimal places in a number
	 *
	 * @param mixed $number Number to check.
	 * @return int Number of decimal places.
	 */
	private function get_decimal_places( $number ) {
		$string = (string) $number;
		if ( strpos( $string, '.' ) !== false ) {
			$parts = explode( '.', $string );
			return strlen( $parts[1] );
		}
		return 0;
	}

	/**
	 * Return error message
	 *
	 * @param string $message Error message.
	 * @return string Formatted error.
	 */
	private function error( $message ) {
		return '<span class="atable-cell-error" style="color: #d63638; font-weight: 600;">[Table Cell Error: ' . esc_html( $message ) . ']</span>';
	}
}

<?php
/**
 * Column Formatter
 *
 * Handles column formatting (alignment, width, colors, etc.)
 *
 * @package ATablesCharts\Shared\Utils
 * @since 1.0.0
 */

namespace ATablesCharts\Shared\Utils;

/**
 * ColumnFormatter Class
 *
 * Responsibilities:
 * - Generate column styles
 * - Apply alignment
 * - Set column widths
 * - Apply colors
 * - Generate CSS classes
 */
class ColumnFormatter {

	/**
	 * Get column style attribute
	 *
	 * @param array $column_settings Column settings.
	 * @return string Style attribute string
	 */
	public static function get_column_style( $column_settings ) {
		if ( empty( $column_settings ) ) {
			return '';
		}

		$styles = array();

		// Alignment
		if ( ! empty( $column_settings['align'] ) ) {
			$valid_aligns = array( 'left', 'center', 'right', 'justify' );
			if ( in_array( $column_settings['align'], $valid_aligns, true ) ) {
				$styles[] = 'text-align: ' . esc_attr( $column_settings['align'] );
			}
		}

		// Width
		if ( ! empty( $column_settings['width'] ) ) {
			$width = $column_settings['width'];
			// Add 'px' if numeric only
			if ( is_numeric( $width ) ) {
				$width .= 'px';
			}
			$styles[] = 'width: ' . esc_attr( $width );
		}

		// Min width
		if ( ! empty( $column_settings['min_width'] ) ) {
			$min_width = $column_settings['min_width'];
			if ( is_numeric( $min_width ) ) {
				$min_width .= 'px';
			}
			$styles[] = 'min-width: ' . esc_attr( $min_width );
		}

		// Max width
		if ( ! empty( $column_settings['max_width'] ) ) {
			$max_width = $column_settings['max_width'];
			if ( is_numeric( $max_width ) ) {
				$max_width .= 'px';
			}
			$styles[] = 'max-width: ' . esc_attr( $max_width );
		}

		// Text color
		if ( ! empty( $column_settings['color'] ) ) {
			$styles[] = 'color: ' . esc_attr( $column_settings['color'] );
		}

		// Background color
		if ( ! empty( $column_settings['background_color'] ) ) {
			$styles[] = 'background-color: ' . esc_attr( $column_settings['background_color'] );
		}

		// Font weight
		if ( ! empty( $column_settings['font_weight'] ) ) {
			$valid_weights = array( 'normal', 'bold', '100', '200', '300', '400', '500', '600', '700', '800', '900' );
			if ( in_array( $column_settings['font_weight'], $valid_weights, true ) ) {
				$styles[] = 'font-weight: ' . esc_attr( $column_settings['font_weight'] );
			}
		}

		// Font style
		if ( ! empty( $column_settings['font_style'] ) ) {
			$valid_styles = array( 'normal', 'italic', 'oblique' );
			if ( in_array( $column_settings['font_style'], $valid_styles, true ) ) {
				$styles[] = 'font-style: ' . esc_attr( $column_settings['font_style'] );
			}
		}

		// Font size
		if ( ! empty( $column_settings['font_size'] ) ) {
			$font_size = $column_settings['font_size'];
			if ( is_numeric( $font_size ) ) {
				$font_size .= 'px';
			}
			$styles[] = 'font-size: ' . esc_attr( $font_size );
		}

		// Padding
		if ( ! empty( $column_settings['padding'] ) ) {
			$styles[] = 'padding: ' . esc_attr( $column_settings['padding'] );
		}

		// Vertical align
		if ( ! empty( $column_settings['vertical_align'] ) ) {
			$valid_vertical = array( 'top', 'middle', 'bottom', 'baseline' );
			if ( in_array( $column_settings['vertical_align'], $valid_vertical, true ) ) {
				$styles[] = 'vertical-align: ' . esc_attr( $column_settings['vertical_align'] );
			}
		}

		// Word wrap
		if ( isset( $column_settings['word_wrap'] ) && $column_settings['word_wrap'] === false ) {
			$styles[] = 'white-space: nowrap';
		}

		// Text transform
		if ( ! empty( $column_settings['text_transform'] ) ) {
			$valid_transforms = array( 'none', 'uppercase', 'lowercase', 'capitalize' );
			if ( in_array( $column_settings['text_transform'], $valid_transforms, true ) ) {
				$styles[] = 'text-transform: ' . esc_attr( $column_settings['text_transform'] );
			}
		}

		if ( empty( $styles ) ) {
			return '';
		}

		return implode( '; ', $styles ) . ';';
	}

	/**
	 * Get column CSS classes
	 *
	 * @param array $column_settings Column settings.
	 * @return string CSS classes
	 */
	public static function get_column_classes( $column_settings ) {
		if ( empty( $column_settings ) ) {
			return '';
		}

		$classes = array();

		// Custom CSS class
		if ( ! empty( $column_settings['css_class'] ) ) {
			$classes[] = sanitize_html_class( $column_settings['css_class'] );
		}

		// Responsive priority class
		if ( ! empty( $column_settings['responsive_priority'] ) ) {
			$priority = (int) $column_settings['responsive_priority'];
			if ( $priority >= 1 && $priority <= 3 ) {
				$classes[] = 'atables-priority-' . $priority;
			}
		}

		// Highlight class
		if ( ! empty( $column_settings['highlight'] ) && $column_settings['highlight'] === true ) {
			$classes[] = 'atables-highlight';
		}

		return implode( ' ', $classes );
	}

	/**
	 * Get header cell style
	 *
	 * @param array $column_settings Column settings.
	 * @return string Style attribute for header cell
	 */
	public static function get_header_style( $column_settings ) {
		if ( empty( $column_settings ) || empty( $column_settings['header_style'] ) ) {
			return '';
		}

		$header_settings = $column_settings['header_style'];
		return self::get_column_style( $header_settings );
	}

	/**
	 * Apply column settings to table
	 *
	 * @param array $headers         Table headers.
	 * @param array $column_settings All column settings (keyed by column name).
	 * @return array Headers with formatting applied
	 */
	public static function apply_to_headers( $headers, $column_settings ) {
		$formatted_headers = array();

		foreach ( $headers as $header ) {
			$settings = isset( $column_settings[ $header ] ) ? $column_settings[ $header ] : array();
			
			$formatted_headers[] = array(
				'name'    => $header,
				'style'   => self::get_column_style( $settings ),
				'classes' => self::get_column_classes( $settings ),
			);
		}

		return $formatted_headers;
	}

	/**
	 * Get default column settings
	 *
	 * @return array Default settings structure
	 */
	public static function get_defaults() {
		return array(
			'align'               => 'left',
			'width'               => 'auto',
			'min_width'           => null,
			'max_width'           => null,
			'color'               => null,
			'background_color'    => null,
			'font_weight'         => 'normal',
			'font_style'          => 'normal',
			'font_size'           => null,
			'padding'             => null,
			'vertical_align'      => 'middle',
			'word_wrap'           => true,
			'text_transform'      => 'none',
			'css_class'           => '',
			'responsive_priority' => null,
			'highlight'           => false,
			'header_style'        => array(),
		);
	}

	/**
	 * Sanitize column settings
	 *
	 * @param array $settings Raw column settings.
	 * @return array Sanitized settings
	 */
	public static function sanitize( $settings ) {
		$defaults = self::get_defaults();
		$sanitized = array();

		foreach ( $defaults as $key => $default ) {
			if ( isset( $settings[ $key ] ) ) {
				$sanitized[ $key ] = $settings[ $key ];
			}
		}

		return $sanitized;
	}
}

<?php
/**
 * Content Formatter
 *
 * Formats table cell content (links, images, etc.)
 *
 * @package ATablesCharts\Shared\Utils
 * @since 1.0.0
 */

namespace ATablesCharts\Shared\Utils;

/**
 * ContentFormatter Class
 *
 * Responsibilities:
 * - Auto-detect and format URLs as clickable links
 * - Auto-detect and display images from URLs
 * - Format email addresses
 * - Format phone numbers
 */
class ContentFormatter {

	/**
	 * Format cell content
	 *
	 * @param string $content Raw cell content.
	 * @param array  $options Formatting options.
	 * @return string Formatted content
	 */
	public static function format( $content, $options = array() ) {
		$defaults = array(
			'auto_link'      => true,
			'auto_image'     => true,
			'auto_email'     => true,
			'auto_type'      => true,  // NEW: Auto-detect column types
			'image_size'     => 'medium', // small, medium, large
			'link_target'    => '_blank',
			'link_nofollow'  => false,
		);

		$options = wp_parse_args( $options, $defaults );

		// Don't process empty content
		if ( empty( $content ) && $content !== 0 && $content !== '0' ) {
			return '';
		}

		// Convert to string if needed
		$content = (string) $content;

		// Auto-detect and display images
		if ( $options['auto_image'] && self::is_image_url( $content ) ) {
			return self::format_image( $content, $options['image_size'] );
		}

		// Auto-detect and format URLs
		if ( $options['auto_link'] && self::is_url( $content ) ) {
			return self::format_link( $content, $options );
		}

		// Auto-detect and format email addresses
		if ( $options['auto_email'] && self::is_email( $content ) ) {
			return self::format_email( $content );
		}

		// NEW: Auto-detect and format column types (currency, dates, etc.)
		if ( $options['auto_type'] ) {
			// Load ColumnTypeFormatter if not already loaded
			if ( ! class_exists( 'ATablesCharts\\Shared\\Utils\\ColumnTypeFormatter' ) ) {
				require_once ATABLES_PLUGIN_DIR . 'src/shared/utils/ColumnTypeFormatter.php';
			}
			
			$formatted = \ATablesCharts\Shared\Utils\ColumnTypeFormatter::format( $content, array( 'type' => 'auto' ) );
			
			// If formatting was applied (not just plain text), return it
			if ( $formatted !== esc_html( $content ) ) {
				return $formatted;
			}
		}

		// Return plain content with escaping
		return esc_html( $content );
	}

	/**
	 * Check if content is a URL
	 *
	 * @param string $content Content to check.
	 * @return bool True if URL
	 */
	public static function is_url( $content ) {
		// Check if it starts with http:// or https://
		if ( preg_match( '/^https?:\/\//i', $content ) ) {
			return filter_var( $content, FILTER_VALIDATE_URL ) !== false;
		}

		return false;
	}

	/**
	 * Check if URL is an image
	 *
	 * @param string $url URL to check.
	 * @return bool True if image URL
	 */
	public static function is_image_url( $url ) {
		if ( ! self::is_url( $url ) ) {
			return false;
		}

		// Check if it's a placeholder image service
		$placeholder_services = array(
			'placeholder.com',
			'placehold.it',
			'via.placeholder.com',
			'dummyimage.com',
			'lorempixel.com',
			'picsum.photos',
		);
		
		foreach ( $placeholder_services as $service ) {
			if ( strpos( $url, $service ) !== false ) {
				return true;
			}
		}

		// Parse URL to get path without query string
		$parsed_url = parse_url( $url );
		$path = isset( $parsed_url['path'] ) ? $parsed_url['path'] : '';
		
		// Check file extension
		$image_extensions = array( 'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'ico' );
		$extension = strtolower( pathinfo( $path, PATHINFO_EXTENSION ) );

		return in_array( $extension, $image_extensions, true );
	}

	/**
	 * Check if content is an email address
	 *
	 * @param string $content Content to check.
	 * @return bool True if email
	 */
	public static function is_email( $content ) {
		return is_email( $content );
	}

	/**
	 * Format URL as clickable link
	 *
	 * @param string $url     URL to format.
	 * @param array  $options Link options.
	 * @return string Formatted link
	 */
	public static function format_link( $url, $options = array() ) {
		$defaults = array(
			'link_target'   => '_blank',
			'link_nofollow' => false,
		);

		$options = wp_parse_args( $options, $defaults );

		$attributes = array(
			'href'   => esc_url( $url ),
			'target' => $options['link_target'],
		);

		if ( $options['link_nofollow'] ) {
			$attributes['rel'] = 'nofollow noopener';
		} else {
			$attributes['rel'] = 'noopener';
		}

		$attr_string = '';
		foreach ( $attributes as $key => $value ) {
			$attr_string .= sprintf( ' %s="%s"', $key, esc_attr( $value ) );
		}

		// Create shortened display text for long URLs
		$display_text = $url;
		if ( strlen( $url ) > 50 ) {
			$display_text = substr( $url, 0, 47 ) . '...';
		}

		return sprintf( '<a%s>%s</a>', $attr_string, esc_html( $display_text ) );
	}

	/**
	 * Format image URL as image tag
	 *
	 * @param string $url  Image URL.
	 * @param string $size Image size (small, medium, large).
	 * @return string Image tag
	 */
	public static function format_image( $url, $size = 'medium' ) {
		$sizes = array(
			'small'  => 60,
			'medium' => 120,
			'large'  => 200,
		);

		$max_width = isset( $sizes[ $size ] ) ? $sizes[ $size ] : $sizes['medium'];

		return sprintf(
			'<a href="%s" target="_blank" rel="noopener"><img src="%s" alt="" style="max-width: %dpx; height: auto; border-radius: 4px; vertical-align: middle;" loading="lazy" /></a>',
			esc_url( $url ),
			esc_url( $url ),
			intval( $max_width )
		);
	}

	/**
	 * Format email address as mailto link
	 *
	 * @param string $email Email address.
	 * @return string Mailto link
	 */
	public static function format_email( $email ) {
		return sprintf(
			'<a href="mailto:%s">%s</a>',
			esc_attr( $email ),
			esc_html( $email )
		);
	}

	/**
	 * Format phone number as tel link
	 *
	 * @param string $phone Phone number.
	 * @return string Tel link
	 */
	public static function format_phone( $phone ) {
		// Clean phone number for href
		$clean_phone = preg_replace( '/[^0-9+]/', '', $phone );

		return sprintf(
			'<a href="tel:%s">%s</a>',
			esc_attr( $clean_phone ),
			esc_html( $phone )
		);
	}

	/**
	 * Format content with multiple possible formats
	 *
	 * Handles content that might contain multiple URLs, emails, etc.
	 *
	 * @param string $content Content to format.
	 * @param array  $options Formatting options.
	 * @return string Formatted content
	 */
	public static function format_mixed( $content, $options = array() ) {
		// For now, just use the simple format
		// In the future, we can add support for mixed content with regex replacement
		return self::format( $content, $options );
	}

	/**
	 * Strip formatting and return plain text
	 *
	 * @param string $content Formatted content.
	 * @return string Plain text
	 */
	public static function strip_formatting( $content ) {
		return wp_strip_all_tags( $content );
	}
}

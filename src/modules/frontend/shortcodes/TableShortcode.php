<?php
/**
 * Table Shortcode - Simple Version
 *
 * Handles the [atable] shortcode for displaying tables on the frontend.
 *
 * @package ATablesCharts\Frontend\Shortcodes
 * @since 1.0.0
 */

namespace ATablesCharts\Frontend\Shortcodes;

use ATablesCharts\Tables\Repositories\TableRepository;
use ATablesCharts\Frontend\Renderers\TableRenderer;

class TableShortcode {

	private $repository;
	private $renderer;

	public function __construct() {
		$this->repository = new TableRepository();
		$this->renderer   = new TableRenderer();
	}

	public function register() {
		add_shortcode( 'atable', array( $this, 'render' ) );
	}

	public function render( $atts ) {
		// 1. Start with plugin defaults (lowest priority)
		$defaults = array(
			'rows_per_page'       => 10,
			'enable_search'       => true,
			'enable_pagination'   => true,
			'enable_sorting'      => true,
			'enable_responsive'   => true,
			'default_table_style' => 'default',
		);
		
		// 2. Apply global settings (higher priority)
		$global_settings = get_option( 'atables_settings', array() );
		$resolved_settings = wp_parse_args( $global_settings, $defaults );
		
		// Get table ID first to load table-specific settings
		$table_id = isset( $atts['id'] ) ? (int) $atts['id'] : 0;
		
		if ( empty( $table_id ) ) {
			return '<p><strong>A-Tables Error:</strong> Table ID is required. Usage: [atable id="123"]</p>';
		}

		$table = $this->repository->find_by_id( $table_id );
		
		if ( ! $table ) {
			// Log for debugging
			error_log( 'A-Tables: Table not found - ID: ' . $table_id );
			return '<p><strong>A-Tables Error:</strong> Table not found. (ID: ' . esc_html( $table_id ) . ')</p>';
		}

		if ( $table->status !== 'active' ) {
			return '<p><strong>A-Tables Error:</strong> This table is not available.</p>';
		}
		
		// 3. Apply per-table settings (higher priority than global)
		$table_settings = $table->get_display_settings();
		if ( ! empty( $table_settings ) ) {
			// Only apply non-null table settings
			foreach ( $table_settings as $key => $value ) {
				if ( $value !== null ) {
					// Map table setting keys to resolved setting keys
					if ( $key === 'table_style' ) {
						$resolved_settings['default_table_style'] = $value;
					} else {
						$resolved_settings[ $key ] = $value;
					}
				}
			}
		}
		
		// 4. Build shortcode defaults from resolved settings (table overrides global)
		$shortcode_defaults = array(
			'id'              => 0,
			'width'           => '100%',
			'style'           => $resolved_settings['default_table_style'],
			'responsive_mode' => 'scroll', // scroll, stack, cards
			'template'        => '',  // NEW: Apply a template
			'search'          => $resolved_settings['enable_search'] ? 'true' : 'false',
			'pagination'      => $resolved_settings['enable_pagination'] ? 'true' : 'false',
			'page_length'     => $resolved_settings['rows_per_page'],
			'sorting'         => $resolved_settings['enable_sorting'] ? 'true' : 'false',
			'info'            => 'true',
		);
		
		// 5. Apply shortcode attributes (highest priority)
		$atts = shortcode_atts( $shortcode_defaults, $atts, 'atable' );
		
		// 6. NEW: Apply template if specified
		if ( ! empty( $atts['template'] ) ) {
			// Load template class
			if ( ! class_exists( 'ATablesCharts\\Templates\\Types\\TableTemplate' ) ) {
				require_once ATABLES_PLUGIN_DIR . 'src/modules/templates/types/TableTemplate.php';
			}
			
			$template = \ATablesCharts\Templates\Types\TableTemplate::create_from_builtin( $atts['template'] );
			
			if ( $template ) {
				$template_config = $template->get_config();
				
				// Apply template settings (shortcode attributes override template)
				if ( isset( $template_config['default_table_style'] ) && empty( $atts['style'] ) ) {
					$atts['style'] = $template_config['default_table_style'];
				}
				if ( isset( $template_config['responsive_mode'] ) ) {
					$atts['responsive_mode'] = $template_config['responsive_mode'];
				}
				if ( isset( $template_config['enable_search'] ) ) {
					$atts['search'] = $template_config['enable_search'] ? 'true' : 'false';
				}
				if ( isset( $template_config['enable_pagination'] ) ) {
					$atts['pagination'] = $template_config['enable_pagination'] ? 'true' : 'false';
				}
				if ( isset( $template_config['enable_sorting'] ) ) {
					$atts['sorting'] = $template_config['enable_sorting'] ? 'true' : 'false';
				}
				if ( isset( $template_config['rows_per_page'] ) ) {
					$atts['page_length'] = $template_config['rows_per_page'];
				}
			}
		}

		$data = $this->repository->get_table_data( $table_id );

		$headers = $table->get_headers();
		$mapped_data = array();
		
		foreach ( $data as $row ) {
			$mapped_row = array();
			foreach ( $row as $index => $value ) {
				if ( isset( $headers[ $index ] ) ) {
					$mapped_row[ $headers[ $index ] ] = $value;
				}
			}
			$mapped_data[] = $mapped_row;
		}

		$options = array(
			'table'           => $table,
			'data'            => $mapped_data,
			'width'           => $atts['width'],
			'style'           => $atts['style'],
			'responsive_mode' => $atts['responsive_mode'],
			'search'          => $atts['search'],
			'pagination'      => $atts['pagination'],
			'page_length'     => $atts['page_length'],
			'sorting'         => $atts['sorting'],
			'info'            => $atts['info'],
			'table_id'        => $table_id,
		);

		return $this->renderer->render( $options );
	}
}

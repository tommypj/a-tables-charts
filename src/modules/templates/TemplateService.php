<?php
/**
 * Template Service
 *
 * @package ATablesCharts\Templates\Services
 */

namespace ATablesCharts\Templates\Services;

use ATablesCharts\Tables\Repositories\TableRepository;

class TemplateService {

	/**
	 * Get all available templates
	 *
	 * @return array Templates
	 */
	public function get_templates() {
		return array(
			'default' => array(
				'label' => __( 'Default', 'a-tables-charts' ),
				'description' => __( 'Clean and simple table', 'a-tables-charts' ),
				'icon' => '📋',
				'config' => array(
					'theme' => 'default',
					'responsive_mode' => 'scroll',
					'enable_search' => true,
					'enable_sorting' => true,
					'enable_pagination' => true,
					'rows_per_page' => 10,
				),
			),
			'striped' => array(
				'label' => __( 'Striped Rows', 'a-tables-charts' ),
				'description' => __( 'Alternating row colors', 'a-tables-charts' ),
				'icon' => '📊',
				'config' => array(
					'theme' => 'striped',
					'responsive_mode' => 'scroll',
					'enable_search' => true,
					'enable_sorting' => true,
					'enable_pagination' => true,
				),
			),
			'bordered' => array(
				'label' => __( 'Bordered', 'a-tables-charts' ),
				'description' => __( 'Clear cell borders', 'a-tables-charts' ),
				'icon' => '🔲',
				'config' => array(
					'theme' => 'bordered',
					'responsive_mode' => 'scroll',
					'enable_search' => true,
					'enable_sorting' => true,
					'enable_pagination' => true,
				),
			),
			'dark' => array(
				'label' => __( 'Dark Mode', 'a-tables-charts' ),
				'description' => __( 'Dark theme table', 'a-tables-charts' ),
				'icon' => '🌙',
				'config' => array(
					'theme' => 'dark',
					'responsive_mode' => 'scroll',
					'enable_search' => true,
					'enable_sorting' => true,
					'enable_pagination' => true,
				),
			),
			'minimal' => array(
				'label' => __( 'Minimal', 'a-tables-charts' ),
				'description' => __( 'Clean minimal design', 'a-tables-charts' ),
				'icon' => '✨',
				'config' => array(
					'theme' => 'minimal',
					'responsive_mode' => 'scroll',
					'enable_search' => false,
					'enable_sorting' => true,
					'enable_pagination' => false,
				),
			),
			'compact' => array(
				'label' => __( 'Compact', 'a-tables-charts' ),
				'description' => __( 'Space-efficient layout', 'a-tables-charts' ),
				'icon' => '📱',
				'config' => array(
					'theme' => 'default',
					'responsive_mode' => 'cards',
					'enable_search' => true,
					'enable_sorting' => true,
					'enable_pagination' => true,
					'rows_per_page' => 25,
				),
			),
			'professional' => array(
				'label' => __( 'Professional', 'a-tables-charts' ),
				'description' => __( 'Business-ready styling', 'a-tables-charts' ),
				'icon' => '💼',
				'config' => array(
					'theme' => 'hover',
					'responsive_mode' => 'scroll',
					'enable_search' => true,
					'enable_sorting' => true,
					'enable_pagination' => true,
					'rows_per_page' => 20,
				),
			),
			'modern' => array(
				'label' => __( 'Modern', 'a-tables-charts' ),
				'description' => __( 'Contemporary design', 'a-tables-charts' ),
				'icon' => '🎨',
				'config' => array(
					'theme' => 'striped',
					'responsive_mode' => 'stack',
					'enable_search' => true,
					'enable_sorting' => true,
					'enable_pagination' => true,
				),
			),
		);
	}

	/**
	 * Get template by ID
	 *
	 * @param string $template_id Template ID.
	 * @return array|null Template or null
	 */
	public function get_template( $template_id ) {
		$templates = $this->get_templates();
		return isset( $templates[ $template_id ] ) ? $templates[ $template_id ] : null;
	}

	/**
	 * Apply template to table
	 *
	 * @param int    $table_id    Table ID.
	 * @param string $template_id Template ID.
	 * @return bool Success
	 */
	public function apply_template( $table_id, $template_id ) {
		$template = $this->get_template( $template_id );
		if ( ! $template ) {
			return false;
		}

		$repository = new TableRepository();

		return $repository->save_display_settings( $table_id, $template['config'] );
	}
}

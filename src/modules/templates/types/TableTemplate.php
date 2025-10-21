<?php
/**
 * Table Template Entity
 *
 * Represents a saved table template/preset
 *
 * @package ATablesCharts\Templates\Types
 * @since 1.0.0
 */

namespace ATablesCharts\Templates\Types;

/**
 * TableTemplate Class
 *
 * Responsibilities:
 * - Store template configuration
 * - Validate template data
 * - Apply template to tables
 */
class TableTemplate {

	/**
	 * Template ID
	 *
	 * @var int|null
	 */
	public $id;

	/**
	 * Template name
	 *
	 * @var string
	 */
	public $name;

	/**
	 * Template description
	 *
	 * @var string
	 */
	public $description;

	/**
	 * Template category
	 *
	 * @var string
	 */
	public $category;

	/**
	 * Template configuration (JSON)
	 *
	 * @var array
	 */
	public $config;

	/**
	 * Is this a built-in template?
	 *
	 * @var bool
	 */
	public $is_builtin;

	/**
	 * Created by user ID
	 *
	 * @var int
	 */
	public $created_by;

	/**
	 * Created timestamp
	 *
	 * @var string
	 */
	public $created_at;

	/**
	 * Updated timestamp
	 *
	 * @var string
	 */
	public $updated_at;

	/**
	 * Constructor
	 *
	 * @param array $data Template data.
	 */
	public function __construct( $data = array() ) {
		$this->id          = isset( $data['id'] ) ? (int) $data['id'] : null;
		$this->name        = isset( $data['name'] ) ? $data['name'] : '';
		$this->description = isset( $data['description'] ) ? $data['description'] : '';
		$this->category    = isset( $data['category'] ) ? $data['category'] : 'custom';
		$this->config      = isset( $data['config'] ) ? $data['config'] : array();
		$this->is_builtin  = isset( $data['is_builtin'] ) ? (bool) $data['is_builtin'] : false;
		$this->created_by  = isset( $data['created_by'] ) ? (int) $data['created_by'] : get_current_user_id();
		$this->created_at  = isset( $data['created_at'] ) ? $data['created_at'] : current_time( 'mysql' );
		$this->updated_at  = isset( $data['updated_at'] ) ? $data['updated_at'] : current_time( 'mysql' );
	}

	/**
	 * Get template configuration
	 *
	 * @return array Template config
	 */
	public function get_config() {
		if ( is_string( $this->config ) ) {
			return json_decode( $this->config, true ) ?: array();
		}
		return is_array( $this->config ) ? $this->config : array();
	}

	/**
	 * Set template configuration
	 *
	 * @param array $config Configuration array.
	 */
	public function set_config( $config ) {
		$this->config = is_array( $config ) ? $config : array();
	}

	/**
	 * Apply template to table display settings
	 *
	 * @param array $current_settings Current table settings.
	 * @return array Merged settings
	 */
	public function apply_to_settings( $current_settings = array() ) {
		$template_config = $this->get_config();
		
		// Merge template config with current settings (template takes priority)
		return array_merge( $current_settings, $template_config );
	}

	/**
	 * Convert to array
	 *
	 * @return array Template data
	 */
	public function to_array() {
		return array(
			'id'          => $this->id,
			'name'        => $this->name,
			'description' => $this->description,
			'category'    => $this->category,
			'config'      => $this->get_config(),
			'is_builtin'  => $this->is_builtin,
			'created_by'  => $this->created_by,
			'created_at'  => $this->created_at,
			'updated_at'  => $this->updated_at,
		);
	}

	/**
	 * Validate template
	 *
	 * @return array Validation result
	 */
	public function validate() {
		$errors = array();

		if ( empty( $this->name ) ) {
			$errors[] = __( 'Template name is required.', 'a-tables-charts' );
		}

		if ( strlen( $this->name ) > 100 ) {
			$errors[] = __( 'Template name must be less than 100 characters.', 'a-tables-charts' );
		}

		$valid_categories = array( 'business', 'finance', 'comparison', 'pricing', 'data', 'custom' );
		if ( ! in_array( $this->category, $valid_categories, true ) ) {
			$errors[] = __( 'Invalid template category.', 'a-tables-charts' );
		}

		return array(
			'valid'  => empty( $errors ),
			'errors' => $errors,
		);
	}

	/**
	 * Get built-in templates
	 *
	 * @return array Array of built-in templates
	 */
	public static function get_builtin_templates() {
		return array(
			'pricing_table' => array(
				'name'        => __( 'Pricing Table', 'a-tables-charts' ),
				'description' => __( 'Professional pricing table with featured column', 'a-tables-charts' ),
				'category'    => 'pricing',
				'config'      => array(
					'default_table_style' => 'modern',
					'responsive_mode'     => 'scroll',
					'enable_search'       => false,
					'enable_pagination'   => false,
					'enable_sorting'      => false,
				),
			),
			'comparison_table' => array(
				'name'        => __( 'Comparison Table', 'a-tables-charts' ),
				'description' => __( 'Compare products or services side-by-side', 'a-tables-charts' ),
				'category'    => 'comparison',
				'config'      => array(
					'default_table_style' => 'minimal',
					'responsive_mode'     => 'scroll',
					'enable_search'       => false,
					'enable_pagination'   => false,
					'enable_sorting'      => true,
				),
			),
			'financial_report' => array(
				'name'        => __( 'Financial Report', 'a-tables-charts' ),
				'description' => __( 'Professional financial data presentation', 'a-tables-charts' ),
				'category'    => 'finance',
				'config'      => array(
					'default_table_style' => 'striped',
					'responsive_mode'     => 'scroll',
					'enable_search'       => true,
					'enable_pagination'   => true,
					'enable_sorting'      => true,
					'rows_per_page'       => 25,
				),
			),
			'product_catalog' => array(
				'name'        => __( 'Product Catalog', 'a-tables-charts' ),
				'description' => __( 'Showcase products with images and details', 'a-tables-charts' ),
				'category'    => 'business',
				'config'      => array(
					'default_table_style' => 'material',
					'responsive_mode'     => 'cards',
					'enable_search'       => true,
					'enable_pagination'   => true,
					'enable_sorting'      => true,
					'rows_per_page'       => 12,
				),
			),
			'data_dashboard' => array(
				'name'        => __( 'Data Dashboard', 'a-tables-charts' ),
				'description' => __( 'Interactive data table with all features', 'a-tables-charts' ),
				'category'    => 'data',
				'config'      => array(
					'default_table_style' => 'classic',
					'responsive_mode'     => 'scroll',
					'enable_search'       => true,
					'enable_pagination'   => true,
					'enable_sorting'      => true,
					'enable_export'       => true,
					'rows_per_page'       => 25,
				),
			),
			'simple_list' => array(
				'name'        => __( 'Simple List', 'a-tables-charts' ),
				'description' => __( 'Clean, minimal table for basic lists', 'a-tables-charts' ),
				'category'    => 'custom',
				'config'      => array(
					'default_table_style' => 'minimal',
					'responsive_mode'     => 'stack',
					'enable_search'       => false,
					'enable_pagination'   => false,
					'enable_sorting'      => false,
				),
			),
			'contact_directory' => array(
				'name'        => __( 'Contact Directory', 'a-tables-charts' ),
				'description' => __( 'Display contact information with clickable links', 'a-tables-charts' ),
				'category'    => 'business',
				'config'      => array(
					'default_table_style' => 'material',
					'responsive_mode'     => 'cards',
					'enable_search'       => true,
					'enable_pagination'   => true,
					'enable_sorting'      => true,
					'rows_per_page'       => 20,
				),
			),
			'sales_report' => array(
				'name'        => __( 'Sales Report', 'a-tables-charts' ),
				'description' => __( 'Track sales performance with conditional formatting', 'a-tables-charts' ),
				'category'    => 'business',
				'config'      => array(
					'default_table_style' => 'striped',
					'responsive_mode'     => 'scroll',
					'enable_search'       => true,
					'enable_pagination'   => true,
					'enable_sorting'      => true,
					'rows_per_page'       => 50,
				),
			),
		);
	}

	/**
	 * Create template from built-in
	 *
	 * @param string $template_key Built-in template key.
	 * @return TableTemplate|null Template instance or null
	 */
	public static function create_from_builtin( $template_key ) {
		$builtins = self::get_builtin_templates();
		
		if ( ! isset( $builtins[ $template_key ] ) ) {
			return null;
		}

		$data = $builtins[ $template_key ];
		$data['is_builtin'] = true;
		
		return new self( $data );
	}
}

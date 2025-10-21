<?php
/**
 * Plugin Name: A-Tables and Charts for WordPress
 * Plugin URI: https://a-tables-charts.com
 * Description: Create responsive tables and charts from various data sources. Import from Excel, CSV, JSON, XML, MySQL, Google Sheets, and more.
 * Version: 1.0.0
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * Author: A-Tables Team
 * Author URI: https://a-tables-charts.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: a-tables-charts
 * Domain Path: /languages
 *
 * @package ATablesCharts
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 */
define( 'ATABLES_VERSION', '1.0.4' );

/**
 * Plugin directory path
 */
define( 'ATABLES_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Plugin directory URL
 */
define( 'ATABLES_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Plugin basename
 */
define( 'ATABLES_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Plugin slug
 */
define( 'ATABLES_SLUG', 'a-tables-charts' );

/**
 * Minimum PHP version required
 */
define( 'ATABLES_MIN_PHP_VERSION', '7.4' );

/**
 * Minimum WordPress version required
 */
define( 'ATABLES_MIN_WP_VERSION', '5.8' );

/**
 * Check PHP and WordPress versions before loading the plugin
 */
function atables_check_requirements() {
	$php_version = phpversion();
	$wp_version  = get_bloginfo( 'version' );

	$errors = array();

	// Check PHP version.
	if ( version_compare( $php_version, ATABLES_MIN_PHP_VERSION, '<' ) ) {
		$errors[] = sprintf(
			/* translators: 1: Current PHP version, 2: Required PHP version */
			__( 'A-Tables and Charts requires PHP version %2$s or higher. You are running version %1$s.', 'a-tables-charts' ),
			$php_version,
			ATABLES_MIN_PHP_VERSION
		);
	}

	// Check WordPress version.
	if ( version_compare( $wp_version, ATABLES_MIN_WP_VERSION, '<' ) ) {
		$errors[] = sprintf(
			/* translators: 1: Current WordPress version, 2: Required WordPress version */
			__( 'A-Tables and Charts requires WordPress version %2$s or higher. You are running version %1$s.', 'a-tables-charts' ),
			$wp_version,
			ATABLES_MIN_WP_VERSION
		);
	}

	// Display errors if any.
	if ( ! empty( $errors ) ) {
		deactivate_plugins( ATABLES_PLUGIN_BASENAME );
		wp_die(
			'<h1>' . esc_html__( 'Plugin Activation Failed', 'a-tables-charts' ) . '</h1>' .
			'<p>' . implode( '</p><p>', array_map( 'esc_html', $errors ) ) . '</p>' .
			'<p><a href="' . esc_url( admin_url( 'plugins.php' ) ) . '">' . 
			esc_html__( '&larr; Return to Plugins', 'a-tables-charts' ) . '</a></p>'
		);
	}
}
add_action( 'admin_init', 'atables_check_requirements' );

/**
 * Composer autoloader
 */
if ( file_exists( ATABLES_PLUGIN_DIR . 'vendor/autoload.php' ) ) {
	require_once ATABLES_PLUGIN_DIR . 'vendor/autoload.php';
}

/**
 * The code that runs during plugin activation.
 */
function atables_activate() {
	require_once ATABLES_PLUGIN_DIR . 'src/modules/core/Activator.php';
	\ATablesCharts\Core\Activator::activate();
}
register_activation_hook( __FILE__, 'atables_activate' );

/**
 * The code that runs during plugin deactivation.
 */
function atables_deactivate() {
	require_once ATABLES_PLUGIN_DIR . 'src/modules/core/Deactivator.php';
	\ATablesCharts\Core\Deactivator::deactivate();
}
register_deactivation_hook( __FILE__, 'atables_deactivate' );

/**
 * Begin plugin execution.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 */
function atables_run() {
	require_once ATABLES_PLUGIN_DIR . 'src/modules/core/Plugin.php';
	$plugin = \ATablesCharts\Core\Plugin::get_instance();
	$plugin->run();
}
atables_run();

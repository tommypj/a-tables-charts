<?php
/**
 * Plugin Name: A-Tables & Charts (Clean v2)
 * Plugin URI: https://yoursite.com/a-tables-charts
 * Description: Create beautiful, responsive tables and charts from Excel/CSV files. Clean architecture v2.
 * Version: 2.0.0
 * Author: Your Name
 * Author URI: https://yoursite.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: a-tables-charts
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

// Plugin constants
define( 'ATABLES_VERSION', '2.0.0' );
define( 'ATABLES_PLUGIN_FILE', __FILE__ );
define( 'ATABLES_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'ATABLES_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'ATABLES_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

// Require Composer autoloader
require_once ATABLES_PLUGIN_DIR . 'vendor/autoload.php';

/**
 * Activation hook
 */
function atables_activate() {
    ATables\Core\Activator::activate();
}
register_activation_hook( __FILE__, 'atables_activate' );

/**
 * Deactivation hook
 */
function atables_deactivate() {
    ATables\Core\Activator::deactivate();
}
register_deactivation_hook( __FILE__, 'atables_deactivate' );

/**
 * Initialize the plugin
 */
function atables_init() {
    $plugin = ATables\Core\Plugin::get_instance();
    $plugin->run();
}
add_action( 'plugins_loaded', 'atables_init' );

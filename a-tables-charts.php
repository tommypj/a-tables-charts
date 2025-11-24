<?php
/**
 * Plugin Name: A-Tables & Charts
 * Plugin URI: https://your-site.com
 * Description: Create beautiful, responsive tables with modular features
 * Version: 3.0.0
 * Author: Your Name
 * Author URI: https://your-site.com
 * License: GPL-2.0+
 * Text Domain: a-tables-charts
 * Domain Path: /languages
 *
 * @package ATables
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Plugin constants
define( 'ATABLES_VERSION', '3.0.0' );
define( 'ATABLES_PLUGIN_FILE', __FILE__ );
define( 'ATABLES_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'ATABLES_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Require Composer autoloader
require_once ATABLES_PLUGIN_DIR . 'vendor/autoload.php';

// Activation hook
register_activation_hook( __FILE__, array( 'ATables\Core\Activator', 'activate' ) );

// Initialize plugin
add_action( 'plugins_loaded', function() {
    $plugin = ATables\Core\Plugin::get_instance();
    $plugin->run();
} );

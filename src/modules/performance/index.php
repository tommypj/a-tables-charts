<?php
/**
 * Performance Module Entry Point
 *
 * Loads all performance monitoring classes.
 *
 * @package ATablesCharts\Performance
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load services.
require_once ATABLES_PLUGIN_DIR . 'src/modules/performance/services/PerformanceMonitor.php';

// Load controllers.
require_once ATABLES_PLUGIN_DIR . 'src/modules/performance/controllers/PerformanceController.php';

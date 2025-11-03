<?php
/**
 * Cron Module Entry Point
 *
 * Loads all cron-related classes for scheduled data refresh functionality.
 *
 * @package ATablesCharts\Cron
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load required dependencies for RefreshService.
require_once ATABLES_PLUGIN_DIR . 'src/modules/database/index.php';
require_once ATABLES_PLUGIN_DIR . 'src/modules/import/index.php';
require_once ATABLES_PLUGIN_DIR . 'src/modules/tables/index.php';

// Load services.
require_once ATABLES_PLUGIN_DIR . 'src/modules/cron/services/ScheduleService.php';
require_once ATABLES_PLUGIN_DIR . 'src/modules/cron/services/RefreshService.php';

// Load controllers.
require_once ATABLES_PLUGIN_DIR . 'src/modules/cron/controllers/CronController.php';

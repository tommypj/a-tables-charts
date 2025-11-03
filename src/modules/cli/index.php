<?php
/**
 * CLI Module Entry Point
 *
 * Loads all WP-CLI command classes.
 *
 * @package ATablesCharts\CLI
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Only load if WP-CLI is available
if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	return;
}

// Load command classes.
require_once ATABLES_PLUGIN_DIR . 'src/modules/cli/commands/TableCommand.php';
require_once ATABLES_PLUGIN_DIR . 'src/modules/cli/commands/ScheduleCommand.php';
require_once ATABLES_PLUGIN_DIR . 'src/modules/cli/commands/CacheCommand.php';
require_once ATABLES_PLUGIN_DIR . 'src/modules/cli/commands/ExportCommand.php';

// Register commands.
WP_CLI::add_command( 'atables table', 'ATablesCharts\CLI\Commands\TableCommand' );
WP_CLI::add_command( 'atables schedule', 'ATablesCharts\CLI\Commands\ScheduleCommand' );
WP_CLI::add_command( 'atables cache', 'ATablesCharts\CLI\Commands\CacheCommand' );
WP_CLI::add_command( 'atables export', 'ATablesCharts\CLI\Commands\ExportCommand' );
WP_CLI::add_command( 'atables import', array( 'ATablesCharts\CLI\Commands\ExportCommand', 'import' ) );

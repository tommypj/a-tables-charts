<?php
/**
 * Gutenberg Module Index
 *
 * Loads the Gutenberg module components.
 *
 * @package ATablesCharts\Gutenberg
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load Gutenberg Controller.
require_once __DIR__ . '/GutenbergController.php';

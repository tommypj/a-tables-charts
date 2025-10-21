<?php
/**
 * Frontend Module
 *
 * Handles frontend display of tables and charts.
 *
 * @package ATablesCharts\Frontend
 * @since 1.0.0
 */

namespace ATablesCharts\Frontend;

// Autoload classes.
require_once __DIR__ . '/shortcodes/TableShortcode.php';
require_once __DIR__ . '/shortcodes/CellShortcode.php';
require_once __DIR__ . '/shortcodes/ChartShortcode.php';
require_once __DIR__ . '/renderers/TableRenderer.php';
require_once __DIR__ . '/renderers/ChartRenderer.php';

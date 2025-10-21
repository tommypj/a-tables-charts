<?php
/**
 * Charts Module
 *
 * Handles chart creation and visualization.
 *
 * @package ATablesCharts\Charts
 * @since 1.0.0
 */

namespace ATablesCharts\Charts;

// Autoload classes.
require_once __DIR__ . '/types/Chart.php';
require_once __DIR__ . '/repositories/ChartRepository.php';
require_once __DIR__ . '/services/ChartService.php';
require_once __DIR__ . '/controllers/ChartController.php';
require_once __DIR__ . '/renderers/GoogleChartsRenderer.php';

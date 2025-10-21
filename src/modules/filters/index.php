<?php
/**
 * Filters Module
 *
 * Handles advanced filtering functionality.
 *
 * @package ATablesCharts\Filters
 * @since 1.0.5
 */

namespace ATablesCharts\Filters;

// Load types first
require_once __DIR__ . '/types/FilterOperator.php';
require_once __DIR__ . '/types/Filter.php';
require_once __DIR__ . '/types/FilterPreset.php';

// Load repositories
require_once __DIR__ . '/repositories/FilterPresetRepository.php';

// Load services
require_once __DIR__ . '/services/FilterService.php';
require_once __DIR__ . '/services/FilterPresetService.php';

// Load controllers
require_once __DIR__ . '/controllers/FilterController.php';

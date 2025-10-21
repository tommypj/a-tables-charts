<?php
/**
 * Data Sources Module - Public API
 *
 * Exports all classes for easy importing.
 *
 * @package ATablesCharts\DataSources
 * @since 1.0.0
 */

namespace ATablesCharts\DataSources;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Types.
require_once __DIR__ . '/types/DataSourceType.php';
require_once __DIR__ . '/types/ImportResult.php';

// Parsers.
require_once __DIR__ . '/parsers/ParserInterface.php';
require_once __DIR__ . '/parsers/CsvParser.php';
require_once __DIR__ . '/parsers/JsonParser.php';

// Services.
require_once __DIR__ . '/services/ImportService.php';

// Controllers.
require_once __DIR__ . '/controllers/ImportController.php';

<?php
/**
 * Import Module
 *
 * Handles file import functionality.
 *
 * @package ATablesCharts\Import
 * @since 1.0.0
 */

namespace ATablesCharts\Import;

// Load parsers.
require_once __DIR__ . '/parsers/ExcelParser.php';
require_once __DIR__ . '/parsers/XmlParser.php';

// Load services.
require_once __DIR__ . '/services/ExcelImportService.php';
require_once __DIR__ . '/services/XmlImportService.php';

// Load controllers.
require_once __DIR__ . '/controllers/ImportController.php';

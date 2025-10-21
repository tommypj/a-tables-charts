<?php
/**
 * Tables Module - Public API
 *
 * Exports all classes for easy importing.
 *
 * @package ATablesCharts\Tables
 * @since 1.0.0
 */

namespace ATablesCharts\Tables;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Schemas.
require_once __DIR__ . '/schemas/DatabaseSchema.php';

// Types.
require_once __DIR__ . '/types/Table.php';

// Repositories.
require_once __DIR__ . '/repositories/TableRepository.php';

// Services.
require_once __DIR__ . '/services/TableService.php';

// Controllers.
require_once __DIR__ . '/controllers/TableController.php';

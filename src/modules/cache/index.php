<?php
/**
 * Cache Module Index
 *
 * Loads the Cache module components.
 *
 * @package ATablesCharts\Cache
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load Cache Service.
require_once __DIR__ . '/services/CacheService.php';

// Load Cache Controller.
require_once __DIR__ . '/controllers/CacheController.php';

<?php
/**
 * Base Test Case
 *
 * Provides common functionality for all test cases.
 *
 * @package ATablesCharts\Tests
 * @since 1.0.0
 */

namespace ATablesCharts\Tests\Bootstrap;

use PHPUnit\Framework\TestCase as PHPUnit_TestCase;

/**
 * Base TestCase Class
 *
 * Extends PHPUnit's TestCase to provide common functionality.
 * Works both with and without WordPress test library.
 */
class TestCase extends PHPUnit_TestCase {

	/**
	 * Set up test environment
	 */
	public function setUp(): void {
		parent::setUp();

		// If WordPress is available, create tables
		if ( function_exists( 'dbDelta' ) ) {
			$this->create_tables();
		}
	}

	/**
	 * Tear down test environment
	 */
	public function tearDown(): void {
		parent::tearDown();
	}

	/**
	 * Create plugin database tables (only if WordPress is available)
	 */
	protected function create_tables() {
		if ( ! function_exists( 'dbDelta' ) ) {
			return; // Skip if WordPress is not available
		}

		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();
		$table_prefix = $wpdb->prefix;

		// Tables table.
		$sql = "CREATE TABLE IF NOT EXISTS {$table_prefix}atables_tables (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			title varchar(255) NOT NULL,
			description text,
			data_source_type varchar(50) NOT NULL,
			data_source_config text,
			row_count int(11) NOT NULL DEFAULT 0,
			column_count int(11) NOT NULL DEFAULT 0,
			status varchar(20) NOT NULL DEFAULT 'active',
			created_by bigint(20) unsigned NOT NULL,
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			KEY status (status),
			KEY created_by (created_by)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );

		// Table data table.
		$sql = "CREATE TABLE IF NOT EXISTS {$table_prefix}atables_table_data (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			table_id bigint(20) unsigned NOT NULL,
			row_index int(11) NOT NULL,
			row_data text NOT NULL,
			PRIMARY KEY (id),
			KEY table_id (table_id),
			KEY row_index (row_index)
		) $charset_collate;";

		dbDelta( $sql );

		// Table meta table.
		$sql = "CREATE TABLE IF NOT EXISTS {$table_prefix}atables_table_meta (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			table_id bigint(20) unsigned NOT NULL,
			meta_key varchar(255) NOT NULL,
			meta_value longtext,
			PRIMARY KEY (id),
			KEY table_id (table_id),
			KEY meta_key (meta_key)
		) $charset_collate;";

		dbDelta( $sql );
	}

	/**
	 * Get fixture file path
	 *
	 * @param string $filename Fixture filename.
	 * @return string Full path to fixture file.
	 */
	protected function get_fixture_path( $filename ) {
		return dirname( __DIR__ ) . '/fixtures/' . $filename;
	}

	/**
	 * Create sample table data
	 *
	 * @return array Table data array.
	 */
	protected function get_sample_table_data() {
		return array(
			'title'        => 'Test Table',
			'description'  => 'A test table for unit testing',
			'source_type'  => 'csv',
			'source_data'  => array(
				'headers' => array( 'Name', 'Email', 'Age', 'City' ),
				'data'    => array(
					array( 'John Doe', 'john@example.com', '25', 'New York' ),
					array( 'Jane Smith', 'jane@example.com', '30', 'Los Angeles' ),
					array( 'Bob Johnson', 'bob@example.com', '35', 'Chicago' ),
				),
			),
			'row_count'    => 3,
			'column_count' => 4,
			'status'       => 'active',
			'created_by'   => 1,
		);
	}

	/**
	 * Assert array has keys
	 *
	 * @param array $keys Expected keys.
	 * @param array $array Array to check.
	 * @param string $message Optional message.
	 */
	protected function assertArrayHasKeys( $keys, $array, $message = '' ) {
		foreach ( $keys as $key ) {
			$this->assertArrayHasKey( $key, $array, $message );
		}
	}

	/**
	 * Assert is valid table object
	 *
	 * @param mixed $table Table object to validate.
	 */
	protected function assertIsValidTable( $table ) {
		$this->assertIsObject( $table );
		$this->assertTrue( property_exists( $table, 'id' ), 'Table object should have id property' );
		$this->assertTrue( property_exists( $table, 'title' ), 'Table object should have title property' );
		$this->assertTrue( property_exists( $table, 'source_type' ), 'Table object should have source_type property' );
		$this->assertTrue( property_exists( $table, 'row_count' ), 'Table object should have row_count property' );
		$this->assertTrue( property_exists( $table, 'column_count' ), 'Table object should have column_count property' );
	}
}

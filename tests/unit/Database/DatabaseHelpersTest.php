<?php
/**
 * DatabaseHelpers Tests
 *
 * Tests for the DatabaseHelpers security utility class.
 * Critical for SQL injection prevention.
 *
 * @package ATablesCharts\Tests\Unit\Database
 * @since 1.0.5
 */

namespace ATablesCharts\Tests\Unit\Database;

use ATablesCharts\Tests\Bootstrap\TestCase;
use A_Tables_Charts\Database\DatabaseHelpers;

/**
 * DatabaseHelpersTest Class
 *
 * Tests SQL injection prevention methods.
 */
class DatabaseHelpersTest extends TestCase {

	/**
	 * Test prepare_order_by with valid column
	 */
	public function test_prepare_order_by_with_valid_column() {
		$allowed = array( 'id', 'title', 'created_at' );
		$result = DatabaseHelpers::prepare_order_by( 'title', $allowed, 'id' );

		$this->assertEquals( 'title', $result );
	}

	/**
	 * Test prepare_order_by with invalid column returns default
	 */
	public function test_prepare_order_by_with_invalid_column_returns_default() {
		$allowed = array( 'id', 'title', 'created_at' );
		$result = DatabaseHelpers::prepare_order_by( 'malicious_column', $allowed, 'id' );

		$this->assertEquals( 'id', $result );
	}

	/**
	 * Test prepare_order_by blocks SQL injection attempt
	 */
	public function test_prepare_order_by_blocks_sql_injection() {
		$allowed = array( 'id', 'title', 'created_at' );
		$malicious = "id; DROP TABLE wp_users--";
		$result = DatabaseHelpers::prepare_order_by( $malicious, $allowed, 'id' );

		// Should return default, not the malicious input
		$this->assertEquals( 'id', $result );
		$this->assertNotContains( 'DROP', $result );
		$this->assertNotContains( ';', $result );
	}

	/**
	 * Test prepare_order_by sanitizes special characters
	 */
	public function test_prepare_order_by_sanitizes_special_characters() {
		$allowed = array( 'id', 'title', 'created_at' );
		$input = "title'; DELETE FROM wp_posts WHERE '1'='1";
		$result = DatabaseHelpers::prepare_order_by( $input, $allowed, 'id' );

		// Should return default since sanitized version won't match allowed columns
		$this->assertEquals( 'id', $result );
	}

	/**
	 * Test prepare_order_by with empty column returns default
	 */
	public function test_prepare_order_by_with_empty_column_returns_default() {
		$allowed = array( 'id', 'title', 'created_at' );
		$result = DatabaseHelpers::prepare_order_by( '', $allowed, 'id' );

		$this->assertEquals( 'id', $result );
	}

	/**
	 * Test prepare_order_direction with valid ASC
	 */
	public function test_prepare_order_direction_with_valid_asc() {
		$result = DatabaseHelpers::prepare_order_direction( 'ASC', 'DESC' );
		$this->assertEquals( 'ASC', $result );
	}

	/**
	 * Test prepare_order_direction with valid DESC
	 */
	public function test_prepare_order_direction_with_valid_desc() {
		$result = DatabaseHelpers::prepare_order_direction( 'DESC', 'ASC' );
		$this->assertEquals( 'DESC', $result );
	}

	/**
	 * Test prepare_order_direction with lowercase input
	 */
	public function test_prepare_order_direction_with_lowercase_input() {
		$result = DatabaseHelpers::prepare_order_direction( 'asc', 'DESC' );
		$this->assertEquals( 'ASC', $result );

		$result = DatabaseHelpers::prepare_order_direction( 'desc', 'ASC' );
		$this->assertEquals( 'DESC', $result );
	}

	/**
	 * Test prepare_order_direction blocks SQL injection
	 */
	public function test_prepare_order_direction_blocks_sql_injection() {
		$malicious = "ASC; DROP TABLE wp_users--";
		$result = DatabaseHelpers::prepare_order_direction( $malicious, 'DESC' );

		// Should return default, not accept malicious input
		$this->assertEquals( 'DESC', $result );
		$this->assertNotContains( 'DROP', $result );
		$this->assertNotContains( ';', $result );
	}

	/**
	 * Test prepare_order_direction with invalid direction returns default
	 */
	public function test_prepare_order_direction_with_invalid_direction_returns_default() {
		$result = DatabaseHelpers::prepare_order_direction( 'INVALID', 'ASC' );
		$this->assertEquals( 'ASC', $result );

		$result = DatabaseHelpers::prepare_order_direction( 'RANDOM', 'DESC' );
		$this->assertEquals( 'DESC', $result );
	}

	/**
	 * Test prepare_like with 'both' position (default)
	 */
	public function test_prepare_like_with_both_position() {
		$result = DatabaseHelpers::prepare_like( 'search', 'both' );

		$this->assertStringStartsWith( '%', $result );
		$this->assertStringEndsWith( '%', $result );
		$this->assertStringContainsString( 'search', $result );
	}

	/**
	 * Test prepare_like with 'start' position
	 */
	public function test_prepare_like_with_start_position() {
		$result = DatabaseHelpers::prepare_like( 'search', 'start' );

		$this->assertStringStartsWith( 'search', $result );
		$this->assertStringEndsWith( '%', $result );
		$this->assertStringNotStartsWith( '%', $result );
	}

	/**
	 * Test prepare_like with 'end' position
	 */
	public function test_prepare_like_with_end_position() {
		$result = DatabaseHelpers::prepare_like( 'search', 'end' );

		$this->assertStringStartsWith( '%', $result );
		$this->assertStringEndsWith( 'search', $result );
	}

	/**
	 * Test prepare_like escapes wildcard characters
	 */
	public function test_prepare_like_escapes_wildcard_characters() {
		$result = DatabaseHelpers::prepare_like( 'search%test_value', 'both' );

		// Should escape % and _ characters
		$this->assertStringContainsString( '\\%', $result );
		$this->assertStringContainsString( '\\_', $result );
	}

	/**
	 * Test prepare_like escapes backslash
	 */
	public function test_prepare_like_escapes_backslash() {
		$result = DatabaseHelpers::prepare_like( 'search\\test', 'both' );

		// Should escape backslash
		$this->assertStringContainsString( '\\\\', $result );
	}

	/**
	 * Test prepare_in_clause with integer values
	 */
	public function test_prepare_in_clause_with_integer_values() {
		$values = array( 1, 2, 3, 4, 5 );
		$result = DatabaseHelpers::prepare_in_clause( $values, '%d' );

		$this->assertStringContainsString( '1', $result );
		$this->assertStringContainsString( '2', $result );
		$this->assertStringContainsString( '3', $result );
		$this->assertStringContainsString( ',', $result );
	}

	/**
	 * Test prepare_in_clause with string values
	 */
	public function test_prepare_in_clause_with_string_values() {
		$values = array( 'active', 'pending', 'draft' );
		$result = DatabaseHelpers::prepare_in_clause( $values, '%s' );

		$this->assertStringContainsString( 'active', $result );
		$this->assertStringContainsString( 'pending', $result );
		$this->assertStringContainsString( 'draft', $result );
	}

	/**
	 * Test prepare_in_clause with empty array returns empty string
	 */
	public function test_prepare_in_clause_with_empty_array_returns_empty_string() {
		$result = DatabaseHelpers::prepare_in_clause( array(), '%d' );
		$this->assertEquals( '', $result );
	}

	/**
	 * Test prepare_in_clause with non-array returns empty string
	 */
	public function test_prepare_in_clause_with_non_array_returns_empty_string() {
		$result = DatabaseHelpers::prepare_in_clause( 'not_an_array', '%d' );
		$this->assertEquals( '', $result );

		$result = DatabaseHelpers::prepare_in_clause( null, '%d' );
		$this->assertEquals( '', $result );
	}

	/**
	 * Test prepare_order_by with numeric column name
	 */
	public function test_prepare_order_by_with_numeric_column() {
		$allowed = array( 'id', 'user_id', 'post_id' );
		$result = DatabaseHelpers::prepare_order_by( 'user_id', $allowed, 'id' );

		$this->assertEquals( 'user_id', $result );
	}

	/**
	 * Test prepare_order_by with underscored column name
	 */
	public function test_prepare_order_by_with_underscored_column() {
		$allowed = array( 'id', 'created_at', 'updated_at', 'data_source_type' );
		$result = DatabaseHelpers::prepare_order_by( 'data_source_type', $allowed, 'id' );

		$this->assertEquals( 'data_source_type', $result );
	}

	/**
	 * Test comprehensive SQL injection protection
	 */
	public function test_comprehensive_sql_injection_protection() {
		$allowed = array( 'id', 'title', 'status' );

		$injection_attempts = array(
			"id; DROP TABLE wp_posts--",
			"title UNION SELECT * FROM wp_users",
			"status' OR '1'='1",
			"id'; DELETE FROM wp_posts WHERE '1'='1",
			"(SELECT * FROM wp_users)",
			"id AND 1=1",
			"title<script>alert('xss')</script>",
		);

		foreach ( $injection_attempts as $attempt ) {
			$result = DatabaseHelpers::prepare_order_by( $attempt, $allowed, 'id' );

			// All injection attempts should return the safe default
			$this->assertEquals( 'id', $result, "Failed to block: $attempt" );

			// Ensure no SQL keywords leaked through
			$this->assertStringNotContainsStringIgnoringCase( 'DROP', $result );
			$this->assertStringNotContainsStringIgnoringCase( 'DELETE', $result );
			$this->assertStringNotContainsStringIgnoringCase( 'UNION', $result );
			$this->assertStringNotContainsStringIgnoringCase( 'SELECT', $result );
		}
	}
}

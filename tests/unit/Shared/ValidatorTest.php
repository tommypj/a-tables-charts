<?php
/**
 * Validator Test
 *
 * Tests for the Validator utility class.
 *
 * @package ATablesCharts\Tests\Unit\Shared
 */

namespace ATablesCharts\Tests\Unit\Shared;

use ATablesCharts\Tests\Bootstrap\TestCase;
use ATablesCharts\Shared\Utils\Validator;

/**
 * ValidatorTest Class
 */
class ValidatorTest extends TestCase {

	/**
	 * Set up before each test
	 */
	public function setUp(): void {
		parent::setUp();
		Validator::clear_errors();
	}

	/**
	 * Test email validation with valid email
	 */
	public function test_email_validation_with_valid_email() {
		$result = Validator::email( 'test@example.com', 'email' );
		
		$this->assertTrue( $result );
		$this->assertEmpty( Validator::get_errors() );
	}

	/**
	 * Test email validation with invalid email
	 */
	public function test_email_validation_with_invalid_email() {
		$result = Validator::email( 'not-an-email', 'email' );
		
		$this->assertFalse( $result );
		
		$errors = Validator::get_errors();
		$this->assertArrayHasKey( 'email', $errors );
		$this->assertStringContainsString( 'valid email', $errors['email'][0] );
	}

	/**
	 * Test email validation with empty value
	 */
	public function test_email_validation_with_empty_value() {
		$result = Validator::email( '', 'email' );
		
		$this->assertFalse( $result );
		
		$errors = Validator::get_errors();
		$this->assertStringContainsString( 'required', $errors['email'][0] );
	}

	/**
	 * Test integer validation with valid integer
	 */
	public function test_integer_validation_with_valid_integer() {
		$result = Validator::integer( 42, null, null, 'count' );
		
		$this->assertTrue( $result );
	}

	/**
	 * Test integer validation with minimum value
	 */
	public function test_integer_validation_with_minimum_value() {
		// Should pass (10 >= 5).
		$result = Validator::integer( 10, 5, null, 'count' );
		$this->assertTrue( $result );
		
		Validator::clear_errors();
		
		// Should fail (3 < 5).
		$result = Validator::integer( 3, 5, null, 'count' );
		$this->assertFalse( $result );
		
		$errors = Validator::get_errors();
		$this->assertArrayHasKey( 'count', $errors );
	}

	/**
	 * Test integer validation with maximum value
	 */
	public function test_integer_validation_with_maximum_value() {
		// Should pass (50 <= 100).
		$result = Validator::integer( 50, null, 100, 'count' );
		$this->assertTrue( $result );
		
		Validator::clear_errors();
		
		// Should fail (150 > 100).
		$result = Validator::integer( 150, null, 100, 'count' );
		$this->assertFalse( $result );
	}

	/**
	 * Test integer validation with range
	 */
	public function test_integer_validation_with_range() {
		// Valid: within range.
		$result = Validator::integer( 50, 1, 100, 'page' );
		$this->assertTrue( $result );
		
		Validator::clear_errors();
		
		// Invalid: too low.
		$result = Validator::integer( 0, 1, 100, 'page' );
		$this->assertFalse( $result );
		
		Validator::clear_errors();
		
		// Invalid: too high.
		$result = Validator::integer( 101, 1, 100, 'page' );
		$this->assertFalse( $result );
	}

	/**
	 * Test required validation with value
	 */
	public function test_required_validation_with_value() {
		$result = Validator::required( 'some value', 'title' );
		$this->assertTrue( $result );
	}

	/**
	 * Test required validation with empty string
	 */
	public function test_required_validation_with_empty_string() {
		$result = Validator::required( '', 'title' );
		$this->assertFalse( $result );
		
		$errors = Validator::get_errors();
		$this->assertArrayHasKey( 'title', $errors );
		$this->assertStringContainsString( 'required', strtolower( $errors['title'][0] ) );
	}

	/**
	 * Test required validation with whitespace only
	 */
	public function test_required_validation_with_whitespace() {
		$result = Validator::required( '   ', 'title' );
		$this->assertFalse( $result );
	}

	/**
	 * Test required validation with array
	 */
	public function test_required_validation_with_array() {
		// Non-empty array should pass.
		$result = Validator::required( array( 'item1', 'item2' ), 'items' );
		$this->assertTrue( $result );
		
		Validator::clear_errors();
		
		// Empty array should fail.
		$result = Validator::required( array(), 'items' );
		$this->assertFalse( $result );
	}

	/**
	 * Test string length validation
	 */
	public function test_string_length_validation() {
		// Valid length.
		$result = Validator::string_length( 'Hello', 3, 10, 'name' );
		$this->assertTrue( $result );
		
		Validator::clear_errors();
		
		// Too short.
		$result = Validator::string_length( 'Hi', 3, 10, 'name' );
		$this->assertFalse( $result );
		
		Validator::clear_errors();
		
		// Too long.
		$result = Validator::string_length( 'This is way too long', 3, 10, 'name' );
		$this->assertFalse( $result );
	}

	/**
	 * Test URL validation
	 */
	public function test_url_validation() {
		// Valid URLs.
		$this->assertTrue( Validator::url( 'https://example.com', 'url' ) );
		
		Validator::clear_errors();
		$this->assertTrue( Validator::url( 'http://example.com', 'url' ) );
		
		Validator::clear_errors();
		$this->assertTrue( Validator::url( 'https://example.com/path?query=1', 'url' ) );
		
		// Invalid URLs.
		Validator::clear_errors();
		$this->assertFalse( Validator::url( 'not a url', 'url' ) );
		
		Validator::clear_errors();
		$this->assertFalse( Validator::url( '', 'url' ) );
	}

	/**
	 * Test JSON validation
	 */
	public function test_json_validation() {
		// Valid JSON.
		$this->assertTrue( Validator::json( '{"key": "value"}', 'data' ) );
		
		Validator::clear_errors();
		$this->assertTrue( Validator::json( '["item1", "item2"]', 'data' ) );
		
		// Invalid JSON.
		Validator::clear_errors();
		$this->assertFalse( Validator::json( '{invalid json}', 'data' ) );
		
		Validator::clear_errors();
		$this->assertFalse( Validator::json( 'not json at all', 'data' ) );
	}

	/**
	 * Test multiple validation errors accumulation
	 */
	public function test_multiple_validation_errors() {
		// Trigger multiple validation failures.
		Validator::email( 'invalid', 'email' );
		Validator::required( '', 'title' );
		Validator::integer( 150, 1, 100, 'count' );
		
		$errors = Validator::get_errors();
		
		// Should have 3 different field errors.
		$this->assertCount( 3, $errors );
		$this->assertArrayHasKey( 'email', $errors );
		$this->assertArrayHasKey( 'title', $errors );
		$this->assertArrayHasKey( 'count', $errors );
	}

	/**
	 * Test clearing errors
	 */
	public function test_clear_errors() {
		// Generate some errors.
		Validator::email( 'invalid', 'email' );
		$this->assertNotEmpty( Validator::get_errors() );
		
		// Clear errors.
		Validator::clear_errors();
		$this->assertEmpty( Validator::get_errors() );
	}
}

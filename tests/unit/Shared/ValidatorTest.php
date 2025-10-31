<?php
/**
 * Validator Utility Tests
 *
 * Comprehensive tests for the Validator utility class.
 * Critical for data validation and security.
 *
 * @package ATablesCharts\Tests\Unit\Shared
 * @since 1.0.0
 */

namespace ATablesCharts\Tests\Unit\Shared;

use ATablesCharts\Tests\Bootstrap\TestCase;
use ATablesCharts\Shared\Utils\Validator;

/**
 * ValidatorTest Class
 *
 * Tests all validation methods for security and correctness.
 */
class ValidatorTest extends TestCase {

	/**
	 * Set up test environment
	 */
	public function setUp(): void {
		parent::setUp();
		Validator::clear_errors();
	}

	/**
	 * Tear down test environment
	 */
	public function tearDown(): void {
		Validator::clear_errors();
		parent::tearDown();
	}

	// Email Validation Tests

	public function test_email_with_valid_email() {
		$this->assertTrue( Validator::email( 'test@example.com' ) );
		$this->assertEmpty( Validator::get_errors() );
	}

	public function test_email_with_invalid_email() {
		$this->assertFalse( Validator::email( 'invalid-email' ) );
		$errors = Validator::get_errors();
		$this->assertArrayHasKey( 'email', $errors );
	}

	public function test_email_with_empty_value() {
		$this->assertFalse( Validator::email( '' ) );
		$errors = Validator::get_errors();
		$this->assertArrayHasKey( 'email', $errors );
	}

	public function test_email_with_custom_field_name() {
		$this->assertFalse( Validator::email( 'bad', 'user_email' ) );
		$errors = Validator::get_errors();
		$this->assertArrayHasKey( 'user_email', $errors );
	}

	// URL Validation Tests

	public function test_url_with_valid_url() {
		$this->assertTrue( Validator::url( 'https://example.com' ) );
		$this->assertTrue( Validator::url( 'http://example.com/path' ) );
		$this->assertEmpty( Validator::get_errors() );
	}

	public function test_url_with_invalid_url() {
		Validator::clear_errors();
		$this->assertFalse( Validator::url( 'not-a-url' ) );
		$errors = Validator::get_errors();
		$this->assertArrayHasKey( 'url', $errors );
	}

	public function test_url_with_empty_value() {
		Validator::clear_errors();
		$this->assertFalse( Validator::url( '' ) );
		$errors = Validator::get_errors();
		$this->assertArrayHasKey( 'url', $errors );
	}

	// Integer Validation Tests

	public function test_integer_with_valid_integer() {
		$this->assertTrue( Validator::integer( 42 ) );
		$this->assertTrue( Validator::integer( '42' ) );
		$this->assertTrue( Validator::integer( 0 ) );
		$this->assertTrue( Validator::integer( -5 ) );
		$this->assertEmpty( Validator::get_errors() );
	}

	public function test_integer_with_invalid_integer() {
		Validator::clear_errors();
		$this->assertFalse( Validator::integer( 'not-a-number' ) );

		Validator::clear_errors();
		$this->assertFalse( Validator::integer( 3.14 ) );

		Validator::clear_errors();
		$this->assertFalse( Validator::integer( '3.14' ) );
	}

	public function test_integer_with_min_constraint() {
		$this->assertTrue( Validator::integer( 10, 5 ) );
		$this->assertTrue( Validator::integer( 5, 5 ) );

		Validator::clear_errors();
		$this->assertFalse( Validator::integer( 3, 5 ) );
		$errors = Validator::get_errors();
		$this->assertArrayHasKey( 'value', $errors );
	}

	public function test_integer_with_max_constraint() {
		$this->assertTrue( Validator::integer( 10, null, 20 ) );
		$this->assertTrue( Validator::integer( 20, null, 20 ) );

		Validator::clear_errors();
		$this->assertFalse( Validator::integer( 25, null, 20 ) );
		$errors = Validator::get_errors();
		$this->assertArrayHasKey( 'value', $errors );
	}

	public function test_integer_with_min_and_max_constraints() {
		$this->assertTrue( Validator::integer( 15, 10, 20 ) );
		$this->assertTrue( Validator::integer( 10, 10, 20 ) );
		$this->assertTrue( Validator::integer( 20, 10, 20 ) );

		Validator::clear_errors();
		$this->assertFalse( Validator::integer( 5, 10, 20 ) );

		Validator::clear_errors();
		$this->assertFalse( Validator::integer( 25, 10, 20 ) );
	}

	// Float Validation Tests

	public function test_float_with_valid_float() {
		$this->assertTrue( Validator::float( 3.14 ) );
		$this->assertTrue( Validator::float( '3.14' ) );
		$this->assertTrue( Validator::float( 0.0 ) );
		$this->assertTrue( Validator::float( -2.5 ) );
		$this->assertEmpty( Validator::get_errors() );
	}

	public function test_float_with_invalid_float() {
		Validator::clear_errors();
		$this->assertFalse( Validator::float( 'not-a-number' ) );
		$errors = Validator::get_errors();
		$this->assertArrayHasKey( 'value', $errors );
	}

	public function test_float_with_min_constraint() {
		$this->assertTrue( Validator::float( 5.5, 5.0 ) );
		$this->assertTrue( Validator::float( 5.0, 5.0 ) );

		Validator::clear_errors();
		$this->assertFalse( Validator::float( 4.9, 5.0 ) );
	}

	public function test_float_with_max_constraint() {
		$this->assertTrue( Validator::float( 9.9, null, 10.0 ) );
		$this->assertTrue( Validator::float( 10.0, null, 10.0 ) );

		Validator::clear_errors();
		$this->assertFalse( Validator::float( 10.1, null, 10.0 ) );
	}

	// Required Field Tests

	public function test_required_with_valid_string() {
		$this->assertTrue( Validator::required( 'hello' ) );
		$this->assertTrue( Validator::required( '0' ) );
		$this->assertEmpty( Validator::get_errors() );
	}

	public function test_required_with_empty_string() {
		Validator::clear_errors();
		$this->assertFalse( Validator::required( '' ) );

		Validator::clear_errors();
		$this->assertFalse( Validator::required( '   ' ) );
	}

	public function test_required_with_array() {
		$this->assertTrue( Validator::required( array( 1, 2, 3 ) ) );

		Validator::clear_errors();
		$this->assertFalse( Validator::required( array() ) );
	}

	// String Length Tests

	public function test_string_length_with_valid_length() {
		$this->assertTrue( Validator::string_length( 'hello', 3, 10 ) );
		$this->assertTrue( Validator::string_length( 'hi', 2, 10 ) );
		$this->assertTrue( Validator::string_length( 'hello world', 5, 20 ) );
		$this->assertEmpty( Validator::get_errors() );
	}

	public function test_string_length_with_min_constraint() {
		$this->assertTrue( Validator::string_length( 'hello', 5 ) );
		$this->assertTrue( Validator::string_length( 'hello', 3 ) );

		Validator::clear_errors();
		$this->assertFalse( Validator::string_length( 'hi', 5 ) );
	}

	public function test_string_length_with_max_constraint() {
		$this->assertTrue( Validator::string_length( 'hello', null, 10 ) );
		$this->assertTrue( Validator::string_length( 'hello', null, 5 ) );

		Validator::clear_errors();
		$this->assertFalse( Validator::string_length( 'hello world', null, 5 ) );
	}

	public function test_string_length_with_unicode_characters() {
		$this->assertTrue( Validator::string_length( 'héllo', 5, 10 ) );
		$this->assertTrue( Validator::string_length( '你好', 2, 10 ) );
	}

	// Alphanumeric Tests

	public function test_alphanumeric_with_valid_string() {
		$this->assertTrue( Validator::alphanumeric( 'abc123' ) );
		$this->assertTrue( Validator::alphanumeric( 'ABC' ) );
		$this->assertTrue( Validator::alphanumeric( '123' ) );
		$this->assertEmpty( Validator::get_errors() );
	}

	public function test_alphanumeric_with_invalid_string() {
		Validator::clear_errors();
		$this->assertFalse( Validator::alphanumeric( 'abc-123' ) );

		Validator::clear_errors();
		$this->assertFalse( Validator::alphanumeric( 'abc 123' ) );

		Validator::clear_errors();
		$this->assertFalse( Validator::alphanumeric( 'abc_123' ) );
	}

	// Slug Tests

	public function test_slug_with_valid_slug() {
		$this->assertTrue( Validator::slug( 'my-slug' ) );
		$this->assertTrue( Validator::slug( 'my_slug' ) );
		$this->assertTrue( Validator::slug( 'myslug123' ) );
		$this->assertTrue( Validator::slug( 'my-slug_123' ) );
		$this->assertEmpty( Validator::get_errors() );
	}

	public function test_slug_with_invalid_slug() {
		Validator::clear_errors();
		$this->assertFalse( Validator::slug( 'My-Slug' ) ); // Uppercase

		Validator::clear_errors();
		$this->assertFalse( Validator::slug( 'my slug' ) ); // Space

		Validator::clear_errors();
		$this->assertFalse( Validator::slug( 'my@slug' ) ); // Special char
	}

	// In Array Tests

	public function test_in_array_with_valid_value() {
		$allowed = array( 'active', 'pending', 'draft' );
		$this->assertTrue( Validator::in_array( 'active', $allowed ) );
		$this->assertTrue( Validator::in_array( 'pending', $allowed ) );
		$this->assertEmpty( Validator::get_errors() );
	}

	public function test_in_array_with_invalid_value() {
		Validator::clear_errors();
		$allowed = array( 'active', 'pending', 'draft' );
		$this->assertFalse( Validator::in_array( 'invalid', $allowed ) );
		$errors = Validator::get_errors();
		$this->assertArrayHasKey( 'field', $errors );
	}

	public function test_in_array_with_strict_type_checking() {
		Validator::clear_errors();
		$allowed = array( 1, 2, 3 );
		$this->assertTrue( Validator::in_array( 1, $allowed ) );

		Validator::clear_errors();
		$this->assertFalse( Validator::in_array( '1', $allowed ) ); // String vs int
	}

	// Table Title Validation Tests

	public function test_table_title_with_valid_title() {
		$result = Validator::table_title( 'My Table' );
		$this->assertTrue( $result['valid'] );
		$this->assertEmpty( $result['errors'] );
	}

	public function test_table_title_with_empty_title() {
		$result = Validator::table_title( '' );
		$this->assertFalse( $result['valid'] );
		$this->assertNotEmpty( $result['errors'] );
	}

	public function test_table_title_with_short_title() {
		$result = Validator::table_title( 'AB' ); // Less than 3 chars
		$this->assertFalse( $result['valid'] );
		$this->assertArrayHasKey( 'title', $result['errors'] );
	}

	public function test_table_title_with_long_title() {
		$result = Validator::table_title( str_repeat( 'A', 201 ) ); // More than 200 chars
		$this->assertFalse( $result['valid'] );
		$this->assertArrayHasKey( 'title', $result['errors'] );
	}

	// Table Data Validation Tests

	public function test_table_data_with_valid_data() {
		$data = array(
			array( 'Name', 'Age', 'City' ),
			array( 'John', '25', 'NYC' ),
			array( 'Jane', '30', 'LA' ),
		);
		$result = Validator::table_data( $data );
		$this->assertTrue( $result['valid'] );
		$this->assertEmpty( $result['errors'] );
	}

	public function test_table_data_with_non_array() {
		$result = Validator::table_data( 'not-an-array' );
		$this->assertFalse( $result['valid'] );
		$this->assertArrayHasKey( 'data', $result['errors'] );
	}

	public function test_table_data_with_empty_data() {
		$result = Validator::table_data( array() );
		$this->assertFalse( $result['valid'] );
		$this->assertArrayHasKey( 'data', $result['errors'] );
	}

	public function test_table_data_with_inconsistent_columns() {
		$data = array(
			array( 'Name', 'Age', 'City' ),
			array( 'John', '25' ), // Missing column
			array( 'Jane', '30', 'LA' ),
		);
		$result = Validator::table_data( $data );
		$this->assertFalse( $result['valid'] );
		$this->assertArrayHasKey( 'data', $result['errors'] );
	}

	public function test_table_data_with_non_array_row() {
		$data = array(
			array( 'Name', 'Age', 'City' ),
			'not-an-array', // Invalid row
			array( 'Jane', '30', 'LA' ),
		);
		$result = Validator::table_data( $data );
		$this->assertFalse( $result['valid'] );
		$this->assertArrayHasKey( 'data', $result['errors'] );
	}

	// Safe Filename Tests

	public function test_safe_filename_with_valid_filename() {
		$this->assertTrue( Validator::safe_filename( 'document.pdf' ) );
		$this->assertTrue( Validator::safe_filename( 'my-file_123.csv' ) );
		$this->assertTrue( Validator::safe_filename( 'data.xlsx' ) );
	}

	public function test_safe_filename_blocks_directory_traversal() {
		$this->assertFalse( Validator::safe_filename( '../etc/passwd' ) );
		$this->assertFalse( Validator::safe_filename( '../../config.php' ) );
		$this->assertFalse( Validator::safe_filename( 'file/../secret.txt' ) );
	}

	public function test_safe_filename_blocks_path_separators() {
		$this->assertFalse( Validator::safe_filename( 'path/to/file.txt' ) );
		$this->assertFalse( Validator::safe_filename( 'folder/document.pdf' ) );
	}

	public function test_safe_filename_blocks_null_bytes() {
		$this->assertFalse( Validator::safe_filename( "file\0.txt" ) );
		$this->assertFalse( Validator::safe_filename( "document.pdf\0.exe" ) );
	}

	// JSON Validation Tests

	public function test_json_with_valid_json() {
		$this->assertTrue( Validator::json( '{"name":"John","age":30}' ) );
		$this->assertTrue( Validator::json( '[]' ) );
		$this->assertTrue( Validator::json( '["a","b","c"]' ) );
		$this->assertEmpty( Validator::get_errors() );
	}

	public function test_json_with_invalid_json() {
		Validator::clear_errors();
		$this->assertFalse( Validator::json( '{invalid json}' ) );

		Validator::clear_errors();
		$this->assertFalse( Validator::json( "{'single': 'quotes'}" ) );
	}

	public function test_json_with_non_string() {
		Validator::clear_errors();
		$this->assertFalse( Validator::json( array( 'not', 'a', 'string' ) ) );
		$errors = Validator::get_errors();
		$this->assertArrayHasKey( 'json', $errors );
	}

	// Date Validation Tests

	public function test_date_with_valid_date() {
		$this->assertTrue( Validator::date( '2024-01-15' ) );
		$this->assertTrue( Validator::date( '2024-12-31' ) );
		$this->assertEmpty( Validator::get_errors() );
	}

	public function test_date_with_invalid_date() {
		Validator::clear_errors();
		$this->assertFalse( Validator::date( '2024-13-01' ) ); // Invalid month

		Validator::clear_errors();
		$this->assertFalse( Validator::date( '2024-02-30' ) ); // Invalid day

		Validator::clear_errors();
		$this->assertFalse( Validator::date( 'not-a-date' ) );
	}

	public function test_date_with_custom_format() {
		Validator::clear_errors();
		$this->assertTrue( Validator::date( '15/01/2024', 'd/m/Y' ) );

		Validator::clear_errors();
		$this->assertTrue( Validator::date( '01-15-2024', 'm-d-Y' ) );
	}

	// Array Structure Tests

	public function test_array_structure_with_valid_structure() {
		$data = array(
			'name'  => 'John',
			'email' => 'john@example.com',
			'age'   => 25,
		);
		$required = array( 'name', 'email', 'age' );
		$result = Validator::array_structure( $data, $required );

		$this->assertTrue( $result['valid'] );
		$this->assertEmpty( $result['missing'] );
	}

	public function test_array_structure_with_missing_keys() {
		$data = array(
			'name'  => 'John',
			'email' => 'john@example.com',
		);
		$required = array( 'name', 'email', 'age', 'city' );
		$result = Validator::array_structure( $data, $required );

		$this->assertFalse( $result['valid'] );
		$this->assertEquals( array( 'age', 'city' ), $result['missing'] );
	}

	public function test_array_structure_with_non_array() {
		$result = Validator::array_structure( 'not-an-array', array( 'name' ) );
		$this->assertFalse( $result['valid'] );
	}

	// Nonce Validation Tests

	public function test_nonce_with_valid_nonce() {
		// Our mock always returns true for non-empty nonces
		$this->assertTrue( Validator::nonce( 'valid-nonce', 'action' ) );
	}

	public function test_nonce_with_empty_nonce() {
		$this->assertFalse( Validator::nonce( '', 'action' ) );
	}

	// Multiple Fields Validation Tests

	public function test_validate_fields_with_valid_data() {
		$data = array(
			'title' => 'My Table',
			'email' => 'test@example.com',
			'age'   => 25,
		);
		$rules = array(
			'title' => array( 'required', 'string_length:3:200' ),
			'email' => array( 'required', 'email' ),
			'age'   => array( 'integer:18:100' ),
		);
		$result = Validator::validate_fields( $data, $rules );

		$this->assertTrue( $result['valid'] );
		$this->assertEmpty( $result['errors'] );
	}

	public function test_validate_fields_with_invalid_data() {
		$data = array(
			'title' => 'AB', // Too short
			'email' => 'invalid-email',
			'age'   => 15, // Too young
		);
		$rules = array(
			'title' => array( 'required', 'string_length:3:200' ),
			'email' => array( 'required', 'email' ),
			'age'   => array( 'integer:18:100' ),
		);
		$result = Validator::validate_fields( $data, $rules );

		$this->assertFalse( $result['valid'] );
		$this->assertNotEmpty( $result['errors'] );
		$this->assertArrayHasKey( 'title', $result['errors'] );
		$this->assertArrayHasKey( 'email', $result['errors'] );
		$this->assertArrayHasKey( 'age', $result['errors'] );
	}

	public function test_validate_fields_with_missing_required_fields() {
		$data = array(
			'email' => 'test@example.com',
		);
		$rules = array(
			'title' => array( 'required' ),
			'email' => array( 'required', 'email' ),
		);
		$result = Validator::validate_fields( $data, $rules );

		$this->assertFalse( $result['valid'] );
		$this->assertArrayHasKey( 'title', $result['errors'] );
	}

	// Error Management Tests

	public function test_clear_errors() {
		Validator::email( 'invalid' ); // Generate error
		$this->assertNotEmpty( Validator::get_errors() );

		Validator::clear_errors();
		$this->assertEmpty( Validator::get_errors() );
	}

	public function test_get_errors_returns_array() {
		$errors = Validator::get_errors();
		$this->assertIsArray( $errors );
	}

	public function test_multiple_errors_for_same_field() {
		Validator::clear_errors();
		Validator::required( '', 'title' );
		Validator::string_length( '', 5, null, 'title' );

		$errors = Validator::get_errors();
		$this->assertArrayHasKey( 'title', $errors );
		$this->assertCount( 2, $errors['title'] );
	}

	// Security Tests

	public function test_validator_prevents_sql_injection_in_slug() {
		$this->assertFalse( Validator::slug( "slug'; DROP TABLE users--" ) );
		$this->assertFalse( Validator::slug( "slug' OR '1'='1" ) );
	}

	public function test_validator_prevents_xss_in_alphanumeric() {
		$this->assertFalse( Validator::alphanumeric( '<script>alert("xss")</script>' ) );
		$this->assertFalse( Validator::alphanumeric( 'test<>' ) );
	}

	public function test_validator_sanitizes_field_names() {
		Validator::clear_errors();
		Validator::required( '', 'user_name' );
		$errors = Validator::get_errors();

		// Field name should be in errors
		$this->assertArrayHasKey( 'user_name', $errors );
	}
}

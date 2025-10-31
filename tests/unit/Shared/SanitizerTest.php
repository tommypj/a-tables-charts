<?php
/**
 * Sanitizer Utility Tests
 *
 * Comprehensive tests for the Sanitizer utility class.
 * Critical for XSS prevention and data sanitization.
 *
 * @package ATablesCharts\Tests\Unit\Shared
 * @since 1.0.0
 */

namespace ATablesCharts\Tests\Unit\Shared;

use ATablesCharts\Tests\Bootstrap\TestCase;
use ATablesCharts\Shared\Utils\Sanitizer;

/**
 * SanitizerTest Class
 *
 * Tests all sanitization methods for security and correctness.
 */
class SanitizerTest extends TestCase {

	// Text Sanitization Tests

	public function test_text_with_clean_string() {
		$result = Sanitizer::text( 'Hello World' );
		$this->assertEquals( 'Hello World', $result );
	}

	public function test_text_removes_html_tags() {
		$result = Sanitizer::text( 'Hello <b>World</b>' );
		$this->assertEquals( 'Hello World', $result );
	}

	public function test_text_removes_script_tags() {
		$result = Sanitizer::text( 'Hello<script>alert("xss")</script>World' );
		$this->assertEquals( 'HelloWorld', $result );
		$this->assertStringNotContainsString( 'script', $result );
	}

	public function test_text_removes_dangerous_html() {
		$result = Sanitizer::text( '<img src=x onerror=alert(1)>' );
		$this->assertStringNotContainsString( 'onerror', $result );
		$this->assertStringNotContainsString( '<img', $result );
	}

	// Textarea Sanitization Tests

	public function test_textarea_preserves_newlines() {
		$input = "Line 1\nLine 2\nLine 3";
		$result = Sanitizer::textarea( $input );
		$this->assertStringContainsString( "\n", $result );
	}

	public function test_textarea_removes_carriage_returns() {
		$input = "Line 1\r\nLine 2\r\n";
		$result = Sanitizer::textarea( $input );
		$this->assertStringNotContainsString( "\r", $result );
	}

	// Email Sanitization Tests

	public function test_email_with_valid_email() {
		$result = Sanitizer::email( 'Test@Example.COM' );
		$this->assertEquals( 'test@example.com', $result );
	}

	public function test_email_trims_whitespace() {
		$result = Sanitizer::email( '  test@example.com  ' );
		$this->assertEquals( 'test@example.com', $result );
	}

	public function test_email_lowercases_email() {
		$result = Sanitizer::email( 'USER@DOMAIN.COM' );
		$this->assertEquals( 'user@domain.com', $result );
	}

	// URL Sanitization Tests

	public function test_url_with_valid_url() {
		$result = Sanitizer::url( 'https://example.com' );
		$this->assertStringContainsString( 'example.com', $result );
	}

	public function test_url_removes_javascript_protocol() {
		$result = Sanitizer::url( 'javascript:alert(1)' );
		$this->assertStringNotContainsString( 'javascript', $result );
	}

	public function test_url_sanitizes_special_characters() {
		$result = Sanitizer::url( 'https://example.com/path?param=value' );
		$this->assertIsString( $result );
	}

	// Integer Sanitization Tests

	public function test_integer_with_valid_integer() {
		$this->assertEquals( 42, Sanitizer::integer( 42 ) );
		$this->assertEquals( 42, Sanitizer::integer( '42' ) );
		$this->assertEquals( 0, Sanitizer::integer( 0 ) );
		$this->assertEquals( -5, Sanitizer::integer( -5 ) );
	}

	public function test_integer_with_float() {
		$this->assertEquals( 3, Sanitizer::integer( 3.14 ) );
		$this->assertEquals( 3, Sanitizer::integer( '3.14' ) );
	}

	public function test_integer_with_invalid_input() {
		$this->assertEquals( 0, Sanitizer::integer( 'not-a-number' ) );
		$this->assertEquals( 0, Sanitizer::integer( 'abc123' ) );
	}

	// Float Sanitization Tests

	public function test_float_with_valid_float() {
		$this->assertEquals( 3.14, Sanitizer::float( 3.14 ) );
		$this->assertEquals( 3.14, Sanitizer::float( '3.14' ) );
		$this->assertEquals( 0.0, Sanitizer::float( 0.0 ) );
	}

	public function test_float_with_integer() {
		$this->assertEquals( 42.0, Sanitizer::float( 42 ) );
	}

	public function test_float_with_invalid_input() {
		$this->assertEquals( 0.0, Sanitizer::float( 'not-a-number' ) );
	}

	// Boolean Sanitization Tests

	public function test_boolean_with_true_values() {
		$this->assertTrue( Sanitizer::boolean( true ) );
		$this->assertTrue( Sanitizer::boolean( 'true' ) );
		$this->assertTrue( Sanitizer::boolean( '1' ) );
		$this->assertTrue( Sanitizer::boolean( 1 ) );
		$this->assertTrue( Sanitizer::boolean( 'yes' ) );
		$this->assertTrue( Sanitizer::boolean( 'on' ) );
	}

	public function test_boolean_with_false_values() {
		$this->assertFalse( Sanitizer::boolean( false ) );
		$this->assertFalse( Sanitizer::boolean( 'false' ) );
		$this->assertFalse( Sanitizer::boolean( '0' ) );
		$this->assertFalse( Sanitizer::boolean( 0 ) );
		$this->assertFalse( Sanitizer::boolean( 'no' ) );
		$this->assertFalse( Sanitizer::boolean( 'off' ) );
	}

	// Array Text Sanitization Tests

	public function test_array_text_with_clean_array() {
		$input = array( 'Hello', 'World', 'Test' );
		$result = Sanitizer::array_text( $input );
		$this->assertEquals( $input, $result );
	}

	public function test_array_text_removes_html_from_all_items() {
		$input = array( '<b>Hello</b>', '<script>alert(1)</script>', 'Clean' );
		$result = Sanitizer::array_text( $input );
		
		$this->assertEquals( 'Hello', $result[0] );
		$this->assertEquals( 'Clean', $result[2] );
		$this->assertStringNotContainsString( '<b>', $result[0] );
		$this->assertStringNotContainsString( 'script', $result[1] );
	}

	public function test_array_text_with_non_array() {
		$result = Sanitizer::array_text( 'not-an-array' );
		$this->assertEquals( array(), $result );
	}

	// Array Recursive Sanitization Tests

	public function test_array_recursive_with_nested_array() {
		$input = array(
			'name' => 'John',
			'data' => array(
				'email' => 'test@example.com',
				'meta'  => array(
					'age' => '25',
				),
			),
		);
		$result = Sanitizer::array_recursive( $input );
		
		$this->assertIsArray( $result );
		$this->assertEquals( 'John', $result['name'] );
		$this->assertIsArray( $result['data'] );
		$this->assertEquals( 'test@example.com', $result['data']['email'] );
	}

	public function test_array_recursive_removes_html_from_nested_values() {
		$input = array(
			'title' => '<b>Bold</b>',
			'nested' => array(
				'content' => '<script>alert(1)</script>',
			),
		);
		$result = Sanitizer::array_recursive( $input );
		
		$this->assertEquals( 'Bold', $result['title'] );
		$this->assertStringNotContainsString( '<b>', $result['title'] );
		$this->assertStringNotContainsString( 'script', $result['nested']['content'] );
	}

	public function test_array_recursive_with_non_array() {
		$result = Sanitizer::array_recursive( '<b>Text</b>' );
		$this->assertEquals( 'Text', $result );
	}

	// HTML Sanitization Tests

	public function test_html_allows_safe_tags() {
		$input = '<p>Hello <b>World</b></p>';
		$result = Sanitizer::html( $input );
		
		$this->assertStringContainsString( '<p>', $result );
		$this->assertStringContainsString( '<b>', $result );
	}

	public function test_html_removes_script_tags() {
		$input = '<p>Hello</p><script>alert(1)</script>';
		$result = Sanitizer::html( $input );
		
		$this->assertStringContainsString( '<p>', $result );
		$this->assertStringNotContainsString( '<script>', $result );
		$this->assertStringNotContainsString( 'alert', $result );
	}

	public function test_html_removes_dangerous_attributes() {
		$input = '<a href="#" onclick="alert(1)">Link</a>';
		$result = Sanitizer::html( $input );
		
		$this->assertStringContainsString( '<a', $result );
		$this->assertStringNotContainsString( 'onclick', $result );
	}

	public function test_html_with_custom_allowed_tags() {
		$input = '<p>Text</p><div>Content</div>';
		$allowed = array( 'div' => array() );
		$result = Sanitizer::html( $input, $allowed );
		
		$this->assertStringContainsString( 'div', $result );
		$this->assertStringNotContainsString( '<p>', $result );
	}

	// SQL OrderBy Sanitization Tests

	public function test_sql_orderby_with_valid_field() {
		$allowed = array( 'id', 'title', 'created_at' );
		$result = Sanitizer::sql_orderby( 'title', $allowed, 'id' );
		
		$this->assertEquals( 'title', $result );
	}

	public function test_sql_orderby_with_invalid_field_returns_default() {
		$allowed = array( 'id', 'title', 'created_at' );
		$result = Sanitizer::sql_orderby( 'malicious', $allowed, 'id' );
		
		$this->assertEquals( 'id', $result );
	}

	public function test_sql_orderby_blocks_sql_injection() {
		$allowed = array( 'id', 'title' );
		$result = Sanitizer::sql_orderby( "id; DROP TABLE users--", $allowed, 'id' );
		
		$this->assertEquals( 'id', $result );
		$this->assertStringNotContainsString( 'DROP', $result );
	}

	public function test_sql_orderby_lowercases_field() {
		$allowed = array( 'id', 'title', 'created_at' );
		$result = Sanitizer::sql_orderby( 'TITLE', $allowed, 'id' );
		
		$this->assertEquals( 'title', $result );
	}

	public function test_sql_orderby_trims_whitespace() {
		$allowed = array( 'id', 'title', 'created_at' );
		$result = Sanitizer::sql_orderby( '  title  ', $allowed, 'id' );
		
		$this->assertEquals( 'title', $result );
	}

	// SQL Order Direction Sanitization Tests

	public function test_sql_order_with_valid_asc() {
		$result = Sanitizer::sql_order( 'ASC', 'DESC' );
		$this->assertEquals( 'ASC', $result );
	}

	public function test_sql_order_with_valid_desc() {
		$result = Sanitizer::sql_order( 'DESC', 'ASC' );
		$this->assertEquals( 'DESC', $result );
	}

	public function test_sql_order_with_lowercase_input() {
		$this->assertEquals( 'ASC', Sanitizer::sql_order( 'asc', 'DESC' ) );
		$this->assertEquals( 'DESC', Sanitizer::sql_order( 'desc', 'ASC' ) );
	}

	public function test_sql_order_with_invalid_direction_returns_default() {
		$result = Sanitizer::sql_order( 'INVALID', 'ASC' );
		$this->assertEquals( 'ASC', $result );
	}

	public function test_sql_order_blocks_sql_injection() {
		$result = Sanitizer::sql_order( "ASC; DROP TABLE users--", 'DESC' );
		$this->assertEquals( 'DESC', $result );
		$this->assertStringNotContainsString( 'DROP', $result );
	}

	// Filename Sanitization Tests

	public function test_filename_with_clean_filename() {
		$result = Sanitizer::filename( 'document.pdf' );
		$this->assertEquals( 'document.pdf', $result );
	}

	public function test_filename_removes_special_characters() {
		$result = Sanitizer::filename( 'my file!@#$.pdf' );
		$this->assertStringNotContainsString( '!', $result );
		$this->assertStringNotContainsString( '@', $result );
		$this->assertStringNotContainsString( '#', $result );
		$this->assertStringNotContainsString( '$', $result );
	}

	public function test_filename_allows_dots_and_hyphens() {
		$result = Sanitizer::filename( 'my-file_v2.1.pdf' );
		$this->assertStringContainsString( '-', $result );
		$this->assertStringContainsString( '_', $result );
		$this->assertStringContainsString( '.', $result );
	}

	// Key Sanitization Tests

	public function test_key_with_clean_key() {
		$result = Sanitizer::key( 'my_key_123' );
		$this->assertEquals( 'my_key_123', $result );
	}

	public function test_key_lowercases_input() {
		$result = Sanitizer::key( 'MY_KEY' );
		$this->assertEquals( 'my_key', $result );
	}

	public function test_key_removes_special_characters() {
		$result = Sanitizer::key( 'my@key#123!' );
		$this->assertEquals( 'mykey123', $result );
		$this->assertStringNotContainsString( '@', $result );
		$this->assertStringNotContainsString( '#', $result );
		$this->assertStringNotContainsString( '!', $result );
	}

	public function test_key_allows_hyphens_and_underscores() {
		$result = Sanitizer::key( 'my-key_123' );
		$this->assertEquals( 'my-key_123', $result );
	}

	// Slug Sanitization Tests

	public function test_slug_with_clean_slug() {
		$result = Sanitizer::slug( 'my-slug' );
		$this->assertEquals( 'my-slug', $result );
	}

	public function test_slug_lowercases_input() {
		$result = Sanitizer::slug( 'MY-SLUG' );
		$this->assertEquals( 'my-slug', $result );
	}

	public function test_slug_replaces_spaces_with_hyphens() {
		$result = Sanitizer::slug( 'my slug title' );
		$this->assertStringContainsString( '-', $result );
		$this->assertStringNotContainsString( ' ', $result );
	}

	public function test_slug_removes_special_characters() {
		$result = Sanitizer::slug( 'my@slug#title!' );
		$this->assertStringNotContainsString( '@', $result );
		$this->assertStringNotContainsString( '#', $result );
		$this->assertStringNotContainsString( '!', $result );
	}

	public function test_slug_trims_hyphens() {
		$result = Sanitizer::slug( '--my-slug--' );
		$this->assertStringStartsNotWith( '-', $result );
		$this->assertStringEndsNotWith( '-', $result );
	}

	// Hex Color Sanitization Tests

	public function test_hex_color_with_valid_6_digit() {
		$result = Sanitizer::hex_color( '#FF5733' );
		$this->assertEquals( '#FF5733', $result );
		
		$result = Sanitizer::hex_color( '#ff5733' );
		$this->assertEquals( '#ff5733', $result );
	}

	public function test_hex_color_with_valid_3_digit() {
		$result = Sanitizer::hex_color( '#F53' );
		$this->assertEquals( '#F53', $result );
	}

	public function test_hex_color_with_invalid_format() {
		$result = Sanitizer::hex_color( 'FF5733' );
		$this->assertNull( $result );
		
		$result = Sanitizer::hex_color( '#GG5733' );
		$this->assertNull( $result );
	}

	public function test_hex_color_with_empty_string() {
		$result = Sanitizer::hex_color( '' );
		$this->assertEquals( '', $result );
	}

	// Comma List Sanitization Tests

	public function test_comma_list_with_clean_list() {
		$result = Sanitizer::comma_list( 'apple, banana, orange' );
		$this->assertStringContainsString( 'apple', $result );
		$this->assertStringContainsString( 'banana', $result );
		$this->assertStringContainsString( 'orange', $result );
	}

	public function test_comma_list_trims_items() {
		$result = Sanitizer::comma_list( '  apple  ,  banana  ,  orange  ' );
		$this->assertEquals( 'apple, banana, orange', $result );
	}

	public function test_comma_list_removes_empty_items() {
		$result = Sanitizer::comma_list( 'apple, , banana, , orange' );
		$this->assertEquals( 'apple, banana, orange', $result );
	}

	public function test_comma_list_removes_html_tags() {
		$result = Sanitizer::comma_list( '<b>apple</b>, banana, <script>orange</script>' );
		$this->assertEquals( 'apple, banana, orange', $result );
		$this->assertStringNotContainsString( '<b>', $result );
		$this->assertStringNotContainsString( 'script', $result );
	}

	// Deep Clean Tests

	public function test_deep_clean_with_string() {
		$result = Sanitizer::deep_clean( '<p>Hello <b>World</b></p>' );
		$this->assertStringContainsString( 'Hello', $result );
		$this->assertStringContainsString( '<p>', $result );
	}

	public function test_deep_clean_with_simple_array() {
		$input = array(
			'title' => '<p>Title</p>',
			'content' => '<b>Content</b>',
		);
		$result = Sanitizer::deep_clean( $input );
		
		$this->assertIsArray( $result );
		$this->assertStringContainsString( '<p>', $result['title'] );
	}

	public function test_deep_clean_with_nested_array() {
		$input = array(
			'post' => array(
				'title' => '<p>Title</p>',
				'meta' => array(
					'author' => '<b>John</b>',
				),
			),
		);
		$result = Sanitizer::deep_clean( $input );
		
		$this->assertIsArray( $result );
		$this->assertIsArray( $result['post'] );
		$this->assertIsArray( $result['post']['meta'] );
		$this->assertStringContainsString( 'John', $result['post']['meta']['author'] );
	}

	public function test_deep_clean_removes_dangerous_html() {
		$input = array(
			'content' => '<script>alert(1)</script>',
			'nested' => array(
				'data' => '<img src=x onerror=alert(1)>',
			),
		);
		$result = Sanitizer::deep_clean( $input );
		
		$this->assertStringNotContainsString( '<script>', $result['content'] );
		$this->assertStringNotContainsString( 'onerror', $result['nested']['data'] );
	}

	// Strip Tags Tests

	public function test_strip_tags_removes_all_html() {
		$result = Sanitizer::strip_tags( '<p>Hello <b>World</b></p>' );
		$this->assertEquals( 'Hello World', $result );
		$this->assertStringNotContainsString( '<p>', $result );
		$this->assertStringNotContainsString( '<b>', $result );
	}

	public function test_strip_tags_removes_script_tags() {
		$result = Sanitizer::strip_tags( 'Hello<script>alert(1)</script>World' );
		$this->assertEquals( 'HelloWorld', $result );
		$this->assertStringNotContainsString( 'script', $result );
		$this->assertStringNotContainsString( 'alert', $result );
	}

	public function test_strip_tags_removes_style_tags() {
		$result = Sanitizer::strip_tags( '<style>body{color:red}</style>Content' );
		$this->assertEquals( 'Content', $result );
		$this->assertStringNotContainsString( 'style', $result );
		$this->assertStringNotContainsString( 'color', $result );
	}

	// Security Tests - XSS Prevention

	public function test_sanitizer_prevents_stored_xss() {
		$xss_attempts = array(
			'<script>alert("xss")</script>',
			'<img src=x onerror=alert(1)>',
			'<svg onload=alert(1)>',
			'<iframe src="javascript:alert(1)">',
			'<body onload=alert(1)>',
			'<input onfocus=alert(1) autofocus>',
			'<select onfocus=alert(1) autofocus>',
			'<textarea onfocus=alert(1) autofocus>',
			'<marquee onstart=alert(1)>',
		);

		foreach ( $xss_attempts as $attempt ) {
			$result = Sanitizer::text( $attempt );
			$this->assertStringNotContainsString( 'alert', $result, "Failed to sanitize: $attempt" );
			$this->assertStringNotContainsString( 'onerror', $result );
			$this->assertStringNotContainsString( 'onload', $result );
			$this->assertStringNotContainsString( 'onfocus', $result );
		}
	}

	public function test_sanitizer_prevents_attribute_xss() {
		$input = '<a href="javascript:alert(1)">Click</a>';
		$result = Sanitizer::text( $input );
		$this->assertStringNotContainsString( 'javascript', $result );
	}

	public function test_sanitizer_prevents_url_xss() {
		$xss_urls = array(
			'javascript:alert(1)',
			'data:text/html,<script>alert(1)</script>',
			'vbscript:msgbox(1)',
		);

		foreach ( $xss_urls as $url ) {
			$result = Sanitizer::url( $url );
			$this->assertStringNotContainsString( 'javascript', $result );
			$this->assertStringNotContainsString( 'vbscript', $result );
		}
	}

	// Security Tests - SQL Injection Prevention

	public function test_sanitizer_prevents_sql_injection_in_orderby() {
		$sql_attempts = array(
			"id; DROP TABLE users--",
			"title' OR '1'='1",
			"id UNION SELECT password FROM users",
			"(SELECT * FROM users)",
		);

		$allowed = array( 'id', 'title', 'created_at' );

		foreach ( $sql_attempts as $attempt ) {
			$result = Sanitizer::sql_orderby( $attempt, $allowed, 'id' );
			$this->assertEquals( 'id', $result, "Failed to block SQL injection: $attempt" );
			$this->assertStringNotContainsString( 'DROP', $result );
			$this->assertStringNotContainsString( 'UNION', $result );
			$this->assertStringNotContainsString( 'SELECT', $result );
		}
	}

	public function test_sanitizer_prevents_sql_injection_in_order() {
		$sql_attempts = array(
			"ASC; DROP TABLE users--",
			"DESC' OR '1'='1",
			"ASC) UNION SELECT NULL--",
		);

		foreach ( $sql_attempts as $attempt ) {
			$result = Sanitizer::sql_order( $attempt, 'ASC' );
			$this->assertEquals( 'ASC', $result, "Failed to block SQL injection: $attempt" );
			$this->assertStringNotContainsString( 'DROP', $result );
			$this->assertStringNotContainsString( 'UNION', $result );
		}
	}

	// Edge Cases Tests

	public function test_sanitizer_handles_null_input() {
		$this->assertEquals( '', Sanitizer::text( null ) );
		$this->assertEquals( 0, Sanitizer::integer( null ) );
		$this->assertEquals( 0.0, Sanitizer::float( null ) );
		$this->assertFalse( Sanitizer::boolean( null ) );
	}

	public function test_sanitizer_handles_empty_strings() {
		$this->assertEquals( '', Sanitizer::text( '' ) );
		$this->assertEquals( '', Sanitizer::email( '' ) );
		$this->assertEquals( '', Sanitizer::slug( '' ) );
	}

	public function test_sanitizer_handles_very_long_strings() {
		$long_string = str_repeat( 'A', 10000 );
		$result = Sanitizer::text( $long_string );
		$this->assertIsString( $result );
		$this->assertLessThanOrEqual( 10000, strlen( $result ) );
	}

	public function test_sanitizer_handles_unicode_characters() {
		$unicode = '你好世界 😀 Héllo';
		$result = Sanitizer::text( $unicode );
		$this->assertIsString( $result );
	}
}

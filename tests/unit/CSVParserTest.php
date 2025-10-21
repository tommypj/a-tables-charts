<?php
/**
 * CSV Parser Tests
 *
 * Tests for the CSVParser class.
 *
 * @package ATablesCharts\Tests\Unit
 * @since 1.0.0
 */

namespace ATablesCharts\Tests\Unit;

use ATablesCharts\Tests\Bootstrap\TestCase;
use ATablesCharts\DataSources\Parsers\CSVParser;

/**
 * CSVParserTest Class
 */
class CSVParserTest extends TestCase {

	/**
	 * Parser instance
	 *
	 * @var CSVParser
	 */
	private $parser;

	/**
	 * Set up test environment
	 */
	public function setUp(): void {
		parent::setUp();
		$this->parser = new CSVParser();
	}

	/**
	 * Test parsing valid CSV file
	 */
	public function test_parse_valid_csv() {
		$file_path = $this->get_fixture_path( 'sample.csv' );

		$result = $this->parser->parse( $file_path );

		$this->assertTrue( $result->success );
		$this->assertIsArray( $result->headers );
		$this->assertIsArray( $result->data );
		$this->assertGreaterThan( 0, $result->row_count );
		$this->assertGreaterThan( 0, $result->column_count );
	}

	/**
	 * Test parsing CSV with headers
	 */
	public function test_parse_csv_with_headers() {
		$file_path = $this->get_fixture_path( 'sample.csv' );

		$result = $this->parser->parse( $file_path, array( 'has_header' => true ) );

		$this->assertContains( 'Name', $result->headers );
		$this->assertContains( 'Email', $result->headers );
		$this->assertContains( 'Age', $result->headers );
	}

	/**
	 * Test parsing CSV without headers
	 */
	public function test_parse_csv_without_headers() {
		$file_path = $this->get_fixture_path( 'sample.csv' );

		$result = $this->parser->parse( $file_path, array( 'has_header' => false ) );

		// Should generate column names like Column 1, Column 2, etc.
		$this->assertStringContainsString( 'Column', $result->headers[0] );
	}

	/**
	 * Test parsing non-existent file
	 */
	public function test_parse_non_existent_file() {
		$result = $this->parser->parse( 'non_existent_file.csv' );

		$this->assertFalse( $result->success );
		$this->assertNotEmpty( $result->error );
	}

	/**
	 * Test parsing with custom delimiter
	 */
	public function test_parse_with_custom_delimiter() {
		$file_path = $this->get_fixture_path( 'sample.csv' );

		$result = $this->parser->parse( $file_path, array( 'delimiter' => ',' ) );

		$this->assertTrue( $result->success );
	}

	/**
	 * Test validation of CSV file
	 */
	public function test_validate_csv_file() {
		$file_path = $this->get_fixture_path( 'sample.csv' );

		$validation = $this->parser->validate( $file_path );

		$this->assertTrue( $validation['valid'] );
		$this->assertEmpty( $validation['errors'] );
	}

	/**
	 * Test validation of non-CSV file
	 */
	public function test_validate_non_csv_file() {
		$validation = $this->parser->validate( 'test.txt' );

		// Should check file extension
		$this->assertIsArray( $validation );
		$this->assertArrayHasKey( 'valid', $validation );
	}

	/**
	 * Test parsing products CSV
	 */
	public function test_parse_products_csv() {
		$file_path = $this->get_fixture_path( 'products.csv' );

		$result = $this->parser->parse( $file_path );

		$this->assertTrue( $result->success );
		$this->assertContains( 'Product', $result->headers );
		$this->assertContains( 'Price', $result->headers );
		$this->assertGreaterThan( 0, $result->row_count );
	}

	/**
	 * Test row count accuracy
	 */
	public function test_row_count_accuracy() {
		$file_path = $this->get_fixture_path( 'sample.csv' );

		$result = $this->parser->parse( $file_path, array( 'has_header' => true ) );

		// Count actual data rows (excluding header)
		$this->assertEquals( count( $result->data ), $result->row_count );
	}

	/**
	 * Test column count accuracy
	 */
	public function test_column_count_accuracy() {
		$file_path = $this->get_fixture_path( 'sample.csv' );

		$result = $this->parser->parse( $file_path );

		$this->assertEquals( count( $result->headers ), $result->column_count );
	}

	/**
	 * Test data structure
	 */
	public function test_data_structure() {
		$file_path = $this->get_fixture_path( 'sample.csv' );

		$result = $this->parser->parse( $file_path );

		// Each row should be an array
		$this->assertIsArray( $result->data[0] );
		// Each row should have same number of columns as headers
		$this->assertCount( $result->column_count, $result->data[0] );
	}

	/**
	 * Test empty CSV handling
	 */
	public function test_parse_empty_csv() {
		// Create temporary empty CSV
		$temp_file = tempnam( sys_get_temp_dir(), 'csv' );
		file_put_contents( $temp_file, '' );

		$result = $this->parser->parse( $temp_file );

		// Should handle empty file gracefully
		$this->assertFalse( $result->success );

		unlink( $temp_file );
	}

	/**
	 * Test CSV with special characters in data
	 */
	public function test_parse_csv_with_special_characters() {
		// Create temp CSV with special chars
		$temp_file = tempnam( sys_get_temp_dir(), 'csv' );
		$content = "Name,Description\n";
		$content .= "Test,\"Contains, comma\"\n";
		$content .= "Another,\"Has \"\"quotes\"\"\"\n";
		file_put_contents( $temp_file, $content );

		$result = $this->parser->parse( $temp_file, array( 'has_header' => true ) );

		$this->assertTrue( $result->success );
		$this->assertCount( 2, $result->data );

		unlink( $temp_file );
	}
}

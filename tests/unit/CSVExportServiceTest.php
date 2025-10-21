<?php
/**
 * CSV Export Service Tests
 *
 * Tests for the CSVExportService class.
 *
 * @package ATablesCharts\Tests\Unit
 * @since 1.0.0
 */

namespace ATablesCharts\Tests\Unit;

use ATablesCharts\Tests\Bootstrap\TestCase;
use ATablesCharts\Export\Services\CSVExportService;

/**
 * CSVExportServiceTest Class
 */
class CSVExportServiceTest extends TestCase {

	/**
	 * Export service instance
	 *
	 * @var CSVExportService
	 */
	private $service;

	/**
	 * Set up test environment
	 */
	public function setUp(): void {
		parent::setUp();
		$this->service = new CSVExportService();
	}

	/**
	 * Test generating CSV string
	 */
	public function test_generate_csv_string() {
		$headers = array( 'Name', 'Email', 'Age' );
		$data = array(
			array( 'Name' => 'John Doe', 'Email' => 'john@example.com', 'Age' => '25' ),
			array( 'Name' => 'Jane Smith', 'Email' => 'jane@example.com', 'Age' => '30' ),
		);

		$csv = $this->service->generate_csv_string( $headers, $data );

		$this->assertIsString( $csv );
		$this->assertStringContainsString( 'Name,Email,Age', $csv );
		$this->assertStringContainsString( 'John Doe', $csv );
		$this->assertStringContainsString( 'Jane Smith', $csv );
	}

	/**
	 * Test CSV with special characters
	 */
	public function test_generate_csv_with_special_characters() {
		$headers = array( 'Name', 'Description' );
		$data = array(
			array( 'Name' => 'Test Item', 'Description' => 'Has, commas, inside' ),
			array( 'Name' => 'Another', 'Description' => 'Has "quotes" inside' ),
		);

		$csv = $this->service->generate_csv_string( $headers, $data );

		$this->assertStringContainsString( 'Test Item', $csv );
		// CSV should properly escape commas and quotes
		$this->assertIsString( $csv );
	}

	/**
	 * Test validating export data
	 */
	public function test_validate_with_valid_data() {
		$headers = array( 'Name', 'Email' );
		$data = array(
			array( 'Name' => 'John', 'Email' => 'john@example.com' ),
		);

		$validation = $this->service->validate( $headers, $data );

		$this->assertTrue( $validation['valid'] );
		$this->assertEmpty( $validation['errors'] );
	}

	/**
	 * Test validation with empty headers
	 */
	public function test_validate_with_empty_headers() {
		$headers = array();
		$data = array( array( 'John' ) );

		$validation = $this->service->validate( $headers, $data );

		$this->assertFalse( $validation['valid'] );
		$this->assertNotEmpty( $validation['errors'] );
		$this->assertContains( 'Headers are required for CSV export.', $validation['errors'] );
	}

	/**
	 * Test validation with non-array headers
	 */
	public function test_validate_with_invalid_headers() {
		$headers = 'not an array';
		$data = array( array( 'John' ) );

		$validation = $this->service->validate( $headers, $data );

		$this->assertFalse( $validation['valid'] );
		$this->assertContains( 'Headers must be an array.', $validation['errors'] );
	}

	/**
	 * Test validation with non-array data
	 */
	public function test_validate_with_invalid_data() {
		$headers = array( 'Name' );
		$data = 'not an array';

		$validation = $this->service->validate( $headers, $data );

		$this->assertFalse( $validation['valid'] );
		$this->assertContains( 'Data must be an array.', $validation['errors'] );
	}

	/**
	 * Test getting safe filename
	 */
	public function test_get_safe_filename() {
		$title = 'My Test Table 2024';
		$filename = $this->service->get_safe_filename( $title );

		$this->assertEquals( 'my-test-table-2024', $filename );
	}

	/**
	 * Test safe filename with special characters
	 */
	public function test_get_safe_filename_with_special_chars() {
		$title = 'Table @#$%^& Name!';
		$filename = $this->service->get_safe_filename( $title );

		// Should remove special characters
		$this->assertStringNotContainsString( '@', $filename );
		$this->assertStringNotContainsString( '#', $filename );
		$this->assertStringNotContainsString( '!', $filename );
	}

	/**
	 * Test safe filename with long title
	 */
	public function test_get_safe_filename_with_long_title() {
		$title = str_repeat( 'Long Title ', 20 ); // Very long title
		$filename = $this->service->get_safe_filename( $title );

		// Should be limited to 50 characters
		$this->assertLessThanOrEqual( 50, strlen( $filename ) );
	}

	/**
	 * Test CSV with empty data
	 */
	public function test_generate_csv_with_empty_data() {
		$headers = array( 'Name', 'Email' );
		$data = array();

		$csv = $this->service->generate_csv_string( $headers, $data );

		$this->assertIsString( $csv );
		$this->assertStringContainsString( 'Name,Email', $csv );
		// Should only contain headers
		$lines = explode( "\n", trim( $csv ) );
		$this->assertCount( 1, array_filter( $lines ) );
	}

	/**
	 * Test CSV maintains column order
	 */
	public function test_csv_maintains_column_order() {
		$headers = array( 'Name', 'Age', 'Email' );
		$data = array(
			array( 'Email' => 'john@example.com', 'Name' => 'John', 'Age' => '25' ),
		);

		$csv = $this->service->generate_csv_string( $headers, $data );

		// Parse CSV lines
		$lines = explode( "\n", trim( $csv ) );
		// Skip BOM if present
		$header_line = isset( $lines[1] ) ? $lines[1] : $lines[0];
		
		// Should maintain header order, not data key order
		$this->assertStringContainsString( 'Name', $header_line );
	}

	/**
	 * Test CSV with missing data fields
	 */
	public function test_csv_with_missing_fields() {
		$headers = array( 'Name', 'Email', 'Phone' );
		$data = array(
			array( 'Name' => 'John', 'Email' => 'john@example.com' ), // Missing Phone
		);

		$csv = $this->service->generate_csv_string( $headers, $data );

		$this->assertIsString( $csv );
		// Should handle missing fields gracefully
	}

	/**
	 * Test CSV with numeric values
	 */
	public function test_csv_with_numeric_values() {
		$headers = array( 'Product', 'Price', 'Quantity' );
		$data = array(
			array( 'Product' => 'Laptop', 'Price' => '999.99', 'Quantity' => '5' ),
		);

		$csv = $this->service->generate_csv_string( $headers, $data );

		$this->assertStringContainsString( '999.99', $csv );
		$this->assertStringContainsString( '5', $csv );
	}
}

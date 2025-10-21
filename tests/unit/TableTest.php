<?php
/**
 * Table Entity Tests
 *
 * Tests for the Table entity class.
 *
 * @package ATablesCharts\Tests\Unit
 * @since 1.0.0
 */

namespace ATablesCharts\Tests\Unit;

use ATablesCharts\Tests\Bootstrap\TestCase;
use ATablesCharts\Tables\Types\Table;

/**
 * TableTest Class
 */
class TableTest extends TestCase {

	/**
	 * Test table creation with valid data
	 */
	public function test_create_table_with_valid_data() {
		$data = $this->get_sample_table_data();
		$table = new Table( $data );

		$this->assertInstanceOf( Table::class, $table );
		$this->assertEquals( 'Test Table', $table->title );
		$this->assertEquals( 'csv', $table->source_type );
		$this->assertEquals( 3, $table->row_count );
		$this->assertEquals( 4, $table->column_count );
		$this->assertEquals( 'active', $table->status );
	}

	/**
	 * Test table validation with valid data
	 */
	public function test_validate_with_valid_data() {
		$data = $this->get_sample_table_data();
		$table = new Table( $data );

		$validation = $table->validate();

		$this->assertTrue( $validation['valid'] );
		$this->assertEmpty( $validation['errors'] );
	}

	/**
	 * Test table validation without title
	 */
	public function test_validate_without_title() {
		$data = $this->get_sample_table_data();
		unset( $data['title'] );
		$table = new Table( $data );

		$validation = $table->validate();

		$this->assertFalse( $validation['valid'] );
		$this->assertNotEmpty( $validation['errors'] );
		$this->assertContains( 'Table title is required.', $validation['errors'] );
	}

	/**
	 * Test table validation with invalid source type
	 */
	public function test_validate_with_invalid_source_type() {
		$data = $this->get_sample_table_data();
		$data['source_type'] = 'invalid_type';
		$table = new Table( $data );

		$validation = $table->validate();

		$this->assertFalse( $validation['valid'] );
		$this->assertNotEmpty( $validation['errors'] );
	}

	/**
	 * Test table validation without headers
	 */
	public function test_validate_without_headers() {
		$data = $this->get_sample_table_data();
		$data['source_data'] = array( 'data' => array() );
		$table = new Table( $data );

		$validation = $table->validate();

		$this->assertFalse( $validation['valid'] );
		$this->assertContains( 'Table must have column headers.', $validation['errors'] );
	}

	/**
	 * Test get headers
	 */
	public function test_get_headers() {
		$data = $this->get_sample_table_data();
		$table = new Table( $data );

		$headers = $table->get_headers();

		$this->assertIsArray( $headers );
		$this->assertEquals( array( 'Name', 'Email', 'Age', 'City' ), $headers );
	}

	/**
	 * Test get data
	 */
	public function test_get_data() {
		$data = $this->get_sample_table_data();
		$table = new Table( $data );

		$table_data = $table->get_data();

		$this->assertIsArray( $table_data );
		$this->assertCount( 3, $table_data );
		$this->assertEquals( 'John Doe', $table_data[0][0] );
	}

	/**
	 * Test to_array method
	 */
	public function test_to_array() {
		$data = $this->get_sample_table_data();
		$table = new Table( $data );

		$array = $table->to_array();

		$this->assertIsArray( $array );
		$this->assertArrayHasKeys(
			array( 'title', 'description', 'source_type', 'row_count', 'column_count', 'status' ),
			$array
		);
	}

	/**
	 * Test to_database method
	 */
	public function test_to_database() {
		$data = $this->get_sample_table_data();
		$table = new Table( $data );

		$db_data = $table->to_database();

		$this->assertIsArray( $db_data );
		$this->assertArrayHasKey( 'data_source_type', $db_data );
		$this->assertArrayHasKey( 'data_source_config', $db_data );
		$this->assertEquals( 'csv', $db_data['data_source_type'] );
	}

	/**
	 * Test is_empty method
	 */
	public function test_is_empty() {
		// Table with data.
		$data = $this->get_sample_table_data();
		$table = new Table( $data );
		$this->assertFalse( $table->is_empty() );

		// Empty table.
		$empty_data = $this->get_sample_table_data();
		$empty_data['row_count'] = 0;
		$empty_data['source_data']['data'] = array();
		$empty_table = new Table( $empty_data );
		$this->assertTrue( $empty_table->is_empty() );
	}

	/**
	 * Test from_import_result factory method
	 */
	public function test_from_import_result() {
		$import_result = (object) array(
			'headers'      => array( 'Name', 'Email' ),
			'data'         => array(
				array( 'John', 'john@example.com' ),
				array( 'Jane', 'jane@example.com' ),
			),
			'row_count'    => 2,
			'column_count' => 2,
		);

		$table = Table::from_import_result( 'Imported Table', $import_result, 'csv' );

		$this->assertInstanceOf( Table::class, $table );
		$this->assertEquals( 'Imported Table', $table->title );
		$this->assertEquals( 'csv', $table->source_type );
		$this->assertEquals( 2, $table->row_count );
		$this->assertEquals( 2, $table->column_count );
	}

	/**
	 * Test table with long title
	 */
	public function test_validate_with_long_title() {
		$data = $this->get_sample_table_data();
		$data['title'] = str_repeat( 'a', 300 ); // 300 characters
		$table = new Table( $data );

		$validation = $table->validate();

		$this->assertFalse( $validation['valid'] );
		$this->assertContains( 'Table title must be less than 255 characters.', $validation['errors'] );
	}

	/**
	 * Test table with invalid status
	 */
	public function test_validate_with_invalid_status() {
		$data = $this->get_sample_table_data();
		$data['status'] = 'invalid_status';
		$table = new Table( $data );

		$validation = $table->validate();

		$this->assertFalse( $validation['valid'] );
		$this->assertContains( 'Invalid status.', $validation['errors'] );
	}

	/**
	 * Test get_size method
	 */
	public function test_get_size() {
		$data = $this->get_sample_table_data();
		$table = new Table( $data );

		$size = $table->get_size();

		$this->assertIsInt( $size );
		$this->assertGreaterThan( 0, $size );
	}
}

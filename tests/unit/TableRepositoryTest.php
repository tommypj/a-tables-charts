<?php
/**
 * TableRepository Tests
 *
 * Tests for the TableRepository class.
 *
 * @package ATablesCharts\Tests\Unit
 * @since 1.0.0
 */

namespace ATablesCharts\Tests\Unit;

use ATablesCharts\Tests\Bootstrap\TestCase;
use ATablesCharts\Tables\Repositories\TableRepository;
use ATablesCharts\Tables\Types\Table;

/**
 * TableRepositoryTest Class
 */
class TableRepositoryTest extends TestCase {

	/**
	 * Repository instance
	 *
	 * @var TableRepository
	 */
	private $repository;

	/**
	 * Set up test environment
	 */
	public function setUp(): void {
		parent::setUp();
		$this->repository = new TableRepository();
	}

	/**
	 * Test creating a table
	 */
	public function test_create_table() {
		$data = $this->get_sample_table_data();
		$table = new Table( $data );

		$table_id = $this->repository->create( $table );

		$this->assertIsInt( $table_id );
		$this->assertGreaterThan( 0, $table_id );
	}

	/**
	 * Test creating table with invalid data
	 */
	public function test_create_table_with_invalid_data() {
		$data = $this->get_sample_table_data();
		unset( $data['title'] ); // Missing required field
		$table = new Table( $data );

		$result = $this->repository->create( $table );

		$this->assertFalse( $result );
	}

	/**
	 * Test finding table by ID
	 */
	public function test_find_by_id() {
		// Create a table first.
		$data = $this->get_sample_table_data();
		$table = new Table( $data );
		$table_id = $this->repository->create( $table );

		// Find it.
		$found_table = $this->repository->find_by_id( $table_id );

		$this->assertInstanceOf( Table::class, $found_table );
		$this->assertEquals( $table_id, $found_table->id );
		$this->assertEquals( 'Test Table', $found_table->title );
	}

	/**
	 * Test finding non-existent table
	 */
	public function test_find_by_id_not_found() {
		$result = $this->repository->find_by_id( 99999 );

		$this->assertNull( $result );
	}

	/**
	 * Test finding all tables
	 */
	public function test_find_all() {
		// Create multiple tables.
		for ( $i = 1; $i <= 3; $i++ ) {
			$data = $this->get_sample_table_data();
			$data['title'] = 'Test Table ' . $i;
			$table = new Table( $data );
			$this->repository->create( $table );
		}

		$tables = $this->repository->find_all();

		$this->assertIsArray( $tables );
		$this->assertCount( 3, $tables );
		$this->assertContainsOnlyInstancesOf( Table::class, $tables );
	}

	/**
	 * Test finding all tables with pagination
	 */
	public function test_find_all_with_pagination() {
		// Create 5 tables.
		for ( $i = 1; $i <= 5; $i++ ) {
			$data = $this->get_sample_table_data();
			$data['title'] = 'Test Table ' . $i;
			$table = new Table( $data );
			$this->repository->create( $table );
		}

		// Get first page (2 per page).
		$tables = $this->repository->find_all( array(
			'per_page' => 2,
			'page'     => 1,
		) );

		$this->assertCount( 2, $tables );
	}

	/**
	 * Test updating a table
	 */
	public function test_update_table() {
		// Create a table.
		$data = $this->get_sample_table_data();
		$table = new Table( $data );
		$table_id = $this->repository->create( $table );

		// Update it.
		$updated_data = $this->get_sample_table_data();
		$updated_data['title'] = 'Updated Table';
		$updated_table = new Table( $updated_data );

		$result = $this->repository->update( $table_id, $updated_table );

		$this->assertTrue( $result !== false );

		// Verify update.
		$found_table = $this->repository->find_by_id( $table_id );
		$this->assertEquals( 'Updated Table', $found_table->title );
	}

	/**
	 * Test deleting a table
	 */
	public function test_delete_table() {
		// Create a table.
		$data = $this->get_sample_table_data();
		$table = new Table( $data );
		$table_id = $this->repository->create( $table );

		// Delete it.
		$result = $this->repository->delete( $table_id );

		$this->assertTrue( $result );

		// Verify deletion.
		$found_table = $this->repository->find_by_id( $table_id );
		$this->assertNull( $found_table );
	}

	/**
	 * Test counting tables
	 */
	public function test_count_tables() {
		// Create tables.
		for ( $i = 1; $i <= 3; $i++ ) {
			$data = $this->get_sample_table_data();
			$table = new Table( $data );
			$this->repository->create( $table );
		}

		$count = $this->repository->count();

		$this->assertEquals( 3, $count );
	}

	/**
	 * Test searching tables
	 */
	public function test_search_tables() {
		// Create tables with different titles.
		$titles = array( 'Customers Table', 'Products Table', 'Orders Table' );
		foreach ( $titles as $title ) {
			$data = $this->get_sample_table_data();
			$data['title'] = $title;
			$table = new Table( $data );
			$this->repository->create( $table );
		}

		// Search for 'Products'.
		$results = $this->repository->search( 'Products' );

		$this->assertCount( 1, $results );
		$this->assertEquals( 'Products Table', $results[0]->title );
	}

	/**
	 * Test getting table data
	 */
	public function test_get_table_data() {
		// Create a table.
		$data = $this->get_sample_table_data();
		$table = new Table( $data );
		$table_id = $this->repository->create( $table );

		// Get table data.
		$table_data = $this->repository->get_table_data( $table_id );

		$this->assertIsArray( $table_data );
		$this->assertCount( 3, $table_data );
	}

	/**
	 * Test getting filtered table data
	 */
	public function test_get_table_data_filtered() {
		// Create a table.
		$data = $this->get_sample_table_data();
		$table = new Table( $data );
		$table_id = $this->repository->create( $table );

		// Get filtered data.
		$result = $this->repository->get_table_data_filtered( $table_id, array(
			'per_page' => 10,
			'page'     => 1,
			'search'   => 'John',
		) );

		$this->assertIsArray( $result );
		$this->assertArrayHasKey( 'data', $result );
		$this->assertArrayHasKey( 'total', $result );
		$this->assertEquals( 1, $result['total'] ); // Only John Doe matches
	}

	/**
	 * Test sorting table data
	 */
	public function test_get_table_data_filtered_with_sort() {
		// Create a table.
		$data = $this->get_sample_table_data();
		$table = new Table( $data );
		$table_id = $this->repository->create( $table );

		// Get sorted data.
		$result = $this->repository->get_table_data_filtered( $table_id, array(
			'per_page'    => 10,
			'page'        => 1,
			'sort_column' => 'Age',
			'sort_order'  => 'desc',
		) );

		$this->assertIsArray( $result['data'] );
		// First row should be Bob Johnson (age 35).
		$this->assertEquals( 'Bob Johnson', $result['data'][0]['Name'] );
	}

	/**
	 * Test pagination of filtered data
	 */
	public function test_get_table_data_filtered_pagination() {
		// Create a table.
		$data = $this->get_sample_table_data();
		$table = new Table( $data );
		$table_id = $this->repository->create( $table );

		// Get first page (1 per page).
		$result = $this->repository->get_table_data_filtered( $table_id, array(
			'per_page' => 1,
			'page'     => 1,
		) );

		$this->assertCount( 1, $result['data'] );
		$this->assertEquals( 3, $result['total'] );
	}
}

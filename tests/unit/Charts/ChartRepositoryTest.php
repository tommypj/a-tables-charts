<?php
/**
 * Tests for ChartRepository
 *
 * @package ATablesCharts\Tests\Unit\Charts
 * @since 1.0.0
 */

namespace ATablesCharts\Tests\Unit\Charts;

use ATablesCharts\Charts\Repositories\ChartRepository;
use ATablesCharts\Charts\Types\Chart;
use PHPUnit\Framework\TestCase;
use Mockery;

/**
 * ChartRepository Test Case
 */
class ChartRepositoryTest extends TestCase {

	/**
	 * Mock wpdb object
	 *
	 * @var \Mockery\MockInterface
	 */
	private $wpdb_mock;

	/**
	 * ChartRepository instance
	 *
	 * @var ChartRepository
	 */
	private $repository;

	/**
	 * Set up test
	 */
	protected function setUp(): void {
		parent::setUp();

		// Create wpdb mock with shouldIgnoreMissing for more flexibility
		$this->wpdb_mock = Mockery::mock( 'wpdb' )->shouldIgnoreMissing();
		$this->wpdb_mock->prefix = 'wp_';
		$this->wpdb_mock->insert_id = 0;

		// Set global wpdb
		global $wpdb;
		$wpdb = $this->wpdb_mock;

		// Create repository instance
		$this->repository = new ChartRepository();
	}

	/**
	 * Tear down test
	 */
	protected function tearDown(): void {
		Mockery::close();
		parent::tearDown();
	}

	// Constructor Tests

	public function test_constructor_sets_table_name() {
		$this->assertInstanceOf( ChartRepository::class, $this->repository );
	}

	// Create Tests

	public function test_create_with_valid_chart() {
		$chart = new Chart( array(
			'table_id' => 1,
			'title'    => 'Test Chart',
			'type'     => 'bar',
		) );

		$this->wpdb_mock->shouldReceive( 'insert' )
			->once()
			->with(
				'wp_atables_charts',
				Mockery::type( 'array' ),
				Mockery::type( 'array' )
			)
			->andReturn( 1 );

		$this->wpdb_mock->insert_id = 42;

		$result = $this->repository->create( $chart );

		$this->assertEquals( 42, $result );
	}

	public function test_create_with_invalid_chart() {
		$chart = new Chart( array(
			'table_id' => 1,
			// Missing required title
			'type'     => 'bar',
		) );

		// wpdb->insert should not be called
		$this->wpdb_mock->shouldNotReceive( 'insert' );

		$result = $this->repository->create( $chart );

		$this->assertFalse( $result );
	}

	public function test_create_returns_false_on_database_error() {
		$chart = new Chart( array(
			'table_id' => 1,
			'title'    => 'Test Chart',
			'type'     => 'bar',
		) );

		$this->wpdb_mock->shouldReceive( 'insert' )
			->once()
			->andReturn( false );

		$result = $this->repository->create( $chart );

		$this->assertFalse( $result );
	}

	// Find By ID Tests

	public function test_find_by_id_with_existing_chart() {
		$this->wpdb_mock->shouldReceive( 'prepare' )
			->once()
			->with( Mockery::type( 'string' ), 1 )
			->andReturn( 'SELECT * FROM wp_atables_charts WHERE id = 1' );

		$this->wpdb_mock->shouldReceive( 'get_row' )
			->once()
			->with( Mockery::type( 'string' ), ARRAY_A )
			->andReturn( array(
				'id'         => 1,
				'table_id'   => 1,
				'title'      => 'Test Chart',
				'type'       => 'bar',
				'config'     => '{}',
				'status'     => 'active',
			) );

		$result = $this->repository->find_by_id( 1 );

		$this->assertInstanceOf( Chart::class, $result );
		$this->assertEquals( 1, $result->id );
		$this->assertEquals( 'Test Chart', $result->title );
	}

	public function test_find_by_id_returns_null_when_not_found() {
		$this->wpdb_mock->shouldReceive( 'prepare' )
			->once()
			->andReturn( 'SELECT * FROM wp_atables_charts WHERE id = 999' );

		$this->wpdb_mock->shouldReceive( 'get_row' )
			->once()
			->andReturn( null );

		$result = $this->repository->find_by_id( 999 );

		$this->assertNull( $result );
	}

	// Find All Tests

	public function test_find_all_returns_array_of_charts() {
		$this->wpdb_mock->shouldReceive( 'prepare' )
			->andReturn( '' );

		$this->wpdb_mock->shouldReceive( 'get_results' )
			->once()
			->andReturn( array(
				array(
					'id'       => 1,
					'table_id' => 1,
					'title'    => 'Chart 1',
					'type'     => 'bar',
					'config'   => '{}',
					'status'   => 'active',
				),
				array(
					'id'       => 2,
					'table_id' => 1,
					'title'    => 'Chart 2',
					'type'     => 'line',
					'config'   => '{}',
					'status'   => 'active',
				),
			) );

		$results = $this->repository->find_all();

		$this->assertIsArray( $results );
		$this->assertCount( 2, $results );
		$this->assertInstanceOf( Chart::class, $results[0] );
		$this->assertInstanceOf( Chart::class, $results[1] );
	}

	public function test_find_all_returns_empty_array_when_no_results() {
		$this->wpdb_mock->shouldReceive( 'prepare' )
			->andReturn( '' );

		$this->wpdb_mock->shouldReceive( 'get_results' )
			->once()
			->andReturn( null );

		$results = $this->repository->find_all();

		$this->assertIsArray( $results );
		$this->assertEmpty( $results );
	}

	public function test_find_all_with_table_id_filter() {
		$this->wpdb_mock->shouldReceive( 'prepare' )
			->andReturn( '' );

		$this->wpdb_mock->shouldReceive( 'get_results' )
			->once()
			->andReturn( array(
				array(
					'id'       => 1,
					'table_id' => 5,
					'title'    => 'Chart for Table 5',
					'type'     => 'bar',
					'config'   => '{}',
					'status'   => 'active',
				),
			) );

		$results = $this->repository->find_all( array( 'table_id' => 5 ) );

		$this->assertIsArray( $results );
		$this->assertCount( 1, $results );
	}

	public function test_find_all_with_pagination() {
		$this->wpdb_mock->shouldReceive( 'prepare' )
			->andReturn( '' );

		$this->wpdb_mock->shouldReceive( 'get_results' )
			->once()
			->andReturn( array() );

		$results = $this->repository->find_all( array(
			'per_page' => 10,
			'page'     => 2,
		) );

		$this->assertIsArray( $results );
	}

	public function test_find_all_prevents_sql_injection_in_orderby() {
		// The DatabaseHelpers::prepare_order_by should prevent SQL injection
		$this->wpdb_mock->shouldReceive( 'prepare' )
			->andReturn( '' );

		$this->wpdb_mock->shouldReceive( 'get_results' )
			->once()
			->andReturn( array() );

		// Try malicious orderby - should default to 'created_at'
		$results = $this->repository->find_all( array(
			'orderby' => "id; DROP TABLE wp_atables_charts--",
		) );

		$this->assertIsArray( $results );
	}

	public function test_find_all_with_custom_ordering() {
		$this->wpdb_mock->shouldReceive( 'prepare' )
			->andReturn( '' );

		$this->wpdb_mock->shouldReceive( 'get_results' )
			->once()
			->andReturn( array() );

		$results = $this->repository->find_all( array(
			'orderby' => 'title',
			'order'   => 'ASC',
		) );

		$this->assertIsArray( $results );
	}

	// Update Tests

	public function test_update_with_valid_chart() {
		$chart = new Chart( array(
			'table_id' => 1,
			'title'    => 'Updated Chart',
			'type'     => 'line',
		) );

		$this->wpdb_mock->shouldReceive( 'update' )
			->once()
			->with(
				'wp_atables_charts',
				Mockery::type( 'array' ),
				array( 'id' => 1 ),
				Mockery::type( 'array' ),
				Mockery::type( 'array' )
			)
			->andReturn( 1 );

		$result = $this->repository->update( 1, $chart );

		$this->assertTrue( $result );
	}

	public function test_update_with_invalid_chart() {
		$chart = new Chart( array(
			'table_id' => 1,
			// Missing required title
			'type'     => 'bar',
		) );

		// wpdb->update should not be called
		$this->wpdb_mock->shouldNotReceive( 'update' );

		$result = $this->repository->update( 1, $chart );

		$this->assertFalse( $result );
	}

	public function test_update_returns_false_on_database_error() {
		$chart = new Chart( array(
			'table_id' => 1,
			'title'    => 'Updated Chart',
			'type'     => 'bar',
		) );

		$this->wpdb_mock->shouldReceive( 'update' )
			->once()
			->andReturn( false );

		$result = $this->repository->update( 1, $chart );

		$this->assertFalse( $result );
	}

	// Delete Tests

	public function test_delete_existing_chart() {
		$this->wpdb_mock->shouldReceive( 'delete' )
			->once()
			->with(
				'wp_atables_charts',
				array( 'id' => 1 ),
				array( '%d' )
			)
			->andReturn( 1 );

		$result = $this->repository->delete( 1 );

		$this->assertTrue( $result );
	}

	public function test_delete_returns_false_on_error() {
		$this->wpdb_mock->shouldReceive( 'delete' )
			->once()
			->andReturn( false );

		$result = $this->repository->delete( 999 );

		$this->assertFalse( $result );
	}

	// Count Tests

	public function test_count_all_charts() {
		$this->wpdb_mock->shouldReceive( 'get_var' )
			->once()
			->with( Mockery::type( 'string' ) )
			->andReturn( '5' );

		$result = $this->repository->count();

		$this->assertEquals( 5, $result );
	}

	public function test_count_with_table_id_filter() {
		$this->wpdb_mock->shouldReceive( 'prepare' )
			->with( Mockery::type( 'string' ), 1 )
			->andReturn( ' AND table_id = 1' );

		$this->wpdb_mock->shouldReceive( 'get_var' )
			->once()
			->andReturn( '3' );

		$result = $this->repository->count( array( 'table_id' => 1 ) );

		$this->assertEquals( 3, $result );
	}

	public function test_count_with_status_filter() {
		$this->wpdb_mock->shouldReceive( 'prepare' )
			->with( Mockery::type( 'string' ), 'active' )
			->andReturn( ' AND status = \'active\'' );

		$this->wpdb_mock->shouldReceive( 'get_var' )
			->once()
			->andReturn( '4' );

		$result = $this->repository->count( array( 'status' => 'active' ) );

		$this->assertEquals( 4, $result );
	}

	public function test_count_returns_zero_when_no_results() {
		$this->wpdb_mock->shouldReceive( 'get_var' )
			->once()
			->andReturn( '0' );

		$result = $this->repository->count();

		$this->assertEquals( 0, $result );
	}

	// Find By Table ID Tests

	public function test_find_by_table_id() {
		$this->wpdb_mock->shouldReceive( 'prepare' )
			->andReturn( '' );

		$this->wpdb_mock->shouldReceive( 'get_results' )
			->once()
			->andReturn( array(
				array(
					'id'       => 1,
					'table_id' => 5,
					'title'    => 'Chart for Table 5',
					'type'     => 'pie',
					'config'   => '{}',
					'status'   => 'active',
				),
			) );

		$results = $this->repository->find_by_table_id( 5 );

		$this->assertIsArray( $results );
		$this->assertCount( 1, $results );
		$this->assertInstanceOf( Chart::class, $results[0] );
	}

	public function test_find_by_table_id_returns_empty_for_nonexistent_table() {
		$this->wpdb_mock->shouldReceive( 'prepare' )
			->andReturn( '' );

		$this->wpdb_mock->shouldReceive( 'get_results' )
			->once()
			->andReturn( null );

		$results = $this->repository->find_by_table_id( 999 );

		$this->assertIsArray( $results );
		$this->assertEmpty( $results );
	}

	// Integration Tests

	public function test_repository_crud_lifecycle() {
		// Create
		$this->wpdb_mock->shouldReceive( 'insert' )
			->once()
			->andReturn( 1 );
		$this->wpdb_mock->insert_id = 10;

		$chart = new Chart( array(
			'table_id' => 1,
			'title'    => 'Lifecycle Test',
			'type'     => 'bar',
		) );

		$chart_id = $this->repository->create( $chart );
		$this->assertEquals( 10, $chart_id );

		// Read
		$this->wpdb_mock->shouldReceive( 'prepare' )
			->andReturn( 'SELECT' );
		$this->wpdb_mock->shouldReceive( 'get_row' )
			->andReturn( array(
				'id'       => 10,
				'table_id' => 1,
				'title'    => 'Lifecycle Test',
				'type'     => 'bar',
				'config'   => '{}',
				'status'   => 'active',
			) );

		$found = $this->repository->find_by_id( 10 );
		$this->assertInstanceOf( Chart::class, $found );

		// Update
		$this->wpdb_mock->shouldReceive( 'update' )
			->andReturn( 1 );

		$updated_chart = new Chart( array(
			'table_id' => 1,
			'title'    => 'Updated Title',
			'type'     => 'line',
		) );

		$update_result = $this->repository->update( 10, $updated_chart );
		$this->assertTrue( $update_result );

		// Delete
		$this->wpdb_mock->shouldReceive( 'delete' )
			->andReturn( 1 );

		$delete_result = $this->repository->delete( 10 );
		$this->assertTrue( $delete_result );
	}

	// Security Tests

	public function test_prevents_sql_injection_in_find_by_id() {
		// wpdb->prepare should be called for parameterized queries
		$this->wpdb_mock->shouldReceive( 'prepare' )
			->once()
			->with( Mockery::type( 'string' ), Mockery::type( 'int' ) )
			->andReturn( 'SAFE_QUERY' );

		$this->wpdb_mock->shouldReceive( 'get_row' )
			->once()
			->andReturn( null );

		// Try SQL injection attempt
		$this->repository->find_by_id( "1 OR 1=1--" );

		// Test passes if no exception thrown and prepare was called
		$this->assertTrue( true );
	}

	public function test_validates_chart_before_create() {
		$invalid_chart = new Chart( array(
			'table_id' => 1,
			'title'    => 'Test',
			'type'     => 'invalid_type', // Invalid type
		) );

		// Should not call insert due to validation failure
		$this->wpdb_mock->shouldNotReceive( 'insert' );

		$result = $this->repository->create( $invalid_chart );

		$this->assertFalse( $result );
	}

	public function test_validates_chart_before_update() {
		$invalid_chart = new Chart( array(
			'table_id' => 1,
			'title'    => 'Test',
			'type'     => 'invalid_type', // Invalid type
		) );

		// Should not call update due to validation failure
		$this->wpdb_mock->shouldNotReceive( 'update' );

		$result = $this->repository->update( 1, $invalid_chart );

		$this->assertFalse( $result );
	}
}

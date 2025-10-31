<?php
/**
 * Chart Entity Tests
 *
 * Comprehensive tests for the Chart entity class.
 * Critical for validating all 8 chart types.
 *
 * @package ATablesCharts\Tests\Unit\Charts
 * @since 1.0.0
 */

namespace ATablesCharts\Tests\Unit\Charts;

use ATablesCharts\Tests\Bootstrap\TestCase;
use ATablesCharts\Charts\Types\Chart;

/**
 * ChartTest Class
 *
 * Tests all Chart functionality including 8 chart types.
 */
class ChartTest extends TestCase {

	// Constructor Tests

	public function test_constructor_with_full_data() {
		$data = array(
			'id'         => 1,
			'table_id'   => 5,
			'title'      => 'Test Chart',
			'type'       => 'bar',
			'config'     => array( 'width' => 800, 'height' => 600 ),
			'status'     => 'active',
			'created_by' => 1,
			'created_at' => '2024-01-15 10:00:00',
			'updated_at' => '2024-01-15 10:00:00',
		);
		
		$chart = new Chart( $data );
		
		$this->assertEquals( 1, $chart->id );
		$this->assertEquals( 5, $chart->table_id );
		$this->assertEquals( 'Test Chart', $chart->title );
		$this->assertEquals( 'bar', $chart->type );
		$this->assertEquals( array( 'width' => 800, 'height' => 600 ), $chart->config );
		$this->assertEquals( 'active', $chart->status );
		$this->assertEquals( 1, $chart->created_by );
	}

	public function test_constructor_with_empty_data() {
		$chart = new Chart( array() );
		
		$this->assertEquals( 0, $chart->id );
		$this->assertEquals( 0, $chart->table_id );
		$this->assertEquals( '', $chart->title );
		$this->assertEquals( 'bar', $chart->type ); // Default
		$this->assertEquals( array(), $chart->config );
		$this->assertEquals( 'active', $chart->status ); // Default
	}

	public function test_constructor_with_partial_data() {
		$data = array(
			'table_id' => 10,
			'title'    => 'Partial Chart',
			'type'     => 'line',
		);
		
		$chart = new Chart( $data );
		
		$this->assertEquals( 10, $chart->table_id );
		$this->assertEquals( 'Partial Chart', $chart->title );
		$this->assertEquals( 'line', $chart->type );
		$this->assertEquals( 0, $chart->id ); // Default
		$this->assertEquals( 'active', $chart->status ); // Default
	}

	public function test_constructor_with_json_string_config() {
		$data = array(
			'table_id' => 5,
			'title'    => 'Chart with JSON',
			'config'   => '{"width":800,"height":600}',
		);
		
		$chart = new Chart( $data );
		
		$this->assertIsArray( $chart->config );
		$this->assertEquals( 800, $chart->config['width'] );
		$this->assertEquals( 600, $chart->config['height'] );
	}

	public function test_constructor_with_invalid_json_config() {
		$data = array(
			'table_id' => 5,
			'title'    => 'Chart with bad JSON',
			'config'   => '{invalid json}',
		);
		
		$chart = new Chart( $data );
		
		// Should fall back to empty array
		$this->assertIsArray( $chart->config );
		$this->assertEmpty( $chart->config );
	}

	// All 8 Chart Types Tests

	public function test_bar_chart_type() {
		$chart = new Chart( array(
			'table_id' => 1,
			'title'    => 'Bar Chart',
			'type'     => 'bar',
		) );
		
		$this->assertEquals( 'bar', $chart->type );
		
		$validation = $chart->validate();
		$this->assertTrue( $validation['valid'] );
	}

	public function test_line_chart_type() {
		$chart = new Chart( array(
			'table_id' => 1,
			'title'    => 'Line Chart',
			'type'     => 'line',
		) );
		
		$this->assertEquals( 'line', $chart->type );
		
		$validation = $chart->validate();
		$this->assertTrue( $validation['valid'] );
	}

	public function test_pie_chart_type() {
		$chart = new Chart( array(
			'table_id' => 1,
			'title'    => 'Pie Chart',
			'type'     => 'pie',
		) );
		
		$this->assertEquals( 'pie', $chart->type );
		
		$validation = $chart->validate();
		$this->assertTrue( $validation['valid'] );
	}

	public function test_doughnut_chart_type() {
		$chart = new Chart( array(
			'table_id' => 1,
			'title'    => 'Doughnut Chart',
			'type'     => 'doughnut',
		) );
		
		$this->assertEquals( 'doughnut', $chart->type );
		
		$validation = $chart->validate();
		$this->assertTrue( $validation['valid'] );
	}

	public function test_column_chart_type() {
		$chart = new Chart( array(
			'table_id' => 1,
			'title'    => 'Column Chart',
			'type'     => 'column',
		) );
		
		$this->assertEquals( 'column', $chart->type );
		
		$validation = $chart->validate();
		$this->assertTrue( $validation['valid'] );
	}

	public function test_area_chart_type() {
		$chart = new Chart( array(
			'table_id' => 1,
			'title'    => 'Area Chart',
			'type'     => 'area',
		) );
		
		$this->assertEquals( 'area', $chart->type );
		
		$validation = $chart->validate();
		$this->assertTrue( $validation['valid'] );
	}

	public function test_scatter_chart_type() {
		$chart = new Chart( array(
			'table_id' => 1,
			'title'    => 'Scatter Chart',
			'type'     => 'scatter',
		) );
		
		$this->assertEquals( 'scatter', $chart->type );
		
		$validation = $chart->validate();
		$this->assertTrue( $validation['valid'] );
	}

	public function test_radar_chart_type() {
		$chart = new Chart( array(
			'table_id' => 1,
			'title'    => 'Radar Chart',
			'type'     => 'radar',
		) );
		
		$this->assertEquals( 'radar', $chart->type );
		
		$validation = $chart->validate();
		$this->assertTrue( $validation['valid'] );
	}

	public function test_all_eight_chart_types_are_valid() {
		$types = array( 'bar', 'line', 'pie', 'doughnut', 'column', 'area', 'scatter', 'radar' );
		
		foreach ( $types as $type ) {
			$chart = new Chart( array(
				'table_id' => 1,
				'title'    => "$type Chart",
				'type'     => $type,
			) );
			
			$validation = $chart->validate();
			$this->assertTrue( $validation['valid'], "Chart type '$type' should be valid" );
			$this->assertEmpty( $validation['errors'], "Chart type '$type' should have no errors" );
		}
	}

	// Validation Tests

	public function test_validate_with_valid_data() {
		$chart = new Chart( array(
			'table_id' => 5,
			'title'    => 'Valid Chart',
			'type'     => 'bar',
		) );
		
		$result = $chart->validate();
		
		$this->assertTrue( $result['valid'] );
		$this->assertEmpty( $result['errors'] );
	}

	public function test_validate_without_table_id() {
		$chart = new Chart( array(
			'title' => 'Chart without table',
			'type'  => 'bar',
		) );
		
		$result = $chart->validate();
		
		$this->assertFalse( $result['valid'] );
		$this->assertNotEmpty( $result['errors'] );
		$this->assertStringContainsString( 'Table ID', $result['errors'][0] );
	}

	public function test_validate_without_title() {
		$chart = new Chart( array(
			'table_id' => 5,
			'type'     => 'bar',
		) );
		
		$result = $chart->validate();
		
		$this->assertFalse( $result['valid'] );
		$this->assertNotEmpty( $result['errors'] );
		$this->assertStringContainsString( 'title', $result['errors'][0] );
	}

	public function test_validate_with_empty_title() {
		$chart = new Chart( array(
			'table_id' => 5,
			'title'    => '',
			'type'     => 'bar',
		) );
		
		$result = $chart->validate();
		
		$this->assertFalse( $result['valid'] );
	}

	public function test_validate_with_invalid_type() {
		$chart = new Chart( array(
			'table_id' => 5,
			'title'    => 'Chart with bad type',
			'type'     => 'invalid_type',
		) );
		
		$result = $chart->validate();
		
		$this->assertFalse( $result['valid'] );
		$this->assertNotEmpty( $result['errors'] );
		$this->assertStringContainsString( 'Invalid chart type', $result['errors'][0] );
	}

	public function test_validate_with_multiple_errors() {
		$chart = new Chart( array(
			'type' => 'invalid_type',
		) );
		
		$result = $chart->validate();
		
		$this->assertFalse( $result['valid'] );
		$this->assertCount( 3, $result['errors'] ); // table_id, title, type
	}

	public function test_validate_rejects_old_unsupported_types() {
		// Ensure types that weren't in original allowed list are properly rejected
		$invalid_types = array( 'bubble', 'funnel', 'gauge', 'heatmap', 'treemap' );
		
		foreach ( $invalid_types as $type ) {
			$chart = new Chart( array(
				'table_id' => 1,
				'title'    => 'Test',
				'type'     => $type,
			) );
			
			$result = $chart->validate();
			$this->assertFalse( $result['valid'], "Type '$type' should be invalid" );
		}
	}

	// to_database() Tests

	public function test_to_database_returns_array() {
		$chart = new Chart( array(
			'table_id' => 5,
			'title'    => 'Test Chart',
			'type'     => 'bar',
		) );
		
		$result = $chart->to_database();
		
		$this->assertIsArray( $result );
	}

	public function test_to_database_includes_required_fields() {
		$chart = new Chart( array(
			'table_id' => 5,
			'title'    => 'Test Chart',
			'type'     => 'bar',
		) );
		
		$result = $chart->to_database();
		
		$this->assertArrayHasKey( 'table_id', $result );
		$this->assertArrayHasKey( 'title', $result );
		$this->assertArrayHasKey( 'type', $result );
		$this->assertArrayHasKey( 'config', $result );
		$this->assertArrayHasKey( 'status', $result );
		$this->assertArrayHasKey( 'created_by', $result );
	}

	public function test_to_database_encodes_config_as_json() {
		$config = array( 'width' => 800, 'height' => 600 );
		$chart = new Chart( array(
			'table_id' => 5,
			'title'    => 'Test Chart',
			'config'   => $config,
		) );
		
		$result = $chart->to_database();
		
		$this->assertIsString( $result['config'] );
		$this->assertStringContainsString( 'width', $result['config'] );
		$this->assertStringContainsString( '800', $result['config'] );
	}

	public function test_to_database_does_not_include_id() {
		$chart = new Chart( array(
			'id'       => 123,
			'table_id' => 5,
			'title'    => 'Test Chart',
		) );
		
		$result = $chart->to_database();
		
		$this->assertArrayNotHasKey( 'id', $result );
	}

	public function test_to_database_does_not_include_timestamps() {
		$chart = new Chart( array(
			'table_id'   => 5,
			'title'      => 'Test Chart',
			'created_at' => '2024-01-01 00:00:00',
			'updated_at' => '2024-01-01 00:00:00',
		) );
		
		$result = $chart->to_database();
		
		$this->assertArrayNotHasKey( 'created_at', $result );
		$this->assertArrayNotHasKey( 'updated_at', $result );
	}

	// to_array() Tests

	public function test_to_array_returns_array() {
		$chart = new Chart( array(
			'table_id' => 5,
			'title'    => 'Test Chart',
		) );
		
		$result = $chart->to_array();
		
		$this->assertIsArray( $result );
	}

	public function test_to_array_includes_all_fields() {
		$chart = new Chart( array(
			'id'       => 1,
			'table_id' => 5,
			'title'    => 'Test Chart',
			'type'     => 'bar',
			'status'   => 'active',
		) );
		
		$result = $chart->to_array();
		
		$this->assertArrayHasKey( 'id', $result );
		$this->assertArrayHasKey( 'table_id', $result );
		$this->assertArrayHasKey( 'title', $result );
		$this->assertArrayHasKey( 'type', $result );
		$this->assertArrayHasKey( 'config', $result );
		$this->assertArrayHasKey( 'status', $result );
		$this->assertArrayHasKey( 'created_by', $result );
		$this->assertArrayHasKey( 'created_at', $result );
		$this->assertArrayHasKey( 'updated_at', $result );
	}

	public function test_to_array_keeps_config_as_array() {
		$config = array( 'width' => 800, 'height' => 600 );
		$chart = new Chart( array(
			'table_id' => 5,
			'title'    => 'Test Chart',
			'config'   => $config,
		) );
		
		$result = $chart->to_array();
		
		$this->assertIsArray( $result['config'] );
		$this->assertEquals( $config, $result['config'] );
	}

	public function test_to_array_preserves_all_values() {
		$data = array(
			'id'         => 10,
			'table_id'   => 5,
			'title'      => 'My Chart',
			'type'       => 'line',
			'config'     => array( 'color' => 'blue' ),
			'status'     => 'inactive',
			'created_by' => 2,
			'created_at' => '2024-01-01 00:00:00',
			'updated_at' => '2024-01-02 00:00:00',
		);
		
		$chart = new Chart( $data );
		$result = $chart->to_array();
		
		$this->assertEquals( 10, $result['id'] );
		$this->assertEquals( 5, $result['table_id'] );
		$this->assertEquals( 'My Chart', $result['title'] );
		$this->assertEquals( 'line', $result['type'] );
		$this->assertEquals( array( 'color' => 'blue' ), $result['config'] );
		$this->assertEquals( 'inactive', $result['status'] );
		$this->assertEquals( 2, $result['created_by'] );
	}

	// Default Values Tests

	public function test_default_type_is_bar() {
		$chart = new Chart( array() );
		$this->assertEquals( 'bar', $chart->type );
	}

	public function test_default_status_is_active() {
		$chart = new Chart( array() );
		$this->assertEquals( 'active', $chart->status );
	}

	public function test_default_config_is_empty_array() {
		$chart = new Chart( array() );
		$this->assertEquals( array(), $chart->config );
	}

	public function test_default_id_is_zero() {
		$chart = new Chart( array() );
		$this->assertEquals( 0, $chart->id );
	}

	public function test_default_table_id_is_zero() {
		$chart = new Chart( array() );
		$this->assertEquals( 0, $chart->table_id );
	}

	// Config Handling Tests

	public function test_config_as_array() {
		$config = array(
			'width'  => 800,
			'height' => 600,
			'colors' => array( '#FF0000', '#00FF00' ),
		);
		
		$chart = new Chart( array(
			'table_id' => 1,
			'title'    => 'Test',
			'config'   => $config,
		) );
		
		$this->assertEquals( $config, $chart->config );
	}

	public function test_config_as_json_string() {
		$json = '{"width":800,"height":600}';
		
		$chart = new Chart( array(
			'table_id' => 1,
			'title'    => 'Test',
			'config'   => $json,
		) );
		
		$this->assertIsArray( $chart->config );
		$this->assertEquals( 800, $chart->config['width'] );
	}

	public function test_config_with_nested_arrays() {
		$config = array(
			'series' => array(
				array( 'name' => 'Series 1', 'data' => array( 1, 2, 3 ) ),
				array( 'name' => 'Series 2', 'data' => array( 4, 5, 6 ) ),
			),
		);
		
		$chart = new Chart( array(
			'table_id' => 1,
			'title'    => 'Test',
			'config'   => $config,
		) );
		
		$this->assertEquals( $config, $chart->config );
	}

	public function test_empty_config_string_becomes_empty_array() {
		$chart = new Chart( array(
			'table_id' => 1,
			'title'    => 'Test',
			'config'   => '',
		) );
		
		$this->assertEquals( array(), $chart->config );
	}

	// Status Tests

	public function test_active_status() {
		$chart = new Chart( array(
			'status' => 'active',
		) );
		
		$this->assertEquals( 'active', $chart->status );
	}

	public function test_inactive_status() {
		$chart = new Chart( array(
			'status' => 'inactive',
		) );
		
		$this->assertEquals( 'inactive', $chart->status );
	}

	// Edge Cases Tests

	public function test_chart_with_very_long_title() {
		$long_title = str_repeat( 'A', 500 );
		$chart = new Chart( array(
			'table_id' => 1,
			'title'    => $long_title,
		) );
		
		$this->assertEquals( $long_title, $chart->title );
	}

	public function test_chart_with_special_characters_in_title() {
		$title = 'Test <>&"\'\\';
		$chart = new Chart( array(
			'table_id' => 1,
			'title'    => $title,
		) );
		
		$this->assertEquals( $title, $chart->title );
	}

	public function test_chart_with_unicode_in_title() {
		$title = 'Chart 你好 😀';
		$chart = new Chart( array(
			'table_id' => 1,
			'title'    => $title,
		) );
		
		$this->assertEquals( $title, $chart->title );
	}

	public function test_chart_with_large_config() {
		$large_config = array();
		for ( $i = 0; $i < 100; $i++ ) {
			$large_config[ "option_$i" ] = "value_$i";
		}
		
		$chart = new Chart( array(
			'table_id' => 1,
			'title'    => 'Test',
			'config'   => $large_config,
		) );
		
		$this->assertCount( 100, $chart->config );
	}

	public function test_chart_type_is_case_sensitive() {
		$chart = new Chart( array(
			'table_id' => 1,
			'title'    => 'Test',
			'type'     => 'BAR', // Uppercase
		) );
		
		$result = $chart->validate();
		
		// Should be invalid because types are lowercase
		$this->assertFalse( $result['valid'] );
	}

	// Security Tests

	public function test_chart_type_validation_prevents_injection() {
		$malicious_types = array(
			"bar'; DROP TABLE charts--",
			"line' OR '1'='1",
			"<script>alert(1)</script>",
			"../../../etc/passwd",
		);
		
		foreach ( $malicious_types as $type ) {
			$chart = new Chart( array(
				'table_id' => 1,
				'title'    => 'Test',
				'type'     => $type,
			) );
			
			$result = $chart->validate();
			$this->assertFalse( $result['valid'], "Should reject malicious type: $type" );
		}
	}

	public function test_config_handles_malicious_json() {
		$malicious = '{"<script>":"alert(1)","xss":"<img src=x onerror=alert(1)>"}';
		
		$chart = new Chart( array(
			'table_id' => 1,
			'title'    => 'Test',
			'config'   => $malicious,
		) );
		
		// Should decode without crashing
		$this->assertIsArray( $chart->config );
	}

	// Integration Tests

	public function test_full_chart_lifecycle() {
		// Create chart
		$chart = new Chart( array(
			'table_id' => 5,
			'title'    => 'Sales Chart',
			'type'     => 'column',
			'config'   => array( 'width' => 800 ),
		) );
		
		// Validate
		$validation = $chart->validate();
		$this->assertTrue( $validation['valid'] );
		
		// Convert to database format
		$db_data = $chart->to_database();
		$this->assertIsString( $db_data['config'] );
		
		// Convert to array
		$array_data = $chart->to_array();
		$this->assertIsArray( $array_data['config'] );
	}

	public function test_chart_roundtrip_database() {
		$original = new Chart( array(
			'table_id' => 5,
			'title'    => 'Test Chart',
			'type'     => 'scatter',
			'config'   => array( 'width' => 800, 'height' => 600 ),
		) );
		
		// to_database() encodes config as JSON
		$db_data = $original->to_database();
		$this->assertIsString( $db_data['config'] );
		
		// Create new chart from database data
		$db_data['config'] = $db_data['config']; // Keep as JSON string
		$restored = new Chart( $db_data );
		
		// Config should be decoded back to array
		$this->assertIsArray( $restored->config );
		$this->assertEquals( 800, $restored->config['width'] );
		$this->assertEquals( 600, $restored->config['height'] );
	}
}

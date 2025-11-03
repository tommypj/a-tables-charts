<?php
/**
 * A-Tables & Charts - REST API Data Source Example
 *
 * This plugin demonstrates how to use A-Tables & Charts developer hooks
 * to integrate custom data sources into the import system.
 *
 * Example Use Case:
 * - Import product data from a REST API
 * - Transform API response to table format
 * - Add custom data validation
 * - Track import events with custom logging
 *
 * Installation:
 * 1. Copy this file to your wp-content/plugins/ directory
 * 2. Rename it to: atables-rest-api-example.php
 * 3. Activate it from WordPress admin plugins page
 * 4. Configure your API settings in WordPress admin
 *
 * @package ATablesCharts\Examples
 * @version 1.0.0
 */

/*
Plugin Name: A-Tables REST API Data Source Example
Plugin URI: https://example.com
Description: Example plugin demonstrating A-Tables & Charts developer hooks for custom data sources
Version: 1.0.0
Author: Your Name
License: GPL v2 or later
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Example 1: Register Custom Parser for REST API Data
 *
 * This allows users to import data directly from REST APIs
 */
add_filter( 'atables_register_parsers', 'atables_example_register_rest_api_parser' );

function atables_example_register_rest_api_parser( $parsers ) {
	// Add a custom parser for REST API endpoints
	$parsers['rest_api'] = new ATablesExample_RestApiParser();

	return $parsers;
}

/**
 * Example REST API Parser Class
 *
 * Handles fetching and parsing data from REST APIs
 */
class ATablesExample_RestApiParser {

	/**
	 * Get supported extensions/types
	 *
	 * @return array
	 */
	public function get_supported_extensions() {
		// Return custom identifier for this parser
		return array( 'api', 'rest', 'json_api' );
	}

	/**
	 * Parse REST API response
	 *
	 * @param string $content API endpoint URL or JSON response
	 * @param array  $options Parser options
	 * @return object ImportResult-like object
	 */
	public function parse_string( $content, $options = array() ) {
		$result = new stdClass();

		try {
			// If content looks like a URL, fetch it
			if ( filter_var( $content, FILTER_VALIDATE_URL ) ) {
				$response = wp_remote_get( $content, array(
					'timeout' => 30,
					'headers' => array(
						'Accept'        => 'application/json',
						'Authorization' => isset( $options['api_key'] ) ? 'Bearer ' . $options['api_key'] : '',
					),
				) );

				if ( is_wp_error( $response ) ) {
					throw new Exception( $response->get_error_message() );
				}

				$content = wp_remote_retrieve_body( $response );
			}

			// Parse JSON response
			$data = json_decode( $content, true );

			if ( json_last_error() !== JSON_ERROR_NONE ) {
				throw new Exception( 'Invalid JSON response: ' . json_last_error_msg() );
			}

			// Extract data array (support common API formats)
			$items = $this->extract_items( $data, $options );

			if ( empty( $items ) || ! is_array( $items ) ) {
				throw new Exception( 'No valid data found in API response' );
			}

			// Extract headers from first item
			$headers = array_keys( $items[0] );

			// Convert associative arrays to indexed arrays
			$rows = array();
			foreach ( $items as $item ) {
				$row = array();
				foreach ( $headers as $header ) {
					$row[] = isset( $item[ $header ] ) ? $item[ $header ] : '';
				}
				$rows[] = $row;
			}

			// Build result object
			$result->success      = true;
			$result->data         = $rows;
			$result->headers      = $headers;
			$result->row_count    = count( $rows );
			$result->column_count = count( $headers );
			$result->message      = sprintf( 'Successfully imported %d rows from REST API', count( $rows ) );

		} catch ( Exception $e ) {
			$result->success = false;
			$result->message = $e->getMessage();
			$result->data    = array();
			$result->headers = array();
		}

		return $result;
	}

	/**
	 * Extract items array from various API response formats
	 *
	 * @param array $data    API response data
	 * @param array $options Parser options
	 * @return array Items array
	 */
	private function extract_items( $data, $options ) {
		// Check if custom data path is specified
		if ( isset( $options['data_path'] ) && ! empty( $options['data_path'] ) ) {
			$path_parts = explode( '.', $options['data_path'] );
			foreach ( $path_parts as $part ) {
				if ( isset( $data[ $part ] ) ) {
					$data = $data[ $part ];
				} else {
					return array();
				}
			}
			return $data;
		}

		// Auto-detect common formats

		// Format 1: { "data": [...] }
		if ( isset( $data['data'] ) && is_array( $data['data'] ) ) {
			return $data['data'];
		}

		// Format 2: { "items": [...] }
		if ( isset( $data['items'] ) && is_array( $data['items'] ) ) {
			return $data['items'];
		}

		// Format 3: { "results": [...] }
		if ( isset( $data['results'] ) && is_array( $data['results'] ) ) {
			return $data['results'];
		}

		// Format 4: Direct array of objects
		if ( is_array( $data ) && isset( $data[0] ) && is_array( $data[0] ) ) {
			return $data;
		}

		return array();
	}
}

/**
 * Example 2: Add Supported Extension for REST API
 *
 * This tells the system to accept .api, .rest, .json_api files
 */
add_filter( 'atables_supported_extensions', 'atables_example_add_rest_api_extension', 10, 2 );

function atables_example_add_rest_api_extension( $allowed_types, $extension ) {
	// Add our custom extensions
	if ( in_array( $extension, array( 'api', 'rest', 'json_api' ) ) ) {
		$allowed_types[] = $extension;
	}

	return $allowed_types;
}

/**
 * Example 3: Transform Data During Import
 *
 * This filters imported data to add custom transformations
 */
add_filter( 'atables_parse_data', 'atables_example_transform_product_data', 10, 3 );

function atables_example_transform_product_data( $data, $extension, $options ) {
	// Only apply to our REST API imports
	if ( ! in_array( $extension, array( 'api', 'rest', 'json_api' ) ) ) {
		return $data;
	}

	// Example transformations:
	foreach ( $data as $index => $row ) {
		// 1. Format currency values
		if ( isset( $row[2] ) && is_numeric( $row[2] ) ) {
			$data[ $index ][2] = '$' . number_format( (float) $row[2], 2 );
		}

		// 2. Convert timestamps to readable dates
		if ( isset( $row[3] ) && is_numeric( $row[3] ) && $row[3] > 1000000000 ) {
			$data[ $index ][3] = date( 'Y-m-d H:i:s', (int) $row[3] );
		}

		// 3. Clean up HTML tags
		foreach ( $row as $col_index => $value ) {
			if ( is_string( $value ) ) {
				$data[ $index ][ $col_index ] = wp_strip_all_tags( $value );
			}
		}
	}

	return $data;
}

/**
 * Example 4: Add Custom Headers
 *
 * Transform or clean up column headers
 */
add_filter( 'atables_import_headers', 'atables_example_clean_headers', 10, 3 );

function atables_example_clean_headers( $headers, $extension, $options ) {
	// Only apply to REST API imports
	if ( ! in_array( $extension, array( 'api', 'rest', 'json_api' ) ) ) {
		return $headers;
	}

	// Clean up headers
	$cleaned = array();
	foreach ( $headers as $header ) {
		// Convert snake_case to Title Case
		$header = str_replace( '_', ' ', $header );
		$header = ucwords( $header );
		$cleaned[] = $header;
	}

	return $cleaned;
}

/**
 * Example 5: Validate Import Data
 *
 * Add custom validation rules for imported data
 */
add_filter( 'atables_validate_import_data', 'atables_example_validate_product_data', 10, 2 );

function atables_example_validate_product_data( $validation_result, $data ) {
	// Example: Ensure minimum number of rows
	if ( count( $data ) < 1 ) {
		$validation_result['valid']  = false;
		$validation_result['errors'][] = 'Import must contain at least 1 row of data';
	}

	// Example: Check for required columns
	if ( isset( $data[0] ) && count( $data[0] ) < 2 ) {
		$validation_result['valid']  = false;
		$validation_result['errors'][] = 'Import must contain at least 2 columns';
	}

	// Example: Validate data quality
	$empty_rows = 0;
	foreach ( $data as $row ) {
		if ( empty( array_filter( $row ) ) ) {
			$empty_rows++;
		}
	}

	if ( $empty_rows > count( $data ) / 2 ) {
		$validation_result['valid']   = false;
		$validation_result['errors'][] = 'Too many empty rows in import data';
	}

	return $validation_result;
}

/**
 * Example 6: Track Import Lifecycle Events
 *
 * Log or process imports before they start
 */
add_action( 'atables_before_import', 'atables_example_log_import_start', 10, 3 );

function atables_example_log_import_start( $extension, $options, $file ) {
	// Log import start
	error_log( sprintf(
		'[A-Tables] Import started - Type: %s, User: %d, Time: %s',
		$extension,
		get_current_user_id(),
		current_time( 'mysql' )
	) );

	// Send notification to admin
	if ( $extension === 'rest' || $extension === 'api' ) {
		$admin_email = get_option( 'admin_email' );
		wp_mail(
			$admin_email,
			'New REST API Import Started',
			sprintf( 'A new REST API import was started by user %d', get_current_user_id() )
		);
	}
}

/**
 * Example 7: Process After Import Completes
 *
 * Perform actions after successful import
 */
add_action( 'atables_after_import', 'atables_example_process_import_result', 10, 3 );

function atables_example_process_import_result( $result, $extension, $options ) {
	// Only process REST API imports
	if ( ! in_array( $extension, array( 'api', 'rest', 'json_api' ) ) ) {
		return;
	}

	if ( $result->success ) {
		// Log successful import
		error_log( sprintf(
			'[A-Tables] Import completed - Rows: %d, Columns: %d',
			$result->row_count,
			$result->column_count
		) );

		// Store import metadata
		$import_history = get_option( 'atables_import_history', array() );
		$import_history[] = array(
			'type'      => $extension,
			'rows'      => $result->row_count,
			'columns'   => $result->column_count,
			'timestamp' => current_time( 'timestamp' ),
			'user_id'   => get_current_user_id(),
		);

		// Keep only last 100 imports
		$import_history = array_slice( $import_history, -100 );
		update_option( 'atables_import_history', $import_history );
	}
}

/**
 * Example 8: Customize Table Creation
 *
 * Modify table properties before it's created
 */
add_action( 'atables_before_table_create_from_import', 'atables_example_customize_table', 10, 3 );

function atables_example_customize_table( $title, $import_result, $source_type ) {
	// Add custom metadata or perform pre-creation tasks
	if ( $source_type === 'rest' || $source_type === 'api' ) {
		// Tag table as API-sourced
		add_filter( 'atables_table_metadata', function( $metadata ) {
			$metadata['data_source'] = 'rest_api';
			$metadata['import_date'] = current_time( 'mysql' );
			return $metadata;
		} );
	}
}

/**
 * Example 9: Process After Table Creation
 *
 * Perform actions after table is created from import
 */
add_action( 'atables_after_table_create_from_import', 'atables_example_post_table_creation', 10, 3 );

function atables_example_post_table_creation( $table_id, $import_result, $source_type ) {
	// Only for REST API sources
	if ( $source_type !== 'rest' && $source_type !== 'api' ) {
		return;
	}

	// Store source information
	update_post_meta( $table_id, '_atables_source_type', $source_type );
	update_post_meta( $table_id, '_atables_import_timestamp', current_time( 'timestamp' ) );

	// Create a chart automatically if we have numeric data
	if ( $import_result->column_count >= 2 && $import_result->row_count >= 3 ) {
		// Auto-create a basic chart for this table
		// (Chart creation code would go here)
		error_log( sprintf(
			'[A-Tables] Auto-created chart for table %d (REST API import)',
			$table_id
		) );
	}
}

/**
 * Admin Settings Page (Optional)
 *
 * Add settings page for API configuration
 */
add_action( 'admin_menu', 'atables_example_add_settings_page' );

function atables_example_add_settings_page() {
	add_submenu_page(
		'options-general.php',
		'A-Tables REST API Settings',
		'A-Tables REST API',
		'manage_options',
		'atables-rest-api',
		'atables_example_render_settings_page'
	);
}

function atables_example_render_settings_page() {
	?>
	<div class="wrap">
		<h1>A-Tables REST API Data Source Settings</h1>

		<h2>Usage Instructions</h2>
		<p>This plugin adds REST API support to A-Tables & Charts. You can now import data from REST APIs.</p>

		<h3>Supported Hook Points:</h3>
		<ul>
			<li><code>atables_register_parsers</code> - Register custom data parsers</li>
			<li><code>atables_supported_extensions</code> - Add file type support</li>
			<li><code>atables_parse_data</code> - Transform imported data</li>
			<li><code>atables_import_headers</code> - Modify column headers</li>
			<li><code>atables_validate_import_data</code> - Add validation rules</li>
			<li><code>atables_before_import</code> - Pre-import actions</li>
			<li><code>atables_after_import</code> - Post-import actions</li>
			<li><code>atables_before_table_create_from_import</code> - Before table creation</li>
			<li><code>atables_after_table_create_from_import</code> - After table creation</li>
		</ul>

		<h3>Example API URL:</h3>
		<code>https://api.example.com/products</code>

		<h3>Import History:</h3>
		<?php
		$history = get_option( 'atables_import_history', array() );
		if ( ! empty( $history ) ) {
			echo '<table class="widefat">';
			echo '<thead><tr><th>Type</th><th>Rows</th><th>Columns</th><th>Date</th></tr></thead>';
			echo '<tbody>';
			foreach ( array_reverse( array_slice( $history, -10 ) ) as $entry ) {
				echo '<tr>';
				echo '<td>' . esc_html( $entry['type'] ) . '</td>';
				echo '<td>' . esc_html( $entry['rows'] ) . '</td>';
				echo '<td>' . esc_html( $entry['columns'] ) . '</td>';
				echo '<td>' . esc_html( date( 'Y-m-d H:i:s', $entry['timestamp'] ) ) . '</td>';
				echo '</tr>';
			}
			echo '</tbody></table>';
		} else {
			echo '<p>No imports yet.</p>';
		}
		?>
	</div>
	<?php
}

/**
 * Add documentation to the Help tab
 */
add_action( 'load-settings_page_atables-rest-api', 'atables_example_add_help_tab' );

function atables_example_add_help_tab() {
	$screen = get_current_screen();

	$screen->add_help_tab( array(
		'id'      => 'atables_rest_api_help',
		'title'   => 'REST API Import',
		'content' => '<h3>How to Import from REST APIs</h3>
			<p>1. Go to A-Tables & Charts → Import Data</p>
			<p>2. Select "REST API" as the import type</p>
			<p>3. Enter your API endpoint URL</p>
			<p>4. Configure any API authentication (API key, etc.)</p>
			<p>5. Click Import</p>
			<h3>Supported API Formats</h3>
			<ul>
				<li>{ "data": [...] }</li>
				<li>{ "items": [...] }</li>
				<li>{ "results": [...] }</li>
				<li>Direct array of objects</li>
			</ul>',
	) );
}

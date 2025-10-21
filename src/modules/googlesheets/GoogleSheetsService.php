<?php
/**
 * Google Sheets Import Service
 *
 * Handles importing data from Google Sheets
 *
 * @package ATablesCharts\GoogleSheets\Services
 * @since 1.0.0
 */

namespace ATablesCharts\GoogleSheets\Services;

/**
 * GoogleSheetsService Class
 *
 * Responsibilities:
 * - Parse Google Sheets URLs
 * - Fetch data from Google Sheets
 * - Convert to table format
 * - Handle public sheets
 */
class GoogleSheetsService {

	/**
	 * Import data from Google Sheets URL
	 *
	 * @param string $url        Google Sheets URL.
	 * @param array  $options    Import options.
	 * @return array Import result with data
	 */
	public function import_from_url( $url, $options = array() ) {
		$defaults = array(
			'sheet_name' => '',
			'range'      => '',
			'headers'    => true,
		);

		$options = wp_parse_args( $options, $defaults );

		// Validate URL
		$validation = $this->validate_url( $url );
		if ( ! $validation['valid'] ) {
			return array(
				'success' => false,
				'message' => $validation['error'],
			);
		}

		// Extract sheet ID
		$sheet_id = $this->extract_sheet_id( $url );
		if ( ! $sheet_id ) {
			return array(
				'success' => false,
				'message' => __( 'Could not extract Sheet ID from URL.', 'a-tables-charts' ),
			);
		}

		// Build CSV export URL
		$csv_url = $this->build_csv_url( $sheet_id, $options );

		// Fetch data
		$csv_data = $this->fetch_sheet_data( $csv_url );
		if ( is_wp_error( $csv_data ) ) {
			return array(
				'success' => false,
				'message' => $csv_data->get_error_message(),
			);
		}

		// Parse CSV data
		$parsed = $this->parse_csv_data( $csv_data, $options );

		return array(
			'success'       => true,
			'data'          => $parsed['data'],
			'headers'       => $parsed['headers'],
			'row_count'     => $parsed['row_count'],
			'column_count'  => $parsed['column_count'],
			'sheet_id'      => $sheet_id,
			'source_url'    => $url,
		);
	}

	/**
	 * Validate Google Sheets URL
	 *
	 * @param string $url Google Sheets URL.
	 * @return array Validation result
	 */
	public function validate_url( $url ) {
		if ( empty( $url ) ) {
			return array(
				'valid' => false,
				'error' => __( 'URL is required.', 'a-tables-charts' ),
			);
		}

		// Check if it's a valid URL
		if ( ! filter_var( $url, FILTER_VALIDATE_URL ) ) {
			return array(
				'valid' => false,
				'error' => __( 'Invalid URL format.', 'a-tables-charts' ),
			);
		}

		// Check if it's a Google Sheets URL
		if ( strpos( $url, 'docs.google.com/spreadsheets' ) === false ) {
			return array(
				'valid' => false,
				'error' => __( 'URL must be a Google Sheets URL.', 'a-tables-charts' ),
			);
		}

		return array(
			'valid' => true,
		);
	}

	/**
	 * Extract Sheet ID from URL
	 *
	 * @param string $url Google Sheets URL.
	 * @return string|false Sheet ID or false
	 */
	public function extract_sheet_id( $url ) {
		// Pattern: /spreadsheets/d/SHEET_ID/
		if ( preg_match( '/\/spreadsheets\/d\/([a-zA-Z0-9-_]+)/', $url, $matches ) ) {
			return $matches[1];
		}

		return false;
	}

	/**
	 * Extract GID (sheet tab ID) from URL
	 *
	 * @param string $url Google Sheets URL.
	 * @return string|null GID or null
	 */
	public function extract_gid( $url ) {
		// Pattern: gid=123456
		if ( preg_match( '/gid=([0-9]+)/', $url, $matches ) ) {
			return $matches[1];
		}

		return null;
	}

	/**
	 * Build CSV export URL
	 *
	 * @param string $sheet_id Sheet ID.
	 * @param array  $options  Export options.
	 * @return string CSV export URL
	 */
	public function build_csv_url( $sheet_id, $options = array() ) {
		$base_url = 'https://docs.google.com/spreadsheets/d/' . $sheet_id . '/export';

		$params = array(
			'format' => 'csv',
		);

		// Add GID if specified
		if ( ! empty( $options['gid'] ) ) {
			$params['gid'] = $options['gid'];
		}

		// Add range if specified (not supported in CSV export, but documented for future)
		// Note: Range is better supported via Google Sheets API

		return add_query_arg( $params, $base_url );
	}

	/**
	 * Fetch sheet data from URL
	 *
	 * @param string $url CSV export URL.
	 * @return string|WP_Error CSV data or error
	 */
	public function fetch_sheet_data( $url ) {
		$response = wp_remote_get( $url, array(
			'timeout' => 30,
			'headers' => array(
				'User-Agent' => 'WordPress/' . get_bloginfo( 'version' ) . '; ' . get_bloginfo( 'url' ),
			),
		) );

		if ( is_wp_error( $response ) ) {
			return new \WP_Error(
				'fetch_failed',
				__( 'Failed to fetch data from Google Sheets. Please check the URL and try again.', 'a-tables-charts' )
			);
		}

		$status_code = wp_remote_retrieve_response_code( $response );

		if ( $status_code !== 200 ) {
			if ( $status_code === 403 ) {
				return new \WP_Error(
					'permission_denied',
					__( 'Access denied. Please make sure the Google Sheet is set to "Anyone with the link can view".', 'a-tables-charts' )
				);
			}

			return new \WP_Error(
				'fetch_failed',
				sprintf(
					__( 'Failed to fetch data. HTTP Status: %d', 'a-tables-charts' ),
					$status_code
				)
			);
		}

		$body = wp_remote_retrieve_body( $response );

		if ( empty( $body ) ) {
			return new \WP_Error(
				'empty_response',
				__( 'The sheet appears to be empty or inaccessible.', 'a-tables-charts' )
			);
		}

		return $body;
	}

	/**
	 * Parse CSV data
	 *
	 * @param string $csv_data CSV content.
	 * @param array  $options  Parse options.
	 * @return array Parsed data
	 */
	public function parse_csv_data( $csv_data, $options = array() ) {
		$defaults = array(
			'headers' => true,
			'delimiter' => ',',
			'enclosure' => '"',
			'escape' => '\\',
		);

		$options = wp_parse_args( $options, $defaults );

		$lines = str_getcsv( $csv_data, "\n" );
		$data = array();
		$headers = array();

		foreach ( $lines as $index => $line ) {
			$row = str_getcsv( $line, $options['delimiter'], $options['enclosure'], $options['escape'] );

			// Skip empty rows
			if ( empty( array_filter( $row ) ) ) {
				continue;
			}

			// First row as headers
			if ( $index === 0 && $options['headers'] ) {
				$headers = array_map( 'trim', $row );
				continue;
			}

			// If no headers, generate them
			if ( empty( $headers ) ) {
				$headers = array_map( function( $i ) {
					return 'Column ' . ( $i + 1 );
				}, array_keys( $row ) );
			}

			// Map row to headers
			$row_data = array();
			foreach ( $headers as $col_index => $header ) {
				$row_data[ $header ] = isset( $row[ $col_index ] ) ? trim( $row[ $col_index ] ) : '';
			}

			$data[] = $row_data;
		}

		return array(
			'headers'      => $headers,
			'data'         => $data,
			'row_count'    => count( $data ),
			'column_count' => count( $headers ),
		);
	}

	/**
	 * Get sheet sharing instructions
	 *
	 * @return string Instructions HTML
	 */
	public static function get_sharing_instructions() {
		return '
			<div class="atables-instructions">
				<h4>' . __( 'How to Share Your Google Sheet:', 'a-tables-charts' ) . '</h4>
				<ol>
					<li>' . __( 'Open your Google Sheet', 'a-tables-charts' ) . '</li>
					<li>' . __( 'Click the "Share" button in the top-right corner', 'a-tables-charts' ) . '</li>
					<li>' . __( 'Under "Get link", click "Change to anyone with the link"', 'a-tables-charts' ) . '</li>
					<li>' . __( 'Set permissions to "Viewer"', 'a-tables-charts' ) . '</li>
					<li>' . __( 'Click "Copy link" and paste it here', 'a-tables-charts' ) . '</li>
				</ol>
				<p class="description">' . __( 'Note: The sheet must be publicly accessible for import to work.', 'a-tables-charts' ) . '</p>
			</div>
		';
	}

	/**
	 * Test sheet access
	 *
	 * @param string $url Sheet URL.
	 * @return array Test result
	 */
	public function test_access( $url ) {
		$validation = $this->validate_url( $url );
		if ( ! $validation['valid'] ) {
			return array(
				'success' => false,
				'message' => $validation['error'],
			);
		}

		$sheet_id = $this->extract_sheet_id( $url );
		if ( ! $sheet_id ) {
			return array(
				'success' => false,
				'message' => __( 'Invalid Sheet ID.', 'a-tables-charts' ),
			);
		}

		$csv_url = $this->build_csv_url( $sheet_id );
		$response = wp_remote_head( $csv_url, array( 'timeout' => 10 ) );

		if ( is_wp_error( $response ) ) {
			return array(
				'success' => false,
				'message' => __( 'Could not connect to Google Sheets.', 'a-tables-charts' ),
			);
		}

		$status_code = wp_remote_retrieve_response_code( $response );

		if ( $status_code === 200 ) {
			return array(
				'success' => true,
				'message' => __( 'Sheet is accessible!', 'a-tables-charts' ),
			);
		} elseif ( $status_code === 403 ) {
			return array(
				'success' => false,
				'message' => __( 'Access denied. Please check sharing settings.', 'a-tables-charts' ),
			);
		} else {
			return array(
				'success' => false,
				'message' => sprintf( __( 'Unexpected status code: %d', 'a-tables-charts' ), $status_code ),
			);
		}
	}

	/**
	 * Get supported URL formats
	 *
	 * @return array URL format examples
	 */
	public static function get_url_formats() {
		return array(
			'Standard' => 'https://docs.google.com/spreadsheets/d/SHEET_ID/edit',
			'With GID' => 'https://docs.google.com/spreadsheets/d/SHEET_ID/edit#gid=0',
			'Published' => 'https://docs.google.com/spreadsheets/d/SHEET_ID/pubhtml',
		);
	}
}

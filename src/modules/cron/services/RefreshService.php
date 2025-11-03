<?php
/**
 * Refresh Service
 *
 * Executes scheduled data refreshes from various sources.
 *
 * @package ATablesCharts\Cron\Services
 * @since 1.0.0
 */

namespace ATablesCharts\Cron\Services;

use ATablesCharts\Database\Services\MySQLQueryService;
use ATablesCharts\DataSources\Services\ImportService;
use ATablesCharts\Tables\Services\TableService;
use ATablesCharts\Shared\Utils\Logger;

/**
 * RefreshService Class
 *
 * Handles execution of scheduled data refreshes.
 */
class RefreshService {

	/**
	 * MySQL service instance
	 *
	 * @var MySQLQueryService
	 */
	private $mysql_service;

	/**
	 * Import service instance
	 *
	 * @var ImportService
	 */
	private $import_service;

	/**
	 * Table service instance
	 *
	 * @var TableService
	 */
	private $table_service;

	/**
	 * Logger instance
	 *
	 * @var Logger
	 */
	private $logger;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->mysql_service  = new MySQLQueryService();
		$this->import_service = new ImportService();
		$this->table_service  = new TableService();
		$this->logger         = new Logger();
	}

	/**
	 * Refresh table data from scheduled source
	 *
	 * @param array $schedule Schedule configuration.
	 * @return array Result with success status and message
	 */
	public function refresh_table( $schedule ) {
		$source_type = $schedule['source_type'];
		$table_id    = $schedule['table_id'];
		$config      = $schedule['config'];

		$this->logger->info( 'Starting table refresh', array(
			'table_id'    => $table_id,
			'source_type' => $source_type,
		) );

		/**
		 * Action triggered before scheduled refresh
		 *
		 * @since 1.0.0
		 * @param int   $table_id    Table ID
		 * @param array $schedule    Schedule configuration
		 */
		do_action( 'atables_before_scheduled_refresh', $table_id, $schedule );

		try {
			switch ( $source_type ) {
				case 'mysql':
					$result = $this->refresh_from_mysql( $table_id, $config );
					break;

				case 'google_sheets':
					$result = $this->refresh_from_google_sheets( $table_id, $config );
					break;

				case 'csv_url':
					$result = $this->refresh_from_csv_url( $table_id, $config );
					break;

				case 'rest_api':
					$result = $this->refresh_from_rest_api( $table_id, $config );
					break;

				default:
					/**
					 * Filter to allow custom refresh sources
					 *
					 * @since 1.0.0
					 * @param array  $result      Refresh result (null by default)
					 * @param int    $table_id    Table ID
					 * @param string $source_type Source type
					 * @param array  $config      Source configuration
					 */
					$result = apply_filters( 'atables_custom_refresh_source', null, $table_id, $source_type, $config );

					if ( $result === null ) {
						throw new \Exception( 'Unsupported source type: ' . $source_type );
					}
					break;
			}

			/**
			 * Action triggered after successful refresh
			 *
			 * @since 1.0.0
			 * @param int   $table_id Table ID
			 * @param array $result   Refresh result
			 */
			do_action( 'atables_after_scheduled_refresh', $table_id, $result );

			return $result;

		} catch ( \Exception $e ) {
			$this->logger->error( 'Table refresh failed', array(
				'table_id' => $table_id,
				'error'    => $e->getMessage(),
			) );

			return array(
				'success' => false,
				'message' => $e->getMessage(),
			);
		}
	}

	/**
	 * Refresh from MySQL query
	 *
	 * @param int   $table_id Table ID.
	 * @param array $config   Configuration.
	 * @return array Result
	 */
	private function refresh_from_mysql( $table_id, $config ) {
		if ( empty( $config['query'] ) ) {
			throw new \Exception( 'MySQL query is required' );
		}

		$query = $config['query'];

		// Validate query
		$validation = $this->mysql_service->validate_query( $query );
		if ( ! $validation['valid'] ) {
			throw new \Exception( 'Invalid MySQL query: ' . implode( ', ', $validation['errors'] ) );
		}

		// Execute query
		$result = $this->mysql_service->execute_query( $query );

		if ( ! $result['success'] ) {
			throw new \Exception( $result['message'] );
		}

		// Update table with new data
		$update_result = $this->table_service->update_table_data( $table_id, $result['data'], $result['headers'] );

		if ( ! $update_result['success'] ) {
			throw new \Exception( 'Failed to update table: ' . $update_result['message'] );
		}

		return array(
			'success' => true,
			'message' => sprintf(
				'Successfully refreshed table from MySQL (%d rows)',
				count( $result['data'] )
			),
			'rows'    => count( $result['data'] ),
		);
	}

	/**
	 * Refresh from Google Sheets
	 *
	 * @param int   $table_id Table ID.
	 * @param array $config   Configuration.
	 * @return array Result
	 */
	private function refresh_from_google_sheets( $table_id, $config ) {
		if ( empty( $config['sheet_id'] ) || empty( $config['api_key'] ) ) {
			throw new \Exception( 'Google Sheets ID and API key are required' );
		}

		$sheet_id = $config['sheet_id'];
		$api_key  = $config['api_key'];
		$range    = ! empty( $config['range'] ) ? $config['range'] : 'Sheet1';

		// Build Google Sheets API URL
		$url = sprintf(
			'https://sheets.googleapis.com/v4/spreadsheets/%s/values/%s?key=%s',
			$sheet_id,
			urlencode( $range ),
			$api_key
		);

		// Fetch data
		$response = wp_remote_get( $url, array( 'timeout' => 30 ) );

		if ( is_wp_error( $response ) ) {
			throw new \Exception( 'Failed to fetch Google Sheets: ' . $response->get_error_message() );
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( empty( $data['values'] ) ) {
			throw new \Exception( 'No data found in Google Sheets' );
		}

		$values = $data['values'];

		// First row as headers
		$headers = array_shift( $values );

		// Update table with new data
		$update_result = $this->table_service->update_table_data( $table_id, $values, $headers );

		if ( ! $update_result['success'] ) {
			throw new \Exception( 'Failed to update table: ' . $update_result['message'] );
		}

		return array(
			'success' => true,
			'message' => sprintf(
				'Successfully refreshed table from Google Sheets (%d rows)',
				count( $values )
			),
			'rows'    => count( $values ),
		);
	}

	/**
	 * Refresh from CSV URL
	 *
	 * @param int   $table_id Table ID.
	 * @param array $config   Configuration.
	 * @return array Result
	 */
	private function refresh_from_csv_url( $table_id, $config ) {
		if ( empty( $config['url'] ) ) {
			throw new \Exception( 'CSV URL is required' );
		}

		$url         = $config['url'];
		$has_headers = isset( $config['has_headers'] ) ? $config['has_headers'] : true;
		$delimiter   = isset( $config['delimiter'] ) ? $config['delimiter'] : ',';

		// Fetch CSV file
		$response = wp_remote_get( $url, array( 'timeout' => 30 ) );

		if ( is_wp_error( $response ) ) {
			throw new \Exception( 'Failed to fetch CSV: ' . $response->get_error_message() );
		}

		$csv_content = wp_remote_retrieve_body( $response );

		if ( empty( $csv_content ) ) {
			throw new \Exception( 'Empty CSV file' );
		}

		// Parse CSV using ImportService
		$import_result = $this->import_service->import_from_string( $csv_content, 'csv', array(
			'has_header' => $has_headers,
			'delimiter'  => $delimiter,
		) );

		if ( ! $import_result->success ) {
			throw new \Exception( 'Failed to parse CSV: ' . $import_result->message );
		}

		// Update table with new data
		$update_result = $this->table_service->update_table_data(
			$table_id,
			$import_result->data,
			$import_result->headers
		);

		if ( ! $update_result['success'] ) {
			throw new \Exception( 'Failed to update table: ' . $update_result['message'] );
		}

		return array(
			'success' => true,
			'message' => sprintf(
				'Successfully refreshed table from CSV URL (%d rows)',
				$import_result->row_count
			),
			'rows'    => $import_result->row_count,
		);
	}

	/**
	 * Refresh from REST API
	 *
	 * @param int   $table_id Table ID.
	 * @param array $config   Configuration.
	 * @return array Result
	 */
	private function refresh_from_rest_api( $table_id, $config ) {
		if ( empty( $config['url'] ) ) {
			throw new \Exception( 'REST API URL is required' );
		}

		$url     = $config['url'];
		$method  = ! empty( $config['method'] ) ? $config['method'] : 'GET';
		$headers = ! empty( $config['headers'] ) ? $config['headers'] : array();

		// Make API request
		$args = array(
			'method'  => $method,
			'timeout' => 30,
			'headers' => $headers,
		);

		$response = wp_remote_request( $url, $args );

		if ( is_wp_error( $response ) ) {
			throw new \Exception( 'REST API request failed: ' . $response->get_error_message() );
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( json_last_error() !== JSON_ERROR_NONE ) {
			throw new \Exception( 'Invalid JSON response: ' . json_last_error_msg() );
		}

		// Extract data array (support common formats)
		$items = $this->extract_api_data( $data );

		if ( empty( $items ) ) {
			throw new \Exception( 'No data found in API response' );
		}

		// Extract headers from first item
		$headers = array_keys( $items[0] );

		// Convert to indexed arrays
		$rows = array();
		foreach ( $items as $item ) {
			$row = array();
			foreach ( $headers as $header ) {
				$row[] = isset( $item[ $header ] ) ? $item[ $header ] : '';
			}
			$rows[] = $row;
		}

		// Update table with new data
		$update_result = $this->table_service->update_table_data( $table_id, $rows, $headers );

		if ( ! $update_result['success'] ) {
			throw new \Exception( 'Failed to update table: ' . $update_result['message'] );
		}

		return array(
			'success' => true,
			'message' => sprintf(
				'Successfully refreshed table from REST API (%d rows)',
				count( $rows )
			),
			'rows'    => count( $rows ),
		);
	}

	/**
	 * Extract data array from API response
	 *
	 * Supports common API response formats:
	 * - { "data": [...] }
	 * - { "items": [...] }
	 * - { "results": [...] }
	 * - Direct array
	 *
	 * @param array $data API response data.
	 * @return array Extracted items
	 */
	private function extract_api_data( $data ) {
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

<?php
/**
 * XML Import Service
 *
 * Handles XML file import operations.
 *
 * @package ATablesCharts\Import\Services
 * @since 1.0.0
 */

namespace ATablesCharts\Import\Services;

use ATablesCharts\Import\Parsers\XmlParser;
use ATablesCharts\Shared\Utils\Logger;
use ATablesCharts\Shared\Utils\Sanitizer;

/**
 * XmlImportService Class
 *
 * Responsibilities:
 * - Handle XML file uploads
 * - Parse XML files
 * - Validate XML data
 * - Return structured import results
 */
class XmlImportService {

	/**
	 * XML parser
	 *
	 * @var XmlParser
	 */
	private $parser;

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
		$this->parser = new XmlParser();
		$this->logger = new Logger();
	}

	/**
	 * Import XML file
	 *
	 * @param string $file_path Path to XML file.
	 * @param array  $options   Import options.
	 * @return object ImportResult object
	 * @throws \Exception If import fails.
	 */
	public function import( $file_path, $options = array() ) {
		$this->logger->info( 'Starting XML import', array(
			'file' => basename( $file_path ),
		) );

		// Validate file.
		$validation = $this->parser->validate( $file_path );
		
		if ( ! $validation['valid'] ) {
			$this->logger->error( 'XML validation failed', array(
				'errors' => $validation['errors'],
			) );
			
			throw new \Exception(
				sprintf(
					/* translators: %s: error messages */
					__( 'XML validation failed: %s', 'a-tables-charts' ),
					implode( ', ', $validation['errors'] )
				)
			);
		}

		try {
			// Parse XML file.
			$parsed_data = $this->parser->parse( $file_path, $options );

			// Sanitize headers.
			$headers = array_map( array( Sanitizer::class, 'text' ), $parsed_data['headers'] );

			// Sanitize data.
			$data = array();
			foreach ( $parsed_data['data'] as $row ) {
				$sanitized_row = array_map( array( Sanitizer::class, 'text' ), $row );
				$data[] = $sanitized_row;
			}

			$this->logger->info( 'XML import successful', array(
				'rows'    => count( $data ),
				'columns' => count( $headers ),
			) );

			// Create import result object.
			return (object) array(
				'success'       => true,
				'headers'       => $headers,
				'data'          => $data,
				'row_count'     => count( $data ),
				'column_count'  => count( $headers ),
				'source_type'   => 'xml',
				'row_element'   => isset( $parsed_data['row_element'] ) ? $parsed_data['row_element'] : null,
			);

		} catch ( \Exception $e ) {
			$this->logger->error( 'XML import failed', array(
				'error' => $e->getMessage(),
			) );
			
			throw $e;
		}
	}

	/**
	 * Get XML structure
	 *
	 * @param string $file_path Path to XML file.
	 * @return array XML structure info
	 */
	public function get_structure( $file_path ) {
		try {
			return $this->parser->get_structure( $file_path );
		} catch ( \Exception $e ) {
			$this->logger->error( 'Failed to get XML structure', array(
				'error' => $e->getMessage(),
			) );
			
			return array(
				'error' => $e->getMessage(),
			);
		}
	}

	/**
	 * Preview XML file
	 *
	 * Returns first few rows for preview.
	 *
	 * @param string $file_path Path to XML file.
	 * @param int    $rows      Number of rows to preview.
	 * @return array Preview data
	 */
	public function preview( $file_path, $rows = 10 ) {
		try {
			$options = array(
				'max_rows' => $rows,
			);

			$parsed_data = $this->parser->parse( $file_path, $options );

			return array(
				'success'      => true,
				'headers'      => $parsed_data['headers'],
				'data'         => $parsed_data['data'],
				'row_count'    => $parsed_data['row_count'],
				'column_count' => $parsed_data['column_count'],
			);

		} catch ( \Exception $e ) {
			return array(
				'success' => false,
				'error'   => $e->getMessage(),
			);
		}
	}

	/**
	 * Validate XML file
	 *
	 * @param string $file_path Path to file.
	 * @return array Validation result
	 */
	public function validate( $file_path ) {
		return $this->parser->validate( $file_path );
	}
}

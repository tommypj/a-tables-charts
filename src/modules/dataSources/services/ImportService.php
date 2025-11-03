<?php
/**
 * Import Service
 *
 * Coordinates all data import operations.
 * Acts as the main entry point for importing data from various sources.
 *
 * @package ATablesCharts\DataSources\Services
 * @since 1.0.0
 */

namespace ATablesCharts\DataSources\Services;

use ATablesCharts\DataSources\Types\DataSourceType;
use ATablesCharts\DataSources\Types\ImportResult;
use ATablesCharts\DataSources\Parsers\CsvParser;
use ATablesCharts\DataSources\Parsers\JsonParser;
use ATablesCharts\Shared\Utils\Logger;
use ATablesCharts\Shared\Utils\Validator;
use ATablesCharts\Shared\Utils\Sanitizer;

/**
 * ImportService Class
 *
 * Responsibilities:
 * - Coordinate file uploads
 * - Select appropriate parser
 * - Validate files
 * - Handle errors
 * - Cache results
 */
class ImportService {

	/**
	 * Logger instance
	 *
	 * @var Logger
	 */
	private $logger;

	/**
	 * Available parsers
	 *
	 * @var array
	 */
	private $parsers = array();

	/**
	 * Maximum file size (in bytes)
	 *
	 * @var int
	 */
	private $max_file_size;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->logger = new Logger();
		
		// Get max upload size from PHP settings.
		$this->max_file_size = $this->get_max_upload_size();
		
		// Register parsers.
		$this->register_parsers();
	}

	/**
	 * Register available parsers
	 */
	private function register_parsers() {
		$this->parsers['csv']  = new CsvParser();
		$this->parsers['json'] = new JsonParser();

		// XML and Excel parsers will be added when we create them.

		/**
		 * Filter to allow developers to register custom parsers
		 *
		 * @since 1.0.0
		 * @param array $parsers Array of parser instances keyed by format
		 */
		$this->parsers = apply_filters( 'atables_register_parsers', $this->parsers );

		$this->logger->info( 'Parsers registered', array(
			'parsers' => array_keys( $this->parsers ),
		) );
	}

	/**
	 * Import data from uploaded file
	 *
	 * @param array $file    File from $_FILES array.
	 * @param array $options Import options.
	 * @return ImportResult Import result
	 */
	public function import_from_upload( $file, $options = array() ) {
		// Validate file upload.
		$validation = $this->validate_upload( $file );
		if ( ! $validation['valid'] ) {
			return ImportResult::error( $validation['error'] );
		}

		// Get file extension from original filename.
		$extension = strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) );

		// Get appropriate parser.
		$parser = $this->get_parser_for_extension( $extension );
		if ( ! $parser ) {
			return ImportResult::error(
				sprintf(
					/* translators: %s: File extension */
					__( 'Unsupported file type: %s', 'a-tables-charts' ),
					$extension
				)
			);
		}

		/**
		 * Allow developers to filter import options before processing
		 *
		 * @since 1.0.0
		 * @param array  $options   Import options
		 * @param string $extension File extension
		 * @param array  $file      File data from $_FILES
		 */
		$options = apply_filters( 'atables_import_options', $options, $extension, $file );

		/**
		 * Action triggered before import begins
		 *
		 * @since 1.0.0
		 * @param string $extension File extension
		 * @param array  $options   Import options
		 * @param array  $file      File data from $_FILES
		 */
		do_action( 'atables_before_import', $extension, $options, $file );

		// Parse file using the temporary file path.
		$this->logger->info( 'Starting import', array(
			'filename'  => $file['name'],
			'size'      => $file['size'],
			'extension' => $extension,
			'tmp_name'  => $file['tmp_name'],
		) );

		try {
			// Read file content and parse as string to avoid extension check issues
			$content = file_get_contents( $file['tmp_name'] );

			if ( false === $content ) {
				throw new \Exception( __( 'Failed to read uploaded file.', 'a-tables-charts' ) );
			}

			// Use parse_string instead of parse_file to bypass extension check
			$result = $parser->parse_string( $content, $options );

			if ( $result->success ) {
				/**
				 * Filter imported data after parsing
				 *
				 * @since 1.0.0
				 * @param array  $data      Parsed data
				 * @param string $extension File extension/source type
				 * @param array  $options   Import options
				 */
				if ( isset( $result->data ) && is_array( $result->data ) ) {
					$result->data = apply_filters( 'atables_parse_data', $result->data, $extension, $options );

					// Update row count after filtering
					$result->row_count = count( $result->data );
				}

				/**
				 * Filter imported headers after parsing
				 *
				 * @since 1.0.0
				 * @param array  $headers   Parsed headers
				 * @param string $extension File extension/source type
				 * @param array  $options   Import options
				 */
				if ( isset( $result->headers ) && is_array( $result->headers ) ) {
					$result->headers = apply_filters( 'atables_import_headers', $result->headers, $extension, $options );

					// Update column count after filtering
					$result->column_count = count( $result->headers );
				}

				$this->logger->info( 'Import successful', array(
					'rows'    => $result->row_count,
					'columns' => $result->column_count,
				) );
			} else {
				$this->logger->error( 'Import failed', array(
					'error' => $result->message,
				) );
			}

			/**
			 * Action triggered after import completes
			 *
			 * @since 1.0.0
			 * @param ImportResult $result    Import result object
			 * @param string       $extension File extension
			 * @param array        $options   Import options
			 */
			do_action( 'atables_after_import', $result, $extension, $options );

			return $result;

		} catch ( \Exception $e ) {
			$this->logger->error( 'Import exception', array(
				'exception' => $e->getMessage(),
			) );

			return ImportResult::error(
				sprintf(
					/* translators: %s: Error message */
					__( 'Import error: %s', 'a-tables-charts' ),
					$e->getMessage()
				)
			);
		}
	}

	/**
	 * Import data from URL
	 *
	 * @param string $url     URL to import from.
	 * @param array  $options Import options.
	 * @return ImportResult Import result
	 */
	public function import_from_url( $url, $options = array() ) {
		// Validate URL.
		if ( ! Validator::url( $url ) ) {
			return ImportResult::error( __( 'Invalid URL provided.', 'a-tables-charts' ) );
		}

		$this->logger->info( 'Importing from URL', array( 'url' => $url ) );

		// Download file.
		$response = wp_remote_get( $url, array(
			'timeout' => 30,
		) );

		if ( is_wp_error( $response ) ) {
			return ImportResult::error( $response->get_error_message() );
		}

		$content = wp_remote_retrieve_body( $response );

		if ( empty( $content ) ) {
			return ImportResult::error( __( 'Failed to download data from URL.', 'a-tables-charts' ) );
		}

		// Detect format from URL.
		$extension = strtolower( pathinfo( wp_parse_url( $url, PHP_URL_PATH ), PATHINFO_EXTENSION ) );
		
		if ( empty( $extension ) ) {
			// Try to detect from content-type header.
			$content_type = wp_remote_retrieve_header( $response, 'content-type' );
			$extension    = $this->get_extension_from_mime( $content_type );
		}

		// Get parser.
		$parser = $this->get_parser_for_extension( $extension );
		if ( ! $parser ) {
			return ImportResult::error( __( 'Could not determine file format from URL.', 'a-tables-charts' ) );
		}

		// Parse content.
		try {
			return $parser->parse_string( $content, $options );
		} catch ( \Exception $e ) {
			return ImportResult::error( $e->getMessage() );
		}
	}

	/**
	 * Import data from string content
	 *
	 * @param string $content Data content.
	 * @param string $format  Data format (csv, json, xml).
	 * @param array  $options Import options.
	 * @return ImportResult Import result
	 */
	public function import_from_string( $content, $format, $options = array() ) {
		// Get parser.
		$parser = $this->get_parser_for_extension( $format );
		if ( ! $parser ) {
			return ImportResult::error(
				sprintf(
					/* translators: %s: Format type */
					__( 'Unsupported format: %s', 'a-tables-charts' ),
					$format
				)
			);
		}

		// Parse content.
		try {
			return $parser->parse_string( $content, $options );
		} catch ( \Exception $e ) {
			return ImportResult::error( $e->getMessage() );
		}
	}

	/**
	 * Validate file upload
	 *
	 * @param array $file File from $_FILES array.
	 * @return array Validation result
	 */
	private function validate_upload( $file ) {
		// Check if file was uploaded.
		$validation = Validator::file_upload( $file, array(), $this->max_file_size );

		if ( ! $validation['valid'] ) {
			return $validation;
		}

		// Additional security checks.
		$extension = strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) );

		// Check if extension is supported.
		if ( ! $this->is_extension_supported( $extension ) ) {
			return array(
				'valid' => false,
				'error' => sprintf(
					/* translators: %s: File extension */
					__( 'File type .%s is not supported.', 'a-tables-charts' ),
					$extension
				),
			);
		}

		return array( 'valid' => true );
	}

	/**
	 * Get parser for file extension
	 *
	 * @param string $extension File extension.
	 * @return object|null Parser instance or null
	 */
	private function get_parser_for_extension( $extension ) {
		$extension = strtolower( $extension );

		// Check each parser.
		foreach ( $this->parsers as $parser ) {
			if ( in_array( $extension, $parser->get_supported_extensions(), true ) ) {
				return $parser;
			}
		}

		return null;
	}

	/**
	 * Check if extension is supported
	 *
	 * @param string $extension File extension.
	 * @return bool True if supported
	 */
	private function is_extension_supported( $extension ) {
		// Check if parser exists for this extension
		if ( null === $this->get_parser_for_extension( $extension ) ) {
			return false;
		}

		// Check if extension is allowed in settings
		$settings = get_option( 'atables_settings', array() );
		$allowed_types = isset( $settings['allowed_file_types'] ) && is_array( $settings['allowed_file_types'] )
			? $settings['allowed_file_types']
			: array( 'csv', 'json', 'xlsx', 'xls', 'xml' ); // Default allowed types

		/**
		 * Filter supported extensions list
		 *
		 * Allows developers to add custom file extensions support
		 *
		 * @since 1.0.0
		 * @param array  $allowed_types Array of allowed file extensions
		 * @param string $extension     The extension being checked
		 */
		$allowed_types = apply_filters( 'atables_supported_extensions', $allowed_types, $extension );

		$this->logger->info( 'Checking extension against settings', array(
			'extension'     => $extension,
			'allowed_types' => $allowed_types,
		) );

		return in_array( $extension, $allowed_types, true );
	}

	/**
	 * Get extension from MIME type
	 *
	 * @param string $mime_type MIME type.
	 * @return string Extension
	 */
	private function get_extension_from_mime( $mime_type ) {
		$mime_map = array(
			'text/csv'                              => 'csv',
			'application/csv'                       => 'csv',
			'text/comma-separated-values'           => 'csv',
			'application/json'                      => 'json',
			'text/json'                             => 'json',
			'application/xml'                       => 'xml',
			'text/xml'                              => 'xml',
			'application/vnd.ms-excel'              => 'xls',
			'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
		);

		return isset( $mime_map[ $mime_type ] ) ? $mime_map[ $mime_type ] : '';
	}

	/**
	 * Get maximum upload size
	 *
	 * @return int Maximum size in bytes
	 */
	private function get_max_upload_size() {
		// Get WordPress max upload size.
		$wp_max = wp_max_upload_size();

		// Get PHP settings.
		$post_max   = $this->parse_size( ini_get( 'post_max_size' ) );
		$upload_max = $this->parse_size( ini_get( 'upload_max_filesize' ) );

		// Return the smallest.
		return min( $wp_max, $post_max, $upload_max );
	}

	/**
	 * Parse size string to bytes
	 *
	 * @param string $size Size string (e.g., '10M', '1G').
	 * @return int Size in bytes
	 */
	private function parse_size( $size ) {
		$unit = strtoupper( substr( $size, -1 ) );
		$size = (int) substr( $size, 0, -1 );

		switch ( $unit ) {
			case 'G':
				$size *= 1024;
				// Fall through.
			case 'M':
				$size *= 1024;
				// Fall through.
			case 'K':
				$size *= 1024;
		}

		return $size;
	}

	/**
	 * Get supported file types
	 *
	 * @return array Array of supported extensions
	 */
	public function get_supported_extensions() {
		$extensions = array();

		foreach ( $this->parsers as $parser ) {
			$extensions = array_merge( $extensions, $parser->get_supported_extensions() );
		}

		return array_unique( $extensions );
	}

	/**
	 * Get maximum file size for display
	 *
	 * @return string Formatted size
	 */
	public function get_max_file_size_formatted() {
		return size_format( $this->max_file_size );
	}
}

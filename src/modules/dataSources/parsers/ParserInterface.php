<?php
/**
 * Parser Interface
 *
 * Interface that all data parsers must implement.
 *
 * @package ATablesCharts\DataSources\Parsers
 * @since 1.0.0
 */

namespace ATablesCharts\DataSources\Parsers;

use ATablesCharts\DataSources\Types\ImportResult;

/**
 * ParserInterface
 *
 * Defines the contract for all data parsers.
 */
interface ParserInterface {

	/**
	 * Parse data from a file
	 *
	 * @param string $file_path Path to the file to parse.
	 * @param array  $options   Optional parsing options.
	 * @return ImportResult Result of the parsing operation
	 */
	public function parse_file( $file_path, $options = array() );

	/**
	 * Parse data from a string
	 *
	 * @param string $content Raw data content.
	 * @param array  $options Optional parsing options.
	 * @return ImportResult Result of the parsing operation
	 */
	public function parse_string( $content, $options = array() );

	/**
	 * Validate that a file can be parsed
	 *
	 * @param string $file_path Path to the file.
	 * @return bool True if file can be parsed, false otherwise
	 */
	public function can_parse( $file_path );

	/**
	 * Get supported file extensions
	 *
	 * @return array Array of supported extensions (without dot)
	 */
	public function get_supported_extensions();

	/**
	 * Get supported MIME types
	 *
	 * @return array Array of supported MIME types
	 */
	public function get_supported_mime_types();
}

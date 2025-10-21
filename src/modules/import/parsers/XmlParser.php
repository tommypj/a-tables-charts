<?php
/**
 * XML Parser
 *
 * Parses XML files and extracts tabular data.
 *
 * @package ATablesCharts\Import\Parsers
 * @since 1.0.0
 */

namespace ATablesCharts\Import\Parsers;

/**
 * XmlParser Class
 *
 * Responsibilities:
 * - Parse XML files
 * - Extract headers from XML structure
 * - Convert XML nodes to tabular data
 * - Handle nested XML elements
 * - Validate XML structure
 */
class XmlParser {

	/**
	 * Parse XML file
	 *
	 * @param string $file_path Path to XML file.
	 * @param array  $options   Parser options.
	 * @return array Parsed data with headers and rows
	 * @throws \Exception If parsing fails.
	 */
	public function parse( $file_path, $options = array() ) {
		// Default options.
		$defaults = array(
			'row_element'    => null,        // XML element that represents a row (auto-detect if null)
			'skip_empty'     => true,        // Skip empty rows
			'max_rows'       => 10000,       // Maximum rows to import
			'trim_values'    => true,        // Trim whitespace from values
			'flatten_nested' => true,        // Flatten nested elements
		);

		$options = array_merge( $defaults, $options );

		// Validate file exists.
		if ( ! file_exists( $file_path ) ) {
			throw new \Exception( __( 'XML file not found.', 'a-tables-charts' ) );
		}

		// Suppress XML errors and handle them manually.
		libxml_use_internal_errors( true );

		try {
			// Load XML file.
			$xml = simplexml_load_file( $file_path );

			if ( false === $xml ) {
				$errors = libxml_get_errors();
				$error_messages = array();
				foreach ( $errors as $error ) {
					$error_messages[] = trim( $error->message );
				}
				libxml_clear_errors();
				
				throw new \Exception(
					sprintf(
						/* translators: %s: error messages */
						__( 'Failed to parse XML: %s', 'a-tables-charts' ),
						implode( ', ', $error_messages )
					)
				);
			}

			// Auto-detect row element if not specified.
			if ( empty( $options['row_element'] ) ) {
				$options['row_element'] = $this->detect_row_element( $xml );
			}

			// Get row elements.
			$row_elements = $xml->xpath( '//' . $options['row_element'] );

			if ( empty( $row_elements ) ) {
				throw new \Exception(
					sprintf(
						/* translators: %s: element name */
						__( 'No "%s" elements found in XML.', 'a-tables-charts' ),
						$options['row_element']
					)
				);
			}

			// Limit rows.
			if ( count( $row_elements ) > $options['max_rows'] ) {
				$row_elements = array_slice( $row_elements, 0, $options['max_rows'] );
			}

			// Extract headers from first row.
			$headers = $this->extract_headers( $row_elements[0], $options );

			// Process data rows.
			$data = array();
			foreach ( $row_elements as $row_element ) {
				$row = $this->extract_row_data( $row_element, $headers, $options );
				
				// Skip empty rows if needed.
				if ( $options['skip_empty'] && empty( array_filter( $row ) ) ) {
					continue;
				}
				
				$data[] = $row;
			}

			return array(
				'headers'      => $headers,
				'data'         => $data,
				'row_count'    => count( $data ),
				'column_count' => count( $headers ),
				'row_element'  => $options['row_element'],
			);

		} catch ( \Exception $e ) {
			throw new \Exception(
				sprintf(
					/* translators: %s: error message */
					__( 'Failed to parse XML file: %s', 'a-tables-charts' ),
					$e->getMessage()
				)
			);
		} finally {
			// Restore error handling.
			libxml_use_internal_errors( false );
		}
	}

	/**
	 * Detect the row element name from XML structure
	 *
	 * Looks for the most common child element of the root.
	 *
	 * @param \SimpleXMLElement $xml XML object.
	 * @return string Row element name
	 */
	private function detect_row_element( $xml ) {
		// Get all child elements of the root.
		$children = $xml->children();
		
		if ( count( $children ) === 0 ) {
			throw new \Exception( __( 'XML file appears to be empty.', 'a-tables-charts' ) );
		}

		// Get the name of the first child element.
		// This assumes all data rows are siblings with the same name.
		$first_child = $children[0];
		return $first_child->getName();
	}

	/**
	 * Extract headers from XML element
	 *
	 * @param \SimpleXMLElement $element XML element.
	 * @param array             $options Parser options.
	 * @return array Headers
	 */
	private function extract_headers( $element, $options ) {
		$headers = array();
		
		if ( $options['flatten_nested'] ) {
			// Flatten nested elements.
			$headers = $this->get_all_element_names( $element );
		} else {
			// Only direct children.
			foreach ( $element->children() as $child ) {
				$headers[] = $child->getName();
			}
		}

		// Make headers unique.
		$headers = $this->make_unique_headers( $headers );

		return $headers;
	}

	/**
	 * Get all element names recursively (for nested XML)
	 *
	 * @param \SimpleXMLElement $element XML element.
	 * @param string            $prefix  Prefix for nested elements.
	 * @return array Element names
	 */
	private function get_all_element_names( $element, $prefix = '' ) {
		$names = array();
		
		foreach ( $element->children() as $child ) {
			$name = $child->getName();
			$full_name = $prefix ? $prefix . '.' . $name : $name;
			
			// Check if element has children (nested).
			if ( $child->children()->count() > 0 ) {
				// Recursively get nested elements.
				$nested = $this->get_all_element_names( $child, $full_name );
				$names = array_merge( $names, $nested );
			} else {
				// Leaf node - add to headers.
				$names[] = $full_name;
			}
		}
		
		return $names;
	}

	/**
	 * Extract row data from XML element
	 *
	 * @param \SimpleXMLElement $element XML element.
	 * @param array             $headers Headers.
	 * @param array             $options Parser options.
	 * @return array Row data
	 */
	private function extract_row_data( $element, $headers, $options ) {
		$row = array();
		
		if ( $options['flatten_nested'] ) {
			// Flatten nested structure.
			$flat_data = $this->flatten_element( $element );
			
			// Map to headers.
			foreach ( $headers as $header ) {
				$value = isset( $flat_data[ $header ] ) ? $flat_data[ $header ] : '';
				
				if ( $options['trim_values'] ) {
					$value = trim( $value );
				}
				
				$row[] = $value;
			}
		} else {
			// Only direct children.
			foreach ( $headers as $header ) {
				$child = $element->{$header};
				$value = (string) $child;
				
				if ( $options['trim_values'] ) {
					$value = trim( $value );
				}
				
				$row[] = $value;
			}
		}
		
		return $row;
	}

	/**
	 * Flatten XML element to key-value pairs
	 *
	 * @param \SimpleXMLElement $element XML element.
	 * @param string            $prefix  Prefix for nested elements.
	 * @return array Flattened data
	 */
	private function flatten_element( $element, $prefix = '' ) {
		$data = array();
		
		foreach ( $element->children() as $child ) {
			$name = $child->getName();
			$full_name = $prefix ? $prefix . '.' . $name : $name;
			
			// Check if element has children (nested).
			if ( $child->children()->count() > 0 ) {
				// Recursively flatten nested elements.
				$nested = $this->flatten_element( $child, $full_name );
				$data = array_merge( $data, $nested );
			} else {
				// Leaf node - add value.
				$data[ $full_name ] = (string) $child;
			}
		}
		
		return $data;
	}

	/**
	 * Make headers unique by adding numbers to duplicates
	 *
	 * @param array $headers Array of headers.
	 * @return array Unique headers
	 */
	private function make_unique_headers( $headers ) {
		$unique = array();
		$counts = array();
		
		foreach ( $headers as $header ) {
			$original = $header;
			
			if ( ! isset( $counts[ $header ] ) ) {
				$counts[ $header ] = 1;
			} else {
				$counts[ $header ]++;
				$header = $original . ' ' . $counts[ $original ];
			}
			
			$unique[] = $header;
		}
		
		return $unique;
	}

	/**
	 * Validate XML file
	 *
	 * @param string $file_path Path to file.
	 * @return array Validation result
	 */
	public function validate( $file_path ) {
		$errors = array();

		if ( ! file_exists( $file_path ) ) {
			$errors[] = __( 'File does not exist.', 'a-tables-charts' );
			return array(
				'valid'  => false,
				'errors' => $errors,
			);
		}

		// Check file extension.
		$extension = strtolower( pathinfo( $file_path, PATHINFO_EXTENSION ) );
		if ( 'xml' !== $extension ) {
			$errors[] = __( 'File must be .xml format.', 'a-tables-charts' );
		}

		// Check file size (max 10MB).
		$max_size = 10 * 1024 * 1024; // 10MB in bytes.
		if ( filesize( $file_path ) > $max_size ) {
			$errors[] = __( 'File size exceeds 10MB limit.', 'a-tables-charts' );
		}

		// Try to parse the XML.
		libxml_use_internal_errors( true );
		
		try {
			$xml = simplexml_load_file( $file_path );
			
			if ( false === $xml ) {
				$xml_errors = libxml_get_errors();
				foreach ( $xml_errors as $error ) {
					$errors[] = trim( $error->message );
				}
				libxml_clear_errors();
			} else {
				// Check if XML has any data.
				$children = $xml->children();
				if ( count( $children ) === 0 ) {
					$errors[] = __( 'XML file appears to be empty.', 'a-tables-charts' );
				}
			}
		} catch ( \Exception $e ) {
			$errors[] = sprintf(
				/* translators: %s: error message */
				__( 'Invalid XML file: %s', 'a-tables-charts' ),
				$e->getMessage()
			);
		} finally {
			libxml_use_internal_errors( false );
		}

		return array(
			'valid'  => empty( $errors ),
			'errors' => $errors,
		);
	}

	/**
	 * Get XML structure information
	 *
	 * @param string $file_path Path to XML file.
	 * @return array XML structure info
	 */
	public function get_structure( $file_path ) {
		if ( ! file_exists( $file_path ) ) {
			throw new \Exception( __( 'XML file not found.', 'a-tables-charts' ) );
		}

		libxml_use_internal_errors( true );

		try {
			$xml = simplexml_load_file( $file_path );
			
			if ( false === $xml ) {
				throw new \Exception( __( 'Invalid XML file.', 'a-tables-charts' ) );
			}

			$root_name = $xml->getName();
			$children = $xml->children();
			
			$child_elements = array();
			foreach ( $children as $child ) {
				$child_name = $child->getName();
				if ( ! isset( $child_elements[ $child_name ] ) ) {
					$child_elements[ $child_name ] = 0;
				}
				$child_elements[ $child_name ]++;
			}

			return array(
				'root_element'   => $root_name,
				'child_elements' => $child_elements,
				'total_rows'     => count( $children ),
			);

		} finally {
			libxml_use_internal_errors( false );
		}
	}
}

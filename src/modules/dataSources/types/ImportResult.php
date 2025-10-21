<?php
/**
 * Import Result Type
 *
 * Represents the result of a data import operation.
 *
 * @package ATablesCharts\DataSources\Types
 * @since 1.0.0
 */

namespace ATablesCharts\DataSources\Types;

/**
 * ImportResult Class
 *
 * Contains the results of a data import operation.
 */
class ImportResult {

	/**
	 * Whether the import was successful
	 *
	 * @var bool
	 */
	public $success;

	/**
	 * Success or error message
	 *
	 * @var string
	 */
	public $message;

	/**
	 * Imported data (array of rows)
	 *
	 * @var array
	 */
	public $data;

	/**
	 * Column headers
	 *
	 * @var array
	 */
	public $headers;

	/**
	 * Number of rows imported
	 *
	 * @var int
	 */
	public $row_count;

	/**
	 * Number of columns
	 *
	 * @var int
	 */
	public $column_count;

	/**
	 * Any warnings encountered during import
	 *
	 * @var array
	 */
	public $warnings;

	/**
	 * Additional metadata about the import
	 *
	 * @var array
	 */
	public $metadata;

	/**
	 * Constructor
	 *
	 * @param bool   $success Whether import succeeded.
	 * @param string $message Success or error message.
	 * @param array  $data    Imported data.
	 * @param array  $headers Column headers.
	 */
	public function __construct( $success = false, $message = '', $data = array(), $headers = array() ) {
		$this->success       = $success;
		$this->message       = $message;
		$this->data          = $data;
		$this->headers       = $headers;
		$this->row_count     = count( $data );
		$this->column_count  = ! empty( $headers ) ? count( $headers ) : 0;
		$this->warnings      = array();
		$this->metadata      = array();
	}

	/**
	 * Create a successful import result
	 *
	 * @param array  $data    Imported data.
	 * @param array  $headers Column headers.
	 * @param string $message Success message.
	 * @return ImportResult
	 */
	public static function success( $data, $headers, $message = '' ) {
		if ( empty( $message ) ) {
			$message = sprintf(
				/* translators: 1: Row count, 2: Column count */
				__( 'Successfully imported %1$d rows with %2$d columns.', 'a-tables-charts' ),
				count( $data ),
				count( $headers )
			);
		}

		return new self( true, $message, $data, $headers );
	}

	/**
	 * Create a failed import result
	 *
	 * @param string $message Error message.
	 * @return ImportResult
	 */
	public static function error( $message ) {
		return new self( false, $message, array(), array() );
	}

	/**
	 * Add a warning to the result
	 *
	 * @param string $warning Warning message.
	 */
	public function add_warning( $warning ) {
		$this->warnings[] = $warning;
	}

	/**
	 * Add metadata
	 *
	 * @param string $key   Metadata key.
	 * @param mixed  $value Metadata value.
	 */
	public function add_metadata( $key, $value ) {
		$this->metadata[ $key ] = $value;
	}

	/**
	 * Check if there are any warnings
	 *
	 * @return bool True if warnings exist
	 */
	public function has_warnings() {
		return ! empty( $this->warnings );
	}

	/**
	 * Get a summary of the import
	 *
	 * @return string Summary text
	 */
	public function get_summary() {
		if ( ! $this->success ) {
			return $this->message;
		}

		$summary = sprintf(
			/* translators: 1: Row count, 2: Column count */
			__( 'Imported %1$d rows with %2$d columns.', 'a-tables-charts' ),
			$this->row_count,
			$this->column_count
		);

		if ( $this->has_warnings() ) {
			$summary .= ' ' . sprintf(
				/* translators: %d: Warning count */
				__( '%d warning(s) encountered.', 'a-tables-charts' ),
				count( $this->warnings )
			);
		}

		return $summary;
	}

	/**
	 * Convert to array for JSON response
	 *
	 * @return array
	 */
	public function to_array() {
		return array(
			'success'       => $this->success,
			'message'       => $this->message,
			'data'          => $this->data,
			'headers'       => $this->headers,
			'row_count'     => $this->row_count,
			'column_count'  => $this->column_count,
			'warnings'      => $this->warnings,
			'metadata'      => $this->metadata,
		);
	}
}

<?php
/**
 * Logger Utility
 *
 * Handles logging throughout the plugin.
 * Provides structured logging with different levels.
 *
 * @package ATablesCharts\Shared\Utils
 * @since 1.0.0
 */

namespace ATablesCharts\Shared\Utils;

/**
 * Logger Class
 *
 * Responsibilities:
 * - Log messages with different severity levels
 * - Format log messages consistently
 * - Support context data
 * - Control logging based on WP_DEBUG
 * - Sanitize sensitive data before logging
 * - Provide static helper methods for easy access
 *
 * SECURITY: Only logs when WP_DEBUG is enabled to prevent
 * sensitive information exposure in production environments.
 */
class Logger {

	/**
	 * Log levels
	 */
	const LEVEL_DEBUG   = 'debug';
	const LEVEL_INFO    = 'info';
	const LEVEL_WARNING = 'warning';
	const LEVEL_ERROR   = 'error';

	/**
	 * Singleton instance
	 *
	 * @var Logger
	 */
	private static $instance = null;

	/**
	 * Whether logging is enabled
	 *
	 * @var bool
	 */
	private $enabled;

	/**
	 * Log file path
	 *
	 * @var string
	 */
	private $log_file;

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->enabled = defined( 'WP_DEBUG' ) && WP_DEBUG;

		$upload_dir     = wp_upload_dir();
		$this->log_file = $upload_dir['basedir'] . '/atables/debug.log';
	}

	/**
	 * Get singleton instance
	 *
	 * @since 1.0.5
	 * @return Logger Logger instance
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Log a debug message
	 *
	 * Use for detailed debugging information.
	 *
	 * @since 1.0.0
	 * @param string $message Log message.
	 * @param array  $context Additional context data.
	 */
	public function debug( $message, $context = array() ) {
		$this->log( self::LEVEL_DEBUG, $message, $context );
	}

	/**
	 * Log an info message
	 *
	 * Use for general informational messages.
	 *
	 * @since 1.0.0
	 * @param string $message Log message.
	 * @param array  $context Additional context data.
	 */
	public function info( $message, $context = array() ) {
		$this->log( self::LEVEL_INFO, $message, $context );
	}

	/**
	 * Log a warning message
	 *
	 * Use for warning conditions.
	 *
	 * @since 1.0.0
	 * @param string $message Log message.
	 * @param array  $context Additional context data.
	 */
	public function warning( $message, $context = array() ) {
		$this->log( self::LEVEL_WARNING, $message, $context );
	}

	/**
	 * Log an error message
	 *
	 * Use for error conditions.
	 *
	 * @since 1.0.0
	 * @param string $message Log message.
	 * @param array  $context Additional context data.
	 */
	public function error( $message, $context = array() ) {
		$this->log( self::LEVEL_ERROR, $message, $context );
	}

	/**
	 * Log a message with given level
	 *
	 * @since 1.0.0
	 * @param string $level   Log level.
	 * @param string $message Log message.
	 * @param array  $context Additional context data.
	 */
	private function log( $level, $message, $context = array() ) {
		// Skip if logging is disabled.
		if ( ! $this->enabled ) {
			return;
		}

		// Format the log entry.
		$log_entry = $this->format_log_entry( $level, $message, $context );

		// Write to WordPress debug log if enabled.
		if ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
			error_log( $log_entry );
		}

		// Write to plugin-specific log file.
		$this->write_to_file( $log_entry );
	}

	/**
	 * Format a log entry
	 *
	 * @since 1.0.0
	 * @param string $level   Log level.
	 * @param string $message Log message.
	 * @param array  $context Additional context data.
	 * @return string Formatted log entry
	 */
	private function format_log_entry( $level, $message, $context ) {
		$timestamp = current_time( 'Y-m-d H:i:s' );
		$level_str = strtoupper( $level );
		
		$entry = sprintf(
			'[%s] [%s] %s',
			$timestamp,
			$level_str,
			$message
		);

		// Add context if provided.
		if ( ! empty( $context ) ) {
			$entry .= ' | Context: ' . wp_json_encode( $context );
		}

		return $entry;
	}

	/**
	 * Write log entry to file
	 *
	 * @since 1.0.0
	 * @param string $entry Log entry to write.
	 */
	private function write_to_file( $entry ) {
		// Ensure directory exists.
		$log_dir = dirname( $this->log_file );
		if ( ! file_exists( $log_dir ) ) {
			wp_mkdir_p( $log_dir );
		}

		// Write to file.
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen
		$handle = @fopen( $this->log_file, 'a' );
		if ( $handle ) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite
			fwrite( $handle, $entry . PHP_EOL );
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
			fclose( $handle );
		}
	}

	/**
	 * Clear log file
	 *
	 * Useful for cleaning up old logs.
	 *
	 * @since 1.0.0
	 * @return bool True on success, false on failure
	 */
	public function clear_log() {
		if ( file_exists( $this->log_file ) ) {
			return wp_delete_file( $this->log_file );
		}
		return true;
	}

	/**
	 * Get log file path
	 *
	 * @since 1.0.0
	 * @return string Log file path
	 */
	public function get_log_file() {
		return $this->log_file;
	}

	/**
	 * Get log file size
	 *
	 * @since 1.0.0
	 * @return int|false File size in bytes or false if file doesn't exist
	 */
	public function get_log_size() {
		if ( file_exists( $this->log_file ) ) {
			return filesize( $this->log_file );
		}
		return false;
	}

	/**
	 * Check if logging is enabled
	 *
	 * @since 1.0.0
	 * @return bool True if enabled, false otherwise
	 */
	public function is_enabled() {
		return $this->enabled;
	}

	/**
	 * Enable logging
	 *
	 * @since 1.0.0
	 */
	public function enable() {
		$this->enabled = true;
	}

	/**
	 * Disable logging
	 *
	 * @since 1.0.0
	 */
	public function disable() {
		$this->enabled = false;
	}

	/**
	 * Sanitize data before logging
	 *
	 * Removes or masks sensitive information that should not be logged.
	 *
	 * @since 1.0.5
	 * @param mixed $data Data to sanitize.
	 * @return mixed Sanitized data
	 */
	private function sanitize_data( $data ) {
		// If it's a string, mask common sensitive patterns
		if ( is_string( $data ) ) {
			// Mask SQL queries with potential sensitive data
			$data = preg_replace( '/password\s*=\s*[\'"][^\'"]*[\'"]?/i', 'password=***', $data );
			$data = preg_replace( '/api[_-]?key\s*[=:]\s*[\'"]?[\w-]+[\'"]?/i', 'api_key=***', $data );
			$data = preg_replace( '/token\s*[=:]\s*[\'"]?[\w-]+[\'"]?/i', 'token=***', $data );

			// Limit string length to prevent log bloat
			if ( strlen( $data ) > 1000 ) {
				$data = substr( $data, 0, 1000 ) . '... [truncated]';
			}
		}

		// If it's an array, recursively sanitize
		if ( is_array( $data ) ) {
			foreach ( $data as $key => $value ) {
				// Mask sensitive keys entirely
				if ( in_array( strtolower( $key ), array( 'password', 'api_key', 'secret', 'token', 'auth' ), true ) ) {
					$data[ $key ] = '***';
				} else {
					$data[ $key ] = $this->sanitize_data( $value );
				}
			}
		}

		return $data;
	}

	/**
	 * Sanitize database error messages
	 *
	 * Removes potentially sensitive information from database errors.
	 *
	 * @since 1.0.5
	 * @param string $error Database error message.
	 * @return string Sanitized error message
	 */
	private function sanitize_db_error( $error ) {
		// Remove table prefixes that might expose database structure
		$error = preg_replace( '/wp_[\w]+/', 'wp_***', $error );

		// Remove actual values from error messages
		$error = preg_replace( '/\'[^\']{20,}\'/', '\'***\'', $error );

		return $error;
	}

	// ========================================
	// STATIC HELPER METHODS
	// ========================================

	/**
	 * Static helper: Log debug message
	 *
	 * Only logs when WP_DEBUG is enabled.
	 *
	 * @since 1.0.5
	 * @param string $message Log message.
	 * @param array  $context Additional context data.
	 */
	public static function log_debug( $message, $context = array() ) {
		self::instance()->debug( $message, $context );
	}

	/**
	 * Static helper: Log info message
	 *
	 * Only logs when WP_DEBUG is enabled.
	 *
	 * @since 1.0.5
	 * @param string $message Log message.
	 * @param array  $context Additional context data.
	 */
	public static function log_info( $message, $context = array() ) {
		self::instance()->info( $message, $context );
	}

	/**
	 * Static helper: Log warning message
	 *
	 * Only logs when WP_DEBUG is enabled.
	 *
	 * @since 1.0.5
	 * @param string $message Log message.
	 * @param array  $context Additional context data.
	 */
	public static function log_warning( $message, $context = array() ) {
		self::instance()->warning( $message, $context );
	}

	/**
	 * Static helper: Log error message
	 *
	 * Only logs when WP_DEBUG is enabled.
	 *
	 * @since 1.0.5
	 * @param string $message Log message.
	 * @param array  $context Additional context data.
	 */
	public static function log_error( $message, $context = array() ) {
		self::instance()->error( $message, $context );
	}

	/**
	 * Static helper: Log database error
	 *
	 * Sanitizes database errors before logging.
	 * Only logs when WP_DEBUG is enabled.
	 *
	 * @since 1.0.5
	 * @param string $message Error message.
	 * @param string $db_error Database error from wpdb.
	 * @param string $query Optional SQL query.
	 */
	public static function log_db_error( $message, $db_error = '', $query = '' ) {
		$logger = self::instance();
		if ( ! $logger->is_enabled() ) {
			return;
		}

		$context = array();

		if ( ! empty( $db_error ) ) {
			$context['db_error'] = $logger->sanitize_db_error( $db_error );
		}

		if ( ! empty( $query ) ) {
			// Limit query length and sanitize
			$context['query'] = $logger->sanitize_data( $query );
		}

		$logger->error( $message, $context );
	}

	/**
	 * Static helper: Check if logging is enabled
	 *
	 * @since 1.0.5
	 * @return bool True if enabled, false otherwise
	 */
	public static function is_debug_enabled() {
		return self::instance()->is_enabled();
	}
}

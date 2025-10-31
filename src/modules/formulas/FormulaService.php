<?php
/**
 * Formula Service
 *
 * Handles formula calculations for table cells
 *
 * @package ATablesCharts\Formulas\Services
 * @since 1.0.0
 */

namespace ATablesCharts\Formulas\Services;

/**
 * FormulaService Class
 *
 * Responsibilities:
 * - Parse formulas
 * - Calculate results
 * - Support cell references
 * - Handle built-in functions
 */
class FormulaService {

	/**
	 * Supported functions
	 *
	 * @var array
	 */
	private $functions = array( 'SUM', 'AVERAGE', 'AVG', 'MIN', 'MAX', 'COUNT', 'IF', 'ROUND', 'ABS', 'MEDIAN', 'PRODUCT', 'POWER', 'POW', 'SQRT', 'CONCAT', 'CONCATENATE' );

	/**
	 * Process formulas in table data
	 *
	 * @param array $data     Table data.
	 * @param array $headers  Table headers.
	 * @param array $formulas Formula configurations.
	 * @return array Processed data
	 */
	public function process_formulas( $data, $headers, $formulas ) {
		if ( empty( $formulas ) || empty( $data ) ) {
			return $data;
		}

		$processed_data = $data;

		foreach ( $formulas as $formula_config ) {
			$processed_data = $this->apply_formula( $processed_data, $headers, $formula_config );
		}

		return $processed_data;
	}

	/**
	 * Apply a single formula
	 *
	 * @param array $data           Table data.
	 * @param array $headers        Table headers.
	 * @param array $formula_config Formula configuration.
	 * @return array Updated data
	 */
	private function apply_formula( $data, $headers, $formula_config ) {
		$target_row = isset( $formula_config['target_row'] ) ? intval( $formula_config['target_row'] ) : -1;
		$target_col = isset( $formula_config['target_col'] ) ? $formula_config['target_col'] : '';
		$formula = isset( $formula_config['formula'] ) ? $formula_config['formula'] : '';

		if ( empty( $formula ) ) {
			return $data;
		}

		// Calculate result
		$result = $this->calculate_formula( $formula, $data, $headers, $target_row );

		// Apply result to target cell
		if ( $target_row === -1 ) {
			// Add as new row
			$new_row = array();
			foreach ( $headers as $header ) {
				$new_row[ $header ] = ( $header === $target_col ) ? $result : '';
			}
			$data[] = $new_row;
		} elseif ( isset( $data[ $target_row ] ) && isset( $data[ $target_row ][ $target_col ] ) ) {
			// Update existing cell
			$data[ $target_row ][ $target_col ] = $result;
		}

		return $data;
	}

	/**
	 * Calculate formula result
	 *
	 * @param string $formula    Formula string.
	 * @param array  $data       Table data.
	 * @param array  $headers    Table headers.
	 * @param int    $current_row Current row index.
	 * @return mixed Calculated result
	 */
	public function calculate_formula( $formula, $data, $headers, $current_row = -1 ) {
		// Remove leading = if present
		$formula = ltrim( $formula, '=' );
		$formula = trim( $formula );

		// Replace cell references with values
		$formula = $this->replace_cell_references( $formula, $data, $headers, $current_row );

		// Parse and evaluate formula
		try {
			$result = $this->evaluate_expression( $formula );
			return $result;
		} catch ( \Exception $e ) {
			return '#ERROR: ' . $e->getMessage();
		}
	}

	/**
	 * Replace cell references with actual values
	 *
	 * @param string $formula     Formula string.
	 * @param array  $data        Table data.
	 * @param array  $headers     Table headers.
	 * @param int    $current_row Current row index.
	 * @return string Formula with values
	 */
	private function replace_cell_references( $formula, $data, $headers, $current_row ) {
		// Pattern: A1, B2, C10, etc.
		$pattern = '/([A-Z]+)(\d+)/';
		
		$formula = preg_replace_callback(
			$pattern,
			function( $matches ) use ( $data, $headers, $current_row ) {
				$col_letter = $matches[1];
				$row_number = intval( $matches[2] );
				
				// Convert column letter to index (A=0, B=1, etc.)
				$col_index = $this->column_letter_to_index( $col_letter );
				
				// Row is 1-based in formula, 0-based in array
				$row_index = $row_number - 1;
				
				// Get value
				$value = $this->get_cell_value( $data, $headers, $row_index, $col_index );
				
				return is_numeric( $value ) ? $value : 0;
			},
			$formula
		);

		// Pattern: A:A (entire column), 1:1 (entire row)
		$formula = $this->replace_range_references( $formula, $data, $headers );

		return $formula;
	}

	/**
	 * Replace range references (A:A, A1:A10, etc.)
	 *
	 * @param string $formula Formula string.
	 * @param array  $data    Table data.
	 * @param array  $headers Table headers.
	 * @return string Formula with ranges replaced
	 */
	private function replace_range_references( $formula, $data, $headers ) {
		// Pattern: A:A (entire column)
		$formula = preg_replace_callback(
			'/([A-Z]+):([A-Z]+)/',
			function( $matches ) use ( $data, $headers ) {
				$col_letter = $matches[1];
				$col_index = $this->column_letter_to_index( $col_letter );
				
				$values = array();
				foreach ( $data as $row ) {
					$row_values = array_values( $row );
					if ( isset( $row_values[ $col_index ] ) && is_numeric( $row_values[ $col_index ] ) ) {
						$values[] = $row_values[ $col_index ];
					}
				}
				
				return implode( ',', $values );
			},
			$formula
		);

		// Pattern: A1:A10 (cell range)
		$formula = preg_replace_callback(
			'/([A-Z]+)(\d+):([A-Z]+)(\d+)/',
			function( $matches ) use ( $data, $headers ) {
				$start_col = $this->column_letter_to_index( $matches[1] );
				$start_row = intval( $matches[2] ) - 1;
				$end_col = $this->column_letter_to_index( $matches[3] );
				$end_row = intval( $matches[4] ) - 1;
				
				$values = array();
				for ( $row = $start_row; $row <= $end_row; $row++ ) {
					for ( $col = $start_col; $col <= $end_col; $col++ ) {
						$value = $this->get_cell_value( $data, $headers, $row, $col );
						if ( is_numeric( $value ) ) {
							$values[] = $value;
						}
					}
				}
				
				return implode( ',', $values );
			},
			$formula
		);

		return $formula;
	}

	/**
	 * Get cell value by row and column index
	 *
	 * @param array $data      Table data.
	 * @param array $headers   Table headers.
	 * @param int   $row_index Row index.
	 * @param int   $col_index Column index.
	 * @return mixed Cell value
	 */
	private function get_cell_value( $data, $headers, $row_index, $col_index ) {
		if ( ! isset( $data[ $row_index ] ) ) {
			return 0;
		}

		$row = array_values( $data[ $row_index ] );
		
		if ( ! isset( $row[ $col_index ] ) ) {
			return 0;
		}

		return $row[ $col_index ];
	}

	/**
	 * Convert column letter to index (A=0, B=1, Z=25, AA=26)
	 *
	 * @param string $letter Column letter.
	 * @return int Column index
	 */
	private function column_letter_to_index( $letter ) {
		$letter = strtoupper( $letter );
		$length = strlen( $letter );
		$index = 0;
		
		for ( $i = 0; $i < $length; $i++ ) {
			$index = $index * 26 + ( ord( $letter[ $i ] ) - ord( 'A' ) + 1 );
		}
		
		return $index - 1;
	}

	/**
	 * Convert column index to letter (0=A, 1=B, 25=Z, 26=AA)
	 *
	 * @param int $index Column index.
	 * @return string Column letter
	 */
	public function column_index_to_letter( $index ) {
		$letter = '';
		$index++;
		
		while ( $index > 0 ) {
			$remainder = ( $index - 1 ) % 26;
			$letter = chr( 65 + $remainder ) . $letter;
			$index = intval( ( $index - $remainder ) / 26 );
		}
		
		return $letter;
	}

	/**
	 * Evaluate mathematical expression with functions
	 *
	 * @param string $expression Expression to evaluate.
	 * @return mixed Result
	 */
	private function evaluate_expression( $expression ) {
		// Handle functions
		$expression = $this->evaluate_functions( $expression );
		
		// Sanitize expression - only allow numbers, operators, parentheses
		if ( ! preg_match( '/^[\d\s\+\-\*\/\(\)\.\,]+$/', $expression ) ) {
			throw new \Exception( 'Invalid expression' );
		}
		
		// Replace commas with nothing (from function results)
		$expression = str_replace( ',', '', $expression );
		
		// Evaluate safely
		try {
			// Use a safe evaluation method
			$result = $this->safe_eval( $expression );
			return $result;
		} catch ( \Exception $e ) {
			throw new \Exception( 'Calculation error' );
		}
	}

	/**
	 * Evaluate functions in expression
	 *
	 * @param string $expression Expression with functions.
	 * @return string Expression with functions replaced
	 */
	private function evaluate_functions( $expression ) {
		// Match function calls: FUNCTION(args)
		$pattern = '/([A-Z]+)\(([^\)]+)\)/';
		
		while ( preg_match( $pattern, $expression, $matches ) ) {
			$function = strtoupper( $matches[1] );
			$args_str = $matches[2];
			
			// Parse arguments
			$args = array_map( 'trim', explode( ',', $args_str ) );
			$numeric_args = array_filter( array_map( 'floatval', $args ) );
			
			// Calculate function result
			$result = $this->calculate_function( $function, $numeric_args, $args_str );
			
			// Replace function call with result
			$expression = str_replace( $matches[0], $result, $expression );
		}
		
		return $expression;
	}

	/**
	 * Calculate function result
	 *
	 * @param string $function     Function name.
	 * @param array  $numeric_args Numeric arguments.
	 * @param string $args_str     Original arguments string.
	 * @return mixed Function result
	 */
	private function calculate_function( $function, $numeric_args, $args_str ) {
		switch ( $function ) {
			case 'SUM':
				return array_sum( $numeric_args );

			case 'AVERAGE':
			case 'AVG':
				return count( $numeric_args ) > 0 ? array_sum( $numeric_args ) / count( $numeric_args ) : 0;

			case 'MIN':
				return count( $numeric_args ) > 0 ? min( $numeric_args ) : 0;

			case 'MAX':
				return count( $numeric_args ) > 0 ? max( $numeric_args ) : 0;

			case 'COUNT':
				return count( $numeric_args );

			case 'ROUND':
				$value = isset( $numeric_args[0] ) ? $numeric_args[0] : 0;
				$decimals = isset( $numeric_args[1] ) ? intval( $numeric_args[1] ) : 0;
				return round( $value, $decimals );

			case 'ABS':
				return isset( $numeric_args[0] ) ? abs( $numeric_args[0] ) : 0;

			case 'IF':
				// IF(condition, true_value, false_value)
				$parts = array_map( 'trim', explode( ',', $args_str ) );
				if ( count( $parts ) >= 3 ) {
					$condition = $this->safe_eval( $parts[0] );
					return $condition ? $this->safe_eval( $parts[1] ) : $this->safe_eval( $parts[2] );
				}
				return 0;

			case 'MEDIAN':
				if ( count( $numeric_args ) === 0 ) {
					return 0;
				}
				sort( $numeric_args );
				$count = count( $numeric_args );
				$middle = floor( $count / 2 );
				if ( $count % 2 === 0 ) {
					// Even number of values - average the two middle values
					return ( $numeric_args[ $middle - 1 ] + $numeric_args[ $middle ] ) / 2;
				}
				// Odd number of values - return the middle value
				return $numeric_args[ $middle ];

			case 'PRODUCT':
				return count( $numeric_args ) > 0 ? array_product( $numeric_args ) : 0;

			case 'POWER':
			case 'POW':
				$base = isset( $numeric_args[0] ) ? $numeric_args[0] : 0;
				$exponent = isset( $numeric_args[1] ) ? $numeric_args[1] : 1;
				return pow( $base, $exponent );

			case 'SQRT':
				$value = isset( $numeric_args[0] ) ? $numeric_args[0] : 0;
				if ( $value < 0 ) {
					return 0; // Return 0 for negative numbers to avoid errors
				}
				return sqrt( $value );

			case 'CONCAT':
			case 'CONCATENATE':
				// For text concatenation, use original args (not numeric_args)
				$parts = array_map( 'trim', explode( ',', $args_str ) );
				$text_parts = array();
				foreach ( $parts as $part ) {
					// Remove quotes if present
					$cleaned = trim( $part, '"\'' );
					$text_parts[] = $cleaned;
				}
				return implode( '', $text_parts );

			default:
				return 0;
		}
	}

	/**
	 * Safe evaluation of mathematical expression
	 *
	 * @param string $expression Expression to evaluate.
	 * @return mixed Result
	 */
	private function safe_eval( $expression ) {
		$expression = trim( $expression );
		
		// Remove spaces
		$expression = str_replace( ' ', '', $expression );
		
		// Check if it's just a number
		if ( is_numeric( $expression ) ) {
			return floatval( $expression );
		}
		
		// Use eval with extreme caution - validate first
		if ( ! preg_match( '/^[\d\+\-\*\/\(\)\.]+$/', $expression ) ) {
			throw new \Exception( 'Invalid expression' );
		}
		
		// Evaluate (safe because we validated)
		$result = @eval( "return $expression;" );
		
		if ( $result === false ) {
			throw new \Exception( 'Evaluation failed' );
		}
		
		return $result;
	}

	/**
	 * Get available functions
	 *
	 * @return array Function descriptions
	 */
	public static function get_available_functions() {
		return array(
			'SUM' => array(
				'label'       => 'SUM',
				'description' => __( 'Sum of all values', 'a-tables-charts' ),
				'syntax'      => 'SUM(A1:A10) or SUM(A1,A2,A3)',
				'example'     => '=SUM(B2:B10)',
			),
			'AVERAGE' => array(
				'label'       => 'AVERAGE',
				'description' => __( 'Average of values', 'a-tables-charts' ),
				'syntax'      => 'AVERAGE(A1:A10)',
				'example'     => '=AVERAGE(C2:C10)',
			),
			'MIN' => array(
				'label'       => 'MIN',
				'description' => __( 'Minimum value', 'a-tables-charts' ),
				'syntax'      => 'MIN(A1:A10)',
				'example'     => '=MIN(D2:D10)',
			),
			'MAX' => array(
				'label'       => 'MAX',
				'description' => __( 'Maximum value', 'a-tables-charts' ),
				'syntax'      => 'MAX(A1:A10)',
				'example'     => '=MAX(E2:E10)',
			),
			'COUNT' => array(
				'label'       => 'COUNT',
				'description' => __( 'Count of values', 'a-tables-charts' ),
				'syntax'      => 'COUNT(A1:A10)',
				'example'     => '=COUNT(F2:F10)',
			),
			'ROUND' => array(
				'label'       => 'ROUND',
				'description' => __( 'Round number', 'a-tables-charts' ),
				'syntax'      => 'ROUND(value, decimals)',
				'example'     => '=ROUND(A1, 2)',
			),
			'ABS' => array(
				'label'       => 'ABS',
				'description' => __( 'Absolute value', 'a-tables-charts' ),
				'syntax'      => 'ABS(value)',
				'example'     => '=ABS(A1)',
			),
			'IF' => array(
				'label'       => 'IF',
				'description' => __( 'Conditional value', 'a-tables-charts' ),
				'syntax'      => 'IF(condition, true_value, false_value)',
				'example'     => '=IF(A1>100, "High", "Low")',
			),
			'MEDIAN' => array(
				'label'       => 'MEDIAN',
				'description' => __( 'Middle value (statistical median)', 'a-tables-charts' ),
				'syntax'      => 'MEDIAN(A1:A10)',
				'example'     => '=MEDIAN(B2:B10)',
			),
			'PRODUCT' => array(
				'label'       => 'PRODUCT',
				'description' => __( 'Multiply all values', 'a-tables-charts' ),
				'syntax'      => 'PRODUCT(A1:A10) or PRODUCT(A1,A2,A3)',
				'example'     => '=PRODUCT(B2:B4)',
			),
			'POWER' => array(
				'label'       => 'POWER',
				'description' => __( 'Raise number to a power', 'a-tables-charts' ),
				'syntax'      => 'POWER(base, exponent)',
				'example'     => '=POWER(2, 3)',
			),
			'SQRT' => array(
				'label'       => 'SQRT',
				'description' => __( 'Square root', 'a-tables-charts' ),
				'syntax'      => 'SQRT(value)',
				'example'     => '=SQRT(16)',
			),
			'CONCAT' => array(
				'label'       => 'CONCAT',
				'description' => __( 'Concatenate text values', 'a-tables-charts' ),
				'syntax'      => 'CONCAT(text1, text2, ...)',
				'example'     => '=CONCAT("Hello", " ", "World")',
			),
		);
	}

	/**
	 * Get formula presets
	 *
	 * @return array Formula presets
	 */
	public static function get_formula_presets() {
		return array(
			'column_sum' => array(
				'label'       => __( 'Column Sum', 'a-tables-charts' ),
				'description' => __( 'Sum entire column', 'a-tables-charts' ),
				'formula'     => '=SUM(A:A)',
			),
			'column_average' => array(
				'label'       => __( 'Column Average', 'a-tables-charts' ),
				'description' => __( 'Average of column', 'a-tables-charts' ),
				'formula'     => '=AVERAGE(A:A)',
			),
			'row_total' => array(
				'label'       => __( 'Row Total', 'a-tables-charts' ),
				'description' => __( 'Sum across row', 'a-tables-charts' ),
				'formula'     => '=SUM(A1:Z1)',
			),
			'percentage' => array(
				'label'       => __( 'Percentage', 'a-tables-charts' ),
				'description' => __( 'Calculate percentage', 'a-tables-charts' ),
				'formula'     => '=(A1/B1)*100',
			),
			'difference' => array(
				'label'       => __( 'Difference', 'a-tables-charts' ),
				'description' => __( 'Subtract values', 'a-tables-charts' ),
				'formula'     => '=A1-B1',
			),
			'product_calc' => array(
				'label'       => __( 'Product (Price × Quantity)', 'a-tables-charts' ),
				'description' => __( 'Multiply price by quantity', 'a-tables-charts' ),
				'formula'     => '=PRODUCT(A1,B1)',
			),
			'growth_rate' => array(
				'label'       => __( 'Growth Rate', 'a-tables-charts' ),
				'description' => __( 'Calculate compound growth', 'a-tables-charts' ),
				'formula'     => '=POWER(1.05, A1)',
			),
		);
	}

	/**
	 * Validate formula syntax
	 *
	 * @param string $formula Formula string.
	 * @return array Validation result
	 */
	public function validate_formula( $formula ) {
		$errors = array();

		if ( empty( $formula ) ) {
			$errors[] = __( 'Formula is empty.', 'a-tables-charts' );
			return array(
				'valid'  => false,
				'errors' => $errors,
			);
		}

		// Check for balanced parentheses
		$open = substr_count( $formula, '(' );
		$close = substr_count( $formula, ')' );
		if ( $open !== $close ) {
			$errors[] = __( 'Unbalanced parentheses.', 'a-tables-charts' );
		}

		// Check for valid function names
		preg_match_all( '/([A-Z]+)\(/', $formula, $matches );
		if ( ! empty( $matches[1] ) ) {
			foreach ( $matches[1] as $func ) {
				if ( ! in_array( $func, $this->functions, true ) ) {
					$errors[] = sprintf( __( 'Unknown function: %s', 'a-tables-charts' ), $func );
				}
			}
		}

		return array(
			'valid'  => empty( $errors ),
			'errors' => $errors,
		);
	}
}

<?php
/**
 * Cell Merging Service
 *
 * Handles cell merging operations for tables
 *
 * @package ATablesCharts\CellMerging\Services
 * @since 1.0.0
 */

namespace ATablesCharts\CellMerging\Services;

/**
 * CellMergingService Class
 *
 * Responsibilities:
 * - Merge cells horizontally
 * - Merge cells vertically
 * - Track merged cell configurations
 * - Generate HTML with proper colspan/rowspan
 */
class CellMergingService {

	/**
	 * Apply cell merging to table data
	 *
	 * @param array $data          Table data.
	 * @param array $merge_config  Merge configuration.
	 * @return array Processed data with merge info
	 */
	public function apply_merging( $data, $merge_config ) {
		if ( empty( $merge_config ) || empty( $data ) ) {
			return array(
				'data'   => $data,
				'merges' => array(),
			);
		}

		// Validate merge config
		$validated_merges = $this->validate_merge_config( $merge_config, $data );

		// Mark cells for merging
		$processed_data = $this->mark_merged_cells( $data, $validated_merges );

		return array(
			'data'   => $processed_data,
			'merges' => $validated_merges,
		);
	}

	/**
	 * Validate merge configuration
	 *
	 * @param array $merge_config Merge configuration.
	 * @param array $data         Table data.
	 * @return array Valid merges
	 */
	private function validate_merge_config( $merge_config, $data ) {
		$valid_merges = array();

		foreach ( $merge_config as $merge ) {
			// Check required fields
			if ( ! isset( $merge['start_row'] ) || ! isset( $merge['start_col'] ) ) {
				continue;
			}

			$start_row = intval( $merge['start_row'] );
			$start_col = intval( $merge['start_col'] );
			$row_span = isset( $merge['row_span'] ) ? intval( $merge['row_span'] ) : 1;
			$col_span = isset( $merge['col_span'] ) ? intval( $merge['col_span'] ) : 1;

			// Validate bounds
			if ( $start_row < 0 || $start_col < 0 || $row_span < 1 || $col_span < 1 ) {
				continue;
			}

			// Check if merge is within data bounds
			$max_row = count( $data );
			if ( $start_row >= $max_row ) {
				continue;
			}

			$valid_merges[] = array(
				'start_row' => $start_row,
				'start_col' => $start_col,
				'row_span'  => $row_span,
				'col_span'  => $col_span,
				'content'   => isset( $merge['content'] ) ? $merge['content'] : '',
			);
		}

		return $valid_merges;
	}

	/**
	 * Mark cells that should be merged
	 *
	 * @param array $data   Table data.
	 * @param array $merges Valid merge configurations.
	 * @return array Data with merge markers
	 */
	private function mark_merged_cells( $data, $merges ) {
		$marked_data = $data;

		foreach ( $merges as $merge ) {
			$start_row = $merge['start_row'];
			$start_col = $merge['start_col'];
			$row_span = $merge['row_span'];
			$col_span = $merge['col_span'];

			// Mark the starting cell
			if ( isset( $marked_data[ $start_row ] ) ) {
				$row_values = array_values( $marked_data[ $start_row ] );
				$columns = array_keys( $marked_data[ $start_row ] );

				if ( isset( $columns[ $start_col ] ) ) {
					$column = $columns[ $start_col ];
					
					// Store merge info with the cell
					$marked_data[ $start_row ][ $column ] = array(
						'value'    => isset( $row_values[ $start_col ] ) ? $row_values[ $start_col ] : '',
						'rowspan'  => $row_span,
						'colspan'  => $col_span,
						'is_merge' => true,
					);

					// Mark cells that should be hidden (covered by merge)
					for ( $r = 0; $r < $row_span; $r++ ) {
						for ( $c = 0; $c < $col_span; $c++ ) {
							// Skip the starting cell
							if ( $r === 0 && $c === 0 ) {
								continue;
							}

							$current_row = $start_row + $r;
							$current_col = $start_col + $c;

							if ( isset( $marked_data[ $current_row ] ) ) {
								$row_columns = array_keys( $marked_data[ $current_row ] );
								if ( isset( $row_columns[ $current_col ] ) ) {
									$col_name = $row_columns[ $current_col ];
									$marked_data[ $current_row ][ $col_name ] = array(
										'hidden'   => true,
										'is_merge' => false,
									);
								}
							}
						}
					}
				}
			}
		}

		return $marked_data;
	}

	/**
	 * Generate HTML table with merged cells
	 *
	 * @param array  $data    Processed data with merge info.
	 * @param array  $headers Table headers.
	 * @param string $classes Additional CSS classes.
	 * @return string HTML table
	 */
	public function generate_html_with_merges( $data, $headers, $classes = '' ) {
		if ( empty( $data ) ) {
			return '';
		}

		$html = '<table class="atables-merged-table ' . esc_attr( $classes ) . '">';

		// Headers
		$html .= '<thead><tr>';
		foreach ( $headers as $header ) {
			$html .= '<th>' . esc_html( $header ) . '</th>';
		}
		$html .= '</tr></thead>';

		// Body
		$html .= '<tbody>';
		foreach ( $data as $row ) {
			$html .= '<tr>';
			foreach ( $row as $cell ) {
				// Skip hidden cells
				if ( is_array( $cell ) && isset( $cell['hidden'] ) && $cell['hidden'] ) {
					continue;
				}

				// Handle merged cells
				if ( is_array( $cell ) && isset( $cell['is_merge'] ) && $cell['is_merge'] ) {
					$rowspan_attr = $cell['rowspan'] > 1 ? ' rowspan="' . intval( $cell['rowspan'] ) . '"' : '';
					$colspan_attr = $cell['colspan'] > 1 ? ' colspan="' . intval( $cell['colspan'] ) . '"' : '';
					$html .= '<td' . $rowspan_attr . $colspan_attr . '>' . esc_html( $cell['value'] ) . '</td>';
				} else {
					// Regular cell
					$value = is_array( $cell ) ? ( isset( $cell['value'] ) ? $cell['value'] : '' ) : $cell;
					$html .= '<td>' . esc_html( $value ) . '</td>';
				}
			}
			$html .= '</tr>';
		}
		$html .= '</tbody>';

		$html .= '</table>';

		return $html;
	}

	/**
	 * Create merge configuration for common patterns
	 *
	 * @param string $pattern Pattern name.
	 * @param array  $options Pattern options.
	 * @return array Merge configuration
	 */
	public function create_merge_pattern( $pattern, $options = array() ) {
		switch ( $pattern ) {
			case 'header_row':
				return $this->create_header_row_merge( $options );

			case 'group_column':
				return $this->create_group_column_merge( $options );

			case 'summary_footer':
				return $this->create_summary_footer_merge( $options );

			default:
				return array();
		}
	}

	/**
	 * Create header row merge (merge first row across all columns)
	 *
	 * @param array $options Options (title, column_count).
	 * @return array Merge config
	 */
	private function create_header_row_merge( $options ) {
		$column_count = isset( $options['column_count'] ) ? intval( $options['column_count'] ) : 3;

		return array(
			array(
				'start_row' => 0,
				'start_col' => 0,
				'row_span'  => 1,
				'col_span'  => $column_count,
				'content'   => isset( $options['title'] ) ? $options['title'] : '',
			),
		);
	}

	/**
	 * Create group column merge (merge same values in first column)
	 *
	 * @param array $options Options (data, column_index).
	 * @return array Merge config
	 */
	private function create_group_column_merge( $options ) {
		if ( ! isset( $options['data'] ) ) {
			return array();
		}

		$data = $options['data'];
		$column_index = isset( $options['column_index'] ) ? intval( $options['column_index'] ) : 0;
		$merges = array();

		$current_value = null;
		$start_row = 0;
		$span = 0;

		foreach ( $data as $row_index => $row ) {
			$row_values = array_values( $row );
			$value = isset( $row_values[ $column_index ] ) ? $row_values[ $column_index ] : '';

			if ( $value === $current_value ) {
				$span++;
			} else {
				// Save previous merge if span > 1
				if ( $span > 1 ) {
					$merges[] = array(
						'start_row' => $start_row,
						'start_col' => $column_index,
						'row_span'  => $span,
						'col_span'  => 1,
					);
				}

				// Start new group
				$current_value = $value;
				$start_row = $row_index;
				$span = 1;
			}
		}

		// Save last merge
		if ( $span > 1 ) {
			$merges[] = array(
				'start_row' => $start_row,
				'start_col' => $column_index,
				'row_span'  => $span,
				'col_span'  => 1,
			);
		}

		return $merges;
	}

	/**
	 * Create summary footer merge
	 *
	 * @param array $options Options (row_count, column_count, label).
	 * @return array Merge config
	 */
	private function create_summary_footer_merge( $options ) {
		$row_count = isset( $options['row_count'] ) ? intval( $options['row_count'] ) : 5;
		$column_count = isset( $options['column_count'] ) ? intval( $options['column_count'] ) : 3;

		return array(
			array(
				'start_row' => $row_count, // Last row
				'start_col' => 0,
				'row_span'  => 1,
				'col_span'  => $column_count - 1, // All but last column
				'content'   => isset( $options['label'] ) ? $options['label'] : 'Total:',
			),
		);
	}

	/**
	 * Get merge presets
	 *
	 * @return array Merge pattern presets
	 */
	public static function get_merge_presets() {
		return array(
			'header_title' => array(
				'label'       => __( 'Header Title Row', 'a-tables-charts' ),
				'description' => __( 'Merge first row across all columns for a title', 'a-tables-charts' ),
				'pattern'     => 'header_row',
			),
			'group_rows' => array(
				'label'       => __( 'Group Rows by Category', 'a-tables-charts' ),
				'description' => __( 'Merge cells in first column with same value', 'a-tables-charts' ),
				'pattern'     => 'group_column',
			),
			'summary_row' => array(
				'label'       => __( 'Summary Footer Row', 'a-tables-charts' ),
				'description' => __( 'Merge footer for totals/summary', 'a-tables-charts' ),
				'pattern'     => 'summary_footer',
			),
		);
	}

	/**
	 * Validate merge doesn't overlap
	 *
	 * @param array $new_merge  New merge to add.
	 * @param array $all_merges Existing merges.
	 * @return bool True if valid
	 */
	public function validate_no_overlap( $new_merge, $all_merges ) {
		$new_start_row = $new_merge['start_row'];
		$new_start_col = $new_merge['start_col'];
		$new_end_row = $new_start_row + $new_merge['row_span'] - 1;
		$new_end_col = $new_start_col + $new_merge['col_span'] - 1;

		foreach ( $all_merges as $existing ) {
			$exist_start_row = $existing['start_row'];
			$exist_start_col = $existing['start_col'];
			$exist_end_row = $exist_start_row + $existing['row_span'] - 1;
			$exist_end_col = $exist_start_col + $existing['col_span'] - 1;

			// Check for overlap
			$row_overlap = ! ( $new_end_row < $exist_start_row || $new_start_row > $exist_end_row );
			$col_overlap = ! ( $new_end_col < $exist_start_col || $new_start_col > $exist_end_col );

			if ( $row_overlap && $col_overlap ) {
				return false; // Overlap detected
			}
		}

		return true; // No overlap
	}

	/**
	 * Auto-merge identical adjacent cells
	 *
	 * @param array $data          Table data.
	 * @param array $column_config Columns to auto-merge.
	 * @return array Merge configuration
	 */
	public function auto_merge_identical( $data, $column_config = array() ) {
		$merges = array();

		if ( empty( $column_config ) || empty( $data ) ) {
			return $merges;
		}

		foreach ( $column_config as $column_index => $config ) {
			$direction = isset( $config['direction'] ) ? $config['direction'] : 'vertical';

			if ( $direction === 'vertical' ) {
				$merges = array_merge( $merges, $this->auto_merge_vertical( $data, $column_index ) );
			} elseif ( $direction === 'horizontal' ) {
				$merges = array_merge( $merges, $this->auto_merge_horizontal( $data, $column_index ) );
			}
		}

		return $merges;
	}

	/**
	 * Auto-merge identical cells vertically
	 *
	 * @param array $data         Table data.
	 * @param int   $column_index Column to merge.
	 * @return array Merge configs
	 */
	private function auto_merge_vertical( $data, $column_index ) {
		$merges = array();
		$current_value = null;
		$start_row = 0;
		$span = 0;

		foreach ( $data as $row_index => $row ) {
			$row_values = array_values( $row );
			$value = isset( $row_values[ $column_index ] ) ? $row_values[ $column_index ] : '';

			if ( $value === $current_value && ! empty( $value ) ) {
				$span++;
			} else {
				if ( $span > 1 ) {
					$merges[] = array(
						'start_row' => $start_row,
						'start_col' => $column_index,
						'row_span'  => $span,
						'col_span'  => 1,
					);
				}

				$current_value = $value;
				$start_row = $row_index;
				$span = 1;
			}
		}

		// Last group
		if ( $span > 1 ) {
			$merges[] = array(
				'start_row' => $start_row,
				'start_col' => $column_index,
				'row_span'  => $span,
				'col_span'  => 1,
			);
		}

		return $merges;
	}

	/**
	 * Auto-merge identical cells horizontally
	 *
	 * @param array $data      Table data.
	 * @param int   $row_index Row to merge.
	 * @return array Merge configs
	 */
	private function auto_merge_horizontal( $data, $row_index ) {
		$merges = array();

		if ( ! isset( $data[ $row_index ] ) ) {
			return $merges;
		}

		$row = array_values( $data[ $row_index ] );
		$current_value = null;
		$start_col = 0;
		$span = 0;

		foreach ( $row as $col_index => $value ) {
			if ( $value === $current_value && ! empty( $value ) ) {
				$span++;
			} else {
				if ( $span > 1 ) {
					$merges[] = array(
						'start_row' => $row_index,
						'start_col' => $start_col,
						'row_span'  => 1,
						'col_span'  => $span,
					);
				}

				$current_value = $value;
				$start_col = $col_index;
				$span = 1;
			}
		}

		// Last group
		if ( $span > 1 ) {
			$merges[] = array(
				'start_row' => $row_index,
				'start_col' => $start_col,
				'row_span'  => 1,
				'col_span'  => $span,
			);
		}

		return $merges;
	}
}

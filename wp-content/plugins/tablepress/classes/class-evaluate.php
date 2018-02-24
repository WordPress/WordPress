<?php
/**
 * TablePress Formula Evaluation Class
 *
 * @package TablePress
 * @subpackage Formulas
 * @author Tobias Bäthge
 * @since 1.5.0
 */

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * TablePress Formula Evaluation Class
 *
 * Before TablePress 1.5, this was part of the TablePress_Render class.
 *
 * @package TablePress
 * @subpackage Formulas
 * @author Tobias Bäthge
 * @since 1.5.0
 */
class TablePress_Evaluate {

	/**
	 * Instance of the EvalMath class.
	 *
	 * @since 1.0.0
	 * @var EvalMath
	 */
	protected $evalmath;

	/**
	 * Table data in which formulas shall be evaluated.
	 *
	 * @since 1.5.0
	 * @var array
	 */
	protected $table_data;

	/**
	 * Storage for cell ranges that have been replaced in formulas.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $known_ranges = array();

	/**
	 * Initialize the Formula Evaluation class, include the EvalMath class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->evalmath = TablePress::load_class( 'EvalMath', 'evalmath.class.php', 'libraries' );
		// Don't raise PHP warnings.
		$this->evalmath->suppress_errors = true;
	}

	/**
	 * Evaluate formulas in the passed table.
	 *
	 * @since 1.0.0
	 *
	 * @param array $table_data Table data in which formulas shall be evaluated.
	 * @return array Table data with evaluated formulas.
	 */
	public function evaluate_table_data( array $table_data ) {
		$this->table_data = $table_data;

		$rows = count( $this->table_data );
		$columns = count( $this->table_data[0] );
		// Use two for-loops instead of foreach here to be sure to always work on the "live" table data and not some in-memory copy.
		for ( $row_idx = 0; $row_idx < $rows; $row_idx++ ) {
			for ( $col_idx = 0; $col_idx < $columns; $col_idx++ ) {
				$this->table_data[ $row_idx ][ $col_idx ] = $this->_evaluate_cell( $this->table_data[ $row_idx ][ $col_idx ] );
			}
		}

		return $this->table_data;
	}

	/**
	 * Parse and evaluate the content of a cell.
	 *
	 * @since 1.0.0
	 *
	 * @param string $content Content of a cell.
	 * @param array  $parents Optional. List of cells that depend on this cell (to prevent circle references).
	 * @return string Result of the parsing/evaluation.
	 */
	protected function _evaluate_cell( $content, array $parents = array() ) {
		if ( '' === $content || '=' === $content || '=' !== $content[0] ) {
			return $content;
		}

		// Cut off the leading =.
		$content = substr( $content, 1 );

		// Support putting formulas in strings, like =Total: {A3+A4}.
		$expressions = array();
		if ( preg_match_all( '#{(.+?)}#', $content, $expressions, PREG_SET_ORDER ) ) {
			$formula_in_string = true;
		} else {
			$formula_in_string = false;
			// Fill array so that it has the same structure as if it came from preg_match_all().
			$expressions[] = array( $content, $content );
		}

		foreach ( $expressions as $expression ) {
			$orig_expression = $expression[0];
			$expression = $expression[1];

			$replaced_references = $replaced_ranges = array();

			// Remove all whitespace characters.
			$expression = str_replace( array( "\n", "\r", "\t", ' ' ), '', $expression );

			// Expand cell ranges (like A3:A6) to a list of single cells (like A3,A4,A5,A6).
			if ( preg_match_all( '#([A-Z]+)([0-9]+):([A-Z]+)([0-9]+)#', $expression, $referenced_cell_ranges, PREG_SET_ORDER ) ) {
				foreach ( $referenced_cell_ranges as $cell_range ) {
					if ( in_array( $cell_range[0], $replaced_ranges, true ) ) {
						continue;
					}

					$replaced_ranges[] = $cell_range[0];

					if ( isset( $this->known_ranges[ $cell_range[0] ] ) ) {
						$expression = preg_replace( '#(?<![A-Z])' . preg_quote( $cell_range[0], '#' ) . '(?![0-9])#', $this->known_ranges[ $cell_range[0] ], $expression );
						continue;
					}

					// No -1 necessary for this transformation, as we don't actually access the table.
					$first_col = TablePress::letter_to_number( $cell_range[1] );
					$first_row = $cell_range[2];
					$last_col = TablePress::letter_to_number( $cell_range[3] );
					$last_row = $cell_range[4];

					$col_start = min( $first_col, $last_col );
					$col_end = max( $first_col, $last_col ) + 1; // +1 for loop below
					$row_start = min( $first_row, $last_row );
					$row_end = max( $first_row, $last_row ) + 1; // +1 for loop below

					$cell_list = array();
					for ( $col = $col_start; $col < $col_end; $col++ ) {
						for ( $row = $row_start; $row < $row_end; $row++ ) {
							$column = TablePress::number_to_letter( $col );
							$cell_list[] = "{$column}{$row}";
						}
					}
					$cell_list = implode( ',', $cell_list );

					$expression = preg_replace( '#(?<![A-Z])' . preg_quote( $cell_range[0], '#' ) . '(?![0-9])#', $cell_list, $expression );
					$this->known_ranges[ $cell_range[0] ] = $cell_list;
				}
			}

			// Parse and evaluate single cell references (like A3 or XY312), while prohibiting circle references.
			if ( preg_match_all( '#([A-Z]+)([0-9]+)#', $expression, $referenced_cells, PREG_SET_ORDER ) ) {
				foreach ( $referenced_cells as $cell_reference ) {
					if ( in_array( $cell_reference[0], $parents, true ) ) {
						return '!ERROR! Circle Reference';
					}

					if ( in_array( $cell_reference[0], $replaced_references, true ) ) {
						continue;
					}

					$replaced_references[] = $cell_reference[0];

					$ref_col = TablePress::letter_to_number( $cell_reference[1] ) - 1;
					$ref_row = $cell_reference[2] - 1;

					if ( ! isset( $this->table_data[ $ref_row ] ) || ! isset( $this->table_data[ $ref_row ][ $ref_col ] ) ) {
						return "!ERROR! Cell {$cell_reference[0]} does not exist";
					}

					$ref_parents = $parents;
					$ref_parents[] = $cell_reference[0];

					$result = $this->table_data[ $ref_row ][ $ref_col ] = $this->_evaluate_cell( $this->table_data[ $ref_row ][ $ref_col ], $ref_parents );
					// Bail if there was an error already.
					if ( false !== strpos( $result, '!ERROR!' ) ) {
						return $result;
					}
					// Remove all whitespace characters.
					$result = str_replace( array( "\n", "\r", "\t", ' ' ), '', $result );
					// Treat empty cells as 0.
					if ( '' === $result ) {
						$result = 0;
					}
					// Bail if the cell does not result in a number (meaning it was a number or expression before being evaluated).
					if ( ! is_numeric( $result ) ) {
						return "!ERROR! {$cell_reference[0]} does not contain a number or expression";
					}

					$expression = preg_replace( '#(?<![A-Z])' . $cell_reference[0] . '(?![0-9])#', $result, $expression );
				}
			}

			$result = $this->_evaluate_math_expression( $expression );
			// Support putting formulas in strings, like =Total: {A3+A4}.
			if ( $formula_in_string ) {
				$content = str_replace( $orig_expression, $result, $content );
			} else {
				$content = $result;
			}
		}

		return $content;
	}

	/**
	 * Evaluate a math expression.
	 *
	 * @since 1.0.0
	 *
	 * @param string $expression without leading = sign.
	 * @return string Result of the evaluation.
	 */
	protected function _evaluate_math_expression( $expression ) {
		// Straight up evaluation, without parsing of variable or function assignments (which is why we only need one instance of the object).
		$result = $this->evalmath->evaluate( $expression );
		if ( false === $result ) {
			return '!ERROR! ' . $this->evalmath->last_error;
		} else {
			return (string) $result;
		}
	}

} // class TablePress_Evaluate

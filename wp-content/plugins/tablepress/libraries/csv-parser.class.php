<?php
/**
 * CSV Parsing class for TablePress, used for import of CSV files
 *
 * @package TablePress
 * @subpackage Import
 * @author Tobias Bäthge
 * @since 1.0.0
 */

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * CSV Parsing class
 * @package TablePress
 * @subpackage Import
 * @author Tobias Bäthge
 * @since 1.0.0
 */
class CSV_Parser {

	/**
	 * The used character for the enclosure of a cell. Defaults to quotation mark ".
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $enclosure = '"';

	/**
	 * Number of rows to analyze when attempting to auto-detect the CSV delimiter.
	 *
	 * @since 1.0.0
	 * @var int
	 */
	protected $delimiter_search_max_lines = 15;

	/**
	 * Characters to ignore when attempting to auto-detect delimiter.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $non_delimiter_chars = "a-zA-Z0-9\n\r";

	/**
	 * The preferred delimiter characters, only used when all filtering method return multiple possible delimiters (happens very rarely).
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $preferred_delimiter_chars = ";,\t";

	/**
	 * The CSV data string that shall be parsed to an array.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $import_data;

	/**
	 * The error state while parsing input data.
	 *
	 * 0 = No errors found. Everything should be fine.
	 * 1 = A hopefully correctable syntax error was found.
	 * 2 = The enclosure character was found in a non-enclosed field. This means the file is either corrupt,
	 *     or does not follow the common CSV standard. Please validate the parsed data manually.
	 *
	 * @since 1.0.0
	 * @var int
	 */
	public $error = 0;

	/**
	 * Detailed error information.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	public $error_info = array();

	/**
	 * Class Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		// Unused.
	}

	/**
	 * Load CSV data that shall be parsed.
	 *
	 * @since 1.0.0
	 *
	 * @param string $data Data to be parsed.
	 */
	public function load_data( $data ) {
		// Check for mandatory trailing line break.
		if ( "\n" !== substr( $data, -1 ) ) {
			$data .= "\n";
		}
		$this->import_data = $data;
	}

	/**
	 * Detect the CSV delimiter, by analyzing some rows to determine the most probable delimiter character.
	 *
	 * @since 1.0.0
	 *
	 * @return string Most probable delimiter character.
	 */
	public function find_delimiter() {
		$data = &$this->import_data;

		$delimiter_count = array();
		$enclosed = false;
		$current_line = 0;

		// Walk through each character in the CSV string (up to $this->delimiter_search_max_lines) and search potential delimiter characters.
		$data_length = strlen( $data );
		for ( $i = 0; $i < $data_length; $i++ ) {
			$prev_char = ( $i - 1 >= 0 ) ? $data[ $i - 1 ] : '';
			$curr_char = $data[ $i ];
			$next_char = ( $i + 1 < $data_length ) ? $data[ $i + 1 ] : '';

			if ( $curr_char === $this->enclosure ) {
				// Open and closing quotes.
				if ( ! $enclosed || $next_char !== $this->enclosure ) {
					$enclosed = ! $enclosed; // Flip bool.
				} elseif ( $enclosed ) {
					$i++; // Skip next character.
				}
			} elseif ( ( "\n" === $curr_char && "\r" !== $prev_char || "\r" === $curr_char ) && ! $enclosed ) {
				// Reached end of a line.
				$current_line++;
				if ( $current_line >= $this->delimiter_search_max_lines ) {
					break;
				}
			} elseif ( ! $enclosed ) {
				// At this point, $curr_char seems to be used as a delimiter, as it is not enclosed.
				// Count $curr_char if it is not in the $this->non_delimiter_chars list
				if ( 0 === preg_match( '#[' . $this->non_delimiter_chars . ']#i', $curr_char ) ) {
					if ( ! isset( $delimiter_count[ $curr_char ][ $current_line ] ) ) {
						$delimiter_count[ $curr_char ][ $current_line ] = 0; // Initialize empty
					}
					$delimiter_count[ $curr_char ][ $current_line ]++;
				}
			}
		}

		// Find most probable delimiter, by sorting their counts.
		$potential_delimiters = array();
		foreach ( $delimiter_count as $char => $line_counts ) {
			$is_possible_delimiter = $this->_check_delimiter_count( $char, $line_counts, $current_line );
			if ( false !== $is_possible_delimiter ) {
				$potential_delimiters[ $is_possible_delimiter ] = $char;
			}
		}
		ksort( $potential_delimiters );

		// If no valid delimiter was found, use the character that was found in most rows.
		if ( empty( $potential_delimiters ) ) {
			$delimiter_counts = array_map( 'count', $delimiter_count );
			arsort( $delimiter_counts, SORT_NUMERIC );
			$potential_delimiters = array_keys( $delimiter_counts );
		}

		// Return first array element, as that has the highest count.
		return array_shift( $potential_delimiters );
	}

	/**
	 * Check if passed character can be a delimiter, by checking counts in each line.
	 *
	 * @since 1.0.0
	 *
	 * @param string $char         Character to check.
	 * @param array  $line_counts  Counts for the characters in the lines.
	 * @param int    $number_lines Number of lines.
	 * @return bool|string False if delimiter is not possible, string to be used as a sort key if character could be a delimiter.
	 */
	protected function _check_delimiter_count( $char, array $line_counts, $number_lines ) {
		// Was the potential delimiter found in every line?
		if ( count( $line_counts ) !== $number_lines ) {
			return false;
		}

		// Check if the count in every line is the same (or one higher for an "almost").
		$first = null;
		$equal = null;
		$almost = false;
		foreach ( $line_counts as $line => $count ) {
			if ( is_null( $first ) ) {
				$first = $count;
			} elseif ( $count === $first && false !== $equal ) {
				$equal = true;
			} elseif ( $count === $first + 1 && false !== $equal ) {
				$equal = true;
				$almost = true;
			} else {
				$equal = false;
			}
		}
		// Check equality only if there's more than one line.
		if ( $number_lines > 1 && ! $equal ) {
			return false;
		}

		// At this point, count is equal in all lines, so determine a string to sort priority.
		$match = ( $almost ) ? 2 : 1;
		$pref = strpos( $this->preferred_delimiter_chars, $char );
		$pref = ( false !== $pref ) ? str_pad( $pref, 3, '0', STR_PAD_LEFT ) : '999';
		return $pref . $match . '.' . ( 99999 - str_pad( $first, 5, '0', STR_PAD_LEFT ) );
	}

	/**
	 * Parse CSV string into a two-dimensional array.
	 *
	 * @since 1.0.0
	 *
	 * @param string $delimiter Delimiter character for the CSV parsing.
	 * @return array Two-dimensional array with the data from the CSV string.
	 */
	public function parse( $delimiter ) {
		$data = &$this->import_data;

		// Filter delimiter from the list, if it is a whitespace character.
		$white_spaces = str_replace( $delimiter, '', " \t\x0B\0" );

		$rows = array(); // Complete rows.
		$row = array(); // Row that is currently built.
		$column = 0; // Current column index.
		$cell_content = ''; // Content of the currently processed cell.
		$enclosed = false;
		$was_enclosed = false; // To determine if the cell content will be trimmed of whitespace (only for enclosed cells).

		// Walk through each character in the CSV string.
		$data_length = strlen( $data );
		for ( $i = 0; $i < $data_length; $i++ ) {
			$curr_char = $data[ $i ];
			$next_char = ( $i + 1 < $data_length ) ? $data[ $i + 1 ] : '';

			if ( $curr_char === $this->enclosure ) {
				// Open/close quotes, and inline quotes.
				if ( ! $enclosed ) {
					if ( '' === ltrim( $cell_content, $white_spaces ) ) {
						$enclosed = true;
						$was_enclosed = true;
					} else {
						$this->error = 2;
						$error_line = count( $rows ) + 1;
						$error_column = $column + 1;
						if ( ! isset( $this->error_info[ "{$error_line}-{$error_column}" ] ) ) {
							$this->error_info[ "{$error_line}-{$error_column}" ] = array(
								'type'   => 2,
								'info'   => "Syntax error found in line {$error_line}. Non-enclosed fields can not contain double-quotes.",
								'line'   => $error_line,
								'column' => $error_column,
							);
						}
						$cell_content .= $curr_char;
					}
				} elseif ( $next_char === $this->enclosure ) {
					// Enclosure character within enclosed cell (" encoded as "").
					$cell_content .= $curr_char;
					$i++; // Skip next character
				} elseif ( $next_char !== $delimiter && "\r" !== $next_char && "\n" !== $next_char ) {
					// for-loop (instead of while-loop) that skips whitespace.
					for ( $x = ( $i + 1 ); isset( $data[ $x ] ) && '' === ltrim( $data[ $x ], $white_spaces ); $x++ ) {
						// Action is in iterator check.
					}
					if ( $data[ $x ] === $delimiter ) {
						$enclosed = false;
						$i = $x;
					} else {
						if ( $this->error < 1 ) {
							$this->error = 1;
						}
						$error_line = count( $rows ) + 1;
						$error_column = $column + 1;
						if ( ! isset( $this->error_info[ "{$error_line}-{$error_column}" ] ) ) {
							$this->error_info[ "{$error_line}-{$error_column}" ] = array(
								'type'   => 1,
								'info'   => "Syntax error found in line {$error_line}. A single double-quote was found within an enclosed string. Enclosed double-quotes must be escaped with a second double-quote.",
								'line'   => $error_line,
								'column' => $error_column,
							);
						}
						$cell_content .= $curr_char;
						$enclosed = false;
					}
				} else {
					// The " was the closing one for the cell.
					$enclosed = false;
				}
			} elseif ( ( $curr_char === $delimiter || "\n" === $curr_char || "\r" === $curr_char ) && ! $enclosed ) {
				// End of cell (by $delimiter), or end of line (by line break, and not enclosed!).

				$row[ $column ] = ( $was_enclosed ) ? $cell_content : trim( $cell_content );
				$cell_content = '';
				$was_enclosed = false;
				$column++;

				// End of line.
				if ( "\n" === $curr_char || "\r" === $curr_char ) {
					// Append completed row.
					$rows[] = $row;
					$row = array();
					$column = 0;
					if ( "\r" === $curr_char && "\n" === $next_char ) {
						// Skip next character in \r\n line breaks.
						$i++;
					}
				}
			} else {
				// Append character to current cell.
				$cell_content .= $curr_char;
			}
		}

		return $rows;
	}

} // class CSV_Parser

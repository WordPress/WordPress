<?php
/**
 * WordPress Diff bastard child of old MediaWiki Diff Formatter.
 *
 * Basically all that remains is the table structure and some method names.
 *
 * @package WordPress
 * @subpackage Diff
 */

if ( ! class_exists( 'Text_Diff', false ) ) {
	/** Text_Diff class */
	require( dirname(__FILE__).'/Text/Diff.php' );
	/** Text_Diff_Renderer class */
	require( dirname(__FILE__).'/Text/Diff/Renderer.php' );
	/** Text_Diff_Renderer_inline class */
	require( dirname(__FILE__).'/Text/Diff/Renderer/inline.php' );
}

/**
 * Table renderer to display the diff lines.
 *
 * @since 2.6.0
 * @uses Text_Diff_Renderer Extends
 */
class WP_Text_Diff_Renderer_Table extends Text_Diff_Renderer {

	/**
	 * @see Text_Diff_Renderer::_leading_context_lines
	 * @var int
	 * @access public
	 * @since 2.6.0
	 */
	public $_leading_context_lines  = 10000;

	/**
	 * @see Text_Diff_Renderer::_trailing_context_lines
	 * @var int
	 * @access public
	 * @since 2.6.0
	 */
	public $_trailing_context_lines = 10000;

	/**
	 * Threshold for when a diff should be saved or omitted.
	 *
	 * @var float
	 * @access protected
	 * @since 2.6.0
	 */
	protected $_diff_threshold = 0.6;

	/**
	 * Inline display helper object name.
	 *
	 * @var string
	 * @access protected
	 * @since 2.6.0
	 */
	protected $inline_diff_renderer = 'WP_Text_Diff_Renderer_inline';

	/**
	 * Should we show the split view or not
	 *
	 * @var string
	 * @access protected
	 * @since 3.6.0
	 */
	protected $_show_split_view = true;

	protected $compat_fields = array( '_show_split_view', 'inline_diff_renderer', '_diff_threshold' );

	/**
	 * Constructor - Call parent constructor with params array.
	 *
	 * This will set class properties based on the key value pairs in the array.
	 *
	 * @since 2.6.0
	 *
	 * @param array $params
	 */
	public function __construct( $params = array() ) {
		parent::__construct( $params );
		if ( isset( $params[ 'show_split_view' ] ) )
			$this->_show_split_view = $params[ 'show_split_view' ];
	}

	/**
	 * @ignore
	 *
	 * @param string $header
	 * @return string
	 */
	public function _startBlock( $header ) {
		return '';
	}

	/**
	 * @ignore
	 *
	 * @param array $lines
	 * @param string $prefix
	 */
	public function _lines( $lines, $prefix=' ' ) {
	}

	/**
	 * @ignore
	 *
	 * @param string $line HTML-escape the value.
	 * @return string
	 */
	public function addedLine( $line ) {
		return "<td class='diff-addedline'>{$line}</td>";

	}

	/**
	 * @ignore
	 *
	 * @param string $line HTML-escape the value.
	 * @return string
	 */
	public function deletedLine( $line ) {
		return "<td class='diff-deletedline'>{$line}</td>";
	}

	/**
	 * @ignore
	 *
	 * @param string $line HTML-escape the value.
	 * @return string
	 */
	public function contextLine( $line ) {
		return "<td class='diff-context'>{$line}</td>";
	}

	/**
	 * @ignore
	 *
	 * @return string
	 */
	public function emptyLine() {
		return '<td>&nbsp;</td>';
	}

	/**
	 * @ignore
	 * @access public
	 *
	 * @param array $lines
	 * @param bool $encode
	 * @return string
	 */
	public function _added( $lines, $encode = true ) {
		$r = '';
		foreach ($lines as $line) {
			if ( $encode ) {
				$processed_line = htmlspecialchars( $line );

				/**
				 * Contextually filter a diffed line.
				 *
				 * Filters TextDiff processing of diffed line. By default, diffs are processed with
				 * htmlspecialchars. Use this filter to remove or change the processing. Passes a context
				 * indicating if the line is added, deleted or unchanged.
				 *
				 * @since 4.1.0
				 *
				 * @param String $processed_line The processed diffed line.
				 * @param String $line           The unprocessed diffed line.
		 		 * @param string null            The line context. Values are 'added', 'deleted' or 'unchanged'.
				 */
				$line = apply_filters( 'process_text_diff_html', $processed_line, $line, 'added' );
			}

			if ( $this->_show_split_view ) {
				$r .= '<tr>' . $this->emptyLine() . $this->emptyLine() . $this->addedLine( $line ) . "</tr>\n";
			} else {
				$r .= '<tr>' . $this->addedLine( $line ) . "</tr>\n";
			}
		}
		return $r;
	}

	/**
	 * @ignore
	 * @access public
	 *
	 * @param array $lines
	 * @param bool $encode
	 * @return string
	 */
	public function _deleted( $lines, $encode = true ) {
		$r = '';
		foreach ($lines as $line) {
			if ( $encode ) {
				$processed_line = htmlspecialchars( $line );

				/** This filter is documented in wp-includes/wp-diff.php */
				$line = apply_filters( 'process_text_diff_html', $processed_line, $line, 'deleted' );
			}
			if ( $this->_show_split_view ) {
				$r .= '<tr>' . $this->deletedLine( $line ) . $this->emptyLine() . $this->emptyLine() . "</tr>\n";
			} else {
				$r .= '<tr>' . $this->deletedLine( $line ) . "</tr>\n";
			}

		}
		return $r;
	}

	/**
	 * @ignore
	 * @access public
	 *
	 * @param array $lines
	 * @param bool $encode
	 * @return string
	 */
	public function _context( $lines, $encode = true ) {
		$r = '';
		foreach ($lines as $line) {
			if ( $encode ) {
				$processed_line = htmlspecialchars( $line );

				/** This filter is documented in wp-includes/wp-diff.php */
				$line = apply_filters( 'process_text_diff_html', $processed_line, $line, 'unchanged' );
			}
			if (  $this->_show_split_view ) {
				$r .= '<tr>' . $this->contextLine( $line ) . $this->emptyLine() . $this->contextLine( $line )  . "</tr>\n";
			} else {
				$r .= '<tr>' . $this->contextLine( $line ) . "</tr>\n";
			}
		}
		return $r;
	}

	/**
	 * Process changed lines to do word-by-word diffs for extra highlighting.
	 *
	 * (TRAC style) sometimes these lines can actually be deleted or added rows.
	 * We do additional processing to figure that out
	 *
	 * @access public
	 * @since 2.6.0
	 *
	 * @param array $orig
	 * @param array $final
	 * @return string
	 */
	public function _changed( $orig, $final ) {
		$r = '';

		// Does the aforementioned additional processing
		// *_matches tell what rows are "the same" in orig and final. Those pairs will be diffed to get word changes
		//	match is numeric: an index in other column
		//	match is 'X': no match. It is a new row
		// *_rows are column vectors for the orig column and the final column.
		//	row >= 0: an indix of the $orig or $final array
		//	row  < 0: a blank row for that column
		list($orig_matches, $final_matches, $orig_rows, $final_rows) = $this->interleave_changed_lines( $orig, $final );

		// These will hold the word changes as determined by an inline diff
		$orig_diffs  = array();
		$final_diffs = array();

		// Compute word diffs for each matched pair using the inline diff
		foreach ( $orig_matches as $o => $f ) {
			if ( is_numeric($o) && is_numeric($f) ) {
				$text_diff = new Text_Diff( 'auto', array( array($orig[$o]), array($final[$f]) ) );
				$renderer = new $this->inline_diff_renderer;
				$diff = $renderer->render( $text_diff );

				// If they're too different, don't include any <ins> or <dels>
				if ( preg_match_all( '!(<ins>.*?</ins>|<del>.*?</del>)!', $diff, $diff_matches ) ) {
					// length of all text between <ins> or <del>
					$stripped_matches = strlen(strip_tags( join(' ', $diff_matches[0]) ));
					// since we count lengith of text between <ins> or <del> (instead of picking just one),
					//	we double the length of chars not in those tags.
					$stripped_diff = strlen(strip_tags( $diff )) * 2 - $stripped_matches;
					$diff_ratio = $stripped_matches / $stripped_diff;
					if ( $diff_ratio > $this->_diff_threshold )
						continue; // Too different. Don't save diffs.
				}

				// Un-inline the diffs by removing del or ins
				$orig_diffs[$o]  = preg_replace( '|<ins>.*?</ins>|', '', $diff );
				$final_diffs[$f] = preg_replace( '|<del>.*?</del>|', '', $diff );
			}
		}

		foreach ( array_keys($orig_rows) as $row ) {
			// Both columns have blanks. Ignore them.
			if ( $orig_rows[$row] < 0 && $final_rows[$row] < 0 )
				continue;

			// If we have a word based diff, use it. Otherwise, use the normal line.
			if ( isset( $orig_diffs[$orig_rows[$row]] ) )
				$orig_line = $orig_diffs[$orig_rows[$row]];
			elseif ( isset( $orig[$orig_rows[$row]] ) )
				$orig_line = htmlspecialchars($orig[$orig_rows[$row]]);
			else
				$orig_line = '';

			if ( isset( $final_diffs[$final_rows[$row]] ) )
				$final_line = $final_diffs[$final_rows[$row]];
			elseif ( isset( $final[$final_rows[$row]] ) )
				$final_line = htmlspecialchars($final[$final_rows[$row]]);
			else
				$final_line = '';

			if ( $orig_rows[$row] < 0 ) { // Orig is blank. This is really an added row.
				$r .= $this->_added( array($final_line), false );
			} elseif ( $final_rows[$row] < 0 ) { // Final is blank. This is really a deleted row.
				$r .= $this->_deleted( array($orig_line), false );
			} else { // A true changed row.
				if ( $this->_show_split_view ) {
					$r .= '<tr>' . $this->deletedLine( $orig_line ) . $this->emptyLine() . $this->addedLine( $final_line ) . "</tr>\n";
				} else {
					$r .= '<tr>' . $this->deletedLine( $orig_line ) . "</tr><tr>" . $this->addedLine( $final_line ) . "</tr>\n";
				}
			}
		}

		return $r;
	}

	/**
	 * Takes changed blocks and matches which rows in orig turned into which rows in final.
	 *
	 * Returns
	 *	*_matches ( which rows match with which )
	 *	*_rows ( order of rows in each column interleaved with blank rows as
	 *		necessary )
	 *
	 * @since 2.6.0
	 *
	 * @param array $orig
	 * @param array $final
	 * @return array
	 */
	public function interleave_changed_lines( $orig, $final ) {

		// Contains all pairwise string comparisons. Keys are such that this need only be a one dimensional array.
		$matches = array();
		foreach ( array_keys($orig) as $o ) {
			foreach ( array_keys($final) as $f ) {
				$matches["$o,$f"] = $this->compute_string_distance( $orig[$o], $final[$f] );
			}
		}
		asort($matches); // Order by string distance.

		$orig_matches  = array();
		$final_matches = array();

		foreach ( $matches as $keys => $difference ) {
			list($o, $f) = explode(',', $keys);
			$o = (int) $o;
			$f = (int) $f;

			// Already have better matches for these guys
			if ( isset($orig_matches[$o]) && isset($final_matches[$f]) )
				continue;

			// First match for these guys. Must be best match
			if ( !isset($orig_matches[$o]) && !isset($final_matches[$f]) ) {
				$orig_matches[$o] = $f;
				$final_matches[$f] = $o;
				continue;
			}

			// Best match of this final is already taken?  Must mean this final is a new row.
			if ( isset($orig_matches[$o]) )
				$final_matches[$f] = 'x';

			// Best match of this orig is already taken?  Must mean this orig is a deleted row.
			elseif ( isset($final_matches[$f]) )
				$orig_matches[$o] = 'x';
		}

		// We read the text in this order
		ksort($orig_matches);
		ksort($final_matches);

		// Stores rows and blanks for each column.
		$orig_rows = $orig_rows_copy = array_keys($orig_matches);
		$final_rows = array_keys($final_matches);

		// Interleaves rows with blanks to keep matches aligned.
		// We may end up with some extraneous blank rows, but we'll just ignore them later.
		foreach ( $orig_rows_copy as $orig_row ) {
			$final_pos = array_search($orig_matches[$orig_row], $final_rows, true);
			$orig_pos = (int) array_search($orig_row, $orig_rows, true);

			if ( false === $final_pos ) { // This orig is paired with a blank final.
				array_splice( $final_rows, $orig_pos, 0, -1 );
			} elseif ( $final_pos < $orig_pos ) { // This orig's match is up a ways. Pad final with blank rows.
				$diff_pos = $final_pos - $orig_pos;
				while ( $diff_pos < 0 )
					array_splice( $final_rows, $orig_pos, 0, $diff_pos++ );
			} elseif ( $final_pos > $orig_pos ) { // This orig's match is down a ways. Pad orig with blank rows.
				$diff_pos = $orig_pos - $final_pos;
				while ( $diff_pos < 0 )
					array_splice( $orig_rows, $orig_pos, 0, $diff_pos++ );
			}
		}

		// Pad the ends with blank rows if the columns aren't the same length
		$diff_count = count($orig_rows) - count($final_rows);
		if ( $diff_count < 0 ) {
			while ( $diff_count < 0 )
				array_push($orig_rows, $diff_count++);
		} elseif ( $diff_count > 0 ) {
			$diff_count = -1 * $diff_count;
			while ( $diff_count < 0 )
				array_push($final_rows, $diff_count++);
		}

		return array($orig_matches, $final_matches, $orig_rows, $final_rows);
	}

	/**
	 * Computes a number that is intended to reflect the "distance" between two strings.
	 *
	 * @since 2.6.0
	 *
	 * @param string $string1
	 * @param string $string2
	 * @return int
	 */
	public function compute_string_distance( $string1, $string2 ) {
		// Vectors containing character frequency for all chars in each string
		$chars1 = count_chars($string1);
		$chars2 = count_chars($string2);

		// L1-norm of difference vector.
		$difference = array_sum( array_map( array($this, 'difference'), $chars1, $chars2 ) );

		// $string1 has zero length? Odd. Give huge penalty by not dividing.
		if ( !$string1 )
			return $difference;

		// Return distance per character (of string1).
		return $difference / strlen($string1);
	}

	/**
	 * @ignore
	 * @since 2.6.0
	 *
	 * @param int $a
	 * @param int $b
	 * @return int
	 */
	public function difference( $a, $b ) {
		return abs( $a - $b );
	}

	/**
	 * Make private properties readable for backward compatibility.
	 *
	 * @since 4.0.0
	 * @access public
	 *
	 * @param string $name Property to get.
	 * @return mixed Property.
	 */
	public function __get( $name ) {
		if ( in_array( $name, $this->compat_fields ) ) {
			return $this->$name;
		}
	}

	/**
	 * Make private properties settable for backward compatibility.
	 *
	 * @since 4.0.0
	 * @access public
	 *
	 * @param string $name  Property to check if set.
	 * @param mixed  $value Property value.
	 * @return mixed Newly-set property.
	 */
	public function __set( $name, $value ) {
		if ( in_array( $name, $this->compat_fields ) ) {
			return $this->$name = $value;
		}
	}

	/**
	 * Make private properties checkable for backward compatibility.
	 *
	 * @since 4.0.0
	 * @access public
	 *
	 * @param string $name Property to check if set.
	 * @return bool Whether the property is set.
	 */
	public function __isset( $name ) {
		if ( in_array( $name, $this->compat_fields ) ) {
			return isset( $this->$name );
		}
	}

	/**
	 * Make private properties un-settable for backward compatibility.
	 *
	 * @since 4.0.0
	 * @access public
	 *
	 * @param string $name Property to unset.
	 */
	public function __unset( $name ) {
		if ( in_array( $name, $this->compat_fields ) ) {
			unset( $this->$name );
		}
	}
}

/**
 * Better word splitting than the PEAR package provides.
 *
 * @since 2.6.0
 * @uses Text_Diff_Renderer_inline Extends
 */
class WP_Text_Diff_Renderer_inline extends Text_Diff_Renderer_inline {

	/**
	 * @ignore
	 * @since 2.6.0
	 *
	 * @param string $string
	 * @param string $newlineEscape
	 * @return string
	 */
	public function _splitOnWords($string, $newlineEscape = "\n") {
		$string = str_replace("\0", '', $string);
		$words  = preg_split( '/([^\w])/u', $string, -1, PREG_SPLIT_DELIM_CAPTURE );
		$words  = str_replace( "\n", $newlineEscape, $words );
		return $words;
	}

}

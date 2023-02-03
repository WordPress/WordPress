<?php
/**
 * HTML Span: Represents a textual span inside an HTML document.
 *
 * @package WordPress
 * @subpackage HTML-API
 * @since 6.2.0
 */

/**
 * Represents a textual span inside an HTML document.
 *
 * This is a two-tuple in disguise, used to avoid the memory
 * overhead involved in using an array for the same purpose.
 *
 * This class is for internal usage of the WP_HTML_Tag_Processor class.
 *
 * @access private
 * @since 6.2.0
 *
 * @see WP_HTML_Tag_Processor
 */
class WP_HTML_Span {
	/**
	 * Byte offset into document where span begins.
	 *
	 * @since 6.2.0
	 * @var int
	 */
	public $start;

	/**
	 * Byte offset into document where span ends.
	 *
	 * @since 6.2.0
	 * @var int
	 */
	public $end;

	/**
	 * Constructor.
	 *
	 * @since 6.2.0
	 *
	 * @param int $start Byte offset into document where replacement span begins.
	 * @param int $end   Byte offset into document where replacement span ends.
	 */
	public function __construct( $start, $end ) {
		$this->start = $start;
		$this->end   = $end;
	}
}

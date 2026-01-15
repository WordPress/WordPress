<?php
/**
 * HTML API: WP_HTML_Text_Replacement class
 *
 * @package WordPress
 * @subpackage HTML-API
 * @since 6.2.0
 */

/**
 * Core class used by the HTML tag processor as a data structure for replacing
 * existing content from start to end, allowing to drastically improve performance.
 *
 * This class is for internal usage of the WP_HTML_Tag_Processor class.
 *
 * @access private
 * @since 6.2.0
 * @since 6.5.0 Replace `end` with `length` to more closely match `substr()`.
 *
 * @see WP_HTML_Tag_Processor
 */
class WP_HTML_Text_Replacement {
	/**
	 * Byte offset into document where replacement span begins.
	 *
	 * @since 6.2.0
	 *
	 * @var int
	 */
	public $start;

	/**
	 * Byte length of span being replaced.
	 *
	 * @since 6.5.0
	 *
	 * @var int
	 */
	public $length;

	/**
	 * Span of text to insert in document to replace existing content from start to end.
	 *
	 * @since 6.2.0
	 *
	 * @var string
	 */
	public $text;

	/**
	 * Constructor.
	 *
	 * @since 6.2.0
	 *
	 * @param int    $start  Byte offset into document where replacement span begins.
	 * @param int    $length Byte length of span in document being replaced.
	 * @param string $text   Span of text to insert in document to replace existing content from start to end.
	 */
	public function __construct( int $start, int $length, string $text ) {
		$this->start  = $start;
		$this->length = $length;
		$this->text   = $text;
	}
}

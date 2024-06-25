<?php
/**
 * HTML API: WP_HTML_Attribute_Token class
 *
 * @package WordPress
 * @subpackage HTML-API
 * @since 6.2.0
 */

/**
 * Core class used by the HTML tag processor as a data structure for the attribute token,
 * allowing to drastically improve performance.
 *
 * This class is for internal usage of the WP_HTML_Tag_Processor class.
 *
 * @access private
 * @since 6.2.0
 * @since 6.5.0 Replaced `end` with `length` to more closely match `substr()`.
 *
 * @see WP_HTML_Tag_Processor
 */
class WP_HTML_Attribute_Token {
	/**
	 * Attribute name.
	 *
	 * @since 6.2.0
	 *
	 * @var string
	 */
	public $name;

	/**
	 * Attribute value.
	 *
	 * @since 6.2.0
	 *
	 * @var int
	 */
	public $value_starts_at;

	/**
	 * How many bytes the value occupies in the input HTML.
	 *
	 * @since 6.2.0
	 *
	 * @var int
	 */
	public $value_length;

	/**
	 * The string offset where the attribute name starts.
	 *
	 * @since 6.2.0
	 *
	 * @var int
	 */
	public $start;

	/**
	 * Byte length of text spanning the attribute inside a tag.
	 *
	 * This span starts at the first character of the attribute name
	 * and it ends after one of three cases:
	 *
	 *  - at the end of the attribute name for boolean attributes.
	 *  - at the end of the value for unquoted attributes.
	 *  - at the final single or double quote for quoted attributes.
	 *
	 * Example:
	 *
	 *     <div class="post">
	 *          ------------ length is 12, including quotes
	 *
	 *     <input type="checked" checked id="selector">
	 *                           ------- length is 6
	 *
	 *     <a rel=noopener>
	 *        ------------ length is 11
	 *
	 * @since 6.5.0 Replaced `end` with `length` to more closely match `substr()`.
	 *
	 * @var int
	 */
	public $length;

	/**
	 * Whether the attribute is a boolean attribute with value `true`.
	 *
	 * @since 6.2.0
	 *
	 * @var bool
	 */
	public $is_true;

	/**
	 * Constructor.
	 *
	 * @since 6.2.0
	 * @since 6.5.0 Replaced `end` with `length` to more closely match `substr()`.
	 *
	 * @param string $name         Attribute name.
	 * @param int    $value_start  Attribute value.
	 * @param int    $value_length Number of bytes attribute value spans.
	 * @param int    $start        The string offset where the attribute name starts.
	 * @param int    $length       Byte length of the entire attribute name or name and value pair expression.
	 * @param bool   $is_true      Whether the attribute is a boolean attribute with true value.
	 */
	public function __construct( $name, $value_start, $value_length, $start, $length, $is_true ) {
		$this->name            = $name;
		$this->value_starts_at = $value_start;
		$this->value_length    = $value_length;
		$this->start           = $start;
		$this->length          = $length;
		$this->is_true         = $is_true;
	}
}

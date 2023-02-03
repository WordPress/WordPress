<?php
/**
 * HTML Tag Processor: Attribute token structure class.
 *
 * @package WordPress
 * @subpackage HTML-API
 * @since 6.2.0
 */

/**
 * Data structure for the attribute token that allows to drastically improve performance.
 *
 * This class is for internal usage of the WP_HTML_Tag_Processor class.
 *
 * @access private
 * @since 6.2.0
 *
 * @see WP_HTML_Tag_Processor
 */
class WP_HTML_Attribute_Token {
	/**
	 * Attribute name.
	 *
	 * @since 6.2.0
	 * @var string
	 */
	public $name;

	/**
	 * Attribute value.
	 *
	 * @since 6.2.0
	 * @var int
	 */
	public $value_starts_at;

	/**
	 * How many bytes the value occupies in the input HTML.
	 *
	 * @since 6.2.0
	 * @var int
	 */
	public $value_length;

	/**
	 * The string offset where the attribute name starts.
	 *
	 * @since 6.2.0
	 * @var int
	 */
	public $start;

	/**
	 * The string offset after the attribute value or its name.
	 *
	 * @since 6.2.0
	 * @var int
	 */
	public $end;

	/**
	 * Whether the attribute is a boolean attribute with value `true`.
	 *
	 * @since 6.2.0
	 * @var bool
	 */
	public $is_true;

	/**
	 * Constructor.
	 *
	 * @since 6.2.0
	 *
	 * @param string $name         Attribute name.
	 * @param int    $value_start  Attribute value.
	 * @param int    $value_length Number of bytes attribute value spans.
	 * @param int    $start        The string offset where the attribute name starts.
	 * @param int    $end          The string offset after the attribute value or its name.
	 * @param bool   $is_true      Whether the attribute is a boolean attribute with true value.
	 */
	public function __construct( $name, $value_start, $value_length, $start, $end, $is_true ) {
		$this->name            = $name;
		$this->value_starts_at = $value_start;
		$this->value_length    = $value_length;
		$this->start           = $start;
		$this->end             = $end;
		$this->is_true         = $is_true;
	}
}

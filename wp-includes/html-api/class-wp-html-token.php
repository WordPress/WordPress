<?php
/**
 * HTML API: WP_HTML_Token class
 *
 * @package WordPress
 * @subpackage HTML-API
 * @since 6.4.0
 */

/**
 * Core class used by the HTML processor during HTML parsing
 * for referring to tokens in the input HTML string.
 *
 * This class is designed for internal use by the HTML processor.
 *
 * @since 6.4.0
 *
 * @access private
 *
 * @see WP_HTML_Processor
 */
class WP_HTML_Token {
	/**
	 * Name of bookmark corresponding to source of token in input HTML string.
	 *
	 * Having a bookmark name does not imply that the token still exists. It
	 * may be that the source token and underlying bookmark was wiped out by
	 * some modification to the source HTML.
	 *
	 * @since 6.4.0
	 *
	 * @var string
	 */
	public $bookmark_name = null;

	/**
	 * Name of node; lowercase names such as "marker" are not HTML elements.
	 *
	 * For HTML elements/tags this value should come from WP_HTML_Processor::get_tag().
	 *
	 * @since 6.4.0
	 *
	 * @see WP_HTML_Processor::get_tag()
	 *
	 * @var string
	 */
	public $node_name = null;

	/**
	 * Whether node contains the self-closing flag.
	 *
	 * A node may have a self-closing flag when it shouldn't. This value
	 * only reports if the flag is present in the original HTML.
	 *
	 * @since 6.4.0
	 *
	 * @see https://html.spec.whatwg.org/#self-closing-flag
	 *
	 * @var bool
	 */
	public $has_self_closing_flag = false;

	/**
	 * Called when token is garbage-collected or otherwise destroyed.
	 *
	 * @var callable|null
	 */
	public $on_destroy = null;

	/**
	 * Constructor - creates a reference to a token in some external HTML string.
	 *
	 * @since 6.4.0
	 *
	 * @param string   $bookmark_name         Name of bookmark corresponding to location in HTML where token is found.
	 * @param string   $node_name             Name of node token represents; if uppercase, an HTML element; if lowercase, a special value like "marker".
	 * @param bool     $has_self_closing_flag Whether the source token contains the self-closing flag, regardless of whether it's valid.
	 * @param callable $on_destroy            Function to call when destroying token, useful for releasing the bookmark.
	 */
	public function __construct( $bookmark_name, $node_name, $has_self_closing_flag, $on_destroy = null ) {
		$this->bookmark_name         = $bookmark_name;
		$this->node_name             = $node_name;
		$this->has_self_closing_flag = $has_self_closing_flag;
		$this->on_destroy            = $on_destroy;
	}

	/**
	 * Destructor.
	 *
	 * @since 6.4.0
	 */
	public function __destruct() {
		if ( is_callable( $this->on_destroy ) ) {
			call_user_func( $this->on_destroy, $this->bookmark_name );
		}
	}

	/**
	 * Wakeup magic method.
	 *
	 * @since 6.4.2
	 */
	public function __wakeup() {
		throw new \LogicException( __CLASS__ . ' should never be unserialized' );
	}
}

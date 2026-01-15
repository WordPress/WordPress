<?php
/**
 * HTML API: WP_HTML_Unsupported_Exception class
 *
 * @package WordPress
 * @subpackage HTML-API
 * @since 6.4.0
 */

/**
 * Core class used by the HTML processor during HTML parsing
 * for indicating that a given operation is unsupported.
 *
 * This class is designed for internal use by the HTML processor.
 *
 * The HTML API aims to operate in compliance with the HTML5
 * specification, but does not implement the full specification.
 * In cases where it lacks support it should not cause breakage
 * or unexpected behavior. In the cases where it recognizes that
 * it cannot proceed, this class is used to abort from any
 * operation and signify that the given HTML cannot be processed.
 *
 * @since 6.4.0
 * @since 6.7.0 Gained contextual information for use in debugging parse failures.
 *
 * @access private
 *
 * @see WP_HTML_Processor
 */
class WP_HTML_Unsupported_Exception extends Exception {
	/**
	 * Name of the matched token when the exception was raised,
	 * if matched on a token.
	 *
	 * This does not imply that the token itself was unsupported, but it
	 * may have been the case that the token triggered part of the HTML
	 * parsing that isn't supported, such as the adoption agency algorithm.
	 *
	 * @since 6.7.0
	 *
	 * @var string
	 */
	public $token_name;

	/**
	 * Number of bytes into the input HTML document where the parser was
	 * parsing when the exception was raised.
	 *
	 * Use this to reconstruct context for the failure.
	 *
	 * @since 6.7.0
	 *
	 * @var int
	 */
	public $token_at;

	/**
	 * Full raw text of the matched token when the exception was raised,
	 * if matched on a token.
	 *
	 * Whereas the `$token_name` will be normalized, this contains the full
	 * raw text of the token, including original casing, duplicated attributes,
	 * and other syntactic variations that are normally abstracted in the HTML API.
	 *
	 * @since 6.7.0
	 *
	 * @var string
	 */
	public $token;

	/**
	 * Stack of open elements when the exception was raised.
	 *
	 * Use this to trace the parsing circumstances which led to the exception.
	 *
	 * @since 6.7.0
	 *
	 * @var string[]
	 */
	public $stack_of_open_elements = array();

	/**
	 * List of active formatting elements when the exception was raised.
	 *
	 * Use this to trace the parsing circumstances which led to the exception.
	 *
	 * @since 6.7.0
	 *
	 * @var string[]
	 */
	public $active_formatting_elements = array();

	/**
	 * Constructor function.
	 *
	 * @since 6.7.0
	 *
	 * @param string   $message                    Brief message explaining what is unsupported, the reason this exception was raised.
	 * @param string   $token_name                 Normalized name of matched token when this exception was raised.
	 * @param int      $token_at                   Number of bytes into source HTML document where matched token starts.
	 * @param string   $token                      Full raw text of matched token when this exception was raised.
	 * @param string[] $stack_of_open_elements     Stack of open elements when this exception was raised.
	 * @param string[] $active_formatting_elements List of active formatting elements when this exception was raised.
	 */
	public function __construct( string $message, string $token_name, int $token_at, string $token, array $stack_of_open_elements, array $active_formatting_elements ) {
		parent::__construct( $message );

		$this->token_name = $token_name;
		$this->token_at   = $token_at;
		$this->token      = $token;

		$this->stack_of_open_elements     = $stack_of_open_elements;
		$this->active_formatting_elements = $active_formatting_elements;
	}
}

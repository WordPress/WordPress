<?php
/**
 * HTML API: WP_HTML_Stack_Event class
 *
 * @package WordPress
 * @subpackage HTML-API
 * @since 6.6.0
 */

/**
 * Core class used by the HTML Processor as a record for stack operations.
 *
 * This class is for internal usage of the WP_HTML_Processor class.
 *
 * @access private
 * @since 6.6.0
 *
 * @see WP_HTML_Processor
 */
class WP_HTML_Stack_Event {
	/**
	 * Refers to popping an element off of the stack of open elements.
	 *
	 * @since 6.6.0
	 */
	const POP = 'pop';

	/**
	 * Refers to pushing an element onto the stack of open elements.
	 *
	 * @since 6.6.0
	 */
	const PUSH = 'push';

	/**
	 * References the token associated with the stack push event,
	 * even if this is a pop event for that element.
	 *
	 * @since 6.6.0
	 *
	 * @var WP_HTML_Token
	 */
	public $token;

	/**
	 * Indicates which kind of stack operation this event represents.
	 *
	 * May be one of the class constants.
	 *
	 * @since 6.6.0
	 *
	 * @see self::POP
	 * @see self::PUSH
	 *
	 * @var string
	 */
	public $operation;

	/**
	 * Indicates if the stack element is a real or virtual node.
	 *
	 * @since 6.6.0
	 *
	 * @var string
	 */
	public $provenance;

	/**
	 * Constructor function.
	 *
	 * @since 6.6.0
	 *
	 * @param WP_HTML_Token $token      Token associated with stack event, always an opening token.
	 * @param string        $operation  One of self::PUSH or self::POP.
	 * @param string        $provenance "virtual" or "real".
	 */
	public function __construct( $token, $operation, $provenance ) {
		$this->token      = $token;
		$this->operation  = $operation;
		$this->provenance = $provenance;
	}
}

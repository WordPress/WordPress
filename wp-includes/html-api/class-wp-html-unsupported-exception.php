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
 *
 * @access private
 *
 * @see WP_HTML_Processor
 */
class WP_HTML_Unsupported_Exception extends Exception {

}

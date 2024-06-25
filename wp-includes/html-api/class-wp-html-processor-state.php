<?php
/**
 * HTML API: WP_HTML_Processor_State class
 *
 * @package WordPress
 * @subpackage HTML-API
 * @since 6.4.0
 */

/**
 * Core class used by the HTML processor during HTML parsing
 * for managing the internal parsing state.
 *
 * This class is designed for internal use by the HTML processor.
 *
 * @since 6.4.0
 *
 * @access private
 *
 * @see WP_HTML_Processor
 */
class WP_HTML_Processor_State {
	/*
	 * Insertion mode constants.
	 *
	 * These constants exist and are named to make it easier to
	 * discover and recognize the supported insertion modes in
	 * the parser.
	 *
	 * Out of all the possible insertion modes, only those
	 * supported by the parser are listed here. As support
	 * is added to the parser for more modes, add them here
	 * following the same naming and value pattern.
	 *
	 * @see https://html.spec.whatwg.org/#the-insertion-mode
	 */

	/**
	 * Initial insertion mode for full HTML parser.
	 *
	 * @since 6.4.0
	 *
	 * @see https://html.spec.whatwg.org/#the-initial-insertion-mode
	 * @see WP_HTML_Processor_State::$insertion_mode
	 *
	 * @var string
	 */
	const INSERTION_MODE_INITIAL = 'insertion-mode-initial';

	/**
	 * In body insertion mode for full HTML parser.
	 *
	 * @since 6.4.0
	 *
	 * @see https://html.spec.whatwg.org/#parsing-main-inbody
	 * @see WP_HTML_Processor_State::$insertion_mode
	 *
	 * @var string
	 */
	const INSERTION_MODE_IN_BODY = 'insertion-mode-in-body';

	/**
	 * Tracks open elements while scanning HTML.
	 *
	 * This property is initialized in the constructor and never null.
	 *
	 * @since 6.4.0
	 *
	 * @see https://html.spec.whatwg.org/#stack-of-open-elements
	 *
	 * @var WP_HTML_Open_Elements
	 */
	public $stack_of_open_elements = null;

	/**
	 * Tracks open formatting elements, used to handle mis-nested formatting element tags.
	 *
	 * This property is initialized in the constructor and never null.
	 *
	 * @since 6.4.0
	 *
	 * @see https://html.spec.whatwg.org/#list-of-active-formatting-elements
	 *
	 * @var WP_HTML_Active_Formatting_Elements
	 */
	public $active_formatting_elements = null;

	/**
	 * Refers to the currently-matched tag, if any.
	 *
	 * @since 6.4.0
	 *
	 * @var WP_HTML_Token|null
	 */
	public $current_token = null;

	/**
	 * Tree construction insertion mode.
	 *
	 * @since 6.4.0
	 *
	 * @see https://html.spec.whatwg.org/#insertion-mode
	 *
	 * @var string
	 */
	public $insertion_mode = self::INSERTION_MODE_INITIAL;

	/**
	 * Context node initializing fragment parser, if created as a fragment parser.
	 *
	 * @since 6.4.0
	 *
	 * @see https://html.spec.whatwg.org/#concept-frag-parse-context
	 *
	 * @var [string, array]|null
	 */
	public $context_node = null;

	/**
	 * The frameset-ok flag indicates if a `FRAMESET` element is allowed in the current state.
	 *
	 * > The frameset-ok flag is set to "ok" when the parser is created. It is set to "not ok" after certain tokens are seen.
	 *
	 * @since 6.4.0
	 *
	 * @see https://html.spec.whatwg.org/#frameset-ok-flag
	 *
	 * @var bool
	 */
	public $frameset_ok = true;

	/**
	 * Constructor - creates a new and empty state value.
	 *
	 * @since 6.4.0
	 *
	 * @see WP_HTML_Processor
	 */
	public function __construct() {
		$this->stack_of_open_elements     = new WP_HTML_Open_Elements();
		$this->active_formatting_elements = new WP_HTML_Active_Formatting_Elements();
	}
}

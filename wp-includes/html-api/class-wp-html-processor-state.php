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
	 * Before HTML insertion mode for full HTML parser.
	 *
	 * @since 6.7.0
	 *
	 * @see https://html.spec.whatwg.org/#the-before-html-insertion-mode
	 * @see WP_HTML_Processor_State::$insertion_mode
	 *
	 * @var string
	 */
	const INSERTION_MODE_BEFORE_HTML = 'insertion-mode-before-html';

	/**
	 * Before head insertion mode for full HTML parser.
	 *
	 * @since 6.7.0
	 *
	 * @see https://html.spec.whatwg.org/#parsing-main-beforehead
	 * @see WP_HTML_Processor_State::$insertion_mode
	 *
	 * @var string
	 */
	const INSERTION_MODE_BEFORE_HEAD = 'insertion-mode-before-head';

	/**
	 * In head insertion mode for full HTML parser.
	 *
	 * @since 6.7.0
	 *
	 * @see https://html.spec.whatwg.org/#parsing-main-inhead
	 * @see WP_HTML_Processor_State::$insertion_mode
	 *
	 * @var string
	 */
	const INSERTION_MODE_IN_HEAD = 'insertion-mode-in-head';

	/**
	 * In head noscript insertion mode for full HTML parser.
	 *
	 * @since 6.7.0
	 *
	 * @see https://html.spec.whatwg.org/#parsing-main-inheadnoscript
	 * @see WP_HTML_Processor_State::$insertion_mode
	 *
	 * @var string
	 */
	const INSERTION_MODE_IN_HEAD_NOSCRIPT = 'insertion-mode-in-head-noscript';

	/**
	 * After head insertion mode for full HTML parser.
	 *
	 * @since 6.7.0
	 *
	 * @see https://html.spec.whatwg.org/#parsing-main-afterhead
	 * @see WP_HTML_Processor_State::$insertion_mode
	 *
	 * @var string
	 */
	const INSERTION_MODE_AFTER_HEAD = 'insertion-mode-after-head';

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
	 * In table insertion mode for full HTML parser.
	 *
	 * @since 6.7.0
	 *
	 * @see https://html.spec.whatwg.org/#parsing-main-intable
	 * @see WP_HTML_Processor_State::$insertion_mode
	 *
	 * @var string
	 */
	const INSERTION_MODE_IN_TABLE = 'insertion-mode-in-table';

	/**
	 * In table text insertion mode for full HTML parser.
	 *
	 * @since 6.7.0
	 *
	 * @see https://html.spec.whatwg.org/#parsing-main-intabletext
	 * @see WP_HTML_Processor_State::$insertion_mode
	 *
	 * @var string
	 */
	const INSERTION_MODE_IN_TABLE_TEXT = 'insertion-mode-in-table-text';

	/**
	 * In caption insertion mode for full HTML parser.
	 *
	 * @since 6.7.0
	 *
	 * @see https://html.spec.whatwg.org/#parsing-main-incaption
	 * @see WP_HTML_Processor_State::$insertion_mode
	 *
	 * @var string
	 */
	const INSERTION_MODE_IN_CAPTION = 'insertion-mode-in-caption';

	/**
	 * In column group insertion mode for full HTML parser.
	 *
	 * @since 6.7.0
	 *
	 * @see https://html.spec.whatwg.org/#parsing-main-incolumngroup
	 * @see WP_HTML_Processor_State::$insertion_mode
	 *
	 * @var string
	 */
	const INSERTION_MODE_IN_COLUMN_GROUP = 'insertion-mode-in-column-group';

	/**
	 * In table body insertion mode for full HTML parser.
	 *
	 * @since 6.7.0
	 *
	 * @see https://html.spec.whatwg.org/#parsing-main-intablebody
	 * @see WP_HTML_Processor_State::$insertion_mode
	 *
	 * @var string
	 */
	const INSERTION_MODE_IN_TABLE_BODY = 'insertion-mode-in-table-body';

	/**
	 * In row insertion mode for full HTML parser.
	 *
	 * @since 6.7.0
	 *
	 * @see https://html.spec.whatwg.org/#parsing-main-inrow
	 * @see WP_HTML_Processor_State::$insertion_mode
	 *
	 * @var string
	 */
	const INSERTION_MODE_IN_ROW = 'insertion-mode-in-row';

	/**
	 * In cell insertion mode for full HTML parser.
	 *
	 * @since 6.7.0
	 *
	 * @see https://html.spec.whatwg.org/#parsing-main-incell
	 * @see WP_HTML_Processor_State::$insertion_mode
	 *
	 * @var string
	 */
	const INSERTION_MODE_IN_CELL = 'insertion-mode-in-cell';

	/**
	 * In select insertion mode for full HTML parser.
	 *
	 * @since 6.7.0
	 *
	 * @see https://html.spec.whatwg.org/#parsing-main-inselect
	 * @see WP_HTML_Processor_State::$insertion_mode
	 *
	 * @var string
	 */
	const INSERTION_MODE_IN_SELECT = 'insertion-mode-in-select';

	/**
	 * In select in table insertion mode for full HTML parser.
	 *
	 * @since 6.7.0
	 *
	 * @see https://html.spec.whatwg.org/#parsing-main-inselectintable
	 * @see WP_HTML_Processor_State::$insertion_mode
	 *
	 * @var string
	 */
	const INSERTION_MODE_IN_SELECT_IN_TABLE = 'insertion-mode-in-select-in-table';

	/**
	 * In template insertion mode for full HTML parser.
	 *
	 * @since 6.7.0
	 *
	 * @see https://html.spec.whatwg.org/#parsing-main-intemplate
	 * @see WP_HTML_Processor_State::$insertion_mode
	 *
	 * @var string
	 */
	const INSERTION_MODE_IN_TEMPLATE = 'insertion-mode-in-template';

	/**
	 * After body insertion mode for full HTML parser.
	 *
	 * @since 6.7.0
	 *
	 * @see https://html.spec.whatwg.org/#parsing-main-afterbody
	 * @see WP_HTML_Processor_State::$insertion_mode
	 *
	 * @var string
	 */
	const INSERTION_MODE_AFTER_BODY = 'insertion-mode-after-body';

	/**
	 * In frameset insertion mode for full HTML parser.
	 *
	 * @since 6.7.0
	 *
	 * @see https://html.spec.whatwg.org/#parsing-main-inframeset
	 * @see WP_HTML_Processor_State::$insertion_mode
	 *
	 * @var string
	 */
	const INSERTION_MODE_IN_FRAMESET = 'insertion-mode-in-frameset';

	/**
	 * After frameset insertion mode for full HTML parser.
	 *
	 * @since 6.7.0
	 *
	 * @see https://html.spec.whatwg.org/#parsing-main-afterframeset
	 * @see WP_HTML_Processor_State::$insertion_mode
	 *
	 * @var string
	 */
	const INSERTION_MODE_AFTER_FRAMESET = 'insertion-mode-after-frameset';

	/**
	 * After after body insertion mode for full HTML parser.
	 *
	 * @since 6.7.0
	 *
	 * @see https://html.spec.whatwg.org/#the-after-after-body-insertion-mode
	 * @see WP_HTML_Processor_State::$insertion_mode
	 *
	 * @var string
	 */
	const INSERTION_MODE_AFTER_AFTER_BODY = 'insertion-mode-after-after-body';

	/**
	 * After after frameset insertion mode for full HTML parser.
	 *
	 * @since 6.7.0
	 *
	 * @see https://html.spec.whatwg.org/#the-after-after-frameset-insertion-mode
	 * @see WP_HTML_Processor_State::$insertion_mode
	 *
	 * @var string
	 */
	const INSERTION_MODE_AFTER_AFTER_FRAMESET = 'insertion-mode-after-after-frameset';

	/**
	 * In foreign content insertion mode for full HTML parser.
	 *
	 * @since 6.7.0
	 *
	 * @see https://html.spec.whatwg.org/#parsing-main-inforeign
	 * @see WP_HTML_Processor_State::$insertion_mode
	 *
	 * @var string
	 */
	const INSERTION_MODE_IN_FOREIGN_CONTENT = 'insertion-mode-in-foreign-content';

	/**
	 * The stack of template insertion modes.
	 *
	 * @since 6.7.0
	 *
	 * @see https://html.spec.whatwg.org/#the-insertion-mode:stack-of-template-insertion-modes
	 *
	 * @var array<string>
	 */
	public $stack_of_template_insertion_modes = array();

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
	 * HEAD element pointer.
	 *
	 * @since 6.7.0
	 *
	 * @see https://html.spec.whatwg.org/multipage/parsing.html#head-element-pointer
	 *
	 * @var WP_HTML_Token|null
	 */
	public $head_element = null;

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

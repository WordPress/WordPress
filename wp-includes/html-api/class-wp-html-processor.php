<?php
/**
 * HTML API: WP_HTML_Processor class
 *
 * @package WordPress
 * @subpackage HTML-API
 * @since 6.4.0
 */

/**
 * Core class used to safely parse and modify an HTML document.
 *
 * The HTML Processor class properly parses and modifies HTML5 documents.
 *
 * It supports a subset of the HTML5 specification, and when it encounters
 * unsupported markup, it aborts early to avoid unintentionally breaking
 * the document. The HTML Processor should never break an HTML document.
 *
 * While the `WP_HTML_Tag_Processor` is a valuable tool for modifying
 * attributes on individual HTML tags, the HTML Processor is more capable
 * and useful for the following operations:
 *
 *  - Querying based on nested HTML structure.
 *
 * Eventually the HTML Processor will also support:
 *  - Wrapping a tag in surrounding HTML.
 *  - Unwrapping a tag by removing its parent.
 *  - Inserting and removing nodes.
 *  - Reading and changing inner content.
 *  - Navigating up or around HTML structure.
 *
 * ## Usage
 *
 * Use of this class requires three steps:
 *
 *   1. Call a static creator method with your input HTML document.
 *   2. Find the location in the document you are looking for.
 *   3. Request changes to the document at that location.
 *
 * Example:
 *
 *     $processor = WP_HTML_Processor::create_fragment( $html );
 *     if ( $processor->next_tag( array( 'breadcrumbs' => array( 'DIV', 'FIGURE', 'IMG' ) ) ) ) {
 *         $processor->add_class( 'responsive-image' );
 *     }
 *
 * #### Breadcrumbs
 *
 * Breadcrumbs represent the stack of open elements from the root
 * of the document or fragment down to the currently-matched node,
 * if one is currently selected. Call WP_HTML_Processor::get_breadcrumbs()
 * to inspect the breadcrumbs for a matched tag.
 *
 * Breadcrumbs can specify nested HTML structure and are equivalent
 * to a CSS selector comprising tag names separated by the child
 * combinator, such as "DIV > FIGURE > IMG".
 *
 * Since all elements find themselves inside a full HTML document
 * when parsed, the return value from `get_breadcrumbs()` will always
 * contain any implicit outermost elements. For example, when parsing
 * with `create_fragment()` in the `BODY` context (the default), any
 * tag in the given HTML document will contain `array( 'HTML', 'BODY', … )`
 * in its breadcrumbs.
 *
 * Despite containing the implied outermost elements in their breadcrumbs,
 * tags may be found with the shortest-matching breadcrumb query. That is,
 * `array( 'IMG' )` matches all IMG elements and `array( 'P', 'IMG' )`
 * matches all IMG elements directly inside a P element. To ensure that no
 * partial matches erroneously match it's possible to specify in a query
 * the full breadcrumb match all the way down from the root HTML element.
 *
 * Example:
 *
 *     $html = '<figure><img><figcaption>A <em>lovely</em> day outside</figcaption></figure>';
 *     //               ----- Matches here.
 *     $processor->next_tag( array( 'breadcrumbs' => array( 'FIGURE', 'IMG' ) ) );
 *
 *     $html = '<figure><img><figcaption>A <em>lovely</em> day outside</figcaption></figure>';
 *     //                                  ---- Matches here.
 *     $processor->next_tag( array( 'breadcrumbs' => array( 'FIGURE', 'FIGCAPTION', 'EM' ) ) );
 *
 *     $html = '<div><img></div><img>';
 *     //                       ----- Matches here, because IMG must be a direct child of the implicit BODY.
 *     $processor->next_tag( array( 'breadcrumbs' => array( 'BODY', 'IMG' ) ) );
 *
 * ## HTML Support
 *
 * This class implements a small part of the HTML5 specification.
 * It's designed to operate within its support and abort early whenever
 * encountering circumstances it can't properly handle. This is
 * the principle way in which this class remains as simple as possible
 * without cutting corners and breaking compliance.
 *
 * ### Supported elements
 *
 * If any unsupported element appears in the HTML input the HTML Processor
 * will abort early and stop all processing. This draconian measure ensures
 * that the HTML Processor won't break any HTML it doesn't fully understand.
 *
 * The following list specifies the HTML tags that _are_ supported:
 *
 *  - Containers: ADDRESS, BLOCKQUOTE, DETAILS, DIALOG, DIV, FOOTER, HEADER, MAIN, MENU, SPAN, SUMMARY.
 *  - Custom elements: All custom elements are supported. :)
 *  - Form elements: BUTTON, DATALIST, FIELDSET, INPUT, LABEL, LEGEND, METER, PROGRESS, SEARCH.
 *  - Formatting elements: B, BIG, CODE, EM, FONT, I, PRE, SMALL, STRIKE, STRONG, TT, U, WBR.
 *  - Heading elements: H1, H2, H3, H4, H5, H6, HGROUP.
 *  - Links: A.
 *  - Lists: DD, DL, DT, LI, OL, UL.
 *  - Media elements: AUDIO, CANVAS, EMBED, FIGCAPTION, FIGURE, IMG, MAP, PICTURE, SOURCE, TRACK, VIDEO.
 *  - Paragraph: BR, P.
 *  - Phrasing elements: ABBR, AREA, BDI, BDO, CITE, DATA, DEL, DFN, INS, MARK, OUTPUT, Q, SAMP, SUB, SUP, TIME, VAR.
 *  - Sectioning elements: ARTICLE, ASIDE, HR, NAV, SECTION.
 *  - Templating elements: SLOT.
 *  - Text decoration: RUBY.
 *  - Deprecated elements: ACRONYM, BLINK, CENTER, DIR, ISINDEX, KEYGEN, LISTING, MULTICOL, NEXTID, PARAM, SPACER.
 *
 * ### Supported markup
 *
 * Some kinds of non-normative HTML involve reconstruction of formatting elements and
 * re-parenting of mis-nested elements. For example, a DIV tag found inside a TABLE
 * may in fact belong _before_ the table in the DOM. If the HTML Processor encounters
 * such a case it will stop processing.
 *
 * The following list specifies HTML markup that _is_ supported:
 *
 *  - Markup involving only those tags listed above.
 *  - Fully-balanced and non-overlapping tags.
 *  - HTML with unexpected tag closers.
 *  - Some unbalanced or overlapping tags.
 *  - P tags after unclosed P tags.
 *  - BUTTON tags after unclosed BUTTON tags.
 *  - A tags after unclosed A tags that don't involve any active formatting elements.
 *
 * @since 6.4.0
 *
 * @see WP_HTML_Tag_Processor
 * @see https://html.spec.whatwg.org/
 */
class WP_HTML_Processor extends WP_HTML_Tag_Processor {
	/**
	 * The maximum number of bookmarks allowed to exist at any given time.
	 *
	 * HTML processing requires more bookmarks than basic tag processing,
	 * so this class constant from the Tag Processor is overwritten.
	 *
	 * @since 6.4.0
	 *
	 * @var int
	 */
	const MAX_BOOKMARKS = 100;

	/**
	 * Holds the working state of the parser, including the stack of
	 * open elements and the stack of active formatting elements.
	 *
	 * Initialized in the constructor.
	 *
	 * @since 6.4.0
	 *
	 * @var WP_HTML_Processor_State
	 */
	private $state = null;

	/**
	 * Used to create unique bookmark names.
	 *
	 * This class sets a bookmark for every tag in the HTML document that it encounters.
	 * The bookmark name is auto-generated and increments, starting with `1`. These are
	 * internal bookmarks and are automatically released when the referring WP_HTML_Token
	 * goes out of scope and is garbage-collected.
	 *
	 * @since 6.4.0
	 *
	 * @see WP_HTML_Processor::$release_internal_bookmark_on_destruct
	 *
	 * @var int
	 */
	private $bookmark_counter = 0;

	/**
	 * Stores an explanation for why something failed, if it did.
	 *
	 * @see self::get_last_error
	 *
	 * @since 6.4.0
	 *
	 * @var string|null
	 */
	private $last_error = null;

	/**
	 * Releases a bookmark when PHP garbage-collects its wrapping WP_HTML_Token instance.
	 *
	 * This function is created inside the class constructor so that it can be passed to
	 * the stack of open elements and the stack of active formatting elements without
	 * exposing it as a public method on the class.
	 *
	 * @since 6.4.0
	 *
	 * @var closure
	 */
	private $release_internal_bookmark_on_destruct = null;

	/**
	 * Stores stack events which arise during parsing of the
	 * HTML document, which will then supply the "match" events.
	 *
	 * @since 6.6.0
	 *
	 * @var WP_HTML_Stack_Event[]
	 */
	private $element_queue = array();

	/**
	 * Current stack event, if set, representing a matched token.
	 *
	 * Because the parser may internally point to a place further along in a document
	 * than the nodes which have already been processed (some "virtual" nodes may have
	 * appeared while scanning the HTML document), this will point at the "current" node
	 * being processed. It comes from the front of the element queue.
	 *
	 * @since 6.6.0
	 *
	 * @var ?WP_HTML_Stack_Event
	 */
	private $current_element = null;

	/**
	 * Context node if created as a fragment parser.
	 *
	 * @var ?WP_HTML_Token
	 */
	private $context_node = null;

	/**
	 * Whether the parser has yet processed the context node,
	 * if created as a fragment parser.
	 *
	 * The context node will be initially pushed onto the stack of open elements,
	 * but when created as a fragment parser, this context element (and the implicit
	 * HTML document node above it) should not be exposed as a matched token or node.
	 *
	 * This boolean indicates whether the processor should skip over the current
	 * node in its initial search for the first node created from the input HTML.
	 *
	 * @var bool
	 */
	private $has_seen_context_node = false;

	/*
	 * Public Interface Functions
	 */

	/**
	 * Creates an HTML processor in the fragment parsing mode.
	 *
	 * Use this for cases where you are processing chunks of HTML that
	 * will be found within a bigger HTML document, such as rendered
	 * block output that exists within a post, `the_content` inside a
	 * rendered site layout.
	 *
	 * Fragment parsing occurs within a context, which is an HTML element
	 * that the document will eventually be placed in. It becomes important
	 * when special elements have different rules than others, such as inside
	 * a TEXTAREA or a TITLE tag where things that look like tags are text,
	 * or inside a SCRIPT tag where things that look like HTML syntax are JS.
	 *
	 * The context value should be a representation of the tag into which the
	 * HTML is found. For most cases this will be the body element. The HTML
	 * form is provided because a context element may have attributes that
	 * impact the parse, such as with a SCRIPT tag and its `type` attribute.
	 *
	 * ## Current HTML Support
	 *
	 *  - The only supported context is `<body>`, which is the default value.
	 *  - The only supported document encoding is `UTF-8`, which is the default value.
	 *
	 * @since 6.4.0
	 * @since 6.6.0 Returns `static` instead of `self` so it can create subclass instances.
	 *
	 * @param string $html     Input HTML fragment to process.
	 * @param string $context  Context element for the fragment, must be default of `<body>`.
	 * @param string $encoding Text encoding of the document; must be default of 'UTF-8'.
	 * @return static|null The created processor if successful, otherwise null.
	 */
	public static function create_fragment( $html, $context = '<body>', $encoding = 'UTF-8' ) {
		if ( '<body>' !== $context || 'UTF-8' !== $encoding ) {
			return null;
		}

		$processor                        = new static( $html, self::CONSTRUCTOR_UNLOCK_CODE );
		$processor->state->context_node   = array( 'BODY', array() );
		$processor->state->insertion_mode = WP_HTML_Processor_State::INSERTION_MODE_IN_BODY;

		// @todo Create "fake" bookmarks for non-existent but implied nodes.
		$processor->bookmarks['root-node']    = new WP_HTML_Span( 0, 0 );
		$processor->bookmarks['context-node'] = new WP_HTML_Span( 0, 0 );

		$processor->state->stack_of_open_elements->push(
			new WP_HTML_Token(
				'root-node',
				'HTML',
				false
			)
		);

		$context_node = new WP_HTML_Token(
			'context-node',
			$processor->state->context_node[0],
			false
		);

		$processor->state->stack_of_open_elements->push( $context_node );
		$processor->context_node = $context_node;

		return $processor;
	}

	/**
	 * Constructor.
	 *
	 * Do not use this method. Use the static creator methods instead.
	 *
	 * @access private
	 *
	 * @since 6.4.0
	 *
	 * @see WP_HTML_Processor::create_fragment()
	 *
	 * @param string      $html                                  HTML to process.
	 * @param string|null $use_the_static_create_methods_instead This constructor should not be called manually.
	 */
	public function __construct( $html, $use_the_static_create_methods_instead = null ) {
		parent::__construct( $html );

		if ( self::CONSTRUCTOR_UNLOCK_CODE !== $use_the_static_create_methods_instead ) {
			_doing_it_wrong(
				__METHOD__,
				sprintf(
					/* translators: %s: WP_HTML_Processor::create_fragment(). */
					__( 'Call %s to create an HTML Processor instead of calling the constructor directly.' ),
					'<code>WP_HTML_Processor::create_fragment()</code>'
				),
				'6.4.0'
			);
		}

		$this->state = new WP_HTML_Processor_State();

		$this->state->stack_of_open_elements->set_push_handler(
			function ( WP_HTML_Token $token ) {
				$is_virtual            = ! isset( $this->state->current_token ) || $this->is_tag_closer();
				$same_node             = isset( $this->state->current_token ) && $token->node_name === $this->state->current_token->node_name;
				$provenance            = ( ! $same_node || $is_virtual ) ? 'virtual' : 'real';
				$this->element_queue[] = new WP_HTML_Stack_Event( $token, WP_HTML_Stack_Event::PUSH, $provenance );
			}
		);

		$this->state->stack_of_open_elements->set_pop_handler(
			function ( WP_HTML_Token $token ) {
				$is_virtual            = ! isset( $this->state->current_token ) || ! $this->is_tag_closer();
				$same_node             = isset( $this->state->current_token ) && $token->node_name === $this->state->current_token->node_name;
				$provenance            = ( ! $same_node || $is_virtual ) ? 'virtual' : 'real';
				$this->element_queue[] = new WP_HTML_Stack_Event( $token, WP_HTML_Stack_Event::POP, $provenance );
			}
		);

		/*
		 * Create this wrapper so that it's possible to pass
		 * a private method into WP_HTML_Token classes without
		 * exposing it to any public API.
		 */
		$this->release_internal_bookmark_on_destruct = function ( $name ) {
			parent::release_bookmark( $name );
		};
	}

	/**
	 * Returns the last error, if any.
	 *
	 * Various situations lead to parsing failure but this class will
	 * return `false` in all those cases. To determine why something
	 * failed it's possible to request the last error. This can be
	 * helpful to know to distinguish whether a given tag couldn't
	 * be found or if content in the document caused the processor
	 * to give up and abort processing.
	 *
	 * Example
	 *
	 *     $processor = WP_HTML_Processor::create_fragment( '<template><strong><button><em><p><em>' );
	 *     false === $processor->next_tag();
	 *     WP_HTML_Processor::ERROR_UNSUPPORTED === $processor->get_last_error();
	 *
	 * @since 6.4.0
	 *
	 * @see self::ERROR_UNSUPPORTED
	 * @see self::ERROR_EXCEEDED_MAX_BOOKMARKS
	 *
	 * @return string|null The last error, if one exists, otherwise null.
	 */
	public function get_last_error() {
		return $this->last_error;
	}

	/**
	 * Finds the next tag matching the $query.
	 *
	 * @todo Support matching the class name and tag name.
	 *
	 * @since 6.4.0
	 * @since 6.6.0 Visits all tokens, including virtual ones.
	 *
	 * @throws Exception When unable to allocate a bookmark for the next token in the input HTML document.
	 *
	 * @param array|string|null $query {
	 *     Optional. Which tag name to find, having which class, etc. Default is to find any tag.
	 *
	 *     @type string|null $tag_name     Which tag to find, or `null` for "any tag."
	 *     @type string      $tag_closers  'visit' to pause at tag closers, 'skip' or unset to only visit openers.
	 *     @type int|null    $match_offset Find the Nth tag matching all search criteria.
	 *                                     1 for "first" tag, 3 for "third," etc.
	 *                                     Defaults to first tag.
	 *     @type string|null $class_name   Tag must contain this whole class name to match.
	 *     @type string[]    $breadcrumbs  DOM sub-path at which element is found, e.g. `array( 'FIGURE', 'IMG' )`.
	 *                                     May also contain the wildcard `*` which matches a single element, e.g. `array( 'SECTION', '*' )`.
	 * }
	 * @return bool Whether a tag was matched.
	 */
	public function next_tag( $query = null ) {
		$visit_closers = isset( $query['tag_closers'] ) && 'visit' === $query['tag_closers'];

		if ( null === $query ) {
			while ( $this->next_token() ) {
				if ( '#tag' !== $this->get_token_type() ) {
					continue;
				}

				if ( ! $this->is_tag_closer() || $visit_closers ) {
					return true;
				}
			}

			return false;
		}

		if ( is_string( $query ) ) {
			$query = array( 'breadcrumbs' => array( $query ) );
		}

		if ( ! is_array( $query ) ) {
			_doing_it_wrong(
				__METHOD__,
				__( 'Please pass a query array to this function.' ),
				'6.4.0'
			);
			return false;
		}

		$needs_class = ( isset( $query['class_name'] ) && is_string( $query['class_name'] ) )
			? $query['class_name']
			: null;

		if ( ! ( array_key_exists( 'breadcrumbs', $query ) && is_array( $query['breadcrumbs'] ) ) ) {
			while ( $this->next_token() ) {
				if ( '#tag' !== $this->get_token_type() ) {
					continue;
				}

				if ( isset( $needs_class ) && ! $this->has_class( $needs_class ) ) {
					continue;
				}

				if ( ! $this->is_tag_closer() || $visit_closers ) {
					return true;
				}
			}

			return false;
		}

		$breadcrumbs  = $query['breadcrumbs'];
		$match_offset = isset( $query['match_offset'] ) ? (int) $query['match_offset'] : 1;

		while ( $match_offset > 0 && $this->next_token() ) {
			if ( '#tag' !== $this->get_token_type() || $this->is_tag_closer() ) {
				continue;
			}

			if ( isset( $needs_class ) && ! $this->has_class( $needs_class ) ) {
				continue;
			}

			if ( $this->matches_breadcrumbs( $breadcrumbs ) && 0 === --$match_offset ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Ensures internal accounting is maintained for HTML semantic rules while
	 * the underlying Tag Processor class is seeking to a bookmark.
	 *
	 * This doesn't currently have a way to represent non-tags and doesn't process
	 * semantic rules for text nodes. For access to the raw tokens consider using
	 * WP_HTML_Tag_Processor instead.
	 *
	 * @since 6.5.0 Added for internal support; do not use.
	 *
	 * @access private
	 *
	 * @return bool
	 */
	public function next_token() {
		$this->current_element = null;

		if ( isset( $this->last_error ) ) {
			return false;
		}

		if ( 'done' !== $this->has_seen_context_node && 0 === count( $this->element_queue ) && ! $this->step() ) {
			while ( 'context-node' !== $this->state->stack_of_open_elements->current_node()->bookmark_name && $this->state->stack_of_open_elements->pop() ) {
				continue;
			}
			$this->has_seen_context_node = 'done';
			return $this->next_token();
		}

		$this->current_element = array_shift( $this->element_queue );
		while ( isset( $this->context_node ) && ! $this->has_seen_context_node ) {
			if ( isset( $this->current_element ) ) {
				if ( $this->context_node === $this->current_element->token && WP_HTML_Stack_Event::PUSH === $this->current_element->operation ) {
					$this->has_seen_context_node = true;
					return $this->next_token();
				}
			}
			$this->current_element = array_shift( $this->element_queue );
		}

		if ( ! isset( $this->current_element ) ) {
			if ( 'done' === $this->has_seen_context_node ) {
				return false;
			} else {
				return $this->next_token();
			}
		}

		if ( isset( $this->context_node ) && WP_HTML_Stack_Event::POP === $this->current_element->operation && $this->context_node === $this->current_element->token ) {
			$this->element_queue   = array();
			$this->current_element = null;
			return false;
		}

		// Avoid sending close events for elements which don't expect a closing.
		if (
			WP_HTML_Stack_Event::POP === $this->current_element->operation &&
			! static::expects_closer( $this->current_element->token )
		) {
			return $this->next_token();
		}

		return true;
	}


	/**
	 * Indicates if the current tag token is a tag closer.
	 *
	 * Example:
	 *
	 *     $p = WP_HTML_Processor::create_fragment( '<div></div>' );
	 *     $p->next_tag( array( 'tag_name' => 'div', 'tag_closers' => 'visit' ) );
	 *     $p->is_tag_closer() === false;
	 *
	 *     $p->next_tag( array( 'tag_name' => 'div', 'tag_closers' => 'visit' ) );
	 *     $p->is_tag_closer() === true;
	 *
	 * @since 6.6.0 Subclassed for HTML Processor.
	 *
	 * @return bool Whether the current tag is a tag closer.
	 */
	public function is_tag_closer() {
		return $this->is_virtual()
			? ( WP_HTML_Stack_Event::POP === $this->current_element->operation && '#tag' === $this->get_token_type() )
			: parent::is_tag_closer();
	}

	/**
	 * Indicates if the currently-matched token is virtual, created by a stack operation
	 * while processing HTML, rather than a token found in the HTML text itself.
	 *
	 * @since 6.6.0
	 *
	 * @return bool Whether the current token is virtual.
	 */
	private function is_virtual() {
		return (
			isset( $this->current_element->provenance ) &&
			'virtual' === $this->current_element->provenance
		);
	}

	/**
	 * Indicates if the currently-matched tag matches the given breadcrumbs.
	 *
	 * A "*" represents a single tag wildcard, where any tag matches, but not no tags.
	 *
	 * At some point this function _may_ support a `**` syntax for matching any number
	 * of unspecified tags in the breadcrumb stack. This has been intentionally left
	 * out, however, to keep this function simple and to avoid introducing backtracking,
	 * which could open up surprising performance breakdowns.
	 *
	 * Example:
	 *
	 *     $processor = WP_HTML_Processor::create_fragment( '<div><span><figure><img></figure></span></div>' );
	 *     $processor->next_tag( 'img' );
	 *     true  === $processor->matches_breadcrumbs( array( 'figure', 'img' ) );
	 *     true  === $processor->matches_breadcrumbs( array( 'span', 'figure', 'img' ) );
	 *     false === $processor->matches_breadcrumbs( array( 'span', 'img' ) );
	 *     true  === $processor->matches_breadcrumbs( array( 'span', '*', 'img' ) );
	 *
	 * @since 6.4.0
	 *
	 * @param string[] $breadcrumbs DOM sub-path at which element is found, e.g. `array( 'FIGURE', 'IMG' )`.
	 *                              May also contain the wildcard `*` which matches a single element, e.g. `array( 'SECTION', '*' )`.
	 * @return bool Whether the currently-matched tag is found at the given nested structure.
	 */
	public function matches_breadcrumbs( $breadcrumbs ) {
		// Everything matches when there are zero constraints.
		if ( 0 === count( $breadcrumbs ) ) {
			return true;
		}

		// Start at the last crumb.
		$crumb = end( $breadcrumbs );

		if ( '*' !== $crumb && $this->get_tag() !== strtoupper( $crumb ) ) {
			return false;
		}

		foreach ( $this->state->stack_of_open_elements->walk_up() as $node ) {
			$crumb = strtoupper( current( $breadcrumbs ) );

			if ( '*' !== $crumb && $node->node_name !== $crumb ) {
				return false;
			}

			if ( false === prev( $breadcrumbs ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Indicates if the currently-matched node expects a closing
	 * token, or if it will self-close on the next step.
	 *
	 * Most HTML elements expect a closer, such as a P element or
	 * a DIV element. Others, like an IMG element are void and don't
	 * have a closing tag. Special elements, such as SCRIPT and STYLE,
	 * are treated just like void tags. Text nodes and self-closing
	 * foreign content will also act just like a void tag, immediately
	 * closing as soon as the processor advances to the next token.
	 *
	 * @since 6.6.0
	 *
	 * @todo When adding support for foreign content, ensure that
	 *       this returns false for self-closing elements in the
	 *       SVG and MathML namespace.
	 *
	 * @param  ?WP_HTML_Token $node Node to examine instead of current node, if provided.
	 * @return bool Whether to expect a closer for the currently-matched node,
	 *              or `null` if not matched on any token.
	 */
	public function expects_closer( $node = null ) {
		$token_name = $node->node_name ?? $this->get_token_name();
		if ( ! isset( $token_name ) ) {
			return null;
		}

		return ! (
			// Comments, text nodes, and other atomic tokens.
			'#' === $token_name[0] ||
			// Doctype declarations.
			'html' === $token_name ||
			// Void elements.
			self::is_void( $token_name ) ||
			// Special atomic elements.
			in_array( $token_name, array( 'IFRAME', 'NOEMBED', 'NOFRAMES', 'SCRIPT', 'STYLE', 'TEXTAREA', 'TITLE', 'XMP' ), true )
		);
	}

	/**
	 * Steps through the HTML document and stop at the next tag, if any.
	 *
	 * @since 6.4.0
	 *
	 * @throws Exception When unable to allocate a bookmark for the next token in the input HTML document.
	 *
	 * @see self::PROCESS_NEXT_NODE
	 * @see self::REPROCESS_CURRENT_NODE
	 *
	 * @param string $node_to_process Whether to parse the next node or reprocess the current node.
	 * @return bool Whether a tag was matched.
	 */
	public function step( $node_to_process = self::PROCESS_NEXT_NODE ) {
		// Refuse to proceed if there was a previous error.
		if ( null !== $this->last_error ) {
			return false;
		}

		if ( self::REPROCESS_CURRENT_NODE !== $node_to_process ) {
			/*
			 * Void elements still hop onto the stack of open elements even though
			 * there's no corresponding closing tag. This is important for managing
			 * stack-based operations such as "navigate to parent node" or checking
			 * on an element's breadcrumbs.
			 *
			 * When moving on to the next node, therefore, if the bottom-most element
			 * on the stack is a void element, it must be closed.
			 *
			 * @todo Once self-closing foreign elements and BGSOUND are supported,
			 *        they must also be implicitly closed here too. BGSOUND is
			 *        special since it's only self-closing if the self-closing flag
			 *        is provided in the opening tag, otherwise it expects a tag closer.
			 */
			$top_node = $this->state->stack_of_open_elements->current_node();
			if ( isset( $top_node ) && ! static::expects_closer( $top_node ) ) {
				$this->state->stack_of_open_elements->pop();
			}
		}

		if ( self::PROCESS_NEXT_NODE === $node_to_process ) {
			parent::next_token();
		}

		// Finish stepping when there are no more tokens in the document.
		if (
			WP_HTML_Tag_Processor::STATE_INCOMPLETE_INPUT === $this->parser_state ||
			WP_HTML_Tag_Processor::STATE_COMPLETE === $this->parser_state
		) {
			return false;
		}

		$this->state->current_token = new WP_HTML_Token(
			$this->bookmark_token(),
			$this->get_token_name(),
			$this->has_self_closing_flag(),
			$this->release_internal_bookmark_on_destruct
		);

		try {
			switch ( $this->state->insertion_mode ) {
				case WP_HTML_Processor_State::INSERTION_MODE_IN_BODY:
					return $this->step_in_body();

				default:
					$this->last_error = self::ERROR_UNSUPPORTED;
					throw new WP_HTML_Unsupported_Exception( "No support for parsing in the '{$this->state->insertion_mode}' state." );
			}
		} catch ( WP_HTML_Unsupported_Exception $e ) {
			/*
			 * Exceptions are used in this class to escape deep call stacks that
			 * otherwise might involve messier calling and return conventions.
			 */
			return false;
		}
	}

	/**
	 * Computes the HTML breadcrumbs for the currently-matched node, if matched.
	 *
	 * Breadcrumbs start at the outermost parent and descend toward the matched element.
	 * They always include the entire path from the root HTML node to the matched element.
	 *
	 * @todo It could be more efficient to expose a generator-based version of this function
	 *       to avoid creating the array copy on tag iteration. If this is done, it would likely
	 *       be more useful to walk up the stack when yielding instead of starting at the top.
	 *
	 * Example
	 *
	 *     $processor = WP_HTML_Processor::create_fragment( '<p><strong><em><img></em></strong></p>' );
	 *     $processor->next_tag( 'IMG' );
	 *     $processor->get_breadcrumbs() === array( 'HTML', 'BODY', 'P', 'STRONG', 'EM', 'IMG' );
	 *
	 * @since 6.4.0
	 *
	 * @return string[]|null Array of tag names representing path to matched node, if matched, otherwise NULL.
	 */
	public function get_breadcrumbs() {
		$breadcrumbs = array();

		foreach ( $this->state->stack_of_open_elements->walk_down() as $stack_item ) {
			$breadcrumbs[] = $stack_item->node_name;
		}

		if ( ! $this->is_virtual() ) {
			return $breadcrumbs;
		}

		foreach ( $this->element_queue as $queue_item ) {
			if ( $this->current_element->token->bookmark_name === $queue_item->token->bookmark_name ) {
				break;
			}

			if ( 'context-node' === $queue_item->token->bookmark_name ) {
				break;
			}

			if ( 'real' === $queue_item->provenance ) {
				break;
			}

			if ( WP_HTML_Stack_Event::PUSH === $queue_item->operation ) {
				$breadcrumbs[] = $queue_item->token->node_name;
			} else {
				array_pop( $breadcrumbs );
			}
		}

		if ( null !== parent::get_token_name() && ! parent::is_tag_closer() ) {
			array_pop( $breadcrumbs );
		}

		// Add the virtual node we're at.
		if ( WP_HTML_Stack_Event::PUSH === $this->current_element->operation ) {
			$breadcrumbs[] = $this->current_element->token->node_name;
		}

		return $breadcrumbs;
	}

	/**
	 * Returns the nesting depth of the current location in the document.
	 *
	 * Example:
	 *
	 *     $processor = WP_HTML_Processor::create_fragment( '<div><p></p></div>' );
	 *     // The processor starts in the BODY context, meaning it has depth from the start: HTML > BODY.
	 *     2 === $processor->get_current_depth();
	 *
	 *     // Opening the DIV element increases the depth.
	 *     $processor->next_token();
	 *     3 === $processor->get_current_depth();
	 *
	 *     // Opening the P element increases the depth.
	 *     $processor->next_token();
	 *     4 === $processor->get_current_depth();
	 *
	 *     // The P element is closed during `next_token()` so the depth is decreased to reflect that.
	 *     $processor->next_token();
	 *     3 === $processor->get_current_depth();
	 *
	 * @since 6.6.0
	 *
	 * @return int Nesting-depth of current location in the document.
	 */
	public function get_current_depth() {
		return $this->is_virtual()
			? count( $this->get_breadcrumbs() )
			: $this->state->stack_of_open_elements->count();
	}

	/**
	 * Parses next element in the 'in body' insertion mode.
	 *
	 * This internal function performs the 'in body' insertion mode
	 * logic for the generalized WP_HTML_Processor::step() function.
	 *
	 * @since 6.4.0
	 *
	 * @throws WP_HTML_Unsupported_Exception When encountering unsupported HTML input.
	 *
	 * @see https://html.spec.whatwg.org/#parsing-main-inbody
	 * @see WP_HTML_Processor::step
	 *
	 * @return bool Whether an element was found.
	 */
	private function step_in_body() {
		$token_name = $this->get_token_name();
		$token_type = $this->get_token_type();
		$op_sigil   = '#tag' === $token_type ? ( parent::is_tag_closer() ? '-' : '+' ) : '';
		$op         = "{$op_sigil}{$token_name}";

		switch ( $op ) {
			case '#comment':
			case '#funky-comment':
			case '#presumptuous-tag':
				$this->insert_html_element( $this->state->current_token );
				return true;

			case '#text':
				$this->reconstruct_active_formatting_elements();

				$current_token = $this->bookmarks[ $this->state->current_token->bookmark_name ];

				/*
				 * > A character token that is U+0000 NULL
				 *
				 * Any successive sequence of NULL bytes is ignored and won't
				 * trigger active format reconstruction. Therefore, if the text
				 * only comprises NULL bytes then the token should be ignored
				 * here, but if there are any other characters in the stream
				 * the active formats should be reconstructed.
				 */
				if (
					1 <= $current_token->length &&
					"\x00" === $this->html[ $current_token->start ] &&
					strspn( $this->html, "\x00", $current_token->start, $current_token->length ) === $current_token->length
				) {
					// Parse error: ignore the token.
					return $this->step();
				}

				/*
				 * Whitespace-only text does not affect the frameset-ok flag.
				 * It is probably inter-element whitespace, but it may also
				 * contain character references which decode only to whitespace.
				 */
				$text = $this->get_modifiable_text();
				if ( strlen( $text ) !== strspn( $text, " \t\n\f\r" ) ) {
					$this->state->frameset_ok = false;
				}

				$this->insert_html_element( $this->state->current_token );
				return true;

			case 'html':
				/*
				 * > A DOCTYPE token
				 * > Parse error. Ignore the token.
				 */
				return $this->step();

			/*
			 * > A start tag whose tag name is "button"
			 */
			case '+BUTTON':
				if ( $this->state->stack_of_open_elements->has_element_in_scope( 'BUTTON' ) ) {
					// @todo Indicate a parse error once it's possible. This error does not impact the logic here.
					$this->generate_implied_end_tags();
					$this->state->stack_of_open_elements->pop_until( 'BUTTON' );
				}

				$this->reconstruct_active_formatting_elements();
				$this->insert_html_element( $this->state->current_token );
				$this->state->frameset_ok = false;

				return true;

			/*
			 * > A start tag whose tag name is one of: "address", "article", "aside",
			 * > "blockquote", "center", "details", "dialog", "dir", "div", "dl",
			 * > "fieldset", "figcaption", "figure", "footer", "header", "hgroup",
			 * > "main", "menu", "nav", "ol", "p", "search", "section", "summary", "ul"
			 */
			case '+ADDRESS':
			case '+ARTICLE':
			case '+ASIDE':
			case '+BLOCKQUOTE':
			case '+CENTER':
			case '+DETAILS':
			case '+DIALOG':
			case '+DIR':
			case '+DIV':
			case '+DL':
			case '+FIELDSET':
			case '+FIGCAPTION':
			case '+FIGURE':
			case '+FOOTER':
			case '+HEADER':
			case '+HGROUP':
			case '+MAIN':
			case '+MENU':
			case '+NAV':
			case '+OL':
			case '+P':
			case '+SEARCH':
			case '+SECTION':
			case '+SUMMARY':
			case '+UL':
				if ( $this->state->stack_of_open_elements->has_p_in_button_scope() ) {
					$this->close_a_p_element();
				}

				$this->insert_html_element( $this->state->current_token );
				return true;

			/*
			 * > An end tag whose tag name is one of: "address", "article", "aside", "blockquote",
			 * > "button", "center", "details", "dialog", "dir", "div", "dl", "fieldset",
			 * > "figcaption", "figure", "footer", "header", "hgroup", "listing", "main",
			 * > "menu", "nav", "ol", "pre", "search", "section", "summary", "ul"
			 */
			case '-ADDRESS':
			case '-ARTICLE':
			case '-ASIDE':
			case '-BLOCKQUOTE':
			case '-BUTTON':
			case '-CENTER':
			case '-DETAILS':
			case '-DIALOG':
			case '-DIR':
			case '-DIV':
			case '-DL':
			case '-FIELDSET':
			case '-FIGCAPTION':
			case '-FIGURE':
			case '-FOOTER':
			case '-HEADER':
			case '-HGROUP':
			case '-LISTING':
			case '-MAIN':
			case '-MENU':
			case '-NAV':
			case '-OL':
			case '-PRE':
			case '-SEARCH':
			case '-SECTION':
			case '-SUMMARY':
			case '-UL':
				if ( ! $this->state->stack_of_open_elements->has_element_in_scope( $token_name ) ) {
					// @todo Report parse error.
					// Ignore the token.
					return $this->step();
				}

				$this->generate_implied_end_tags();
				if ( $this->state->stack_of_open_elements->current_node()->node_name !== $token_name ) {
					// @todo Record parse error: this error doesn't impact parsing.
				}
				$this->state->stack_of_open_elements->pop_until( $token_name );
				return true;

			/*
			 * > A start tag whose tag name is one of: "h1", "h2", "h3", "h4", "h5", "h6"
			 */
			case '+H1':
			case '+H2':
			case '+H3':
			case '+H4':
			case '+H5':
			case '+H6':
				if ( $this->state->stack_of_open_elements->has_p_in_button_scope() ) {
					$this->close_a_p_element();
				}

				if (
					in_array(
						$this->state->stack_of_open_elements->current_node()->node_name,
						array( 'H1', 'H2', 'H3', 'H4', 'H5', 'H6' ),
						true
					)
				) {
					// @todo Indicate a parse error once it's possible.
					$this->state->stack_of_open_elements->pop();
				}

				$this->insert_html_element( $this->state->current_token );
				return true;

			/*
			 * > A start tag whose tag name is one of: "pre", "listing"
			 */
			case '+PRE':
			case '+LISTING':
				if ( $this->state->stack_of_open_elements->has_p_in_button_scope() ) {
					$this->close_a_p_element();
				}
				$this->insert_html_element( $this->state->current_token );
				$this->state->frameset_ok = false;
				return true;

			/*
			 * > An end tag whose tag name is one of: "h1", "h2", "h3", "h4", "h5", "h6"
			 */
			case '-H1':
			case '-H2':
			case '-H3':
			case '-H4':
			case '-H5':
			case '-H6':
				if ( ! $this->state->stack_of_open_elements->has_element_in_scope( '(internal: H1 through H6 - do not use)' ) ) {
					/*
					 * This is a parse error; ignore the token.
					 *
					 * @todo Indicate a parse error once it's possible.
					 */
					return $this->step();
				}

				$this->generate_implied_end_tags();

				if ( $this->state->stack_of_open_elements->current_node()->node_name !== $token_name ) {
					// @todo Record parse error: this error doesn't impact parsing.
				}

				$this->state->stack_of_open_elements->pop_until( '(internal: H1 through H6 - do not use)' );
				return true;

			/*
			 * > A start tag whose tag name is "li"
			 * > A start tag whose tag name is one of: "dd", "dt"
			 */
			case '+DD':
			case '+DT':
			case '+LI':
				$this->state->frameset_ok = false;
				$node                     = $this->state->stack_of_open_elements->current_node();
				$is_li                    = 'LI' === $token_name;

				in_body_list_loop:
				/*
				 * The logic for LI and DT/DD is the same except for one point: LI elements _only_
				 * close other LI elements, but a DT or DD element closes _any_ open DT or DD element.
				 */
				if ( $is_li ? 'LI' === $node->node_name : ( 'DD' === $node->node_name || 'DT' === $node->node_name ) ) {
					$node_name = $is_li ? 'LI' : $node->node_name;
					$this->generate_implied_end_tags( $node_name );
					if ( $node_name !== $this->state->stack_of_open_elements->current_node()->node_name ) {
						// @todo Indicate a parse error once it's possible. This error does not impact the logic here.
					}

					$this->state->stack_of_open_elements->pop_until( $node_name );
					goto in_body_list_done;
				}

				if (
					'ADDRESS' !== $node->node_name &&
					'DIV' !== $node->node_name &&
					'P' !== $node->node_name &&
					$this->is_special( $node->node_name )
				) {
					/*
					 * > If node is in the special category, but is not an address, div,
					 * > or p element, then jump to the step labeled done below.
					 */
					goto in_body_list_done;
				} else {
					/*
					 * > Otherwise, set node to the previous entry in the stack of open elements
					 * > and return to the step labeled loop.
					 */
					foreach ( $this->state->stack_of_open_elements->walk_up( $node ) as $item ) {
						$node = $item;
						break;
					}
					goto in_body_list_loop;
				}

				in_body_list_done:
				if ( $this->state->stack_of_open_elements->has_p_in_button_scope() ) {
					$this->close_a_p_element();
				}

				$this->insert_html_element( $this->state->current_token );
				return true;

			/*
			 * > An end tag whose tag name is "li"
			 * > An end tag whose tag name is one of: "dd", "dt"
			 */
			case '-DD':
			case '-DT':
			case '-LI':
				if (
					/*
					 * An end tag whose tag name is "li":
					 * If the stack of open elements does not have an li element in list item scope,
					 * then this is a parse error; ignore the token.
					 */
					(
						'LI' === $token_name &&
						! $this->state->stack_of_open_elements->has_element_in_list_item_scope( 'LI' )
					) ||
					/*
					 * An end tag whose tag name is one of: "dd", "dt":
					 * If the stack of open elements does not have an element in scope that is an
					 * HTML element with the same tag name as that of the token, then this is a
					 * parse error; ignore the token.
					 */
					(
						'LI' !== $token_name &&
						! $this->state->stack_of_open_elements->has_element_in_scope( $token_name )
					)
				) {
					/*
					 * This is a parse error, ignore the token.
					 *
					 * @todo Indicate a parse error once it's possible.
					 */
					return $this->step();
				}

				$this->generate_implied_end_tags( $token_name );

				if ( $token_name !== $this->state->stack_of_open_elements->current_node()->node_name ) {
					// @todo Indicate a parse error once it's possible. This error does not impact the logic here.
				}

				$this->state->stack_of_open_elements->pop_until( $token_name );
				return true;

			/*
			 * > An end tag whose tag name is "p"
			 */
			case '-P':
				if ( ! $this->state->stack_of_open_elements->has_p_in_button_scope() ) {
					$this->insert_html_element( $this->state->current_token );
				}

				$this->close_a_p_element();
				return true;

			// > A start tag whose tag name is "a"
			case '+A':
				foreach ( $this->state->active_formatting_elements->walk_up() as $item ) {
					switch ( $item->node_name ) {
						case 'marker':
							break;

						case 'A':
							$this->run_adoption_agency_algorithm();
							$this->state->active_formatting_elements->remove_node( $item );
							$this->state->stack_of_open_elements->remove_node( $item );
							break;
					}
				}

				$this->reconstruct_active_formatting_elements();
				$this->insert_html_element( $this->state->current_token );
				$this->state->active_formatting_elements->push( $this->state->current_token );
				return true;

			/*
			 * > A start tag whose tag name is one of: "b", "big", "code", "em", "font", "i",
			 * > "s", "small", "strike", "strong", "tt", "u"
			 */
			case '+B':
			case '+BIG':
			case '+CODE':
			case '+EM':
			case '+FONT':
			case '+I':
			case '+S':
			case '+SMALL':
			case '+STRIKE':
			case '+STRONG':
			case '+TT':
			case '+U':
				$this->reconstruct_active_formatting_elements();
				$this->insert_html_element( $this->state->current_token );
				$this->state->active_formatting_elements->push( $this->state->current_token );
				return true;

			/*
			 * > An end tag whose tag name is one of: "a", "b", "big", "code", "em", "font", "i",
			 * > "nobr", "s", "small", "strike", "strong", "tt", "u"
			 */
			case '-A':
			case '-B':
			case '-BIG':
			case '-CODE':
			case '-EM':
			case '-FONT':
			case '-I':
			case '-S':
			case '-SMALL':
			case '-STRIKE':
			case '-STRONG':
			case '-TT':
			case '-U':
				$this->run_adoption_agency_algorithm();
				return true;

			/*
			 * > An end tag whose tag name is "br"
			 * >   Parse error. Drop the attributes from the token, and act as described in the next
			 * >   entry; i.e. act as if this was a "br" start tag token with no attributes, rather
			 * >   than the end tag token that it actually is.
			 */
			case '-BR':
				$this->last_error = self::ERROR_UNSUPPORTED;
				throw new WP_HTML_Unsupported_Exception( 'Closing BR tags require unimplemented special handling.' );

			/*
			 * > A start tag whose tag name is one of: "area", "br", "embed", "img", "keygen", "wbr"
			 */
			case '+AREA':
			case '+BR':
			case '+EMBED':
			case '+IMG':
			case '+KEYGEN':
			case '+WBR':
				$this->reconstruct_active_formatting_elements();
				$this->insert_html_element( $this->state->current_token );
				$this->state->frameset_ok = false;
				return true;

			/*
			 * > A start tag whose tag name is "input"
			 */
			case '+INPUT':
				$this->reconstruct_active_formatting_elements();
				$this->insert_html_element( $this->state->current_token );
				$type_attribute = $this->get_attribute( 'type' );
				/*
				 * > If the token does not have an attribute with the name "type", or if it does,
				 * > but that attribute's value is not an ASCII case-insensitive match for the
				 * > string "hidden", then: set the frameset-ok flag to "not ok".
				 */
				if ( ! is_string( $type_attribute ) || 'hidden' !== strtolower( $type_attribute ) ) {
					$this->state->frameset_ok = false;
				}
				return true;

			/*
			 * > A start tag whose tag name is "hr"
			 */
			case '+HR':
				if ( $this->state->stack_of_open_elements->has_p_in_button_scope() ) {
					$this->close_a_p_element();
				}
				$this->insert_html_element( $this->state->current_token );
				$this->state->frameset_ok = false;
				return true;

			/*
			 * > A start tag whose tag name is one of: "param", "source", "track"
			 */
			case '+PARAM':
			case '+SOURCE':
			case '+TRACK':
				$this->insert_html_element( $this->state->current_token );
				return true;
		}

		/*
		 * These tags require special handling in the 'in body' insertion mode
		 * but that handling hasn't yet been implemented.
		 *
		 * As the rules for each tag are implemented, the corresponding tag
		 * name should be removed from this list. An accompanying test should
		 * help ensure this list is maintained.
		 *
		 * @see Tests_HtmlApi_WpHtmlProcessor::test_step_in_body_fails_on_unsupported_tags
		 *
		 * Since this switch structure throws a WP_HTML_Unsupported_Exception, it's
		 * possible to handle "any other start tag" and "any other end tag" below,
		 * as that guarantees execution doesn't proceed for the unimplemented tags.
		 *
		 * @see https://html.spec.whatwg.org/multipage/parsing.html#parsing-main-inbody
		 */
		switch ( $token_name ) {
			case 'APPLET':
			case 'BASE':
			case 'BASEFONT':
			case 'BGSOUND':
			case 'BODY':
			case 'CAPTION':
			case 'COL':
			case 'COLGROUP':
			case 'FORM':
			case 'FRAME':
			case 'FRAMESET':
			case 'HEAD':
			case 'HTML':
			case 'IFRAME':
			case 'LINK':
			case 'MARQUEE':
			case 'MATH':
			case 'META':
			case 'NOBR':
			case 'NOEMBED':
			case 'NOFRAMES':
			case 'NOSCRIPT':
			case 'OBJECT':
			case 'OPTGROUP':
			case 'OPTION':
			case 'PLAINTEXT':
			case 'RB':
			case 'RP':
			case 'RT':
			case 'RTC':
			case 'SARCASM':
			case 'SCRIPT':
			case 'SELECT':
			case 'STYLE':
			case 'SVG':
			case 'TABLE':
			case 'TBODY':
			case 'TD':
			case 'TEMPLATE':
			case 'TEXTAREA':
			case 'TFOOT':
			case 'TH':
			case 'THEAD':
			case 'TITLE':
			case 'TR':
			case 'XMP':
				$this->last_error = self::ERROR_UNSUPPORTED;
				throw new WP_HTML_Unsupported_Exception( "Cannot process {$token_name} element." );
		}

		if ( ! parent::is_tag_closer() ) {
			/*
			 * > Any other start tag
			 */
			$this->reconstruct_active_formatting_elements();
			$this->insert_html_element( $this->state->current_token );
			return true;
		} else {
			/*
			 * > Any other end tag
			 */

			/*
			 * Find the corresponding tag opener in the stack of open elements, if
			 * it exists before reaching a special element, which provides a kind
			 * of boundary in the stack. For example, a `</custom-tag>` should not
			 * close anything beyond its containing `P` or `DIV` element.
			 */
			foreach ( $this->state->stack_of_open_elements->walk_up() as $node ) {
				if ( $token_name === $node->node_name ) {
					break;
				}

				if ( self::is_special( $node->node_name ) ) {
					// This is a parse error, ignore the token.
					return $this->step();
				}
			}

			$this->generate_implied_end_tags( $token_name );
			if ( $node !== $this->state->stack_of_open_elements->current_node() ) {
				// @todo Record parse error: this error doesn't impact parsing.
			}

			foreach ( $this->state->stack_of_open_elements->walk_up() as $item ) {
				$this->state->stack_of_open_elements->pop();
				if ( $node === $item ) {
					return true;
				}
			}
		}
	}

	/*
	 * Internal helpers
	 */

	/**
	 * Creates a new bookmark for the currently-matched token and returns the generated name.
	 *
	 * @since 6.4.0
	 * @since 6.5.0 Renamed from bookmark_tag() to bookmark_token().
	 *
	 * @throws Exception When unable to allocate requested bookmark.
	 *
	 * @return string|false Name of created bookmark, or false if unable to create.
	 */
	private function bookmark_token() {
		if ( ! parent::set_bookmark( ++$this->bookmark_counter ) ) {
			$this->last_error = self::ERROR_EXCEEDED_MAX_BOOKMARKS;
			throw new Exception( 'could not allocate bookmark' );
		}

		return "{$this->bookmark_counter}";
	}

	/*
	 * HTML semantic overrides for Tag Processor
	 */

	/**
	 * Returns the uppercase name of the matched tag.
	 *
	 * The semantic rules for HTML specify that certain tags be reprocessed
	 * with a different tag name. Because of this, the tag name presented
	 * by the HTML Processor may differ from the one reported by the HTML
	 * Tag Processor, which doesn't apply these semantic rules.
	 *
	 * Example:
	 *
	 *     $processor = new WP_HTML_Tag_Processor( '<div class="test">Test</div>' );
	 *     $processor->next_tag() === true;
	 *     $processor->get_tag() === 'DIV';
	 *
	 *     $processor->next_tag() === false;
	 *     $processor->get_tag() === null;
	 *
	 * @since 6.4.0
	 *
	 * @return string|null Name of currently matched tag in input HTML, or `null` if none found.
	 */
	public function get_tag() {
		if ( null !== $this->last_error ) {
			return null;
		}

		if ( $this->is_virtual() ) {
			return $this->current_element->token->node_name;
		}

		$tag_name = parent::get_tag();

		switch ( $tag_name ) {
			case 'IMAGE':
				/*
				 * > A start tag whose tag name is "image"
				 * > Change the token's tag name to "img" and reprocess it. (Don't ask.)
				 */
				return 'IMG';

			default:
				return $tag_name;
		}
	}

	/**
	 * Indicates if the currently matched tag contains the self-closing flag.
	 *
	 * No HTML elements ought to have the self-closing flag and for those, the self-closing
	 * flag will be ignored. For void elements this is benign because they "self close"
	 * automatically. For non-void HTML elements though problems will appear if someone
	 * intends to use a self-closing element in place of that element with an empty body.
	 * For HTML foreign elements and custom elements the self-closing flag determines if
	 * they self-close or not.
	 *
	 * This function does not determine if a tag is self-closing,
	 * but only if the self-closing flag is present in the syntax.
	 *
	 * @since 6.6.0 Subclassed for the HTML Processor.
	 *
	 * @return bool Whether the currently matched tag contains the self-closing flag.
	 */
	public function has_self_closing_flag() {
		return $this->is_virtual() ? false : parent::has_self_closing_flag();
	}

	/**
	 * Returns the node name represented by the token.
	 *
	 * This matches the DOM API value `nodeName`. Some values
	 * are static, such as `#text` for a text node, while others
	 * are dynamically generated from the token itself.
	 *
	 * Dynamic names:
	 *  - Uppercase tag name for tag matches.
	 *  - `html` for DOCTYPE declarations.
	 *
	 * Note that if the Tag Processor is not matched on a token
	 * then this function will return `null`, either because it
	 * hasn't yet found a token or because it reached the end
	 * of the document without matching a token.
	 *
	 * @since 6.6.0 Subclassed for the HTML Processor.
	 *
	 * @return string|null Name of the matched token.
	 */
	public function get_token_name() {
		return $this->is_virtual()
			? $this->current_element->token->node_name
			: parent::get_token_name();
	}

	/**
	 * Indicates the kind of matched token, if any.
	 *
	 * This differs from `get_token_name()` in that it always
	 * returns a static string indicating the type, whereas
	 * `get_token_name()` may return values derived from the
	 * token itself, such as a tag name or processing
	 * instruction tag.
	 *
	 * Possible values:
	 *  - `#tag` when matched on a tag.
	 *  - `#text` when matched on a text node.
	 *  - `#cdata-section` when matched on a CDATA node.
	 *  - `#comment` when matched on a comment.
	 *  - `#doctype` when matched on a DOCTYPE declaration.
	 *  - `#presumptuous-tag` when matched on an empty tag closer.
	 *  - `#funky-comment` when matched on a funky comment.
	 *
	 * @since 6.6.0 Subclassed for the HTML Processor.
	 *
	 * @return string|null What kind of token is matched, or null.
	 */
	public function get_token_type() {
		if ( $this->is_virtual() ) {
			/*
			 * This logic comes from the Tag Processor.
			 *
			 * @todo It would be ideal not to repeat this here, but it's not clearly
			 *       better to allow passing a token name to `get_token_type()`.
			 */
			$node_name     = $this->current_element->token->node_name;
			$starting_char = $node_name[0];
			if ( 'A' <= $starting_char && 'Z' >= $starting_char ) {
				return '#tag';
			}

			if ( 'html' === $node_name ) {
				return '#doctype';
			}

			return $node_name;
		}

		return parent::get_token_type();
	}

	/**
	 * Returns the value of a requested attribute from a matched tag opener if that attribute exists.
	 *
	 * Example:
	 *
	 *     $p = WP_HTML_Processor::create_fragment( '<div enabled class="test" data-test-id="14">Test</div>' );
	 *     $p->next_token() === true;
	 *     $p->get_attribute( 'data-test-id' ) === '14';
	 *     $p->get_attribute( 'enabled' ) === true;
	 *     $p->get_attribute( 'aria-label' ) === null;
	 *
	 *     $p->next_tag() === false;
	 *     $p->get_attribute( 'class' ) === null;
	 *
	 * @since 6.6.0 Subclassed for HTML Processor.
	 *
	 * @param string $name Name of attribute whose value is requested.
	 * @return string|true|null Value of attribute or `null` if not available. Boolean attributes return `true`.
	 */
	public function get_attribute( $name ) {
		return $this->is_virtual() ? null : parent::get_attribute( $name );
	}

	/**
	 * Updates or creates a new attribute on the currently matched tag with the passed value.
	 *
	 * For boolean attributes special handling is provided:
	 *  - When `true` is passed as the value, then only the attribute name is added to the tag.
	 *  - When `false` is passed, the attribute gets removed if it existed before.
	 *
	 * For string attributes, the value is escaped using the `esc_attr` function.
	 *
	 * @since 6.6.0 Subclassed for the HTML Processor.
	 *
	 * @param string      $name  The attribute name to target.
	 * @param string|bool $value The new attribute value.
	 * @return bool Whether an attribute value was set.
	 */
	public function set_attribute( $name, $value ) {
		return $this->is_virtual() ? false : parent::set_attribute( $name, $value );
	}

	/**
	 * Remove an attribute from the currently-matched tag.
	 *
	 * @since 6.6.0 Subclassed for HTML Processor.
	 *
	 * @param string $name The attribute name to remove.
	 * @return bool Whether an attribute was removed.
	 */
	public function remove_attribute( $name ) {
		return $this->is_virtual() ? false : parent::remove_attribute( $name );
	}

	/**
	 * Gets lowercase names of all attributes matching a given prefix in the current tag.
	 *
	 * Note that matching is case-insensitive. This is in accordance with the spec:
	 *
	 * > There must never be two or more attributes on
	 * > the same start tag whose names are an ASCII
	 * > case-insensitive match for each other.
	 *     - HTML 5 spec
	 *
	 * Example:
	 *
	 *     $p = new WP_HTML_Tag_Processor( '<div data-ENABLED class="test" DATA-test-id="14">Test</div>' );
	 *     $p->next_tag( array( 'class_name' => 'test' ) ) === true;
	 *     $p->get_attribute_names_with_prefix( 'data-' ) === array( 'data-enabled', 'data-test-id' );
	 *
	 *     $p->next_tag() === false;
	 *     $p->get_attribute_names_with_prefix( 'data-' ) === null;
	 *
	 * @since 6.6.0 Subclassed for the HTML Processor.
	 *
	 * @see https://html.spec.whatwg.org/multipage/syntax.html#attributes-2:ascii-case-insensitive
	 *
	 * @param string $prefix Prefix of requested attribute names.
	 * @return array|null List of attribute names, or `null` when no tag opener is matched.
	 */
	public function get_attribute_names_with_prefix( $prefix ) {
		return $this->is_virtual() ? null : parent::get_attribute_names_with_prefix( $prefix );
	}

	/**
	 * Adds a new class name to the currently matched tag.
	 *
	 * @since 6.6.0 Subclassed for the HTML Processor.
	 *
	 * @param string $class_name The class name to add.
	 * @return bool Whether the class was set to be added.
	 */
	public function add_class( $class_name ) {
		return $this->is_virtual() ? false : parent::add_class( $class_name );
	}

	/**
	 * Removes a class name from the currently matched tag.
	 *
	 * @since 6.6.0 Subclassed for the HTML Processor.
	 *
	 * @param string $class_name The class name to remove.
	 * @return bool Whether the class was set to be removed.
	 */
	public function remove_class( $class_name ) {
		return $this->is_virtual() ? false : parent::remove_class( $class_name );
	}

	/**
	 * Returns if a matched tag contains the given ASCII case-insensitive class name.
	 *
	 * @since 6.6.0 Subclassed for the HTML Processor.
	 *
	 * @param string $wanted_class Look for this CSS class name, ASCII case-insensitive.
	 * @return bool|null Whether the matched tag contains the given class name, or null if not matched.
	 */
	public function has_class( $wanted_class ) {
		return $this->is_virtual() ? null : parent::has_class( $wanted_class );
	}

	/**
	 * Generator for a foreach loop to step through each class name for the matched tag.
	 *
	 * This generator function is designed to be used inside a "foreach" loop.
	 *
	 * Example:
	 *
	 *     $p = WP_HTML_Processor::create_fragment( "<div class='free &lt;egg&lt;\tlang-en'>" );
	 *     $p->next_tag();
	 *     foreach ( $p->class_list() as $class_name ) {
	 *         echo "{$class_name} ";
	 *     }
	 *     // Outputs: "free <egg> lang-en "
	 *
	 * @since 6.6.0 Subclassed for the HTML Processor.
	 */
	public function class_list() {
		return $this->is_virtual() ? null : parent::class_list();
	}

	/**
	 * Returns the modifiable text for a matched token, or an empty string.
	 *
	 * Modifiable text is text content that may be read and changed without
	 * changing the HTML structure of the document around it. This includes
	 * the contents of `#text` nodes in the HTML as well as the inner
	 * contents of HTML comments, Processing Instructions, and others, even
	 * though these nodes aren't part of a parsed DOM tree. They also contain
	 * the contents of SCRIPT and STYLE tags, of TEXTAREA tags, and of any
	 * other section in an HTML document which cannot contain HTML markup (DATA).
	 *
	 * If a token has no modifiable text then an empty string is returned to
	 * avoid needless crashing or type errors. An empty string does not mean
	 * that a token has modifiable text, and a token with modifiable text may
	 * have an empty string (e.g. a comment with no contents).
	 *
	 * @since 6.6.0 Subclassed for the HTML Processor.
	 *
	 * @return string
	 */
	public function get_modifiable_text() {
		return $this->is_virtual() ? '' : parent::get_modifiable_text();
	}

	/**
	 * Indicates what kind of comment produced the comment node.
	 *
	 * Because there are different kinds of HTML syntax which produce
	 * comments, the Tag Processor tracks and exposes this as a type
	 * for the comment. Nominally only regular HTML comments exist as
	 * they are commonly known, but a number of unrelated syntax errors
	 * also produce comments.
	 *
	 * @see self::COMMENT_AS_ABRUPTLY_CLOSED_COMMENT
	 * @see self::COMMENT_AS_CDATA_LOOKALIKE
	 * @see self::COMMENT_AS_INVALID_HTML
	 * @see self::COMMENT_AS_HTML_COMMENT
	 * @see self::COMMENT_AS_PI_NODE_LOOKALIKE
	 *
	 * @since 6.6.0 Subclassed for the HTML Processor.
	 *
	 * @return string|null
	 */
	public function get_comment_type() {
		return $this->is_virtual() ? null : parent::get_comment_type();
	}

	/**
	 * Removes a bookmark that is no longer needed.
	 *
	 * Releasing a bookmark frees up the small
	 * performance overhead it requires.
	 *
	 * @since 6.4.0
	 *
	 * @param string $bookmark_name Name of the bookmark to remove.
	 * @return bool Whether the bookmark already existed before removal.
	 */
	public function release_bookmark( $bookmark_name ) {
		return parent::release_bookmark( "_{$bookmark_name}" );
	}

	/**
	 * Moves the internal cursor in the HTML Processor to a given bookmark's location.
	 *
	 * Be careful! Seeking backwards to a previous location resets the parser to the
	 * start of the document and reparses the entire contents up until it finds the
	 * sought-after bookmarked location.
	 *
	 * In order to prevent accidental infinite loops, there's a
	 * maximum limit on the number of times seek() can be called.
	 *
	 * @throws Exception When unable to allocate a bookmark for the next token in the input HTML document.
	 *
	 * @since 6.4.0
	 *
	 * @param string $bookmark_name Jump to the place in the document identified by this bookmark name.
	 * @return bool Whether the internal cursor was successfully moved to the bookmark's location.
	 */
	public function seek( $bookmark_name ) {
		// Flush any pending updates to the document before beginning.
		$this->get_updated_html();

		$actual_bookmark_name = "_{$bookmark_name}";
		$processor_started_at = $this->state->current_token
			? $this->bookmarks[ $this->state->current_token->bookmark_name ]->start
			: 0;
		$bookmark_starts_at   = $this->bookmarks[ $actual_bookmark_name ]->start;
		$bookmark_length      = $this->bookmarks[ $actual_bookmark_name ]->length;
		$direction            = $bookmark_starts_at > $processor_started_at ? 'forward' : 'backward';

		/*
		 * If seeking backwards, it's possible that the sought-after bookmark exists within an element
		 * which has been closed before the current cursor; in other words, it has already been removed
		 * from the stack of open elements. This means that it's insufficient to simply pop off elements
		 * from the stack of open elements which appear after the bookmarked location and then jump to
		 * that location, as the elements which were open before won't be re-opened.
		 *
		 * In order to maintain consistency, the HTML Processor rewinds to the start of the document
		 * and reparses everything until it finds the sought-after bookmark.
		 *
		 * There are potentially better ways to do this: cache the parser state for each bookmark and
		 * restore it when seeking; store an immutable and idempotent register of where elements open
		 * and close.
		 *
		 * If caching the parser state it will be essential to properly maintain the cached stack of
		 * open elements and active formatting elements when modifying the document. This could be a
		 * tedious and time-consuming process as well, and so for now will not be performed.
		 *
		 * It may be possible to track bookmarks for where elements open and close, and in doing so
		 * be able to quickly recalculate breadcrumbs for any element in the document. It may even
		 * be possible to remove the stack of open elements and compute it on the fly this way.
		 * If doing this, the parser would need to track the opening and closing locations for all
		 * tokens in the breadcrumb path for any and all bookmarks. By utilizing bookmarks themselves
		 * this list could be automatically maintained while modifying the document. Finding the
		 * breadcrumbs would then amount to traversing that list from the start until the token
		 * being inspected. Once an element closes, if there are no bookmarks pointing to locations
		 * within that element, then all of these locations may be forgotten to save on memory use
		 * and computation time.
		 */
		if ( 'backward' === $direction ) {
			/*
			 * Instead of clearing the parser state and starting fresh, calling the stack methods
			 * maintains the proper flags in the parser.
			 */
			foreach ( $this->state->stack_of_open_elements->walk_up() as $item ) {
				if ( 'context-node' === $item->bookmark_name ) {
					break;
				}

				$this->state->stack_of_open_elements->remove_node( $item );
			}

			foreach ( $this->state->active_formatting_elements->walk_up() as $item ) {
				if ( 'context-node' === $item->bookmark_name ) {
					break;
				}

				$this->state->active_formatting_elements->remove_node( $item );
			}

			parent::seek( 'context-node' );
			$this->state->insertion_mode = WP_HTML_Processor_State::INSERTION_MODE_IN_BODY;
			$this->state->frameset_ok    = true;
			$this->element_queue         = array();
			$this->current_element       = null;
		}

		// When moving forwards, reparse the document until reaching the same location as the original bookmark.
		if ( $bookmark_starts_at === $this->bookmarks[ $this->state->current_token->bookmark_name ]->start ) {
			return true;
		}

		while ( $this->next_token() ) {
			if ( $bookmark_starts_at === $this->bookmarks[ $this->state->current_token->bookmark_name ]->start ) {
				while ( isset( $this->current_element ) && WP_HTML_Stack_Event::POP === $this->current_element->operation ) {
					$this->current_element = array_shift( $this->element_queue );
				}
				return true;
			}
		}

		return false;
	}

	/**
	 * Sets a bookmark in the HTML document.
	 *
	 * Bookmarks represent specific places or tokens in the HTML
	 * document, such as a tag opener or closer. When applying
	 * edits to a document, such as setting an attribute, the
	 * text offsets of that token may shift; the bookmark is
	 * kept updated with those shifts and remains stable unless
	 * the entire span of text in which the token sits is removed.
	 *
	 * Release bookmarks when they are no longer needed.
	 *
	 * Example:
	 *
	 *     <main><h2>Surprising fact you may not know!</h2></main>
	 *           ^  ^
	 *            \-|-- this `H2` opener bookmark tracks the token
	 *
	 *     <main class="clickbait"><h2>Surprising fact you may no…
	 *                             ^  ^
	 *                              \-|-- it shifts with edits
	 *
	 * Bookmarks provide the ability to seek to a previously-scanned
	 * place in the HTML document. This avoids the need to re-scan
	 * the entire document.
	 *
	 * Example:
	 *
	 *     <ul><li>One</li><li>Two</li><li>Three</li></ul>
	 *                                 ^^^^
	 *                                 want to note this last item
	 *
	 *     $p = new WP_HTML_Tag_Processor( $html );
	 *     $in_list = false;
	 *     while ( $p->next_tag( array( 'tag_closers' => $in_list ? 'visit' : 'skip' ) ) ) {
	 *         if ( 'UL' === $p->get_tag() ) {
	 *             if ( $p->is_tag_closer() ) {
	 *                 $in_list = false;
	 *                 $p->set_bookmark( 'resume' );
	 *                 if ( $p->seek( 'last-li' ) ) {
	 *                     $p->add_class( 'last-li' );
	 *                 }
	 *                 $p->seek( 'resume' );
	 *                 $p->release_bookmark( 'last-li' );
	 *                 $p->release_bookmark( 'resume' );
	 *             } else {
	 *                 $in_list = true;
	 *             }
	 *         }
	 *
	 *         if ( 'LI' === $p->get_tag() ) {
	 *             $p->set_bookmark( 'last-li' );
	 *         }
	 *     }
	 *
	 * Bookmarks intentionally hide the internal string offsets
	 * to which they refer. They are maintained internally as
	 * updates are applied to the HTML document and therefore
	 * retain their "position" - the location to which they
	 * originally pointed. The inability to use bookmarks with
	 * functions like `substr` is therefore intentional to guard
	 * against accidentally breaking the HTML.
	 *
	 * Because bookmarks allocate memory and require processing
	 * for every applied update, they are limited and require
	 * a name. They should not be created with programmatically-made
	 * names, such as "li_{$index}" with some loop. As a general
	 * rule they should only be created with string-literal names
	 * like "start-of-section" or "last-paragraph".
	 *
	 * Bookmarks are a powerful tool to enable complicated behavior.
	 * Consider double-checking that you need this tool if you are
	 * reaching for it, as inappropriate use could lead to broken
	 * HTML structure or unwanted processing overhead.
	 *
	 * @since 6.4.0
	 *
	 * @param string $bookmark_name Identifies this particular bookmark.
	 * @return bool Whether the bookmark was successfully created.
	 */
	public function set_bookmark( $bookmark_name ) {
		return parent::set_bookmark( "_{$bookmark_name}" );
	}

	/**
	 * Checks whether a bookmark with the given name exists.
	 *
	 * @since 6.5.0
	 *
	 * @param string $bookmark_name Name to identify a bookmark that potentially exists.
	 * @return bool Whether that bookmark exists.
	 */
	public function has_bookmark( $bookmark_name ) {
		return parent::has_bookmark( "_{$bookmark_name}" );
	}

	/*
	 * HTML Parsing Algorithms
	 */

	/**
	 * Closes a P element.
	 *
	 * @since 6.4.0
	 *
	 * @throws WP_HTML_Unsupported_Exception When encountering unsupported HTML input.
	 *
	 * @see https://html.spec.whatwg.org/#close-a-p-element
	 */
	private function close_a_p_element() {
		$this->generate_implied_end_tags( 'P' );
		$this->state->stack_of_open_elements->pop_until( 'P' );
	}

	/**
	 * Closes elements that have implied end tags.
	 *
	 * @since 6.4.0
	 *
	 * @see https://html.spec.whatwg.org/#generate-implied-end-tags
	 *
	 * @param string|null $except_for_this_element Perform as if this element doesn't exist in the stack of open elements.
	 */
	private function generate_implied_end_tags( $except_for_this_element = null ) {
		$elements_with_implied_end_tags = array(
			'DD',
			'DT',
			'LI',
			'P',
		);

		$current_node = $this->state->stack_of_open_elements->current_node();
		while (
			$current_node && $current_node->node_name !== $except_for_this_element &&
			in_array( $this->state->stack_of_open_elements->current_node(), $elements_with_implied_end_tags, true )
		) {
			$this->state->stack_of_open_elements->pop();
		}
	}

	/**
	 * Closes elements that have implied end tags, thoroughly.
	 *
	 * See the HTML specification for an explanation why this is
	 * different from generating end tags in the normal sense.
	 *
	 * @since 6.4.0
	 *
	 * @see WP_HTML_Processor::generate_implied_end_tags
	 * @see https://html.spec.whatwg.org/#generate-implied-end-tags
	 */
	private function generate_implied_end_tags_thoroughly() {
		$elements_with_implied_end_tags = array(
			'DD',
			'DT',
			'LI',
			'P',
		);

		while ( in_array( $this->state->stack_of_open_elements->current_node(), $elements_with_implied_end_tags, true ) ) {
			$this->state->stack_of_open_elements->pop();
		}
	}

	/**
	 * Reconstructs the active formatting elements.
	 *
	 * > This has the effect of reopening all the formatting elements that were opened
	 * > in the current body, cell, or caption (whichever is youngest) that haven't
	 * > been explicitly closed.
	 *
	 * @since 6.4.0
	 *
	 * @throws WP_HTML_Unsupported_Exception When encountering unsupported HTML input.
	 *
	 * @see https://html.spec.whatwg.org/#reconstruct-the-active-formatting-elements
	 *
	 * @return bool Whether any formatting elements needed to be reconstructed.
	 */
	private function reconstruct_active_formatting_elements() {
		/*
		 * > If there are no entries in the list of active formatting elements, then there is nothing
		 * > to reconstruct; stop this algorithm.
		 */
		if ( 0 === $this->state->active_formatting_elements->count() ) {
			return false;
		}

		$last_entry = $this->state->active_formatting_elements->current_node();
		if (

			/*
			 * > If the last (most recently added) entry in the list of active formatting elements is a marker;
			 * > stop this algorithm.
			 */
			'marker' === $last_entry->node_name ||

			/*
			 * > If the last (most recently added) entry in the list of active formatting elements is an
			 * > element that is in the stack of open elements, then there is nothing to reconstruct;
			 * > stop this algorithm.
			 */
			$this->state->stack_of_open_elements->contains_node( $last_entry )
		) {
			return false;
		}

		$this->last_error = self::ERROR_UNSUPPORTED;
		throw new WP_HTML_Unsupported_Exception( 'Cannot reconstruct active formatting elements when advancing and rewinding is required.' );
	}

	/**
	 * Runs the adoption agency algorithm.
	 *
	 * @since 6.4.0
	 *
	 * @throws WP_HTML_Unsupported_Exception When encountering unsupported HTML input.
	 *
	 * @see https://html.spec.whatwg.org/#adoption-agency-algorithm
	 */
	private function run_adoption_agency_algorithm() {
		$budget       = 1000;
		$subject      = $this->get_tag();
		$current_node = $this->state->stack_of_open_elements->current_node();

		if (
			// > If the current node is an HTML element whose tag name is subject
			$current_node && $subject === $current_node->node_name &&
			// > the current node is not in the list of active formatting elements
			! $this->state->active_formatting_elements->contains_node( $current_node )
		) {
			$this->state->stack_of_open_elements->pop();
			return;
		}

		$outer_loop_counter = 0;
		while ( $budget-- > 0 ) {
			if ( $outer_loop_counter++ >= 8 ) {
				return;
			}

			/*
			 * > Let formatting element be the last element in the list of active formatting elements that:
			 * >   - is between the end of the list and the last marker in the list,
			 * >     if any, or the start of the list otherwise,
			 * >   - and has the tag name subject.
			 */
			$formatting_element = null;
			foreach ( $this->state->active_formatting_elements->walk_up() as $item ) {
				if ( 'marker' === $item->node_name ) {
					break;
				}

				if ( $subject === $item->node_name ) {
					$formatting_element = $item;
					break;
				}
			}

			// > If there is no such element, then return and instead act as described in the "any other end tag" entry above.
			if ( null === $formatting_element ) {
				$this->last_error = self::ERROR_UNSUPPORTED;
				throw new WP_HTML_Unsupported_Exception( 'Cannot run adoption agency when "any other end tag" is required.' );
			}

			// > If formatting element is not in the stack of open elements, then this is a parse error; remove the element from the list, and return.
			if ( ! $this->state->stack_of_open_elements->contains_node( $formatting_element ) ) {
				$this->state->active_formatting_elements->remove_node( $formatting_element );
				return;
			}

			// > If formatting element is in the stack of open elements, but the element is not in scope, then this is a parse error; return.
			if ( ! $this->state->stack_of_open_elements->has_element_in_scope( $formatting_element->node_name ) ) {
				return;
			}

			/*
			 * > Let furthest block be the topmost node in the stack of open elements that is lower in the stack
			 * > than formatting element, and is an element in the special category. There might not be one.
			 */
			$is_above_formatting_element = true;
			$furthest_block              = null;
			foreach ( $this->state->stack_of_open_elements->walk_down() as $item ) {
				if ( $is_above_formatting_element && $formatting_element->bookmark_name !== $item->bookmark_name ) {
					continue;
				}

				if ( $is_above_formatting_element ) {
					$is_above_formatting_element = false;
					continue;
				}

				if ( self::is_special( $item->node_name ) ) {
					$furthest_block = $item;
					break;
				}
			}

			/*
			 * > If there is no furthest block, then the UA must first pop all the nodes from the bottom of the
			 * > stack of open elements, from the current node up to and including formatting element, then
			 * > remove formatting element from the list of active formatting elements, and finally return.
			 */
			if ( null === $furthest_block ) {
				foreach ( $this->state->stack_of_open_elements->walk_up() as $item ) {
					$this->state->stack_of_open_elements->pop();

					if ( $formatting_element->bookmark_name === $item->bookmark_name ) {
						$this->state->active_formatting_elements->remove_node( $formatting_element );
						return;
					}
				}
			}

			$this->last_error = self::ERROR_UNSUPPORTED;
			throw new WP_HTML_Unsupported_Exception( 'Cannot extract common ancestor in adoption agency algorithm.' );
		}

		$this->last_error = self::ERROR_UNSUPPORTED;
		throw new WP_HTML_Unsupported_Exception( 'Cannot run adoption agency when looping required.' );
	}

	/**
	 * Inserts an HTML element on the stack of open elements.
	 *
	 * @since 6.4.0
	 *
	 * @see https://html.spec.whatwg.org/#insert-a-foreign-element
	 *
	 * @param WP_HTML_Token $token Name of bookmark pointing to element in original input HTML.
	 */
	private function insert_html_element( $token ) {
		$this->state->stack_of_open_elements->push( $token );
	}

	/*
	 * HTML Specification Helpers
	 */

	/**
	 * Returns whether an element of a given name is in the HTML special category.
	 *
	 * @since 6.4.0
	 *
	 * @see https://html.spec.whatwg.org/#special
	 *
	 * @param string $tag_name Name of element to check.
	 * @return bool Whether the element of the given name is in the special category.
	 */
	public static function is_special( $tag_name ) {
		$tag_name = strtoupper( $tag_name );

		return (
			'ADDRESS' === $tag_name ||
			'APPLET' === $tag_name ||
			'AREA' === $tag_name ||
			'ARTICLE' === $tag_name ||
			'ASIDE' === $tag_name ||
			'BASE' === $tag_name ||
			'BASEFONT' === $tag_name ||
			'BGSOUND' === $tag_name ||
			'BLOCKQUOTE' === $tag_name ||
			'BODY' === $tag_name ||
			'BR' === $tag_name ||
			'BUTTON' === $tag_name ||
			'CAPTION' === $tag_name ||
			'CENTER' === $tag_name ||
			'COL' === $tag_name ||
			'COLGROUP' === $tag_name ||
			'DD' === $tag_name ||
			'DETAILS' === $tag_name ||
			'DIR' === $tag_name ||
			'DIV' === $tag_name ||
			'DL' === $tag_name ||
			'DT' === $tag_name ||
			'EMBED' === $tag_name ||
			'FIELDSET' === $tag_name ||
			'FIGCAPTION' === $tag_name ||
			'FIGURE' === $tag_name ||
			'FOOTER' === $tag_name ||
			'FORM' === $tag_name ||
			'FRAME' === $tag_name ||
			'FRAMESET' === $tag_name ||
			'H1' === $tag_name ||
			'H2' === $tag_name ||
			'H3' === $tag_name ||
			'H4' === $tag_name ||
			'H5' === $tag_name ||
			'H6' === $tag_name ||
			'HEAD' === $tag_name ||
			'HEADER' === $tag_name ||
			'HGROUP' === $tag_name ||
			'HR' === $tag_name ||
			'HTML' === $tag_name ||
			'IFRAME' === $tag_name ||
			'IMG' === $tag_name ||
			'INPUT' === $tag_name ||
			'KEYGEN' === $tag_name ||
			'LI' === $tag_name ||
			'LINK' === $tag_name ||
			'LISTING' === $tag_name ||
			'MAIN' === $tag_name ||
			'MARQUEE' === $tag_name ||
			'MENU' === $tag_name ||
			'META' === $tag_name ||
			'NAV' === $tag_name ||
			'NOEMBED' === $tag_name ||
			'NOFRAMES' === $tag_name ||
			'NOSCRIPT' === $tag_name ||
			'OBJECT' === $tag_name ||
			'OL' === $tag_name ||
			'P' === $tag_name ||
			'PARAM' === $tag_name ||
			'PLAINTEXT' === $tag_name ||
			'PRE' === $tag_name ||
			'SCRIPT' === $tag_name ||
			'SEARCH' === $tag_name ||
			'SECTION' === $tag_name ||
			'SELECT' === $tag_name ||
			'SOURCE' === $tag_name ||
			'STYLE' === $tag_name ||
			'SUMMARY' === $tag_name ||
			'TABLE' === $tag_name ||
			'TBODY' === $tag_name ||
			'TD' === $tag_name ||
			'TEMPLATE' === $tag_name ||
			'TEXTAREA' === $tag_name ||
			'TFOOT' === $tag_name ||
			'TH' === $tag_name ||
			'THEAD' === $tag_name ||
			'TITLE' === $tag_name ||
			'TR' === $tag_name ||
			'TRACK' === $tag_name ||
			'UL' === $tag_name ||
			'WBR' === $tag_name ||
			'XMP' === $tag_name ||

			// MathML.
			'MI' === $tag_name ||
			'MO' === $tag_name ||
			'MN' === $tag_name ||
			'MS' === $tag_name ||
			'MTEXT' === $tag_name ||
			'ANNOTATION-XML' === $tag_name ||

			// SVG.
			'FOREIGNOBJECT' === $tag_name ||
			'DESC' === $tag_name ||
			'TITLE' === $tag_name
		);
	}

	/**
	 * Returns whether a given element is an HTML Void Element
	 *
	 * > area, base, br, col, embed, hr, img, input, link, meta, source, track, wbr
	 *
	 * @since 6.4.0
	 *
	 * @see https://html.spec.whatwg.org/#void-elements
	 *
	 * @param string $tag_name Name of HTML tag to check.
	 * @return bool Whether the given tag is an HTML Void Element.
	 */
	public static function is_void( $tag_name ) {
		$tag_name = strtoupper( $tag_name );

		return (
			'AREA' === $tag_name ||
			'BASE' === $tag_name ||
			'BASEFONT' === $tag_name || // Obsolete but still treated as void.
			'BGSOUND' === $tag_name || // Obsolete but still treated as void.
			'BR' === $tag_name ||
			'COL' === $tag_name ||
			'EMBED' === $tag_name ||
			'FRAME' === $tag_name ||
			'HR' === $tag_name ||
			'IMG' === $tag_name ||
			'INPUT' === $tag_name ||
			'KEYGEN' === $tag_name || // Obsolete but still treated as void.
			'LINK' === $tag_name ||
			'META' === $tag_name ||
			'PARAM' === $tag_name || // Obsolete but still treated as void.
			'SOURCE' === $tag_name ||
			'TRACK' === $tag_name ||
			'WBR' === $tag_name
		);
	}

	/*
	 * Constants that would pollute the top of the class if they were found there.
	 */

	/**
	 * Indicates that the next HTML token should be parsed and processed.
	 *
	 * @since 6.4.0
	 *
	 * @var string
	 */
	const PROCESS_NEXT_NODE = 'process-next-node';

	/**
	 * Indicates that the current HTML token should be reprocessed in the newly-selected insertion mode.
	 *
	 * @since 6.4.0
	 *
	 * @var string
	 */
	const REPROCESS_CURRENT_NODE = 'reprocess-current-node';

	/**
	 * Indicates that the current HTML token should be processed without advancing the parser.
	 *
	 * @since 6.5.0
	 *
	 * @var string
	 */
	const PROCESS_CURRENT_NODE = 'process-current-node';

	/**
	 * Indicates that the parser encountered unsupported markup and has bailed.
	 *
	 * @since 6.4.0
	 *
	 * @var string
	 */
	const ERROR_UNSUPPORTED = 'unsupported';

	/**
	 * Indicates that the parser encountered more HTML tokens than it
	 * was able to process and has bailed.
	 *
	 * @since 6.4.0
	 *
	 * @var string
	 */
	const ERROR_EXCEEDED_MAX_BOOKMARKS = 'exceeded-max-bookmarks';

	/**
	 * Unlock code that must be passed into the constructor to create this class.
	 *
	 * This class extends the WP_HTML_Tag_Processor, which has a public class
	 * constructor. Therefore, it's not possible to have a private constructor here.
	 *
	 * This unlock code is used to ensure that anyone calling the constructor is
	 * doing so with a full understanding that it's intended to be a private API.
	 *
	 * @access private
	 */
	const CONSTRUCTOR_UNLOCK_CODE = 'Use WP_HTML_Processor::create_fragment() instead of calling the class constructor directly.';
}

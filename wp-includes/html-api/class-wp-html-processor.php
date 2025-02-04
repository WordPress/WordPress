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
 * The HTML Processor supports all elements other than a specific set:
 *
 *  - Any element inside a TABLE.
 *  - Any element inside foreign content, including SVG and MATH.
 *  - Any element outside the IN BODY insertion mode, e.g. doctype declarations, meta, links.
 *
 * ### Supported markup
 *
 * Some kinds of non-normative HTML involve reconstruction of formatting elements and
 * re-parenting of mis-nested elements. For example, a DIV tag found inside a TABLE
 * may in fact belong _before_ the table in the DOM. If the HTML Processor encounters
 * such a case it will stop processing.
 *
 * The following list illustrates some common examples of unexpected HTML inputs that
 * the HTML Processor properly parses and represents:
 *
 *  - HTML with optional tags omitted, e.g. `<p>one<p>two`.
 *  - HTML with unexpected tag closers, e.g. `<p>one </span> more</p>`.
 *  - Non-void tags with self-closing flag, e.g. `<div/>the DIV is still open.</div>`.
 *  - Heading elements which close open heading elements of another level, e.g. `<h1>Closed by </h2>`.
 *  - Elements containing text that looks like other tags but isn't, e.g. `<title>The <img> is plaintext</title>`.
 *  - SCRIPT and STYLE tags containing text that looks like HTML but isn't, e.g. `<script>document.write('<p>Hi</p>');</script>`.
 *  - SCRIPT content which has been escaped, e.g. `<script><!-- document.write('<script>console.log("hi")</script>') --></script>`.
 *
 * ### Unsupported Features
 *
 * This parser does not report parse errors.
 *
 * Normally, when additional HTML or BODY tags are encountered in a document, if there
 * are any additional attributes on them that aren't found on the previous elements,
 * the existing HTML and BODY elements adopt those missing attribute values. This
 * parser does not add those additional attributes.
 *
 * In certain situations, elements are moved to a different part of the document in
 * a process called "adoption" and "fostering." Because the nodes move to a location
 * in the document that the parser had already processed, this parser does not support
 * these situations and will bail.
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
	private $state;

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
	 * Stores context for why the parser bailed on unsupported HTML, if it did.
	 *
	 * @see self::get_unsupported_exception
	 *
	 * @since 6.7.0
	 *
	 * @var WP_HTML_Unsupported_Exception|null
	 */
	private $unsupported_exception = null;

	/**
	 * Releases a bookmark when PHP garbage-collects its wrapping WP_HTML_Token instance.
	 *
	 * This function is created inside the class constructor so that it can be passed to
	 * the stack of open elements and the stack of active formatting elements without
	 * exposing it as a public method on the class.
	 *
	 * @since 6.4.0
	 *
	 * @var Closure|null
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
	 * Stores the current breadcrumbs.
	 *
	 * @since 6.7.0
	 *
	 * @var string[]
	 */
	private $breadcrumbs = array();

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
	 * @var WP_HTML_Stack_Event|null
	 */
	private $current_element = null;

	/**
	 * Context node if created as a fragment parser.
	 *
	 * @var WP_HTML_Token|null
	 */
	private $context_node = null;

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

		$processor                             = new static( $html, self::CONSTRUCTOR_UNLOCK_CODE );
		$processor->state->context_node        = array( 'BODY', array() );
		$processor->state->insertion_mode      = WP_HTML_Processor_State::INSERTION_MODE_IN_BODY;
		$processor->state->encoding            = $encoding;
		$processor->state->encoding_confidence = 'certain';

		// @todo Create "fake" bookmarks for non-existent but implied nodes.
		$processor->bookmarks['root-node']    = new WP_HTML_Span( 0, 0 );
		$processor->bookmarks['context-node'] = new WP_HTML_Span( 0, 0 );

		$root_node = new WP_HTML_Token(
			'root-node',
			'HTML',
			false
		);

		$processor->state->stack_of_open_elements->push( $root_node );

		$context_node = new WP_HTML_Token(
			'context-node',
			$processor->state->context_node[0],
			false
		);

		$processor->context_node = $context_node;
		$processor->breadcrumbs  = array( 'HTML', $context_node->node_name );

		return $processor;
	}

	/**
	 * Creates an HTML processor in the full parsing mode.
	 *
	 * It's likely that a fragment parser is more appropriate, unless sending an
	 * entire HTML document from start to finish. Consider a fragment parser with
	 * a context node of `<body>`.
	 *
	 * Since UTF-8 is the only currently-accepted charset, if working with a
	 * document that isn't UTF-8, it's important to convert the document before
	 * creating the processor: pass in the converted HTML.
	 *
	 * @param string      $html                    Input HTML document to process.
	 * @param string|null $known_definite_encoding Optional. If provided, specifies the charset used
	 *                                             in the input byte stream. Currently must be UTF-8.
	 * @return static|null The created processor if successful, otherwise null.
	 */
	public static function create_full_parser( $html, $known_definite_encoding = 'UTF-8' ) {
		if ( 'UTF-8' !== $known_definite_encoding ) {
			return null;
		}

		$processor                             = new static( $html, self::CONSTRUCTOR_UNLOCK_CODE );
		$processor->state->encoding            = $known_definite_encoding;
		$processor->state->encoding_confidence = 'certain';

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
			function ( WP_HTML_Token $token ): void {
				$is_virtual            = ! isset( $this->state->current_token ) || $this->is_tag_closer();
				$same_node             = isset( $this->state->current_token ) && $token->node_name === $this->state->current_token->node_name;
				$provenance            = ( ! $same_node || $is_virtual ) ? 'virtual' : 'real';
				$this->element_queue[] = new WP_HTML_Stack_Event( $token, WP_HTML_Stack_Event::PUSH, $provenance );

				$this->change_parsing_namespace( $token->integration_node_type ? 'html' : $token->namespace );
			}
		);

		$this->state->stack_of_open_elements->set_pop_handler(
			function ( WP_HTML_Token $token ): void {
				$is_virtual            = ! isset( $this->state->current_token ) || ! $this->is_tag_closer();
				$same_node             = isset( $this->state->current_token ) && $token->node_name === $this->state->current_token->node_name;
				$provenance            = ( ! $same_node || $is_virtual ) ? 'virtual' : 'real';
				$this->element_queue[] = new WP_HTML_Stack_Event( $token, WP_HTML_Stack_Event::POP, $provenance );

				$adjusted_current_node = $this->get_adjusted_current_node();

				if ( $adjusted_current_node ) {
					$this->change_parsing_namespace( $adjusted_current_node->integration_node_type ? 'html' : $adjusted_current_node->namespace );
				} else {
					$this->change_parsing_namespace( 'html' );
				}
			}
		);

		/*
		 * Create this wrapper so that it's possible to pass
		 * a private method into WP_HTML_Token classes without
		 * exposing it to any public API.
		 */
		$this->release_internal_bookmark_on_destruct = function ( string $name ): void {
			parent::release_bookmark( $name );
		};
	}

	/**
	 * Stops the parser and terminates its execution when encountering unsupported markup.
	 *
	 * @throws WP_HTML_Unsupported_Exception Halts execution of the parser.
	 *
	 * @since 6.7.0
	 *
	 * @param string $message Explains support is missing in order to parse the current node.
	 */
	private function bail( string $message ) {
		$here  = $this->bookmarks[ $this->state->current_token->bookmark_name ];
		$token = substr( $this->html, $here->start, $here->length );

		$open_elements = array();
		foreach ( $this->state->stack_of_open_elements->stack as $item ) {
			$open_elements[] = $item->node_name;
		}

		$active_formats = array();
		foreach ( $this->state->active_formatting_elements->walk_down() as $item ) {
			$active_formats[] = $item->node_name;
		}

		$this->last_error = self::ERROR_UNSUPPORTED;

		$this->unsupported_exception = new WP_HTML_Unsupported_Exception(
			$message,
			$this->state->current_token->node_name,
			$here->start,
			$token,
			$open_elements,
			$active_formats
		);

		throw $this->unsupported_exception;
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
	public function get_last_error(): ?string {
		return $this->last_error;
	}

	/**
	 * Returns context for why the parser aborted due to unsupported HTML, if it did.
	 *
	 * This is meant for debugging purposes, not for production use.
	 *
	 * @since 6.7.0
	 *
	 * @see self::$unsupported_exception
	 *
	 * @return WP_HTML_Unsupported_Exception|null
	 */
	public function get_unsupported_exception() {
		return $this->unsupported_exception;
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
	public function next_tag( $query = null ): bool {
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

		if ( isset( $query['tag_name'] ) ) {
			$query['tag_name'] = strtoupper( $query['tag_name'] );
		}

		$needs_class = ( isset( $query['class_name'] ) && is_string( $query['class_name'] ) )
			? $query['class_name']
			: null;

		if ( ! ( array_key_exists( 'breadcrumbs', $query ) && is_array( $query['breadcrumbs'] ) ) ) {
			while ( $this->next_token() ) {
				if ( '#tag' !== $this->get_token_type() ) {
					continue;
				}

				if ( isset( $query['tag_name'] ) && $query['tag_name'] !== $this->get_token_name() ) {
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
	 * Finds the next token in the HTML document.
	 *
	 * This doesn't currently have a way to represent non-tags and doesn't process
	 * semantic rules for text nodes. For access to the raw tokens consider using
	 * WP_HTML_Tag_Processor instead.
	 *
	 * @since 6.5.0 Added for internal support; do not use.
	 * @since 6.7.2 Refactored so subclasses may extend.
	 *
	 * @return bool Whether a token was parsed.
	 */
	public function next_token(): bool {
		return $this->next_visitable_token();
	}

	/**
	 * Ensures internal accounting is maintained for HTML semantic rules while
	 * the underlying Tag Processor class is seeking to a bookmark.
	 *
	 * This doesn't currently have a way to represent non-tags and doesn't process
	 * semantic rules for text nodes. For access to the raw tokens consider using
	 * WP_HTML_Tag_Processor instead.
	 *
	 * Note that this method may call itself recursively. This is why it is not
	 * implemented as {@see WP_HTML_Processor::next_token()}, which instead calls
	 * this method similarly to how {@see WP_HTML_Tag_Processor::next_token()}
	 * calls the {@see WP_HTML_Tag_Processor::base_class_next_token()} method.
	 *
	 * @since 6.7.2 Added for internal support.
	 *
	 * @access private
	 *
	 * @return bool
	 */
	private function next_visitable_token(): bool {
		$this->current_element = null;

		if ( isset( $this->last_error ) ) {
			return false;
		}

		/*
		 * Prime the events if there are none.
		 *
		 * @todo In some cases, probably related to the adoption agency
		 *       algorithm, this call to step() doesn't create any new
		 *       events. Calling it again creates them. Figure out why
		 *       this is and if it's inherent or if it's a bug. Looping
		 *       until there are events or until there are no more
		 *       tokens works in the meantime and isn't obviously wrong.
		 */
		if ( empty( $this->element_queue ) && $this->step() ) {
			return $this->next_visitable_token();
		}

		// Process the next event on the queue.
		$this->current_element = array_shift( $this->element_queue );
		if ( ! isset( $this->current_element ) ) {
			// There are no tokens left, so close all remaining open elements.
			while ( $this->state->stack_of_open_elements->pop() ) {
				continue;
			}

			return empty( $this->element_queue ) ? false : $this->next_visitable_token();
		}

		$is_pop = WP_HTML_Stack_Event::POP === $this->current_element->operation;

		/*
		 * The root node only exists in the fragment parser, and closing it
		 * indicates that the parse is complete. Stop before popping it from
		 * the breadcrumbs.
		 */
		if ( 'root-node' === $this->current_element->token->bookmark_name ) {
			return $this->next_visitable_token();
		}

		// Adjust the breadcrumbs for this event.
		if ( $is_pop ) {
			array_pop( $this->breadcrumbs );
		} else {
			$this->breadcrumbs[] = $this->current_element->token->node_name;
		}

		// Avoid sending close events for elements which don't expect a closing.
		if ( $is_pop && ! $this->expects_closer( $this->current_element->token ) ) {
			return $this->next_visitable_token();
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
	public function is_tag_closer(): bool {
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
	private function is_virtual(): bool {
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
	public function matches_breadcrumbs( $breadcrumbs ): bool {
		// Everything matches when there are zero constraints.
		if ( 0 === count( $breadcrumbs ) ) {
			return true;
		}

		// Start at the last crumb.
		$crumb = end( $breadcrumbs );

		if ( '*' !== $crumb && $this->get_tag() !== strtoupper( $crumb ) ) {
			return false;
		}

		for ( $i = count( $this->breadcrumbs ) - 1; $i >= 0; $i-- ) {
			$node  = $this->breadcrumbs[ $i ];
			$crumb = strtoupper( current( $breadcrumbs ) );

			if ( '*' !== $crumb && $node !== $crumb ) {
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
	 * @param WP_HTML_Token|null $node Optional. Node to examine, if provided.
	 *                                 Default is to examine current node.
	 * @return bool|null Whether to expect a closer for the currently-matched node,
	 *                   or `null` if not matched on any token.
	 */
	public function expects_closer( ?WP_HTML_Token $node = null ): ?bool {
		$token_name = $node->node_name ?? $this->get_token_name();

		if ( ! isset( $token_name ) ) {
			return null;
		}

		$token_namespace        = $node->namespace ?? $this->get_namespace();
		$token_has_self_closing = $node->has_self_closing_flag ?? $this->has_self_closing_flag();

		return ! (
			// Comments, text nodes, and other atomic tokens.
			'#' === $token_name[0] ||
			// Doctype declarations.
			'html' === $token_name ||
			// Void elements.
			( 'html' === $token_namespace && self::is_void( $token_name ) ) ||
			// Special atomic elements.
			( 'html' === $token_namespace && in_array( $token_name, array( 'IFRAME', 'NOEMBED', 'NOFRAMES', 'SCRIPT', 'STYLE', 'TEXTAREA', 'TITLE', 'XMP' ), true ) ) ||
			// Self-closing elements in foreign content.
			( 'html' !== $token_namespace && $token_has_self_closing )
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
	public function step( $node_to_process = self::PROCESS_NEXT_NODE ): bool {
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
			 */
			$top_node = $this->state->stack_of_open_elements->current_node();
			if ( isset( $top_node ) && ! $this->expects_closer( $top_node ) ) {
				$this->state->stack_of_open_elements->pop();
			}
		}

		if ( self::PROCESS_NEXT_NODE === $node_to_process ) {
			parent::next_token();
			if ( WP_HTML_Tag_Processor::STATE_TEXT_NODE === $this->parser_state ) {
				parent::subdivide_text_appropriately();
			}
		}

		// Finish stepping when there are no more tokens in the document.
		if (
			WP_HTML_Tag_Processor::STATE_INCOMPLETE_INPUT === $this->parser_state ||
			WP_HTML_Tag_Processor::STATE_COMPLETE === $this->parser_state
		) {
			return false;
		}

		$adjusted_current_node = $this->get_adjusted_current_node();
		$is_closer             = $this->is_tag_closer();
		$is_start_tag          = WP_HTML_Tag_Processor::STATE_MATCHED_TAG === $this->parser_state && ! $is_closer;
		$token_name            = $this->get_token_name();

		if ( self::REPROCESS_CURRENT_NODE !== $node_to_process ) {
			$this->state->current_token = new WP_HTML_Token(
				$this->bookmark_token(),
				$token_name,
				$this->has_self_closing_flag(),
				$this->release_internal_bookmark_on_destruct
			);
		}

		$parse_in_current_insertion_mode = (
			0 === $this->state->stack_of_open_elements->count() ||
			'html' === $adjusted_current_node->namespace ||
			(
				'math' === $adjusted_current_node->integration_node_type &&
				(
					( $is_start_tag && ! in_array( $token_name, array( 'MGLYPH', 'MALIGNMARK' ), true ) ) ||
					'#text' === $token_name
				)
			) ||
			(
				'math' === $adjusted_current_node->namespace &&
				'ANNOTATION-XML' === $adjusted_current_node->node_name &&
				$is_start_tag && 'SVG' === $token_name
			) ||
			(
				'html' === $adjusted_current_node->integration_node_type &&
				( $is_start_tag || '#text' === $token_name )
			)
		);

		try {
			if ( ! $parse_in_current_insertion_mode ) {
				return $this->step_in_foreign_content();
			}

			switch ( $this->state->insertion_mode ) {
				case WP_HTML_Processor_State::INSERTION_MODE_INITIAL:
					return $this->step_initial();

				case WP_HTML_Processor_State::INSERTION_MODE_BEFORE_HTML:
					return $this->step_before_html();

				case WP_HTML_Processor_State::INSERTION_MODE_BEFORE_HEAD:
					return $this->step_before_head();

				case WP_HTML_Processor_State::INSERTION_MODE_IN_HEAD:
					return $this->step_in_head();

				case WP_HTML_Processor_State::INSERTION_MODE_IN_HEAD_NOSCRIPT:
					return $this->step_in_head_noscript();

				case WP_HTML_Processor_State::INSERTION_MODE_AFTER_HEAD:
					return $this->step_after_head();

				case WP_HTML_Processor_State::INSERTION_MODE_IN_BODY:
					return $this->step_in_body();

				case WP_HTML_Processor_State::INSERTION_MODE_IN_TABLE:
					return $this->step_in_table();

				case WP_HTML_Processor_State::INSERTION_MODE_IN_TABLE_TEXT:
					return $this->step_in_table_text();

				case WP_HTML_Processor_State::INSERTION_MODE_IN_CAPTION:
					return $this->step_in_caption();

				case WP_HTML_Processor_State::INSERTION_MODE_IN_COLUMN_GROUP:
					return $this->step_in_column_group();

				case WP_HTML_Processor_State::INSERTION_MODE_IN_TABLE_BODY:
					return $this->step_in_table_body();

				case WP_HTML_Processor_State::INSERTION_MODE_IN_ROW:
					return $this->step_in_row();

				case WP_HTML_Processor_State::INSERTION_MODE_IN_CELL:
					return $this->step_in_cell();

				case WP_HTML_Processor_State::INSERTION_MODE_IN_SELECT:
					return $this->step_in_select();

				case WP_HTML_Processor_State::INSERTION_MODE_IN_SELECT_IN_TABLE:
					return $this->step_in_select_in_table();

				case WP_HTML_Processor_State::INSERTION_MODE_IN_TEMPLATE:
					return $this->step_in_template();

				case WP_HTML_Processor_State::INSERTION_MODE_AFTER_BODY:
					return $this->step_after_body();

				case WP_HTML_Processor_State::INSERTION_MODE_IN_FRAMESET:
					return $this->step_in_frameset();

				case WP_HTML_Processor_State::INSERTION_MODE_AFTER_FRAMESET:
					return $this->step_after_frameset();

				case WP_HTML_Processor_State::INSERTION_MODE_AFTER_AFTER_BODY:
					return $this->step_after_after_body();

				case WP_HTML_Processor_State::INSERTION_MODE_AFTER_AFTER_FRAMESET:
					return $this->step_after_after_frameset();

				// This should be unreachable but PHP doesn't have total type checking on switch.
				default:
					$this->bail( "Unaware of the requested parsing mode: '{$this->state->insertion_mode}'." );
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
	public function get_breadcrumbs(): ?array {
		return $this->breadcrumbs;
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
	public function get_current_depth(): int {
		return count( $this->breadcrumbs );
	}

	/**
	 * Normalizes an HTML fragment by serializing it.
	 *
	 * This method assumes that the given HTML snippet is found in BODY context.
	 * For normalizing full documents or fragments found in other contexts, create
	 * a new processor using {@see WP_HTML_Processor::create_fragment} or
	 * {@see WP_HTML_Processor::create_full_parser} and call {@see WP_HTML_Processor::serialize}
	 * on the created instances.
	 *
	 * Many aspects of an input HTML fragment may be changed during normalization.
	 *
	 *  - Attribute values will be double-quoted.
	 *  - Duplicate attributes will be removed.
	 *  - Omitted tags will be added.
	 *  - Tag and attribute name casing will be lower-cased,
	 *    except for specific SVG and MathML tags or attributes.
	 *  - Text will be re-encoded, null bytes handled,
	 *    and invalid UTF-8 replaced with U+FFFD.
	 *  - Any incomplete syntax trailing at the end will be omitted,
	 *    for example, an unclosed comment opener will be removed.
	 *
	 * Example:
	 *
	 *     echo WP_HTML_Processor::normalize( '<a href=#anchor v=5 href="/" enabled>One</a another v=5><!--' );
	 *     // <a href="#anchor" v="5" enabled>One</a>
	 *
	 *     echo WP_HTML_Processor::normalize( '<div></p>fun<table><td>cell</div>' );
	 *     // <div><p></p>fun<table><tbody><tr><td>cell</td></tr></tbody></table></div>
	 *
	 *     echo WP_HTML_Processor::normalize( '<![CDATA[invalid comment]]> syntax < <> "oddities"' );
	 *     // <!--[CDATA[invalid comment]]--> syntax &lt; &lt;&gt; &quot;oddities&quot;
	 *
	 * @since 6.7.0
	 *
	 * @param string $html Input HTML to normalize.
	 *
	 * @return string|null Normalized output, or `null` if unable to normalize.
	 */
	public static function normalize( string $html ): ?string {
		return static::create_fragment( $html )->serialize();
	}

	/**
	 * Returns normalized HTML for a fragment by serializing it.
	 *
	 * This differs from {@see WP_HTML_Processor::normalize} in that it starts with
	 * a specific HTML Processor, which _must_ not have already started scanning;
	 * it must be in the initial ready state and will be in the completed state once
	 * serialization is complete.
	 *
	 * Many aspects of an input HTML fragment may be changed during normalization.
	 *
	 *  - Attribute values will be double-quoted.
	 *  - Duplicate attributes will be removed.
	 *  - Omitted tags will be added.
	 *  - Tag and attribute name casing will be lower-cased,
	 *    except for specific SVG and MathML tags or attributes.
	 *  - Text will be re-encoded, null bytes handled,
	 *    and invalid UTF-8 replaced with U+FFFD.
	 *  - Any incomplete syntax trailing at the end will be omitted,
	 *    for example, an unclosed comment opener will be removed.
	 *
	 * Example:
	 *
	 *     $processor = WP_HTML_Processor::create_fragment( '<a href=#anchor v=5 href="/" enabled>One</a another v=5><!--' );
	 *     echo $processor->serialize();
	 *     // <a href="#anchor" v="5" enabled>One</a>
	 *
	 *     $processor = WP_HTML_Processor::create_fragment( '<div></p>fun<table><td>cell</div>' );
	 *     echo $processor->serialize();
	 *     // <div><p></p>fun<table><tbody><tr><td>cell</td></tr></tbody></table></div>
	 *
	 *     $processor = WP_HTML_Processor::create_fragment( '<![CDATA[invalid comment]]> syntax < <> "oddities"' );
	 *     echo $processor->serialize();
	 *     // <!--[CDATA[invalid comment]]--> syntax &lt; &lt;&gt; &quot;oddities&quot;
	 *
	 * @since 6.7.0
	 *
	 * @return string|null Normalized HTML markup represented by processor,
	 *                     or `null` if unable to generate serialization.
	 */
	public function serialize(): ?string {
		if ( WP_HTML_Tag_Processor::STATE_READY !== $this->parser_state ) {
			wp_trigger_error(
				__METHOD__,
				'An HTML Processor which has already started processing cannot serialize its contents. Serialize immediately after creating the instance.',
				E_USER_WARNING
			);
			return null;
		}

		$html = '';
		while ( $this->next_token() ) {
			$html .= $this->serialize_token();
		}

		if ( null !== $this->get_last_error() ) {
			wp_trigger_error(
				__METHOD__,
				"Cannot serialize HTML Processor with parsing error: {$this->get_last_error()}.",
				E_USER_WARNING
			);
			return null;
		}

		return $html;
	}

	/**
	 * Serializes the currently-matched token.
	 *
	 * This method produces a fully-normative HTML string for the currently-matched token,
	 * if able. If not matched at any token or if the token doesn't correspond to any HTML
	 * it will return an empty string (for example, presumptuous end tags are ignored).
	 *
	 * @see static::serialize()
	 *
	 * @since 6.7.0
	 *
	 * @return string Serialization of token, or empty string if no serialization exists.
	 */
	protected function serialize_token(): string {
		$html       = '';
		$token_type = $this->get_token_type();

		switch ( $token_type ) {
			case '#doctype':
				$doctype = $this->get_doctype_info();
				if ( null === $doctype ) {
					break;
				}

				$html .= '<!DOCTYPE';

				if ( $doctype->name ) {
					$html .= " {$doctype->name}";
				}

				if ( null !== $doctype->public_identifier ) {
					$quote = str_contains( $doctype->public_identifier, '"' ) ? "'" : '"';
					$html .= " PUBLIC {$quote}{$doctype->public_identifier}{$quote}";
				}
				if ( null !== $doctype->system_identifier ) {
					if ( null === $doctype->public_identifier ) {
						$html .= ' SYSTEM';
					}
					$quote = str_contains( $doctype->system_identifier, '"' ) ? "'" : '"';
					$html .= " {$quote}{$doctype->system_identifier}{$quote}";
				}

				$html .= '>';
				break;

			case '#text':
				$html .= htmlspecialchars( $this->get_modifiable_text(), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8' );
				break;

			// Unlike the `<>` which is interpreted as plaintext, this is ignored entirely.
			case '#presumptuous-tag':
				break;

			case '#funky-comment':
			case '#comment':
				$html .= "<!--{$this->get_full_comment_text()}-->";
				break;

			case '#cdata-section':
				$html .= "<![CDATA[{$this->get_modifiable_text()}]]>";
				break;
		}

		if ( '#tag' !== $token_type ) {
			return $html;
		}

		$tag_name       = str_replace( "\x00", "\u{FFFD}", $this->get_tag() );
		$in_html        = 'html' === $this->get_namespace();
		$qualified_name = $in_html ? strtolower( $tag_name ) : $this->get_qualified_tag_name();

		if ( $this->is_tag_closer() ) {
			$html .= "</{$qualified_name}>";
			return $html;
		}

		$attribute_names = $this->get_attribute_names_with_prefix( '' );
		if ( ! isset( $attribute_names ) ) {
			$html .= "<{$qualified_name}>";
			return $html;
		}

		$html .= "<{$qualified_name}";
		foreach ( $attribute_names as $attribute_name ) {
			$html .= " {$this->get_qualified_attribute_name( $attribute_name )}";
			$value = $this->get_attribute( $attribute_name );

			if ( is_string( $value ) ) {
				$html .= '="' . htmlspecialchars( $value, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5 ) . '"';
			}

			$html = str_replace( "\x00", "\u{FFFD}", $html );
		}

		if ( ! $in_html && $this->has_self_closing_flag() ) {
			$html .= ' /';
		}

		$html .= '>';

		// Flush out self-contained elements.
		if ( $in_html && in_array( $tag_name, array( 'IFRAME', 'NOEMBED', 'NOFRAMES', 'SCRIPT', 'STYLE', 'TEXTAREA', 'TITLE', 'XMP' ), true ) ) {
			$text = $this->get_modifiable_text();

			switch ( $tag_name ) {
				case 'IFRAME':
				case 'NOEMBED':
				case 'NOFRAMES':
					$text = '';
					break;

				case 'SCRIPT':
				case 'STYLE':
					break;

				default:
					$text = htmlspecialchars( $text, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8' );
			}

			$html .= "{$text}</{$qualified_name}>";
		}

		return $html;
	}

	/**
	 * Parses next element in the 'initial' insertion mode.
	 *
	 * This internal function performs the 'initial' insertion mode
	 * logic for the generalized WP_HTML_Processor::step() function.
	 *
	 * @since 6.7.0
	 *
	 * @throws WP_HTML_Unsupported_Exception When encountering unsupported HTML input.
	 *
	 * @see https://html.spec.whatwg.org/#the-initial-insertion-mode
	 * @see WP_HTML_Processor::step
	 *
	 * @return bool Whether an element was found.
	 */
	private function step_initial(): bool {
		$token_name = $this->get_token_name();
		$token_type = $this->get_token_type();
		$op_sigil   = '#tag' === $token_type ? ( parent::is_tag_closer() ? '-' : '+' ) : '';
		$op         = "{$op_sigil}{$token_name}";

		switch ( $op ) {
			/*
			 * > A character token that is one of U+0009 CHARACTER TABULATION,
			 * > U+000A LINE FEED (LF), U+000C FORM FEED (FF),
			 * > U+000D CARRIAGE RETURN (CR), or U+0020 SPACE
			 *
			 * Parse error: ignore the token.
			 */
			case '#text':
				if ( parent::TEXT_IS_WHITESPACE === $this->text_node_classification ) {
					return $this->step();
				}
				goto initial_anything_else;
				break;

			/*
			 * > A comment token
			 */
			case '#comment':
			case '#funky-comment':
			case '#presumptuous-tag':
				$this->insert_html_element( $this->state->current_token );
				return true;

			/*
			 * > A DOCTYPE token
			 */
			case 'html':
				$doctype = $this->get_doctype_info();
				if ( null !== $doctype && 'quirks' === $doctype->indicated_compatability_mode ) {
					$this->compat_mode = WP_HTML_Tag_Processor::QUIRKS_MODE;
				}

				/*
				 * > Then, switch the insertion mode to "before html".
				 */
				$this->state->insertion_mode = WP_HTML_Processor_State::INSERTION_MODE_BEFORE_HTML;
				$this->insert_html_element( $this->state->current_token );
				return true;
		}

		/*
		 * > Anything else
		 */
		initial_anything_else:
		$this->compat_mode           = WP_HTML_Tag_Processor::QUIRKS_MODE;
		$this->state->insertion_mode = WP_HTML_Processor_State::INSERTION_MODE_BEFORE_HTML;
		return $this->step( self::REPROCESS_CURRENT_NODE );
	}

	/**
	 * Parses next element in the 'before html' insertion mode.
	 *
	 * This internal function performs the 'before html' insertion mode
	 * logic for the generalized WP_HTML_Processor::step() function.
	 *
	 * @since 6.7.0
	 *
	 * @throws WP_HTML_Unsupported_Exception When encountering unsupported HTML input.
	 *
	 * @see https://html.spec.whatwg.org/#the-before-html-insertion-mode
	 * @see WP_HTML_Processor::step
	 *
	 * @return bool Whether an element was found.
	 */
	private function step_before_html(): bool {
		$token_name = $this->get_token_name();
		$token_type = $this->get_token_type();
		$is_closer  = parent::is_tag_closer();
		$op_sigil   = '#tag' === $token_type ? ( $is_closer ? '-' : '+' ) : '';
		$op         = "{$op_sigil}{$token_name}";

		switch ( $op ) {
			/*
			 * > A DOCTYPE token
			 */
			case 'html':
				// Parse error: ignore the token.
				return $this->step();

			/*
			 * > A comment token
			 */
			case '#comment':
			case '#funky-comment':
			case '#presumptuous-tag':
				$this->insert_html_element( $this->state->current_token );
				return true;

			/*
			 * > A character token that is one of U+0009 CHARACTER TABULATION,
			 * > U+000A LINE FEED (LF), U+000C FORM FEED (FF),
			 * > U+000D CARRIAGE RETURN (CR), or U+0020 SPACE
			 *
			 * Parse error: ignore the token.
			 */
			case '#text':
				if ( parent::TEXT_IS_WHITESPACE === $this->text_node_classification ) {
					return $this->step();
				}
				goto before_html_anything_else;
				break;

			/*
			 * > A start tag whose tag name is "html"
			 */
			case '+HTML':
				$this->insert_html_element( $this->state->current_token );
				$this->state->insertion_mode = WP_HTML_Processor_State::INSERTION_MODE_BEFORE_HEAD;
				return true;

			/*
			 * > An end tag whose tag name is one of: "head", "body", "html", "br"
			 *
			 * Closing BR tags are always reported by the Tag Processor as opening tags.
			 */
			case '-HEAD':
			case '-BODY':
			case '-HTML':
				/*
				 * > Act as described in the "anything else" entry below.
				 */
				goto before_html_anything_else;
				break;
		}

		/*
		 * > Any other end tag
		 */
		if ( $is_closer ) {
			// Parse error: ignore the token.
			return $this->step();
		}

		/*
		 * > Anything else.
		 *
		 * > Create an html element whose node document is the Document object.
		 * > Append it to the Document object. Put this element in the stack of open elements.
		 * > Switch the insertion mode to "before head", then reprocess the token.
		 */
		before_html_anything_else:
		$this->insert_virtual_node( 'HTML' );
		$this->state->insertion_mode = WP_HTML_Processor_State::INSERTION_MODE_BEFORE_HEAD;
		return $this->step( self::REPROCESS_CURRENT_NODE );
	}

	/**
	 * Parses next element in the 'before head' insertion mode.
	 *
	 * This internal function performs the 'before head' insertion mode
	 * logic for the generalized WP_HTML_Processor::step() function.
	 *
	 * @since 6.7.0 Stub implementation.
	 *
	 * @throws WP_HTML_Unsupported_Exception When encountering unsupported HTML input.
	 *
	 * @see https://html.spec.whatwg.org/#the-before-head-insertion-mode
	 * @see WP_HTML_Processor::step
	 *
	 * @return bool Whether an element was found.
	 */
	private function step_before_head(): bool {
		$token_name = $this->get_token_name();
		$token_type = $this->get_token_type();
		$is_closer  = parent::is_tag_closer();
		$op_sigil   = '#tag' === $token_type ? ( $is_closer ? '-' : '+' ) : '';
		$op         = "{$op_sigil}{$token_name}";

		switch ( $op ) {
			/*
			 * > A character token that is one of U+0009 CHARACTER TABULATION,
			 * > U+000A LINE FEED (LF), U+000C FORM FEED (FF),
			 * > U+000D CARRIAGE RETURN (CR), or U+0020 SPACE
			 *
			 * Parse error: ignore the token.
			 */
			case '#text':
				if ( parent::TEXT_IS_WHITESPACE === $this->text_node_classification ) {
					return $this->step();
				}
				goto before_head_anything_else;
				break;

			/*
			 * > A comment token
			 */
			case '#comment':
			case '#funky-comment':
			case '#presumptuous-tag':
				$this->insert_html_element( $this->state->current_token );
				return true;

			/*
			 * > A DOCTYPE token
			 */
			case 'html':
				// Parse error: ignore the token.
				return $this->step();

			/*
			 * > A start tag whose tag name is "html"
			 */
			case '+HTML':
				return $this->step_in_body();

			/*
			 * > A start tag whose tag name is "head"
			 */
			case '+HEAD':
				$this->insert_html_element( $this->state->current_token );
				$this->state->head_element   = $this->state->current_token;
				$this->state->insertion_mode = WP_HTML_Processor_State::INSERTION_MODE_IN_HEAD;
				return true;

			/*
			 * > An end tag whose tag name is one of: "head", "body", "html", "br"
			 * > Act as described in the "anything else" entry below.
			 *
			 * Closing BR tags are always reported by the Tag Processor as opening tags.
			 */
			case '-HEAD':
			case '-BODY':
			case '-HTML':
				goto before_head_anything_else;
				break;
		}

		if ( $is_closer ) {
			// Parse error: ignore the token.
			return $this->step();
		}

		/*
		 * > Anything else
		 *
		 * > Insert an HTML element for a "head" start tag token with no attributes.
		 */
		before_head_anything_else:
		$this->state->head_element   = $this->insert_virtual_node( 'HEAD' );
		$this->state->insertion_mode = WP_HTML_Processor_State::INSERTION_MODE_IN_HEAD;
		return $this->step( self::REPROCESS_CURRENT_NODE );
	}

	/**
	 * Parses next element in the 'in head' insertion mode.
	 *
	 * This internal function performs the 'in head' insertion mode
	 * logic for the generalized WP_HTML_Processor::step() function.
	 *
	 * @since 6.7.0
	 *
	 * @throws WP_HTML_Unsupported_Exception When encountering unsupported HTML input.
	 *
	 * @see https://html.spec.whatwg.org/multipage/parsing.html#parsing-main-inhead
	 * @see WP_HTML_Processor::step
	 *
	 * @return bool Whether an element was found.
	 */
	private function step_in_head(): bool {
		$token_name = $this->get_token_name();
		$token_type = $this->get_token_type();
		$is_closer  = parent::is_tag_closer();
		$op_sigil   = '#tag' === $token_type ? ( $is_closer ? '-' : '+' ) : '';
		$op         = "{$op_sigil}{$token_name}";

		switch ( $op ) {
			case '#text':
				/*
				 * > A character token that is one of U+0009 CHARACTER TABULATION,
				 * > U+000A LINE FEED (LF), U+000C FORM FEED (FF),
				 * > U+000D CARRIAGE RETURN (CR), or U+0020 SPACE
				 */
				if ( parent::TEXT_IS_WHITESPACE === $this->text_node_classification ) {
					// Insert the character.
					$this->insert_html_element( $this->state->current_token );
					return true;
				}

				goto in_head_anything_else;
				break;

			/*
			 * > A comment token
			 */
			case '#comment':
			case '#funky-comment':
			case '#presumptuous-tag':
				$this->insert_html_element( $this->state->current_token );
				return true;

			/*
			 * > A DOCTYPE token
			 */
			case 'html':
				// Parse error: ignore the token.
				return $this->step();

			/*
			 * > A start tag whose tag name is "html"
			 */
			case '+HTML':
				return $this->step_in_body();

			/*
			 * > A start tag whose tag name is one of: "base", "basefont", "bgsound", "link"
			 */
			case '+BASE':
			case '+BASEFONT':
			case '+BGSOUND':
			case '+LINK':
				$this->insert_html_element( $this->state->current_token );
				return true;

			/*
			 * > A start tag whose tag name is "meta"
			 */
			case '+META':
				$this->insert_html_element( $this->state->current_token );

				/*
				 * > If the active speculative HTML parser is null, then:
				 * >   - If the element has a charset attribute, and getting an encoding from
				 * >     its value results in an encoding, and the confidence is currently
				 * >     tentative, then change the encoding to the resulting encoding.
				 */
				$charset = $this->get_attribute( 'charset' );
				if ( is_string( $charset ) && 'tentative' === $this->state->encoding_confidence ) {
					$this->bail( 'Cannot yet process META tags with charset to determine encoding.' );
				}

				/*
				 * >   - Otherwise, if the element has an http-equiv attribute whose value is
				 * >     an ASCII case-insensitive match for the string "Content-Type", and
				 * >     the element has a content attribute, and applying the algorithm for
				 * >     extracting a character encoding from a meta element to that attribute's
				 * >     value returns an encoding, and the confidence is currently tentative,
				 * >     then change the encoding to the extracted encoding.
				 */
				$http_equiv = $this->get_attribute( 'http-equiv' );
				$content    = $this->get_attribute( 'content' );
				if (
					is_string( $http_equiv ) &&
					is_string( $content ) &&
					0 === strcasecmp( $http_equiv, 'Content-Type' ) &&
					'tentative' === $this->state->encoding_confidence
				) {
					$this->bail( 'Cannot yet process META tags with http-equiv Content-Type to determine encoding.' );
				}

				return true;

			/*
			 * > A start tag whose tag name is "title"
			 */
			case '+TITLE':
				$this->insert_html_element( $this->state->current_token );
				return true;

			/*
			 * > A start tag whose tag name is "noscript", if the scripting flag is enabled
			 * > A start tag whose tag name is one of: "noframes", "style"
			 *
			 * The scripting flag is never enabled in this parser.
			 */
			case '+NOFRAMES':
			case '+STYLE':
				$this->insert_html_element( $this->state->current_token );
				return true;

			/*
			 * > A start tag whose tag name is "noscript", if the scripting flag is disabled
			 */
			case '+NOSCRIPT':
				$this->insert_html_element( $this->state->current_token );
				$this->state->insertion_mode = WP_HTML_Processor_State::INSERTION_MODE_IN_HEAD_NOSCRIPT;
				return true;

			/*
			 * > A start tag whose tag name is "script"
			 *
			 * @todo Could the adjusted insertion location be anything other than the current location?
			 */
			case '+SCRIPT':
				$this->insert_html_element( $this->state->current_token );
				return true;

			/*
			 * > An end tag whose tag name is "head"
			 */
			case '-HEAD':
				$this->state->stack_of_open_elements->pop();
				$this->state->insertion_mode = WP_HTML_Processor_State::INSERTION_MODE_AFTER_HEAD;
				return true;

			/*
			 * > An end tag whose tag name is one of: "body", "html", "br"
			 *
			 * BR tags are always reported by the Tag Processor as opening tags.
			 */
			case '-BODY':
			case '-HTML':
				/*
				 * > Act as described in the "anything else" entry below.
				 */
				goto in_head_anything_else;
				break;

			/*
			 * > A start tag whose tag name is "template"
			 *
			 * @todo Could the adjusted insertion location be anything other than the current location?
			 */
			case '+TEMPLATE':
				$this->state->active_formatting_elements->insert_marker();
				$this->state->frameset_ok = false;

				$this->state->insertion_mode                      = WP_HTML_Processor_State::INSERTION_MODE_IN_TEMPLATE;
				$this->state->stack_of_template_insertion_modes[] = WP_HTML_Processor_State::INSERTION_MODE_IN_TEMPLATE;

				$this->insert_html_element( $this->state->current_token );
				return true;

			/*
			 * > An end tag whose tag name is "template"
			 */
			case '-TEMPLATE':
				if ( ! $this->state->stack_of_open_elements->contains( 'TEMPLATE' ) ) {
					// @todo Indicate a parse error once it's possible.
					return $this->step();
				}

				$this->generate_implied_end_tags_thoroughly();
				if ( ! $this->state->stack_of_open_elements->current_node_is( 'TEMPLATE' ) ) {
					// @todo Indicate a parse error once it's possible.
				}

				$this->state->stack_of_open_elements->pop_until( 'TEMPLATE' );
				$this->state->active_formatting_elements->clear_up_to_last_marker();
				array_pop( $this->state->stack_of_template_insertion_modes );
				$this->reset_insertion_mode_appropriately();
				return true;
		}

		/*
		 * > A start tag whose tag name is "head"
		 * > Any other end tag
		 */
		if ( '+HEAD' === $op || $is_closer ) {
			// Parse error: ignore the token.
			return $this->step();
		}

		/*
		 * > Anything else
		 */
		in_head_anything_else:
		$this->state->stack_of_open_elements->pop();
		$this->state->insertion_mode = WP_HTML_Processor_State::INSERTION_MODE_AFTER_HEAD;
		return $this->step( self::REPROCESS_CURRENT_NODE );
	}

	/**
	 * Parses next element in the 'in head noscript' insertion mode.
	 *
	 * This internal function performs the 'in head noscript' insertion mode
	 * logic for the generalized WP_HTML_Processor::step() function.
	 *
	 * @since 6.7.0 Stub implementation.
	 *
	 * @throws WP_HTML_Unsupported_Exception When encountering unsupported HTML input.
	 *
	 * @see https://html.spec.whatwg.org/#parsing-main-inheadnoscript
	 * @see WP_HTML_Processor::step
	 *
	 * @return bool Whether an element was found.
	 */
	private function step_in_head_noscript(): bool {
		$token_name = $this->get_token_name();
		$token_type = $this->get_token_type();
		$is_closer  = parent::is_tag_closer();
		$op_sigil   = '#tag' === $token_type ? ( $is_closer ? '-' : '+' ) : '';
		$op         = "{$op_sigil}{$token_name}";

		switch ( $op ) {
			/*
			 * > A character token that is one of U+0009 CHARACTER TABULATION,
			 * > U+000A LINE FEED (LF), U+000C FORM FEED (FF),
			 * > U+000D CARRIAGE RETURN (CR), or U+0020 SPACE
			 *
			 * Parse error: ignore the token.
			 */
			case '#text':
				if ( parent::TEXT_IS_WHITESPACE === $this->text_node_classification ) {
					return $this->step_in_head();
				}

				goto in_head_noscript_anything_else;
				break;

			/*
			 * > A DOCTYPE token
			 */
			case 'html':
				// Parse error: ignore the token.
				return $this->step();

			/*
			 * > A start tag whose tag name is "html"
			 */
			case '+HTML':
				return $this->step_in_body();

			/*
			 * > An end tag whose tag name is "noscript"
			 */
			case '-NOSCRIPT':
				$this->state->stack_of_open_elements->pop();
				$this->state->insertion_mode = WP_HTML_Processor_State::INSERTION_MODE_IN_HEAD;
				return true;

			/*
			 * > A comment token
			 * >
			 * > A start tag whose tag name is one of: "basefont", "bgsound",
			 * > "link", "meta", "noframes", "style"
			 */
			case '#comment':
			case '#funky-comment':
			case '#presumptuous-tag':
			case '+BASEFONT':
			case '+BGSOUND':
			case '+LINK':
			case '+META':
			case '+NOFRAMES':
			case '+STYLE':
				return $this->step_in_head();

			/*
			 * > An end tag whose tag name is "br"
			 *
			 * This should never happen, as the Tag Processor prevents showing a BR closing tag.
			 */
		}

		/*
		 * > A start tag whose tag name is one of: "head", "noscript"
		 * > Any other end tag
		 */
		if ( '+HEAD' === $op || '+NOSCRIPT' === $op || $is_closer ) {
			// Parse error: ignore the token.
			return $this->step();
		}

		/*
		 * > Anything else
		 *
		 * Anything here is a parse error.
		 */
		in_head_noscript_anything_else:
		$this->state->stack_of_open_elements->pop();
		$this->state->insertion_mode = WP_HTML_Processor_State::INSERTION_MODE_IN_HEAD;
		return $this->step( self::REPROCESS_CURRENT_NODE );
	}

	/**
	 * Parses next element in the 'after head' insertion mode.
	 *
	 * This internal function performs the 'after head' insertion mode
	 * logic for the generalized WP_HTML_Processor::step() function.
	 *
	 * @since 6.7.0 Stub implementation.
	 *
	 * @throws WP_HTML_Unsupported_Exception When encountering unsupported HTML input.
	 *
	 * @see https://html.spec.whatwg.org/#the-after-head-insertion-mode
	 * @see WP_HTML_Processor::step
	 *
	 * @return bool Whether an element was found.
	 */
	private function step_after_head(): bool {
		$token_name = $this->get_token_name();
		$token_type = $this->get_token_type();
		$is_closer  = parent::is_tag_closer();
		$op_sigil   = '#tag' === $token_type ? ( $is_closer ? '-' : '+' ) : '';
		$op         = "{$op_sigil}{$token_name}";

		switch ( $op ) {
			/*
			 * > A character token that is one of U+0009 CHARACTER TABULATION,
			 * > U+000A LINE FEED (LF), U+000C FORM FEED (FF),
			 * > U+000D CARRIAGE RETURN (CR), or U+0020 SPACE
			 */
			case '#text':
				if ( parent::TEXT_IS_WHITESPACE === $this->text_node_classification ) {
					// Insert the character.
					$this->insert_html_element( $this->state->current_token );
					return true;
				}
				goto after_head_anything_else;
				break;

			/*
			 * > A comment token
			 */
			case '#comment':
			case '#funky-comment':
			case '#presumptuous-tag':
				$this->insert_html_element( $this->state->current_token );
				return true;

			/*
			 * > A DOCTYPE token
			 */
			case 'html':
				// Parse error: ignore the token.
				return $this->step();

			/*
			 * > A start tag whose tag name is "html"
			 */
			case '+HTML':
				return $this->step_in_body();

			/*
			 * > A start tag whose tag name is "body"
			 */
			case '+BODY':
				$this->insert_html_element( $this->state->current_token );
				$this->state->frameset_ok    = false;
				$this->state->insertion_mode = WP_HTML_Processor_State::INSERTION_MODE_IN_BODY;
				return true;

			/*
			 * > A start tag whose tag name is "frameset"
			 */
			case '+FRAMESET':
				$this->insert_html_element( $this->state->current_token );
				$this->state->insertion_mode = WP_HTML_Processor_State::INSERTION_MODE_IN_FRAMESET;
				return true;

			/*
			 * > A start tag whose tag name is one of: "base", "basefont", "bgsound",
			 * > "link", "meta", "noframes", "script", "style", "template", "title"
			 *
			 * Anything here is a parse error.
			 */
			case '+BASE':
			case '+BASEFONT':
			case '+BGSOUND':
			case '+LINK':
			case '+META':
			case '+NOFRAMES':
			case '+SCRIPT':
			case '+STYLE':
			case '+TEMPLATE':
			case '+TITLE':
				/*
				 * > Push the node pointed to by the head element pointer onto the stack of open elements.
				 * > Process the token using the rules for the "in head" insertion mode.
				 * > Remove the node pointed to by the head element pointer from the stack of open elements. (It might not be the current node at this point.)
				 */
				$this->bail( 'Cannot process elements after HEAD which reopen the HEAD element.' );
				/*
				 * Do not leave this break in when adding support; it's here to prevent
				 * WPCS from getting confused at the switch structure without a return,
				 * because it doesn't know that `bail()` always throws.
				 */
				break;

			/*
			 * > An end tag whose tag name is "template"
			 */
			case '-TEMPLATE':
				return $this->step_in_head();

			/*
			 * > An end tag whose tag name is one of: "body", "html", "br"
			 *
			 * Closing BR tags are always reported by the Tag Processor as opening tags.
			 */
			case '-BODY':
			case '-HTML':
				/*
				 * > Act as described in the "anything else" entry below.
				 */
				goto after_head_anything_else;
				break;
		}

		/*
		 * > A start tag whose tag name is "head"
		 * > Any other end tag
		 */
		if ( '+HEAD' === $op || $is_closer ) {
			// Parse error: ignore the token.
			return $this->step();
		}

		/*
		 * > Anything else
		 * > Insert an HTML element for a "body" start tag token with no attributes.
		 */
		after_head_anything_else:
		$this->insert_virtual_node( 'BODY' );
		$this->state->insertion_mode = WP_HTML_Processor_State::INSERTION_MODE_IN_BODY;
		return $this->step( self::REPROCESS_CURRENT_NODE );
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
	private function step_in_body(): bool {
		$token_name = $this->get_token_name();
		$token_type = $this->get_token_type();
		$op_sigil   = '#tag' === $token_type ? ( parent::is_tag_closer() ? '-' : '+' ) : '';
		$op         = "{$op_sigil}{$token_name}";

		switch ( $op ) {
			case '#text':
				/*
				 * > A character token that is U+0000 NULL
				 *
				 * Any successive sequence of NULL bytes is ignored and won't
				 * trigger active format reconstruction. Therefore, if the text
				 * only comprises NULL bytes then the token should be ignored
				 * here, but if there are any other characters in the stream
				 * the active formats should be reconstructed.
				 */
				if ( parent::TEXT_IS_NULL_SEQUENCE === $this->text_node_classification ) {
					// Parse error: ignore the token.
					return $this->step();
				}

				$this->reconstruct_active_formatting_elements();

				/*
				 * Whitespace-only text does not affect the frameset-ok flag.
				 * It is probably inter-element whitespace, but it may also
				 * contain character references which decode only to whitespace.
				 */
				if ( parent::TEXT_IS_GENERIC === $this->text_node_classification ) {
					$this->state->frameset_ok = false;
				}

				$this->insert_html_element( $this->state->current_token );
				return true;

			case '#comment':
			case '#funky-comment':
			case '#presumptuous-tag':
				$this->insert_html_element( $this->state->current_token );
				return true;

			/*
			 * > A DOCTYPE token
			 * > Parse error. Ignore the token.
			 */
			case 'html':
				return $this->step();

			/*
			 * > A start tag whose tag name is "html"
			 */
			case '+HTML':
				if ( ! $this->state->stack_of_open_elements->contains( 'TEMPLATE' ) ) {
					/*
					 * > Otherwise, for each attribute on the token, check to see if the attribute
					 * > is already present on the top element of the stack of open elements. If
					 * > it is not, add the attribute and its corresponding value to that element.
					 *
					 * This parser does not currently support this behavior: ignore the token.
					 */
				}

				// Ignore the token.
				return $this->step();

			/*
			 * > A start tag whose tag name is one of: "base", "basefont", "bgsound", "link",
			 * > "meta", "noframes", "script", "style", "template", "title"
			 * >
			 * > An end tag whose tag name is "template"
			 */
			case '+BASE':
			case '+BASEFONT':
			case '+BGSOUND':
			case '+LINK':
			case '+META':
			case '+NOFRAMES':
			case '+SCRIPT':
			case '+STYLE':
			case '+TEMPLATE':
			case '+TITLE':
			case '-TEMPLATE':
				return $this->step_in_head();

			/*
			 * > A start tag whose tag name is "body"
			 *
			 * This tag in the IN BODY insertion mode is a parse error.
			 */
			case '+BODY':
				if (
					1 === $this->state->stack_of_open_elements->count() ||
					'BODY' !== ( $this->state->stack_of_open_elements->at( 2 )->node_name ?? null ) ||
					$this->state->stack_of_open_elements->contains( 'TEMPLATE' )
				) {
					// Ignore the token.
					return $this->step();
				}

				/*
				 * > Otherwise, set the frameset-ok flag to "not ok"; then, for each attribute
				 * > on the token, check to see if the attribute is already present on the body
				 * > element (the second element) on the stack of open elements, and if it is
				 * > not, add the attribute and its corresponding value to that element.
				 *
				 * This parser does not currently support this behavior: ignore the token.
				 */
				$this->state->frameset_ok = false;
				return $this->step();

			/*
			 * > A start tag whose tag name is "frameset"
			 *
			 * This tag in the IN BODY insertion mode is a parse error.
			 */
			case '+FRAMESET':
				if (
					1 === $this->state->stack_of_open_elements->count() ||
					'BODY' !== ( $this->state->stack_of_open_elements->at( 2 )->node_name ?? null ) ||
					false === $this->state->frameset_ok
				) {
					// Ignore the token.
					return $this->step();
				}

				/*
				 * > Otherwise, run the following steps:
				 */
				$this->bail( 'Cannot process non-ignored FRAMESET tags.' );
				break;

			/*
			 * > An end tag whose tag name is "body"
			 */
			case '-BODY':
				if ( ! $this->state->stack_of_open_elements->has_element_in_scope( 'BODY' ) ) {
					// Parse error: ignore the token.
					return $this->step();
				}

				/*
				 * > Otherwise, if there is a node in the stack of open elements that is not either a
				 * > dd element, a dt element, an li element, an optgroup element, an option element,
				 * > a p element, an rb element, an rp element, an rt element, an rtc element, a tbody
				 * > element, a td element, a tfoot element, a th element, a thread element, a tr
				 * > element, the body element, or the html element, then this is a parse error.
				 *
				 * There is nothing to do for this parse error, so don't check for it.
				 */

				$this->state->insertion_mode = WP_HTML_Processor_State::INSERTION_MODE_AFTER_BODY;
				return true;

			/*
			 * > An end tag whose tag name is "html"
			 */
			case '-HTML':
				if ( ! $this->state->stack_of_open_elements->has_element_in_scope( 'BODY' ) ) {
					// Parse error: ignore the token.
					return $this->step();
				}

				/*
				 * > Otherwise, if there is a node in the stack of open elements that is not either a
				 * > dd element, a dt element, an li element, an optgroup element, an option element,
				 * > a p element, an rb element, an rp element, an rt element, an rtc element, a tbody
				 * > element, a td element, a tfoot element, a th element, a thread element, a tr
				 * > element, the body element, or the html element, then this is a parse error.
				 *
				 * There is nothing to do for this parse error, so don't check for it.
				 */

				$this->state->insertion_mode = WP_HTML_Processor_State::INSERTION_MODE_AFTER_BODY;
				return $this->step( self::REPROCESS_CURRENT_NODE );

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

				/*
				 * > If the next token is a U+000A LINE FEED (LF) character token,
				 * > then ignore that token and move on to the next one. (Newlines
				 * > at the start of pre blocks are ignored as an authoring convenience.)
				 *
				 * This is handled in `get_modifiable_text()`.
				 */

				$this->insert_html_element( $this->state->current_token );
				$this->state->frameset_ok = false;
				return true;

			/*
			 * > A start tag whose tag name is "form"
			 */
			case '+FORM':
				$stack_contains_template = $this->state->stack_of_open_elements->contains( 'TEMPLATE' );

				if ( isset( $this->state->form_element ) && ! $stack_contains_template ) {
					// Parse error: ignore the token.
					return $this->step();
				}

				if ( $this->state->stack_of_open_elements->has_p_in_button_scope() ) {
					$this->close_a_p_element();
				}

				$this->insert_html_element( $this->state->current_token );
				if ( ! $stack_contains_template ) {
					$this->state->form_element = $this->state->current_token;
				}

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
					if ( ! $this->state->stack_of_open_elements->current_node_is( $node_name ) ) {
						// @todo Indicate a parse error once it's possible. This error does not impact the logic here.
					}

					$this->state->stack_of_open_elements->pop_until( $node_name );
					goto in_body_list_done;
				}

				if (
					'ADDRESS' !== $node->node_name &&
					'DIV' !== $node->node_name &&
					'P' !== $node->node_name &&
					self::is_special( $node )
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

			case '+PLAINTEXT':
				if ( $this->state->stack_of_open_elements->has_p_in_button_scope() ) {
					$this->close_a_p_element();
				}

				/*
				 * @todo This may need to be handled in the Tag Processor and turn into
				 *       a single self-contained tag like TEXTAREA, whose modifiable text
				 *       is the rest of the input document as plaintext.
				 */
				$this->bail( 'Cannot process PLAINTEXT elements.' );
				break;

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
				if ( ! $this->state->stack_of_open_elements->current_node_is( $token_name ) ) {
					// @todo Record parse error: this error doesn't impact parsing.
				}
				$this->state->stack_of_open_elements->pop_until( $token_name );
				return true;

			/*
			 * > An end tag whose tag name is "form"
			 */
			case '-FORM':
				if ( ! $this->state->stack_of_open_elements->contains( 'TEMPLATE' ) ) {
					$node                      = $this->state->form_element;
					$this->state->form_element = null;

					/*
					 * > If node is null or if the stack of open elements does not have node
					 * > in scope, then this is a parse error; return and ignore the token.
					 *
					 * @todo It's necessary to check if the form token itself is in scope, not
					 *       simply whether any FORM is in scope.
					 */
					if (
						null === $node ||
						! $this->state->stack_of_open_elements->has_element_in_scope( 'FORM' )
					) {
						// Parse error: ignore the token.
						return $this->step();
					}

					$this->generate_implied_end_tags();
					if ( $node !== $this->state->stack_of_open_elements->current_node() ) {
						// @todo Indicate a parse error once it's possible. This error does not impact the logic here.
						$this->bail( 'Cannot close a FORM when other elements remain open as this would throw off the breadcrumbs for the following tokens.' );
					}

					$this->state->stack_of_open_elements->remove_node( $node );
					return true;
				} else {
					/*
					 * > If the stack of open elements does not have a form element in scope,
					 * > then this is a parse error; return and ignore the token.
					 *
					 * Note that unlike in the clause above, this is checking for any FORM in scope.
					 */
					if ( ! $this->state->stack_of_open_elements->has_element_in_scope( 'FORM' ) ) {
						// Parse error: ignore the token.
						return $this->step();
					}

					$this->generate_implied_end_tags();

					if ( ! $this->state->stack_of_open_elements->current_node_is( 'FORM' ) ) {
						// @todo Indicate a parse error once it's possible. This error does not impact the logic here.
					}

					$this->state->stack_of_open_elements->pop_until( 'FORM' );
					return true;
				}
				break;

			/*
			 * > An end tag whose tag name is "p"
			 */
			case '-P':
				if ( ! $this->state->stack_of_open_elements->has_p_in_button_scope() ) {
					$this->insert_html_element( $this->state->current_token );
				}

				$this->close_a_p_element();
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

				if ( ! $this->state->stack_of_open_elements->current_node_is( $token_name ) ) {
					// @todo Indicate a parse error once it's possible. This error does not impact the logic here.
				}

				$this->state->stack_of_open_elements->pop_until( $token_name );
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

				if ( ! $this->state->stack_of_open_elements->current_node_is( $token_name ) ) {
					// @todo Record parse error: this error doesn't impact parsing.
				}

				$this->state->stack_of_open_elements->pop_until( '(internal: H1 through H6 - do not use)' );
				return true;

			/*
			 * > A start tag whose tag name is "a"
			 */
			case '+A':
				foreach ( $this->state->active_formatting_elements->walk_up() as $item ) {
					switch ( $item->node_name ) {
						case 'marker':
							break 2;

						case 'A':
							$this->run_adoption_agency_algorithm();
							$this->state->active_formatting_elements->remove_node( $item );
							$this->state->stack_of_open_elements->remove_node( $item );
							break 2;
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
			 * > A start tag whose tag name is "nobr"
			 */
			case '+NOBR':
				$this->reconstruct_active_formatting_elements();

				if ( $this->state->stack_of_open_elements->has_element_in_scope( 'NOBR' ) ) {
					// Parse error.
					$this->run_adoption_agency_algorithm();
					$this->reconstruct_active_formatting_elements();
				}

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
			case '-NOBR':
			case '-S':
			case '-SMALL':
			case '-STRIKE':
			case '-STRONG':
			case '-TT':
			case '-U':
				$this->run_adoption_agency_algorithm();
				return true;

			/*
			 * > A start tag whose tag name is one of: "applet", "marquee", "object"
			 */
			case '+APPLET':
			case '+MARQUEE':
			case '+OBJECT':
				$this->reconstruct_active_formatting_elements();
				$this->insert_html_element( $this->state->current_token );
				$this->state->active_formatting_elements->insert_marker();
				$this->state->frameset_ok = false;
				return true;

			/*
			 * > A end tag token whose tag name is one of: "applet", "marquee", "object"
			 */
			case '-APPLET':
			case '-MARQUEE':
			case '-OBJECT':
				if ( ! $this->state->stack_of_open_elements->has_element_in_scope( $token_name ) ) {
					// Parse error: ignore the token.
					return $this->step();
				}

				$this->generate_implied_end_tags();
				if ( ! $this->state->stack_of_open_elements->current_node_is( $token_name ) ) {
					// This is a parse error.
				}

				$this->state->stack_of_open_elements->pop_until( $token_name );
				$this->state->active_formatting_elements->clear_up_to_last_marker();
				return true;

			/*
			 * > A start tag whose tag name is "table"
			 */
			case '+TABLE':
				/*
				 * > If the Document is not set to quirks mode, and the stack of open elements
				 * > has a p element in button scope, then close a p element.
				 */
				if (
					WP_HTML_Tag_Processor::QUIRKS_MODE !== $this->compat_mode &&
					$this->state->stack_of_open_elements->has_p_in_button_scope()
				) {
					$this->close_a_p_element();
				}

				$this->insert_html_element( $this->state->current_token );
				$this->state->frameset_ok    = false;
				$this->state->insertion_mode = WP_HTML_Processor_State::INSERTION_MODE_IN_TABLE;
				return true;

			/*
			 * > An end tag whose tag name is "br"
			 *
			 * This is prevented from happening because the Tag Processor
			 * reports all closing BR tags as if they were opening tags.
			 */

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

				/*
				 * > If the token does not have an attribute with the name "type", or if it does,
				 * > but that attribute's value is not an ASCII case-insensitive match for the
				 * > string "hidden", then: set the frameset-ok flag to "not ok".
				 */
				$type_attribute = $this->get_attribute( 'type' );
				if ( ! is_string( $type_attribute ) || 'hidden' !== strtolower( $type_attribute ) ) {
					$this->state->frameset_ok = false;
				}

				return true;

			/*
			 * > A start tag whose tag name is one of: "param", "source", "track"
			 */
			case '+PARAM':
			case '+SOURCE':
			case '+TRACK':
				$this->insert_html_element( $this->state->current_token );
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
			 * > A start tag whose tag name is "image"
			 */
			case '+IMAGE':
				/*
				 * > Parse error. Change the token's tag name to "img" and reprocess it. (Don't ask.)
				 *
				 * Note that this is handled elsewhere, so it should not be possible to reach this code.
				 */
				$this->bail( "Cannot process an IMAGE tag. (Don't ask.)" );
				break;

			/*
			 * > A start tag whose tag name is "textarea"
			 */
			case '+TEXTAREA':
				$this->insert_html_element( $this->state->current_token );

				/*
				 * > If the next token is a U+000A LINE FEED (LF) character token, then ignore
				 * > that token and move on to the next one. (Newlines at the start of
				 * > textarea elements are ignored as an authoring convenience.)
				 *
				 * This is handled in `get_modifiable_text()`.
				 */

				$this->state->frameset_ok = false;

				/*
				 * > Switch the insertion mode to "text".
				 *
				 * As a self-contained node, this behavior is handled in the Tag Processor.
				 */
				return true;

			/*
			 * > A start tag whose tag name is "xmp"
			 */
			case '+XMP':
				if ( $this->state->stack_of_open_elements->has_p_in_button_scope() ) {
					$this->close_a_p_element();
				}

				$this->reconstruct_active_formatting_elements();
				$this->state->frameset_ok = false;

				/*
				 * > Follow the generic raw text element parsing algorithm.
				 *
				 * As a self-contained node, this behavior is handled in the Tag Processor.
				 */
				$this->insert_html_element( $this->state->current_token );
				return true;

			/*
			 * A start tag whose tag name is "iframe"
			 */
			case '+IFRAME':
				$this->state->frameset_ok = false;

				/*
				 * > Follow the generic raw text element parsing algorithm.
				 *
				 * As a self-contained node, this behavior is handled in the Tag Processor.
				 */
				$this->insert_html_element( $this->state->current_token );
				return true;

			/*
			 * > A start tag whose tag name is "noembed"
			 * > A start tag whose tag name is "noscript", if the scripting flag is enabled
			 *
			 * The scripting flag is never enabled in this parser.
			 */
			case '+NOEMBED':
				$this->insert_html_element( $this->state->current_token );
				return true;

			/*
			 * > A start tag whose tag name is "select"
			 */
			case '+SELECT':
				$this->reconstruct_active_formatting_elements();
				$this->insert_html_element( $this->state->current_token );
				$this->state->frameset_ok = false;

				switch ( $this->state->insertion_mode ) {
					/*
					 * > If the insertion mode is one of "in table", "in caption", "in table body", "in row",
					 * > or "in cell", then switch the insertion mode to "in select in table".
					 */
					case WP_HTML_Processor_State::INSERTION_MODE_IN_TABLE:
					case WP_HTML_Processor_State::INSERTION_MODE_IN_CAPTION:
					case WP_HTML_Processor_State::INSERTION_MODE_IN_TABLE_BODY:
					case WP_HTML_Processor_State::INSERTION_MODE_IN_ROW:
					case WP_HTML_Processor_State::INSERTION_MODE_IN_CELL:
						$this->state->insertion_mode = WP_HTML_Processor_State::INSERTION_MODE_IN_SELECT_IN_TABLE;
						break;

					/*
					 * > Otherwise, switch the insertion mode to "in select".
					 */
					default:
						$this->state->insertion_mode = WP_HTML_Processor_State::INSERTION_MODE_IN_SELECT;
						break;
				}
				return true;

			/*
			 * > A start tag whose tag name is one of: "optgroup", "option"
			 */
			case '+OPTGROUP':
			case '+OPTION':
				if ( $this->state->stack_of_open_elements->current_node_is( 'OPTION' ) ) {
					$this->state->stack_of_open_elements->pop();
				}
				$this->reconstruct_active_formatting_elements();
				$this->insert_html_element( $this->state->current_token );
				return true;

			/*
			 * > A start tag whose tag name is one of: "rb", "rtc"
			 */
			case '+RB':
			case '+RTC':
				if ( $this->state->stack_of_open_elements->has_element_in_scope( 'RUBY' ) ) {
					$this->generate_implied_end_tags();

					if ( $this->state->stack_of_open_elements->current_node_is( 'RUBY' ) ) {
						// @todo Indicate a parse error once it's possible.
					}
				}

				$this->insert_html_element( $this->state->current_token );
				return true;

			/*
			 * > A start tag whose tag name is one of: "rp", "rt"
			 */
			case '+RP':
			case '+RT':
				if ( $this->state->stack_of_open_elements->has_element_in_scope( 'RUBY' ) ) {
					$this->generate_implied_end_tags( 'RTC' );

					$current_node_name = $this->state->stack_of_open_elements->current_node()->node_name;
					if ( 'RTC' === $current_node_name || 'RUBY' === $current_node_name ) {
						// @todo Indicate a parse error once it's possible.
					}
				}

				$this->insert_html_element( $this->state->current_token );
				return true;

			/*
			 * > A start tag whose tag name is "math"
			 */
			case '+MATH':
				$this->reconstruct_active_formatting_elements();

				/*
				 * @todo Adjust MathML attributes for the token. (This fixes the case of MathML attributes that are not all lowercase.)
				 * @todo Adjust foreign attributes for the token. (This fixes the use of namespaced attributes, in particular XLink.)
				 *
				 * These ought to be handled in the attribute methods.
				 */
				$this->state->current_token->namespace = 'math';
				$this->insert_html_element( $this->state->current_token );
				if ( $this->state->current_token->has_self_closing_flag ) {
					$this->state->stack_of_open_elements->pop();
				}
				return true;

			/*
			 * > A start tag whose tag name is "svg"
			 */
			case '+SVG':
				$this->reconstruct_active_formatting_elements();

				/*
				 * @todo Adjust SVG attributes for the token. (This fixes the case of SVG attributes that are not all lowercase.)
				 * @todo Adjust foreign attributes for the token. (This fixes the use of namespaced attributes, in particular XLink in SVG.)
				 *
				 * These ought to be handled in the attribute methods.
				 */
				$this->state->current_token->namespace = 'svg';
				$this->insert_html_element( $this->state->current_token );
				if ( $this->state->current_token->has_self_closing_flag ) {
					$this->state->stack_of_open_elements->pop();
				}
				return true;

			/*
			 * > A start tag whose tag name is one of: "caption", "col", "colgroup",
			 * > "frame", "head", "tbody", "td", "tfoot", "th", "thead", "tr"
			 */
			case '+CAPTION':
			case '+COL':
			case '+COLGROUP':
			case '+FRAME':
			case '+HEAD':
			case '+TBODY':
			case '+TD':
			case '+TFOOT':
			case '+TH':
			case '+THEAD':
			case '+TR':
				// Parse error. Ignore the token.
				return $this->step();
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
				if ( 'html' === $node->namespace && $token_name === $node->node_name ) {
					break;
				}

				if ( self::is_special( $node ) ) {
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

		$this->bail( 'Should not have been able to reach end of IN BODY processing. Check HTML API code.' );
		// This unnecessary return prevents tools from inaccurately reporting type errors.
		return false;
	}

	/**
	 * Parses next element in the 'in table' insertion mode.
	 *
	 * This internal function performs the 'in table' insertion mode
	 * logic for the generalized WP_HTML_Processor::step() function.
	 *
	 * @since 6.7.0
	 *
	 * @throws WP_HTML_Unsupported_Exception When encountering unsupported HTML input.
	 *
	 * @see https://html.spec.whatwg.org/#parsing-main-intable
	 * @see WP_HTML_Processor::step
	 *
	 * @return bool Whether an element was found.
	 */
	private function step_in_table(): bool {
		$token_name = $this->get_token_name();
		$token_type = $this->get_token_type();
		$op_sigil   = '#tag' === $token_type ? ( parent::is_tag_closer() ? '-' : '+' ) : '';
		$op         = "{$op_sigil}{$token_name}";

		switch ( $op ) {
			/*
			 * > A character token, if the current node is table,
			 * > tbody, template, tfoot, thead, or tr element
			 */
			case '#text':
				$current_node      = $this->state->stack_of_open_elements->current_node();
				$current_node_name = $current_node ? $current_node->node_name : null;
				if (
					$current_node_name && (
						'TABLE' === $current_node_name ||
						'TBODY' === $current_node_name ||
						'TEMPLATE' === $current_node_name ||
						'TFOOT' === $current_node_name ||
						'THEAD' === $current_node_name ||
						'TR' === $current_node_name
					)
				) {
					/*
					 * If the text is empty after processing HTML entities and stripping
					 * U+0000 NULL bytes then ignore the token.
					 */
					if ( parent::TEXT_IS_NULL_SEQUENCE === $this->text_node_classification ) {
						return $this->step();
					}

					/*
					 * This follows the rules for "in table text" insertion mode.
					 *
					 * Whitespace-only text nodes are inserted in-place. Otherwise
					 * foster parenting is enabled and the nodes would be
					 * inserted out-of-place.
					 *
					 * > If any of the tokens in the pending table character tokens
					 * > list are character tokens that are not ASCII whitespace,
					 * > then this is a parse error: reprocess the character tokens
					 * > in the pending table character tokens list using the rules
					 * > given in the "anything else" entry in the "in table"
					 * > insertion mode.
					 * >
					 * > Otherwise, insert the characters given by the pending table
					 * > character tokens list.
					 *
					 * @see https://html.spec.whatwg.org/#parsing-main-intabletext
					 */
					if ( parent::TEXT_IS_WHITESPACE === $this->text_node_classification ) {
						$this->insert_html_element( $this->state->current_token );
						return true;
					}

					// Non-whitespace would trigger fostering, unsupported at this time.
					$this->bail( 'Foster parenting is not supported.' );
					break;
				}
				break;

			/*
			 * > A comment token
			 */
			case '#comment':
			case '#funky-comment':
			case '#presumptuous-tag':
				$this->insert_html_element( $this->state->current_token );
				return true;

			/*
			 * > A DOCTYPE token
			 */
			case 'html':
				// Parse error: ignore the token.
				return $this->step();

			/*
			 * > A start tag whose tag name is "caption"
			 */
			case '+CAPTION':
				$this->state->stack_of_open_elements->clear_to_table_context();
				$this->state->active_formatting_elements->insert_marker();
				$this->insert_html_element( $this->state->current_token );
				$this->state->insertion_mode = WP_HTML_Processor_State::INSERTION_MODE_IN_CAPTION;
				return true;

			/*
			 * > A start tag whose tag name is "colgroup"
			 */
			case '+COLGROUP':
				$this->state->stack_of_open_elements->clear_to_table_context();
				$this->insert_html_element( $this->state->current_token );
				$this->state->insertion_mode = WP_HTML_Processor_State::INSERTION_MODE_IN_COLUMN_GROUP;
				return true;

			/*
			 * > A start tag whose tag name is "col"
			 */
			case '+COL':
				$this->state->stack_of_open_elements->clear_to_table_context();

				/*
				 * > Insert an HTML element for a "colgroup" start tag token with no attributes,
				 * > then switch the insertion mode to "in column group".
				 */
				$this->insert_virtual_node( 'COLGROUP' );
				$this->state->insertion_mode = WP_HTML_Processor_State::INSERTION_MODE_IN_COLUMN_GROUP;
				return $this->step( self::REPROCESS_CURRENT_NODE );

			/*
			 * > A start tag whose tag name is one of: "tbody", "tfoot", "thead"
			 */
			case '+TBODY':
			case '+TFOOT':
			case '+THEAD':
				$this->state->stack_of_open_elements->clear_to_table_context();
				$this->insert_html_element( $this->state->current_token );
				$this->state->insertion_mode = WP_HTML_Processor_State::INSERTION_MODE_IN_TABLE_BODY;
				return true;

			/*
			 * > A start tag whose tag name is one of: "td", "th", "tr"
			 */
			case '+TD':
			case '+TH':
			case '+TR':
				$this->state->stack_of_open_elements->clear_to_table_context();
				/*
				 * > Insert an HTML element for a "tbody" start tag token with no attributes,
				 * > then switch the insertion mode to "in table body".
				 */
				$this->insert_virtual_node( 'TBODY' );
				$this->state->insertion_mode = WP_HTML_Processor_State::INSERTION_MODE_IN_TABLE_BODY;
				return $this->step( self::REPROCESS_CURRENT_NODE );

			/*
			 * > A start tag whose tag name is "table"
			 *
			 * This tag in the IN TABLE insertion mode is a parse error.
			 */
			case '+TABLE':
				if ( ! $this->state->stack_of_open_elements->has_element_in_table_scope( 'TABLE' ) ) {
					return $this->step();
				}

				$this->state->stack_of_open_elements->pop_until( 'TABLE' );
				$this->reset_insertion_mode_appropriately();
				return $this->step( self::REPROCESS_CURRENT_NODE );

			/*
			 * > An end tag whose tag name is "table"
			 */
			case '-TABLE':
				if ( ! $this->state->stack_of_open_elements->has_element_in_table_scope( 'TABLE' ) ) {
					// @todo Indicate a parse error once it's possible.
					return $this->step();
				}

				$this->state->stack_of_open_elements->pop_until( 'TABLE' );
				$this->reset_insertion_mode_appropriately();
				return true;

			/*
			 * > An end tag whose tag name is one of: "body", "caption", "col", "colgroup", "html", "tbody", "td", "tfoot", "th", "thead", "tr"
			 */
			case '-BODY':
			case '-CAPTION':
			case '-COL':
			case '-COLGROUP':
			case '-HTML':
			case '-TBODY':
			case '-TD':
			case '-TFOOT':
			case '-TH':
			case '-THEAD':
			case '-TR':
				// Parse error: ignore the token.
				return $this->step();

			/*
			 * > A start tag whose tag name is one of: "style", "script", "template"
			 * > An end tag whose tag name is "template"
			 */
			case '+STYLE':
			case '+SCRIPT':
			case '+TEMPLATE':
			case '-TEMPLATE':
				/*
				 * > Process the token using the rules for the "in head" insertion mode.
				 */
				return $this->step_in_head();

			/*
			 * > A start tag whose tag name is "input"
			 *
			 * > If the token does not have an attribute with the name "type", or if it does, but
			 * > that attribute's value is not an ASCII case-insensitive match for the string
			 * > "hidden", then: act as described in the "anything else" entry below.
			 */
			case '+INPUT':
				$type_attribute = $this->get_attribute( 'type' );
				if ( ! is_string( $type_attribute ) || 'hidden' !== strtolower( $type_attribute ) ) {
					goto anything_else;
				}
				// @todo Indicate a parse error once it's possible.
				$this->insert_html_element( $this->state->current_token );
				return true;

			/*
			 * > A start tag whose tag name is "form"
			 *
			 * This tag in the IN TABLE insertion mode is a parse error.
			 */
			case '+FORM':
				if (
					$this->state->stack_of_open_elements->has_element_in_scope( 'TEMPLATE' ) ||
					isset( $this->state->form_element )
				) {
					return $this->step();
				}

				// This FORM is special because it immediately closes and cannot have other children.
				$this->insert_html_element( $this->state->current_token );
				$this->state->form_element = $this->state->current_token;
				$this->state->stack_of_open_elements->pop();
				return true;
		}

		/*
		 * > Anything else
		 * > Parse error. Enable foster parenting, process the token using the rules for the
		 * > "in body" insertion mode, and then disable foster parenting.
		 *
		 * @todo Indicate a parse error once it's possible.
		 */
		anything_else:
		$this->bail( 'Foster parenting is not supported.' );
	}

	/**
	 * Parses next element in the 'in table text' insertion mode.
	 *
	 * This internal function performs the 'in table text' insertion mode
	 * logic for the generalized WP_HTML_Processor::step() function.
	 *
	 * @since 6.7.0 Stub implementation.
	 *
	 * @throws WP_HTML_Unsupported_Exception When encountering unsupported HTML input.
	 *
	 * @see https://html.spec.whatwg.org/#parsing-main-intabletext
	 * @see WP_HTML_Processor::step
	 *
	 * @return bool Whether an element was found.
	 */
	private function step_in_table_text(): bool {
		$this->bail( 'No support for parsing in the ' . WP_HTML_Processor_State::INSERTION_MODE_IN_TABLE_TEXT . ' state.' );
	}

	/**
	 * Parses next element in the 'in caption' insertion mode.
	 *
	 * This internal function performs the 'in caption' insertion mode
	 * logic for the generalized WP_HTML_Processor::step() function.
	 *
	 * @since 6.7.0
	 *
	 * @throws WP_HTML_Unsupported_Exception When encountering unsupported HTML input.
	 *
	 * @see https://html.spec.whatwg.org/#parsing-main-incaption
	 * @see WP_HTML_Processor::step
	 *
	 * @return bool Whether an element was found.
	 */
	private function step_in_caption(): bool {
		$tag_name = $this->get_tag();
		$op_sigil = $this->is_tag_closer() ? '-' : '+';
		$op       = "{$op_sigil}{$tag_name}";

		switch ( $op ) {
			/*
			 * > An end tag whose tag name is "caption"
			 * > A start tag whose tag name is one of: "caption", "col", "colgroup", "tbody", "td", "tfoot", "th", "thead", "tr"
			 * > An end tag whose tag name is "table"
			 *
			 * These tag handling rules are identical except for the final instruction.
			 * Handle them in a single block.
			 */
			case '-CAPTION':
			case '+CAPTION':
			case '+COL':
			case '+COLGROUP':
			case '+TBODY':
			case '+TD':
			case '+TFOOT':
			case '+TH':
			case '+THEAD':
			case '+TR':
			case '-TABLE':
				if ( ! $this->state->stack_of_open_elements->has_element_in_table_scope( 'CAPTION' ) ) {
					// Parse error: ignore the token.
					return $this->step();
				}

				$this->generate_implied_end_tags();
				if ( ! $this->state->stack_of_open_elements->current_node_is( 'CAPTION' ) ) {
					// @todo Indicate a parse error once it's possible.
				}

				$this->state->stack_of_open_elements->pop_until( 'CAPTION' );
				$this->state->active_formatting_elements->clear_up_to_last_marker();
				$this->state->insertion_mode = WP_HTML_Processor_State::INSERTION_MODE_IN_TABLE;

				// If this is not a CAPTION end tag, the token should be reprocessed.
				if ( '-CAPTION' === $op ) {
					return true;
				}
				return $this->step( self::REPROCESS_CURRENT_NODE );

			/**
			 * > An end tag whose tag name is one of: "body", "col", "colgroup", "html", "tbody", "td", "tfoot", "th", "thead", "tr"
			 */
			case '-BODY':
			case '-COL':
			case '-COLGROUP':
			case '-HTML':
			case '-TBODY':
			case '-TD':
			case '-TFOOT':
			case '-TH':
			case '-THEAD':
			case '-TR':
				// Parse error: ignore the token.
				return $this->step();
		}

		/**
		 * > Anything else
		 * >   Process the token using the rules for the "in body" insertion mode.
		 */
		return $this->step_in_body();
	}

	/**
	 * Parses next element in the 'in column group' insertion mode.
	 *
	 * This internal function performs the 'in column group' insertion mode
	 * logic for the generalized WP_HTML_Processor::step() function.
	 *
	 * @since 6.7.0
	 *
	 * @throws WP_HTML_Unsupported_Exception When encountering unsupported HTML input.
	 *
	 * @see https://html.spec.whatwg.org/#parsing-main-incolgroup
	 * @see WP_HTML_Processor::step
	 *
	 * @return bool Whether an element was found.
	 */
	private function step_in_column_group(): bool {
		$token_name = $this->get_token_name();
		$token_type = $this->get_token_type();
		$op_sigil   = '#tag' === $token_type ? ( parent::is_tag_closer() ? '-' : '+' ) : '';
		$op         = "{$op_sigil}{$token_name}";

		switch ( $op ) {
			/*
			 * > A character token that is one of U+0009 CHARACTER TABULATION, U+000A LINE FEED (LF),
			 * > U+000C FORM FEED (FF), U+000D CARRIAGE RETURN (CR), or U+0020 SPACE
			 */
			case '#text':
				if ( parent::TEXT_IS_WHITESPACE === $this->text_node_classification ) {
					// Insert the character.
					$this->insert_html_element( $this->state->current_token );
					return true;
				}

				goto in_column_group_anything_else;
				break;

			/*
			 * > A comment token
			 */
			case '#comment':
			case '#funky-comment':
			case '#presumptuous-tag':
				$this->insert_html_element( $this->state->current_token );
				return true;

			/*
			 * > A DOCTYPE token
			 */
			case 'html':
				// @todo Indicate a parse error once it's possible.
				return $this->step();

			/*
			 * > A start tag whose tag name is "html"
			 */
			case '+HTML':
				return $this->step_in_body();

			/*
			 * > A start tag whose tag name is "col"
			 */
			case '+COL':
				$this->insert_html_element( $this->state->current_token );
				$this->state->stack_of_open_elements->pop();
				return true;

			/*
			 * > An end tag whose tag name is "colgroup"
			 */
			case '-COLGROUP':
				if ( ! $this->state->stack_of_open_elements->current_node_is( 'COLGROUP' ) ) {
					// @todo Indicate a parse error once it's possible.
					return $this->step();
				}
				$this->state->stack_of_open_elements->pop();
				$this->state->insertion_mode = WP_HTML_Processor_State::INSERTION_MODE_IN_TABLE;
				return true;

			/*
			 * > An end tag whose tag name is "col"
			 */
			case '-COL':
				// Parse error: ignore the token.
				return $this->step();

			/*
			 * > A start tag whose tag name is "template"
			 * > An end tag whose tag name is "template"
			 */
			case '+TEMPLATE':
			case '-TEMPLATE':
				return $this->step_in_head();
		}

		in_column_group_anything_else:
		/*
		 * > Anything else
		 */
		if ( ! $this->state->stack_of_open_elements->current_node_is( 'COLGROUP' ) ) {
			// @todo Indicate a parse error once it's possible.
			return $this->step();
		}
		$this->state->stack_of_open_elements->pop();
		$this->state->insertion_mode = WP_HTML_Processor_State::INSERTION_MODE_IN_TABLE;
		return $this->step( self::REPROCESS_CURRENT_NODE );
	}

	/**
	 * Parses next element in the 'in table body' insertion mode.
	 *
	 * This internal function performs the 'in table body' insertion mode
	 * logic for the generalized WP_HTML_Processor::step() function.
	 *
	 * @since 6.7.0
	 *
	 * @throws WP_HTML_Unsupported_Exception When encountering unsupported HTML input.
	 *
	 * @see https://html.spec.whatwg.org/#parsing-main-intbody
	 * @see WP_HTML_Processor::step
	 *
	 * @return bool Whether an element was found.
	 */
	private function step_in_table_body(): bool {
		$tag_name = $this->get_tag();
		$op_sigil = $this->is_tag_closer() ? '-' : '+';
		$op       = "{$op_sigil}{$tag_name}";

		switch ( $op ) {
			/*
			 * > A start tag whose tag name is "tr"
			 */
			case '+TR':
				$this->state->stack_of_open_elements->clear_to_table_body_context();
				$this->insert_html_element( $this->state->current_token );
				$this->state->insertion_mode = WP_HTML_Processor_State::INSERTION_MODE_IN_ROW;
				return true;

			/*
			 * > A start tag whose tag name is one of: "th", "td"
			 */
			case '+TH':
			case '+TD':
				// @todo Indicate a parse error once it's possible.
				$this->state->stack_of_open_elements->clear_to_table_body_context();
				$this->insert_virtual_node( 'TR' );
				$this->state->insertion_mode = WP_HTML_Processor_State::INSERTION_MODE_IN_ROW;
				return $this->step( self::REPROCESS_CURRENT_NODE );

			/*
			 * > An end tag whose tag name is one of: "tbody", "tfoot", "thead"
			 */
			case '-TBODY':
			case '-TFOOT':
			case '-THEAD':
				if ( ! $this->state->stack_of_open_elements->has_element_in_table_scope( $tag_name ) ) {
					// Parse error: ignore the token.
					return $this->step();
				}

				$this->state->stack_of_open_elements->clear_to_table_body_context();
				$this->state->stack_of_open_elements->pop();
				$this->state->insertion_mode = WP_HTML_Processor_State::INSERTION_MODE_IN_TABLE;
				return true;

			/*
			 * > A start tag whose tag name is one of: "caption", "col", "colgroup", "tbody", "tfoot", "thead"
			 * > An end tag whose tag name is "table"
			 */
			case '+CAPTION':
			case '+COL':
			case '+COLGROUP':
			case '+TBODY':
			case '+TFOOT':
			case '+THEAD':
			case '-TABLE':
				if (
					! $this->state->stack_of_open_elements->has_element_in_table_scope( 'TBODY' ) &&
					! $this->state->stack_of_open_elements->has_element_in_table_scope( 'THEAD' ) &&
					! $this->state->stack_of_open_elements->has_element_in_table_scope( 'TFOOT' )
				) {
					// Parse error: ignore the token.
					return $this->step();
				}
				$this->state->stack_of_open_elements->clear_to_table_body_context();
				$this->state->stack_of_open_elements->pop();
				$this->state->insertion_mode = WP_HTML_Processor_State::INSERTION_MODE_IN_TABLE;
				return $this->step( self::REPROCESS_CURRENT_NODE );

			/*
			 * > An end tag whose tag name is one of: "body", "caption", "col", "colgroup", "html", "td", "th", "tr"
			 */
			case '-BODY':
			case '-CAPTION':
			case '-COL':
			case '-COLGROUP':
			case '-HTML':
			case '-TD':
			case '-TH':
			case '-TR':
				// Parse error: ignore the token.
				return $this->step();
		}

		/*
		 * > Anything else
		 * > Process the token using the rules for the "in table" insertion mode.
		 */
		return $this->step_in_table();
	}

	/**
	 * Parses next element in the 'in row' insertion mode.
	 *
	 * This internal function performs the 'in row' insertion mode
	 * logic for the generalized WP_HTML_Processor::step() function.
	 *
	 * @since 6.7.0
	 *
	 * @throws WP_HTML_Unsupported_Exception When encountering unsupported HTML input.
	 *
	 * @see https://html.spec.whatwg.org/#parsing-main-intr
	 * @see WP_HTML_Processor::step
	 *
	 * @return bool Whether an element was found.
	 */
	private function step_in_row(): bool {
		$tag_name = $this->get_tag();
		$op_sigil = $this->is_tag_closer() ? '-' : '+';
		$op       = "{$op_sigil}{$tag_name}";

		switch ( $op ) {
			/*
			 * > A start tag whose tag name is one of: "th", "td"
			 */
			case '+TH':
			case '+TD':
				$this->state->stack_of_open_elements->clear_to_table_row_context();
				$this->insert_html_element( $this->state->current_token );
				$this->state->insertion_mode = WP_HTML_Processor_State::INSERTION_MODE_IN_CELL;
				$this->state->active_formatting_elements->insert_marker();
				return true;

			/*
			 * > An end tag whose tag name is "tr"
			 */
			case '-TR':
				if ( ! $this->state->stack_of_open_elements->has_element_in_table_scope( 'TR' ) ) {
					// Parse error: ignore the token.
					return $this->step();
				}

				$this->state->stack_of_open_elements->clear_to_table_row_context();
				$this->state->stack_of_open_elements->pop();
				$this->state->insertion_mode = WP_HTML_Processor_State::INSERTION_MODE_IN_TABLE_BODY;
				return true;

			/*
			 * > A start tag whose tag name is one of: "caption", "col", "colgroup", "tbody", "tfoot", "thead", "tr"
			 * > An end tag whose tag name is "table"
			 */
			case '+CAPTION':
			case '+COL':
			case '+COLGROUP':
			case '+TBODY':
			case '+TFOOT':
			case '+THEAD':
			case '+TR':
			case '-TABLE':
				if ( ! $this->state->stack_of_open_elements->has_element_in_table_scope( 'TR' ) ) {
					// Parse error: ignore the token.
					return $this->step();
				}

				$this->state->stack_of_open_elements->clear_to_table_row_context();
				$this->state->stack_of_open_elements->pop();
				$this->state->insertion_mode = WP_HTML_Processor_State::INSERTION_MODE_IN_TABLE_BODY;
				return $this->step( self::REPROCESS_CURRENT_NODE );

			/*
			 * > An end tag whose tag name is one of: "tbody", "tfoot", "thead"
			 */
			case '-TBODY':
			case '-TFOOT':
			case '-THEAD':
				if ( ! $this->state->stack_of_open_elements->has_element_in_table_scope( $tag_name ) ) {
					// Parse error: ignore the token.
					return $this->step();
				}

				if ( ! $this->state->stack_of_open_elements->has_element_in_table_scope( 'TR' ) ) {
					// Ignore the token.
					return $this->step();
				}

				$this->state->stack_of_open_elements->clear_to_table_row_context();
				$this->state->stack_of_open_elements->pop();
				$this->state->insertion_mode = WP_HTML_Processor_State::INSERTION_MODE_IN_TABLE_BODY;
				return $this->step( self::REPROCESS_CURRENT_NODE );

			/*
			 * > An end tag whose tag name is one of: "body", "caption", "col", "colgroup", "html", "td", "th"
			 */
			case '-BODY':
			case '-CAPTION':
			case '-COL':
			case '-COLGROUP':
			case '-HTML':
			case '-TD':
			case '-TH':
				// Parse error: ignore the token.
				return $this->step();
		}

		/*
		 * > Anything else
		 * >   Process the token using the rules for the "in table" insertion mode.
		 */
		return $this->step_in_table();
	}

	/**
	 * Parses next element in the 'in cell' insertion mode.
	 *
	 * This internal function performs the 'in cell' insertion mode
	 * logic for the generalized WP_HTML_Processor::step() function.
	 *
	 * @since 6.7.0
	 *
	 * @throws WP_HTML_Unsupported_Exception When encountering unsupported HTML input.
	 *
	 * @see https://html.spec.whatwg.org/#parsing-main-intd
	 * @see WP_HTML_Processor::step
	 *
	 * @return bool Whether an element was found.
	 */
	private function step_in_cell(): bool {
		$tag_name = $this->get_tag();
		$op_sigil = $this->is_tag_closer() ? '-' : '+';
		$op       = "{$op_sigil}{$tag_name}";

		switch ( $op ) {
			/*
			 * > An end tag whose tag name is one of: "td", "th"
			 */
			case '-TD':
			case '-TH':
				if ( ! $this->state->stack_of_open_elements->has_element_in_table_scope( $tag_name ) ) {
					// Parse error: ignore the token.
					return $this->step();
				}

				$this->generate_implied_end_tags();

				/*
				 * @todo This needs to check if the current node is an HTML element, meaning that
				 *       when SVG and MathML support is added, this needs to differentiate between an
				 *       HTML element of the given name, such as `<center>`, and a foreign element of
				 *       the same given name.
				 */
				if ( ! $this->state->stack_of_open_elements->current_node_is( $tag_name ) ) {
					// @todo Indicate a parse error once it's possible.
				}

				$this->state->stack_of_open_elements->pop_until( $tag_name );
				$this->state->active_formatting_elements->clear_up_to_last_marker();
				$this->state->insertion_mode = WP_HTML_Processor_State::INSERTION_MODE_IN_ROW;
				return true;

			/*
			 * > A start tag whose tag name is one of: "caption", "col", "colgroup", "tbody", "td",
			 * > "tfoot", "th", "thead", "tr"
			 */
			case '+CAPTION':
			case '+COL':
			case '+COLGROUP':
			case '+TBODY':
			case '+TD':
			case '+TFOOT':
			case '+TH':
			case '+THEAD':
			case '+TR':
				/*
				 * > Assert: The stack of open elements has a td or th element in table scope.
				 *
				 * Nothing to do here, except to verify in tests that this never appears.
				 */

				$this->close_cell();
				return $this->step( self::REPROCESS_CURRENT_NODE );

			/*
			 * > An end tag whose tag name is one of: "body", "caption", "col", "colgroup", "html"
			 */
			case '-BODY':
			case '-CAPTION':
			case '-COL':
			case '-COLGROUP':
			case '-HTML':
				// Parse error: ignore the token.
				return $this->step();

			/*
			 * > An end tag whose tag name is one of: "table", "tbody", "tfoot", "thead", "tr"
			 */
			case '-TABLE':
			case '-TBODY':
			case '-TFOOT':
			case '-THEAD':
			case '-TR':
				if ( ! $this->state->stack_of_open_elements->has_element_in_table_scope( $tag_name ) ) {
					// Parse error: ignore the token.
					return $this->step();
				}
				$this->close_cell();
				return $this->step( self::REPROCESS_CURRENT_NODE );
		}

		/*
		 * > Anything else
		 * >   Process the token using the rules for the "in body" insertion mode.
		 */
		return $this->step_in_body();
	}

	/**
	 * Parses next element in the 'in select' insertion mode.
	 *
	 * This internal function performs the 'in select' insertion mode
	 * logic for the generalized WP_HTML_Processor::step() function.
	 *
	 * @since 6.7.0
	 *
	 * @throws WP_HTML_Unsupported_Exception When encountering unsupported HTML input.
	 *
	 * @see https://html.spec.whatwg.org/multipage/parsing.html#parsing-main-inselect
	 * @see WP_HTML_Processor::step
	 *
	 * @return bool Whether an element was found.
	 */
	private function step_in_select(): bool {
		$token_name = $this->get_token_name();
		$token_type = $this->get_token_type();
		$op_sigil   = '#tag' === $token_type ? ( parent::is_tag_closer() ? '-' : '+' ) : '';
		$op         = "{$op_sigil}{$token_name}";

		switch ( $op ) {
			/*
			 * > Any other character token
			 */
			case '#text':
				/*
				 * > A character token that is U+0000 NULL
				 *
				 * If a text node only comprises null bytes then it should be
				 * entirely ignored and should not return to calling code.
				 */
				if ( parent::TEXT_IS_NULL_SEQUENCE === $this->text_node_classification ) {
					// Parse error: ignore the token.
					return $this->step();
				}

				$this->insert_html_element( $this->state->current_token );
				return true;

			/*
			 * > A comment token
			 */
			case '#comment':
			case '#funky-comment':
			case '#presumptuous-tag':
				$this->insert_html_element( $this->state->current_token );
				return true;

			/*
			 * > A DOCTYPE token
			 */
			case 'html':
				// Parse error: ignore the token.
				return $this->step();

			/*
			 * > A start tag whose tag name is "html"
			 */
			case '+HTML':
				return $this->step_in_body();

			/*
			 * > A start tag whose tag name is "option"
			 */
			case '+OPTION':
				if ( $this->state->stack_of_open_elements->current_node_is( 'OPTION' ) ) {
					$this->state->stack_of_open_elements->pop();
				}
				$this->insert_html_element( $this->state->current_token );
				return true;

			/*
			 * > A start tag whose tag name is "optgroup"
			 * > A start tag whose tag name is "hr"
			 *
			 * These rules are identical except for the treatment of the self-closing flag and
			 * the subsequent pop of the HR void element, all of which is handled elsewhere in the processor.
			 */
			case '+OPTGROUP':
			case '+HR':
				if ( $this->state->stack_of_open_elements->current_node_is( 'OPTION' ) ) {
					$this->state->stack_of_open_elements->pop();
				}

				if ( $this->state->stack_of_open_elements->current_node_is( 'OPTGROUP' ) ) {
					$this->state->stack_of_open_elements->pop();
				}

				$this->insert_html_element( $this->state->current_token );
				return true;

			/*
			 * > An end tag whose tag name is "optgroup"
			 */
			case '-OPTGROUP':
				$current_node = $this->state->stack_of_open_elements->current_node();
				if ( $current_node && 'OPTION' === $current_node->node_name ) {
					foreach ( $this->state->stack_of_open_elements->walk_up( $current_node ) as $parent ) {
						break;
					}
					if ( $parent && 'OPTGROUP' === $parent->node_name ) {
						$this->state->stack_of_open_elements->pop();
					}
				}

				if ( $this->state->stack_of_open_elements->current_node_is( 'OPTGROUP' ) ) {
					$this->state->stack_of_open_elements->pop();
					return true;
				}

				// Parse error: ignore the token.
				return $this->step();

			/*
			 * > An end tag whose tag name is "option"
			 */
			case '-OPTION':
				if ( $this->state->stack_of_open_elements->current_node_is( 'OPTION' ) ) {
					$this->state->stack_of_open_elements->pop();
					return true;
				}

				// Parse error: ignore the token.
				return $this->step();

			/*
			 * > An end tag whose tag name is "select"
			 * > A start tag whose tag name is "select"
			 *
			 * > It just gets treated like an end tag.
			 */
			case '-SELECT':
			case '+SELECT':
				if ( ! $this->state->stack_of_open_elements->has_element_in_select_scope( 'SELECT' ) ) {
					// Parse error: ignore the token.
					return $this->step();
				}
				$this->state->stack_of_open_elements->pop_until( 'SELECT' );
				$this->reset_insertion_mode_appropriately();
				return true;

			/*
			 * > A start tag whose tag name is one of: "input", "keygen", "textarea"
			 *
			 * All three of these tags are considered a parse error when found in this insertion mode.
			 */
			case '+INPUT':
			case '+KEYGEN':
			case '+TEXTAREA':
				if ( ! $this->state->stack_of_open_elements->has_element_in_select_scope( 'SELECT' ) ) {
					// Ignore the token.
					return $this->step();
				}
				$this->state->stack_of_open_elements->pop_until( 'SELECT' );
				$this->reset_insertion_mode_appropriately();
				return $this->step( self::REPROCESS_CURRENT_NODE );

			/*
			 * > A start tag whose tag name is one of: "script", "template"
			 * > An end tag whose tag name is "template"
			 */
			case '+SCRIPT':
			case '+TEMPLATE':
			case '-TEMPLATE':
				return $this->step_in_head();
		}

		/*
		 * > Anything else
		 * >   Parse error: ignore the token.
		 */
		return $this->step();
	}

	/**
	 * Parses next element in the 'in select in table' insertion mode.
	 *
	 * This internal function performs the 'in select in table' insertion mode
	 * logic for the generalized WP_HTML_Processor::step() function.
	 *
	 * @since 6.7.0
	 *
	 * @throws WP_HTML_Unsupported_Exception When encountering unsupported HTML input.
	 *
	 * @see https://html.spec.whatwg.org/#parsing-main-inselectintable
	 * @see WP_HTML_Processor::step
	 *
	 * @return bool Whether an element was found.
	 */
	private function step_in_select_in_table(): bool {
		$token_name = $this->get_token_name();
		$token_type = $this->get_token_type();
		$op_sigil   = '#tag' === $token_type ? ( parent::is_tag_closer() ? '-' : '+' ) : '';
		$op         = "{$op_sigil}{$token_name}";

		switch ( $op ) {
			/*
			 * > A start tag whose tag name is one of: "caption", "table", "tbody", "tfoot", "thead", "tr", "td", "th"
			 */
			case '+CAPTION':
			case '+TABLE':
			case '+TBODY':
			case '+TFOOT':
			case '+THEAD':
			case '+TR':
			case '+TD':
			case '+TH':
				// @todo Indicate a parse error once it's possible.
				$this->state->stack_of_open_elements->pop_until( 'SELECT' );
				$this->reset_insertion_mode_appropriately();
				return $this->step( self::REPROCESS_CURRENT_NODE );

			/*
			 * > An end tag whose tag name is one of: "caption", "table", "tbody", "tfoot", "thead", "tr", "td", "th"
			 */
			case '-CAPTION':
			case '-TABLE':
			case '-TBODY':
			case '-TFOOT':
			case '-THEAD':
			case '-TR':
			case '-TD':
			case '-TH':
				// @todo Indicate a parse error once it's possible.
				if ( ! $this->state->stack_of_open_elements->has_element_in_table_scope( $token_name ) ) {
					return $this->step();
				}
				$this->state->stack_of_open_elements->pop_until( 'SELECT' );
				$this->reset_insertion_mode_appropriately();
				return $this->step( self::REPROCESS_CURRENT_NODE );
		}

		/*
		 * > Anything else
		 */
		return $this->step_in_select();
	}

	/**
	 * Parses next element in the 'in template' insertion mode.
	 *
	 * This internal function performs the 'in template' insertion mode
	 * logic for the generalized WP_HTML_Processor::step() function.
	 *
	 * @since 6.7.0 Stub implementation.
	 *
	 * @throws WP_HTML_Unsupported_Exception When encountering unsupported HTML input.
	 *
	 * @see https://html.spec.whatwg.org/#parsing-main-intemplate
	 * @see WP_HTML_Processor::step
	 *
	 * @return bool Whether an element was found.
	 */
	private function step_in_template(): bool {
		$token_name = $this->get_token_name();
		$token_type = $this->get_token_type();
		$is_closer  = $this->is_tag_closer();
		$op_sigil   = '#tag' === $token_type ? ( $is_closer ? '-' : '+' ) : '';
		$op         = "{$op_sigil}{$token_name}";

		switch ( $op ) {
			/*
			 * > A character token
			 * > A comment token
			 * > A DOCTYPE token
			 */
			case '#text':
			case '#comment':
			case '#funky-comment':
			case '#presumptuous-tag':
			case 'html':
				return $this->step_in_body();

			/*
			 * > A start tag whose tag name is one of: "base", "basefont", "bgsound", "link",
			 * > "meta", "noframes", "script", "style", "template", "title"
			 * > An end tag whose tag name is "template"
			 */
			case '+BASE':
			case '+BASEFONT':
			case '+BGSOUND':
			case '+LINK':
			case '+META':
			case '+NOFRAMES':
			case '+SCRIPT':
			case '+STYLE':
			case '+TEMPLATE':
			case '+TITLE':
			case '-TEMPLATE':
				return $this->step_in_head();

			/*
			 * > A start tag whose tag name is one of: "caption", "colgroup", "tbody", "tfoot", "thead"
			 */
			case '+CAPTION':
			case '+COLGROUP':
			case '+TBODY':
			case '+TFOOT':
			case '+THEAD':
				array_pop( $this->state->stack_of_template_insertion_modes );
				$this->state->stack_of_template_insertion_modes[] = WP_HTML_Processor_State::INSERTION_MODE_IN_TABLE;
				$this->state->insertion_mode                      = WP_HTML_Processor_State::INSERTION_MODE_IN_TABLE;
				return $this->step( self::REPROCESS_CURRENT_NODE );

			/*
			 * > A start tag whose tag name is "col"
			 */
			case '+COL':
				array_pop( $this->state->stack_of_template_insertion_modes );
				$this->state->stack_of_template_insertion_modes[] = WP_HTML_Processor_State::INSERTION_MODE_IN_COLUMN_GROUP;
				$this->state->insertion_mode                      = WP_HTML_Processor_State::INSERTION_MODE_IN_COLUMN_GROUP;
				return $this->step( self::REPROCESS_CURRENT_NODE );

			/*
			 * > A start tag whose tag name is "tr"
			 */
			case '+TR':
				array_pop( $this->state->stack_of_template_insertion_modes );
				$this->state->stack_of_template_insertion_modes[] = WP_HTML_Processor_State::INSERTION_MODE_IN_TABLE_BODY;
				$this->state->insertion_mode                      = WP_HTML_Processor_State::INSERTION_MODE_IN_TABLE_BODY;
				return $this->step( self::REPROCESS_CURRENT_NODE );

			/*
			 * > A start tag whose tag name is one of: "td", "th"
			 */
			case '+TD':
			case '+TH':
				array_pop( $this->state->stack_of_template_insertion_modes );
				$this->state->stack_of_template_insertion_modes[] = WP_HTML_Processor_State::INSERTION_MODE_IN_ROW;
				$this->state->insertion_mode                      = WP_HTML_Processor_State::INSERTION_MODE_IN_ROW;
				return $this->step( self::REPROCESS_CURRENT_NODE );
		}

		/*
		 * > Any other start tag
		 */
		if ( ! $is_closer ) {
			array_pop( $this->state->stack_of_template_insertion_modes );
			$this->state->stack_of_template_insertion_modes[] = WP_HTML_Processor_State::INSERTION_MODE_IN_BODY;
			$this->state->insertion_mode                      = WP_HTML_Processor_State::INSERTION_MODE_IN_BODY;
			return $this->step( self::REPROCESS_CURRENT_NODE );
		}

		/*
		 * > Any other end tag
		 */
		if ( $is_closer ) {
			// Parse error: ignore the token.
			return $this->step();
		}

		/*
		 * > An end-of-file token
		 */
		if ( ! $this->state->stack_of_open_elements->contains( 'TEMPLATE' ) ) {
			// Stop parsing.
			return false;
		}

		// @todo Indicate a parse error once it's possible.
		$this->state->stack_of_open_elements->pop_until( 'TEMPLATE' );
		$this->state->active_formatting_elements->clear_up_to_last_marker();
		array_pop( $this->state->stack_of_template_insertion_modes );
		$this->reset_insertion_mode_appropriately();
		return $this->step( self::REPROCESS_CURRENT_NODE );
	}

	/**
	 * Parses next element in the 'after body' insertion mode.
	 *
	 * This internal function performs the 'after body' insertion mode
	 * logic for the generalized WP_HTML_Processor::step() function.
	 *
	 * @since 6.7.0 Stub implementation.
	 *
	 * @throws WP_HTML_Unsupported_Exception When encountering unsupported HTML input.
	 *
	 * @see https://html.spec.whatwg.org/#parsing-main-afterbody
	 * @see WP_HTML_Processor::step
	 *
	 * @return bool Whether an element was found.
	 */
	private function step_after_body(): bool {
		$tag_name   = $this->get_token_name();
		$token_type = $this->get_token_type();
		$op_sigil   = '#tag' === $token_type ? ( $this->is_tag_closer() ? '-' : '+' ) : '';
		$op         = "{$op_sigil}{$tag_name}";

		switch ( $op ) {
			/*
			 * > A character token that is one of U+0009 CHARACTER TABULATION, U+000A LINE FEED (LF),
			 * >   U+000C FORM FEED (FF), U+000D CARRIAGE RETURN (CR), or U+0020 SPACE
			 *
			 * > Process the token using the rules for the "in body" insertion mode.
			 */
			case '#text':
				if ( parent::TEXT_IS_WHITESPACE === $this->text_node_classification ) {
					return $this->step_in_body();
				}
				goto after_body_anything_else;
				break;

			/*
			 * > A comment token
			 */
			case '#comment':
			case '#funky-comment':
			case '#presumptuous-tag':
				$this->bail( 'Content outside of BODY is unsupported.' );
				break;

			/*
			 * > A DOCTYPE token
			 */
			case 'html':
				// Parse error: ignore the token.
				return $this->step();

			/*
			 * > A start tag whose tag name is "html"
			 */
			case '+HTML':
				return $this->step_in_body();

			/*
			 * > An end tag whose tag name is "html"
			 *
			 * > If the parser was created as part of the HTML fragment parsing algorithm,
			 * > this is a parse error; ignore the token. (fragment case)
			 * >
			 * > Otherwise, switch the insertion mode to "after after body".
			 */
			case '-HTML':
				if ( isset( $this->context_node ) ) {
					return $this->step();
				}

				$this->state->insertion_mode = WP_HTML_Processor_State::INSERTION_MODE_AFTER_AFTER_BODY;
				return true;
		}

		/*
		 * > Parse error. Switch the insertion mode to "in body" and reprocess the token.
		 */
		after_body_anything_else:
		$this->state->insertion_mode = WP_HTML_Processor_State::INSERTION_MODE_IN_BODY;
		return $this->step( self::REPROCESS_CURRENT_NODE );
	}

	/**
	 * Parses next element in the 'in frameset' insertion mode.
	 *
	 * This internal function performs the 'in frameset' insertion mode
	 * logic for the generalized WP_HTML_Processor::step() function.
	 *
	 * @since 6.7.0 Stub implementation.
	 *
	 * @throws WP_HTML_Unsupported_Exception When encountering unsupported HTML input.
	 *
	 * @see https://html.spec.whatwg.org/#parsing-main-inframeset
	 * @see WP_HTML_Processor::step
	 *
	 * @return bool Whether an element was found.
	 */
	private function step_in_frameset(): bool {
		$tag_name   = $this->get_token_name();
		$token_type = $this->get_token_type();
		$op_sigil   = '#tag' === $token_type ? ( $this->is_tag_closer() ? '-' : '+' ) : '';
		$op         = "{$op_sigil}{$tag_name}";

		switch ( $op ) {
			/*
			 * > A character token that is one of U+0009 CHARACTER TABULATION, U+000A LINE FEED (LF),
			 * >   U+000C FORM FEED (FF), U+000D CARRIAGE RETURN (CR), or U+0020 SPACE
			 * >
			 * > Insert the character.
			 *
			 * This algorithm effectively strips non-whitespace characters from text and inserts
			 * them under HTML. This is not supported at this time.
			 */
			case '#text':
				if ( parent::TEXT_IS_WHITESPACE === $this->text_node_classification ) {
					return $this->step_in_body();
				}
				$this->bail( 'Non-whitespace characters cannot be handled in frameset.' );
				break;

			/*
			 * > A comment token
			 */
			case '#comment':
			case '#funky-comment':
			case '#presumptuous-tag':
				$this->insert_html_element( $this->state->current_token );
				return true;

			/*
			 * > A DOCTYPE token
			 */
			case 'html':
				// Parse error: ignore the token.
				return $this->step();

			/*
			 * > A start tag whose tag name is "html"
			 */
			case '+HTML':
				return $this->step_in_body();

			/*
			 * > A start tag whose tag name is "frameset"
			 */
			case '+FRAMESET':
				$this->insert_html_element( $this->state->current_token );
				return true;

			/*
			 * > An end tag whose tag name is "frameset"
			 */
			case '-FRAMESET':
				/*
				 * > If the current node is the root html element, then this is a parse error;
				 * > ignore the token. (fragment case)
				 */
				if ( $this->state->stack_of_open_elements->current_node_is( 'HTML' ) ) {
					return $this->step();
				}

				/*
				 * > Otherwise, pop the current node from the stack of open elements.
				 */
				$this->state->stack_of_open_elements->pop();

				/*
				 * > If the parser was not created as part of the HTML fragment parsing algorithm
				 * > (fragment case), and the current node is no longer a frameset element, then
				 * > switch the insertion mode to "after frameset".
				 */
				if ( ! isset( $this->context_node ) && ! $this->state->stack_of_open_elements->current_node_is( 'FRAMESET' ) ) {
					$this->state->insertion_mode = WP_HTML_Processor_State::INSERTION_MODE_AFTER_FRAMESET;
				}

				return true;

			/*
			 * > A start tag whose tag name is "frame"
			 *
			 * > Insert an HTML element for the token. Immediately pop the
			 * > current node off the stack of open elements.
			 * >
			 * > Acknowledge the token's self-closing flag, if it is set.
			 */
			case '+FRAME':
				$this->insert_html_element( $this->state->current_token );
				$this->state->stack_of_open_elements->pop();
				return true;

			/*
			 * > A start tag whose tag name is "noframes"
			 */
			case '+NOFRAMES':
				return $this->step_in_head();
		}

		// Parse error: ignore the token.
		return $this->step();
	}

	/**
	 * Parses next element in the 'after frameset' insertion mode.
	 *
	 * This internal function performs the 'after frameset' insertion mode
	 * logic for the generalized WP_HTML_Processor::step() function.
	 *
	 * @since 6.7.0 Stub implementation.
	 *
	 * @throws WP_HTML_Unsupported_Exception When encountering unsupported HTML input.
	 *
	 * @see https://html.spec.whatwg.org/#parsing-main-afterframeset
	 * @see WP_HTML_Processor::step
	 *
	 * @return bool Whether an element was found.
	 */
	private function step_after_frameset(): bool {
		$tag_name   = $this->get_token_name();
		$token_type = $this->get_token_type();
		$op_sigil   = '#tag' === $token_type ? ( $this->is_tag_closer() ? '-' : '+' ) : '';
		$op         = "{$op_sigil}{$tag_name}";

		switch ( $op ) {
			/*
			 * > A character token that is one of U+0009 CHARACTER TABULATION, U+000A LINE FEED (LF),
			 * >   U+000C FORM FEED (FF), U+000D CARRIAGE RETURN (CR), or U+0020 SPACE
			 * >
			 * > Insert the character.
			 *
			 * This algorithm effectively strips non-whitespace characters from text and inserts
			 * them under HTML. This is not supported at this time.
			 */
			case '#text':
				if ( parent::TEXT_IS_WHITESPACE === $this->text_node_classification ) {
					return $this->step_in_body();
				}
				$this->bail( 'Non-whitespace characters cannot be handled in after frameset' );
				break;

			/*
			 * > A comment token
			 */
			case '#comment':
			case '#funky-comment':
			case '#presumptuous-tag':
				$this->insert_html_element( $this->state->current_token );
				return true;

			/*
			 * > A DOCTYPE token
			 */
			case 'html':
				// Parse error: ignore the token.
				return $this->step();

			/*
			 * > A start tag whose tag name is "html"
			 */
			case '+HTML':
				return $this->step_in_body();

			/*
			 * > An end tag whose tag name is "html"
			 */
			case '-HTML':
				$this->state->insertion_mode = WP_HTML_Processor_State::INSERTION_MODE_AFTER_AFTER_FRAMESET;
				return true;

			/*
			 * > A start tag whose tag name is "noframes"
			 */
			case '+NOFRAMES':
				return $this->step_in_head();
		}

		// Parse error: ignore the token.
		return $this->step();
	}

	/**
	 * Parses next element in the 'after after body' insertion mode.
	 *
	 * This internal function performs the 'after after body' insertion mode
	 * logic for the generalized WP_HTML_Processor::step() function.
	 *
	 * @since 6.7.0 Stub implementation.
	 *
	 * @throws WP_HTML_Unsupported_Exception When encountering unsupported HTML input.
	 *
	 * @see https://html.spec.whatwg.org/#the-after-after-body-insertion-mode
	 * @see WP_HTML_Processor::step
	 *
	 * @return bool Whether an element was found.
	 */
	private function step_after_after_body(): bool {
		$tag_name   = $this->get_token_name();
		$token_type = $this->get_token_type();
		$op_sigil   = '#tag' === $token_type ? ( $this->is_tag_closer() ? '-' : '+' ) : '';
		$op         = "{$op_sigil}{$tag_name}";

		switch ( $op ) {
			/*
			 * > A comment token
			 */
			case '#comment':
			case '#funky-comment':
			case '#presumptuous-tag':
				$this->bail( 'Content outside of HTML is unsupported.' );
				break;

			/*
			 * > A DOCTYPE token
			 * > A start tag whose tag name is "html"
			 *
			 * > Process the token using the rules for the "in body" insertion mode.
			 */
			case 'html':
			case '+HTML':
				return $this->step_in_body();

			/*
			 * > A character token that is one of U+0009 CHARACTER TABULATION, U+000A LINE FEED (LF),
			 * >   U+000C FORM FEED (FF), U+000D CARRIAGE RETURN (CR), or U+0020 SPACE
			 * >
			 * > Process the token using the rules for the "in body" insertion mode.
			 */
			case '#text':
				if ( parent::TEXT_IS_WHITESPACE === $this->text_node_classification ) {
					return $this->step_in_body();
				}
				goto after_after_body_anything_else;
				break;
		}

		/*
		 * > Parse error. Switch the insertion mode to "in body" and reprocess the token.
		 */
		after_after_body_anything_else:
		$this->state->insertion_mode = WP_HTML_Processor_State::INSERTION_MODE_IN_BODY;
		return $this->step( self::REPROCESS_CURRENT_NODE );
	}

	/**
	 * Parses next element in the 'after after frameset' insertion mode.
	 *
	 * This internal function performs the 'after after frameset' insertion mode
	 * logic for the generalized WP_HTML_Processor::step() function.
	 *
	 * @since 6.7.0 Stub implementation.
	 *
	 * @throws WP_HTML_Unsupported_Exception When encountering unsupported HTML input.
	 *
	 * @see https://html.spec.whatwg.org/#the-after-after-frameset-insertion-mode
	 * @see WP_HTML_Processor::step
	 *
	 * @return bool Whether an element was found.
	 */
	private function step_after_after_frameset(): bool {
		$tag_name   = $this->get_token_name();
		$token_type = $this->get_token_type();
		$op_sigil   = '#tag' === $token_type ? ( $this->is_tag_closer() ? '-' : '+' ) : '';
		$op         = "{$op_sigil}{$tag_name}";

		switch ( $op ) {
			/*
			 * > A comment token
			 */
			case '#comment':
			case '#funky-comment':
			case '#presumptuous-tag':
				$this->bail( 'Content outside of HTML is unsupported.' );
				break;

			/*
			 * > A DOCTYPE token
			 * > A start tag whose tag name is "html"
			 *
			 * > Process the token using the rules for the "in body" insertion mode.
			 */
			case 'html':
			case '+HTML':
				return $this->step_in_body();

			/*
			 * > A character token that is one of U+0009 CHARACTER TABULATION, U+000A LINE FEED (LF),
			 * >   U+000C FORM FEED (FF), U+000D CARRIAGE RETURN (CR), or U+0020 SPACE
			 * >
			 * > Process the token using the rules for the "in body" insertion mode.
			 *
			 * This algorithm effectively strips non-whitespace characters from text and inserts
			 * them under HTML. This is not supported at this time.
			 */
			case '#text':
				if ( parent::TEXT_IS_WHITESPACE === $this->text_node_classification ) {
					return $this->step_in_body();
				}
				$this->bail( 'Non-whitespace characters cannot be handled in after after frameset.' );
				break;

			/*
			 * > A start tag whose tag name is "noframes"
			 */
			case '+NOFRAMES':
				return $this->step_in_head();
		}

		// Parse error: ignore the token.
		return $this->step();
	}

	/**
	 * Parses next element in the 'in foreign content' insertion mode.
	 *
	 * This internal function performs the 'in foreign content' insertion mode
	 * logic for the generalized WP_HTML_Processor::step() function.
	 *
	 * @since 6.7.0 Stub implementation.
	 *
	 * @throws WP_HTML_Unsupported_Exception When encountering unsupported HTML input.
	 *
	 * @see https://html.spec.whatwg.org/#parsing-main-inforeign
	 * @see WP_HTML_Processor::step
	 *
	 * @return bool Whether an element was found.
	 */
	private function step_in_foreign_content(): bool {
		$tag_name   = $this->get_token_name();
		$token_type = $this->get_token_type();
		$op_sigil   = '#tag' === $token_type ? ( $this->is_tag_closer() ? '-' : '+' ) : '';
		$op         = "{$op_sigil}{$tag_name}";

		/*
		 * > A start tag whose name is "font", if the token has any attributes named "color", "face", or "size"
		 *
		 * This section drawn out above the switch to more easily incorporate
		 * the additional rules based on the presence of the attributes.
		 */
		if (
			'+FONT' === $op &&
			(
				null !== $this->get_attribute( 'color' ) ||
				null !== $this->get_attribute( 'face' ) ||
				null !== $this->get_attribute( 'size' )
			)
		) {
			$op = '+FONT with attributes';
		}

		switch ( $op ) {
			case '#text':
				/*
				 * > A character token that is U+0000 NULL
				 *
				 * This is handled by `get_modifiable_text()`.
				 */

				/*
				 * Whitespace-only text does not affect the frameset-ok flag.
				 * It is probably inter-element whitespace, but it may also
				 * contain character references which decode only to whitespace.
				 */
				if ( parent::TEXT_IS_GENERIC === $this->text_node_classification ) {
					$this->state->frameset_ok = false;
				}

				$this->insert_foreign_element( $this->state->current_token, false );
				return true;

			/*
			 * CDATA sections are alternate wrappers for text content and therefore
			 * ought to follow the same rules as text nodes.
			 */
			case '#cdata-section':
				/*
				 * NULL bytes and whitespace do not change the frameset-ok flag.
				 */
				$current_token        = $this->bookmarks[ $this->state->current_token->bookmark_name ];
				$cdata_content_start  = $current_token->start + 9;
				$cdata_content_length = $current_token->length - 12;
				if ( strspn( $this->html, "\0 \t\n\f\r", $cdata_content_start, $cdata_content_length ) !== $cdata_content_length ) {
					$this->state->frameset_ok = false;
				}

				$this->insert_foreign_element( $this->state->current_token, false );
				return true;

			/*
			 * > A comment token
			 */
			case '#comment':
			case '#funky-comment':
			case '#presumptuous-tag':
				$this->insert_foreign_element( $this->state->current_token, false );
				return true;

			/*
			 * > A DOCTYPE token
			 */
			case 'html':
				// Parse error: ignore the token.
				return $this->step();

			/*
			 * > A start tag whose tag name is "b", "big", "blockquote", "body", "br", "center",
			 * > "code", "dd", "div", "dl", "dt", "em", "embed", "h1", "h2", "h3", "h4", "h5",
			 * > "h6", "head", "hr", "i", "img", "li", "listing", "menu", "meta", "nobr", "ol",
			 * > "p", "pre", "ruby", "s", "small", "span", "strong", "strike", "sub", "sup",
			 * > "table", "tt", "u", "ul", "var"
			 *
			 * > A start tag whose name is "font", if the token has any attributes named "color", "face", or "size"
			 *
			 * > An end tag whose tag name is "br", "p"
			 *
			 * Closing BR tags are always reported by the Tag Processor as opening tags.
			 */
			case '+B':
			case '+BIG':
			case '+BLOCKQUOTE':
			case '+BODY':
			case '+BR':
			case '+CENTER':
			case '+CODE':
			case '+DD':
			case '+DIV':
			case '+DL':
			case '+DT':
			case '+EM':
			case '+EMBED':
			case '+H1':
			case '+H2':
			case '+H3':
			case '+H4':
			case '+H5':
			case '+H6':
			case '+HEAD':
			case '+HR':
			case '+I':
			case '+IMG':
			case '+LI':
			case '+LISTING':
			case '+MENU':
			case '+META':
			case '+NOBR':
			case '+OL':
			case '+P':
			case '+PRE':
			case '+RUBY':
			case '+S':
			case '+SMALL':
			case '+SPAN':
			case '+STRONG':
			case '+STRIKE':
			case '+SUB':
			case '+SUP':
			case '+TABLE':
			case '+TT':
			case '+U':
			case '+UL':
			case '+VAR':
			case '+FONT with attributes':
			case '-BR':
			case '-P':
				// @todo Indicate a parse error once it's possible.
				foreach ( $this->state->stack_of_open_elements->walk_up() as $current_node ) {
					if (
						'math' === $current_node->integration_node_type ||
						'html' === $current_node->integration_node_type ||
						'html' === $current_node->namespace
					) {
						break;
					}

					$this->state->stack_of_open_elements->pop();
				}
				goto in_foreign_content_process_in_current_insertion_mode;
		}

		/*
		 * > Any other start tag
		 */
		if ( ! $this->is_tag_closer() ) {
			$this->insert_foreign_element( $this->state->current_token, false );

			/*
			 * > If the token has its self-closing flag set, then run
			 * > the appropriate steps from the following list:
			 * >
			 * >   ↪ the token's tag name is "script", and the new current node is in the SVG namespace
			 * >         Acknowledge the token's self-closing flag, and then act as
			 * >         described in the steps for a "script" end tag below.
			 * >
			 * >   ↪ Otherwise
			 * >         Pop the current node off the stack of open elements and
			 * >         acknowledge the token's self-closing flag.
			 *
			 * Since the rules for SCRIPT below indicate to pop the element off of the stack of
			 * open elements, which is the same for the Otherwise condition, there's no need to
			 * separate these checks. The difference comes when a parser operates with the scripting
			 * flag enabled, and executes the script, which this parser does not support.
			 */
			if ( $this->state->current_token->has_self_closing_flag ) {
				$this->state->stack_of_open_elements->pop();
			}
			return true;
		}

		/*
		 * > An end tag whose name is "script", if the current node is an SVG script element.
		 */
		if ( $this->is_tag_closer() && 'SCRIPT' === $this->state->current_token->node_name && 'svg' === $this->state->current_token->namespace ) {
			$this->state->stack_of_open_elements->pop();
			return true;
		}

		/*
		 * > Any other end tag
		 */
		if ( $this->is_tag_closer() ) {
			$node = $this->state->stack_of_open_elements->current_node();
			if ( $tag_name !== $node->node_name ) {
				// @todo Indicate a parse error once it's possible.
			}
			in_foreign_content_end_tag_loop:
			if ( $node === $this->state->stack_of_open_elements->at( 1 ) ) {
				return true;
			}

			/*
			 * > If node's tag name, converted to ASCII lowercase, is the same as the tag name
			 * > of the token, pop elements from the stack of open elements until node has
			 * > been popped from the stack, and then return.
			 */
			if ( 0 === strcasecmp( $node->node_name, $tag_name ) ) {
				foreach ( $this->state->stack_of_open_elements->walk_up() as $item ) {
					$this->state->stack_of_open_elements->pop();
					if ( $node === $item ) {
						return true;
					}
				}
			}

			foreach ( $this->state->stack_of_open_elements->walk_up( $node ) as $item ) {
				$node = $item;
				break;
			}

			if ( 'html' !== $node->namespace ) {
				goto in_foreign_content_end_tag_loop;
			}

			in_foreign_content_process_in_current_insertion_mode:
			switch ( $this->state->insertion_mode ) {
				case WP_HTML_Processor_State::INSERTION_MODE_INITIAL:
					return $this->step_initial();

				case WP_HTML_Processor_State::INSERTION_MODE_BEFORE_HTML:
					return $this->step_before_html();

				case WP_HTML_Processor_State::INSERTION_MODE_BEFORE_HEAD:
					return $this->step_before_head();

				case WP_HTML_Processor_State::INSERTION_MODE_IN_HEAD:
					return $this->step_in_head();

				case WP_HTML_Processor_State::INSERTION_MODE_IN_HEAD_NOSCRIPT:
					return $this->step_in_head_noscript();

				case WP_HTML_Processor_State::INSERTION_MODE_AFTER_HEAD:
					return $this->step_after_head();

				case WP_HTML_Processor_State::INSERTION_MODE_IN_BODY:
					return $this->step_in_body();

				case WP_HTML_Processor_State::INSERTION_MODE_IN_TABLE:
					return $this->step_in_table();

				case WP_HTML_Processor_State::INSERTION_MODE_IN_TABLE_TEXT:
					return $this->step_in_table_text();

				case WP_HTML_Processor_State::INSERTION_MODE_IN_CAPTION:
					return $this->step_in_caption();

				case WP_HTML_Processor_State::INSERTION_MODE_IN_COLUMN_GROUP:
					return $this->step_in_column_group();

				case WP_HTML_Processor_State::INSERTION_MODE_IN_TABLE_BODY:
					return $this->step_in_table_body();

				case WP_HTML_Processor_State::INSERTION_MODE_IN_ROW:
					return $this->step_in_row();

				case WP_HTML_Processor_State::INSERTION_MODE_IN_CELL:
					return $this->step_in_cell();

				case WP_HTML_Processor_State::INSERTION_MODE_IN_SELECT:
					return $this->step_in_select();

				case WP_HTML_Processor_State::INSERTION_MODE_IN_SELECT_IN_TABLE:
					return $this->step_in_select_in_table();

				case WP_HTML_Processor_State::INSERTION_MODE_IN_TEMPLATE:
					return $this->step_in_template();

				case WP_HTML_Processor_State::INSERTION_MODE_AFTER_BODY:
					return $this->step_after_body();

				case WP_HTML_Processor_State::INSERTION_MODE_IN_FRAMESET:
					return $this->step_in_frameset();

				case WP_HTML_Processor_State::INSERTION_MODE_AFTER_FRAMESET:
					return $this->step_after_frameset();

				case WP_HTML_Processor_State::INSERTION_MODE_AFTER_AFTER_BODY:
					return $this->step_after_after_body();

				case WP_HTML_Processor_State::INSERTION_MODE_AFTER_AFTER_FRAMESET:
					return $this->step_after_after_frameset();

				// This should be unreachable but PHP doesn't have total type checking on switch.
				default:
					$this->bail( "Unaware of the requested parsing mode: '{$this->state->insertion_mode}'." );
			}
		}

		$this->bail( 'Should not have been able to reach end of IN FOREIGN CONTENT processing. Check HTML API code.' );
		// This unnecessary return prevents tools from inaccurately reporting type errors.
		return false;
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
	 * Indicates the namespace of the current token, or "html" if there is none.
	 *
	 * @return string One of "html", "math", or "svg".
	 */
	public function get_namespace(): string {
		if ( ! isset( $this->current_element ) ) {
			return parent::get_namespace();
		}

		return $this->current_element->token->namespace;
	}

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
	public function get_tag(): ?string {
		if ( null !== $this->last_error ) {
			return null;
		}

		if ( $this->is_virtual() ) {
			return $this->current_element->token->node_name;
		}

		$tag_name = parent::get_tag();

		/*
		 * > A start tag whose tag name is "image"
		 * > Change the token's tag name to "img" and reprocess it. (Don't ask.)
		 */
		return ( 'IMAGE' === $tag_name && 'html' === $this->get_namespace() )
			? 'IMG'
			: $tag_name;
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
	public function has_self_closing_flag(): bool {
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
	public function get_token_name(): ?string {
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
	public function get_token_type(): ?string {
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
	public function set_attribute( $name, $value ): bool {
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
	public function remove_attribute( $name ): bool {
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
	public function get_attribute_names_with_prefix( $prefix ): ?array {
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
	public function add_class( $class_name ): bool {
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
	public function remove_class( $class_name ): bool {
		return $this->is_virtual() ? false : parent::remove_class( $class_name );
	}

	/**
	 * Returns if a matched tag contains the given ASCII case-insensitive class name.
	 *
	 * @since 6.6.0 Subclassed for the HTML Processor.
	 *
	 * @todo When reconstructing active formatting elements with attributes, find a way
	 *       to indicate if the virtually-reconstructed formatting elements contain the
	 *       wanted class name.
	 *
	 * @param string $wanted_class Look for this CSS class name, ASCII case-insensitive.
	 * @return bool|null Whether the matched tag contains the given class name, or null if not matched.
	 */
	public function has_class( $wanted_class ): ?bool {
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
	public function get_modifiable_text(): string {
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
	public function get_comment_type(): ?string {
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
	public function release_bookmark( $bookmark_name ): bool {
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
	public function seek( $bookmark_name ): bool {
		// Flush any pending updates to the document before beginning.
		$this->get_updated_html();

		$actual_bookmark_name = "_{$bookmark_name}";
		$processor_started_at = $this->state->current_token
			? $this->bookmarks[ $this->state->current_token->bookmark_name ]->start
			: 0;
		$bookmark_starts_at   = $this->bookmarks[ $actual_bookmark_name ]->start;
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
			 * When moving backward, stateful stacks should be cleared.
			 */
			foreach ( $this->state->stack_of_open_elements->walk_up() as $item ) {
				$this->state->stack_of_open_elements->remove_node( $item );
			}

			foreach ( $this->state->active_formatting_elements->walk_up() as $item ) {
				$this->state->active_formatting_elements->remove_node( $item );
			}

			/*
			 * **After** clearing stacks, more processor state can be reset.
			 * This must be done after clearing the stack because those stacks generate events that
			 * would appear on a subsequent call to `next_token()`.
			 */
			$this->state->frameset_ok                       = true;
			$this->state->stack_of_template_insertion_modes = array();
			$this->state->head_element                      = null;
			$this->state->form_element                      = null;
			$this->state->current_token                     = null;
			$this->current_element                          = null;
			$this->element_queue                            = array();

			/*
			 * The absence of a context node indicates a full parse.
			 * The presence of a context node indicates a fragment parser.
			 */
			if ( null === $this->context_node ) {
				$this->change_parsing_namespace( 'html' );
				$this->state->insertion_mode = WP_HTML_Processor_State::INSERTION_MODE_INITIAL;
				$this->breadcrumbs           = array();

				$this->bookmarks['initial'] = new WP_HTML_Span( 0, 0 );
				parent::seek( 'initial' );
				unset( $this->bookmarks['initial'] );
			} else {

				/*
				 * Push the root-node (HTML) back onto the stack of open elements.
				 *
				 * Fragment parsers require this extra bit of setup.
				 * It's handled in full parsers by advancing the processor state.
				 */
				$this->state->stack_of_open_elements->push(
					new WP_HTML_Token(
						'root-node',
						'HTML',
						false
					)
				);

				$this->change_parsing_namespace(
					$this->context_node->integration_node_type
						? 'html'
						: $this->context_node->namespace
				);

				if ( 'TEMPLATE' === $this->context_node->node_name ) {
					$this->state->stack_of_template_insertion_modes[] = WP_HTML_Processor_State::INSERTION_MODE_IN_TEMPLATE;
				}

				$this->reset_insertion_mode_appropriately();
				$this->breadcrumbs = array_slice( $this->breadcrumbs, 0, 2 );
				parent::seek( $this->context_node->bookmark_name );
			}
		}

		/*
		 * Here, the processor moves forward through the document until it matches the bookmark.
		 * do-while is used here because the processor is expected to already be stopped on
		 * a token than may match the bookmarked location.
		 */
		do {
			/*
			 * The processor will stop on virtual tokens, but bookmarks may not be set on them.
			 * They should not be matched when seeking a bookmark, skip them.
			 */
			if ( $this->is_virtual() ) {
				continue;
			}
			if ( $bookmark_starts_at === $this->bookmarks[ $this->state->current_token->bookmark_name ]->start ) {
				return true;
			}
		} while ( $this->next_token() );

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
	public function set_bookmark( $bookmark_name ): bool {
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
	public function has_bookmark( $bookmark_name ): bool {
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
	private function close_a_p_element(): void {
		$this->generate_implied_end_tags( 'P' );
		$this->state->stack_of_open_elements->pop_until( 'P' );
	}

	/**
	 * Closes elements that have implied end tags.
	 *
	 * @since 6.4.0
	 * @since 6.7.0 Full spec support.
	 *
	 * @see https://html.spec.whatwg.org/#generate-implied-end-tags
	 *
	 * @param string|null $except_for_this_element Perform as if this element doesn't exist in the stack of open elements.
	 */
	private function generate_implied_end_tags( ?string $except_for_this_element = null ): void {
		$elements_with_implied_end_tags = array(
			'DD',
			'DT',
			'LI',
			'OPTGROUP',
			'OPTION',
			'P',
			'RB',
			'RP',
			'RT',
			'RTC',
		);

		$no_exclusions = ! isset( $except_for_this_element );

		while (
			( $no_exclusions || ! $this->state->stack_of_open_elements->current_node_is( $except_for_this_element ) ) &&
			in_array( $this->state->stack_of_open_elements->current_node()->node_name, $elements_with_implied_end_tags, true )
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
	 * @since 6.7.0 Full spec support.
	 *
	 * @see WP_HTML_Processor::generate_implied_end_tags
	 * @see https://html.spec.whatwg.org/#generate-implied-end-tags
	 */
	private function generate_implied_end_tags_thoroughly(): void {
		$elements_with_implied_end_tags = array(
			'CAPTION',
			'COLGROUP',
			'DD',
			'DT',
			'LI',
			'OPTGROUP',
			'OPTION',
			'P',
			'RB',
			'RP',
			'RT',
			'RTC',
			'TBODY',
			'TD',
			'TFOOT',
			'TH',
			'THEAD',
			'TR',
		);

		while ( in_array( $this->state->stack_of_open_elements->current_node()->node_name, $elements_with_implied_end_tags, true ) ) {
			$this->state->stack_of_open_elements->pop();
		}
	}

	/**
	 * Returns the adjusted current node.
	 *
	 * > The adjusted current node is the context element if the parser was created as
	 * > part of the HTML fragment parsing algorithm and the stack of open elements
	 * > has only one element in it (fragment case); otherwise, the adjusted current
	 * > node is the current node.
	 *
	 * @see https://html.spec.whatwg.org/#adjusted-current-node
	 *
	 * @since 6.7.0
	 *
	 * @return WP_HTML_Token|null The adjusted current node.
	 */
	private function get_adjusted_current_node(): ?WP_HTML_Token {
		if ( isset( $this->context_node ) && 1 === $this->state->stack_of_open_elements->count() ) {
			return $this->context_node;
		}

		return $this->state->stack_of_open_elements->current_node();
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
	private function reconstruct_active_formatting_elements(): bool {
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

		$this->bail( 'Cannot reconstruct active formatting elements when advancing and rewinding is required.' );
	}

	/**
	 * Runs the reset the insertion mode appropriately algorithm.
	 *
	 * @since 6.7.0
	 *
	 * @see https://html.spec.whatwg.org/multipage/parsing.html#reset-the-insertion-mode-appropriately
	 */
	private function reset_insertion_mode_appropriately(): void {
		// Set the first node.
		$first_node = null;
		foreach ( $this->state->stack_of_open_elements->walk_down() as $first_node ) {
			break;
		}

		/*
		 * > 1. Let _last_ be false.
		 */
		$last = false;
		foreach ( $this->state->stack_of_open_elements->walk_up() as $node ) {
			/*
			 * > 2. Let _node_ be the last node in the stack of open elements.
			 * > 3. _Loop_: If _node_ is the first node in the stack of open elements, then set _last_
			 * >            to true, and, if the parser was created as part of the HTML fragment parsing
			 * >            algorithm (fragment case), set node to the context element passed to
			 * >            that algorithm.
			 * > …
			 */
			if ( $node === $first_node ) {
				$last = true;
				if ( isset( $this->context_node ) ) {
					$node = $this->context_node;
				}
			}

			// All of the following rules are for matching HTML elements.
			if ( 'html' !== $node->namespace ) {
				continue;
			}

			switch ( $node->node_name ) {
				/*
				 * > 4. If node is a `select` element, run these substeps:
				 * >   1. If _last_ is true, jump to the step below labeled done.
				 * >   2. Let _ancestor_ be _node_.
				 * >   3. _Loop_: If _ancestor_ is the first node in the stack of open elements,
				 * >      jump to the step below labeled done.
				 * >   4. Let ancestor be the node before ancestor in the stack of open elements.
				 * >   …
				 * >   7. Jump back to the step labeled _loop_.
				 * >   8. _Done_: Switch the insertion mode to "in select" and return.
				 */
				case 'SELECT':
					if ( ! $last ) {
						foreach ( $this->state->stack_of_open_elements->walk_up( $node ) as $ancestor ) {
							if ( 'html' !== $ancestor->namespace ) {
								continue;
							}

							switch ( $ancestor->node_name ) {
								/*
								 * > 5. If _ancestor_ is a `template` node, jump to the step below
								 * >    labeled _done_.
								 */
								case 'TEMPLATE':
									break 2;

								/*
								 * > 6. If _ancestor_ is a `table` node, switch the insertion mode to
								 * >    "in select in table" and return.
								 */
								case 'TABLE':
									$this->state->insertion_mode = WP_HTML_Processor_State::INSERTION_MODE_IN_SELECT_IN_TABLE;
									return;
							}
						}
					}
					$this->state->insertion_mode = WP_HTML_Processor_State::INSERTION_MODE_IN_SELECT;
					return;

				/*
				 * > 5. If _node_ is a `td` or `th` element and _last_ is false, then switch the
				 * >    insertion mode to "in cell" and return.
				 */
				case 'TD':
				case 'TH':
					if ( ! $last ) {
						$this->state->insertion_mode = WP_HTML_Processor_State::INSERTION_MODE_IN_CELL;
						return;
					}
					break;

					/*
					* > 6. If _node_ is a `tr` element, then switch the insertion mode to "in row"
					* >    and return.
					*/
				case 'TR':
					$this->state->insertion_mode = WP_HTML_Processor_State::INSERTION_MODE_IN_ROW;
					return;

				/*
				 * > 7. If _node_ is a `tbody`, `thead`, or `tfoot` element, then switch the
				 * >    insertion mode to "in table body" and return.
				 */
				case 'TBODY':
				case 'THEAD':
				case 'TFOOT':
					$this->state->insertion_mode = WP_HTML_Processor_State::INSERTION_MODE_IN_TABLE_BODY;
					return;

				/*
				 * > 8. If _node_ is a `caption` element, then switch the insertion mode to
				 * >    "in caption" and return.
				 */
				case 'CAPTION':
					$this->state->insertion_mode = WP_HTML_Processor_State::INSERTION_MODE_IN_CAPTION;
					return;

				/*
				 * > 9. If _node_ is a `colgroup` element, then switch the insertion mode to
				 * >    "in column group" and return.
				 */
				case 'COLGROUP':
					$this->state->insertion_mode = WP_HTML_Processor_State::INSERTION_MODE_IN_COLUMN_GROUP;
					return;

				/*
				 * > 10. If _node_ is a `table` element, then switch the insertion mode to
				 * >     "in table" and return.
				 */
				case 'TABLE':
					$this->state->insertion_mode = WP_HTML_Processor_State::INSERTION_MODE_IN_TABLE;
					return;

				/*
				 * > 11. If _node_ is a `template` element, then switch the insertion mode to the
				 * >     current template insertion mode and return.
				 */
				case 'TEMPLATE':
					$this->state->insertion_mode = end( $this->state->stack_of_template_insertion_modes );
					return;

				/*
				 * > 12. If _node_ is a `head` element and _last_ is false, then switch the
				 * >     insertion mode to "in head" and return.
				 */
				case 'HEAD':
					if ( ! $last ) {
						$this->state->insertion_mode = WP_HTML_Processor_State::INSERTION_MODE_IN_HEAD;
						return;
					}
					break;

				/*
				 * > 13. If _node_ is a `body` element, then switch the insertion mode to "in body"
				 * >     and return.
				 */
				case 'BODY':
					$this->state->insertion_mode = WP_HTML_Processor_State::INSERTION_MODE_IN_BODY;
					return;

				/*
				 * > 14. If _node_ is a `frameset` element, then switch the insertion mode to
				 * >     "in frameset" and return. (fragment case)
				 */
				case 'FRAMESET':
					$this->state->insertion_mode = WP_HTML_Processor_State::INSERTION_MODE_IN_FRAMESET;
					return;

				/*
				 * > 15. If _node_ is an `html` element, run these substeps:
				 * >     1. If the head element pointer is null, switch the insertion mode to
				 * >        "before head" and return. (fragment case)
				 * >     2. Otherwise, the head element pointer is not null, switch the insertion
				 * >        mode to "after head" and return.
				 */
				case 'HTML':
					$this->state->insertion_mode = isset( $this->state->head_element )
						? WP_HTML_Processor_State::INSERTION_MODE_AFTER_HEAD
						: WP_HTML_Processor_State::INSERTION_MODE_BEFORE_HEAD;
					return;
			}
		}

		/*
		 * > 16. If _last_ is true, then switch the insertion mode to "in body"
		 * >     and return. (fragment case)
		 *
		 * This is only reachable if `$last` is true, as per the fragment parsing case.
		 */
		$this->state->insertion_mode = WP_HTML_Processor_State::INSERTION_MODE_IN_BODY;
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
	private function run_adoption_agency_algorithm(): void {
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
				$this->bail( 'Cannot run adoption agency when "any other end tag" is required.' );
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

				if ( self::is_special( $item ) ) {
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

			$this->bail( 'Cannot extract common ancestor in adoption agency algorithm.' );
		}

		$this->bail( 'Cannot run adoption agency when looping required.' );
	}

	/**
	 * Runs the "close the cell" algorithm.
	 *
	 * > Where the steps above say to close the cell, they mean to run the following algorithm:
	 * >   1. Generate implied end tags.
	 * >   2. If the current node is not now a td element or a th element, then this is a parse error.
	 * >   3. Pop elements from the stack of open elements stack until a td element or a th element has been popped from the stack.
	 * >   4. Clear the list of active formatting elements up to the last marker.
	 * >   5. Switch the insertion mode to "in row".
	 *
	 * @see https://html.spec.whatwg.org/multipage/parsing.html#close-the-cell
	 *
	 * @since 6.7.0
	 */
	private function close_cell(): void {
		$this->generate_implied_end_tags();
		// @todo Parse error if the current node is a "td" or "th" element.
		foreach ( $this->state->stack_of_open_elements->walk_up() as $element ) {
			$this->state->stack_of_open_elements->pop();
			if ( 'TD' === $element->node_name || 'TH' === $element->node_name ) {
				break;
			}
		}
		$this->state->active_formatting_elements->clear_up_to_last_marker();
		$this->state->insertion_mode = WP_HTML_Processor_State::INSERTION_MODE_IN_ROW;
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
	private function insert_html_element( WP_HTML_Token $token ): void {
		$this->state->stack_of_open_elements->push( $token );
	}

	/**
	 * Inserts a foreign element on to the stack of open elements.
	 *
	 * @since 6.7.0
	 *
	 * @see https://html.spec.whatwg.org/#insert-a-foreign-element
	 *
	 * @param WP_HTML_Token $token                     Insert this token. The token's namespace and
	 *                                                 insertion point will be updated correctly.
	 * @param bool          $only_add_to_element_stack Whether to skip the "insert an element at the adjusted
	 *                                                 insertion location" algorithm when adding this element.
	 */
	private function insert_foreign_element( WP_HTML_Token $token, bool $only_add_to_element_stack ): void {
		$adjusted_current_node = $this->get_adjusted_current_node();

		$token->namespace = $adjusted_current_node ? $adjusted_current_node->namespace : 'html';

		if ( $this->is_mathml_integration_point() ) {
			$token->integration_node_type = 'math';
		} elseif ( $this->is_html_integration_point() ) {
			$token->integration_node_type = 'html';
		}

		if ( false === $only_add_to_element_stack ) {
			/*
			 * @todo Implement the "appropriate place for inserting a node" and the
			 *       "insert an element at the adjusted insertion location" algorithms.
			 *
			 * These algorithms mostly impacts DOM tree construction and not the HTML API.
			 * Here, there's no DOM node onto which the element will be appended, so the
			 * parser will skip this step.
			 *
			 * @see https://html.spec.whatwg.org/#insert-an-element-at-the-adjusted-insertion-location
			 */
		}

		$this->insert_html_element( $token );
	}

	/**
	 * Inserts a virtual element on the stack of open elements.
	 *
	 * @since 6.7.0
	 *
	 * @param string      $token_name    Name of token to create and insert into the stack of open elements.
	 * @param string|null $bookmark_name Optional. Name to give bookmark for created virtual node.
	 *                                   Defaults to auto-creating a bookmark name.
	 * @return WP_HTML_Token Newly-created virtual token.
	 */
	private function insert_virtual_node( $token_name, $bookmark_name = null ): WP_HTML_Token {
		$here = $this->bookmarks[ $this->state->current_token->bookmark_name ];
		$name = $bookmark_name ?? $this->bookmark_token();

		$this->bookmarks[ $name ] = new WP_HTML_Span( $here->start, 0 );

		$token = new WP_HTML_Token( $name, $token_name, false );
		$this->insert_html_element( $token );
		return $token;
	}

	/*
	 * HTML Specification Helpers
	 */

	/**
	 * Indicates if the current token is a MathML integration point.
	 *
	 * @since 6.7.0
	 *
	 * @see https://html.spec.whatwg.org/#mathml-text-integration-point
	 *
	 * @return bool Whether the current token is a MathML integration point.
	 */
	private function is_mathml_integration_point(): bool {
		$current_token = $this->state->current_token;
		if ( ! isset( $current_token ) ) {
			return false;
		}

		if ( 'math' !== $current_token->namespace || 'M' !== $current_token->node_name[0] ) {
			return false;
		}

		$tag_name = $current_token->node_name;

		return (
			'MI' === $tag_name ||
			'MO' === $tag_name ||
			'MN' === $tag_name ||
			'MS' === $tag_name ||
			'MTEXT' === $tag_name
		);
	}

	/**
	 * Indicates if the current token is an HTML integration point.
	 *
	 * Note that this method must be an instance method with access
	 * to the current token, since it needs to examine the attributes
	 * of the currently-matched tag, if it's in the MathML namespace.
	 * Otherwise it would be required to scan the HTML and ensure that
	 * no other accounting is overlooked.
	 *
	 * @since 6.7.0
	 *
	 * @see https://html.spec.whatwg.org/#html-integration-point
	 *
	 * @return bool Whether the current token is an HTML integration point.
	 */
	private function is_html_integration_point(): bool {
		$current_token = $this->state->current_token;
		if ( ! isset( $current_token ) ) {
			return false;
		}

		if ( 'html' === $current_token->namespace ) {
			return false;
		}

		$tag_name = $current_token->node_name;

		if ( 'svg' === $current_token->namespace ) {
			return (
				'DESC' === $tag_name ||
				'FOREIGNOBJECT' === $tag_name ||
				'TITLE' === $tag_name
			);
		}

		if ( 'math' === $current_token->namespace ) {
			if ( 'ANNOTATION-XML' !== $tag_name ) {
				return false;
			}

			$encoding = $this->get_attribute( 'encoding' );

			return (
				is_string( $encoding ) &&
				(
					0 === strcasecmp( $encoding, 'application/xhtml+xml' ) ||
					0 === strcasecmp( $encoding, 'text/html' )
				)
			);
		}

		$this->bail( 'Should not have reached end of HTML Integration Point detection: check HTML API code.' );
		// This unnecessary return prevents tools from inaccurately reporting type errors.
		return false;
	}

	/**
	 * Returns whether an element of a given name is in the HTML special category.
	 *
	 * @since 6.4.0
	 *
	 * @see https://html.spec.whatwg.org/#special
	 *
	 * @param WP_HTML_Token|string $tag_name Node to check, or only its name if in the HTML namespace.
	 * @return bool Whether the element of the given name is in the special category.
	 */
	public static function is_special( $tag_name ): bool {
		if ( is_string( $tag_name ) ) {
			$tag_name = strtoupper( $tag_name );
		} else {
			$tag_name = 'html' === $tag_name->namespace
				? strtoupper( $tag_name->node_name )
				: "{$tag_name->namespace} {$tag_name->node_name}";
		}

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
			'math MI' === $tag_name ||
			'math MO' === $tag_name ||
			'math MN' === $tag_name ||
			'math MS' === $tag_name ||
			'math MTEXT' === $tag_name ||
			'math ANNOTATION-XML' === $tag_name ||

			// SVG.
			'svg DESC' === $tag_name ||
			'svg FOREIGNOBJECT' === $tag_name ||
			'svg TITLE' === $tag_name
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
	public static function is_void( $tag_name ): bool {
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

	/**
	 * Gets an encoding from a given string.
	 *
	 * This is an algorithm defined in the WHAT-WG specification.
	 *
	 * Example:
	 *
	 *     'UTF-8' === self::get_encoding( 'utf8' );
	 *     'UTF-8' === self::get_encoding( "  \tUTF-8 " );
	 *     null    === self::get_encoding( 'UTF-7' );
	 *     null    === self::get_encoding( 'utf8; charset=' );
	 *
	 * @see https://encoding.spec.whatwg.org/#concept-encoding-get
	 *
	 * @todo As this parser only supports UTF-8, only the UTF-8
	 *       encodings are detected. Add more as desired, but the
	 *       parser will bail on non-UTF-8 encodings.
	 *
	 * @since 6.7.0
	 *
	 * @param string $label A string which may specify a known encoding.
	 * @return string|null Known encoding if matched, otherwise null.
	 */
	protected static function get_encoding( string $label ): ?string {
		/*
		 * > Remove any leading and trailing ASCII whitespace from label.
		 */
		$label = trim( $label, " \t\f\r\n" );

		/*
		 * > If label is an ASCII case-insensitive match for any of the labels listed in the
		 * > table below, then return the corresponding encoding; otherwise return failure.
		 */
		switch ( strtolower( $label ) ) {
			case 'unicode-1-1-utf-8':
			case 'unicode11utf8':
			case 'unicode20utf8':
			case 'utf-8':
			case 'utf8':
			case 'x-unicode20utf8':
				return 'UTF-8';

			default:
				return null;
		}
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

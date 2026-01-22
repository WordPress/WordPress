<?php
/**
 * Efficiently scan through block structure in document without parsing
 * the entire block tree and all of its JSON attributes into memory.
 *
 * @package WordPress
 * @subpackage Blocks
 * @since 6.9.0
 */

/**
 * Class for efficiently scanning through block structure in a document
 * without parsing the entire block tree and JSON attributes into memory.
 *
 * ## Overview
 *
 * This class is designed to help analyze and modify block structure in a
 * streaming fashion and to bridge the gap between parsed block trees and
 * the text representing them.
 *
 * Use-cases for this class include but are not limited to:
 *
 *  - Counting block types in a document.
 *  - Queuing stylesheets based on the presence of various block types.
 *  - Modifying blocks of a given type, i.e. migrations, updates, and styling.
 *  - Searching for content of specific kinds, e.g. checking for blocks
 *    with certain theme support attributes, or block bindings.
 *  - Adding CSS class names to the element wrapping a block’s inner blocks.
 *
 * > *Note!* If a fully-parsed block tree of a document is necessary, including
 * >         all the parsed JSON attributes, nested blocks, and HTML, consider
 * >         using {@see \parse_blocks()} instead which will parse the document
 * >         in one swift pass.
 *
 * For typical usage, jump first to the methods {@see self::next_block()},
 * {@see self::next_delimiter()}, or {@see self::next_token()}.
 *
 * ### Values
 *
 * As a lower-level interface than {@see parse_blocks()} this class follows
 * different performance-focused values:
 *
 *  - Minimize allocations so that documents of any size may be processed
 *    on a fixed or marginal amount of memory.
 *  - Make hidden costs explicit so that calling code only has to pay the
 *    performance penalty for features it needs.
 *  - Operate with a streaming and re-entrant design to make it possible
 *    to operate on chunks of a document and to resume after pausing.
 *
 * This means that some operations might appear more cumbersome than one
 * might expect. This design tradeoff opens up opportunity to wrap this in
 * a convenience class to add higher-level functionality.
 *
 * ## Concepts
 *
 * All text documents can be considered a block document containing a combination
 * of “freeform HTML” and explicit block structure. Block structure forms through
 * special HTML comments called _delimiters_ which include a block type and,
 * optionally, block attributes encoded as a JSON object payload.
 *
 * This processor is designed to scan through a block document from delimiter to
 * delimiter, tracking how the delimiters impact the structure of the document.
 * Spans of HTML appear between delimiters. If these spans exist at the top level
 * of the document, meaning there is no containing block around them, they are
 * considered freeform HTML content. If, however, they appear _inside_ block
 * structure they are interpreted as `innerHTML` for the containing block.
 *
 * ### Tokens and scanning
 *
 * As the processor scans through a document is reports information about the token
 * on which is pauses. Tokens represent spans of text in the input comprising block
 * delimiters and spans of HTML.
 *
 *  - {@see self::next_token()} visits every contiguous subspan of text in the
 *    input document. This includes all explicit block comment delimiters and spans
 *    of HTML content (whether freeform or inner HTML).
 *  - {@see self::next_delimiter()} visits every explicit block comment delimiter
 *    unless passed a block type which covers freeform HTML content. In these cases
 *    it will stop at top-level spans of HTML and report a `null` block type.
 *  - {@see self::next_block()} visits every block delimiter which _opens_ a block.
 *    This includes opening block delimiters as well as void block delimiters. With
 *    the same exception as above for freeform HTML block types, this will visit
 *    top-level spans of HTML content.
 *
 * When matched on a particular token, the following methods provide structural
 * and textual information about it:
 *
 *  - {@see self::get_delimiter_type()} reports whether the delimiter is an opener,
 *    a closer, or if it represents a whole void block.
 *  - {@see self::get_block_type()} reports the fully-qualified block type which
 *    the delimiter represents.
 *  - {@see self::get_printable_block_type()} reports the fully-qualified block type,
 *    but returns `core/freeform` instead of `null` for top-level freeform HTML content.
 *  - {@see self::is_block_type()} indicates if the delimiter represents a block of
 *    the given block type, or wildcard or pseudo-block type described below.
 *  - {@see self::opens_block()} indicates if the delimiter opens a block of one
 *    of the provided block types. Opening, void, and top-level freeform HTML content
 *    all open blocks.
 *  - {@see static::get_attributes()} is currently reserved for a future streaming
 *    JSON parser class.
 *  - {@see self::allocate_and_return_parsed_attributes()} extracts the JSON attributes
 *    for delimiters which open blocks and return the fully-parsed attributes as an
 *    associative array. {@see static::get_last_json_error()} for when this fails.
 *  - {@see self::is_html()} indicates if the token is a span of HTML which might
 *    be top-level freeform content or a block’s inner HTML.
 *  - {@see self::get_html_content()} returns the span of HTML.
 *  - {@see self::get_span()} for the byte offset and length into the input document
 *    representing the token.
 *
 * It’s possible for the processor to fail to scan forward if the input document ends
 * in a proper prefix of an explicit block comment delimiter. For example, if the input
 * ends in `<!-- wp:` then it _might_ be the start of another delimiter. The parser
 * cannot know, however, and therefore refuses to proceed. {@see static::get_last_error()}
 * to distinguish between a failure to find the next token and an incomplete input.
 *
 * ### Block types
 *
 * A block’s “type” comprises an optional _namespace_ and _name_. If the namespace
 * isn’t provided it will be interpreted as the implicit `core` namespace. For example,
 * the type `gallery` is the name of the block in the `core` namespace, but the type
 * `abc/gallery` is the _fully-qualified_ block type for the block whose name is still
 * `gallery`, but in the `abc` namespace.
 *
 * Methods on this class are aware of this block naming semantic and anywhere a block
 * type is an argument to a method it will be normalized to account for implicit namespaces.
 * Passing `paragraph` is the same as passing `core/paragraph`. On the contrary, anywhere
 * this class returns a block type, it will return the fully-qualified and normalized form.
 * For example, for the `<!-- wp:group -->` delimiter it will return `core/group` as the
 * block type.
 *
 * There are two special block types that change the behavior of the processor:
 *
 *  - The wildcard `*` represents _any block_. In addition to matching all block types,
 *    it also represents top-level freeform HTML whose block type is reported as `null`.
 *
 *  - The `core/freeform` block type is a pseudo-block type which explicitly matches
 *    top-level freeform HTML.
 *
 * These special block types can be passed into any method which searches for blocks.
 *
 * There is one additional special block type which may be returned from
 * {@see self::get_printable_block_type()}. This is the `#innerHTML` type, which
 * indicates that the HTML span on which the processor is paused is inner HTML for
 * a containing block.
 *
 * ### Spans of HTML
 *
 * Non-block content plays a complicated role in processing block documents. This
 * processor exposes tools to help work with these spans of HTML.
 *
 *  - {@see self::is_html()} indicates if the processor is paused at a span of
 *    HTML but does not differentiate between top-level freeform content and inner HTML.
 *  - {@see self::is_non_whitespace_html()} indicates not only if the processor
 *    is paused at a span of HTML, but also whether that span incorporates more than
 *    whitespace characters. Because block serialization often inserts newlines between
 *    block comment delimiters, this is useful for distinguishing “real” freeform
 *    content from purely aesthetic syntax.
 *  - {@see self::is_block_type()} matches top-level freeform HTML content when
 *    provided one of the special block types described above.
 *
 * ### Block structure
 *
 * As the processor traverses block delimiters it maintains a stack of which blocks are
 * open at the given place in the document where it’s paused. This stack represents the
 * block structure of a document and is used to determine where blocks end, which blocks
 * represent inner blocks, whether a span of HTML is top-level freeform content, and
 * more. Investigate the stack with {@see self::get_breadcrumbs()}, which returns an
 * array of block types starting at the outermost-open block and descending to the
 * currently-visited block.
 *
 * Unlike {@parse_blocks()}, spans of HTML appear in this structure as the special
 * reported block type `#html`. Such a span represents inner HTML for a block if the
 * depth reported by {@see self::get_depth()} is greater than one.
 *
 * It will generally not be necessary to inspect the stack of open blocks, though
 * depth may be important for finding where blocks end. When visiting a block opener,
 * the depth will have been increased before pausing; in contrast the depth is
 * decremented before visiting a closer. This makes the following an easy way to
 * determine if a block is still open.
 *
 * Example:
 *
 *     $depth = $processor->get_depth();
 *     while ( $processor->next_token() && $processor->get_depth() > $depth ) {
 *         continue
 *     }
 *     // Processor is now paused at the token immediately following the closed block.
 *
 * #### Extracting blocks
 *
 * A unique feature of this processor is the ability to return the same output as
 * {@see \parse_blocks()} would produce, but for a subset of the input document.
 * For example, it’s possible to extract an image block, manipulate that parsed
 * block, and re-serialize it into the original document. It’s possible to do so
 * while skipping over the parse of the rest of the document.
 *
 * {@see self::extract_full_block_and_advance()} will scan forward from the current block opener
 * and build the parsed block structure until the current block is closed. It will
 * include all inner HTML and inner blocks, and parse all of the inner blocks. It
 * can be used to extract a block at any depth in the document, helpful for operating
 * on blocks within nested structure.
 *
 * Example:
 *
 *     if ( ! $processor->next_block( 'gallery' ) ) {
 *         return $post_content;
 *     }
 *
 *     $gallery_at    = $processor->get_span()->start;
 *     $gallery_block = $processor->extract_full_block_and_advance();
 *     $after_gallery = $processor->get_span()->start;
 *     return (
 *         substr( $post_content, 0, $gallery_at ) .
 *         serialize_block( modify_gallery( $gallery_block ) .
 *         substr( $post_content, $after_gallery )
 *     );
 *
 * #### Handling of malformed structure
 *
 * There are situations where closing block delimiters appear for which no open block
 * exists, or where a document ends before a block is closed, or where a closing block
 * delimiter appears but references a different block type than the most-recently
 * opened block does. In all of these cases, the stack of open blocks should mirror
 * the behavior in {@see \parse_blocks()}.
 *
 * Unlike {@see \parse_blocks()}, however, this processor can still operate on the
 * invalid block delimiters. It provides a few functions which can be used for building
 * custom and non-spec-compliant error handling.
 *
 *  - {@see self::has_closing_flag()} indicates if the block delimiter contains the
 *    closing flag at the end. Some invalid block delimiters might contain both the
 *    void and closing flag, in which case {@see self::get_delimiter_type()} will
 *    report that it’s a void block.
 *  - {@see static::get_last_error()} indicates if the processor reached an invalid
 *    block closing. Depending on the context, {@see \parse_blocks()} might instead
 *    ignore the token or treat it as freeform HTML content.
 *
 * ## Static helpers
 *
 * This class provides helpers for performing semantic block-related operations.
 *
 *  - {@see self::normalize_block_type()} takes a block type with or without the
 *    implicit `core` namespace and returns a fully-qualified block type.
 *  - {@see self::are_equal_block_types()} indicates if two spans across one or
 *    more input texts represent the same fully-qualified block type.
 *
 * ## Subclassing
 *
 * This processor is designed to accurately parse a block document. Therefore, many
 * of its methods are not meant for subclassing. However, overall this class supports
 * building higher-level convenience classes which may choose to subclass it. For those
 * classes, avoid re-implementing methods except for the list below. Instead, create
 * new names representing the higher-level concepts being introduced. For example, instead
 * of creating a new method named `next_block()` which only advances to blocks of a given
 * kind, consider creating a new method named something like `next_layout_block()` which
 * won’t interfere with the base class method.
 *
 *  - {@see static::get_last_error()} may be reimplemented to report new errors in the subclass
 *    which aren’t intrinsic to block parsing.
 *  - {@see static::get_attributes()} may be reimplemented to provide a streaming interface
 *    to reading and modifying a block’s JSON attributes. It should be fast and memory efficient.
 *  - {@see static::get_last_json_error()} may be reimplemented to report new errors introduced
 *    with a reimplementation of {@see static::get_attributes()}.
 *
 * @since 6.9.0
 */
class WP_Block_Processor {
	/**
	 * Indicates if the last operation failed, otherwise
	 * will be `null` for success.
	 *
	 * @since 6.9.0
	 *
	 * @var string|null
	 */
	private $last_error = null;

	/**
	 * Indicates failures from decoding JSON attributes.
	 *
	 * @since 6.9.0
	 *
	 * @see \json_last_error()
	 *
	 * @var int
	 */
	private $last_json_error = JSON_ERROR_NONE;

	/**
	 * Source text provided to processor.
	 *
	 * @since 6.9.0
	 *
	 * @var string
	 */
	protected $source_text;

	/**
	 * Byte offset into source text where a matched delimiter starts.
	 *
	 * Example:
	 *
	 *          5    10   15   20   25   30   35   40   45   50
	 *     <!-- wp:group --><!-- wp:void /--><!-- /wp:group -->
	 *                      ╰─ Starts at byte offset 17.
	 *
	 * @since 6.9.0
	 *
	 * @var int
	 */
	private $matched_delimiter_at = 0;

	/**
	 * Byte length of full span of a matched delimiter.
	 *
	 * Example:
	 *
	 *          5    10   15   20   25   30   35   40   45   50
	 *     <!-- wp:group --><!-- wp:void /--><!-- /wp:group -->
	 *                      ╰───────────────╯
	 *                        17 bytes long.
	 *
	 * @since 6.9.0
	 *
	 * @var int
	 */
	private $matched_delimiter_length = 0;

	/**
	 * First byte offset into source text following any previously-matched delimiter.
	 * Used to indicate where an HTML span starts.
	 *
	 * Example:
	 *
	 *          5    10   15   20   25   30   35   40   45   50   55
	 *     <!-- wp:paragraph --><p>Content</p><⃨!⃨-⃨-⃨ ⃨/⃨w⃨p⃨:⃨p⃨a⃨r⃨a⃨g⃨r⃨a⃨p⃨h⃨ ⃨-⃨-⃨>⃨
	 *                          │             ╰─ This delimiter was matched, and after matching,
	 *                          │                revealed the preceding HTML span.
	 *                          │
	 *                          ╰─ The first byte offset after the previous matched delimiter
	 *                             is 21. Because the matched delimiter starts at 55, which is after
	 *                             this, a span of HTML must exist between these boundaries.
	 *
	 * @since 6.9.0
	 *
	 * @var int
	 */
	private $after_previous_delimiter = 0;

	/**
	 * Byte offset where namespace span begins.
	 *
	 * When no namespace is present, this will be the same as the starting
	 * byte offset for the block name.
	 *
	 * Example:
	 *
	 *     <!-- wp:core/gallery -->
	 *             │    ╰─ Name starts here.
	 *             ╰─ Namespace starts here.
	 *
	 *     <!-- wp:gallery -->
	 *             ├─ The namespace would start here but is implied as “core.”
	 *             ╰─ The name starts here.
	 *
	 * @since 6.9.0
	 *
	 * @var int
	 */
	private $namespace_at = 0;

	/**
	 * Byte offset where block name span begins.
	 *
	 * When no namespace is present, this will be the same as the starting
	 * byte offset for the block namespace.
	 *
	 * Example:
	 *
	 *     <!-- wp:core/gallery -->
	 *             │    ╰─ Name starts here.
	 *             ╰─ Namespace starts here.
	 *
	 *     <!-- wp:gallery -->
	 *             ├─ The namespace would start here but is implied as “core.”
	 *             ╰─ The name starts here.
	 *
	 * @since 6.9.0
	 *
	 * @var int
	 */
	private $name_at = 0;

	/**
	 * Byte length of block name span.
	 *
	 * Example:
	 *
	 *          5    10   15   20   25
	 *     <!-- wp:core/gallery -->
	 *                  ╰─────╯
	 *                7 bytes long.
	 *
	 * @since 6.9.0
	 *
	 * @var int
	 */
	private $name_length = 0;

	/**
	 * Whether the delimiter contains the block-closing flag.
	 *
	 * This may be erroneous if present within a void block,
	 * therefore the {@see self::has_closing_flag()} can be used by
	 * calling code to perform custom error-handling.
	 *
	 * @since 6.9.0
	 *
	 * @var bool
	 */
	private $has_closing_flag = false;

	/**
	 * Byte offset where JSON attributes span begins.
	 *
	 * Example:
	 *
	 *          5    10   15   20   25   30   35   40
	 *     <!-- wp:paragraph {"dropCaps":true} -->
	 *                       ╰─ Starts at byte offset 18.
	 *
	 * @since 6.9.0
	 *
	 * @var int
	 */
	private $json_at;

	/**
	 * Byte length of JSON attributes span, or 0 if none are present.
	 *
	 * Example:
	 *
	 *          5    10   15   20   25   30   35   40
	 *     <!-- wp:paragraph {"dropCaps":true} -->
	 *                       ╰───────────────╯
	 *                         17 bytes long.
	 *
	 * @since 6.9.0
	 *
	 * @var int
	 */
	private $json_length = 0;

	/**
	 * Internal parser state, differentiating whether the instance is currently matched,
	 * on an implicit freeform node, in error, or ready to begin parsing.
	 *
	 * @see self::READY
	 * @see self::MATCHED
	 * @see self::HTML_SPAN
	 * @see self::INCOMPLETE_INPUT
	 * @see self::COMPLETE
	 *
	 * @since 6.9.0
	 *
	 * @var string
	 */
	protected $state = self::READY;

	/**
	 * Indicates what kind of block comment delimiter was matched.
	 *
	 * One of:
	 *
	 *  - {@see self::OPENER} If the delimiter is opening a block.
	 *  - {@see self::CLOSER} If the delimiter is closing an open block.
	 *  - {@see self::VOID}   If the delimiter represents a void block with no inner content.
	 *
	 * If a parsed comment delimiter contains both the closing and the void
	 * flags then it will be interpreted as a void block to match the behavior
	 * of the official block parser, however, this is a syntax error and probably
	 * the block ought to close an open block of the same name, if one is open.
	 *
	 * @since 6.9.0
	 *
	 * @var string
	 */
	private $type;

	/**
	 * Whether the last-matched delimiter acts like a void block and should be
	 * popped from the stack of open blocks as soon as the parser advances.
	 *
	 * This applies to void block delimiters and to HTML spans.
	 *
	 * @since 6.9.0
	 *
	 * @var bool
	 */
	private $was_void = false;

	/**
	 * For every open block, in hierarchical order, this stores the byte offset
	 * into the source text where the block type starts, including for HTML spans.
	 *
	 * To avoid allocating and normalizing block names when they aren’t requested,
	 * the stack of open blocks is stored as the byte offsets and byte lengths of
	 * each open block’s block type. This allows for minimal tracking and quick
	 * reading or comparison of block types when requested.
	 *
	 * @since 6.9.0
	 *
	 * @see self::$open_blocks_length
	 *
	 * @var int[]
	 */
	private $open_blocks_at = array();

	/**
	 * For every open block, in hierarchical order, this stores the byte length
	 * of the block’s block type in the source text. For HTML spans this is 0.
	 *
	 * @since 6.9.0
	 *
	 * @see self::$open_blocks_at
	 *
	 * @var int[]
	 */
	private $open_blocks_length = array();

	/**
	 * Indicates which operation should apply to the stack of open blocks after
	 * processing any pending spans of HTML.
	 *
	 * Since HTML spans are discovered after matching block delimiters, those
	 * delimiters need to defer modifying the stack of open blocks. This value,
	 * if set, indicates what operation should be applied. The properties
	 * associated with token boundaries still point to the delimiters even
	 * when processing HTML spans, so there’s no need to track them independently.
	 *
	 * @var 'push'|'void'|'pop'|null
	 */
	private $next_stack_op = null;

	/**
	 * Creates a new block processor.
	 *
	 * Example:
	 *
	 *     $processor = new WP_Block_Processor( $post_content );
	 *     if ( $processor->next_block( 'core/image' ) ) {
	 *         echo "Found an image!\n";
	 *     }
	 *
	 * @see self::next_block() to advance to the start of the next block (skips closers).
	 * @see self::next_delimiter() to advance to the next explicit block delimiter.
	 * @see self::next_token() to advance to the next block delimiter or HTML span.
	 *
	 * @since 6.9.0
	 *
	 * @param string $source_text Input document potentially containing block content.
	 */
	public function __construct( string $source_text ) {
		$this->source_text = $source_text;
	}

	/**
	 * Advance to the next block delimiter which opens a block, indicating if one was found.
	 *
	 * Delimiters which open blocks include opening and void block delimiters. To visit
	 * freeform HTML content, pass the wildcard “*” as the block type.
	 *
	 * Use this function to walk through the blocks in a document, pausing where they open.
	 *
	 * Example blocks:
	 *
	 *     // The first delimiter opens the paragraph block.
	 *     <⃨!⃨-⃨-⃨ ⃨w⃨p⃨:⃨p⃨a⃨r⃨a⃨g⃨r⃨a⃨p⃨h⃨ ⃨-⃨-⃨>⃨<p>Content</p><!-- /wp:paragraph-->
	 *
	 *     // The void block is the first opener in this sequence of closers.
	 *     <!-- /wp:group --><⃨!⃨-⃨-⃨ ⃨w⃨p⃨:⃨s⃨p⃨a⃨c⃨e⃨r⃨ ⃨{⃨"⃨h⃨e⃨i⃨g⃨h⃨t⃨"⃨:⃨"⃨2⃨0⃨0⃨p⃨x⃨"⃨}⃨ ⃨/⃨-⃨-⃨>⃨<!-- /wp:group -->
	 *
	 *     // If, however, `*` is provided as the block type, freeform content is matched.
	 *     <⃨h⃨2⃨>⃨M⃨y⃨ ⃨s⃨y⃨n⃨o⃨p⃨s⃨i⃨s⃨<⃨/⃨h⃨2⃨>⃨\⃨n⃨<!-- wp:my/table-of-contents /-->
	 *
	 *     // Inner HTML is never freeform content, and will not be matched even with the wildcard.
	 *     <!-- /wp:list-item --></ul><!-- /wp:list --><⃨!⃨-⃨-⃨ ⃨w⃨p⃨:⃨p⃨a⃨r⃨a⃨g⃨r⃨a⃨p⃨h⃨ ⃨-⃨>⃨<p>
	 *
	 * Example:
	 *
	 *     // Find all textual ranges of image block opening delimiters.
	 *     $images = array();
	 *     $processor = new WP_Block_Processor( $html );
	 *     while ( $processor->next_block( 'core/image' ) ) {
	 *         $images[] = $processor->get_span();
	 *     }
	 *
	 *  In some cases it may be useful to conditionally visit the implicit freeform
	 *  blocks, such as when determining if a post contains freeform content that
	 *  isn’t purely whitespace.
	 *
	 *  Example:
	 *
	 *      $seen_block_types = [];
	 *      $block_type       = '*';
	 *      $processor        = new WP_Block_Processor( $html );
	 *      while ( $processor->next_block( $block_type ) {
	 *          // Stop wasting time visiting freeform blocks after one has been found.
	 *          if (
	 *              '*' === $block_type &&
	 *              null === $processor->get_block_type() &&
	 *              $processor->is_non_whitespace_html()
	 *          ) {
	 *              $block_type = null;
	 *              $seen_block_types['core/freeform'] = true;
	 *              continue;
	 *          }
	 *
	 *          $seen_block_types[ $processor->get_block_type() ] = true;
	 *      }
	 *
	 * @since 6.9.0
	 *
	 * @see self::next_delimiter() to advance to the next explicit block delimiter.
	 * @see self::next_token() to advance to the next block delimiter or HTML span.
	 *
	 * @param string|null $block_type Optional. If provided, advance until a block of this type is found.
	 *                                Default is to stop at any block regardless of its type.
	 * @return bool Whether an opening delimiter for a block was found.
	 */
	public function next_block( ?string $block_type = null ): bool {
		while ( $this->next_delimiter( $block_type ) ) {
			if ( self::CLOSER !== $this->get_delimiter_type() ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Advance to the next block delimiter in a document, indicating if one was found.
	 *
	 * Delimiters may include invalid JSON. This parser does not attempt to parse the
	 * JSON attributes until requested; when invalid, the attributes will be null. This
	 * matches the behavior of {@see \parse_blocks()}. To visit freeform HTML content,
	 * pass the wildcard “*” as the block type.
	 *
	 * Use this function to walk through the block delimiters in a document.
	 *
	 * Example delimiters:
	 *
	 *     <!-- wp:paragraph {"dropCap": true} -->
	 *     <!-- wp:separator /-->
	 *     <!-- /wp:paragraph -->
	 *
	 *     // If the wildcard `*` is provided as the block type, freeform content is matched.
	 *     <⃨h⃨2⃨>⃨M⃨y⃨ ⃨s⃨y⃨n⃨o⃨p⃨s⃨i⃨s⃨<⃨/⃨h⃨2⃨>⃨\⃨n⃨<!-- wp:my/table-of-contents /-->
	 *
	 *     // Inner HTML is never freeform content, and will not be matched even with the wildcard.
	 *     ...</ul><⃨!⃨-⃨-⃨ ⃨/⃨w⃨p⃨:⃨l⃨i⃨s⃨t⃨ ⃨-⃨-⃨>⃨<!-- wp:paragraph --><p>
	 *
	 * Example:
	 *
	 *     $html      = '<!-- wp:void /-->\n<!-- wp:void /-->';
	 *     $processor = new WP_Block_Processor( $html );
	 *     while ( $processor->next_delimiter() {
	 *         // Runs twice, seeing both void blocks of type “core/void.”
	 *     }
	 *
	 *     $processor = new WP_Block_Processor( $html );
	 *     while ( $processor->next_delimiter( '*' ) ) {
	 *         // Runs thrice, seeing the void block, the newline span, and the void block.
	 *     }
	 *
	 * @since 6.9.0
	 *
	 * @param string|null $block_name Optional. Keep searching until a block of this name is found.
	 *                                Defaults to visit every block regardless of type.
	 * @return bool Whether a block delimiter was matched.
	 */
	public function next_delimiter( ?string $block_name = null ): bool {
		if ( ! isset( $block_name ) ) {
			while ( $this->next_token() ) {
				if ( ! $this->is_html() ) {
					return true;
				}
			}

			return false;
		}

		while ( $this->next_token() ) {
			if ( $this->is_block_type( $block_name ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Advance to the next block delimiter or HTML span in a document, indicating if one was found.
	 *
	 * This function steps through every syntactic chunk in a document. This includes explicit
	 * block comment delimiters, freeform non-block content, and inner HTML segments.
	 *
	 * Example tokens:
	 *
	 *     <!-- wp:paragraph {"dropCap": true} -->
	 *     <!-- wp:separator /-->
	 *     <!-- /wp:paragraph -->
	 *     <p>Normal HTML content</p>
	 *     Plaintext content too!
	 *
	 * Example:
	 *
	 *     // Find span containing wrapping HTML element surrounding inner blocks.
	 *     $processor = new WP_Block_Processor( $html );
	 *     if ( ! $processor->next_block( 'gallery' ) ) {
	 *         return null;
	 *     }
	 *
	 *     $containing_span = null;
	 *     while ( $processor->next_token() && $processor->is_html() ) {
	 *         $containing_span = $processor->get_span();
	 *     }
	 *
	 * This method will visit all HTML spans including those forming freeform non-block
	 * content as well as those which are part of a block’s inner HTML.
	 *
	 * @since 6.9.0
	 *
	 * @return bool Whether a token was matched or the end of the document was reached without finding any.
	 */
	public function next_token(): bool {
		if ( $this->last_error || self::COMPLETE === $this->state || self::INCOMPLETE_INPUT === $this->state ) {
			return false;
		}

		// Void tokens automatically pop off the stack of open blocks.
		if ( $this->was_void ) {
			array_pop( $this->open_blocks_at );
			array_pop( $this->open_blocks_length );
			$this->was_void = false;
		}

		$text = $this->source_text;
		$end  = strlen( $text );

		/*
		 * Because HTML spans are inferred after finding the next delimiter, it means that
		 * the parser must transition out of that HTML state and reuse the token boundaries
		 * it found after the HTML span. If those boundaries are before the end of the
		 * document it implies that a real delimiter was found; otherwise this must be the
		 * terminating HTML span and the parsing is complete.
		 */
		if ( self::HTML_SPAN === $this->state ) {
			if ( $this->matched_delimiter_at >= $end ) {
				$this->state = self::COMPLETE;
				return false;
			}

			switch ( $this->next_stack_op ) {
				case 'void':
					$this->was_void             = true;
					$this->open_blocks_at[]     = $this->namespace_at;
					$this->open_blocks_length[] = $this->name_at + $this->name_length - $this->namespace_at;
					break;

				case 'push':
					$this->open_blocks_at[]     = $this->namespace_at;
					$this->open_blocks_length[] = $this->name_at + $this->name_length - $this->namespace_at;
					break;

				case 'pop':
					array_pop( $this->open_blocks_at );
					array_pop( $this->open_blocks_length );
					break;
			}

			$this->next_stack_op = null;
			$this->state         = self::MATCHED;
			return true;
		}

		$this->state          = self::READY;
		$after_prev_delimiter = $this->matched_delimiter_at + $this->matched_delimiter_length;
		$at                   = $after_prev_delimiter;

		while ( $at < $end ) {
			/*
			 * Find the next possible start of a delimiter.
			 *
			 * This follows the behavior in the official block parser, which segments a post
			 * by the block comment delimiters. It is possible for an HTML attribute to contain
			 * what looks like a block comment delimiter but which is actually an HTML attribute
			 * value. In such a case, the parser here will break apart the HTML and create the
			 * block boundary inside the HTML attribute. In other words, the block parser
			 * isolates sections of HTML from each other, even if that leads to malformed markup.
			 *
			 * For a more robust parse, scan through the document with the HTML API and parse
			 * comments once they are matched to see if they are also block delimiters. In
			 * practice, this nuance has not caused any known problems since developing blocks.
			 *
			 * <⃨!⃨-⃨-⃨ /wp:core/paragraph {"dropCap":true} /-->
			 */
			$comment_opening_at = strpos( $text, '<!--', $at );

			/*
			 * Even if the start of a potential block delimiter is not found, the document
			 * might end in a prefix of such, and in that case there is incomplete input.
			 */
			if ( false === $comment_opening_at ) {
				if ( str_ends_with( $text, '<!-' ) ) {
					$backup = 3;
				} elseif ( str_ends_with( $text, '<!' ) ) {
					$backup = 2;
				} elseif ( str_ends_with( $text, '<' ) ) {
					$backup = 1;
				} else {
					$backup = 0;
				}

				// Whether or not there is a potential delimiter, there might be an HTML span.
				if ( $after_prev_delimiter < ( $end - $backup ) ) {
					$this->state                    = self::HTML_SPAN;
					$this->after_previous_delimiter = $after_prev_delimiter;
					$this->matched_delimiter_at     = $end - $backup;
					$this->matched_delimiter_length = $backup;
					$this->open_blocks_at[]         = $after_prev_delimiter;
					$this->open_blocks_length[]     = 0;
					$this->was_void                 = true;
					return true;
				}

				/*
				 * In the case that there is the start of an HTML comment, it means that there
				 * might be a block delimiter, but it’s not possible know, therefore it’s incomplete.
				 */
				if ( $backup > 0 ) {
					goto incomplete;
				}

				// Otherwise this is the end.
				$this->state = self::COMPLETE;
				return false;
			}

			// <!-- ⃨/wp:core/paragraph {"dropCap":true} /-->
			$opening_whitespace_at = $comment_opening_at + 4;
			if ( $opening_whitespace_at >= $end ) {
				goto incomplete;
			}

			$opening_whitespace_length = strspn( $text, " \t\f\r\n", $opening_whitespace_at );

			/*
			 * The `wp` prefix cannot come before this point, but it may come after it
			 * depending on the presence of the closer. This is detected next.
			 */
			$wp_prefix_at = $opening_whitespace_at + $opening_whitespace_length;
			if ( $wp_prefix_at >= $end ) {
				goto incomplete;
			}

			if ( 0 === $opening_whitespace_length ) {
				$at = $this->find_html_comment_end( $comment_opening_at, $end );
				continue;
			}

			// <!-- /⃨wp:core/paragraph {"dropCap":true} /-->
			$has_closer = false;
			if ( '/' === $text[ $wp_prefix_at ] ) {
				$has_closer = true;
				++$wp_prefix_at;
			}

			// <!-- /w⃨p⃨:⃨core/paragraph {"dropCap":true} /-->
			if ( $wp_prefix_at < $end && 0 !== substr_compare( $text, 'wp:', $wp_prefix_at, 3 ) ) {
				if (
					( $wp_prefix_at + 2 >= $end && str_ends_with( $text, 'wp' ) ) ||
					( $wp_prefix_at + 1 >= $end && str_ends_with( $text, 'w' ) )
				) {
					goto incomplete;
				}

				$at = $this->find_html_comment_end( $comment_opening_at, $end );
				continue;
			}

			/*
			 * If the block contains no namespace, this will end up masquerading with
			 * the block name. It’s easier to first detect the span and then determine
			 * if it’s a namespace of a name.
			 *
			 * <!-- /wp:c⃨o⃨r⃨e⃨/paragraph {"dropCap":true} /-->
			 */
			$namespace_at = $wp_prefix_at + 3;
			if ( $namespace_at >= $end ) {
				goto incomplete;
			}

			$start_of_namespace = $text[ $namespace_at ];

			// The namespace must start with a-z.
			if ( 'a' > $start_of_namespace || 'z' < $start_of_namespace ) {
				$at = $this->find_html_comment_end( $comment_opening_at, $end );
				continue;
			}

			$namespace_length = 1 + strspn( $text, 'abcdefghijklmnopqrstuvwxyz0123456789-_', $namespace_at + 1 );
			$separator_at     = $namespace_at + $namespace_length;
			if ( $separator_at >= $end ) {
				goto incomplete;
			}

			// <!-- /wp:core/⃨paragraph {"dropCap":true} /-->
			$has_separator = '/' === $text[ $separator_at ];
			if ( $has_separator ) {
				$name_at = $separator_at + 1;

				if ( $name_at >= $end ) {
					goto incomplete;
				}

				// <!-- /wp:core/p⃨a⃨r⃨a⃨g⃨r⃨a⃨p⃨h⃨ {"dropCap":true} /-->
				$start_of_name = $text[ $name_at ];
				if ( 'a' > $start_of_name || 'z' < $start_of_name ) {
					$at = $this->find_html_comment_end( $comment_opening_at, $end );
					continue;
				}

				$name_length = 1 + strspn( $text, 'abcdefghijklmnopqrstuvwxyz0123456789-_', $name_at + 1 );
			} else {
				$name_at     = $namespace_at;
				$name_length = $namespace_length;
			}

			if ( $name_at + $name_length >= $end ) {
				goto incomplete;
			}

			/*
			 * For this next section of the delimiter, it could be the JSON attributes
			 * or it could be the end of the comment. Assume that the JSON is there and
			 * update if it’s not.
			 */

			// <!-- /wp:core/paragraph ⃨{"dropCap":true} /-->
			$after_name_whitespace_at     = $name_at + $name_length;
			$after_name_whitespace_length = strspn( $text, " \t\f\r\n", $after_name_whitespace_at );
			$json_at                      = $after_name_whitespace_at + $after_name_whitespace_length;

			if ( $json_at >= $end ) {
				goto incomplete;
			}

			if ( 0 === $after_name_whitespace_length ) {
				$at = $this->find_html_comment_end( $comment_opening_at, $end );
				continue;
			}

			// <!-- /wp:core/paragraph {⃨"dropCap":true} /-->
			$has_json    = '{' === $text[ $json_at ];
			$json_length = 0;

			/*
			 * For the final span of the delimiter it's most efficient to find the end of the
			 * HTML comment and work backwards. This prevents complicated parsing inside the
			 * JSON span, which is not allowed to contain the HTML comment terminator.
			 *
			 * This also matches the behavior in the official block parser,
			 * even though it allows for matching invalid JSON content.
			 *
			 * <!-- /wp:core/paragraph {"dropCap":true} /-⃨-⃨>⃨
			 */
			$comment_closing_at = strpos( $text, '-->', $json_at );
			if ( false === $comment_closing_at ) {
				goto incomplete;
			}

			// <!-- /wp:core/paragraph {"dropCap":true} /⃨-->
			if ( '/' === $text[ $comment_closing_at - 1 ] ) {
				$has_void_flag    = true;
				$void_flag_length = 1;
			} else {
				$has_void_flag    = false;
				$void_flag_length = 0;
			}

			/*
			 * If there's no JSON, then the span of text after the name
			 * until the comment closing must be completely whitespace.
			 * Otherwise it’s a normal HTML comment.
			 */
			if ( ! $has_json ) {
				if ( $after_name_whitespace_at + $after_name_whitespace_length === $comment_closing_at - $void_flag_length ) {
					// This must be a block delimiter!
					$this->state = self::MATCHED;
					break;
				}

				$at = $this->find_html_comment_end( $comment_opening_at, $end );
				continue;
			}

			/*
			 * There's JSON, so attempt to find its boundary.
			 *
			 * @todo It’s likely faster to scan forward instead of in reverse.
			 *
			 * <!-- /wp:core/paragraph {"dropCap":true}⃨ ⃨/-->
			 */
			$after_json_whitespace_length = 0;
			for ( $char_at = $comment_closing_at - $void_flag_length - 1; $char_at > $json_at; $char_at-- ) {
				$char = $text[ $char_at ];

				switch ( $char ) {
					case ' ':
					case "\t":
					case "\f":
					case "\r":
					case "\n":
						++$after_json_whitespace_length;
						continue 2;

					case '}':
						$json_length = $char_at - $json_at + 1;
						break 2;

					default:
						++$at;
						continue 3;
				}
			}

			/*
			 * This covers cases where there is no terminating “}” or where
			 * mandatory whitespace is missing.
			 */
			if ( 0 === $json_length || 0 === $after_json_whitespace_length ) {
				$at = $this->find_html_comment_end( $comment_opening_at, $end );
				continue;
			}

			// This must be a block delimiter!
			$this->state = self::MATCHED;
			break;
		}

		// The end of the document was reached without a match.
		if ( self::MATCHED !== $this->state ) {
			$this->state = self::COMPLETE;
			return false;
		}

		/*
		 * From this point forward, a delimiter has been matched. There
		 * might also be an HTML span that appears before the delimiter.
		 */

		$this->after_previous_delimiter = $after_prev_delimiter;

		$this->matched_delimiter_at     = $comment_opening_at;
		$this->matched_delimiter_length = $comment_closing_at + 3 - $comment_opening_at;

		$this->namespace_at = $namespace_at;
		$this->name_at      = $name_at;
		$this->name_length  = $name_length;

		$this->json_at     = $json_at;
		$this->json_length = $json_length;

		/*
		 * When delimiters contain both the void flag and the closing flag
		 * they shall be interpreted as void blocks, per the spec parser.
		 */
		if ( $has_void_flag ) {
			$this->type          = self::VOID;
			$this->next_stack_op = 'void';
		} elseif ( $has_closer ) {
			$this->type          = self::CLOSER;
			$this->next_stack_op = 'pop';

			/*
			 * @todo Check if the name matches and bail according to the spec parser.
			 *       The default parser doesn’t examine the names.
			 */
		} else {
			$this->type          = self::OPENER;
			$this->next_stack_op = 'push';
		}

		$this->has_closing_flag = $has_closer;

		// HTML spans are visited before the delimiter that follows them.
		if ( $comment_opening_at > $after_prev_delimiter ) {
			$this->state                = self::HTML_SPAN;
			$this->open_blocks_at[]     = $after_prev_delimiter;
			$this->open_blocks_length[] = 0;
			$this->was_void             = true;

			return true;
		}

		// If there were no HTML spans then flush the enqueued stack operations immediately.
		switch ( $this->next_stack_op ) {
			case 'void':
				$this->was_void             = true;
				$this->open_blocks_at[]     = $namespace_at;
				$this->open_blocks_length[] = $name_at + $name_length - $namespace_at;
				break;

			case 'push':
				$this->open_blocks_at[]     = $namespace_at;
				$this->open_blocks_length[] = $name_at + $name_length - $namespace_at;
				break;

			case 'pop':
				array_pop( $this->open_blocks_at );
				array_pop( $this->open_blocks_length );
				break;
		}

		$this->next_stack_op = null;

		return true;

		incomplete:
		$this->state      = self::COMPLETE;
		$this->last_error = self::INCOMPLETE_INPUT;
		return false;
	}

	/**
	 * Returns an array containing the names of the currently-open blocks, in order
	 * from outermost to innermost, with HTML spans indicated as “#html”.
	 *
	 * Example:
	 *
	 *     // Freeform HTML content is an HTML span.
	 *     $processor = new WP_Block_Processor( 'Just text' );
	 *     $processor->next_token();
	 *     array( '#text' ) === $processor->get_breadcrumbs();
	 *
	 *     $processor = new WP_Block_Processor( '<!-- wp:a --><!-- wp:b --><!-- wp:c /--><!-- /wp:b --><!-- /wp:a -->' );
	 *     $processor->next_token();
	 *     array( 'core/a' ) === $processor->get_breadcrumbs();
	 *     $processor->next_token();
	 *     array( 'core/a', 'core/b' ) === $processor->get_breadcrumbs();
	 *     $processor->next_token();
	 *     // Void blocks are only open while visiting them.
	 *     array( 'core/a', 'core/b', 'core/c' ) === $processor->get_breadcrumbs();
	 *     $processor->next_token();
	 *     // Blocks are closed before visiting their closing delimiter.
	 *     array( 'core/a' ) === $processor->get_breadcrumbs();
	 *     $processor->next_token();
	 *     array() === $processor->get_breadcrumbs();
	 *
	 *     // Inner HTML is also an HTML span.
	 *     $processor = new WP_Block_Processor( '<!-- wp:a -->Inner HTML<!-- /wp:a -->' );
	 *     $processor->next_token();
	 *     $processor->next_token();
	 *     array( 'core/a', '#html' ) === $processor->get_breadcrumbs();
	 *
	 * @since 6.9.0
	 *
	 * @return string[]
	 */
	public function get_breadcrumbs(): array {
		$breadcrumbs = array_fill( 0, count( $this->open_blocks_at ), null );

		/*
		 * Since HTML spans can only be at the very end, set the normalized block name for
		 * each open element and then work backwards after creating the array. This allows
		 * for the elimination of a conditional on each iteration of the loop.
		 */
		foreach ( $this->open_blocks_at as $i => $at ) {
			$block_type        = substr( $this->source_text, $at, $this->open_blocks_length[ $i ] );
			$breadcrumbs[ $i ] = self::normalize_block_type( $block_type );
		}

		if ( isset( $i ) && 0 === $this->open_blocks_length[ $i ] ) {
			$breadcrumbs[ $i ] = '#html';
		}

		return $breadcrumbs;
	}

	/**
	 * Returns the depth of the open blocks where the processor is currently matched.
	 *
	 * Depth increases before visiting openers and void blocks and decreases before
	 * visiting closers. HTML spans behave like void blocks.
	 *
	 * @since 6.9.0
	 *
	 * @return int
	 */
	public function get_depth(): int {
		return count( $this->open_blocks_at );
	}

	/**
	 * Extracts a block object, and all inner content, starting at a matched opening
	 * block delimiter, or at a matched top-level HTML span as freeform HTML content.
	 *
	 * Use this function to extract some blocks within a document, but not all. For example,
	 * one might want to find image galleries, parse them, modify them, and then reserialize
	 * them in place.
	 *
	 * Once this function returns, the parser will be matched on token following the close
	 * of the given block.
	 *
	 * The return type of this method is compatible with the return of {@see \parse_blocks()}.
	 *
	 * Example:
	 *
	 *     $processor = new WP_Block_Processor( $post_content );
	 *     if ( ! $processor->next_block( 'gallery' ) ) {
	 *         return $post_content;
	 *     }
	 *
	 *     $gallery_at  = $processor->get_span()->start;
	 *     $gallery     = $processor->extract_full_block_and_advance();
	 *     $ends_before = $processor->get_span();
	 *     $ends_before = $ends_before->start ?? strlen( $post_content );
	 *
	 *     $new_gallery = update_gallery( $gallery );
	 *     $new_gallery = serialize_block( $new_gallery );
	 *
	 *     return (
	 *         substr( $post_content, 0, $gallery_at ) .
	 *         $new_gallery .
	 *         substr( $post_content, $ends_before )
	 *     );
	 *
	 * @since 6.9.0
	 *
	 * @return array[]|null {
	 *     Array of block structures.
	 *
	 *     @type array ...$0 {
	 *         An associative array of a single parsed block object. See WP_Block_Parser_Block.
	 *
	 *         @type string|null $blockName    Name of block.
	 *         @type array       $attrs        Attributes from block comment delimiters.
	 *         @type array[]     $innerBlocks  List of inner blocks. An array of arrays that
	 *                                         have the same structure as this one.
	 *         @type string      $innerHTML    HTML from inside block comment delimiters.
	 *         @type array       $innerContent List of string fragments and null markers where
	 *                                         inner blocks were found.
	 *     }
	 * }
	 */
	public function extract_full_block_and_advance(): ?array {
		if ( $this->is_html() ) {
			$chunk = $this->get_html_content();

			return array(
				'blockName'    => null,
				'attrs'        => array(),
				'innerBlocks'  => array(),
				'innerHTML'    => $chunk,
				'innerContent' => array( $chunk ),
			);
		}

		$block = array(
			'blockName'    => $this->get_block_type(),
			'attrs'        => $this->allocate_and_return_parsed_attributes() ?? array(),
			'innerBlocks'  => array(),
			'innerHTML'    => '',
			'innerContent' => array(),
		);

		$depth = $this->get_depth();
		while ( $this->next_token() && $this->get_depth() > $depth ) {
			if ( $this->is_html() ) {
				$chunk                   = $this->get_html_content();
				$block['innerHTML']     .= $chunk;
				$block['innerContent'][] = $chunk;
				continue;
			}

			/**
			 * Inner blocks.
			 *
			 * @todo This is a decent place to call {@link \render_block()}
			 * @todo Use iteration instead of recursion, or at least refactor to tail-call form.
			 */
			if ( $this->opens_block() ) {
				$inner_block             = $this->extract_full_block_and_advance();
				$block['innerBlocks'][]  = $inner_block;
				$block['innerContent'][] = null;
			}

			/*
			 * Because the parser has advanced past the closing block token, it
			 * may be matched on an HTML span. This needs to be processed before
			 * moving on to the next token at the start of the next loop iteration.
			 */
			if ( $this->is_html() ) {
				$chunk                   = $this->get_html_content();
				$block['innerHTML']     .= $chunk;
				$block['innerContent'][] = $chunk;
			}
		}

		return $block;
	}

	/**
	 * Returns the byte-offset after the ending character of an HTML comment,
	 * assuming the proper starting byte offset.
	 *
	 * @since 6.9.0
	 *
	 * @param int $comment_starting_at Where the HTML comment started, the leading `<`.
	 * @param int $search_end          Last offset in which to search, for limiting search span.
	 * @return int Offset after the current HTML comment ends, or `$search_end` if no end was found.
	 */
	private function find_html_comment_end( int $comment_starting_at, int $search_end ): int {
		$text = $this->source_text;

		// Find span-of-dashes comments which look like `<!----->`.
		$span_of_dashes = strspn( $text, '-', $comment_starting_at + 2 );
		if (
			$comment_starting_at + 2 + $span_of_dashes < $search_end &&
			'>' === $text[ $comment_starting_at + 2 + $span_of_dashes ]
		) {
			return $comment_starting_at + $span_of_dashes + 1;
		}

		// Otherwise, there are other characters inside the comment, find the first `-->` or `--!>`.
		$now_at = $comment_starting_at + 4;
		while ( $now_at < $search_end ) {
			$dashes_at = strpos( $text, '--', $now_at );
			if ( false === $dashes_at ) {
				return $search_end;
			}

			$closer_must_be_at = $dashes_at + 2 + strspn( $text, '-', $dashes_at + 2 );
			if ( $closer_must_be_at < $search_end && '!' === $text[ $closer_must_be_at ] ) {
				++$closer_must_be_at;
			}

			if ( $closer_must_be_at < $search_end && '>' === $text[ $closer_must_be_at ] ) {
				return $closer_must_be_at + 1;
			}

			++$now_at;
		}

		return $search_end;
	}

	/**
	 * Indicates if the last attempt to parse a block comment delimiter
	 * failed, if set, otherwise `null` if the last attempt succeeded.
	 *
	 * @since 6.9.0
	 *
	 * @return string|null Error from last attempt at parsing next block delimiter,
	 *                     or `null` if last attempt succeeded.
	 */
	public function get_last_error(): ?string {
		return $this->last_error;
	}

	/**
	 * Indicates if the last attempt to parse a block’s JSON attributes failed.
	 *
	 * @see \json_last_error()
	 *
	 * @since 6.9.0
	 *
	 * @return int JSON_ERROR_ code from last attempt to parse block JSON attributes.
	 */
	public function get_last_json_error(): int {
		return $this->last_json_error;
	}

	/**
	 * Returns the type of the block comment delimiter.
	 *
	 * One of:
	 *
	 *  - {@see self::OPENER}
	 *  - {@see self::CLOSER}
	 *  - {@see self::VOID}
	 *  - `null`
	 *
	 * @since 6.9.0
	 *
	 * @return string|null type of the block comment delimiter, if currently matched.
	 */
	public function get_delimiter_type(): ?string {
		switch ( $this->state ) {
			case self::HTML_SPAN:
				return self::VOID;

			case self::MATCHED:
				return $this->type;

			default:
				return null;
		}
	}

	/**
	 * Returns whether the delimiter contains the closing flag.
	 *
	 * This should be avoided except in cases of custom error-handling
	 * with block closers containing the void flag. For normative use,
	 * {@see self::get_delimiter_type()}.
	 *
	 * @since 6.9.0
	 *
	 * @return bool Whether the currently-matched block delimiter contains the closing flag.
	 */
	public function has_closing_flag(): bool {
		return $this->has_closing_flag;
	}

	/**
	 * Indicates if the block delimiter represents a block of the given type.
	 *
	 * Since the “core” namespace may be implicit, it’s allowable to pass
	 * either the fully-qualified block type with namespace and block name
	 * as well as the shorthand version only containing the block name, if
	 * the desired block is in the “core” namespace.
	 *
	 * Since freeform HTML content is non-block content, it has no block type.
	 * Passing the wildcard “*” will, however, return true for all block types,
	 * even the implicit freeform content, though not for spans of inner HTML.
	 *
	 * Example:
	 *
	 *     $is_core_paragraph = $processor->is_block_type( 'paragraph' );
	 *     $is_core_paragraph = $processor->is_block_type( 'core/paragraph' );
	 *     $is_formula        = $processor->is_block_type( 'math-block/formula' );
	 *
	 * @param string $block_type Block type name for the desired block.
	 *                           E.g. "paragraph", "core/paragraph", "math-blocks/formula".
	 * @return bool Whether this delimiter represents a block of the given type.
	 */
	public function is_block_type( string $block_type ): bool {
		if ( '*' === $block_type ) {
			return true;
		}

		if ( $this->is_html() ) {
			// This is a core/freeform text block, it’s special.
			if ( 0 === ( $this->open_blocks_length[0] ?? null ) ) {
				return (
					'core/freeform' === $block_type ||
					'freeform' === $block_type
				);
			}

			// Otherwise this is innerHTML and not a block.
			return false;
		}

		return $this->are_equal_block_types( $this->source_text, $this->namespace_at, $this->name_at - $this->namespace_at + $this->name_length, $block_type, 0, strlen( $block_type ) );
	}

	/**
	 * Given two spans of text, indicate if they represent identical block types.
	 *
	 * This function normalizes block types to account for implicit core namespacing.
	 *
	 * Note! This function only returns valid results when the complete block types are
	 *       represented in the span offsets and lengths. This means that the full optional
	 *       namespace and block name must be represented in the input arguments.
	 *
	 * Example:
	 *
	 *              0    5   10   15   20   25   30   35   40
	 *     $text = '<!-- wp:block --><!-- /wp:core/block -->';
	 *
	 *     true  === WP_Block_Processor::are_equal_block_types( $text, 9, 5, $text, 27, 10 );
	 *     false === WP_Block_Processor::are_equal_block_types( $text, 9, 5, 'my/block', 0, 8 );
	 *
	 * @since 6.9.0
	 *
	 * @param string $a_text   Text in which first block type appears.
	 * @param int    $a_at     Byte offset into text in which first block type starts.
	 * @param int    $a_length Byte length of first block type.
	 * @param string $b_text   Text in which second block type appears (may be the same as the first text).
	 * @param int    $b_at     Byte offset into text in which second block type starts.
	 * @param int    $b_length Byte length of second block type.
	 * @return bool Whether the spans of text represent identical block types, normalized for namespacing.
	 */
	public static function are_equal_block_types( string $a_text, int $a_at, int $a_length, string $b_text, int $b_at, int $b_length ): bool {
		$a_ns_length = strcspn( $a_text, '/', $a_at, $a_length );
		$b_ns_length = strcspn( $b_text, '/', $b_at, $b_length );

		$a_has_ns = $a_ns_length !== $a_length;
		$b_has_ns = $b_ns_length !== $b_length;

		// Both contain namespaces.
		if ( $a_has_ns && $b_has_ns ) {
			if ( $a_length !== $b_length ) {
				return false;
			}

			$a_block_type = substr( $a_text, $a_at, $a_length );

			return 0 === substr_compare( $b_text, $a_block_type, $b_at, $b_length );
		}

		if ( $a_has_ns ) {
			$b_block_type = 'core/' . substr( $b_text, $b_at, $b_length );

			return (
				strlen( $b_block_type ) === $a_length &&
				0 === substr_compare( $a_text, $b_block_type, $a_at, $a_length )
			);
		}

		if ( $b_has_ns ) {
			$a_block_type = 'core/' . substr( $a_text, $a_at, $a_length );

			return (
				strlen( $a_block_type ) === $b_length &&
				0 === substr_compare( $b_text, $a_block_type, $b_at, $b_length )
			);
		}

		// Neither contains a namespace.
		if ( $a_length !== $b_length ) {
			return false;
		}

		$a_name = substr( $a_text, $a_at, $a_length );

		return 0 === substr_compare( $b_text, $a_name, $b_at, $b_length );
	}

	/**
	 * Indicates if the matched delimiter is an opening or void delimiter of the given type,
	 * if a type is provided, otherwise if it opens any block or implicit freeform HTML content.
	 *
	 * This is a helper method to ease handling of code inspecting where blocks start, and for
	 * checking if the blocks are of a given type. The function is variadic to allow for
	 * checking if the delimiter opens one of many possible block types.
	 *
	 * To advance to the start of a block {@see self::next_block()}.
	 *
	 * Example:
	 *
	 *     $processor = new WP_Block_Processor( $html );
	 *     while ( $processor->next_delimiter() ) {
	 *         if ( $processor->opens_block( 'core/code', 'syntaxhighlighter/code' ) ) {
	 *             echo "Found code!";
	 *             continue;
	 *         }
	 *
	 *         if ( $processor->opens_block( 'core/image' ) ) {
	 *             echo "Found an image!";
	 *             continue;
	 *         }
	 *
	 *         if ( $processor->opens_block() ) {
	 *             echo "Found a new block!";
	 *         }
	 *     }
	 *
	 * @since 6.9.0
	 *
	 * @see self::is_block_type()
	 *
	 * @param string[] $block_type Optional. Is the matched block type one of these?
	 *                             If none are provided, will not test block type.
	 * @return bool Whether the matched block delimiter opens a block, and whether it
	 *              opens a block of one of the given block types, if provided.
	 */
	public function opens_block( string ...$block_type ): bool {
		// HTML spans only open implicit freeform content at the top level.
		if ( self::HTML_SPAN === $this->state && 1 !== count( $this->open_blocks_at ) ) {
			return false;
		}

		/*
		 * Because HTML spans are discovered after the next delimiter is found,
		 * the delimiter type when visiting HTML spans refers to the type of the
		 * following delimiter. Therefore the HTML case is handled by checking
		 * the state and depth of the stack of open block.
		 */
		if ( self::CLOSER === $this->type && ! $this->is_html() ) {
			return false;
		}

		if ( count( $block_type ) === 0 ) {
			return true;
		}

		foreach ( $block_type as $block ) {
			if ( $this->is_block_type( $block ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Indicates if the matched delimiter is an HTML span.
	 *
	 * @since 6.9.0
	 *
	 * @see self::is_non_whitespace_html()
	 *
	 * @return bool Whether the processor is matched on an HTML span.
	 */
	public function is_html(): bool {
		return self::HTML_SPAN === $this->state;
	}

	/**
	 * Indicates if the matched delimiter is an HTML span and comprises more
	 * than whitespace characters, i.e. contains real content.
	 *
	 * Many block serializers introduce newlines between block delimiters,
	 * so the presence of top-level non-block content does not imply that
	 * there are “real” freeform HTML blocks. Checking if there is content
	 * beyond whitespace is a more certain check, such as for determining
	 * whether to load CSS for the freeform or fallback block type.
	 *
	 * @since 6.9.0
	 *
	 * @see self::is_html()
	 *
	 * @return bool Whether the currently-matched delimiter is an HTML
	 *              span containing non-whitespace text.
	 */
	public function is_non_whitespace_html(): bool {
		if ( ! $this->is_html() ) {
			return false;
		}

		$length = $this->matched_delimiter_at - $this->after_previous_delimiter;

		$whitespace_length = strspn(
			$this->source_text,
			" \t\f\r\n",
			$this->after_previous_delimiter,
			$length
		);

		return $whitespace_length !== $length;
	}

	/**
	 * Returns the string content of a matched HTML span, or `null` otherwise.
	 *
	 * @since 6.9.0
	 *
	 * @return string|null Raw HTML content, or `null` if not currently matched on HTML.
	 */
	public function get_html_content(): ?string {
		if ( ! $this->is_html() ) {
			return null;
		}

		return substr(
			$this->source_text,
			$this->after_previous_delimiter,
			$this->matched_delimiter_at - $this->after_previous_delimiter
		);
	}

	/**
	 * Allocates a substring for the block type and returns the fully-qualified
	 * name, including the namespace, if matched on a delimiter, otherwise `null`.
	 *
	 * This function is like {@see self::get_printable_block_type()} but when
	 * paused on a freeform HTML block, will return `null` instead of “core/freeform”.
	 * The `null` behavior matches what {@see \parse_blocks()} returns but may not
	 * be as useful as having a string value.
	 *
	 * This function allocates a substring for the given block type. This
	 * allocation will be small and likely fine in most cases, but it's
	 * preferable to call {@see self::is_block_type()} if only needing
	 * to know whether the delimiter is for a given block type, as that
	 * function is more efficient for this purpose and avoids the allocation.
	 *
	 * Example:
	 *
	 *     // Avoid.
	 *     'core/paragraph' = $processor->get_block_type();
	 *
	 *     // Prefer.
	 *     $processor->is_block_type( 'core/paragraph' );
	 *     $processor->is_block_type( 'paragraph' );
	 *     $processor->is_block_type( 'core/freeform' );
	 *
	 *     // Freeform HTML content has no block type.
	 *     $processor = new WP_Block_Processor( 'non-block content' );
	 *     $processor->next_token();
	 *     null === $processor->get_block_type();
	 *
	 * @since 6.9.0
	 *
	 * @see self::are_equal_block_types()
	 *
	 * @return string|null Fully-qualified block namespace and type, e.g. "core/paragraph",
	 *                     if matched on an explicit delimiter, otherwise `null`.
	 */
	public function get_block_type(): ?string {
		if (
			self::READY === $this->state ||
			self::COMPLETE === $this->state ||
			self::INCOMPLETE_INPUT === $this->state
		) {
			return null;
		}

		// This is a core/freeform text block, it’s special.
		if ( $this->is_html() ) {
			return null;
		}

		$block_type = substr( $this->source_text, $this->namespace_at, $this->name_at - $this->namespace_at + $this->name_length );
		return self::normalize_block_type( $block_type );
	}

	/**
	 * Allocates a printable substring for the block type and returns the fully-qualified
	 * name, including the namespace, if matched on a delimiter or freeform block, otherwise `null`.
	 *
	 * This function is like {@see self::get_block_type()} but when paused on a freeform
	 * HTML block, will return “core/freeform” instead of `null`. The `null` behavior matches
	 * what {@see \parse_blocks()} returns but may not be as useful as having a string value.
	 *
	 * This function allocates a substring for the given block type. This
	 * allocation will be small and likely fine in most cases, but it's
	 * preferable to call {@see self::is_block_type()} if only needing
	 * to know whether the delimiter is for a given block type, as that
	 * function is more efficient for this purpose and avoids the allocation.
	 *
	 * Example:
	 *
	 *     // Avoid.
	 *     'core/paragraph' = $processor->get_printable_block_type();
	 *
	 *     // Prefer.
	 *     $processor->is_block_type( 'core/paragraph' );
	 *     $processor->is_block_type( 'paragraph' );
	 *     $processor->is_block_type( 'core/freeform' );
	 *
	 *     // Freeform HTML content is given an implicit type.
	 *     $processor = new WP_Block_Processor( 'non-block content' );
	 *     $processor->next_token();
	 *     'core/freeform' === $processor->get_printable_block_type();
	 *
	 * @since 6.9.0
	 *
	 * @see self::are_equal_block_types()
	 *
	 * @return string|null Fully-qualified block namespace and type, e.g. "core/paragraph",
	 *                     if matched on an explicit delimiter or freeform block, otherwise `null`.
	 */
	public function get_printable_block_type(): ?string {
		if (
			self::READY === $this->state ||
			self::COMPLETE === $this->state ||
			self::INCOMPLETE_INPUT === $this->state
		) {
			return null;
		}

		// This is a core/freeform text block, it’s special.
		if ( $this->is_html() ) {
			return 1 === count( $this->open_blocks_at )
				? 'core/freeform'
				: '#innerHTML';
		}

		$block_type = substr( $this->source_text, $this->namespace_at, $this->name_at - $this->namespace_at + $this->name_length );
		return self::normalize_block_type( $block_type );
	}

	/**
	 * Normalizes a block name to ensure that missing implicit “core” namespaces are present.
	 *
	 * Example:
	 *
	 *     'core/paragraph' === WP_Block_Processor::normalize_block_byte( 'paragraph' );
	 *     'core/paragraph' === WP_Block_Processor::normalize_block_byte( 'core/paragraph' );
	 *     'my/paragraph'   === WP_Block_Processor::normalize_block_byte( 'my/paragraph' );
	 *
	 * @since 6.9.0
	 *
	 * @param string $block_type Valid block name, potentially without a namespace.
	 * @return string Fully-qualified block type including namespace.
	 */
	public static function normalize_block_type( string $block_type ): string {
		return false === strpos( $block_type, '/' )
			? "core/{$block_type}"
			: $block_type;
	}

	/**
	 * Returns a lazy wrapper around the block attributes, which can be used
	 * for efficiently interacting with the JSON attributes.
	 *
	 * This stub hints that there should be a lazy interface for parsing
	 * block attributes but doesn’t define it. It serves both as a placeholder
	 * for one to come as well as a guard against implementing an eager
	 * function in its place.
	 *
	 * @throws Exception This function is a stub for subclasses to implement
	 *                   when providing streaming attribute parsing.
	 *
	 * @since 6.9.0
	 *
	 * @see self::allocate_and_return_parsed_attributes()
	 *
	 * @return never
	 */
	public function get_attributes() {
		throw new Exception( 'Lazy attribute parsing not yet supported' );
	}

	/**
	 * Attempts to parse and return the entire JSON attributes from the delimiter,
	 * allocating memory and processing the JSON span in the process.
	 *
	 * This does not return any parsed attributes for a closing block delimiter
	 * even if there is a span of JSON content; this JSON is a parsing error.
	 *
	 * Consider calling {@see static::get_attributes()} instead if it's not
	 * necessary to read all the attributes at the same time, as that provides
	 * a more efficient mechanism for typical use cases.
	 *
	 * Since the JSON span inside the comment delimiter may not be valid JSON,
	 * this function will return `null` if it cannot parse the span and set the
	 * {@see static::get_last_json_error()} to the appropriate JSON_ERROR_ constant.
	 *
	 * If the delimiter contains no JSON span, it will also return `null`,
	 * but the last error will be set to {@see \JSON_ERROR_NONE}.
	 *
	 * Example:
	 *
	 *     $processor = new WP_Block_Processor( '<!-- wp:image {"url": "https://wordpress.org/favicon.ico"} -->' );
	 *     $processor->next_delimiter();
	 *     $memory_hungry_and_slow_attributes = $processor->allocate_and_return_parsed_attributes();
	 *     $memory_hungry_and_slow_attributes === array( 'url' => 'https://wordpress.org/favicon.ico' );
	 *
	 *     $processor = new WP_Block_Processor( '<!-- /wp:image {"url": "https://wordpress.org/favicon.ico"} -->' );
	 *     $processor->next_delimiter();
	 *     null            = $processor->allocate_and_return_parsed_attributes();
	 *     JSON_ERROR_NONE = $processor->get_last_json_error();
	 *
	 *     $processor = new WP_Block_Processor( '<!-- wp:separator {} /-->' );
	 *     $processor->next_delimiter();
	 *     array() === $processor->allocate_and_return_parsed_attributes();
	 *
	 *     $processor = new WP_Block_Processor( '<!-- wp:separator /-->' );
	 *     $processor->next_delimiter();
	 *     null = $processor->allocate_and_return_parsed_attributes();
	 *
	 *     $processor = new WP_Block_Processor( '<!-- wp:image {"url} -->' );
	 *     $processor->next_delimiter();
	 *     null                 = $processor->allocate_and_return_parsed_attributes();
	 *     JSON_ERROR_CTRL_CHAR = $processor->get_last_json_error();
	 *
	 * @since 6.9.0
	 *
	 * @return array|null Parsed JSON attributes, if present and valid, otherwise `null`.
	 */
	public function allocate_and_return_parsed_attributes(): ?array {
		$this->last_json_error = JSON_ERROR_NONE;

		if ( self::CLOSER === $this->type || $this->is_html() || 0 === $this->json_length ) {
			return null;
		}

		$json_span = substr( $this->source_text, $this->json_at, $this->json_length );
		$parsed    = json_decode( $json_span, null, 512, JSON_OBJECT_AS_ARRAY | JSON_INVALID_UTF8_SUBSTITUTE );

		$last_error            = json_last_error();
		$this->last_json_error = $last_error;

		return ( JSON_ERROR_NONE === $last_error && is_array( $parsed ) )
			? $parsed
			: null;
	}

	/**
	 * Returns the span representing the currently-matched delimiter, if matched, otherwise `null`.
	 *
	 * Example:
	 *
	 *     $processor = new WP_Block_Processor( '<!-- wp:void /-->' );
	 *     null     === $processor->get_span();
	 *
	 *     $processor->next_delimiter();
	 *     WP_HTML_Span( 0, 17 ) === $processor->get_span();
	 *
	 * @since 6.9.0
	 *
	 * @return WP_HTML_Span|null Span of text in source text spanning matched delimiter.
	 */
	public function get_span(): ?WP_HTML_Span {
		switch ( $this->state ) {
			case self::HTML_SPAN:
				return new WP_HTML_Span( $this->after_previous_delimiter, $this->matched_delimiter_at - $this->after_previous_delimiter );

			case self::MATCHED:
				return new WP_HTML_Span( $this->matched_delimiter_at, $this->matched_delimiter_length );

			default:
				return null;
		}
	}

	//
	// Constant declarations that would otherwise pollute the top of the class.
	//

	/**
	 * Indicates that the block comment delimiter closes an open block.
	 *
	 * @see self::$type
	 *
	 * @since 6.9.0
	 */
	const CLOSER = 'closer';

	/**
	 * Indicates that the block comment delimiter opens a block.
	 *
	 * @see self::$type
	 *
	 * @since 6.9.0
	 */
	const OPENER = 'opener';

	/**
	 * Indicates that the block comment delimiter represents a void block
	 * with no inner content of any kind.
	 *
	 * @see self::$type
	 *
	 * @since 6.9.0
	 */
	const VOID = 'void';

	/**
	 * Indicates that the processor is ready to start parsing but hasn’t yet begun.
	 *
	 * @see self::$state
	 *
	 * @since 6.9.0
	 */
	const READY = 'processor-ready';

	/**
	 * Indicates that the processor is matched on an explicit block delimiter.
	 *
	 * @see self::$state
	 *
	 * @since 6.9.0
	 */
	const MATCHED = 'processor-matched';

	/**
	 * Indicates that the processor is matched on the opening of an implicit freeform delimiter.
	 *
	 * @see self::$state
	 *
	 * @since 6.9.0
	 */
	const HTML_SPAN = 'processor-html-span';

	/**
	 * Indicates that the parser started parsing a block comment delimiter, but
	 * the input document ended before it could finish. The document was likely truncated.
	 *
	 * @see self::$state
	 *
	 * @since 6.9.0
	 */
	const INCOMPLETE_INPUT = 'incomplete-input';

	/**
	 * Indicates that the processor has finished parsing and has nothing left to scan.
	 *
	 * @see self::$state
	 *
	 * @since 6.9.0
	 */
	const COMPLETE = 'processor-complete';
}

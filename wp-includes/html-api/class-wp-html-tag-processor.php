<?php
/**
 * Scans through an HTML document to find specific tags, then
 * transforms those tags by adding, removing, or updating the
 * values of the HTML attributes within that tag (opener).
 *
 * Does not fully parse HTML or _recurse_ into the HTML structure
 * Instead this scans linearly through a document and only parses
 * the HTML tag openers.
 *
 * ### Possible future direction for this module
 *
 *  - Prune the whitespace when removing classes/attributes: e.g. "a b c" -> "c" not " c".
 *    This would increase the size of the changes for some operations but leave more
 *    natural-looking output HTML.
 *  - Decode HTML character references within class names when matching. E.g. match having
 *    class `1<"2` needs to recognize `class="1&lt;&quot;2"`. Currently the Tag Processor
 *    will fail to find the right tag if the class name is encoded as such.
 *  - Properly decode HTML character references in `get_attribute()`. PHP's
 *    `html_entity_decode()` is wrong in a couple ways: it doesn't account for the
 *    no-ambiguous-ampersand rule, and it improperly handles the way semicolons may
 *    or may not terminate a character reference.
 *
 * @package WordPress
 * @subpackage HTML-API
 * @since 6.2.0
 */

/**
 * Modifies attributes in an HTML document for tags matching a query.
 *
 * ## Usage
 *
 * Use of this class requires three steps:
 *
 *  1. Create a new class instance with your input HTML document.
 *  2. Find the tag(s) you are looking for.
 *  3. Request changes to the attributes in those tag(s).
 *
 * Example:
 * ```php
 *     $tags = new WP_HTML_Tag_Processor( $html );
 *     if ( $tags->next_tag( 'option' ) ) {
 *         $tags->set_attribute( 'selected', true );
 *     }
 * ```
 *
 * ### Finding tags
 *
 * The `next_tag()` function moves the internal cursor through
 * your input HTML document until it finds a tag meeting any of
 * the supplied restrictions in the optional query argument. If
 * no argument is provided then it will find the next HTML tag,
 * regardless of what kind it is.
 *
 * If you want to _find whatever the next tag is_:
 * ```php
 *     $tags->next_tag();
 * ```
 *
 * | Goal                                                      | Query                                                                           |
 * |-----------------------------------------------------------|---------------------------------------------------------------------------------|
 * | Find any tag.                                             | `$tags->next_tag();`                                                            |
 * | Find next image tag.                                      | `$tags->next_tag( array( 'tag_name' => 'img' ) );`                              |
 * | Find next image tag (without passing the array).          | `$tags->next_tag( 'img' );`                                                     |
 * | Find next tag containing the `fullwidth` CSS class.       | `$tags->next_tag( array( 'class_name' => 'fullwidth' ) );`                      |
 * | Find next image tag containing the `fullwidth` CSS class. | `$tags->next_tag( array( 'tag_name' => 'img', 'class_name' => 'fullwidth' ) );` |
 *
 * If a tag was found meeting your criteria then `next_tag()`
 * will return `true` and you can proceed to modify it. If it
 * returns `false`, however, it failed to find the tag and
 * moved the cursor to the end of the file.
 *
 * Once the cursor reaches the end of the file the processor
 * is done and if you want to reach an earlier tag you will
 * need to recreate the processor and start over, as it's
 * unable to back up or move in reverse.
 *
 * See the section on bookmarks for an exception to this
 * no-backing-up rule.
 *
 * #### Custom queries
 *
 * Sometimes it's necessary to further inspect an HTML tag than
 * the query syntax here permits. In these cases one may further
 * inspect the search results using the read-only functions
 * provided by the processor or external state or variables.
 *
 * Example:
 * ```php
 *     // Paint up to the first five DIV or SPAN tags marked with the "jazzy" style.
 *     $remaining_count = 5;
 *     while ( $remaining_count > 0 && $tags->next_tag() ) {
 *         if (
 *              ( 'DIV' === $tags->get_tag() || 'SPAN' === $tags->get_tag() ) &&
 *              'jazzy' === $tags->get_attribute( 'data-style' )
 *         ) {
 *             $tags->add_class( 'theme-style-everest-jazz' );
 *             $remaining_count--;
 *         }
 *     }
 * ```
 *
 * `get_attribute()` will return `null` if the attribute wasn't present
 * on the tag when it was called. It may return `""` (the empty string)
 * in cases where the attribute was present but its value was empty.
 * For boolean attributes, those whose name is present but no value is
 * given, it will return `true` (the only way to set `false` for an
 * attribute is to remove it).
 *
 * ### Modifying HTML attributes for a found tag
 *
 * Once you've found the start of an opening tag you can modify
 * any number of the attributes on that tag. You can set a new
 * value for an attribute, remove the entire attribute, or do
 * nothing and move on to the next opening tag.
 *
 * Example:
 * ```php
 *     if ( $tags->next_tag( array( 'class' => 'wp-group-block' ) ) ) {
 *         $tags->set_attribute( 'title', 'This groups the contained content.' );
 *         $tags->remove_attribute( 'data-test-id' );
 *     }
 * ```
 *
 * If `set_attribute()` is called for an existing attribute it will
 * overwrite the existing value. Similarly, calling `remove_attribute()`
 * for a non-existing attribute has no effect on the document. Both
 * of these methods are safe to call without knowing if a given attribute
 * exists beforehand.
 *
 * ### Modifying CSS classes for a found tag
 *
 * The tag processor treats the `class` attribute as a special case.
 * Because it's a common operation to add or remove CSS classes, this
 * interface adds helper methods to make that easier.
 *
 * As with attribute values, adding or removing CSS classes is a safe
 * operation that doesn't require checking if the attribute or class
 * exists before making changes. If removing the only class then the
 * entire `class` attribute will be removed.
 *
 * Example:
 * ```php
 *     // from `<span>Yippee!</span>`
 *     //   to `<span class="is-active">Yippee!</span>`
 *     $tags->add_class( 'is-active' );
 *
 *     // from `<span class="excited">Yippee!</span>`
 *     //   to `<span class="excited is-active">Yippee!</span>`
 *     $tags->add_class( 'is-active' );
 *
 *     // from `<span class="is-active heavy-accent">Yippee!</span>`
 *     //   to `<span class="is-active heavy-accent">Yippee!</span>`
 *     $tags->add_class( 'is-active' );
 *
 *     // from `<input type="text" class="is-active rugby not-disabled" length="24">`
 *     //   to `<input type="text" class="is-active not-disabled" length="24">
 *     $tags->remove_class( 'rugby' );
 *
 *     // from `<input type="text" class="rugby" length="24">`
 *     //   to `<input type="text" length="24">
 *     $tags->remove_class( 'rugby' );
 *
 *     // from `<input type="text" length="24">`
 *     //   to `<input type="text" length="24">
 *     $tags->remove_class( 'rugby' );
 * ```
 *
 * When class changes are enqueued but a direct change to `class` is made via
 * `set_attribute` then the changes to `set_attribute` (or `remove_attribute`)
 * will take precedence over those made through `add_class` and `remove_class`.
 *
 * ### Bookmarks
 *
 * While scanning through the input HTMl document it's possible to set
 * a named bookmark when a particular tag is found. Later on, after
 * continuing to scan other tags, it's possible to `seek` to one of
 * the set bookmarks and then proceed again from that point forward.
 *
 * Because bookmarks create processing overhead one should avoid
 * creating too many of them. As a rule, create only bookmarks
 * of known string literal names; avoid creating "mark_{$index}"
 * and so on. It's fine from a performance standpoint to create a
 * bookmark and update it frequently, such as within a loop.
 *
 * ```php
 *     $total_todos = 0;
 *     while ( $p->next_tag( array( 'tag_name' => 'UL', 'class_name' => 'todo' ) ) ) {
 *         $p->set_bookmark( 'list-start' );
 *         while ( $p->next_tag( array( 'tag_closers' => 'visit' ) ) ) {
 *             if ( 'UL' === $p->get_tag() && $p->is_tag_closer() ) {
 *                 $p->set_bookmark( 'list-end' );
 *                 $p->seek( 'list-start' );
 *                 $p->set_attribute( 'data-contained-todos', (string) $total_todos );
 *                 $total_todos = 0;
 *                 $p->seek( 'list-end' );
 *                 break;
 *             }
 *
 *             if ( 'LI' === $p->get_tag() && ! $p->is_tag_closer() ) {
 *                 $total_todos++;
 *             }
 *         }
 *     }
 * ```
 *
 * ## Design and limitations
 *
 * The Tag Processor is designed to linearly scan HTML documents and tokenize
 * HTML tags and their attributes. It's designed to do this as efficiently as
 * possible without compromising parsing integrity. Therefore it will be
 * slower than some methods of modifying HTML, such as those incorporating
 * over-simplified PCRE patterns, but will not introduce the defects and
 * failures that those methods bring in, which lead to broken page renders
 * and often to security vulnerabilities. On the other hand, it will be faster
 * than full-blown HTML parsers such as DOMDocument and use considerably
 * less memory. It requires a negligible memory overhead, enough to consider
 * it a zero-overhead system.
 *
 * The performance characteristics are maintained by avoiding tree construction
 * and semantic cleanups which are specified in HTML5. Because of this, for
 * example, it's not possible for the Tag Processor to associate any given
 * opening tag with its corresponding closing tag, or to return the inner markup
 * inside an element. Systems may be built on top of the Tag Processor to do
 * this, but the Tag Processor is and should be constrained so it can remain an
 * efficient, low-level, and reliable HTML scanner.
 *
 * The Tag Processor's design incorporates a "garbage-in-garbage-out" philosophy.
 * HTML5 specifies that certain invalid content be transformed into different forms
 * for display, such as removing null bytes from an input document and replacing
 * invalid characters with the Unicode replacement character U+FFFD �. Where errors
 * or transformations exist within the HTML5 specification, the Tag Processor leaves
 * those invalid inputs untouched, passing them through to the final browser to handle.
 * While this implies that certain operations will be non-spec-compliant, such as
 * reading the value of an attribute with invalid content, it also preserves a
 * simplicity and efficiency for handling those error cases.
 *
 * Most operations within the Tag Processor are designed to minimize the difference
 * between an input and output document for any given change. For example, the
 * `add_class` and `remove_class` methods preserve whitespace and the class ordering
 * within the `class` attribute; and when encountering tags with duplicated attributes,
 * the Tag Processor will leave those invalid duplicate attributes where they are but
 * update the proper attribute which the browser will read for parsing its value. An
 * exception to this rule is that all attribute updates store their values as
 * double-quoted strings, meaning that attributes on input with single-quoted or
 * unquoted values will appear in the output with double-quotes.
 *
 * @since 6.2.0
 */
class WP_HTML_Tag_Processor {
	/**
	 * The maximum number of bookmarks allowed to exist at
	 * any given time.
	 *
	 * @see set_bookmark()
	 * @since 6.2.0
	 * @var int
	 */
	const MAX_BOOKMARKS = 10;

	/**
	 * Maximum number of times seek() can be called.
	 * Prevents accidental infinite loops.
	 *
	 * @see seek()
	 * @since 6.2.0
	 * @var int
	 */
	const MAX_SEEK_OPS = 1000;

	/**
	 * The HTML document to parse.
	 *
	 * @since 6.2.0
	 * @var string
	 */
	protected $html;

	/**
	 * The last query passed to next_tag().
	 *
	 * @since 6.2.0
	 * @var array|null
	 */
	private $last_query;

	/**
	 * The tag name this processor currently scans for.
	 *
	 * @since 6.2.0
	 * @var string|null
	 */
	private $sought_tag_name;

	/**
	 * The CSS class name this processor currently scans for.
	 *
	 * @since 6.2.0
	 * @var string|null
	 */
	private $sought_class_name;

	/**
	 * The match offset this processor currently scans for.
	 *
	 * @since 6.2.0
	 * @var int|null
	 */
	private $sought_match_offset;

	/**
	 * Whether to visit tag closers, e.g. </div>, when walking an input document.
	 *
	 * @since 6.2.0
	 * @var bool
	 */
	private $stop_on_tag_closers;

	/**
	 * Holds updated HTML as updates are applied.
	 *
	 * Updates and unmodified portions of the input document are
	 * appended to this value as they are applied. It will hold
	 * a copy of the updated document up until the point of the
	 * latest applied update. The fully-updated HTML document
	 * will comprise this value plus the part of the input document
	 * which follows that latest update.
	 *
	 * @see $bytes_already_copied
	 *
	 * @since 6.2.0
	 * @var string
	 */
	private $output_buffer = '';

	/**
	 * How many bytes from the original HTML document have been read and parsed.
	 *
	 * This value points to the latest byte offset in the input document which
	 * has been already parsed. It is the internal cursor for the Tag Processor
	 * and updates while scanning through the HTML tokens.
	 *
	 * @since 6.2.0
	 * @var int
	 */
	private $bytes_already_parsed = 0;

	/**
	 * How many bytes from the input HTML document have already been
	 * copied into the output buffer.
	 *
	 * Lexical updates are enqueued and processed in batches. Prior
	 * to any given update in the input document, there might exist
	 * a span of HTML unaffected by any changes. This span ought to
	 * be copied verbatim into the output buffer before applying the
	 * following update. This value will point to the starting byte
	 * offset in the input document where that unaffected span of
	 * HTML starts.
	 *
	 * @since 6.2.0
	 * @var int
	 */
	private $bytes_already_copied = 0;

	/**
	 * Byte offset in input document where current tag name starts.
	 *
	 * Example:
	 * ```
	 *   <div id="test">...
	 *   01234
	 *    - tag name starts at 1
	 * ```
	 *
	 * @since 6.2.0
	 * @var int|null
	 */
	private $tag_name_starts_at;

	/**
	 * Byte length of current tag name.
	 *
	 * Example:
	 * ```
	 *   <div id="test">...
	 *   01234
	 *    --- tag name length is 3
	 * ```
	 *
	 * @since 6.2.0
	 * @var int|null
	 */
	private $tag_name_length;

	/**
	 * Byte offset in input document where current tag token ends.
	 *
	 * Example:
	 * ```
	 *   <div id="test">...
	 *   0         1   |
	 *   01234567890123456
	 *    --- tag name ends at 14
	 * ```
	 *
	 * @since 6.2.0
	 * @var int|null
	 */
	private $tag_ends_at;

	/**
	 * Whether the current tag is an opening tag, e.g. <div>, or a closing tag, e.g. </div>.
	 *
	 * @var bool
	 */
	private $is_closing_tag;

	/**
	 * Lazily-built index of attributes found within an HTML tag, keyed by the attribute name.
	 *
	 * Example:
	 * ```php
	 *     // supposing the parser is working through this content
	 *     // and stops after recognizing the `id` attribute
	 *     // <div id="test-4" class=outline title="data:text/plain;base64=asdk3nk1j3fo8">
	 *     //                 ^ parsing will continue from this point
	 *     $this->attributes = array(
	 *         'id' => new WP_HTML_Attribute_Match( 'id', null, 6, 17 )
	 *     );
	 *
	 *     // when picking up parsing again, or when asking to find the
	 *     // `class` attribute we will continue and add to this array
	 *     $this->attributes = array(
	 *         'id'    => new WP_HTML_Attribute_Match( 'id', null, 6, 17 ),
	 *         'class' => new WP_HTML_Attribute_Match( 'class', 'outline', 18, 32 )
	 *     );
	 *
	 *     // Note that only the `class` attribute value is stored in the index.
	 *     // That's because it is the only value used by this class at the moment.
	 * ```
	 *
	 * @since 6.2.0
	 * @var WP_HTML_Attribute_Token[]
	 */
	private $attributes = array();

	/**
	 * Which class names to add or remove from a tag.
	 *
	 * These are tracked separately from attribute updates because they are
	 * semantically distinct, whereas this interface exists for the common
	 * case of adding and removing class names while other attributes are
	 * generally modified as with DOM `setAttribute` calls.
	 *
	 * When modifying an HTML document these will eventually be collapsed
	 * into a single `set_attribute( 'class', $changes )` call.
	 *
	 * Example:
	 * ```php
	 *     // Add the `wp-block-group` class, remove the `wp-group` class.
	 *     $classname_updates = array(
	 *         // Indexed by a comparable class name
	 *         'wp-block-group' => WP_HTML_Tag_Processor::ADD_CLASS,
	 *         'wp-group'       => WP_HTML_Tag_Processor::REMOVE_CLASS
	 *     );
	 * ```
	 *
	 * @since 6.2.0
	 * @var bool[]
	 */
	private $classname_updates = array();

	/**
	 * Tracks a semantic location in the original HTML which
	 * shifts with updates as they are applied to the document.
	 *
	 * @since 6.2.0
	 * @var WP_HTML_Span[]
	 */
	protected $bookmarks = array();

	const ADD_CLASS    = true;
	const REMOVE_CLASS = false;
	const SKIP_CLASS   = null;

	/**
	 * Lexical replacements to apply to input HTML document.
	 *
	 * "Lexical" in this class refers to the part of this class which
	 * operates on pure text _as text_ and not as HTML. There's a line
	 * between the public interface, with HTML-semantic methods like
	 * `set_attribute` and `add_class`, and an internal state that tracks
	 * text offsets in the input document.
	 *
	 * When higher-level HTML methods are called, those have to transform their
	 * operations (such as setting an attribute's value) into text diffing
	 * operations (such as replacing the sub-string from indices A to B with
	 * some given new string). These text-diffing operations are the lexical
	 * updates.
	 *
	 * As new higher-level methods are added they need to collapse their
	 * operations into these lower-level lexical updates since that's the
	 * Tag Processor's internal language of change. Any code which creates
	 * these lexical updates must ensure that they do not cross HTML syntax
	 * boundaries, however, so these should never be exposed outside of this
	 * class or any classes which intentionally expand its functionality.
	 *
	 * These are enqueued while editing the document instead of being immediately
	 * applied to avoid processing overhead, string allocations, and string
	 * copies when applying many updates to a single document.
	 *
	 * Example:
	 * ```php
	 *     // Replace an attribute stored with a new value, indices
	 *     // sourced from the lazily-parsed HTML recognizer.
	 *     $start = $attributes['src']->start;
	 *     $end   = $attributes['src']->end;
	 *     $modifications[] = new WP_HTML_Text_Replacement( $start, $end, $new_value );
	 *
	 *     // Correspondingly, something like this will appear in this array.
	 *     $lexical_updates = array(
	 *         WP_HTML_Text_Replacement( 14, 28, 'https://my-site.my-domain/wp-content/uploads/2014/08/kittens.jpg' )
	 *     );
	 * ```
	 *
	 * @since 6.2.0
	 * @var WP_HTML_Text_Replacement[]
	 */
	protected $lexical_updates = array();

	/**
	 * Tracks and limits `seek()` calls to prevent accidental infinite loops.
	 *
	 * @see seek
	 * @since 6.2.0
	 * @var int
	 */
	protected $seek_count = 0;

	/**
	 * Constructor.
	 *
	 * @since 6.2.0
	 *
	 * @param string $html HTML to process.
	 */
	public function __construct( $html ) {
		$this->html = $html;
	}

	/**
	 * Finds the next tag matching the $query.
	 *
	 * @since 6.2.0
	 *
	 * @param array|string|null $query {
	 *     Optional. Which tag name to find, having which class, etc. Default is to find any tag.
	 *
	 *     @type string|null $tag_name     Which tag to find, or `null` for "any tag."
	 *     @type int|null    $match_offset Find the Nth tag matching all search criteria.
	 *                                     1 for "first" tag, 3 for "third," etc.
	 *                                     Defaults to first tag.
	 *     @type string|null $class_name   Tag must contain this whole class name to match.
	 *     @type string|null $tag_closers  "visit" or "skip": whether to stop on tag closers, e.g. </div>.
	 * }
	 * @return boolean Whether a tag was matched.
	 */
	public function next_tag( $query = null ) {
		$this->parse_query( $query );
		$already_found = 0;

		do {
			if ( $this->bytes_already_parsed >= strlen( $this->html ) ) {
				return false;
			}

			// Find the next tag if it exists.
			if ( false === $this->parse_next_tag() ) {
				$this->bytes_already_parsed = strlen( $this->html );

				return false;
			}

			// Parse all of its attributes.
			while ( $this->parse_next_attribute() ) {
				continue;
			}

			// Ensure that the tag closes before the end of the document.
			$tag_ends_at = strpos( $this->html, '>', $this->bytes_already_parsed );
			if ( false === $tag_ends_at ) {
				return false;
			}
			$this->tag_ends_at          = $tag_ends_at;
			$this->bytes_already_parsed = $tag_ends_at;

			// Finally, check if the parsed tag and its attributes match the search query.
			if ( $this->matches() ) {
				++$already_found;
			}

			/*
			 * For non-DATA sections which might contain text that looks like HTML tags but
			 * isn't, scan with the appropriate alternative mode. Looking at the first letter
			 * of the tag name as a pre-check avoids a string allocation when it's not needed.
			 */
			$t = $this->html[ $this->tag_name_starts_at ];
			if ( ! $this->is_closing_tag && ( 's' === $t || 'S' === $t || 't' === $t || 'T' === $t ) ) {
				$tag_name = $this->get_tag();

				if ( 'SCRIPT' === $tag_name && ! $this->skip_script_data() ) {
					$this->bytes_already_parsed = strlen( $this->html );
					return false;
				} elseif (
					( 'TEXTAREA' === $tag_name || 'TITLE' === $tag_name ) &&
					! $this->skip_rcdata( $tag_name )
				) {
					$this->bytes_already_parsed = strlen( $this->html );
					return false;
				}
			}
		} while ( $already_found < $this->sought_match_offset );

		return true;
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
	 * ```
	 *     <main><h2>Surprising fact you may not know!</h2></main>
	 *           ^  ^
	 *            \-|-- this `H2` opener bookmark tracks the token
	 *
	 *     <main class="clickbait"><h2>Surprising fact you may no…
	 *                             ^  ^
	 *                              \-|-- it shifts with edits
	 * ```
	 *
	 * Bookmarks provide the ability to seek to a previously-scanned
	 * place in the HTML document. This avoids the need to re-scan
	 * the entire document.
	 *
	 * Example:
	 * ```
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
	 * ```
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
	 * @since 6.2.0
	 *
	 * @param string $name Identifies this particular bookmark.
	 * @return bool Whether the bookmark was successfully created.
	 */
	public function set_bookmark( $name ) {
		if ( null === $this->tag_name_starts_at ) {
			return false;
		}

		if ( ! array_key_exists( $name, $this->bookmarks ) && count( $this->bookmarks ) >= self::MAX_BOOKMARKS ) {
			_doing_it_wrong(
				__METHOD__,
				__( 'Too many bookmarks: cannot create any more.' ),
				'6.2.0'
			);
			return false;
		}

		$this->bookmarks[ $name ] = new WP_HTML_Span(
			$this->tag_name_starts_at - ( $this->is_closing_tag ? 2 : 1 ),
			$this->tag_ends_at
		);

		return true;
	}


	/**
	 * Removes a bookmark that is no longer needed.
	 *
	 * Releasing a bookmark frees up the small
	 * performance overhead it requires.
	 *
	 * @param string $name Name of the bookmark to remove.
	 * @return bool Whether the bookmark already existed before removal.
	 */
	public function release_bookmark( $name ) {
		if ( ! array_key_exists( $name, $this->bookmarks ) ) {
			return false;
		}

		unset( $this->bookmarks[ $name ] );

		return true;
	}


	/**
	 * Skips contents of title and textarea tags.
	 *
	 * @see https://html.spec.whatwg.org/multipage/parsing.html#rcdata-state
	 * @since 6.2.0
	 *
	 * @param string $tag_name – the lowercase tag name which will close the RCDATA region.
	 * @return bool Whether an end to the RCDATA region was found before the end of the document.
	 */
	private function skip_rcdata( $tag_name ) {
		$html       = $this->html;
		$doc_length = strlen( $html );
		$tag_length = strlen( $tag_name );

		$at = $this->bytes_already_parsed;

		while ( false !== $at && $at < $doc_length ) {
			$at = strpos( $this->html, '</', $at );

			// If there is no possible tag closer then fail.
			if ( false === $at || ( $at + $tag_length ) >= $doc_length ) {
				$this->bytes_already_parsed = $doc_length;
				return false;
			}

			$closer_potentially_starts_at = $at;
			$at                          += 2;

			/*
			 * Find a case-insensitive match to the tag name.
			 *
			 * Because tag names are limited to US-ASCII there is no
			 * need to perform any kind of Unicode normalization when
			 * comparing; any character which could be impacted by such
			 * normalization could not be part of a tag name.
			 */
			for ( $i = 0; $i < $tag_length; $i++ ) {
				$tag_char  = $tag_name[ $i ];
				$html_char = $html[ $at + $i ];

				if ( $html_char !== $tag_char && strtoupper( $html_char ) !== $tag_char ) {
					$at += $i;
					continue 2;
				}
			}

			$at                        += $tag_length;
			$this->bytes_already_parsed = $at;

			/*
			 * Ensure that the tag name terminates to avoid matching on
			 * substrings of a longer tag name. For example, the sequence
			 * "</textarearug" should not match for "</textarea" even
			 * though "textarea" is found within the text.
			 */
			$c = $html[ $at ];
			if ( ' ' !== $c && "\t" !== $c && "\r" !== $c && "\n" !== $c && '/' !== $c && '>' !== $c ) {
				continue;
			}

			while ( $this->parse_next_attribute() ) {
				continue;
			}
			$at = $this->bytes_already_parsed;
			if ( $at >= strlen( $this->html ) ) {
				return false;
			}

			if ( '>' === $html[ $at ] || '/' === $html[ $at ] ) {
				$this->bytes_already_parsed = $closer_potentially_starts_at;
				return true;
			}
		}

		return false;
	}

	/**
	 * Skips contents of script tags.
	 *
	 * @since 6.2.0
	 *
	 * @return bool Whether the script tag was closed before the end of the document.
	 */
	private function skip_script_data() {
		$state      = 'unescaped';
		$html       = $this->html;
		$doc_length = strlen( $html );
		$at         = $this->bytes_already_parsed;

		while ( false !== $at && $at < $doc_length ) {
			$at += strcspn( $html, '-<', $at );

			/*
			 * For all script states a "-->"  transitions
			 * back into the normal unescaped script mode,
			 * even if that's the current state.
			 */
			if (
				$at + 2 < $doc_length &&
				'-' === $html[ $at ] &&
				'-' === $html[ $at + 1 ] &&
				'>' === $html[ $at + 2 ]
			) {
				$at   += 3;
				$state = 'unescaped';
				continue;
			}

			// Everything of interest past here starts with "<".
			if ( $at + 1 >= $doc_length || '<' !== $html[ $at++ ] ) {
				continue;
			}

			/*
			 * Unlike with "-->", the "<!--" only transitions
			 * into the escaped mode if not already there.
			 *
			 * Inside the escaped modes it will be ignored; and
			 * should never break out of the double-escaped
			 * mode and back into the escaped mode.
			 *
			 * While this requires a mode change, it does not
			 * impact the parsing otherwise, so continue
			 * parsing after updating the state.
			 */
			if (
				$at + 2 < $doc_length &&
				'!' === $html[ $at ] &&
				'-' === $html[ $at + 1 ] &&
				'-' === $html[ $at + 2 ]
			) {
				$at   += 3;
				$state = 'unescaped' === $state ? 'escaped' : $state;
				continue;
			}

			if ( '/' === $html[ $at ] ) {
				$closer_potentially_starts_at = $at - 1;
				$is_closing                   = true;
				++$at;
			} else {
				$is_closing = false;
			}

			/*
			 * At this point the only remaining state-changes occur with the
			 * <script> and </script> tags; unless one of these appears next,
			 * proceed scanning to the next potential token in the text.
			 */
			if ( ! (
				$at + 6 < $doc_length &&
				( 's' === $html[ $at ] || 'S' === $html[ $at ] ) &&
				( 'c' === $html[ $at + 1 ] || 'C' === $html[ $at + 1 ] ) &&
				( 'r' === $html[ $at + 2 ] || 'R' === $html[ $at + 2 ] ) &&
				( 'i' === $html[ $at + 3 ] || 'I' === $html[ $at + 3 ] ) &&
				( 'p' === $html[ $at + 4 ] || 'P' === $html[ $at + 4 ] ) &&
				( 't' === $html[ $at + 5 ] || 'T' === $html[ $at + 5 ] )
			) ) {
				++$at;
				continue;
			}

			/*
			 * Ensure that the script tag terminates to avoid matching on
			 * substrings of a non-match. For example, the sequence
			 * "<script123" should not end a script region even though
			 * "<script" is found within the text.
			 */
			if ( $at + 6 >= $doc_length ) {
				continue;
			}
			$at += 6;
			$c   = $html[ $at ];
			if ( ' ' !== $c && "\t" !== $c && "\r" !== $c && "\n" !== $c && '/' !== $c && '>' !== $c ) {
				++$at;
				continue;
			}

			if ( 'escaped' === $state && ! $is_closing ) {
				$state = 'double-escaped';
				continue;
			}

			if ( 'double-escaped' === $state && $is_closing ) {
				$state = 'escaped';
				continue;
			}

			if ( $is_closing ) {
				$this->bytes_already_parsed = $closer_potentially_starts_at;
				if ( $this->bytes_already_parsed >= $doc_length ) {
					return false;
				}

				while ( $this->parse_next_attribute() ) {
					continue;
				}

				if ( '>' === $html[ $this->bytes_already_parsed ] ) {
					$this->bytes_already_parsed = $closer_potentially_starts_at;
					return true;
				}
			}

			++$at;
		}

		return false;
	}

	/**
	 * Parses the next tag.
	 *
	 * This will find and start parsing the next tag, including
	 * the opening `<`, the potential closer `/`, and the tag
	 * name. It does not parse the attributes or scan to the
	 * closing `>`; these are left for other methods.
	 *
	 * @since 6.2.0
	 *
	 * @return bool Whether a tag was found before the end of the document.
	 */
	private function parse_next_tag() {
		$this->after_tag();

		$html       = $this->html;
		$doc_length = strlen( $html );
		$at         = $this->bytes_already_parsed;

		while ( false !== $at && $at < $doc_length ) {
			$at = strpos( $html, '<', $at );
			if ( false === $at ) {
				return false;
			}

			if ( '/' === $this->html[ $at + 1 ] ) {
				$this->is_closing_tag = true;
				$at++;
			} else {
				$this->is_closing_tag = false;
			}

			/*
			 * HTML tag names must start with [a-zA-Z] otherwise they are not tags.
			 * For example, "<3" is rendered as text, not a tag opener. If at least
			 * one letter follows the "<" then _it is_ a tag, but if the following
			 * character is anything else it _is not a tag_.
			 *
			 * It's not uncommon to find non-tags starting with `<` in an HTML
			 * document, so it's good for performance to make this pre-check before
			 * continuing to attempt to parse a tag name.
			 *
			 * Reference:
			 * * https://html.spec.whatwg.org/multipage/parsing.html#data-state
			 * * https://html.spec.whatwg.org/multipage/parsing.html#tag-open-state
			 */
			$tag_name_prefix_length = strspn( $html, 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', $at + 1 );
			if ( $tag_name_prefix_length > 0 ) {
				++$at;
				$this->tag_name_length      = $tag_name_prefix_length + strcspn( $html, " \t\f\r\n/>", $at + $tag_name_prefix_length );
				$this->tag_name_starts_at   = $at;
				$this->bytes_already_parsed = $at + $this->tag_name_length;
				return true;
			}

			/*
			 * Abort if no tag is found before the end of
			 * the document. There is nothing left to parse.
			 */
			if ( $at + 1 >= strlen( $html ) ) {
				return false;
			}

			/*
			 * <! transitions to markup declaration open state
			 * https://html.spec.whatwg.org/multipage/parsing.html#markup-declaration-open-state
			 */
			if ( '!' === $html[ $at + 1 ] ) {
				/*
				 * <!-- transitions to a bogus comment state – skip to the nearest -->
				 * https://html.spec.whatwg.org/multipage/parsing.html#tag-open-state
				 */
				if (
					strlen( $html ) > $at + 3 &&
					'-' === $html[ $at + 2 ] &&
					'-' === $html[ $at + 3 ]
				) {
					$closer_at = strpos( $html, '-->', $at + 4 );
					if ( false === $closer_at ) {
						return false;
					}

					$at = $closer_at + 3;
					continue;
				}

				/*
				 * <![CDATA[ transitions to CDATA section state – skip to the nearest ]]>
				 * The CDATA is case-sensitive.
				 * https://html.spec.whatwg.org/multipage/parsing.html#tag-open-state
				 */
				if (
					strlen( $html ) > $at + 8 &&
					'[' === $html[ $at + 2 ] &&
					'C' === $html[ $at + 3 ] &&
					'D' === $html[ $at + 4 ] &&
					'A' === $html[ $at + 5 ] &&
					'T' === $html[ $at + 6 ] &&
					'A' === $html[ $at + 7 ] &&
					'[' === $html[ $at + 8 ]
				) {
					$closer_at = strpos( $html, ']]>', $at + 9 );
					if ( false === $closer_at ) {
						return false;
					}

					$at = $closer_at + 3;
					continue;
				}

				/*
				 * <!DOCTYPE transitions to DOCTYPE state – skip to the nearest >
				 * These are ASCII-case-insensitive.
				 * https://html.spec.whatwg.org/multipage/parsing.html#tag-open-state
				 */
				if (
					strlen( $html ) > $at + 8 &&
					( 'D' === $html[ $at + 2 ] || 'd' === $html[ $at + 2 ] ) &&
					( 'O' === $html[ $at + 3 ] || 'o' === $html[ $at + 3 ] ) &&
					( 'C' === $html[ $at + 4 ] || 'c' === $html[ $at + 4 ] ) &&
					( 'T' === $html[ $at + 5 ] || 't' === $html[ $at + 5 ] ) &&
					( 'Y' === $html[ $at + 6 ] || 'y' === $html[ $at + 6 ] ) &&
					( 'P' === $html[ $at + 7 ] || 'p' === $html[ $at + 7 ] ) &&
					( 'E' === $html[ $at + 8 ] || 'e' === $html[ $at + 8 ] )
				) {
					$closer_at = strpos( $html, '>', $at + 9 );
					if ( false === $closer_at ) {
						return false;
					}

					$at = $closer_at + 1;
					continue;
				}

				/*
				 * Anything else here is an incorrectly-opened comment and transitions
				 * to the bogus comment state - skip to the nearest >.
				 */
				$at = strpos( $html, '>', $at + 1 );
				continue;
			}

			/*
			 * <? transitions to a bogus comment state – skip to the nearest >
			 * https://html.spec.whatwg.org/multipage/parsing.html#tag-open-state
			 */
			if ( '?' === $html[ $at + 1 ] ) {
				$closer_at = strpos( $html, '>', $at + 2 );
				if ( false === $closer_at ) {
					return false;
				}

				$at = $closer_at + 1;
				continue;
			}

			++$at;
		}

		return false;
	}

	/**
	 * Parses the next attribute.
	 *
	 * @since 6.2.0
	 *
	 * @return bool Whether an attribute was found before the end of the document.
	 */
	private function parse_next_attribute() {
		// Skip whitespace and slashes.
		$this->bytes_already_parsed += strspn( $this->html, " \t\f\r\n/", $this->bytes_already_parsed );
		if ( $this->bytes_already_parsed >= strlen( $this->html ) ) {
			return false;
		}

		/*
		 * Treat the equal sign as a part of the attribute
		 * name if it is the first encountered byte.
		 *
		 * @see https://html.spec.whatwg.org/multipage/parsing.html#before-attribute-name-state
		 */
		$name_length = '=' === $this->html[ $this->bytes_already_parsed ]
			? 1 + strcspn( $this->html, "=/> \t\f\r\n", $this->bytes_already_parsed + 1 )
			: strcspn( $this->html, "=/> \t\f\r\n", $this->bytes_already_parsed );

		// No attribute, just tag closer.
		if ( 0 === $name_length || $this->bytes_already_parsed + $name_length >= strlen( $this->html ) ) {
			return false;
		}

		$attribute_start             = $this->bytes_already_parsed;
		$attribute_name              = substr( $this->html, $attribute_start, $name_length );
		$this->bytes_already_parsed += $name_length;
		if ( $this->bytes_already_parsed >= strlen( $this->html ) ) {
			return false;
		}

		$this->skip_whitespace();
		if ( $this->bytes_already_parsed >= strlen( $this->html ) ) {
			return false;
		}

		$has_value = '=' === $this->html[ $this->bytes_already_parsed ];
		if ( $has_value ) {
			++$this->bytes_already_parsed;
			$this->skip_whitespace();
			if ( $this->bytes_already_parsed >= strlen( $this->html ) ) {
				return false;
			}

			switch ( $this->html[ $this->bytes_already_parsed ] ) {
				case "'":
				case '"':
					$quote                      = $this->html[ $this->bytes_already_parsed ];
					$value_start                = $this->bytes_already_parsed + 1;
					$value_length               = strcspn( $this->html, $quote, $value_start );
					$attribute_end              = $value_start + $value_length + 1;
					$this->bytes_already_parsed = $attribute_end;
					break;

				default:
					$value_start                = $this->bytes_already_parsed;
					$value_length               = strcspn( $this->html, "> \t\f\r\n", $value_start );
					$attribute_end              = $value_start + $value_length;
					$this->bytes_already_parsed = $attribute_end;
			}
		} else {
			$value_start   = $this->bytes_already_parsed;
			$value_length  = 0;
			$attribute_end = $attribute_start + $name_length;
		}

		if ( $attribute_end >= strlen( $this->html ) ) {
			return false;
		}

		if ( $this->is_closing_tag ) {
			return true;
		}

		/*
		 * > There must never be two or more attributes on
		 * > the same start tag whose names are an ASCII
		 * > case-insensitive match for each other.
		 *     - HTML 5 spec
		 *
		 * @see https://html.spec.whatwg.org/multipage/syntax.html#attributes-2:ascii-case-insensitive
		 */
		$comparable_name = strtolower( $attribute_name );

		// If an attribute is listed many times, only use the first declaration and ignore the rest.
		if ( ! array_key_exists( $comparable_name, $this->attributes ) ) {
			$this->attributes[ $comparable_name ] = new WP_HTML_Attribute_Token(
				$attribute_name,
				$value_start,
				$value_length,
				$attribute_start,
				$attribute_end,
				! $has_value
			);
		}

		return true;
	}

	/**
	 * Move the internal cursor past any immediate successive whitespace.
	 *
	 * @since 6.2.0
	 *
	 * @return void
	 */
	private function skip_whitespace() {
		$this->bytes_already_parsed += strspn( $this->html, " \t\f\r\n", $this->bytes_already_parsed );
	}

	/**
	 * Applies attribute updates and cleans up once a tag is fully parsed.
	 *
	 * @since 6.2.0
	 *
	 * @return void
	 */
	private function after_tag() {
		$this->class_name_updates_to_attributes_updates();
		$this->apply_attributes_updates();
		$this->tag_name_starts_at = null;
		$this->tag_name_length    = null;
		$this->tag_ends_at        = null;
		$this->is_closing_tag     = null;
		$this->attributes         = array();
	}

	/**
	 * Converts class name updates into tag attributes updates
	 * (they are accumulated in different data formats for performance).
	 *
	 * @see $lexical_updates
	 * @see $classname_updates
	 *
	 * @since 6.2.0
	 *
	 * @return void
	 */
	private function class_name_updates_to_attributes_updates() {
		if ( count( $this->classname_updates ) === 0 ) {
			return;
		}

		$existing_class = $this->get_enqueued_attribute_value( 'class' );
		if ( null === $existing_class || true === $existing_class ) {
			$existing_class = '';
		}

		if ( false === $existing_class && isset( $this->attributes['class'] ) ) {
			$existing_class = substr(
				$this->html,
				$this->attributes['class']->value_starts_at,
				$this->attributes['class']->value_length
			);
		}

		if ( false === $existing_class ) {
			$existing_class = '';
		}

		/**
		 * Updated "class" attribute value.
		 *
		 * This is incrementally built while scanning through the existing class
		 * attribute, skipping removed classes on the way, and then appending
		 * added classes at the end. Only when finished processing will the
		 * value contain the final new value.

		 * @var string $class
		 */
		$class = '';

		/**
		 * Tracks the cursor position in the existing
		 * class attribute value while parsing.
		 *
		 * @var int $at
		 */
		$at = 0;

		/**
		 * Indicates if there's any need to modify the existing class attribute.
		 *
		 * If a call to `add_class()` and `remove_class()` wouldn't impact
		 * the `class` attribute value then there's no need to rebuild it.
		 * For example, when adding a class that's already present or
		 * removing one that isn't.
		 *
		 * This flag enables a performance optimization when none of the enqueued
		 * class updates would impact the `class` attribute; namely, that the
		 * processor can continue without modifying the input document, as if
		 * none of the `add_class()` or `remove_class()` calls had been made.
		 *
		 * This flag is set upon the first change that requires a string update.
		 *
		 * @var bool $modified
		 */
		$modified = false;

		// Remove unwanted classes by only copying the new ones.
		$existing_class_length = strlen( $existing_class );
		while ( $at < $existing_class_length ) {
			// Skip to the first non-whitespace character.
			$ws_at     = $at;
			$ws_length = strspn( $existing_class, " \t\f\r\n", $ws_at );
			$at       += $ws_length;

			// Capture the class name – it's everything until the next whitespace.
			$name_length = strcspn( $existing_class, " \t\f\r\n", $at );
			if ( 0 === $name_length ) {
				// If no more class names are found then that's the end.
				break;
			}

			$name = substr( $existing_class, $at, $name_length );
			$at  += $name_length;

			// If this class is marked for removal, start processing the next one.
			$remove_class = (
				isset( $this->classname_updates[ $name ] ) &&
				self::REMOVE_CLASS === $this->classname_updates[ $name ]
			);

			// If a class has already been seen then skip it; it should not be added twice.
			if ( ! $remove_class ) {
				$this->classname_updates[ $name ] = self::SKIP_CLASS;
			}

			if ( $remove_class ) {
				$modified = true;
				continue;
			}

			/*
			 * Otherwise, append it to the new "class" attribute value.
			 *
			 * There are options for handling whitespace between tags.
			 * Preserving the existing whitespace produces fewer changes
			 * to the HTML content and should clarify the before/after
			 * content when debugging the modified output.
			 *
			 * This approach contrasts normalizing the inter-class
			 * whitespace to a single space, which might appear cleaner
			 * in the output HTML but produce a noisier change.
			 */
			$class .= substr( $existing_class, $ws_at, $ws_length );
			$class .= $name;
		}

		// Add new classes by appending those which haven't already been seen.
		foreach ( $this->classname_updates as $name => $operation ) {
			if ( self::ADD_CLASS === $operation ) {
				$modified = true;

				$class .= strlen( $class ) > 0 ? ' ' : '';
				$class .= $name;
			}
		}

		$this->classname_updates = array();
		if ( ! $modified ) {
			return;
		}

		if ( strlen( $class ) > 0 ) {
			$this->set_attribute( 'class', $class );
		} else {
			$this->remove_attribute( 'class' );
		}
	}

	/**
	 * Applies attribute updates to HTML document.
	 *
	 * @since 6.2.0
	 * @since 6.3.0 Invalidate any bookmarks whose targets are overwritten.
	 *
	 * @return void
	 */
	private function apply_attributes_updates() {
		if ( ! count( $this->lexical_updates ) ) {
			return;
		}

		/*
		 * Attribute updates can be enqueued in any order but updates
		 * to the document must occur in lexical order; that is, each
		 * replacement must be made before all others which follow it
		 * at later string indices in the input document.
		 *
		 * Sorting avoid making out-of-order replacements which
		 * can lead to mangled output, partially-duplicated
		 * attributes, and overwritten attributes.
		 */
		usort( $this->lexical_updates, array( self::class, 'sort_start_ascending' ) );

		foreach ( $this->lexical_updates as $diff ) {
			$this->output_buffer       .= substr( $this->html, $this->bytes_already_copied, $diff->start - $this->bytes_already_copied );
			$this->output_buffer       .= $diff->text;
			$this->bytes_already_copied = $diff->end;
		}

		/*
		 * Adjust bookmark locations to account for how the text
		 * replacements adjust offsets in the input document.
		 */
		foreach ( $this->bookmarks as $bookmark_name => $bookmark ) {
			/*
			 * Each lexical update which appears before the bookmark's endpoints
			 * might shift the offsets for those endpoints. Loop through each change
			 * and accumulate the total shift for each bookmark, then apply that
			 * shift after tallying the full delta.
			 */
			$head_delta = 0;
			$tail_delta = 0;

			foreach ( $this->lexical_updates as $diff ) {
				if ( $bookmark->start < $diff->start && $bookmark->end < $diff->start ) {
					break;
				}

				if ( $bookmark->start >= $diff->start && $bookmark->end < $diff->end ) {
					$this->release_bookmark( $bookmark_name );
					continue 2;
				}

				$delta = strlen( $diff->text ) - ( $diff->end - $diff->start );

				if ( $bookmark->start >= $diff->start ) {
					$head_delta += $delta;
				}

				if ( $bookmark->end >= $diff->end ) {
					$tail_delta += $delta;
				}
			}

			$bookmark->start += $head_delta;
			$bookmark->end   += $tail_delta;
		}

		$this->lexical_updates = array();
	}

	/**
	 * Checks whether a bookmark with the given name exists.
	 *
	 * @since 6.3.0
	 *
	 * @param string $bookmark_name Name to identify a bookmark that potentially exists.
	 * @return bool Whether that bookmark exists.
	 */
	public function has_bookmark( $bookmark_name ) {
		return array_key_exists( $bookmark_name, $this->bookmarks );
	}

	/**
	 * Move the internal cursor in the Tag Processor to a given bookmark's location.
	 *
	 * In order to prevent accidental infinite loops, there's a
	 * maximum limit on the number of times seek() can be called.
	 *
	 * @since 6.2.0
	 *
	 * @param string $bookmark_name Jump to the place in the document identified by this bookmark name.
	 * @return bool Whether the internal cursor was successfully moved to the bookmark's location.
	 */
	public function seek( $bookmark_name ) {
		if ( ! array_key_exists( $bookmark_name, $this->bookmarks ) ) {
			_doing_it_wrong(
				__METHOD__,
				__( 'Unknown bookmark name.' ),
				'6.2.0'
			);
			return false;
		}

		if ( ++$this->seek_count > self::MAX_SEEK_OPS ) {
			_doing_it_wrong(
				__METHOD__,
				__( 'Too many calls to seek() - this can lead to performance issues.' ),
				'6.2.0'
			);
			return false;
		}

		// Flush out any pending updates to the document.
		$this->get_updated_html();

		// Point this tag processor before the sought tag opener and consume it.
		$this->bytes_already_parsed = $this->bookmarks[ $bookmark_name ]->start;
		$this->bytes_already_copied = $this->bytes_already_parsed;
		$this->output_buffer        = substr( $this->html, 0, $this->bytes_already_copied );
		return $this->next_tag( array( 'tag_closers' => 'visit' ) );
	}

	/**
	 * Compare two WP_HTML_Text_Replacement objects.
	 *
	 * @since 6.2.0
	 *
	 * @param WP_HTML_Text_Replacement $a First attribute update.
	 * @param WP_HTML_Text_Replacement $b Second attribute update.
	 * @return int Comparison value for string order.
	 */
	private static function sort_start_ascending( $a, $b ) {
		$by_start = $a->start - $b->start;
		if ( 0 !== $by_start ) {
			return $by_start;
		}

		$by_text = isset( $a->text, $b->text ) ? strcmp( $a->text, $b->text ) : 0;
		if ( 0 !== $by_text ) {
			return $by_text;
		}

		/*
		 * This code should be unreachable, because it implies the two replacements
		 * start at the same location and contain the same text.
		 */
		return $a->end - $b->end;
	}

	/**
	 * Return the enqueued value for a given attribute, if one exists.
	 *
	 * Enqueued updates can take different data types:
	 *  - If an update is enqueued and is boolean, the return will be `true`
	 *  - If an update is otherwise enqueued, the return will be the string value of that update.
	 *  - If an attribute is enqueued to be removed, the return will be `null` to indicate that.
	 *  - If no updates are enqueued, the return will be `false` to differentiate from "removed."
	 *
	 * @since 6.2.0
	 *
	 * @param string $comparable_name The attribute name in its comparable form.
	 * @return string|boolean|null Value of enqueued update if present, otherwise false.
	 */
	private function get_enqueued_attribute_value( $comparable_name ) {
		if ( ! isset( $this->lexical_updates[ $comparable_name ] ) ) {
			return false;
		}

		$enqueued_text = $this->lexical_updates[ $comparable_name ]->text;

		// Removed attributes erase the entire span.
		if ( '' === $enqueued_text ) {
			return null;
		}

		/*
		 * Boolean attribute updates are just the attribute name without a corresponding value.
		 *
		 * This value might differ from the given comparable name in that there could be leading
		 * or trailing whitespace, and that the casing follows the name given in `set_attribute`.
		 *
		 * Example:
		 * ```
		 *     $p->set_attribute( 'data-TEST-id', 'update' );
		 *     'update' === $p->get_enqueued_attribute_value( 'data-test-id' );
		 * ```
		 *
		 * Detect this difference based on the absence of the `=`, which _must_ exist in any
		 * attribute containing a value, e.g. `<input type="text" enabled />`.
		 *                                            ¹           ²
		 *                                       1. Attribute with a string value.
		 *                                       2. Boolean attribute whose value is `true`.
		 */
		$equals_at = strpos( $enqueued_text, '=' );
		if ( false === $equals_at ) {
			return true;
		}

		/*
		 * Finally, a normal update's value will appear after the `=` and
		 * be double-quoted, as performed incidentally by `set_attribute`.
		 *
		 * e.g. `type="text"`
		 *           ¹²    ³
		 *        1. Equals is here.
		 *        2. Double-quoting starts one after the equals sign.
		 *        3. Double-quoting ends at the last character in the update.
		 */
		$enqueued_value = substr( $enqueued_text, $equals_at + 2, -1 );
		return html_entity_decode( $enqueued_value );
	}

	/**
	 * Returns the value of a requested attribute from a matched tag opener if that attribute exists.
	 *
	 * Example:
	 * ```php
	 *     $p = new WP_HTML_Tag_Processor( '<div enabled class="test" data-test-id="14">Test</div>' );
	 *     $p->next_tag( array( 'class_name' => 'test' ) ) === true;
	 *     $p->get_attribute( 'data-test-id' ) === '14';
	 *     $p->get_attribute( 'enabled' ) === true;
	 *     $p->get_attribute( 'aria-label' ) === null;
	 *
	 *     $p->next_tag() === false;
	 *     $p->get_attribute( 'class' ) === null;
	 * ```
	 *
	 * @since 6.2.0
	 *
	 * @param string $name Name of attribute whose value is requested.
	 * @return string|true|null Value of attribute or `null` if not available. Boolean attributes return `true`.
	 */
	public function get_attribute( $name ) {
		if ( null === $this->tag_name_starts_at ) {
			return null;
		}

		$comparable = strtolower( $name );

		/*
		 * For every attribute other than `class` it's possible to perform a quick check if
		 * there's an enqueued lexical update whose value takes priority over what's found in
		 * the input document.
		 *
		 * The `class` attribute is special though because of the exposed helpers `add_class`
		 * and `remove_class`. These form a builder for the `class` attribute, so an additional
		 * check for enqueued class changes is required in addition to the check for any enqueued
		 * attribute values. If any exist, those enqueued class changes must first be flushed out
		 * into an attribute value update.
		 */
		if ( 'class' === $name ) {
			$this->class_name_updates_to_attributes_updates();
		}

		// Return any enqueued attribute value updates if they exist.
		$enqueued_value = $this->get_enqueued_attribute_value( $comparable );
		if ( false !== $enqueued_value ) {
			return $enqueued_value;
		}

		if ( ! isset( $this->attributes[ $comparable ] ) ) {
			return null;
		}

		$attribute = $this->attributes[ $comparable ];

		/*
		 * This flag distinguishes an attribute with no value
		 * from an attribute with an empty string value. For
		 * unquoted attributes this could look very similar.
		 * It refers to whether an `=` follows the name.
		 *
		 * e.g. <div boolean-attribute empty-attribute=></div>
		 *           ¹                 ²
		 *        1. Attribute `boolean-attribute` is `true`.
		 *        2. Attribute `empty-attribute` is `""`.
		 */
		if ( true === $attribute->is_true ) {
			return true;
		}

		$raw_value = substr( $this->html, $attribute->value_starts_at, $attribute->value_length );

		return html_entity_decode( $raw_value );
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
	 * @see https://html.spec.whatwg.org/multipage/syntax.html#attributes-2:ascii-case-insensitive
	 *
	 * Example:
	 * ```php
	 *     $p = new WP_HTML_Tag_Processor( '<div data-ENABLED class="test" DATA-test-id="14">Test</div>' );
	 *     $p->next_tag( array( 'class_name' => 'test' ) ) === true;
	 *     $p->get_attribute_names_with_prefix( 'data-' ) === array( 'data-enabled', 'data-test-id' );
	 *
	 *     $p->next_tag() === false;
	 *     $p->get_attribute_names_with_prefix( 'data-' ) === null;
	 * ```
	 *
	 * @since 6.2.0
	 *
	 * @param string $prefix Prefix of requested attribute names.
	 * @return array|null List of attribute names, or `null` when no tag opener is matched.
	 */
	function get_attribute_names_with_prefix( $prefix ) {
		if ( $this->is_closing_tag || null === $this->tag_name_starts_at ) {
			return null;
		}

		$comparable = strtolower( $prefix );

		$matches = array();
		foreach ( array_keys( $this->attributes ) as $attr_name ) {
			if ( str_starts_with( $attr_name, $comparable ) ) {
				$matches[] = $attr_name;
			}
		}
		return $matches;
	}

	/**
	 * Returns the uppercase name of the matched tag.
	 *
	 * Example:
	 * ```php
	 *     $p = new WP_HTML_Tag_Processor( '<DIV CLASS="test">Test</DIV>' );
	 *     $p->next_tag() === true;
	 *     $p->get_tag() === 'DIV';
	 *
	 *     $p->next_tag() === false;
	 *     $p->get_tag() === null;
	 * ```
	 *
	 * @since 6.2.0
	 *
	 * @return string|null Name of currently matched tag in input HTML, or `null` if none found.
	 */
	public function get_tag() {
		if ( null === $this->tag_name_starts_at ) {
			return null;
		}

		$tag_name = substr( $this->html, $this->tag_name_starts_at, $this->tag_name_length );

		return strtoupper( $tag_name );
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
	 * @since 6.3.0
	 *
	 * @return bool Whether the currently matched tag contains the self-closing flag.
	 */
	public function has_self_closing_flag() {
		if ( ! $this->tag_name_starts_at ) {
			return false;
		}

		return '/' === $this->html[ $this->tag_ends_at - 1 ];
	}

	/**
	 * Indicates if the current tag token is a tag closer.
	 *
	 * Example:
	 * ```php
	 *     $p = new WP_HTML_Tag_Processor( '<div></div>' );
	 *     $p->next_tag( array( 'tag_name' => 'div', 'tag_closers' => 'visit' ) );
	 *     $p->is_tag_closer() === false;
	 *
	 *     $p->next_tag( array( 'tag_name' => 'div', 'tag_closers' => 'visit' ) );
	 *     $p->is_tag_closer() === true;
	 * ```
	 *
	 * @since 6.2.0
	 *
	 * @return bool Whether the current tag is a tag closer.
	 */
	public function is_tag_closer() {
		return $this->is_closing_tag;
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
	 * @since 6.2.0
	 *
	 * @param string      $name  The attribute name to target.
	 * @param string|bool $value The new attribute value.
	 * @return bool Whether an attribute value was set.
	 */
	public function set_attribute( $name, $value ) {
		if ( $this->is_closing_tag || null === $this->tag_name_starts_at ) {
			return false;
		}

		/*
		 * WordPress rejects more characters than are strictly forbidden
		 * in HTML5. This is to prevent additional security risks deeper
		 * in the WordPress and plugin stack. Specifically the
		 * less-than (<) greater-than (>) and ampersand (&) aren't allowed.
		 *
		 * The use of a PCRE match enables looking for specific Unicode
		 * code points without writing a UTF-8 decoder. Whereas scanning
		 * for one-byte characters is trivial (with `strcspn`), scanning
		 * for the longer byte sequences would be more complicated. Given
		 * that this shouldn't be in the hot path for execution, it's a
		 * reasonable compromise in efficiency without introducing a
		 * noticeable impact on the overall system.
		 *
		 * @see https://html.spec.whatwg.org/#attributes-2
		 *
		 * @TODO as the only regex pattern maybe we should take it out? are
		 *       Unicode patterns available broadly in Core?
		 */
		if ( preg_match(
			'~[' .
				// Syntax-like characters.
				'"\'>&</ =' .
				// Control characters.
				'\x{00}-\x{1F}' .
				// HTML noncharacters.
				'\x{FDD0}-\x{FDEF}' .
				'\x{FFFE}\x{FFFF}\x{1FFFE}\x{1FFFF}\x{2FFFE}\x{2FFFF}\x{3FFFE}\x{3FFFF}' .
				'\x{4FFFE}\x{4FFFF}\x{5FFFE}\x{5FFFF}\x{6FFFE}\x{6FFFF}\x{7FFFE}\x{7FFFF}' .
				'\x{8FFFE}\x{8FFFF}\x{9FFFE}\x{9FFFF}\x{AFFFE}\x{AFFFF}\x{BFFFE}\x{BFFFF}' .
				'\x{CFFFE}\x{CFFFF}\x{DFFFE}\x{DFFFF}\x{EFFFE}\x{EFFFF}\x{FFFFE}\x{FFFFF}' .
				'\x{10FFFE}\x{10FFFF}' .
			']~Ssu',
			$name
		) ) {
			_doing_it_wrong(
				__METHOD__,
				__( 'Invalid attribute name.' ),
				'6.2.0'
			);

			return false;
		}

		/*
		 * > The values "true" and "false" are not allowed on boolean attributes.
		 * > To represent a false value, the attribute has to be omitted altogether.
		 *     - HTML5 spec, https://html.spec.whatwg.org/#boolean-attributes
		 */
		if ( false === $value ) {
			return $this->remove_attribute( $name );
		}

		if ( true === $value ) {
			$updated_attribute = $name;
		} else {
			$escaped_new_value = esc_attr( $value );
			$updated_attribute = "{$name}=\"{$escaped_new_value}\"";
		}

		/*
		 * > There must never be two or more attributes on
		 * > the same start tag whose names are an ASCII
		 * > case-insensitive match for each other.
		 *     - HTML 5 spec
		 *
		 * @see https://html.spec.whatwg.org/multipage/syntax.html#attributes-2:ascii-case-insensitive
		 */
		$comparable_name = strtolower( $name );

		if ( isset( $this->attributes[ $comparable_name ] ) ) {
			/*
			 * Update an existing attribute.
			 *
			 * Example – set attribute id to "new" in <div id="initial_id" />:
			 *    <div id="initial_id"/>
			 *         ^-------------^
			 *         start         end
			 *    replacement: `id="new"`
			 *
			 *    Result: <div id="new"/>
			 */
			$existing_attribute             = $this->attributes[ $comparable_name ];
			$this->lexical_updates[ $name ] = new WP_HTML_Text_Replacement(
				$existing_attribute->start,
				$existing_attribute->end,
				$updated_attribute
			);
		} else {
			/*
			 * Create a new attribute at the tag's name end.
			 *
			 * Example – add attribute id="new" to <div />:
			 *    <div/>
			 *        ^
			 *        start and end
			 *    replacement: ` id="new"`
			 *
			 *    Result: <div id="new"/>
			 */
			$this->lexical_updates[ $comparable_name ] = new WP_HTML_Text_Replacement(
				$this->tag_name_starts_at + $this->tag_name_length,
				$this->tag_name_starts_at + $this->tag_name_length,
				' ' . $updated_attribute
			);
		}

		/*
		 * Any calls to update the `class` attribute directly should wipe out any
		 * enqueued class changes from `add_class` and `remove_class`.
		 */
		if ( 'class' === $comparable_name && ! empty( $this->classname_updates ) ) {
			$this->classname_updates = array();
		}

		return true;
	}

	/**
	 * Remove an attribute from the currently-matched tag.
	 *
	 * @since 6.2.0
	 *
	 * @param string $name The attribute name to remove.
	 * @return bool Whether an attribute was removed.
	 */
	public function remove_attribute( $name ) {
		if ( $this->is_closing_tag ) {
			return false;
		}

		/*
		 * > There must never be two or more attributes on
		 * > the same start tag whose names are an ASCII
		 * > case-insensitive match for each other.
		 *     - HTML 5 spec
		 *
		 * @see https://html.spec.whatwg.org/multipage/syntax.html#attributes-2:ascii-case-insensitive
		 */
		$name = strtolower( $name );

		/*
		 * Any calls to update the `class` attribute directly should wipe out any
		 * enqueued class changes from `add_class` and `remove_class`.
		 */
		if ( 'class' === $name && count( $this->classname_updates ) !== 0 ) {
			$this->classname_updates = array();
		}

		/*
		 * If updating an attribute that didn't exist in the input
		 * document, then remove the enqueued update and move on.
		 *
		 * For example, this might occur when calling `remove_attribute()`
		 * after calling `set_attribute()` for the same attribute
		 * and when that attribute wasn't originally present.
		 */
		if ( ! isset( $this->attributes[ $name ] ) ) {
			if ( isset( $this->lexical_updates[ $name ] ) ) {
				unset( $this->lexical_updates[ $name ] );
			}
			return false;
		}

		/*
		 * Removes an existing tag attribute.
		 *
		 * Example – remove the attribute id from <div id="main"/>:
		 *    <div id="initial_id"/>
		 *         ^-------------^
		 *         start         end
		 *    replacement: ``
		 *
		 *    Result: <div />
		 */
		$this->lexical_updates[ $name ] = new WP_HTML_Text_Replacement(
			$this->attributes[ $name ]->start,
			$this->attributes[ $name ]->end,
			''
		);

		return true;
	}

	/**
	 * Adds a new class name to the currently matched tag.
	 *
	 * @since 6.2.0
	 *
	 * @param string $class_name The class name to add.
	 * @return bool Whether the class was set to be added.
	 */
	public function add_class( $class_name ) {
		if ( $this->is_closing_tag ) {
			return false;
		}

		if ( null !== $this->tag_name_starts_at ) {
			$this->classname_updates[ $class_name ] = self::ADD_CLASS;
		}

		return true;
	}

	/**
	 * Removes a class name from the currently matched tag.
	 *
	 * @since 6.2.0
	 *
	 * @param string $class_name The class name to remove.
	 * @return bool Whether the class was set to be removed.
	 */
	public function remove_class( $class_name ) {
		if ( $this->is_closing_tag ) {
			return false;
		}

		if ( null !== $this->tag_name_starts_at ) {
			$this->classname_updates[ $class_name ] = self::REMOVE_CLASS;
		}

		return true;
	}

	/**
	 * Returns the string representation of the HTML Tag Processor.
	 *
	 * @since 6.2.0
	 * @see get_updated_html
	 *
	 * @return string The processed HTML.
	 */
	public function __toString() {
		return $this->get_updated_html();
	}

	/**
	 * Returns the string representation of the HTML Tag Processor.
	 *
	 * @since 6.2.0
	 *
	 * @return string The processed HTML.
	 */
	public function get_updated_html() {
		$requires_no_updating = 0 === count( $this->classname_updates ) && 0 === count( $this->lexical_updates );

		/*
		 * When there is nothing more to update and nothing has already been
		 * updated, return the original document and avoid a string copy.
		 */
		if ( $requires_no_updating && 0 === $this->bytes_already_copied ) {
			return $this->html;
		}

		/*
		 * If there are no updates left to apply, but some have already
		 * been applied, then finish by copying the rest of the input
		 * to the end of the updated document and return.
		 */
		if ( $requires_no_updating && $this->bytes_already_copied > 0 ) {
			return $this->output_buffer . substr( $this->html, $this->bytes_already_copied );
		}

		// Apply the updates, rewind to before the current tag, and reparse the attributes.
		$content_up_to_opened_tag_name = $this->output_buffer . substr(
			$this->html,
			$this->bytes_already_copied,
			$this->tag_name_starts_at + $this->tag_name_length - $this->bytes_already_copied
		);

		/*
		 * 1. Apply the edits by flushing them to the output buffer and updating the copied byte count.
		 *
		 * Note: `apply_attributes_updates()` modifies `$this->output_buffer`.
		 */
		$this->class_name_updates_to_attributes_updates();
		$this->apply_attributes_updates();

		/*
		 * 2. Replace the original HTML with the now-updated HTML so that it's possible to
		 *    seek to a previous location and have a consistent view of the updated document.
		 */
		$this->html                 = $this->output_buffer . substr( $this->html, $this->bytes_already_copied );
		$this->output_buffer        = $content_up_to_opened_tag_name;
		$this->bytes_already_copied = strlen( $this->output_buffer );

		/*
		 * 3. Point this tag processor at the original tag opener and consume it
		 *
		 * At this point the internal cursor points to the end of the tag name.
		 * Rewind before the tag name starts so that it's as if the cursor didn't
		 * move; a call to `next_tag()` will reparse the recently-updated attributes
		 * and additional calls to modify the attributes will apply at this same
		 * location.
		 *
		 * <p>Previous HTML<em>More HTML</em></p>
		 *                 ^  | back up by the length of the tag name plus the opening <
		 *                 \<-/ back up by strlen("em") + 1 ==> 3
		 */
		$this->bytes_already_parsed = strlen( $content_up_to_opened_tag_name ) - $this->tag_name_length - 1;
		$this->next_tag();

		return $this->html;
	}

	/**
	 * Parses tag query input into internal search criteria.
	 *
	 * @since 6.2.0
	 *
	 * @param array|string|null $query {
	 *     Optional. Which tag name to find, having which class, etc. Default is to find any tag.
	 *
	 *     @type string|null $tag_name     Which tag to find, or `null` for "any tag."
	 *     @type int|null    $match_offset Find the Nth tag matching all search criteria.
	 *                                     1 for "first" tag, 3 for "third," etc.
	 *                                     Defaults to first tag.
	 *     @type string|null $class_name   Tag must contain this class name to match.
	 *     @type string      $tag_closers  "visit" or "skip": whether to stop on tag closers, e.g. </div>.
	 * }
	 * @return void
	 */
	private function parse_query( $query ) {
		if ( null !== $query && $query === $this->last_query ) {
			return;
		}

		$this->last_query          = $query;
		$this->sought_tag_name     = null;
		$this->sought_class_name   = null;
		$this->sought_match_offset = 1;
		$this->stop_on_tag_closers = false;

		// A single string value means "find the tag of this name".
		if ( is_string( $query ) ) {
			$this->sought_tag_name = $query;
			return;
		}

		// An empty query parameter applies no restrictions on the search.
		if ( null === $query ) {
			return;
		}

		// If not using the string interface, an associative array is required.
		if ( ! is_array( $query ) ) {
			_doing_it_wrong(
				__METHOD__,
				__( 'The query argument must be an array or a tag name.' ),
				'6.2.0'
			);
			return;
		}

		if ( isset( $query['tag_name'] ) && is_string( $query['tag_name'] ) ) {
			$this->sought_tag_name = $query['tag_name'];
		}

		if ( isset( $query['class_name'] ) && is_string( $query['class_name'] ) ) {
			$this->sought_class_name = $query['class_name'];
		}

		if ( isset( $query['match_offset'] ) && is_int( $query['match_offset'] ) && 0 < $query['match_offset'] ) {
			$this->sought_match_offset = $query['match_offset'];
		}

		if ( isset( $query['tag_closers'] ) ) {
			$this->stop_on_tag_closers = 'visit' === $query['tag_closers'];
		}
	}


	/**
	 * Checks whether a given tag and its attributes match the search criteria.
	 *
	 * @since 6.2.0
	 *
	 * @return boolean Whether the given tag and its attribute match the search criteria.
	 */
	private function matches() {
		if ( $this->is_closing_tag && ! $this->stop_on_tag_closers ) {
			return false;
		}

		// Does the tag name match the requested tag name in a case-insensitive manner?
		if ( null !== $this->sought_tag_name ) {
			/*
			 * String (byte) length lookup is fast. If they aren't the
			 * same length then they can't be the same string values.
			 */
			if ( strlen( $this->sought_tag_name ) !== $this->tag_name_length ) {
				return false;
			}

			/*
			 * Check each character to determine if they are the same.
			 * Defer calls to `strtoupper()` to avoid them when possible.
			 * Calling `strcasecmp()` here tested slowed than comparing each
			 * character, so unless benchmarks show otherwise, it should
			 * not be used.
			 *
			 * It's expected that most of the time that this runs, a
			 * lower-case tag name will be supplied and the input will
			 * contain lower-case tag names, thus normally bypassing
			 * the case comparison code.
			 */
			for ( $i = 0; $i < $this->tag_name_length; $i++ ) {
				$html_char = $this->html[ $this->tag_name_starts_at + $i ];
				$tag_char  = $this->sought_tag_name[ $i ];

				if ( $html_char !== $tag_char && strtoupper( $html_char ) !== $tag_char ) {
					return false;
				}
			}
		}

		$needs_class_name = null !== $this->sought_class_name;

		if ( $needs_class_name && ! isset( $this->attributes['class'] ) ) {
			return false;
		}

		/*
		 * Match byte-for-byte (case-sensitive and encoding-form-sensitive) on the class name.
		 *
		 * This will overlook certain classes that exist in other lexical variations
		 * than was supplied to the search query, but requires more complicated searching.
		 */
		if ( $needs_class_name ) {
			$class_start = $this->attributes['class']->value_starts_at;
			$class_end   = $class_start + $this->attributes['class']->value_length;
			$class_at    = $class_start;

			/*
			 * Ensure that boundaries surround the class name to avoid matching on
			 * substrings of a longer name. For example, the sequence "not-odd"
			 * should not match for the class "odd" even though "odd" is found
			 * within the class attribute text.
			 *
			 * See https://html.spec.whatwg.org/#attributes-3
			 * See https://html.spec.whatwg.org/#space-separated-tokens
			 */
			while (
				// phpcs:ignore WordPress.CodeAnalysis.AssignmentInCondition.FoundInWhileCondition
				false !== ( $class_at = strpos( $this->html, $this->sought_class_name, $class_at ) ) &&
				$class_at < $class_end
			) {
				/*
				 * Verify this class starts at a boundary.
				 */
				if ( $class_at > $class_start ) {
					$character = $this->html[ $class_at - 1 ];

					if ( ' ' !== $character && "\t" !== $character && "\f" !== $character && "\r" !== $character && "\n" !== $character ) {
						$class_at += strlen( $this->sought_class_name );
						continue;
					}
				}

				/*
				 * Verify this class ends at a boundary as well.
				 */
				if ( $class_at + strlen( $this->sought_class_name ) < $class_end ) {
					$character = $this->html[ $class_at + strlen( $this->sought_class_name ) ];

					if ( ' ' !== $character && "\t" !== $character && "\f" !== $character && "\r" !== $character && "\n" !== $character ) {
						$class_at += strlen( $this->sought_class_name );
						continue;
					}
				}

				return true;
			}

			return false;
		}

		return true;
	}
}

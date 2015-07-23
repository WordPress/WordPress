<?php
/**
 * WordPress API for creating bbcode like tags or what WordPress calls
 * "shortcodes." The tag and attribute parsing or regular expression code is
 * based on the Textpattern tag parser.
 *
 * A few examples are below:
 *
 * [shortcode /]
 * [shortcode foo="bar" baz="bing" /]
 * [shortcode foo="bar"]content[/shortcode]
 *
 * Shortcode tags support attributes and enclosed content, but does not entirely
 * support inline shortcodes in other shortcodes. You will have to call the
 * shortcode parser in your function to account for that.
 *
 * {@internal
 * Please be aware that the above note was made during the beta of WordPress 2.6
 * and in the future may not be accurate. Please update the note when it is no
 * longer the case.}}
 *
 * To apply shortcode tags to content:
 *
 * <code>
 * $out = do_shortcode($content);
 * </code>
 *
 * @link http://codex.wordpress.org/Shortcode_API
 *
 * @package WordPress
 * @subpackage Shortcodes
 * @since 2.5.0
 */

/**
 * Container for storing shortcode tags and their hook to call for the shortcode
 *
 * @since 2.5.0
 *
 * @name $shortcode_tags
 * @var array
 * @global array $shortcode_tags
 */
$shortcode_tags = array();

/**
 * Add hook for shortcode tag.
 *
 * There can only be one hook for each shortcode. Which means that if another
 * plugin has a similar shortcode, it will override yours or yours will override
 * theirs depending on which order the plugins are included and/or ran.
 *
 * Simplest example of a shortcode tag using the API:
 *
 * <code>
 * // [footag foo="bar"]
 * function footag_func($atts) {
 * 	return "foo = {$atts[foo]}";
 * }
 * add_shortcode('footag', 'footag_func');
 * </code>
 *
 * Example with nice attribute defaults:
 *
 * <code>
 * // [bartag foo="bar"]
 * function bartag_func($atts) {
 * 	$args = shortcode_atts(array(
 * 		'foo' => 'no foo',
 * 		'baz' => 'default baz',
 * 	), $atts);
 *
 * 	return "foo = {$args['foo']}";
 * }
 * add_shortcode('bartag', 'bartag_func');
 * </code>
 *
 * Example with enclosed content:
 *
 * <code>
 * // [baztag]content[/baztag]
 * function baztag_func($atts, $content='') {
 * 	return "content = $content";
 * }
 * add_shortcode('baztag', 'baztag_func');
 * </code>
 *
 * @since 2.5.0
 *
 * @uses $shortcode_tags
 *
 * @param string $tag Shortcode tag to be searched in post content.
 * @param callable $func Hook to run when shortcode is found.
 */
function add_shortcode($tag, $func) {
	global $shortcode_tags;

	if ( is_callable($func) )
		$shortcode_tags[$tag] = $func;
}

/**
 * Removes hook for shortcode.
 *
 * @since 2.5.0
 *
 * @uses $shortcode_tags
 *
 * @param string $tag shortcode tag to remove hook for.
 */
function remove_shortcode($tag) {
	global $shortcode_tags;

	unset($shortcode_tags[$tag]);
}

/**
 * Clear all shortcodes.
 *
 * This function is simple, it clears all of the shortcode tags by replacing the
 * shortcodes global by a empty array. This is actually a very efficient method
 * for removing all shortcodes.
 *
 * @since 2.5.0
 *
 * @uses $shortcode_tags
 */
function remove_all_shortcodes() {
	global $shortcode_tags;

	$shortcode_tags = array();
}

/**
 * Whether a registered shortcode exists named $tag
 *
 * @since 3.6.0
 *
 * @global array $shortcode_tags
 * @param string $tag
 * @return boolean
 */
function shortcode_exists( $tag ) {
	global $shortcode_tags;
	return array_key_exists( $tag, $shortcode_tags );
}

/**
 * Whether the passed content contains the specified shortcode
 *
 * @since 3.6.0
 *
 * @global array $shortcode_tags
 * @param string $tag
 * @return boolean
 */
function has_shortcode( $content, $tag ) {
	if ( false === strpos( $content, '[' ) ) {
		return false;
	}

	if ( shortcode_exists( $tag ) ) {
		preg_match_all( '/' . get_shortcode_regex() . '/s', $content, $matches, PREG_SET_ORDER );
		if ( empty( $matches ) )
			return false;

		foreach ( $matches as $shortcode ) {
			if ( $tag === $shortcode[2] ) {
				return true;
			} elseif ( ! empty( $shortcode[5] ) && has_shortcode( $shortcode[5], $tag ) ) {
				return true;
			}
		}
	}
	return false;
}

/**
 * Search content for shortcodes and filter shortcodes through their hooks.
 *
 * If there are no shortcode tags defined, then the content will be returned
 * without any filtering. This might cause issues when plugins are disabled but
 * the shortcode will still show up in the post or content.
 *
 * @since 2.5.0
 *
 * @uses $shortcode_tags
 * @uses get_shortcode_regex() Gets the search pattern for searching shortcodes.
 *
 * @param string $content Content to search for shortcodes
 * @param bool $ignore_html When true, shortcodes inside HTML elements will be skipped.
 * @return string Content with shortcodes filtered out.
 */
function do_shortcode( $content, $ignore_html = false ) {
	global $shortcode_tags;

	if ( false === strpos( $content, '[' ) ) {
		return $content;
	}

	if (empty($shortcode_tags) || !is_array($shortcode_tags))
		return $content;

	$tagnames = array_keys($shortcode_tags);
	$tagregexp = join( '|', array_map('preg_quote', $tagnames) );
	$pattern = "/\\[($tagregexp)/s";

	if ( 1 !== preg_match( $pattern, $content ) ) {
		// Avoids parsing HTML when there are no shortcodes or embeds anyway.
		return $content;
	}

	$content = do_shortcodes_in_html_tags( $content, $ignore_html );

	$pattern = get_shortcode_regex();
	$content = preg_replace_callback( "/$pattern/s", 'do_shortcode_tag', $content );
	
	// Always restore square braces so we don't break things like <!--[if IE ]>
	$content = unescape_invalid_shortcodes( $content );
	
	return $content;
}

/**
 * Retrieve the shortcode regular expression for searching.
 *
 * The regular expression combines the shortcode tags in the regular expression
 * in a regex class.
 *
 * The regular expression contains 6 different sub matches to help with parsing.
 *
 * 1 - An extra [ to allow for escaping shortcodes with double [[]]
 * 2 - The shortcode name
 * 3 - The shortcode argument list
 * 4 - The self closing /
 * 5 - The content of a shortcode when it wraps some content.
 * 6 - An extra ] to allow for escaping shortcodes with double [[]]
 *
 * @since 2.5.0
 *
 * @uses $shortcode_tags
 *
 * @return string The shortcode search regular expression
 */
function get_shortcode_regex() {
	global $shortcode_tags;
	$tagnames = array_keys($shortcode_tags);
	$tagregexp = join( '|', array_map('preg_quote', $tagnames) );

	// WARNING! Do not change this regex without changing do_shortcode_tag() and strip_shortcode_tag()
	// Also, see shortcode_unautop() and shortcode.js.
	return
		  '\\['                              // Opening bracket
		. '(\\[?)'                           // 1: Optional second opening bracket for escaping shortcodes: [[tag]]
		. "($tagregexp)"                     // 2: Shortcode name
		. '(?![\\w-])'                       // Not followed by word character or hyphen
		. '('                                // 3: Unroll the loop: Inside the opening shortcode tag
		.     '[^\\]\\/]*'                   // Not a closing bracket or forward slash
		.     '(?:'
		.         '\\/(?!\\])'               // A forward slash not followed by a closing bracket
		.         '[^\\]\\/]*'               // Not a closing bracket or forward slash
		.     ')*?'
		. ')'
		. '(?:'
		.     '(\\/)'                        // 4: Self closing tag ...
		.     '\\]'                          // ... and closing bracket
		. '|'
		.     '\\]'                          // Closing bracket
		.     '(?:'
		.         '('                        // 5: Unroll the loop: Optionally, anything between the opening and closing shortcode tags
		.             '[^\\[]*+'             // Not an opening bracket
		.             '(?:'
		.                 '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing shortcode tag
		.                 '[^\\[]*+'         // Not an opening bracket
		.             ')*+'
		.         ')'
		.         '\\[\\/\\2\\]'             // Closing shortcode tag
		.     ')?'
		. ')'
		. '(\\]?)';                          // 6: Optional second closing brocket for escaping shortcodes: [[tag]]
}

/**
 * Regular Expression callable for do_shortcode() for calling shortcode hook.
 * @see get_shortcode_regex for details of the match array contents.
 *
 * @since 2.5.0
 * @access private
 * @uses $shortcode_tags
 *
 * @param array $m Regular expression match array
 * @return mixed False on failure.
 */
function do_shortcode_tag( $m ) {
	global $shortcode_tags;

	// allow [[foo]] syntax for escaping a tag
	if ( $m[1] == '[' && $m[6] == ']' ) {
		return substr($m[0], 1, -1);
	}

	$tag = $m[2];
	$attr = shortcode_parse_atts( $m[3] );

	if ( isset( $m[5] ) ) {
		// enclosing tag - extra parameter
		return $m[1] . call_user_func( $shortcode_tags[$tag], $attr, $m[5], $tag ) . $m[6];
	} else {
		// self-closing tag
		return $m[1] . call_user_func( $shortcode_tags[$tag], $attr, null,  $tag ) . $m[6];
	}
}

/**
 * Search only inside HTML elements for shortcodes and process them.
 *
 * Any [ or ] characters remaining inside elements will be HTML encoded
 * to prevent interference with shortcodes that are outside the elements.
 * Assumes $content processed by KSES already.  Users with unfiltered_html
 * capability may get unexpected output if angle braces are nested in tags.
 *
 * @since 4.2.3
 *
 * @param string $content Content to search for shortcodes
 * @param bool $ignore_html When true, all square braces inside elements will be encoded.
 * @return string Content with shortcodes filtered out.
 */
function do_shortcodes_in_html_tags( $content, $ignore_html ) {
	// Normalize entities in unfiltered HTML before adding placeholders.
	$trans = array( '&#91;' => '&#091;', '&#93;' => '&#093;' );
	$content = strtr( $content, $trans );
	$trans = array( '[' => '&#91;', ']' => '&#93;' );
	
	$pattern = get_shortcode_regex();

	$comment_regex =
		  '!'           // Start of comment, after the <.
		. '(?:'         // Unroll the loop: Consume everything until --> is found.
		.     '-(?!->)' // Dash not followed by end of comment.
		.     '[^\-]*+' // Consume non-dashes.
		. ')*+'         // Loop possessively.
		. '(?:-->)?';   // End of comment. If not found, match all input.

	$regex =
		  '/('                   // Capture the entire match.
		.     '<'                // Find start of element.
		.     '(?(?=!--)'        // Is this a comment?
		.         $comment_regex // Find end of comment.
		.     '|'
		.         '[^>]*>?'      // Find end of element. If not found, match all input.
		.     ')'
		. ')/s';

	$textarr = preg_split( $regex, $content, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY );

	foreach ( $textarr as &$element ) {
		if ( '<' !== $element[0] ) {
			continue;
		}

		$noopen = false === strpos( $element, '[' );
		$noclose = false === strpos( $element, ']' );
		if ( $noopen || $noclose ) {
			// This element does not contain shortcodes.
			if ( $noopen xor $noclose ) {
				// Need to encode stray [ or ] chars.
				$element = strtr( $element, $trans );
			}
			continue;
		}

		if ( $ignore_html || '<!--' === substr( $element, 0, 4 ) ) {
			// Encode all [ and ] chars.
			$element = strtr( $element, $trans );
			continue;
		}

		$attributes = wp_kses_attr_parse( $element );
		if ( false === $attributes ) {
			// Looks like we found some crazy unfiltered HTML.  Skipping it for sanity.
			$element = strtr( $element, $trans );
			continue;
		}
		
		// Get element name
		$front = array_shift( $attributes );
		$back = array_pop( $attributes );
		$matches = array();
		preg_match('%[a-zA-Z0-9]+%', $front, $matches);
		$elname = $matches[0];
		
		// Look for shortcodes in each attribute separately.
		foreach ( $attributes as &$attr ) {
			$open = strpos( $attr, '[' );
			$close = strpos( $attr, ']' );
			if ( false === $open || false === $close ) {
				continue; // Go to next attribute.  Square braces will be escaped at end of loop.
			}
			$double = strpos( $attr, '"' );
			$single = strpos( $attr, "'" );
			if ( ( false === $single || $open < $single ) && ( false === $double || $open < $double ) ) {
				// $attr like '[shortcode]' or 'name = [shortcode]' implies unfiltered_html.
				// In this specific situation we assume KSES did not run because the input
				// was written by an administrator, so we should avoid changing the output
				// and we do not need to run KSES here.
				$attr = preg_replace_callback( "/$pattern/s", 'do_shortcode_tag', $attr );
			} else {
				// $attr like 'name = "[shortcode]"' or "name = '[shortcode]'"
				// We do not know if $content was unfiltered. Assume KSES ran before shortcodes.
				$count = 0;
				$new_attr = preg_replace_callback( "/$pattern/s", 'do_shortcode_tag', $attr, -1, $count );
				if ( $count > 0 ) {
					// Sanitize the shortcode output using KSES.
					$new_attr = wp_kses_one_attr( $new_attr, $elname );
					if ( '' !== $new_attr ) {
						// The shortcode is safe to use now.
						$attr = $new_attr;
					}
				}
			}
		}
		$element = $front . implode( '', $attributes ) . $back;
		
		// Now encode any remaining [ or ] chars.
		$element = strtr( $element, $trans );
	}
	
	$content = implode( '', $textarr );
	
	return $content;
}

/**
 * Remove placeholders added by do_shortcodes_in_html_tags().
 *
 * @since 4.2.3
 *
 * @param string $content Content to search for placeholders.
 * @return string Content with placeholders removed.
 */
function unescape_invalid_shortcodes( $content ) {
        // Clean up entire string, avoids re-parsing HTML.
        $trans = array( '&#91;' => '[', '&#93;' => ']' );
        $content = strtr( $content, $trans );
        
        return $content;
}

/**
 * Retrieve all attributes from the shortcodes tag.
 *
 * The attributes list has the attribute name as the key and the value of the
 * attribute as the value in the key/value pair. This allows for easier
 * retrieval of the attributes, since all attributes have to be known.
 *
 * @since 2.5.0
 *
 * @param string $text
 * @return array List of attributes and their value.
 */
function shortcode_parse_atts($text) {
	$atts = array();
	$pattern = '/(\w+)\s*=\s*"([^"]*)"(?:\s|$)|(\w+)\s*=\s*\'([^\']*)\'(?:\s|$)|(\w+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|(\S+)(?:\s|$)/';
	$text = preg_replace("/[\x{00a0}\x{200b}]+/u", " ", $text);
	if ( preg_match_all($pattern, $text, $match, PREG_SET_ORDER) ) {
		foreach ($match as $m) {
			if (!empty($m[1]))
				$atts[strtolower($m[1])] = stripcslashes($m[2]);
			elseif (!empty($m[3]))
				$atts[strtolower($m[3])] = stripcslashes($m[4]);
			elseif (!empty($m[5]))
				$atts[strtolower($m[5])] = stripcslashes($m[6]);
			elseif (isset($m[7]) and strlen($m[7]))
				$atts[] = stripcslashes($m[7]);
			elseif (isset($m[8]))
				$atts[] = stripcslashes($m[8]);
		}
	} else {
		$atts = ltrim($text);
	}
	return $atts;
}

/**
 * Combine user attributes with known attributes and fill in defaults when needed.
 *
 * The pairs should be considered to be all of the attributes which are
 * supported by the caller and given as a list. The returned attributes will
 * only contain the attributes in the $pairs list.
 *
 * If the $atts list has unsupported attributes, then they will be ignored and
 * removed from the final returned list.
 *
 * @since 2.5.0
 *
 * @param array $pairs Entire list of supported attributes and their defaults.
 * @param array $atts User defined attributes in shortcode tag.
 * @param string $shortcode Optional. The name of the shortcode, provided for context to enable filtering
 * @return array Combined and filtered attribute list.
 */
function shortcode_atts( $pairs, $atts, $shortcode = '' ) {
	$atts = (array)$atts;
	$out = array();
	foreach($pairs as $name => $default) {
		if ( array_key_exists($name, $atts) )
			$out[$name] = $atts[$name];
		else
			$out[$name] = $default;
	}
	/**
	 * Filter a shortcode's default attributes.
	 *
	 * If the third parameter of the shortcode_atts() function is present then this filter is available.
	 * The third parameter, $shortcode, is the name of the shortcode.
	 *
	 * @since 3.6.0
	 *
	 * @param array $out The output array of shortcode attributes.
	 * @param array $pairs The supported attributes and their defaults.
	 * @param array $atts The user defined shortcode attributes.
	 */
	if ( $shortcode )
		$out = apply_filters( "shortcode_atts_{$shortcode}", $out, $pairs, $atts );

	return $out;
}

/**
 * Remove all shortcode tags from the given content.
 *
 * @since 2.5.0
 *
 * @uses $shortcode_tags
 *
 * @param string $content Content to remove shortcode tags.
 * @return string Content without shortcode tags.
 */
function strip_shortcodes( $content ) {
	global $shortcode_tags;

	if ( false === strpos( $content, '[' ) ) {
		return $content;
	}

	if (empty($shortcode_tags) || !is_array($shortcode_tags))
		return $content;

	$content = do_shortcodes_in_html_tags( $content, true );

	$pattern = get_shortcode_regex();
	$content = preg_replace_callback( "/$pattern/s", 'strip_shortcode_tag', $content );

	// Always restore square braces so we don't break things like <!--[if IE ]>
	$content = unescape_invalid_shortcodes( $content );
	
	return $content;
}

function strip_shortcode_tag( $m ) {
	// allow [[foo]] syntax for escaping a tag
	if ( $m[1] == '[' && $m[6] == ']' ) {
		return substr($m[0], 1, -1);
	}

	return $m[1] . $m[6];
}

add_filter('the_content', 'do_shortcode', 11); // AFTER wpautop()

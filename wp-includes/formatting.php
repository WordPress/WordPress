<?php
/**
 * Main WordPress Formatting API.
 *
 * Handles many functions for formatting output.
 *
 * @package WordPress
 */

/**
 * Replaces common plain text characters into formatted entities
 *
 * As an example,
 *
 *     'cause today's effort makes it worth tomorrow's "holiday" ...
 *
 * Becomes:
 *
 *     &#8217;cause today&#8217;s effort makes it worth tomorrow&#8217;s &#8220;holiday&#8221; &#8230;
 *
 * Code within certain html blocks are skipped.
 *
 * Do not use this function before the 'init' action hook; everything will break.
 *
 * @since 0.71
 *
 * @global array $wp_cockneyreplace Array of formatted entities for certain common phrases
 * @global array $shortcode_tags
 * @staticvar array $static_characters
 * @staticvar array $static_replacements
 * @staticvar array $dynamic_characters
 * @staticvar array $dynamic_replacements
 * @staticvar array $default_no_texturize_tags
 * @staticvar array $default_no_texturize_shortcodes
 * @staticvar bool  $run_texturize
 *
 * @param string $text The text to be formatted
 * @param bool   $reset Set to true for unit testing. Translated patterns will reset.
 * @return string The string replaced with html entities
 */
function wptexturize( $text, $reset = false ) {
	global $wp_cockneyreplace, $shortcode_tags;
	static $static_characters = null,
		$static_replacements = null,
		$dynamic_characters = null,
		$dynamic_replacements = null,
		$default_no_texturize_tags = null,
		$default_no_texturize_shortcodes = null,
		$run_texturize = true,
		$apos = null,
		$prime = null,
		$double_prime = null,
		$opening_quote = null,
		$closing_quote = null,
		$opening_single_quote = null,
		$closing_single_quote = null,
		$open_q_flag = '<!--oq-->',
		$open_sq_flag = '<!--osq-->',
		$apos_flag = '<!--apos-->';

	// If there's nothing to do, just stop.
	if ( empty( $text ) || false === $run_texturize ) {
		return $text;
	}

	// Set up static variables. Run once only.
	if ( $reset || ! isset( $static_characters ) ) {
		/**
		 * Filter whether to skip running wptexturize().
		 *
		 * Passing false to the filter will effectively short-circuit wptexturize().
		 * returning the original text passed to the function instead.
		 *
		 * The filter runs only once, the first time wptexturize() is called.
		 *
		 * @since 4.0.0
		 *
		 * @see wptexturize()
		 *
		 * @param bool $run_texturize Whether to short-circuit wptexturize().
		 */
		$run_texturize = apply_filters( 'run_wptexturize', $run_texturize );
		if ( false === $run_texturize ) {
			return $text;
		}

		/* translators: opening curly double quote */
		$opening_quote = _x( '&#8220;', 'opening curly double quote' );
		/* translators: closing curly double quote */
		$closing_quote = _x( '&#8221;', 'closing curly double quote' );

		/* translators: apostrophe, for example in 'cause or can't */
		$apos = _x( '&#8217;', 'apostrophe' );

		/* translators: prime, for example in 9' (nine feet) */
		$prime = _x( '&#8242;', 'prime' );
		/* translators: double prime, for example in 9" (nine inches) */
		$double_prime = _x( '&#8243;', 'double prime' );

		/* translators: opening curly single quote */
		$opening_single_quote = _x( '&#8216;', 'opening curly single quote' );
		/* translators: closing curly single quote */
		$closing_single_quote = _x( '&#8217;', 'closing curly single quote' );

		/* translators: en dash */
		$en_dash = _x( '&#8211;', 'en dash' );
		/* translators: em dash */
		$em_dash = _x( '&#8212;', 'em dash' );

		$default_no_texturize_tags = array('pre', 'code', 'kbd', 'style', 'script', 'tt');
		$default_no_texturize_shortcodes = array('code');

		// if a plugin has provided an autocorrect array, use it
		if ( isset($wp_cockneyreplace) ) {
			$cockney = array_keys( $wp_cockneyreplace );
			$cockneyreplace = array_values( $wp_cockneyreplace );
		} else {
			/* translators: This is a comma-separated list of words that defy the syntax of quotations in normal use,
			 * for example...  'We do not have enough words yet' ... is a typical quoted phrase.  But when we write
			 * lines of code 'til we have enough of 'em, then we need to insert apostrophes instead of quotes.
			 */
			$cockney = explode( ',', _x( "'tain't,'twere,'twas,'tis,'twill,'til,'bout,'nuff,'round,'cause,'em",
				'Comma-separated list of words to texturize in your language' ) );

			$cockneyreplace = explode( ',', _x( '&#8217;tain&#8217;t,&#8217;twere,&#8217;twas,&#8217;tis,&#8217;twill,&#8217;til,&#8217;bout,&#8217;nuff,&#8217;round,&#8217;cause,&#8217;em',
				'Comma-separated list of replacement words in your language' ) );
		}

		$static_characters = array_merge( array( '...', '``', '\'\'', ' (tm)' ), $cockney );
		$static_replacements = array_merge( array( '&#8230;', $opening_quote, $closing_quote, ' &#8482;' ), $cockneyreplace );


		// Pattern-based replacements of characters.
		// Sort the remaining patterns into several arrays for performance tuning.
		$dynamic_characters = array( 'apos' => array(), 'quote' => array(), 'dash' => array() );
		$dynamic_replacements = array( 'apos' => array(), 'quote' => array(), 'dash' => array() );
		$dynamic = array();
		$spaces = wp_spaces_regexp();

		// '99' and '99" are ambiguous among other patterns; assume it's an abbreviated year at the end of a quotation.
		if ( "'" !== $apos || "'" !== $closing_single_quote ) {
			$dynamic[ '/\'(\d\d)\'(?=\Z|[.,:;!?)}\-\]]|&gt;|' . $spaces . ')/' ] = $apos_flag . '$1' . $closing_single_quote;
		}
		if ( "'" !== $apos || '"' !== $closing_quote ) {
			$dynamic[ '/\'(\d\d)"(?=\Z|[.,:;!?)}\-\]]|&gt;|' . $spaces . ')/' ] = $apos_flag . '$1' . $closing_quote;
		}

		// '99 '99s '99's (apostrophe)  But never '9 or '99% or '999 or '99.0.
		if ( "'" !== $apos ) {
			$dynamic[ '/\'(?=\d\d(?:\Z|(?![%\d]|[.,]\d)))/' ] = $apos_flag;
		}

		// Quoted Numbers like '0.42'
		if ( "'" !== $opening_single_quote && "'" !== $closing_single_quote ) {
			$dynamic[ '/(?<=\A|' . $spaces . ')\'(\d[.,\d]*)\'/' ] = $open_sq_flag . '$1' . $closing_single_quote;
		}

		// Single quote at start, or preceded by (, {, <, [, ", -, or spaces.
		if ( "'" !== $opening_single_quote ) {
			$dynamic[ '/(?<=\A|[([{"\-]|&lt;|' . $spaces . ')\'/' ] = $open_sq_flag;
		}

		// Apostrophe in a word.  No spaces, double apostrophes, or other punctuation.
		if ( "'" !== $apos ) {
			$dynamic[ '/(?<!' . $spaces . ')\'(?!\Z|[.,:;!?"\'(){}[\]\-]|&[lg]t;|' . $spaces . ')/' ] = $apos_flag;
		}

		$dynamic_characters['apos'] = array_keys( $dynamic );
		$dynamic_replacements['apos'] = array_values( $dynamic );
		$dynamic = array();

		// Quoted Numbers like "42"
		if ( '"' !== $opening_quote && '"' !== $closing_quote ) {
			$dynamic[ '/(?<=\A|' . $spaces . ')"(\d[.,\d]*)"/' ] = $open_q_flag . '$1' . $closing_quote;
		}

		// Double quote at start, or preceded by (, {, <, [, -, or spaces, and not followed by spaces.
		if ( '"' !== $opening_quote ) {
			$dynamic[ '/(?<=\A|[([{\-]|&lt;|' . $spaces . ')"(?!' . $spaces . ')/' ] = $open_q_flag;
		}

		$dynamic_characters['quote'] = array_keys( $dynamic );
		$dynamic_replacements['quote'] = array_values( $dynamic );
		$dynamic = array();

		// Dashes and spaces
		$dynamic[ '/---/' ] = $em_dash;
		$dynamic[ '/(?<=^|' . $spaces . ')--(?=$|' . $spaces . ')/' ] = $em_dash;
		$dynamic[ '/(?<!xn)--/' ] = $en_dash;
		$dynamic[ '/(?<=^|' . $spaces . ')-(?=$|' . $spaces . ')/' ] = $en_dash;

		$dynamic_characters['dash'] = array_keys( $dynamic );
		$dynamic_replacements['dash'] = array_values( $dynamic );
	}

	// Must do this every time in case plugins use these filters in a context sensitive manner
	/**
	 * Filter the list of HTML elements not to texturize.
	 *
	 * @since 2.8.0
	 *
	 * @param array $default_no_texturize_tags An array of HTML element names.
	 */
	$no_texturize_tags = apply_filters( 'no_texturize_tags', $default_no_texturize_tags );
	/**
	 * Filter the list of shortcodes not to texturize.
	 *
	 * @since 2.8.0
	 *
	 * @param array $default_no_texturize_shortcodes An array of shortcode names.
	 */
	$no_texturize_shortcodes = apply_filters( 'no_texturize_shortcodes', $default_no_texturize_shortcodes );

	$no_texturize_tags_stack = array();
	$no_texturize_shortcodes_stack = array();

	// Look for shortcodes and HTML elements.

	preg_match_all( '@\[/?([^<>&/\[\]\x00-\x20=]++)@', $text, $matches );
	$tagnames = array_intersect( array_keys( $shortcode_tags ), $matches[1] );
	$found_shortcodes = ! empty( $tagnames );
	$shortcode_regex = $found_shortcodes ? _get_wptexturize_shortcode_regex( $tagnames ) : '';
	$regex = _get_wptexturize_split_regex( $shortcode_regex );

	$textarr = preg_split( $regex, $text, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY );

	foreach ( $textarr as &$curl ) {
		// Only call _wptexturize_pushpop_element if $curl is a delimiter.
		$first = $curl[0];
		if ( '<' === $first ) {
			if ( '<!--' === substr( $curl, 0, 4 ) ) {
				// This is an HTML comment delimiter.
				continue;
			} else {
				// This is an HTML element delimiter.

				// Replace each & with &#038; unless it already looks like an entity.
				$curl = preg_replace( '/&(?!#(?:\d+|x[a-f0-9]+);|[a-z1-4]{1,8};)/i', '&#038;', $curl );

				_wptexturize_pushpop_element( $curl, $no_texturize_tags_stack, $no_texturize_tags );
			}

		} elseif ( '' === trim( $curl ) ) {
			// This is a newline between delimiters.  Performance improves when we check this.
			continue;

		} elseif ( '[' === $first && $found_shortcodes && 1 === preg_match( '/^' . $shortcode_regex . '$/', $curl ) ) {
			// This is a shortcode delimiter.

			if ( '[[' !== substr( $curl, 0, 2 ) && ']]' !== substr( $curl, -2 ) ) {
				// Looks like a normal shortcode.
				_wptexturize_pushpop_element( $curl, $no_texturize_shortcodes_stack, $no_texturize_shortcodes );
			} else {
				// Looks like an escaped shortcode.
				continue;
			}

		} elseif ( empty( $no_texturize_shortcodes_stack ) && empty( $no_texturize_tags_stack ) ) {
			// This is neither a delimiter, nor is this content inside of no_texturize pairs.  Do texturize.

			$curl = str_replace( $static_characters, $static_replacements, $curl );

			if ( false !== strpos( $curl, "'" ) ) {
				$curl = preg_replace( $dynamic_characters['apos'], $dynamic_replacements['apos'], $curl );
				$curl = wptexturize_primes( $curl, "'", $prime, $open_sq_flag, $closing_single_quote );
				$curl = str_replace( $apos_flag, $apos, $curl );
				$curl = str_replace( $open_sq_flag, $opening_single_quote, $curl );
			}
			if ( false !== strpos( $curl, '"' ) ) {
				$curl = preg_replace( $dynamic_characters['quote'], $dynamic_replacements['quote'], $curl );
				$curl = wptexturize_primes( $curl, '"', $double_prime, $open_q_flag, $closing_quote );
				$curl = str_replace( $open_q_flag, $opening_quote, $curl );
			}
			if ( false !== strpos( $curl, '-' ) ) {
				$curl = preg_replace( $dynamic_characters['dash'], $dynamic_replacements['dash'], $curl );
			}

			// 9x9 (times), but never 0x9999
			if ( 1 === preg_match( '/(?<=\d)x\d/', $curl ) ) {
				// Searching for a digit is 10 times more expensive than for the x, so we avoid doing this one!
				$curl = preg_replace( '/\b(\d(?(?<=0)[\d\.,]+|[\d\.,]*))x(\d[\d\.,]*)\b/', '$1&#215;$2', $curl );
			}

			// Replace each & with &#038; unless it already looks like an entity.
			$curl = preg_replace( '/&(?!#(?:\d+|x[a-f0-9]+);|[a-z1-4]{1,8};)/i', '&#038;', $curl );
		}
	}

	return implode( '', $textarr );
}

/**
 * Implements a logic tree to determine whether or not "7'." represents seven feet,
 * then converts the special char into either a prime char or a closing quote char.
 *
 * @since 4.3.0
 *
 * @param string $haystack    The plain text to be searched.
 * @param string $needle      The character to search for such as ' or ".
 * @param string $prime       The prime char to use for replacement.
 * @param string $open_quote  The opening quote char. Opening quote replacement must be
 *                            accomplished already.
 * @param string $close_quote The closing quote char to use for replacement.
 * @return string The $haystack value after primes and quotes replacements.
 */
function wptexturize_primes( $haystack, $needle, $prime, $open_quote, $close_quote ) {
	$spaces = wp_spaces_regexp();
	$flag = '<!--wp-prime-or-quote-->';
	$quote_pattern = "/$needle(?=\\Z|[.,:;!?)}\\-\\]]|&gt;|" . $spaces . ")/";
	$prime_pattern    = "/(?<=\\d)$needle/";
	$flag_after_digit = "/(?<=\\d)$flag/";
	$flag_no_digit    = "/(?<!\\d)$flag/";

	$sentences = explode( $open_quote, $haystack );

	foreach ( $sentences as $key => &$sentence ) {
		if ( false === strpos( $sentence, $needle ) ) {
			continue;
		} elseif ( 0 !== $key && 0 === substr_count( $sentence, $close_quote ) ) {
			$sentence = preg_replace( $quote_pattern, $flag, $sentence, -1, $count );
			if ( $count > 1 ) {
				// This sentence appears to have multiple closing quotes.  Attempt Vulcan logic.
				$sentence = preg_replace( $flag_no_digit, $close_quote, $sentence, -1, $count2 );
				if ( 0 === $count2 ) {
					// Try looking for a quote followed by a period.
					$count2 = substr_count( $sentence, "$flag." );
					if ( $count2 > 0 ) {
						// Assume the rightmost quote-period match is the end of quotation.
						$pos = strrpos( $sentence, "$flag." );
					} else {
						// When all else fails, make the rightmost candidate a closing quote.
						// This is most likely to be problematic in the context of bug #18549.
						$pos = strrpos( $sentence, $flag );
					}
					$sentence = substr_replace( $sentence, $close_quote, $pos, strlen( $flag ) );
				}
				// Use conventional replacement on any remaining primes and quotes.
				$sentence = preg_replace( $prime_pattern, $prime, $sentence );
				$sentence = preg_replace( $flag_after_digit, $prime, $sentence );
				$sentence = str_replace( $flag, $close_quote, $sentence );
			} elseif ( 1 == $count ) {
				// Found only one closing quote candidate, so give it priority over primes.
				$sentence = str_replace( $flag, $close_quote, $sentence );
				$sentence = preg_replace( $prime_pattern, $prime, $sentence );
			} else {
				// No closing quotes found.  Just run primes pattern.
				$sentence = preg_replace( $prime_pattern, $prime, $sentence );
			}
		} else {
			$sentence = preg_replace( $prime_pattern, $prime, $sentence );
			$sentence = preg_replace( $quote_pattern, $close_quote, $sentence );
		}
		if ( '"' == $needle && false !== strpos( $sentence, '"' ) ) {
			$sentence = str_replace( '"', $close_quote, $sentence );
		}
	}

	return implode( $open_quote, $sentences );
}

/**
 * Search for disabled element tags. Push element to stack on tag open and pop
 * on tag close.
 *
 * Assumes first char of $text is tag opening and last char is tag closing.
 * Assumes second char of $text is optionally '/' to indicate closing as in </html>.
 *
 * @since 2.9.0
 * @access private
 *
 * @param string $text Text to check. Must be a tag like `<html>` or `[shortcode]`.
 * @param array  $stack List of open tag elements.
 * @param array  $disabled_elements The tag names to match against. Spaces are not allowed in tag names.
 */
function _wptexturize_pushpop_element( $text, &$stack, $disabled_elements ) {
	// Is it an opening tag or closing tag?
	if ( '/' !== $text[1] ) {
		$opening_tag = true;
		$name_offset = 1;
	} elseif ( 0 == count( $stack ) ) {
		// Stack is empty. Just stop.
		return;
	} else {
		$opening_tag = false;
		$name_offset = 2;
	}

	// Parse out the tag name.
	$space = strpos( $text, ' ' );
	if ( false === $space ) {
		$space = -1;
	} else {
		$space -= $name_offset;
	}
	$tag = substr( $text, $name_offset, $space );

	// Handle disabled tags.
	if ( in_array( $tag, $disabled_elements ) ) {
		if ( $opening_tag ) {
			/*
			 * This disables texturize until we find a closing tag of our type
			 * (e.g. <pre>) even if there was invalid nesting before that
			 *
			 * Example: in the case <pre>sadsadasd</code>"baba"</pre>
			 *          "baba" won't be texturize
			 */

			array_push( $stack, $tag );
		} elseif ( end( $stack ) == $tag ) {
			array_pop( $stack );
		}
	}
}

/**
 * Replaces double line-breaks with paragraph elements.
 *
 * A group of regex replaces used to identify text formatted with newlines and
 * replace double line-breaks with HTML paragraph tags. The remaining line-breaks
 * after conversion become <<br />> tags, unless $br is set to '0' or 'false'.
 *
 * @since 0.71
 *
 * @param string $pee The text which has to be formatted.
 * @param bool   $br  Optional. If set, this will convert all remaining line-breaks
 *                    after paragraphing. Default true.
 * @return string Text which has been converted into correct paragraph tags.
 */
function wpautop( $pee, $br = true ) {
	$pre_tags = array();

	if ( trim($pee) === '' )
		return '';

	// Just to make things a little easier, pad the end.
	$pee = $pee . "\n";

	/*
	 * Pre tags shouldn't be touched by autop.
	 * Replace pre tags with placeholders and bring them back after autop.
	 */
	if ( strpos($pee, '<pre') !== false ) {
		$pee_parts = explode( '</pre>', $pee );
		$last_pee = array_pop($pee_parts);
		$pee = '';
		$i = 0;

		foreach ( $pee_parts as $pee_part ) {
			$start = strpos($pee_part, '<pre');

			// Malformed html?
			if ( $start === false ) {
				$pee .= $pee_part;
				continue;
			}

			$name = "<pre wp-pre-tag-$i></pre>";
			$pre_tags[$name] = substr( $pee_part, $start ) . '</pre>';

			$pee .= substr( $pee_part, 0, $start ) . $name;
			$i++;
		}

		$pee .= $last_pee;
	}
	// Change multiple <br>s into two line breaks, which will turn into paragraphs.
	$pee = preg_replace('|<br\s*/?>\s*<br\s*/?>|', "\n\n", $pee);

	$allblocks = '(?:table|thead|tfoot|caption|col|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|form|map|area|blockquote|address|math|style|p|h[1-6]|hr|fieldset|legend|section|article|aside|hgroup|header|footer|nav|figure|figcaption|details|menu|summary)';

	// Add a single line break above block-level opening tags.
	$pee = preg_replace('!(<' . $allblocks . '[\s/>])!', "\n$1", $pee);

	// Add a double line break below block-level closing tags.
	$pee = preg_replace('!(</' . $allblocks . '>)!', "$1\n\n", $pee);

	// Standardize newline characters to "\n".
	$pee = str_replace(array("\r\n", "\r"), "\n", $pee);

	// Find newlines in all elements and add placeholders.
	$pee = wp_replace_in_html_tags( $pee, array( "\n" => " <!-- wpnl --> " ) );

	// Collapse line breaks before and after <option> elements so they don't get autop'd.
	if ( strpos( $pee, '<option' ) !== false ) {
		$pee = preg_replace( '|\s*<option|', '<option', $pee );
		$pee = preg_replace( '|</option>\s*|', '</option>', $pee );
	}

	/*
	 * Collapse line breaks inside <object> elements, before <param> and <embed> elements
	 * so they don't get autop'd.
	 */
	if ( strpos( $pee, '</object>' ) !== false ) {
		$pee = preg_replace( '|(<object[^>]*>)\s*|', '$1', $pee );
		$pee = preg_replace( '|\s*</object>|', '</object>', $pee );
		$pee = preg_replace( '%\s*(</?(?:param|embed)[^>]*>)\s*%', '$1', $pee );
	}

	/*
	 * Collapse line breaks inside <audio> and <video> elements,
	 * before and after <source> and <track> elements.
	 */
	if ( strpos( $pee, '<source' ) !== false || strpos( $pee, '<track' ) !== false ) {
		$pee = preg_replace( '%([<\[](?:audio|video)[^>\]]*[>\]])\s*%', '$1', $pee );
		$pee = preg_replace( '%\s*([<\[]/(?:audio|video)[>\]])%', '$1', $pee );
		$pee = preg_replace( '%\s*(<(?:source|track)[^>]*>)\s*%', '$1', $pee );
	}

	// Remove more than two contiguous line breaks.
	$pee = preg_replace("/\n\n+/", "\n\n", $pee);

	// Split up the contents into an array of strings, separated by double line breaks.
	$pees = preg_split('/\n\s*\n/', $pee, -1, PREG_SPLIT_NO_EMPTY);

	// Reset $pee prior to rebuilding.
	$pee = '';

	// Rebuild the content as a string, wrapping every bit with a <p>.
	foreach ( $pees as $tinkle ) {
		$pee .= '<p>' . trim($tinkle, "\n") . "</p>\n";
	}

	// Under certain strange conditions it could create a P of entirely whitespace.
	$pee = preg_replace('|<p>\s*</p>|', '', $pee);

	// Add a closing <p> inside <div>, <address>, or <form> tag if missing.
	$pee = preg_replace('!<p>([^<]+)</(div|address|form)>!', "<p>$1</p></$2>", $pee);

	// If an opening or closing block element tag is wrapped in a <p>, unwrap it.
	$pee = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee);

	// In some cases <li> may get wrapped in <p>, fix them.
	$pee = preg_replace("|<p>(<li.+?)</p>|", "$1", $pee);

	// If a <blockquote> is wrapped with a <p>, move it inside the <blockquote>.
	$pee = preg_replace('|<p><blockquote([^>]*)>|i', "<blockquote$1><p>", $pee);
	$pee = str_replace('</blockquote></p>', '</p></blockquote>', $pee);

	// If an opening or closing block element tag is preceded by an opening <p> tag, remove it.
	$pee = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)!', "$1", $pee);

	// If an opening or closing block element tag is followed by a closing <p> tag, remove it.
	$pee = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee);

	// Optionally insert line breaks.
	if ( $br ) {
		// Replace newlines that shouldn't be touched with a placeholder.
		$pee = preg_replace_callback('/<(script|style).*?<\/\\1>/s', '_autop_newline_preservation_helper', $pee);

		// Normalize <br>
		$pee = str_replace( array( '<br>', '<br/>' ), '<br />', $pee );

		// Replace any new line characters that aren't preceded by a <br /> with a <br />.
		$pee = preg_replace('|(?<!<br />)\s*\n|', "<br />\n", $pee);

		// Replace newline placeholders with newlines.
		$pee = str_replace('<WPPreserveNewline />', "\n", $pee);
	}

	// If a <br /> tag is after an opening or closing block tag, remove it.
	$pee = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*<br />!', "$1", $pee);

	// If a <br /> tag is before a subset of opening or closing block tags, remove it.
	$pee = preg_replace('!<br />(\s*</?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)[^>]*>)!', '$1', $pee);
	$pee = preg_replace( "|\n</p>$|", '</p>', $pee );

	// Replace placeholder <pre> tags with their original content.
	if ( !empty($pre_tags) )
		$pee = str_replace(array_keys($pre_tags), array_values($pre_tags), $pee);

	// Restore newlines in all elements.
	if ( false !== strpos( $pee, '<!-- wpnl -->' ) ) {
		$pee = str_replace( array( ' <!-- wpnl --> ', '<!-- wpnl -->' ), "\n", $pee );
	}

	return $pee;
}

/**
 * Separate HTML elements and comments from the text.
 *
 * @since 4.2.4
 *
 * @param string $input The text which has to be formatted.
 * @return array The formatted text.
 */
function wp_html_split( $input ) {
	return preg_split( get_html_split_regex(), $input, -1, PREG_SPLIT_DELIM_CAPTURE );
}

/**
 * Retrieve the regular expression for an HTML element.
 *
 * @since 4.4.0
 *
 * @return string The regular expression
 */
function get_html_split_regex() {
	static $regex;

	if ( ! isset( $regex ) ) {
		$comments =
			  '!'           // Start of comment, after the <.
			. '(?:'         // Unroll the loop: Consume everything until --> is found.
			.     '-(?!->)' // Dash not followed by end of comment.
			.     '[^\-]*+' // Consume non-dashes.
			. ')*+'         // Loop possessively.
			. '(?:-->)?';   // End of comment. If not found, match all input.

		$cdata =
			  '!\[CDATA\['  // Start of comment, after the <.
			. '[^\]]*+'     // Consume non-].
			. '(?:'         // Unroll the loop: Consume everything until ]]> is found.
			.     '](?!]>)' // One ] not followed by end of comment.
			.     '[^\]]*+' // Consume non-].
			. ')*+'         // Loop possessively.
			. '(?:]]>)?';   // End of comment. If not found, match all input.

		$escaped =
			  '(?='           // Is the element escaped?
			.    '!--'
			. '|'
			.    '!\[CDATA\['
			. ')'
			. '(?(?=!-)'      // If yes, which type?
			.     $comments
			. '|'
			.     $cdata
			. ')';

		$regex =
			  '/('              // Capture the entire match.
			.     '<'           // Find start of element.
			.     '(?'          // Conditional expression follows.
			.         $escaped  // Find end of escaped element.
			.     '|'           // ... else ...
			.         '[^>]*>?' // Find end of normal element.
			.     ')'
			. ')/';
	}

	return $regex;
}

/**
 * Retrieve the combined regular expression for HTML and shortcodes.
 *
 * @access private
 * @ignore
 * @internal This function will be removed in 4.5.0 per Shortcode API Roadmap.
 * @since 4.4.0
 *
 * @param string $shortcode_regex The result from _get_wptexturize_shortcode_regex().  Optional.
 * @return string The regular expression
 */
function _get_wptexturize_split_regex( $shortcode_regex = '' ) {
	static $html_regex;

	if ( ! isset( $html_regex ) ) {
		$comment_regex =
			  '!'           // Start of comment, after the <.
			. '(?:'         // Unroll the loop: Consume everything until --> is found.
			.     '-(?!->)' // Dash not followed by end of comment.
			.     '[^\-]*+' // Consume non-dashes.
			. ')*+'         // Loop possessively.
			. '(?:-->)?';   // End of comment. If not found, match all input.

		$html_regex =			 // Needs replaced with wp_html_split() per Shortcode API Roadmap.
			  '<'                // Find start of element.
			. '(?(?=!--)'        // Is this a comment?
			.     $comment_regex // Find end of comment.
			. '|'
			.     '[^>]*>?'      // Find end of element. If not found, match all input.
			. ')';
	}

	if ( empty( $shortcode_regex ) ) {
		$regex = '/(' . $html_regex . ')/';
	} else {
		$regex = '/(' . $html_regex . '|' . $shortcode_regex . ')/';
	}

	return $regex;
}

/**
 * Retrieve the regular expression for shortcodes.
 *
 * @access private
 * @ignore
 * @internal This function will be removed in 4.5.0 per Shortcode API Roadmap.
 * @since 4.4.0
 *
 * @param array $tagnames List of shortcodes to find.
 * @return string The regular expression
 */
function _get_wptexturize_shortcode_regex( $tagnames ) {
	$tagregexp = join( '|', array_map( 'preg_quote', $tagnames ) );
	$tagregexp = "(?:$tagregexp)(?=[\\s\\]\\/])"; // Excerpt of get_shortcode_regex().
	$regex =
		  '\['              // Find start of shortcode.
		. '[\/\[]?'         // Shortcodes may begin with [/ or [[
		. $tagregexp        // Only match registered shortcodes, because performance.
		. '(?:'
		.     '[^\[\]<>]+'  // Shortcodes do not contain other shortcodes. Quantifier critical.
		. '|'
		.     '<[^\[\]>]*>' // HTML elements permitted. Prevents matching ] before >.
		. ')*+'             // Possessive critical.
		. '\]'              // Find end of shortcode.
		. '\]?';            // Shortcodes may end with ]]

	return $regex;
}

/**
 * Replace characters or phrases within HTML elements only.
 *
 * @since 4.2.3
 *
 * @param string $haystack The text which has to be formatted.
 * @param array $replace_pairs In the form array('from' => 'to', ...).
 * @return string The formatted text.
 */
function wp_replace_in_html_tags( $haystack, $replace_pairs ) {
	// Find all elements.
	$textarr = wp_html_split( $haystack );
	$changed = false;

	// Optimize when searching for one item.
	if ( 1 === count( $replace_pairs ) ) {
		// Extract $needle and $replace.
		foreach ( $replace_pairs as $needle => $replace );

		// Loop through delimiters (elements) only.
		for ( $i = 1, $c = count( $textarr ); $i < $c; $i += 2 ) {
			if ( false !== strpos( $textarr[$i], $needle ) ) {
				$textarr[$i] = str_replace( $needle, $replace, $textarr[$i] );
				$changed = true;
			}
		}
	} else {
		// Extract all $needles.
		$needles = array_keys( $replace_pairs );

		// Loop through delimiters (elements) only.
		for ( $i = 1, $c = count( $textarr ); $i < $c; $i += 2 ) {
			foreach ( $needles as $needle ) {
				if ( false !== strpos( $textarr[$i], $needle ) ) {
					$textarr[$i] = strtr( $textarr[$i], $replace_pairs );
					$changed = true;
					// After one strtr() break out of the foreach loop and look at next element.
					break;
				}
			}
		}
	}

	if ( $changed ) {
		$haystack = implode( $textarr );
	}

	return $haystack;
}

/**
 * Newline preservation help function for wpautop
 *
 * @since 3.1.0
 * @access private
 *
 * @param array $matches preg_replace_callback matches array
 * @return string
 */
function _autop_newline_preservation_helper( $matches ) {
	return str_replace( "\n", "<WPPreserveNewline />", $matches[0] );
}

/**
 * Don't auto-p wrap shortcodes that stand alone
 *
 * Ensures that shortcodes are not wrapped in `<p>...</p>`.
 *
 * @since 2.9.0
 *
 * @global array $shortcode_tags
 *
 * @param string $pee The content.
 * @return string The filtered content.
 */
function shortcode_unautop( $pee ) {
	global $shortcode_tags;

	if ( empty( $shortcode_tags ) || !is_array( $shortcode_tags ) ) {
		return $pee;
	}

	$tagregexp = join( '|', array_map( 'preg_quote', array_keys( $shortcode_tags ) ) );
	$spaces = wp_spaces_regexp();

	$pattern =
		  '/'
		. '<p>'                              // Opening paragraph
		. '(?:' . $spaces . ')*+'            // Optional leading whitespace
		. '('                                // 1: The shortcode
		.     '\\['                          // Opening bracket
		.     "($tagregexp)"                 // 2: Shortcode name
		.     '(?![\\w-])'                   // Not followed by word character or hyphen
		                                     // Unroll the loop: Inside the opening shortcode tag
		.     '[^\\]\\/]*'                   // Not a closing bracket or forward slash
		.     '(?:'
		.         '\\/(?!\\])'               // A forward slash not followed by a closing bracket
		.         '[^\\]\\/]*'               // Not a closing bracket or forward slash
		.     ')*?'
		.     '(?:'
		.         '\\/\\]'                   // Self closing tag and closing bracket
		.     '|'
		.         '\\]'                      // Closing bracket
		.         '(?:'                      // Unroll the loop: Optionally, anything between the opening and closing shortcode tags
		.             '[^\\[]*+'             // Not an opening bracket
		.             '(?:'
		.                 '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing shortcode tag
		.                 '[^\\[]*+'         // Not an opening bracket
		.             ')*+'
		.             '\\[\\/\\2\\]'         // Closing shortcode tag
		.         ')?'
		.     ')'
		. ')'
		. '(?:' . $spaces . ')*+'            // optional trailing whitespace
		. '<\\/p>'                           // closing paragraph
		. '/';

	return preg_replace( $pattern, '$1', $pee );
}

/**
 * Checks to see if a string is utf8 encoded.
 *
 * NOTE: This function checks for 5-Byte sequences, UTF8
 *       has Bytes Sequences with a maximum length of 4.
 *
 * @author bmorel at ssi dot fr (modified)
 * @since 1.2.1
 *
 * @param string $str The string to be checked
 * @return bool True if $str fits a UTF-8 model, false otherwise.
 */
function seems_utf8( $str ) {
	mbstring_binary_safe_encoding();
	$length = strlen($str);
	reset_mbstring_encoding();
	for ($i=0; $i < $length; $i++) {
		$c = ord($str[$i]);
		if ($c < 0x80) $n = 0; // 0bbbbbbb
		elseif (($c & 0xE0) == 0xC0) $n=1; // 110bbbbb
		elseif (($c & 0xF0) == 0xE0) $n=2; // 1110bbbb
		elseif (($c & 0xF8) == 0xF0) $n=3; // 11110bbb
		elseif (($c & 0xFC) == 0xF8) $n=4; // 111110bb
		elseif (($c & 0xFE) == 0xFC) $n=5; // 1111110b
		else return false; // Does not match any model
		for ($j=0; $j<$n; $j++) { // n bytes matching 10bbbbbb follow ?
			if ((++$i == $length) || ((ord($str[$i]) & 0xC0) != 0x80))
				return false;
		}
	}
	return true;
}

/**
 * Converts a number of special characters into their HTML entities.
 *
 * Specifically deals with: &, <, >, ", and '.
 *
 * $quote_style can be set to ENT_COMPAT to encode " to
 * &quot;, or ENT_QUOTES to do both. Default is ENT_NOQUOTES where no quotes are encoded.
 *
 * @since 1.2.2
 * @access private
 *
 * @staticvar string $_charset
 *
 * @param string     $string         The text which is to be encoded.
 * @param int|string $quote_style    Optional. Converts double quotes if set to ENT_COMPAT,
 *                                   both single and double if set to ENT_QUOTES or none if set to ENT_NOQUOTES.
 *                                   Also compatible with old values; converting single quotes if set to 'single',
 *                                   double if set to 'double' or both if otherwise set.
 *                                   Default is ENT_NOQUOTES.
 * @param string     $charset        Optional. The character encoding of the string. Default is false.
 * @param bool       $double_encode  Optional. Whether to encode existing html entities. Default is false.
 * @return string The encoded text with HTML entities.
 */
function _wp_specialchars( $string, $quote_style = ENT_NOQUOTES, $charset = false, $double_encode = false ) {
	$string = (string) $string;

	if ( 0 === strlen( $string ) )
		return '';

	// Don't bother if there are no specialchars - saves some processing
	if ( ! preg_match( '/[&<>"\']/', $string ) )
		return $string;

	// Account for the previous behaviour of the function when the $quote_style is not an accepted value
	if ( empty( $quote_style ) )
		$quote_style = ENT_NOQUOTES;
	elseif ( ! in_array( $quote_style, array( 0, 2, 3, 'single', 'double' ), true ) )
		$quote_style = ENT_QUOTES;

	// Store the site charset as a static to avoid multiple calls to wp_load_alloptions()
	if ( ! $charset ) {
		static $_charset = null;
		if ( ! isset( $_charset ) ) {
			$alloptions = wp_load_alloptions();
			$_charset = isset( $alloptions['blog_charset'] ) ? $alloptions['blog_charset'] : '';
		}
		$charset = $_charset;
	}

	if ( in_array( $charset, array( 'utf8', 'utf-8', 'UTF8' ) ) )
		$charset = 'UTF-8';

	$_quote_style = $quote_style;

	if ( $quote_style === 'double' ) {
		$quote_style = ENT_COMPAT;
		$_quote_style = ENT_COMPAT;
	} elseif ( $quote_style === 'single' ) {
		$quote_style = ENT_NOQUOTES;
	}

	if ( ! $double_encode ) {
		// Guarantee every &entity; is valid, convert &garbage; into &amp;garbage;
		// This is required for PHP < 5.4.0 because ENT_HTML401 flag is unavailable.
		$string = wp_kses_normalize_entities( $string );
	}

	$string = @htmlspecialchars( $string, $quote_style, $charset, $double_encode );

	// Backwards compatibility
	if ( 'single' === $_quote_style )
		$string = str_replace( "'", '&#039;', $string );

	return $string;
}

/**
 * Converts a number of HTML entities into their special characters.
 *
 * Specifically deals with: &, <, >, ", and '.
 *
 * $quote_style can be set to ENT_COMPAT to decode " entities,
 * or ENT_QUOTES to do both " and '. Default is ENT_NOQUOTES where no quotes are decoded.
 *
 * @since 2.8.0
 *
 * @param string     $string The text which is to be decoded.
 * @param string|int $quote_style Optional. Converts double quotes if set to ENT_COMPAT,
 *                                both single and double if set to ENT_QUOTES or
 *                                none if set to ENT_NOQUOTES.
 *                                Also compatible with old _wp_specialchars() values;
 *                                converting single quotes if set to 'single',
 *                                double if set to 'double' or both if otherwise set.
 *                                Default is ENT_NOQUOTES.
 * @return string The decoded text without HTML entities.
 */
function wp_specialchars_decode( $string, $quote_style = ENT_NOQUOTES ) {
	$string = (string) $string;

	if ( 0 === strlen( $string ) ) {
		return '';
	}

	// Don't bother if there are no entities - saves a lot of processing
	if ( strpos( $string, '&' ) === false ) {
		return $string;
	}

	// Match the previous behaviour of _wp_specialchars() when the $quote_style is not an accepted value
	if ( empty( $quote_style ) ) {
		$quote_style = ENT_NOQUOTES;
	} elseif ( !in_array( $quote_style, array( 0, 2, 3, 'single', 'double' ), true ) ) {
		$quote_style = ENT_QUOTES;
	}

	// More complete than get_html_translation_table( HTML_SPECIALCHARS )
	$single = array( '&#039;'  => '\'', '&#x27;' => '\'' );
	$single_preg = array( '/&#0*39;/'  => '&#039;', '/&#x0*27;/i' => '&#x27;' );
	$double = array( '&quot;' => '"', '&#034;'  => '"', '&#x22;' => '"' );
	$double_preg = array( '/&#0*34;/'  => '&#034;', '/&#x0*22;/i' => '&#x22;' );
	$others = array( '&lt;'   => '<', '&#060;'  => '<', '&gt;'   => '>', '&#062;'  => '>', '&amp;'  => '&', '&#038;'  => '&', '&#x26;' => '&' );
	$others_preg = array( '/&#0*60;/'  => '&#060;', '/&#0*62;/'  => '&#062;', '/&#0*38;/'  => '&#038;', '/&#x0*26;/i' => '&#x26;' );

	if ( $quote_style === ENT_QUOTES ) {
		$translation = array_merge( $single, $double, $others );
		$translation_preg = array_merge( $single_preg, $double_preg, $others_preg );
	} elseif ( $quote_style === ENT_COMPAT || $quote_style === 'double' ) {
		$translation = array_merge( $double, $others );
		$translation_preg = array_merge( $double_preg, $others_preg );
	} elseif ( $quote_style === 'single' ) {
		$translation = array_merge( $single, $others );
		$translation_preg = array_merge( $single_preg, $others_preg );
	} elseif ( $quote_style === ENT_NOQUOTES ) {
		$translation = $others;
		$translation_preg = $others_preg;
	}

	// Remove zero padding on numeric entities
	$string = preg_replace( array_keys( $translation_preg ), array_values( $translation_preg ), $string );

	// Replace characters according to translation table
	return strtr( $string, $translation );
}

/**
 * Checks for invalid UTF8 in a string.
 *
 * @since 2.8.0
 *
 * @staticvar bool $is_utf8
 * @staticvar bool $utf8_pcre
 *
 * @param string  $string The text which is to be checked.
 * @param bool    $strip Optional. Whether to attempt to strip out invalid UTF8. Default is false.
 * @return string The checked text.
 */
function wp_check_invalid_utf8( $string, $strip = false ) {
	$string = (string) $string;

	if ( 0 === strlen( $string ) ) {
		return '';
	}

	// Store the site charset as a static to avoid multiple calls to get_option()
	static $is_utf8 = null;
	if ( ! isset( $is_utf8 ) ) {
		$is_utf8 = in_array( get_option( 'blog_charset' ), array( 'utf8', 'utf-8', 'UTF8', 'UTF-8' ) );
	}
	if ( ! $is_utf8 ) {
		return $string;
	}

	// Check for support for utf8 in the installed PCRE library once and store the result in a static
	static $utf8_pcre = null;
	if ( ! isset( $utf8_pcre ) ) {
		$utf8_pcre = @preg_match( '/^./u', 'a' );
	}
	// We can't demand utf8 in the PCRE installation, so just return the string in those cases
	if ( !$utf8_pcre ) {
		return $string;
	}

	// preg_match fails when it encounters invalid UTF8 in $string
	if ( 1 === @preg_match( '/^./us', $string ) ) {
		return $string;
	}

	// Attempt to strip the bad chars if requested (not recommended)
	if ( $strip && function_exists( 'iconv' ) ) {
		return iconv( 'utf-8', 'utf-8', $string );
	}

	return '';
}

/**
 * Encode the Unicode values to be used in the URI.
 *
 * @since 1.5.0
 *
 * @param string $utf8_string
 * @param int    $length Max  length of the string
 * @return string String with Unicode encoded for URI.
 */
function utf8_uri_encode( $utf8_string, $length = 0 ) {
	$unicode = '';
	$values = array();
	$num_octets = 1;
	$unicode_length = 0;

	mbstring_binary_safe_encoding();
	$string_length = strlen( $utf8_string );
	reset_mbstring_encoding();

	for ($i = 0; $i < $string_length; $i++ ) {

		$value = ord( $utf8_string[ $i ] );

		if ( $value < 128 ) {
			if ( $length && ( $unicode_length >= $length ) )
				break;
			$unicode .= chr($value);
			$unicode_length++;
		} else {
			if ( count( $values ) == 0 ) {
				if ( $value < 224 ) {
					$num_octets = 2;
				} elseif ( $value < 240 ) {
					$num_octets = 3;
				} else {
					$num_octets = 4;
				}
			}

			$values[] = $value;

			if ( $length && ( $unicode_length + ($num_octets * 3) ) > $length )
				break;
			if ( count( $values ) == $num_octets ) {
				for ( $j = 0; $j < $num_octets; $j++ ) {
					$unicode .= '%' . dechex( $values[ $j ] );
				}

				$unicode_length += $num_octets * 3;

				$values = array();
				$num_octets = 1;
			}
		}
	}

	return $unicode;
}

/**
 * Converts all accent characters to ASCII characters.
 *
 * If there are no accent characters, then the string given is just returned.
 *
 * @since 1.2.1
 *
 * @param string $string Text that might have accent characters
 * @return string Filtered string with replaced "nice" characters.
 */
function remove_accents( $string ) {
	if ( !preg_match('/[\x80-\xff]/', $string) )
		return $string;

	if (seems_utf8($string)) {
		$chars = array(
		// Decompositions for Latin-1 Supplement
		chr(194).chr(170) => 'a', chr(194).chr(186) => 'o',
		chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
		chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
		chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
		chr(195).chr(134) => 'AE',chr(195).chr(135) => 'C',
		chr(195).chr(136) => 'E', chr(195).chr(137) => 'E',
		chr(195).chr(138) => 'E', chr(195).chr(139) => 'E',
		chr(195).chr(140) => 'I', chr(195).chr(141) => 'I',
		chr(195).chr(142) => 'I', chr(195).chr(143) => 'I',
		chr(195).chr(144) => 'D', chr(195).chr(145) => 'N',
		chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
		chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
		chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
		chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
		chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
		chr(195).chr(158) => 'TH',chr(195).chr(159) => 's',
		chr(195).chr(160) => 'a', chr(195).chr(161) => 'a',
		chr(195).chr(162) => 'a', chr(195).chr(163) => 'a',
		chr(195).chr(164) => 'a', chr(195).chr(165) => 'a',
		chr(195).chr(166) => 'ae',chr(195).chr(167) => 'c',
		chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
		chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
		chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
		chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
		chr(195).chr(176) => 'd', chr(195).chr(177) => 'n',
		chr(195).chr(178) => 'o', chr(195).chr(179) => 'o',
		chr(195).chr(180) => 'o', chr(195).chr(181) => 'o',
		chr(195).chr(182) => 'o', chr(195).chr(184) => 'o',
		chr(195).chr(185) => 'u', chr(195).chr(186) => 'u',
		chr(195).chr(187) => 'u', chr(195).chr(188) => 'u',
		chr(195).chr(189) => 'y', chr(195).chr(190) => 'th',
		chr(195).chr(191) => 'y', chr(195).chr(152) => 'O',
		// Decompositions for Latin Extended-A
		chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
		chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
		chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
		chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
		chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
		chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
		chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
		chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
		chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
		chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
		chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
		chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
		chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
		chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
		chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
		chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
		chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
		chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
		chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
		chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
		chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
		chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
		chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
		chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
		chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
		chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij',
		chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
		chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
		chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
		chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
		chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
		chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
		chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
		chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
		chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
		chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
		chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
		chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
		chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
		chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
		chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
		chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
		chr(197).chr(148) => 'R',chr(197).chr(149) => 'r',
		chr(197).chr(150) => 'R',chr(197).chr(151) => 'r',
		chr(197).chr(152) => 'R',chr(197).chr(153) => 'r',
		chr(197).chr(154) => 'S',chr(197).chr(155) => 's',
		chr(197).chr(156) => 'S',chr(197).chr(157) => 's',
		chr(197).chr(158) => 'S',chr(197).chr(159) => 's',
		chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
		chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
		chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
		chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
		chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
		chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
		chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
		chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
		chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
		chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
		chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
		chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
		chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
		chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
		chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
		chr(197).chr(190) => 'z', chr(197).chr(191) => 's',
		// Decompositions for Latin Extended-B
		chr(200).chr(152) => 'S', chr(200).chr(153) => 's',
		chr(200).chr(154) => 'T', chr(200).chr(155) => 't',
		// Euro Sign
		chr(226).chr(130).chr(172) => 'E',
		// GBP (Pound) Sign
		chr(194).chr(163) => '',
		// Vowels with diacritic (Vietnamese)
		// unmarked
		chr(198).chr(160) => 'O', chr(198).chr(161) => 'o',
		chr(198).chr(175) => 'U', chr(198).chr(176) => 'u',
		// grave accent
		chr(225).chr(186).chr(166) => 'A', chr(225).chr(186).chr(167) => 'a',
		chr(225).chr(186).chr(176) => 'A', chr(225).chr(186).chr(177) => 'a',
		chr(225).chr(187).chr(128) => 'E', chr(225).chr(187).chr(129) => 'e',
		chr(225).chr(187).chr(146) => 'O', chr(225).chr(187).chr(147) => 'o',
		chr(225).chr(187).chr(156) => 'O', chr(225).chr(187).chr(157) => 'o',
		chr(225).chr(187).chr(170) => 'U', chr(225).chr(187).chr(171) => 'u',
		chr(225).chr(187).chr(178) => 'Y', chr(225).chr(187).chr(179) => 'y',
		// hook
		chr(225).chr(186).chr(162) => 'A', chr(225).chr(186).chr(163) => 'a',
		chr(225).chr(186).chr(168) => 'A', chr(225).chr(186).chr(169) => 'a',
		chr(225).chr(186).chr(178) => 'A', chr(225).chr(186).chr(179) => 'a',
		chr(225).chr(186).chr(186) => 'E', chr(225).chr(186).chr(187) => 'e',
		chr(225).chr(187).chr(130) => 'E', chr(225).chr(187).chr(131) => 'e',
		chr(225).chr(187).chr(136) => 'I', chr(225).chr(187).chr(137) => 'i',
		chr(225).chr(187).chr(142) => 'O', chr(225).chr(187).chr(143) => 'o',
		chr(225).chr(187).chr(148) => 'O', chr(225).chr(187).chr(149) => 'o',
		chr(225).chr(187).chr(158) => 'O', chr(225).chr(187).chr(159) => 'o',
		chr(225).chr(187).chr(166) => 'U', chr(225).chr(187).chr(167) => 'u',
		chr(225).chr(187).chr(172) => 'U', chr(225).chr(187).chr(173) => 'u',
		chr(225).chr(187).chr(182) => 'Y', chr(225).chr(187).chr(183) => 'y',
		// tilde
		chr(225).chr(186).chr(170) => 'A', chr(225).chr(186).chr(171) => 'a',
		chr(225).chr(186).chr(180) => 'A', chr(225).chr(186).chr(181) => 'a',
		chr(225).chr(186).chr(188) => 'E', chr(225).chr(186).chr(189) => 'e',
		chr(225).chr(187).chr(132) => 'E', chr(225).chr(187).chr(133) => 'e',
		chr(225).chr(187).chr(150) => 'O', chr(225).chr(187).chr(151) => 'o',
		chr(225).chr(187).chr(160) => 'O', chr(225).chr(187).chr(161) => 'o',
		chr(225).chr(187).chr(174) => 'U', chr(225).chr(187).chr(175) => 'u',
		chr(225).chr(187).chr(184) => 'Y', chr(225).chr(187).chr(185) => 'y',
		// acute accent
		chr(225).chr(186).chr(164) => 'A', chr(225).chr(186).chr(165) => 'a',
		chr(225).chr(186).chr(174) => 'A', chr(225).chr(186).chr(175) => 'a',
		chr(225).chr(186).chr(190) => 'E', chr(225).chr(186).chr(191) => 'e',
		chr(225).chr(187).chr(144) => 'O', chr(225).chr(187).chr(145) => 'o',
		chr(225).chr(187).chr(154) => 'O', chr(225).chr(187).chr(155) => 'o',
		chr(225).chr(187).chr(168) => 'U', chr(225).chr(187).chr(169) => 'u',
		// dot below
		chr(225).chr(186).chr(160) => 'A', chr(225).chr(186).chr(161) => 'a',
		chr(225).chr(186).chr(172) => 'A', chr(225).chr(186).chr(173) => 'a',
		chr(225).chr(186).chr(182) => 'A', chr(225).chr(186).chr(183) => 'a',
		chr(225).chr(186).chr(184) => 'E', chr(225).chr(186).chr(185) => 'e',
		chr(225).chr(187).chr(134) => 'E', chr(225).chr(187).chr(135) => 'e',
		chr(225).chr(187).chr(138) => 'I', chr(225).chr(187).chr(139) => 'i',
		chr(225).chr(187).chr(140) => 'O', chr(225).chr(187).chr(141) => 'o',
		chr(225).chr(187).chr(152) => 'O', chr(225).chr(187).chr(153) => 'o',
		chr(225).chr(187).chr(162) => 'O', chr(225).chr(187).chr(163) => 'o',
		chr(225).chr(187).chr(164) => 'U', chr(225).chr(187).chr(165) => 'u',
		chr(225).chr(187).chr(176) => 'U', chr(225).chr(187).chr(177) => 'u',
		chr(225).chr(187).chr(180) => 'Y', chr(225).chr(187).chr(181) => 'y',
		// Vowels with diacritic (Chinese, Hanyu Pinyin)
		chr(201).chr(145) => 'a',
		// macron
		chr(199).chr(149) => 'U', chr(199).chr(150) => 'u',
		// acute accent
		chr(199).chr(151) => 'U', chr(199).chr(152) => 'u',
		// caron
		chr(199).chr(141) => 'A', chr(199).chr(142) => 'a',
		chr(199).chr(143) => 'I', chr(199).chr(144) => 'i',
		chr(199).chr(145) => 'O', chr(199).chr(146) => 'o',
		chr(199).chr(147) => 'U', chr(199).chr(148) => 'u',
		chr(199).chr(153) => 'U', chr(199).chr(154) => 'u',
		// grave accent
		chr(199).chr(155) => 'U', chr(199).chr(156) => 'u',
		);

		// Used for locale-specific rules
		$locale = get_locale();

		if ( 'de_DE' == $locale || 'de_DE_formal' == $locale ) {
			$chars[ chr(195).chr(132) ] = 'Ae';
			$chars[ chr(195).chr(164) ] = 'ae';
			$chars[ chr(195).chr(150) ] = 'Oe';
			$chars[ chr(195).chr(182) ] = 'oe';
			$chars[ chr(195).chr(156) ] = 'Ue';
			$chars[ chr(195).chr(188) ] = 'ue';
			$chars[ chr(195).chr(159) ] = 'ss';
		} elseif ( 'da_DK' === $locale ) {
			$chars[ chr(195).chr(134) ] = 'Ae';
 			$chars[ chr(195).chr(166) ] = 'ae';
			$chars[ chr(195).chr(152) ] = 'Oe';
			$chars[ chr(195).chr(184) ] = 'oe';
			$chars[ chr(195).chr(133) ] = 'Aa';
			$chars[ chr(195).chr(165) ] = 'aa';
		}

		$string = strtr($string, $chars);
	} else {
		$chars = array();
		// Assume ISO-8859-1 if not UTF-8
		$chars['in'] = chr(128).chr(131).chr(138).chr(142).chr(154).chr(158)
			.chr(159).chr(162).chr(165).chr(181).chr(192).chr(193).chr(194)
			.chr(195).chr(196).chr(197).chr(199).chr(200).chr(201).chr(202)
			.chr(203).chr(204).chr(205).chr(206).chr(207).chr(209).chr(210)
			.chr(211).chr(212).chr(213).chr(214).chr(216).chr(217).chr(218)
			.chr(219).chr(220).chr(221).chr(224).chr(225).chr(226).chr(227)
			.chr(228).chr(229).chr(231).chr(232).chr(233).chr(234).chr(235)
			.chr(236).chr(237).chr(238).chr(239).chr(241).chr(242).chr(243)
			.chr(244).chr(245).chr(246).chr(248).chr(249).chr(250).chr(251)
			.chr(252).chr(253).chr(255);

		$chars['out'] = "EfSZszYcYuAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy";

		$string = strtr($string, $chars['in'], $chars['out']);
		$double_chars = array();
		$double_chars['in'] = array(chr(140), chr(156), chr(198), chr(208), chr(222), chr(223), chr(230), chr(240), chr(254));
		$double_chars['out'] = array('OE', 'oe', 'AE', 'DH', 'TH', 'ss', 'ae', 'dh', 'th');
		$string = str_replace($double_chars['in'], $double_chars['out'], $string);
	}

	return $string;
}

/**
 * Sanitizes a filename, replacing whitespace with dashes.
 *
 * Removes special characters that are illegal in filenames on certain
 * operating systems and special characters requiring special escaping
 * to manipulate at the command line. Replaces spaces and consecutive
 * dashes with a single dash. Trims period, dash and underscore from beginning
 * and end of filename.
 *
 * @since 2.1.0
 *
 * @param string $filename The filename to be sanitized
 * @return string The sanitized filename
 */
function sanitize_file_name( $filename ) {
	$filename_raw = $filename;
	$special_chars = array("?", "[", "]", "/", "\\", "=", "<", ">", ":", ";", ",", "'", "\"", "&", "$", "#", "*", "(", ")", "|", "~", "`", "!", "{", "}", "%", "+", chr(0));
	/**
	 * Filter the list of characters to remove from a filename.
	 *
	 * @since 2.8.0
	 *
	 * @param array  $special_chars Characters to remove.
	 * @param string $filename_raw  Filename as it was passed into sanitize_file_name().
	 */
	$special_chars = apply_filters( 'sanitize_file_name_chars', $special_chars, $filename_raw );
	$filename = preg_replace( "#\x{00a0}#siu", ' ', $filename );
	$filename = str_replace( $special_chars, '', $filename );
	$filename = str_replace( array( '%20', '+' ), '-', $filename );
	$filename = preg_replace( '/[\r\n\t -]+/', '-', $filename );
	$filename = trim( $filename, '.-_' );

	// Split the filename into a base and extension[s]
	$parts = explode('.', $filename);

	// Return if only one extension
	if ( count( $parts ) <= 2 ) {
		/**
		 * Filter a sanitized filename string.
		 *
		 * @since 2.8.0
		 *
		 * @param string $filename     Sanitized filename.
		 * @param string $filename_raw The filename prior to sanitization.
		 */
		return apply_filters( 'sanitize_file_name', $filename, $filename_raw );
	}

	// Process multiple extensions
	$filename = array_shift($parts);
	$extension = array_pop($parts);
	$mimes = get_allowed_mime_types();

	/*
	 * Loop over any intermediate extensions. Postfix them with a trailing underscore
	 * if they are a 2 - 5 character long alpha string not in the extension whitelist.
	 */
	foreach ( (array) $parts as $part) {
		$filename .= '.' . $part;

		if ( preg_match("/^[a-zA-Z]{2,5}\d?$/", $part) ) {
			$allowed = false;
			foreach ( $mimes as $ext_preg => $mime_match ) {
				$ext_preg = '!^(' . $ext_preg . ')$!i';
				if ( preg_match( $ext_preg, $part ) ) {
					$allowed = true;
					break;
				}
			}
			if ( !$allowed )
				$filename .= '_';
		}
	}
	$filename .= '.' . $extension;
	/** This filter is documented in wp-includes/formatting.php */
	return apply_filters('sanitize_file_name', $filename, $filename_raw);
}

/**
 * Sanitizes a username, stripping out unsafe characters.
 *
 * Removes tags, octets, entities, and if strict is enabled, will only keep
 * alphanumeric, _, space, ., -, @. After sanitizing, it passes the username,
 * raw username (the username in the parameter), and the value of $strict as
 * parameters for the 'sanitize_user' filter.
 *
 * @since 2.0.0
 *
 * @param string $username The username to be sanitized.
 * @param bool   $strict   If set limits $username to specific characters. Default false.
 * @return string The sanitized username, after passing through filters.
 */
function sanitize_user( $username, $strict = false ) {
	$raw_username = $username;
	$username = wp_strip_all_tags( $username );
	$username = remove_accents( $username );
	// Kill octets
	$username = preg_replace( '|%([a-fA-F0-9][a-fA-F0-9])|', '', $username );
	$username = preg_replace( '/&.+?;/', '', $username ); // Kill entities

	// If strict, reduce to ASCII for max portability.
	if ( $strict )
		$username = preg_replace( '|[^a-z0-9 _.\-@]|i', '', $username );

	$username = trim( $username );
	// Consolidate contiguous whitespace
	$username = preg_replace( '|\s+|', ' ', $username );

	/**
	 * Filter a sanitized username string.
	 *
	 * @since 2.0.1
	 *
	 * @param string $username     Sanitized username.
	 * @param string $raw_username The username prior to sanitization.
	 * @param bool   $strict       Whether to limit the sanitization to specific characters. Default false.
	 */
	return apply_filters( 'sanitize_user', $username, $raw_username, $strict );
}

/**
 * Sanitizes a string key.
 *
 * Keys are used as internal identifiers. Lowercase alphanumeric characters, dashes and underscores are allowed.
 *
 * @since 3.0.0
 *
 * @param string $key String key
 * @return string Sanitized key
 */
function sanitize_key( $key ) {
	$raw_key = $key;
	$key = strtolower( $key );
	$key = preg_replace( '/[^a-z0-9_\-]/', '', $key );

	/**
	 * Filter a sanitized key string.
	 *
	 * @since 3.0.0
	 *
	 * @param string $key     Sanitized key.
	 * @param string $raw_key The key prior to sanitization.
	 */
	return apply_filters( 'sanitize_key', $key, $raw_key );
}

/**
 * Sanitizes a title, or returns a fallback title.
 *
 * Specifically, HTML and PHP tags are stripped. Further actions can be added
 * via the plugin API. If $title is empty and $fallback_title is set, the latter
 * will be used.
 *
 * @since 1.0.0
 *
 * @param string $title          The string to be sanitized.
 * @param string $fallback_title Optional. A title to use if $title is empty.
 * @param string $context        Optional. The operation for which the string is sanitized
 * @return string The sanitized string.
 */
function sanitize_title( $title, $fallback_title = '', $context = 'save' ) {
	$raw_title = $title;

	if ( 'save' == $context )
		$title = remove_accents($title);

	/**
	 * Filter a sanitized title string.
	 *
	 * @since 1.2.0
	 *
	 * @param string $title     Sanitized title.
	 * @param string $raw_title The title prior to sanitization.
	 * @param string $context   The context for which the title is being sanitized.
	 */
	$title = apply_filters( 'sanitize_title', $title, $raw_title, $context );

	if ( '' === $title || false === $title )
		$title = $fallback_title;

	return $title;
}

/**
 * Sanitizes a title with the 'query' context.
 *
 * Used for querying the database for a value from URL.
 *
 * @since 3.1.0
 *
 * @param string $title The string to be sanitized.
 * @return string The sanitized string.
 */
function sanitize_title_for_query( $title ) {
	return sanitize_title( $title, '', 'query' );
}

/**
 * Sanitizes a title, replacing whitespace and a few other characters with dashes.
 *
 * Limits the output to alphanumeric characters, underscore (_) and dash (-).
 * Whitespace becomes a dash.
 *
 * @since 1.2.0
 *
 * @param string $title     The title to be sanitized.
 * @param string $raw_title Optional. Not used.
 * @param string $context   Optional. The operation for which the string is sanitized.
 * @return string The sanitized title.
 */
function sanitize_title_with_dashes( $title, $raw_title = '', $context = 'display' ) {
	$title = strip_tags($title);
	// Preserve escaped octets.
	$title = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '---$1---', $title);
	// Remove percent signs that are not part of an octet.
	$title = str_replace('%', '', $title);
	// Restore octets.
	$title = preg_replace('|---([a-fA-F0-9][a-fA-F0-9])---|', '%$1', $title);

	if (seems_utf8($title)) {
		if (function_exists('mb_strtolower')) {
			$title = mb_strtolower($title, 'UTF-8');
		}
		$title = utf8_uri_encode($title, 200);
	}

	$title = strtolower($title);
	$title = preg_replace('/&.+?;/', '', $title); // kill entities
	$title = str_replace('.', '-', $title);

	if ( 'save' == $context ) {
		// Convert nbsp, ndash and mdash to hyphens
		$title = str_replace( array( '%c2%a0', '%e2%80%93', '%e2%80%94' ), '-', $title );

		// Strip these characters entirely
		$title = str_replace( array(
			// iexcl and iquest
			'%c2%a1', '%c2%bf',
			// angle quotes
			'%c2%ab', '%c2%bb', '%e2%80%b9', '%e2%80%ba',
			// curly quotes
			'%e2%80%98', '%e2%80%99', '%e2%80%9c', '%e2%80%9d',
			'%e2%80%9a', '%e2%80%9b', '%e2%80%9e', '%e2%80%9f',
			// copy, reg, deg, hellip and trade
			'%c2%a9', '%c2%ae', '%c2%b0', '%e2%80%a6', '%e2%84%a2',
			// acute accents
			'%c2%b4', '%cb%8a', '%cc%81', '%cd%81',
			// grave accent, macron, caron
			'%cc%80', '%cc%84', '%cc%8c',
		), '', $title );

		// Convert times to x
		$title = str_replace( '%c3%97', 'x', $title );
	}

	$title = preg_replace('/[^%a-z0-9 _-]/', '', $title);
	$title = preg_replace('/\s+/', '-', $title);
	$title = preg_replace('|-+|', '-', $title);
	$title = trim($title, '-');

	return $title;
}

/**
 * Ensures a string is a valid SQL 'order by' clause.
 *
 * Accepts one or more columns, with or without a sort order (ASC / DESC).
 * e.g. 'column_1', 'column_1, column_2', 'column_1 ASC, column_2 DESC' etc.
 *
 * Also accepts 'RAND()'.
 *
 * @since 2.5.1
 *
 * @param string $orderby Order by clause to be validated.
 * @return string|false Returns $orderby if valid, false otherwise.
 */
function sanitize_sql_orderby( $orderby ) {
	if ( preg_match( '/^\s*(([a-z0-9_]+|`[a-z0-9_]+`)(\s+(ASC|DESC))?\s*(,\s*(?=[a-z0-9_`])|$))+$/i', $orderby ) || preg_match( '/^\s*RAND\(\s*\)\s*$/i', $orderby ) ) {
		return $orderby;
	}
	return false;
}

/**
 * Sanitizes an HTML classname to ensure it only contains valid characters.
 *
 * Strips the string down to A-Z,a-z,0-9,_,-. If this results in an empty
 * string then it will return the alternative value supplied.
 *
 * @todo Expand to support the full range of CDATA that a class attribute can contain.
 *
 * @since 2.8.0
 *
 * @param string $class    The classname to be sanitized
 * @param string $fallback Optional. The value to return if the sanitization ends up as an empty string.
 * 	Defaults to an empty string.
 * @return string The sanitized value
 */
function sanitize_html_class( $class, $fallback = '' ) {
	//Strip out any % encoded octets
	$sanitized = preg_replace( '|%[a-fA-F0-9][a-fA-F0-9]|', '', $class );

	//Limit to A-Z,a-z,0-9,_,-
	$sanitized = preg_replace( '/[^A-Za-z0-9_-]/', '', $sanitized );

	if ( '' == $sanitized && $fallback ) {
		return sanitize_html_class( $fallback );
	}
	/**
	 * Filter a sanitized HTML class string.
	 *
	 * @since 2.8.0
	 *
	 * @param string $sanitized The sanitized HTML class.
	 * @param string $class     HTML class before sanitization.
	 * @param string $fallback  The fallback string.
	 */
	return apply_filters( 'sanitize_html_class', $sanitized, $class, $fallback );
}

/**
 * Converts lone & characters into `&#038;` (a.k.a. `&amp;`)
 *
 * @since 0.71
 *
 * @param string $content    String of characters to be converted.
 * @param string $deprecated Not used.
 * @return string Converted string.
 */
function convert_chars( $content, $deprecated = '' ) {
	if ( ! empty( $deprecated ) ) {
		_deprecated_argument( __FUNCTION__, '0.71' );
	}

	if ( strpos( $content, '&' ) !== false ) {
		$content = preg_replace( '/&([^#])(?![a-z1-4]{1,8};)/i', '&#038;$1', $content );
	}

	return $content;
}

/**
 * Converts invalid Unicode references range to valid range.
 *
 * @since 4.3.0
 *
 * @param string $content String with entities that need converting.
 * @return string Converted string.
 */
function convert_invalid_entities( $content ) {
	$wp_htmltranswinuni = array(
		'&#128;' => '&#8364;', // the Euro sign
		'&#129;' => '',
		'&#130;' => '&#8218;', // these are Windows CP1252 specific characters
		'&#131;' => '&#402;',  // they would look weird on non-Windows browsers
		'&#132;' => '&#8222;',
		'&#133;' => '&#8230;',
		'&#134;' => '&#8224;',
		'&#135;' => '&#8225;',
		'&#136;' => '&#710;',
		'&#137;' => '&#8240;',
		'&#138;' => '&#352;',
		'&#139;' => '&#8249;',
		'&#140;' => '&#338;',
		'&#141;' => '',
		'&#142;' => '&#381;',
		'&#143;' => '',
		'&#144;' => '',
		'&#145;' => '&#8216;',
		'&#146;' => '&#8217;',
		'&#147;' => '&#8220;',
		'&#148;' => '&#8221;',
		'&#149;' => '&#8226;',
		'&#150;' => '&#8211;',
		'&#151;' => '&#8212;',
		'&#152;' => '&#732;',
		'&#153;' => '&#8482;',
		'&#154;' => '&#353;',
		'&#155;' => '&#8250;',
		'&#156;' => '&#339;',
		'&#157;' => '',
		'&#158;' => '&#382;',
		'&#159;' => '&#376;'
	);

	if ( strpos( $content, '&#1' ) !== false ) {
		$content = strtr( $content, $wp_htmltranswinuni );
	}

	return $content;
}

/**
 * Balances tags if forced to, or if the 'use_balanceTags' option is set to true.
 *
 * @since 0.71
 *
 * @param string $text  Text to be balanced
 * @param bool   $force If true, forces balancing, ignoring the value of the option. Default false.
 * @return string Balanced text
 */
function balanceTags( $text, $force = false ) {
	if ( $force || get_option('use_balanceTags') == 1 ) {
		return force_balance_tags( $text );
	} else {
		return $text;
	}
}

/**
 * Balances tags of string using a modified stack.
 *
 * @since 2.0.4
 *
 * @author Leonard Lin <leonard@acm.org>
 * @license GPL
 * @copyright November 4, 2001
 * @version 1.1
 * @todo Make better - change loop condition to $text in 1.2
 * @internal Modified by Scott Reilly (coffee2code) 02 Aug 2004
 *		1.1  Fixed handling of append/stack pop order of end text
 *			 Added Cleaning Hooks
 *		1.0  First Version
 *
 * @param string $text Text to be balanced.
 * @return string Balanced text.
 */
function force_balance_tags( $text ) {
	$tagstack = array();
	$stacksize = 0;
	$tagqueue = '';
	$newtext = '';
	// Known single-entity/self-closing tags
	$single_tags = array( 'area', 'base', 'basefont', 'br', 'col', 'command', 'embed', 'frame', 'hr', 'img', 'input', 'isindex', 'link', 'meta', 'param', 'source' );
	// Tags that can be immediately nested within themselves
	$nestable_tags = array( 'blockquote', 'div', 'object', 'q', 'span' );

	// WP bug fix for comments - in case you REALLY meant to type '< !--'
	$text = str_replace('< !--', '<    !--', $text);
	// WP bug fix for LOVE <3 (and other situations with '<' before a number)
	$text = preg_replace('#<([0-9]{1})#', '&lt;$1', $text);

	while ( preg_match("/<(\/?[\w:]*)\s*([^>]*)>/", $text, $regex) ) {
		$newtext .= $tagqueue;

		$i = strpos($text, $regex[0]);
		$l = strlen($regex[0]);

		// clear the shifter
		$tagqueue = '';
		// Pop or Push
		if ( isset($regex[1][0]) && '/' == $regex[1][0] ) { // End Tag
			$tag = strtolower(substr($regex[1],1));
			// if too many closing tags
			if ( $stacksize <= 0 ) {
				$tag = '';
				// or close to be safe $tag = '/' . $tag;
			}
			// if stacktop value = tag close value then pop
			elseif ( $tagstack[$stacksize - 1] == $tag ) { // found closing tag
				$tag = '</' . $tag . '>'; // Close Tag
				// Pop
				array_pop( $tagstack );
				$stacksize--;
			} else { // closing tag not at top, search for it
				for ( $j = $stacksize-1; $j >= 0; $j-- ) {
					if ( $tagstack[$j] == $tag ) {
					// add tag to tagqueue
						for ( $k = $stacksize-1; $k >= $j; $k--) {
							$tagqueue .= '</' . array_pop( $tagstack ) . '>';
							$stacksize--;
						}
						break;
					}
				}
				$tag = '';
			}
		} else { // Begin Tag
			$tag = strtolower($regex[1]);

			// Tag Cleaning

			// If it's an empty tag "< >", do nothing
			if ( '' == $tag ) {
				// do nothing
			}
			// ElseIf it presents itself as a self-closing tag...
			elseif ( substr( $regex[2], -1 ) == '/' ) {
				// ...but it isn't a known single-entity self-closing tag, then don't let it be treated as such and
				// immediately close it with a closing tag (the tag will encapsulate no text as a result)
				if ( ! in_array( $tag, $single_tags ) )
					$regex[2] = trim( substr( $regex[2], 0, -1 ) ) . "></$tag";
			}
			// ElseIf it's a known single-entity tag but it doesn't close itself, do so
			elseif ( in_array($tag, $single_tags) ) {
				$regex[2] .= '/';
			}
			// Else it's not a single-entity tag
			else {
				// If the top of the stack is the same as the tag we want to push, close previous tag
				if ( $stacksize > 0 && !in_array($tag, $nestable_tags) && $tagstack[$stacksize - 1] == $tag ) {
					$tagqueue = '</' . array_pop( $tagstack ) . '>';
					$stacksize--;
				}
				$stacksize = array_push( $tagstack, $tag );
			}

			// Attributes
			$attributes = $regex[2];
			if ( ! empty( $attributes ) && $attributes[0] != '>' )
				$attributes = ' ' . $attributes;

			$tag = '<' . $tag . $attributes . '>';
			//If already queuing a close tag, then put this tag on, too
			if ( !empty($tagqueue) ) {
				$tagqueue .= $tag;
				$tag = '';
			}
		}
		$newtext .= substr($text, 0, $i) . $tag;
		$text = substr($text, $i + $l);
	}

	// Clear Tag Queue
	$newtext .= $tagqueue;

	// Add Remaining text
	$newtext .= $text;

	// Empty Stack
	while( $x = array_pop($tagstack) )
		$newtext .= '</' . $x . '>'; // Add remaining tags to close

	// WP fix for the bug with HTML comments
	$newtext = str_replace("< !--","<!--",$newtext);
	$newtext = str_replace("<    !--","< !--",$newtext);

	return $newtext;
}

/**
 * Acts on text which is about to be edited.
 *
 * The $content is run through esc_textarea(), which uses htmlspecialchars()
 * to convert special characters to HTML entities. If $richedit is set to true,
 * it is simply a holder for the 'format_to_edit' filter.
 *
 * @since 0.71
 * @since 4.4.0 The `$richedit` parameter was renamed to `$rich_text` for clarity.
 *
 * @param string $content   The text about to be edited.
 * @param bool   $rich_text Optional. Whether `$content` should be considered rich text,
 *                          in which case it would not be passed through esc_textarea().
 *                          Default false.
 * @return string The text after the filter (and possibly htmlspecialchars()) has been run.
 */
function format_to_edit( $content, $rich_text = false ) {
	/**
	 * Filter the text to be formatted for editing.
	 *
	 * @since 1.2.0
	 *
	 * @param string $content The text, prior to formatting for editing.
	 */
	$content = apply_filters( 'format_to_edit', $content );
	if ( ! $rich_text )
		$content = esc_textarea( $content );
	return $content;
}

/**
 * Add leading zeros when necessary.
 *
 * If you set the threshold to '4' and the number is '10', then you will get
 * back '0010'. If you set the threshold to '4' and the number is '5000', then you
 * will get back '5000'.
 *
 * Uses sprintf to append the amount of zeros based on the $threshold parameter
 * and the size of the number. If the number is large enough, then no zeros will
 * be appended.
 *
 * @since 0.71
 *
 * @param int $number     Number to append zeros to if not greater than threshold.
 * @param int $threshold  Digit places number needs to be to not have zeros added.
 * @return string Adds leading zeros to number if needed.
 */
function zeroise( $number, $threshold ) {
	return sprintf( '%0' . $threshold . 's', $number );
}

/**
 * Adds backslashes before letters and before a number at the start of a string.
 *
 * @since 0.71
 *
 * @param string $string Value to which backslashes will be added.
 * @return string String with backslashes inserted.
 */
function backslashit( $string ) {
	if ( isset( $string[0] ) && $string[0] >= '0' && $string[0] <= '9' )
		$string = '\\\\' . $string;
	return addcslashes( $string, 'A..Za..z' );
}

/**
 * Appends a trailing slash.
 *
 * Will remove trailing forward and backslashes if it exists already before adding
 * a trailing forward slash. This prevents double slashing a string or path.
 *
 * The primary use of this is for paths and thus should be used for paths. It is
 * not restricted to paths and offers no specific path support.
 *
 * @since 1.2.0
 *
 * @param string $string What to add the trailing slash to.
 * @return string String with trailing slash added.
 */
function trailingslashit( $string ) {
	return untrailingslashit( $string ) . '/';
}

/**
 * Removes trailing forward slashes and backslashes if they exist.
 *
 * The primary use of this is for paths and thus should be used for paths. It is
 * not restricted to paths and offers no specific path support.
 *
 * @since 2.2.0
 *
 * @param string $string What to remove the trailing slashes from.
 * @return string String without the trailing slashes.
 */
function untrailingslashit( $string ) {
	return rtrim( $string, '/\\' );
}

/**
 * Adds slashes to escape strings.
 *
 * Slashes will first be removed if magic_quotes_gpc is set, see {@link
 * http://www.php.net/magic_quotes} for more details.
 *
 * @since 0.71
 *
 * @param string $gpc The string returned from HTTP request data.
 * @return string Returns a string escaped with slashes.
 */
function addslashes_gpc($gpc) {
	if ( get_magic_quotes_gpc() )
		$gpc = stripslashes($gpc);

	return wp_slash($gpc);
}

/**
 * Navigates through an array, object, or scalar, and removes slashes from the values.
 *
 * @since 2.0.0
 *
 * @param mixed $value The value to be stripped.
 * @return mixed Stripped value.
 */
function stripslashes_deep( $value ) {
	return map_deep( $value, 'stripslashes_from_strings_only' );
}

/**
 * Callback function for `stripslashes_deep()` which strips slashes from strings.
 *
 * @since 4.4.0
 *
 * @param mixed $value The array or string to be stripped.
 * @return mixed $value The stripped value.
 */
function stripslashes_from_strings_only( $value ) {
	return is_string( $value ) ? stripslashes( $value ) : $value;
}

/**
 * Navigates through an array, object, or scalar, and encodes the values to be used in a URL.
 *
 * @since 2.2.0
 *
 * @param mixed $value The array or string to be encoded.
 * @return mixed $value The encoded value.
 */
function urlencode_deep( $value ) {
	return map_deep( $value, 'urlencode' );
}

/**
 * Navigates through an array, object, or scalar, and raw-encodes the values to be used in a URL.
 *
 * @since 3.4.0
 *
 * @param mixed $value The array or string to be encoded.
 * @return mixed $value The encoded value.
 */
function rawurlencode_deep( $value ) {
	return map_deep( $value, 'rawurlencode' );
}

/**
 * Navigates through an array, object, or scalar, and decodes URL-encoded values
 *
 * @since 4.4.0
 *
 * @param mixed $value The array or string to be decoded.
 * @return mixed $value The decoded value.
 */
function urldecode_deep( $value ) {
	return map_deep( $value, 'urldecode' );
}

/**
 * Converts email addresses characters to HTML entities to block spam bots.
 *
 * @since 0.71
 *
 * @param string $email_address Email address.
 * @param int    $hex_encoding  Optional. Set to 1 to enable hex encoding.
 * @return string Converted email address.
 */
function antispambot( $email_address, $hex_encoding = 0 ) {
	$email_no_spam_address = '';
	for ( $i = 0, $len = strlen( $email_address ); $i < $len; $i++ ) {
		$j = rand( 0, 1 + $hex_encoding );
		if ( $j == 0 ) {
			$email_no_spam_address .= '&#' . ord( $email_address[$i] ) . ';';
		} elseif ( $j == 1 ) {
			$email_no_spam_address .= $email_address[$i];
		} elseif ( $j == 2 ) {
			$email_no_spam_address .= '%' . zeroise( dechex( ord( $email_address[$i] ) ), 2 );
		}
	}

	return str_replace( '@', '&#64;', $email_no_spam_address );
}

/**
 * Callback to convert URI match to HTML A element.
 *
 * This function was backported from 2.5.0 to 2.3.2. Regex callback for {@link
 * make_clickable()}.
 *
 * @since 2.3.2
 * @access private
 *
 * @param array $matches Single Regex Match.
 * @return string HTML A element with URI address.
 */
function _make_url_clickable_cb( $matches ) {
	$url = $matches[2];

	if ( ')' == $matches[3] && strpos( $url, '(' ) ) {
		// If the trailing character is a closing parethesis, and the URL has an opening parenthesis in it, add the closing parenthesis to the URL.
		// Then we can let the parenthesis balancer do its thing below.
		$url .= $matches[3];
		$suffix = '';
	} else {
		$suffix = $matches[3];
	}

	// Include parentheses in the URL only if paired
	while ( substr_count( $url, '(' ) < substr_count( $url, ')' ) ) {
		$suffix = strrchr( $url, ')' ) . $suffix;
		$url = substr( $url, 0, strrpos( $url, ')' ) );
	}

	$url = esc_url($url);
	if ( empty($url) )
		return $matches[0];

	return $matches[1] . "<a href=\"$url\" rel=\"nofollow\">$url</a>" . $suffix;
}

/**
 * Callback to convert URL match to HTML A element.
 *
 * This function was backported from 2.5.0 to 2.3.2. Regex callback for {@link
 * make_clickable()}.
 *
 * @since 2.3.2
 * @access private
 *
 * @param array $matches Single Regex Match.
 * @return string HTML A element with URL address.
 */
function _make_web_ftp_clickable_cb( $matches ) {
	$ret = '';
	$dest = $matches[2];
	$dest = 'http://' . $dest;

	// removed trailing [.,;:)] from URL
	if ( in_array( substr($dest, -1), array('.', ',', ';', ':', ')') ) === true ) {
		$ret = substr($dest, -1);
		$dest = substr($dest, 0, strlen($dest)-1);
	}

	$dest = esc_url($dest);
	if ( empty($dest) )
		return $matches[0];

	return $matches[1] . "<a href=\"$dest\" rel=\"nofollow\">$dest</a>$ret";
}

/**
 * Callback to convert email address match to HTML A element.
 *
 * This function was backported from 2.5.0 to 2.3.2. Regex callback for {@link
 * make_clickable()}.
 *
 * @since 2.3.2
 * @access private
 *
 * @param array $matches Single Regex Match.
 * @return string HTML A element with email address.
 */
function _make_email_clickable_cb( $matches ) {
	$email = $matches[2] . '@' . $matches[3];
	return $matches[1] . "<a href=\"mailto:$email\">$email</a>";
}

/**
 * Convert plaintext URI to HTML links.
 *
 * Converts URI, www and ftp, and email addresses. Finishes by fixing links
 * within links.
 *
 * @since 0.71
 *
 * @param string $text Content to convert URIs.
 * @return string Content with converted URIs.
 */
function make_clickable( $text ) {
	$r = '';
	$textarr = preg_split( '/(<[^<>]+>)/', $text, -1, PREG_SPLIT_DELIM_CAPTURE ); // split out HTML tags
	$nested_code_pre = 0; // Keep track of how many levels link is nested inside <pre> or <code>
	foreach ( $textarr as $piece ) {

		if ( preg_match( '|^<code[\s>]|i', $piece ) || preg_match( '|^<pre[\s>]|i', $piece ) || preg_match( '|^<script[\s>]|i', $piece ) || preg_match( '|^<style[\s>]|i', $piece ) )
			$nested_code_pre++;
		elseif ( $nested_code_pre && ( '</code>' === strtolower( $piece ) || '</pre>' === strtolower( $piece ) || '</script>' === strtolower( $piece ) || '</style>' === strtolower( $piece ) ) )
			$nested_code_pre--;

		if ( $nested_code_pre || empty( $piece ) || ( $piece[0] === '<' && ! preg_match( '|^<\s*[\w]{1,20}+://|', $piece ) ) ) {
			$r .= $piece;
			continue;
		}

		// Long strings might contain expensive edge cases ...
		if ( 10000 < strlen( $piece ) ) {
			// ... break it up
			foreach ( _split_str_by_whitespace( $piece, 2100 ) as $chunk ) { // 2100: Extra room for scheme and leading and trailing paretheses
				if ( 2101 < strlen( $chunk ) ) {
					$r .= $chunk; // Too big, no whitespace: bail.
				} else {
					$r .= make_clickable( $chunk );
				}
			}
		} else {
			$ret = " $piece "; // Pad with whitespace to simplify the regexes

			$url_clickable = '~
				([\\s(<.,;:!?])                                        # 1: Leading whitespace, or punctuation
				(                                                      # 2: URL
					[\\w]{1,20}+://                                # Scheme and hier-part prefix
					(?=\S{1,2000}\s)                               # Limit to URLs less than about 2000 characters long
					[\\w\\x80-\\xff#%\\~/@\\[\\]*(+=&$-]*+         # Non-punctuation URL character
					(?:                                            # Unroll the Loop: Only allow puctuation URL character if followed by a non-punctuation URL character
						[\'.,;:!?)]                            # Punctuation URL character
						[\\w\\x80-\\xff#%\\~/@\\[\\]*(+=&$-]++ # Non-punctuation URL character
					)*
				)
				(\)?)                                                  # 3: Trailing closing parenthesis (for parethesis balancing post processing)
			~xS'; // The regex is a non-anchored pattern and does not have a single fixed starting character.
			      // Tell PCRE to spend more time optimizing since, when used on a page load, it will probably be used several times.

			$ret = preg_replace_callback( $url_clickable, '_make_url_clickable_cb', $ret );

			$ret = preg_replace_callback( '#([\s>])((www|ftp)\.[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]+)#is', '_make_web_ftp_clickable_cb', $ret );
			$ret = preg_replace_callback( '#([\s>])([.0-9a-z_+-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,})#i', '_make_email_clickable_cb', $ret );

			$ret = substr( $ret, 1, -1 ); // Remove our whitespace padding.
			$r .= $ret;
		}
	}

	// Cleanup of accidental links within links
	return preg_replace( '#(<a([ \r\n\t]+[^>]+?>|>))<a [^>]+?>([^>]+?)</a></a>#i', "$1$3</a>", $r );
}

/**
 * Breaks a string into chunks by splitting at whitespace characters.
 * The length of each returned chunk is as close to the specified length goal as possible,
 * with the caveat that each chunk includes its trailing delimiter.
 * Chunks longer than the goal are guaranteed to not have any inner whitespace.
 *
 * Joining the returned chunks with empty delimiters reconstructs the input string losslessly.
 *
 * Input string must have no null characters (or eventual transformations on output chunks must not care about null characters)
 *
 *     _split_str_by_whitespace( "1234 67890 1234 67890a cd 1234   890 123456789 1234567890a    45678   1 3 5 7 90 ", 10 ) ==
 *     array (
 *         0 => '1234 67890 ',  // 11 characters: Perfect split
 *         1 => '1234 ',        //  5 characters: '1234 67890a' was too long
 *         2 => '67890a cd ',   // 10 characters: '67890a cd 1234' was too long
 *         3 => '1234   890 ',  // 11 characters: Perfect split
 *         4 => '123456789 ',   // 10 characters: '123456789 1234567890a' was too long
 *         5 => '1234567890a ', // 12 characters: Too long, but no inner whitespace on which to split
 *         6 => '   45678   ',  // 11 characters: Perfect split
 *         7 => '1 3 5 7 90 ',  // 11 characters: End of $string
 *     );
 *
 * @since 3.4.0
 * @access private
 *
 * @param string $string The string to split.
 * @param int    $goal   The desired chunk length.
 * @return array Numeric array of chunks.
 */
function _split_str_by_whitespace( $string, $goal ) {
	$chunks = array();

	$string_nullspace = strtr( $string, "\r\n\t\v\f ", "\000\000\000\000\000\000" );

	while ( $goal < strlen( $string_nullspace ) ) {
		$pos = strrpos( substr( $string_nullspace, 0, $goal + 1 ), "\000" );

		if ( false === $pos ) {
			$pos = strpos( $string_nullspace, "\000", $goal + 1 );
			if ( false === $pos ) {
				break;
			}
		}

		$chunks[] = substr( $string, 0, $pos + 1 );
		$string = substr( $string, $pos + 1 );
		$string_nullspace = substr( $string_nullspace, $pos + 1 );
	}

	if ( $string ) {
		$chunks[] = $string;
	}

	return $chunks;
}

/**
 * Adds rel nofollow string to all HTML A elements in content.
 *
 * @since 1.5.0
 *
 * @param string $text Content that may contain HTML A elements.
 * @return string Converted content.
 */
function wp_rel_nofollow( $text ) {
	// This is a pre save filter, so text is already escaped.
	$text = stripslashes($text);
	$text = preg_replace_callback('|<a (.+?)>|i', 'wp_rel_nofollow_callback', $text);
	return wp_slash( $text );
}

/**
 * Callback to add rel=nofollow string to HTML A element.
 *
 * Will remove already existing rel="nofollow" and rel='nofollow' from the
 * string to prevent from invalidating (X)HTML.
 *
 * @since 2.3.0
 *
 * @param array $matches Single Match
 * @return string HTML A Element with rel nofollow.
 */
function wp_rel_nofollow_callback( $matches ) {
	$text = $matches[1];
	$atts = shortcode_parse_atts( $matches[1] );
	$rel  = 'nofollow';

	if ( preg_match( '%href=["\'](' . preg_quote( set_url_scheme( home_url(), 'http' ) ) . ')%i', $text ) ||
	     preg_match( '%href=["\'](' . preg_quote( set_url_scheme( home_url(), 'https' ) ) . ')%i', $text )
	) {
		return "<a $text>";
	}

	if ( ! empty( $atts['rel'] ) ) {
		$parts = array_map( 'trim', explode( ' ', $atts['rel'] ) );
		if ( false === array_search( 'nofollow', $parts ) ) {
			$parts[] = 'nofollow';
		}
		$rel = implode( ' ', $parts );
		unset( $atts['rel'] );

		$html = '';
		foreach ( $atts as $name => $value ) {
			$html .= "{$name}=\"$value\" ";
		}
		$text = trim( $html );
	}
	return "<a $text rel=\"$rel\">";
}

/**
 * Convert one smiley code to the icon graphic file equivalent.
 *
 * Callback handler for {@link convert_smilies()}.
 * Looks up one smiley code in the $wpsmiliestrans global array and returns an
 * `<img>` string for that smiley.
 *
 * @since 2.8.0
 *
 * @global array $wpsmiliestrans
 *
 * @param array $matches Single match. Smiley code to convert to image.
 * @return string Image string for smiley.
 */
function translate_smiley( $matches ) {
	global $wpsmiliestrans;

	if ( count( $matches ) == 0 )
		return '';

	$smiley = trim( reset( $matches ) );
	$img = $wpsmiliestrans[ $smiley ];

	$matches = array();
	$ext = preg_match( '/\.([^.]+)$/', $img, $matches ) ? strtolower( $matches[1] ) : false;
	$image_exts = array( 'jpg', 'jpeg', 'jpe', 'gif', 'png' );

	// Don't convert smilies that aren't images - they're probably emoji.
	if ( ! in_array( $ext, $image_exts ) ) {
		return $img;
	}

	/**
	 * Filter the Smiley image URL before it's used in the image element.
	 *
	 * @since 2.9.0
	 *
	 * @param string $smiley_url URL for the smiley image.
	 * @param string $img        Filename for the smiley image.
	 * @param string $site_url   Site URL, as returned by site_url().
	 */
	$src_url = apply_filters( 'smilies_src', includes_url( "images/smilies/$img" ), $img, site_url() );

	return sprintf( '<img src="%s" alt="%s" class="wp-smiley" style="height: 1em; max-height: 1em;" />', esc_url( $src_url ), esc_attr( $smiley ) );
}

/**
 * Convert text equivalent of smilies to images.
 *
 * Will only convert smilies if the option 'use_smilies' is true and the global
 * used in the function isn't empty.
 *
 * @since 0.71
 *
 * @global string|array $wp_smiliessearch
 *
 * @param string $text Content to convert smilies from text.
 * @return string Converted content with text smilies replaced with images.
 */
function convert_smilies( $text ) {
	global $wp_smiliessearch;
	$output = '';
	if ( get_option( 'use_smilies' ) && ! empty( $wp_smiliessearch ) ) {
		// HTML loop taken from texturize function, could possible be consolidated
		$textarr = preg_split( '/(<.*>)/U', $text, -1, PREG_SPLIT_DELIM_CAPTURE ); // capture the tags as well as in between
		$stop = count( $textarr );// loop stuff

		// Ignore proessing of specific tags
		$tags_to_ignore = 'code|pre|style|script|textarea';
		$ignore_block_element = '';

		for ( $i = 0; $i < $stop; $i++ ) {
			$content = $textarr[$i];

			// If we're in an ignore block, wait until we find its closing tag
			if ( '' == $ignore_block_element && preg_match( '/^<(' . $tags_to_ignore . ')>/', $content, $matches ) )  {
				$ignore_block_element = $matches[1];
			}

			// If it's not a tag and not in ignore block
			if ( '' ==  $ignore_block_element && strlen( $content ) > 0 && '<' != $content[0] ) {
				$content = preg_replace_callback( $wp_smiliessearch, 'translate_smiley', $content );
			}

			// did we exit ignore block
			if ( '' != $ignore_block_element && '</' . $ignore_block_element . '>' == $content )  {
				$ignore_block_element = '';
			}

			$output .= $content;
		}
	} else {
		// return default text.
		$output = $text;
	}
	return $output;
}

/**
 * Verifies that an email is valid.
 *
 * Does not grok i18n domains. Not RFC compliant.
 *
 * @since 0.71
 *
 * @param string $email      Email address to verify.
 * @param bool   $deprecated Deprecated.
 * @return string|bool Either false or the valid email address.
 */
function is_email( $email, $deprecated = false ) {
	if ( ! empty( $deprecated ) )
		_deprecated_argument( __FUNCTION__, '3.0' );

	// Test for the minimum length the email can be
	if ( strlen( $email ) < 3 ) {
		/**
		 * Filter whether an email address is valid.
		 *
		 * This filter is evaluated under several different contexts, such as 'email_too_short',
		 * 'email_no_at', 'local_invalid_chars', 'domain_period_sequence', 'domain_period_limits',
		 * 'domain_no_periods', 'sub_hyphen_limits', 'sub_invalid_chars', or no specific context.
		 *
		 * @since 2.8.0
		 *
		 * @param bool   $is_email Whether the email address has passed the is_email() checks. Default false.
		 * @param string $email    The email address being checked.
		 * @param string $context  Context under which the email was tested.
		 */
		return apply_filters( 'is_email', false, $email, 'email_too_short' );
	}

	// Test for an @ character after the first position
	if ( strpos( $email, '@', 1 ) === false ) {
		/** This filter is documented in wp-includes/formatting.php */
		return apply_filters( 'is_email', false, $email, 'email_no_at' );
	}

	// Split out the local and domain parts
	list( $local, $domain ) = explode( '@', $email, 2 );

	// LOCAL PART
	// Test for invalid characters
	if ( !preg_match( '/^[a-zA-Z0-9!#$%&\'*+\/=?^_`{|}~\.-]+$/', $local ) ) {
		/** This filter is documented in wp-includes/formatting.php */
		return apply_filters( 'is_email', false, $email, 'local_invalid_chars' );
	}

	// DOMAIN PART
	// Test for sequences of periods
	if ( preg_match( '/\.{2,}/', $domain ) ) {
		/** This filter is documented in wp-includes/formatting.php */
		return apply_filters( 'is_email', false, $email, 'domain_period_sequence' );
	}

	// Test for leading and trailing periods and whitespace
	if ( trim( $domain, " \t\n\r\0\x0B." ) !== $domain ) {
		/** This filter is documented in wp-includes/formatting.php */
		return apply_filters( 'is_email', false, $email, 'domain_period_limits' );
	}

	// Split the domain into subs
	$subs = explode( '.', $domain );

	// Assume the domain will have at least two subs
	if ( 2 > count( $subs ) ) {
		/** This filter is documented in wp-includes/formatting.php */
		return apply_filters( 'is_email', false, $email, 'domain_no_periods' );
	}

	// Loop through each sub
	foreach ( $subs as $sub ) {
		// Test for leading and trailing hyphens and whitespace
		if ( trim( $sub, " \t\n\r\0\x0B-" ) !== $sub ) {
			/** This filter is documented in wp-includes/formatting.php */
			return apply_filters( 'is_email', false, $email, 'sub_hyphen_limits' );
		}

		// Test for invalid characters
		if ( !preg_match('/^[a-z0-9-]+$/i', $sub ) ) {
			/** This filter is documented in wp-includes/formatting.php */
			return apply_filters( 'is_email', false, $email, 'sub_invalid_chars' );
		}
	}

	// Congratulations your email made it!
	/** This filter is documented in wp-includes/formatting.php */
	return apply_filters( 'is_email', $email, $email, null );
}

/**
 * Convert to ASCII from email subjects.
 *
 * @since 1.2.0
 *
 * @param string $string Subject line
 * @return string Converted string to ASCII
 */
function wp_iso_descrambler( $string ) {
	/* this may only work with iso-8859-1, I'm afraid */
	if (!preg_match('#\=\?(.+)\?Q\?(.+)\?\=#i', $string, $matches)) {
		return $string;
	} else {
		$subject = str_replace('_', ' ', $matches[2]);
		return preg_replace_callback( '#\=([0-9a-f]{2})#i', '_wp_iso_convert', $subject );
	}
}

/**
 * Helper function to convert hex encoded chars to ASCII
 *
 * @since 3.1.0
 * @access private
 *
 * @param array $match The preg_replace_callback matches array
 * @return string Converted chars
 */
function _wp_iso_convert( $match ) {
	return chr( hexdec( strtolower( $match[1] ) ) );
}

/**
 * Returns a date in the GMT equivalent.
 *
 * Requires and returns a date in the Y-m-d H:i:s format. If there is a
 * timezone_string available, the date is assumed to be in that timezone,
 * otherwise it simply subtracts the value of the 'gmt_offset' option. Return
 * format can be overridden using the $format parameter.
 *
 * @since 1.2.0
 *
 * @param string $string The date to be converted.
 * @param string $format The format string for the returned date (default is Y-m-d H:i:s)
 * @return string GMT version of the date provided.
 */
function get_gmt_from_date( $string, $format = 'Y-m-d H:i:s' ) {
	$tz = get_option( 'timezone_string' );
	if ( $tz ) {
		$datetime = date_create( $string, new DateTimeZone( $tz ) );
		if ( ! $datetime ) {
			return gmdate( $format, 0 );
		}
		$datetime->setTimezone( new DateTimeZone( 'UTC' ) );
		$string_gmt = $datetime->format( $format );
	} else {
		if ( ! preg_match( '#([0-9]{1,4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})#', $string, $matches ) ) {
			$datetime = strtotime( $string );
			if ( false === $datetime ) {
				return gmdate( $format, 0 );
			}
			return gmdate( $format, $datetime );
		}
		$string_time = gmmktime( $matches[4], $matches[5], $matches[6], $matches[2], $matches[3], $matches[1] );
		$string_gmt = gmdate( $format, $string_time - get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );
	}
	return $string_gmt;
}

/**
 * Converts a GMT date into the correct format for the blog.
 *
 * Requires and returns a date in the Y-m-d H:i:s format. If there is a
 * timezone_string available, the returned date is in that timezone, otherwise
 * it simply adds the value of gmt_offset. Return format can be overridden
 * using the $format parameter
 *
 * @since 1.2.0
 *
 * @param string $string The date to be converted.
 * @param string $format The format string for the returned date (default is Y-m-d H:i:s)
 * @return string Formatted date relative to the timezone / GMT offset.
 */
function get_date_from_gmt( $string, $format = 'Y-m-d H:i:s' ) {
	$tz = get_option( 'timezone_string' );
	if ( $tz ) {
		$datetime = date_create( $string, new DateTimeZone( 'UTC' ) );
		if ( ! $datetime )
			return date( $format, 0 );
		$datetime->setTimezone( new DateTimeZone( $tz ) );
		$string_localtime = $datetime->format( $format );
	} else {
		if ( ! preg_match('#([0-9]{1,4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})#', $string, $matches) )
			return date( $format, 0 );
		$string_time = gmmktime( $matches[4], $matches[5], $matches[6], $matches[2], $matches[3], $matches[1] );
		$string_localtime = gmdate( $format, $string_time + get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );
	}
	return $string_localtime;
}

/**
 * Computes an offset in seconds from an iso8601 timezone.
 *
 * @since 1.5.0
 *
 * @param string $timezone Either 'Z' for 0 offset or '±hhmm'.
 * @return int|float The offset in seconds.
 */
function iso8601_timezone_to_offset( $timezone ) {
	// $timezone is either 'Z' or '[+|-]hhmm'
	if ($timezone == 'Z') {
		$offset = 0;
	} else {
		$sign    = (substr($timezone, 0, 1) == '+') ? 1 : -1;
		$hours   = intval(substr($timezone, 1, 2));
		$minutes = intval(substr($timezone, 3, 4)) / 60;
		$offset  = $sign * HOUR_IN_SECONDS * ($hours + $minutes);
	}
	return $offset;
}

/**
 * Converts an iso8601 date to MySQL DateTime format used by post_date[_gmt].
 *
 * @since 1.5.0
 *
 * @param string $date_string Date and time in ISO 8601 format {@link http://en.wikipedia.org/wiki/ISO_8601}.
 * @param string $timezone    Optional. If set to GMT returns the time minus gmt_offset. Default is 'user'.
 * @return string The date and time in MySQL DateTime format - Y-m-d H:i:s.
 */
function iso8601_to_datetime( $date_string, $timezone = 'user' ) {
	$timezone = strtolower($timezone);

	if ($timezone == 'gmt') {

		preg_match('#([0-9]{4})([0-9]{2})([0-9]{2})T([0-9]{2}):([0-9]{2}):([0-9]{2})(Z|[\+|\-][0-9]{2,4}){0,1}#', $date_string, $date_bits);

		if (!empty($date_bits[7])) { // we have a timezone, so let's compute an offset
			$offset = iso8601_timezone_to_offset($date_bits[7]);
		} else { // we don't have a timezone, so we assume user local timezone (not server's!)
			$offset = HOUR_IN_SECONDS * get_option('gmt_offset');
		}

		$timestamp = gmmktime($date_bits[4], $date_bits[5], $date_bits[6], $date_bits[2], $date_bits[3], $date_bits[1]);
		$timestamp -= $offset;

		return gmdate('Y-m-d H:i:s', $timestamp);

	} elseif ($timezone == 'user') {
		return preg_replace('#([0-9]{4})([0-9]{2})([0-9]{2})T([0-9]{2}):([0-9]{2}):([0-9]{2})(Z|[\+|\-][0-9]{2,4}){0,1}#', '$1-$2-$3 $4:$5:$6', $date_string);
	}
}

/**
 * Strips out all characters that are not allowable in an email.
 *
 * @since 1.5.0
 *
 * @param string $email Email address to filter.
 * @return string Filtered email address.
 */
function sanitize_email( $email ) {
	// Test for the minimum length the email can be
	if ( strlen( $email ) < 3 ) {
		/**
		 * Filter a sanitized email address.
		 *
		 * This filter is evaluated under several contexts, including 'email_too_short',
		 * 'email_no_at', 'local_invalid_chars', 'domain_period_sequence', 'domain_period_limits',
		 * 'domain_no_periods', 'domain_no_valid_subs', or no context.
		 *
		 * @since 2.8.0
		 *
		 * @param string $email   The sanitized email address.
		 * @param string $email   The email address, as provided to sanitize_email().
		 * @param string $message A message to pass to the user.
		 */
		return apply_filters( 'sanitize_email', '', $email, 'email_too_short' );
	}

	// Test for an @ character after the first position
	if ( strpos( $email, '@', 1 ) === false ) {
		/** This filter is documented in wp-includes/formatting.php */
		return apply_filters( 'sanitize_email', '', $email, 'email_no_at' );
	}

	// Split out the local and domain parts
	list( $local, $domain ) = explode( '@', $email, 2 );

	// LOCAL PART
	// Test for invalid characters
	$local = preg_replace( '/[^a-zA-Z0-9!#$%&\'*+\/=?^_`{|}~\.-]/', '', $local );
	if ( '' === $local ) {
		/** This filter is documented in wp-includes/formatting.php */
		return apply_filters( 'sanitize_email', '', $email, 'local_invalid_chars' );
	}

	// DOMAIN PART
	// Test for sequences of periods
	$domain = preg_replace( '/\.{2,}/', '', $domain );
	if ( '' === $domain ) {
		/** This filter is documented in wp-includes/formatting.php */
		return apply_filters( 'sanitize_email', '', $email, 'domain_period_sequence' );
	}

	// Test for leading and trailing periods and whitespace
	$domain = trim( $domain, " \t\n\r\0\x0B." );
	if ( '' === $domain ) {
		/** This filter is documented in wp-includes/formatting.php */
		return apply_filters( 'sanitize_email', '', $email, 'domain_period_limits' );
	}

	// Split the domain into subs
	$subs = explode( '.', $domain );

	// Assume the domain will have at least two subs
	if ( 2 > count( $subs ) ) {
		/** This filter is documented in wp-includes/formatting.php */
		return apply_filters( 'sanitize_email', '', $email, 'domain_no_periods' );
	}

	// Create an array that will contain valid subs
	$new_subs = array();

	// Loop through each sub
	foreach ( $subs as $sub ) {
		// Test for leading and trailing hyphens
		$sub = trim( $sub, " \t\n\r\0\x0B-" );

		// Test for invalid characters
		$sub = preg_replace( '/[^a-z0-9-]+/i', '', $sub );

		// If there's anything left, add it to the valid subs
		if ( '' !== $sub ) {
			$new_subs[] = $sub;
		}
	}

	// If there aren't 2 or more valid subs
	if ( 2 > count( $new_subs ) ) {
		/** This filter is documented in wp-includes/formatting.php */
		return apply_filters( 'sanitize_email', '', $email, 'domain_no_valid_subs' );
	}

	// Join valid subs into the new domain
	$domain = join( '.', $new_subs );

	// Put the email back together
	$email = $local . '@' . $domain;

	// Congratulations your email made it!
	/** This filter is documented in wp-includes/formatting.php */
	return apply_filters( 'sanitize_email', $email, $email, null );
}

/**
 * Determines the difference between two timestamps.
 *
 * The difference is returned in a human readable format such as "1 hour",
 * "5 mins", "2 days".
 *
 * @since 1.5.0
 *
 * @param int $from Unix timestamp from which the difference begins.
 * @param int $to   Optional. Unix timestamp to end the time difference. Default becomes time() if not set.
 * @return string Human readable time difference.
 */
function human_time_diff( $from, $to = '' ) {
	if ( empty( $to ) ) {
		$to = time();
	}

	$diff = (int) abs( $to - $from );

	if ( $diff < HOUR_IN_SECONDS ) {
		$mins = round( $diff / MINUTE_IN_SECONDS );
		if ( $mins <= 1 )
			$mins = 1;
		/* translators: min=minute */
		$since = sprintf( _n( '%s min', '%s mins', $mins ), $mins );
	} elseif ( $diff < DAY_IN_SECONDS && $diff >= HOUR_IN_SECONDS ) {
		$hours = round( $diff / HOUR_IN_SECONDS );
		if ( $hours <= 1 )
			$hours = 1;
		$since = sprintf( _n( '%s hour', '%s hours', $hours ), $hours );
	} elseif ( $diff < WEEK_IN_SECONDS && $diff >= DAY_IN_SECONDS ) {
		$days = round( $diff / DAY_IN_SECONDS );
		if ( $days <= 1 )
			$days = 1;
		$since = sprintf( _n( '%s day', '%s days', $days ), $days );
	} elseif ( $diff < MONTH_IN_SECONDS && $diff >= WEEK_IN_SECONDS ) {
		$weeks = round( $diff / WEEK_IN_SECONDS );
		if ( $weeks <= 1 )
			$weeks = 1;
		$since = sprintf( _n( '%s week', '%s weeks', $weeks ), $weeks );
	} elseif ( $diff < YEAR_IN_SECONDS && $diff >= MONTH_IN_SECONDS ) {
		$months = round( $diff / MONTH_IN_SECONDS );
		if ( $months <= 1 )
			$months = 1;
		$since = sprintf( _n( '%s month', '%s months', $months ), $months );
	} elseif ( $diff >= YEAR_IN_SECONDS ) {
		$years = round( $diff / YEAR_IN_SECONDS );
		if ( $years <= 1 )
			$years = 1;
		$since = sprintf( _n( '%s year', '%s years', $years ), $years );
	}

	/**
	 * Filter the human readable difference between two timestamps.
	 *
	 * @since 4.0.0
	 *
	 * @param string $since The difference in human readable text.
	 * @param int    $diff  The difference in seconds.
	 * @param int    $from  Unix timestamp from which the difference begins.
	 * @param int    $to    Unix timestamp to end the time difference.
	 */
	return apply_filters( 'human_time_diff', $since, $diff, $from, $to );
}

/**
 * Generates an excerpt from the content, if needed.
 *
 * The excerpt word amount will be 55 words and if the amount is greater than
 * that, then the string ' [&hellip;]' will be appended to the excerpt. If the string
 * is less than 55 words, then the content will be returned as is.
 *
 * The 55 word limit can be modified by plugins/themes using the excerpt_length filter
 * The ' [&hellip;]' string can be modified by plugins/themes using the excerpt_more filter
 *
 * @since 1.5.0
 *
 * @param string $text Optional. The excerpt. If set to empty, an excerpt is generated.
 * @return string The excerpt.
 */
function wp_trim_excerpt( $text = '' ) {
	$raw_excerpt = $text;
	if ( '' == $text ) {
		$text = get_the_content('');

		$text = strip_shortcodes( $text );

		/** This filter is documented in wp-includes/post-template.php */
		$text = apply_filters( 'the_content', $text );
		$text = str_replace(']]>', ']]&gt;', $text);

		/**
		 * Filter the number of words in an excerpt.
		 *
		 * @since 2.7.0
		 *
		 * @param int $number The number of words. Default 55.
		 */
		$excerpt_length = apply_filters( 'excerpt_length', 55 );
		/**
		 * Filter the string in the "more" link displayed after a trimmed excerpt.
		 *
		 * @since 2.9.0
		 *
		 * @param string $more_string The string shown within the more link.
		 */
		$excerpt_more = apply_filters( 'excerpt_more', ' ' . '[&hellip;]' );
		$text = wp_trim_words( $text, $excerpt_length, $excerpt_more );
	}
	/**
	 * Filter the trimmed excerpt string.
	 *
	 * @since 2.8.0
	 *
	 * @param string $text        The trimmed text.
	 * @param string $raw_excerpt The text prior to trimming.
	 */
	return apply_filters( 'wp_trim_excerpt', $text, $raw_excerpt );
}

/**
 * Trims text to a certain number of words.
 *
 * This function is localized. For languages that count 'words' by the individual
 * character (such as East Asian languages), the $num_words argument will apply
 * to the number of individual characters.
 *
 * @since 3.3.0
 *
 * @param string $text      Text to trim.
 * @param int    $num_words Number of words. Default 55.
 * @param string $more      Optional. What to append if $text needs to be trimmed. Default '&hellip;'.
 * @return string Trimmed text.
 */
function wp_trim_words( $text, $num_words = 55, $more = null ) {
	if ( null === $more ) {
		$more = __( '&hellip;' );
	}

	$original_text = $text;
	$text = wp_strip_all_tags( $text );

	/*
	 * translators: If your word count is based on single characters (e.g. East Asian characters),
	 * enter 'characters_excluding_spaces' or 'characters_including_spaces'. Otherwise, enter 'words'.
	 * Do not translate into your own language.
	 */
	if ( strpos( _x( 'words', 'Word count type. Do not translate!' ), 'characters' ) === 0 && preg_match( '/^utf\-?8$/i', get_option( 'blog_charset' ) ) ) {
		$text = trim( preg_replace( "/[\n\r\t ]+/", ' ', $text ), ' ' );
		preg_match_all( '/./u', $text, $words_array );
		$words_array = array_slice( $words_array[0], 0, $num_words + 1 );
		$sep = '';
	} else {
		$words_array = preg_split( "/[\n\r\t ]+/", $text, $num_words + 1, PREG_SPLIT_NO_EMPTY );
		$sep = ' ';
	}

	if ( count( $words_array ) > $num_words ) {
		array_pop( $words_array );
		$text = implode( $sep, $words_array );
		$text = $text . $more;
	} else {
		$text = implode( $sep, $words_array );
	}

	/**
	 * Filter the text content after words have been trimmed.
	 *
	 * @since 3.3.0
	 *
	 * @param string $text          The trimmed text.
	 * @param int    $num_words     The number of words to trim the text to. Default 5.
	 * @param string $more          An optional string to append to the end of the trimmed text, e.g. &hellip;.
	 * @param string $original_text The text before it was trimmed.
	 */
	return apply_filters( 'wp_trim_words', $text, $num_words, $more, $original_text );
}

/**
 * Converts named entities into numbered entities.
 *
 * @since 1.5.1
 *
 * @param string $text The text within which entities will be converted.
 * @return string Text with converted entities.
 */
function ent2ncr( $text ) {

	/**
	 * Filter text before named entities are converted into numbered entities.
	 *
	 * A non-null string must be returned for the filter to be evaluated.
	 *
	 * @since 3.3.0
	 *
	 * @param null   $converted_text The text to be converted. Default null.
	 * @param string $text           The text prior to entity conversion.
	 */
	$filtered = apply_filters( 'pre_ent2ncr', null, $text );
	if ( null !== $filtered )
		return $filtered;

	$to_ncr = array(
		'&quot;' => '&#34;',
		'&amp;' => '&#38;',
		'&lt;' => '&#60;',
		'&gt;' => '&#62;',
		'|' => '&#124;',
		'&nbsp;' => '&#160;',
		'&iexcl;' => '&#161;',
		'&cent;' => '&#162;',
		'&pound;' => '&#163;',
		'&curren;' => '&#164;',
		'&yen;' => '&#165;',
		'&brvbar;' => '&#166;',
		'&brkbar;' => '&#166;',
		'&sect;' => '&#167;',
		'&uml;' => '&#168;',
		'&die;' => '&#168;',
		'&copy;' => '&#169;',
		'&ordf;' => '&#170;',
		'&laquo;' => '&#171;',
		'&not;' => '&#172;',
		'&shy;' => '&#173;',
		'&reg;' => '&#174;',
		'&macr;' => '&#175;',
		'&hibar;' => '&#175;',
		'&deg;' => '&#176;',
		'&plusmn;' => '&#177;',
		'&sup2;' => '&#178;',
		'&sup3;' => '&#179;',
		'&acute;' => '&#180;',
		'&micro;' => '&#181;',
		'&para;' => '&#182;',
		'&middot;' => '&#183;',
		'&cedil;' => '&#184;',
		'&sup1;' => '&#185;',
		'&ordm;' => '&#186;',
		'&raquo;' => '&#187;',
		'&frac14;' => '&#188;',
		'&frac12;' => '&#189;',
		'&frac34;' => '&#190;',
		'&iquest;' => '&#191;',
		'&Agrave;' => '&#192;',
		'&Aacute;' => '&#193;',
		'&Acirc;' => '&#194;',
		'&Atilde;' => '&#195;',
		'&Auml;' => '&#196;',
		'&Aring;' => '&#197;',
		'&AElig;' => '&#198;',
		'&Ccedil;' => '&#199;',
		'&Egrave;' => '&#200;',
		'&Eacute;' => '&#201;',
		'&Ecirc;' => '&#202;',
		'&Euml;' => '&#203;',
		'&Igrave;' => '&#204;',
		'&Iacute;' => '&#205;',
		'&Icirc;' => '&#206;',
		'&Iuml;' => '&#207;',
		'&ETH;' => '&#208;',
		'&Ntilde;' => '&#209;',
		'&Ograve;' => '&#210;',
		'&Oacute;' => '&#211;',
		'&Ocirc;' => '&#212;',
		'&Otilde;' => '&#213;',
		'&Ouml;' => '&#214;',
		'&times;' => '&#215;',
		'&Oslash;' => '&#216;',
		'&Ugrave;' => '&#217;',
		'&Uacute;' => '&#218;',
		'&Ucirc;' => '&#219;',
		'&Uuml;' => '&#220;',
		'&Yacute;' => '&#221;',
		'&THORN;' => '&#222;',
		'&szlig;' => '&#223;',
		'&agrave;' => '&#224;',
		'&aacute;' => '&#225;',
		'&acirc;' => '&#226;',
		'&atilde;' => '&#227;',
		'&auml;' => '&#228;',
		'&aring;' => '&#229;',
		'&aelig;' => '&#230;',
		'&ccedil;' => '&#231;',
		'&egrave;' => '&#232;',
		'&eacute;' => '&#233;',
		'&ecirc;' => '&#234;',
		'&euml;' => '&#235;',
		'&igrave;' => '&#236;',
		'&iacute;' => '&#237;',
		'&icirc;' => '&#238;',
		'&iuml;' => '&#239;',
		'&eth;' => '&#240;',
		'&ntilde;' => '&#241;',
		'&ograve;' => '&#242;',
		'&oacute;' => '&#243;',
		'&ocirc;' => '&#244;',
		'&otilde;' => '&#245;',
		'&ouml;' => '&#246;',
		'&divide;' => '&#247;',
		'&oslash;' => '&#248;',
		'&ugrave;' => '&#249;',
		'&uacute;' => '&#250;',
		'&ucirc;' => '&#251;',
		'&uuml;' => '&#252;',
		'&yacute;' => '&#253;',
		'&thorn;' => '&#254;',
		'&yuml;' => '&#255;',
		'&OElig;' => '&#338;',
		'&oelig;' => '&#339;',
		'&Scaron;' => '&#352;',
		'&scaron;' => '&#353;',
		'&Yuml;' => '&#376;',
		'&fnof;' => '&#402;',
		'&circ;' => '&#710;',
		'&tilde;' => '&#732;',
		'&Alpha;' => '&#913;',
		'&Beta;' => '&#914;',
		'&Gamma;' => '&#915;',
		'&Delta;' => '&#916;',
		'&Epsilon;' => '&#917;',
		'&Zeta;' => '&#918;',
		'&Eta;' => '&#919;',
		'&Theta;' => '&#920;',
		'&Iota;' => '&#921;',
		'&Kappa;' => '&#922;',
		'&Lambda;' => '&#923;',
		'&Mu;' => '&#924;',
		'&Nu;' => '&#925;',
		'&Xi;' => '&#926;',
		'&Omicron;' => '&#927;',
		'&Pi;' => '&#928;',
		'&Rho;' => '&#929;',
		'&Sigma;' => '&#931;',
		'&Tau;' => '&#932;',
		'&Upsilon;' => '&#933;',
		'&Phi;' => '&#934;',
		'&Chi;' => '&#935;',
		'&Psi;' => '&#936;',
		'&Omega;' => '&#937;',
		'&alpha;' => '&#945;',
		'&beta;' => '&#946;',
		'&gamma;' => '&#947;',
		'&delta;' => '&#948;',
		'&epsilon;' => '&#949;',
		'&zeta;' => '&#950;',
		'&eta;' => '&#951;',
		'&theta;' => '&#952;',
		'&iota;' => '&#953;',
		'&kappa;' => '&#954;',
		'&lambda;' => '&#955;',
		'&mu;' => '&#956;',
		'&nu;' => '&#957;',
		'&xi;' => '&#958;',
		'&omicron;' => '&#959;',
		'&pi;' => '&#960;',
		'&rho;' => '&#961;',
		'&sigmaf;' => '&#962;',
		'&sigma;' => '&#963;',
		'&tau;' => '&#964;',
		'&upsilon;' => '&#965;',
		'&phi;' => '&#966;',
		'&chi;' => '&#967;',
		'&psi;' => '&#968;',
		'&omega;' => '&#969;',
		'&thetasym;' => '&#977;',
		'&upsih;' => '&#978;',
		'&piv;' => '&#982;',
		'&ensp;' => '&#8194;',
		'&emsp;' => '&#8195;',
		'&thinsp;' => '&#8201;',
		'&zwnj;' => '&#8204;',
		'&zwj;' => '&#8205;',
		'&lrm;' => '&#8206;',
		'&rlm;' => '&#8207;',
		'&ndash;' => '&#8211;',
		'&mdash;' => '&#8212;',
		'&lsquo;' => '&#8216;',
		'&rsquo;' => '&#8217;',
		'&sbquo;' => '&#8218;',
		'&ldquo;' => '&#8220;',
		'&rdquo;' => '&#8221;',
		'&bdquo;' => '&#8222;',
		'&dagger;' => '&#8224;',
		'&Dagger;' => '&#8225;',
		'&bull;' => '&#8226;',
		'&hellip;' => '&#8230;',
		'&permil;' => '&#8240;',
		'&prime;' => '&#8242;',
		'&Prime;' => '&#8243;',
		'&lsaquo;' => '&#8249;',
		'&rsaquo;' => '&#8250;',
		'&oline;' => '&#8254;',
		'&frasl;' => '&#8260;',
		'&euro;' => '&#8364;',
		'&image;' => '&#8465;',
		'&weierp;' => '&#8472;',
		'&real;' => '&#8476;',
		'&trade;' => '&#8482;',
		'&alefsym;' => '&#8501;',
		'&crarr;' => '&#8629;',
		'&lArr;' => '&#8656;',
		'&uArr;' => '&#8657;',
		'&rArr;' => '&#8658;',
		'&dArr;' => '&#8659;',
		'&hArr;' => '&#8660;',
		'&forall;' => '&#8704;',
		'&part;' => '&#8706;',
		'&exist;' => '&#8707;',
		'&empty;' => '&#8709;',
		'&nabla;' => '&#8711;',
		'&isin;' => '&#8712;',
		'&notin;' => '&#8713;',
		'&ni;' => '&#8715;',
		'&prod;' => '&#8719;',
		'&sum;' => '&#8721;',
		'&minus;' => '&#8722;',
		'&lowast;' => '&#8727;',
		'&radic;' => '&#8730;',
		'&prop;' => '&#8733;',
		'&infin;' => '&#8734;',
		'&ang;' => '&#8736;',
		'&and;' => '&#8743;',
		'&or;' => '&#8744;',
		'&cap;' => '&#8745;',
		'&cup;' => '&#8746;',
		'&int;' => '&#8747;',
		'&there4;' => '&#8756;',
		'&sim;' => '&#8764;',
		'&cong;' => '&#8773;',
		'&asymp;' => '&#8776;',
		'&ne;' => '&#8800;',
		'&equiv;' => '&#8801;',
		'&le;' => '&#8804;',
		'&ge;' => '&#8805;',
		'&sub;' => '&#8834;',
		'&sup;' => '&#8835;',
		'&nsub;' => '&#8836;',
		'&sube;' => '&#8838;',
		'&supe;' => '&#8839;',
		'&oplus;' => '&#8853;',
		'&otimes;' => '&#8855;',
		'&perp;' => '&#8869;',
		'&sdot;' => '&#8901;',
		'&lceil;' => '&#8968;',
		'&rceil;' => '&#8969;',
		'&lfloor;' => '&#8970;',
		'&rfloor;' => '&#8971;',
		'&lang;' => '&#9001;',
		'&rang;' => '&#9002;',
		'&larr;' => '&#8592;',
		'&uarr;' => '&#8593;',
		'&rarr;' => '&#8594;',
		'&darr;' => '&#8595;',
		'&harr;' => '&#8596;',
		'&loz;' => '&#9674;',
		'&spades;' => '&#9824;',
		'&clubs;' => '&#9827;',
		'&hearts;' => '&#9829;',
		'&diams;' => '&#9830;'
	);

	return str_replace( array_keys($to_ncr), array_values($to_ncr), $text );
}

/**
 * Formats text for the editor.
 *
 * Generally the browsers treat everything inside a textarea as text, but
 * it is still a good idea to HTML entity encode `<`, `>` and `&` in the content.
 *
 * The filter {@see 'format_for_editor'} is applied here. If `$text` is empty the
 * filter will be applied to an empty string.
 *
 * @since 4.3.0
 *
 * @see _WP_Editors::editor()
 *
 * @param string $text           The text to be formatted.
 * @param string $default_editor The default editor for the current user.
 *                               It is usually either 'html' or 'tinymce'.
 * @return string The formatted text after filter is applied.
 */
function format_for_editor( $text, $default_editor = null ) {
	if ( $text ) {
		$text = htmlspecialchars( $text, ENT_NOQUOTES, get_option( 'blog_charset' ) );
	}

	/**
	 * Filter the text after it is formatted for the editor.
	 *
	 * @since 4.3.0
	 *
	 * @param string $text           The formatted text.
	 * @param string $default_editor The default editor for the current user.
	 *                               It is usually either 'html' or 'tinymce'.
	 */
	return apply_filters( 'format_for_editor', $text, $default_editor );
}

/**
 * Perform a deep string replace operation to ensure the values in $search are no longer present
 *
 * Repeats the replacement operation until it no longer replaces anything so as to remove "nested" values
 * e.g. $subject = '%0%0%0DDD', $search ='%0D', $result ='' rather than the '%0%0DD' that
 * str_replace would return
 *
 * @since 2.8.1
 * @access private
 *
 * @param string|array $search  The value being searched for, otherwise known as the needle.
 *                              An array may be used to designate multiple needles.
 * @param string       $subject The string being searched and replaced on, otherwise known as the haystack.
 * @return string The string with the replaced svalues.
 */
function _deep_replace( $search, $subject ) {
	$subject = (string) $subject;

	$count = 1;
	while ( $count ) {
		$subject = str_replace( $search, '', $subject, $count );
	}

	return $subject;
}

/**
 * Escapes data for use in a MySQL query.
 *
 * Usually you should prepare queries using wpdb::prepare().
 * Sometimes, spot-escaping is required or useful. One example
 * is preparing an array for use in an IN clause.
 *
 * @since 2.8.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string|array $data Unescaped data
 * @return string|array Escaped data
 */
function esc_sql( $data ) {
	global $wpdb;
	return $wpdb->_escape( $data );
}

/**
 * Checks and cleans a URL.
 *
 * A number of characters are removed from the URL. If the URL is for displaying
 * (the default behaviour) ampersands are also replaced. The 'clean_url' filter
 * is applied to the returned cleaned URL.
 *
 * @since 2.8.0
 *
 * @param string $url       The URL to be cleaned.
 * @param array  $protocols Optional. An array of acceptable protocols.
 *		                    Defaults to return value of wp_allowed_protocols()
 * @param string $_context  Private. Use esc_url_raw() for database usage.
 * @return string The cleaned $url after the 'clean_url' filter is applied.
 */
function esc_url( $url, $protocols = null, $_context = 'display' ) {
	$original_url = $url;

	if ( '' == $url )
		return $url;

	$url = str_replace( ' ', '%20', $url );
	$url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\[\]\\x80-\\xff]|i', '', $url);

	if ( '' === $url ) {
		return $url;
	}

	if ( 0 !== stripos( $url, 'mailto:' ) ) {
		$strip = array('%0d', '%0a', '%0D', '%0A');
		$url = _deep_replace($strip, $url);
	}

	$url = str_replace(';//', '://', $url);
	/* If the URL doesn't appear to contain a scheme, we
	 * presume it needs http:// prepended (unless a relative
	 * link starting with /, # or ? or a php file).
	 */
	if ( strpos($url, ':') === false && ! in_array( $url[0], array( '/', '#', '?' ) ) &&
		! preg_match('/^[a-z0-9-]+?\.php/i', $url) )
		$url = 'http://' . $url;

	// Replace ampersands and single quotes only when displaying.
	if ( 'display' == $_context ) {
		$url = wp_kses_normalize_entities( $url );
		$url = str_replace( '&amp;', '&#038;', $url );
		$url = str_replace( "'", '&#039;', $url );
	}

	if ( ( false !== strpos( $url, '[' ) ) || ( false !== strpos( $url, ']' ) ) ) {

		$parsed = wp_parse_url( $url );
		$front  = '';

		if ( isset( $parsed['scheme'] ) ) {
			$front .= $parsed['scheme'] . '://';
		} elseif ( '/' === $url[0] ) {
			$front .= '//';
		}

		if ( isset( $parsed['user'] ) ) {
			$front .= $parsed['user'];
		}

		if ( isset( $parsed['pass'] ) ) {
			$front .= ':' . $parsed['pass'];
		}

		if ( isset( $parsed['user'] ) || isset( $parsed['pass'] ) ) {
			$front .= '@';
		}

		if ( isset( $parsed['host'] ) ) {
			$front .= $parsed['host'];
		}

		if ( isset( $parsed['port'] ) ) {
			$front .= ':' . $parsed['port'];
		}

		$end_dirty = str_replace( $front, '', $url );
		$end_clean = str_replace( array( '[', ']' ), array( '%5B', '%5D' ), $end_dirty );
		$url       = str_replace( $end_dirty, $end_clean, $url );

	}

	if ( '/' === $url[0] ) {
		$good_protocol_url = $url;
	} else {
		if ( ! is_array( $protocols ) )
			$protocols = wp_allowed_protocols();
		$good_protocol_url = wp_kses_bad_protocol( $url, $protocols );
		if ( strtolower( $good_protocol_url ) != strtolower( $url ) )
			return '';
	}

	/**
	 * Filter a string cleaned and escaped for output as a URL.
	 *
	 * @since 2.3.0
	 *
	 * @param string $good_protocol_url The cleaned URL to be returned.
	 * @param string $original_url      The URL prior to cleaning.
	 * @param string $_context          If 'display', replace ampersands and single quotes only.
	 */
	return apply_filters( 'clean_url', $good_protocol_url, $original_url, $_context );
}

/**
 * Performs esc_url() for database usage.
 *
 * @since 2.8.0
 *
 * @param string $url       The URL to be cleaned.
 * @param array  $protocols An array of acceptable protocols.
 * @return string The cleaned URL.
 */
function esc_url_raw( $url, $protocols = null ) {
	return esc_url( $url, $protocols, 'db' );
}

/**
 * Convert entities, while preserving already-encoded entities.
 *
 * @link http://www.php.net/htmlentities Borrowed from the PHP Manual user notes.
 *
 * @since 1.2.2
 *
 * @param string $myHTML The text to be converted.
 * @return string Converted text.
 */
function htmlentities2( $myHTML ) {
	$translation_table = get_html_translation_table( HTML_ENTITIES, ENT_QUOTES );
	$translation_table[chr(38)] = '&';
	return preg_replace( "/&(?![A-Za-z]{0,4}\w{2,3};|#[0-9]{2,3};)/", "&amp;", strtr($myHTML, $translation_table) );
}

/**
 * Escape single quotes, htmlspecialchar " < > &, and fix line endings.
 *
 * Escapes text strings for echoing in JS. It is intended to be used for inline JS
 * (in a tag attribute, for example onclick="..."). Note that the strings have to
 * be in single quotes. The filter 'js_escape' is also applied here.
 *
 * @since 2.8.0
 *
 * @param string $text The text to be escaped.
 * @return string Escaped text.
 */
function esc_js( $text ) {
	$safe_text = wp_check_invalid_utf8( $text );
	$safe_text = _wp_specialchars( $safe_text, ENT_COMPAT );
	$safe_text = preg_replace( '/&#(x)?0*(?(1)27|39);?/i', "'", stripslashes( $safe_text ) );
	$safe_text = str_replace( "\r", '', $safe_text );
	$safe_text = str_replace( "\n", '\\n', addslashes( $safe_text ) );
	/**
	 * Filter a string cleaned and escaped for output in JavaScript.
	 *
	 * Text passed to esc_js() is stripped of invalid or special characters,
	 * and properly slashed for output.
	 *
	 * @since 2.0.6
	 *
	 * @param string $safe_text The text after it has been escaped.
 	 * @param string $text      The text prior to being escaped.
	 */
	return apply_filters( 'js_escape', $safe_text, $text );
}

/**
 * Escaping for HTML blocks.
 *
 * @since 2.8.0
 *
 * @param string $text
 * @return string
 */
function esc_html( $text ) {
	$safe_text = wp_check_invalid_utf8( $text );
	$safe_text = _wp_specialchars( $safe_text, ENT_QUOTES );
	/**
	 * Filter a string cleaned and escaped for output in HTML.
	 *
	 * Text passed to esc_html() is stripped of invalid or special characters
	 * before output.
	 *
	 * @since 2.8.0
	 *
	 * @param string $safe_text The text after it has been escaped.
 	 * @param string $text      The text prior to being escaped.
	 */
	return apply_filters( 'esc_html', $safe_text, $text );
}

/**
 * Escaping for HTML attributes.
 *
 * @since 2.8.0
 *
 * @param string $text
 * @return string
 */
function esc_attr( $text ) {
	$safe_text = wp_check_invalid_utf8( $text );
	$safe_text = _wp_specialchars( $safe_text, ENT_QUOTES );
	/**
	 * Filter a string cleaned and escaped for output in an HTML attribute.
	 *
	 * Text passed to esc_attr() is stripped of invalid or special characters
	 * before output.
	 *
	 * @since 2.0.6
	 *
	 * @param string $safe_text The text after it has been escaped.
 	 * @param string $text      The text prior to being escaped.
	 */
	return apply_filters( 'attribute_escape', $safe_text, $text );
}

/**
 * Escaping for textarea values.
 *
 * @since 3.1.0
 *
 * @param string $text
 * @return string
 */
function esc_textarea( $text ) {
	$safe_text = htmlspecialchars( $text, ENT_QUOTES, get_option( 'blog_charset' ) );
	/**
	 * Filter a string cleaned and escaped for output in a textarea element.
	 *
	 * @since 3.1.0
	 *
	 * @param string $safe_text The text after it has been escaped.
 	 * @param string $text      The text prior to being escaped.
	 */
	return apply_filters( 'esc_textarea', $safe_text, $text );
}

/**
 * Escape an HTML tag name.
 *
 * @since 2.5.0
 *
 * @param string $tag_name
 * @return string
 */
function tag_escape( $tag_name ) {
	$safe_tag = strtolower( preg_replace('/[^a-zA-Z0-9_:]/', '', $tag_name) );
	/**
	 * Filter a string cleaned and escaped for output as an HTML tag.
	 *
	 * @since 2.8.0
	 *
	 * @param string $safe_tag The tag name after it has been escaped.
 	 * @param string $tag_name The text before it was escaped.
	 */
	return apply_filters( 'tag_escape', $safe_tag, $tag_name );
}

/**
 * Convert full URL paths to absolute paths.
 *
 * Removes the http or https protocols and the domain. Keeps the path '/' at the
 * beginning, so it isn't a true relative link, but from the web root base.
 *
 * @since 2.1.0
 * @since 4.1.0 Support was added for relative URLs.
 *
 * @param string $link Full URL path.
 * @return string Absolute path.
 */
function wp_make_link_relative( $link ) {
	return preg_replace( '|^(https?:)?//[^/]+(/?.*)|i', '$2', $link );
}

/**
 * Sanitises various option values based on the nature of the option.
 *
 * This is basically a switch statement which will pass $value through a number
 * of functions depending on the $option.
 *
 * @since 2.0.5
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string $option The name of the option.
 * @param string $value  The unsanitised value.
 * @return string Sanitized value.
 */
function sanitize_option( $option, $value ) {
	global $wpdb;

	$original_value = $value;
	$error = '';

	switch ( $option ) {
		case 'admin_email' :
		case 'new_admin_email' :
			$value = $wpdb->strip_invalid_text_for_column( $wpdb->options, 'option_value', $value );
			if ( is_wp_error( $value ) ) {
				$error = $value->get_error_message();
			} else {
				$value = sanitize_email( $value );
				if ( ! is_email( $value ) ) {
					$error = __( 'The email address entered did not appear to be a valid email address. Please enter a valid email address.' );
				}
			}
			break;

		case 'thumbnail_size_w':
		case 'thumbnail_size_h':
		case 'medium_size_w':
		case 'medium_size_h':
		case 'medium_large_size_w':
		case 'medium_large_size_h':
		case 'large_size_w':
		case 'large_size_h':
		case 'mailserver_port':
		case 'comment_max_links':
		case 'page_on_front':
		case 'page_for_posts':
		case 'rss_excerpt_length':
		case 'default_category':
		case 'default_email_category':
		case 'default_link_category':
		case 'close_comments_days_old':
		case 'comments_per_page':
		case 'thread_comments_depth':
		case 'users_can_register':
		case 'start_of_week':
		case 'site_icon':
			$value = absint( $value );
			break;

		case 'posts_per_page':
		case 'posts_per_rss':
			$value = (int) $value;
			if ( empty($value) )
				$value = 1;
			if ( $value < -1 )
				$value = abs($value);
			break;

		case 'default_ping_status':
		case 'default_comment_status':
			// Options that if not there have 0 value but need to be something like "closed"
			if ( $value == '0' || $value == '')
				$value = 'closed';
			break;

		case 'blogdescription':
		case 'blogname':
			$value = $wpdb->strip_invalid_text_for_column( $wpdb->options, 'option_value', $value );
			if ( is_wp_error( $value ) ) {
				$error = $value->get_error_message();
			} else {
				$value = esc_html( $value );
			}
			break;

		case 'blog_charset':
			$value = preg_replace('/[^a-zA-Z0-9_-]/', '', $value); // strips slashes
			break;

		case 'blog_public':
			// This is the value if the settings checkbox is not checked on POST. Don't rely on this.
			if ( null === $value )
				$value = 1;
			else
				$value = intval( $value );
			break;

		case 'date_format':
		case 'time_format':
		case 'mailserver_url':
		case 'mailserver_login':
		case 'mailserver_pass':
		case 'upload_path':
			$value = $wpdb->strip_invalid_text_for_column( $wpdb->options, 'option_value', $value );
			if ( is_wp_error( $value ) ) {
				$error = $value->get_error_message();
			} else {
				$value = strip_tags( $value );
				$value = wp_kses_data( $value );
			}
			break;

		case 'ping_sites':
			$value = explode( "\n", $value );
			$value = array_filter( array_map( 'trim', $value ) );
			$value = array_filter( array_map( 'esc_url_raw', $value ) );
			$value = implode( "\n", $value );
			break;

		case 'gmt_offset':
			$value = preg_replace('/[^0-9:.-]/', '', $value); // strips slashes
			break;

		case 'siteurl':
			$value = $wpdb->strip_invalid_text_for_column( $wpdb->options, 'option_value', $value );
			if ( is_wp_error( $value ) ) {
				$error = $value->get_error_message();
			} else {
				if ( preg_match( '#http(s?)://(.+)#i', $value ) ) {
					$value = esc_url_raw( $value );
				} else {
					$error = __( 'The WordPress address you entered did not appear to be a valid URL. Please enter a valid URL.' );
				}
			}
			break;

		case 'home':
			$value = $wpdb->strip_invalid_text_for_column( $wpdb->options, 'option_value', $value );
			if ( is_wp_error( $value ) ) {
				$error = $value->get_error_message();
			} else {
				if ( preg_match( '#http(s?)://(.+)#i', $value ) ) {
					$value = esc_url_raw( $value );
				} else {
					$error = __( 'The Site address you entered did not appear to be a valid URL. Please enter a valid URL.' );
				}
			}
			break;

		case 'WPLANG':
			$allowed = get_available_languages();
			if ( ! is_multisite() && defined( 'WPLANG' ) && '' !== WPLANG && 'en_US' !== WPLANG ) {
				$allowed[] = WPLANG;
			}
			if ( ! in_array( $value, $allowed ) && ! empty( $value ) ) {
				$value = get_option( $option );
			}
			break;

		case 'illegal_names':
			$value = $wpdb->strip_invalid_text_for_column( $wpdb->options, 'option_value', $value );
			if ( is_wp_error( $value ) ) {
				$error = $value->get_error_message();
			} else {
				if ( ! is_array( $value ) )
					$value = explode( ' ', $value );

				$value = array_values( array_filter( array_map( 'trim', $value ) ) );

				if ( ! $value )
					$value = '';
			}
			break;

		case 'limited_email_domains':
		case 'banned_email_domains':
			$value = $wpdb->strip_invalid_text_for_column( $wpdb->options, 'option_value', $value );
			if ( is_wp_error( $value ) ) {
				$error = $value->get_error_message();
			} else {
				if ( ! is_array( $value ) )
					$value = explode( "\n", $value );

				$domains = array_values( array_filter( array_map( 'trim', $value ) ) );
				$value = array();

				foreach ( $domains as $domain ) {
					if ( ! preg_match( '/(--|\.\.)/', $domain ) && preg_match( '|^([a-zA-Z0-9-\.])+$|', $domain ) ) {
						$value[] = $domain;
					}
				}
				if ( ! $value )
					$value = '';
			}
			break;

		case 'timezone_string':
			$allowed_zones = timezone_identifiers_list();
			if ( ! in_array( $value, $allowed_zones ) && ! empty( $value ) ) {
				$error = __( 'The timezone you have entered is not valid. Please select a valid timezone.' );
			}
			break;

		case 'permalink_structure':
		case 'category_base':
		case 'tag_base':
			$value = $wpdb->strip_invalid_text_for_column( $wpdb->options, 'option_value', $value );
			if ( is_wp_error( $value ) ) {
				$error = $value->get_error_message();
			} else {
				$value = esc_url_raw( $value );
				$value = str_replace( 'http://', '', $value );
			}
			break;

		case 'default_role' :
			if ( ! get_role( $value ) && get_role( 'subscriber' ) )
				$value = 'subscriber';
			break;

		case 'moderation_keys':
		case 'blacklist_keys':
			$value = $wpdb->strip_invalid_text_for_column( $wpdb->options, 'option_value', $value );
			if ( is_wp_error( $value ) ) {
				$error = $value->get_error_message();
			} else {
				$value = explode( "\n", $value );
				$value = array_filter( array_map( 'trim', $value ) );
				$value = array_unique( $value );
				$value = implode( "\n", $value );
			}
			break;
	}

	if ( ! empty( $error ) ) {
		$value = get_option( $option );
		if ( function_exists( 'add_settings_error' ) ) {
			add_settings_error( $option, "invalid_{$option}", $error );
		}
	}

	/**
	 * Filter an option value following sanitization.
	 *
	 * @since 2.3.0
	 * @since 4.3.0 Added the `$original_value` parameter.
	 *
	 * @param string $value          The sanitized option value.
	 * @param string $option         The option name.
	 * @param string $original_value The original value passed to the function.
	 */
	return apply_filters( "sanitize_option_{$option}", $value, $option, $original_value );
}

/**
 * Maps a function to all non-iterable elements of an array or an object.
 *
 * This is similar to `array_walk_recursive()` but acts upon objects too.
 *
 * @since 4.4.0
 *
 * @param mixed    $value    The array, object, or scalar.
 * @param callable $callback The function to map onto $value.
 * @return The value with the callback applied to all non-arrays and non-objects inside it.
 */
function map_deep( $value, $callback ) {
	if ( is_array( $value ) ) {
		foreach ( $value as $index => $item ) {
			$value[ $index ] = map_deep( $item, $callback );
		}
	} elseif ( is_object( $value ) ) {
		$object_vars = get_object_vars( $value );
		foreach ( $object_vars as $property_name => $property_value ) {
			$value->$property_name = map_deep( $property_value, $callback );
		}
	} else {
		$value = call_user_func( $callback, $value );
	}

	return $value;
}

/**
 * Parses a string into variables to be stored in an array.
 *
 * Uses {@link http://www.php.net/parse_str parse_str()} and stripslashes if
 * {@link http://www.php.net/magic_quotes magic_quotes_gpc} is on.
 *
 * @since 2.2.1
 *
 * @param string $string The string to be parsed.
 * @param array  $array  Variables will be stored in this array.
 */
function wp_parse_str( $string, &$array ) {
	parse_str( $string, $array );
	if ( get_magic_quotes_gpc() )
		$array = stripslashes_deep( $array );
	/**
	 * Filter the array of variables derived from a parsed string.
	 *
	 * @since 2.3.0
	 *
	 * @param array $array The array populated with variables.
	 */
	$array = apply_filters( 'wp_parse_str', $array );
}

/**
 * Convert lone less than signs.
 *
 * KSES already converts lone greater than signs.
 *
 * @since 2.3.0
 *
 * @param string $text Text to be converted.
 * @return string Converted text.
 */
function wp_pre_kses_less_than( $text ) {
	return preg_replace_callback('%<[^>]*?((?=<)|>|$)%', 'wp_pre_kses_less_than_callback', $text);
}

/**
 * Callback function used by preg_replace.
 *
 * @since 2.3.0
 *
 * @param array $matches Populated by matches to preg_replace.
 * @return string The text returned after esc_html if needed.
 */
function wp_pre_kses_less_than_callback( $matches ) {
	if ( false === strpos($matches[0], '>') )
		return esc_html($matches[0]);
	return $matches[0];
}

/**
 * WordPress implementation of PHP sprintf() with filters.
 *
 * @since 2.5.0
 * @link http://www.php.net/sprintf
 *
 * @param string $pattern   The string which formatted args are inserted.
 * @param mixed  $args ,... Arguments to be formatted into the $pattern string.
 * @return string The formatted string.
 */
function wp_sprintf( $pattern ) {
	$args = func_get_args();
	$len = strlen($pattern);
	$start = 0;
	$result = '';
	$arg_index = 0;
	while ( $len > $start ) {
		// Last character: append and break
		if ( strlen($pattern) - 1 == $start ) {
			$result .= substr($pattern, -1);
			break;
		}

		// Literal %: append and continue
		if ( substr($pattern, $start, 2) == '%%' ) {
			$start += 2;
			$result .= '%';
			continue;
		}

		// Get fragment before next %
		$end = strpos($pattern, '%', $start + 1);
		if ( false === $end )
			$end = $len;
		$fragment = substr($pattern, $start, $end - $start);

		// Fragment has a specifier
		if ( $pattern[$start] == '%' ) {
			// Find numbered arguments or take the next one in order
			if ( preg_match('/^%(\d+)\$/', $fragment, $matches) ) {
				$arg = isset($args[$matches[1]]) ? $args[$matches[1]] : '';
				$fragment = str_replace("%{$matches[1]}$", '%', $fragment);
			} else {
				++$arg_index;
				$arg = isset($args[$arg_index]) ? $args[$arg_index] : '';
			}

			/**
			 * Filter a fragment from the pattern passed to wp_sprintf().
			 *
			 * If the fragment is unchanged, then sprintf() will be run on the fragment.
			 *
			 * @since 2.5.0
			 *
			 * @param string $fragment A fragment from the pattern.
			 * @param string $arg      The argument.
			 */
			$_fragment = apply_filters( 'wp_sprintf', $fragment, $arg );
			if ( $_fragment != $fragment )
				$fragment = $_fragment;
			else
				$fragment = sprintf($fragment, strval($arg) );
		}

		// Append to result and move to next fragment
		$result .= $fragment;
		$start = $end;
	}
	return $result;
}

/**
 * Localize list items before the rest of the content.
 *
 * The '%l' must be at the first characters can then contain the rest of the
 * content. The list items will have ', ', ', and', and ' and ' added depending
 * on the amount of list items in the $args parameter.
 *
 * @since 2.5.0
 *
 * @param string $pattern Content containing '%l' at the beginning.
 * @param array  $args    List items to prepend to the content and replace '%l'.
 * @return string Localized list items and rest of the content.
 */
function wp_sprintf_l( $pattern, $args ) {
	// Not a match
	if ( substr($pattern, 0, 2) != '%l' )
		return $pattern;

	// Nothing to work with
	if ( empty($args) )
		return '';

	/**
	 * Filter the translated delimiters used by wp_sprintf_l().
	 * Placeholders (%s) are included to assist translators and then
	 * removed before the array of strings reaches the filter.
	 *
	 * Please note: Ampersands and entities should be avoided here.
	 *
	 * @since 2.5.0
	 *
	 * @param array $delimiters An array of translated delimiters.
	 */
	$l = apply_filters( 'wp_sprintf_l', array(
		/* translators: used to join items in a list with more than 2 items */
		'between'          => sprintf( __('%s, %s'), '', '' ),
		/* translators: used to join last two items in a list with more than 2 times */
		'between_last_two' => sprintf( __('%s, and %s'), '', '' ),
		/* translators: used to join items in a list with only 2 items */
		'between_only_two' => sprintf( __('%s and %s'), '', '' ),
	) );

	$args = (array) $args;
	$result = array_shift($args);
	if ( count($args) == 1 )
		$result .= $l['between_only_two'] . array_shift($args);
	// Loop when more than two args
	$i = count($args);
	while ( $i ) {
		$arg = array_shift($args);
		$i--;
		if ( 0 == $i )
			$result .= $l['between_last_two'] . $arg;
		else
			$result .= $l['between'] . $arg;
	}
	return $result . substr($pattern, 2);
}

/**
 * Safely extracts not more than the first $count characters from html string.
 *
 * UTF-8, tags and entities safe prefix extraction. Entities inside will *NOT*
 * be counted as one character. For example &amp; will be counted as 4, &lt; as
 * 3, etc.
 *
 * @since 2.5.0
 *
 * @param string $str   String to get the excerpt from.
 * @param int    $count Maximum number of characters to take.
 * @param string $more  Optional. What to append if $str needs to be trimmed. Defaults to empty string.
 * @return string The excerpt.
 */
function wp_html_excerpt( $str, $count, $more = null ) {
	if ( null === $more )
		$more = '';
	$str = wp_strip_all_tags( $str, true );
	$excerpt = mb_substr( $str, 0, $count );
	// remove part of an entity at the end
	$excerpt = preg_replace( '/&[^;\s]{0,6}$/', '', $excerpt );
	if ( $str != $excerpt )
		$excerpt = trim( $excerpt ) . $more;
	return $excerpt;
}

/**
 * Add a Base url to relative links in passed content.
 *
 * By default it supports the 'src' and 'href' attributes. However this can be
 * changed via the 3rd param.
 *
 * @since 2.7.0
 *
 * @global string $_links_add_base
 *
 * @param string $content String to search for links in.
 * @param string $base    The base URL to prefix to links.
 * @param array  $attrs   The attributes which should be processed.
 * @return string The processed content.
 */
function links_add_base_url( $content, $base, $attrs = array('src', 'href') ) {
	global $_links_add_base;
	$_links_add_base = $base;
	$attrs = implode('|', (array)$attrs);
	return preg_replace_callback( "!($attrs)=(['\"])(.+?)\\2!i", '_links_add_base', $content );
}

/**
 * Callback to add a base url to relative links in passed content.
 *
 * @since 2.7.0
 * @access private
 *
 * @global string $_links_add_base
 *
 * @param string $m The matched link.
 * @return string The processed link.
 */
function _links_add_base( $m ) {
	global $_links_add_base;
	//1 = attribute name  2 = quotation mark  3 = URL
	return $m[1] . '=' . $m[2] .
		( preg_match( '#^(\w{1,20}):#', $m[3], $protocol ) && in_array( $protocol[1], wp_allowed_protocols() ) ?
			$m[3] :
			WP_Http::make_absolute_url( $m[3], $_links_add_base )
		)
		. $m[2];
}

/**
 * Adds a Target attribute to all links in passed content.
 *
 * This function by default only applies to `<a>` tags, however this can be
 * modified by the 3rd param.
 *
 * *NOTE:* Any current target attributed will be stripped and replaced.
 *
 * @since 2.7.0
 *
 * @global string $_links_add_target
 *
 * @param string $content String to search for links in.
 * @param string $target  The Target to add to the links.
 * @param array  $tags    An array of tags to apply to.
 * @return string The processed content.
 */
function links_add_target( $content, $target = '_blank', $tags = array('a') ) {
	global $_links_add_target;
	$_links_add_target = $target;
	$tags = implode('|', (array)$tags);
	return preg_replace_callback( "!<($tags)([^>]*)>!i", '_links_add_target', $content );
}

/**
 * Callback to add a target attribute to all links in passed content.
 *
 * @since 2.7.0
 * @access private
 *
 * @global string $_links_add_target
 *
 * @param string $m The matched link.
 * @return string The processed link.
 */
function _links_add_target( $m ) {
	global $_links_add_target;
	$tag = $m[1];
	$link = preg_replace('|( target=([\'"])(.*?)\2)|i', '', $m[2]);
	return '<' . $tag . $link . ' target="' . esc_attr( $_links_add_target ) . '">';
}

/**
 * Normalize EOL characters and strip duplicate whitespace.
 *
 * @since 2.7.0
 *
 * @param string $str The string to normalize.
 * @return string The normalized string.
 */
function normalize_whitespace( $str ) {
	$str  = trim( $str );
	$str  = str_replace( "\r", "\n", $str );
	$str  = preg_replace( array( '/\n+/', '/[ \t]+/' ), array( "\n", ' ' ), $str );
	return $str;
}

/**
 * Properly strip all HTML tags including script and style
 *
 * This differs from strip_tags() because it removes the contents of
 * the `<script>` and `<style>` tags. E.g. `strip_tags( '<script>something</script>' )`
 * will return 'something'. wp_strip_all_tags will return ''
 *
 * @since 2.9.0
 *
 * @param string $string        String containing HTML tags
 * @param bool   $remove_breaks Optional. Whether to remove left over line breaks and white space chars
 * @return string The processed string.
 */
function wp_strip_all_tags($string, $remove_breaks = false) {
	$string = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $string );
	$string = strip_tags($string);

	if ( $remove_breaks )
		$string = preg_replace('/[\r\n\t ]+/', ' ', $string);

	return trim( $string );
}

/**
 * Sanitize a string from user input or from the db
 *
 * check for invalid UTF-8,
 * Convert single < characters to entity,
 * strip all tags,
 * remove line breaks, tabs and extra white space,
 * strip octets.
 *
 * @since 2.9.0
 *
 * @param string $str
 * @return string
 */
function sanitize_text_field( $str ) {
	$filtered = wp_check_invalid_utf8( $str );

	if ( strpos($filtered, '<') !== false ) {
		$filtered = wp_pre_kses_less_than( $filtered );
		// This will strip extra whitespace for us.
		$filtered = wp_strip_all_tags( $filtered, true );
	} else {
		$filtered = trim( preg_replace('/[\r\n\t ]+/', ' ', $filtered) );
	}

	$found = false;
	while ( preg_match('/%[a-f0-9]{2}/i', $filtered, $match) ) {
		$filtered = str_replace($match[0], '', $filtered);
		$found = true;
	}

	if ( $found ) {
		// Strip out the whitespace that may now exist after removing the octets.
		$filtered = trim( preg_replace('/ +/', ' ', $filtered) );
	}

	/**
	 * Filter a sanitized text field string.
	 *
	 * @since 2.9.0
	 *
	 * @param string $filtered The sanitized string.
	 * @param string $str      The string prior to being sanitized.
	 */
	return apply_filters( 'sanitize_text_field', $filtered, $str );
}

/**
 * i18n friendly version of basename()
 *
 * @since 3.1.0
 *
 * @param string $path   A path.
 * @param string $suffix If the filename ends in suffix this will also be cut off.
 * @return string
 */
function wp_basename( $path, $suffix = '' ) {
	return urldecode( basename( str_replace( array( '%2F', '%5C' ), '/', urlencode( $path ) ), $suffix ) );
}

/**
 * Forever eliminate "Wordpress" from the planet (or at least the little bit we can influence).
 *
 * Violating our coding standards for a good function name.
 *
 * @since 3.0.0
 *
 * @staticvar string|false $dblq
 *
 * @param string $text The text to be modified.
 * @return string The modified text.
 */
function capital_P_dangit( $text ) {
	// Simple replacement for titles
	$current_filter = current_filter();
	if ( 'the_title' === $current_filter || 'wp_title' === $current_filter )
		return str_replace( 'Wordpress', 'WordPress', $text );
	// Still here? Use the more judicious replacement
	static $dblq = false;
	if ( false === $dblq ) {
		$dblq = _x( '&#8220;', 'opening curly double quote' );
	}
	return str_replace(
		array( ' Wordpress', '&#8216;Wordpress', $dblq . 'Wordpress', '>Wordpress', '(Wordpress' ),
		array( ' WordPress', '&#8216;WordPress', $dblq . 'WordPress', '>WordPress', '(WordPress' ),
	$text );
}

/**
 * Sanitize a mime type
 *
 * @since 3.1.3
 *
 * @param string $mime_type Mime type
 * @return string Sanitized mime type
 */
function sanitize_mime_type( $mime_type ) {
	$sani_mime_type = preg_replace( '/[^-+*.a-zA-Z0-9\/]/', '', $mime_type );
	/**
	 * Filter a mime type following sanitization.
	 *
	 * @since 3.1.3
	 *
	 * @param string $sani_mime_type The sanitized mime type.
	 * @param string $mime_type      The mime type prior to sanitization.
	 */
	return apply_filters( 'sanitize_mime_type', $sani_mime_type, $mime_type );
}

/**
 * Sanitize space or carriage return separated URLs that are used to send trackbacks.
 *
 * @since 3.4.0
 *
 * @param string $to_ping Space or carriage return separated URLs
 * @return string URLs starting with the http or https protocol, separated by a carriage return.
 */
function sanitize_trackback_urls( $to_ping ) {
	$urls_to_ping = preg_split( '/[\r\n\t ]/', trim( $to_ping ), -1, PREG_SPLIT_NO_EMPTY );
	foreach ( $urls_to_ping as $k => $url ) {
		if ( !preg_match( '#^https?://.#i', $url ) )
			unset( $urls_to_ping[$k] );
	}
	$urls_to_ping = array_map( 'esc_url_raw', $urls_to_ping );
	$urls_to_ping = implode( "\n", $urls_to_ping );
	/**
	 * Filter a list of trackback URLs following sanitization.
	 *
	 * The string returned here consists of a space or carriage return-delimited list
	 * of trackback URLs.
	 *
	 * @since 3.4.0
	 *
	 * @param string $urls_to_ping Sanitized space or carriage return separated URLs.
	 * @param string $to_ping      Space or carriage return separated URLs before sanitization.
	 */
	return apply_filters( 'sanitize_trackback_urls', $urls_to_ping, $to_ping );
}

/**
 * Add slashes to a string or array of strings.
 *
 * This should be used when preparing data for core API that expects slashed data.
 * This should not be used to escape data going directly into an SQL query.
 *
 * @since 3.6.0
 *
 * @param string|array $value String or array of strings to slash.
 * @return string|array Slashed $value
 */
function wp_slash( $value ) {
	if ( is_array( $value ) ) {
		foreach ( $value as $k => $v ) {
			if ( is_array( $v ) ) {
				$value[$k] = wp_slash( $v );
			} else {
				$value[$k] = addslashes( $v );
			}
		}
	} else {
		$value = addslashes( $value );
	}

	return $value;
}

/**
 * Remove slashes from a string or array of strings.
 *
 * This should be used to remove slashes from data passed to core API that
 * expects data to be unslashed.
 *
 * @since 3.6.0
 *
 * @param string|array $value String or array of strings to unslash.
 * @return string|array Unslashed $value
 */
function wp_unslash( $value ) {
	return stripslashes_deep( $value );
}

/**
 * Extract and return the first URL from passed content.
 *
 * @since 3.6.0
 *
 * @param string $content A string which might contain a URL.
 * @return string|false The found URL.
 */
function get_url_in_content( $content ) {
	if ( empty( $content ) ) {
		return false;
	}

	if ( preg_match( '/<a\s[^>]*?href=([\'"])(.+?)\1/is', $content, $matches ) ) {
		return esc_url_raw( $matches[2] );
	}

	return false;
}

/**
 * Returns the regexp for common whitespace characters.
 *
 * By default, spaces include new lines, tabs, nbsp entities, and the UTF-8 nbsp.
 * This is designed to replace the PCRE \s sequence.  In ticket #22692, that
 * sequence was found to be unreliable due to random inclusion of the A0 byte.
 *
 * @since 4.0.0
 *
 * @staticvar string $spaces
 *
 * @return string The spaces regexp.
 */
function wp_spaces_regexp() {
	static $spaces = '';

	if ( empty( $spaces ) ) {
		/**
		 * Filter the regexp for common whitespace characters.
		 *
		 * This string is substituted for the \s sequence as needed in regular
		 * expressions. For websites not written in English, different characters
		 * may represent whitespace. For websites not encoded in UTF-8, the 0xC2 0xA0
		 * sequence may not be in use.
		 *
		 * @since 4.0.0
		 *
		 * @param string $spaces Regexp pattern for matching common whitespace characters.
		 */
		$spaces = apply_filters( 'wp_spaces_regexp', '[\r\n\t ]|\xC2\xA0|&nbsp;' );
	}

	return $spaces;
}

/**
 * Print the important emoji-related styles.
 *
 * @since 4.2.0
 *
 * @staticvar bool $printed
 */
function print_emoji_styles() {
	static $printed = false;

	if ( $printed ) {
		return;
	}

	$printed = true;
?>
<style type="text/css">
img.wp-smiley,
img.emoji {
	display: inline !important;
	border: none !important;
	box-shadow: none !important;
	height: 1em !important;
	width: 1em !important;
	margin: 0 .07em !important;
	vertical-align: -0.1em !important;
	background: none !important;
	padding: 0 !important;
}
</style>
<?php
}

/**
 *
 * @global string $wp_version
 * @staticvar bool $printed
 */
function print_emoji_detection_script() {
	global $wp_version;
	static $printed = false;

	if ( $printed ) {
		return;
	}

	$printed = true;

	$settings = array(
		/**
		 * Filter the URL where emoji images are hosted.
		 *
		 * @since 4.2.0
		 *
		 * @param string The emoji base URL.
		 */
		'baseUrl' => apply_filters( 'emoji_url', 'https://s.w.org/images/core/emoji/72x72/' ),

		/**
		 * Filter the extension of the emoji files.
		 *
		 * @since 4.2.0
		 *
		 * @param string The emoji extension. Default .png.
		 */
		'ext' => apply_filters( 'emoji_ext', '.png' ),
	);

	$version = 'ver=' . $wp_version;

	if ( SCRIPT_DEBUG ) {
		$settings['source'] = array(
			/** This filter is documented in wp-includes/class.wp-scripts.php */
			'wpemoji' => apply_filters( 'script_loader_src', includes_url( "js/wp-emoji.js?$version" ), 'wpemoji' ),
			/** This filter is documented in wp-includes/class.wp-scripts.php */
			'twemoji' => apply_filters( 'script_loader_src', includes_url( "js/twemoji.js?$version" ), 'twemoji' ),
		);

		?>
		<script type="text/javascript">
			window._wpemojiSettings = <?php echo wp_json_encode( $settings ); ?>;
			<?php readfile( ABSPATH . WPINC . "/js/wp-emoji-loader.js" ); ?>
		</script>
		<?php
	} else {
		$settings['source'] = array(
			/** This filter is documented in wp-includes/class.wp-scripts.php */
			'concatemoji' => apply_filters( 'script_loader_src', includes_url( "js/wp-emoji-release.min.js?$version" ), 'concatemoji' ),
		);

		/*
		 * If you're looking at a src version of this file, you'll see an "include"
		 * statement below. This is used by the `grunt build` process to directly
		 * include a minified version of wp-emoji-loader.js, instead of using the
		 * readfile() method from above.
		 *
		 * If you're looking at a build version of this file, you'll see a string of
		 * minified JavaScript. If you need to debug it, please turn on SCRIPT_DEBUG
		 * and edit wp-emoji-loader.js directly.
		 */
		?>
		<script type="text/javascript">
			window._wpemojiSettings = <?php echo wp_json_encode( $settings ); ?>;
			!function(a,b,c){function d(a){var c,d=b.createElement("canvas"),e=d.getContext&&d.getContext("2d"),f=String.fromCharCode;return e&&e.fillText?(e.textBaseline="top",e.font="600 32px Arial","flag"===a?(e.fillText(f(55356,56806,55356,56826),0,0),d.toDataURL().length>3e3):"diversity"===a?(e.fillText(f(55356,57221),0,0),c=e.getImageData(16,16,1,1).data.toString(),e.fillText(f(55356,57221,55356,57343),0,0),c!==e.getImageData(16,16,1,1).data.toString()):("simple"===a?e.fillText(f(55357,56835),0,0):e.fillText(f(55356,57135),0,0),0!==e.getImageData(16,16,1,1).data[0])):!1}function e(a){var c=b.createElement("script");c.src=a,c.type="text/javascript",b.getElementsByTagName("head")[0].appendChild(c)}var f,g;c.supports={simple:d("simple"),flag:d("flag"),unicode8:d("unicode8"),diversity:d("diversity")},c.DOMReady=!1,c.readyCallback=function(){c.DOMReady=!0},c.supports.simple&&c.supports.flag&&c.supports.unicode8&&c.supports.diversity||(g=function(){c.readyCallback()},b.addEventListener?(b.addEventListener("DOMContentLoaded",g,!1),a.addEventListener("load",g,!1)):(a.attachEvent("onload",g),b.attachEvent("onreadystatechange",function(){"complete"===b.readyState&&c.readyCallback()})),f=c.source||{},f.concatemoji?e(f.concatemoji):f.wpemoji&&f.twemoji&&(e(f.twemoji),e(f.wpemoji)))}(window,document,window._wpemojiSettings);
		</script>
		<?php
	}
}

/**
 * Convert any 4 byte emoji in a string to their equivalent HTML entity.
 *
 * Currently, only Unicode 7 emoji are supported. Skin tone modifiers are allowed,
 * all other Unicode 8 emoji will be added when the spec is finalised.
 *
 * This allows us to store emoji in a DB using the utf8 character set.
 *
 * @since 4.2.0
 *
 * @param string $content The content to encode.
 * @return string The encoded content.
 */
function wp_encode_emoji( $content ) {
	if ( function_exists( 'mb_convert_encoding' ) ) {
		$regex = '/(
		     \x23\xE2\x83\xA3               # Digits
		     [\x30-\x39]\xE2\x83\xA3
		   | \xF0\x9F[\x85-\x88][\xA6-\xBF] # Enclosed characters
		   | \xF0\x9F[\x8C-\x97][\x80-\xBF] # Misc
		   | \xF0\x9F\x98[\x80-\xBF]        # Smilies
		   | \xF0\x9F\x99[\x80-\x8F]
		   | \xF0\x9F\x9A[\x80-\xBF]        # Transport and map symbols
		)/x';

		$matches = array();
		if ( preg_match_all( $regex, $content, $matches ) ) {
			if ( ! empty( $matches[1] ) ) {
				foreach ( $matches[1] as $emoji ) {
					/*
					 * UTF-32's hex encoding is the same as HTML's hex encoding.
					 * So, by converting the emoji from UTF-8 to UTF-32, we magically
					 * get the correct hex encoding.
					 */
					$unpacked = unpack( 'H*', mb_convert_encoding( $emoji, 'UTF-32', 'UTF-8' ) );
					if ( isset( $unpacked[1] ) ) {
						$entity = '&#x' . ltrim( $unpacked[1], '0' ) . ';';
						$content = str_replace( $emoji, $entity, $content );
					}
				}
			}
		}
	}

	return $content;
}

/**
 * Convert emoji to a static img element.
 *
 * @since 4.2.0
 *
 * @param string $text The content to encode.
 * @return string The encoded content.
 */
function wp_staticize_emoji( $text ) {
	$text = wp_encode_emoji( $text );

	/** This filter is documented in wp-includes/formatting.php */
	$cdn_url = apply_filters( 'emoji_url', 'https://s.w.org/images/core/emoji/72x72/' );

	/** This filter is documented in wp-includes/formatting.php */
	$ext = apply_filters( 'emoji_ext', '.png' );

	$output = '';
	/*
	 * HTML loop taken from smiley function, which was taken from texturize function.
	 * It'll never be consolidated.
	 *
	 * First, capture the tags as well as in between.
	 */
	$textarr = preg_split( '/(<.*>)/U', $text, -1, PREG_SPLIT_DELIM_CAPTURE );
	$stop = count( $textarr );

	// Ignore processing of specific tags.
	$tags_to_ignore = 'code|pre|style|script|textarea';
	$ignore_block_element = '';

	for ( $i = 0; $i < $stop; $i++ ) {
		$content = $textarr[$i];

		// If we're in an ignore block, wait until we find its closing tag.
		if ( '' == $ignore_block_element && preg_match( '/^<(' . $tags_to_ignore . ')>/', $content, $matches ) )  {
			$ignore_block_element = $matches[1];
		}

		// If it's not a tag and not in ignore block.
		if ( '' ==  $ignore_block_element && strlen( $content ) > 0 && '<' != $content[0] ) {
			$matches = array();
			if ( preg_match_all( '/(&#x1f1(e[6-9a-f]|f[0-9a-f]);){2}/', $content, $matches ) ) {
				if ( ! empty( $matches[0] ) ) {
					foreach ( $matches[0] as $flag ) {
						$chars = str_replace( array( '&#x', ';'), '', $flag );

						list( $char1, $char2 ) = str_split( $chars, 5 );
						$entity = sprintf( '<img src="%s" alt="%s" class="wp-smiley" style="height: 1em; max-height: 1em;" />', $cdn_url . $char1 . '-' . $char2 . $ext, html_entity_decode( $flag ) );

						$content = str_replace( $flag, $entity, $content );
					}
				}
			}

			// Loosely match the Emoji Unicode range.
			$regex = '/(&#x[2-3][0-9a-f]{3};|&#x1f[1-6][0-9a-f]{2};)/';

			$matches = array();
			if ( preg_match_all( $regex, $content, $matches ) ) {
				if ( ! empty( $matches[1] ) ) {
					foreach ( $matches[1] as $emoji ) {
						$char = str_replace( array( '&#x', ';'), '', $emoji );
						$entity = sprintf( '<img src="%s" alt="%s" class="wp-smiley" style="height: 1em; max-height: 1em;" />', $cdn_url . $char . $ext, html_entity_decode( $emoji ) );

						$content = str_replace( $emoji, $entity, $content );
					}
				}
			}
		}

		// Did we exit ignore block.
		if ( '' != $ignore_block_element && '</' . $ignore_block_element . '>' == $content )  {
			$ignore_block_element = '';
		}

		$output .= $content;
	}

	return $output;
}

/**
 * Convert emoji in emails into static images.
 *
 * @since 4.2.0
 *
 * @param array $mail The email data array.
 * @return array The email data array, with emoji in the message staticized.
 */
function wp_staticize_emoji_for_email( $mail ) {
	if ( ! isset( $mail['message'] ) ) {
		return $mail;
	}

	/*
	 * We can only transform the emoji into images if it's a text/html email.
	 * To do that, here's a cut down version of the same process that happens
	 * in wp_mail() - get the Content-Type from the headers, if there is one,
	 * then pass it through the wp_mail_content_type filter, in case a plugin
	 * is handling changing the Content-Type.
	 */
	$headers = array();
	if ( isset( $mail['headers'] ) ) {
		if ( is_array( $mail['headers'] ) ) {
			$headers = $mail['headers'];
		} else {
			$headers = explode( "\n", str_replace( "\r\n", "\n", $mail['headers'] ) );
		}
	}

	foreach ( $headers as $header ) {
		if ( strpos($header, ':') === false ) {
			continue;
		}

		// Explode them out.
		list( $name, $content ) = explode( ':', trim( $header ), 2 );

		// Cleanup crew.
		$name    = trim( $name    );
		$content = trim( $content );

		if ( 'content-type' === strtolower( $name ) ) {
			if ( strpos( $content, ';' ) !== false ) {
				list( $type, $charset ) = explode( ';', $content );
				$content_type = trim( $type );
			} else {
				$content_type = trim( $content );
			}
			break;
		}
	}

	// Set Content-Type if we don't have a content-type from the input headers.
	if ( ! isset( $content_type ) ) {
		$content_type = 'text/plain';
	}

	/** This filter is documented in wp-includes/pluggable.php */
	$content_type = apply_filters( 'wp_mail_content_type', $content_type );

	if ( 'text/html' === $content_type ) {
		$mail['message'] = wp_staticize_emoji( $mail['message'] );
	}

	return $mail;
}

/**
 * Shorten an URL, to be used as link text.
 *
 * @since 1.2.0
 * @since 4.4.0 Moved to wp-includes/formatting.php from wp-admin/includes/misc.php and added $length param.
 *
 * @param string $url    URL to shorten.
 * @param int    $length Optional. Maximum length of the shortened URL. Default 35 characters.
 * @return string Shortened URL.
 */
function url_shorten( $url, $length = 35 ) {
	$stripped = str_replace( array( 'https://', 'http://', 'www.' ), '', $url );
	$short_url = untrailingslashit( $stripped );

	if ( strlen( $short_url ) > $length ) {
		$short_url = substr( $short_url, 0, $length - 3 ) . '&hellip;';
	}
	return $short_url;
}

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
 * Do not use this function before the {@see 'init'} action hook; everything will break.
 *
 * @since 0.71
 *
 * @global array $wp_cockneyreplace Array of formatted entities for certain common phrases
 * @global array $shortcode_tags
 * @staticvar array  $static_characters
 * @staticvar array  $static_replacements
 * @staticvar array  $dynamic_characters
 * @staticvar array  $dynamic_replacements
 * @staticvar array  $default_no_texturize_tags
 * @staticvar array  $default_no_texturize_shortcodes
 * @staticvar bool   $run_texturize
 * @staticvar string $apos
 * @staticvar string $prime
 * @staticvar string $double_prime
 * @staticvar string $opening_quote
 * @staticvar string $closing_quote
 * @staticvar string $opening_single_quote
 * @staticvar string $closing_single_quote
 * @staticvar string $open_q_flag
 * @staticvar string $open_sq_flag
 * @staticvar string $apos_flag
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
		 * Filters whether to skip running wptexturize().
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
	 * Filters the list of HTML elements not to texturize.
	 *
	 * @since 2.8.0
	 *
	 * @param array $default_no_texturize_tags An array of HTML element names.
	 */
	$no_texturize_tags = apply_filters( 'no_texturize_tags', $default_no_texturize_tags );
	/**
	 * Filters the list of shortcodes not to texturize.
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
	if ( isset( $text[1] ) && '/' !== $text[1] ) {
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

	// Add a double line break above block-level opening tags.
	$pee = preg_replace('!(<' . $allblocks . '[\s/>])!', "\n\n$1", $pee);

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

	// Collapse line breaks before and after <figcaption> elements.
	if ( strpos( $pee, '<figcaption' ) !== false ) {
		$pee = preg_replace( '|\s*(<figcaption[^>]*>)|', '$1', $pee );
		$pee = preg_replace( '|</figcaption>\s*|', '</figcaption>', $pee );
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
 * @staticvar string $regex
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
 * @staticvar string $html_regex
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

	// Back-compat.
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
 * **Accent characters converted:**
 *
 * Currency signs:
 *
 * |   Code   | Glyph | Replacement |     Description     |
 * | -------- | ----- | ----------- | ------------------- |
 * | U+00A3   | £     | (empty)     | British Pound sign  |
 * | U+20AC   | €     | E           | Euro sign           |
 *
 * Decompositions for Latin-1 Supplement:
 *
 * |  Code   | Glyph | Replacement |               Description              |
 * | ------- | ----- | ----------- | -------------------------------------- |
 * | U+00AA  | ª     | a           | Feminine ordinal indicator             |
 * | U+00BA  | º     | o           | Masculine ordinal indicator            |
 * | U+00C0  | À     | A           | Latin capital letter A with grave      |
 * | U+00C1  | Á     | A           | Latin capital letter A with acute      |
 * | U+00C2  | Â     | A           | Latin capital letter A with circumflex |
 * | U+00C3  | Ã     | A           | Latin capital letter A with tilde      |
 * | U+00C4  | Ä     | A           | Latin capital letter A with diaeresis  |
 * | U+00C5  | Å     | A           | Latin capital letter A with ring above |
 * | U+00C6  | Æ     | AE          | Latin capital letter AE                |
 * | U+00C7  | Ç     | C           | Latin capital letter C with cedilla    |
 * | U+00C8  | È     | E           | Latin capital letter E with grave      |
 * | U+00C9  | É     | E           | Latin capital letter E with acute      |
 * | U+00CA  | Ê     | E           | Latin capital letter E with circumflex |
 * | U+00CB  | Ë     | E           | Latin capital letter E with diaeresis  |
 * | U+00CC  | Ì     | I           | Latin capital letter I with grave      |
 * | U+00CD  | Í     | I           | Latin capital letter I with acute      |
 * | U+00CE  | Î     | I           | Latin capital letter I with circumflex |
 * | U+00CF  | Ï     | I           | Latin capital letter I with diaeresis  |
 * | U+00D0  | Ð     | D           | Latin capital letter Eth               |
 * | U+00D1  | Ñ     | N           | Latin capital letter N with tilde      |
 * | U+00D2  | Ò     | O           | Latin capital letter O with grave      |
 * | U+00D3  | Ó     | O           | Latin capital letter O with acute      |
 * | U+00D4  | Ô     | O           | Latin capital letter O with circumflex |
 * | U+00D5  | Õ     | O           | Latin capital letter O with tilde      |
 * | U+00D6  | Ö     | O           | Latin capital letter O with diaeresis  |
 * | U+00D8  | Ø     | O           | Latin capital letter O with stroke     |
 * | U+00D9  | Ù     | U           | Latin capital letter U with grave      |
 * | U+00DA  | Ú     | U           | Latin capital letter U with acute      |
 * | U+00DB  | Û     | U           | Latin capital letter U with circumflex |
 * | U+00DC  | Ü     | U           | Latin capital letter U with diaeresis  |
 * | U+00DD  | Ý     | Y           | Latin capital letter Y with acute      |
 * | U+00DE  | Þ     | TH          | Latin capital letter Thorn             |
 * | U+00DF  | ß     | s           | Latin small letter sharp s             |
 * | U+00E0  | à     | a           | Latin small letter a with grave        |
 * | U+00E1  | á     | a           | Latin small letter a with acute        |
 * | U+00E2  | â     | a           | Latin small letter a with circumflex   |
 * | U+00E3  | ã     | a           | Latin small letter a with tilde        |
 * | U+00E4  | ä     | a           | Latin small letter a with diaeresis    |
 * | U+00E5  | å     | a           | Latin small letter a with ring above   |
 * | U+00E6  | æ     | ae          | Latin small letter ae                  |
 * | U+00E7  | ç     | c           | Latin small letter c with cedilla      |
 * | U+00E8  | è     | e           | Latin small letter e with grave        |
 * | U+00E9  | é     | e           | Latin small letter e with acute        |
 * | U+00EA  | ê     | e           | Latin small letter e with circumflex   |
 * | U+00EB  | ë     | e           | Latin small letter e with diaeresis    |
 * | U+00EC  | ì     | i           | Latin small letter i with grave        |
 * | U+00ED  | í     | i           | Latin small letter i with acute        |
 * | U+00EE  | î     | i           | Latin small letter i with circumflex   |
 * | U+00EF  | ï     | i           | Latin small letter i with diaeresis    |
 * | U+00F0  | ð     | d           | Latin small letter Eth                 |
 * | U+00F1  | ñ     | n           | Latin small letter n with tilde        |
 * | U+00F2  | ò     | o           | Latin small letter o with grave        |
 * | U+00F3  | ó     | o           | Latin small letter o with acute        |
 * | U+00F4  | ô     | o           | Latin small letter o with circumflex   |
 * | U+00F5  | õ     | o           | Latin small letter o with tilde        |
 * | U+00F6  | ö     | o           | Latin small letter o with diaeresis    |
 * | U+00F8  | ø     | o           | Latin small letter o with stroke       |
 * | U+00F9  | ù     | u           | Latin small letter u with grave        |
 * | U+00FA  | ú     | u           | Latin small letter u with acute        |
 * | U+00FB  | û     | u           | Latin small letter u with circumflex   |
 * | U+00FC  | ü     | u           | Latin small letter u with diaeresis    |
 * | U+00FD  | ý     | y           | Latin small letter y with acute        |
 * | U+00FE  | þ     | th          | Latin small letter Thorn               |
 * | U+00FF  | ÿ     | y           | Latin small letter y with diaeresis    |
 *
 * Decompositions for Latin Extended-A:
 *
 * |  Code   | Glyph | Replacement |                    Description                    |
 * | ------- | ----- | ----------- | ------------------------------------------------- |
 * | U+0100  | Ā     | A           | Latin capital letter A with macron                |
 * | U+0101  | ā     | a           | Latin small letter a with macron                  |
 * | U+0102  | Ă     | A           | Latin capital letter A with breve                 |
 * | U+0103  | ă     | a           | Latin small letter a with breve                   |
 * | U+0104  | Ą     | A           | Latin capital letter A with ogonek                |
 * | U+0105  | ą     | a           | Latin small letter a with ogonek                  |
 * | U+01006 | Ć     | C           | Latin capital letter C with acute                 |
 * | U+0107  | ć     | c           | Latin small letter c with acute                   |
 * | U+0108  | Ĉ     | C           | Latin capital letter C with circumflex            |
 * | U+0109  | ĉ     | c           | Latin small letter c with circumflex              |
 * | U+010A  | Ċ     | C           | Latin capital letter C with dot above             |
 * | U+010B  | ċ     | c           | Latin small letter c with dot above               |
 * | U+010C  | Č     | C           | Latin capital letter C with caron                 |
 * | U+010D  | č     | c           | Latin small letter c with caron                   |
 * | U+010E  | Ď     | D           | Latin capital letter D with caron                 |
 * | U+010F  | ď     | d           | Latin small letter d with caron                   |
 * | U+0110  | Đ     | D           | Latin capital letter D with stroke                |
 * | U+0111  | đ     | d           | Latin small letter d with stroke                  |
 * | U+0112  | Ē     | E           | Latin capital letter E with macron                |
 * | U+0113  | ē     | e           | Latin small letter e with macron                  |
 * | U+0114  | Ĕ     | E           | Latin capital letter E with breve                 |
 * | U+0115  | ĕ     | e           | Latin small letter e with breve                   |
 * | U+0116  | Ė     | E           | Latin capital letter E with dot above             |
 * | U+0117  | ė     | e           | Latin small letter e with dot above               |
 * | U+0118  | Ę     | E           | Latin capital letter E with ogonek                |
 * | U+0119  | ę     | e           | Latin small letter e with ogonek                  |
 * | U+011A  | Ě     | E           | Latin capital letter E with caron                 |
 * | U+011B  | ě     | e           | Latin small letter e with caron                   |
 * | U+011C  | Ĝ     | G           | Latin capital letter G with circumflex            |
 * | U+011D  | ĝ     | g           | Latin small letter g with circumflex              |
 * | U+011E  | Ğ     | G           | Latin capital letter G with breve                 |
 * | U+011F  | ğ     | g           | Latin small letter g with breve                   |
 * | U+0120  | Ġ     | G           | Latin capital letter G with dot above             |
 * | U+0121  | ġ     | g           | Latin small letter g with dot above               |
 * | U+0122  | Ģ     | G           | Latin capital letter G with cedilla               |
 * | U+0123  | ģ     | g           | Latin small letter g with cedilla                 |
 * | U+0124  | Ĥ     | H           | Latin capital letter H with circumflex            |
 * | U+0125  | ĥ     | h           | Latin small letter h with circumflex              |
 * | U+0126  | Ħ     | H           | Latin capital letter H with stroke                |
 * | U+0127  | ħ     | h           | Latin small letter h with stroke                  |
 * | U+0128  | Ĩ     | I           | Latin capital letter I with tilde                 |
 * | U+0129  | ĩ     | i           | Latin small letter i with tilde                   |
 * | U+012A  | Ī     | I           | Latin capital letter I with macron                |
 * | U+012B  | ī     | i           | Latin small letter i with macron                  |
 * | U+012C  | Ĭ     | I           | Latin capital letter I with breve                 |
 * | U+012D  | ĭ     | i           | Latin small letter i with breve                   |
 * | U+012E  | Į     | I           | Latin capital letter I with ogonek                |
 * | U+012F  | į     | i           | Latin small letter i with ogonek                  |
 * | U+0130  | İ     | I           | Latin capital letter I with dot above             |
 * | U+0131  | ı     | i           | Latin small letter dotless i                      |
 * | U+0132  | Ĳ     | IJ          | Latin capital ligature IJ                         |
 * | U+0133  | ĳ     | ij          | Latin small ligature ij                           |
 * | U+0134  | Ĵ     | J           | Latin capital letter J with circumflex            |
 * | U+0135  | ĵ     | j           | Latin small letter j with circumflex              |
 * | U+0136  | Ķ     | K           | Latin capital letter K with cedilla               |
 * | U+0137  | ķ     | k           | Latin small letter k with cedilla                 |
 * | U+0138  | ĸ     | k           | Latin small letter Kra                            |
 * | U+0139  | Ĺ     | L           | Latin capital letter L with acute                 |
 * | U+013A  | ĺ     | l           | Latin small letter l with acute                   |
 * | U+013B  | Ļ     | L           | Latin capital letter L with cedilla               |
 * | U+013C  | ļ     | l           | Latin small letter l with cedilla                 |
 * | U+013D  | Ľ     | L           | Latin capital letter L with caron                 |
 * | U+013E  | ľ     | l           | Latin small letter l with caron                   |
 * | U+013F  | Ŀ     | L           | Latin capital letter L with middle dot            |
 * | U+0140  | ŀ     | l           | Latin small letter l with middle dot              |
 * | U+0141  | Ł     | L           | Latin capital letter L with stroke                |
 * | U+0142  | ł     | l           | Latin small letter l with stroke                  |
 * | U+0143  | Ń     | N           | Latin capital letter N with acute                 |
 * | U+0144  | ń     | n           | Latin small letter N with acute                   |
 * | U+0145  | Ņ     | N           | Latin capital letter N with cedilla               |
 * | U+0146  | ņ     | n           | Latin small letter n with cedilla                 |
 * | U+0147  | Ň     | N           | Latin capital letter N with caron                 |
 * | U+0148  | ň     | n           | Latin small letter n with caron                   |
 * | U+0149  | ŉ     | n           | Latin small letter n preceded by apostrophe       |
 * | U+014A  | Ŋ     | N           | Latin capital letter Eng                          |
 * | U+014B  | ŋ     | n           | Latin small letter Eng                            |
 * | U+014C  | Ō     | O           | Latin capital letter O with macron                |
 * | U+014D  | ō     | o           | Latin small letter o with macron                  |
 * | U+014E  | Ŏ     | O           | Latin capital letter O with breve                 |
 * | U+014F  | ŏ     | o           | Latin small letter o with breve                   |
 * | U+0150  | Ő     | O           | Latin capital letter O with double acute          |
 * | U+0151  | ő     | o           | Latin small letter o with double acute            |
 * | U+0152  | Œ     | OE          | Latin capital ligature OE                         |
 * | U+0153  | œ     | oe          | Latin small ligature oe                           |
 * | U+0154  | Ŕ     | R           | Latin capital letter R with acute                 |
 * | U+0155  | ŕ     | r           | Latin small letter r with acute                   |
 * | U+0156  | Ŗ     | R           | Latin capital letter R with cedilla               |
 * | U+0157  | ŗ     | r           | Latin small letter r with cedilla                 |
 * | U+0158  | Ř     | R           | Latin capital letter R with caron                 |
 * | U+0159  | ř     | r           | Latin small letter r with caron                   |
 * | U+015A  | Ś     | S           | Latin capital letter S with acute                 |
 * | U+015B  | ś     | s           | Latin small letter s with acute                   |
 * | U+015C  | Ŝ     | S           | Latin capital letter S with circumflex            |
 * | U+015D  | ŝ     | s           | Latin small letter s with circumflex              |
 * | U+015E  | Ş     | S           | Latin capital letter S with cedilla               |
 * | U+015F  | ş     | s           | Latin small letter s with cedilla                 |
 * | U+0160  | Š     | S           | Latin capital letter S with caron                 |
 * | U+0161  | š     | s           | Latin small letter s with caron                   |
 * | U+0162  | Ţ     | T           | Latin capital letter T with cedilla               |
 * | U+0163  | ţ     | t           | Latin small letter t with cedilla                 |
 * | U+0164  | Ť     | T           | Latin capital letter T with caron                 |
 * | U+0165  | ť     | t           | Latin small letter t with caron                   |
 * | U+0166  | Ŧ     | T           | Latin capital letter T with stroke                |
 * | U+0167  | ŧ     | t           | Latin small letter t with stroke                  |
 * | U+0168  | Ũ     | U           | Latin capital letter U with tilde                 |
 * | U+0169  | ũ     | u           | Latin small letter u with tilde                   |
 * | U+016A  | Ū     | U           | Latin capital letter U with macron                |
 * | U+016B  | ū     | u           | Latin small letter u with macron                  |
 * | U+016C  | Ŭ     | U           | Latin capital letter U with breve                 |
 * | U+016D  | ŭ     | u           | Latin small letter u with breve                   |
 * | U+016E  | Ů     | U           | Latin capital letter U with ring above            |
 * | U+016F  | ů     | u           | Latin small letter u with ring above              |
 * | U+0170  | Ű     | U           | Latin capital letter U with double acute          |
 * | U+0171  | ű     | u           | Latin small letter u with double acute            |
 * | U+0172  | Ų     | U           | Latin capital letter U with ogonek                |
 * | U+0173  | ų     | u           | Latin small letter u with ogonek                  |
 * | U+0174  | Ŵ     | W           | Latin capital letter W with circumflex            |
 * | U+0175  | ŵ     | w           | Latin small letter w with circumflex              |
 * | U+0176  | Ŷ     | Y           | Latin capital letter Y with circumflex            |
 * | U+0177  | ŷ     | y           | Latin small letter y with circumflex              |
 * | U+0178  | Ÿ     | Y           | Latin capital letter Y with diaeresis             |
 * | U+0179  | Ź     | Z           | Latin capital letter Z with acute                 |
 * | U+017A  | ź     | z           | Latin small letter z with acute                   |
 * | U+017B  | Ż     | Z           | Latin capital letter Z with dot above             |
 * | U+017C  | ż     | z           | Latin small letter z with dot above               |
 * | U+017D  | Ž     | Z           | Latin capital letter Z with caron                 |
 * | U+017E  | ž     | z           | Latin small letter z with caron                   |
 * | U+017F  | ſ     | s           | Latin small letter long s                         |
 * | U+01A0  | Ơ     | O           | Latin capital letter O with horn                  |
 * | U+01A1  | ơ     | o           | Latin small letter o with horn                    |
 * | U+01AF  | Ư     | U           | Latin capital letter U with horn                  |
 * | U+01B0  | ư     | u           | Latin small letter u with horn                    |
 * | U+01CD  | Ǎ     | A           | Latin capital letter A with caron                 |
 * | U+01CE  | ǎ     | a           | Latin small letter a with caron                   |
 * | U+01CF  | Ǐ     | I           | Latin capital letter I with caron                 |
 * | U+01D0  | ǐ     | i           | Latin small letter i with caron                   |
 * | U+01D1  | Ǒ     | O           | Latin capital letter O with caron                 |
 * | U+01D2  | ǒ     | o           | Latin small letter o with caron                   |
 * | U+01D3  | Ǔ     | U           | Latin capital letter U with caron                 |
 * | U+01D4  | ǔ     | u           | Latin small letter u with caron                   |
 * | U+01D5  | Ǖ     | U           | Latin capital letter U with diaeresis and macron  |
 * | U+01D6  | ǖ     | u           | Latin small letter u with diaeresis and macron    |
 * | U+01D7  | Ǘ     | U           | Latin capital letter U with diaeresis and acute   |
 * | U+01D8  | ǘ     | u           | Latin small letter u with diaeresis and acute     |
 * | U+01D9  | Ǚ     | U           | Latin capital letter U with diaeresis and caron   |
 * | U+01DA  | ǚ     | u           | Latin small letter u with diaeresis and caron     |
 * | U+01DB  | Ǜ     | U           | Latin capital letter U with diaeresis and grave   |
 * | U+01DC  | ǜ     | u           | Latin small letter u with diaeresis and grave     |
 *
 * Decompositions for Latin Extended-B:
 *
 * |   Code   | Glyph | Replacement |                Description                |
 * | -------- | ----- | ----------- | ----------------------------------------- |
 * | U+0218   | Ș     | S           | Latin capital letter S with comma below   |
 * | U+0219   | ș     | s           | Latin small letter s with comma below     |
 * | U+021A   | Ț     | T           | Latin capital letter T with comma below   |
 * | U+021B   | ț     | t           | Latin small letter t with comma below     |
 *
 * Vowels with diacritic (Chinese, Hanyu Pinyin):
 *
 * |   Code   | Glyph | Replacement |                      Description                      |
 * | -------- | ----- | ----------- | ----------------------------------------------------- |
 * | U+0251   | ɑ     | a           | Latin small letter alpha                              |
 * | U+1EA0   | Ạ     | A           | Latin capital letter A with dot below                 |
 * | U+1EA1   | ạ     | a           | Latin small letter a with dot below                   |
 * | U+1EA2   | Ả     | A           | Latin capital letter A with hook above                |
 * | U+1EA3   | ả     | a           | Latin small letter a with hook above                  |
 * | U+1EA4   | Ấ     | A           | Latin capital letter A with circumflex and acute      |
 * | U+1EA5   | ấ     | a           | Latin small letter a with circumflex and acute        |
 * | U+1EA6   | Ầ     | A           | Latin capital letter A with circumflex and grave      |
 * | U+1EA7   | ầ     | a           | Latin small letter a with circumflex and grave        |
 * | U+1EA8   | Ẩ     | A           | Latin capital letter A with circumflex and hook above |
 * | U+1EA9   | ẩ     | a           | Latin small letter a with circumflex and hook above   |
 * | U+1EAA   | Ẫ     | A           | Latin capital letter A with circumflex and tilde      |
 * | U+1EAB   | ẫ     | a           | Latin small letter a with circumflex and tilde        |
 * | U+1EA6   | Ậ     | A           | Latin capital letter A with circumflex and dot below  |
 * | U+1EAD   | ậ     | a           | Latin small letter a with circumflex and dot below    |
 * | U+1EAE   | Ắ     | A           | Latin capital letter A with breve and acute           |
 * | U+1EAF   | ắ     | a           | Latin small letter a with breve and acute             |
 * | U+1EB0   | Ằ     | A           | Latin capital letter A with breve and grave           |
 * | U+1EB1   | ằ     | a           | Latin small letter a with breve and grave             |
 * | U+1EB2   | Ẳ     | A           | Latin capital letter A with breve and hook above      |
 * | U+1EB3   | ẳ     | a           | Latin small letter a with breve and hook above        |
 * | U+1EB4   | Ẵ     | A           | Latin capital letter A with breve and tilde           |
 * | U+1EB5   | ẵ     | a           | Latin small letter a with breve and tilde             |
 * | U+1EB6   | Ặ     | A           | Latin capital letter A with breve and dot below       |
 * | U+1EB7   | ặ     | a           | Latin small letter a with breve and dot below         |
 * | U+1EB8   | Ẹ     | E           | Latin capital letter E with dot below                 |
 * | U+1EB9   | ẹ     | e           | Latin small letter e with dot below                   |
 * | U+1EBA   | Ẻ     | E           | Latin capital letter E with hook above                |
 * | U+1EBB   | ẻ     | e           | Latin small letter e with hook above                  |
 * | U+1EBC   | Ẽ     | E           | Latin capital letter E with tilde                     |
 * | U+1EBD   | ẽ     | e           | Latin small letter e with tilde                       |
 * | U+1EBE   | Ế     | E           | Latin capital letter E with circumflex and acute      |
 * | U+1EBF   | ế     | e           | Latin small letter e with circumflex and acute        |
 * | U+1EC0   | Ề     | E           | Latin capital letter E with circumflex and grave      |
 * | U+1EC1   | ề     | e           | Latin small letter e with circumflex and grave        |
 * | U+1EC2   | Ể     | E           | Latin capital letter E with circumflex and hook above |
 * | U+1EC3   | ể     | e           | Latin small letter e with circumflex and hook above   |
 * | U+1EC4   | Ễ     | E           | Latin capital letter E with circumflex and tilde      |
 * | U+1EC5   | ễ     | e           | Latin small letter e with circumflex and tilde        |
 * | U+1EC6   | Ệ     | E           | Latin capital letter E with circumflex and dot below  |
 * | U+1EC7   | ệ     | e           | Latin small letter e with circumflex and dot below    |
 * | U+1EC8   | Ỉ     | I           | Latin capital letter I with hook above                |
 * | U+1EC9   | ỉ     | i           | Latin small letter i with hook above                  |
 * | U+1ECA   | Ị     | I           | Latin capital letter I with dot below                 |
 * | U+1ECB   | ị     | i           | Latin small letter i with dot below                   |
 * | U+1ECC   | Ọ     | O           | Latin capital letter O with dot below                 |
 * | U+1ECD   | ọ     | o           | Latin small letter o with dot below                   |
 * | U+1ECE   | Ỏ     | O           | Latin capital letter O with hook above                |
 * | U+1ECF   | ỏ     | o           | Latin small letter o with hook above                  |
 * | U+1ED0   | Ố     | O           | Latin capital letter O with circumflex and acute      |
 * | U+1ED1   | ố     | o           | Latin small letter o with circumflex and acute        |
 * | U+1ED2   | Ồ     | O           | Latin capital letter O with circumflex and grave      |
 * | U+1ED3   | ồ     | o           | Latin small letter o with circumflex and grave        |
 * | U+1ED4   | Ổ     | O           | Latin capital letter O with circumflex and hook above |
 * | U+1ED5   | ổ     | o           | Latin small letter o with circumflex and hook above   |
 * | U+1ED6   | Ỗ     | O           | Latin capital letter O with circumflex and tilde      |
 * | U+1ED7   | ỗ     | o           | Latin small letter o with circumflex and tilde        |
 * | U+1ED8   | Ộ     | O           | Latin capital letter O with circumflex and dot below  |
 * | U+1ED9   | ộ     | o           | Latin small letter o with circumflex and dot below    |
 * | U+1EDA   | Ớ     | O           | Latin capital letter O with horn and acute            |
 * | U+1EDB   | ớ     | o           | Latin small letter o with horn and acute              |
 * | U+1EDC   | Ờ     | O           | Latin capital letter O with horn and grave            |
 * | U+1EDD   | ờ     | o           | Latin small letter o with horn and grave              |
 * | U+1EDE   | Ở     | O           | Latin capital letter O with horn and hook above       |
 * | U+1EDF   | ở     | o           | Latin small letter o with horn and hook above         |
 * | U+1EE0   | Ỡ     | O           | Latin capital letter O with horn and tilde            |
 * | U+1EE1   | ỡ     | o           | Latin small letter o with horn and tilde              |
 * | U+1EE2   | Ợ     | O           | Latin capital letter O with horn and dot below        |
 * | U+1EE3   | ợ     | o           | Latin small letter o with horn and dot below          |
 * | U+1EE4   | Ụ     | U           | Latin capital letter U with dot below                 |
 * | U+1EE5   | ụ     | u           | Latin small letter u with dot below                   |
 * | U+1EE6   | Ủ     | U           | Latin capital letter U with hook above                |
 * | U+1EE7   | ủ     | u           | Latin small letter u with hook above                  |
 * | U+1EE8   | Ứ     | U           | Latin capital letter U with horn and acute            |
 * | U+1EE9   | ứ     | u           | Latin small letter u with horn and acute              |
 * | U+1EEA   | Ừ     | U           | Latin capital letter U with horn and grave            |
 * | U+1EEB   | ừ     | u           | Latin small letter u with horn and grave              |
 * | U+1EEC   | Ử     | U           | Latin capital letter U with horn and hook above       |
 * | U+1EED   | ử     | u           | Latin small letter u with horn and hook above         |
 * | U+1EEE   | Ữ     | U           | Latin capital letter U with horn and tilde            |
 * | U+1EEF   | ữ     | u           | Latin small letter u with horn and tilde              |
 * | U+1EF0   | Ự     | U           | Latin capital letter U with horn and dot below        |
 * | U+1EF1   | ự     | u           | Latin small letter u with horn and dot below          |
 * | U+1EF2   | Ỳ     | Y           | Latin capital letter Y with grave                     |
 * | U+1EF3   | ỳ     | y           | Latin small letter y with grave                       |
 * | U+1EF4   | Ỵ     | Y           | Latin capital letter Y with dot below                 |
 * | U+1EF5   | ỵ     | y           | Latin small letter y with dot below                   |
 * | U+1EF6   | Ỷ     | Y           | Latin capital letter Y with hook above                |
 * | U+1EF7   | ỷ     | y           | Latin small letter y with hook above                  |
 * | U+1EF8   | Ỹ     | Y           | Latin capital letter Y with tilde                     |
 * | U+1EF9   | ỹ     | y           | Latin small letter y with tilde                       |
 *
 * German (`de_DE`), German formal (`de_DE_formal`), German (Switzerland) formal (`de_CH`),
 * and German (Switzerland) informal (`de_CH_informal`) locales:
 *
 * |   Code   | Glyph | Replacement |               Description               |
 * | -------- | ----- | ----------- | --------------------------------------- |
 * | U+00C4   | Ä     | Ae          | Latin capital letter A with diaeresis   |
 * | U+00E4   | ä     | ae          | Latin small letter a with diaeresis     |
 * | U+00D6   | Ö     | Oe          | Latin capital letter O with diaeresis   |
 * | U+00F6   | ö     | oe          | Latin small letter o with diaeresis     |
 * | U+00DC   | Ü     | Ue          | Latin capital letter U with diaeresis   |
 * | U+00FC   | ü     | ue          | Latin small letter u with diaeresis     |
 * | U+00DF   | ß     | ss          | Latin small letter sharp s              |
 *
 * Danish (`da_DK`) locale:
 *
 * |   Code   | Glyph | Replacement |               Description               |
 * | -------- | ----- | ----------- | --------------------------------------- |
 * | U+00C6   | Æ     | Ae          | Latin capital letter AE                 |
 * | U+00E6   | æ     | ae          | Latin small letter ae                   |
 * | U+00D8   | Ø     | Oe          | Latin capital letter O with stroke      |
 * | U+00F8   | ø     | oe          | Latin small letter o with stroke        |
 * | U+00C5   | Å     | Aa          | Latin capital letter A with ring above  |
 * | U+00E5   | å     | aa          | Latin small letter a with ring above    |
 *
 * Catalan (`ca`) locale:
 *
 * |   Code   | Glyph | Replacement |               Description               |
 * | -------- | ----- | ----------- | --------------------------------------- |
 * | U+00B7   | l·l   | ll          | Flown dot (between two Ls)              |
 *
 * Serbian (`sr_RS`) and Bosnian (`bs_BA`) locales:
 *
 * |   Code   | Glyph | Replacement |               Description               |
 * | -------- | ----- | ----------- | --------------------------------------- |
 * | U+0110   | Đ     | DJ          | Latin capital letter D with stroke      |
 * | U+0111   | đ     | dj          | Latin small letter d with stroke        |
 *
 * @since 1.2.1
 * @since 4.6.0 Added locale support for `de_CH`, `de_CH_informal`, and `ca`.
 * @since 4.7.0 Added locale support for `sr_RS`.
 * @since 4.8.0 Added locale support for `bs_BA`.
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
		'ª' => 'a', 'º' => 'o',
		'À' => 'A', 'Á' => 'A',
		'Â' => 'A', 'Ã' => 'A',
		'Ä' => 'A', 'Å' => 'A',
		'Æ' => 'AE','Ç' => 'C',
		'È' => 'E', 'É' => 'E',
		'Ê' => 'E', 'Ë' => 'E',
		'Ì' => 'I', 'Í' => 'I',
		'Î' => 'I', 'Ï' => 'I',
		'Ð' => 'D', 'Ñ' => 'N',
		'Ò' => 'O', 'Ó' => 'O',
		'Ô' => 'O', 'Õ' => 'O',
		'Ö' => 'O', 'Ù' => 'U',
		'Ú' => 'U', 'Û' => 'U',
		'Ü' => 'U', 'Ý' => 'Y',
		'Þ' => 'TH','ß' => 's',
		'à' => 'a', 'á' => 'a',
		'â' => 'a', 'ã' => 'a',
		'ä' => 'a', 'å' => 'a',
		'æ' => 'ae','ç' => 'c',
		'è' => 'e', 'é' => 'e',
		'ê' => 'e', 'ë' => 'e',
		'ì' => 'i', 'í' => 'i',
		'î' => 'i', 'ï' => 'i',
		'ð' => 'd', 'ñ' => 'n',
		'ò' => 'o', 'ó' => 'o',
		'ô' => 'o', 'õ' => 'o',
		'ö' => 'o', 'ø' => 'o',
		'ù' => 'u', 'ú' => 'u',
		'û' => 'u', 'ü' => 'u',
		'ý' => 'y', 'þ' => 'th',
		'ÿ' => 'y', 'Ø' => 'O',
		// Decompositions for Latin Extended-A
		'Ā' => 'A', 'ā' => 'a',
		'Ă' => 'A', 'ă' => 'a',
		'Ą' => 'A', 'ą' => 'a',
		'Ć' => 'C', 'ć' => 'c',
		'Ĉ' => 'C', 'ĉ' => 'c',
		'Ċ' => 'C', 'ċ' => 'c',
		'Č' => 'C', 'č' => 'c',
		'Ď' => 'D', 'ď' => 'd',
		'Đ' => 'D', 'đ' => 'd',
		'Ē' => 'E', 'ē' => 'e',
		'Ĕ' => 'E', 'ĕ' => 'e',
		'Ė' => 'E', 'ė' => 'e',
		'Ę' => 'E', 'ę' => 'e',
		'Ě' => 'E', 'ě' => 'e',
		'Ĝ' => 'G', 'ĝ' => 'g',
		'Ğ' => 'G', 'ğ' => 'g',
		'Ġ' => 'G', 'ġ' => 'g',
		'Ģ' => 'G', 'ģ' => 'g',
		'Ĥ' => 'H', 'ĥ' => 'h',
		'Ħ' => 'H', 'ħ' => 'h',
		'Ĩ' => 'I', 'ĩ' => 'i',
		'Ī' => 'I', 'ī' => 'i',
		'Ĭ' => 'I', 'ĭ' => 'i',
		'Į' => 'I', 'į' => 'i',
		'İ' => 'I', 'ı' => 'i',
		'Ĳ' => 'IJ','ĳ' => 'ij',
		'Ĵ' => 'J', 'ĵ' => 'j',
		'Ķ' => 'K', 'ķ' => 'k',
		'ĸ' => 'k', 'Ĺ' => 'L',
		'ĺ' => 'l', 'Ļ' => 'L',
		'ļ' => 'l', 'Ľ' => 'L',
		'ľ' => 'l', 'Ŀ' => 'L',
		'ŀ' => 'l', 'Ł' => 'L',
		'ł' => 'l', 'Ń' => 'N',
		'ń' => 'n', 'Ņ' => 'N',
		'ņ' => 'n', 'Ň' => 'N',
		'ň' => 'n', 'ŉ' => 'n',
		'Ŋ' => 'N', 'ŋ' => 'n',
		'Ō' => 'O', 'ō' => 'o',
		'Ŏ' => 'O', 'ŏ' => 'o',
		'Ő' => 'O', 'ő' => 'o',
		'Œ' => 'OE','œ' => 'oe',
		'Ŕ' => 'R','ŕ' => 'r',
		'Ŗ' => 'R','ŗ' => 'r',
		'Ř' => 'R','ř' => 'r',
		'Ś' => 'S','ś' => 's',
		'Ŝ' => 'S','ŝ' => 's',
		'Ş' => 'S','ş' => 's',
		'Š' => 'S', 'š' => 's',
		'Ţ' => 'T', 'ţ' => 't',
		'Ť' => 'T', 'ť' => 't',
		'Ŧ' => 'T', 'ŧ' => 't',
		'Ũ' => 'U', 'ũ' => 'u',
		'Ū' => 'U', 'ū' => 'u',
		'Ŭ' => 'U', 'ŭ' => 'u',
		'Ů' => 'U', 'ů' => 'u',
		'Ű' => 'U', 'ű' => 'u',
		'Ų' => 'U', 'ų' => 'u',
		'Ŵ' => 'W', 'ŵ' => 'w',
		'Ŷ' => 'Y', 'ŷ' => 'y',
		'Ÿ' => 'Y', 'Ź' => 'Z',
		'ź' => 'z', 'Ż' => 'Z',
		'ż' => 'z', 'Ž' => 'Z',
		'ž' => 'z', 'ſ' => 's',
		// Decompositions for Latin Extended-B
		'Ș' => 'S', 'ș' => 's',
		'Ț' => 'T', 'ț' => 't',
		// Euro Sign
		'€' => 'E',
		// GBP (Pound) Sign
		'£' => '',
		// Vowels with diacritic (Vietnamese)
		// unmarked
		'Ơ' => 'O', 'ơ' => 'o',
		'Ư' => 'U', 'ư' => 'u',
		// grave accent
		'Ầ' => 'A', 'ầ' => 'a',
		'Ằ' => 'A', 'ằ' => 'a',
		'Ề' => 'E', 'ề' => 'e',
		'Ồ' => 'O', 'ồ' => 'o',
		'Ờ' => 'O', 'ờ' => 'o',
		'Ừ' => 'U', 'ừ' => 'u',
		'Ỳ' => 'Y', 'ỳ' => 'y',
		// hook
		'Ả' => 'A', 'ả' => 'a',
		'Ẩ' => 'A', 'ẩ' => 'a',
		'Ẳ' => 'A', 'ẳ' => 'a',
		'Ẻ' => 'E', 'ẻ' => 'e',
		'Ể' => 'E', 'ể' => 'e',
		'Ỉ' => 'I', 'ỉ' => 'i',
		'Ỏ' => 'O', 'ỏ' => 'o',
		'Ổ' => 'O', 'ổ' => 'o',
		'Ở' => 'O', 'ở' => 'o',
		'Ủ' => 'U', 'ủ' => 'u',
		'Ử' => 'U', 'ử' => 'u',
		'Ỷ' => 'Y', 'ỷ' => 'y',
		// tilde
		'Ẫ' => 'A', 'ẫ' => 'a',
		'Ẵ' => 'A', 'ẵ' => 'a',
		'Ẽ' => 'E', 'ẽ' => 'e',
		'Ễ' => 'E', 'ễ' => 'e',
		'Ỗ' => 'O', 'ỗ' => 'o',
		'Ỡ' => 'O', 'ỡ' => 'o',
		'Ữ' => 'U', 'ữ' => 'u',
		'Ỹ' => 'Y', 'ỹ' => 'y',
		// acute accent
		'Ấ' => 'A', 'ấ' => 'a',
		'Ắ' => 'A', 'ắ' => 'a',
		'Ế' => 'E', 'ế' => 'e',
		'Ố' => 'O', 'ố' => 'o',
		'Ớ' => 'O', 'ớ' => 'o',
		'Ứ' => 'U', 'ứ' => 'u',
		// dot below
		'Ạ' => 'A', 'ạ' => 'a',
		'Ậ' => 'A', 'ậ' => 'a',
		'Ặ' => 'A', 'ặ' => 'a',
		'Ẹ' => 'E', 'ẹ' => 'e',
		'Ệ' => 'E', 'ệ' => 'e',
		'Ị' => 'I', 'ị' => 'i',
		'Ọ' => 'O', 'ọ' => 'o',
		'Ộ' => 'O', 'ộ' => 'o',
		'Ợ' => 'O', 'ợ' => 'o',
		'Ụ' => 'U', 'ụ' => 'u',
		'Ự' => 'U', 'ự' => 'u',
		'Ỵ' => 'Y', 'ỵ' => 'y',
		// Vowels with diacritic (Chinese, Hanyu Pinyin)
		'ɑ' => 'a',
		// macron
		'Ǖ' => 'U', 'ǖ' => 'u',
		// acute accent
		'Ǘ' => 'U', 'ǘ' => 'u',
		// caron
		'Ǎ' => 'A', 'ǎ' => 'a',
		'Ǐ' => 'I', 'ǐ' => 'i',
		'Ǒ' => 'O', 'ǒ' => 'o',
		'Ǔ' => 'U', 'ǔ' => 'u',
		'Ǚ' => 'U', 'ǚ' => 'u',
		// grave accent
		'Ǜ' => 'U', 'ǜ' => 'u',
		);

		// Used for locale-specific rules
		$locale = get_locale();

		if ( 'de_DE' == $locale || 'de_DE_formal' == $locale || 'de_CH' == $locale || 'de_CH_informal' == $locale ) {
			$chars[ 'Ä' ] = 'Ae';
			$chars[ 'ä' ] = 'ae';
			$chars[ 'Ö' ] = 'Oe';
			$chars[ 'ö' ] = 'oe';
			$chars[ 'Ü' ] = 'Ue';
			$chars[ 'ü' ] = 'ue';
			$chars[ 'ß' ] = 'ss';
		} elseif ( 'da_DK' === $locale ) {
			$chars[ 'Æ' ] = 'Ae';
 			$chars[ 'æ' ] = 'ae';
			$chars[ 'Ø' ] = 'Oe';
			$chars[ 'ø' ] = 'oe';
			$chars[ 'Å' ] = 'Aa';
			$chars[ 'å' ] = 'aa';
		} elseif ( 'ca' === $locale ) {
			$chars[ 'l·l' ] = 'll';
		} elseif ( 'sr_RS' === $locale || 'bs_BA' === $locale ) {
			$chars[ 'Đ' ] = 'DJ';
			$chars[ 'đ' ] = 'dj';
		}

		$string = strtr($string, $chars);
	} else {
		$chars = array();
		// Assume ISO-8859-1 if not UTF-8
		$chars['in'] = "\x80\x83\x8a\x8e\x9a\x9e"
			."\x9f\xa2\xa5\xb5\xc0\xc1\xc2"
			."\xc3\xc4\xc5\xc7\xc8\xc9\xca"
			."\xcb\xcc\xcd\xce\xcf\xd1\xd2"
			."\xd3\xd4\xd5\xd6\xd8\xd9\xda"
			."\xdb\xdc\xdd\xe0\xe1\xe2\xe3"
			."\xe4\xe5\xe7\xe8\xe9\xea\xeb"
			."\xec\xed\xee\xef\xf1\xf2\xf3"
			."\xf4\xf5\xf6\xf8\xf9\xfa\xfb"
			."\xfc\xfd\xff";

		$chars['out'] = "EfSZszYcYuAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy";

		$string = strtr($string, $chars['in'], $chars['out']);
		$double_chars = array();
		$double_chars['in'] = array("\x8c", "\x9c", "\xc6", "\xd0", "\xde", "\xdf", "\xe6", "\xf0", "\xfe");
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
 * and end of filename. It is not guaranteed that this function will return a
 * filename that is allowed to be uploaded.
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
	 * Filters the list of characters to remove from a filename.
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

	if ( false === strpos( $filename, '.' ) ) {
		$mime_types = wp_get_mime_types();
		$filetype = wp_check_filetype( 'test.' . $filename, $mime_types );
		if ( $filetype['ext'] === $filename ) {
			$filename = 'unnamed-file.' . $filetype['ext'];
		}
	}

	// Split the filename into a base and extension[s]
	$parts = explode('.', $filename);

	// Return if only one extension
	if ( count( $parts ) <= 2 ) {
		/**
		 * Filters a sanitized filename string.
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
 * parameters for the {@see 'sanitize_user'} filter.
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
	 * Filters a sanitized username string.
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
	 * Filters a sanitized key string.
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
	 * Filters a sanitized title string.
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

	if ( 'save' == $context ) {
		// Convert nbsp, ndash and mdash to hyphens
		$title = str_replace( array( '%c2%a0', '%e2%80%93', '%e2%80%94' ), '-', $title );
		// Convert nbsp, ndash and mdash HTML entities to hyphens
		$title = str_replace( array( '&nbsp;', '&#160;', '&ndash;', '&#8211;', '&mdash;', '&#8212;' ), '-', $title );
		// Convert forward slash to hyphen
		$title = str_replace( '/', '-', $title );

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

	$title = preg_replace('/&.+?;/', '', $title); // kill entities
	$title = str_replace('.', '-', $title);

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
	 * Filters a sanitized HTML class string.
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
 * to convert special characters to HTML entities. If `$richedit` is set to true,
 * it is simply a holder for the {@see 'format_to_edit'} filter.
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
	 * Filters the text to be formatted for editing.
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
 * https://secure.php.net/magic_quotes} for more details.
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
 * This function was backported from 2.5.0 to 2.3.2. Regex callback for make_clickable().
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
 * This function was backported from 2.5.0 to 2.3.2. Regex callback for make_clickable().
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
 * This function was backported from 2.5.0 to 2.3.2. Regex callback for make_clickable().
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

	if ( ! empty( $atts['href'] ) ) {
		if ( in_array( strtolower( wp_parse_url( $atts['href'], PHP_URL_SCHEME ) ), array( 'http', 'https' ), true ) ) {
			if ( strtolower( wp_parse_url( $atts['href'], PHP_URL_HOST ) ) === strtolower( wp_parse_url( home_url(), PHP_URL_HOST ) ) ) {
				return "<a $text>";
			}
		}
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
			$html .= "{$name}=\"" . esc_attr( $value ) . "\" ";
		}
		$text = trim( $html );
	}
	return "<a $text rel=\"" . esc_attr( $rel ) . "\">";
}

/**
 * Convert one smiley code to the icon graphic file equivalent.
 *
 * Callback handler for convert_smilies().
 *
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
	 * Filters the Smiley image URL before it's used in the image element.
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
		_deprecated_argument( __FUNCTION__, '3.0.0' );

	// Test for the minimum length the email can be
	if ( strlen( $email ) < 6 ) {
		/**
		 * Filters whether an email address is valid.
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
 * @param string $date_string Date and time in ISO 8601 format {@link https://en.wikipedia.org/wiki/ISO_8601}.
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
	if ( strlen( $email ) < 6 ) {
		/**
		 * Filters a sanitized email address.
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
		/* translators: Time difference between two dates, in minutes (min=minute). 1: Number of minutes */
		$since = sprintf( _n( '%s min', '%s mins', $mins ), $mins );
	} elseif ( $diff < DAY_IN_SECONDS && $diff >= HOUR_IN_SECONDS ) {
		$hours = round( $diff / HOUR_IN_SECONDS );
		if ( $hours <= 1 )
			$hours = 1;
		/* translators: Time difference between two dates, in hours. 1: Number of hours */
		$since = sprintf( _n( '%s hour', '%s hours', $hours ), $hours );
	} elseif ( $diff < WEEK_IN_SECONDS && $diff >= DAY_IN_SECONDS ) {
		$days = round( $diff / DAY_IN_SECONDS );
		if ( $days <= 1 )
			$days = 1;
		/* translators: Time difference between two dates, in days. 1: Number of days */
		$since = sprintf( _n( '%s day', '%s days', $days ), $days );
	} elseif ( $diff < MONTH_IN_SECONDS && $diff >= WEEK_IN_SECONDS ) {
		$weeks = round( $diff / WEEK_IN_SECONDS );
		if ( $weeks <= 1 )
			$weeks = 1;
		/* translators: Time difference between two dates, in weeks. 1: Number of weeks */
		$since = sprintf( _n( '%s week', '%s weeks', $weeks ), $weeks );
	} elseif ( $diff < YEAR_IN_SECONDS && $diff >= MONTH_IN_SECONDS ) {
		$months = round( $diff / MONTH_IN_SECONDS );
		if ( $months <= 1 )
			$months = 1;
		/* translators: Time difference between two dates, in months. 1: Number of months */
		$since = sprintf( _n( '%s month', '%s months', $months ), $months );
	} elseif ( $diff >= YEAR_IN_SECONDS ) {
		$years = round( $diff / YEAR_IN_SECONDS );
		if ( $years <= 1 )
			$years = 1;
		/* translators: Time difference between two dates, in years. 1: Number of years */
		$since = sprintf( _n( '%s year', '%s years', $years ), $years );
	}

	/**
	 * Filters the human readable difference between two timestamps.
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
 * The 55 word limit can be modified by plugins/themes using the {@see 'excerpt_length'} filter
 * The ' [&hellip;]' string can be modified by plugins/themes using the {@see 'excerpt_more'} filter
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
		 * Filters the number of words in an excerpt.
		 *
		 * @since 2.7.0
		 *
		 * @param int $number The number of words. Default 55.
		 */
		$excerpt_length = apply_filters( 'excerpt_length', 55 );
		/**
		 * Filters the string in the "more" link displayed after a trimmed excerpt.
		 *
		 * @since 2.9.0
		 *
		 * @param string $more_string The string shown within the more link.
		 */
		$excerpt_more = apply_filters( 'excerpt_more', ' ' . '[&hellip;]' );
		$text = wp_trim_words( $text, $excerpt_length, $excerpt_more );
	}
	/**
	 * Filters the trimmed excerpt string.
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
	 * Filters the text content after words have been trimmed.
	 *
	 * @since 3.3.0
	 *
	 * @param string $text          The trimmed text.
	 * @param int    $num_words     The number of words to trim the text to. Default 55.
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
	 * Filters text before named entities are converted into numbered entities.
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
	 * Filters the text after it is formatted for the editor.
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
 * NOTE: Since 4.8.3, '%' characters will be replaced with a placeholder string,
 * this prevents certain SQLi attacks from taking place. This change in behaviour
 * may cause issues for code that expects the return value of esc_sql() to be useable
 * for other purposes.
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
 * (the default behaviour) ampersands are also replaced. The {@see 'clean_url'} filter
 * is applied to the returned cleaned URL.
 *
 * @since 2.8.0
 *
 * @param string $url       The URL to be cleaned.
 * @param array  $protocols Optional. An array of acceptable protocols.
 *		                    Defaults to return value of wp_allowed_protocols()
 * @param string $_context  Private. Use esc_url_raw() for database usage.
 * @return string The cleaned $url after the {@see 'clean_url'} filter is applied.
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
	 * Filters a string cleaned and escaped for output as a URL.
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
 * @link https://secure.php.net/htmlentities Borrowed from the PHP Manual user notes.
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
 * be in single quotes. The {@see 'js_escape'} filter is also applied here.
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
	 * Filters a string cleaned and escaped for output in JavaScript.
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
	 * Filters a string cleaned and escaped for output in HTML.
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
	 * Filters a string cleaned and escaped for output in an HTML attribute.
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
	 * Filters a string cleaned and escaped for output in a textarea element.
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
	 * Filters a string cleaned and escaped for output as an HTML tag.
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
			if ( $value !== $original_value ) {
				$value = $wpdb->strip_invalid_text_for_column( $wpdb->options, 'option_value', wp_encode_emoji( $original_value ) );
			}

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

			if ( 'permalink_structure' === $option && '' !== $value && ! preg_match( '/%[^\/%]+%/', $value ) ) {
				$error = sprintf(
					/* translators: %s: Codex URL */
					__( 'A structure tag is required when using custom permalinks. <a href="%s">Learn more</a>' ),
					__( 'https://codex.wordpress.org/Using_Permalinks#Choosing_your_permalink_structure' )
				);
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
	 * Filters an option value following sanitization.
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
 * @return mixed The value with the callback applied to all non-arrays and non-objects inside it.
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
 * Uses {@link https://secure.php.net/parse_str parse_str()} and stripslashes if
 * {@link https://secure.php.net/magic_quotes magic_quotes_gpc} is on.
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
	 * Filters the array of variables derived from a parsed string.
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
 * @link https://secure.php.net/sprintf
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
			 * Filters a fragment from the pattern passed to wp_sprintf().
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
	 * Filters the translated delimiters used by wp_sprintf_l().
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
 * Sanitizes a string from user input or from the database.
 *
 * - Checks for invalid UTF-8,
 * - Converts single `<` characters to entities
 * - Strips all tags
 * - Removes line breaks, tabs, and extra whitespace
 * - Strips octets
 *
 * @since 2.9.0
 *
 * @see sanitize_textarea_field()
 * @see wp_check_invalid_utf8()
 * @see wp_strip_all_tags()
 *
 * @param string $str String to sanitize.
 * @return string Sanitized string.
 */
function sanitize_text_field( $str ) {
	$filtered = _sanitize_text_fields( $str, false );

	/**
	 * Filters a sanitized text field string.
	 *
	 * @since 2.9.0
	 *
	 * @param string $filtered The sanitized string.
	 * @param string $str      The string prior to being sanitized.
	 */
	return apply_filters( 'sanitize_text_field', $filtered, $str );
}

/**
 * Sanitizes a multiline string from user input or from the database.
 *
 * The function is like sanitize_text_field(), but preserves
 * new lines (\n) and other whitespace, which are legitimate
 * input in textarea elements.
 *
 * @see sanitize_text_field()
 *
 * @since 4.7.0
 *
 * @param string $str String to sanitize.
 * @return string Sanitized string.
 */
function sanitize_textarea_field( $str ) {
	$filtered = _sanitize_text_fields( $str, true );

	/**
	 * Filters a sanitized textarea field string.
	 *
	 * @since 4.7.0
	 *
	 * @param string $filtered The sanitized string.
	 * @param string $str      The string prior to being sanitized.
	 */
	return apply_filters( 'sanitize_textarea_field', $filtered, $str );
}

/**
 * Internal helper function to sanitize a string from user input or from the db
 *
 * @since 4.7.0
 * @access private
 *
 * @param string $str String to sanitize.
 * @param bool $keep_newlines optional Whether to keep newlines. Default: false.
 * @return string Sanitized string.
 */
function _sanitize_text_fields( $str, $keep_newlines = false ) {
	$filtered = wp_check_invalid_utf8( $str );

	if ( strpos($filtered, '<') !== false ) {
		$filtered = wp_pre_kses_less_than( $filtered );
		// This will strip extra whitespace for us.
		$filtered = wp_strip_all_tags( $filtered, false );

		// Use html entities in a special case to make sure no later
		// newline stripping stage could lead to a functional tag
		$filtered = str_replace("<\n", "&lt;\n", $filtered);
	}

	if ( ! $keep_newlines ) {
		$filtered = preg_replace( '/[\r\n\t ]+/', ' ', $filtered );
	}
	$filtered = trim( $filtered );

	$found = false;
	while ( preg_match('/%[a-f0-9]{2}/i', $filtered, $match) ) {
		$filtered = str_replace($match[0], '', $filtered);
		$found = true;
	}

	if ( $found ) {
		// Strip out the whitespace that may now exist after removing the octets.
		$filtered = trim( preg_replace('/ +/', ' ', $filtered) );
	}

	return $filtered;
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
	 * Filters a mime type following sanitization.
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
	 * Filters a list of trackback URLs following sanitization.
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
		 * Filters the regexp for common whitespace characters.
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
 * Print the inline Emoji detection script if it is not already printed.
 *
 * @since 4.2.0
 * @staticvar bool $printed
 */
function print_emoji_detection_script() {
	static $printed = false;

	if ( $printed ) {
		return;
	}

	$printed = true;

	_print_emoji_detection_script();
}

/**
 * Prints inline Emoji dection script
 *
 * @ignore
 * @since 4.6.0
 * @access private
 */
function _print_emoji_detection_script() {
	$settings = array(
		/**
		 * Filters the URL where emoji png images are hosted.
		 *
		 * @since 4.2.0
		 *
		 * @param string The emoji base URL for png images.
		 */
		'baseUrl' => apply_filters( 'emoji_url', 'https://s.w.org/images/core/emoji/11/72x72/' ),

		/**
		 * Filters the extension of the emoji png files.
		 *
		 * @since 4.2.0
		 *
		 * @param string The emoji extension for png files. Default .png.
		 */
		'ext' => apply_filters( 'emoji_ext', '.png' ),

		/**
		 * Filters the URL where emoji SVG images are hosted.
		 *
		 * @since 4.6.0
		 *
		 * @param string The emoji base URL for svg images.
		 */
		'svgUrl' => apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/11/svg/' ),

		/**
		 * Filters the extension of the emoji SVG files.
		 *
		 * @since 4.6.0
		 *
		 * @param string The emoji extension for svg files. Default .svg.
		 */
		'svgExt' => apply_filters( 'emoji_svg_ext', '.svg' ),
	);

	$version = 'ver=' . get_bloginfo( 'version' );

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
			!function(a,b,c){function d(a,b){var c=String.fromCharCode;l.clearRect(0,0,k.width,k.height),l.fillText(c.apply(this,a),0,0);var d=k.toDataURL();l.clearRect(0,0,k.width,k.height),l.fillText(c.apply(this,b),0,0);var e=k.toDataURL();return d===e}function e(a){var b;if(!l||!l.fillText)return!1;switch(l.textBaseline="top",l.font="600 32px Arial",a){case"flag":return!(b=d([55356,56826,55356,56819],[55356,56826,8203,55356,56819]))&&(b=d([55356,57332,56128,56423,56128,56418,56128,56421,56128,56430,56128,56423,56128,56447],[55356,57332,8203,56128,56423,8203,56128,56418,8203,56128,56421,8203,56128,56430,8203,56128,56423,8203,56128,56447]),!b);case"emoji":return b=d([55358,56760,9792,65039],[55358,56760,8203,9792,65039]),!b}return!1}function f(a){var c=b.createElement("script");c.src=a,c.defer=c.type="text/javascript",b.getElementsByTagName("head")[0].appendChild(c)}var g,h,i,j,k=b.createElement("canvas"),l=k.getContext&&k.getContext("2d");for(j=Array("flag","emoji"),c.supports={everything:!0,everythingExceptFlag:!0},i=0;i<j.length;i++)c.supports[j[i]]=e(j[i]),c.supports.everything=c.supports.everything&&c.supports[j[i]],"flag"!==j[i]&&(c.supports.everythingExceptFlag=c.supports.everythingExceptFlag&&c.supports[j[i]]);c.supports.everythingExceptFlag=c.supports.everythingExceptFlag&&!c.supports.flag,c.DOMReady=!1,c.readyCallback=function(){c.DOMReady=!0},c.supports.everything||(h=function(){c.readyCallback()},b.addEventListener?(b.addEventListener("DOMContentLoaded",h,!1),a.addEventListener("load",h,!1)):(a.attachEvent("onload",h),b.attachEvent("onreadystatechange",function(){"complete"===b.readyState&&c.readyCallback()})),g=c.source||{},g.concatemoji?f(g.concatemoji):g.wpemoji&&g.twemoji&&(f(g.twemoji),f(g.wpemoji)))}(window,document,window._wpemojiSettings);
		</script>
		<?php
	}
}

/**
 * Convert emoji characters to their equivalent HTML entity.
 *
 * This allows us to store emoji in a DB using the utf8 character set.
 *
 * @since 4.2.0
 *
 * @param string $content The content to encode.
 * @return string The encoded content.
 */
function wp_encode_emoji( $content ) {
	$emoji = _wp_emoji_list( 'partials' );

	foreach ( $emoji as $emojum ) {
		if ( version_compare( phpversion(), '5.4', '<' ) ) {
			$emoji_char = html_entity_decode( $emojum, ENT_COMPAT, 'UTF-8' );
		} else {
			$emoji_char = html_entity_decode( $emojum );
		}
		if ( false !== strpos( $content, $emoji_char ) ) {
			$content = preg_replace( "/$emoji_char/", $emojum, $content );
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
	if ( false === strpos( $text, '&#x' ) ) {
		if ( ( function_exists( 'mb_check_encoding' ) && mb_check_encoding( $text, 'ASCII' ) ) || ! preg_match( '/[^\x00-\x7F]/', $text ) ) {
			// The text doesn't contain anything that might be emoji, so we can return early.
			return $text;
		} else {
			$encoded_text = wp_encode_emoji( $text );
			if ( $encoded_text === $text ) {
				return $encoded_text;
			}

			$text = $encoded_text;
		}
	}

	$emoji = _wp_emoji_list( 'entities' );

	// Quickly narrow down the list of emoji that might be in the text and need replacing.
	$possible_emoji = array();
	foreach( $emoji as $emojum ) {
		if ( false !== strpos( $text, $emojum ) ) {
			if ( version_compare( phpversion(), '5.4', '<' ) ) {
				$possible_emoji[ $emojum ] = html_entity_decode( $emojum, ENT_COMPAT, 'UTF-8' );
			} else {
				$possible_emoji[ $emojum ] = html_entity_decode( $emojum );
			}
		}
	}

	if ( ! $possible_emoji ) {
		return $text;
	}

	/** This filter is documented in wp-includes/formatting.php */
	$cdn_url = apply_filters( 'emoji_url', 'https://s.w.org/images/core/emoji/11/72x72/' );

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
		if ( '' ==  $ignore_block_element && strlen( $content ) > 0 && '<' != $content[0] && false !== strpos( $content, '&#x' ) ) {
			foreach ( $possible_emoji as $emojum => $emoji_char ) {
				if ( false === strpos( $content, $emojum ) ) {
					continue;
				}

				$file = str_replace( ';&#x', '-', $emojum );
				$file = str_replace( array( '&#x', ';'), '', $file );

				$entity = sprintf( '<img src="%s" alt="%s" class="wp-smiley" style="height: 1em; max-height: 1em;" />', $cdn_url . $file . $ext, $emoji_char );

				$content = str_replace( $emojum, $entity, $content );
			}
		}

		// Did we exit ignore block.
		if ( '' != $ignore_block_element && '</' . $ignore_block_element . '>' == $content )  {
			$ignore_block_element = '';
		}

		$output .= $content;
	}

	// Finally, remove any stray U+FE0F characters
	$output = str_replace( '&#xfe0f;', '', $output );

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
 * Returns a arrays of emoji data.
 *
 * These arrays automatically built from the regex in twemoji.js - if they need to be updated,
 * you should update the regex there, then run the `grunt precommit:emoji` job.
 *
 * @since 4.9.0
 * @access private
 *
 * @param string $type Optional. Which array type to return. Accepts 'partials' or 'entities', default 'entities'.
 * @return array An array to match all emoji that WordPress recognises.
 */
function _wp_emoji_list( $type = 'entities' ) {
	// Do not remove the START/END comments - they're used to find where to insert the arrays.

	// START: emoji arrays
	$entities = array('&#x1f469;&#x200d;&#x2764;&#xfe0f;&#x200d;&#x1f48b;&#x200d;&#x1f469;','&#x1f469;&#x200d;&#x2764;&#xfe0f;&#x200d;&#x1f48b;&#x200d;&#x1f468;','&#x1f468;&#x200d;&#x2764;&#xfe0f;&#x200d;&#x1f48b;&#x200d;&#x1f468;','&#x1f3f4;&#xe0067;&#xe0062;&#xe0073;&#xe0063;&#xe0074;&#xe007f;','&#x1f3f4;&#xe0067;&#xe0062;&#xe0077;&#xe006c;&#xe0073;&#xe007f;','&#x1f3f4;&#xe0067;&#xe0062;&#xe0065;&#xe006e;&#xe0067;&#xe007f;','&#x1f469;&#x200d;&#x1f469;&#x200d;&#x1f467;&#x200d;&#x1f466;','&#x1f468;&#x200d;&#x1f468;&#x200d;&#x1f466;&#x200d;&#x1f466;','&#x1f469;&#x200d;&#x1f469;&#x200d;&#x1f466;&#x200d;&#x1f466;','&#x1f468;&#x200d;&#x1f468;&#x200d;&#x1f467;&#x200d;&#x1f466;','&#x1f468;&#x200d;&#x1f468;&#x200d;&#x1f467;&#x200d;&#x1f467;','&#x1f468;&#x200d;&#x1f469;&#x200d;&#x1f467;&#x200d;&#x1f467;','&#x1f468;&#x200d;&#x1f469;&#x200d;&#x1f466;&#x200d;&#x1f466;','&#x1f469;&#x200d;&#x1f469;&#x200d;&#x1f467;&#x200d;&#x1f467;','&#x1f468;&#x200d;&#x1f469;&#x200d;&#x1f467;&#x200d;&#x1f466;','&#x1f468;&#x200d;&#x2764;&#xfe0f;&#x200d;&#x1f468;','&#x1f469;&#x200d;&#x2764;&#xfe0f;&#x200d;&#x1f468;','&#x1f469;&#x200d;&#x2764;&#xfe0f;&#x200d;&#x1f469;','&#x1f469;&#x200d;&#x1f469;&#x200d;&#x1f466;','&#x1f469;&#x200d;&#x1f466;&#x200d;&#x1f466;','&#x1f469;&#x200d;&#x1f467;&#x200d;&#x1f466;','&#x1f469;&#x200d;&#x1f467;&#x200d;&#x1f467;','&#x1f468;&#x200d;&#x1f468;&#x200d;&#x1f466;','&#x1f468;&#x200d;&#x1f469;&#x200d;&#x1f467;','&#x1f468;&#x200d;&#x1f467;&#x200d;&#x1f467;','&#x1f468;&#x200d;&#x1f467;&#x200d;&#x1f466;','&#x1f469;&#x200d;&#x1f469;&#x200d;&#x1f467;','&#x1f468;&#x200d;&#x1f469;&#x200d;&#x1f466;','&#x1f468;&#x200d;&#x1f468;&#x200d;&#x1f467;','&#x1f468;&#x200d;&#x1f466;&#x200d;&#x1f466;','&#x1f935;&#x1f3ff;&#x200d;&#x2640;&#xfe0f;','&#x1f3cb;&#x1f3fb;&#x200d;&#x2642;&#xfe0f;','&#x1f3cb;&#x1f3fc;&#x200d;&#x2640;&#xfe0f;','&#x1f3cb;&#x1f3fc;&#x200d;&#x2642;&#xfe0f;','&#x1f3cb;&#x1f3fd;&#x200d;&#x2640;&#xfe0f;','&#x1f3cb;&#x1f3fd;&#x200d;&#x2642;&#xfe0f;','&#x1f3cb;&#x1f3fe;&#x200d;&#x2640;&#xfe0f;','&#x1f3cb;&#x1f3fe;&#x200d;&#x2642;&#xfe0f;','&#x1f3cb;&#x1f3ff;&#x200d;&#x2640;&#xfe0f;','&#x1f3cb;&#x1f3ff;&#x200d;&#x2642;&#xfe0f;','&#x1f3cc;&#x1f3fb;&#x200d;&#x2640;&#xfe0f;','&#x1f3cc;&#x1f3fb;&#x200d;&#x2642;&#xfe0f;','&#x1f3cc;&#x1f3fc;&#x200d;&#x2640;&#xfe0f;','&#x1f3cc;&#x1f3fc;&#x200d;&#x2642;&#xfe0f;','&#x1f3cc;&#x1f3fd;&#x200d;&#x2640;&#xfe0f;','&#x1f3cc;&#x1f3fd;&#x200d;&#x2642;&#xfe0f;','&#x1f3cc;&#x1f3fe;&#x200d;&#x2640;&#xfe0f;','&#x1f3cc;&#x1f3fe;&#x200d;&#x2642;&#xfe0f;','&#x1f3cc;&#x1f3ff;&#x200d;&#x2640;&#xfe0f;','&#x1f3cc;&#x1f3ff;&#x200d;&#x2642;&#xfe0f;','&#x1f482;&#x1f3fb;&#x200d;&#x2640;&#xfe0f;','&#x1f3c3;&#x1f3fc;&#x200d;&#x2640;&#xfe0f;','&#x1f3c3;&#x1f3fc;&#x200d;&#x2642;&#xfe0f;','&#x1f9dd;&#x1f3ff;&#x200d;&#x2642;&#xfe0f;','&#x1f9dd;&#x1f3ff;&#x200d;&#x2640;&#xfe0f;','&#x1f9dd;&#x1f3fe;&#x200d;&#x2642;&#xfe0f;','&#x1f9dd;&#x1f3fe;&#x200d;&#x2640;&#xfe0f;','&#x1f9dd;&#x1f3fd;&#x200d;&#x2642;&#xfe0f;','&#x1f9dd;&#x1f3fd;&#x200d;&#x2640;&#xfe0f;','&#x1f9dd;&#x1f3fc;&#x200d;&#x2642;&#xfe0f;','&#x1f9dd;&#x1f3fc;&#x200d;&#x2640;&#xfe0f;','&#x1f9dd;&#x1f3fb;&#x200d;&#x2642;&#xfe0f;','&#x1f9dd;&#x1f3fb;&#x200d;&#x2640;&#xfe0f;','&#x1f9dc;&#x1f3ff;&#x200d;&#x2642;&#xfe0f;','&#x1f9dc;&#x1f3ff;&#x200d;&#x2640;&#xfe0f;','&#x1f9dc;&#x1f3fe;&#x200d;&#x2642;&#xfe0f;','&#x1f9dc;&#x1f3fe;&#x200d;&#x2640;&#xfe0f;','&#x1f9dc;&#x1f3fd;&#x200d;&#x2642;&#xfe0f;','&#x1f9dc;&#x1f3fd;&#x200d;&#x2640;&#xfe0f;','&#x1f9dc;&#x1f3fc;&#x200d;&#x2642;&#xfe0f;','&#x1f9dc;&#x1f3fc;&#x200d;&#x2640;&#xfe0f;','&#x1f468;&#x1f3fb;&#x200d;&#x2695;&#xfe0f;','&#x1f468;&#x1f3fb;&#x200d;&#x2696;&#xfe0f;','&#x1f468;&#x1f3fb;&#x200d;&#x2708;&#xfe0f;','&#x1f9dc;&#x1f3fb;&#x200d;&#x2642;&#xfe0f;','&#x1f9dc;&#x1f3fb;&#x200d;&#x2640;&#xfe0f;','&#x1f9db;&#x1f3ff;&#x200d;&#x2642;&#xfe0f;','&#x1f9db;&#x1f3ff;&#x200d;&#x2640;&#xfe0f;','&#x1f9db;&#x1f3fe;&#x200d;&#x2642;&#xfe0f;','&#x1f9db;&#x1f3fe;&#x200d;&#x2640;&#xfe0f;','&#x1f9db;&#x1f3fd;&#x200d;&#x2642;&#xfe0f;','&#x1f9db;&#x1f3fd;&#x200d;&#x2640;&#xfe0f;','&#x1f9db;&#x1f3fc;&#x200d;&#x2642;&#xfe0f;','&#x1f9db;&#x1f3fc;&#x200d;&#x2640;&#xfe0f;','&#x1f9db;&#x1f3fb;&#x200d;&#x2642;&#xfe0f;','&#x1f9db;&#x1f3fb;&#x200d;&#x2640;&#xfe0f;','&#x1f9da;&#x1f3ff;&#x200d;&#x2642;&#xfe0f;','&#x1f9da;&#x1f3ff;&#x200d;&#x2640;&#xfe0f;','&#x1f9da;&#x1f3fe;&#x200d;&#x2642;&#xfe0f;','&#x1f9da;&#x1f3fe;&#x200d;&#x2640;&#xfe0f;','&#x1f9da;&#x1f3fd;&#x200d;&#x2642;&#xfe0f;','&#x1f468;&#x1f3fc;&#x200d;&#x2695;&#xfe0f;','&#x1f468;&#x1f3fc;&#x200d;&#x2696;&#xfe0f;','&#x1f468;&#x1f3fc;&#x200d;&#x2708;&#xfe0f;','&#x1f9da;&#x1f3fd;&#x200d;&#x2640;&#xfe0f;','&#x1f9da;&#x1f3fc;&#x200d;&#x2642;&#xfe0f;','&#x1f9da;&#x1f3fc;&#x200d;&#x2640;&#xfe0f;','&#x1f9da;&#x1f3fb;&#x200d;&#x2642;&#xfe0f;','&#x1f9da;&#x1f3fb;&#x200d;&#x2640;&#xfe0f;','&#x1f9d9;&#x1f3ff;&#x200d;&#x2642;&#xfe0f;','&#x1f9d9;&#x1f3ff;&#x200d;&#x2640;&#xfe0f;','&#x1f9d9;&#x1f3fe;&#x200d;&#x2642;&#xfe0f;','&#x1f9d9;&#x1f3fe;&#x200d;&#x2640;&#xfe0f;','&#x1f9d9;&#x1f3fd;&#x200d;&#x2642;&#xfe0f;','&#x1f9d9;&#x1f3fd;&#x200d;&#x2640;&#xfe0f;','&#x1f9d9;&#x1f3fc;&#x200d;&#x2642;&#xfe0f;','&#x1f9d9;&#x1f3fc;&#x200d;&#x2640;&#xfe0f;','&#x1f9d9;&#x1f3fb;&#x200d;&#x2642;&#xfe0f;','&#x1f9d9;&#x1f3fb;&#x200d;&#x2640;&#xfe0f;','&#x1f9d8;&#x1f3ff;&#x200d;&#x2642;&#xfe0f;','&#x1f9d8;&#x1f3ff;&#x200d;&#x2640;&#xfe0f;','&#x1f468;&#x1f3fd;&#x200d;&#x2695;&#xfe0f;','&#x1f468;&#x1f3fd;&#x200d;&#x2696;&#xfe0f;','&#x1f468;&#x1f3fd;&#x200d;&#x2708;&#xfe0f;','&#x1f9d8;&#x1f3fe;&#x200d;&#x2642;&#xfe0f;','&#x1f9d8;&#x1f3fe;&#x200d;&#x2640;&#xfe0f;','&#x1f9d8;&#x1f3fd;&#x200d;&#x2642;&#xfe0f;','&#x1f9d8;&#x1f3fd;&#x200d;&#x2640;&#xfe0f;','&#x1f9d8;&#x1f3fc;&#x200d;&#x2642;&#xfe0f;','&#x1f9d8;&#x1f3fc;&#x200d;&#x2640;&#xfe0f;','&#x1f9d8;&#x1f3fb;&#x200d;&#x2642;&#xfe0f;','&#x1f9d8;&#x1f3fb;&#x200d;&#x2640;&#xfe0f;','&#x1f9d7;&#x1f3ff;&#x200d;&#x2642;&#xfe0f;','&#x1f9d7;&#x1f3ff;&#x200d;&#x2640;&#xfe0f;','&#x1f9d7;&#x1f3fe;&#x200d;&#x2642;&#xfe0f;','&#x1f9d7;&#x1f3fe;&#x200d;&#x2640;&#xfe0f;','&#x1f9d7;&#x1f3fd;&#x200d;&#x2642;&#xfe0f;','&#x1f9d7;&#x1f3fd;&#x200d;&#x2640;&#xfe0f;','&#x1f9d7;&#x1f3fc;&#x200d;&#x2642;&#xfe0f;','&#x1f9d7;&#x1f3fc;&#x200d;&#x2640;&#xfe0f;','&#x1f9d7;&#x1f3fb;&#x200d;&#x2642;&#xfe0f;','&#x1f468;&#x1f3fe;&#x200d;&#x2695;&#xfe0f;','&#x1f468;&#x1f3fe;&#x200d;&#x2696;&#xfe0f;','&#x1f468;&#x1f3fe;&#x200d;&#x2708;&#xfe0f;','&#x1f9d7;&#x1f3fb;&#x200d;&#x2640;&#xfe0f;','&#x1f9d6;&#x1f3ff;&#x200d;&#x2642;&#xfe0f;','&#x1f9d6;&#x1f3ff;&#x200d;&#x2640;&#xfe0f;','&#x1f9d6;&#x1f3fe;&#x200d;&#x2642;&#xfe0f;','&#x1f9d6;&#x1f3fe;&#x200d;&#x2640;&#xfe0f;','&#x1f9d6;&#x1f3fd;&#x200d;&#x2642;&#xfe0f;','&#x1f9d6;&#x1f3fd;&#x200d;&#x2640;&#xfe0f;','&#x1f9d6;&#x1f3fc;&#x200d;&#x2642;&#xfe0f;','&#x1f9d6;&#x1f3fc;&#x200d;&#x2640;&#xfe0f;','&#x1f9d6;&#x1f3fb;&#x200d;&#x2642;&#xfe0f;','&#x1f9d6;&#x1f3fb;&#x200d;&#x2640;&#xfe0f;','&#x1f9b9;&#x1f3ff;&#x200d;&#x2642;&#xfe0f;','&#x1f9b9;&#x1f3ff;&#x200d;&#x2640;&#xfe0f;','&#x1f9b9;&#x1f3fe;&#x200d;&#x2642;&#xfe0f;','&#x1f9b9;&#x1f3fe;&#x200d;&#x2640;&#xfe0f;','&#x1f9b9;&#x1f3fd;&#x200d;&#x2642;&#xfe0f;','&#x1f9b9;&#x1f3fd;&#x200d;&#x2640;&#xfe0f;','&#x1f468;&#x1f3ff;&#x200d;&#x2695;&#xfe0f;','&#x1f468;&#x1f3ff;&#x200d;&#x2696;&#xfe0f;','&#x1f468;&#x1f3ff;&#x200d;&#x2708;&#xfe0f;','&#x1f9b9;&#x1f3fc;&#x200d;&#x2642;&#xfe0f;','&#x1f9b9;&#x1f3fc;&#x200d;&#x2640;&#xfe0f;','&#x1f9b9;&#x1f3fb;&#x200d;&#x2642;&#xfe0f;','&#x1f9b9;&#x1f3fb;&#x200d;&#x2640;&#xfe0f;','&#x1f9b8;&#x1f3ff;&#x200d;&#x2642;&#xfe0f;','&#x1f9b8;&#x1f3ff;&#x200d;&#x2640;&#xfe0f;','&#x1f9b8;&#x1f3fe;&#x200d;&#x2642;&#xfe0f;','&#x1f3c3;&#x1f3fd;&#x200d;&#x2640;&#xfe0f;','&#x1f9b8;&#x1f3fe;&#x200d;&#x2640;&#xfe0f;','&#x1f3c3;&#x1f3fd;&#x200d;&#x2642;&#xfe0f;','&#x1f3c3;&#x1f3fe;&#x200d;&#x2640;&#xfe0f;','&#x1f9b8;&#x1f3fd;&#x200d;&#x2642;&#xfe0f;','&#x1f3c3;&#x1f3fe;&#x200d;&#x2642;&#xfe0f;','&#x1f3c3;&#x1f3ff;&#x200d;&#x2640;&#xfe0f;','&#x1f3c3;&#x1f3ff;&#x200d;&#x2642;&#xfe0f;','&#x1f3c4;&#x1f3fb;&#x200d;&#x2640;&#xfe0f;','&#x1f3c4;&#x1f3fb;&#x200d;&#x2642;&#xfe0f;','&#x1f3c4;&#x1f3fc;&#x200d;&#x2640;&#xfe0f;','&#x1f3c4;&#x1f3fc;&#x200d;&#x2642;&#xfe0f;','&#x1f3c4;&#x1f3fd;&#x200d;&#x2640;&#xfe0f;','&#x1f3c4;&#x1f3fd;&#x200d;&#x2642;&#xfe0f;','&#x1f3c4;&#x1f3fe;&#x200d;&#x2640;&#xfe0f;','&#x1f9b8;&#x1f3fd;&#x200d;&#x2640;&#xfe0f;','&#x1f9b8;&#x1f3fc;&#x200d;&#x2642;&#xfe0f;','&#x1f9b8;&#x1f3fc;&#x200d;&#x2640;&#xfe0f;','&#x1f9b8;&#x1f3fb;&#x200d;&#x2642;&#xfe0f;','&#x1f9b8;&#x1f3fb;&#x200d;&#x2640;&#xfe0f;','&#x1f93e;&#x1f3ff;&#x200d;&#x2642;&#xfe0f;','&#x1f93e;&#x1f3ff;&#x200d;&#x2640;&#xfe0f;','&#x1f93e;&#x1f3fe;&#x200d;&#x2642;&#xfe0f;','&#x1f93e;&#x1f3fe;&#x200d;&#x2640;&#xfe0f;','&#x1f93e;&#x1f3fd;&#x200d;&#x2642;&#xfe0f;','&#x1f93e;&#x1f3fd;&#x200d;&#x2640;&#xfe0f;','&#x1f93e;&#x1f3fc;&#x200d;&#x2642;&#xfe0f;','&#x1f93e;&#x1f3fc;&#x200d;&#x2640;&#xfe0f;','&#x1f3c4;&#x1f3fe;&#x200d;&#x2642;&#xfe0f;','&#x1f3c4;&#x1f3ff;&#x200d;&#x2640;&#xfe0f;','&#x1f93e;&#x1f3fb;&#x200d;&#x2642;&#xfe0f;','&#x1f93e;&#x1f3fb;&#x200d;&#x2640;&#xfe0f;','&#x1f93d;&#x1f3ff;&#x200d;&#x2642;&#xfe0f;','&#x1f93d;&#x1f3ff;&#x200d;&#x2640;&#xfe0f;','&#x1f93d;&#x1f3fe;&#x200d;&#x2642;&#xfe0f;','&#x1f93d;&#x1f3fe;&#x200d;&#x2640;&#xfe0f;','&#x1f93d;&#x1f3fd;&#x200d;&#x2642;&#xfe0f;','&#x1f93d;&#x1f3fd;&#x200d;&#x2640;&#xfe0f;','&#x1f93d;&#x1f3fc;&#x200d;&#x2642;&#xfe0f;','&#x1f93d;&#x1f3fc;&#x200d;&#x2640;&#xfe0f;','&#x1f93d;&#x1f3fb;&#x200d;&#x2642;&#xfe0f;','&#x1f93d;&#x1f3fb;&#x200d;&#x2640;&#xfe0f;','&#x1f939;&#x1f3ff;&#x200d;&#x2642;&#xfe0f;','&#x1f939;&#x1f3ff;&#x200d;&#x2640;&#xfe0f;','&#x1f939;&#x1f3fe;&#x200d;&#x2642;&#xfe0f;','&#x1f939;&#x1f3fe;&#x200d;&#x2640;&#xfe0f;','&#x1f939;&#x1f3fd;&#x200d;&#x2642;&#xfe0f;','&#x1f469;&#x1f3fb;&#x200d;&#x2695;&#xfe0f;','&#x1f469;&#x1f3fb;&#x200d;&#x2696;&#xfe0f;','&#x1f469;&#x1f3fb;&#x200d;&#x2708;&#xfe0f;','&#x1f939;&#x1f3fd;&#x200d;&#x2640;&#xfe0f;','&#x1f939;&#x1f3fc;&#x200d;&#x2642;&#xfe0f;','&#x1f939;&#x1f3fc;&#x200d;&#x2640;&#xfe0f;','&#x1f939;&#x1f3fb;&#x200d;&#x2642;&#xfe0f;','&#x1f939;&#x1f3fb;&#x200d;&#x2640;&#xfe0f;','&#x1f938;&#x1f3ff;&#x200d;&#x2642;&#xfe0f;','&#x1f938;&#x1f3ff;&#x200d;&#x2640;&#xfe0f;','&#x1f938;&#x1f3fe;&#x200d;&#x2642;&#xfe0f;','&#x1f938;&#x1f3fe;&#x200d;&#x2640;&#xfe0f;','&#x1f938;&#x1f3fd;&#x200d;&#x2642;&#xfe0f;','&#x1f938;&#x1f3fd;&#x200d;&#x2640;&#xfe0f;','&#x1f938;&#x1f3fc;&#x200d;&#x2642;&#xfe0f;','&#x1f938;&#x1f3fc;&#x200d;&#x2640;&#xfe0f;','&#x1f938;&#x1f3fb;&#x200d;&#x2642;&#xfe0f;','&#x1f938;&#x1f3fb;&#x200d;&#x2640;&#xfe0f;','&#x1f937;&#x1f3ff;&#x200d;&#x2642;&#xfe0f;','&#x1f937;&#x1f3ff;&#x200d;&#x2640;&#xfe0f;','&#x1f469;&#x1f3fc;&#x200d;&#x2695;&#xfe0f;','&#x1f469;&#x1f3fc;&#x200d;&#x2696;&#xfe0f;','&#x1f469;&#x1f3fc;&#x200d;&#x2708;&#xfe0f;','&#x1f937;&#x1f3fe;&#x200d;&#x2642;&#xfe0f;','&#x1f937;&#x1f3fe;&#x200d;&#x2640;&#xfe0f;','&#x1f937;&#x1f3fd;&#x200d;&#x2642;&#xfe0f;','&#x1f937;&#x1f3fd;&#x200d;&#x2640;&#xfe0f;','&#x1f937;&#x1f3fc;&#x200d;&#x2642;&#xfe0f;','&#x1f937;&#x1f3fc;&#x200d;&#x2640;&#xfe0f;','&#x1f937;&#x1f3fb;&#x200d;&#x2642;&#xfe0f;','&#x1f937;&#x1f3fb;&#x200d;&#x2640;&#xfe0f;','&#x1f935;&#x1f3ff;&#x200d;&#x2642;&#xfe0f;','&#x1f3c3;&#x1f3fb;&#x200d;&#x2642;&#xfe0f;','&#x1f935;&#x1f3fe;&#x200d;&#x2642;&#xfe0f;','&#x1f935;&#x1f3fe;&#x200d;&#x2640;&#xfe0f;','&#x1f935;&#x1f3fd;&#x200d;&#x2642;&#xfe0f;','&#x1f935;&#x1f3fd;&#x200d;&#x2640;&#xfe0f;','&#x1f935;&#x1f3fc;&#x200d;&#x2642;&#xfe0f;','&#x1f935;&#x1f3fc;&#x200d;&#x2640;&#xfe0f;','&#x1f935;&#x1f3fb;&#x200d;&#x2642;&#xfe0f;','&#x1f469;&#x1f3fd;&#x200d;&#x2695;&#xfe0f;','&#x1f469;&#x1f3fd;&#x200d;&#x2696;&#xfe0f;','&#x1f469;&#x1f3fd;&#x200d;&#x2708;&#xfe0f;','&#x1f935;&#x1f3fb;&#x200d;&#x2640;&#xfe0f;','&#x1f926;&#x1f3ff;&#x200d;&#x2642;&#xfe0f;','&#x1f926;&#x1f3ff;&#x200d;&#x2640;&#xfe0f;','&#x1f926;&#x1f3fe;&#x200d;&#x2642;&#xfe0f;','&#x1f926;&#x1f3fe;&#x200d;&#x2640;&#xfe0f;','&#x1f926;&#x1f3fd;&#x200d;&#x2642;&#xfe0f;','&#x1f926;&#x1f3fd;&#x200d;&#x2640;&#xfe0f;','&#x1f926;&#x1f3fc;&#x200d;&#x2642;&#xfe0f;','&#x1f926;&#x1f3fc;&#x200d;&#x2640;&#xfe0f;','&#x1f926;&#x1f3fb;&#x200d;&#x2642;&#xfe0f;','&#x1f926;&#x1f3fb;&#x200d;&#x2640;&#xfe0f;','&#x1f6b6;&#x1f3ff;&#x200d;&#x2642;&#xfe0f;','&#x1f6b6;&#x1f3ff;&#x200d;&#x2640;&#xfe0f;','&#x1f6b6;&#x1f3fe;&#x200d;&#x2642;&#xfe0f;','&#x1f6b6;&#x1f3fe;&#x200d;&#x2640;&#xfe0f;','&#x1f6b6;&#x1f3fd;&#x200d;&#x2642;&#xfe0f;','&#x1f6b6;&#x1f3fd;&#x200d;&#x2640;&#xfe0f;','&#x1f469;&#x1f3fe;&#x200d;&#x2695;&#xfe0f;','&#x1f469;&#x1f3fe;&#x200d;&#x2696;&#xfe0f;','&#x1f469;&#x1f3fe;&#x200d;&#x2708;&#xfe0f;','&#x1f6b6;&#x1f3fc;&#x200d;&#x2642;&#xfe0f;','&#x1f6b6;&#x1f3fc;&#x200d;&#x2640;&#xfe0f;','&#x1f6b6;&#x1f3fb;&#x200d;&#x2642;&#xfe0f;','&#x1f6b6;&#x1f3fb;&#x200d;&#x2640;&#xfe0f;','&#x1f6b5;&#x1f3ff;&#x200d;&#x2642;&#xfe0f;','&#x1f6b5;&#x1f3ff;&#x200d;&#x2640;&#xfe0f;','&#x1f6b5;&#x1f3fe;&#x200d;&#x2642;&#xfe0f;','&#x1f6b5;&#x1f3fe;&#x200d;&#x2640;&#xfe0f;','&#x1f6b5;&#x1f3fd;&#x200d;&#x2642;&#xfe0f;','&#x1f6b5;&#x1f3fd;&#x200d;&#x2640;&#xfe0f;','&#x1f6b5;&#x1f3fc;&#x200d;&#x2642;&#xfe0f;','&#x1f6b5;&#x1f3fc;&#x200d;&#x2640;&#xfe0f;','&#x1f6b5;&#x1f3fb;&#x200d;&#x2642;&#xfe0f;','&#x1f6b5;&#x1f3fb;&#x200d;&#x2640;&#xfe0f;','&#x1f6b4;&#x1f3ff;&#x200d;&#x2642;&#xfe0f;','&#x1f6b4;&#x1f3ff;&#x200d;&#x2640;&#xfe0f;','&#x1f6b4;&#x1f3fe;&#x200d;&#x2642;&#xfe0f;','&#x1f469;&#x1f3ff;&#x200d;&#x2695;&#xfe0f;','&#x1f469;&#x1f3ff;&#x200d;&#x2696;&#xfe0f;','&#x1f469;&#x1f3ff;&#x200d;&#x2708;&#xfe0f;','&#x1f6b4;&#x1f3fe;&#x200d;&#x2640;&#xfe0f;','&#x1f6b4;&#x1f3fd;&#x200d;&#x2642;&#xfe0f;','&#x1f6b4;&#x1f3fd;&#x200d;&#x2640;&#xfe0f;','&#x1f6b4;&#x1f3fc;&#x200d;&#x2642;&#xfe0f;','&#x1f6b4;&#x1f3fc;&#x200d;&#x2640;&#xfe0f;','&#x1f6b4;&#x1f3fb;&#x200d;&#x2642;&#xfe0f;','&#x1f6b4;&#x1f3fb;&#x200d;&#x2640;&#xfe0f;','&#x1f3c4;&#x1f3ff;&#x200d;&#x2642;&#xfe0f;','&#x1f6a3;&#x1f3ff;&#x200d;&#x2642;&#xfe0f;','&#x1f3ca;&#x1f3fb;&#x200d;&#x2640;&#xfe0f;','&#x1f3ca;&#x1f3fb;&#x200d;&#x2642;&#xfe0f;','&#x1f6a3;&#x1f3ff;&#x200d;&#x2640;&#xfe0f;','&#x1f3ca;&#x1f3fc;&#x200d;&#x2640;&#xfe0f;','&#x1f3ca;&#x1f3fc;&#x200d;&#x2642;&#xfe0f;','&#x1f3ca;&#x1f3fd;&#x200d;&#x2640;&#xfe0f;','&#x1f3ca;&#x1f3fd;&#x200d;&#x2642;&#xfe0f;','&#x1f3ca;&#x1f3fe;&#x200d;&#x2640;&#xfe0f;','&#x1f6a3;&#x1f3fe;&#x200d;&#x2642;&#xfe0f;','&#x1f6a3;&#x1f3fe;&#x200d;&#x2640;&#xfe0f;','&#x1f6a3;&#x1f3fd;&#x200d;&#x2642;&#xfe0f;','&#x1f6a3;&#x1f3fd;&#x200d;&#x2640;&#xfe0f;','&#x1f6a3;&#x1f3fc;&#x200d;&#x2642;&#xfe0f;','&#x1f6a3;&#x1f3fc;&#x200d;&#x2640;&#xfe0f;','&#x1f6a3;&#x1f3fb;&#x200d;&#x2642;&#xfe0f;','&#x1f6a3;&#x1f3fb;&#x200d;&#x2640;&#xfe0f;','&#x1f64e;&#x1f3ff;&#x200d;&#x2642;&#xfe0f;','&#x1f64e;&#x1f3ff;&#x200d;&#x2640;&#xfe0f;','&#x1f64e;&#x1f3fe;&#x200d;&#x2642;&#xfe0f;','&#x1f64e;&#x1f3fe;&#x200d;&#x2640;&#xfe0f;','&#x1f64e;&#x1f3fd;&#x200d;&#x2642;&#xfe0f;','&#x1f3ca;&#x1f3fe;&#x200d;&#x2642;&#xfe0f;','&#x1f3ca;&#x1f3ff;&#x200d;&#x2640;&#xfe0f;','&#x1f3ca;&#x1f3ff;&#x200d;&#x2642;&#xfe0f;','&#x1f3cb;&#x1f3fb;&#x200d;&#x2640;&#xfe0f;','&#x1f46e;&#x1f3fb;&#x200d;&#x2640;&#xfe0f;','&#x1f46e;&#x1f3fb;&#x200d;&#x2642;&#xfe0f;','&#x1f46e;&#x1f3fc;&#x200d;&#x2640;&#xfe0f;','&#x1f46e;&#x1f3fc;&#x200d;&#x2642;&#xfe0f;','&#x1f46e;&#x1f3fd;&#x200d;&#x2640;&#xfe0f;','&#x1f46e;&#x1f3fd;&#x200d;&#x2642;&#xfe0f;','&#x1f46e;&#x1f3fe;&#x200d;&#x2640;&#xfe0f;','&#x1f46e;&#x1f3fe;&#x200d;&#x2642;&#xfe0f;','&#x1f46e;&#x1f3ff;&#x200d;&#x2640;&#xfe0f;','&#x1f46e;&#x1f3ff;&#x200d;&#x2642;&#xfe0f;','&#x1f64e;&#x1f3fd;&#x200d;&#x2640;&#xfe0f;','&#x1f64e;&#x1f3fc;&#x200d;&#x2642;&#xfe0f;','&#x1f64e;&#x1f3fc;&#x200d;&#x2640;&#xfe0f;','&#x1f64e;&#x1f3fb;&#x200d;&#x2642;&#xfe0f;','&#x1f471;&#x1f3fb;&#x200d;&#x2640;&#xfe0f;','&#x1f471;&#x1f3fb;&#x200d;&#x2642;&#xfe0f;','&#x1f471;&#x1f3fc;&#x200d;&#x2640;&#xfe0f;','&#x1f471;&#x1f3fc;&#x200d;&#x2642;&#xfe0f;','&#x1f471;&#x1f3fd;&#x200d;&#x2640;&#xfe0f;','&#x1f471;&#x1f3fd;&#x200d;&#x2642;&#xfe0f;','&#x1f471;&#x1f3fe;&#x200d;&#x2640;&#xfe0f;','&#x1f471;&#x1f3fe;&#x200d;&#x2642;&#xfe0f;','&#x1f471;&#x1f3ff;&#x200d;&#x2640;&#xfe0f;','&#x1f471;&#x1f3ff;&#x200d;&#x2642;&#xfe0f;','&#x1f64e;&#x1f3fb;&#x200d;&#x2640;&#xfe0f;','&#x1f64d;&#x1f3ff;&#x200d;&#x2642;&#xfe0f;','&#x1f473;&#x1f3fb;&#x200d;&#x2640;&#xfe0f;','&#x1f473;&#x1f3fb;&#x200d;&#x2642;&#xfe0f;','&#x1f473;&#x1f3fc;&#x200d;&#x2640;&#xfe0f;','&#x1f473;&#x1f3fc;&#x200d;&#x2642;&#xfe0f;','&#x1f473;&#x1f3fd;&#x200d;&#x2640;&#xfe0f;','&#x1f473;&#x1f3fd;&#x200d;&#x2642;&#xfe0f;','&#x1f473;&#x1f3fe;&#x200d;&#x2640;&#xfe0f;','&#x1f473;&#x1f3fe;&#x200d;&#x2642;&#xfe0f;','&#x1f473;&#x1f3ff;&#x200d;&#x2640;&#xfe0f;','&#x1f473;&#x1f3ff;&#x200d;&#x2642;&#xfe0f;','&#x1f64d;&#x1f3ff;&#x200d;&#x2640;&#xfe0f;','&#x1f64d;&#x1f3fe;&#x200d;&#x2642;&#xfe0f;','&#x1f477;&#x1f3fb;&#x200d;&#x2640;&#xfe0f;','&#x1f477;&#x1f3fb;&#x200d;&#x2642;&#xfe0f;','&#x1f477;&#x1f3fc;&#x200d;&#x2640;&#xfe0f;','&#x1f477;&#x1f3fc;&#x200d;&#x2642;&#xfe0f;','&#x1f477;&#x1f3fd;&#x200d;&#x2640;&#xfe0f;','&#x1f477;&#x1f3fd;&#x200d;&#x2642;&#xfe0f;','&#x1f477;&#x1f3fe;&#x200d;&#x2640;&#xfe0f;','&#x1f477;&#x1f3fe;&#x200d;&#x2642;&#xfe0f;','&#x1f477;&#x1f3ff;&#x200d;&#x2640;&#xfe0f;','&#x1f477;&#x1f3ff;&#x200d;&#x2642;&#xfe0f;','&#x1f64d;&#x1f3fe;&#x200d;&#x2640;&#xfe0f;','&#x1f64d;&#x1f3fd;&#x200d;&#x2642;&#xfe0f;','&#x1f481;&#x1f3fb;&#x200d;&#x2640;&#xfe0f;','&#x1f481;&#x1f3fb;&#x200d;&#x2642;&#xfe0f;','&#x1f481;&#x1f3fc;&#x200d;&#x2640;&#xfe0f;','&#x1f481;&#x1f3fc;&#x200d;&#x2642;&#xfe0f;','&#x1f481;&#x1f3fd;&#x200d;&#x2640;&#xfe0f;','&#x1f481;&#x1f3fd;&#x200d;&#x2642;&#xfe0f;','&#x1f481;&#x1f3fe;&#x200d;&#x2640;&#xfe0f;','&#x1f481;&#x1f3fe;&#x200d;&#x2642;&#xfe0f;','&#x1f481;&#x1f3ff;&#x200d;&#x2640;&#xfe0f;','&#x1f481;&#x1f3ff;&#x200d;&#x2642;&#xfe0f;','&#x1f64d;&#x1f3fd;&#x200d;&#x2640;&#xfe0f;','&#x1f64d;&#x1f3fc;&#x200d;&#x2642;&#xfe0f;','&#x1f3c3;&#x1f3fb;&#x200d;&#x2640;&#xfe0f;','&#x1f482;&#x1f3fb;&#x200d;&#x2642;&#xfe0f;','&#x1f482;&#x1f3fc;&#x200d;&#x2640;&#xfe0f;','&#x1f482;&#x1f3fc;&#x200d;&#x2642;&#xfe0f;','&#x1f482;&#x1f3fd;&#x200d;&#x2640;&#xfe0f;','&#x1f482;&#x1f3fd;&#x200d;&#x2642;&#xfe0f;','&#x1f482;&#x1f3fe;&#x200d;&#x2640;&#xfe0f;','&#x1f482;&#x1f3fe;&#x200d;&#x2642;&#xfe0f;','&#x1f482;&#x1f3ff;&#x200d;&#x2640;&#xfe0f;','&#x1f482;&#x1f3ff;&#x200d;&#x2642;&#xfe0f;','&#x1f64d;&#x1f3fc;&#x200d;&#x2640;&#xfe0f;','&#x1f64d;&#x1f3fb;&#x200d;&#x2642;&#xfe0f;','&#x1f486;&#x1f3fb;&#x200d;&#x2640;&#xfe0f;','&#x1f486;&#x1f3fb;&#x200d;&#x2642;&#xfe0f;','&#x1f486;&#x1f3fc;&#x200d;&#x2640;&#xfe0f;','&#x1f486;&#x1f3fc;&#x200d;&#x2642;&#xfe0f;','&#x1f486;&#x1f3fd;&#x200d;&#x2640;&#xfe0f;','&#x1f486;&#x1f3fd;&#x200d;&#x2642;&#xfe0f;','&#x1f486;&#x1f3fe;&#x200d;&#x2640;&#xfe0f;','&#x1f486;&#x1f3fe;&#x200d;&#x2642;&#xfe0f;','&#x1f486;&#x1f3ff;&#x200d;&#x2640;&#xfe0f;','&#x1f486;&#x1f3ff;&#x200d;&#x2642;&#xfe0f;','&#x1f64d;&#x1f3fb;&#x200d;&#x2640;&#xfe0f;','&#x1f64b;&#x1f3ff;&#x200d;&#x2642;&#xfe0f;','&#x1f487;&#x1f3fb;&#x200d;&#x2640;&#xfe0f;','&#x1f487;&#x1f3fb;&#x200d;&#x2642;&#xfe0f;','&#x1f487;&#x1f3fc;&#x200d;&#x2640;&#xfe0f;','&#x1f487;&#x1f3fc;&#x200d;&#x2642;&#xfe0f;','&#x1f487;&#x1f3fd;&#x200d;&#x2640;&#xfe0f;','&#x1f487;&#x1f3fd;&#x200d;&#x2642;&#xfe0f;','&#x1f487;&#x1f3fe;&#x200d;&#x2640;&#xfe0f;','&#x1f487;&#x1f3fe;&#x200d;&#x2642;&#xfe0f;','&#x1f487;&#x1f3ff;&#x200d;&#x2640;&#xfe0f;','&#x1f487;&#x1f3ff;&#x200d;&#x2642;&#xfe0f;','&#x1f64b;&#x1f3ff;&#x200d;&#x2640;&#xfe0f;','&#x1f64b;&#x1f3fe;&#x200d;&#x2642;&#xfe0f;','&#x1f574;&#x1f3fb;&#x200d;&#x2640;&#xfe0f;','&#x1f574;&#x1f3fb;&#x200d;&#x2642;&#xfe0f;','&#x1f574;&#x1f3fc;&#x200d;&#x2640;&#xfe0f;','&#x1f574;&#x1f3fc;&#x200d;&#x2642;&#xfe0f;','&#x1f574;&#x1f3fd;&#x200d;&#x2640;&#xfe0f;','&#x1f574;&#x1f3fd;&#x200d;&#x2642;&#xfe0f;','&#x1f574;&#x1f3fe;&#x200d;&#x2640;&#xfe0f;','&#x1f574;&#x1f3fe;&#x200d;&#x2642;&#xfe0f;','&#x1f574;&#x1f3ff;&#x200d;&#x2640;&#xfe0f;','&#x1f574;&#x1f3ff;&#x200d;&#x2642;&#xfe0f;','&#x1f575;&#x1f3fb;&#x200d;&#x2640;&#xfe0f;','&#x1f575;&#x1f3fb;&#x200d;&#x2642;&#xfe0f;','&#x1f575;&#x1f3fc;&#x200d;&#x2640;&#xfe0f;','&#x1f575;&#x1f3fc;&#x200d;&#x2642;&#xfe0f;','&#x1f575;&#x1f3fd;&#x200d;&#x2640;&#xfe0f;','&#x1f575;&#x1f3fd;&#x200d;&#x2642;&#xfe0f;','&#x1f575;&#x1f3fe;&#x200d;&#x2640;&#xfe0f;','&#x1f575;&#x1f3fe;&#x200d;&#x2642;&#xfe0f;','&#x1f575;&#x1f3ff;&#x200d;&#x2640;&#xfe0f;','&#x1f575;&#x1f3ff;&#x200d;&#x2642;&#xfe0f;','&#x1f645;&#x1f3fb;&#x200d;&#x2640;&#xfe0f;','&#x1f645;&#x1f3fb;&#x200d;&#x2642;&#xfe0f;','&#x1f645;&#x1f3fc;&#x200d;&#x2640;&#xfe0f;','&#x1f645;&#x1f3fc;&#x200d;&#x2642;&#xfe0f;','&#x1f645;&#x1f3fd;&#x200d;&#x2640;&#xfe0f;','&#x1f645;&#x1f3fd;&#x200d;&#x2642;&#xfe0f;','&#x1f645;&#x1f3fe;&#x200d;&#x2640;&#xfe0f;','&#x1f645;&#x1f3fe;&#x200d;&#x2642;&#xfe0f;','&#x1f645;&#x1f3ff;&#x200d;&#x2640;&#xfe0f;','&#x1f645;&#x1f3ff;&#x200d;&#x2642;&#xfe0f;','&#x1f64b;&#x1f3fe;&#x200d;&#x2640;&#xfe0f;','&#x1f64b;&#x1f3fd;&#x200d;&#x2642;&#xfe0f;','&#x1f646;&#x1f3fb;&#x200d;&#x2640;&#xfe0f;','&#x1f646;&#x1f3fb;&#x200d;&#x2642;&#xfe0f;','&#x1f646;&#x1f3fc;&#x200d;&#x2640;&#xfe0f;','&#x1f646;&#x1f3fc;&#x200d;&#x2642;&#xfe0f;','&#x1f646;&#x1f3fd;&#x200d;&#x2640;&#xfe0f;','&#x1f646;&#x1f3fd;&#x200d;&#x2642;&#xfe0f;','&#x1f646;&#x1f3fe;&#x200d;&#x2640;&#xfe0f;','&#x1f646;&#x1f3fe;&#x200d;&#x2642;&#xfe0f;','&#x1f646;&#x1f3ff;&#x200d;&#x2640;&#xfe0f;','&#x1f646;&#x1f3ff;&#x200d;&#x2642;&#xfe0f;','&#x1f64b;&#x1f3fd;&#x200d;&#x2640;&#xfe0f;','&#x1f64b;&#x1f3fc;&#x200d;&#x2642;&#xfe0f;','&#x1f647;&#x1f3fb;&#x200d;&#x2640;&#xfe0f;','&#x1f647;&#x1f3fb;&#x200d;&#x2642;&#xfe0f;','&#x1f647;&#x1f3fc;&#x200d;&#x2640;&#xfe0f;','&#x1f647;&#x1f3fc;&#x200d;&#x2642;&#xfe0f;','&#x1f647;&#x1f3fd;&#x200d;&#x2640;&#xfe0f;','&#x1f647;&#x1f3fd;&#x200d;&#x2642;&#xfe0f;','&#x1f647;&#x1f3fe;&#x200d;&#x2640;&#xfe0f;','&#x1f647;&#x1f3fe;&#x200d;&#x2642;&#xfe0f;','&#x1f647;&#x1f3ff;&#x200d;&#x2640;&#xfe0f;','&#x1f647;&#x1f3ff;&#x200d;&#x2642;&#xfe0f;','&#x1f64b;&#x1f3fc;&#x200d;&#x2640;&#xfe0f;','&#x1f64b;&#x1f3fb;&#x200d;&#x2642;&#xfe0f;','&#x1f64b;&#x1f3fb;&#x200d;&#x2640;&#xfe0f;','&#x1f574;&#xfe0f;&#x200d;&#x2640;&#xfe0f;','&#x1f574;&#xfe0f;&#x200d;&#x2642;&#xfe0f;','&#x1f3cb;&#xfe0f;&#x200d;&#x2640;&#xfe0f;','&#x1f3cb;&#xfe0f;&#x200d;&#x2642;&#xfe0f;','&#x26f9;&#x1f3fe;&#x200d;&#x2640;&#xfe0f;','&#x1f3cc;&#xfe0f;&#x200d;&#x2640;&#xfe0f;','&#x1f3cc;&#xfe0f;&#x200d;&#x2642;&#xfe0f;','&#x26f9;&#x1f3fb;&#x200d;&#x2642;&#xfe0f;','&#x26f9;&#x1f3fb;&#x200d;&#x2640;&#xfe0f;','&#x26f9;&#x1f3fe;&#x200d;&#x2642;&#xfe0f;','&#x26f9;&#x1f3ff;&#x200d;&#x2640;&#xfe0f;','&#x26f9;&#x1f3ff;&#x200d;&#x2642;&#xfe0f;','&#x1f575;&#xfe0f;&#x200d;&#x2640;&#xfe0f;','&#x1f575;&#xfe0f;&#x200d;&#x2642;&#xfe0f;','&#x26f9;&#x1f3fd;&#x200d;&#x2640;&#xfe0f;','&#x26f9;&#x1f3fd;&#x200d;&#x2642;&#xfe0f;','&#x26f9;&#x1f3fc;&#x200d;&#x2640;&#xfe0f;','&#x26f9;&#x1f3fc;&#x200d;&#x2642;&#xfe0f;','&#x26f9;&#xfe0f;&#x200d;&#x2640;&#xfe0f;','&#x26f9;&#xfe0f;&#x200d;&#x2642;&#xfe0f;','&#x1f469;&#x1f3fb;&#x200d;&#x1f3a8;','&#x1f468;&#x1f3fb;&#x200d;&#x1f33e;','&#x1f468;&#x1f3fb;&#x200d;&#x1f373;','&#x1f468;&#x1f3fb;&#x200d;&#x1f393;','&#x1f468;&#x1f3fb;&#x200d;&#x1f3a4;','&#x1f468;&#x1f3fb;&#x200d;&#x1f3a8;','&#x1f468;&#x1f3fb;&#x200d;&#x1f3eb;','&#x1f468;&#x1f3fb;&#x200d;&#x1f3ed;','&#x1f468;&#x1f3fb;&#x200d;&#x1f4bb;','&#x1f468;&#x1f3fb;&#x200d;&#x1f4bc;','&#x1f468;&#x1f3fb;&#x200d;&#x1f527;','&#x1f468;&#x1f3fb;&#x200d;&#x1f52c;','&#x1f468;&#x1f3fb;&#x200d;&#x1f680;','&#x1f468;&#x1f3fb;&#x200d;&#x1f692;','&#x1f468;&#x1f3fb;&#x200d;&#x1f9b0;','&#x1f468;&#x1f3fb;&#x200d;&#x1f9b1;','&#x1f468;&#x1f3fb;&#x200d;&#x1f9b2;','&#x1f468;&#x1f3fb;&#x200d;&#x1f9b3;','&#x1f468;&#x1f3fc;&#x200d;&#x1f33e;','&#x1f468;&#x1f3fc;&#x200d;&#x1f373;','&#x1f468;&#x1f3fc;&#x200d;&#x1f393;','&#x1f468;&#x1f3fc;&#x200d;&#x1f3a4;','&#x1f468;&#x1f3fc;&#x200d;&#x1f3a8;','&#x1f468;&#x1f3fc;&#x200d;&#x1f3eb;','&#x1f468;&#x1f3fc;&#x200d;&#x1f3ed;','&#x1f468;&#x1f3fc;&#x200d;&#x1f4bb;','&#x1f468;&#x1f3fc;&#x200d;&#x1f4bc;','&#x1f468;&#x1f3fc;&#x200d;&#x1f527;','&#x1f468;&#x1f3fc;&#x200d;&#x1f52c;','&#x1f468;&#x1f3fc;&#x200d;&#x1f680;','&#x1f468;&#x1f3fc;&#x200d;&#x1f692;','&#x1f468;&#x1f3fc;&#x200d;&#x1f9b0;','&#x1f468;&#x1f3fc;&#x200d;&#x1f9b1;','&#x1f468;&#x1f3fc;&#x200d;&#x1f9b2;','&#x1f468;&#x1f3fc;&#x200d;&#x1f9b3;','&#x1f468;&#x1f3fd;&#x200d;&#x1f33e;','&#x1f468;&#x1f3fd;&#x200d;&#x1f373;','&#x1f468;&#x1f3fd;&#x200d;&#x1f393;','&#x1f468;&#x1f3fd;&#x200d;&#x1f3a4;','&#x1f468;&#x1f3fd;&#x200d;&#x1f3a8;','&#x1f468;&#x1f3fd;&#x200d;&#x1f3eb;','&#x1f468;&#x1f3fd;&#x200d;&#x1f3ed;','&#x1f468;&#x1f3fd;&#x200d;&#x1f4bb;','&#x1f468;&#x1f3fd;&#x200d;&#x1f4bc;','&#x1f468;&#x1f3fd;&#x200d;&#x1f527;','&#x1f468;&#x1f3fd;&#x200d;&#x1f52c;','&#x1f468;&#x1f3fd;&#x200d;&#x1f680;','&#x1f468;&#x1f3fd;&#x200d;&#x1f692;','&#x1f468;&#x1f3fd;&#x200d;&#x1f9b0;','&#x1f468;&#x1f3fd;&#x200d;&#x1f9b1;','&#x1f468;&#x1f3fd;&#x200d;&#x1f9b2;','&#x1f469;&#x1f3ff;&#x200d;&#x1f9b3;','&#x1f469;&#x1f3ff;&#x200d;&#x1f9b2;','&#x1f469;&#x1f3ff;&#x200d;&#x1f9b1;','&#x1f468;&#x1f3fd;&#x200d;&#x1f9b3;','&#x1f468;&#x1f3fe;&#x200d;&#x1f33e;','&#x1f469;&#x1f3ff;&#x200d;&#x1f9b0;','&#x1f469;&#x1f3ff;&#x200d;&#x1f692;','&#x1f469;&#x1f3ff;&#x200d;&#x1f680;','&#x1f469;&#x1f3ff;&#x200d;&#x1f52c;','&#x1f469;&#x1f3ff;&#x200d;&#x1f527;','&#x1f469;&#x1f3ff;&#x200d;&#x1f4bc;','&#x1f469;&#x1f3ff;&#x200d;&#x1f4bb;','&#x1f469;&#x1f3ff;&#x200d;&#x1f3ed;','&#x1f469;&#x1f3ff;&#x200d;&#x1f3eb;','&#x1f469;&#x1f3ff;&#x200d;&#x1f3a8;','&#x1f468;&#x1f3fe;&#x200d;&#x1f373;','&#x1f468;&#x1f3fe;&#x200d;&#x1f393;','&#x1f469;&#x1f3ff;&#x200d;&#x1f3a4;','&#x1f469;&#x1f3ff;&#x200d;&#x1f393;','&#x1f469;&#x1f3ff;&#x200d;&#x1f373;','&#x1f469;&#x1f3ff;&#x200d;&#x1f33e;','&#x1f469;&#x1f3fe;&#x200d;&#x1f9b3;','&#x1f469;&#x1f3fe;&#x200d;&#x1f9b2;','&#x1f469;&#x1f3fe;&#x200d;&#x1f9b1;','&#x1f469;&#x1f3fe;&#x200d;&#x1f9b0;','&#x1f469;&#x1f3fe;&#x200d;&#x1f692;','&#x1f469;&#x1f3fe;&#x200d;&#x1f680;','&#x1f468;&#x1f3fe;&#x200d;&#x1f3a4;','&#x1f468;&#x1f3fe;&#x200d;&#x1f3a8;','&#x1f469;&#x1f3fe;&#x200d;&#x1f52c;','&#x1f469;&#x1f3fe;&#x200d;&#x1f527;','&#x1f469;&#x1f3fe;&#x200d;&#x1f4bc;','&#x1f469;&#x1f3fe;&#x200d;&#x1f4bb;','&#x1f469;&#x1f3fe;&#x200d;&#x1f3ed;','&#x1f469;&#x1f3fe;&#x200d;&#x1f3eb;','&#x1f469;&#x1f3fe;&#x200d;&#x1f3a8;','&#x1f469;&#x1f3fe;&#x200d;&#x1f3a4;','&#x1f469;&#x1f3fe;&#x200d;&#x1f393;','&#x1f469;&#x1f3fe;&#x200d;&#x1f373;','&#x1f468;&#x1f3fe;&#x200d;&#x1f3eb;','&#x1f468;&#x1f3fe;&#x200d;&#x1f3ed;','&#x1f469;&#x1f3fe;&#x200d;&#x1f33e;','&#x1f469;&#x1f3fd;&#x200d;&#x1f9b3;','&#x1f469;&#x1f3fd;&#x200d;&#x1f9b2;','&#x1f469;&#x1f3fd;&#x200d;&#x1f9b1;','&#x1f469;&#x1f3fd;&#x200d;&#x1f9b0;','&#x1f469;&#x1f3fd;&#x200d;&#x1f692;','&#x1f469;&#x1f3fd;&#x200d;&#x1f680;','&#x1f469;&#x1f3fd;&#x200d;&#x1f52c;','&#x1f469;&#x1f3fd;&#x200d;&#x1f527;','&#x1f469;&#x1f3fd;&#x200d;&#x1f4bc;','&#x1f468;&#x1f3fe;&#x200d;&#x1f4bb;','&#x1f468;&#x1f3fe;&#x200d;&#x1f4bc;','&#x1f469;&#x1f3fd;&#x200d;&#x1f4bb;','&#x1f469;&#x1f3fd;&#x200d;&#x1f3ed;','&#x1f469;&#x1f3fd;&#x200d;&#x1f3eb;','&#x1f469;&#x1f3fd;&#x200d;&#x1f3a8;','&#x1f469;&#x1f3fd;&#x200d;&#x1f3a4;','&#x1f469;&#x1f3fd;&#x200d;&#x1f393;','&#x1f469;&#x1f3fd;&#x200d;&#x1f373;','&#x1f469;&#x1f3fd;&#x200d;&#x1f33e;','&#x1f469;&#x1f3fc;&#x200d;&#x1f9b3;','&#x1f469;&#x1f3fc;&#x200d;&#x1f9b2;','&#x1f468;&#x1f3fe;&#x200d;&#x1f527;','&#x1f468;&#x1f3fe;&#x200d;&#x1f52c;','&#x1f469;&#x1f3fc;&#x200d;&#x1f9b1;','&#x1f469;&#x1f3fc;&#x200d;&#x1f9b0;','&#x1f469;&#x1f3fc;&#x200d;&#x1f692;','&#x1f469;&#x1f3fc;&#x200d;&#x1f680;','&#x1f469;&#x1f3fc;&#x200d;&#x1f52c;','&#x1f469;&#x1f3fc;&#x200d;&#x1f527;','&#x1f469;&#x1f3fc;&#x200d;&#x1f4bc;','&#x1f469;&#x1f3fc;&#x200d;&#x1f4bb;','&#x1f469;&#x1f3fc;&#x200d;&#x1f3ed;','&#x1f469;&#x1f3fc;&#x200d;&#x1f3eb;','&#x1f468;&#x1f3fe;&#x200d;&#x1f680;','&#x1f468;&#x1f3fe;&#x200d;&#x1f692;','&#x1f469;&#x1f3fc;&#x200d;&#x1f3a8;','&#x1f469;&#x1f3fc;&#x200d;&#x1f3a4;','&#x1f469;&#x1f3fc;&#x200d;&#x1f393;','&#x1f469;&#x1f3fc;&#x200d;&#x1f373;','&#x1f469;&#x1f3fc;&#x200d;&#x1f33e;','&#x1f469;&#x1f3fb;&#x200d;&#x1f9b3;','&#x1f469;&#x1f3fb;&#x200d;&#x1f9b2;','&#x1f469;&#x1f3fb;&#x200d;&#x1f9b1;','&#x1f469;&#x1f3fb;&#x200d;&#x1f9b0;','&#x1f469;&#x1f3fb;&#x200d;&#x1f692;','&#x1f468;&#x1f3fe;&#x200d;&#x1f9b0;','&#x1f468;&#x1f3fe;&#x200d;&#x1f9b1;','&#x1f468;&#x1f3fe;&#x200d;&#x1f9b2;','&#x1f468;&#x1f3fe;&#x200d;&#x1f9b3;','&#x1f469;&#x1f3fb;&#x200d;&#x1f680;','&#x1f469;&#x1f3fb;&#x200d;&#x1f52c;','&#x1f469;&#x1f3fb;&#x200d;&#x1f527;','&#x1f469;&#x1f3fb;&#x200d;&#x1f4bc;','&#x1f469;&#x1f3fb;&#x200d;&#x1f4bb;','&#x1f469;&#x1f3fb;&#x200d;&#x1f3ed;','&#x1f469;&#x1f3fb;&#x200d;&#x1f3eb;','&#x1f468;&#x1f3ff;&#x200d;&#x1f33e;','&#x1f469;&#x1f3fb;&#x200d;&#x1f3a4;','&#x1f469;&#x1f3fb;&#x200d;&#x1f393;','&#x1f468;&#x1f3ff;&#x200d;&#x1f373;','&#x1f468;&#x1f3ff;&#x200d;&#x1f393;','&#x1f469;&#x1f3fb;&#x200d;&#x1f373;','&#x1f469;&#x1f3fb;&#x200d;&#x1f33e;','&#x1f468;&#x1f3ff;&#x200d;&#x1f3a4;','&#x1f468;&#x1f3ff;&#x200d;&#x1f3a8;','&#x1f468;&#x1f3ff;&#x200d;&#x1f3eb;','&#x1f468;&#x1f3ff;&#x200d;&#x1f3ed;','&#x1f468;&#x1f3ff;&#x200d;&#x1f4bb;','&#x1f468;&#x1f3ff;&#x200d;&#x1f4bc;','&#x1f468;&#x1f3ff;&#x200d;&#x1f527;','&#x1f468;&#x1f3ff;&#x200d;&#x1f52c;','&#x1f468;&#x1f3ff;&#x200d;&#x1f680;','&#x1f468;&#x1f3ff;&#x200d;&#x1f692;','&#x1f468;&#x1f3ff;&#x200d;&#x1f9b0;','&#x1f468;&#x1f3ff;&#x200d;&#x1f9b1;','&#x1f468;&#x1f3ff;&#x200d;&#x1f9b2;','&#x1f468;&#x1f3ff;&#x200d;&#x1f9b3;','&#x1f3f3;&#xfe0f;&#x200d;&#x1f308;','&#x1f469;&#x200d;&#x2696;&#xfe0f;','&#x1f9b8;&#x200d;&#x2642;&#xfe0f;','&#x1f93e;&#x200d;&#x2642;&#xfe0f;','&#x1f93e;&#x200d;&#x2640;&#xfe0f;','&#x1f9b9;&#x200d;&#x2640;&#xfe0f;','&#x1f9b9;&#x200d;&#x2642;&#xfe0f;','&#x1f468;&#x200d;&#x2695;&#xfe0f;','&#x1f468;&#x200d;&#x2696;&#xfe0f;','&#x1f468;&#x200d;&#x2708;&#xfe0f;','&#x1f93d;&#x200d;&#x2642;&#xfe0f;','&#x1f93d;&#x200d;&#x2640;&#xfe0f;','&#x1f9d6;&#x200d;&#x2640;&#xfe0f;','&#x1f9d6;&#x200d;&#x2642;&#xfe0f;','&#x1f647;&#x200d;&#x2640;&#xfe0f;','&#x1f93c;&#x200d;&#x2642;&#xfe0f;','&#x1f93c;&#x200d;&#x2640;&#xfe0f;','&#x1f939;&#x200d;&#x2642;&#xfe0f;','&#x1f939;&#x200d;&#x2640;&#xfe0f;','&#x1f938;&#x200d;&#x2642;&#xfe0f;','&#x1f938;&#x200d;&#x2640;&#xfe0f;','&#x1f937;&#x200d;&#x2642;&#xfe0f;','&#x1f937;&#x200d;&#x2640;&#xfe0f;','&#x1f935;&#x200d;&#x2642;&#xfe0f;','&#x1f9d7;&#x200d;&#x2640;&#xfe0f;','&#x1f9d7;&#x200d;&#x2642;&#xfe0f;','&#x1f935;&#x200d;&#x2640;&#xfe0f;','&#x1f926;&#x200d;&#x2642;&#xfe0f;','&#x1f926;&#x200d;&#x2640;&#xfe0f;','&#x1f6b6;&#x200d;&#x2642;&#xfe0f;','&#x1f6b6;&#x200d;&#x2640;&#xfe0f;','&#x1f6b5;&#x200d;&#x2642;&#xfe0f;','&#x1f6b5;&#x200d;&#x2640;&#xfe0f;','&#x1f6b4;&#x200d;&#x2642;&#xfe0f;','&#x1f6b4;&#x200d;&#x2640;&#xfe0f;','&#x1f9d8;&#x200d;&#x2640;&#xfe0f;','&#x1f9d8;&#x200d;&#x2642;&#xfe0f;','&#x1f6a3;&#x200d;&#x2642;&#xfe0f;','&#x1f6a3;&#x200d;&#x2640;&#xfe0f;','&#x1f9d9;&#x200d;&#x2640;&#xfe0f;','&#x1f9d9;&#x200d;&#x2642;&#xfe0f;','&#x1f64e;&#x200d;&#x2642;&#xfe0f;','&#x1f64e;&#x200d;&#x2640;&#xfe0f;','&#x1f9da;&#x200d;&#x2640;&#xfe0f;','&#x1f9da;&#x200d;&#x2642;&#xfe0f;','&#x1f469;&#x200d;&#x2695;&#xfe0f;','&#x1f9b8;&#x200d;&#x2640;&#xfe0f;','&#x1f469;&#x200d;&#x2708;&#xfe0f;','&#x1f46e;&#x200d;&#x2640;&#xfe0f;','&#x1f46e;&#x200d;&#x2642;&#xfe0f;','&#x1f46f;&#x200d;&#x2640;&#xfe0f;','&#x1f46f;&#x200d;&#x2642;&#xfe0f;','&#x1f471;&#x200d;&#x2640;&#xfe0f;','&#x1f9db;&#x200d;&#x2640;&#xfe0f;','&#x1f9db;&#x200d;&#x2642;&#xfe0f;','&#x1f64d;&#x200d;&#x2642;&#xfe0f;','&#x1f64d;&#x200d;&#x2640;&#xfe0f;','&#x1f471;&#x200d;&#x2642;&#xfe0f;','&#x1f473;&#x200d;&#x2640;&#xfe0f;','&#x1f473;&#x200d;&#x2642;&#xfe0f;','&#x1f477;&#x200d;&#x2640;&#xfe0f;','&#x1f477;&#x200d;&#x2642;&#xfe0f;','&#x1f481;&#x200d;&#x2640;&#xfe0f;','&#x1f481;&#x200d;&#x2642;&#xfe0f;','&#x1f482;&#x200d;&#x2640;&#xfe0f;','&#x1f9dc;&#x200d;&#x2640;&#xfe0f;','&#x1f9dc;&#x200d;&#x2642;&#xfe0f;','&#x1f482;&#x200d;&#x2642;&#xfe0f;','&#x1f486;&#x200d;&#x2640;&#xfe0f;','&#x1f64b;&#x200d;&#x2642;&#xfe0f;','&#x1f64b;&#x200d;&#x2640;&#xfe0f;','&#x1f486;&#x200d;&#x2642;&#xfe0f;','&#x1f487;&#x200d;&#x2640;&#xfe0f;','&#x1f487;&#x200d;&#x2642;&#xfe0f;','&#x1f645;&#x200d;&#x2640;&#xfe0f;','&#x1f645;&#x200d;&#x2642;&#xfe0f;','&#x1f9dd;&#x200d;&#x2640;&#xfe0f;','&#x1f9dd;&#x200d;&#x2642;&#xfe0f;','&#x1f9de;&#x200d;&#x2640;&#xfe0f;','&#x1f9de;&#x200d;&#x2642;&#xfe0f;','&#x1f9df;&#x200d;&#x2640;&#xfe0f;','&#x1f9df;&#x200d;&#x2642;&#xfe0f;','&#x1f3f4;&#x200d;&#x2620;&#xfe0f;','&#x1f647;&#x200d;&#x2642;&#xfe0f;','&#x1f646;&#x200d;&#x2640;&#xfe0f;','&#x1f646;&#x200d;&#x2642;&#xfe0f;','&#x1f3ca;&#x200d;&#x2642;&#xfe0f;','&#x1f3ca;&#x200d;&#x2640;&#xfe0f;','&#x1f3c4;&#x200d;&#x2642;&#xfe0f;','&#x1f3c4;&#x200d;&#x2640;&#xfe0f;','&#x1f3c3;&#x200d;&#x2642;&#xfe0f;','&#x1f3c3;&#x200d;&#x2640;&#xfe0f;','&#x1f468;&#x200d;&#x1f3a8;','&#x1f469;&#x200d;&#x1f373;','&#x1f469;&#x200d;&#x1f393;','&#x1f469;&#x200d;&#x1f3a4;','&#x1f469;&#x200d;&#x1f3a8;','&#x1f469;&#x200d;&#x1f3eb;','&#x1f469;&#x200d;&#x1f3ed;','&#x1f468;&#x200d;&#x1f4bb;','&#x1f468;&#x200d;&#x1f692;','&#x1f469;&#x200d;&#x1f466;','&#x1f468;&#x200d;&#x1f467;','&#x1f468;&#x200d;&#x1f9b0;','&#x1f468;&#x200d;&#x1f9b1;','&#x1f469;&#x200d;&#x1f4bb;','&#x1f469;&#x200d;&#x1f4bc;','&#x1f469;&#x200d;&#x1f527;','&#x1f469;&#x200d;&#x1f52c;','&#x1f469;&#x200d;&#x1f680;','&#x1f469;&#x200d;&#x1f692;','&#x1f469;&#x200d;&#x1f9b0;','&#x1f469;&#x200d;&#x1f9b1;','&#x1f441;&#x200d;&#x1f5e8;','&#x1f468;&#x200d;&#x1f9b2;','&#x1f468;&#x200d;&#x1f9b3;','&#x1f468;&#x200d;&#x1f466;','&#x1f468;&#x200d;&#x1f3a4;','&#x1f469;&#x200d;&#x1f9b2;','&#x1f469;&#x200d;&#x1f9b3;','&#x1f468;&#x200d;&#x1f393;','&#x1f468;&#x200d;&#x1f373;','&#x1f468;&#x200d;&#x1f33e;','&#x1f468;&#x200d;&#x1f4bc;','&#x1f468;&#x200d;&#x1f527;','&#x1f468;&#x200d;&#x1f52c;','&#x1f468;&#x200d;&#x1f680;','&#x1f468;&#x200d;&#x1f3ed;','&#x1f469;&#x200d;&#x1f33e;','&#x1f468;&#x200d;&#x1f3eb;','&#x1f469;&#x200d;&#x1f467;','&#x1f446;&#x1f3ff;','&#x1f447;&#x1f3fb;','&#x1f477;&#x1f3fc;','&#x1f447;&#x1f3fc;','&#x1f447;&#x1f3fd;','&#x1f477;&#x1f3fd;','&#x1f447;&#x1f3fe;','&#x1f447;&#x1f3ff;','&#x1f477;&#x1f3fe;','&#x1f448;&#x1f3fb;','&#x1f448;&#x1f3fc;','&#x1f477;&#x1f3ff;','&#x1f448;&#x1f3fd;','&#x1f448;&#x1f3fe;','&#x1f478;&#x1f3fb;','&#x1f478;&#x1f3fc;','&#x1f478;&#x1f3fd;','&#x1f478;&#x1f3fe;','&#x1f478;&#x1f3ff;','&#x1f47c;&#x1f3fb;','&#x1f47c;&#x1f3fc;','&#x1f47c;&#x1f3fd;','&#x1f47c;&#x1f3fe;','&#x1f47c;&#x1f3ff;','&#x1f448;&#x1f3ff;','&#x1f449;&#x1f3fb;','&#x1f481;&#x1f3fb;','&#x1f449;&#x1f3fc;','&#x1f449;&#x1f3fd;','&#x1f481;&#x1f3fc;','&#x1f449;&#x1f3fe;','&#x1f449;&#x1f3ff;','&#x1f481;&#x1f3fd;','&#x1f44a;&#x1f3fb;','&#x1f44a;&#x1f3fc;','&#x1f481;&#x1f3fe;','&#x1f44a;&#x1f3fd;','&#x1f44a;&#x1f3fe;','&#x1f481;&#x1f3ff;','&#x1f44a;&#x1f3ff;','&#x1f44b;&#x1f3fb;','&#x1f44b;&#x1f3fc;','&#x1f44b;&#x1f3fd;','&#x1f482;&#x1f3fb;','&#x1f44b;&#x1f3fe;','&#x1f44b;&#x1f3ff;','&#x1f482;&#x1f3fc;','&#x1f44c;&#x1f3fb;','&#x1f44c;&#x1f3fc;','&#x1f482;&#x1f3fd;','&#x1f44c;&#x1f3fd;','&#x1f44c;&#x1f3fe;','&#x1f482;&#x1f3fe;','&#x1f44c;&#x1f3ff;','&#x1f44d;&#x1f3fb;','&#x1f482;&#x1f3ff;','&#x1f44d;&#x1f3fc;','&#x1f44d;&#x1f3fd;','&#x1f483;&#x1f3fb;','&#x1f483;&#x1f3fc;','&#x1f483;&#x1f3fd;','&#x1f483;&#x1f3fe;','&#x1f483;&#x1f3ff;','&#x1f485;&#x1f3fb;','&#x1f485;&#x1f3fc;','&#x1f485;&#x1f3fd;','&#x1f485;&#x1f3fe;','&#x1f485;&#x1f3ff;','&#x1f44d;&#x1f3fe;','&#x1f44d;&#x1f3ff;','&#x1f486;&#x1f3fb;','&#x1f44e;&#x1f3fb;','&#x1f44e;&#x1f3fc;','&#x1f486;&#x1f3fc;','&#x1f44e;&#x1f3fd;','&#x1f44e;&#x1f3fe;','&#x1f486;&#x1f3fd;','&#x1f44e;&#x1f3ff;','&#x1f44f;&#x1f3fb;','&#x1f486;&#x1f3fe;','&#x1f44f;&#x1f3fc;','&#x1f44f;&#x1f3fd;','&#x1f486;&#x1f3ff;','&#x1f44f;&#x1f3fe;','&#x1f44f;&#x1f3ff;','&#x1f450;&#x1f3fb;','&#x1f450;&#x1f3fc;','&#x1f487;&#x1f3fb;','&#x1f450;&#x1f3fd;','&#x1f450;&#x1f3fe;','&#x1f487;&#x1f3fc;','&#x1f450;&#x1f3ff;','&#x1f466;&#x1f3fb;','&#x1f487;&#x1f3fd;','&#x1f466;&#x1f3fc;','&#x1f466;&#x1f3fd;','&#x1f487;&#x1f3fe;','&#x1f466;&#x1f3fe;','&#x1f466;&#x1f3ff;','&#x1f487;&#x1f3ff;','&#x1f467;&#x1f3fb;','&#x1f467;&#x1f3fc;','&#x1f4aa;&#x1f3fb;','&#x1f4aa;&#x1f3fc;','&#x1f4aa;&#x1f3fd;','&#x1f4aa;&#x1f3fe;','&#x1f4aa;&#x1f3ff;','&#x1f467;&#x1f3fd;','&#x1f467;&#x1f3fe;','&#x1f574;&#x1f3fb;','&#x1f467;&#x1f3ff;','&#x1f1ea;&#x1f1ea;','&#x1f574;&#x1f3fc;','&#x1f1ea;&#x1f1ec;','&#x1f1ea;&#x1f1ed;','&#x1f574;&#x1f3fd;','&#x1f1ea;&#x1f1f7;','&#x1f1ea;&#x1f1f8;','&#x1f574;&#x1f3fe;','&#x1f1ea;&#x1f1f9;','&#x1f1ea;&#x1f1fa;','&#x1f574;&#x1f3ff;','&#x1f1eb;&#x1f1ee;','&#x1f1eb;&#x1f1ef;','&#x1f1eb;&#x1f1f0;','&#x1f1eb;&#x1f1f2;','&#x1f575;&#x1f3fb;','&#x1f1eb;&#x1f1f4;','&#x1f1eb;&#x1f1f7;','&#x1f575;&#x1f3fc;','&#x1f1ec;&#x1f1e6;','&#x1f1ec;&#x1f1e7;','&#x1f575;&#x1f3fd;','&#x1f1ec;&#x1f1e9;','&#x1f1ec;&#x1f1ea;','&#x1f575;&#x1f3fe;','&#x1f1ec;&#x1f1eb;','&#x1f1ec;&#x1f1ec;','&#x1f575;&#x1f3ff;','&#x1f1ec;&#x1f1ed;','&#x1f468;&#x1f3fb;','&#x1f57a;&#x1f3fb;','&#x1f57a;&#x1f3fc;','&#x1f57a;&#x1f3fd;','&#x1f57a;&#x1f3fe;','&#x1f57a;&#x1f3ff;','&#x1f590;&#x1f3fb;','&#x1f590;&#x1f3fc;','&#x1f590;&#x1f3fd;','&#x1f590;&#x1f3fe;','&#x1f590;&#x1f3ff;','&#x1f595;&#x1f3fb;','&#x1f595;&#x1f3fc;','&#x1f595;&#x1f3fd;','&#x1f595;&#x1f3fe;','&#x1f595;&#x1f3ff;','&#x1f596;&#x1f3fb;','&#x1f596;&#x1f3fc;','&#x1f596;&#x1f3fd;','&#x1f596;&#x1f3fe;','&#x1f596;&#x1f3ff;','&#x1f1ec;&#x1f1ee;','&#x1f1ec;&#x1f1f1;','&#x1f645;&#x1f3fb;','&#x1f1ec;&#x1f1f2;','&#x1f1ec;&#x1f1f3;','&#x1f645;&#x1f3fc;','&#x1f1ec;&#x1f1f5;','&#x1f1ec;&#x1f1f6;','&#x1f645;&#x1f3fd;','&#x1f1ec;&#x1f1f7;','&#x1f1ec;&#x1f1f8;','&#x1f645;&#x1f3fe;','&#x1f1ec;&#x1f1f9;','&#x1f1ec;&#x1f1fa;','&#x1f645;&#x1f3ff;','&#x1f1ec;&#x1f1fc;','&#x1f1ec;&#x1f1fe;','&#x1f1ed;&#x1f1f0;','&#x1f1ed;&#x1f1f2;','&#x1f646;&#x1f3fb;','&#x1f1ed;&#x1f1f3;','&#x1f1ed;&#x1f1f7;','&#x1f646;&#x1f3fc;','&#x1f1ed;&#x1f1f9;','&#x1f1ed;&#x1f1fa;','&#x1f646;&#x1f3fd;','&#x1f1ee;&#x1f1e8;','&#x1f1ee;&#x1f1e9;','&#x1f646;&#x1f3fe;','&#x1f468;&#x1f3fc;','&#x1f1ee;&#x1f1ea;','&#x1f646;&#x1f3ff;','&#x1f1ee;&#x1f1f1;','&#x1f1ee;&#x1f1f2;','&#x1f1ee;&#x1f1f3;','&#x1f1ee;&#x1f1f4;','&#x1f647;&#x1f3fb;','&#x1f1ee;&#x1f1f6;','&#x1f1ee;&#x1f1f7;','&#x1f647;&#x1f3fc;','&#x1f1ee;&#x1f1f8;','&#x1f1ee;&#x1f1f9;','&#x1f647;&#x1f3fd;','&#x1f1ef;&#x1f1ea;','&#x1f1ef;&#x1f1f2;','&#x1f647;&#x1f3fe;','&#x1f1ef;&#x1f1f4;','&#x1f1ef;&#x1f1f5;','&#x1f647;&#x1f3ff;','&#x1f1f0;&#x1f1ea;','&#x1f1f0;&#x1f1ec;','&#x1f1f0;&#x1f1ed;','&#x1f1f0;&#x1f1ee;','&#x1f64b;&#x1f3fb;','&#x1f1f0;&#x1f1f2;','&#x1f1f0;&#x1f1f3;','&#x1f64b;&#x1f3fc;','&#x1f1f0;&#x1f1f5;','&#x1f468;&#x1f3fd;','&#x1f64b;&#x1f3fd;','&#x1f1f0;&#x1f1f7;','&#x1f1f0;&#x1f1fc;','&#x1f64b;&#x1f3fe;','&#x1f1f0;&#x1f1fe;','&#x1f1f0;&#x1f1ff;','&#x1f64b;&#x1f3ff;','&#x1f1f1;&#x1f1e6;','&#x1f1f1;&#x1f1e7;','&#x1f64c;&#x1f3fb;','&#x1f64c;&#x1f3fc;','&#x1f64c;&#x1f3fd;','&#x1f64c;&#x1f3fe;','&#x1f64c;&#x1f3ff;','&#x1f1f1;&#x1f1e8;','&#x1f1f1;&#x1f1ee;','&#x1f64d;&#x1f3fb;','&#x1f1f1;&#x1f1f0;','&#x1f1f1;&#x1f1f7;','&#x1f64d;&#x1f3fc;','&#x1f1f1;&#x1f1f8;','&#x1f1f1;&#x1f1f9;','&#x1f64d;&#x1f3fd;','&#x1f1f1;&#x1f1fa;','&#x1f1f1;&#x1f1fb;','&#x1f64d;&#x1f3fe;','&#x1f1f1;&#x1f1fe;','&#x1f1f2;&#x1f1e6;','&#x1f64d;&#x1f3ff;','&#x1f1f2;&#x1f1e8;','&#x1f1f2;&#x1f1e9;','&#x1f1f2;&#x1f1ea;','&#x1f1f2;&#x1f1eb;','&#x1f64e;&#x1f3fb;','&#x1f468;&#x1f3fe;','&#x1f1f2;&#x1f1ec;','&#x1f64e;&#x1f3fc;','&#x1f1f2;&#x1f1ed;','&#x1f1f2;&#x1f1f0;','&#x1f64e;&#x1f3fd;','&#x1f1f2;&#x1f1f1;','&#x1f1f2;&#x1f1f2;','&#x1f64e;&#x1f3fe;','&#x1f1f2;&#x1f1f3;','&#x1f1f2;&#x1f1f4;','&#x1f64e;&#x1f3ff;','&#x1f1f2;&#x1f1f5;','&#x1f1f2;&#x1f1f6;','&#x1f64f;&#x1f3fb;','&#x1f64f;&#x1f3fc;','&#x1f64f;&#x1f3fd;','&#x1f64f;&#x1f3fe;','&#x1f64f;&#x1f3ff;','&#x1f1f2;&#x1f1f7;','&#x1f1f2;&#x1f1f8;','&#x1f6a3;&#x1f3fb;','&#x1f1f2;&#x1f1f9;','&#x1f1f2;&#x1f1fa;','&#x1f6a3;&#x1f3fc;','&#x1f1f2;&#x1f1fb;','&#x1f1f2;&#x1f1fc;','&#x1f6a3;&#x1f3fd;','&#x1f1f2;&#x1f1fd;','&#x1f1f2;&#x1f1fe;','&#x1f6a3;&#x1f3fe;','&#x1f1f2;&#x1f1ff;','&#x1f1f3;&#x1f1e6;','&#x1f6a3;&#x1f3ff;','&#x1f1f3;&#x1f1e8;','&#x1f468;&#x1f3ff;','&#x1f1f3;&#x1f1ea;','&#x1f1f3;&#x1f1eb;','&#x1f6b4;&#x1f3fb;','&#x1f1f3;&#x1f1ec;','&#x1f1f3;&#x1f1ee;','&#x1f6b4;&#x1f3fc;','&#x1f1f3;&#x1f1f1;','&#x1f1f3;&#x1f1f4;','&#x1f6b4;&#x1f3fd;','&#x1f1f3;&#x1f1f5;','&#x1f1f3;&#x1f1f7;','&#x1f6b4;&#x1f3fe;','&#x1f1f3;&#x1f1fa;','&#x1f1f3;&#x1f1ff;','&#x1f6b4;&#x1f3ff;','&#x1f1f4;&#x1f1f2;','&#x1f1f5;&#x1f1e6;','&#x1f1f5;&#x1f1ea;','&#x1f1f5;&#x1f1eb;','&#x1f6b5;&#x1f3fb;','&#x1f1f5;&#x1f1ec;','&#x1f1f5;&#x1f1ed;','&#x1f6b5;&#x1f3fc;','&#x1f1f5;&#x1f1f0;','&#x1f1f5;&#x1f1f1;','&#x1f6b5;&#x1f3fd;','&#x1f1f5;&#x1f1f2;','&#x1f1f5;&#x1f1f3;','&#x1f6b5;&#x1f3fe;','&#x1f1f5;&#x1f1f7;','&#x1f1f5;&#x1f1f8;','&#x1f6b5;&#x1f3ff;','&#x1f1f5;&#x1f1f9;','&#x1f1f5;&#x1f1fc;','&#x1f1f5;&#x1f1fe;','&#x1f1f6;&#x1f1e6;','&#x1f6b6;&#x1f3fb;','&#x1f1f7;&#x1f1ea;','&#x1f1f7;&#x1f1f4;','&#x1f6b6;&#x1f3fc;','&#x1f1f7;&#x1f1f8;','&#x1f1f7;&#x1f1fa;','&#x1f6b6;&#x1f3fd;','&#x1f1f7;&#x1f1fc;','&#x1f1f8;&#x1f1e6;','&#x1f6b6;&#x1f3fe;','&#x1f1f8;&#x1f1e7;','&#x1f1f8;&#x1f1e8;','&#x1f6b6;&#x1f3ff;','&#x1f1f8;&#x1f1e9;','&#x1f1f8;&#x1f1ea;','&#x1f6c0;&#x1f3fb;','&#x1f6c0;&#x1f3fc;','&#x1f6c0;&#x1f3fd;','&#x1f6c0;&#x1f3fe;','&#x1f6c0;&#x1f3ff;','&#x1f6cc;&#x1f3fb;','&#x1f6cc;&#x1f3fc;','&#x1f6cc;&#x1f3fd;','&#x1f6cc;&#x1f3fe;','&#x1f6cc;&#x1f3ff;','&#x1f918;&#x1f3fb;','&#x1f918;&#x1f3fc;','&#x1f918;&#x1f3fd;','&#x1f918;&#x1f3fe;','&#x1f918;&#x1f3ff;','&#x1f919;&#x1f3fb;','&#x1f919;&#x1f3fc;','&#x1f919;&#x1f3fd;','&#x1f919;&#x1f3fe;','&#x1f919;&#x1f3ff;','&#x1f91a;&#x1f3fb;','&#x1f91a;&#x1f3fc;','&#x1f91a;&#x1f3fd;','&#x1f91a;&#x1f3fe;','&#x1f91a;&#x1f3ff;','&#x1f91b;&#x1f3fb;','&#x1f91b;&#x1f3fc;','&#x1f91b;&#x1f3fd;','&#x1f91b;&#x1f3fe;','&#x1f91b;&#x1f3ff;','&#x1f91c;&#x1f3fb;','&#x1f91c;&#x1f3fc;','&#x1f91c;&#x1f3fd;','&#x1f91c;&#x1f3fe;','&#x1f91c;&#x1f3ff;','&#x1f91e;&#x1f3fb;','&#x1f91e;&#x1f3fc;','&#x1f91e;&#x1f3fd;','&#x1f91e;&#x1f3fe;','&#x1f91e;&#x1f3ff;','&#x1f91f;&#x1f3fb;','&#x1f91f;&#x1f3fc;','&#x1f91f;&#x1f3fd;','&#x1f91f;&#x1f3fe;','&#x1f91f;&#x1f3ff;','&#x1f1f8;&#x1f1ec;','&#x1f1f8;&#x1f1ed;','&#x1f926;&#x1f3fb;','&#x1f1f8;&#x1f1ee;','&#x1f1f8;&#x1f1ef;','&#x1f926;&#x1f3fc;','&#x1f1f8;&#x1f1f0;','&#x1f1e6;&#x1f1e9;','&#x1f926;&#x1f3fd;','&#x1f1f8;&#x1f1f2;','&#x1f1f8;&#x1f1f3;','&#x1f926;&#x1f3fe;','&#x1f1f8;&#x1f1f4;','&#x1f1f8;&#x1f1f7;','&#x1f926;&#x1f3ff;','&#x1f1f8;&#x1f1f8;','&#x1f1f8;&#x1f1f9;','&#x1f930;&#x1f3fb;','&#x1f930;&#x1f3fc;','&#x1f930;&#x1f3fd;','&#x1f930;&#x1f3fe;','&#x1f930;&#x1f3ff;','&#x1f931;&#x1f3fb;','&#x1f931;&#x1f3fc;','&#x1f931;&#x1f3fd;','&#x1f931;&#x1f3fe;','&#x1f931;&#x1f3ff;','&#x1f932;&#x1f3fb;','&#x1f932;&#x1f3fc;','&#x1f932;&#x1f3fd;','&#x1f932;&#x1f3fe;','&#x1f932;&#x1f3ff;','&#x1f933;&#x1f3fb;','&#x1f933;&#x1f3fc;','&#x1f933;&#x1f3fd;','&#x1f933;&#x1f3fe;','&#x1f933;&#x1f3ff;','&#x1f934;&#x1f3fb;','&#x1f934;&#x1f3fc;','&#x1f934;&#x1f3fd;','&#x1f934;&#x1f3fe;','&#x1f934;&#x1f3ff;','&#x1f1f8;&#x1f1fb;','&#x1f1f8;&#x1f1fd;','&#x1f935;&#x1f3fb;','&#x1f1f8;&#x1f1fe;','&#x1f1f8;&#x1f1ff;','&#x1f935;&#x1f3fc;','&#x1f1f9;&#x1f1e6;','&#x1f1f9;&#x1f1e8;','&#x1f935;&#x1f3fd;','&#x1f1f9;&#x1f1e9;','&#x1f1f9;&#x1f1eb;','&#x1f935;&#x1f3fe;','&#x1f1f9;&#x1f1ec;','&#x1f469;&#x1f3fb;','&#x1f935;&#x1f3ff;','&#x1f1f9;&#x1f1ed;','&#x1f1f9;&#x1f1ef;','&#x1f936;&#x1f3fb;','&#x1f936;&#x1f3fc;','&#x1f936;&#x1f3fd;','&#x1f936;&#x1f3fe;','&#x1f936;&#x1f3ff;','&#x1f1f9;&#x1f1f0;','&#x1f1f9;&#x1f1f1;','&#x1f937;&#x1f3fb;','&#x1f1f9;&#x1f1f2;','&#x1f1f9;&#x1f1f3;','&#x1f937;&#x1f3fc;','&#x1f1f9;&#x1f1f4;','&#x1f1f9;&#x1f1f7;','&#x1f937;&#x1f3fd;','&#x1f1f9;&#x1f1f9;','&#x1f1f9;&#x1f1fb;','&#x1f937;&#x1f3fe;','&#x1f1f9;&#x1f1fc;','&#x1f1f9;&#x1f1ff;','&#x1f937;&#x1f3ff;','&#x1f1fa;&#x1f1e6;','&#x1f1fa;&#x1f1ec;','&#x1f1fa;&#x1f1f2;','&#x1f1fa;&#x1f1f3;','&#x1f938;&#x1f3fb;','&#x1f1fa;&#x1f1f8;','&#x1f1fa;&#x1f1fe;','&#x1f938;&#x1f3fc;','&#x1f1fa;&#x1f1ff;','&#x1f1fb;&#x1f1e6;','&#x1f938;&#x1f3fd;','&#x1f469;&#x1f3fc;','&#x1f1fb;&#x1f1e8;','&#x1f938;&#x1f3fe;','&#x1f1fb;&#x1f1ea;','&#x1f1fb;&#x1f1ec;','&#x1f938;&#x1f3ff;','&#x1f1fb;&#x1f1ee;','&#x1f1fb;&#x1f1f3;','&#x1f1fb;&#x1f1fa;','&#x1f1fc;&#x1f1eb;','&#x1f939;&#x1f3fb;','&#x1f1fc;&#x1f1f8;','&#x1f1fd;&#x1f1f0;','&#x1f939;&#x1f3fc;','&#x1f1fe;&#x1f1ea;','&#x1f1fe;&#x1f1f9;','&#x1f939;&#x1f3fd;','&#x1f1ff;&#x1f1e6;','&#x1f1ff;&#x1f1f2;','&#x1f939;&#x1f3fe;','&#x1f1ff;&#x1f1fc;','&#x1f385;&#x1f3fb;','&#x1f939;&#x1f3ff;','&#x1f385;&#x1f3fc;','&#x1f385;&#x1f3fd;','&#x1f385;&#x1f3fe;','&#x1f385;&#x1f3ff;','&#x1f3c2;&#x1f3fb;','&#x1f469;&#x1f3fd;','&#x1f93d;&#x1f3fb;','&#x1f3c2;&#x1f3fc;','&#x1f3c2;&#x1f3fd;','&#x1f93d;&#x1f3fc;','&#x1f3c2;&#x1f3fe;','&#x1f3c2;&#x1f3ff;','&#x1f93d;&#x1f3fd;','&#x1f1e6;&#x1f1e8;','&#x1f1e6;&#x1f1ea;','&#x1f93d;&#x1f3fe;','&#x1f3c3;&#x1f3fb;','&#x1f1e6;&#x1f1eb;','&#x1f93d;&#x1f3ff;','&#x1f1e6;&#x1f1ec;','&#x1f3c3;&#x1f3fc;','&#x1f1e6;&#x1f1ee;','&#x1f1e6;&#x1f1f1;','&#x1f93e;&#x1f3fb;','&#x1f3c3;&#x1f3fd;','&#x1f1e6;&#x1f1f2;','&#x1f93e;&#x1f3fc;','&#x1f1e6;&#x1f1f4;','&#x1f3c3;&#x1f3fe;','&#x1f93e;&#x1f3fd;','&#x1f1e6;&#x1f1f6;','&#x1f1e6;&#x1f1f7;','&#x1f93e;&#x1f3fe;','&#x1f3c3;&#x1f3ff;','&#x1f1e6;&#x1f1f8;','&#x1f93e;&#x1f3ff;','&#x1f469;&#x1f3fe;','&#x1f1e6;&#x1f1f9;','&#x1f9b5;&#x1f3fb;','&#x1f9b5;&#x1f3fc;','&#x1f9b5;&#x1f3fd;','&#x1f9b5;&#x1f3fe;','&#x1f9b5;&#x1f3ff;','&#x1f9b6;&#x1f3fb;','&#x1f9b6;&#x1f3fc;','&#x1f9b6;&#x1f3fd;','&#x1f9b6;&#x1f3fe;','&#x1f9b6;&#x1f3ff;','&#x1f1e6;&#x1f1fa;','&#x1f1e6;&#x1f1fc;','&#x1f9b8;&#x1f3fb;','&#x1f3c4;&#x1f3fb;','&#x1f1e6;&#x1f1fd;','&#x1f9b8;&#x1f3fc;','&#x1f1e6;&#x1f1ff;','&#x1f3c4;&#x1f3fc;','&#x1f9b8;&#x1f3fd;','&#x1f1e7;&#x1f1e6;','&#x1f1e7;&#x1f1e7;','&#x1f9b8;&#x1f3fe;','&#x1f3c4;&#x1f3fd;','&#x1f1e7;&#x1f1e9;','&#x1f9b8;&#x1f3ff;','&#x1f1e7;&#x1f1ea;','&#x1f3c4;&#x1f3fe;','&#x1f1e7;&#x1f1eb;','&#x1f1e7;&#x1f1ec;','&#x1f9b9;&#x1f3fb;','&#x1f3c4;&#x1f3ff;','&#x1f1e7;&#x1f1ed;','&#x1f9b9;&#x1f3fc;','&#x1f1e7;&#x1f1ee;','&#x1f3c7;&#x1f3fb;','&#x1f9b9;&#x1f3fd;','&#x1f3c7;&#x1f3fc;','&#x1f469;&#x1f3ff;','&#x1f9b9;&#x1f3fe;','&#x1f3c7;&#x1f3fd;','&#x1f3c7;&#x1f3fe;','&#x1f9b9;&#x1f3ff;','&#x1f3c7;&#x1f3ff;','&#x1f1e7;&#x1f1ef;','&#x1f9d1;&#x1f3fb;','&#x1f9d1;&#x1f3fc;','&#x1f9d1;&#x1f3fd;','&#x1f9d1;&#x1f3fe;','&#x1f9d1;&#x1f3ff;','&#x1f9d2;&#x1f3fb;','&#x1f9d2;&#x1f3fc;','&#x1f9d2;&#x1f3fd;','&#x1f9d2;&#x1f3fe;','&#x1f9d2;&#x1f3ff;','&#x1f9d3;&#x1f3fb;','&#x1f9d3;&#x1f3fc;','&#x1f9d3;&#x1f3fd;','&#x1f9d3;&#x1f3fe;','&#x1f9d3;&#x1f3ff;','&#x1f9d4;&#x1f3fb;','&#x1f9d4;&#x1f3fc;','&#x1f9d4;&#x1f3fd;','&#x1f9d4;&#x1f3fe;','&#x1f9d4;&#x1f3ff;','&#x1f9d5;&#x1f3fb;','&#x1f9d5;&#x1f3fc;','&#x1f9d5;&#x1f3fd;','&#x1f9d5;&#x1f3fe;','&#x1f9d5;&#x1f3ff;','&#x1f1e7;&#x1f1f1;','&#x1f3ca;&#x1f3fb;','&#x1f9d6;&#x1f3fb;','&#x1f1e7;&#x1f1f2;','&#x1f1e7;&#x1f1f3;','&#x1f9d6;&#x1f3fc;','&#x1f3ca;&#x1f3fc;','&#x1f1e7;&#x1f1f4;','&#x1f9d6;&#x1f3fd;','&#x1f1e7;&#x1f1f6;','&#x1f3ca;&#x1f3fd;','&#x1f9d6;&#x1f3fe;','&#x1f1e7;&#x1f1f7;','&#x1f1e7;&#x1f1f8;','&#x1f9d6;&#x1f3ff;','&#x1f3ca;&#x1f3fe;','&#x1f1e7;&#x1f1f9;','&#x1f1e7;&#x1f1fb;','&#x1f3ca;&#x1f3ff;','&#x1f9d7;&#x1f3fb;','&#x1f1e7;&#x1f1fc;','&#x1f1e7;&#x1f1fe;','&#x1f9d7;&#x1f3fc;','&#x1f1e7;&#x1f1ff;','&#x1f1e8;&#x1f1e6;','&#x1f9d7;&#x1f3fd;','&#x1f3cb;&#x1f3fb;','&#x1f1e8;&#x1f1e8;','&#x1f9d7;&#x1f3fe;','&#x1f1e8;&#x1f1e9;','&#x1f3cb;&#x1f3fc;','&#x1f9d7;&#x1f3ff;','&#x1f1e8;&#x1f1eb;','&#x1f1e8;&#x1f1ec;','&#x1f3cb;&#x1f3fd;','&#x1f1e8;&#x1f1ed;','&#x1f9d8;&#x1f3fb;','&#x1f1e8;&#x1f1ee;','&#x1f3cb;&#x1f3fe;','&#x1f9d8;&#x1f3fc;','&#x1f1e8;&#x1f1f0;','&#x1f1e8;&#x1f1f1;','&#x1f9d8;&#x1f3fd;','&#x1f3cb;&#x1f3ff;','&#x1f1e8;&#x1f1f2;','&#x1f9d8;&#x1f3fe;','&#x1f46e;&#x1f3fb;','&#x1f1e8;&#x1f1f3;','&#x1f9d8;&#x1f3ff;','&#x1f1e8;&#x1f1f4;','&#x1f46e;&#x1f3fc;','&#x1f1e8;&#x1f1f5;','&#x1f3cc;&#x1f3fb;','&#x1f9d9;&#x1f3fb;','&#x1f46e;&#x1f3fd;','&#x1f1e8;&#x1f1f7;','&#x1f9d9;&#x1f3fc;','&#x1f1e8;&#x1f1fa;','&#x1f46e;&#x1f3fe;','&#x1f9d9;&#x1f3fd;','&#x1f3cc;&#x1f3fc;','&#x1f1e8;&#x1f1fb;','&#x1f9d9;&#x1f3fe;','&#x1f46e;&#x1f3ff;','&#x1f1e8;&#x1f1fc;','&#x1f9d9;&#x1f3ff;','&#x1f3cc;&#x1f3fd;','&#x1f1e8;&#x1f1fd;','&#x1f1e8;&#x1f1fe;','&#x1f470;&#x1f3fb;','&#x1f9da;&#x1f3fb;','&#x1f470;&#x1f3fc;','&#x1f470;&#x1f3fd;','&#x1f9da;&#x1f3fc;','&#x1f470;&#x1f3fe;','&#x1f470;&#x1f3ff;','&#x1f9da;&#x1f3fd;','&#x1f3cc;&#x1f3fe;','&#x1f1e8;&#x1f1ff;','&#x1f9da;&#x1f3fe;','&#x1f471;&#x1f3fb;','&#x1f1e9;&#x1f1ea;','&#x1f9da;&#x1f3ff;','&#x1f3cc;&#x1f3ff;','&#x1f471;&#x1f3fc;','&#x1f1e9;&#x1f1ec;','&#x1f1e9;&#x1f1ef;','&#x1f9db;&#x1f3fb;','&#x1f471;&#x1f3fd;','&#x1f1e9;&#x1f1f0;','&#x1f9db;&#x1f3fc;','&#x1f1e9;&#x1f1f2;','&#x1f471;&#x1f3fe;','&#x1f9db;&#x1f3fd;','&#x1f1e9;&#x1f1f4;','&#x1f1e9;&#x1f1ff;','&#x1f9db;&#x1f3fe;','&#x1f471;&#x1f3ff;','&#x1f1ea;&#x1f1e6;','&#x1f9db;&#x1f3ff;','&#x1f1ea;&#x1f1e8;','&#x1f472;&#x1f3fb;','&#x1f472;&#x1f3fc;','&#x1f472;&#x1f3fd;','&#x1f9dc;&#x1f3fb;','&#x1f472;&#x1f3fe;','&#x1f472;&#x1f3ff;','&#x1f9dc;&#x1f3fc;','&#x1f442;&#x1f3fb;','&#x1f442;&#x1f3fc;','&#x1f9dc;&#x1f3fd;','&#x1f473;&#x1f3fb;','&#x1f442;&#x1f3fd;','&#x1f9dc;&#x1f3fe;','&#x1f442;&#x1f3fe;','&#x1f473;&#x1f3fc;','&#x1f9dc;&#x1f3ff;','&#x1f442;&#x1f3ff;','&#x1f443;&#x1f3fb;','&#x1f473;&#x1f3fd;','&#x1f443;&#x1f3fc;','&#x1f9dd;&#x1f3fb;','&#x1f443;&#x1f3fd;','&#x1f473;&#x1f3fe;','&#x1f9dd;&#x1f3fc;','&#x1f443;&#x1f3fe;','&#x1f443;&#x1f3ff;','&#x1f9dd;&#x1f3fd;','&#x1f473;&#x1f3ff;','&#x1f446;&#x1f3fb;','&#x1f9dd;&#x1f3fe;','&#x1f446;&#x1f3fc;','&#x1f474;&#x1f3fb;','&#x1f9dd;&#x1f3ff;','&#x1f474;&#x1f3fc;','&#x1f474;&#x1f3fd;','&#x1f474;&#x1f3fe;','&#x1f474;&#x1f3ff;','&#x1f475;&#x1f3fb;','&#x1f475;&#x1f3fc;','&#x1f475;&#x1f3fd;','&#x1f475;&#x1f3fe;','&#x1f475;&#x1f3ff;','&#x1f476;&#x1f3fb;','&#x1f476;&#x1f3fc;','&#x1f476;&#x1f3fd;','&#x1f476;&#x1f3fe;','&#x1f476;&#x1f3ff;','&#x1f446;&#x1f3fd;','&#x1f446;&#x1f3fe;','&#x1f477;&#x1f3fb;','&#x1f1f8;&#x1f1f1;','&#x270d;&#x1f3ff;','&#x26f9;&#x1f3fb;','&#x270d;&#x1f3fe;','&#x270d;&#x1f3fd;','&#x270d;&#x1f3fc;','&#x270d;&#x1f3fb;','&#x270c;&#x1f3ff;','&#x270c;&#x1f3fe;','&#x270c;&#x1f3fd;','&#x270c;&#x1f3fc;','&#x270c;&#x1f3fb;','&#x270b;&#x1f3ff;','&#x270b;&#x1f3fe;','&#x270b;&#x1f3fd;','&#x270b;&#x1f3fc;','&#x270b;&#x1f3fb;','&#x270a;&#x1f3ff;','&#x270a;&#x1f3fe;','&#x270a;&#x1f3fd;','&#x270a;&#x1f3fc;','&#x270a;&#x1f3fb;','&#x26f7;&#x1f3fd;','&#x26f7;&#x1f3fe;','&#x26f9;&#x1f3ff;','&#x261d;&#x1f3ff;','&#x261d;&#x1f3fe;','&#x26f9;&#x1f3fe;','&#x261d;&#x1f3fd;','&#x261d;&#x1f3fc;','&#x26f9;&#x1f3fd;','&#x261d;&#x1f3fb;','&#x26f7;&#x1f3ff;','&#x26f9;&#x1f3fc;','&#x26f7;&#x1f3fb;','&#x26f7;&#x1f3fc;','&#x34;&#x20e3;','&#x23;&#x20e3;','&#x30;&#x20e3;','&#x31;&#x20e3;','&#x32;&#x20e3;','&#x33;&#x20e3;','&#x2a;&#x20e3;','&#x35;&#x20e3;','&#x36;&#x20e3;','&#x37;&#x20e3;','&#x38;&#x20e3;','&#x39;&#x20e3;','&#x1f0cf;','&#x1f57a;','&#x1f587;','&#x1f58a;','&#x1f58b;','&#x1f58c;','&#x1f58d;','&#x1f004;','&#x1f1fe;','&#x1f1e6;','&#x1f170;','&#x1f171;','&#x1f590;','&#x1f1ff;','&#x1f201;','&#x1f202;','&#x1f3c4;','&#x1f3c5;','&#x1f595;','&#x1f3c6;','&#x1f21a;','&#x1f22f;','&#x1f232;','&#x1f233;','&#x1f596;','&#x1f5a4;','&#x1f5a5;','&#x1f5a8;','&#x1f5b1;','&#x1f5b2;','&#x1f5bc;','&#x1f5c2;','&#x1f5c3;','&#x1f5c4;','&#x1f5d1;','&#x1f5d2;','&#x1f5d3;','&#x1f5dc;','&#x1f5dd;','&#x1f5de;','&#x1f5e1;','&#x1f5e3;','&#x1f5e8;','&#x1f5ef;','&#x1f5f3;','&#x1f5fa;','&#x1f5fb;','&#x1f5fc;','&#x1f5fd;','&#x1f5fe;','&#x1f5ff;','&#x1f600;','&#x1f601;','&#x1f602;','&#x1f603;','&#x1f604;','&#x1f605;','&#x1f606;','&#x1f607;','&#x1f608;','&#x1f609;','&#x1f60a;','&#x1f60b;','&#x1f60c;','&#x1f60d;','&#x1f60e;','&#x1f60f;','&#x1f610;','&#x1f611;','&#x1f612;','&#x1f613;','&#x1f614;','&#x1f615;','&#x1f616;','&#x1f617;','&#x1f618;','&#x1f619;','&#x1f61a;','&#x1f61b;','&#x1f61c;','&#x1f61d;','&#x1f61e;','&#x1f61f;','&#x1f620;','&#x1f621;','&#x1f622;','&#x1f623;','&#x1f624;','&#x1f625;','&#x1f626;','&#x1f627;','&#x1f628;','&#x1f629;','&#x1f62a;','&#x1f62b;','&#x1f62c;','&#x1f62d;','&#x1f62e;','&#x1f62f;','&#x1f630;','&#x1f631;','&#x1f632;','&#x1f633;','&#x1f634;','&#x1f635;','&#x1f636;','&#x1f637;','&#x1f638;','&#x1f639;','&#x1f63a;','&#x1f63b;','&#x1f63c;','&#x1f63d;','&#x1f63e;','&#x1f63f;','&#x1f640;','&#x1f641;','&#x1f642;','&#x1f643;','&#x1f644;','&#x1f234;','&#x1f3c7;','&#x1f3c8;','&#x1f3c9;','&#x1f235;','&#x1f236;','&#x1f237;','&#x1f238;','&#x1f239;','&#x1f23a;','&#x1f250;','&#x1f251;','&#x1f300;','&#x1f301;','&#x1f302;','&#x1f303;','&#x1f304;','&#x1f645;','&#x1f305;','&#x1f306;','&#x1f307;','&#x1f308;','&#x1f3ca;','&#x1f309;','&#x1f30a;','&#x1f30b;','&#x1f30c;','&#x1f468;','&#x1f30d;','&#x1f30e;','&#x1f30f;','&#x1f310;','&#x1f311;','&#x1f312;','&#x1f313;','&#x1f646;','&#x1f314;','&#x1f315;','&#x1f316;','&#x1f317;','&#x1f318;','&#x1f319;','&#x1f3cb;','&#x1f31a;','&#x1f31b;','&#x1f31c;','&#x1f31d;','&#x1f31e;','&#x1f31f;','&#x1f320;','&#x1f321;','&#x1f324;','&#x1f325;','&#x1f647;','&#x1f648;','&#x1f649;','&#x1f64a;','&#x1f326;','&#x1f327;','&#x1f328;','&#x1f329;','&#x1f32a;','&#x1f32b;','&#x1f32c;','&#x1f3cc;','&#x1f3cd;','&#x1f3ce;','&#x1f3cf;','&#x1f3d0;','&#x1f3d1;','&#x1f3d2;','&#x1f3d3;','&#x1f3d4;','&#x1f3d5;','&#x1f64b;','&#x1f3d6;','&#x1f3d7;','&#x1f3d8;','&#x1f3d9;','&#x1f3da;','&#x1f64c;','&#x1f3db;','&#x1f3dc;','&#x1f3dd;','&#x1f3de;','&#x1f3df;','&#x1f3e0;','&#x1f3e1;','&#x1f3e2;','&#x1f3e3;','&#x1f3e4;','&#x1f3e5;','&#x1f3e6;','&#x1f3e7;','&#x1f3e8;','&#x1f3e9;','&#x1f3ea;','&#x1f3eb;','&#x1f64d;','&#x1f3ec;','&#x1f3ed;','&#x1f3ee;','&#x1f3ef;','&#x1f3f0;','&#x1f32d;','&#x1f3f3;','&#x1f32e;','&#x1f32f;','&#x1f330;','&#x1f331;','&#x1f3f4;','&#x1f3f5;','&#x1f3f7;','&#x1f3f8;','&#x1f3f9;','&#x1f3fa;','&#x1f64e;','&#x1f3fb;','&#x1f3fc;','&#x1f3fd;','&#x1f3fe;','&#x1f3ff;','&#x1f64f;','&#x1f680;','&#x1f681;','&#x1f682;','&#x1f683;','&#x1f684;','&#x1f685;','&#x1f686;','&#x1f687;','&#x1f688;','&#x1f689;','&#x1f68a;','&#x1f68b;','&#x1f68c;','&#x1f68d;','&#x1f68e;','&#x1f68f;','&#x1f690;','&#x1f691;','&#x1f692;','&#x1f693;','&#x1f694;','&#x1f695;','&#x1f696;','&#x1f697;','&#x1f698;','&#x1f699;','&#x1f69a;','&#x1f69b;','&#x1f69c;','&#x1f69d;','&#x1f69e;','&#x1f69f;','&#x1f6a0;','&#x1f6a1;','&#x1f6a2;','&#x1f400;','&#x1f401;','&#x1f402;','&#x1f403;','&#x1f404;','&#x1f405;','&#x1f406;','&#x1f407;','&#x1f408;','&#x1f409;','&#x1f40a;','&#x1f40b;','&#x1f40c;','&#x1f40d;','&#x1f40e;','&#x1f40f;','&#x1f410;','&#x1f6a3;','&#x1f6a4;','&#x1f6a5;','&#x1f6a6;','&#x1f6a7;','&#x1f6a8;','&#x1f6a9;','&#x1f6aa;','&#x1f6ab;','&#x1f6ac;','&#x1f6ad;','&#x1f6ae;','&#x1f6af;','&#x1f6b0;','&#x1f6b1;','&#x1f6b2;','&#x1f6b3;','&#x1f411;','&#x1f412;','&#x1f413;','&#x1f414;','&#x1f415;','&#x1f416;','&#x1f417;','&#x1f418;','&#x1f419;','&#x1f41a;','&#x1f41b;','&#x1f41c;','&#x1f41d;','&#x1f41e;','&#x1f41f;','&#x1f420;','&#x1f421;','&#x1f6b4;','&#x1f422;','&#x1f423;','&#x1f424;','&#x1f425;','&#x1f426;','&#x1f427;','&#x1f428;','&#x1f429;','&#x1f42a;','&#x1f42b;','&#x1f42c;','&#x1f42d;','&#x1f42e;','&#x1f42f;','&#x1f430;','&#x1f431;','&#x1f432;','&#x1f6b5;','&#x1f433;','&#x1f434;','&#x1f435;','&#x1f469;','&#x1f46a;','&#x1f46b;','&#x1f46c;','&#x1f46d;','&#x1f436;','&#x1f437;','&#x1f438;','&#x1f439;','&#x1f43a;','&#x1f43b;','&#x1f43c;','&#x1f43d;','&#x1f43e;','&#x1f6b6;','&#x1f6b7;','&#x1f6b8;','&#x1f6b9;','&#x1f6ba;','&#x1f6bb;','&#x1f6bc;','&#x1f6bd;','&#x1f6be;','&#x1f6bf;','&#x1f43f;','&#x1f440;','&#x1f332;','&#x1f441;','&#x1f333;','&#x1f6c0;','&#x1f6c1;','&#x1f6c2;','&#x1f6c3;','&#x1f6c4;','&#x1f6c5;','&#x1f6cb;','&#x1f334;','&#x1f335;','&#x1f336;','&#x1f46e;','&#x1f337;','&#x1f6cc;','&#x1f6cd;','&#x1f6ce;','&#x1f6cf;','&#x1f6d0;','&#x1f6d1;','&#x1f6d2;','&#x1f6e0;','&#x1f6e1;','&#x1f6e2;','&#x1f6e3;','&#x1f6e4;','&#x1f6e5;','&#x1f6e9;','&#x1f6eb;','&#x1f6ec;','&#x1f6f0;','&#x1f6f3;','&#x1f6f4;','&#x1f6f5;','&#x1f6f6;','&#x1f6f7;','&#x1f6f8;','&#x1f6f9;','&#x1f910;','&#x1f911;','&#x1f912;','&#x1f913;','&#x1f914;','&#x1f915;','&#x1f916;','&#x1f917;','&#x1f442;','&#x1f46f;','&#x1f338;','&#x1f339;','&#x1f33a;','&#x1f918;','&#x1f33b;','&#x1f33c;','&#x1f470;','&#x1f443;','&#x1f444;','&#x1f919;','&#x1f445;','&#x1f33d;','&#x1f33e;','&#x1f33f;','&#x1f340;','&#x1f91a;','&#x1f341;','&#x1f446;','&#x1f342;','&#x1f343;','&#x1f344;','&#x1f91b;','&#x1f345;','&#x1f346;','&#x1f447;','&#x1f347;','&#x1f348;','&#x1f91c;','&#x1f91d;','&#x1f471;','&#x1f349;','&#x1f34a;','&#x1f34b;','&#x1f448;','&#x1f91e;','&#x1f34c;','&#x1f472;','&#x1f34d;','&#x1f34e;','&#x1f34f;','&#x1f91f;','&#x1f920;','&#x1f921;','&#x1f922;','&#x1f923;','&#x1f924;','&#x1f925;','&#x1f350;','&#x1f449;','&#x1f351;','&#x1f352;','&#x1f353;','&#x1f354;','&#x1f355;','&#x1f44a;','&#x1f356;','&#x1f357;','&#x1f358;','&#x1f359;','&#x1f35a;','&#x1f44b;','&#x1f473;','&#x1f35b;','&#x1f35c;','&#x1f926;','&#x1f927;','&#x1f928;','&#x1f929;','&#x1f92a;','&#x1f92b;','&#x1f92c;','&#x1f92d;','&#x1f92e;','&#x1f92f;','&#x1f35d;','&#x1f35e;','&#x1f35f;','&#x1f474;','&#x1f44c;','&#x1f930;','&#x1f360;','&#x1f361;','&#x1f362;','&#x1f363;','&#x1f475;','&#x1f931;','&#x1f364;','&#x1f44d;','&#x1f365;','&#x1f366;','&#x1f367;','&#x1f932;','&#x1f476;','&#x1f368;','&#x1f369;','&#x1f44e;','&#x1f36a;','&#x1f933;','&#x1f36b;','&#x1f36c;','&#x1f36d;','&#x1f36e;','&#x1f44f;','&#x1f934;','&#x1f36f;','&#x1f370;','&#x1f371;','&#x1f372;','&#x1f373;','&#x1f450;','&#x1f451;','&#x1f452;','&#x1f477;','&#x1f453;','&#x1f454;','&#x1f455;','&#x1f456;','&#x1f457;','&#x1f478;','&#x1f479;','&#x1f47a;','&#x1f935;','&#x1f47b;','&#x1f458;','&#x1f459;','&#x1f45a;','&#x1f45b;','&#x1f936;','&#x1f45c;','&#x1f47c;','&#x1f47d;','&#x1f47e;','&#x1f47f;','&#x1f480;','&#x1f45d;','&#x1f45e;','&#x1f45f;','&#x1f460;','&#x1f461;','&#x1f462;','&#x1f463;','&#x1f464;','&#x1f465;','&#x1f374;','&#x1f375;','&#x1f937;','&#x1f376;','&#x1f377;','&#x1f378;','&#x1f466;','&#x1f379;','&#x1f37a;','&#x1f481;','&#x1f37b;','&#x1f37c;','&#x1f37d;','&#x1f467;','&#x1f37e;','&#x1f37f;','&#x1f380;','&#x1f381;','&#x1f382;','&#x1f383;','&#x1f938;','&#x1f384;','&#x1f1f5;','&#x1f17e;','&#x1f1f6;','&#x1f1f2;','&#x1f17f;','&#x1f385;','&#x1f482;','&#x1f386;','&#x1f387;','&#x1f388;','&#x1f389;','&#x1f38a;','&#x1f483;','&#x1f484;','&#x1f38b;','&#x1f38c;','&#x1f939;','&#x1f93a;','&#x1f38d;','&#x1f38e;','&#x1f93c;','&#x1f38f;','&#x1f485;','&#x1f390;','&#x1f391;','&#x1f392;','&#x1f393;','&#x1f396;','&#x1f397;','&#x1f399;','&#x1f39a;','&#x1f39b;','&#x1f39e;','&#x1f39f;','&#x1f3a0;','&#x1f3a1;','&#x1f3a2;','&#x1f3a3;','&#x1f93d;','&#x1f3a4;','&#x1f3a5;','&#x1f486;','&#x1f3a6;','&#x1f3a7;','&#x1f3a8;','&#x1f3a9;','&#x1f3aa;','&#x1f3ab;','&#x1f3ac;','&#x1f3ad;','&#x1f3ae;','&#x1f3af;','&#x1f3b0;','&#x1f3b1;','&#x1f3b2;','&#x1f3b3;','&#x1f93e;','&#x1f940;','&#x1f941;','&#x1f942;','&#x1f943;','&#x1f944;','&#x1f945;','&#x1f947;','&#x1f948;','&#x1f949;','&#x1f94a;','&#x1f94b;','&#x1f94c;','&#x1f94d;','&#x1f94e;','&#x1f94f;','&#x1f950;','&#x1f951;','&#x1f952;','&#x1f953;','&#x1f954;','&#x1f955;','&#x1f956;','&#x1f957;','&#x1f958;','&#x1f959;','&#x1f95a;','&#x1f95b;','&#x1f95c;','&#x1f95d;','&#x1f95e;','&#x1f95f;','&#x1f960;','&#x1f961;','&#x1f962;','&#x1f963;','&#x1f964;','&#x1f965;','&#x1f966;','&#x1f967;','&#x1f968;','&#x1f969;','&#x1f96a;','&#x1f96b;','&#x1f96c;','&#x1f96d;','&#x1f96e;','&#x1f96f;','&#x1f970;','&#x1f973;','&#x1f974;','&#x1f975;','&#x1f976;','&#x1f97a;','&#x1f97c;','&#x1f97d;','&#x1f97e;','&#x1f97f;','&#x1f980;','&#x1f981;','&#x1f982;','&#x1f983;','&#x1f984;','&#x1f985;','&#x1f986;','&#x1f987;','&#x1f988;','&#x1f989;','&#x1f98a;','&#x1f98b;','&#x1f98c;','&#x1f98d;','&#x1f98e;','&#x1f98f;','&#x1f990;','&#x1f991;','&#x1f992;','&#x1f993;','&#x1f994;','&#x1f995;','&#x1f996;','&#x1f997;','&#x1f998;','&#x1f999;','&#x1f99a;','&#x1f99b;','&#x1f99c;','&#x1f99d;','&#x1f99e;','&#x1f99f;','&#x1f9a0;','&#x1f9a1;','&#x1f9a2;','&#x1f9b4;','&#x1f3b4;','&#x1f3b5;','&#x1f3b6;','&#x1f487;','&#x1f488;','&#x1f9b5;','&#x1f489;','&#x1f48a;','&#x1f48b;','&#x1f48c;','&#x1f48d;','&#x1f9b6;','&#x1f9b7;','&#x1f48e;','&#x1f48f;','&#x1f490;','&#x1f491;','&#x1f492;','&#x1f493;','&#x1f494;','&#x1f495;','&#x1f496;','&#x1f497;','&#x1f498;','&#x1f499;','&#x1f49a;','&#x1f49b;','&#x1f49c;','&#x1f49d;','&#x1f49e;','&#x1f9b8;','&#x1f49f;','&#x1f4a0;','&#x1f4a1;','&#x1f4a2;','&#x1f4a3;','&#x1f4a4;','&#x1f4a5;','&#x1f4a6;','&#x1f4a7;','&#x1f4a8;','&#x1f4a9;','&#x1f3b7;','&#x1f3b8;','&#x1f3b9;','&#x1f3ba;','&#x1f3bb;','&#x1f4aa;','&#x1f9b9;','&#x1f9c0;','&#x1f9c1;','&#x1f9c2;','&#x1f9d0;','&#x1f4ab;','&#x1f4ac;','&#x1f4ad;','&#x1f4ae;','&#x1f4af;','&#x1f9d1;','&#x1f4b0;','&#x1f4b1;','&#x1f4b2;','&#x1f4b3;','&#x1f4b4;','&#x1f9d2;','&#x1f4b5;','&#x1f4b6;','&#x1f4b7;','&#x1f4b8;','&#x1f4b9;','&#x1f9d3;','&#x1f4ba;','&#x1f4bb;','&#x1f4bc;','&#x1f4bd;','&#x1f4be;','&#x1f9d4;','&#x1f4bf;','&#x1f4c0;','&#x1f4c1;','&#x1f4c2;','&#x1f4c3;','&#x1f9d5;','&#x1f4c4;','&#x1f4c5;','&#x1f4c6;','&#x1f4c7;','&#x1f4c8;','&#x1f4c9;','&#x1f4ca;','&#x1f4cb;','&#x1f4cc;','&#x1f4cd;','&#x1f4ce;','&#x1f4cf;','&#x1f4d0;','&#x1f4d1;','&#x1f4d2;','&#x1f4d3;','&#x1f4d4;','&#x1f9d6;','&#x1f4d5;','&#x1f4d6;','&#x1f4d7;','&#x1f4d8;','&#x1f4d9;','&#x1f4da;','&#x1f4db;','&#x1f4dc;','&#x1f4dd;','&#x1f4de;','&#x1f4df;','&#x1f4e0;','&#x1f4e1;','&#x1f4e2;','&#x1f4e3;','&#x1f4e4;','&#x1f4e5;','&#x1f9d7;','&#x1f4e6;','&#x1f4e7;','&#x1f4e8;','&#x1f4e9;','&#x1f4ea;','&#x1f4eb;','&#x1f4ec;','&#x1f4ed;','&#x1f4ee;','&#x1f4ef;','&#x1f4f0;','&#x1f4f1;','&#x1f4f2;','&#x1f4f3;','&#x1f4f4;','&#x1f4f5;','&#x1f4f6;','&#x1f9d8;','&#x1f4f7;','&#x1f4f8;','&#x1f4f9;','&#x1f4fa;','&#x1f4fb;','&#x1f4fc;','&#x1f4fd;','&#x1f4ff;','&#x1f500;','&#x1f501;','&#x1f502;','&#x1f503;','&#x1f504;','&#x1f505;','&#x1f506;','&#x1f507;','&#x1f508;','&#x1f9d9;','&#x1f509;','&#x1f50a;','&#x1f50b;','&#x1f50c;','&#x1f50d;','&#x1f50e;','&#x1f50f;','&#x1f510;','&#x1f511;','&#x1f512;','&#x1f513;','&#x1f514;','&#x1f515;','&#x1f516;','&#x1f517;','&#x1f518;','&#x1f519;','&#x1f9da;','&#x1f51a;','&#x1f51b;','&#x1f51c;','&#x1f51d;','&#x1f51e;','&#x1f51f;','&#x1f520;','&#x1f521;','&#x1f522;','&#x1f523;','&#x1f524;','&#x1f525;','&#x1f526;','&#x1f527;','&#x1f528;','&#x1f529;','&#x1f52a;','&#x1f9db;','&#x1f52b;','&#x1f52c;','&#x1f52d;','&#x1f52e;','&#x1f52f;','&#x1f530;','&#x1f531;','&#x1f532;','&#x1f533;','&#x1f534;','&#x1f535;','&#x1f536;','&#x1f537;','&#x1f538;','&#x1f539;','&#x1f53a;','&#x1f53b;','&#x1f9dc;','&#x1f53c;','&#x1f53d;','&#x1f549;','&#x1f54a;','&#x1f54b;','&#x1f54c;','&#x1f54d;','&#x1f54e;','&#x1f550;','&#x1f551;','&#x1f552;','&#x1f553;','&#x1f554;','&#x1f555;','&#x1f556;','&#x1f557;','&#x1f558;','&#x1f9dd;','&#x1f559;','&#x1f55a;','&#x1f9de;','&#x1f55b;','&#x1f55c;','&#x1f9df;','&#x1f9e0;','&#x1f9e1;','&#x1f9e2;','&#x1f9e3;','&#x1f9e4;','&#x1f9e5;','&#x1f9e6;','&#x1f9e7;','&#x1f9e8;','&#x1f9e9;','&#x1f9ea;','&#x1f9eb;','&#x1f9ec;','&#x1f9ed;','&#x1f9ee;','&#x1f9ef;','&#x1f9f0;','&#x1f9f1;','&#x1f9f2;','&#x1f9f3;','&#x1f9f4;','&#x1f9f5;','&#x1f9f6;','&#x1f9f7;','&#x1f9f8;','&#x1f9f9;','&#x1f9fa;','&#x1f9fb;','&#x1f9fc;','&#x1f9fd;','&#x1f9fe;','&#x1f9ff;','&#x1f55d;','&#x1f55e;','&#x1f55f;','&#x1f560;','&#x1f561;','&#x1f562;','&#x1f563;','&#x1f564;','&#x1f565;','&#x1f566;','&#x1f567;','&#x1f56f;','&#x1f570;','&#x1f573;','&#x1f3bc;','&#x1f3bd;','&#x1f3be;','&#x1f3bf;','&#x1f3c0;','&#x1f3c1;','&#x1f1e7;','&#x1f1ee;','&#x1f1ea;','&#x1f1f7;','&#x1f1f1;','&#x1f3c2;','&#x1f18e;','&#x1f191;','&#x1f1e8;','&#x1f1f9;','&#x1f1ef;','&#x1f574;','&#x1f192;','&#x1f1ec;','&#x1f193;','&#x1f1f3;','&#x1f194;','&#x1f1f4;','&#x1f1fa;','&#x1f1eb;','&#x1f195;','&#x1f196;','&#x1f197;','&#x1f1ed;','&#x1f3c3;','&#x1f198;','&#x1f1e9;','&#x1f1fb;','&#x1f1f0;','&#x1f575;','&#x1f576;','&#x1f577;','&#x1f578;','&#x1f579;','&#x1f199;','&#x1f1fc;','&#x1f19a;','&#x1f1fd;','&#x1f1f8;','&#x25ab;','&#x2626;','&#x262e;','&#x262f;','&#x2638;','&#x2639;','&#x263a;','&#x2640;','&#x2642;','&#x2648;','&#x2649;','&#x264a;','&#x264b;','&#x264c;','&#x264d;','&#x264e;','&#x264f;','&#x2650;','&#x2651;','&#x2652;','&#x2653;','&#x265f;','&#x2660;','&#x2663;','&#x2665;','&#x2666;','&#x2668;','&#x267b;','&#x267e;','&#x267f;','&#x2692;','&#x2693;','&#x2694;','&#x2695;','&#x2696;','&#x2697;','&#x2699;','&#x269b;','&#x269c;','&#x26a0;','&#x26a1;','&#x26aa;','&#x26ab;','&#x26b0;','&#x26b1;','&#x26bd;','&#x26be;','&#x26c4;','&#x26c5;','&#x26c8;','&#x26ce;','&#x26cf;','&#x26d1;','&#x26d3;','&#x26d4;','&#x26e9;','&#x26ea;','&#x26f0;','&#x26f1;','&#x26f2;','&#x26f3;','&#x26f4;','&#x26f5;','&#x2623;','&#x2622;','&#x2620;','&#x261d;','&#x2618;','&#x26f7;','&#x26f8;','&#x2615;','&#x2614;','&#x2611;','&#x260e;','&#x2604;','&#x2603;','&#x2602;','&#x2601;','&#x2600;','&#x25fe;','&#x25fd;','&#x25fc;','&#x25fb;','&#x25c0;','&#x25b6;','&#x262a;','&#x25aa;','&#x26f9;','&#x26fa;','&#x26fd;','&#x2702;','&#x2705;','&#x2708;','&#x2709;','&#x24c2;','&#x23fa;','&#x23f9;','&#x23f8;','&#x23f3;','&#x270a;','&#x23f2;','&#x23f1;','&#x23f0;','&#x23ef;','&#x23ee;','&#x270b;','&#x23ed;','&#x23ec;','&#x23eb;','&#x23ea;','&#x23e9;','&#x270c;','&#x23cf;','&#x2328;','&#x231b;','&#x231a;','&#x21aa;','&#x270d;','&#x270f;','&#x2712;','&#x2714;','&#x2716;','&#x271d;','&#x2721;','&#x2728;','&#x2733;','&#x2734;','&#x2744;','&#x2747;','&#x274c;','&#x274e;','&#x2753;','&#x2754;','&#x2755;','&#x2757;','&#x2763;','&#x2764;','&#x2795;','&#x2796;','&#x2797;','&#x27a1;','&#x27b0;','&#x27bf;','&#x2934;','&#x2935;','&#x21a9;','&#x2b05;','&#x2b06;','&#x2b07;','&#x2b1b;','&#x2b1c;','&#x2b50;','&#x2b55;','&#x2199;','&#x3030;','&#x303d;','&#x2198;','&#x2197;','&#x3297;','&#x3299;','&#x2196;','&#x2195;','&#x2194;','&#x2139;','&#x2122;','&#x2049;','&#x203c;','&#xe50a;');
	$partials = array('&#x1f004;','&#x1f0cf;','&#x1f170;','&#x1f171;','&#x1f17e;','&#x1f17f;','&#x1f18e;','&#x1f191;','&#x1f192;','&#x1f193;','&#x1f194;','&#x1f195;','&#x1f196;','&#x1f197;','&#x1f198;','&#x1f199;','&#x1f19a;','&#x1f1e6;','&#x1f1e8;','&#x1f1e9;','&#x1f1ea;','&#x1f1eb;','&#x1f1ec;','&#x1f1ee;','&#x1f1f1;','&#x1f1f2;','&#x1f1f4;','&#x1f1f6;','&#x1f1f7;','&#x1f1f8;','&#x1f1f9;','&#x1f1fa;','&#x1f1fc;','&#x1f1fd;','&#x1f1ff;','&#x1f1e7;','&#x1f1ed;','&#x1f1ef;','&#x1f1f3;','&#x1f1fb;','&#x1f1fe;','&#x1f1f0;','&#x1f1f5;','&#x1f201;','&#x1f202;','&#x1f21a;','&#x1f22f;','&#x1f232;','&#x1f233;','&#x1f234;','&#x1f235;','&#x1f236;','&#x1f237;','&#x1f238;','&#x1f239;','&#x1f23a;','&#x1f250;','&#x1f251;','&#x1f300;','&#x1f301;','&#x1f302;','&#x1f303;','&#x1f304;','&#x1f305;','&#x1f306;','&#x1f307;','&#x1f308;','&#x1f309;','&#x1f30a;','&#x1f30b;','&#x1f30c;','&#x1f30d;','&#x1f30e;','&#x1f30f;','&#x1f310;','&#x1f311;','&#x1f312;','&#x1f313;','&#x1f314;','&#x1f315;','&#x1f316;','&#x1f317;','&#x1f318;','&#x1f319;','&#x1f31a;','&#x1f31b;','&#x1f31c;','&#x1f31d;','&#x1f31e;','&#x1f31f;','&#x1f320;','&#x1f321;','&#x1f324;','&#x1f325;','&#x1f326;','&#x1f327;','&#x1f328;','&#x1f329;','&#x1f32a;','&#x1f32b;','&#x1f32c;','&#x1f32d;','&#x1f32e;','&#x1f32f;','&#x1f330;','&#x1f331;','&#x1f332;','&#x1f333;','&#x1f334;','&#x1f335;','&#x1f336;','&#x1f337;','&#x1f338;','&#x1f339;','&#x1f33a;','&#x1f33b;','&#x1f33c;','&#x1f33d;','&#x1f33e;','&#x1f33f;','&#x1f340;','&#x1f341;','&#x1f342;','&#x1f343;','&#x1f344;','&#x1f345;','&#x1f346;','&#x1f347;','&#x1f348;','&#x1f349;','&#x1f34a;','&#x1f34b;','&#x1f34c;','&#x1f34d;','&#x1f34e;','&#x1f34f;','&#x1f350;','&#x1f351;','&#x1f352;','&#x1f353;','&#x1f354;','&#x1f355;','&#x1f356;','&#x1f357;','&#x1f358;','&#x1f359;','&#x1f35a;','&#x1f35b;','&#x1f35c;','&#x1f35d;','&#x1f35e;','&#x1f35f;','&#x1f360;','&#x1f361;','&#x1f362;','&#x1f363;','&#x1f364;','&#x1f365;','&#x1f366;','&#x1f367;','&#x1f368;','&#x1f369;','&#x1f36a;','&#x1f36b;','&#x1f36c;','&#x1f36d;','&#x1f36e;','&#x1f36f;','&#x1f370;','&#x1f371;','&#x1f372;','&#x1f373;','&#x1f374;','&#x1f375;','&#x1f376;','&#x1f377;','&#x1f378;','&#x1f379;','&#x1f37a;','&#x1f37b;','&#x1f37c;','&#x1f37d;','&#x1f37e;','&#x1f37f;','&#x1f380;','&#x1f381;','&#x1f382;','&#x1f383;','&#x1f384;','&#x1f385;','&#x1f3fb;','&#x1f3fc;','&#x1f3fd;','&#x1f3fe;','&#x1f3ff;','&#x1f386;','&#x1f387;','&#x1f388;','&#x1f389;','&#x1f38a;','&#x1f38b;','&#x1f38c;','&#x1f38d;','&#x1f38e;','&#x1f38f;','&#x1f390;','&#x1f391;','&#x1f392;','&#x1f393;','&#x1f396;','&#x1f397;','&#x1f399;','&#x1f39a;','&#x1f39b;','&#x1f39e;','&#x1f39f;','&#x1f3a0;','&#x1f3a1;','&#x1f3a2;','&#x1f3a3;','&#x1f3a4;','&#x1f3a5;','&#x1f3a6;','&#x1f3a7;','&#x1f3a8;','&#x1f3a9;','&#x1f3aa;','&#x1f3ab;','&#x1f3ac;','&#x1f3ad;','&#x1f3ae;','&#x1f3af;','&#x1f3b0;','&#x1f3b1;','&#x1f3b2;','&#x1f3b3;','&#x1f3b4;','&#x1f3b5;','&#x1f3b6;','&#x1f3b7;','&#x1f3b8;','&#x1f3b9;','&#x1f3ba;','&#x1f3bb;','&#x1f3bc;','&#x1f3bd;','&#x1f3be;','&#x1f3bf;','&#x1f3c0;','&#x1f3c1;','&#x1f3c2;','&#x1f3c3;','&#x200d;','&#x2640;','&#xfe0f;','&#x2642;','&#x1f3c4;','&#x1f3c5;','&#x1f3c6;','&#x1f3c7;','&#x1f3c8;','&#x1f3c9;','&#x1f3ca;','&#x1f3cb;','&#x1f3cc;','&#x1f3cd;','&#x1f3ce;','&#x1f3cf;','&#x1f3d0;','&#x1f3d1;','&#x1f3d2;','&#x1f3d3;','&#x1f3d4;','&#x1f3d5;','&#x1f3d6;','&#x1f3d7;','&#x1f3d8;','&#x1f3d9;','&#x1f3da;','&#x1f3db;','&#x1f3dc;','&#x1f3dd;','&#x1f3de;','&#x1f3df;','&#x1f3e0;','&#x1f3e1;','&#x1f3e2;','&#x1f3e3;','&#x1f3e4;','&#x1f3e5;','&#x1f3e6;','&#x1f3e7;','&#x1f3e8;','&#x1f3e9;','&#x1f3ea;','&#x1f3eb;','&#x1f3ec;','&#x1f3ed;','&#x1f3ee;','&#x1f3ef;','&#x1f3f0;','&#x1f3f3;','&#x1f3f4;','&#x2620;','&#xe0067;','&#xe0062;','&#xe0065;','&#xe006e;','&#xe007f;','&#xe0073;','&#xe0063;','&#xe0074;','&#xe0077;','&#xe006c;','&#x1f3f5;','&#x1f3f7;','&#x1f3f8;','&#x1f3f9;','&#x1f3fa;','&#x1f400;','&#x1f401;','&#x1f402;','&#x1f403;','&#x1f404;','&#x1f405;','&#x1f406;','&#x1f407;','&#x1f408;','&#x1f409;','&#x1f40a;','&#x1f40b;','&#x1f40c;','&#x1f40d;','&#x1f40e;','&#x1f40f;','&#x1f410;','&#x1f411;','&#x1f412;','&#x1f413;','&#x1f414;','&#x1f415;','&#x1f416;','&#x1f417;','&#x1f418;','&#x1f419;','&#x1f41a;','&#x1f41b;','&#x1f41c;','&#x1f41d;','&#x1f41e;','&#x1f41f;','&#x1f420;','&#x1f421;','&#x1f422;','&#x1f423;','&#x1f424;','&#x1f425;','&#x1f426;','&#x1f427;','&#x1f428;','&#x1f429;','&#x1f42a;','&#x1f42b;','&#x1f42c;','&#x1f42d;','&#x1f42e;','&#x1f42f;','&#x1f430;','&#x1f431;','&#x1f432;','&#x1f433;','&#x1f434;','&#x1f435;','&#x1f436;','&#x1f437;','&#x1f438;','&#x1f439;','&#x1f43a;','&#x1f43b;','&#x1f43c;','&#x1f43d;','&#x1f43e;','&#x1f43f;','&#x1f440;','&#x1f441;','&#x1f5e8;','&#x1f442;','&#x1f443;','&#x1f444;','&#x1f445;','&#x1f446;','&#x1f447;','&#x1f448;','&#x1f449;','&#x1f44a;','&#x1f44b;','&#x1f44c;','&#x1f44d;','&#x1f44e;','&#x1f44f;','&#x1f450;','&#x1f451;','&#x1f452;','&#x1f453;','&#x1f454;','&#x1f455;','&#x1f456;','&#x1f457;','&#x1f458;','&#x1f459;','&#x1f45a;','&#x1f45b;','&#x1f45c;','&#x1f45d;','&#x1f45e;','&#x1f45f;','&#x1f460;','&#x1f461;','&#x1f462;','&#x1f463;','&#x1f464;','&#x1f465;','&#x1f466;','&#x1f467;','&#x1f468;','&#x1f4bb;','&#x1f4bc;','&#x1f527;','&#x1f52c;','&#x1f680;','&#x1f692;','&#x1f9b0;','&#x1f9b1;','&#x1f9b2;','&#x1f9b3;','&#x2695;','&#x2696;','&#x2708;','&#x1f469;','&#x2764;','&#x1f48b;','&#x1f46a;','&#x1f46b;','&#x1f46c;','&#x1f46d;','&#x1f46e;','&#x1f46f;','&#x1f470;','&#x1f471;','&#x1f472;','&#x1f473;','&#x1f474;','&#x1f475;','&#x1f476;','&#x1f477;','&#x1f478;','&#x1f479;','&#x1f47a;','&#x1f47b;','&#x1f47c;','&#x1f47d;','&#x1f47e;','&#x1f47f;','&#x1f480;','&#x1f481;','&#x1f482;','&#x1f483;','&#x1f484;','&#x1f485;','&#x1f486;','&#x1f487;','&#x1f488;','&#x1f489;','&#x1f48a;','&#x1f48c;','&#x1f48d;','&#x1f48e;','&#x1f48f;','&#x1f490;','&#x1f491;','&#x1f492;','&#x1f493;','&#x1f494;','&#x1f495;','&#x1f496;','&#x1f497;','&#x1f498;','&#x1f499;','&#x1f49a;','&#x1f49b;','&#x1f49c;','&#x1f49d;','&#x1f49e;','&#x1f49f;','&#x1f4a0;','&#x1f4a1;','&#x1f4a2;','&#x1f4a3;','&#x1f4a4;','&#x1f4a5;','&#x1f4a6;','&#x1f4a7;','&#x1f4a8;','&#x1f4a9;','&#x1f4aa;','&#x1f4ab;','&#x1f4ac;','&#x1f4ad;','&#x1f4ae;','&#x1f4af;','&#x1f4b0;','&#x1f4b1;','&#x1f4b2;','&#x1f4b3;','&#x1f4b4;','&#x1f4b5;','&#x1f4b6;','&#x1f4b7;','&#x1f4b8;','&#x1f4b9;','&#x1f4ba;','&#x1f4bd;','&#x1f4be;','&#x1f4bf;','&#x1f4c0;','&#x1f4c1;','&#x1f4c2;','&#x1f4c3;','&#x1f4c4;','&#x1f4c5;','&#x1f4c6;','&#x1f4c7;','&#x1f4c8;','&#x1f4c9;','&#x1f4ca;','&#x1f4cb;','&#x1f4cc;','&#x1f4cd;','&#x1f4ce;','&#x1f4cf;','&#x1f4d0;','&#x1f4d1;','&#x1f4d2;','&#x1f4d3;','&#x1f4d4;','&#x1f4d5;','&#x1f4d6;','&#x1f4d7;','&#x1f4d8;','&#x1f4d9;','&#x1f4da;','&#x1f4db;','&#x1f4dc;','&#x1f4dd;','&#x1f4de;','&#x1f4df;','&#x1f4e0;','&#x1f4e1;','&#x1f4e2;','&#x1f4e3;','&#x1f4e4;','&#x1f4e5;','&#x1f4e6;','&#x1f4e7;','&#x1f4e8;','&#x1f4e9;','&#x1f4ea;','&#x1f4eb;','&#x1f4ec;','&#x1f4ed;','&#x1f4ee;','&#x1f4ef;','&#x1f4f0;','&#x1f4f1;','&#x1f4f2;','&#x1f4f3;','&#x1f4f4;','&#x1f4f5;','&#x1f4f6;','&#x1f4f7;','&#x1f4f8;','&#x1f4f9;','&#x1f4fa;','&#x1f4fb;','&#x1f4fc;','&#x1f4fd;','&#x1f4ff;','&#x1f500;','&#x1f501;','&#x1f502;','&#x1f503;','&#x1f504;','&#x1f505;','&#x1f506;','&#x1f507;','&#x1f508;','&#x1f509;','&#x1f50a;','&#x1f50b;','&#x1f50c;','&#x1f50d;','&#x1f50e;','&#x1f50f;','&#x1f510;','&#x1f511;','&#x1f512;','&#x1f513;','&#x1f514;','&#x1f515;','&#x1f516;','&#x1f517;','&#x1f518;','&#x1f519;','&#x1f51a;','&#x1f51b;','&#x1f51c;','&#x1f51d;','&#x1f51e;','&#x1f51f;','&#x1f520;','&#x1f521;','&#x1f522;','&#x1f523;','&#x1f524;','&#x1f525;','&#x1f526;','&#x1f528;','&#x1f529;','&#x1f52a;','&#x1f52b;','&#x1f52d;','&#x1f52e;','&#x1f52f;','&#x1f530;','&#x1f531;','&#x1f532;','&#x1f533;','&#x1f534;','&#x1f535;','&#x1f536;','&#x1f537;','&#x1f538;','&#x1f539;','&#x1f53a;','&#x1f53b;','&#x1f53c;','&#x1f53d;','&#x1f549;','&#x1f54a;','&#x1f54b;','&#x1f54c;','&#x1f54d;','&#x1f54e;','&#x1f550;','&#x1f551;','&#x1f552;','&#x1f553;','&#x1f554;','&#x1f555;','&#x1f556;','&#x1f557;','&#x1f558;','&#x1f559;','&#x1f55a;','&#x1f55b;','&#x1f55c;','&#x1f55d;','&#x1f55e;','&#x1f55f;','&#x1f560;','&#x1f561;','&#x1f562;','&#x1f563;','&#x1f564;','&#x1f565;','&#x1f566;','&#x1f567;','&#x1f56f;','&#x1f570;','&#x1f573;','&#x1f574;','&#x1f575;','&#x1f576;','&#x1f577;','&#x1f578;','&#x1f579;','&#x1f57a;','&#x1f587;','&#x1f58a;','&#x1f58b;','&#x1f58c;','&#x1f58d;','&#x1f590;','&#x1f595;','&#x1f596;','&#x1f5a4;','&#x1f5a5;','&#x1f5a8;','&#x1f5b1;','&#x1f5b2;','&#x1f5bc;','&#x1f5c2;','&#x1f5c3;','&#x1f5c4;','&#x1f5d1;','&#x1f5d2;','&#x1f5d3;','&#x1f5dc;','&#x1f5dd;','&#x1f5de;','&#x1f5e1;','&#x1f5e3;','&#x1f5ef;','&#x1f5f3;','&#x1f5fa;','&#x1f5fb;','&#x1f5fc;','&#x1f5fd;','&#x1f5fe;','&#x1f5ff;','&#x1f600;','&#x1f601;','&#x1f602;','&#x1f603;','&#x1f604;','&#x1f605;','&#x1f606;','&#x1f607;','&#x1f608;','&#x1f609;','&#x1f60a;','&#x1f60b;','&#x1f60c;','&#x1f60d;','&#x1f60e;','&#x1f60f;','&#x1f610;','&#x1f611;','&#x1f612;','&#x1f613;','&#x1f614;','&#x1f615;','&#x1f616;','&#x1f617;','&#x1f618;','&#x1f619;','&#x1f61a;','&#x1f61b;','&#x1f61c;','&#x1f61d;','&#x1f61e;','&#x1f61f;','&#x1f620;','&#x1f621;','&#x1f622;','&#x1f623;','&#x1f624;','&#x1f625;','&#x1f626;','&#x1f627;','&#x1f628;','&#x1f629;','&#x1f62a;','&#x1f62b;','&#x1f62c;','&#x1f62d;','&#x1f62e;','&#x1f62f;','&#x1f630;','&#x1f631;','&#x1f632;','&#x1f633;','&#x1f634;','&#x1f635;','&#x1f636;','&#x1f637;','&#x1f638;','&#x1f639;','&#x1f63a;','&#x1f63b;','&#x1f63c;','&#x1f63d;','&#x1f63e;','&#x1f63f;','&#x1f640;','&#x1f641;','&#x1f642;','&#x1f643;','&#x1f644;','&#x1f645;','&#x1f646;','&#x1f647;','&#x1f648;','&#x1f649;','&#x1f64a;','&#x1f64b;','&#x1f64c;','&#x1f64d;','&#x1f64e;','&#x1f64f;','&#x1f681;','&#x1f682;','&#x1f683;','&#x1f684;','&#x1f685;','&#x1f686;','&#x1f687;','&#x1f688;','&#x1f689;','&#x1f68a;','&#x1f68b;','&#x1f68c;','&#x1f68d;','&#x1f68e;','&#x1f68f;','&#x1f690;','&#x1f691;','&#x1f693;','&#x1f694;','&#x1f695;','&#x1f696;','&#x1f697;','&#x1f698;','&#x1f699;','&#x1f69a;','&#x1f69b;','&#x1f69c;','&#x1f69d;','&#x1f69e;','&#x1f69f;','&#x1f6a0;','&#x1f6a1;','&#x1f6a2;','&#x1f6a3;','&#x1f6a4;','&#x1f6a5;','&#x1f6a6;','&#x1f6a7;','&#x1f6a8;','&#x1f6a9;','&#x1f6aa;','&#x1f6ab;','&#x1f6ac;','&#x1f6ad;','&#x1f6ae;','&#x1f6af;','&#x1f6b0;','&#x1f6b1;','&#x1f6b2;','&#x1f6b3;','&#x1f6b4;','&#x1f6b5;','&#x1f6b6;','&#x1f6b7;','&#x1f6b8;','&#x1f6b9;','&#x1f6ba;','&#x1f6bb;','&#x1f6bc;','&#x1f6bd;','&#x1f6be;','&#x1f6bf;','&#x1f6c0;','&#x1f6c1;','&#x1f6c2;','&#x1f6c3;','&#x1f6c4;','&#x1f6c5;','&#x1f6cb;','&#x1f6cc;','&#x1f6cd;','&#x1f6ce;','&#x1f6cf;','&#x1f6d0;','&#x1f6d1;','&#x1f6d2;','&#x1f6e0;','&#x1f6e1;','&#x1f6e2;','&#x1f6e3;','&#x1f6e4;','&#x1f6e5;','&#x1f6e9;','&#x1f6eb;','&#x1f6ec;','&#x1f6f0;','&#x1f6f3;','&#x1f6f4;','&#x1f6f5;','&#x1f6f6;','&#x1f6f7;','&#x1f6f8;','&#x1f6f9;','&#x1f910;','&#x1f911;','&#x1f912;','&#x1f913;','&#x1f914;','&#x1f915;','&#x1f916;','&#x1f917;','&#x1f918;','&#x1f919;','&#x1f91a;','&#x1f91b;','&#x1f91c;','&#x1f91d;','&#x1f91e;','&#x1f91f;','&#x1f920;','&#x1f921;','&#x1f922;','&#x1f923;','&#x1f924;','&#x1f925;','&#x1f926;','&#x1f927;','&#x1f928;','&#x1f929;','&#x1f92a;','&#x1f92b;','&#x1f92c;','&#x1f92d;','&#x1f92e;','&#x1f92f;','&#x1f930;','&#x1f931;','&#x1f932;','&#x1f933;','&#x1f934;','&#x1f935;','&#x1f936;','&#x1f937;','&#x1f938;','&#x1f939;','&#x1f93a;','&#x1f93c;','&#x1f93d;','&#x1f93e;','&#x1f940;','&#x1f941;','&#x1f942;','&#x1f943;','&#x1f944;','&#x1f945;','&#x1f947;','&#x1f948;','&#x1f949;','&#x1f94a;','&#x1f94b;','&#x1f94c;','&#x1f94d;','&#x1f94e;','&#x1f94f;','&#x1f950;','&#x1f951;','&#x1f952;','&#x1f953;','&#x1f954;','&#x1f955;','&#x1f956;','&#x1f957;','&#x1f958;','&#x1f959;','&#x1f95a;','&#x1f95b;','&#x1f95c;','&#x1f95d;','&#x1f95e;','&#x1f95f;','&#x1f960;','&#x1f961;','&#x1f962;','&#x1f963;','&#x1f964;','&#x1f965;','&#x1f966;','&#x1f967;','&#x1f968;','&#x1f969;','&#x1f96a;','&#x1f96b;','&#x1f96c;','&#x1f96d;','&#x1f96e;','&#x1f96f;','&#x1f970;','&#x1f973;','&#x1f974;','&#x1f975;','&#x1f976;','&#x1f97a;','&#x1f97c;','&#x1f97d;','&#x1f97e;','&#x1f97f;','&#x1f980;','&#x1f981;','&#x1f982;','&#x1f983;','&#x1f984;','&#x1f985;','&#x1f986;','&#x1f987;','&#x1f988;','&#x1f989;','&#x1f98a;','&#x1f98b;','&#x1f98c;','&#x1f98d;','&#x1f98e;','&#x1f98f;','&#x1f990;','&#x1f991;','&#x1f992;','&#x1f993;','&#x1f994;','&#x1f995;','&#x1f996;','&#x1f997;','&#x1f998;','&#x1f999;','&#x1f99a;','&#x1f99b;','&#x1f99c;','&#x1f99d;','&#x1f99e;','&#x1f99f;','&#x1f9a0;','&#x1f9a1;','&#x1f9a2;','&#x1f9b4;','&#x1f9b5;','&#x1f9b6;','&#x1f9b7;','&#x1f9b8;','&#x1f9b9;','&#x1f9c0;','&#x1f9c1;','&#x1f9c2;','&#x1f9d0;','&#x1f9d1;','&#x1f9d2;','&#x1f9d3;','&#x1f9d4;','&#x1f9d5;','&#x1f9d6;','&#x1f9d7;','&#x1f9d8;','&#x1f9d9;','&#x1f9da;','&#x1f9db;','&#x1f9dc;','&#x1f9dd;','&#x1f9de;','&#x1f9df;','&#x1f9e0;','&#x1f9e1;','&#x1f9e2;','&#x1f9e3;','&#x1f9e4;','&#x1f9e5;','&#x1f9e6;','&#x1f9e7;','&#x1f9e8;','&#x1f9e9;','&#x1f9ea;','&#x1f9eb;','&#x1f9ec;','&#x1f9ed;','&#x1f9ee;','&#x1f9ef;','&#x1f9f0;','&#x1f9f1;','&#x1f9f2;','&#x1f9f3;','&#x1f9f4;','&#x1f9f5;','&#x1f9f6;','&#x1f9f7;','&#x1f9f8;','&#x1f9f9;','&#x1f9fa;','&#x1f9fb;','&#x1f9fc;','&#x1f9fd;','&#x1f9fe;','&#x1f9ff;','&#x203c;','&#x2049;','&#x2122;','&#x2139;','&#x2194;','&#x2195;','&#x2196;','&#x2197;','&#x2198;','&#x2199;','&#x21a9;','&#x21aa;','&#x20e3;','&#x231a;','&#x231b;','&#x2328;','&#x23cf;','&#x23e9;','&#x23ea;','&#x23eb;','&#x23ec;','&#x23ed;','&#x23ee;','&#x23ef;','&#x23f0;','&#x23f1;','&#x23f2;','&#x23f3;','&#x23f8;','&#x23f9;','&#x23fa;','&#x24c2;','&#x25aa;','&#x25ab;','&#x25b6;','&#x25c0;','&#x25fb;','&#x25fc;','&#x25fd;','&#x25fe;','&#x2600;','&#x2601;','&#x2602;','&#x2603;','&#x2604;','&#x260e;','&#x2611;','&#x2614;','&#x2615;','&#x2618;','&#x261d;','&#x2622;','&#x2623;','&#x2626;','&#x262a;','&#x262e;','&#x262f;','&#x2638;','&#x2639;','&#x263a;','&#x2648;','&#x2649;','&#x264a;','&#x264b;','&#x264c;','&#x264d;','&#x264e;','&#x264f;','&#x2650;','&#x2651;','&#x2652;','&#x2653;','&#x265f;','&#x2660;','&#x2663;','&#x2665;','&#x2666;','&#x2668;','&#x267b;','&#x267e;','&#x267f;','&#x2692;','&#x2693;','&#x2694;','&#x2697;','&#x2699;','&#x269b;','&#x269c;','&#x26a0;','&#x26a1;','&#x26aa;','&#x26ab;','&#x26b0;','&#x26b1;','&#x26bd;','&#x26be;','&#x26c4;','&#x26c5;','&#x26c8;','&#x26ce;','&#x26cf;','&#x26d1;','&#x26d3;','&#x26d4;','&#x26e9;','&#x26ea;','&#x26f0;','&#x26f1;','&#x26f2;','&#x26f3;','&#x26f4;','&#x26f5;','&#x26f7;','&#x26f8;','&#x26f9;','&#x26fa;','&#x26fd;','&#x2702;','&#x2705;','&#x2709;','&#x270a;','&#x270b;','&#x270c;','&#x270d;','&#x270f;','&#x2712;','&#x2714;','&#x2716;','&#x271d;','&#x2721;','&#x2728;','&#x2733;','&#x2734;','&#x2744;','&#x2747;','&#x274c;','&#x274e;','&#x2753;','&#x2754;','&#x2755;','&#x2757;','&#x2763;','&#x2795;','&#x2796;','&#x2797;','&#x27a1;','&#x27b0;','&#x27bf;','&#x2934;','&#x2935;','&#x2b05;','&#x2b06;','&#x2b07;','&#x2b1b;','&#x2b1c;','&#x2b50;','&#x2b55;','&#x3030;','&#x303d;','&#x3297;','&#x3299;','&#xe50a;');
	// END: emoji arrays

	if ( 'entities' === $type ) {
		return $entities;
	}

	return $partials;
}

/**
 * Shorten a URL, to be used as link text.
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

/**
 * Sanitizes a hex color.
 *
 * Returns either '', a 3 or 6 digit hex color (with #), or nothing.
 * For sanitizing values without a #, see sanitize_hex_color_no_hash().
 *
 * @since 3.4.0
 *
 * @param string $color
 * @return string|void
 */
function sanitize_hex_color( $color ) {
	if ( '' === $color ) {
		return '';
	}

	// 3 or 6 hex digits, or the empty string.
	if ( preg_match('|^#([A-Fa-f0-9]{3}){1,2}$|', $color ) ) {
		return $color;
	}
}

/**
 * Sanitizes a hex color without a hash. Use sanitize_hex_color() when possible.
 *
 * Saving hex colors without a hash puts the burden of adding the hash on the
 * UI, which makes it difficult to use or upgrade to other color types such as
 * rgba, hsl, rgb, and html color names.
 *
 * Returns either '', a 3 or 6 digit hex color (without a #), or null.
 *
 * @since 3.4.0
 *
 * @param string $color
 * @return string|null
 */
function sanitize_hex_color_no_hash( $color ) {
	$color = ltrim( $color, '#' );

	if ( '' === $color ) {
		return '';
	}

	return sanitize_hex_color( '#' . $color ) ? $color : null;
}

/**
 * Ensures that any hex color is properly hashed.
 * Otherwise, returns value untouched.
 *
 * This method should only be necessary if using sanitize_hex_color_no_hash().
 *
 * @since 3.4.0
 *
 * @param string $color
 * @return string
 */
function maybe_hash_hex_color( $color ) {
	if ( $unhashed = sanitize_hex_color_no_hash( $color ) ) {
		return '#' . $unhashed;
	}

	return $color;
}

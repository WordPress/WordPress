<?php

/**
 * HTML API: WP_HTML_Decoder class
 *
 * Decodes spans of raw text found inside HTML content.
 *
 * @package WordPress
 * @subpackage HTML-API
 * @since 6.6.0
 */
class WP_HTML_Decoder {
	/**
	 * Indicates if an attribute value starts with a given raw string value.
	 *
	 * Use this method to determine if an attribute value starts with a given string, regardless
	 * of how it might be encoded in HTML. For instance, `http:` could be represented as `http:`
	 * or as `http&colon;` or as `&#x68;ttp:` or as `h&#116;tp&colon;`, or in many other ways.
	 *
	 * Example:
	 *
	 *     $value = 'http&colon;//wordpress.org/';
	 *     true   === WP_HTML_Decoder::attribute_starts_with( $value, 'http:', 'ascii-case-insensitive' );
	 *     false  === WP_HTML_Decoder::attribute_starts_with( $value, 'https:', 'ascii-case-insensitive' );
	 *
	 * @since 6.6.0
	 *
	 * @param string $haystack         String containing the raw non-decoded attribute value.
	 * @param string $search_text      Does the attribute value start with this plain string.
	 * @param string $case_sensitivity Optional. Pass 'ascii-case-insensitive' to ignore ASCII case when matching.
	 *                                 Default 'case-sensitive'.
	 * @return bool Whether the attribute value starts with the given string.
	 */
	public static function attribute_starts_with( $haystack, $search_text, $case_sensitivity = 'case-sensitive' ): bool {
		$search_length = strlen( $search_text );
		$loose_case    = 'ascii-case-insensitive' === $case_sensitivity;
		$haystack_end  = strlen( $haystack );
		$search_at     = 0;
		$haystack_at   = 0;

		while ( $search_at < $search_length && $haystack_at < $haystack_end ) {
			$chars_match = $loose_case
				? strtolower( $haystack[ $haystack_at ] ) === strtolower( $search_text[ $search_at ] )
				: $haystack[ $haystack_at ] === $search_text[ $search_at ];

			$is_introducer = '&' === $haystack[ $haystack_at ];
			$next_chunk    = $is_introducer
				? self::read_character_reference( 'attribute', $haystack, $haystack_at, $token_length )
				: null;

			// If there's no character reference and the characters don't match, the match fails.
			if ( null === $next_chunk && ! $chars_match ) {
				return false;
			}

			// If there's no character reference but the character do match, then it could still match.
			if ( null === $next_chunk && $chars_match ) {
				++$haystack_at;
				++$search_at;
				continue;
			}

			// If there is a character reference, then the decoded value must exactly match what follows in the search string.
			if ( 0 !== substr_compare( $search_text, $next_chunk, $search_at, strlen( $next_chunk ), $loose_case ) ) {
				return false;
			}

			// The character reference matched, so continue checking.
			$haystack_at += $token_length;
			$search_at   += strlen( $next_chunk );
		}

		return true;
	}

	/**
	 * Returns a string containing the decoded value of a given HTML text node.
	 *
	 * Text nodes appear in HTML DATA sections, which are the text segments inside
	 * and around tags, excepting SCRIPT and STYLE elements (and some others),
	 * whose inner text is not decoded. Use this function to read the decoded
	 * value of such a text span in an HTML document.
	 *
	 * Example:
	 *
	 *     '“😄”' === WP_HTML_Decode::decode_text_node( '&#x93;&#x1f604;&#x94' );
	 *
	 * @since 6.6.0
	 *
	 * @param string $text Text containing raw and non-decoded text node to decode.
	 * @return string Decoded UTF-8 value of given text node.
	 */
	public static function decode_text_node( $text ): string {
		return static::decode( 'data', $text );
	}

	/**
	 * Returns a string containing the decoded value of a given HTML attribute.
	 *
	 * Text found inside an HTML attribute has different parsing rules than for
	 * text found inside other markup, or DATA segments. Use this function to
	 * read the decoded value of an HTML string inside a quoted attribute.
	 *
	 * Example:
	 *
	 *     '“😄”' === WP_HTML_Decode::decode_attribute( '&#x93;&#x1f604;&#x94' );
	 *
	 * @since 6.6.0
	 *
	 * @param string $text Text containing raw and non-decoded attribute value to decode.
	 * @return string Decoded UTF-8 value of given attribute value.
	 */
	public static function decode_attribute( $text ): string {
		return static::decode( 'attribute', $text );
	}

	/**
	 * Decodes a span of HTML text, depending on the context in which it's found.
	 *
	 * This is a low-level method; prefer calling WP_HTML_Decoder::decode_attribute() or
	 * WP_HTML_Decoder::decode_text_node() instead. It's provided for cases where this
	 * may be difficult to do from calling code.
	 *
	 * Example:
	 *
	 *     '©' = WP_HTML_Decoder::decode( 'data', '&copy;' );
	 *
	 * @since 6.6.0
	 *
	 * @access private
	 *
	 * @param string $context `attribute` for decoding attribute values, `data` otherwise.
	 * @param string $text    Text document containing span of text to decode.
	 * @return string Decoded UTF-8 string.
	 */
	public static function decode( $context, $text ): string {
		$decoded = '';
		$end     = strlen( $text );
		$at      = 0;
		$was_at  = 0;

		while ( $at < $end ) {
			$next_character_reference_at = strpos( $text, '&', $at );
			if ( false === $next_character_reference_at ) {
				break;
			}

			$character_reference = self::read_character_reference( $context, $text, $next_character_reference_at, $token_length );
			if ( isset( $character_reference ) ) {
				$at       = $next_character_reference_at;
				$decoded .= substr( $text, $was_at, $at - $was_at );
				$decoded .= $character_reference;
				$at      += $token_length;
				$was_at   = $at;
				continue;
			}

			++$at;
		}

		if ( 0 === $was_at ) {
			return $text;
		}

		if ( $was_at < $end ) {
			$decoded .= substr( $text, $was_at, $end - $was_at );
		}

		return $decoded;
	}

	/**
	 * Attempt to read a character reference at the given location in a given string,
	 * depending on the context in which it's found.
	 *
	 * If a character reference is found, this function will return the translated value
	 * that the reference maps to. It will then set `$match_byte_length` the
	 * number of bytes of input it read while consuming the character reference. This
	 * gives calling code the opportunity to advance its cursor when traversing a string
	 * and decoding.
	 *
	 * Example:
	 *
	 *     null === WP_HTML_Decoder::read_character_reference( 'attribute', 'Ships&hellip;', 0 );
	 *     '…'  === WP_HTML_Decoder::read_character_reference( 'attribute', 'Ships&hellip;', 5, $token_length );
	 *     8    === $token_length; // `&hellip;`
	 *
	 *     null === WP_HTML_Decoder::read_character_reference( 'attribute', '&notin', 0 );
	 *     '∉'  === WP_HTML_Decoder::read_character_reference( 'attribute', '&notin;', 0, $token_length );
	 *     7    === $token_length; // `&notin;`
	 *
	 *     '¬'  === WP_HTML_Decoder::read_character_reference( 'data', '&notin', 0, $token_length );
	 *     4    === $token_length; // `&not`
	 *     '∉'  === WP_HTML_Decoder::read_character_reference( 'data', '&notin;', 0, $token_length );
	 *     7    === $token_length; // `&notin;`
	 *
	 * @since 6.6.0
	 *
	 * @global WP_Token_Map $html5_named_character_references Mappings for HTML5 named character references.
	 *
	 * @param string $context            `attribute` for decoding attribute values, `data` otherwise.
	 * @param string $text               Text document containing span of text to decode.
	 * @param int    $at                 Optional. Byte offset into text where span begins, defaults to the beginning (0).
	 * @param int    &$match_byte_length Optional. Set to byte-length of character reference if provided and if a match
	 *                                   is found, otherwise not set. Default null.
	 * @return string|false Decoded character reference in UTF-8 if found, otherwise `false`.
	 */
	public static function read_character_reference( $context, $text, $at = 0, &$match_byte_length = null ) {
		/**
		 * Mappings for HTML5 named character references.
		 *
		 * @var WP_Token_Map $html5_named_character_references
		 */
		global $html5_named_character_references;

		$length = strlen( $text );
		if ( $at + 1 >= $length ) {
			return null;
		}

		if ( '&' !== $text[ $at ] ) {
			return null;
		}

		/*
		 * Numeric character references.
		 *
		 * When truncated, these will encode the code point found by parsing the
		 * digits that are available. For example, when `&#x1f170;` is truncated
		 * to `&#x1f1` it will encode `Ǳ`. It does not:
		 *  - know how to parse the original `🅰`.
		 *  - fail to parse and return plaintext `&#x1f1`.
		 *  - fail to parse and return the replacement character `�`
		 */
		if ( '#' === $text[ $at + 1 ] ) {
			if ( $at + 2 >= $length ) {
				return null;
			}

			/** Tracks inner parsing within the numeric character reference. */
			$digits_at = $at + 2;

			if ( 'x' === $text[ $digits_at ] || 'X' === $text[ $digits_at ] ) {
				$numeric_base   = 16;
				$numeric_digits = '0123456789abcdefABCDEF';
				$max_digits     = 6; // &#x10FFFF;
				++$digits_at;
			} else {
				$numeric_base   = 10;
				$numeric_digits = '0123456789';
				$max_digits     = 7; // &#1114111;
			}

			// Cannot encode invalid Unicode code points. Max is to U+10FFFF.
			$zero_count    = strspn( $text, '0', $digits_at );
			$digit_count   = strspn( $text, $numeric_digits, $digits_at + $zero_count );
			$after_digits  = $digits_at + $zero_count + $digit_count;
			$has_semicolon = $after_digits < $length && ';' === $text[ $after_digits ];
			$end_of_span   = $has_semicolon ? $after_digits + 1 : $after_digits;

			// `&#` or `&#x` without digits returns into plaintext.
			if ( 0 === $digit_count && 0 === $zero_count ) {
				return null;
			}

			// Whereas `&#` and only zeros is invalid.
			if ( 0 === $digit_count ) {
				$match_byte_length = $end_of_span - $at;
				return '�';
			}

			// If there are too many digits then it's not worth parsing. It's invalid.
			if ( $digit_count > $max_digits ) {
				$match_byte_length = $end_of_span - $at;
				return '�';
			}

			$digits     = substr( $text, $digits_at + $zero_count, $digit_count );
			$code_point = intval( $digits, $numeric_base );

			/*
			 * Noncharacters, 0x0D, and non-ASCII-whitespace control characters.
			 *
			 * > A noncharacter is a code point that is in the range U+FDD0 to U+FDEF,
			 * > inclusive, or U+FFFE, U+FFFF, U+1FFFE, U+1FFFF, U+2FFFE, U+2FFFF,
			 * > U+3FFFE, U+3FFFF, U+4FFFE, U+4FFFF, U+5FFFE, U+5FFFF, U+6FFFE,
			 * > U+6FFFF, U+7FFFE, U+7FFFF, U+8FFFE, U+8FFFF, U+9FFFE, U+9FFFF,
			 * > U+AFFFE, U+AFFFF, U+BFFFE, U+BFFFF, U+CFFFE, U+CFFFF, U+DFFFE,
			 * > U+DFFFF, U+EFFFE, U+EFFFF, U+FFFFE, U+FFFFF, U+10FFFE, or U+10FFFF.
			 *
			 * A C0 control is a code point that is in the range of U+00 to U+1F,
			 * but ASCII whitespace includes U+09, U+0A, U+0C, and U+0D.
			 *
			 * These characters are invalid but still decode as any valid character.
			 * This comment is here to note and explain why there's no check to
			 * remove these characters or replace them.
			 *
			 * @see https://infra.spec.whatwg.org/#noncharacter
			 */

			/*
			 * Code points in the C1 controls area need to be remapped as if they
			 * were stored in Windows-1252. Note! This transformation only happens
			 * for numeric character references. The raw code points in the byte
			 * stream are not translated.
			 *
			 * > If the number is one of the numbers in the first column of
			 * > the following table, then find the row with that number in
			 * > the first column, and set the character reference code to
			 * > the number in the second column of that row.
			 */
			if ( $code_point >= 0x80 && $code_point <= 0x9F ) {
				$windows_1252_mapping = array(
					0x20AC, // 0x80 -> EURO SIGN (€).
					0x81,   // 0x81 -> (no change).
					0x201A, // 0x82 -> SINGLE LOW-9 QUOTATION MARK (‚).
					0x0192, // 0x83 -> LATIN SMALL LETTER F WITH HOOK (ƒ).
					0x201E, // 0x84 -> DOUBLE LOW-9 QUOTATION MARK („).
					0x2026, // 0x85 -> HORIZONTAL ELLIPSIS (…).
					0x2020, // 0x86 -> DAGGER (†).
					0x2021, // 0x87 -> DOUBLE DAGGER (‡).
					0x02C6, // 0x88 -> MODIFIER LETTER CIRCUMFLEX ACCENT (ˆ).
					0x2030, // 0x89 -> PER MILLE SIGN (‰).
					0x0160, // 0x8A -> LATIN CAPITAL LETTER S WITH CARON (Š).
					0x2039, // 0x8B -> SINGLE LEFT-POINTING ANGLE QUOTATION MARK (‹).
					0x0152, // 0x8C -> LATIN CAPITAL LIGATURE OE (Œ).
					0x8D,   // 0x8D -> (no change).
					0x017D, // 0x8E -> LATIN CAPITAL LETTER Z WITH CARON (Ž).
					0x8F,   // 0x8F -> (no change).
					0x90,   // 0x90 -> (no change).
					0x2018, // 0x91 -> LEFT SINGLE QUOTATION MARK (‘).
					0x2019, // 0x92 -> RIGHT SINGLE QUOTATION MARK (’).
					0x201C, // 0x93 -> LEFT DOUBLE QUOTATION MARK (“).
					0x201D, // 0x94 -> RIGHT DOUBLE QUOTATION MARK (”).
					0x2022, // 0x95 -> BULLET (•).
					0x2013, // 0x96 -> EN DASH (–).
					0x2014, // 0x97 -> EM DASH (—).
					0x02DC, // 0x98 -> SMALL TILDE (˜).
					0x2122, // 0x99 -> TRADE MARK SIGN (™).
					0x0161, // 0x9A -> LATIN SMALL LETTER S WITH CARON (š).
					0x203A, // 0x9B -> SINGLE RIGHT-POINTING ANGLE QUOTATION MARK (›).
					0x0153, // 0x9C -> LATIN SMALL LIGATURE OE (œ).
					0x9D,   // 0x9D -> (no change).
					0x017E, // 0x9E -> LATIN SMALL LETTER Z WITH CARON (ž).
					0x0178, // 0x9F -> LATIN CAPITAL LETTER Y WITH DIAERESIS (Ÿ).
				);

				$code_point = $windows_1252_mapping[ $code_point - 0x80 ];
			}

			$match_byte_length = $end_of_span - $at;
			return self::code_point_to_utf8_bytes( $code_point );
		}

		/** Tracks inner parsing within the named character reference. */
		$name_at = $at + 1;
		// Minimum named character reference is two characters. E.g. `GT`.
		if ( $name_at + 2 > $length ) {
			return null;
		}

		$name_length = 0;
		$replacement = $html5_named_character_references->read_token( $text, $name_at, $name_length );
		if ( false === $replacement ) {
			return null;
		}

		$after_name = $name_at + $name_length;

		// If the match ended with a semicolon then it should always be decoded.
		if ( ';' === $text[ $name_at + $name_length - 1 ] ) {
			$match_byte_length = $after_name - $at;
			return $replacement;
		}

		/*
		 * At this point though there's a match for an entry in the named
		 * character reference table but the match doesn't end in `;`.
		 * It may be allowed if it's followed by something unambiguous.
		 */
		$ambiguous_follower = (
			$after_name < $length &&
			$name_at < $length &&
			(
				ctype_alnum( $text[ $after_name ] ) ||
				'=' === $text[ $after_name ]
			)
		);

		// It's non-ambiguous, safe to leave it in.
		if ( ! $ambiguous_follower ) {
			$match_byte_length = $after_name - $at;
			return $replacement;
		}

		// It's ambiguous, which isn't allowed inside attributes.
		if ( 'attribute' === $context ) {
			return null;
		}

		$match_byte_length = $after_name - $at;
		return $replacement;
	}

	/**
	 * Encode a code point number into the UTF-8 encoding.
	 *
	 * This encoder implements the UTF-8 encoding algorithm for converting
	 * a code point into a byte sequence. If it receives an invalid code
	 * point it will return the Unicode Replacement Character U+FFFD `�`.
	 *
	 * Example:
	 *
	 *     '🅰' === WP_HTML_Decoder::code_point_to_utf8_bytes( 0x1f170 );
	 *
	 *     // Half of a surrogate pair is an invalid code point.
	 *     '�' === WP_HTML_Decoder::code_point_to_utf8_bytes( 0xd83c );
	 *
	 * @since 6.6.0
	 *
	 * @see https://www.rfc-editor.org/rfc/rfc3629 For the UTF-8 standard.
	 *
	 * @param int $code_point Which code point to convert.
	 * @return string Converted code point, or `�` if invalid.
	 */
	public static function code_point_to_utf8_bytes( $code_point ): string {
		// Pre-check to ensure a valid code point.
		if (
			$code_point <= 0 ||
			( $code_point >= 0xD800 && $code_point <= 0xDFFF ) ||
			$code_point > 0x10FFFF
		) {
			return '�';
		}

		if ( $code_point <= 0x7F ) {
			return chr( $code_point );
		}

		if ( $code_point <= 0x7FF ) {
			$byte1 = chr( ( $code_point >> 6 ) | 0xC0 );
			$byte2 = chr( $code_point & 0x3F | 0x80 );

			return "{$byte1}{$byte2}";
		}

		if ( $code_point <= 0xFFFF ) {
			$byte1 = chr( ( $code_point >> 12 ) | 0xE0 );
			$byte2 = chr( ( $code_point >> 6 ) & 0x3F | 0x80 );
			$byte3 = chr( $code_point & 0x3F | 0x80 );

			return "{$byte1}{$byte2}{$byte3}";
		}

		// Any values above U+10FFFF are eliminated above in the pre-check.
		$byte1 = chr( ( $code_point >> 18 ) | 0xF0 );
		$byte2 = chr( ( $code_point >> 12 ) & 0x3F | 0x80 );
		$byte3 = chr( ( $code_point >> 6 ) & 0x3F | 0x80 );
		$byte4 = chr( $code_point & 0x3F | 0x80 );

		return "{$byte1}{$byte2}{$byte3}{$byte4}";
	}
}

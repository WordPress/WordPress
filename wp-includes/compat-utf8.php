<?php

/**
 * Finds spans of valid and invalid UTF-8 bytes in a given string.
 *
 * This is a low-level tool to power various UTF-8 functionality.
 * It scans through a string until it finds invalid byte spans.
 * When it does this, it does three things:
 *
 *  - Assigns `$at` to the position after the last successful code point.
 *  - Assigns `$invalid_length` to the length of the maximal subpart of
 *    the invalid bytes starting at `$at`.
 *  - Returns how many code points were successfully scanned.
 *
 * This information is enough to build a number of useful UTF-8 functions.
 *
 * Example:
 *
 *     // ñ is U+F1, which in `ISO-8859-1`/`latin1`/`Windows-1252`/`cp1252` is 0xF1.
 *     "Pi\xF1a" === $pineapple = mb_convert_encoding( "Piña", 'Windows-1252', 'UTF-8' );
 *     $at = $invalid_length = 0;
 *
 *     // The first step finds the invalid 0xF1 byte.
 *     2 === _wp_scan_utf8( $pineapple, $at, $invalid_length );
 *     $at === 2; $invalid_length === 1;
 *
 *     // The second step continues to the end of the string.
 *     1 === _wp_scan_utf8( $pineapple, $at, $invalid_length );
 *     $at === 4; $invalid_length === 0;
 *
 * Note! While passing an options array here might be convenient from a calling-code standpoint,
 *       this function is intended to serve as a very low-level foundation upon which to build
 *       higher level functionality. For the sake of keeping costs explicit all arguments are
 *       passed directly.
 *
 * @since 6.9.0
 * @access private
 *
 * @param string    $bytes             UTF-8 encoded string which might include invalid spans of bytes.
 * @param int       $at                Where to start scanning.
 * @param int       $invalid_length    Will be set to how many bytes are to be ignored after `$at`.
 * @param int|null  $max_bytes         Stop scanning after this many bytes have been seen.
 * @param int|null  $max_code_points   Stop scanning after this many code points have been seen.
 * @param bool|null $has_noncharacters Set to indicate if scanned string contained noncharacters.
 * @return int How many code points were successfully scanned.
 */
function _wp_scan_utf8( string $bytes, int &$at, int &$invalid_length, ?int $max_bytes = null, ?int $max_code_points = null, ?bool &$has_noncharacters = null ): int {
	$byte_length       = strlen( $bytes );
	$end               = min( $byte_length, $at + ( $max_bytes ?? PHP_INT_MAX ) );
	$invalid_length    = 0;
	$count             = 0;
	$max_count         = $max_code_points ?? PHP_INT_MAX;
	$has_noncharacters = false;

	for ( $i = $at; $i < $end && $count <= $max_count; $i++ ) {
		/*
		 * Quickly skip past US-ASCII bytes, all of which are valid UTF-8.
		 *
		 * This optimization step improves the speed from 10x to 100x
		 * depending on whether the JIT has optimized the function.
		 */
		$ascii_byte_count = strspn(
			$bytes,
			"\x00\x01\x02\x03\x04\x05\x06\x07\x08\x09\x0a\x0b\x0c\x0d\x0e\x0f" .
			"\x10\x11\x12\x13\x14\x15\x16\x17\x18\x19\x1a\x1b\x1c\x1d\x1e\x1f" .
			" !\"#$%&'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\\]^_`abcdefghijklmnopqrstuvwxyz{|}~\x7f",
			$i,
			$end - $i
		);

		if ( $count + $ascii_byte_count >= $max_count ) {
			$at    = $i + ( $max_count - $count );
			$count = $max_count;
			return $count;
		}

		$count += $ascii_byte_count;
		$i     += $ascii_byte_count;

		if ( $i >= $end ) {
			$at = $end;
			return $count;
		}

		/**
		 * The above fast-track handled all single-byte UTF-8 characters. What
		 * follows MUST be a multibyte sequence otherwise there’s invalid UTF-8.
		 *
		 * Therefore everything past here is checking those multibyte sequences.
		 *
		 * It may look like there’s a need to check against the max bytes here,
		 * but since each match of a single character returns, this functions will
		 * bail already if crossing the max-bytes threshold. This function SHALL
		 * NOT return in the middle of a multi-byte character, so if a character
		 * falls on each side of the max bytes, the entire character will be scanned.
		 *
		 * Because it’s possible that there are truncated characters, the use of
		 * the null-coalescing operator with "\xC0" is a convenience for skipping
		 * length checks on every continuation bytes. This works because 0xC0 is
		 * always invalid in a UTF-8 string, meaning that if the string has been
		 * truncated, it will find 0xC0 and reject as invalid UTF-8.
		 *
		 * > [The following table] lists all of the byte sequences that are well-formed
		 * > in UTF-8. A range of byte values such as A0..BF indicates that any byte
		 * > from A0 to BF (inclusive) is well-formed in that position. Any byte value
		 * > outside of the ranges listed is ill-formed.
		 *
		 * > Table 3-7. Well-Formed UTF-8 Byte Sequences
		 *  ╭─────────────────────┬────────────┬──────────────┬─────────────┬──────────────╮
		 *  │ Code Points         │ First Byte │ Second Byte  │ Third Byte  │ Fourth Byte  │
		 *  ├─────────────────────┼────────────┼──────────────┼─────────────┼──────────────┤
		 *  │ U+0000..U+007F      │ 00..7F     │              │             │              │
		 *  │ U+0080..U+07FF      │ C2..DF     │ 80..BF       │             │              │
		 *  │ U+0800..U+0FFF      │ E0         │ A0..BF       │ 80..BF      │              │
		 *  │ U+1000..U+CFFF      │ E1..EC     │ 80..BF       │ 80..BF      │              │
		 *  │ U+D000..U+D7FF      │ ED         │ 80..9F       │ 80..BF      │              │
		 *  │ U+E000..U+FFFF      │ EE..EF     │ 80..BF       │ 80..BF      │              │
		 *  │ U+10000..U+3FFFF    │ F0         │ 90..BF       │ 80..BF      │ 80..BF       │
		 *  │ U+40000..U+FFFFF    │ F1..F3     │ 80..BF       │ 80..BF      │ 80..BF       │
		 *  │ U+100000..U+10FFFF  │ F4         │ 80..8F       │ 80..BF      │ 80..BF       │
		 *  ╰─────────────────────┴────────────┴──────────────┴─────────────┴──────────────╯
		 *
		 * @see https://www.unicode.org/versions/Unicode16.0.0/core-spec/chapter-3/#G27506
		 */

		// Valid two-byte code points.
		$b1 = ord( $bytes[ $i ] );
		$b2 = ord( $bytes[ $i + 1 ] ?? "\xC0" );

		if ( $b1 >= 0xC2 && $b1 <= 0xDF && $b2 >= 0x80 && $b2 <= 0xBF ) {
			++$count;
			++$i;
			continue;
		}

		// Valid three-byte code points.
		$b3 = ord( $bytes[ $i + 2 ] ?? "\xC0" );

		if ( $b3 < 0x80 || $b3 > 0xBF ) {
			goto invalid_utf8;
		}

		if (
			( 0xE0 === $b1 && $b2 >= 0xA0 && $b2 <= 0xBF ) ||
			( $b1 >= 0xE1 && $b1 <= 0xEC && $b2 >= 0x80 && $b2 <= 0xBF ) ||
			( 0xED === $b1 && $b2 >= 0x80 && $b2 <= 0x9F ) ||
			( $b1 >= 0xEE && $b1 <= 0xEF && $b2 >= 0x80 && $b2 <= 0xBF )
		) {
			++$count;
			$i += 2;

			// Covers the range U+FDD0–U+FDEF, U+FFFE, U+FFFF.
			if ( 0xEF === $b1 ) {
				$has_noncharacters |= (
					( 0xB7 === $b2 && $b3 >= 0x90 && $b3 <= 0xAF ) ||
					( 0xBF === $b2 && ( 0xBE === $b3 || 0xBF === $b3 ) )
				);
			}

			continue;
		}

		// Valid four-byte code points.
		$b4 = ord( $bytes[ $i + 3 ] ?? "\xC0" );

		if ( $b4 < 0x80 || $b4 > 0xBF ) {
			goto invalid_utf8;
		}

		if (
			( 0xF0 === $b1 && $b2 >= 0x90 && $b2 <= 0xBF ) ||
			( $b1 >= 0xF1 && $b1 <= 0xF3 && $b2 >= 0x80 && $b2 <= 0xBF ) ||
			( 0xF4 === $b1 && $b2 >= 0x80 && $b2 <= 0x8F )
		) {
			++$count;
			$i += 3;

			// Covers U+1FFFE, U+1FFFF, U+2FFFE, U+2FFFF, …, U+10FFFE, U+10FFFF.
			$has_noncharacters |= (
				( 0x0F === ( $b2 & 0x0F ) ) &&
				0xBF === $b3 &&
				( 0xBE === $b4 || 0xBF === $b4 )
			);

			continue;
		}

		/**
		 * When encountering invalid byte sequences, Unicode suggests finding the
		 * maximal subpart of a text and replacing that subpart with a single
		 * replacement character.
		 *
		 * > This practice is more secure because it does not result in the
		 * > conversion consuming parts of valid sequences as though they were
		 * > invalid. It also guarantees at least one replacement character will
		 * > occur for each instance of an invalid sequence in the original text.
		 * > Furthermore, this practice can be defined consistently for better
		 * > interoperability between different implementations of conversion.
		 *
		 * @see https://www.unicode.org/versions/Unicode16.0.0/core-spec/chapter-5/#G40630
		 */
		invalid_utf8:
		$at             = $i;
		$invalid_length = 1;

		// Single-byte and two-byte characters.
		if ( ( 0x00 === ( $b1 & 0x80 ) ) || ( 0xC0 === ( $b1 & 0xE0 ) ) ) {
			return $count;
		}

		$b2 = ord( $bytes[ $i + 1 ] ?? "\xC0" );
		$b3 = ord( $bytes[ $i + 2 ] ?? "\xC0" );

		// Find the maximal subpart and skip past it.
		if ( 0xE0 === ( $b1 & 0xF0 ) ) {
			// Three-byte characters.
			$b2_valid = (
				( 0xE0 === $b1 && $b2 >= 0xA0 && $b2 <= 0xBF ) ||
				( $b1 >= 0xE1 && $b1 <= 0xEC && $b2 >= 0x80 && $b2 <= 0xBF ) ||
				( 0xED === $b1 && $b2 >= 0x80 && $b2 <= 0x9F ) ||
				( $b1 >= 0xEE && $b1 <= 0xEF && $b2 >= 0x80 && $b2 <= 0xBF )
			);

			$invalid_length = min( $end - $i, $b2_valid ? 2 : 1 );
			return $count;
		} elseif ( 0xF0 === ( $b1 & 0xF8 ) ) {
			// Four-byte characters.
			$b2_valid = (
				( 0xF0 === $b1 && $b2 >= 0x90 && $b2 <= 0xBF ) ||
				( $b1 >= 0xF1 && $b1 <= 0xF3 && $b2 >= 0x80 && $b2 <= 0xBF ) ||
				( 0xF4 === $b1 && $b2 >= 0x80 && $b2 <= 0x8F )
			);

			$b3_valid = $b3 >= 0x80 && $b3 <= 0xBF;

			$invalid_length = min( $end - $i, $b2_valid ? ( $b3_valid ? 3 : 2 ) : 1 );
			return $count;
		}

		return $count;
	}

	$at = $i;
	return $count;
}

/**
 * Fallback mechanism for safely validating UTF-8 bytes.
 *
 * @since 6.9.0
 * @access private
 *
 * @see wp_is_valid_utf8()
 *
 * @param string $bytes String which might contain text encoded as UTF-8.
 * @return bool Whether the provided bytes can decode as valid UTF-8.
 */
function _wp_is_valid_utf8_fallback( string $bytes ): bool {
	$bytes_length = strlen( $bytes );
	if ( 0 === $bytes_length ) {
		return true;
	}

	$next_byte_at   = 0;
	$invalid_length = 0;

	_wp_scan_utf8( $bytes, $next_byte_at, $invalid_length );

	return $bytes_length === $next_byte_at && 0 === $invalid_length;
}

/**
 * Fallback mechanism for replacing invalid spans of UTF-8 bytes.
 *
 * Example:
 *
 *     'Pi�a' === _wp_scrub_utf8_fallback( "Pi\xF1a" ); // “ñ” is 0xF1 in Windows-1252.
 *
 * @since 6.9.0
 * @access private
 *
 * @see wp_scrub_utf8()
 *
 * @param string $bytes UTF-8 encoded string which might contain spans of invalid bytes.
 * @return string Input string with spans of invalid bytes swapped with the replacement character.
 */
function _wp_scrub_utf8_fallback( string $bytes ): string {
	$bytes_length   = strlen( $bytes );
	$next_byte_at   = 0;
	$was_at         = 0;
	$invalid_length = 0;
	$scrubbed       = '';

	while ( $next_byte_at <= $bytes_length ) {
		_wp_scan_utf8( $bytes, $next_byte_at, $invalid_length );

		if ( $next_byte_at >= $bytes_length ) {
			if ( 0 === $was_at ) {
				return $bytes;
			}

			return $scrubbed . substr( $bytes, $was_at, $next_byte_at - $was_at - $invalid_length );
		}

		$scrubbed .= substr( $bytes, $was_at, $next_byte_at - $was_at );
		$scrubbed .= "\u{FFFD}";

		$next_byte_at += $invalid_length;
		$was_at        = $next_byte_at;
	}

	return $scrubbed;
}

/**
 * Returns how many code points are found in the given UTF-8 string.
 *
 * Invalid spans of bytes count as a single code point according
 * to the maximal subpart rule. This function is a fallback method
 * for calling `mb_strlen( $text, 'UTF-8' )`.
 *
 * When negative values are provided for the byte offsets or length,
 * this will always report zero code points.
 *
 * Example:
 *
 *     4  === _wp_utf8_codepoint_count( 'text' );
 *
 *     // Groups are 'test', "\x90" as '�', 'wp', "\xE2\x80" as '�', "\xC0" as '�', and 'test'.
 *     13 === _wp_utf8_codepoint_count( "test\x90wp\xE2\x80\xC0test" );
 *
 * @since 6.9.0
 * @access private
 *
 * @param string $text            Count code points in this string.
 * @param ?int   $byte_offset     Start counting after this many bytes in `$text`. Must be positive.
 * @param ?int   $max_byte_length Optional. Stop counting after having scanned past this many bytes.
 *                                Default is to scan until the end of the string. Must be positive.
 * @return int How many code points were found.
 */
function _wp_utf8_codepoint_count( string $text, ?int $byte_offset = 0, ?int $max_byte_length = PHP_INT_MAX ): int {
	if ( $byte_offset < 0 ) {
		return 0;
	}

	$count           = 0;
	$at              = $byte_offset;
	$end             = strlen( $text );
	$invalid_length  = 0;
	$max_byte_length = min( $end - $at, $max_byte_length );

	while ( $at < $end && ( $at - $byte_offset ) < $max_byte_length ) {
		$count += _wp_scan_utf8( $text, $at, $invalid_length, $max_byte_length - ( $at - $byte_offset ) );
		$count += $invalid_length > 0 ? 1 : 0;
		$at    += $invalid_length;
	}

	return $count;
}

/**
 * Given a starting offset within a string and a maximum number of code points,
 * return how many bytes are occupied by the span of characters.
 *
 * Invalid spans of bytes count as a single code point according to the maximal
 * subpart rule. This function is a fallback method for calling
 * `strlen( mb_substr( substr( $text, $at ), 0, $max_code_points ) )`.
 *
 * @since 6.9.0
 * @access private
 *
 * @param string $text              Count bytes of span in this text.
 * @param int    $byte_offset       Start counting at this byte offset.
 * @param int    $max_code_points   Stop counting after this many code points have been seen,
 *                                  or at the end of the string.
 * @param ?int   $found_code_points Optional. Will be set to number of found code points in
 *                                  span, as this might be smaller than the maximum count if
 *                                  the string is not long enough.
 * @return int Number of bytes spanned by the code points.
 */
function _wp_utf8_codepoint_span( string $text, int $byte_offset, int $max_code_points, ?int &$found_code_points = 0 ): int {
	$was_at            = $byte_offset;
	$invalid_length    = 0;
	$end               = strlen( $text );
	$found_code_points = 0;

	while ( $byte_offset < $end && $found_code_points < $max_code_points ) {
		$needed      = $max_code_points - $found_code_points;
		$chunk_count = _wp_scan_utf8( $text, $byte_offset, $invalid_length, null, $needed );

		$found_code_points += $chunk_count;

		// Invalid spans only convey one code point count regardless of how long they are.
		if ( 0 !== $invalid_length && $found_code_points < $max_code_points ) {
			++$found_code_points;
			$byte_offset += $invalid_length;
		}
	}

	return $byte_offset - $was_at;
}

/**
 * Fallback support for determining if a string contains Unicode noncharacters.
 *
 * @since 6.9.0
 * @access private
 *
 * @see \wp_has_noncharacters()
 *
 * @param string $text Are there noncharacters in this string?
 * @return bool Whether noncharacters were found in the string.
 */
function _wp_has_noncharacters_fallback( string $text ): bool {
	$at                = 0;
	$invalid_length    = 0;
	$has_noncharacters = false;
	$end               = strlen( $text );

	while ( $at < $end && ! $has_noncharacters ) {
		_wp_scan_utf8( $text, $at, $invalid_length, null, null, $has_noncharacters );
		$at += $invalid_length;
	}

	return $has_noncharacters;
}

/**
 * Converts a string from ISO-8859-1 to UTF-8, maintaining backwards compatibility
 * with the deprecated function from the PHP standard library.
 *
 * @since 6.9.0
 * @access private
 *
 * @see \utf8_encode()
 *
 * @param string $iso_8859_1_text Text treated as ISO-8859-1 (latin1) bytes.
 * @return string Text converted into UTF-8.
 */
function _wp_utf8_encode_fallback( $iso_8859_1_text ) {
	$iso_8859_1_text = (string) $iso_8859_1_text;
	$at              = 0;
	$was_at          = 0;
	$end             = strlen( $iso_8859_1_text );
	$utf8            = '';

	while ( $at < $end ) {
		// US-ASCII bytes are identical in ISO-8859-1 and UTF-8. These are 0x00–0x7F.
		$ascii_byte_count = strspn(
			$iso_8859_1_text,
			"\x00\x01\x02\x03\x04\x05\x06\x07\x08\x09\x0a\x0b\x0c\x0d\x0e\x0f" .
			"\x10\x11\x12\x13\x14\x15\x16\x17\x18\x19\x1a\x1b\x1c\x1d\x1e\x1f" .
			" !\"#$%&'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\\]^_`abcdefghijklmnopqrstuvwxyz{|}~\x7f",
			$at
		);

		if ( $ascii_byte_count > 0 ) {
			$at += $ascii_byte_count;
			continue;
		}

		// All other bytes transform into two-byte UTF-8 sequences.
		$code_point = ord( $iso_8859_1_text[ $at ] );
		$byte1      = chr( 0xC0 | ( $code_point >> 6 ) );
		$byte2      = chr( 0x80 | ( $code_point & 0x3F ) );

		$utf8 .= substr( $iso_8859_1_text, $was_at, $at - $was_at );
		$utf8 .= "{$byte1}{$byte2}";

		++$at;
		$was_at = $at;
	}

	if ( 0 === $was_at ) {
		return $iso_8859_1_text;
	}

	$utf8 .= substr( $iso_8859_1_text, $was_at );
	return $utf8;
}

/**
 * Converts a string from UTF-8 to ISO-8859-1, maintaining backwards compatibility
 * with the deprecated function from the PHP standard library.
 *
 * @since 6.9.0
 * @access private
 *
 * @see \utf8_decode()
 *
 * @param string $utf8_text Text treated as UTF-8 bytes.
 * @return string Text converted into ISO-8859-1.
 */
function _wp_utf8_decode_fallback( $utf8_text ) {
	$utf8_text       = (string) $utf8_text;
	$at              = 0;
	$was_at          = 0;
	$end             = strlen( $utf8_text );
	$iso_8859_1_text = '';

	while ( $at < $end ) {
		// US-ASCII bytes are identical in ISO-8859-1 and UTF-8. These are 0x00–0x7F.
		$ascii_byte_count = strspn(
			$utf8_text,
			"\x00\x01\x02\x03\x04\x05\x06\x07\x08\x09\x0a\x0b\x0c\x0d\x0e\x0f" .
			"\x10\x11\x12\x13\x14\x15\x16\x17\x18\x19\x1a\x1b\x1c\x1d\x1e\x1f" .
			" !\"#$%&'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\\]^_`abcdefghijklmnopqrstuvwxyz{|}~\x7f",
			$at
		);

		if ( $ascii_byte_count > 0 ) {
			$at += $ascii_byte_count;
			continue;
		}

		$next_at        = $at;
		$invalid_length = 0;
		$found          = _wp_scan_utf8( $utf8_text, $next_at, $invalid_length, null, 1 );
		$span_length    = $next_at - $at;
		$next_byte      = '?';

		if ( 1 !== $found ) {
			if ( $invalid_length > 0 ) {
				$next_byte = '';
				goto flush_sub_part;
			}

			break;
		}

		// All convertible code points are two-bytes long.
		$byte1 = ord( $utf8_text[ $at ] );
		if ( 0xC0 !== ( $byte1 & 0xE0 ) ) {
			goto flush_sub_part;
		}

		// All convertible code points are not greater than U+FF.
		$byte2      = ord( $utf8_text[ $at + 1 ] );
		$code_point = ( ( $byte1 & 0x1F ) << 6 ) | ( ( $byte2 & 0x3F ) );
		if ( $code_point > 0xFF ) {
			goto flush_sub_part;
		}

		$next_byte = chr( $code_point );

		flush_sub_part:
		$iso_8859_1_text .= substr( $utf8_text, $was_at, $at - $was_at );
		$iso_8859_1_text .= $next_byte;
		$at              += $span_length;
		$was_at           = $at;

		if ( $invalid_length > 0 ) {
			$iso_8859_1_text .= '?';
			$at              += $invalid_length;
			$was_at           = $at;
		}
	}

	if ( 0 === $was_at ) {
		return $utf8_text;
	}

	$iso_8859_1_text .= substr( $utf8_text, $was_at );
	return $iso_8859_1_text;
}

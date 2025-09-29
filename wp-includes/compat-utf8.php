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
 * Note! This functions many arguments are passed without and “options”
 * array. This choice is based on the fact that this is a low-level function
 * and there’s no need to create an array of items on every invocation.
 *
 * @since 6.9.0
 * @access private
 *
 * @param string   $bytes           UTF-8 encoded string which might include invalid spans of bytes.
 * @param int      $at              Where to start scanning.
 * @param int      $invalid_length  Will be set to how many bytes are to be ignored after `$at`.
 * @param int|null $max_bytes       Stop scanning after this many bytes have been seen.
 * @param int|null $max_code_points Stop scanning after this many code points have been seen.
 * @return int How many code points were successfully scanned.
 */
function _wp_scan_utf8( string $bytes, int &$at, int &$invalid_length, ?int $max_bytes = null, ?int $max_code_points = null ): int {
	$byte_length    = strlen( $bytes );
	$end            = min( $byte_length, $at + ( $max_bytes ?? PHP_INT_MAX ) );
	$invalid_length = 0;
	$count          = 0;
	$max_count      = $max_code_points ?? PHP_INT_MAX;

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

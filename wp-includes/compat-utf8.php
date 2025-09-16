<?php

/**
 * Fallback mechanism for safely validating UTF-8 bytes.
 *
 * By implementing a raw method here the code will behave in the same way on
 * all installed systems, regardless of what extensions are installed.
 *
 * @see wp_is_valid_utf8
 *
 * @since 6.9.0
 * @access private
 *
 * @param string $bytes String which might contain text encoded as UTF-8.
 * @return bool Whether the provided bytes can decode as valid UTF-8.
 */
function _wp_is_valid_utf8_fallback( string $bytes ): bool {
	$end = strlen( $bytes );

	for ( $i = 0; $i < $end; $i++ ) {
		/*
		 * Quickly skip past US-ASCII bytes, all of which are valid UTF-8.
		 *
		 * This optimization step improves the speed from 10x to 100x
		 * depending on whether the JIT has optimized the function.
		 */
		$i += strspn(
			$bytes,
			"\x00\x01\x02\x03\x04\x05\x06\x07\x08\x09\x0a\x0b\x0c\x0d\x0e\x0f" .
			"\x10\x11\x12\x13\x14\x15\x16\x17\x18\x19\x1a\x1b\x1c\x1d\x1e\x1f" .
			" !\"#$%&'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\\]^_`abcdefghijklmnopqrstuvwxyz{|}~\x7f",
			$i
		);
		if ( $i >= $end ) {
			break;
		}

		/**
		 * The above fast-track handled all single-byte UTF-8 characters. What
		 * follows MUST be a multibyte sequence otherwise there’s invalid UTF-8.
		 *
		 * Therefore everything past here is checking those multibyte sequences.
		 * Because it’s possible that there are truncated characters, the use of
		 * the null-coalescing operator with "\xC0" is a convenience for skipping
		 * length checks on every continuation bytes. This works because 0xC0 is
		 * always invalid in a UTF-8 string, meaning that if the string has been
		 * truncated, it will find 0xC0 and reject as invalid UTF-8.
		 *
		 *  > [The following table] lists all of the byte sequences that are well-formed
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
		 * Notice that all valid third and forth bytes are in the range 80..BF. This
		 * validator takes advantage of that to only check the range of those bytes once.
		 *
		 * @see https://lemire.me/blog/2018/05/09/how-quickly-can-you-check-that-a-string-is-valid-unicode-utf-8/
		 * @see https://www.unicode.org/versions/Unicode16.0.0/core-spec/chapter-3/#G27506
		 */

		$b1 = ord( $bytes[ $i ] );
		$b2 = ord( $bytes[ $i + 1 ] ?? "\xC0" );

		// Valid two-byte code points.

		if ( $b1 >= 0xC2 && $b1 <= 0xDF && $b2 >= 0x80 && $b2 <= 0xBF ) {
			++$i;
			continue;
		}

		$b3 = ord( $bytes[ $i + 2 ] ?? "\xC0" );

		// Valid three-byte code points.

		if ( $b3 < 0x80 || $b3 > 0xBF ) {
			return false;
		}

		if (
			( 0xE0 === $b1 && $b2 >= 0xA0 && $b2 <= 0xBF ) ||
			( $b1 >= 0xE1 && $b1 <= 0xEC && $b2 >= 0x80 && $b2 <= 0xBF ) ||
			( 0xED === $b1 && $b2 >= 0x80 && $b2 <= 0x9F ) ||
			( $b1 >= 0xEE && $b1 <= 0xEF && $b2 >= 0x80 && $b2 <= 0xBF )
		) {
			$i += 2;
			continue;
		}

		$b4 = ord( $bytes[ $i + 3 ] ?? "\xC0" );

		// Valid four-byte code points.

		if ( $b4 < 0x80 || $b4 > 0xBF ) {
			return false;
		}

		if (
			( 0xF0 === $b1 && $b2 >= 0x90 && $b2 <= 0xBF ) ||
			( $b1 >= 0xF1 && $b1 <= 0xF3 && $b2 >= 0x80 && $b2 <= 0xBF ) ||
			( 0xF4 === $b1 && $b2 >= 0x80 && $b2 <= 0x8F )
		) {
			$i += 3;
			continue;
		}

		// Any other sequence is invalid.
		return false;
	}

	// Reaching the end implies validating every byte.
	return true;
}

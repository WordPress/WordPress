<?php

if ( extension_loaded( 'mbstring' ) ) :
	/**
	 * Determines if a given byte string represents a valid UTF-8 encoding.
	 *
	 * Note that it’s unlikely for non-UTF-8 data to validate as UTF-8, but
	 * it is still possible. Many texts are simultaneously valid UTF-8,
	 * valid US-ASCII, and valid ISO-8859-1 (`latin1`).
	 *
	 * Example:
	 *
	 *     true === wp_is_valid_utf8( '' );
	 *     true === wp_is_valid_utf8( 'just a test' );
	 *     true === wp_is_valid_utf8( "\xE2\x9C\x8F" );    // Pencil, U+270F.
	 *     true === wp_is_valid_utf8( "\u{270F}" );        // Pencil, U+270F.
	 *     true === wp_is_valid_utf8( '✏' );              // Pencil, U+270F.
	 *
	 *     false === wp_is_valid_utf8( "just \xC0 test" ); // Invalid bytes.
	 *     false === wp_is_valid_utf8( "\xE2\x9C" );       // Invalid/incomplete sequences.
	 *     false === wp_is_valid_utf8( "\xC1\xBF" );       // Overlong sequences.
	 *     false === wp_is_valid_utf8( "\xED\xB0\x80" );   // Surrogate halves.
	 *     false === wp_is_valid_utf8( "B\xFCch" );        // ISO-8859-1 high-bytes.
	 *                                                     // E.g. The “ü” in ISO-8859-1 is a single byte 0xFC,
	 *                                                     // but in UTF-8 is the two-byte sequence 0xC3 0xBC.
	 *
	 *  A “valid” string consists of “well-formed UTF-8 code unit sequence[s],” meaning
	 *  that the bytes conform to the UTF-8 encoding scheme, all characters use the minimal
	 *  byte sequence required by UTF-8, and that no sequence encodes a UTF-16 surrogate
	 *  code point or any character above the representable range.
	 *
	 * @see https://www.unicode.org/versions/Unicode16.0.0/core-spec/chapter-3/#G32860
	 *
	 * @since 6.9.0
	 *
	 * @param string $bytes String which might contain text encoded as UTF-8.
	 * @return bool Whether the provided bytes can decode as valid UTF-8.
	 */
	function wp_is_valid_utf8( string $bytes ): bool {
		return mb_check_encoding( $bytes, 'UTF-8' );
	}
else :
	/**
	 * Fallback function for validating UTF-8.
	 *
	 * @ignore
	 * @private
	 *
	 * @since 6.9.0
	 */
	function wp_is_valid_utf8( string $string ): bool {
		return _wp_is_valid_utf8_fallback( $string );
	}
endif;

if (
	extension_loaded( 'mbstring' ) &&
	// Maximal subpart substitution introduced by php/php-src@04e59c916f12b322ac55f22314e31bd0176d01cb.
	version_compare( PHP_VERSION, '8.1.6', '>=' )
) :
	/**
	 * Replaces ill-formed UTF-8 byte sequences with the Unicode Replacement Character.
	 *
	 * Knowing what to do in the presence of text encoding issues can be complicated.
	 * This function replaces invalid spans of bytes to neutralize any corruption that
	 * may be there and prevent it from causing further problems downstream.
	 *
	 * However, it’s not always ideal to replace those bytes. In some settings it may
	 * be best to leave the invalid bytes in the string so that downstream code can handle
	 * them in a specific way. Replacing the bytes too early, like escaping for HTML too
	 * early, can introduce other forms of corruption and data loss.
	 *
	 * When in doubt, use this function to replace spans of invalid bytes.
	 *
	 * Replacement follows the “maximal subpart” algorithm for secure and interoperable
	 * strings. This can lead to sequences of multiple replacement characters in a row.
	 *
	 * Example:
	 *
	 *     // Valid strings come through unchanged.
	 *     'test' === wp_scrub_utf8( 'test' );
	 *
	 *     // Invalid sequences of bytes are replaced.
	 *     $invalid = "the byte \xC0 is never allowed in a UTF-8 string.";
	 *     "the byte \u{FFFD} is never allowed in a UTF-8 string." === wp_scrub_utf8( $invalid, true );
	 *     'the byte � is never allowed in a UTF-8 string.' === wp_scrub_utf8( $invalid, true );
	 *
	 *     // Maximal subparts are replaced individually.
	 *     '.�.' === wp_scrub_utf8( ".\xC0." );              // C0 is never valid.
	 *     '.�.' === wp_scrub_utf8( ".\xE2\x8C." );          // Missing A3 at end.
	 *     '.��.' === wp_scrub_utf8( ".\xE2\x8C\xE2\x8C." ); // Maximal subparts replaced separately.
	 *     '.��.' === wp_scrub_utf8( ".\xC1\xBF." );         // Overlong sequence.
	 *     '.���.' === wp_scrub_utf8( ".\xED\xA0\x80." );    // Surrogate half.
	 *
	 * Note! The Unicode Replacement Character is itself a Unicode character (U+FFFD).
	 * Once a span of invalid bytes has been replaced by one, it will not be possible
	 * to know whether the replacement character was originally intended to be there
	 * or if it is the result of scrubbing bytes. It is ideal to leave replacement for
	 * display only, but some contexts (e.g. generating XML or passing data into a
	 * large language model) require valid input strings.
	 *
	 * @since 6.9.0
	 *
	 * @see https://www.unicode.org/versions/Unicode16.0.0/core-spec/chapter-5/#G40630
	 *
	 * @param string $text String which is assumed to be UTF-8 but may contain invalid sequences of bytes.
	 * @return string Input text with invalid sequences of bytes replaced with the Unicode replacement character.
	 */
	function wp_scrub_utf8( $text ) {
		/*
		 * While it looks like setting the substitute character could fail,
		 * the internal PHP code will never fail when provided a valid
		 * code point as a number. In this case, there’s no need to check
		 * its return value to see if it succeeded.
		 */
		$prev_replacement_character = mb_substitute_character();
		mb_substitute_character( 0xFFFD );
		$scrubbed = mb_scrub( $text, 'UTF-8' );
		mb_substitute_character( $prev_replacement_character );

		return $scrubbed;
	}
else :
	/**
	 * Fallback function for scrubbing UTF-8.
	 *
	 * @ignore
	 * @private
	 *
	 * @since 6.9.0
	 */
	function wp_scrub_utf8( $text ) {
		return _wp_scrub_utf8_fallback( $text );
	}
endif;

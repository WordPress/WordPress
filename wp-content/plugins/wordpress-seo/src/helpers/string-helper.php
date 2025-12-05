<?php

namespace Yoast\WP\SEO\Helpers;

/**
 * A helper object for string operations.
 */
class String_Helper {

	/**
	 * Strips all HTML tags including script and style.
	 *
	 * @param string $text The text to strip the tags from.
	 *
	 * @return string The processed string.
	 */
	public function strip_all_tags( $text ) {
		return \wp_strip_all_tags( $text );
	}

	/**
	 * Standardize whitespace in a string.
	 *
	 * Replace line breaks, carriage returns, tabs with a space, then remove double spaces.
	 *
	 * @param string $text Text input to standardize.
	 *
	 * @return string
	 */
	public function standardize_whitespace( $text ) {
		return \trim( \str_replace( '  ', ' ', \str_replace( [ "\t", "\n", "\r", "\f" ], ' ', $text ) ) );
	}

	/**
	 * First strip out registered and enclosing shortcodes using native WordPress strip_shortcodes function.
	 * Then strip out the shortcodes with a filthy regex, because people don't properly register their shortcodes.
	 *
	 * @param string $text Input string that might contain shortcodes.
	 *
	 * @return string String without shortcodes.
	 */
	public function strip_shortcode( $text ) {
		return \preg_replace( '`\[[^\]]+\]`s', '', \strip_shortcodes( $text ) );
	}
}

<?php
/**
 * Custom CSS
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_One
 * @since 1.0.0
 */

/**
 * Generate CSS.
 *
 * @since 1.0.0
 *
 * @param string $selector The CSS selector.
 * @param string $style The CSS style.
 * @param string $value The CSS value.
 * @param string $prefix The CSS prefix.
 * @param string $suffix The CSS suffix.
 * @param bool   $echo Echo the styles.
 *
 * @return string
 */
function twenty_twenty_one_generate_css( $selector, $style, $value, $prefix = '', $suffix = '', $echo = true ) {

	// Bail early if we have no $selector elements or properties and $value.
	if ( ! $value || ! $selector ) {
		return '';
	}

	$css = sprintf( '%s { %s: %s; }', $selector, $style, $prefix . $value . $suffix );

	if ( $echo ) {
		/**
		 * Note to reviewers: $css contains auto-generated CSS.
		 * It is included inside <style> tags and can only be interpreted as CSS on the browser.
		 * Using wp_strip_all_tags() here is sufficient escaping since we just need to avoid
		 * malicious attempts to close </style> and open a <script>.
		 */
		echo wp_strip_all_tags( $css ); // phpcs:ignore WordPress.Security.EscapeOutput
	}
	return $css;
}

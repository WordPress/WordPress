<?php

namespace Yoast\WP\SEO\Helpers\Schema;

/**
 * Class HTML_Helper.
 */
class HTML_Helper {

	/**
	 * Sanitizes a HTML string by stripping all tags except headings, breaks, lists, links, paragraphs and formatting.
	 *
	 * @param string $html The original HTML.
	 *
	 * @return string The sanitized HTML.
	 */
	public function sanitize( $html ) {
		if ( ! $this->is_non_empty_string_or_stringable( $html ) ) {
			if ( \is_int( $html ) || \is_float( $html ) ) {
				return (string) $html;
			}

			return '';
		}

		return \strip_tags( $html, '<h1><h2><h3><h4><h5><h6><br><ol><ul><li><a><p><b><strong><i><em>' );
	}

	/**
	 * Strips the tags in a smart way.
	 *
	 * @param string $html The original HTML.
	 *
	 * @return string The sanitized HTML.
	 */
	public function smart_strip_tags( $html ) {
		if ( ! $this->is_non_empty_string_or_stringable( $html ) ) {
			if ( \is_int( $html ) || \is_float( $html ) ) {
				return (string) $html;
			}

			return '';
		}

		// Replace all new lines with spaces.
		$html = \preg_replace( '/(\r|\n)/', ' ', $html );

		// Replace <br> tags with spaces.
		$html = \preg_replace( '/<br(\s*)?\/?>/i', ' ', $html );

		// Replace closing </p> and other tags with the same tag with a space after it, so we don't end up connecting words when we remove them later.
		$html = \preg_replace( '/<\/(p|div|h\d)>/i', '</$1> ', $html );

		// Replace list items with list identifiers so it still looks natural.
		$html = \preg_replace( '/(<li[^>]*>)/i', '$1• ', $html );

		// Strip tags.
		$html = \wp_strip_all_tags( $html );

		// Replace multiple spaces with one space.
		$html = \preg_replace( '!\s+!', ' ', $html );

		return \trim( $html );
	}

	/**
	 * Verifies that the received input is either a string or stringable object.
	 *
	 * @param string $html The original HTML.
	 *
	 * @return bool
	 */
	private function is_non_empty_string_or_stringable( $html ) {
		return ( \is_string( $html ) || ( \is_object( $html ) && \method_exists( $html, '__toString' ) ) ) && ! empty( $html );
	}
}

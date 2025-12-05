<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
namespace Yoast\WP\SEO\Llms_Txt\Application;

/**
 * The escaper of markdown.
 */
class Markdown_Escaper {

	/**
	 * Escapes markdown text.
	 *
	 * @param string $text The markdown text to escape.
	 *
	 * @return string The escaped markdown text.
	 */
	public function escape_markdown_content( $text ) {
		// We have to decode the text first mostly because ampersands will be escaped below.
		$text = \html_entity_decode( $text, \ENT_QUOTES, 'UTF-8' );

		// Define a regex pattern for all the special characters in markdown that we want to escape.
		$pattern = '/[-#*+`._[\]()!&<>_{}|]/';

		$replacement = static function ( $matches ) {
			return '\\' . $matches[0];
		};

		return \preg_replace_callback( $pattern, $replacement, $text );
	}

	/**
	 * Escapes URLs in markdown.
	 *
	 * @param string $url The markdown URL to escape.
	 *
	 * @return string The escaped markdown URL.
	 */
	public function escape_markdown_url( $url ) {
		$escaped_url = \str_replace( [ ' ', '(', ')', '\\' ], [ '%20', '%28', '%29', '%5C' ], $url );

		return $escaped_url;
	}
}

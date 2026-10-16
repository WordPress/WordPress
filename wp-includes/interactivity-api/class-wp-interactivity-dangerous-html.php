<?php
/**
 * Interactivity API: WP_Interactivity_Dangerous_HTML class.
 *
 * @package WordPress
 * @subpackage Interactivity API
 * @since 7.2.0
 */

/**
 * Token that marks an HTML string as trusted, for server directive processing
 * to render as the content of an element.
 *
 * Create tokens with {@see wp_interactivity_as_dangerous_html()}, which
 * documents the risks of marking HTML as trusted.
 *
 * The HTML is kept in a private property, so encoding a token as JSON never
 * includes it. A token restored by unserialization is inert: it holds no HTML,
 * so it never renders.
 *
 * @since 7.2.0
 *
 * @access private
 */
final class WP_Interactivity_Dangerous_HTML {
	/**
	 * The trusted HTML. It is `null` for a token restored by unserialization,
	 * which never renders.
	 *
	 * @since 7.2.0
	 * @var string|null
	 */
	private $html = null;

	/**
	 * Constructor.
	 *
	 * @since 7.2.0
	 *
	 * @param string $html The HTML to mark as trusted. It is not sanitized.
	 */
	public function __construct( string $html ) {
		$this->html = $html;
	}

	/**
	 * Gets the trusted HTML of a value.
	 *
	 * @since 7.2.0
	 *
	 * @param mixed $value Any value.
	 * @return string|null The HTML of the token if the value is a token whose constructor
	 *                     ran, or `null` for any other value.
	 */
	public static function get_html( $value ): ?string {
		return $value instanceof self ? $value->html : null;
	}

	/**
	 * Makes a token restored by unserialization inert.
	 *
	 * Clears the HTML, so the token never renders, and reports the unserialization
	 * instead of throwing, so a page that cached a token keeps working.
	 *
	 * @since 7.2.0
	 */
	public function __wakeup(): void {
		$this->html = null;

		_doing_it_wrong(
			__METHOD__,
			__( 'A token that marks HTML as trusted should not be unserialized. The unserialized token holds no HTML and does not render.' ),
			'7.2.0'
		);
	}
}

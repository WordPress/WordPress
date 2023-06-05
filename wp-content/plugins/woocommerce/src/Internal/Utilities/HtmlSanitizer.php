<?php

namespace Automattic\WooCommerce\Internal\Utilities;

/**
 * Utility for re-using WP Kses-based sanitization rules.
 */
class HtmlSanitizer {
	/**
	 * Rules for allowing minimal HTML (breaks, images, paragraphs and spans) without any links.
	 */
	public const LOW_HTML_BALANCED_TAGS_NO_LINKS = array(
		'pre_processors'  => array(
			'stripslashes',
			'force_balance_tags',
		),
		'wp_kses_rules'   => array(
			'br'   => true,
			'img'  => array(
				'alt'   => true,
				'class' => true,
				'src'   => true,
				'title' => true,
			),
			'p'    => array(
				'class' => true,
			),
			'span' => array(
				'class' => true,
				'title' => true,
			),
		),
	);

	/**
	 * Sanitizes the HTML according to the provided rules.
	 *
	 * @see wp_kses()
	 *
	 * @param string $html HTML string to be sanitized.
	 * @param array  $sanitizer_rules {
	 *     Optional and defaults to self::TRIMMED_BALANCED_LOW_HTML_NO_LINKS. Otherwise, one or more of the following
	 *     keys should be set.
	 *
	 *     @type array $pre_processors  Callbacks to run before invoking `wp_kses()`.
	 *     @type array $wp_kses_rules   Element names and attributes to allow, per `wp_kses()`.
	 * }
	 *
	 * @return string
	 */
	public function sanitize( string $html, array $sanitizer_rules = self::LOW_HTML_BALANCED_TAGS_NO_LINKS ): string {
		if ( isset( $sanitizer_rules['pre_processors'] ) && is_array( $sanitizer_rules['pre_processors'] ) ) {
			$html = $this->apply_string_callbacks( $sanitizer_rules['pre_processors'], $html );
		}

		// If no KSES rules are specified, assume all HTML should be stripped.
		$kses_rules = isset( $sanitizer_rules['wp_kses_rules'] ) && is_array( $sanitizer_rules['wp_kses_rules'] )
			? $sanitizer_rules['wp_kses_rules']
			: array();

		return wp_kses( $html, $kses_rules );
	}

	/**
	 * Applies callbacks used to process the string before and after wp_kses().
	 *
	 * If a callback is invalid we will short-circuit and return an empty string, on the grounds that it is better to
	 * output nothing than risky HTML. We also call the problem out via _doing_it_wrong() to highlight the problem (and
	 * increase the chances of this being caught during development).
	 *
	 * @param callable[] $callbacks The callbacks used to mutate the string.
	 * @param string     $string    The string being processed.
	 *
	 * @return string
	 */
	private function apply_string_callbacks( array $callbacks, string $string ): string {
		foreach ( $callbacks as $callback ) {
			if ( ! is_callable( $callback ) ) {
				_doing_it_wrong( __CLASS__ . '::apply', esc_html__( 'String processors must be an array of valid callbacks.', 'woocommerce' ), esc_html( WC()->version ) );
				return '';
			}

			$string = (string) $callback( $string );
		}

		return $string;
	}
}

<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Ajax
 */

/**
 * Class WPSEO_Shortcode_Filter.
 *
 * Used for parsing WP shortcodes with AJAX.
 */
class WPSEO_Shortcode_Filter {

	/**
	 * Initialize the AJAX hooks.
	 */
	public function __construct() {
		add_action( 'wp_ajax_wpseo_filter_shortcodes', [ $this, 'do_filter' ] );
	}

	/**
	 * Parse the shortcodes.
	 *
	 * @return void
	 */
	public function do_filter() {
		check_ajax_referer( 'wpseo-filter-shortcodes', 'nonce' );

		if ( ! isset( $_POST['data'] ) || ! is_array( $_POST['data'] ) ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Reason: WPSEO_Utils::format_json_encode is considered safe.
			wp_die( WPSEO_Utils::format_json_encode( [] ) );
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason: $shortcodes is getting sanitized later, before it's used.
		$shortcodes        = wp_unslash( $_POST['data'] );
		$parsed_shortcodes = [];

		foreach ( $shortcodes as $shortcode ) {
			if ( $shortcode !== sanitize_text_field( $shortcode ) ) {
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Reason: WPSEO_Utils::format_json_encode is considered safe.
				wp_die( WPSEO_Utils::format_json_encode( [] ) );
			}

			$parsed_shortcodes[] = [
				'shortcode' => $shortcode,
				'output'    => do_shortcode( $shortcode ),
			];
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Reason: WPSEO_Utils::format_json_encode is considered safe.
		wp_die( WPSEO_Utils::format_json_encode( $parsed_shortcodes ) );
	}
}

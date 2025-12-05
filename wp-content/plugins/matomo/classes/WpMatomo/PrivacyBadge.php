<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 */

namespace WpMatomo;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // if accessed directly
}

class PrivacyBadge {
	public function register_hooks() {
		add_shortcode( 'matomo_privacy_badge', [ $this, 'show_privacy_page' ] );
	}

	public function show_privacy_page( $atts ) {
		$a = shortcode_atts(
			[
				'size'  => '120',
				'align' => '',
			],
			$atts
		);

		$option = sprintf( ' width="%s" height="%s"', esc_attr( $a['size'] ), esc_attr( $a['size'] ) );

		if ( ! empty( $a['align'] ) ) {
			$option .= sprintf( ' align="%s"', esc_attr( $a['align'] ) );
		}

		$url = plugins_url( 'assets/img/privacybadge.png', MATOMO_ANALYTICS_FILE );

		$title = __( 'Your privacy protected! This website uses Matomo.', 'matomo' );

		return sprintf( '<img alt="%s" src="%s" %s>', $title, esc_attr( $url ), $option );
	}
}

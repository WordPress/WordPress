<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 */

namespace WpMatomo\Admin;

use WpMatomo\Capabilities;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // if accessed directly
}

class Info {
	const NONCE_NAME = 'matomo_newsletter';
	const FORM_NAME  = 'matomo_newsletter_signup';

	private function update_if_submitted() {
		if ( isset( $_POST )
			 && ! empty( $_POST[ self::FORM_NAME ] )
			 && is_admin()
			 && check_admin_referer( self::NONCE_NAME )
			 && $this->show_newsletter_signup()
			 && current_user_can( Capabilities::KEY_VIEW ) ) {
			$user   = wp_get_current_user();
			$locale = explode( '_', get_user_locale( $user->ID ) );
			wp_remote_get(
				'https://api.matomo.org/1.0/subscribeNewsletter/?' . http_build_query(
					[
						'email'     => $user->user_email,
						'wordpress' => 1,
						'language'  => $locale[0],
					]
				)
			);
			update_user_meta( $user->ID, self::FORM_NAME, '1' );

			return true;
		}
	}

	private function show_newsletter_signup() {
		if ( ! is_user_logged_in() ) {
			return false;
		}

		$user = wp_get_current_user();

		return ! get_user_meta( $user->ID, self::FORM_NAME, true );
	}

	public function show() {
		$this->render( 'info' );
	}

	public function show_multisite() {
		$this->render( 'info_multisite' );
	}

	private function render( $template ) {
		$signedup_newsletter = $this->update_if_submitted();
		$show_newsletter     = $this->show_newsletter_signup();

		include dirname( __FILE__ ) . '/views/' . $template . '.php';
	}
}

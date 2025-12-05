<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 */

namespace WpMatomo\Admin;

use WpMatomo\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // if accessed directly
}

class Admin {
	/**
	 * @param Settings $settings
	 */
	public function __construct( $settings, $init_menu = true ) {
		if ( $init_menu ) {
			new Menu( $settings );
		}

		add_action( 'admin_enqueue_scripts', [ $this, 'load_scripts' ] );
	}

	public static function is_matomo_admin() {
		return substr( self::get_current_page(), 0, 7 ) === 'matomo-';
	}

	public static function get_current_page() {
		return isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
	}

	public function load_scripts() {
		wp_enqueue_style( 'matomo_admin_css', plugins_url( 'assets/css/admin-style.css', MATOMO_ANALYTICS_FILE ), false, '1.0.3' );
		wp_enqueue_script( 'matomo_iframe_resizer', plugins_url( 'assets/js/iframeResizer.min.js', MATOMO_ANALYTICS_FILE ), [], '1.0', [ 'defer', false ] );

		wp_enqueue_script(
			'matomo-admin-js',
			plugins_url( '/assets/js/admin.js', MATOMO_ANALYTICS_FILE ),
			[ 'jquery' ],
			'1.0.3',
			true
		);
		wp_localize_script(
			'matomo-admin-js',
			'mtmSystemReportErrorNoticeAjax',
			[
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'matomo-systemreport-notice-dismiss' ),
			]
		);
		wp_localize_script(
			'matomo-admin-js',
			'mtmReferralDismissNoticeAjax',
			[
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'matomo-referral-notice-dismiss' ),
			]
		);
	}
}

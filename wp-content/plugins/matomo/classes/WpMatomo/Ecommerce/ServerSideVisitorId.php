<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 */

namespace WpMatomo\Ecommerce;

use WpMatomo\Logger;
use WpMatomo\Settings;
use WpMatomo\AjaxTracker;

class ServerSideVisitorId {

	const VISITOR_ID_SESSION_VAR_NAME = 'matomo-for-wordpress-visitor-id';

	/**
	 * @var Settings
	 */
	private $settings;

	/**
	 * @var Logger
	 */
	private $logger;

	public function __construct( Settings $settings, Logger $logger ) {
		$this->settings = $settings;
		$this->logger   = $logger;
	}

	public function register_hooks() {
		if ( did_action( 'woocommerce_init' ) ) {
			$this->force_server_side_visitor_id();
		} else {
			add_action( 'woocommerce_init', [ $this, 'force_server_side_visitor_id' ] );
		}
	}

	public function force_server_side_visitor_id() {
		if ( $this->is_visitor_id_cookie_present() ) {
			return; // cookie found, no need to force a server side generated one
		}

		if ( is_admin() ) {
			return;
		}

		if ( ! empty( $GLOBALS['MATOMO_LOADED_DIRECTLY'] ) ) {
			return;
		}

		$this->logger->log( 'ServerSideVisitorId: visitor ID cookie missing from request' );

		// only initialize the session early for requests that do not have the visitor ID cookie
		$this->initialize_woocommerce_session_if_needed();

		$visitor_id = WC()->session->get( self::VISITOR_ID_SESSION_VAR_NAME );
		if ( empty( $visitor_id ) ) {
			$this->logger->log( 'ServerSideVisitorId: no visitor ID in Woocommerce session, generating a new one' );

			$tracker    = new AjaxTracker( $this->settings );
			$visitor_id = $tracker->setNewVisitorId()->randomVisitorId;
			WC()->session->set( self::VISITOR_ID_SESSION_VAR_NAME, $visitor_id );
		}

		add_action(
			'wp_head',
			function () use ( $visitor_id ) {
				$this->logger->log( 'ServerSideVisitorId: forcing use of server side visitor ID' );

				echo '<script>window._paq = window._paq || []; window._paq.push(["setVisitorId", ' . wp_json_encode( $visitor_id ) . ']);</script>';
			}
		);
	}

	/**
	 * Checks if any visitor ID cookie is found for the current request. This means it checks
	 * for any cookie with the visitor ID cookie name prefix (_pk_id.). We don't look for the
	 * full cookie name, since that would require getting the configured cookie domain, which
	 * would add an extra DB query to every page load.
	 *
	 * @return bool
	 */
	private function is_visitor_id_cookie_present() {
		if ( empty( $_COOKIE ) || ! is_array( $_COOKIE ) ) {
			return false;
		}

		$cookie_prefix = AjaxTracker::FIRST_PARTY_COOKIES_PREFIX . 'id.';
		foreach ( $_COOKIE as $name => $value ) {
			if ( strpos( $name, $cookie_prefix ) === 0 ) {
				return true;
			}
		}

		return false;
	}

	private function initialize_woocommerce_session_if_needed() {
		WC()->initialize_session();
		if ( ! WC()->session->has_session() ) {
			WC()->session->set_customer_session_cookie( true );
		}
	}
}

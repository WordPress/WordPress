<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 */

namespace WpMatomo;

use WpMatomo\Ecommerce\ServerSideVisitorId;
use WpMatomo\TrackingCode\GeneratorOptions;
use WpMatomo\TrackingCode\TrackingCodeGenerator;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // if accessed directly
}

if ( ! class_exists( '\PiwikTracker' ) ) {
	include_once plugin_dir_path( MATOMO_ANALYTICS_FILE ) . 'app/vendor/matomo/matomo-php-tracker/MatomoTracker.php';
}

class AjaxTracker extends \MatomoTracker {

	const IP_ADDRESS_FORWARDING_HEADER             = 'X-Matomo-Forwarded-Ip';
	const IP_ADDRESS_FORWARDING_HEADER_SERVER_NAME = 'HTTP_X_MATOMO_FORWARDED_IP';
	const IP_ADDRESS_FORWARDING_NONCE_NAME         = 'matomo-track-forward-ip';

	private $has_cookie = false;
	private $logger;

	public function __construct( Settings $settings ) {
		$this->logger = new Logger();

		$site   = new Site();
		$idsite = $site->get_current_matomo_site_id();

		if ( ! $idsite ) {
			return;
		}

		$paths = new Paths();

		if ( $settings->get_global_option( 'track_api_endpoint' ) === 'restapi' ) {
			$api_endpoint = $paths->get_tracker_api_rest_api_endpoint();
		} else {
			$api_endpoint = $paths->get_tracker_api_url_in_matomo_dir();
		}

		parent::__construct( $idsite, $api_endpoint );

		$this->ip = false;

		// we are using the tracker only in ajax so the referer contains the actual url
		$this->urlReferrer = false;
		$this->pageUrl     = ! empty( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : false;

		if ( ! $settings->get_global_option( 'disable_cookies' ) ) {
			$tracking_code_generator = new TrackingCodeGenerator( $settings, new GeneratorOptions( $settings ) );
			$cookie_domain = $tracking_code_generator->get_tracking_cookie_domain();
			$this->enableCookies( $cookie_domain );
		} else {
			$this->disableCookieSupport();
		}

		if ( $this->loadVisitorIdCookie() ) {
			if ( ! empty( $this->cookieVisitorId ) ) {
				$this->has_cookie = true;
				$this->set_visitor_id_safe( $this->cookieVisitorId );
			}
		} else if ( function_exists( 'WC' ) && isset( WC()->session ) ) {
			$visitor_id = WC()->session->get( ServerSideVisitorId::VISITOR_ID_SESSION_VAR_NAME );
			if ( ! empty( $visitor_id ) ) {
				$this->hasCookie = true; // do not set cookies for this visitor, since it would have no effect anyway
				$this->set_visitor_id_safe( $visitor_id );
			}
		}
	}

	public function set_visitor_id_safe( $visitor_id ) {
		try {
			$this->setVisitorId( $visitor_id );
		} catch ( \Exception $ex ) {
			// do not fatal if the visitor ID is invalid for some reason
			if ( ! $this->is_invalid_visitor_id_error( $ex ) ) {
				throw $ex;
			}
		}
	}

	public function is_success_response( $response ) {
		$gif_response = "R0lGODlhAQABAIAAAAAAAAAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==";
		return $response === base64_decode( $gif_response );
	}

	protected function setCookie( $cookieName, $cookieValue, $cookieTTL ) {
		if ( ! $this->has_cookie ) {
			// we only set / overwrite cookies if it is a visitor that has eg no JS enabled or ad blocker enabled etc.
			// this way we will track all cart updates and orders into the same visitor on following requests.
			// If we recognized the visitor before via cookie we want in our case to make sure to not overwrite
			// any cookie
			parent::setCookie( $cookieName, $cookieValue, $cookieTTL );
		}
	}

	protected function sendRequest( $url, $method = 'GET', $data = null, $force = false ) {
		if ( ! $this->idSite ) {
			$this->logger->log('ecommerce tracking could not find idSite, cannot send request');
			return null; // not installed or synced yet
		}

		if ( $this->is_prerender() ) {
			// do not track if for some reason we are prerendering
			return null;
		}

		$args = array(
			'method' => $method,
		);
		if ( ! empty( $data ) ) {
			$args['body'] = $data;
		}

		if ( ! empty( $this->ip ) ) {
			$args['headers'] = [
				self::IP_ADDRESS_FORWARDING_HEADER => $this->ip,
			];

			$ip_nonce = wp_create_nonce( self::IP_ADDRESS_FORWARDING_NONCE_NAME );
			$url      = $url . '&ip_nonce=' . rawurlencode( $ip_nonce );
		}

		// todo at some point we could think about including `matomo.php` here instead of doing an http request
		// however we would need to make sure to set a custom tracker response handler to
		// 1) Not send any response no matter what happens
		// 2) Never exit at any point

		$url = $url . '&bots=1';

		$response = $this->wp_remote_request( $url, $args );

		if (is_wp_error($response)) {
			$this->logger->log_exception('ajax_tracker', new \Exception($response->get_error_message()));
		}

		return $response;
	}

	private function is_invalid_visitor_id_error( \Exception $ex ) {
		return strpos( $ex->getMessage(), 'setVisitorId() expects' ) === 0;
	}

	/**
	 * See https://developer.chrome.com/docs/web-platform/prerender-pages
	 * @return bool
	 */
	private function is_prerender() {
		// phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$purpose = strtolower( isset( $_SERVER['HTTP_SEC_PURPOSE'] ) ? wp_unslash( $_SERVER['HTTP_SEC_PURPOSE'] ) : '' );
		return strpos( $purpose, 'prefetch' ) !== false
			|| strpos( $purpose, 'prerender' ) !== false;
	}

	/**
	 * for tests to override
	 * @param string $url
	 * @param array $args
	 * @return array|\WP_Error
	 */
	protected function wp_remote_request( $url, $args ) {
		return wp_remote_request( $url, $args );
	}

	/**
	 * In Matomo for WordPress we want to rely entirely on JavaScript tracker
	 * for creating cookies.
	 *
	 * @return void
	 */
	protected function setFirstPartyCookies() {
		// disabled
	}

	/**
	 * Enables the handling of the X-Matomo-Forwarded-Ip, if it should be for
	 * the current request.
	 *
	 * Matomo for WordPress uses a custom header to correctly track client IP addresses
	 * when doing server side tracking. We choose to use this approach instead
	 * of the `cip` tracking parameter, since that parameter requires the use of
	 * a token_auth, and creating and storing a token_auth is more complexity than
	 * we want.
	 *
	 * Instead, we use a WP nonce to check whether the current request is authorized
	 * to handle the X-Matomo-Forwarded-Ip header.
	 *
	 * If it should be handled, the X-Matomo-Forwarded-Ip header is added to Matomo's
	 * list of proxy HTTP headers to look at for IP addresses.
	 */
	public static function add_ip_forward_proxy_header_to_config(\Piwik\Config $config ) {
		if ( empty( $_REQUEST['ip_nonce'] ) ) {
			return;
		}

		$ip_nonce = $_REQUEST['ip_nonce'];
		if ( ! wp_verify_nonce( $ip_nonce, self::IP_ADDRESS_FORWARDING_NONCE_NAME ) ) {
			return;
		}

		$proxy_client_headers = $config->General['proxy_client_headers'];
		if ( ! is_array( $proxy_client_headers ) ) {
			$proxy_client_headers = [];
		}
		$proxy_client_headers[] = self::IP_ADDRESS_FORWARDING_HEADER_SERVER_NAME;

		$config->General['proxy_client_headers'] = $proxy_client_headers;
	}
}

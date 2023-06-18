<?php
/**
 * WooCommerce.com Product Installation.
 *
 * @package WooCommerce\WCCom
 * @since   3.7.0
 */

use WC_REST_WCCOM_Site_Installer_Error_Codes as Installer_Error_Codes;
use WC_REST_WCCOM_Site_Installer_Error as Installer_Error;

defined( 'ABSPATH' ) || exit;

/**
 * WC_WCCOM_Site Class
 *
 * Main class for WooCommerce.com connected site.
 */
class WC_WCCOM_Site {

	const AUTH_ERROR_FILTER_NAME = 'wccom_auth_error';

	/**
	 * Load the WCCOM site class.
	 *
	 * @since 3.7.0
	 */
	public static function load() {
		self::includes();

		add_action( 'woocommerce_wccom_install_products', array( 'WC_WCCOM_Site_Installer', 'install' ) );
		add_filter( 'determine_current_user', array( __CLASS__, 'authenticate_wccom' ), 14 );
		add_action( 'woocommerce_rest_api_get_rest_namespaces', array( __CLASS__, 'register_rest_namespace' ) );
	}

	/**
	 * Include support files.
	 *
	 * @since 3.7.0
	 */
	protected static function includes() {
		require_once WC_ABSPATH . 'includes/admin/helper/class-wc-helper.php';
		require_once WC_ABSPATH . 'includes/wccom-site/class-wc-wccom-site-installer.php';
		require_once WC_ABSPATH . 'includes/wccom-site/class-wc-wccom-site-installer-requirements-check.php';
	}

	/**
	 * Authenticate WooCommerce.com request.
	 *
	 * @since 3.7.0
	 * @param int|false $user_id User ID.
	 * @return int|false
	 */
	public static function authenticate_wccom( $user_id ) {
		if ( ! empty( $user_id ) || ! self::is_request_to_wccom_site_rest_api() ) {
			return $user_id;
		}

		$auth_header = trim( self::get_authorization_header() );

		if ( stripos( $auth_header, 'Bearer ' ) === 0 ) {
			$access_token = trim( substr( $auth_header, 7 ) );
		} elseif ( ! empty( $_GET['token'] ) && is_string( $_GET['token'] ) ) {  // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$access_token = trim( $_GET['token'] );  // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		} else {
			add_filter(
				self::AUTH_ERROR_FILTER_NAME,
				function() {
					return new Installer_Error( Installer_Error_Codes::NO_ACCESS_TOKEN );
				}
			);
			return false;
		}

		if ( ! empty( $_SERVER['HTTP_X_WOO_SIGNATURE'] ) ) {
			$signature = trim( $_SERVER['HTTP_X_WOO_SIGNATURE'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		} elseif ( ! empty( $_GET['signature'] ) && is_string( $_GET['signature'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$signature = trim( $_GET['signature'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		} else {
			add_filter(
				self::AUTH_ERROR_FILTER_NAME,
				function() {
					return new Installer_Error( Installer_Error_Codes::NO_SIGNATURE );
				}
			);
			return false;
		}

		require_once WC_ABSPATH . 'includes/admin/helper/class-wc-helper-options.php';
		$site_auth = WC_Helper_Options::get( 'auth' );

		if ( empty( $site_auth['access_token'] ) ) {
			add_filter(
				self::AUTH_ERROR_FILTER_NAME,
				function() {
					return new Installer_Error( Installer_Error_Codes::SITE_NOT_CONNECTED );
				}
			);
			return false;
		}

		if ( ! hash_equals( $access_token, $site_auth['access_token'] ) ) {
			add_filter(
				self::AUTH_ERROR_FILTER_NAME,
				function() {
					return new Installer_Error( Installer_Error_Codes::INVALID_TOKEN );
				}
			);
			return false;
		}

		$body = WP_REST_Server::get_raw_data();

		if ( ! self::verify_wccom_request( $body, $signature, $site_auth['access_token_secret'] ) ) {
			add_filter(
				self::AUTH_ERROR_FILTER_NAME,
				function() {
					return new Installer_Error( Installer_Error_Codes::REQUEST_VERIFICATION_FAILED );
				}
			);
			return false;
		}

		$user = get_user_by( 'id', $site_auth['user_id'] );
		if ( ! $user ) {
			add_filter(
				self::AUTH_ERROR_FILTER_NAME,
				function() {
					return new Installer_Error( Installer_Error_Codes::USER_NOT_FOUND );
				}
			);
			return false;
		}

		return $user;
	}

	/**
	 * Get the authorization header.
	 *
	 * On certain systems and configurations, the Authorization header will be
	 * stripped out by the server or PHP. Typically this is then used to
	 * generate `PHP_AUTH_USER`/`PHP_AUTH_PASS` but not passed on. We use
	 * `getallheaders` here to try and grab it out instead.
	 *
	 * @since 3.7.0
	 * @return string Authorization header if set.
	 */
	protected static function get_authorization_header() {
		if ( ! empty( $_SERVER['HTTP_AUTHORIZATION'] ) ) {
			return wp_unslash( $_SERVER['HTTP_AUTHORIZATION'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		}

		if ( function_exists( 'getallheaders' ) ) {
			$headers = getallheaders();
			// Check for the authoization header case-insensitively.
			foreach ( $headers as $key => $value ) {
				if ( 'authorization' === strtolower( $key ) ) {
					return $value;
				}
			}
		}

		return '';
	}

	/**
	 * Check if this is a request to WCCOM Site REST API.
	 *
	 * @since 3.7.0
	 * @return bool
	 */
	protected static function is_request_to_wccom_site_rest_api() {

		if ( isset( $_REQUEST['rest_route'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$route       = wp_unslash( $_REQUEST['rest_route'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Recommended
			$rest_prefix = '';
		} else {
			$route       = wp_unslash( add_query_arg( array() ) );
			$rest_prefix = trailingslashit( rest_get_url_prefix() );
		}

		return false !== strpos( $route, $rest_prefix . 'wccom-site/' );
	}

	/**
	 * Verify WooCommerce.com request from a given body and signature request.
	 *
	 * @since 3.7.0
	 * @param string $body                Request body.
	 * @param string $signature           Request signature found in X-Woo-Signature header.
	 * @param string $access_token_secret Access token secret for this site.
	 * @return bool
	 */
	protected static function verify_wccom_request( $body, $signature, $access_token_secret ) {
		// phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$data = array(
			'host'        => $_SERVER['HTTP_HOST'],
			'request_uri' => urldecode( remove_query_arg( array( 'token', 'signature' ), $_SERVER['REQUEST_URI'] ) ),
			'method'      => strtoupper( $_SERVER['REQUEST_METHOD'] ),
		);
		// phpcs:enable

		if ( ! empty( $body ) ) {
			$data['body'] = $body;
		}

		$expected_signature = hash_hmac( 'sha256', wp_json_encode( $data ), $access_token_secret );

		return hash_equals( $expected_signature, $signature );
	}

	/**
	 * Register wccom-site REST namespace.
	 *
	 * @since 3.7.0
	 * @param array $namespaces List of registered namespaces.
	 * @return array Registered namespaces.
	 */
	public static function register_rest_namespace( $namespaces ) {

		require_once WC_ABSPATH . 'includes/wccom-site/rest-api/class-wc-rest-wccom-site-installer-error-codes.php';
		require_once WC_ABSPATH . 'includes/wccom-site/rest-api/class-wc-rest-wccom-site-installer-error.php';
		require_once WC_ABSPATH . 'includes/wccom-site/rest-api/endpoints/class-wc-rest-wccom-site-installer-controller.php';
		require_once WC_ABSPATH . 'includes/wccom-site/rest-api/endpoints/class-wc-rest-wccom-site-installer-controller-v2.php';
		require_once WC_ABSPATH . 'includes/wccom-site/rest-api/endpoints/class-wc-rest-wccom-site-ssr-controller.php';

		require_once WC_ABSPATH . 'includes/wccom-site/installation/class-wc-wccom-site-installation-state.php';
		require_once WC_ABSPATH . 'includes/wccom-site/installation/class-wc-wccom-site-installation-state-storage.php';
		require_once WC_ABSPATH . 'includes/wccom-site/installation/class-wc-wccom-site-installation-manager.php';

		require_once WC_ABSPATH . 'includes/wccom-site/installation/installation-steps/interface-installaton-step.php';
		require_once WC_ABSPATH . 'includes/wccom-site/installation/installation-steps/class-wc-wccom-site-installation-step-get-product-info.php';
		require_once WC_ABSPATH . 'includes/wccom-site/installation/installation-steps/class-wc-wccom-site-installation-step-download-product.php';
		require_once WC_ABSPATH . 'includes/wccom-site/installation/installation-steps/class-wc-wccom-site-installation-step-unpack-product.php';
		require_once WC_ABSPATH . 'includes/wccom-site/installation/installation-steps/class-wc-wccom-site-installation-step-move-product.php';
		require_once WC_ABSPATH . 'includes/wccom-site/installation/installation-steps/class-wc-wccom-site-installation-step-activate-product.php';

		$namespaces['wccom-site/v1'] = array(
			'installer' => 'WC_REST_WCCOM_Site_Installer_Controller',
			'ssr'       => 'WC_REST_WCCOM_Site_SSR_Controller',
		);

		$namespaces['wccom-site/v2'] = array(
			'installer' => 'WC_REST_WCCOM_Site_Installer_Controller_V2',
		);

		return $namespaces;
	}
}

WC_WCCOM_Site::load();

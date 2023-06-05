<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WC_HTTPS class.
 *
 * @class    WC_HTTPS
 * @version  2.2.0
 * @package  WooCommerce\Classes
 * @category Class
 * @author   WooThemes
 */
class WC_HTTPS {

	/**
	 * Hook in our HTTPS functions if we're on the frontend. This will ensure any links output to a page (when viewing via HTTPS) are also served over HTTPS.
	 */
	public static function init() {
		if ( 'yes' === get_option( 'woocommerce_force_ssl_checkout' ) && ! is_admin() ) {
			// HTTPS urls with SSL on
			$filters = array(
				'post_thumbnail_html',
				'wp_get_attachment_image_attributes',
				'wp_get_attachment_url',
				'option_stylesheet_url',
				'option_template_url',
				'script_loader_src',
				'style_loader_src',
				'template_directory_uri',
				'stylesheet_directory_uri',
				'site_url',
			);

			foreach ( $filters as $filter ) {
				add_filter( $filter, array( __CLASS__, 'force_https_url' ), 999 );
			}

			add_filter( 'page_link', array( __CLASS__, 'force_https_page_link' ), 10, 2 );
			add_action( 'template_redirect', array( __CLASS__, 'force_https_template_redirect' ) );

			if ( 'yes' == get_option( 'woocommerce_unforce_ssl_checkout' ) ) {
				add_action( 'template_redirect', array( __CLASS__, 'unforce_https_template_redirect' ) );
			}
		}
		add_action( 'http_api_curl', array( __CLASS__, 'http_api_curl' ), 10, 3 );
	}

	/**
	 * Force https for urls.
	 *
	 * @param mixed $content
	 * @return string
	 */
	public static function force_https_url( $content ) {
		if ( is_ssl() ) {
			if ( is_array( $content ) ) {
				$content = array_map( 'WC_HTTPS::force_https_url', $content );
			} else {
				$content = str_replace( 'http:', 'https:', $content );
			}
		}
		return $content;
	}

	/**
	 * Force a post link to be SSL if needed.
	 *
	 * @param string $link
	 * @param int $page_id
	 *
	 * @return string
	 */
	public static function force_https_page_link( $link, $page_id ) {
		if ( in_array( $page_id, array( get_option( 'woocommerce_checkout_page_id' ), get_option( 'woocommerce_myaccount_page_id' ) ) ) ) {
			$link = str_replace( 'http:', 'https:', $link );
		} elseif ( 'yes' === get_option( 'woocommerce_unforce_ssl_checkout' ) && ! wc_site_is_https() ) {
			$link = str_replace( 'https:', 'http:', $link );
		}
		return $link;
	}

	/**
	 * Template redirect - if we end up on a page ensure it has the correct http/https url.
	 */
	public static function force_https_template_redirect() {
		if ( ! is_ssl() && ( is_checkout() || is_account_page() || apply_filters( 'woocommerce_force_ssl_checkout', false ) ) ) {

			if ( 0 === strpos( $_SERVER['REQUEST_URI'], 'http' ) ) {
				wp_safe_redirect( preg_replace( '|^http://|', 'https://', $_SERVER['REQUEST_URI'] ) );
				exit;
			} else {
				wp_safe_redirect( 'https://' . ( ! empty( $_SERVER['HTTP_X_FORWARDED_HOST'] ) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : $_SERVER['HTTP_HOST'] ) . $_SERVER['REQUEST_URI'] );
				exit;
			}
		}
	}

	/**
	 * Template redirect - if we end up on a page ensure it has the correct http/https url.
	 */
	public static function unforce_https_template_redirect() {
		if ( function_exists( 'is_customize_preview' ) && is_customize_preview() ) {
			return;
		}

		if ( ! wc_site_is_https() && is_ssl() && $_SERVER['REQUEST_URI'] && ! is_checkout() && ! wp_doing_ajax() && ! is_account_page() && apply_filters( 'woocommerce_unforce_ssl_checkout', true ) ) {

			if ( 0 === strpos( $_SERVER['REQUEST_URI'], 'http' ) ) {
				wp_safe_redirect( preg_replace( '|^https://|', 'http://', $_SERVER['REQUEST_URI'] ) );
				exit;
			} else {
				wp_safe_redirect( 'http://' . ( ! empty( $_SERVER['HTTP_X_FORWARDED_HOST'] ) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : $_SERVER['HTTP_HOST'] ) . $_SERVER['REQUEST_URI'] );
				exit;
			}
		}
	}

	/**
	 * Force posts to PayPal to use TLS v1.2. See:
	 *        https://core.trac.wordpress.org/ticket/36320
	 *        https://core.trac.wordpress.org/ticket/34924#comment:13
	 *        https://www.paypal-knowledge.com/infocenter/index?page=content&widgetview=true&id=FAQ1914&viewlocale=en_US
	 *
	 * @param string $handle
	 * @param mixed $r
	 * @param string $url
	 */
	public static function http_api_curl( $handle, $r, $url ) {
		if ( strstr( $url, 'https://' ) && ( strstr( $url, '.paypal.com/nvp' ) || strstr( $url, '.paypal.com/cgi-bin/webscr' ) ) ) {
			curl_setopt( $handle, CURLOPT_SSLVERSION, 6 );
		}
	}
}

WC_HTTPS::init();

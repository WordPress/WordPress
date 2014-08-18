<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WC_HTTPS class.
 *
 * @class 		WC_HTTPS
 * @version		2.1.0
 * @package		WooCommerce/Classes
 * @category	Class
 * @author 		WooThemes
 */
class WC_HTTPS {

	/**
	 * Hook in our HTTPS functions if we're on the frontend. This will ensure any links output to a page (when viewing via HTTPS) are also served over HTTPS.
	 */
	public function __construct() {
		if ( 'yes' == get_option( 'woocommerce_force_ssl_checkout' ) ) {
			if ( ! is_admin() || ( defined( 'DOING_AJAX' ) && in_array( $_REQUEST['action'], array( 'woocommerce_get_refreshed_fragments', 'woocommerce_checkout', 'woocommerce_update_order_review', 'woocommerce_update_shipping_method', 'woocommerce_apply_coupon' ) ) ) ) {
				// HTTPS urls with SSL on
				$filters = array( 'post_thumbnail_html', 'wp_get_attachment_url', 'wp_get_attachment_image_attributes', 'wp_get_attachment_url', 'option_stylesheet_url', 'option_template_url', 'script_loader_src', 'style_loader_src', 'template_directory_uri', 'stylesheet_directory_uri', 'site_url' );

				foreach ( $filters as $filter ) {
					add_filter( $filter, 'WC_HTTPS::force_https_url' );
				}
				
				add_filter( 'page_link', array( $this, 'force_https_page_link' ), 10, 2 );
				add_action( 'template_redirect', array( $this, 'force_https_template_redirect' ) );

				if ( get_option('woocommerce_unforce_ssl_checkout') == 'yes' )
					add_action( 'template_redirect', array( $this, 'unforce_https_template_redirect' ) );
			}
		}
	}

	/**
	 * force_https_url function.
	 *
	 * @param mixed $content
	 * @return string
	 */
	public static function force_https_url( $content ) {
		if ( is_ssl() ) {
			if ( is_array( $content ) )
				$content = array_map( 'WC_HTTPS::force_https_url', $content );
			else
				$content = str_replace( 'http:', 'https:', $content );
		}
		return $content;
	}

	/**
	 * Force a post link to be SSL if needed
	 *
	 * @param  string $post_link
	 * @param  object $post
	 * @return string
	 */
	public function force_https_page_link( $link, $page_id ) {
		if ( in_array( $page_id, array( get_option( 'woocommerce_checkout_page_id' ), get_option( 'woocommerce_myaccount_page_id' ) ) ) ) {
			$link = str_replace( 'http:', 'https:', $link );
		} elseif ( get_option('woocommerce_unforce_ssl_checkout') == 'yes' ) {
			$link = str_replace( 'https:', 'http:', $link );
		}
		return $link;
	}

	/**
	 * Template redirect - if we end up on a page ensure it has the correct http/https url
	 */
	public function force_https_template_redirect() {
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
	 * Template redirect - if we end up on a page ensure it has the correct http/https url
	 */
	public function unforce_https_template_redirect() {
		if ( is_ssl() && $_SERVER['REQUEST_URI'] && ! is_checkout() && ! is_ajax() && ! is_account_page() && apply_filters( 'woocommerce_unforce_ssl_checkout', true ) ) {

			if ( 0 === strpos( $_SERVER['REQUEST_URI'], 'http' ) ) {
				wp_safe_redirect( preg_replace( '|^https://|', 'http://', $_SERVER['REQUEST_URI'] ) );
				exit;
			} else {
				wp_safe_redirect( 'http://' . ( ! empty( $_SERVER['HTTP_X_FORWARDED_HOST'] ) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : $_SERVER['HTTP_HOST'] ) . $_SERVER['REQUEST_URI'] );
				exit;
			}
		}
	}
}

new WC_HTTPS();

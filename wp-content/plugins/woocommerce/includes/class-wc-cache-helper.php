<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WC_Cache_Helper class.
 *
 * @class 		WC_Cache_Helper
 * @version		2.0.6
 * @package		WooCommerce/Classes
 * @category	Class
 * @author 		WooThemes
 */
class WC_Cache_Helper {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		add_action( 'before_woocommerce_init', array( $this, 'init' ) );
		add_action( 'admin_notices', array( $this, 'notices' ) );
	}

	/**
	 * Prevent caching on dynamic pages.
	 *
	 * @access public
	 * @return void
	 */
	public function init() {
		if ( false === ( $wc_page_uris = get_transient( 'woocommerce_cache_excluded_uris' ) ) ) {

			if ( wc_get_page_id( 'cart' ) < 1 || wc_get_page_id( 'checkout' ) < 1 || wc_get_page_id( 'myaccount' ) < 1 )
				return;

			$wc_page_uris   = array();

			// Exclude querystring when using page ID
			$wc_page_uris[] = 'p=' . wc_get_page_id( 'cart' );
	    	$wc_page_uris[] = 'p=' . wc_get_page_id( 'checkout' );
	    	$wc_page_uris[] = 'p=' . wc_get_page_id( 'myaccount' );

	    	// Exclude permalinks
			$cart_page      = get_post( wc_get_page_id( 'cart' ) );
			$checkout_page  = get_post( wc_get_page_id( 'checkout' ) );
			$account_page   = get_post( wc_get_page_id( 'myaccount' ) );

			if ( ! is_null( $cart_page ) )
				$wc_page_uris[] = '/' . $cart_page->post_name;
	    	if ( ! is_null( $checkout_page ) )
	    		$wc_page_uris[] = '/' . $checkout_page->post_name;
	    	if ( ! is_null( $account_page ) )
	    		$wc_page_uris[] = '/' . $account_page->post_name;

	    	set_transient( 'woocommerce_cache_excluded_uris', $wc_page_uris );
		}

		if ( is_array( $wc_page_uris ) )
			foreach( $wc_page_uris as $uri )
				if ( strstr( $_SERVER['REQUEST_URI'], $uri ) ) {
					$this->nocache();
					break;
				}
	}

	/**
	 * Set nocache constants and headers.
	 *
	 * @access private
	 * @return void
	 */
	private function nocache() {
		if ( ! defined( 'DONOTCACHEPAGE' ) )
			define( "DONOTCACHEPAGE", "true" );

		if ( ! defined( 'DONOTCACHEOBJECT' ) )
			define( "DONOTCACHEOBJECT", "true" );

		if ( ! defined( 'DONOTCACHEDB' ) )
			define( "DONOTCACHEDB", "true" );

		nocache_headers();
	}

	/**
	 * notices function.
	 *
	 * @access public
	 * @return void
	 */
	public function notices() {
		if ( ! function_exists( 'w3tc_pgcache_flush' ) || ! function_exists( 'w3_instance' ) )
			return;

		$config   = w3_instance('W3_Config');
		$enabled  = $config->get_integer( 'dbcache.enabled' );
		$settings = $config->get_array( 'dbcache.reject.sql' );

		if ( $enabled && ! in_array( '_wc_session_', $settings ) ) {
			?>
			<div class="error">
				<p><?php printf( __( 'In order for <strong>database caching</strong> to work with WooCommerce you must add <code>_wc_session_</code> to the "Ignored Query Strings" option in W3 Total Cache settings <a href="%s">here</a>.', 'woocommerce' ), admin_url( 'admin.php?page=w3tc_dbcache' ) ); ?></p>
			</div>
			<?php
		}
	}
}

new WC_Cache_Helper();

<?php
/**
 * Twenty Twenty TWO support.
 *
 * @since   6.0.0
 * @package WooCommerce\Classes
 */

use Automattic\Jetpack\Constants;

defined( 'ABSPATH' ) || exit;

/**
 * WC_Twenty_Twenty_One class.
 */
class WC_Twenty_Twenty_Two {

	/**
	 * Theme init.
	 */
	public static function init() {

		// This theme doesn't have a traditional sidebar.
		remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

		// Enqueue theme compatibility styles.
		add_filter( 'woocommerce_enqueue_styles', array( __CLASS__, 'enqueue_styles' ) );

		// Wrap checkout form elements for styling.
		add_action( 'woocommerce_checkout_before_order_review_heading', array( __CLASS__, 'before_order_review' ) );
		add_action( 'woocommerce_checkout_after_order_review', array( __CLASS__, 'after_order_review' ) );

		// Register theme features.
		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );
		add_theme_support(
			'woocommerce',
			array(
				'thumbnail_image_width' => 450,
				'single_image_width'    => 600,
			)
		);

	}

	/**
	 * Enqueue CSS for this theme.
	 *
	 * @param  array $styles Array of registered styles.
	 * @return array
	 */
	public static function enqueue_styles( $styles ) {
		unset( $styles['woocommerce-general'] );

		$styles['woocommerce-general'] = array(
			'src'     => str_replace( array( 'http:', 'https:' ), '', WC()->plugin_url() ) . '/assets/css/twenty-twenty-two.css',
			'deps'    => '',
			'version' => Constants::get_constant( 'WC_VERSION' ),
			'media'   => 'all',
			'has_rtl' => true,
		);

		return apply_filters( 'woocommerce_twenty_twenty_two_styles', $styles );
	}

	/**
	 * Wrap checkout order review with a `col2-set` div.
	 */
	public static function before_order_review() {
		echo '<div class="col2-set">';
	}

	/**
	 * Close the div wrapper.
	 */
	public static function after_order_review() {
		echo '</div>';
	}
}

WC_Twenty_Twenty_Two::init();

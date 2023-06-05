<?php
/**
 * Twenty Twenty One support.
 *
 * @since   4.7.0
 * @package WooCommerce\Classes
 */

use Automattic\Jetpack\Constants;

defined( 'ABSPATH' ) || exit;

/**
 * WC_Twenty_Twenty_One class.
 */
class WC_Twenty_Twenty_One {

	/**
	 * Theme init.
	 */
	public static function init() {

		// Change WooCommerce wrappers.
		remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
		remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

		// This theme doesn't have a traditional sidebar.
		remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

		// Enqueue theme compatibility styles.
		add_filter( 'woocommerce_enqueue_styles', array( __CLASS__, 'enqueue_styles' ) );

		// Enqueue wp-admin compatibility styles.
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_admin_styles' ) );

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
			'src'     => str_replace( array( 'http:', 'https:' ), '', WC()->plugin_url() ) . '/assets/css/twenty-twenty-one.css',
			'deps'    => '',
			'version' => Constants::get_constant( 'WC_VERSION' ),
			'media'   => 'all',
			'has_rtl' => true,
		);

		return apply_filters( 'woocommerce_twenty_twenty_one_styles', $styles );
	}

	/**
	 * Enqueue the wp-admin CSS overrides for this theme.
	 */
	public static function enqueue_admin_styles() {
		wp_enqueue_style(
			'woocommerce-twenty-twenty-one-admin',
			str_replace( array( 'http:', 'https:' ), '', WC()->plugin_url() ) . '/assets/css/twenty-twenty-one-admin.css',
			'',
			Constants::get_constant( 'WC_VERSION' ),
			'all'
		);
	}


}

WC_Twenty_Twenty_One::init();

<?php
/**
 * Twenty Twenty support.
 *
 * @since   3.8.1
 * @package WooCommerce\Classes
 */

use Automattic\Jetpack\Constants;

defined( 'ABSPATH' ) || exit;

/**
 * WC_Twenty_Twenty class.
 */
class WC_Twenty_Twenty {

	/**
	 * Theme init.
	 */
	public static function init() {

		// Change WooCommerce wrappers.
		remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
		remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

		add_action( 'woocommerce_before_main_content', array( __CLASS__, 'output_content_wrapper' ), 10 );
		add_action( 'woocommerce_after_main_content', array( __CLASS__, 'output_content_wrapper_end' ), 10 );

		// This theme doesn't have a traditional sidebar.
		remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

		// Enqueue theme compatibility styles.
		add_filter( 'woocommerce_enqueue_styles', array( __CLASS__, 'enqueue_styles' ) );

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

		// Background color change.
		add_action( 'after_setup_theme', array( __CLASS__, 'set_white_background' ), 10 );

	}

	/**
	 * Open the Twenty Twenty wrapper.
	 */
	public static function output_content_wrapper() {
		echo '<section id="primary" class="content-area">';
		echo '<main id="main" class="site-main">';
	}

	/**
	 * Close the Twenty Twenty wrapper.
	 */
	public static function output_content_wrapper_end() {
		echo '</main>';
		echo '</section>';
	}

	/**
	 * Set background color to white if it's default, otherwise don't touch it.
	 */
	public static function set_white_background() {
		$background         = sanitize_hex_color_no_hash( get_theme_mod( 'background_color' ) );
		$background_default = 'f5efe0';

		// Don't change user's choice of background color.
		if ( ! empty( $background ) && $background !== $background_default ) {
			return;
		}

		// In case default background is found, change it to white.
		set_theme_mod( 'background_color', 'fff' );
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
			'src'     => str_replace( array( 'http:', 'https:' ), '', WC()->plugin_url() ) . '/assets/css/twenty-twenty.css',
			'deps'    => '',
			'version' => Constants::get_constant( 'WC_VERSION' ),
			'media'   => 'all',
			'has_rtl' => true,
		);

		return apply_filters( 'woocommerce_twenty_twenty_styles', $styles );
	}

}

WC_Twenty_Twenty::init();

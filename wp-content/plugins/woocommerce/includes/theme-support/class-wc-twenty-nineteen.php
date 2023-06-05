<?php
/**
 * Twenty Nineteen support.
 *
 * @since   3.5.X
 * @package WooCommerce\Classes
 */

use Automattic\Jetpack\Constants;

defined( 'ABSPATH' ) || exit;

/**
 * WC_Twenty_Nineteen class.
 */
class WC_Twenty_Nineteen {

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
				'thumbnail_image_width' => 300,
				'single_image_width'    => 450,
			)
		);

		// Tweak Twenty Nineteen features.
		add_action( 'wp', array( __CLASS__, 'tweak_theme_features' ) );

		// Color scheme CSS.
		add_filter( 'twentynineteen_custom_colors_css', array( __CLASS__, 'custom_colors_css' ), 10, 3 );
	}

	/**
	 * Open the Twenty Nineteen wrapper.
	 */
	public static function output_content_wrapper() {
		echo '<section id="primary" class="content-area">';
		echo '<main id="main" class="site-main">';
	}

	/**
	 * Close the Twenty Nineteen wrapper.
	 */
	public static function output_content_wrapper_end() {
		echo '</main>';
		echo '</section>';
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
			'src'     => str_replace( array( 'http:', 'https:' ), '', WC()->plugin_url() ) . '/assets/css/twenty-nineteen.css',
			'deps'    => '',
			'version' => Constants::get_constant( 'WC_VERSION' ),
			'media'   => 'all',
			'has_rtl' => true,
		);

		return apply_filters( 'woocommerce_twenty_nineteen_styles', $styles );
	}

	/**
	 * Tweak Twenty Nineteen features.
	 */
	public static function tweak_theme_features() {
		if ( is_woocommerce() ) {
			add_filter( 'twentynineteen_can_show_post_thumbnail', '__return_false' );
		}
	}

	/**
	 * Filters Twenty Nineteen custom colors CSS.
	 *
	 * @param string $css           Base theme colors CSS.
	 * @param int    $primary_color The user's selected color hue.
	 * @param string $saturation    Filtered theme color saturation level.
	 */
	public static function custom_colors_css( $css, $primary_color, $saturation ) {
		if ( function_exists( 'register_block_type' ) && is_admin() ) {
			return $css;
		}

		$lightness = absint( apply_filters( 'twentynineteen_custom_colors_lightness', 33 ) );
		$lightness = $lightness . '%';

		$css .= '
			.onsale,
			.woocommerce-info,
			.woocommerce-store-notice {
				background-color: hsl( ' . $primary_color . ', ' . $saturation . ', ' . $lightness . ' );
			}

			.woocommerce-tabs ul li.active a {
				color: hsl( ' . $primary_color . ', ' . $saturation . ', ' . $lightness . ' );
				box-shadow: 0 2px 0 hsl( ' . $primary_color . ', ' . $saturation . ', ' . $lightness . ' );
			}
		';

		return $css;
	}
}

WC_Twenty_Nineteen::init();

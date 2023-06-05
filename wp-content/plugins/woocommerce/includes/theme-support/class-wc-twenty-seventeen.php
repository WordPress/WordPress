<?php
/**
 * Twenty Seventeen support.
 *
 * @since   2.6.9
 * @package WooCommerce\Classes
 */

use Automattic\Jetpack\Constants;

defined( 'ABSPATH' ) || exit;

/**
 * WC_Twenty_Seventeen class.
 */
class WC_Twenty_Seventeen {

	/**
	 * Theme init.
	 */
	public static function init() {
		remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
		remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

		add_action( 'woocommerce_before_main_content', array( __CLASS__, 'output_content_wrapper' ), 10 );
		add_action( 'woocommerce_after_main_content', array( __CLASS__, 'output_content_wrapper_end' ), 10 );
		add_filter( 'woocommerce_enqueue_styles', array( __CLASS__, 'enqueue_styles' ) );
		add_filter( 'twentyseventeen_custom_colors_css', array( __CLASS__, 'custom_colors_css' ), 10, 3 );

		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );
		add_theme_support(
			'woocommerce',
			array(
				'thumbnail_image_width' => 250,
				'single_image_width'    => 350,
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
			'src'     => str_replace( array( 'http:', 'https:' ), '', WC()->plugin_url() ) . '/assets/css/twenty-seventeen.css',
			'deps'    => '',
			'version' => Constants::get_constant( 'WC_VERSION' ),
			'media'   => 'all',
			'has_rtl' => true,
		);

		return apply_filters( 'woocommerce_twenty_seventeen_styles', $styles );
	}

	/**
	 * Open the Twenty Seventeen wrapper.
	 */
	public static function output_content_wrapper() {
		echo '<div class="wrap">';
		echo '<div id="primary" class="content-area twentyseventeen">';
		echo '<main id="main" class="site-main" role="main">';
	}

	/**
	 * Close the Twenty Seventeen wrapper.
	 */
	public static function output_content_wrapper_end() {
		echo '</main>';
		echo '</div>';
		get_sidebar();
		echo '</div>';
	}

	/**
	 * Custom colors.
	 *
	 * @param  string $css Styles.
	 * @param  string $hue Color.
	 * @param  string $saturation Saturation.
	 * @return string
	 */
	public static function custom_colors_css( $css, $hue, $saturation ) {
		$css .= '
			.colors-custom .select2-container--default .select2-selection--single {
				border-color: hsl( ' . $hue . ', ' . $saturation . ', 73% );
			}
			.colors-custom .select2-container--default .select2-selection__rendered {
				color: hsl( ' . $hue . ', ' . $saturation . ', 40% );
			}
			.colors-custom .select2-container--default .select2-selection--single .select2-selection__arrow b {
				border-color: hsl( ' . $hue . ', ' . $saturation . ', 40% ) transparent transparent transparent;
			}
			.colors-custom .select2-container--focus .select2-selection {
				border-color: #000;
			}
			.colors-custom .select2-container--focus .select2-selection--single .select2-selection__arrow b {
				border-color: #000 transparent transparent transparent;
			}
			.colors-custom .select2-container--focus .select2-selection .select2-selection__rendered {
				color: #000;
			}
		';
		return $css;
	}
}

WC_Twenty_Seventeen::init();

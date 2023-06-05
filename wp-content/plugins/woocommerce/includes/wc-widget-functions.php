<?php
/**
 * WooCommerce Widget Functions
 *
 * Widget related functions and widget registration.
 *
 * @package WooCommerce\Functions
 * @version 2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Include widget classes.
require_once dirname( __FILE__ ) . '/abstracts/abstract-wc-widget.php';
require_once dirname( __FILE__ ) . '/widgets/class-wc-widget-cart.php';
require_once dirname( __FILE__ ) . '/widgets/class-wc-widget-layered-nav-filters.php';
require_once dirname( __FILE__ ) . '/widgets/class-wc-widget-layered-nav.php';
require_once dirname( __FILE__ ) . '/widgets/class-wc-widget-price-filter.php';
require_once dirname( __FILE__ ) . '/widgets/class-wc-widget-product-categories.php';
require_once dirname( __FILE__ ) . '/widgets/class-wc-widget-product-search.php';
require_once dirname( __FILE__ ) . '/widgets/class-wc-widget-product-tag-cloud.php';
require_once dirname( __FILE__ ) . '/widgets/class-wc-widget-products.php';
require_once dirname( __FILE__ ) . '/widgets/class-wc-widget-rating-filter.php';
require_once dirname( __FILE__ ) . '/widgets/class-wc-widget-recent-reviews.php';
require_once dirname( __FILE__ ) . '/widgets/class-wc-widget-recently-viewed.php';
require_once dirname( __FILE__ ) . '/widgets/class-wc-widget-top-rated-products.php';

/**
 * Register Widgets.
 *
 * @since 2.3.0
 */
function wc_register_widgets() {
	register_widget( 'WC_Widget_Cart' );
	register_widget( 'WC_Widget_Layered_Nav_Filters' );
	register_widget( 'WC_Widget_Layered_Nav' );
	register_widget( 'WC_Widget_Price_Filter' );
	register_widget( 'WC_Widget_Product_Categories' );
	register_widget( 'WC_Widget_Product_Search' );
	register_widget( 'WC_Widget_Product_Tag_Cloud' );
	register_widget( 'WC_Widget_Products' );
	register_widget( 'WC_Widget_Recently_Viewed' );

	if ( 'yes' === get_option( 'woocommerce_enable_reviews', 'yes' ) ) {
		register_widget( 'WC_Widget_Top_Rated_Products' );
		register_widget( 'WC_Widget_Recent_Reviews' );
		register_widget( 'WC_Widget_Rating_Filter' );
	}
}
add_action( 'widgets_init', 'wc_register_widgets' );

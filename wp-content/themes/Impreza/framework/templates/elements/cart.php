<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output cart element
 *
 * @var $visibility string Visibility: 'always' / 'not_empty'
 * @var $icon int
 * @var $icon_size int
 * @var $design_options array
 * @var $id string
 */

if ( ! class_exists( 'woocommerce' ) ) {
	return;
}

global $woocommerce;
$link = $woocommerce->cart->get_cart_url();
global $cache_enabled;

$classes = '';
if ( isset( $design_options ) AND isset( $design_options['hide_for_sticky'] ) AND $design_options['hide_for_sticky'] ) {
	$classes .= ' hide-for-sticky';
}
if ( isset( $id ) AND ! empty( $id ) ) {
	$classes .= ' ush_' . str_replace( ':', '_', $id );
}
echo '<div class="w-cart' . $classes . '">';
echo '<div class="w-cart-h">';
echo '<a class="w-cart-link" href="' . esc_attr( $link ) . '">';

if ( ! empty( $icon ) ) {
	echo '<i class="' . us_prepare_icon_class( $icon ) . '"></i>';
}

echo '<span class="w-cart-quantity"></span></a>';
echo '<div class="w-cart-notification"><span class="product-name">' . __( 'Product', 'us' ) . '</span> ' . __( 'was added to your cart', 'us' ) . '</div>';
echo '<div class="w-cart-dropdown">';
the_widget( 'WC_Widget_Cart' ); // This widget being always filled with products via AJAX
echo '</div>';
echo '</div>';
echo '</div>';

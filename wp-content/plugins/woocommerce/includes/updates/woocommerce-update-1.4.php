<?php
/**
 * Update WC to 1.4
 *
 * @author 		WooThemes
 * @category 	Admin
 * @package 	WooCommerce/Admin/Updates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $wpdb, $woocommerce;

// Upgrade from old downloadable/virtual product types
$downloadable_type = get_term_by( 'slug', 'downloadable', 'product_type' );
if ( $downloadable_type ) {
	$products = get_objects_in_term( $downloadable_type->term_id, 'product_type' );
	foreach ( $products as $product ) {
		update_post_meta( $product, '_downloadable', 'yes' );
		update_post_meta( $product, '_virtual', 'yes' );
		wp_set_object_terms( $product, 'simple', 'product_type');
	}
}

$virtual_type = get_term_by( 'slug', 'virtual', 'product_type' );
if ( $virtual_type ) {
	$products = get_objects_in_term( $virtual_type->term_id, 'product_type' );
	foreach ( $products as $product ) {
		update_post_meta( $product, '_downloadable', 'no' );
		update_post_meta( $product, '_virtual', 'yes' );
		wp_set_object_terms( $product, 'simple', 'product_type');
	}
}
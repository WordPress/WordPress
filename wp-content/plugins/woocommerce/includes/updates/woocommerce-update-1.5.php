<?php
/**
 * Update WC to 1.5
 *
 * @author 		WooThemes
 * @category 	Admin
 * @package 	WooCommerce/Admin/Updates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $wpdb, $woocommerce;

// Update woocommerce_downloadable_product_permissions table to include order ID's as well as keys
$results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}woocommerce_downloadable_product_permissions WHERE order_id = 0;" );

if ( $results ) foreach ( $results as $result ) {

	if ( ! $result->order_key )
		continue;

	$order_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_order_key' AND meta_value = '%s' LIMIT 1;", $result->order_key ) );

	if ( $order_id ) {

		$wpdb->update( $wpdb->prefix . "woocommerce_downloadable_product_permissions", array(
			'order_id' => $order_id,
		), array(
			'product_id' => $result->product_id,
			'order_key' => $result->order_key
		), array( '%s' ), array( '%s', '%s' ) );

	}

}

// Upgrade old meta keys for product data
$meta = array( 'sku', 'downloadable', 'virtual', 'price', 'visibility', 'stock', 'stock_status', 'backorders', 'manage_stock', 'sale_price', 'regular_price', 'weight', 'length', 'width', 'height', 'tax_status', 'tax_class', 'upsell_ids', 'crosssell_ids', 'sale_price_dates_from', 'sale_price_dates_to', 'min_variation_price', 'max_variation_price', 'featured', 'product_attributes', 'file_path', 'download_limit', 'product_url', 'min_variation_price', 'max_variation_price' );

$wpdb->query( "
	UPDATE {$wpdb->postmeta}
	LEFT JOIN {$wpdb->posts} ON ( {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID )
	SET meta_key = CONCAT( '_', meta_key )
	WHERE meta_key IN ( '" . implode( "', '", $meta ) . "' )
	AND {$wpdb->posts}.post_type IN ('product', 'product_variation')
" );
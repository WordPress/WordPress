<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Simple Product Class
 *
 * The default product type kinda product.
 *
 * @class 		WC_Product_Simple
 * @version		2.0.0
 * @package		WooCommerce/Classes/Products
 * @category	Class
 * @author 		WooThemes
 */
class WC_Product_Simple extends WC_Product {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @param mixed $product
	 */
	public function __construct( $product ) {
		$this->product_type = 'simple';
		parent::__construct( $product );
	}

	/**
	 * Get the add to url used mainly in loops.
	 *
	 * @access public
	 * @return string
	 */
	public function add_to_cart_url() {
		$url = $this->is_purchasable() && $this->is_in_stock() ? remove_query_arg( 'added-to-cart', add_query_arg( 'add-to-cart', $this->id ) ) : get_permalink( $this->id );

		return apply_filters( 'woocommerce_product_add_to_cart_url', $url, $this );
	}

	/**
	 * Get the add to cart button text
	 *
	 * @access public
	 * @return string
	 */
	public function add_to_cart_text() {
		$text = $this->is_purchasable() && $this->is_in_stock() ? __( 'Add to cart', 'woocommerce' ) : __( 'Read More', 'woocommerce' );

		return apply_filters( 'woocommerce_product_add_to_cart_text', $text, $this );
	}

	/**
	 * Get the title of the post.
	 *
	 * @access public
	 * @return string
	 */
	public function get_title() {

		$title = $this->post->post_title;

		if ( $this->get_parent() > 0 ) {
			$title = get_the_title( $this->get_parent() ) . ' &rarr; ' . $title;
		}

		return apply_filters( 'woocommerce_product_title', $title, $this );
	}

	/**
	 * Sync grouped products with the children lowest price (so they can be sorted by price accurately).
	 *
	 * @access public
	 * @return void
	 */
	public function grouped_product_sync() {
		global $wpdb, $woocommerce;

		if ( ! $this->get_parent() ) return;

		$children_by_price = get_posts( array(
			'post_parent'    => $this->get_parent(),
			'orderby'        => 'meta_value_num',
			'order'          => 'asc',
			'meta_key'       => '_price',
			'posts_per_page' => 1,
			'post_type'      => 'product',
			'fields'         => 'ids'
		));
		if ( $children_by_price ) {
			foreach ( $children_by_price as $child ) {
				$child_price = get_post_meta( $child, '_price', true );
				update_post_meta( $this->get_parent(), '_price', $child_price );
			}
		}

		wc_delete_product_transients( $this->id );
	}
}
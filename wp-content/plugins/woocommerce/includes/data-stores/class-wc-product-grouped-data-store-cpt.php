<?php
/**
 * Class WC_Product_Grouped_Data_Store_CPT file.
 *
 * @package WooCommerce\DataStores
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC Grouped Product Data Store: Stored in CPT.
 *
 * @version  3.0.0
 */
class WC_Product_Grouped_Data_Store_CPT extends WC_Product_Data_Store_CPT implements WC_Object_Data_Store_Interface {

	/**
	 * Helper method that updates all the post meta for a grouped product.
	 *
	 * @param WC_Product $product Product object.
	 * @param bool       $force Force update. Used during create.
	 * @since 3.0.0
	 */
	protected function update_post_meta( &$product, $force = false ) {
		$meta_key_to_props = array(
			'_children' => 'children',
		);

		$props_to_update = $force ? $meta_key_to_props : $this->get_props_to_update( $product, $meta_key_to_props );

		foreach ( $props_to_update as $meta_key => $prop ) {
			$value   = $product->{"get_$prop"}( 'edit' );
			$updated = update_post_meta( $product->get_id(), $meta_key, $value );
			if ( $updated ) {
				$this->updated_props[] = $prop;
			}
		}

		parent::update_post_meta( $product, $force );
	}

	/**
	 * Handle updated meta props after updating meta data.
	 *
	 * @since  3.0.0
	 * @param  WC_Product $product Product object.
	 */
	protected function handle_updated_props( &$product ) {
		if ( in_array( 'children', $this->updated_props, true ) ) {
			$this->update_prices_from_children( $product );
		}
		parent::handle_updated_props( $product );
	}

	/**
	 * Sync grouped product prices with children.
	 *
	 * @since 3.0.0
	 * @param WC_Product|int $product Product object or product ID.
	 */
	public function sync_price( &$product ) {
		$this->update_prices_from_children( $product );
	}

	/**
	 * Loop over child products and update the grouped product prices.
	 *
	 * @param WC_Product $product Product object.
	 */
	protected function update_prices_from_children( &$product ) {
		$child_prices = array();
		foreach ( $product->get_children( 'edit' ) as $child_id ) {
			$child = wc_get_product( $child_id );
			if ( $child ) {
				$child_prices[] = $child->get_price( 'edit' );
			}
		}
		$child_prices = array_filter( $child_prices );
		delete_post_meta( $product->get_id(), '_price' );
		delete_post_meta( $product->get_id(), '_sale_price' );
		delete_post_meta( $product->get_id(), '_regular_price' );

		if ( ! empty( $child_prices ) ) {
			add_post_meta( $product->get_id(), '_price', min( $child_prices ) );
			add_post_meta( $product->get_id(), '_price', max( $child_prices ) );
		}

		$this->update_lookup_table( $product->get_id(), 'wc_product_meta_lookup' );

		/**
		 * Fire an action for this direct update so it can be detected by other code.
		 *
		 * @since 3.6
		 * @param int $product_id Product ID that was updated directly.
		 */
		do_action( 'woocommerce_updated_product_price', $product->get_id() );
	}
}

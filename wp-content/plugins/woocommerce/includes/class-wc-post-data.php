<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Post Data
 *
 * Standardises certain post data on save.
 *
 * @class 		WC_Post_Data
 * @version		2.1.0
 * @package		WooCommerce/Classes/Data
 * @category	Class
 * @author 		WooThemes
 */
class WC_Post_Data {

	private $editing_term = null;

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'edit_term', array( $this, 'edit_term' ), 10, 3 );
		add_action( 'edited_term', array( $this, 'edited_term' ), 10, 3 );
		add_filter( 'update_order_item_metadata', array( $this, 'update_order_item_metadata' ), 10, 5 );
		add_filter( 'update_post_metadata', array( $this, 'update_post_metadata' ), 10, 5 );
	}

	/**
	 * When editing a term, check for product attributes
	 * @param  id $term_id
	 * @param  id $tt_id
	 * @param  string $taxonomy
	 */
	public function edit_term( $term_id, $tt_id, $taxonomy ) {
		if ( strpos( $taxonomy, 'pa_' ) === 0 ) {
			$this->editing_term = get_term_by( 'id', $term_id, $taxonomy );
		} else {
			$this->editing_term = null;
		}
	}

	/**
	 * When a term is edited, check for product attributes and update variations
	 * @param  id $term_id
	 * @param  id $tt_id
	 * @param  string $taxonomy
	 */
	public function edited_term( $term_id, $tt_id, $taxonomy ) {
		if ( ! is_null( $this->editing_term ) && strpos( $taxonomy, 'pa_' ) === 0 ) {
			$edited_term = get_term_by( 'id', $term_id, $taxonomy );

			if ( $edited_term->slug !== $this->editing_term->slug ) {
				global $wpdb;

				$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->postmeta} SET meta_value = %s WHERE meta_key = %s AND meta_value = %s;", $edited_term->slug, 'attribute_' . sanitize_title( $taxonomy ), $this->editing_term->slug ) );
			}
		} else {
			$this->editing_term = null;
		}
	}

	/**
	 * Ensure floats are correctly converted to strings based on PHP locale
	 * 
	 * @param  null $check
	 * @param  int $object_id
	 * @param  string $meta_key
	 * @param  mixed $meta_value
	 * @param  mixed $prev_value
	 * @return null|bool
	 */
	public function update_order_item_metadata( $check, $object_id, $meta_key, $meta_value, $prev_value ) {
		if ( ! empty( $meta_value ) && is_float( $meta_value ) ) {

			// Convert float to string
			$meta_value = wc_float_to_string( $meta_value );

			// Update meta value with new string
			update_metadata( 'order_item', $object_id, $meta_key, $meta_value, $prev_value );

			// Return
			return true;
		}
		return $check;
	}

	/**
	 * Ensure floats are correctly converted to strings based on PHP locale
	 * 
	 * @param  null $check
	 * @param  int $object_id
	 * @param  string $meta_key
	 * @param  mixed $meta_value
	 * @param  mixed $prev_value
	 * @return null|bool
	 */
	public function update_post_metadata( $check, $object_id, $meta_key, $meta_value, $prev_value ) {
		if ( ! empty( $meta_value ) && is_float( $meta_value ) && in_array( get_post_type( $object_id ), array( 'shop_order', 'shop_coupon', 'product', 'product_variation' ) ) ) {

			// Convert float to string
			$meta_value = wc_float_to_string( $meta_value );

			// Update meta value with new string
			update_metadata( 'post', $object_id, $meta_key, $meta_value, $prev_value );

			// Return
			return true;
		}
		return $check;
	}

}

new WC_Post_Data();
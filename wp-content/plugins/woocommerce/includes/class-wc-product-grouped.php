<?php
/**
 * Grouped Product
 *
 * Grouped products cannot be purchased - they are wrappers for other products.
 *
 * @package WooCommerce\Classes\Products
 * @version 3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Product grouped class.
 */
class WC_Product_Grouped extends WC_Product {

	/**
	 * Stores product data.
	 *
	 * @var array
	 */
	protected $extra_data = array(
		'children' => array(),
	);

	/**
	 * Get internal type.
	 *
	 * @return string
	 */
	public function get_type() {
		return 'grouped';
	}

	/**
	 * Get the add to cart button text.
	 *
	 * @return string
	 */
	public function add_to_cart_text() {
		return apply_filters( 'woocommerce_product_add_to_cart_text', __( 'View products', 'woocommerce' ), $this );
	}

	/**
	 * Get the add to cart button text description - used in aria tags.
	 *
	 * @since 3.3.0
	 * @return string
	 */
	public function add_to_cart_description() {
		/* translators: %s: Product title */
		return apply_filters( 'woocommerce_product_add_to_cart_description', sprintf( __( 'View products in the &ldquo;%s&rdquo; group', 'woocommerce' ), $this->get_name() ), $this );
	}

	/**
	 * Returns whether or not the product is on sale.
	 *
	 * @param  string $context What the value is for. Valid values are view and edit.
	 * @return bool
	 */
	public function is_on_sale( $context = 'view' ) {
		$children = array_filter( array_map( 'wc_get_product', $this->get_children( $context ) ), 'wc_products_array_filter_visible_grouped' );
		$on_sale  = false;

		foreach ( $children as $child ) {
			if ( $child->is_purchasable() && ! $child->has_child() && $child->is_on_sale() ) {
				$on_sale = true;
				break;
			}
		}

		return 'view' === $context ? apply_filters( 'woocommerce_product_is_on_sale', $on_sale, $this ) : $on_sale;
	}

	/**
	 * Returns false if the product cannot be bought.
	 *
	 * @return bool
	 */
	public function is_purchasable() {
		return apply_filters( 'woocommerce_is_purchasable', false, $this );
	}

	/**
	 * Returns the price in html format.
	 *
	 * @param string $price (default: '').
	 * @return string
	 */
	public function get_price_html( $price = '' ) {
		$tax_display_mode = get_option( 'woocommerce_tax_display_shop' );
		$child_prices     = array();
		$children         = array_filter( array_map( 'wc_get_product', $this->get_children() ), 'wc_products_array_filter_visible_grouped' );

		foreach ( $children as $child ) {
			if ( '' !== $child->get_price() ) {
				$child_prices[] = 'incl' === $tax_display_mode ? wc_get_price_including_tax( $child ) : wc_get_price_excluding_tax( $child );
			}
		}

		if ( ! empty( $child_prices ) ) {
			$min_price = min( $child_prices );
			$max_price = max( $child_prices );
		} else {
			$min_price = '';
			$max_price = '';
		}

		if ( '' !== $min_price ) {
			if ( $min_price !== $max_price ) {
				$price = wc_format_price_range( $min_price, $max_price );
			} else {
				$price = wc_price( $min_price );
			}

			$is_free = 0 === $min_price && 0 === $max_price;

			if ( $is_free ) {
				$price = apply_filters( 'woocommerce_grouped_free_price_html', __( 'Free!', 'woocommerce' ), $this );
			} else {
				$price = apply_filters( 'woocommerce_grouped_price_html', $price . $this->get_price_suffix(), $this, $child_prices );
			}
		} else {
			$price = apply_filters( 'woocommerce_grouped_empty_price_html', '', $this );
		}

		return apply_filters( 'woocommerce_get_price_html', $price, $this );
	}

	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	|
	| Methods for getting data from the product object.
	*/

	/**
	 * Return the children of this product.
	 *
	 * @param  string $context What the value is for. Valid values are view and edit.
	 * @return array
	 */
	public function get_children( $context = 'view' ) {
		return $this->get_prop( 'children', $context );
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	|
	| Methods for getting data from the product object.
	*/

	/**
	 * Return the children of this product.
	 *
	 * @param array $children List of product children.
	 */
	public function set_children( $children ) {
		$this->set_prop( 'children', array_filter( wp_parse_id_list( (array) $children ) ) );
	}

	/*
	|--------------------------------------------------------------------------
	| Sync with children.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Sync a grouped product with it's children. These sync functions sync
	 * upwards (from child to parent) when the variation is saved.
	 *
	 * @param WC_Product|int $product Product object or ID for which you wish to sync.
	 * @param bool           $save If true, the product object will be saved to the DB before returning it.
	 * @return WC_Product Synced product object.
	 */
	public static function sync( $product, $save = true ) {
		if ( ! is_a( $product, 'WC_Product' ) ) {
			$product = wc_get_product( $product );
		}
		if ( is_a( $product, 'WC_Product_Grouped' ) ) {
			$data_store = WC_Data_Store::load( 'product-' . $product->get_type() );
			$data_store->sync_price( $product );
			if ( $save ) {
				$product->save();
			}
		}
		return $product;
	}
}

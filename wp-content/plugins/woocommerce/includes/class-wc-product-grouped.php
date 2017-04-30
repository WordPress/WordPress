<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Grouped Product Class
 *
 * Grouped products cannot be purchased - they are wrappers for other products.
 *
 * @class 		WC_Product_Grouped
 * @version		2.0.0
 * @package		WooCommerce/Classes/Products
 * @category	Class
 * @author 		WooThemes
 */
class WC_Product_Grouped extends WC_Product {

	/** @public array Array of child products/posts/variations. */
	public $children;

	/** @public string The product's total stock, including that of its children. */
	public $total_stock;

	/**
	 * __construct function.
	 *
	 * @access public
	 * @param mixed $product
	 */
	public function __construct( $product ) {
		$this->product_type = 'grouped';
		parent::__construct( $product );
	}

	/**
	 * Get the add to cart button text
	 *
	 * @access public
	 * @return string
	 */
	public function add_to_cart_text() {
		return apply_filters( 'woocommerce_product_add_to_cart_text', __( 'View products', 'woocommerce' ), $this );
	}

    /**
     * Get total stock.
     *
     * This is the stock of parent and children combined.
     *
     * @access public
     * @return int
     */
    public function get_total_stock() {

        if ( empty( $this->total_stock ) ) {

        	$transient_name = 'wc_product_total_stock_' . $this->id;

        	if ( false === ( $this->total_stock = get_transient( $transient_name ) ) ) {
		        $this->total_stock = $this->stock;

				if ( sizeof( $this->get_children() ) > 0 ) {
					foreach ($this->get_children() as $child_id) {
						$stock = get_post_meta( $child_id, '_stock', true );

						if ( $stock != '' ) {
							$this->total_stock += intval( $stock );
						}
					}
				}

				set_transient( $transient_name, $this->total_stock, YEAR_IN_SECONDS );
			}
		}

		return apply_filters( 'woocommerce_stock_amount', $this->total_stock );
    }

	/**
	 * Return the products children posts.
	 *
	 * @access public
	 * @return array
	 */
	public function get_children() {

		if ( ! is_array( $this->children ) ) {

			$this->children = array();

			$transient_name = 'wc_product_children_ids_' . $this->id;

        	if ( false === ( $this->children = get_transient( $transient_name ) ) ) {

		        $this->children = get_posts( 'post_parent=' . $this->id . '&post_type=product&orderby=menu_order&order=ASC&fields=ids&post_status=publish&numberposts=-1' );

				set_transient( $transient_name, $this->children, YEAR_IN_SECONDS );
			}
		}

		return (array) $this->children;
	}


	/**
	 * get_child function.
	 *
	 * @access public
	 * @param mixed $child_id
	 * @return object WC_Product or WC_Product_variation
	 */
	public function get_child( $child_id ) {
		return get_product( $child_id );
	}


	/**
	 * Returns whether or not the product has any child product.
	 *
	 * @access public
	 * @return bool
	 */
	public function has_child() {
		return sizeof( $this->get_children() ) ? true : false;
	}


	/**
	 * Returns whether or not the product is on sale.
	 *
	 * @access public
	 * @return bool
	 */
	public function is_on_sale() {
		if ( $this->has_child() ) {

			foreach ( $this->get_children() as $child_id ) {
				$sale_price = get_post_meta( $child_id, '_sale_price', true );
				if ( $sale_price !== "" && $sale_price >= 0 )
					return true;
			}

		} else {

			if ( $this->sale_price && $this->sale_price == $this->price )
				return true;

		}
		return false;
	}


	/**
	 * Returns false if the product cannot be bought.
	 *
	 * @access public
	 * @return bool
	 */
	public function is_purchasable() {
		return apply_filters( 'woocommerce_is_purchasable', false, $this );
	}

	/**
	 * Returns the price in html format.
	 *
	 * @access public
	 * @param string $price (default: '')
	 * @return string
	 */
	public function get_price_html( $price = '' ) {
		$tax_display_mode = get_option( 'woocommerce_tax_display_shop' );
		$child_prices     = array();

		foreach ( $this->get_children() as $child_id )
			$child_prices[] = get_post_meta( $child_id, '_price', true );

		$child_prices     = array_unique( $child_prices );
		$get_price_method = 'get_price_' . $tax_display_mode . 'uding_tax';

		if ( ! empty( $child_prices ) ) {
			$min_price = min( $child_prices );
			$max_price = max( $child_prices );
		} else {
			$min_price = '';
			$max_price = '';
		}

		if ( $min_price ) {
			if ( $min_price == $max_price ) {
				$display_price = wc_price( $this->$get_price_method( 1, $min_price ) );
			} else {
				$from          = wc_price( $this->$get_price_method( 1, $min_price ) );
				$to            = wc_price( $this->$get_price_method( 1, $max_price ) );
				$display_price = sprintf( _x( '%1$s&ndash;%2$s', 'Price range: from-to', 'woocommerce' ), $from, $to );
			}

			$price .= $display_price . $this->get_price_suffix();

			$price = apply_filters( 'woocommerce_grouped_price_html', $price, $this );
		} else {
			$price = apply_filters( 'woocommerce_grouped_empty_price_html', '', $this );
		}

		return apply_filters( 'woocommerce_get_price_html', $price, $this );
	}
}

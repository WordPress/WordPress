<?php
/**
 * Legacy cart
 *
 * Legacy and deprecated functions are here to keep the WC_Cart class clean.
 * This class will be removed in future versions.
 *
 * @version  3.2.0
 * @package  WooCommerce\Classes
 * @category Class
 * @author   Automattic
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Legacy cart class.
 */
abstract class WC_Legacy_Cart {

	/**
	 * Array of defaults. Not used since 3.2.
	 *
	 * @deprecated 3.2.0
	 */
	public $cart_session_data = array(
		'cart_contents_total'         => 0,
		'total'                       => 0,
		'subtotal'                    => 0,
		'subtotal_ex_tax'             => 0,
		'tax_total'                   => 0,
		'taxes'                       => array(),
		'shipping_taxes'              => array(),
		'discount_cart'               => 0,
		'discount_cart_tax'           => 0,
		'shipping_total'              => 0,
		'shipping_tax_total'          => 0,
		'coupon_discount_amounts'     => array(),
		'coupon_discount_tax_amounts' => array(),
		'fee_total'                   => 0,
		'fees'                        => array(),
	);

	/**
	 * Contains an array of coupon usage counts after they have been applied.
	 *
	 * @deprecated 3.2.0
	 * @var array
	 */
	public $coupon_applied_count = array();

	/**
	 * Map legacy variables.
	 *
	 * @param string $name Property name.
	 * @param mixed  $value Value to set.
	 */
	public function __isset( $name ) {
		$legacy_keys = array_merge(
			array(
				'dp',
				'prices_include_tax',
				'round_at_subtotal',
				'cart_contents_total',
				'total',
				'subtotal',
				'subtotal_ex_tax',
				'tax_total',
				'fee_total',
				'discount_cart',
				'discount_cart_tax',
				'shipping_total',
				'shipping_tax_total',
				'display_totals_ex_tax',
				'display_cart_ex_tax',
				'cart_contents_weight',
				'cart_contents_count',
				'coupons',
				'taxes',
				'shipping_taxes',
				'coupon_discount_amounts',
				'coupon_discount_tax_amounts',
				'fees',
				'tax',
				'discount_total',
				'tax_display_cart',
			),
			is_array( $this->cart_session_data ) ? array_keys( $this->cart_session_data ) : array()
		);

		if ( in_array( $name, $legacy_keys, true ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Magic getters.
	 *
	 * If you add/remove cases here please update $legacy_keys in __isset accordingly.
	 *
	 * @param string $name Property name.
	 * @return mixed
	 */
	public function &__get( $name ) {
		$value = '';

		switch ( $name ) {
			case 'dp' :
				$value = wc_get_price_decimals();
				break;
			case 'prices_include_tax' :
				$value = wc_prices_include_tax();
				break;
			case 'round_at_subtotal' :
				$value = 'yes' === get_option( 'woocommerce_tax_round_at_subtotal' );
				break;
			case 'cart_contents_total' :
				$value = $this->get_cart_contents_total();
				break;
			case 'total' :
				$value = $this->get_total( 'edit' );
				break;
			case 'subtotal' :
				$value = $this->get_subtotal() + $this->get_subtotal_tax();
				break;
			case 'subtotal_ex_tax' :
				$value = $this->get_subtotal();
				break;
			case 'tax_total' :
				$value = $this->get_fee_tax() + $this->get_cart_contents_tax();
				break;
			case 'fee_total' :
				$value = $this->get_fee_total();
				break;
			case 'discount_cart' :
				$value = $this->get_discount_total();
				break;
			case 'discount_cart_tax' :
				$value = $this->get_discount_tax();
				break;
			case 'shipping_total' :
				$value = $this->get_shipping_total();
				break;
			case 'shipping_tax_total' :
				$value = $this->get_shipping_tax();
				break;
			case 'display_totals_ex_tax' :
			case 'display_cart_ex_tax' :
				$value = ! $this->display_prices_including_tax();
				break;
			case 'cart_contents_weight' :
				$value = $this->get_cart_contents_weight();
				break;
			case 'cart_contents_count' :
				$value = $this->get_cart_contents_count();
				break;
			case 'coupons' :
				$value = $this->get_coupons();
				break;

			// Arrays returned by reference to allow modification without notices. TODO: Remove in 4.0.
			case 'taxes' :
				wc_deprecated_function( 'WC_Cart->taxes', '3.2', sprintf( 'getters (%s) and setters (%s)', 'WC_Cart::get_cart_contents_taxes()', 'WC_Cart::set_cart_contents_taxes()' ) );
				$value = &$this->totals[ 'cart_contents_taxes' ];
				break;
			case 'shipping_taxes' :
				wc_deprecated_function( 'WC_Cart->shipping_taxes', '3.2', sprintf( 'getters (%s) and setters (%s)', 'WC_Cart::get_shipping_taxes()', 'WC_Cart::set_shipping_taxes()' ) );
				$value = &$this->totals[ 'shipping_taxes' ];
				break;
			case 'coupon_discount_amounts' :
				$value = &$this->coupon_discount_totals;
				break;
			case 'coupon_discount_tax_amounts' :
				$value = &$this->coupon_discount_tax_totals;
				break;
			case 'fees' :
				wc_deprecated_function( 'WC_Cart->fees', '3.2', sprintf( 'the fees API (%s)', 'WC_Cart::get_fees' ) );

				// Grab fees from the new API.
				$new_fees   = $this->fees_api()->get_fees();

				// Add new fees to the legacy prop so it can be adjusted via legacy property.
				$this->fees = $new_fees;

				// Return by reference.
				$value = &$this->fees;
				break;
			// Deprecated args. TODO: Remove in 4.0.
			case 'tax' :
				wc_deprecated_argument( 'WC_Cart->tax', '2.3', 'Use WC_Tax directly' );
				$this->tax = new WC_Tax();
				$value = $this->tax;
				break;
			case 'discount_total':
				wc_deprecated_argument( 'WC_Cart->discount_total', '2.3', 'After tax coupons are no longer supported. For more information see: https://woocommerce.wordpress.com/2014/12/upcoming-coupon-changes-in-woocommerce-2-3/' );
				$value = 0;
				break;
			case 'tax_display_cart':
				wc_deprecated_argument( 'WC_Cart->tax_display_cart', '4.4', 'Use WC_Cart->get_tax_price_display_mode() instead.' );
				$value = $this->get_tax_price_display_mode();
				break;
		}
		return $value;
	}

	/**
	 * Map legacy variables to setters.
	 *
	 * @param string $name Property name.
	 * @param mixed  $value Value to set.
	 */
	public function __set( $name, $value ) {
		switch ( $name ) {
			case 'cart_contents_total' :
				$this->set_cart_contents_total( $value );
				break;
			case 'total' :
				$this->set_total( $value );
				break;
			case 'subtotal' :
				$this->set_subtotal( $value );
				break;
			case 'subtotal_ex_tax' :
				$this->set_subtotal( $value );
				break;
			case 'tax_total' :
				$this->set_cart_contents_tax( $value );
				$this->set_fee_tax( 0 );
				break;
			case 'taxes' :
				$this->set_cart_contents_taxes( $value );
				break;
			case 'shipping_taxes' :
				$this->set_shipping_taxes( $value );
				break;
			case 'fee_total' :
				$this->set_fee_total( $value );
				break;
			case 'discount_cart' :
				$this->set_discount_total( $value );
				break;
			case 'discount_cart_tax' :
				$this->set_discount_tax( $value );
				break;
			case 'shipping_total' :
				$this->set_shipping_total( $value );
				break;
			case 'shipping_tax_total' :
				$this->set_shipping_tax( $value );
				break;
			case 'coupon_discount_amounts' :
				$this->set_coupon_discount_totals( $value );
				break;
			case 'coupon_discount_tax_amounts' :
				$this->set_coupon_discount_tax_totals( $value );
				break;
			case 'fees' :
				wc_deprecated_function( 'WC_Cart->fees', '3.2', sprintf( 'the fees API (%s)', 'WC_Cart::add_fee' ) );
				$this->fees = $value;
				break;
			default :
				$this->$name = $value;
				break;
		}
	}

	/**
	 * Methods moved to session class in 3.2.0.
	 */
	public function get_cart_from_session() { $this->session->get_cart_from_session(); }
	public function maybe_set_cart_cookies() { $this->session->maybe_set_cart_cookies(); }
	public function set_session() { $this->session->set_session(); }
	public function get_cart_for_session() { return $this->session->get_cart_for_session(); }
	public function persistent_cart_update() { $this->session->persistent_cart_update(); }
	public function persistent_cart_destroy() { $this->session->persistent_cart_destroy(); }

	/**
	 * Get the total of all cart discounts.
	 *
	 * @return float
	 */
	public function get_cart_discount_total() {
		return $this->get_discount_total();
	}

	/**
	 * Get the total of all cart tax discounts (used for discounts on tax inclusive prices).
	 *
	 * @return float
	 */
	public function get_cart_discount_tax_total() {
		return $this->get_discount_tax();
	}

	/**
	 * Renamed for consistency.
	 *
	 * @param string $coupon_code
	 * @return bool	True if the coupon is applied, false if it does not exist or cannot be applied.
	 */
	public function add_discount( $coupon_code ) {
		return $this->apply_coupon( $coupon_code );
	}
	/**
	 * Remove taxes.
	 *
	 * @deprecated 3.2.0 Taxes are never calculated if customer is tax except making this function unused.
	 */
	public function remove_taxes() {
		wc_deprecated_function( 'WC_Cart::remove_taxes', '3.2', '' );
	}
	/**
	 * Init.
	 *
	 * @deprecated 3.2.0 Session is loaded via hooks rather than directly.
	 */
	public function init() {
		wc_deprecated_function( 'WC_Cart::init', '3.2', '' );
		$this->get_cart_from_session();
	}

	/**
	 * Function to apply discounts to a product and get the discounted price (before tax is applied).
	 *
	 * @deprecated 3.2.0 Calculation and coupon logic is handled in WC_Cart_Totals.
	 * @param mixed $values Cart item.
	 * @param mixed $price Price of item.
	 * @param bool  $add_totals Legacy.
	 * @return float price
	 */
	public function get_discounted_price( $values, $price, $add_totals = false ) {
		wc_deprecated_function( 'WC_Cart::get_discounted_price', '3.2', '' );

		$cart_item_key = $values['key'];
		$cart_item     = $this->cart_contents[ $cart_item_key ];

		return $cart_item['line_total'];
	}

	/**
	 * Gets the url to the cart page.
	 *
	 * @deprecated 2.5.0 in favor to wc_get_cart_url()
	 * @return string url to page
	 */
	public function get_cart_url() {
		wc_deprecated_function( 'WC_Cart::get_cart_url', '2.5', 'wc_get_cart_url' );
		return wc_get_cart_url();
	}

	/**
	 * Gets the url to the checkout page.
	 *
	 * @deprecated 2.5.0 in favor to wc_get_checkout_url()
	 * @return string url to page
	 */
	public function get_checkout_url() {
		wc_deprecated_function( 'WC_Cart::get_checkout_url', '2.5', 'wc_get_checkout_url' );
		return wc_get_checkout_url();
	}

	/**
	 * Sees if we need a shipping address.
	 *
	 * @deprecated 2.5.0 in favor to wc_ship_to_billing_address_only()
	 * @return bool
	 */
	public function ship_to_billing_address_only() {
		wc_deprecated_function( 'WC_Cart::ship_to_billing_address_only', '2.5', 'wc_ship_to_billing_address_only' );
		return wc_ship_to_billing_address_only();
	}

	/**
	 * Coupons enabled function. Filterable.
	 *
	 * @deprecated 2.5.0
	 * @return bool
	 */
	public function coupons_enabled() {
		wc_deprecated_function( 'WC_Legacy_Cart::coupons_enabled', '2.5.0', 'wc_coupons_enabled' );
		return wc_coupons_enabled();
	}

	/**
	 * Gets the total (product) discount amount - these are applied before tax.
	 *
	 * @deprecated 2.3.0 Order discounts (after tax) removed in 2.3 so multiple methods for discounts are no longer required.
	 * @return mixed formatted price or false if there are none.
	 */
	public function get_discounts_before_tax() {
		wc_deprecated_function( 'get_discounts_before_tax', '2.3', 'get_total_discount' );
		if ( $this->get_cart_discount_total() ) {
			$discounts_before_tax = wc_price( $this->get_cart_discount_total() );
		} else {
			$discounts_before_tax = false;
		}
		return apply_filters( 'woocommerce_cart_discounts_before_tax', $discounts_before_tax, $this );
	}

	/**
	 * Get the total of all order discounts (after tax discounts).
	 *
	 * @deprecated 2.3.0 Order discounts (after tax) removed in 2.3.
	 * @return int
	 */
	public function get_order_discount_total() {
		wc_deprecated_function( 'get_order_discount_total', '2.3' );
		return 0;
	}

	/**
	 * Function to apply cart discounts after tax.
	 *
	 * @deprecated 2.3.0 Coupons can not be applied after tax.
	 * @param $values
	 * @param $price
	 */
	public function apply_cart_discounts_after_tax( $values, $price ) {
		wc_deprecated_function( 'apply_cart_discounts_after_tax', '2.3' );
	}

	/**
	 * Function to apply product discounts after tax.
	 *
	 * @deprecated 2.3.0 Coupons can not be applied after tax.
	 *
	 * @param $values
	 * @param $price
	 */
	public function apply_product_discounts_after_tax( $values, $price ) {
		wc_deprecated_function( 'apply_product_discounts_after_tax', '2.3' );
	}

	/**
	 * Gets the order discount amount - these are applied after tax.
	 *
	 * @deprecated 2.3.0 Coupons can not be applied after tax.
	 */
	public function get_discounts_after_tax() {
		wc_deprecated_function( 'get_discounts_after_tax', '2.3' );
	}
}

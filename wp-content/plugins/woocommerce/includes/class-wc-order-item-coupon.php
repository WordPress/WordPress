<?php
/**
 * Order Line Item (coupon)
 *
 * @package WooCommerce\Classes
 * @version 3.0.0
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Order item coupon class.
 */
class WC_Order_Item_Coupon extends WC_Order_Item {

	/**
	 * Order Data array. This is the core order data exposed in APIs since 3.0.0.
	 *
	 * @since 3.0.0
	 * @var array
	 */
	protected $extra_data = array(
		'code'         => '',
		'discount'     => 0,
		'discount_tax' => 0,
	);

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Set order item name.
	 *
	 * @param string $value Coupon code.
	 */
	public function set_name( $value ) {
		return $this->set_code( $value );
	}

	/**
	 * Set code.
	 *
	 * @param string $value Coupon code.
	 */
	public function set_code( $value ) {
		$this->set_prop( 'code', wc_format_coupon_code( $value ) );
	}

	/**
	 * Set discount amount.
	 *
	 * @param string $value Discount.
	 */
	public function set_discount( $value ) {
		$this->set_prop( 'discount', wc_format_decimal( $value ) );
	}

	/**
	 * Set discounted tax amount.
	 *
	 * @param string $value Discount tax.
	 */
	public function set_discount_tax( $value ) {
		$this->set_prop( 'discount_tax', wc_format_decimal( $value ) );
	}

	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Get order item type.
	 *
	 * @return string
	 */
	public function get_type() {
		return 'coupon';
	}

	/**
	 * Get order item name.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return string
	 */
	public function get_name( $context = 'view' ) {
		return $this->get_code( $context );
	}

	/**
	 * Get coupon code.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return string
	 */
	public function get_code( $context = 'view' ) {
		return $this->get_prop( 'code', $context );
	}

	/**
	 * Get discount amount.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return string
	 */
	public function get_discount( $context = 'view' ) {
		return $this->get_prop( 'discount', $context );
	}

	/**
	 * Get discounted tax amount.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_discount_tax( $context = 'view' ) {
		return $this->get_prop( 'discount_tax', $context );
	}

	/*
	|--------------------------------------------------------------------------
	| Array Access Methods
	|--------------------------------------------------------------------------
	|
	| For backwards compatibility with legacy arrays.
	|
	*/

	/**
	 * OffsetGet for ArrayAccess/Backwards compatibility.
	 *
	 * @deprecated 4.4.0
	 * @param string $offset Offset.
	 * @return mixed
	 */
	#[\ReturnTypeWillChange]
	public function offsetGet( $offset ) {
		wc_deprecated_function( 'WC_Order_Item_Coupon::offsetGet', '4.4.0', '' );
		if ( 'discount_amount' === $offset ) {
			$offset = 'discount';
		} elseif ( 'discount_amount_tax' === $offset ) {
			$offset = 'discount_tax';
		}
		return parent::offsetGet( $offset );
	}

	/**
	 * OffsetSet for ArrayAccess/Backwards compatibility.
	 *
	 * @deprecated 4.4.0
	 * @param string $offset Offset.
	 * @param mixed  $value  Value.
	 */
	#[\ReturnTypeWillChange]
	public function offsetSet( $offset, $value ) {
		wc_deprecated_function( 'WC_Order_Item_Coupon::offsetSet', '4.4.0', '' );
		if ( 'discount_amount' === $offset ) {
			$offset = 'discount';
		} elseif ( 'discount_amount_tax' === $offset ) {
			$offset = 'discount_tax';
		}
		parent::offsetSet( $offset, $value );
	}

	/**
	 * OffsetExists for ArrayAccess.
	 *
	 * @param string $offset Offset.
	 * @return bool
	 */
	#[\ReturnTypeWillChange]
	public function offsetExists( $offset ) {
		if ( in_array( $offset, array( 'discount_amount', 'discount_amount_tax' ), true ) ) {
			return true;
		}
		return parent::offsetExists( $offset );
	}
}

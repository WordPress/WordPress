<?php
/**
 * Order Line Item (tax)
 *
 * @package WooCommerce\Classes
 * @version 3.0.0
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Order item tax.
 */
class WC_Order_Item_Tax extends WC_Order_Item {

	/**
	 * Order Data array. This is the core order data exposed in APIs since 3.0.0.
	 *
	 * @since 3.0.0
	 * @var array
	 */
	protected $extra_data = array(
		'rate_code'          => '',
		'rate_id'            => 0,
		'label'              => '',
		'compound'           => false,
		'tax_total'          => 0,
		'shipping_tax_total' => 0,
		'rate_percent'       => null,
	);

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Set order item name.
	 *
	 * @param string $value Name.
	 */
	public function set_name( $value ) {
		$this->set_rate_code( $value );
	}

	/**
	 * Set item name.
	 *
	 * @param string $value Rate code.
	 */
	public function set_rate_code( $value ) {
		$this->set_prop( 'rate_code', wc_clean( $value ) );
	}

	/**
	 * Set item name.
	 *
	 * @param string $value Label.
	 */
	public function set_label( $value ) {
		$this->set_prop( 'label', wc_clean( $value ) );
	}

	/**
	 * Set tax rate id.
	 *
	 * @param int $value Rate ID.
	 */
	public function set_rate_id( $value ) {
		$this->set_prop( 'rate_id', absint( $value ) );
	}

	/**
	 * Set tax total.
	 *
	 * @param string $value Tax total.
	 */
	public function set_tax_total( $value ) {
		$this->set_prop( 'tax_total', $value ? wc_format_decimal( $value ) : 0 );
	}

	/**
	 * Set shipping tax total.
	 *
	 * @param string $value Shipping tax total.
	 */
	public function set_shipping_tax_total( $value ) {
		$this->set_prop( 'shipping_tax_total', $value ? wc_format_decimal( $value ) : 0 );
	}

	/**
	 * Set compound.
	 *
	 * @param bool $value If tax is compound.
	 */
	public function set_compound( $value ) {
		$this->set_prop( 'compound', (bool) $value );
	}

	/**
	 * Set rate value.
	 *
	 * @param float $value tax rate value.
	 */
	public function set_rate_percent( $value ) {
		$this->set_prop( 'rate_percent', (float) $value );
	}

	/**
	 * Set properties based on passed in tax rate by ID.
	 *
	 * @param int $tax_rate_id Tax rate ID.
	 */
	public function set_rate( $tax_rate_id ) {
		$tax_rate = WC_Tax::_get_tax_rate( $tax_rate_id, OBJECT );

		$this->set_rate_id( $tax_rate_id );
		$this->set_rate_code( WC_Tax::get_rate_code( $tax_rate ) );
		$this->set_label( WC_Tax::get_rate_label( $tax_rate ) );
		$this->set_compound( WC_Tax::is_compound( $tax_rate ) );
		$this->set_rate_percent( WC_Tax::get_rate_percent_value( $tax_rate ) );
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
		return 'tax';
	}

	/**
	 * Get rate code/name.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return string
	 */
	public function get_name( $context = 'view' ) {
		return $this->get_rate_code( $context );
	}

	/**
	 * Get rate code/name.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return string
	 */
	public function get_rate_code( $context = 'view' ) {
		return $this->get_prop( 'rate_code', $context );
	}

	/**
	 * Get label.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return string
	 */
	public function get_label( $context = 'view' ) {
		$label = $this->get_prop( 'label', $context );
		if ( 'view' === $context ) {
			return $label ? $label : __( 'Tax', 'woocommerce' );
		} else {
			return $label;
		}
	}

	/**
	 * Get tax rate ID.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return int
	 */
	public function get_rate_id( $context = 'view' ) {
		return $this->get_prop( 'rate_id', $context );
	}

	/**
	 * Get tax_total
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return string
	 */
	public function get_tax_total( $context = 'view' ) {
		return $this->get_prop( 'tax_total', $context );
	}

	/**
	 * Get shipping_tax_total
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return string
	 */
	public function get_shipping_tax_total( $context = 'view' ) {
		return $this->get_prop( 'shipping_tax_total', $context );
	}

	/**
	 * Get compound.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return bool
	 */
	public function get_compound( $context = 'view' ) {
		return $this->get_prop( 'compound', $context );
	}

	/**
	 * Is this a compound tax rate?
	 *
	 * @return boolean
	 */
	public function is_compound() {
		return $this->get_compound();
	}

	/**
	 * Get rate value
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return float
	 */
	public function get_rate_percent( $context = 'view' ) {
		return $this->get_prop( 'rate_percent', $context );
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
	 * O for ArrayAccess/Backwards compatibility.
	 *
	 * @param string $offset Offset.
	 * @return mixed
	 */
	#[\ReturnTypeWillChange]
	public function offsetGet( $offset ) {
		if ( 'tax_amount' === $offset ) {
			$offset = 'tax_total';
		} elseif ( 'shipping_tax_amount' === $offset ) {
			$offset = 'shipping_tax_total';
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
		wc_deprecated_function( 'WC_Order_Item_Tax::offsetSet', '4.4.0', '' );
		if ( 'tax_amount' === $offset ) {
			$offset = 'tax_total';
		} elseif ( 'shipping_tax_amount' === $offset ) {
			$offset = 'shipping_tax_total';
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
		if ( in_array( $offset, array( 'tax_amount', 'shipping_tax_amount' ), true ) ) {
			return true;
		}
		return parent::offsetExists( $offset );
	}
}

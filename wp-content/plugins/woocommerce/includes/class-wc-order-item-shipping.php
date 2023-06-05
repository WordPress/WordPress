<?php
/**
 * Order Line Item (shipping)
 *
 * @package WooCommerce\Classes
 * @version 3.0.0
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Order item shipping class.
 */
class WC_Order_Item_Shipping extends WC_Order_Item {

	/**
	 * Order Data array. This is the core order data exposed in APIs since 3.0.0.
	 *
	 * @since 3.0.0
	 * @var array
	 */
	protected $extra_data = array(
		'method_title' => '',
		'method_id'    => '',
		'instance_id'  => '',
		'total'        => 0,
		'total_tax'    => 0,
		'taxes'        => array(
			'total' => array(),
		),
	);

	/**
	 * Calculate item taxes.
	 *
	 * @since  3.2.0
	 * @param  array $calculate_tax_for Location data to get taxes for. Required.
	 * @return bool  True if taxes were calculated.
	 */
	public function calculate_taxes( $calculate_tax_for = array() ) {
		if ( ! isset( $calculate_tax_for['country'], $calculate_tax_for['state'], $calculate_tax_for['postcode'], $calculate_tax_for['city'], $calculate_tax_for['tax_class'] ) ) {
			return false;
		}
		if ( wc_tax_enabled() ) {
			$tax_rates = WC_Tax::find_shipping_rates( $calculate_tax_for );
			$taxes     = WC_Tax::calc_tax( $this->get_total(), $tax_rates, false );
			$this->set_taxes( array( 'total' => $taxes ) );
		} else {
			$this->set_taxes( false );
		}

		do_action( 'woocommerce_order_item_shipping_after_calculate_taxes', $this, $calculate_tax_for );

		return true;
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Set order item name.
	 *
	 * @param string $value Value to set.
	 * @throws WC_Data_Exception May throw exception if data is invalid.
	 */
	public function set_name( $value ) {
		$this->set_method_title( $value );
	}

	/**
	 * Set method title.
	 *
	 * @param string $value Value to set.
	 * @throws WC_Data_Exception May throw exception if data is invalid.
	 */
	public function set_method_title( $value ) {
		$this->set_prop( 'name', wc_clean( $value ) );
		$this->set_prop( 'method_title', wc_clean( $value ) );
	}

	/**
	 * Set shipping method id.
	 *
	 * @param string $value Value to set.
	 * @throws WC_Data_Exception May throw exception if data is invalid.
	 */
	public function set_method_id( $value ) {
		$this->set_prop( 'method_id', wc_clean( $value ) );
	}

	/**
	 * Set shipping instance id.
	 *
	 * @param string $value Value to set.
	 * @throws WC_Data_Exception May throw exception if data is invalid.
	 */
	public function set_instance_id( $value ) {
		$this->set_prop( 'instance_id', wc_clean( $value ) );
	}

	/**
	 * Set total.
	 *
	 * @param string $value Value to set.
	 * @throws WC_Data_Exception May throw exception if data is invalid.
	 */
	public function set_total( $value ) {
		$this->set_prop( 'total', wc_format_decimal( $value ) );
	}

	/**
	 * Set total tax.
	 *
	 * @param string $value Value to set.
	 * @throws WC_Data_Exception May throw exception if data is invalid.
	 */
	protected function set_total_tax( $value ) {
		$this->set_prop( 'total_tax', wc_format_decimal( $value ) );
	}

	/**
	 * Set taxes.
	 *
	 * This is an array of tax ID keys with total amount values.
	 *
	 * @param array $raw_tax_data Value to set.
	 * @throws WC_Data_Exception May throw exception if data is invalid.
	 */
	public function set_taxes( $raw_tax_data ) {
		$raw_tax_data = maybe_unserialize( $raw_tax_data );
		$tax_data     = array(
			'total' => array(),
		);
		if ( isset( $raw_tax_data['total'] ) ) {
			$tax_data['total'] = array_map( 'wc_format_decimal', $raw_tax_data['total'] );
		} elseif ( ! empty( $raw_tax_data ) && is_array( $raw_tax_data ) ) {
			// Older versions just used an array.
			$tax_data['total'] = array_map( 'wc_format_decimal', $raw_tax_data );
		}
		$this->set_prop( 'taxes', $tax_data );

		if ( 'yes' === get_option( 'woocommerce_tax_round_at_subtotal' ) ) {
			$this->set_total_tax( array_sum( $tax_data['total'] ) );
		} else {
			$this->set_total_tax( array_sum( array_map( 'wc_round_tax_total', $tax_data['total'] ) ) );
		}
	}

	/**
	 * Set properties based on passed in shipping rate object.
	 *
	 * @param WC_Shipping_Rate $shipping_rate Shipping rate to set.
	 */
	public function set_shipping_rate( $shipping_rate ) {
		$this->set_method_title( $shipping_rate->get_label() );
		$this->set_method_id( $shipping_rate->get_method_id() );
		$this->set_instance_id( $shipping_rate->get_instance_id() );
		$this->set_total( $shipping_rate->get_cost() );
		$this->set_taxes( $shipping_rate->get_taxes() );
		$this->set_meta_data( $shipping_rate->get_meta_data() );
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
		return 'shipping';
	}

	/**
	 * Get order item name.
	 *
	 * @param  string $context View or edit context.
	 * @return string
	 */
	public function get_name( $context = 'view' ) {
		return $this->get_method_title( $context );
	}

	/**
	 * Get title.
	 *
	 * @param  string $context View or edit context.
	 * @return string
	 */
	public function get_method_title( $context = 'view' ) {
		$method_title = $this->get_prop( 'method_title', $context );
		if ( 'view' === $context ) {
			return $method_title ? $method_title : __( 'Shipping', 'woocommerce' );
		} else {
			return $method_title;
		}
	}

	/**
	 * Get method ID.
	 *
	 * @param  string $context View or edit context.
	 * @return string
	 */
	public function get_method_id( $context = 'view' ) {
		return $this->get_prop( 'method_id', $context );
	}

	/**
	 * Get instance ID.
	 *
	 * @param  string $context View or edit context.
	 * @return string
	 */
	public function get_instance_id( $context = 'view' ) {
		return $this->get_prop( 'instance_id', $context );
	}

	/**
	 * Get total cost.
	 *
	 * @param  string $context View or edit context.
	 * @return string
	 */
	public function get_total( $context = 'view' ) {
		return $this->get_prop( 'total', $context );
	}

	/**
	 * Get total tax.
	 *
	 * @param  string $context View or edit context.
	 * @return string
	 */
	public function get_total_tax( $context = 'view' ) {
		return $this->get_prop( 'total_tax', $context );
	}

	/**
	 * Get taxes.
	 *
	 * @param  string $context View or edit context.
	 * @return array
	 */
	public function get_taxes( $context = 'view' ) {
		return $this->get_prop( 'taxes', $context );
	}

	/**
	 * Get tax class.
	 *
	 * @param  string $context View or edit context.
	 * @return string
	 */
	public function get_tax_class( $context = 'view' ) {
		return get_option( 'woocommerce_shipping_tax_class' );
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
	 * Offset get: for ArrayAccess/Backwards compatibility.
	 *
	 * @param string $offset Key.
	 * @return mixed
	 */
	#[\ReturnTypeWillChange]
	public function offsetGet( $offset ) {
		if ( 'cost' === $offset ) {
			$offset = 'total';
		}
		return parent::offsetGet( $offset );
	}

	/**
	 * Offset set: for ArrayAccess/Backwards compatibility.
	 *
	 * @deprecated 4.4.0
	 * @param string $offset Key.
	 * @param mixed  $value Value to set.
	 */
	#[\ReturnTypeWillChange]
	public function offsetSet( $offset, $value ) {
		wc_deprecated_function( 'WC_Order_Item_Shipping::offsetSet', '4.4.0', '' );
		if ( 'cost' === $offset ) {
			$offset = 'total';
		}
		parent::offsetSet( $offset, $value );
	}

	/**
	 * Offset exists: for ArrayAccess.
	 *
	 * @param string $offset Key.
	 * @return bool
	 */
	#[\ReturnTypeWillChange]
	public function offsetExists( $offset ) {
		if ( in_array( $offset, array( 'cost' ), true ) ) {
			return true;
		}
		return parent::offsetExists( $offset );
	}
}

<?php
/**
 * WooCommerce Shipping Rate
 *
 * Simple Class for storing rates.
 *
 * @package WooCommerce\Classes\Shipping
 * @since   2.6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Shipping rate class.
 */
class WC_Shipping_Rate {

	/**
	 * Stores data for this rate.
	 *
	 * @since 3.2.0
	 * @var   array
	 */
	protected $data = array(
		'id'          => '',
		'method_id'   => '',
		'instance_id' => 0,
		'label'       => '',
		'cost'        => 0,
		'taxes'       => array(),
	);

	/**
	 * Stores meta data for this rate.
	 *
	 * @since 2.6.0
	 * @var   array
	 */
	protected $meta_data = array();

	/**
	 * Constructor.
	 *
	 * @param string  $id          Shipping rate ID.
	 * @param string  $label       Shipping rate label.
	 * @param integer $cost        Cost.
	 * @param array   $taxes       Taxes applied to shipping rate.
	 * @param string  $method_id   Shipping method ID.
	 * @param int     $instance_id Shipping instance ID.
	 */
	public function __construct( $id = '', $label = '', $cost = 0, $taxes = array(), $method_id = '', $instance_id = 0 ) {
		$this->set_id( $id );
		$this->set_label( $label );
		$this->set_cost( $cost );
		$this->set_taxes( $taxes );
		$this->set_method_id( $method_id );
		$this->set_instance_id( $instance_id );
	}

	/**
	 * Magic methods to support direct access to props.
	 *
	 * @since 3.2.0
	 * @param string $key Key.
	 * @return bool
	 */
	public function __isset( $key ) {
		if ( 'meta_data' === $key ) {
			wc_doing_it_wrong( __FUNCTION__, __( 'Use `array_key_exists` to check for meta_data on WC_Shipping_Rate to get the correct result.', 'woocommerce' ), '6.0' );
		}
		return isset( $this->data[ $key ] );
	}

	/**
	 * Magic methods to support direct access to props.
	 *
	 * @since 3.2.0
	 * @param string $key Key.
	 * @return mixed
	 */
	public function __get( $key ) {
		if ( is_callable( array( $this, "get_{$key}" ) ) ) {
			return $this->{"get_{$key}"}();
		} elseif ( isset( $this->data[ $key ] ) ) {
			return $this->data[ $key ];
		} else {
			return '';
		}
	}

	/**
	 * Magic methods to support direct access to props.
	 *
	 * @since 3.2.0
	 * @param string $key   Key.
	 * @param mixed  $value Value.
	 */
	public function __set( $key, $value ) {
		if ( is_callable( array( $this, "set_{$key}" ) ) ) {
			$this->{"set_{$key}"}( $value );
		} else {
			$this->data[ $key ] = $value;
		}
	}

	/**
	 * Set ID for the rate. This is usually a combination of the method and instance IDs.
	 *
	 * @since 3.2.0
	 * @param string $id Shipping rate ID.
	 */
	public function set_id( $id ) {
		$this->data['id'] = (string) $id;
	}

	/**
	 * Set shipping method ID the rate belongs to.
	 *
	 * @since 3.2.0
	 * @param string $method_id Shipping method ID.
	 */
	public function set_method_id( $method_id ) {
		$this->data['method_id'] = (string) $method_id;
	}

	/**
	 * Set instance ID the rate belongs to.
	 *
	 * @since 3.2.0
	 * @param int $instance_id Instance ID.
	 */
	public function set_instance_id( $instance_id ) {
		$this->data['instance_id'] = absint( $instance_id );
	}

	/**
	 * Set rate label.
	 *
	 * @since 3.2.0
	 * @param string $label Shipping rate label.
	 */
	public function set_label( $label ) {
		$this->data['label'] = (string) $label;
	}

	/**
	 * Set rate cost.
	 *
	 * @todo 4.0 Prevent negative value being set. #19293
	 * @since 3.2.0
	 * @param string $cost Shipping rate cost.
	 */
	public function set_cost( $cost ) {
		$this->data['cost'] = $cost;
	}

	/**
	 * Set rate taxes.
	 *
	 * @since 3.2.0
	 * @param array $taxes List of taxes applied to shipping rate.
	 */
	public function set_taxes( $taxes ) {
		$this->data['taxes'] = ! empty( $taxes ) && is_array( $taxes ) ? $taxes : array();
	}

	/**
	 * Get ID for the rate. This is usually a combination of the method and instance IDs.
	 *
	 * @since 3.2.0
	 * @return string
	 */
	public function get_id() {
		return apply_filters( 'woocommerce_shipping_rate_id', $this->data['id'], $this );
	}

	/**
	 * Get shipping method ID the rate belongs to.
	 *
	 * @since 3.2.0
	 * @return string
	 */
	public function get_method_id() {
		return apply_filters( 'woocommerce_shipping_rate_method_id', $this->data['method_id'], $this );
	}

	/**
	 * Get instance ID the rate belongs to.
	 *
	 * @since 3.2.0
	 * @return int
	 */
	public function get_instance_id() {
		return apply_filters( 'woocommerce_shipping_rate_instance_id', $this->data['instance_id'], $this );
	}

	/**
	 * Get rate label.
	 *
	 * @return string
	 */
	public function get_label() {
		return apply_filters( 'woocommerce_shipping_rate_label', $this->data['label'], $this );
	}

	/**
	 * Get rate cost.
	 *
	 * @since 3.2.0
	 * @return string
	 */
	public function get_cost() {
		return apply_filters( 'woocommerce_shipping_rate_cost', $this->data['cost'], $this );
	}

	/**
	 * Get rate taxes.
	 *
	 * @since 3.2.0
	 * @return array
	 */
	public function get_taxes() {
		return apply_filters( 'woocommerce_shipping_rate_taxes', $this->data['taxes'], $this );
	}

	/**
	 * Get shipping tax.
	 *
	 * @return float
	 */
	public function get_shipping_tax() {
		return apply_filters( 'woocommerce_get_shipping_tax', count( $this->taxes ) > 0 && ! WC()->customer->get_is_vat_exempt() ? (float) array_sum( $this->taxes ) : 0.0, $this );
	}

	/**
	 * Add some meta data for this rate.
	 *
	 * @since 2.6.0
	 * @param string $key   Key.
	 * @param string $value Value.
	 */
	public function add_meta_data( $key, $value ) {
		$this->meta_data[ wc_clean( $key ) ] = wc_clean( $value );
	}

	/**
	 * Get all meta data for this rate.
	 *
	 * @since 2.6.0
	 * @return array
	 */
	public function get_meta_data() {
		return $this->meta_data;
	}
}

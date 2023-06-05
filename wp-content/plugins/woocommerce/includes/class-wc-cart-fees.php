<?php
/**
 * Cart fees API.
 *
 * Developers can add fees to the cart via WC()->cart->fees_api() which will reference this class.
 *
 * We suggest using the action woocommerce_cart_calculate_fees hook for adding fees.
 *
 * @package WooCommerce\Classes
 * @version 3.2.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Cart_Fees class.
 *
 * @since 3.2.0
 */
final class WC_Cart_Fees {

	/**
	 * An array of fee objects.
	 *
	 * @var object[]
	 */
	private $fees = array();

	/**
	 * Reference to cart object.
	 *
	 * @since 3.2.0
	 * @var WC_Cart
	 */
	private $cart;

	/**
	 * New fees are made out of these props.
	 *
	 * @var array
	 */
	private $default_fee_props = array(
		'id'        => '',
		'name'      => '',
		'tax_class' => '',
		'taxable'   => false,
		'amount'    => 0,
		'total'     => 0,
	);

	/**
	 * Constructor. Reference to the cart.
	 *
	 * @since 3.2.0
	 * @throws Exception If missing WC_Cart object.
	 * @param WC_Cart $cart Cart object.
	 */
	public function __construct( &$cart ) {
		if ( ! is_a( $cart, 'WC_Cart' ) ) {
			throw new Exception( 'A valid WC_Cart object is required' );
		}

		$this->cart = $cart;
	}

	/**
	 * Register methods for this object on the appropriate WordPress hooks.
	 */
	public function init() {}

	/**
	 * Add a fee. Fee IDs must be unique.
	 *
	 * @since 3.2.0
	 * @param array $args Array of fee properties.
	 * @return object Either a fee object if added, or a WP_Error if it failed.
	 */
	public function add_fee( $args = array() ) {
		$fee_props            = (object) wp_parse_args( $args, $this->default_fee_props );
		$fee_props->name      = $fee_props->name ? $fee_props->name : __( 'Fee', 'woocommerce' );
		$fee_props->tax_class = in_array( $fee_props->tax_class, array_merge( WC_Tax::get_tax_classes(), WC_Tax::get_tax_class_slugs() ), true ) ? $fee_props->tax_class : '';
		$fee_props->taxable   = wc_string_to_bool( $fee_props->taxable );
		$fee_props->amount    = wc_format_decimal( $fee_props->amount );

		if ( empty( $fee_props->id ) ) {
			$fee_props->id = $this->generate_id( $fee_props );
		}

		if ( array_key_exists( $fee_props->id, $this->fees ) ) {
			return new WP_Error( 'fee_exists', __( 'Fee has already been added.', 'woocommerce' ) );
		}

		$this->fees[ $fee_props->id ] = $fee_props;

		return $this->fees[ $fee_props->id ];
	}

	/**
	 * Get fees.
	 *
	 * @return array
	 */
	public function get_fees() {
		uasort( $this->fees, array( $this, 'sort_fees_callback' ) );

		return $this->fees;
	}

	/**
	 * Set fees.
	 *
	 * @param object[] $raw_fees Array of fees.
	 */
	public function set_fees( $raw_fees = array() ) {
		$this->fees = array();

		foreach ( $raw_fees as $raw_fee ) {
			$this->add_fee( $raw_fee );
		}
	}

	/**
	 * Remove all fees.
	 *
	 * @since 3.2.0
	 */
	public function remove_all_fees() {
		$this->set_fees();
	}

	/**
	 * Sort fees by amount.
	 *
	 * @param stdClass $a Fee object.
	 * @param stdClass $b Fee object.
	 * @return int
	 */
	protected function sort_fees_callback( $a, $b ) {
		/**
		 * Filter sort fees callback.
		 *
		 * @since 3.8.0
		 * @param int Sort order, -1 or 1.
		 * @param stdClass $a Fee object.
		 * @param stdClass $b Fee object.
		 */
		return apply_filters( 'woocommerce_sort_fees_callback', $a->amount > $b->amount ? -1 : 1, $a, $b );
	}

	/**
	 * Generate a unique ID for the fee being added.
	 *
	 * @param string $fee Fee object.
	 * @return string fee key.
	 */
	private function generate_id( $fee ) {
		return sanitize_title( $fee->name );
	}
}

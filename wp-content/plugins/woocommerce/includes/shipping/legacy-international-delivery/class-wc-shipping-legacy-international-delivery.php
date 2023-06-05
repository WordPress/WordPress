<?php
/**
 * Class WC_Shipping_Legacy_International_Delivery file.
 *
 * @package WooCommerce\Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * International Delivery - Based on the Flat Rate Shipping Method.
 *
 * This class is here for backwards compatibility for methods existing before zones existed.
 *
 * @deprecated  2.6.0
 * @version     2.4.0
 * @package     WooCommerce\Classes\Shipping
 */
class WC_Shipping_Legacy_International_Delivery extends WC_Shipping_Legacy_Flat_Rate {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id           = 'legacy_international_delivery';
		$this->method_title = __( 'International flat rate (legacy)', 'woocommerce' );
		/* translators: %s: Admin shipping settings URL */
		$this->method_description = '<strong>' . sprintf( __( 'This method is deprecated in 2.6.0 and will be removed in future versions - we recommend disabling it and instead setting up a new rate within your <a href="%s">Shipping zones</a>.', 'woocommerce' ), admin_url( 'admin.php?page=wc-settings&tab=shipping' ) ) . '</strong>';
		$this->init();

		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
	}

	/**
	 * Return the name of the option in the WP DB.
	 *
	 * @since 2.6.0
	 * @return string
	 */
	public function get_option_key() {
		return $this->plugin_id . 'international_delivery_settings';
	}

	/**
	 * Initialise settings form fields.
	 */
	public function init_form_fields() {
		parent::init_form_fields();
		$this->form_fields['availability'] = array(
			'title'       => __( 'Availability', 'woocommerce' ),
			'type'        => 'select',
			'class'       => 'wc-enhanced-select',
			'description' => '',
			'default'     => 'including',
			'options'     => array(
				'including' => __( 'Selected countries', 'woocommerce' ),
				'excluding' => __( 'Excluding selected countries', 'woocommerce' ),
			),
		);
	}

	/**
	 * Check if package is available.
	 *
	 * @param array $package Package information.
	 * @return bool
	 */
	public function is_available( $package ) {
		if ( 'no' === $this->enabled ) {
			return false;
		}
		if ( 'including' === $this->availability ) {
			if ( is_array( $this->countries ) && ! in_array( $package['destination']['country'], $this->countries, true ) ) {
				return false;
			}
		} else {
			if ( is_array( $this->countries ) && ( in_array( $package['destination']['country'], $this->countries, true ) || ! $package['destination']['country'] ) ) {
				return false;
			}
		}
		return apply_filters( 'woocommerce_shipping_' . $this->id . '_is_available', true, $package, $this );
	}
}

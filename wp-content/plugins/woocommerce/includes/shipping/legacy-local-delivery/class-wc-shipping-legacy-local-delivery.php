<?php
/**
 * Class WC_Shipping_Legacy_Local_Delivery file.
 *
 * @package WooCommerce\Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Local Delivery Shipping Method.
 *
 * This class is here for backwards compatibility for methods existing before zones existed.
 *
 * @deprecated  2.6.0
 * @version     2.3.0
 * @package     WooCommerce\Classes\Shipping
 */
class WC_Shipping_Legacy_Local_Delivery extends WC_Shipping_Local_Pickup {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id           = 'legacy_local_delivery';
		$this->method_title = __( 'Local delivery (legacy)', 'woocommerce' );
		/* translators: %s: Admin shipping settings URL */
		$this->method_description = '<strong>' . sprintf( __( 'This method is deprecated in 2.6.0 and will be removed in future versions - we recommend disabling it and instead setting up a new rate within your <a href="%s">Shipping zones</a>.', 'woocommerce' ), admin_url( 'admin.php?page=wc-settings&tab=shipping' ) ) . '</strong>';
		$this->init();
	}

	/**
	 * Process and redirect if disabled.
	 */
	public function process_admin_options() {
		parent::process_admin_options();

		if ( 'no' === $this->settings['enabled'] ) {
			wp_redirect( admin_url( 'admin.php?page=wc-settings&tab=shipping&section=options' ) );
			exit;
		}
	}

	/**
	 * Return the name of the option in the WP DB.
	 *
	 * @since 2.6.0
	 * @return string
	 */
	public function get_option_key() {
		return $this->plugin_id . 'local_delivery_settings';
	}

	/**
	 * Init function.
	 */
	public function init() {

		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();

		// Define user set variables.
		$this->title        = $this->get_option( 'title' );
		$this->type         = $this->get_option( 'type' );
		$this->fee          = $this->get_option( 'fee' );
		$this->type         = $this->get_option( 'type' );
		$this->codes        = $this->get_option( 'codes' );
		$this->availability = $this->get_option( 'availability' );
		$this->countries    = $this->get_option( 'countries' );

		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
	}

	/**
	 * Calculate_shipping function.
	 *
	 * @param array $package (default: array()).
	 */
	public function calculate_shipping( $package = array() ) {
		$shipping_total = 0;

		switch ( $this->type ) {
			case 'fixed':
				$shipping_total = $this->fee;
				break;
			case 'percent':
				$shipping_total = $package['contents_cost'] * ( $this->fee / 100 );
				break;
			case 'product':
				foreach ( $package['contents'] as $item_id => $values ) {
					if ( $values['quantity'] > 0 && $values['data']->needs_shipping() ) {
						$shipping_total += $this->fee * $values['quantity'];
					}
				}
				break;
		}

		$rate = array(
			'id'      => $this->id,
			'label'   => $this->title,
			'cost'    => $shipping_total,
			'package' => $package,
		);

		$this->add_rate( $rate );
	}

	/**
	 * Init form fields.
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled'      => array(
				'title'   => __( 'Enable', 'woocommerce' ),
				'type'    => 'checkbox',
				'label'   => __( 'Once disabled, this legacy method will no longer be available.', 'woocommerce' ),
				'default' => 'no',
			),
			'title'        => array(
				'title'       => __( 'Title', 'woocommerce' ),
				'type'        => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
				'default'     => __( 'Local delivery', 'woocommerce' ),
				'desc_tip'    => true,
			),
			'type'         => array(
				'title'       => __( 'Fee type', 'woocommerce' ),
				'type'        => 'select',
				'class'       => 'wc-enhanced-select',
				'description' => __( 'How to calculate delivery charges', 'woocommerce' ),
				'default'     => 'fixed',
				'options'     => array(
					'fixed'   => __( 'Fixed amount', 'woocommerce' ),
					'percent' => __( 'Percentage of cart total', 'woocommerce' ),
					'product' => __( 'Fixed amount per product', 'woocommerce' ),
				),
				'desc_tip'    => true,
			),
			'fee'          => array(
				'title'       => __( 'Delivery fee', 'woocommerce' ),
				'type'        => 'price',
				'description' => __( 'What fee do you want to charge for local delivery, disregarded if you choose free. Leave blank to disable.', 'woocommerce' ),
				'default'     => '',
				'desc_tip'    => true,
				'placeholder' => wc_format_localized_price( 0 ),
			),
			'codes'        => array(
				'title'       => __( 'Allowed ZIP/post codes', 'woocommerce' ),
				'type'        => 'text',
				'desc_tip'    => __( 'What ZIP/post codes are available for local delivery?', 'woocommerce' ),
				'default'     => '',
				'description' => __( 'Separate codes with a comma. Accepts wildcards, e.g. <code>P*</code> will match a postcode of PE30. Also accepts a pattern, e.g. <code>NG1___</code> would match NG1 1AA but not NG10 1AA', 'woocommerce' ),
				'placeholder' => 'e.g. 12345, 56789',
			),
			'availability' => array(
				'title'   => __( 'Method availability', 'woocommerce' ),
				'type'    => 'select',
				'default' => 'all',
				'class'   => 'availability wc-enhanced-select',
				'options' => array(
					'all'      => __( 'All allowed countries', 'woocommerce' ),
					'specific' => __( 'Specific Countries', 'woocommerce' ),
				),
			),
			'countries'    => array(
				'title'             => __( 'Specific countries', 'woocommerce' ),
				'type'              => 'multiselect',
				'class'             => 'wc-enhanced-select',
				'css'               => 'width: 400px;',
				'default'           => '',
				'options'           => WC()->countries->get_shipping_countries(),
				'custom_attributes' => array(
					'data-placeholder' => __( 'Select some countries', 'woocommerce' ),
				),
			),
		);
	}
}

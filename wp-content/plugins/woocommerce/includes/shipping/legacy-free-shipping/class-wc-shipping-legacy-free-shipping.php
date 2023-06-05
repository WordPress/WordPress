<?php
/**
 * Class WC_Shipping_Legacy_Free_Shipping file.
 *
 * @package WooCommerce\Shipping
 */

use Automattic\WooCommerce\Utilities\NumberUtil;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Free Shipping Method.
 *
 * This class is here for backwards compatibility for methods existing before zones existed.
 *
 * @deprecated  2.6.0
 * @version 2.4.0
 * @package WooCommerce\Classes\Shipping
 */
class WC_Shipping_Legacy_Free_Shipping extends WC_Shipping_Method {

	/**
	 * Min amount to be valid.
	 *
	 * @var float
	 */
	public $min_amount;

	/**
	 * Requires option.
	 *
	 * @var string
	 */
	public $requires;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id           = 'legacy_free_shipping';
		$this->method_title = __( 'Free shipping (legacy)', 'woocommerce' );
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
		return $this->plugin_id . 'free_shipping_settings';
	}

	/**
	 * Init function.
	 */
	public function init() {

		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();

		// Define user set variables.
		$this->enabled      = $this->get_option( 'enabled' );
		$this->title        = $this->get_option( 'title' );
		$this->min_amount   = $this->get_option( 'min_amount', 0 );
		$this->availability = $this->get_option( 'availability' );
		$this->countries    = $this->get_option( 'countries' );
		$this->requires     = $this->get_option( 'requires' );

		// Actions.
		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
	}

	/**
	 * Initialise Gateway Settings Form Fields.
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled'      => array(
				'title'   => __( 'Enable/Disable', 'woocommerce' ),
				'type'    => 'checkbox',
				'label'   => __( 'Once disabled, this legacy method will no longer be available.', 'woocommerce' ),
				'default' => 'no',
			),
			'title'        => array(
				'title'       => __( 'Method title', 'woocommerce' ),
				'type'        => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
				'default'     => __( 'Free Shipping', 'woocommerce' ),
				'desc_tip'    => true,
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
			'requires'     => array(
				'title'   => __( 'Free shipping requires...', 'woocommerce' ),
				'type'    => 'select',
				'class'   => 'wc-enhanced-select',
				'default' => '',
				'options' => array(
					''           => __( 'N/A', 'woocommerce' ),
					'coupon'     => __( 'A valid free shipping coupon', 'woocommerce' ),
					'min_amount' => __( 'A minimum order amount', 'woocommerce' ),
					'either'     => __( 'A minimum order amount OR a coupon', 'woocommerce' ),
					'both'       => __( 'A minimum order amount AND a coupon', 'woocommerce' ),
				),
			),
			'min_amount'   => array(
				'title'       => __( 'Minimum order amount', 'woocommerce' ),
				'type'        => 'price',
				'placeholder' => wc_format_localized_price( 0 ),
				'description' => __( 'Users will need to spend this amount to get free shipping (if enabled above).', 'woocommerce' ),
				'default'     => '0',
				'desc_tip'    => true,
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

		if ( 'specific' === $this->availability ) {
			$ship_to_countries = $this->countries;
		} else {
			$ship_to_countries = array_keys( WC()->countries->get_shipping_countries() );
		}

		if ( is_array( $ship_to_countries ) && ! in_array( $package['destination']['country'], $ship_to_countries, true ) ) {
			return false;
		}

		// Enabled logic.
		$is_available       = false;
		$has_coupon         = false;
		$has_met_min_amount = false;

		if ( in_array( $this->requires, array( 'coupon', 'either', 'both' ), true ) ) {
			$coupons = WC()->cart->get_coupons();

			if ( $coupons ) {
				foreach ( $coupons as $code => $coupon ) {
					if ( $coupon->is_valid() && $coupon->get_free_shipping() ) {
						$has_coupon = true;
					}
				}
			}
		}

		if ( in_array( $this->requires, array( 'min_amount', 'either', 'both' ), true ) ) {
			$total = WC()->cart->get_displayed_subtotal();

			if ( WC()->cart->display_prices_including_tax() ) {
				$total = NumberUtil::round( $total - ( WC()->cart->get_discount_total() + WC()->cart->get_discount_tax() ), wc_get_price_decimals() );
			} else {
				$total = NumberUtil::round( $total - WC()->cart->get_discount_total(), wc_get_price_decimals() );
			}

			if ( $total >= $this->min_amount ) {
				$has_met_min_amount = true;
			}
		}

		switch ( $this->requires ) {
			case 'min_amount':
				if ( $has_met_min_amount ) {
					$is_available = true;
				}
				break;
			case 'coupon':
				if ( $has_coupon ) {
					$is_available = true;
				}
				break;
			case 'both':
				if ( $has_met_min_amount && $has_coupon ) {
					$is_available = true;
				}
				break;
			case 'either':
				if ( $has_met_min_amount || $has_coupon ) {
					$is_available = true;
				}
				break;
			default:
				$is_available = true;
				break;
		}

		return apply_filters( 'woocommerce_shipping_' . $this->id . '_is_available', $is_available, $package, $this );
	}

	/**
	 * Calculate shipping.
	 *
	 * @param array $package Package information.
	 */
	public function calculate_shipping( $package = array() ) {
		$args = array(
			'id'      => $this->id,
			'label'   => $this->title,
			'cost'    => 0,
			'taxes'   => false,
			'package' => $package,
		);
		$this->add_rate( $args );
	}
}

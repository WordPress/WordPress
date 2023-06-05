<?php
/**
 * Class WC_Shipping_Legacy_Local_Pickup file.
 *
 * @package WooCommerce\Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Local Pickup Shipping Method.
 *
 * This class is here for backwards compatibility for methods existing before zones existed.
 *
 * @deprecated  2.6.0
 * @version     2.3.0
 * @package     WooCommerce\Classes\Shipping
 */
class WC_Shipping_Legacy_Local_Pickup extends WC_Shipping_Method {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id           = 'legacy_local_pickup';
		$this->method_title = __( 'Local pickup (legacy)', 'woocommerce' );
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
		return $this->plugin_id . 'local_pickup_settings';
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
		$this->codes        = $this->get_option( 'codes' );
		$this->availability = $this->get_option( 'availability' );
		$this->countries    = $this->get_option( 'countries' );

		// Actions.
		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
	}

	/**
	 * Calculate shipping.
	 *
	 * @param array $package Package information.
	 */
	public function calculate_shipping( $package = array() ) {
		$rate = array(
			'id'      => $this->id,
			'label'   => $this->title,
			'package' => $package,
		);
		$this->add_rate( $rate );
	}

	/**
	 * Initialize form fields.
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
				'default'     => __( 'Local pickup', 'woocommerce' ),
				'desc_tip'    => true,
			),
			'codes'        => array(
				'title'       => __( 'Allowed ZIP/post codes', 'woocommerce' ),
				'type'        => 'text',
				'desc_tip'    => __( 'What ZIP/post codes are available for local pickup?', 'woocommerce' ),
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
					'specific' => __( 'Specific countries', 'woocommerce' ),
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

	/**
	 * Get postcodes for this method.
	 *
	 * @return array
	 */
	public function get_valid_postcodes() {
		$codes = array();

		if ( '' !== $this->codes ) {
			foreach ( explode( ',', $this->codes ) as $code ) {
				$codes[] = strtoupper( trim( $code ) );
			}
		}

		return $codes;
	}

	/**
	 * See if a given postcode matches valid postcodes.
	 *
	 * @param  string $postcode Postcode to check.
	 * @param  string $country code Code of the country to check postcode against.
	 * @return boolean
	 */
	public function is_valid_postcode( $postcode, $country ) {
		$codes              = $this->get_valid_postcodes();
		$postcode           = $this->clean( $postcode );
		$formatted_postcode = wc_format_postcode( $postcode, $country );

		if ( in_array( $postcode, $codes, true ) || in_array( $formatted_postcode, $codes, true ) ) {
			return true;
		}

		// Pattern matching.
		foreach ( $codes as $c ) {
			$pattern = '/^' . str_replace( '_', '[0-9a-zA-Z]', preg_quote( $c ) ) . '$/i';
			if ( preg_match( $pattern, $postcode ) ) {
				return true;
			}
		}

		// Wildcard search.
		$wildcard_postcode = $formatted_postcode . '*';
		$postcode_length   = strlen( $formatted_postcode );

		for ( $i = 0; $i < $postcode_length; $i++ ) {
			if ( in_array( $wildcard_postcode, $codes, true ) ) {
				return true;
			}
			$wildcard_postcode = substr( $wildcard_postcode, 0, -2 ) . '*';
		}

		return false;
	}

	/**
	 * See if the method is available.
	 *
	 * @param array $package Package information.
	 * @return bool
	 */
	public function is_available( $package ) {
		$is_available = 'yes' === $this->enabled;

		if ( $is_available && $this->get_valid_postcodes() ) {
			$is_available = $this->is_valid_postcode( $package['destination']['postcode'], $package['destination']['country'] );
		}

		if ( $is_available ) {
			if ( 'specific' === $this->availability ) {
				$ship_to_countries = $this->countries;
			} else {
				$ship_to_countries = array_keys( WC()->countries->get_shipping_countries() );
			}
			if ( is_array( $ship_to_countries ) && ! in_array( $package['destination']['country'], $ship_to_countries, true ) ) {
				$is_available = false;
			}
		}

		return apply_filters( 'woocommerce_shipping_' . $this->id . '_is_available', $is_available, $package, $this );
	}

	/**
	 * Clean function.
	 *
	 * @access public
	 * @param mixed $code Code.
	 * @return string
	 */
	public function clean( $code ) {
		return str_replace( '-', '', sanitize_title( $code ) ) . ( strstr( $code, '*' ) ? '*' : '' );
	}
}

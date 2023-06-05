<?php
/**
 * WooCommerce Integrations class
 *
 * Loads Integrations into WooCommerce.
 *
 * @version 3.9.0
 * @package WooCommerce\Classes\Integrations
 */

defined( 'ABSPATH' ) || exit;

/**
 * Integrations class.
 */
class WC_Integrations {

	/**
	 * Array of integrations.
	 *
	 * @var array
	 */
	public $integrations = array();

	/**
	 * Initialize integrations.
	 */
	public function __construct() {

		do_action( 'woocommerce_integrations_init' );

		$load_integrations = array(
			'WC_Integration_MaxMind_Geolocation',
		);

		$load_integrations = apply_filters( 'woocommerce_integrations', $load_integrations );

		// Load integration classes.
		foreach ( $load_integrations as $integration ) {

			$load_integration = new $integration();

			$this->integrations[ $load_integration->id ] = $load_integration;
		}
	}

	/**
	 * Return loaded integrations.
	 *
	 * @return array
	 */
	public function get_integrations() {
		return $this->integrations;
	}

	/**
	 * Return a desired integration.
	 *
	 * @since 3.9.0
	 * @param string $id The id of the integration to get.
	 * @return mixed|null The integration if one is found, otherwise null.
	 */
	public function get_integration( $id ) {
		if ( isset( $this->integrations[ $id ] ) ) {
			return $this->integrations[ $id ];
		}

		return null;
	}
}

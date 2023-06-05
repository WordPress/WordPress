<?php
/**
 * WooCommerce Admin Setup Wizard Tracking
 *
 * @package WooCommerce\Tracks
 *
 * @deprecated 4.6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * This class adds actions to track usage of the WooCommerce Onboarding Wizard.
 */
class WC_Admin_Setup_Wizard_Tracking {
	/**
	 * Steps for the setup wizard
	 *
	 * @var array
	 */
	private $steps = array();

	/**
	 * Init tracking.
	 *
	 * @deprecated 4.6.0
	 */
	public function init() {
		_deprecated_function( __CLASS__ . '::' . __FUNCTION__, '4.6.0', __( 'Onboarding is maintained in WooCommerce Admin.', 'woocommerce' ) );
	}

	/**
	 * Get the name of the current step.
	 *
	 * @deprecated 4.6.0
	 * @return string
	 */
	public function get_current_step() {
		_deprecated_function( __CLASS__ . '::' . __FUNCTION__, '4.6.0', __( 'Onboarding is maintained in WooCommerce Admin.', 'woocommerce' ) );
		return isset( $_GET['step'] ) ? sanitize_key( $_GET['step'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}

	/**
	 * Add footer scripts to OBW via woocommerce_setup_footer
	 *
	 * @deprecated 4.6.0
	 */
	public function add_footer_scripts() {
		_deprecated_function( __CLASS__ . '::' . __FUNCTION__, '4.6.0', __( 'Onboarding is maintained in WooCommerce Admin.', 'woocommerce' ) );
	}

	/**
	 * Dequeue unwanted scripts from OBW footer.
	 *
	 * @deprecated 4.6.0
	 */
	public function dequeue_non_allowed_scripts() {
		_deprecated_function( __CLASS__ . '::' . __FUNCTION__, '4.6.0', __( 'Onboarding is maintained in WooCommerce Admin.', 'woocommerce' ) );
		global $wp_scripts;
		$allowed = array( 'woo-tracks' );

		foreach ( $wp_scripts->queue as $script ) {
			if ( in_array( $script, $allowed, true ) ) {
				continue;
			}
			wp_dequeue_script( $script );
		}
	}

	/**
	 * Track when tracking is opted into and OBW has started.
	 *
	 * @param string $option Option name.
	 * @param string $value  Option value.
	 *
	 * @deprecated 4.6.0
	 */
	public function track_start( $option, $value ) {
		_deprecated_function( __CLASS__ . '::' . __FUNCTION__, '4.6.0', __( 'Onboarding is maintained in WooCommerce Admin.', 'woocommerce' ) );
	}

	/**
	 * Track the marketing form on submit.
	 *
	 * @deprecated 4.6.0
	 */
	public function track_ready_next_steps() {
		_deprecated_function( __CLASS__ . '::' . __FUNCTION__, '4.6.0', __( 'Onboarding is maintained in WooCommerce Admin.', 'woocommerce' ) );
	}

	/**
	 * Track various events when a step is saved.
	 *
	 * @deprecated 4.6.0
	 */
	public function add_step_save_events() {
		_deprecated_function( __CLASS__ . '::' . __FUNCTION__, '4.6.0', __( 'Onboarding is maintained in WooCommerce Admin.', 'woocommerce' ) );
	}

	/**
	 * Track store setup and store properties on save.
	 *
	 * @deprecated 4.6.0
	 */
	public function track_store_setup() {
		_deprecated_function( __CLASS__ . '::' . __FUNCTION__, '4.6.0', __( 'Onboarding is maintained in WooCommerce Admin.', 'woocommerce' ) );
	}

	/**
	 * Track payment gateways selected.
	 *
	 * @deprecated 4.6.0
	 */
	public function track_payments() {
		_deprecated_function( __CLASS__ . '::' . __FUNCTION__, '4.6.0', __( 'Onboarding is maintained in WooCommerce Admin.', 'woocommerce' ) );
	}

	/**
	 * Track shipping units and whether or not labels are set.
	 *
	 * @deprecated 4.6.0
	 */
	public function track_shipping() {
		_deprecated_function( __CLASS__ . '::' . __FUNCTION__, '4.6.0', __( 'Onboarding is maintained in WooCommerce Admin.', 'woocommerce' ) );
	}

	/**
	 * Track recommended plugins selected for install.
	 *
	 * @deprecated 4.6.0
	 */
	public function track_recommended() {
		_deprecated_function( __CLASS__ . '::' . __FUNCTION__, '4.6.0', __( 'Onboarding is maintained in WooCommerce Admin.', 'woocommerce' ) );
	}

	/**
	 * Tracks when Jetpack is activated through the OBW.
	 *
	 * @deprecated 4.6.0
	 */
	public function track_jetpack_activate() {
		_deprecated_function( __CLASS__ . '::' . __FUNCTION__, '4.6.0', __( 'Onboarding is maintained in WooCommerce Admin.', 'woocommerce' ) );
	}

	/**
	 * Tracks when last next_steps screen is viewed in the OBW.
	 *
	 * @deprecated 4.6.0
	 */
	public function track_next_steps() {
		_deprecated_function( __CLASS__ . '::' . __FUNCTION__, '4.6.0', __( 'Onboarding is maintained in WooCommerce Admin.', 'woocommerce' ) );
	}

	/**
	 * Track skipped steps.
	 *
	 * @deprecated 4.6.0
	 */
	public function track_skip_step() {
		_deprecated_function( __CLASS__ . '::' . __FUNCTION__, '4.6.0', __( 'Onboarding is maintained in WooCommerce Admin.', 'woocommerce' ) );
	}

	/**
	 * Set the OBW steps inside this class instance.
	 *
	 * @param array $steps Array of OBW steps.
	 *
	 * @deprecated 4.6.0
	 */
	public function set_obw_steps( $steps ) {
		_deprecated_function( __CLASS__ . '::' . __FUNCTION__, '4.6.0', __( 'Onboarding is maintained in WooCommerce Admin.', 'woocommerce' ) );
		$this->steps = $steps;

		return $steps;
	}
}

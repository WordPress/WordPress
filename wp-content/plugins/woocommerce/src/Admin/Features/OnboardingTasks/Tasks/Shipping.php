<?php

namespace Automattic\WooCommerce\Admin\Features\OnboardingTasks\Tasks;

use Automattic\WooCommerce\Admin\Features\Features;
use Automattic\WooCommerce\Internal\Admin\Onboarding\OnboardingProfile;
use Automattic\WooCommerce\Admin\Features\OnboardingTasks\Task;
use WC_Data_Store;

/**
 * Shipping Task
 */
class Shipping extends Task {

	const ZONE_COUNT_TRANSIENT_NAME = 'woocommerce_shipping_task_zone_count_transient';

	/**
	 * Constructor
	 *
	 * @param TaskList $task_list Parent task list.
	 */
	public function __construct( $task_list = null ) {
		parent::__construct( $task_list );
		// wp_ajax_woocommerce_shipping_zone_methods_save_changes
		// and wp_ajax_woocommerce_shipping_zones_save_changes get fired
		// when a new zone is added or an existing one has been changed.
		add_action( 'wp_ajax_woocommerce_shipping_zones_save_changes', array( __CLASS__, 'delete_zone_count_transient' ), 9 );
		add_action( 'wp_ajax_woocommerce_shipping_zone_methods_save_changes', array( __CLASS__, 'delete_zone_count_transient' ), 9 );
		add_action( 'woocommerce_shipping_zone_method_added', array( __CLASS__, 'delete_zone_count_transient' ), 9 );
		add_action( 'woocommerce_after_shipping_zone_object_save', array( __CLASS__, 'delete_zone_count_transient' ), 9 );
	}

	/**
	 * ID.
	 *
	 * @return string
	 */
	public function get_id() {
		return 'shipping';
	}

	/**
	 * Title.
	 *
	 * @return string
	 */
	public function get_title() {
		if ( true === $this->get_parent_option( 'use_completed_title' ) ) {
			if ( $this->is_complete() ) {
				return __( 'You added shipping costs', 'woocommerce' );
			}
			return __( 'Add shipping costs', 'woocommerce' );
		}
		return __( 'Set up shipping', 'woocommerce' );
	}

	/**
	 * Content.
	 *
	 * @return string
	 */
	public function get_content() {
		return __(
			"Set your store location and where you'll ship to.",
			'woocommerce'
		);
	}

	/**
	 * Time.
	 *
	 * @return string
	 */
	public function get_time() {
		return __( '1 minute', 'woocommerce' );
	}

	/**
	 * Task completion.
	 *
	 * @return bool
	 */
	public function is_complete() {
		return self::has_shipping_zones();
	}

	/**
	 * Task visibility.
	 *
	 * @return bool
	 */
	public function can_view() {
		if ( Features::is_enabled( 'shipping-smart-defaults' ) ) {
			if ( 'yes' === get_option( 'woocommerce_admin_created_default_shipping_zones' ) ) {
				// If the user has already created a default shipping zone, we don't need to show the task.
				return false;
			}

			/**
			 * Do not display the task when:
			 * - The store sells digital products only
			 * Display the task when:
			 * - We don't know where the store's located
			 * - The store is located in the UK, Australia or Canada
			*/

			if ( self::is_selling_digital_type_only() ) {
				return false;
			}

			$default_store_country = wc_format_country_state_string( get_option( 'woocommerce_default_country', '' ) )['country'];

			// Check if a store address is set so that we don't default to WooCommerce's default country US.
			// Similar logic: https://github.com/woocommerce/woocommerce/blob/059d542394b48468587f252dcb6941c6425cd8d3/plugins/woocommerce-admin/client/profile-wizard/steps/store-details/index.js#L511-L516.
			$store_country = '';
			if ( ! empty( get_option( 'woocommerce_store_address', '' ) ) || 'US' !== $default_store_country ) {
				$store_country = $default_store_country;
			}

			// Unknown country.
			if ( empty( $store_country ) ) {
				return true;
			}

			return in_array( $store_country, array( 'CA', 'AU', 'GB', 'ES', 'IT', 'DE', 'FR', 'MX', 'CO', 'CL', 'AR', 'PE', 'BR', 'UY', 'GT', 'NL', 'AT', 'BE' ), true );
		}

		return self::has_physical_products();
	}

	/**
	 * Action URL.
	 *
	 * @return string
	 */
	public function get_action_url() {
		return self::has_shipping_zones()
			? admin_url( 'admin.php?page=wc-settings&tab=shipping' )
			: null;
	}

	/**
	 * Check if the store has any shipping zones.
	 *
	 * @return bool
	 */
	public static function has_shipping_zones() {
		$zone_count = get_transient( self::ZONE_COUNT_TRANSIENT_NAME );
		if ( false !== $zone_count ) {
			return (int) $zone_count > 0;
		}

		$zone_count = count( WC_Data_Store::load( 'shipping-zone' )->get_zones() );
		set_transient( self::ZONE_COUNT_TRANSIENT_NAME, $zone_count );

		return $zone_count > 0;
	}

	/**
	 * Check if the store has physical products.
	 *
	 * @return bool
	 */
	public static function has_physical_products() {
		$profiler_data = get_option( OnboardingProfile::DATA_OPTION, array() );
		$product_types = isset( $profiler_data['product_types'] ) ? $profiler_data['product_types'] : array();

		return in_array( 'physical', $product_types, true );
	}

	/**
	 * Delete the zone count transient used in has_shipping_zones() method
	 * to refresh the cache.
	 */
	public static function delete_zone_count_transient() {
		delete_transient( self::ZONE_COUNT_TRANSIENT_NAME );
	}

	/**
	 * Check if the store sells digital products only.
	 *
	 * @return bool
	 */
	private static function is_selling_digital_type_only() {
		$profiler_data = get_option( OnboardingProfile::DATA_OPTION, array() );
		$product_types = isset( $profiler_data['product_types'] ) ? $profiler_data['product_types'] : array();

		return array( 'downloads' ) === $product_types;
	}
}

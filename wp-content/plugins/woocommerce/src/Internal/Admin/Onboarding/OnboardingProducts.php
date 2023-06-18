<?php
/**
 * WooCommerce Onboarding Products
 */

namespace Automattic\WooCommerce\Internal\Admin\Onboarding;

use Automattic\WooCommerce\Admin\Features\Features;
use Automattic\WooCommerce\Internal\Admin\Onboarding\OnboardingProfile;
use Automattic\WooCommerce\Admin\Loader;
use Automattic\WooCommerce\Admin\PluginsHelper;

/**
 * Class for handling product types and data around product types.
 */
class OnboardingProducts {

	/**
	 * Name of product data transient.
	 *
	 * @var string
	 */
	const PRODUCT_DATA_TRANSIENT = 'wc_onboarding_product_data';

	/**
	 * Get a list of allowed product types for the onboarding wizard.
	 *
	 * @return array
	 */
	public static function get_allowed_product_types() {
		$products         = array(
			'physical'        => array(
				'label'   => __( 'Physical products', 'woocommerce' ),
				'default' => true,
			),
			'downloads'       => array(
				'label' => __( 'Downloads', 'woocommerce' ),
			),
			'subscriptions'   => array(
				'label' => __( 'Subscriptions', 'woocommerce' ),
			),
			'memberships'     => array(
				'label'   => __( 'Memberships', 'woocommerce' ),
				'product' => 958589,
			),
			'bookings'        => array(
				'label'   => __( 'Bookings', 'woocommerce' ),
				'product' => 390890,
			),
			'product-bundles' => array(
				'label'   => __( 'Bundles', 'woocommerce' ),
				'product' => 18716,
			),
			'product-add-ons' => array(
				'label'   => __( 'Customizable products', 'woocommerce' ),
				'product' => 18618,
			),
		);
		$base_location    = wc_get_base_location();
		$has_cbd_industry = false;
		if ( 'US' === $base_location['country'] ) {
			$profile = get_option( OnboardingProfile::DATA_OPTION, array() );
			if ( ! empty( $profile['industry'] ) ) {
				$has_cbd_industry = in_array( 'cbd-other-hemp-derived-products', array_column( $profile['industry'], 'slug' ), true );
			}
		}
		if ( ! Features::is_enabled( 'subscriptions' ) || 'US' !== $base_location['country'] || $has_cbd_industry ) {
			$products['subscriptions']['product'] = 27147;
		}

		return apply_filters( 'woocommerce_admin_onboarding_product_types', $products );
	}

	/**
	 * Get dynamic product data from API.
	 *
	 * @param array $product_types Array of product types.
	 * @return array
	 */
	public static function get_product_data( $product_types ) {
		$locale = get_user_locale();
		// Transient value is an array of product data keyed by locale.
		$transient_value      = get_transient( self::PRODUCT_DATA_TRANSIENT );
		$transient_value      = is_array( $transient_value ) ? $transient_value : array();
		$woocommerce_products = $transient_value[ $locale ] ?? false;

		if ( false === $woocommerce_products ) {
			$woocommerce_products = wp_remote_get(
				add_query_arg(
					array(
						'locale' => $locale,
					),
					'https://woocommerce.com/wp-json/wccom-extensions/1.0/search'
				),
				array(
					'user-agent' => 'WooCommerce/' . WC()->version . '; ' . get_bloginfo( 'url' ),
				)
			);
			if ( is_wp_error( $woocommerce_products ) ) {
				return $product_types;
			}
			$transient_value[ $locale ] = $woocommerce_products;
			set_transient( self::PRODUCT_DATA_TRANSIENT, $transient_value, DAY_IN_SECONDS );
		}

		$data         = json_decode( $woocommerce_products['body'] );
		$products     = array();
		$product_data = array();

		// Map product data by ID.
		if ( isset( $data ) && isset( $data->products ) ) {
			foreach ( $data->products as $product_datum ) {
				if ( isset( $product_datum->id ) ) {
					$products[ $product_datum->id ] = $product_datum;
				}
			}
		}

		// Loop over product types and append data.
		foreach ( $product_types as $key => $product_type ) {
			$product_data[ $key ] = $product_types[ $key ];

			if ( isset( $product_type['product'] ) && isset( $products[ $product_type['product'] ] ) ) {
				$price        = html_entity_decode( $products[ $product_type['product'] ]->price );
				$yearly_price = (float) str_replace( '$', '', $price );

				$product_data[ $key ]['yearly_price'] = $yearly_price;
				$product_data[ $key ]['description']  = $products[ $product_type['product'] ]->excerpt;
				$product_data[ $key ]['more_url']     = $products[ $product_type['product'] ]->link;
				$product_data[ $key ]['slug']         = strtolower( preg_replace( '~[^\pL\d]+~u', '-', $products[ $product_type['product'] ]->slug ) );
			}
		}

		return $product_data;
	}

	/**
	 * Get the allowed product types with the polled data.
	 *
	 * @return array
	 */
	public static function get_product_types_with_data() {
		return self::get_product_data( self::get_allowed_product_types() );
	}

	/**
	 * Get relevant purchaseable products for the site.
	 *
	 * @return array
	 */
	public static function get_relevant_products() {
		$profiler_data = get_option( OnboardingProfile::DATA_OPTION, array() );
		$installed     = PluginsHelper::get_installed_plugin_slugs();
		$product_types = isset( $profiler_data['product_types'] ) ? $profiler_data['product_types'] : array();
		$product_data  = self::get_product_types_with_data();
		$purchaseable  = array();
		$remaining     = array();
		foreach ( $product_types as $type ) {
			if ( ! isset( $product_data[ $type ]['slug'] ) ) {
				continue;
			}

			$purchaseable[] = $product_data[ $type ];

			if ( ! in_array( $product_data[ $type ]['slug'], $installed, true ) ) {
				$remaining[] = $product_data[ $type ]['label'];
			}
		}

		return array(
			'purchaseable' => $purchaseable,
			'remaining'    => $remaining,
		);
	}
}

<?php

namespace Automattic\WooCommerce\Admin\Features\ShippingPartnerSuggestions;

/**
 * Default Shipping Partners
 */
class DefaultShippingPartners {

	/**
	 * Get default specs.
	 *
	 * @return array Default specs.
	 */
	public static function get_all() {
		$asset_base_url         = WC()->plugin_url() . '/assets/images/shipping_partners/';
		$column_layout_features = array(
			array(
				'icon'        => $asset_base_url . 'timer.svg',
				'title'       => __( 'Save time', 'woocommerce' ),
				'description' => __(
					'Automatically import order information to quickly print your labels.',
					'woocommerce'
				),
			),
			array(
				'icon'        => $asset_base_url . 'discount.svg',
				'title'       => __( 'Save money', 'woocommerce' ),
				'description' => __(
					'Shop for the best shipping rates, and access pre-negotiated discounted rates.',
					'woocommerce'
				),
			),
			array(
				'icon'        => $asset_base_url . 'star.svg',
				'title'       => __( 'Wow your shoppers', 'woocommerce' ),
				'description' => __(
					'Keep your customers informed with tracking notifications.',
					'woocommerce'
				),
			),
		);

		$check_icon = $asset_base_url . 'check.svg';

		return array(
			array(
				'name'              => 'ShipStation',
				'slug'              => 'woocommerce-shipstation-integration',
				'description'       => __( 'Powerful yet easy-to-use solution:', 'woocommerce' ),
				'layout_column'     => array(
					'image'    => $asset_base_url . 'shipstation-column.svg',
					'features' => $column_layout_features,
				),
				'layout_row'        => array(
					'image'    => $asset_base_url . 'shipstation-row.svg',
					'features' => array(
						array(
							'icon'        => $check_icon,
							'description' => __(
								'Print labels from Royal Mail, Parcel Force, DPD, and many more',
								'woocommerce'
							),
						),
						array(
							'icon'        => $check_icon,
							'description' => __(
								'Shop for the best rates, in real-time',
								'woocommerce'
							),
						),
						array(
							'icon'        => $check_icon,
							'description' => __( 'Connect selling channels easily', 'woocommerce' ),
						),
						array(
							'icon'        => $check_icon,
							'description' => __( 'Advance automated workflows', 'woocommerce' ),
						),
						array(
							'icon'        => $check_icon,
							'description' => __( '30-days free trial', 'woocommerce' ),
						),
					),
				),
				'learn_more_link'   => 'https://wordpress.org/plugins/woocommerce-shipstation-integration/',
				'is_visible'        => array(
					self::get_rules_for_countries( array( 'AU', 'CA', 'GB' ) ),
				),
				'available_layouts' => array( 'row', 'column' ),
			),
			array(
				'name'              => 'Skydropx',
				'slug'              => 'skydropx-cotizador-y-envios',
				'layout_column'     => array(
					'image'    => $asset_base_url . 'skydropx-column.svg',
					'features' => $column_layout_features,
				),
				'description'       => '',
				'learn_more_link'   => 'https://wordpress.org/plugins/skydropx-cotizador-y-envios/',
				'is_visible'        => array(
					self::get_rules_for_countries( array( 'MX', 'CO' ) ),
				),
				'available_layouts' => array( 'column' ),
			),
			array(
				'name'              => 'Envia',
				'slug'              => '',
				'description'       => '',
				'layout_column'     => array(
					'image'    => $asset_base_url . 'envia-column.svg',
					'features' => $column_layout_features,
				),
				'learn_more_link'   => 'https://woocommerce.com/products/envia-shipping-and-fulfillment/',
				'is_visible'        => array(
					self::get_rules_for_countries( array( 'CL', 'AR', 'PE', 'BR', 'UY', 'GT' ) ),
				),
				'available_layouts' => array( 'column' ),
			),
			array(
				'name'              => 'Sendcloud',
				'slug'              => 'sendcloud-shipping',
				'description'       => __( 'All-in-one shipping tool:', 'woocommerce' ),
				'layout_column'     => array(
					'image'    => $asset_base_url . 'sendcloud-column.svg',
					'features' => $column_layout_features,
				),
				'layout_row'        => array(
					'image'    => $asset_base_url . 'sendcloud-row.svg',
					'features' => array(
						array(
							'icon'        => $check_icon,
							'description' => __( 'Print labels from 80+ carriers', 'woocommerce' ),
						),
						array(
							'icon'        => $check_icon,
							'description' => __(
								'Process orders in just a few clicks',
								'woocommerce'
							),
						),
						array(
							'icon'        => $check_icon,
							'description' => __( 'Customize checkout options', 'woocommerce' ),
						),

						array(
							'icon'        => $check_icon,
							'description' => __( 'Self-service tracking & returns', 'woocommerce' ),
						),
						array(
							'icon'        => $check_icon,
							'description' => __( 'Start with a free plan', 'woocommerce' ),
						),
					),
				),
				'learn_more_link'   => 'https://wordpress.org/plugins/sendcloud-shipping/',
				'is_visible'        => array(
					self::get_rules_for_countries( array( 'NL', 'AT', 'BE', 'FR', 'DE', 'ES', 'GB', 'IT' ) ),
				),
				'available_layouts' => array( 'row', 'column' ),
			),
			array(
				'name'              => 'Packlink',
				'slug'              => 'packlink-pro-shipping',
				'description'       => __( 'Optimize your full shipping process:', 'woocommerce' ),
				'layout_column'     => array(
					'image'    => $asset_base_url . 'packlink-column.svg',
					'features' => $column_layout_features,
				),
				'layout_row'        => array(
					'image'    => $asset_base_url . 'packlink-row.svg',
					'features' => array(
						array(
							'icon'        => $check_icon,
							'description' => __(
								'Automated, real-time order import',
								'woocommerce'
							),
						),
						array(
							'icon'        => $check_icon,
							'description' => __(
								'Direct access to leading carriers',
								'woocommerce'
							),
						),
						array(
							'icon'        => $check_icon,
							'description' => __(
								'Access competitive shipping prices',
								'woocommerce'
							),
						),
						array(
							'icon'        => $check_icon,
							'description' => __( 'Quickly bulk print labels', 'woocommerce' ),
						),
						array(
							'icon'        => $check_icon,
							'description' => __( 'Free shipping platform', 'woocommerce' ),
						),
					),
				),
				'learn_more_link'   => 'https://wordpress.org/plugins/packlink-pro-shipping/',
				'is_visible'        => array(
					self::get_rules_for_countries( array( 'FR', 'DE', 'ES', 'IT' ) ),
				),
				'available_layouts' => array( 'row', 'column' ),
			),
			array(
				'title'             => 'WooCommerce Shipping',
				'slug'              => 'woocommerce-services',
				'description'       => __( 'Save time and money by printing your shipping labels right from your computer with WooCommerce Shipping. Try WooCommerce Shipping for free.', 'woocommerce' ),
				'layout_column'     => array(
					'image'    => $asset_base_url . 'wcs-column.svg',
					'features' => $column_layout_features,
				),
				'learn_more_link'   => 'https://woocommerce.com/products/shipping/',
				'is_visible'        => array(
					self::get_rules_for_countries( array( 'US' ) ),
				),
				'available_layouts' => array( 'column' ),
			),
		);
	}

	/**
	 * Get rules that match the store base location to one of the provided countries.
	 *
	 * @param array $countries Array of countries to match.
	 * @return object Rules to match.
	 */
	public static function get_rules_for_countries( $countries ) {
		$rules = array();

		foreach ( $countries as $country ) {
			$rules[] = (object) array(
				'type'      => 'base_location_country',
				'value'     => $country,
				'operation' => '=',
			);
		}

		return (object) array(
			'type'     => 'or',
			'operands' => $rules,
		);
	}
}

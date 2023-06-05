<?php
/**
 * Gets a list of fallback methods if remote fetching is disabled.
 */

namespace Automattic\WooCommerce\Internal\Admin\RemoteFreeExtensions;

use Automattic\WooCommerce\Admin\Features\PaymentGatewaySuggestions\DefaultPaymentGateways;

defined( 'ABSPATH' ) || exit;


/**
 * Default Free Extensions
 */
class DefaultFreeExtensions {

	/**
	 * Get default specs.
	 *
	 * @return array Default specs.
	 */
	public static function get_all() {
		$bundles = [
			[
				'key'     => 'obw/basics',
				'title'   => __( 'Get the basics', 'woocommerce' ),
				'plugins' => [
					self::get_plugin( 'woocommerce-payments' ),
					self::get_plugin( 'woocommerce-services:shipping' ),
					self::get_plugin( 'woocommerce-services:tax' ),
					self::get_plugin( 'jetpack' ),
				],
			],
			[
				'key'     => 'obw/grow',
				'title'   => __( 'Grow your store', 'woocommerce' ),
				'plugins' => [
					self::get_plugin( 'mailpoet' ),
					self::get_plugin( 'codistoconnect' ),
					self::get_plugin( 'google-listings-and-ads' ),
					self::get_plugin( 'pinterest-for-woocommerce' ),
					self::get_plugin( 'facebook-for-woocommerce' ),
					self::get_plugin( 'tiktok-for-business:alt' ),
				],
			],
			[
				'key'     => 'task-list/reach',
				'title'   => __( 'Reach out to customers', 'woocommerce' ),
				'plugins' => [
					self::get_plugin( 'mailpoet:alt' ),
					self::get_plugin( 'mailchimp-for-woocommerce' ),
					self::get_plugin( 'creative-mail-by-constant-contact' ),
				],
			],
			[
				'key'     => 'task-list/grow',
				'title'   => __( 'Grow your store', 'woocommerce' ),
				'plugins' => [
					self::get_plugin( 'google-listings-and-ads:alt' ),
					self::get_plugin( 'tiktok-for-business' ),
					self::get_plugin( 'pinterest-for-woocommerce:alt' ),
					self::get_plugin( 'facebook-for-woocommerce:alt' ),
					self::get_plugin( 'codistoconnect:alt' ),
				],
			],
		];

		$bundles = wp_json_encode( $bundles );
		return json_decode( $bundles );
	}

	/**
	 * Get the plugin arguments by slug.
	 *
	 * @param string $slug Slug.
	 * @return array
	 */
	public static function get_plugin( $slug ) {
		$plugins = array(
			'google-listings-and-ads'           => [
				'min_php_version' => '7.4',
				'name'            => __( 'Google Listings & Ads', 'woocommerce' ),
				'description'     => sprintf(
					/* translators: 1: opening product link tag. 2: closing link tag */
					__( 'Drive sales with %1$sGoogle Listings and Ads%2$s', 'woocommerce' ),
					'<a href="https://woocommerce.com/products/google-listings-and-ads" target="_blank">',
					'</a>'
				),
				'image_url'       => plugins_url( '/assets/images/onboarding/google.svg', WC_PLUGIN_FILE ),
				'manage_url'      => 'admin.php?page=wc-admin&path=%2Fgoogle%2Fstart',
				'is_built_by_wc'  => true,
				'is_visible'      => [
					[
						'type'    => 'not',
						'operand' => [
							[
								'type'    => 'plugins_activated',
								'plugins' => [ 'google-listings-and-ads' ],
							],
						],
					],
				],
			],
			'google-listings-and-ads:alt'       => [
				'name'           => __( 'Google Listings & Ads', 'woocommerce' ),
				'description'    => __( 'Reach more shoppers and drive sales for your store. Integrate with Google to list your products for free and launch paid ad campaigns.', 'woocommerce' ),
				'image_url'      => plugins_url( '/assets/images/onboarding/google.svg', WC_PLUGIN_FILE ),
				'manage_url'     => 'admin.php?page=wc-admin&path=%2Fgoogle%2Fstart',
				'is_built_by_wc' => true,
			],
			'facebook-for-woocommerce'          => [
				'name'           => __( 'Facebook for WooCommerce', 'woocommerce' ),
				'description'    => __( 'List products and create ads on Facebook and Instagram with <a href="https://woocommerce.com/products/facebook/">Facebook for WooCommerce</a>', 'woocommerce' ),
				'image_url'      => plugins_url( '/assets/images/onboarding/facebook.png', WC_PLUGIN_FILE ),
				'manage_url'     => 'admin.php?page=wc-facebook',
				'is_visible'     => false,
				'is_built_by_wc' => false,
			],
			'facebook-for-woocommerce:alt'      => [
				'name'           => __( 'Facebook for WooCommerce', 'woocommerce' ),
				'description'    => __( 'List products and create ads on Facebook and Instagram.', 'woocommerce' ),
				'image_url'      => plugins_url( '/assets/images/onboarding/facebook.png', WC_PLUGIN_FILE ),
				'manage_url'     => 'admin.php?page=wc-facebook',
				'is_visible'     => false,
				'is_built_by_wc' => false,
			],
			'pinterest-for-woocommerce'         => [
				'name'            => __( 'Pinterest for WooCommerce', 'woocommerce' ),
				'description'     => __( 'Get your products in front of Pinners searching for ideas and things to buy.', 'woocommerce' ),
				'image_url'       => plugins_url( '/assets/images/onboarding/pinterest.png', WC_PLUGIN_FILE ),
				'manage_url'      => 'admin.php?page=wc-admin&path=%2Fpinterest%2Flanding',
				'is_built_by_wc'  => true,
				'min_php_version' => '7.3',
			],
			'pinterest-for-woocommerce:alt'     => [
				'name'           => __( 'Pinterest for WooCommerce', 'woocommerce' ),
				'description'    => __( 'Get your products in front of Pinterest users searching for ideas and things to buy. Get started with Pinterest and make your entire product catalog browsable.', 'woocommerce' ),
				'image_url'      => plugins_url( '/assets/images/onboarding/pinterest.png', WC_PLUGIN_FILE ),
				'manage_url'     => 'admin.php?page=wc-admin&path=%2Fpinterest%2Flanding',
				'is_built_by_wc' => true,
			],
			'mailpoet'                          => [
				'name'           => __( 'MailPoet', 'woocommerce' ),
				'description'    => __( 'Create and send purchase follow-up emails, newsletters, and promotional campaigns straight from your dashboard.', 'woocommerce' ),
				'image_url'      => plugins_url( '/assets/images/onboarding/mailpoet.png', WC_PLUGIN_FILE ),
				'manage_url'     => 'admin.php?page=mailpoet-newsletters',
				'is_built_by_wc' => true,
			],
			'mailchimp-for-woocommerce'         => [
				'name'           => __( 'Mailchimp', 'woocommerce' ),
				'description'    => __( 'Send targeted campaigns, recover abandoned carts and much more with Mailchimp.', 'woocommerce' ),
				'image_url'      => plugins_url( '/assets/images/onboarding/mailchimp-for-woocommerce.png', WC_PLUGIN_FILE ),
				'manage_url'     => 'admin.php?page=mailchimp-woocommerce',
				'is_built_by_wc' => false,
			],
			'creative-mail-by-constant-contact' => [
				'name'           => __( 'Creative Mail for WooCommerce', 'woocommerce' ),
				'description'    => __( 'Create on-brand store campaigns, fast email promotions and customer retargeting with Creative Mail.', 'woocommerce' ),
				'image_url'      => plugins_url( '/assets/images/onboarding/creative-mail-by-constant-contact.png', WC_PLUGIN_FILE ),
				'manage_url'     => 'admin.php?page=creativemail',
				'is_built_by_wc' => false,
			],
			'codistoconnect'                    => [
				'name'           => __( 'Codisto for WooCommerce', 'woocommerce' ),
				'description'    => sprintf(
					/* translators: 1: opening product link tag. 2: closing link tag */
					__( 'Sell on Amazon, eBay, Walmart and more directly from WooCommerce with  %1$sCodisto%2$s', 'woocommerce' ),
					'<a href="https://woocommerce.com/pt-br/products/amazon-ebay-integration/?quid=c247a85321c9e93e7c3c6f1eb072e6e5" target="_blank">',
					'</a>'
				),
				'image_url'      => plugins_url( '/assets/images/onboarding/codistoconnect.png', WC_PLUGIN_FILE ),
				'manage_url'     => 'admin.php?page=codisto-settings',
				'is_built_by_wc' => true,
			],
			'codistoconnect:alt'                => [
				'name'           => __( 'Codisto for WooCommerce', 'woocommerce' ),
				'description'    => __( 'Sell on Amazon, eBay, Walmart and more directly from WooCommerce.', 'woocommerce' ),
				'image_url'      => plugins_url( '/assets/images/onboarding/codistoconnect.png', WC_PLUGIN_FILE ),
				'manage_url'     => 'admin.php?page=codisto-settings',
				'is_built_by_wc' => true,
			],
			'woocommerce-payments'              => [
				'description'    => sprintf(
					/* translators: 1: opening product link tag. 2: closing link tag */
					__( 'Accept credit cards and other popular payment methods with %1$sWooCommerce Payments%2$s', 'woocommerce' ),
					'<a href="https://woocommerce.com/products/woocommerce-payments" target="_blank">',
					'</a>'
				),
				'is_visible'     => [
					[
						'type'     => 'or',
						'operands' => [
							[
								'type'      => 'base_location_country',
								'value'     => 'US',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'PR',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'AU',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'CA',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'DE',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'ES',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'FR',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'GB',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'IE',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'IT',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'NZ',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'AT',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'BE',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'NL',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'PL',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'PT',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'CH',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'HK',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'SG',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'CY',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'DK',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'EE',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'FI',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'GR',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'LU',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'LT',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'LV',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'NO',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'MT',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'SI',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'SK',
								'operation' => '=',
							],
						],
					],
					DefaultPaymentGateways::get_rules_for_cbd( false ),
				],
				'is_built_by_wc' => true,
				'min_wp_version' => '5.9',
			],
			'woocommerce-services:shipping'     => [
				'description'    => sprintf(
				/* translators: 1: opening product link tag. 2: closing link tag */
					__( 'Print shipping labels with %1$sWooCommerce Shipping%2$s', 'woocommerce' ),
					'<a href="https://woocommerce.com/products/shipping" target="_blank">',
					'</a>'
				),
				'is_visible'     => [
					[
						'type'      => 'base_location_country',
						'value'     => 'US',
						'operation' => '=',
					],
					[
						'type'    => 'not',
						'operand' => [
							[
								'type'    => 'plugins_activated',
								'plugins' => [ 'woocommerce-services' ],
							],
						],
					],
					[
						'type'     => 'or',
						'operands' => [
							[
								[
									'type'         => 'option',
									'transformers' => [
										[
											'use'       => 'dot_notation',
											'arguments' => [
												'path' => 'product_types',
											],
										],
										[
											'use' => 'count',
										],
									],
									'option_name'  => 'woocommerce_onboarding_profile',
									'value'        => 1,
									'default'      => array(),
									'operation'    => '!=',
								],
							],
							[
								[
									'type'         => 'option',
									'transformers' => [
										[
											'use'       => 'dot_notation',
											'arguments' => [
												'path' => 'product_types.0',
											],
										],
									],
									'option_name'  => 'woocommerce_onboarding_profile',
									'value'        => 'downloads',
									'default'      => '',
									'operation'    => '!=',
								],
							],
						],
					],
				],
				'is_built_by_wc' => true,
			],
			'woocommerce-services:tax'          => [
				'description'    => sprintf(
					/* translators: 1: opening product link tag. 2: closing link tag */
					__( 'Get automated sales tax with %1$sWooCommerce Tax%2$s', 'woocommerce' ),
					'<a href="https://woocommerce.com/products/tax" target="_blank">',
					'</a>'
				),
				'is_visible'     => [
					[
						'type'     => 'or',
						'operands' => [
							[
								'type'      => 'base_location_country',
								'value'     => 'US',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'FR',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'GB',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'DE',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'CA',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'AU',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'GR',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'BE',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'PT',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'DK',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'SE',
								'operation' => '=',
							],
						],
					],
					[
						'type'    => 'not',
						'operand' => [
							[
								'type'    => 'plugins_activated',
								'plugins' => [ 'woocommerce-services' ],
							],
						],
					],
				],
				'is_built_by_wc' => true,
			],
			'jetpack'                           => [
				'description'    => sprintf(
					/* translators: 1: opening product link tag. 2: closing link tag */
					__( 'Enhance speed and security with %1$sJetpack%2$s', 'woocommerce' ),
					'<a href="https://woocommerce.com/products/jetpack" target="_blank">',
					'</a>'
				),
				'is_visible'     => [
					[
						'type'    => 'not',
						'operand' => [
							[
								'type'    => 'plugins_activated',
								'plugins' => [ 'jetpack' ],
							],
						],
					],
				],
				'is_built_by_wc' => false,
				'min_wp_version' => '6.0',
			],
			'mailpoet'                          => [
				'name'           => __( 'MailPoet', 'woocommerce' ),
				'description'    => sprintf(
					/* translators: 1: opening product link tag. 2: closing link tag */
					__( 'Level up your email marketing with %1$sMailPoet%2$s', 'woocommerce' ),
					'<a href="https://woocommerce.com/products/mailpoet" target="_blank">',
					'</a>'
				),
				'manage_url'     => 'admin.php?page=mailpoet-newsletters',
				'is_visible'     => [
					[
						'type'    => 'not',
						'operand' => [
							[
								'type'    => 'plugins_activated',
								'plugins' => [ 'mailpoet' ],
							],
						],
					],
				],
				'is_built_by_wc' => true,
			],
			'mailpoet:alt'                      => [
				'name'           => __( 'MailPoet', 'woocommerce' ),
				'description'    => __( 'Create and send purchase follow-up emails, newsletters, and promotional campaigns straight from your dashboard.', 'woocommerce' ),
				'image_url'      => plugins_url( '/assets/images/onboarding/mailpoet.png', WC_PLUGIN_FILE ),
				'manage_url'     => 'admin.php?page=mailpoet-newsletters',
				'is_built_by_wc' => true,
			],
			'tiktok-for-business'               => [
				'name'           => __( 'TikTok for WooCommerce', 'woocommerce' ),
				'image_url'      => plugins_url( '/assets/images/onboarding/tiktok.svg', WC_PLUGIN_FILE ),
				'description'    =>
					__( 'Grow your online sales by promoting your products on TikTok to over one billion monthly active users around the world.', 'woocommerce' ),
				'manage_url'     => 'admin.php?page=tiktok',
				'is_visible'     => [
					[
						'type'     => 'or',
						'operands' => [
							[
								'type'      => 'base_location_country',
								'value'     => 'US',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'CA',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'MX',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'AT',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'BE',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'CZ',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'DK',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'FI',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'FR',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'DE',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'GR',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'HU',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'IE',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'IT',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'NL',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'PL',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'PT',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'RO',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'ES',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'SE',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'GB',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'CH',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'NO',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'AU',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'NZ',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'SG',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'MY',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'PH',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'ID',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'VN',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'TH',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'KR',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'IL',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'AE',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'RU',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'UA',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'TR',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'SA',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'BR',
								'operation' => '=',
							],
							[
								'type'      => 'base_location_country',
								'value'     => 'JP',
								'operation' => '=',
							],
						],
					],
				],
				'is_built_by_wc' => false,
			],
			'tiktok-for-business:alt'           => [
				'name'           => __( 'TikTok for WooCommerce', 'woocommerce' ),
				'image_url'      => plugins_url( '/assets/images/onboarding/tiktok.svg', WC_PLUGIN_FILE ),
				'description'    => sprintf(
					/* translators: 1: opening product link tag. 2: closing link tag */
					__( 'Create ad campaigns and reach one billion global users with %1$sTikTok for WooCommerce%2$s', 'woocommerce' ),
					'<a href="https://woocommerce.com/products/tiktok-for-woocommerce" target="_blank">',
					'</a>'
				),
				'manage_url'     => 'admin.php?page=tiktok',
				'is_built_by_wc' => false,
				'is_visible'     => false,
			],
		);

		$plugin        = $plugins[ $slug ];
		$plugin['key'] = $slug;

		return $plugin;
	}
}

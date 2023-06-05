<?php
/**
 * WooCommerce Onboarding Industries
 */

namespace Automattic\WooCommerce\Internal\Admin\Onboarding;

/**
 * Logic around onboarding industries.
 */
class OnboardingIndustries {
	/**
	 * Init.
	 */
	public static function init() {
		add_filter( 'woocommerce_admin_onboarding_preloaded_data', array( __CLASS__, 'preload_data' ) );
	}

	/**
	 * Get a list of allowed industries for the onboarding wizard.
	 *
	 * @return array
	 */
	public static function get_allowed_industries() {
		/* With "use_description" we turn the description input on. With "description_label" we set the input label */
		return apply_filters(
			'woocommerce_admin_onboarding_industries',
			array(
				'fashion-apparel-accessories'     => array(
					'label'             => __( 'Fashion, apparel, and accessories', 'woocommerce' ),
					'use_description'   => false,
					'description_label' => '',
				),
				'health-beauty'                   => array(
					'label'             => __( 'Health and beauty', 'woocommerce' ),
					'use_description'   => false,
					'description_label' => '',
				),
				'electronics-computers'           => array(
					'label'             => __( 'Electronics and computers', 'woocommerce' ),
					'use_description'   => false,
					'description_label' => '',
				),
				'food-drink'                      => array(
					'label'             => __( 'Food and drink', 'woocommerce' ),
					'use_description'   => false,
					'description_label' => '',
				),
				'home-furniture-garden'           => array(
					'label'             => __( 'Home, furniture, and garden', 'woocommerce' ),
					'use_description'   => false,
					'description_label' => '',
				),
				'cbd-other-hemp-derived-products' => array(
					'label'             => __( 'CBD and other hemp-derived products', 'woocommerce' ),
					'use_description'   => false,
					'description_label' => '',
				),
				'education-and-learning'          => array(
					'label'             => __( 'Education and learning', 'woocommerce' ),
					'use_description'   => false,
					'description_label' => '',
				),
				'other'                           => array(
					'label'             => __( 'Other', 'woocommerce' ),
					'use_description'   => true,
					'description_label' => __( 'Description', 'woocommerce' ),
				),
			)
		);
	}

	/**
	 * Add preloaded data to onboarding.
	 *
	 * @param array $settings Component settings.
	 * @return array
	 */
	public static function preload_data( $settings ) {
		$settings['onboarding']['industries'] = self::get_allowed_industries();
		return $settings;
	}
}

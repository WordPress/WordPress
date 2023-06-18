<?php
/**
 * WooCommerce-specific auto-insert locations.
 *
 * @package WPCode
 */

/**
 * Class WPCode_Auto_Insert_WooCommerce_Lite.
 */
class WPCode_Auto_Insert_WooCommerce_Lite extends WPCode_Auto_Insert_Type {

	/**
	 * The category of this type.
	 *
	 * @var string
	 */
	public $category = 'ecommerce';

	/**
	 * Not available to select.
	 *
	 * @var string
	 */
	public $code_type = 'pro';

	/**
	 * Text to display next to optgroup label.
	 *
	 * @var string
	 */
	public $label_pill = 'PRO';

	/**
	 * Load the available options and labels.
	 *
	 * @return void
	 */
	public function init() {
		$this->label         = 'WooCommerce';
		$this->locations     = array(
			'wc_before_products_list'              => array(
				'label'       => __( 'Before the List of Products', 'insert-headers-and-footers' ),
				'description' => __( 'Insert snippet before the list of products on a WooCommerce page.', 'insert-headers-and-footers' ),
			),
			'wc_after_products_list'               => array(
				'label'       => __( 'After the List of Products', 'insert-headers-and-footers' ),
				'description' => __( 'Insert snippet after the list of products on a WooCommerce page.', 'insert-headers-and-footers' ),
			),
			'wc_before_single_product'             => array(
				'label'       => __( 'Before the Single Product', 'insert-headers-and-footers' ),
				'description' => __( 'Insert snippet before the content on the single WooCommerce product page.', 'insert-headers-and-footers' ),
			),
			'wc_after_single_product'              => array(
				'label'       => __( 'After the Single Product', 'insert-headers-and-footers' ),
				'description' => __( 'Insert snippet after the content on the single WooCommerce product page.', 'insert-headers-and-footers' ),
			),
			'wc_before_single_product_summary'     => array(
				'label'       => __( 'Before the Single Product Summary', 'insert-headers-and-footers' ),
				'description' => __( 'Insert snippet before the product summary on the single WooCommerce product page.', 'insert-headers-and-footers' ),
			),
			'wc_after_single_product_summary'      => array(
				'label'       => __( 'After the Single Product Summary', 'insert-headers-and-footers' ),
				'description' => __( 'Insert snippet after the product summary on the single WooCommerce product page.', 'insert-headers-and-footers' ),
			),
			'woocommerce_before_cart'              => array(
				'label'       => __( 'Before the Cart', 'insert-headers-and-footers' ),
				'description' => __( 'Insert snippet before the cart on WooCommerce pages.', 'insert-headers-and-footers' ),
			),
			'woocommerce_after_cart'               => array(
				'label'       => __( 'After the Cart', 'insert-headers-and-footers' ),
				'description' => __( 'Insert snippet after the cart on WooCommerce pages.', 'insert-headers-and-footers' ),
			),
			'woocommerce_before_checkout_form'     => array(
				'label'       => __( 'Before the Checkout Form', 'insert-headers-and-footers' ),
				'description' => __( 'Insert snippet before the checkout form on the WooCommerce checkout page.', 'insert-headers-and-footers' ),
			),
			'woocommerce_after_checkout_form'      => array(
				'label'       => __( 'After the Checkout Form', 'insert-headers-and-footers' ),
				'description' => __( 'Insert snippet after the checkout form on the WooCommerce checkout page.', 'insert-headers-and-footers' ),
			),
			'woocommerce_checkout_order_review_19' => array(
				'label'       => __( 'Before Checkout Payment Button', 'insert-headers-and-footers' ),
				'description' => __( 'Insert snippet before the checkout payment button on the WooCommerce checkout page.', 'insert-headers-and-footers' ),
			),
			'woocommerce_checkout_order_review_21' => array(
				'label'       => __( 'After Checkout Payment Button', 'insert-headers-and-footers' ),
				'description' => __( 'Insert snippet after the checkout payment button on the WooCommerce checkout page.', 'insert-headers-and-footers' ),
			),
			'woocommerce_before_thankyou'          => array(
				'label'       => __( 'Before the Thank You Page', 'insert-headers-and-footers' ),
				'description' => __( 'Insert snippet before the thank you page content for WooCommerce.', 'insert-headers-and-footers' ),
			),
		);
		$this->upgrade_title = __( 'WooCommerce Locations are a PRO feature', 'insert-headers-and-footers' );
		$this->upgrade_text  = __( 'Upgrade to PRO today and get access to advanced eCommerce auto-insert locations and conditional logic rules for your needs.', 'insert-headers-and-footers' );
		$this->upgrade_link  = wpcode_utm_url( 'https://wpcode.com/lite/', 'edit-snippet', 'auto-insert', 'woocommerce' );
	}
}

new WPCode_Auto_Insert_WooCommerce_Lite();

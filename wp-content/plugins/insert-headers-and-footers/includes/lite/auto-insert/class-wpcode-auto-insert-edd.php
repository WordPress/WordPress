<?php
/**
 * Easy Digital Downloads specific auto-insert locations.
 *
 * @package WPCode
 */

/**
 * Class WPCode_Auto_Insert_EDD.
 */
class WPCode_Auto_Insert_EDD_Lite extends WPCode_Auto_Insert_Type {

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
		$this->label         = 'Easy Digital Downloads';
		$this->locations     = array(
			'edd_purchase_link_top'       => array(
				'label'       => __( 'Before the Purchase Button', 'insert-headers-and-footers' ),
				'description' => __( 'Insert snippet before the EDD purchase button.', 'insert-headers-and-footers' ),
			),
			'edd_purchase_link_end'       => array(
				'label'       => __( 'After the Purchase Button', 'insert-headers-and-footers' ),
				'description' => __( 'Insert snippet after the EDD purchase button.', 'insert-headers-and-footers' ),
			),
			'edd_before_download_content' => array(
				'label'       => __( 'Before the Single Download', 'insert-headers-and-footers' ),
				'description' => __( 'Insert snippet before the single EDD download content.', 'insert-headers-and-footers' ),
			),
			'edd_after_download_content'  => array(
				'label'       => __( 'After the Single Download', 'insert-headers-and-footers' ),
				'description' => __( 'Insert snippet after the single EDD download content.', 'insert-headers-and-footers' ),
			),
			'edd_before_cart'             => array(
				'label'       => __( 'Before the Cart', 'insert-headers-and-footers' ),
				'description' => __( 'Insert snippet before the EDD cart.', 'insert-headers-and-footers' ),
			),
			'edd_after_cart'              => array(
				'label'       => __( 'After the Cart', 'insert-headers-and-footers' ),
				'description' => __( 'Insert snippet after the EDD cart.', 'insert-headers-and-footers' ),
			),
			'edd_before_checkout_cart'    => array(
				'label'       => __( 'Before the Checkout Cart', 'insert-headers-and-footers' ),
				'description' => __( 'Insert snippet before the EDD cart on the checkout page.', 'insert-headers-and-footers' ),
			),
			'edd_after_checkout_cart'     => array(
				'label'       => __( 'After the Checkout Cart', 'insert-headers-and-footers' ),
				'description' => __( 'Insert snippet after the EDD cart on the checkout page.', 'insert-headers-and-footers' ),
			),
			'edd_before_purchase_form'    => array(
				'label'       => __( 'Before the Checkout Form', 'insert-headers-and-footers' ),
				'description' => __( 'Insert snippet before the EDD checkout form on the checkout page.', 'insert-headers-and-footers' ),
			),
			'edd_after_purchase_form'     => array(
				'label'       => __( 'After the Checkout Form', 'insert-headers-and-footers' ),
				'description' => __( 'Insert snippet after the EDD checkout form on the checkout page', 'insert-headers-and-footers' ),
			),
		);
		$this->upgrade_title = __( 'Easy Digital Downloads Locations are a PRO feature', 'insert-headers-and-footers' );
		$this->upgrade_text  = __( 'Upgrade to PRO today and get access to advanced eCommerce auto-insert locations and conditional logic rules for your needs.', 'insert-headers-and-footers' );
		$this->upgrade_link  = wpcode_utm_url( 'https://wpcode.com/lite/', 'edit-snippet', 'auto-insert', 'edd' );
	}
}

new WPCode_Auto_Insert_EDD_Lite();

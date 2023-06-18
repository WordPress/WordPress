<?php
/**
 * MemberPress-specific auto-insert locations.
 *
 * @package WPCode
 */

/**
 * Class WPCode_Auto_Insert_MemberPress_Lite.
 */
class WPCode_Auto_Insert_MemberPress_Lite extends WPCode_Auto_Insert_Type {

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
		$this->label         = 'MemberPress';
		$this->locations     = array(
			'mepr-above-checkout-form'          => array(
				'label'       => __( 'Before the Registration Form', 'insert-headers-and-footers' ),
				'description' => __( 'Insert snippet before the MemberPress registration form used for checkout.', 'insert-headers-and-footers' ),
			),
			'mepr-checkout-before-submit'       => array(
				'label'       => __( 'Before Checkout Submit Button', 'insert-headers-and-footers' ),
				'description' => __( 'Insert snippet right before the MemberPress checkout submit button.', 'insert-headers-and-footers' ),
			),
			'mepr-checkout-before-coupon-field' => array(
				'label'       => __( 'Before Checkout Coupon Field', 'insert-headers-and-footers' ),
				'description' => __( 'Insert snippet before the MemberPress checkout coupon field.', 'insert-headers-and-footers' ),
			),
			'mepr-account-home-before-name'     => array(
				'label'       => __( 'Before Account First Name', 'insert-headers-and-footers' ),
				'description' => __( 'Insert snippet to the Home tab of the MemberPress Account page before First Name field.', 'insert-headers-and-footers' ),
			),
			'mepr_before_account_subscriptions' => array(
				'label'       => __( 'Before Subscriptions Content', 'insert-headers-and-footers' ),
				'description' => __( 'Insert snippet at the beginning of the Subscriptions tab on the MemberPress Account page.', 'insert-headers-and-footers' ),
			),
			'mepr-login-form-before-submit'     => array(
				'label'       => __( 'Before Login Form Submit', 'insert-headers-and-footers' ),
				'description' => __( 'Insert snippet before the Remember Me checkbox on the MemberPress Login page.', 'insert-headers-and-footers' ),
			),
			'mepr_unauthorized_message_before'  => array(
				'label'       => __( 'Before the Unauthorized Message', 'insert-headers-and-footers' ),
				'description' => __( 'Insert a snippet before the notice that access to the content is unauthorized. ', 'insert-headers-and-footers' ),
			),
			'mepr_unauthorized_message_after'   => array(
				'label'       => __( 'After the Unauthorized Message', 'insert-headers-and-footers' ),
				'description' => __( 'Insert a snippet after the notice that access to the content is unauthorized. ', 'insert-headers-and-footers' ),
			),
		);
		$this->upgrade_title = __( 'MemberPress Locations are a PRO feature', 'insert-headers-and-footers' );
		$this->upgrade_text  = __( 'Upgrade to PRO today and get access to advanced eCommerce auto-insert locations and conditional logic rules for your needs.', 'insert-headers-and-footers' );
		$this->upgrade_link  = wpcode_utm_url( 'https://wpcode.com/lite/', 'edit-snippet', 'auto-insert', 'memberpress' );
	}
}

new WPCode_Auto_Insert_MemberPress_Lite();

<?php
/**
 * WooCommerce Account Settings
 *
 * @author 		WooThemes
 * @category 	Admin
 * @package 	WooCommerce/Admin
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WC_Settings_Accounts' ) ) :

/**
 * WC_Settings_Accounts
 */
class WC_Settings_Accounts extends WC_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'account';
		$this->label = __( 'Accounts', 'woocommerce' );

		add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
		add_action( 'woocommerce_settings_' . $this->id, array( $this, 'output' ) );
		add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'save' ) );
	}

	/**
	 * Get settings array
	 *
	 * @return array
	 */
	public function get_settings() {

		return apply_filters( 'woocommerce_' . $this->id . '_settings', array(

			array( 'title' => __( 'Account Pages', 'woocommerce' ), 'type' => 'title', 'desc' => __( 'These pages need to be set so that WooCommerce knows where to send users to access account related functionality.', 'woocommerce' ), 'id' => 'account_page_options' ),

			array(
				'title' => __( 'My Account Page', 'woocommerce' ),
				'desc' 		=> __( 'Page contents:', 'woocommerce' ) . ' [' . apply_filters( 'woocommerce_my_account_shortcode_tag', 'woocommerce_my_account' ) . ']',
				'id' 		=> 'woocommerce_myaccount_page_id',
				'type' 		=> 'single_select_page',
				'default'	=> '',
				'class'		=> 'chosen_select_nostd',
				'css' 		=> 'min-width:300px;',
				'desc_tip'	=> true,
			),

			array( 'type' => 'sectionend', 'id' => 'account_page_options' ),

			array( 'title' => __( 'My Account Endpoints', 'woocommerce' ), 'type' => 'title', 'desc' => __( 'Endpoints are appended to your page URLs to handle specific actions on the accounts pages. They should be unique.', 'woocommerce' ), 'id' => 'account_endpoint_options' ),

			array(
				'title' => __( 'View Order', 'woocommerce' ),
				'desc' 		=> __( 'Endpoint for the My Account &rarr; View Order page', 'woocommerce' ),
				'id' 		=> 'woocommerce_myaccount_view_order_endpoint',
				'type' 		=> 'text',
				'default'	=> 'view-order',
				'desc_tip'	=> true,
			),

			array(
				'title' => __( 'Edit Account', 'woocommerce' ),
				'desc' 		=> __( 'Endpoint for the My Account &rarr; Edit Account page', 'woocommerce' ),
				'id' 		=> 'woocommerce_myaccount_edit_account_endpoint',
				'type' 		=> 'text',
				'default'	=> 'edit-account',
				'desc_tip'	=> true,
			),

			array(
				'title' => __( 'Edit Address', 'woocommerce' ),
				'desc' 		=> __( 'Endpoint for the My Account &rarr; Edit Address page', 'woocommerce' ),
				'id' 		=> 'woocommerce_myaccount_edit_address_endpoint',
				'type' 		=> 'text',
				'default'	=> 'edit-address',
				'desc_tip'	=> true,
			),

			array(
				'title' => __( 'Lost Password', 'woocommerce' ),
				'desc' 		=> __( 'Endpoint for the My Account &rarr; Lost Password page', 'woocommerce' ),
				'id' 		=> 'woocommerce_myaccount_lost_password_endpoint',
				'type' 		=> 'text',
				'default'	=> 'lost-password',
				'desc_tip'	=> true,
			),

			array(
				'title' => __( 'Logout', 'woocommerce' ),
				'desc' 		=> __( 'Endpoint for the triggering logout. You can add this to your menus via a custom link: yoursite.com/?customer-logout=true', 'woocommerce' ),
				'id' 		=> 'woocommerce_logout_endpoint',
				'type' 		=> 'text',
				'default'	=> 'customer-logout',
				'desc_tip'	=> true,
			),

			array( 'type' => 'sectionend', 'id' => 'account_endpoint_options' ),

			array(	'title' => __( 'Registration Options', 'woocommerce' ), 'type' => 'title', 'id' => 'account_registration_options' ),

			array(
				'title'         => __( 'Enable Registration', 'woocommerce' ),
				'desc'          => __( 'Enable registration on the "Checkout" page', 'woocommerce' ),
				'id'            => 'woocommerce_enable_signup_and_login_from_checkout',
				'default'       => 'yes',
				'type'          => 'checkbox',
				'checkboxgroup' => 'start',
				'autoload'      => false
			),

			array(
				'desc'          => __( 'Enable registration on the "My Account" page', 'woocommerce' ),
				'id'            => 'woocommerce_enable_myaccount_registration',
				'default'       => 'no',
				'type'          => 'checkbox',
				'checkboxgroup' => 'end',
				'autoload'      => false
			),

			array(
				'desc'          => __( 'Display returning customer login reminder on the "Checkout" page', 'woocommerce' ),
				'id'            => 'woocommerce_enable_checkout_login_reminder',
				'default'       => 'yes',
				'type'          => 'checkbox',
				'checkboxgroup' => 'start',
				'autoload'      => false
			),

			array(
				'title'         => __( 'Account Creation', 'woocommerce' ),
				'desc'          => __( 'Automatically generate username from customer email', 'woocommerce' ),
				'id'            => 'woocommerce_registration_generate_username',
				'default'       => 'yes',
				'type'          => 'checkbox',
				'checkboxgroup' => 'start',
				'autoload'      => false
			),

			array(
				'desc'          => __( 'Automatically generate customer password', 'woocommerce' ),
				'id'            => 'woocommerce_registration_generate_password',
				'default'       => 'no',
				'type'          => 'checkbox',
				'checkboxgroup' => 'end',
				'autoload'      => false
			),

			array( 'type' => 'sectionend', 'id' => 'account_registration_options'),

		)); // End pages settings
	}
}

endif;

return new WC_Settings_Accounts();
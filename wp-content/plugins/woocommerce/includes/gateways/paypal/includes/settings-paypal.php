<?php
/**
 * Settings for PayPal Standard Gateway.
 *
 * @package WooCommerce\Classes\Payment
 */

defined( 'ABSPATH' ) || exit;

return array(
	'enabled'               => array(
		'title'   => __( 'Enable/Disable', 'woocommerce' ),
		'type'    => 'checkbox',
		'label'   => __( 'Enable PayPal Standard', 'woocommerce' ),
		'default' => 'no',
	),
	'title'                 => array(
		'title'       => __( 'Title', 'woocommerce' ),
		'type'        => 'safe_text',
		'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
		'default'     => __( 'PayPal', 'woocommerce' ),
		'desc_tip'    => true,
	),
	'description'           => array(
		'title'       => __( 'Description', 'woocommerce' ),
		'type'        => 'text',
		'desc_tip'    => true,
		'description' => __( 'This controls the description which the user sees during checkout.', 'woocommerce' ),
		'default'     => __( "Pay via PayPal; you can pay with your credit card if you don't have a PayPal account.", 'woocommerce' ),
	),
	'email'                 => array(
		'title'       => __( 'PayPal email', 'woocommerce' ),
		'type'        => 'email',
		'description' => __( 'Please enter your PayPal email address; this is needed in order to take payment.', 'woocommerce' ),
		'default'     => get_option( 'admin_email' ),
		'desc_tip'    => true,
		'placeholder' => 'you@youremail.com',
	),
	'advanced'              => array(
		'title'       => __( 'Advanced options', 'woocommerce' ),
		'type'        => 'title',
		'description' => '',
	),
	'testmode'              => array(
		'title'       => __( 'PayPal sandbox', 'woocommerce' ),
		'type'        => 'checkbox',
		'label'       => __( 'Enable PayPal sandbox', 'woocommerce' ),
		'default'     => 'no',
		/* translators: %s: URL */
		'description' => sprintf( __( 'PayPal sandbox can be used to test payments. Sign up for a <a href="%s">developer account</a>.', 'woocommerce' ), 'https://developer.paypal.com/' ),
	),
	'debug'                 => array(
		'title'       => __( 'Debug log', 'woocommerce' ),
		'type'        => 'checkbox',
		'label'       => __( 'Enable logging', 'woocommerce' ),
		'default'     => 'no',
		/* translators: %s: URL */
		'description' => sprintf( __( 'Log PayPal events, such as IPN requests, inside %s Note: this may log personal information. We recommend using this for debugging purposes only and deleting the logs when finished.', 'woocommerce' ), '<code>' . WC_Log_Handler_File::get_log_file_path( 'paypal' ) . '</code>' ),
	),
	'ipn_notification'      => array(
		'title'       => __( 'IPN email notifications', 'woocommerce' ),
		'type'        => 'checkbox',
		'label'       => __( 'Enable IPN email notifications', 'woocommerce' ),
		'default'     => 'yes',
		'description' => __( 'Send notifications when an IPN is received from PayPal indicating refunds, chargebacks and cancellations.', 'woocommerce' ),
	),
	'receiver_email'        => array(
		'title'       => __( 'Receiver email', 'woocommerce' ),
		'type'        => 'email',
		'description' => __( 'If your main PayPal email differs from the PayPal email entered above, input your main receiver email for your PayPal account here. This is used to validate IPN requests.', 'woocommerce' ),
		'default'     => '',
		'desc_tip'    => true,
		'placeholder' => 'you@youremail.com',
	),
	'identity_token'        => array(
		'title'       => __( 'PayPal identity token', 'woocommerce' ),
		'type'        => 'text',
		'description' => __( 'Optionally enable "Payment Data Transfer" (Profile > Profile and Settings > My Selling Tools > Website Preferences) and then copy your identity token here. This will allow payments to be verified without the need for PayPal IPN.', 'woocommerce' ),
		'default'     => '',
		'desc_tip'    => true,
		'placeholder' => '',
	),
	'invoice_prefix'        => array(
		'title'       => __( 'Invoice prefix', 'woocommerce' ),
		'type'        => 'text',
		'description' => __( 'Please enter a prefix for your invoice numbers. If you use your PayPal account for multiple stores ensure this prefix is unique as PayPal will not allow orders with the same invoice number.', 'woocommerce' ),
		'default'     => 'WC-',
		'desc_tip'    => true,
	),
	'send_shipping'         => array(
		'title'       => __( 'Shipping details', 'woocommerce' ),
		'type'        => 'checkbox',
		'label'       => __( 'Send shipping details to PayPal instead of billing.', 'woocommerce' ),
		'description' => __( 'PayPal allows us to send one address. If you are using PayPal for shipping labels you may prefer to send the shipping address rather than billing. Turning this option off may prevent PayPal Seller protection from applying.', 'woocommerce' ),
		'default'     => 'yes',
	),
	'address_override'      => array(
		'title'       => __( 'Address override', 'woocommerce' ),
		'type'        => 'checkbox',
		'label'       => __( 'Enable "address_override" to prevent address information from being changed.', 'woocommerce' ),
		'description' => __( 'PayPal verifies addresses therefore this setting can cause errors (we recommend keeping it disabled).', 'woocommerce' ),
		'default'     => 'no',
	),
	'paymentaction'         => array(
		'title'       => __( 'Payment action', 'woocommerce' ),
		'type'        => 'select',
		'class'       => 'wc-enhanced-select',
		'description' => __( 'Choose whether you wish to capture funds immediately or authorize payment only.', 'woocommerce' ),
		'default'     => 'sale',
		'desc_tip'    => true,
		'options'     => array(
			'sale'          => __( 'Capture', 'woocommerce' ),
			'authorization' => __( 'Authorize', 'woocommerce' ),
		),
	),
	'image_url'             => array(
		'title'       => __( 'Image url', 'woocommerce' ),
		'type'        => 'text',
		'description' => __( 'Optionally enter the URL to a 150x50px image displayed as your logo in the upper left corner of the PayPal checkout pages.', 'woocommerce' ),
		'default'     => '',
		'desc_tip'    => true,
		'placeholder' => __( 'Optional', 'woocommerce' ),
	),
	'api_details'           => array(
		'title'       => __( 'API credentials', 'woocommerce' ),
		'type'        => 'title',
		/* translators: %s: URL */
		'description' => sprintf( __( 'Enter your PayPal API credentials to process refunds via PayPal. Learn how to access your <a href="%s">PayPal API Credentials</a>.', 'woocommerce' ), 'https://developer.paypal.com/webapps/developer/docs/classic/api/apiCredentials/#create-an-api-signature' ),
	),
	'api_username'          => array(
		'title'       => __( 'Live API username', 'woocommerce' ),
		'type'        => 'text',
		'description' => __( 'Get your API credentials from PayPal.', 'woocommerce' ),
		'default'     => '',
		'desc_tip'    => true,
		'placeholder' => __( 'Optional', 'woocommerce' ),
	),
	'api_password'          => array(
		'title'       => __( 'Live API password', 'woocommerce' ),
		'type'        => 'password',
		'description' => __( 'Get your API credentials from PayPal.', 'woocommerce' ),
		'default'     => '',
		'desc_tip'    => true,
		'placeholder' => __( 'Optional', 'woocommerce' ),
	),
	'api_signature'         => array(
		'title'       => __( 'Live API signature', 'woocommerce' ),
		'type'        => 'password',
		'description' => __( 'Get your API credentials from PayPal.', 'woocommerce' ),
		'default'     => '',
		'desc_tip'    => true,
		'placeholder' => __( 'Optional', 'woocommerce' ),
	),
	'sandbox_api_username'  => array(
		'title'       => __( 'Sandbox API username', 'woocommerce' ),
		'type'        => 'text',
		'description' => __( 'Get your API credentials from PayPal.', 'woocommerce' ),
		'default'     => '',
		'desc_tip'    => true,
		'placeholder' => __( 'Optional', 'woocommerce' ),
	),
	'sandbox_api_password'  => array(
		'title'       => __( 'Sandbox API password', 'woocommerce' ),
		'type'        => 'password',
		'description' => __( 'Get your API credentials from PayPal.', 'woocommerce' ),
		'default'     => '',
		'desc_tip'    => true,
		'placeholder' => __( 'Optional', 'woocommerce' ),
	),
	'sandbox_api_signature' => array(
		'title'       => __( 'Sandbox API signature', 'woocommerce' ),
		'type'        => 'password',
		'description' => __( 'Get your API credentials from PayPal.', 'woocommerce' ),
		'default'     => '',
		'desc_tip'    => true,
		'placeholder' => __( 'Optional', 'woocommerce' ),
	),
);

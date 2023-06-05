<?php
/**
 * WooCommerce Account Settings.
 *
 * @package WooCommerce\Admin
 */

defined( 'ABSPATH' ) || exit;

if ( class_exists( 'WC_Settings_Accounts', false ) ) {
	return new WC_Settings_Accounts();
}

/**
 * WC_Settings_Accounts.
 */
class WC_Settings_Accounts extends WC_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'account';
		$this->label = __( 'Accounts &amp; Privacy', 'woocommerce' );
		parent::__construct();
	}

	/**
	 * Get settings array.
	 *
	 * @return array
	 */
	protected function get_settings_for_default_section() {

		$erasure_text = esc_html__( 'account erasure request', 'woocommerce' );
		$privacy_text = esc_html__( 'privacy page', 'woocommerce' );
		if ( current_user_can( 'manage_privacy_options' ) ) {
			if ( version_compare( get_bloginfo( 'version' ), '5.3', '<' ) ) {
				$erasure_text = sprintf( '<a href="%s">%s</a>', esc_url( admin_url( 'tools.php?page=remove_personal_data' ) ), $erasure_text );
			} else {
				$erasure_text = sprintf( '<a href="%s">%s</a>', esc_url( admin_url( 'erase-personal-data.php' ) ), $erasure_text );
			}
			$privacy_text = sprintf( '<a href="%s">%s</a>', esc_url( admin_url( 'options-privacy.php' ) ), $privacy_text );
		}

		$account_settings = array(
			array(
				'title' => '',
				'type'  => 'title',
				'id'    => 'account_registration_options',
			),
			array(
				'title'         => __( 'Guest checkout', 'woocommerce' ),
				'desc'          => __( 'Allow customers to place orders without an account', 'woocommerce' ),
				'id'            => 'woocommerce_enable_guest_checkout',
				'default'       => 'yes',
				'type'          => 'checkbox',
				'checkboxgroup' => 'start',
				'autoload'      => false,
			),
			array(
				'title'         => __( 'Login', 'woocommerce' ),
				'desc'          => __( 'Allow customers to log into an existing account during checkout', 'woocommerce' ),
				'id'            => 'woocommerce_enable_checkout_login_reminder',
				'default'       => 'no',
				'type'          => 'checkbox',
				'checkboxgroup' => 'end',
				'autoload'      => false,
			),
			array(
				'title'         => __( 'Account creation', 'woocommerce' ),
				'desc'          => __( 'Allow customers to create an account during checkout', 'woocommerce' ),
				'id'            => 'woocommerce_enable_signup_and_login_from_checkout',
				'default'       => 'no',
				'type'          => 'checkbox',
				'checkboxgroup' => 'start',
				'autoload'      => false,
			),
			array(
				'desc'          => __( 'Allow customers to create an account on the "My account" page', 'woocommerce' ),
				'id'            => 'woocommerce_enable_myaccount_registration',
				'default'       => 'no',
				'type'          => 'checkbox',
				'checkboxgroup' => '',
				'autoload'      => false,
			),
			array(
				'desc'          => __( 'When creating an account, automatically generate an account username for the customer based on their name, surname or email', 'woocommerce' ),
				'id'            => 'woocommerce_registration_generate_username',
				'default'       => 'yes',
				'type'          => 'checkbox',
				'checkboxgroup' => '',
				'autoload'      => false,
			),
			array(
				'desc'          => __( 'When creating an account, send the new user a link to set their password', 'woocommerce' ),
				'id'            => 'woocommerce_registration_generate_password',
				'default'       => 'yes',
				'type'          => 'checkbox',
				'checkboxgroup' => 'end',
				'autoload'      => false,
			),
			array(
				'title'         => __( 'Account erasure requests', 'woocommerce' ),
				'desc'          => __( 'Remove personal data from orders on request', 'woocommerce' ),
				/* Translators: %s URL to erasure request screen. */
				'desc_tip'      => sprintf( esc_html__( 'When handling an %s, should personal data within orders be retained or removed?', 'woocommerce' ), $erasure_text ),
				'id'            => 'woocommerce_erasure_request_removes_order_data',
				'type'          => 'checkbox',
				'default'       => 'no',
				'checkboxgroup' => 'start',
				'autoload'      => false,
			),
			array(
				'desc'          => __( 'Remove access to downloads on request', 'woocommerce' ),
				/* Translators: %s URL to erasure request screen. */
				'desc_tip'      => sprintf( esc_html__( 'When handling an %s, should access to downloadable files be revoked and download logs cleared?', 'woocommerce' ), $erasure_text ),
				'id'            => 'woocommerce_erasure_request_removes_download_data',
				'type'          => 'checkbox',
				'default'       => 'no',
				'checkboxgroup' => 'end',
				'autoload'      => false,
			),
			array(
				'title'         => __( 'Personal data removal', 'woocommerce' ),
				'desc'          => __( 'Allow personal data to be removed in bulk from orders', 'woocommerce' ),
				'desc_tip'      => __( 'Adds an option to the orders screen for removing personal data in bulk. Note that removing personal data cannot be undone.', 'woocommerce' ),
				'id'            => 'woocommerce_allow_bulk_remove_personal_data',
				'type'          => 'checkbox',
				'checkboxgroup' => 'start',
				'default'       => 'no',
				'autoload'      => false,
			),
			array(
				'type' => 'sectionend',
				'id'   => 'account_registration_options',
			),
			array(
				'title' => __( 'Privacy policy', 'woocommerce' ),
				'type'  => 'title',
				'id'    => 'privacy_policy_options',
				/* translators: %s: privacy page link. */
				'desc'  => sprintf( esc_html__( 'This section controls the display of your website privacy policy. The privacy notices below will not show up unless a %s is set.', 'woocommerce' ), $privacy_text ),
			),

			array(
				'title'    => __( 'Registration privacy policy', 'woocommerce' ),
				'desc_tip' => __( 'Optionally add some text about your store privacy policy to show on account registration forms.', 'woocommerce' ),
				'id'       => 'woocommerce_registration_privacy_policy_text',
				/* translators: %s privacy policy page name and link */
				'default'  => sprintf( __( 'Your personal data will be used to support your experience throughout this website, to manage access to your account, and for other purposes described in our %s.', 'woocommerce' ), '[privacy_policy]' ),
				'type'     => 'textarea',
				'css'      => 'min-width: 50%; height: 75px;',
			),

			array(
				'title'    => __( 'Checkout privacy policy', 'woocommerce' ),
				'desc_tip' => __( 'Optionally add some text about your store privacy policy to show during checkout.', 'woocommerce' ),
				'id'       => 'woocommerce_checkout_privacy_policy_text',
				/* translators: %s privacy policy page name and link */
				'default'  => sprintf( __( 'Your personal data will be used to process your order, support your experience throughout this website, and for other purposes described in our %s.', 'woocommerce' ), '[privacy_policy]' ),
				'type'     => 'textarea',
				'css'      => 'min-width: 50%; height: 75px;',
			),
			array(
				'type' => 'sectionend',
				'id'   => 'privacy_policy_options',
			),
			array(
				'title' => __( 'Personal data retention', 'woocommerce' ),
				'desc'  => __( 'Choose how long to retain personal data when it\'s no longer needed for processing. Leave the following options blank to retain this data indefinitely.', 'woocommerce' ),
				'type'  => 'title',
				'id'    => 'personal_data_retention',
			),
			array(
				'title'       => __( 'Retain inactive accounts ', 'woocommerce' ),
				'desc_tip'    => __( 'Inactive accounts are those which have not logged in, or placed an order, for the specified duration. They will be deleted. Any orders will be converted into guest orders.', 'woocommerce' ),
				'id'          => 'woocommerce_delete_inactive_accounts',
				'type'        => 'relative_date_selector',
				'placeholder' => __( 'N/A', 'woocommerce' ),
				'default'     => array(
					'number' => '',
					'unit'   => 'months',
				),
				'autoload'    => false,
			),
			array(
				'title'       => __( 'Retain pending orders ', 'woocommerce' ),
				'desc_tip'    => __( 'Pending orders are unpaid and may have been abandoned by the customer. They will be trashed after the specified duration.', 'woocommerce' ),
				'id'          => 'woocommerce_trash_pending_orders',
				'type'        => 'relative_date_selector',
				'placeholder' => __( 'N/A', 'woocommerce' ),
				'default'     => '',
				'autoload'    => false,
			),
			array(
				'title'       => __( 'Retain failed orders', 'woocommerce' ),
				'desc_tip'    => __( 'Failed orders are unpaid and may have been abandoned by the customer. They will be trashed after the specified duration.', 'woocommerce' ),
				'id'          => 'woocommerce_trash_failed_orders',
				'type'        => 'relative_date_selector',
				'placeholder' => __( 'N/A', 'woocommerce' ),
				'default'     => '',
				'autoload'    => false,
			),
			array(
				'title'       => __( 'Retain cancelled orders', 'woocommerce' ),
				'desc_tip'    => __( 'Cancelled orders are unpaid and may have been cancelled by the store owner or customer. They will be trashed after the specified duration.', 'woocommerce' ),
				'id'          => 'woocommerce_trash_cancelled_orders',
				'type'        => 'relative_date_selector',
				'placeholder' => __( 'N/A', 'woocommerce' ),
				'default'     => '',
				'autoload'    => false,
			),
			array(
				'title'       => __( 'Retain completed orders', 'woocommerce' ),
				'desc_tip'    => __( 'Retain completed orders for a specified duration before anonymizing the personal data within them.', 'woocommerce' ),
				'id'          => 'woocommerce_anonymize_completed_orders',
				'type'        => 'relative_date_selector',
				'placeholder' => __( 'N/A', 'woocommerce' ),
				'default'     => array(
					'number' => '',
					'unit'   => 'months',
				),
				'autoload'    => false,
			),
			array(
				'type' => 'sectionend',
				'id'   => 'personal_data_retention',
			),
		);

		return apply_filters(
			'woocommerce_' . $this->id . '_settings',
			$account_settings
		);
	}
}

return new WC_Settings_Accounts();

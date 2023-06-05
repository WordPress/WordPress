<?php
namespace Automattic\WooCommerce\Blocks\Domain\Services\Email;

use Automattic\WooCommerce\Blocks\Domain\Package;

/**
 * Customer New Account.
 *
 * An email sent to the customer when they create an account.
 * This is intended as a replacement to \WC_Email_Customer_New_Account(),
 * with a set password link instead of emailing the new password in email
 * content.
 *
 * @extends     \WC_Email
 */
class CustomerNewAccount extends \WC_Email {

	/**
	 * User login name.
	 *
	 * @var string
	 */
	public $user_login;

	/**
	 * User email.
	 *
	 * @var string
	 */
	public $user_email;

	/**
	 * Magic link to set initial password.
	 *
	 * @var string
	 */
	public $set_password_url;

	/**
	 * Override (force) default template path
	 *
	 * @var string
	 */
	public $default_template_path;

	/**
	 * Constructor.
	 *
	 * @param Package $package An instance of (Woo Blocks) Package.
	 */
	public function __construct( Package $package ) {
		// Note - we're using the same ID as the real email.
		// This ensures that any merchant tweaks (Settings > Emails)
		// apply to this email (consistent with the core email).
		$this->id                    = 'customer_new_account';
		$this->customer_email        = true;
		$this->title                 = __( 'New account', 'woocommerce' );
		$this->description           = __( '“New Account” emails are sent when a customer signs up via the checkout flow.', 'woocommerce' );
		$this->template_html         = 'emails/customer-new-account-blocks.php';
		$this->template_plain        = 'emails/plain/customer-new-account-blocks.php';
		$this->default_template_path = $package->get_path( '/templates/' );

		// Call parent constructor.
		parent::__construct();
	}

	/**
	 * Get email subject.
	 *
	 * @since  3.1.0
	 * @return string
	 */
	public function get_default_subject() {
		return __( 'Your {site_title} account has been created!', 'woocommerce' );
	}

	/**
	 * Get email heading.
	 *
	 * @since  3.1.0
	 * @return string
	 */
	public function get_default_heading() {
		return __( 'Welcome to {site_title}', 'woocommerce' );
	}

	/**
	 * Trigger.
	 *
	 * @param int    $user_id User ID.
	 * @param string $user_pass User password.
	 * @param bool   $password_generated Whether the password was generated automatically or not.
	 */
	public function trigger( $user_id, $user_pass = '', $password_generated = false ) {
		$this->setup_locale();

		if ( $user_id ) {
			$this->object = new \WP_User( $user_id );

			// Generate a magic link so user can set initial password.
			$key = get_password_reset_key( $this->object );
			if ( ! is_wp_error( $key ) ) {
				$action                 = 'newaccount';
				$this->set_password_url = wc_get_account_endpoint_url( 'lost-password' ) . "?action=$action&key=$key&login=" . rawurlencode( $this->object->user_login );
			}

			$this->user_login = stripslashes( $this->object->user_login );
			$this->user_email = stripslashes( $this->object->user_email );
			$this->recipient  = $this->user_email;
		}

		if ( $this->is_enabled() && $this->get_recipient() ) {
			$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments(), $this->set_password_url );
		}

		$this->restore_locale();
	}

	/**
	 * Get content html.
	 *
	 * @return string
	 */
	public function get_content_html() {
		return wc_get_template_html(
			$this->template_html,
			array(
				'email_heading'      => $this->get_heading(),
				'additional_content' => $this->get_additional_content(),
				'user_login'         => $this->user_login,
				'blogname'           => $this->get_blogname(),
				'set_password_url'   => $this->set_password_url,
				'sent_to_admin'      => false,
				'plain_text'         => false,
				'email'              => $this,
			),
			'',
			$this->default_template_path
		);
	}

	/**
	 * Get content plain.
	 *
	 * @return string
	 */
	public function get_content_plain() {
		return wc_get_template_html(
			$this->template_plain,
			array(
				'email_heading'      => $this->get_heading(),
				'additional_content' => $this->get_additional_content(),
				'user_login'         => $this->user_login,
				'blogname'           => $this->get_blogname(),
				'set_password_url'   => $this->set_password_url,
				'sent_to_admin'      => false,
				'plain_text'         => true,
				'email'              => $this,
			),
			'',
			$this->default_template_path
		);
	}

	/**
	 * Default content to show below main email content.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_default_additional_content() {
		return __( 'We look forward to seeing you soon.', 'woocommerce' );
	}
}

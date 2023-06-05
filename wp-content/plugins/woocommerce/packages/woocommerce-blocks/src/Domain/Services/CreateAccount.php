<?php
namespace Automattic\WooCommerce\Blocks\Domain\Services;

use Automattic\WooCommerce\Blocks\Domain\Package;
use Automattic\WooCommerce\Blocks\Domain\Services\Email\CustomerNewAccount;

/**
 * Service class implementing new create account emails used for order processing via the Block Based Checkout.
 */
class CreateAccount {
	/**
	 * Reference to the Package instance
	 *
	 * @var Package
	 */
	private $package;

	/**
	 * Constructor.
	 *
	 * @param Package $package An instance of (Woo Blocks) Package.
	 */
	public function __construct( Package $package ) {
		$this->package = $package;
	}

	/**
	 * Init - register handlers for WooCommerce core email hooks.
	 */
	public function init() {
		// Override core email handlers to add our new improved "new account" email.
		add_action(
			'woocommerce_email',
			function ( $wc_emails_instance ) {
				// Remove core "new account" handler; we are going to replace it.
				remove_action( 'woocommerce_created_customer_notification', array( $wc_emails_instance, 'customer_new_account' ), 10, 3 );

				// Add custom "new account" handler.
				add_action(
					'woocommerce_created_customer_notification',
					function( $customer_id, $new_customer_data = array(), $password_generated = false ) use ( $wc_emails_instance ) {
						// If this is a block-based signup, send a new email with password reset link (no password in email).
						if ( isset( $new_customer_data['source'] ) && 'store-api' === $new_customer_data['source'] ) {
							$this->customer_new_account( $customer_id, $new_customer_data );
							return;
						}

						// Otherwise, trigger the existing legacy email (with new password inline).
						$wc_emails_instance->customer_new_account( $customer_id, $new_customer_data, $password_generated );
					},
					10,
					3
				);
			}
		);
	}

	/**
	 * Trigger new account email.
	 * This is intended as a replacement to WC_Emails::customer_new_account(),
	 * with a set password link instead of emailing the new password in email
	 * content.
	 *
	 * @param int   $customer_id       The ID of the new customer account.
	 * @param array $new_customer_data Assoc array of data for the new account.
	 */
	public function customer_new_account( $customer_id = 0, array $new_customer_data = array() ) {
		$new_account_email = new CustomerNewAccount( $this->package );
		$new_account_email->trigger( $customer_id, $new_customer_data );
	}
}

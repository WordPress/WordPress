<?php
/**
 * WooCommerce Admin: Payments reminder note.
 *
 * Adds a notes to complete the payment methods.
 */

namespace Automattic\WooCommerce\Internal\Admin\Notes;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\Notes\Note;
use Automattic\WooCommerce\Admin\Notes\NoteTraits;

/**
 * Onboarding_Payments.
 */
class OnboardingPayments {
	/**
	 * Note traits.
	 */
	use NoteTraits;

	/**
	 * Name of the note for use in the database.
	 */
	const NOTE_NAME = 'wc-admin-onboarding-payments-reminder';

	/**
	 * Get the note.
	 *
	 * @return Note
	 */
	public static function get_note() {
		// We want to show the note after five days.
		if ( ! self::is_wc_admin_active_in_date_range( 'week-1-4', 5 * DAY_IN_SECONDS ) ) {
			return;
		}

		// Check to see if any gateways have been added.
		$gateways         = WC()->payment_gateways->get_available_payment_gateways();
		$enabled_gateways = array_filter(
			$gateways,
			function( $gateway ) {
				return 'yes' === $gateway->enabled;
			}
		);
		if ( ! empty( $enabled_gateways ) ) {
			return;
		}

		$note = new Note();
		$note->set_title( __( 'Start accepting payments on your store!', 'woocommerce' ) );
		$note->set_content( __( 'Take payments with the provider that’s right for you - choose from 100+ payment gateways for WooCommerce.', 'woocommerce' ) );
		$note->set_type( Note::E_WC_ADMIN_NOTE_INFORMATIONAL );
		$note->set_name( self::NOTE_NAME );
		$note->set_content_data( (object) array() );
		$note->set_source( 'woocommerce-admin' );
		$note->add_action(
			'view-payment-gateways',
			__( 'Learn more', 'woocommerce' ),
			'https://woocommerce.com/product-category/woocommerce-extensions/payment-gateways/?utm_medium=product',
			Note::E_WC_ADMIN_NOTE_ACTIONED,
			true
		);
		return $note;
	}
}

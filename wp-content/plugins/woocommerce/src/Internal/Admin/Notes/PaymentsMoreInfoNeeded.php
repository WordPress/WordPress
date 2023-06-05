<?php
/**
 * WooCommerce Admin Payments More Info Needed Inbox Note Provider
 */

namespace Automattic\WooCommerce\Internal\Admin\Notes;

use Automattic\WooCommerce\Admin\Notes\Note;
use Automattic\WooCommerce\Admin\Notes\NoteTraits;
use Automattic\WooCommerce\Admin\PluginsHelper;

defined( 'ABSPATH' ) || exit;

/**
 * PaymentsMoreInfoNeeded
 */
class PaymentsMoreInfoNeeded {
	/**
	 * Note traits.
	 */
	use NoteTraits;

	/**
	 * Name of the note for use in the database.
	 */
	const NOTE_NAME = 'wc-admin-payments-more-info-needed';

	/**
	 * Should this note exist?
	 */
	public static function is_applicable() {
		return self::should_display_note();
	}

	/**
	 * Returns true if we should display the note.
	 *
	 * @return bool
	 */
	public static function should_display_note() {
		// If user has installed WCPay, don't show this note.
		$installed_plugins = PluginsHelper::get_installed_plugin_slugs();
		if ( in_array( 'woocommerce-payments', $installed_plugins, true ) ) {
			return false;
		}

		// User has dismissed the WCPay Welcome Page.
		if ( 'yes' !== get_option( 'wc_calypso_bridge_payments_dismissed', 'no' ) ) {
			return false;
		}

		// More than 30 days since viewing the welcome page.
		$exit_survey_timestamp = get_option( 'wc_pay_exit_survey_more_info_needed_timestamp', false );
		if ( ! $exit_survey_timestamp ||
			( time() - $exit_survey_timestamp < 30 * DAY_IN_SECONDS )
		) {
			return false;
		}

		return true;
	}

	/**
	 * Get the note.
	 *
	 * @return Note
	 */
	public static function get_note() {
		if ( ! self::should_display_note() ) {
			return;
		}
		$content = __( 'We recently asked you if you wanted more information about WooCommerce Payments. Run your business and manage your payments in one place with the solution built and supported by WooCommerce.', 'woocommerce' );

		$note = new Note();
		$note->set_title( __( 'Payments made simple with WooCommerce Payments', 'woocommerce' ) );
		$note->set_content( $content );
		$note->set_content_data( (object) array() );
		$note->set_type( Note::E_WC_ADMIN_NOTE_INFORMATIONAL );
		$note->set_name( self::NOTE_NAME );
		$note->set_source( 'woocommerce-admin' );
		$note->add_action( 'learn-more', __( 'Learn more here', 'woocommerce' ), 'https://woocommerce.com/payments/' );
		return $note;
	}
}

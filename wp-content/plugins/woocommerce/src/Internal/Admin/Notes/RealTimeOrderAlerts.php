<?php
/**
 * WooCommerce Admin Real Time Order Alerts Note.
 *
 * Adds a note to download the mobile app to monitor store activity.
 */

namespace Automattic\WooCommerce\Internal\Admin\Notes;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\Notes\Note;
use Automattic\WooCommerce\Admin\Notes\NoteTraits;

/**
 * Real_Time_Order_Alerts
 */
class RealTimeOrderAlerts {
	/**
	 * Note traits.
	 */
	use NoteTraits;

	/**
	 * Name of the note for use in the database.
	 */
	const NOTE_NAME = 'wc-admin-real-time-order-alerts';

	/**
	 * Get the note.
	 *
	 * @return Note
	 */
	public static function get_note() {
		// Only add this note if the store is 3 months old.
		if ( ! self::is_wc_admin_active_in_date_range( 'month-3-6' ) ) {
			return;
		}

		// Check that the previous mobile app note was not actioned.
		if ( MobileApp::has_note_been_actioned() ) {
			return;
		}

		$content = __( 'Get notifications about store activity, including new orders and product reviews directly on your mobile devices with the Woo app.', 'woocommerce' );

		$note = new Note();
		$note->set_title( __( 'Get real-time order alerts anywhere', 'woocommerce' ) );
		$note->set_content( $content );
		$note->set_content_data( (object) array() );
		$note->set_type( Note::E_WC_ADMIN_NOTE_INFORMATIONAL );
		$note->set_name( self::NOTE_NAME );
		$note->set_source( 'woocommerce-admin' );
		$note->add_action( 'learn-more', __( 'Learn more', 'woocommerce' ), 'https://woocommerce.com/mobile/?utm_source=inbox&utm_medium=product' );
		return $note;
	}
}

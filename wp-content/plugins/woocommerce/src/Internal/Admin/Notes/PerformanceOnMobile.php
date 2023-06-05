<?php
/**
 * WooCommerce Admin Performance on mobile note.
 *
 * Adds a note to download the mobile app, performance on mobile.
 */

namespace Automattic\WooCommerce\Internal\Admin\Notes;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\Notes\Note;
use Automattic\WooCommerce\Admin\Notes\NoteTraits;

/**
 * Performance_On_Mobile
 */
class PerformanceOnMobile {
	/**
	 * Note traits.
	 */
	use NoteTraits;

	/**
	 * Name of the note for use in the database.
	 */
	const NOTE_NAME = 'wc-admin-performance-on-mobile';

	/**
	 * Get the note.
	 *
	 * @return Note
	 */
	public static function get_note() {
		// Only add this note if this store is at least 9 months old.
		$nine_months_in_seconds = MONTH_IN_SECONDS * 9;
		if ( ! self::wc_admin_active_for( $nine_months_in_seconds ) ) {
			return;
		}

		// Check that the previous mobile app notes have not been actioned.
		if ( MobileApp::has_note_been_actioned() ) {
			return;
		}
		if ( RealTimeOrderAlerts::has_note_been_actioned() ) {
			return;
		}
		if ( ManageOrdersOnTheGo::has_note_been_actioned() ) {
			return;
		}

		$note = new Note();

		$note->set_title( __( 'Track your store performance on mobile', 'woocommerce' ) );
		$note->set_content( __( 'Monitor your sales and high performing products with the Woo app.', 'woocommerce' ) );
		$note->set_content_data( (object) array() );
		$note->set_type( Note::E_WC_ADMIN_NOTE_INFORMATIONAL );
		$note->set_name( self::NOTE_NAME );
		$note->set_source( 'woocommerce-admin' );
		$note->add_action(
			'learn-more',
			__( 'Learn more', 'woocommerce' ),
			'https://woocommerce.com/mobile/?utm_source=inbox&utm_medium=product'
		);

		return $note;
	}
}

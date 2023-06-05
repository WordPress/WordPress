<?php
/**
 * WooCommerce Admin Manage orders on the go note.
 *
 * Adds a note to download the mobile app to manage orders on the go.
 */

namespace Automattic\WooCommerce\Internal\Admin\Notes;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\Notes\Note;
use Automattic\WooCommerce\Admin\Notes\NoteTraits;

/**
 * Manage_Orders_On_The_Go
 */
class ManageOrdersOnTheGo {
	/**
	 * Note traits.
	 */
	use NoteTraits;

	/**
	 * Name of the note for use in the database.
	 */
	const NOTE_NAME = 'wc-admin-manage-orders-on-the-go';

	/**
	 * Get the note.
	 *
	 * @return Note|null
	 */
	public static function get_note() {
		// Only add this note if this store is at least 6 months old.
		if ( ! self::is_wc_admin_active_in_date_range( 'month-6+' ) ) {
			return;
		}

		// Check that the previous mobile app notes have not been actioned.
		if ( MobileApp::has_note_been_actioned() ) {
			return;
		}
		if ( RealTimeOrderAlerts::has_note_been_actioned() ) {
			return;
		}

		$note = new Note();

		$note->set_title( __( 'Manage your orders on the go', 'woocommerce' ) );
		$note->set_content( __( 'Look for orders, customer info, and process refunds in one click with the Woo app.', 'woocommerce' ) );
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

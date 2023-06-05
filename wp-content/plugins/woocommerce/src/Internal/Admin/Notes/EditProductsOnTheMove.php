<?php
/**
 * WooCommerce Admin Edit products on the move note.
 *
 * Adds a note to download the mobile app.
 */

namespace Automattic\WooCommerce\Internal\Admin\Notes;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\Notes\Note;
use Automattic\WooCommerce\Admin\Notes\NoteTraits;

/**
 * Edit_Products_On_The_Move
 */
class EditProductsOnTheMove {
	/**
	 * Note traits.
	 */
	use NoteTraits;

	/**
	 * Name of the note for use in the database.
	 */
	const NOTE_NAME = 'wc-admin-edit-products-on-the-move';

	/**
	 * Get the note.
	 *
	 * @return Note
	 */
	public static function get_note() {
		// Only add this note if this store is at least a year old.
		$year_in_seconds = 365 * DAY_IN_SECONDS;
		if ( ! self::wc_admin_active_for( $year_in_seconds ) ) {
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
		if ( PerformanceOnMobile::has_note_been_actioned() ) {
			return;
		}

		$note = new Note();

		$note->set_title( __( 'Edit products on the move', 'woocommerce' ) );
		$note->set_content( __( 'Edit and create new products from your mobile devices with the Woo app', 'woocommerce' ) );
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

<?php
/**
 * WooCommerce Admin (Dashboard) choosing a theme note
 *
 * Adds notes to the merchant's inbox about choosing a theme.
 */

namespace Automattic\WooCommerce\Internal\Admin\Notes;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\Notes\Note;
use Automattic\WooCommerce\Admin\Notes\NoteTraits;

/**
 * Giving_Feedback_Notes
 */
class ChoosingTheme {
	/**
	 * Note traits.
	 */
	use NoteTraits;

	/**
	 * Name of the note for use in the database.
	 */
	const NOTE_NAME = 'wc-admin-choosing-a-theme';

	/**
	 * Get the note.
	 *
	 * @return Note
	 */
	public static function get_note() {
		// We need to show choosing a theme notification after 1 day of install.
		if ( ! self::is_wc_admin_active_in_date_range( 'week-1', DAY_IN_SECONDS ) ) {
			return;
		}

		// Otherwise, create our new note.
		$note = new Note();
		$note->set_title( __( 'Choosing a theme?', 'woocommerce' ) );
		$note->set_content( __( 'Check out the themes that are compatible with WooCommerce and choose one aligned with your brand and business needs.', 'woocommerce' ) );
		$note->set_content_data( (object) array() );
		$note->set_type( Note::E_WC_ADMIN_NOTE_MARKETING );
		$note->set_name( self::NOTE_NAME );
		$note->set_source( 'woocommerce-admin' );
		$note->add_action(
			'visit-the-theme-marketplace',
			__( 'Visit the theme marketplace', 'woocommerce' ),
			'https://woocommerce.com/product-category/themes/?utm_source=inbox&utm_medium=product'
		);
		return $note;
	}
}

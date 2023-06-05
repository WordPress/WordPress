<?php
/**
 * Refund and Returns Policy Page Note Provider.
 *
 * Adds notes to the merchant's inbox concerning the created page.
 *
 * @package WooCommerce
 */

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\Notes\Note;

/**
 * WC_Notes_Refund_Returns.
 */
class WC_Notes_Refund_Returns {
	/**
	 * Name of the note for use in the database.
	 */
	const NOTE_NAME = 'wc-refund-returns-page';

	/**
	 * Attach hooks.
	 */
	public static function init() {
		add_filter( 'woocommerce_get_note_from_db', array( __CLASS__, 'get_note_from_db' ), 10, 1 );
	}

	/**
	 * Maybe add a note to the inbox.
	 *
	 * @param int $page_id The ID of the page.
	 */
	public static function possibly_add_note( $page_id ) {
		$data_store = \WC_Data_Store::load( 'admin-note' );

		// Do we already have this note?
		$note_id = $data_store->get_notes_with_name( self::NOTE_NAME );

		if ( ! empty( $note_id ) ) {
			$note = new Note( $note_id );

			if ( false !== $note || $note::E_WC_ADMIN_NOTE_ACTIONED === $note->get_status() ) {
				// note actioned -> don't show it.
				return;
			}
		}

		// Add note.
		$note = self::get_note( $page_id );
		$note->save();
		delete_option( 'woocommerce_refund_returns_page_created' );
	}

	/**
	 * Get the note.
	 *
	 * @param int $page_id The ID of the page.
	 * @return object $note The note object.
	 */
	public static function get_note( $page_id ) {
		$note = new Note();
		$note->set_title( __( 'Setup a Refund and Returns Policy page to boost your store\'s credibility.', 'woocommerce' ) );
		$note->set_content( __( 'We have created a sample draft Refund and Returns Policy page for you. Please have a look and update it to fit your store.', 'woocommerce' ) );
		$note->set_type( Note::E_WC_ADMIN_NOTE_INFORMATIONAL );
		$note->set_name( self::NOTE_NAME );
		$note->set_content_data( (object) array() );
		$note->set_source( 'woocommerce-core' );
		$note->add_action(
			'notify-refund-returns-page',
			__( 'Edit page', 'woocommerce' ),
			admin_url( sprintf( 'post.php?post=%d&action=edit', (int) $page_id ) )
		);

		return $note;
	}

	/**
	 * Get the note.
	 *
	 * @param Note $note_from_db The note object from the database.
	 * @return Note $note The note object.
	 */
	public static function get_note_from_db( $note_from_db ) {
		if ( ! $note_from_db instanceof Note || get_user_locale() === $note_from_db->get_locale() ) {
			return $note_from_db;
		}

		if ( self::NOTE_NAME === $note_from_db->get_name() ) {
			$note = self::get_note( 0 );
			$note_from_db->set_title( $note->get_title() );
			$note_from_db->set_content( $note->get_content() );

			$action_from_db    = $note_from_db->get_action( 'notify-refund-returns-page' );
			$action_from_class = $note->get_action( 'notify-refund-returns-page' );

			if ( $action_from_db && $action_from_class ) {
				$action_from_db->label = $action_from_class->label;
				$note_from_db->set_actions( array( $action_from_db ) );
			}
		}

		return $note_from_db;
	}
}

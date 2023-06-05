<?php
/**
 * WooCommerce Admin Personalize Your Store Note Provider.
 *
 * Adds a note to the merchant's inbox prompting them to personalize their store.
 */

namespace Automattic\WooCommerce\Internal\Admin\Notes;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\Notes\Note;
use Automattic\WooCommerce\Admin\Notes\NoteTraits;

/**
 * Personalize_Store
 */
class PersonalizeStore {
	/**
	 * Note traits.
	 */
	use NoteTraits;

	/**
	 * Name of the note for use in the database.
	 */
	const NOTE_NAME = 'wc-admin-personalize-store';

	/**
	 * Get the note.
	 *
	 * @return Note
	 */
	public static function get_note() {
		// Only show the note to stores with homepage.
		$homepage_id = get_option( 'woocommerce_onboarding_homepage_post_id', false );
		if ( ! $homepage_id ) {
			return;
		}

		// Show the note after task list is done.
		$is_task_list_complete = get_option( 'woocommerce_task_list_complete', false );

		// We want to show the note after day 5.
		$five_days_in_seconds = 5 * DAY_IN_SECONDS;

		if ( ! self::is_wc_admin_active_in_date_range( 'week-1-4', $five_days_in_seconds ) && ! $is_task_list_complete ) {
			return;
		}

		$content = __( 'The homepage is one of the most important entry points in your store. When done right it can lead to higher conversions and engagement. Don\'t forget to personalize the homepage that we created for your store during the onboarding.', 'woocommerce' );

		$note = new Note();
		$note->set_title( __( 'Personalize your store\'s homepage', 'woocommerce' ) );
		$note->set_content( $content );
		$note->set_content_data( (object) array() );
		$note->set_type( Note::E_WC_ADMIN_NOTE_INFORMATIONAL );
		$note->set_name( self::NOTE_NAME );
		$note->set_source( 'woocommerce-admin' );
		$note->add_action( 'personalize-homepage', __( 'Personalize homepage', 'woocommerce' ), admin_url( 'post.php?post=' . $homepage_id . '&action=edit' ), Note::E_WC_ADMIN_NOTE_ACTIONED );
		return $note;
	}
}

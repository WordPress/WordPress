<?php
/**
 * WooCommerce Admin Launch Checklist Note.
 *
 * Adds a note to cover pre-launch checklist items for store owners.
 */

namespace Automattic\WooCommerce\Internal\Admin\Notes;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\Notes\Note;
use Automattic\WooCommerce\Admin\Notes\NoteTraits;

/**
 * Launch_Checklist
 */
class LaunchChecklist {
	/**
	 * Note traits.
	 */
	use NoteTraits;

	/**
	 * Name of the note for use in the database.
	 */
	const NOTE_NAME = 'wc-admin-launch-checklist';

	/**
	 * Get the note.
	 *
	 * @return Note
	 */
	public static function get_note() {
		// Only add this note if completing the task list or completed 3 tasks in 10 days.
		$completed_tasks     = get_option( 'woocommerce_task_list_tracked_completed_tasks', array() );
		$ten_days_in_seconds = 10 * DAY_IN_SECONDS;
		if (
			! get_option( 'woocommerce_task_list_complete' ) &&
			(
				count( $completed_tasks ) < 3 ||
				self::is_wc_admin_active_in_date_range( 'week-1-4', $ten_days_in_seconds )
			)
		) {
			return;
		}

		$content = __( 'To make sure you never get that sinking "what did I forget" feeling, we\'ve put together the essential pre-launch checklist.', 'woocommerce' );

		$note = new Note();
		$note->set_title( __( 'Ready to launch your store?', 'woocommerce' ) );
		$note->set_content( $content );
		$note->set_content_data( (object) array() );
		$note->set_type( Note::E_WC_ADMIN_NOTE_INFORMATIONAL );
		$note->set_name( self::NOTE_NAME );
		$note->set_source( 'woocommerce-admin' );
		$note->add_action( 'learn-more', __( 'Learn more', 'woocommerce' ), 'https://woocommerce.com/posts/pre-launch-checklist-the-essentials/?utm_source=inbox&utm_medium=product' );
		return $note;
	}
}

<?php
/**
 * WC Admin Note Traits
 *
 * WC Admin Note Traits class that houses shared functionality across notes.
 */

namespace Automattic\WooCommerce\Admin\Notes;

use Automattic\WooCommerce\Admin\WCAdminHelper;

defined( 'ABSPATH' ) || exit;

/**
 * NoteTraits class.
 */
trait NoteTraits {
	/**
	 * Test how long WooCommerce Admin has been active.
	 *
	 * @param int $seconds Time in seconds to check.
	 * @return bool Whether or not WooCommerce admin has been active for $seconds.
	 */
	private static function wc_admin_active_for( $seconds ) {
		return WCAdminHelper::is_wc_admin_active_for( $seconds );
	}

	/**
	 * Test if WooCommerce Admin has been active within a pre-defined range.
	 *
	 * @param string $range range available in WC_ADMIN_STORE_AGE_RANGES.
	 * @param int    $custom_start custom start in range.
	 * @return bool Whether or not WooCommerce admin has been active within the range.
	 */
	private static function is_wc_admin_active_in_date_range( $range, $custom_start = null ) {
		return WCAdminHelper::is_wc_admin_active_in_date_range( $range, $custom_start );
	}

	/**
	 * Check if the note has been previously added.
	 *
	 * @throws NotesUnavailableException Throws exception when notes are unavailable.
	 */
	public static function note_exists() {
		$data_store = Notes::load_data_store();
		$note_ids   = $data_store->get_notes_with_name( self::NOTE_NAME );
		return ! empty( $note_ids );
	}

	/**
	 * Checks if a note can and should be added.
	 *
	 * @return bool
	 * @throws NotesUnavailableException Throws exception when notes are unavailable.
	 */
	public static function can_be_added() {
		$note = self::get_note();

		if ( ! $note instanceof Note && ! $note instanceof WC_Admin_Note ) {
			return;
		}

		if ( self::note_exists() ) {
			return false;
		}

		if (
			'no' === get_option( 'woocommerce_show_marketplace_suggestions', 'yes' ) &&
			Note::E_WC_ADMIN_NOTE_MARKETING === $note->get_type()
		) {
			return false;
		}

		return true;
	}

	/**
	 * Add the note if it passes predefined conditions.
	 *
	 * @throws NotesUnavailableException Throws exception when notes are unavailable.
	 */
	public static function possibly_add_note() {
		$note = self::get_note();

		if ( ! self::can_be_added() ) {
			return;
		}

		$note->save();
	}

	/**
	 * Alias this method for backwards compatibility.
	 *
	 * @throws NotesUnavailableException Throws exception when notes are unavailable.
	 */
	public static function add_note() {
		self::possibly_add_note();
	}

	/**
	 * Should this note exist? (Default implementation is generous. Override as needed.)
	 */
	public static function is_applicable() {
		return true;
	}

	/**
	 * Delete this note if it is not applicable, unless has been soft-deleted or actioned already.
	 */
	public static function delete_if_not_applicable() {
		if ( ! self::is_applicable() ) {
			$data_store = Notes::load_data_store();
			$note_ids   = $data_store->get_notes_with_name( self::NOTE_NAME );

			if ( ! empty( $note_ids ) ) {
				$note = Notes::get_note( $note_ids[0] );

				if ( ! $note->get_is_deleted() && ( Note::E_WC_ADMIN_NOTE_ACTIONED !== $note->get_status() ) ) {
					return self::possibly_delete_note();
				}
			}
		}
	}

	/**
	 * Possibly delete the note, if it exists in the database. Note that this
	 * is a hard delete, for where it doesn't make sense to soft delete or
	 * action the note.
	 *
	 * @throws NotesUnavailableException Throws exception when notes are unavailable.
	 */
	public static function possibly_delete_note() {
		$data_store = Notes::load_data_store();
		$note_ids   = $data_store->get_notes_with_name( self::NOTE_NAME );

		foreach ( $note_ids as $note_id ) {
			$note = Notes::get_note( $note_id );

			if ( $note ) {
				$data_store->delete( $note );
			}
		}
	}


	/**
	 * Update the note if it passes predefined conditions.
	 *
	 * @throws NotesUnavailableException Throws exception when notes are unavailable.
	 */
	public static function possibly_update_note() {
		$note_in_db = Notes::get_note_by_name( self::NOTE_NAME );
		if ( ! $note_in_db ) {
			return;
		}

		if ( ! method_exists( self::class, 'get_note' ) ) {
			return;
		}

		$note = self::get_note();
		if ( ! $note instanceof Note && ! $note instanceof WC_Admin_Note ) {
			return;
		}

		$need_save = in_array(
			true,
			array(
				self::update_note_field_if_changed( $note_in_db, $note, 'title' ),
				self::update_note_field_if_changed( $note_in_db, $note, 'content' ),
				self::update_note_field_if_changed( $note_in_db, $note, 'content_data' ),
				self::update_note_field_if_changed( $note_in_db, $note, 'type' ),
				self::update_note_field_if_changed( $note_in_db, $note, 'locale' ),
				self::update_note_field_if_changed( $note_in_db, $note, 'source' ),
				self::update_note_field_if_changed( $note_in_db, $note, 'actions' )
			),
			true
		);

		if ( $need_save ) {
			$note_in_db->save();
		}
	}


	/**
	 * Get if the note has been actioned.
	 *
	 * @return bool
	 * @throws NotesUnavailableException Throws exception when notes are unavailable.
	 */
	public static function has_note_been_actioned() {
		$data_store = Notes::load_data_store();
		$note_ids   = $data_store->get_notes_with_name( self::NOTE_NAME );

		if ( ! empty( $note_ids ) ) {
			$note = Notes::get_note( $note_ids[0] );

			if ( Note::E_WC_ADMIN_NOTE_ACTIONED === $note->get_status() ) {
				return true;
			}
		}

		return false;
	}

/**
	 * Update a note field of note1 if it's different from note2 with getter and setter.
	 *
	 * @param Note   $note1 Note to update.
	 * @param Note   $note2 Note to compare against.
	 * @param string $field_name Field to update.
	 * @return bool True if the field was updated.
	 */
	private static function update_note_field_if_changed( $note1, $note2, $field_name ) {
		// We need to serialize the stdObject to compare it.
		$note1_field_value = self::possibly_convert_object_to_array(
			call_user_func( array( $note1, 'get_' . $field_name ) )
		);
		$note2_field_value = self::possibly_convert_object_to_array(
			call_user_func( array( $note2, 'get_' . $field_name ) )
		);

		if ( 'actions' === $field_name ) {
			// We need to individually compare the action fields because action object from db is different from action object of note.
			// For example, action object from db has "id".
			$diff        = array_udiff(
				$note1_field_value,
				$note2_field_value,
				function( $action1, $action2 ) {
					if ( $action1->name === $action2->name &&
						$action1->label === $action2->label &&
						$action1->query === $action2->query ) {
						return 0;
					}
					return -1;
				}
			);
			$need_update = count( $diff ) > 0;
		} else {
			$need_update = $note1_field_value !== $note2_field_value;
		}

		if ( $need_update ) {
			call_user_func(
				array( $note1, 'set_' . $field_name ),
				// Get note2 field again because it may have been changed during the comparison.
				call_user_func( array( $note2, 'get_' . $field_name ) )
			);
			return true;
		}
		return false;
	}

	/**
	 * Convert a value to array if it's a stdClass.
	 *
	 * @param mixed $obj variable to convert.
	 * @return mixed
	 */
	private static function possibly_convert_object_to_array( $obj ) {
		if ( $obj instanceof \stdClass ) {
			return (array) $obj;
		}
		return $obj;
	}
}

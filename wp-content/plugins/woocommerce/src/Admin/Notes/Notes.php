<?php
/**
 * Handles storage and retrieval of admin notes
 */

namespace Automattic\WooCommerce\Admin\Notes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Admin Notes class.
 */
class Notes {
	/**
	 * Hook used for recurring "unsnooze" action.
	 */
	const UNSNOOZE_HOOK = 'wc_admin_unsnooze_admin_notes';

	/**
	 * Hook appropriate actions.
	 */
	public static function init() {
		add_action( 'admin_init', array( __CLASS__, 'schedule_unsnooze_notes' ) );
		add_action( 'admin_init', array( __CLASS__, 'possibly_delete_survey_notes' ) );
		add_action( 'update_option_woocommerce_show_marketplace_suggestions', array( __CLASS__, 'possibly_delete_marketing_notes' ), 10, 2 );
	}

	/**
	 * Get notes from the database.
	 *
	 * @param string $context Getting notes for what context. Valid values: view, edit.
	 * @param array  $args Arguments to pass to the query( e.g. per_page and page).
	 * @return array Array of arrays.
	 */
	public static function get_notes( $context = 'edit', $args = array() ) {
		$data_store = self::load_data_store();
		$raw_notes  = $data_store->get_notes( $args );
		$notes      = array();
		foreach ( (array) $raw_notes as $raw_note ) {
			try {
				$note = new Note( $raw_note );
				/**
				 * Filter the note from db. This is used to modify the note before it is returned.
				 *
				 * @since 6.9.0
				 * @param Note $note The note object from the database.
				 */
				$note                               = apply_filters( 'woocommerce_get_note_from_db', $note );
				$note_id                            = $note->get_id();
				$notes[ $note_id ]                  = $note->get_data();
				$notes[ $note_id ]['name']          = $note->get_name( $context );
				$notes[ $note_id ]['type']          = $note->get_type( $context );
				$notes[ $note_id ]['locale']        = $note->get_locale( $context );
				$notes[ $note_id ]['title']         = $note->get_title( $context );
				$notes[ $note_id ]['content']       = $note->get_content( $context );
				$notes[ $note_id ]['content_data']  = $note->get_content_data( $context );
				$notes[ $note_id ]['status']        = $note->get_status( $context );
				$notes[ $note_id ]['source']        = $note->get_source( $context );
				$notes[ $note_id ]['date_created']  = $note->get_date_created( $context );
				$notes[ $note_id ]['date_reminder'] = $note->get_date_reminder( $context );
				$notes[ $note_id ]['actions']       = $note->get_actions( $context );
				$notes[ $note_id ]['layout']        = $note->get_layout( $context );
				$notes[ $note_id ]['image']         = $note->get_image( $context );
				$notes[ $note_id ]['is_deleted']    = $note->get_is_deleted( $context );
			} catch ( \Exception $e ) {
				wc_caught_exception( $e, __CLASS__ . '::' . __FUNCTION__, array( $note_id ) );
			}
		}
		return $notes;
	}

	/**
	 * Get admin note using it's ID
	 *
	 * @param int $note_id Note ID.
	 * @return Note|bool
	 */
	public static function get_note( $note_id ) {
		if ( false !== $note_id ) {
			try {
				return new Note( $note_id );
			} catch ( \Exception $e ) {
				wc_caught_exception( $e, __CLASS__ . '::' . __FUNCTION__, array( $note_id ) );
				return false;
			}
		}

		return false;
	}

	/**
	 * Get admin note using its name.
	 *
	 * This is a shortcut for the common pattern of looking up note ids by name and then passing the first id to get_note().
	 * It will behave unpredictably when more than one note with the given name exists.
	 *
	 * @param string $note_name Note name.
	 * @return Note|bool
	 **/
	public static function get_note_by_name( $note_name ) {
		$data_store = self::load_data_store();
		$note_ids   = $data_store->get_notes_with_name( $note_name );

		if ( empty( $note_ids ) ) {
			return false;
		}

		return self::get_note( $note_ids[0] );
	}

	/**
	 * Get the total number of notes
	 *
	 * @param string $type Comma separated list of note types.
	 * @param string $status Comma separated list of statuses.
	 * @return int
	 */
	public static function get_notes_count( $type = array(), $status = array() ) {
		$data_store = self::load_data_store();
		return $data_store->get_notes_count( $type, $status );
	}

	/**
	 * Deletes admin notes with a given name.
	 *
	 * @param string|array $names Name(s) to search for.
	 */
	public static function delete_notes_with_name( $names ) {
		if ( is_string( $names ) ) {
			$names = array( $names );
		} elseif ( ! is_array( $names ) ) {
			return;
		}

		$data_store = self::load_data_store();

		foreach ( $names as $name ) {
			$note_ids = $data_store->get_notes_with_name( $name );
			foreach ( (array) $note_ids as $note_id ) {
				$note = self::get_note( $note_id );
				if ( $note ) {
					$note->delete();
				}
			}
		}
	}

	/**
	 * Update a note.
	 *
	 * @param Note  $note              The note that will be updated.
	 * @param array $requested_updates a list of requested updates.
	 */
	public static function update_note( $note, $requested_updates ) {
		$note_changed = false;
		if ( isset( $requested_updates['status'] ) ) {
			$note->set_status( $requested_updates['status'] );
			$note_changed = true;
		}

		if ( isset( $requested_updates['date_reminder'] ) ) {
			$note->set_date_reminder( $requested_updates['date_reminder'] );
			$note_changed = true;
		}

		if ( isset( $requested_updates['is_deleted'] ) ) {
			$note->set_is_deleted( $requested_updates['is_deleted'] );
			$note_changed = true;
		}

		if ( isset( $requested_updates['is_read'] ) ) {
			$note->set_is_read( $requested_updates['is_read'] );
			$note_changed = true;
		}

		if ( $note_changed ) {
			$note->save();
		}
	}

	/**
	 * Soft delete of a note.
	 *
	 * @param Note $note The note that will be deleted.
	 */
	public static function delete_note( $note ) {
		$note->set_is_deleted( 1 );
		$note->save();
	}

	/**
	 * Soft delete of all the admin notes. Returns the deleted items.
	 *
	 * @param array $args Arguments to pass to the query (ex: status).
	 * @return array Array of notes.
	 */
	public static function delete_all_notes( $args = array() ) {
		$data_store = self::load_data_store();
		$defaults   = array(
			'order'      => 'desc',
			'orderby'    => 'date_created',
			'per_page'   => 25,
			'page'       => 1,
			'type'       => array(
				Note::E_WC_ADMIN_NOTE_INFORMATIONAL,
				Note::E_WC_ADMIN_NOTE_MARKETING,
				Note::E_WC_ADMIN_NOTE_WARNING,
				Note::E_WC_ADMIN_NOTE_SURVEY,
			),
			'is_deleted' => 0,
		);
		$args       = wp_parse_args( $args, $defaults );
		// Here we filter for the same params we are using to show the note list in client side.
		$raw_notes = $data_store->get_notes( $args );

		$notes = array();
		foreach ( (array) $raw_notes as $raw_note ) {
			$note = self::get_note( $raw_note->note_id );
			if ( $note ) {
				self::delete_note( $note );
				array_push( $notes, $note );
			}
		}
		return $notes;
	}

	/**
	 * Clear note snooze status if the reminder date has been reached.
	 */
	public static function unsnooze_notes() {
		$data_store = self::load_data_store();
		$raw_notes  = $data_store->get_notes(
			array(
				'status' => array( Note::E_WC_ADMIN_NOTE_SNOOZED ),
			)
		);
		$now        = new \DateTime();

		foreach ( $raw_notes as $raw_note ) {
			$note = self::get_note( $raw_note->note_id );
			if ( false === $note ) {
				continue;
			}

			$date_reminder = $note->get_date_reminder( 'edit' );

			if ( $date_reminder < $now ) {
				$note->set_status( Note::E_WC_ADMIN_NOTE_UNACTIONED );
				$note->set_date_reminder( null );
				$note->save();
			}
		}
	}

	/**
	 * Schedule unsnooze notes event.
	 */
	public static function schedule_unsnooze_notes() {
		if ( ! wp_next_scheduled( self::UNSNOOZE_HOOK ) ) {
			wp_schedule_event( time() + 5, 'hourly', self::UNSNOOZE_HOOK );
		}
	}

	/**
	 * Unschedule unsnooze notes event.
	 */
	public static function clear_queued_actions() {
		wp_clear_scheduled_hook( self::UNSNOOZE_HOOK );
	}

	/**
	 * Delete marketing notes if marketing has been opted out.
	 *
	 * @param string $old_value Old value.
	 * @param string $value New value.
	 */
	public static function possibly_delete_marketing_notes( $old_value, $value ) {
		if ( 'no' !== $value ) {
			return;
		}

		$data_store = self::load_data_store();
		$note_ids   = $data_store->get_note_ids_by_type( Note::E_WC_ADMIN_NOTE_MARKETING );

		foreach ( $note_ids as $note_id ) {
			$note = self::get_note( $note_id );
			if ( $note ) {
				$note->delete();
			}
		}
	}

	/**
	 * Delete actioned survey notes.
	 */
	public static function possibly_delete_survey_notes() {
		$data_store = self::load_data_store();
		$note_ids   = $data_store->get_note_ids_by_type( Note::E_WC_ADMIN_NOTE_SURVEY );

		foreach ( $note_ids as $note_id ) {
			$note = self::get_note( $note_id );
			if ( $note && ( $note->get_status() === Note::E_WC_ADMIN_NOTE_ACTIONED ) ) {
				$note->set_is_deleted( 1 );
				$note->save();
			}
		}
	}

	/**
	 * Get the status of a given note by name.
	 *
	 * @param string $note_name Name of the note.
	 * @return string|bool The note status.
	 */
	public static function get_note_status( $note_name ) {
		$note = self::get_note_by_name( $note_name );

		if ( ! $note ) {
			return false;
		}

		return $note->get_status();
	}

	/**
	 * Get action by id.
	 *
	 * @param Note $note The note that has of the action.
	 * @param int  $action_id Action ID.
	 * @return object|bool The found action.
	 */
	public static function get_action_by_id( $note, $action_id ) {
		$actions      = $note->get_actions( 'edit' );
		$found_action = false;

		foreach ( $actions as $action ) {
			if ( $action->id === $action_id ) {
				$found_action = $action;
			}
		}
		return $found_action;
	}

	/**
	 * Trigger note action.
	 *
	 * @param Note   $note The note that has the triggered action.
	 * @param object $triggered_action The triggered action.
	 * @return Note|bool
	 */
	public static function trigger_note_action( $note, $triggered_action ) {
		/**
		 * Fires when an admin note action is taken.
		 *
		 * @param string $name The triggered action name.
		 * @param Note   $note The corresponding Note.
		 */
		do_action( 'woocommerce_note_action', $triggered_action->name, $note );

		/**
		 * Fires when an admin note action is taken.
		 * For more specific targeting of note actions.
		 *
		 * @param Note $note The corresponding Note.
		 */
		do_action( 'woocommerce_note_action_' . $triggered_action->name, $note );

		// Update the note with the status for this action.
		if ( ! empty( $triggered_action->status ) ) {
			$note->set_status( $triggered_action->status );
		}

		$note->save();

		$event_params = array(
			'note_name'    => $note->get_name(),
			'note_type'    => $note->get_type(),
			'note_title'   => $note->get_title(),
			'note_content' => $note->get_content(),
			'action_name'  => $triggered_action->name,
			'action_label' => $triggered_action->label,
			'screen'       => self::get_screen_name(),
		);

		if ( in_array( $note->get_type(), array( 'error', 'update' ), true ) ) {
			wc_admin_record_tracks_event( 'store_alert_action', $event_params );
		} else {
			self::record_tracks_event_without_cookies( 'inbox_action_click', $event_params );
		}

		return $note;
	}

	/**
	 * Record tracks event for a specific user.
	 *
	 * @param int    $user_id The user id we want to record for the event.
	 * @param string $event_name Name of the event to record.
	 * @param array  $params The params to send to the event recording.
	 */
	public static function record_tracks_event_with_user( $user_id, $event_name, $params ) {
		// We save the current user id to set it back after the event recording.
		$current_user_id = get_current_user_id();

		wp_set_current_user( $user_id );
		self::record_tracks_event_without_cookies( $event_name, $params );
		wp_set_current_user( $current_user_id );

	}

	/**
	 * Record tracks event without using cookies.
	 *
	 * @param string $event_name Name of the event to record.
	 * @param array  $params The params to send to the event recording.
	 */
	private static function record_tracks_event_without_cookies( $event_name, $params ) {
		// We save the cookie to set it back after the event recording.
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$anon_id = isset( $_COOKIE['tk_ai'] ) ? $_COOKIE['tk_ai'] : null;

		unset( $_COOKIE['tk_ai'] );
		wc_admin_record_tracks_event( $event_name, $params );
		if ( isset( $anon_id ) ) {
			setcookie( 'tk_ai', $anon_id );
		}
	}

	/**
	 * Get screen name.
	 *
	 * @return string The screen name.
	 */
	public static function get_screen_name() {
		$screen_name = '';

		if ( isset( $_SERVER['HTTP_REFERER'] ) ) {
			parse_str( wp_parse_url( $_SERVER['HTTP_REFERER'], PHP_URL_QUERY ), $queries ); // phpcs:ignore sanitization ok.
		}
		if ( isset( $queries ) ) {
			$page      = isset( $queries['page'] ) ? $queries['page'] : null;
			$path      = isset( $queries['path'] ) ? $queries['path'] : null;
			$post_type = isset( $queries['post_type'] ) ? $queries['post_type'] : null;
			$post      = isset( $queries['post'] ) ? get_post_type( $queries['post'] ) : null;
		}

		if ( isset( $page ) ) {
			$current_page = 'wc-admin' === $page ? 'home_screen' : $page;
			$screen_name  = isset( $path ) ? substr( str_replace( '/', '_', $path ), 1 ) : $current_page;
		} elseif ( isset( $post_type ) ) {
			$screen_name = $post_type;
		} elseif ( isset( $post ) ) {
			$screen_name = $post;
		}
		return $screen_name;
	}

	/**
	 * Loads the data store.
	 *
	 * If the "admin-note" data store is unavailable, attempts to load it
	 * will result in an exception.
	 * This method catches that exception and throws a custom one instead.
	 *
	 * @return \WC_Data_Store The "admin-note" data store.
	 * @throws NotesUnavailableException Throws exception if data store loading fails.
	 */
	public static function load_data_store() {
		try {
			return \WC_Data_Store::load( 'admin-note' );
		} catch ( \Exception $e ) {
			throw new NotesUnavailableException(
				'woocommerce_admin_notes_unavailable',
				__( 'Notes are unavailable because the "admin-note" data store cannot be loaded.', 'woocommerce' )
			);
		}
	}
}

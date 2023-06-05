<?php
/**
 * Runs a single spec.
 */

namespace Automattic\WooCommerce\Admin\RemoteInboxNotifications;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\Notes\Note;
use Automattic\WooCommerce\Admin\Notes\Notes;

/**
 * Runs a single spec.
 */
class SpecRunner {
	/**
	 * Run the spec.
	 *
	 * @param object $spec         The spec to run.
	 * @param object $stored_state Stored state.
	 */
	public static function run_spec( $spec, $stored_state ) {
		$data_store = Notes::load_data_store();

		// Create or update the note.
		$existing_note_ids = $data_store->get_notes_with_name( $spec->slug );
		if ( count( $existing_note_ids ) === 0 ) {
			$note = new Note();
			$note->set_status( Note::E_WC_ADMIN_NOTE_PENDING );
		} else {
			$note = Notes::get_note( $existing_note_ids[0] );
			if ( $note === false ) {
				return;
			}
		}

		// Evaluate the spec and get the new note status.
		$previous_status = $note->get_status();
		$status          = EvaluateAndGetStatus::evaluate(
			$spec,
			$previous_status,
			$stored_state,
			new RuleEvaluator()
		);

		// If the status is changing, update the created date to now.
		if ( $previous_status !== $status ) {
			$note->set_date_created( time() );
		}

		// Get the matching locale or fall back to en-US.
		$locale = self::get_locale( $spec->locales );

		if ( $locale === null ) {
			return;
		}

		// Set up the note.
		$note->set_title( $locale->title );
		$note->set_content( $locale->content );
		$note->set_content_data( isset( $spec->content_data ) ? $spec->content_data : (object) array() );
		$note->set_status( $status );
		$note->set_type( $spec->type );
		$note->set_name( $spec->slug );
		if ( isset( $spec->source ) ) {
			$note->set_source( $spec->source );
		}

		// Recreate actions.
		$note->set_actions( self::get_actions( $spec ) );

		$note->save();
	}

	/**
	 * Get the URL for an action.
	 *
	 * @param object $action The action.
	 *
	 * @return string The URL for the action.
	 */
	private static function get_url( $action ) {
		if ( ! isset( $action->url ) ) {
			return '';
		}

		if ( isset( $action->url_is_admin_query ) && $action->url_is_admin_query ) {
			if ( strpos( $action->url, '&path' ) === 0 ) {
				return wc_admin_url( $action->url );
			}
			return admin_url( $action->url );
		}

		return $action->url;
	}

	/**
	 * Get the locale for the WordPress locale, or fall back to the en_US
	 * locale.
	 *
	 * @param Array $locales The locales to search through.
	 *
	 * @returns object The locale that was found, or null if no matching locale was found.
	 */
	public static function get_locale( $locales ) {
		$wp_locale           = get_user_locale();
		$matching_wp_locales = array_values(
			array_filter(
				$locales,
				function( $l ) use ( $wp_locale ) {
					return $wp_locale === $l->locale;
				}
			)
		);

		if ( count( $matching_wp_locales ) !== 0 ) {
			return $matching_wp_locales[0];
		}

		// Fall back to en_US locale.
		$en_us_locales = array_values(
			array_filter(
				$locales,
				function( $l ) {
					return $l->locale === 'en_US';
				}
			)
		);

		if ( count( $en_us_locales ) !== 0 ) {
			return $en_us_locales[0];
		}

		return null;
	}

	/**
	 * Get the action locale that matches the note locale, or fall back to the
	 * en_US locale.
	 *
	 * @param Array $action_locales The locales from the spec's action.
	 *
	 * @return object The matching locale, or the en_US fallback locale, or null if neither was found.
	 */
	public static function get_action_locale( $action_locales ) {
		$wp_locale           = get_user_locale();
		$matching_wp_locales = array_values(
			array_filter(
				$action_locales,
				function ( $l ) use ( $wp_locale ) {
					return $wp_locale === $l->locale;
				}
			)
		);

		if ( count( $matching_wp_locales ) !== 0 ) {
			return $matching_wp_locales[0];
		}

		// Fall back to en_US locale.
		$en_us_locales = array_values(
			array_filter(
				$action_locales,
				function( $l ) {
					return $l->locale === 'en_US';
				}
			)
		);

		if ( count( $en_us_locales ) !== 0 ) {
			return $en_us_locales[0];
		}

		return null;
	}

	/**
	 * Get the actions for a note.
	 *
	 * @param object $spec The spec.
	 *
	 * @return array The actions.
	 */
	public static function get_actions( $spec ) {
		$note    = new Note();
		$actions = isset( $spec->actions ) ? $spec->actions : array();
		foreach ( $actions as $action ) {
			$action_locale = self::get_action_locale( $action->locales );

			$url = self::get_url( $action );

			$note->add_action(
				$action->name,
				( $action_locale === null || ! isset( $action_locale->label ) )
					? ''
					: $action_locale->label,
				$url,
				$action->status
			);
		}
		return $note->get_actions();
	}
}

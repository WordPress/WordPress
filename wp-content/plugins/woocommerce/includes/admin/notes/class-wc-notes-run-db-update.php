<?php
/**
 * WooCommerce: Db update note.
 *
 * Adds a note to complete the WooCommerce db update after the upgrade in the WC Admin context.
 *
 * @package WooCommerce
 */

defined( 'ABSPATH' ) || exit;

use Automattic\Jetpack\Constants;
use Automattic\WooCommerce\Admin\Notes\Note;

/**
 * WC_Notes_Run_Db_Update.
 */
class WC_Notes_Run_Db_Update {
	const NOTE_NAME = 'wc-update-db-reminder';

	/**
	 * Attach hooks.
	 */
	public function __construct() {
		// If the old notice gets dismissed, also hide this new one.
		add_action( 'woocommerce_hide_update_notice', array( __CLASS__, 'set_notice_actioned' ) );

		// Not using Jetpack\Constants here as it can run before 'plugin_loaded' is done.
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX
			|| defined( 'DOING_CRON' ) && DOING_CRON
			|| ! is_admin() ) {
			return;
		}

		add_action( 'current_screen', array( __CLASS__, 'show_reminder' ) );
	}

	/**
	 * Get current notice id from the database.
	 *
	 * Retrieves the first notice of this type.
	 *
	 * @return int|void Note id or null in case no note was found.
	 */
	private static function get_current_notice() {
		try {
			$data_store = \WC_Data_Store::load( 'admin-note' );
		} catch ( Exception $e ) {
			return;
		}
		$note_ids = $data_store->get_notes_with_name( self::NOTE_NAME );

		if ( empty( $note_ids ) ) {
			return;
		}

		if ( count( $note_ids ) > 1 ) {
			// Remove weird duplicates. Leave the first one.
			$current_notice = array_shift( $note_ids );
			foreach ( $note_ids as $note_id ) {
				$note = new Note( $note_id );
				$data_store->delete( $note );
			}
			return $current_notice;
		}

		return current( $note_ids );
	}

	/**
	 * Set this notice to an actioned one, so that it's no longer displayed.
	 */
	public static function set_notice_actioned() {
		$note_id = self::get_current_notice();

		if ( ! $note_id ) {
			return;
		}

		$note = new Note( $note_id );
		$note->set_status( Note::E_WC_ADMIN_NOTE_ACTIONED );
		$note->save();
	}

	/**
	 * Check whether the note is up to date for a fresh display.
	 *
	 * The check tests if
	 *  - actions are set up for the first 'Update database' notice, and
	 *  - URL for note's action is equal to the given URL (to check for potential nonce update).
	 *
	 * @param Note               $note            Note to check.
	 * @param string             $update_url      URL to check the note against.
	 * @param array<int, string> $current_actions List of actions to check for.
	 * @return bool
	 */
	private static function note_up_to_date( $note, $update_url, $current_actions ) {
		$actions = $note->get_actions();
		return count( $current_actions ) === count( array_intersect( wp_list_pluck( $actions, 'name' ), $current_actions ) )
			&& in_array( $update_url, wp_list_pluck( $actions, 'query' ), true );
	}

	/**
	 * Create and set up the first (out of 3) 'Database update needed' notice and store it in the database.
	 *
	 * If a $note_id is given, the method updates the note instead of creating a new one.
	 *
	 * @param integer $note_id Note db record to update.
	 * @return int Created/Updated note id
	 */
	private static function update_needed_notice( $note_id = null ) {
		$update_url =
			add_query_arg(
				array(
					'do_update_woocommerce' => 'true',
				),
				wc_get_current_admin_url() ? wc_get_current_admin_url() : admin_url( 'admin.php?page=wc-settings' )
			);

		$note_actions = array(
			array(
				'name'         => 'update-db_run',
				'label'        => __( 'Update WooCommerce Database', 'woocommerce' ),
				'url'          => $update_url,
				'status'       => 'unactioned',
				'primary'      => true,
				'nonce_action' => 'wc_db_update',
				'nonce_name'   => 'wc_db_update_nonce',
			),
			array(
				'name'    => 'update-db_learn-more',
				'label'   => __( 'Learn more about updates', 'woocommerce' ),
				'url'     => 'https://docs.woocommerce.com/document/how-to-update-woocommerce/',
				'status'  => 'unactioned',
				'primary' => false,
			),
		);

		if ( $note_id ) {
			$note = new Note( $note_id );
		} else {
			$note = new Note();
		}

		// Check if the note needs to be updated (e.g. expired nonce or different note type stored in the previous run).
		if ( self::note_up_to_date( $note, $update_url, wp_list_pluck( $note_actions, 'name' ) ) ) {
			return $note_id;
		}

		$note->set_title( __( 'WooCommerce database update required', 'woocommerce' ) );
		$note->set_content(
			__( 'WooCommerce has been updated! To keep things running smoothly, we have to update your database to the newest version.', 'woocommerce' )
			/* translators: %1$s: opening <a> tag %2$s: closing </a> tag*/
			. sprintf( ' ' . esc_html__( 'The database update process runs in the background and may take a little while, so please be patient. Advanced users can alternatively update via %1$sWP CLI%2$s.', 'woocommerce' ), '<a href="https://github.com/woocommerce/woocommerce/wiki/Upgrading-the-database-using-WP-CLI">', '</a>' )
		);
		$note->set_type( Note::E_WC_ADMIN_NOTE_UPDATE );
		$note->set_name( self::NOTE_NAME );
		$note->set_content_data( (object) array() );
		$note->set_source( 'woocommerce-core' );
		// In case db version is out of sync with WC version or during the next update, the notice needs to show up again,
		// so set it to unactioned.
		$note->set_status( Note::E_WC_ADMIN_NOTE_UNACTIONED );

		// Set new actions.
		$note->clear_actions();
		foreach ( $note_actions as $note_action ) {
			$note->add_action( ...array_values( $note_action ) );

			if ( isset( $note_action['nonce_action'] ) ) {
				$note->add_nonce_to_action( $note_action['name'], $note_action['nonce_action'], $note_action['nonce_name'] );
			}
		}

		return $note->save();
	}

	/**
	 * Update the existing note with $note_id with information about the db upgrade being in progress.
	 *
	 * This is the second out of 3 notices displayed to the user.
	 *
	 * @param int $note_id Note id to update.
	 */
	private static function update_in_progress_notice( $note_id ) {
		// Same actions as in includes/admin/views/html-notice-updating.php. This just redirects, performs no action, so without nonce.
		$pending_actions_url = admin_url( 'admin.php?page=wc-status&tab=action-scheduler&s=woocommerce_run_update&status=pending' );
		$cron_disabled       = Constants::is_true( 'DISABLE_WP_CRON' );
		$cron_cta            = $cron_disabled ? __( 'You can manually run queued updates here.', 'woocommerce' ) : __( 'View progress →', 'woocommerce' );

		$note = new Note( $note_id );
		$note->set_title( __( 'WooCommerce database update in progress', 'woocommerce' ) );
		$note->set_content( __( 'WooCommerce is updating the database in the background. The database update process may take a little while, so please be patient.', 'woocommerce' ) );

		$note->clear_actions();
		$note->add_action(
			'update-db_see-progress',
			$cron_cta,
			$pending_actions_url,
			'unactioned',
			false
		);

		$note->save();
	}

	/**
	 * Update the existing note with $note_id with information that db upgrade is done.
	 *
	 * This is the last notice (3 out of 3 notices) displayed to the user.
	 *
	 * @param int $note_id Note id to update.
	 */
	private static function update_done_notice( $note_id ) {
		$hide_notices_url = html_entity_decode( // to convert &amp;s to normal &, otherwise produces invalid link.
			add_query_arg(
				array(
					'wc-hide-notice' => 'update',
				),
				wc_get_current_admin_url() ? remove_query_arg( 'do_update_woocommerce', wc_get_current_admin_url() ) : admin_url( 'admin.php?page=wc-settings' )
			)
		);

		$note_actions = array(
			array(
				'name'         => 'update-db_done',
				'label'        => __( 'Thanks!', 'woocommerce' ),
				'url'          => $hide_notices_url,
				'status'       => 'actioned',
				'primary'      => true,
				'nonce_action' => 'woocommerce_hide_notices_nonce',
				'nonce_name'   => '_wc_notice_nonce',
			),
		);

		$note = new Note( $note_id );

		// Check if the note needs to be updated (e.g. expired nonce or different note type stored in the previous run).
		if ( self::note_up_to_date( $note, $hide_notices_url, wp_list_pluck( $note_actions, 'name' ) ) ) {
			return $note_id;
		}

		$note->set_title( __( 'WooCommerce database update done', 'woocommerce' ) );
		$note->set_content( __( 'WooCommerce database update complete. Thank you for updating to the latest version!', 'woocommerce' ) );

		$note->clear_actions();
		foreach ( $note_actions as $note_action ) {
			$note->add_action( ...array_values( $note_action ) );

			if ( isset( $note_action['nonce_action'] ) ) {
				$note->add_nonce_to_action( $note_action['name'], $note_action['nonce_action'], $note_action['nonce_name'] );
			}
		}

		$note->save();
	}

	/**
	 * Prepare the correct content of the db update note to be displayed by WC Admin.
	 *
	 * This one gets called on each page load, so try to bail quickly.
	 *
	 * If the db needs an update, the notice should be always shown.
	 * If the db does not need an update, but the notice has *not* been actioned (i.e. after the db update, when
	 * store owner hasn't acknowledged the successful db update), still show the Thanks notice.
	 * If the db does not need an update, and the notice has been actioned, then notice should *not* be shown.
	 * The notice should also be hidden if the db does not need an update and the notice does not exist.
	 */
	public static function show_reminder() {
		$needs_db_update = \WC_Install::needs_db_update();

		$note_id = self::get_current_notice();
		if ( ! $needs_db_update ) {
			// Db update not needed && note does not exist -> don't show it.
			if ( ! $note_id ) {
				return;
			}

			$note = new Note( $note_id );
			if ( $note::E_WC_ADMIN_NOTE_ACTIONED === $note->get_status() ) {
				// Db update not needed && note actioned -> don't show it.
				return;
			} else {
				// Db update not needed && notice is unactioned -> Thank you note.
				self::update_done_notice( $note_id );
				return;
			}
		} else {
			// Db needs update &&.
			if ( ! $note_id ) {
				// Db needs update && no notice exists -> create one that shows Nudge to update.
				$note_id = self::update_needed_notice();
			}

			$next_scheduled_date = WC()->queue()->get_next( 'woocommerce_run_update_callback', null, 'woocommerce-db-updates' );

			if ( $next_scheduled_date || ! empty( $_GET['do_update_woocommerce'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				// Db needs update && db update is scheduled -> update note to In progress.
				self::update_in_progress_notice( $note_id );
			} else {
				// Db needs update && db update is not scheduled -> Nudge to run the db update.
				self::update_needed_notice( $note_id );
			}
		}
	}

}

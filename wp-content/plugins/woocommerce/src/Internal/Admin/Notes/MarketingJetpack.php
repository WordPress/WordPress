<?php
/**
 * WooCommerce Admin Jetpack Marketing Note Provider.
 *
 * Adds notes to the merchant's inbox concerning Jetpack Backup.
 */

namespace Automattic\WooCommerce\Internal\Admin\Notes;

defined( 'ABSPATH' ) || exit;

use Automattic\Jetpack\Constants;
use Automattic\WooCommerce\Admin\Notes\Note;
use Automattic\WooCommerce\Admin\Notes\Notes;
use Automattic\WooCommerce\Admin\Notes\NoteTraits;
use Automattic\WooCommerce\Admin\PluginsHelper;

/**
 * Suggest Jetpack Backup to Woo users.
 *
 * Note: This should probably live in the Jetpack plugin in the future.
 *
 * @see  https://developer.woocommerce.com/2020/10/16/using-the-admin-notes-inbox-in-woocommerce/
 */
class MarketingJetpack {
	// Shared Note Traits.
	use NoteTraits;

	/**
	 * Name of the note for use in the database.
	 */
	const NOTE_NAME = 'wc-admin-marketing-jetpack-backup';

	/**
	 * Product IDs that include Backup.
	 */
	const BACKUP_IDS = [
		2010,
		2011,
		2012,
		2013,
		2014,
		2015,
		2100,
		2101,
		2102,
		2103,
		2005,
		2006,
		2000,
		2003,
		2001,
		2004,
	];

	/**
	 * Maybe add a note on Jetpack Backups for Jetpack sites older than a week without Backups.
	 */
	public static function possibly_add_note() {
		/**
		 * Check if Jetpack is installed.
		 */
		$installed_plugins = PluginsHelper::get_installed_plugin_slugs();
		if ( ! in_array( 'jetpack', $installed_plugins, true ) ) {
			return;
		}

		$data_store = \WC_Data_Store::load( 'admin-note' );

		// Do we already have this note?
		$note_ids = $data_store->get_notes_with_name( self::NOTE_NAME );
		if ( ! empty( $note_ids ) ) {

			$note_id = array_pop( $note_ids );
			$note    = Notes::get_note( $note_id );
			if ( false === $note ) {
				return;
			}

			// If Jetpack Backups was purchased after the note was created, mark this note as actioned.
			if ( self::has_backups() && Note::E_WC_ADMIN_NOTE_ACTIONED !== $note->get_status() ) {
				$note->set_status( Note::E_WC_ADMIN_NOTE_ACTIONED );
				$note->save();
			}

			return;
		}

		// Check requirements.
		if ( ! self::is_wc_admin_active_in_date_range( 'week-1-4', DAY_IN_SECONDS * 3 ) || ! self::can_be_added() || self::has_backups() ) {
			return;
		}

		// Add note.
		$note = self::get_note();
		$note->save();
	}

	/**
	 * Get the note.
	 */
	public static function get_note() {
		$note = new Note();
		$note->set_title( __( 'Protect your WooCommerce Store with Jetpack Backup.', 'woocommerce' ) );
		$note->set_content( __( 'Store downtime means lost sales. One-click restores get you back online quickly if something goes wrong.', 'woocommerce' ) );
		$note->set_type( Note::E_WC_ADMIN_NOTE_MARKETING );
		$note->set_name( self::NOTE_NAME );
		$note->set_layout( 'thumbnail' );
		$note->set_image(
			WC_ADMIN_IMAGES_FOLDER_URL . '/admin_notes/marketing-jetpack-2x.png'
		);
		$note->set_content_data( (object) array() );
		$note->set_source( 'woocommerce-admin-notes' );
		$note->add_action(
			'jetpack-backup-woocommerce',
			__( 'Get backups', 'woocommerce' ),
			esc_url( 'https://jetpack.com/upgrade/backup-woocommerce/?utm_source=inbox&utm_medium=automattic_referred&utm_campaign=jp_backup_to_woo' ),
			Note::E_WC_ADMIN_NOTE_ACTIONED
		);
		return $note;
	}

	/**
	 * Check if this blog already has a Jetpack Backups product.
	 *
	 * @return boolean  Whether or not this blog has backups.
	 */
	protected static function has_backups() {
		$product_ids = [];

		$plan = get_option( 'jetpack_active_plan' );
		if ( ! empty( $plan ) ) {
			$product_ids[] = $plan['product_id'];
		}

		$products = get_option( 'jetpack_site_products' );
		if ( ! empty( $products ) ) {
			foreach ( $products as $product ) {
				$product_ids[] = $product['product_id'];
			}
		}

		return (bool) array_intersect( self::BACKUP_IDS, $product_ids );
	}

}

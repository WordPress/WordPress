<?php
/**
 * WooCommerce Admin: Start your online clothing store.
 *
 * Adds a note to ask the client if they are considering starting an online
 * clothing store.
 */

namespace Automattic\WooCommerce\Internal\Admin\Notes;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\Notes\Note;
use Automattic\WooCommerce\Admin\Notes\NoteTraits;

/**
 * Online_Clothing_Store.
 */
class OnlineClothingStore {
	/**
	 * Note traits.
	 */
	use NoteTraits;

	/**
	 * Name of the note for use in the database.
	 */
	const NOTE_NAME = 'wc-admin-online-clothing-store';

	/**
	 * Returns whether the industries includes fashion-apparel-accessories.
	 *
	 * @param array $industries The industries to search.
	 *
	 * @return bool Whether the industries includes fashion-apparel-accessories.
	 */
	private static function is_in_fashion_industry( $industries ) {
		foreach ( $industries as $industry ) {
			if ( 'fashion-apparel-accessories' === $industry['slug'] ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Get the note.
	 *
	 * @return Note
	 */
	public static function get_note() {
		// We want to show the note after two days.
		if ( ! self::is_wc_admin_active_in_date_range( 'week-1', 2 * DAY_IN_SECONDS ) ) {
			return;
		}

		$onboarding_profile = get_option( 'woocommerce_onboarding_profile', array() );

		// Confirm that $onboarding_profile is set.
		if ( empty( $onboarding_profile ) ) {
			return;
		}

		// Make sure that the person who filled out the OBW was not setting up
		// the store for their customer/client.
		if (
			! isset( $onboarding_profile['setup_client'] ) ||
			$onboarding_profile['setup_client']
		) {
			return;
		}

		// We need to show the notification when the industry is
		// fashion/apparel/accessories.
		if ( ! isset( $onboarding_profile['industry'] ) ) {
			return;
		}
		if ( ! self::is_in_fashion_industry( $onboarding_profile['industry'] ) ) {
			return;
		}

		$note = new Note();
		$note->set_title( __( 'Start your online clothing store', 'woocommerce' ) );
		$note->set_content( __( 'Starting a fashion website is exciting but it may seem overwhelming as well. In this article, we\'ll walk you through the setup process, teach you to create successful product listings, and show you how to market to your ideal audience.', 'woocommerce' ) );
		$note->set_type( Note::E_WC_ADMIN_NOTE_INFORMATIONAL );
		$note->set_name( self::NOTE_NAME );
		$note->set_content_data( (object) array() );
		$note->set_source( 'woocommerce-admin' );
		$note->add_action(
			'online-clothing-store',
			__( 'Learn more', 'woocommerce' ),
			'https://woocommerce.com/posts/starting-an-online-clothing-store/?utm_source=inbox&utm_medium=product',
			Note::E_WC_ADMIN_NOTE_ACTIONED
		);
		return $note;
	}
}

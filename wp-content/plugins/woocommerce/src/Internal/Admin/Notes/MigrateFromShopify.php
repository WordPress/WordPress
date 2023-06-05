<?php
/**
 * WooCommerce Admin: Migrate from Shopify to WooCommerce.
 *
 * Adds a note to ask the client if they want to migrate from Shopify to WooCommerce.
 */

namespace Automattic\WooCommerce\Internal\Admin\Notes;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\Notes\Note;
use Automattic\WooCommerce\Admin\Notes\NoteTraits;

/**
 * Migrate_From_Shopify.
 */
class MigrateFromShopify {
	/**
	 * Note traits.
	 */
	use NoteTraits;

	/**
	 * Name of the note for use in the database.
	 */
	const NOTE_NAME = 'wc-admin-migrate-from-shopify';

	/**
	 * Get the note.
	 *
	 * @return Note
	 */
	public static function get_note() {

		// We want to show the note after two days.
		$two_days = 2 * DAY_IN_SECONDS;
		if ( ! self::is_wc_admin_active_in_date_range( 'week-1', $two_days ) ) {
			return;
		}

		$onboarding_profile = get_option( 'woocommerce_onboarding_profile', array() );
		if (
			! isset( $onboarding_profile['setup_client'] ) ||
			! isset( $onboarding_profile['selling_venues'] ) ||
			! isset( $onboarding_profile['other_platform'] )
		) {
			return;
		}

		// Make sure the client is not setup.
		if ( $onboarding_profile['setup_client'] ) {
			return;
		}

		// We will show the notification when the client already is selling and is using Shopify.
		if (
			'other' !== $onboarding_profile['selling_venues'] ||
			'shopify' !== $onboarding_profile['other_platform']
		) {
			return;
		}

		$note = new Note();
		$note->set_title( __( 'Do you want to migrate from Shopify to WooCommerce?', 'woocommerce' ) );
		$note->set_content( __( 'Changing eCommerce platforms might seem like a big hurdle to overcome, but it is easier than you might think to move your products, customers, and orders to WooCommerce. This article will help you with going through this process.', 'woocommerce' ) );
		$note->set_type( Note::E_WC_ADMIN_NOTE_INFORMATIONAL );
		$note->set_name( self::NOTE_NAME );
		$note->set_content_data( (object) array() );
		$note->set_source( 'woocommerce-admin' );
		$note->add_action(
			'migrate-from-shopify',
			__( 'Learn more', 'woocommerce' ),
			'https://woocommerce.com/posts/migrate-from-shopify-to-woocommerce/?utm_source=inbox&utm_medium=product',
			Note::E_WC_ADMIN_NOTE_ACTIONED
		);
		return $note;
	}
}

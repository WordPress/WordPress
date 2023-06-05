<?php
/**
 * WooCommerce Admin: Customize your online store with WooCommerce blocks.
 *
 * Adds a note to customize the client online store with WooCommerce blocks.
 *
 * @package WooCommerce\Admin
 */

namespace Automattic\WooCommerce\Internal\Admin\Notes;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\Notes\Note;
use Automattic\WooCommerce\Admin\Notes\NoteTraits;

/**
 * Customize_Store_With_Blocks.
 */
class CustomizeStoreWithBlocks {
	/**
	 * Note traits.
	 */
	use NoteTraits;

	/**
	 * Name of the note for use in the database.
	 */
	const NOTE_NAME = 'wc-admin-customize-store-with-blocks';

	/**
	 * Get the note.
	 *
	 * @return Note
	 */
	public static function get_note() {
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

		// We want to show the note after fourteen days.
		if ( ! self::is_wc_admin_active_in_date_range( 'week-1-4', 14 * DAY_IN_SECONDS ) ) {
			return;
		}

		// Don't show if there aren't products.
		$query    = new \WC_Product_Query(
			array(
				'limit'  => 1,
				'return' => 'ids',
				'status' => array( 'publish' ),
			)
		);
		$products = $query->get_products();
		if ( 0 === count( $products ) ) {
			return;
		}

		$note = new Note();
		$note->set_title( __( 'Customize your online store with WooCommerce blocks', 'woocommerce' ) );
		$note->set_content( __( 'With our blocks, you can select and display products, categories, filters, and more virtually anywhere on your site — no need to use shortcodes or edit lines of code. Learn more about how to use each one of them.', 'woocommerce' ) );
		$note->set_type( Note::E_WC_ADMIN_NOTE_INFORMATIONAL );
		$note->set_name( self::NOTE_NAME );
		$note->set_content_data( (object) array() );
		$note->set_source( 'woocommerce-admin' );
		$note->add_action(
			'customize-store-with-blocks',
			__( 'Learn more', 'woocommerce' ),
			'https://woocommerce.com/posts/how-to-customize-your-online-store-with-woocommerce-blocks/?utm_source=inbox&utm_medium=product',
			Note::E_WC_ADMIN_NOTE_ACTIONED
		);
		return $note;
	}
}

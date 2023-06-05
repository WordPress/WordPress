<?php
/**
 * WooCommerce Admin: Do you need help with adding your first product?
 *
 * Adds a note to ask the client if they need help adding their first product.
 */

namespace Automattic\WooCommerce\Internal\Admin\Notes;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\Notes\Note;
use Automattic\WooCommerce\Admin\Notes\NoteTraits;

/**
 * First_Product.
 */
class FirstProduct {
	/**
	 * Note traits.
	 */
	use NoteTraits;

	/**
	 * Name of the note for use in the database.
	 */
	const NOTE_NAME = 'wc-admin-first-product';

	/**
	 * Get the note.
	 *
	 * @return Note
	 */
	public static function get_note() {
		// We want to show the note after seven days.
		if ( ! self::is_wc_admin_active_in_date_range( 'week-1-4' ) ) {
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

		// Don't show if there are products.
		$query    = new \WC_Product_Query(
			array(
				'limit'    => 1,
				'paginate' => true,
				'return'   => 'ids',
				'status'   => array( 'publish' ),
			)
		);
		$products = $query->get_products();
		$count    = $products->total;
		if ( 0 !== $count ) {
			return;
		}

		$note = new Note();
		$note->set_title( __( 'Do you need help with adding your first product?', 'woocommerce' ) );
		$note->set_content( __( 'This video tutorial will help you go through the process of adding your first product in WooCommerce.', 'woocommerce' ) );
		$note->set_type( Note::E_WC_ADMIN_NOTE_INFORMATIONAL );
		$note->set_name( self::NOTE_NAME );
		$note->set_content_data( (object) array() );
		$note->set_source( 'woocommerce-admin' );
		$note->add_action(
			'first-product-watch-tutorial',
			__( 'Watch tutorial', 'woocommerce' ),
			'https://www.youtube.com/watch?v=sFtXa00Jf_o&list=PLHdG8zvZd0E575Ia8Mu3w1h750YLXNfsC&index=24'
		);

		return $note;
	}
}

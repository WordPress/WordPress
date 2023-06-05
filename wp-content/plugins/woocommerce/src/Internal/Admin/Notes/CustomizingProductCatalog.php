<?php
/**
 * WooCommerce Admin: How to customize your product catalog note provider
 *
 * Adds a note with a link to the customizer a day after adding the first product
 */

namespace Automattic\WooCommerce\Internal\Admin\Notes;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\Notes\Note;
use Automattic\WooCommerce\Admin\Notes\NoteTraits;

/**
 * Class CustomizingProductCatalog
 *
 * @package Automattic\WooCommerce\Admin\Notes
 */
class CustomizingProductCatalog {
	/**
	 * Note traits.
	 */
	use NoteTraits;

	/**
	 * Name of the note for use in the database.
	 */
	const NOTE_NAME = 'wc-admin-customizing-product-catalog';

	/**
	 * Get the note.
	 *
	 * @return Note
	 */
	public static function get_note() {
		$query = new \WC_Product_Query(
			array(
				'limit'    => 1,
				'paginate' => true,
				'status'   => array( 'publish' ),
				'orderby'  => 'post_date',
				'order'    => 'DESC',
			)
		);

		$products = $query->get_products();

		// we need at least 1 product.
		if ( 0 === $products->total ) {
			return;
		}

		$product           = $products->products[0];
		$created_timestamp = $product->get_date_created()->getTimestamp();
		$is_a_day_old      = ( time() - $created_timestamp ) >= DAY_IN_SECONDS;

		// the product must be at least 1 day old.
		if ( ! $is_a_day_old ) {
			return;
		}

		// store must not been active more than 14 days.
		if ( self::wc_admin_active_for( DAY_IN_SECONDS * 14 ) ) {
			return;
		}

		$note = new Note();
		$note->set_title( __( 'How to customize your product catalog', 'woocommerce' ) );
		$note->set_content( __( 'You want your product catalog and images to look great and align with your brand. This guide will give you all the tips you need to get your products looking great in your store.', 'woocommerce' ) );
		$note->set_type( Note::E_WC_ADMIN_NOTE_INFORMATIONAL );
		$note->set_name( self::NOTE_NAME );
		$note->set_content_data( (object) array() );
		$note->set_source( 'woocommerce-admin' );
		$note->add_action(
			'day-after-first-product',
			__( 'Learn more', 'woocommerce' ),
			'https://woocommerce.com/document/woocommerce-customizer/?utm_source=inbox&utm_medium=product'
		);

		return $note;
	}
}

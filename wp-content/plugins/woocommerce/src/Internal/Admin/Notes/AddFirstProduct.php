<?php
/**
 * WooCommerce Admin: Add First Product.
 *
 * Adds a note (type `email`) to bring the client back to the store setup flow.
 */

namespace Automattic\WooCommerce\Internal\Admin\Notes;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\Notes\Note;
use Automattic\WooCommerce\Admin\Notes\NoteTraits;

/**
 * Add_First_Product.
 */
class AddFirstProduct {
	/**
	 * Note traits.
	 */
	use NoteTraits;

	/**
	 * Name of the note for use in the database.
	 */
	const NOTE_NAME = 'wc-admin-add-first-product-note';

	/**
	 * Get the note.
	 *
	 * @return Note
	 */
	public static function get_note() {
		if ( ! self::wc_admin_active_for( 2 * DAY_IN_SECONDS ) || self::wc_admin_active_for( 5 * DAY_IN_SECONDS ) ) {
			return;
		}

		// Don't show if there is a product.
		$query    = new \WC_Product_Query(
			array(
				'limit'  => 1,
				'return' => 'ids',
				'status' => array( 'publish' ),
			)
		);
		$products = $query->get_products();
		if ( 0 !== count( $products ) ) {
			return;
		}

		// Don't show if there is an orders.
		$args   = array(
			'limit'  => 1,
			'return' => 'ids',
		);
		$orders = wc_get_orders( $args );
		if ( 0 !== count( $orders ) ) {
			return;
		}

		// If you're updating the following please use sprintf to separate HTML tags.
		// https://github.com/woocommerce/woocommerce-admin/pull/6617#discussion_r596889685.
		$content_lines = array(
			'{greetings}<br/><br/>',
			/* translators: %s: line break */
			sprintf( __( 'Nice one; you\'ve created a WooCommerce store! Now it\'s time to add your first product and get ready to start selling.%s', 'woocommerce' ), '<br/><br/>' ),
			__( 'There are three ways to add your products: you can <strong>create products manually, import them at once via CSV file</strong>, or <strong>migrate them from another service</strong>.<br/><br/>', 'woocommerce' ),
			/* translators: %1$s is an open anchor tag (<a>) and %2$s is a close link tag (</a>). */
			sprintf( __( '%1$1sExplore our docs%2$2s for more information, or just get started!', 'woocommerce' ), '<a href="https://woocommerce.com/document/managing-products/?utm_source=help_panel&utm_medium=product">', '</a>' ),
		);

		$additional_data = array(
			'role' => 'administrator',
		);

		$note = new Note();
		$note->set_title( __( 'Add your first product', 'woocommerce' ) );
		$note->set_content( implode( '', $content_lines ) );
		$note->set_content_data( (object) $additional_data );
		$note->set_image(
			plugins_url(
				'/images/admin_notes/dashboard-widget-setup.png',
				WC_ADMIN_PLUGIN_FILE
			)
		);
		$note->set_type( Note::E_WC_ADMIN_NOTE_EMAIL );
		$note->set_name( self::NOTE_NAME );
		$note->set_source( 'woocommerce-admin' );
		$note->add_action( 'add-first-product', __( 'Add a product', 'woocommerce' ), admin_url( 'admin.php?page=wc-admin&task=products' ) );
		return $note;
	}
}

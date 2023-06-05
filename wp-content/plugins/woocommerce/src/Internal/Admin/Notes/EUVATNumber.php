<?php
/**
 * WooCommerce Admin: EU VAT Number Note.
 *
 * Adds a note for EU store to install the EU VAT Number extension.
 */

namespace Automattic\WooCommerce\Internal\Admin\Notes;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\Notes\Note;
use Automattic\WooCommerce\Admin\Notes\NoteTraits;

/**
 * EU_VAT_Number
 */
class EUVATNumber {
	/**
	 * Note traits.
	 */
	use NoteTraits;

	/**
	 * Name of the note for use in the database.
	 */
	const NOTE_NAME = 'wc-admin-eu-vat-number';

	/**
	 * Get the note.
	 *
	 * @return Note
	 */
	public static function get_note() {
		if ( 'yes' !== get_option( 'wc_connect_taxes_enabled', 'no' ) ) {
			return;
		}

		$country_code = WC()->countries->get_base_country();
		$eu_countries = WC()->countries->get_european_union_countries();
		if ( ! in_array( $country_code, $eu_countries, true ) ) {
			return;
		}

		$content = __( "If your store is based in the EU, we recommend using the EU VAT Number extension in addition to automated taxes. It provides your checkout with a field to collect and validate a customer's EU VAT number, if they have one.", 'woocommerce' );

		$note = new Note();
		$note->set_title( __( 'Collect and validate EU VAT numbers at checkout', 'woocommerce' ) );
		$note->set_content( $content );
		$note->set_content_data( (object) array() );
		$note->set_type( Note::E_WC_ADMIN_NOTE_MARKETING );
		$note->set_name( self::NOTE_NAME );
		$note->set_source( 'woocommerce-admin' );
		$note->add_action(
			'learn-more',
			__( 'Learn more', 'woocommerce' ),
			'https://woocommerce.com/products/eu-vat-number/?utm_medium=product',
			Note::E_WC_ADMIN_NOTE_ACTIONED
		);
		return $note;
	}
}

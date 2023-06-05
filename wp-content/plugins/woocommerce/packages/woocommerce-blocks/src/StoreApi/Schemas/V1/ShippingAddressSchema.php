<?php
namespace Automattic\WooCommerce\StoreApi\Schemas\V1;

use Automattic\WooCommerce\StoreApi\Exceptions\RouteException;
use Automattic\WooCommerce\StoreApi\Utilities\ValidationUtils;

/**
 * ShippingAddressSchema class.
 *
 * Provides a generic shipping address schema for composition in other schemas.
 */
class ShippingAddressSchema extends AbstractAddressSchema {
	/**
	 * The schema item name.
	 *
	 * @var string
	 */
	protected $title = 'shipping_address';

	/**
	 * The schema item identifier.
	 *
	 * @var string
	 */
	const IDENTIFIER = 'shipping-address';

	/**
	 * Convert a term object into an object suitable for the response.
	 *
	 * @param \WC_Order|\WC_Customer $address An object with shipping address.
	 *
	 * @throws RouteException When the invalid object types are provided.
	 * @return stdClass
	 */
	public function get_item_response( $address ) {
		$validation_util = new ValidationUtils();
		if ( ( $address instanceof \WC_Customer || $address instanceof \WC_Order ) ) {
			$shipping_country = $address->get_shipping_country();
			$shipping_state   = $address->get_shipping_state();

			if ( ! $validation_util->validate_state( $shipping_state, $shipping_country ) ) {
				$shipping_state = '';
			}

			return (object) $this->prepare_html_response(
				[
					'first_name' => $address->get_shipping_first_name(),
					'last_name'  => $address->get_shipping_last_name(),
					'company'    => $address->get_shipping_company(),
					'address_1'  => $address->get_shipping_address_1(),
					'address_2'  => $address->get_shipping_address_2(),
					'city'       => $address->get_shipping_city(),
					'state'      => $shipping_state,
					'postcode'   => $address->get_shipping_postcode(),
					'country'    => $shipping_country,
					'phone'      => $address->get_shipping_phone(),
				]
			);
		}
		throw new RouteException(
			'invalid_object_type',
			sprintf(
				/* translators: Placeholders are class and method names */
				__( '%1$s requires an instance of %2$s or %3$s for the address', 'woocommerce' ),
				'ShippingAddressSchema::get_item_response',
				'WC_Customer',
				'WC_Order'
			),
			500
		);
	}
}

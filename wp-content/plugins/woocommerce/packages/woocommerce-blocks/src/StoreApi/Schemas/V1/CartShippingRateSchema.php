<?php
namespace Automattic\WooCommerce\StoreApi\Schemas\V1;

use WC_Shipping_Rate as ShippingRate;

/**
 * CartShippingRateSchema class.
 */
class CartShippingRateSchema extends AbstractSchema {
	/**
	 * The schema item name.
	 *
	 * @var string
	 */
	protected $title = 'cart-shipping-rate';

	/**
	 * The schema item identifier.
	 *
	 * @var string
	 */
	const IDENTIFIER = 'cart-shipping-rate';

	/**
	 * Cart schema properties.
	 *
	 * @return array
	 */
	public function get_properties() {
		return [
			'package_id'     => [
				'description' => __( 'The ID of the package the shipping rates belong to.', 'woocommerce' ),
				'type'        => [ 'integer', 'string' ],
				'context'     => [ 'view', 'edit' ],
				'readonly'    => true,
			],
			'name'           => [
				'description' => __( 'Name of the package.', 'woocommerce' ),
				'type'        => 'string',
				'context'     => [ 'view', 'edit' ],
				'readonly'    => true,
			],
			'destination'    => [
				'description' => __( 'Shipping destination address.', 'woocommerce' ),
				'type'        => 'object',
				'context'     => [ 'view', 'edit' ],
				'readonly'    => true,
				'properties'  => [
					'address_1' => [
						'description' => __( 'First line of the address being shipped to.', 'woocommerce' ),
						'type'        => 'string',
						'context'     => [ 'view', 'edit' ],
						'readonly'    => true,
					],
					'address_2' => [
						'description' => __( 'Second line of the address being shipped to.', 'woocommerce' ),
						'type'        => 'string',
						'context'     => [ 'view', 'edit' ],
						'readonly'    => true,
					],
					'city'      => [
						'description' => __( 'City of the address being shipped to.', 'woocommerce' ),
						'type'        => 'string',
						'context'     => [ 'view', 'edit' ],
						'readonly'    => true,
					],
					'state'     => [
						'description' => __( 'ISO code, or name, for the state, province, or district of the address being shipped to.', 'woocommerce' ),
						'type'        => 'string',
						'context'     => [ 'view', 'edit' ],
						'readonly'    => true,
					],
					'postcode'  => [
						'description' => __( 'Zip or Postcode of the address being shipped to.', 'woocommerce' ),
						'type'        => 'string',
						'context'     => [ 'view', 'edit' ],
						'readonly'    => true,
					],
					'country'   => [
						'description' => __( 'ISO code for the country of the address being shipped to.', 'woocommerce' ),
						'type'        => 'string',
						'context'     => [ 'view', 'edit' ],
						'readonly'    => true,
					],
				],
			],
			'items'          => [
				'description' => __( 'List of cart items the returned shipping rates apply to.', 'woocommerce' ),
				'type'        => 'array',
				'context'     => [ 'view', 'edit' ],
				'readonly'    => true,
				'items'       => [
					'type'       => 'object',
					'properties' => [
						'key'      => [
							'description' => __( 'Unique identifier for the item within the cart.', 'woocommerce' ),
							'type'        => 'string',
							'context'     => [ 'view', 'edit' ],
							'readonly'    => true,
						],
						'name'     => [
							'description' => __( 'Name of the item.', 'woocommerce' ),
							'type'        => 'string',
							'context'     => [ 'view', 'edit' ],
							'readonly'    => true,
						],
						'quantity' => [
							'description' => __( 'Quantity of the item in the current package.', 'woocommerce' ),
							'type'        => 'number',
							'context'     => [ 'view', 'edit' ],
							'readonly'    => true,
						],
					],
				],
			],
			'shipping_rates' => [
				'description' => __( 'List of shipping rates.', 'woocommerce' ),
				'type'        => 'array',
				'context'     => [ 'view', 'edit' ],
				'readonly'    => true,
				'items'       => [
					'type'       => 'object',
					'properties' => $this->get_rate_properties(),
				],
			],
		];
	}

	/**
	 * Schema for a single rate.
	 *
	 * @return array
	 */
	protected function get_rate_properties() {
		return array_merge(
			[
				'rate_id'       => [
					'description' => __( 'ID of the shipping rate.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => [ 'view', 'edit' ],
					'readonly'    => true,
				],
				'name'          => [
					'description' => __( 'Name of the shipping rate, e.g. Express shipping.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => [ 'view', 'edit' ],
					'readonly'    => true,
				],
				'description'   => [
					'description' => __( 'Description of the shipping rate, e.g. Dispatched via USPS.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => [ 'view', 'edit' ],
					'readonly'    => true,
				],
				'delivery_time' => [
					'description' => __( 'Delivery time estimate text, e.g. 3-5 business days.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => [ 'view', 'edit' ],
					'readonly'    => true,
				],
				'price'         => [
					'description' => __( 'Price of this shipping rate using the smallest unit of the currency.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => [ 'view', 'edit' ],
					'readonly'    => true,
				],
				'taxes'         => [
					'description' => __( 'Taxes applied to this shipping rate using the smallest unit of the currency.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => [ 'view', 'edit' ],
					'readonly'    => true,
				],
				'method_id'     => [
					'description' => __( 'ID of the shipping method that provided the rate.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => [ 'view', 'edit' ],
					'readonly'    => true,
				],
				'instance_id'   => [
					'description' => __( 'Instance ID of the shipping method that provided the rate.', 'woocommerce' ),
					'type'        => 'integer',
					'context'     => [ 'view', 'edit' ],
					'readonly'    => true,
				],
				'meta_data'     => [
					'description' => __( 'Meta data attached to the shipping rate.', 'woocommerce' ),
					'type'        => 'array',
					'context'     => [ 'view', 'edit' ],
					'items'       => [
						'type'       => 'object',
						'properties' => [
							'key'   => [
								'description' => __( 'Meta key.', 'woocommerce' ),
								'type'        => 'string',
								'context'     => [ 'view', 'edit' ],
								'readonly'    => true,
							],
							'value' => [
								'description' => __( 'Meta value.', 'woocommerce' ),
								'type'        => 'string',
								'context'     => [ 'view', 'edit' ],
								'readonly'    => true,
							],
						],
					],
				],
				'selected'      => [
					'description' => __( 'True if this is the rate currently selected by the customer for the cart.', 'woocommerce' ),
					'type'        => 'boolean',
					'context'     => [ 'view', 'edit' ],
					'readonly'    => true,
				],
			],
			$this->get_store_currency_properties()
		);
	}

	/**
	 * Convert a shipping rate from WooCommerce into a valid response.
	 *
	 * @param array $package Shipping package complete with rates from WooCommerce.
	 * @return array
	 */
	public function get_item_response( $package ) {
		return [
			'package_id'     => $package['package_id'],
			'name'           => $package['package_name'],
			'destination'    => $this->prepare_package_destination_response( $package ),
			'items'          => $this->prepare_package_items_response( $package ),
			'shipping_rates' => $this->prepare_package_shipping_rates_response( $package ),
		];
	}

	/**
	 * Gets and formats the destination address of a package.
	 *
	 * @param array $package Shipping package complete with rates from WooCommerce.
	 * @return object
	 */
	protected function prepare_package_destination_response( $package ) {
		// If address_1 fails check address for back compatability.
		$address = isset( $package['destination']['address_1'] ) ? $package['destination']['address_1'] : $package['destination']['address'];
		return (object) $this->prepare_html_response(
			[
				'address_1' => $address,
				'address_2' => $package['destination']['address_2'],
				'city'      => $package['destination']['city'],
				'state'     => $package['destination']['state'],
				'postcode'  => $package['destination']['postcode'],
				'country'   => $package['destination']['country'],
			]
		);
	}

	/**
	 * Gets items from a package and creates an array of strings containing product names and quantities.
	 *
	 * @param array $package Shipping package complete with rates from WooCommerce.
	 * @return array
	 */
	protected function prepare_package_items_response( $package ) {
		$items = array();
		foreach ( $package['contents'] as $values ) {
			$items[] = [
				'key'      => $values['key'],
				'name'     => $values['data']->get_name(),
				'quantity' => $values['quantity'],
			];
		}
		return $items;
	}

	/**
	 * Prepare an array of rates from a package for the response.
	 *
	 * @param array $package Shipping package complete with rates from WooCommerce.
	 * @return array
	 */
	protected function prepare_package_shipping_rates_response( $package ) {
		$rates          = $package['rates'];
		$selected_rates = wc()->session->get( 'chosen_shipping_methods', array() );
		$selected_rate  = isset( $selected_rates[ $package['package_id'] ] ) ? $selected_rates[ $package['package_id'] ] : '';

		if ( empty( $selected_rate ) && ! empty( $package['rates'] ) ) {
			$selected_rate = wc_get_chosen_shipping_method_for_package( $package['package_id'], $package );
		}

		$response = [];

		foreach ( $package['rates'] as $rate ) {
			$response[] = $this->get_rate_response( $rate, $selected_rate );
		}

		return $response;
	}

	/**
	 * Response for a single rate.
	 *
	 * @param WC_Shipping_Rate $rate Rate object.
	 * @param string           $selected_rate Selected rate.
	 * @return array
	 */
	protected function get_rate_response( $rate, $selected_rate = '' ) {
		return $this->prepare_currency_response(
			[
				'rate_id'       => $this->get_rate_prop( $rate, 'id' ),
				'name'          => $this->prepare_html_response( $this->get_rate_prop( $rate, 'label' ) ),
				'description'   => $this->prepare_html_response( $this->get_rate_prop( $rate, 'description' ) ),
				'delivery_time' => $this->prepare_html_response( $this->get_rate_prop( $rate, 'delivery_time' ) ),
				'price'         => $this->prepare_money_response( $this->get_rate_prop( $rate, 'cost' ), wc_get_price_decimals() ),
				'taxes'         => $this->prepare_money_response( array_sum( (array) $this->get_rate_prop( $rate, 'taxes' ) ), wc_get_price_decimals() ),
				'instance_id'   => $this->get_rate_prop( $rate, 'instance_id' ),
				'method_id'     => $this->get_rate_prop( $rate, 'method_id' ),
				'meta_data'     => $this->get_rate_meta_data( $rate ),
				'selected'      => $selected_rate === $this->get_rate_prop( $rate, 'id' ),
			]
		);
	}

	/**
	 * Gets a prop of the rate object, if callable.
	 *
	 * @param WC_Shipping_Rate $rate Rate object.
	 * @param string           $prop Prop name.
	 * @return string
	 */
	protected function get_rate_prop( $rate, $prop ) {
		$getter = 'get_' . $prop;
		return \is_callable( array( $rate, $getter ) ) ? $rate->$getter() : '';
	}

	/**
	 * Converts rate meta data into a suitable response object.
	 *
	 * @param WC_Shipping_Rate $rate Rate object.
	 * @return array
	 */
	protected function get_rate_meta_data( $rate ) {
		$meta_data = $rate->get_meta_data();

		return array_reduce(
			array_keys( $meta_data ),
			function( $return, $key ) use ( $meta_data ) {
				$return[] = [
					'key'   => $key,
					'value' => $meta_data[ $key ],
				];
				return $return;
			},
			[]
		);
	}
}

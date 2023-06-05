<?php
namespace Automattic\WooCommerce\StoreApi\Schemas\V1;

/**
 * CartFeeSchema class.
 */
class CartFeeSchema extends AbstractSchema {
	/**
	 * The schema item name.
	 *
	 * @var string
	 */
	protected $title = 'cart_fee';

	/**
	 * The schema item identifier.
	 *
	 * @var string
	 */
	const IDENTIFIER = 'cart-fee';

	/**
	 * Cart schema properties.
	 *
	 * @return array
	 */
	public function get_properties() {
		return [
			'id'     => [
				'description' => __( 'Unique identifier for the fee within the cart.', 'woocommerce' ),
				'type'        => 'string',
				'context'     => [ 'view', 'edit' ],
				'readonly'    => true,
			],
			'name'   => [
				'description' => __( 'Fee name.', 'woocommerce' ),
				'type'        => 'string',
				'context'     => [ 'view', 'edit' ],
				'readonly'    => true,
			],
			'totals' => [
				'description' => __( 'Fee total amounts provided using the smallest unit of the currency.', 'woocommerce' ),
				'type'        => 'object',
				'context'     => [ 'view', 'edit' ],
				'readonly'    => true,
				'properties'  => array_merge(
					$this->get_store_currency_properties(),
					[
						'total'     => [
							'description' => __( 'Total amount for this fee.', 'woocommerce' ),
							'type'        => 'string',
							'context'     => [ 'view', 'edit' ],
							'readonly'    => true,
						],
						'total_tax' => [
							'description' => __( 'Total tax amount for this fee.', 'woocommerce' ),
							'type'        => 'string',
							'context'     => [ 'view', 'edit' ],
							'readonly'    => true,
						],
					]
				),
			],
		];
	}

	/**
	 * Convert a WooCommerce cart fee to an object suitable for the response.
	 *
	 * @param array $fee Cart fee data.
	 * @return array
	 */
	public function get_item_response( $fee ) {
		return [
			'key'    => $fee->id,
			'name'   => $this->prepare_html_response( $fee->name ),
			'totals' => (object) $this->prepare_currency_response(
				[
					'total'     => $this->prepare_money_response( $fee->total, wc_get_price_decimals() ),
					'total_tax' => $this->prepare_money_response( $fee->tax, wc_get_price_decimals(), PHP_ROUND_HALF_DOWN ),
				]
			),
		];
	}
}

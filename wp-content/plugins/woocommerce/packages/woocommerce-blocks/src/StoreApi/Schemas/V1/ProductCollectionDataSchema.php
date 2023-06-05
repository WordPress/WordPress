<?php
namespace Automattic\WooCommerce\StoreApi\Schemas\V1;

/**
 * ProductCollectionDataSchema class.
 */
class ProductCollectionDataSchema extends AbstractSchema {
	/**
	 * The schema item name.
	 *
	 * @var string
	 */
	protected $title = 'product-collection-data';

	/**
	 * The schema item identifier.
	 *
	 * @var string
	 */
	const IDENTIFIER = 'product-collection-data';

	/**
	 * Product collection data schema properties.
	 *
	 * @return array
	 */
	public function get_properties() {
		return [
			'price_range'         => [
				'description' => __( 'Min and max prices found in collection of products, provided using the smallest unit of the currency.', 'woocommerce' ),
				'type'        => [ 'object', 'null' ],
				'context'     => [ 'view', 'edit' ],
				'readonly'    => true,
				'properties'  => array_merge(
					$this->get_store_currency_properties(),
					[
						'min_price' => [
							'description' => __( 'Min price found in collection of products.', 'woocommerce' ),
							'type'        => 'string',
							'context'     => [ 'view', 'edit' ],
							'readonly'    => true,
						],
						'max_price' => [
							'description' => __( 'Max price found in collection of products.', 'woocommerce' ),
							'type'        => 'string',
							'context'     => [ 'view', 'edit' ],
							'readonly'    => true,
						],
					]
				),
			],
			'attribute_counts'    => [
				'description' => __( 'Returns number of products within attribute terms.', 'woocommerce' ),
				'type'        => [ 'array', 'null' ],
				'context'     => [ 'view', 'edit' ],
				'readonly'    => true,
				'items'       => [
					'type'       => 'object',
					'properties' => [
						'term'  => [
							'description' => __( 'Term ID', 'woocommerce' ),
							'type'        => 'integer',
							'context'     => [ 'view', 'edit' ],
							'readonly'    => true,
						],
						'count' => [
							'description' => __( 'Number of products.', 'woocommerce' ),
							'type'        => 'integer',
							'context'     => [ 'view', 'edit' ],
							'readonly'    => true,
						],
					],
				],
			],
			'rating_counts'       => [
				'description' => __( 'Returns number of products with each average rating.', 'woocommerce' ),
				'type'        => [ 'array', 'null' ],
				'context'     => [ 'view', 'edit' ],
				'readonly'    => true,
				'items'       => [
					'type'       => 'object',
					'properties' => [
						'rating' => [
							'description' => __( 'Average rating', 'woocommerce' ),
							'type'        => 'integer',
							'context'     => [ 'view', 'edit' ],
							'readonly'    => true,
						],
						'count'  => [
							'description' => __( 'Number of products.', 'woocommerce' ),
							'type'        => 'integer',
							'context'     => [ 'view', 'edit' ],
							'readonly'    => true,
						],
					],
				],
			],
			'stock_status_counts' => [
				'description' => __( 'Returns number of products with each stock status.', 'woocommerce' ),
				'type'        => [ 'array', 'null' ],
				'context'     => [ 'view', 'edit' ],
				'readonly'    => true,
				'items'       => [
					'type'       => 'object',
					'properties' => [
						'status' => [
							'description' => __( 'Status', 'woocommerce' ),
							'type'        => 'string',
							'context'     => [ 'view', 'edit' ],
							'readonly'    => true,
						],
						'count'  => [
							'description' => __( 'Number of products.', 'woocommerce' ),
							'type'        => 'integer',
							'context'     => [ 'view', 'edit' ],
							'readonly'    => true,
						],
					],
				],
			],
		];
	}

	/**
	 * Format data.
	 *
	 * @param array $data Collection data to format and return.
	 * @return array
	 */
	public function get_item_response( $data ) {
		return [
			'price_range'         => ! is_null( $data['min_price'] ) && ! is_null( $data['max_price'] ) ? (object) $this->prepare_currency_response(
				[
					'min_price' => $this->prepare_money_response( $data['min_price'], wc_get_price_decimals() ),
					'max_price' => $this->prepare_money_response( $data['max_price'], wc_get_price_decimals() ),
				]
			) : null,
			'attribute_counts'    => $data['attribute_counts'],
			'rating_counts'       => $data['rating_counts'],
			'stock_status_counts' => $data['stock_status_counts'],
		];
	}
}

<?php
namespace Automattic\WooCommerce\StoreApi\Routes\V1;

/**
 * CartUpdateItem class.
 */
class CartUpdateItem extends AbstractCartRoute {
	/**
	 * The route identifier.
	 *
	 * @var string
	 */
	const IDENTIFIER = 'cart-update-item';

	/**
	 * Get the path of this REST route.
	 *
	 * @return string
	 */
	public function get_path() {
		return '/cart/update-item';
	}

	/**
	 * Get method arguments for this REST route.
	 *
	 * @return array An array of endpoints.
	 */
	public function get_args() {
		return [
			[
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'get_response' ],
				'permission_callback' => '__return_true',
				'args'                => [
					'key'      => [
						'description' => __( 'Unique identifier (key) for the cart item to update.', 'woocommerce' ),
						'type'        => 'string',
					],
					'quantity' => [
						'description' => __( 'New quantity of the item in the cart.', 'woocommerce' ),
						'type'        => 'integer',
					],
				],
			],
			'schema'      => [ $this->schema, 'get_public_item_schema' ],
			'allow_batch' => [ 'v1' => true ],
		];
	}

	/**
	 * Handle the request and return a valid response for this endpoint.
	 * .
	 *
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response
	 */
	protected function get_route_post_response( \WP_REST_Request $request ) {
		$cart = $this->cart_controller->get_cart_instance();

		if ( isset( $request['quantity'] ) ) {
			$this->cart_controller->set_cart_item_quantity( $request['key'], $request['quantity'] );
		}

		return rest_ensure_response( $this->schema->get_item_response( $cart ) );
	}
}

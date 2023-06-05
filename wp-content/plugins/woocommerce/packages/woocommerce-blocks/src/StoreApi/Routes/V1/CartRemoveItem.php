<?php
namespace Automattic\WooCommerce\StoreApi\Routes\V1;

use Automattic\WooCommerce\StoreApi\Utilities\DraftOrderTrait;
use Automattic\WooCommerce\StoreApi\Exceptions\RouteException;

/**
 * CartRemoveItem class.
 */
class CartRemoveItem extends AbstractCartRoute {
	use DraftOrderTrait;

	/**
	 * The route identifier.
	 *
	 * @var string
	 */
	const IDENTIFIER = 'cart-remove-item';

	/**
	 * Get the path of this REST route.
	 *
	 * @return string
	 */
	public function get_path() {
		return '/cart/remove-item';
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
					'key' => [
						'description' => __( 'Unique identifier (key) for the cart item.', 'woocommerce' ),
						'type'        => 'string',
					],
				],
			],
			'schema'      => [ $this->schema, 'get_public_item_schema' ],
			'allow_batch' => [ 'v1' => true ],
		];
	}

	/**
	 * Handle the request and return a valid response for this endpoint.
	 *
	 * @throws RouteException On error.
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response
	 */
	protected function get_route_post_response( \WP_REST_Request $request ) {
		$cart      = $this->cart_controller->get_cart_instance();
		$cart_item = $this->cart_controller->get_cart_item( $request['key'] );

		if ( empty( $cart_item ) ) {
			throw new RouteException( 'woocommerce_rest_cart_invalid_key', __( 'Cart item no longer exists or is invalid.', 'woocommerce' ), 409 );
		}

		$cart->remove_cart_item( $request['key'] );
		$this->maybe_release_stock();

		return rest_ensure_response( $this->schema->get_item_response( $cart ) );
	}

	/**
	 * If there is a draft order, releases stock.
	 *
	 * @return void
	 */
	protected function maybe_release_stock() {
		$draft_order_id = $this->get_draft_order_id();

		if ( ! $draft_order_id ) {
			return;
		}

		wc_release_stock_for_order( $draft_order_id );
	}
}

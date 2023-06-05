<?php
namespace Automattic\WooCommerce\StoreApi\Routes\V1;

use Automattic\WooCommerce\StoreApi\Exceptions\RouteException;

/**
 * CartItemsByKey class.
 */
class CartItemsByKey extends AbstractCartRoute {
	/**
	 * The route identifier.
	 *
	 * @var string
	 */
	const IDENTIFIER = 'cart-items-by-key';

	/**
	 * The routes schema.
	 *
	 * @var string
	 */
	const SCHEMA_TYPE = 'cart-item';

	/**
	 * Get the path of this REST route.
	 *
	 * @return string
	 */
	public function get_path() {
		return '/cart/items/(?P<key>[\w-]{32})';
	}

	/**
	 * Get method arguments for this REST route.
	 *
	 * @return array An array of endpoints.
	 */
	public function get_args() {
		return [
			'args'        => [
				'key' => [
					'description' => __( 'Unique identifier for the item within the cart.', 'woocommerce' ),
					'type'        => 'string',
				],
			],
			[
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_response' ],
				'permission_callback' => '__return_true',
				'args'                => [
					'context' => $this->get_context_param( [ 'default' => 'view' ] ),
				],
			],
			[
				'methods'             => \WP_REST_Server::EDITABLE,
				'callback'            => array( $this, 'get_response' ),
				'permission_callback' => '__return_true',
				'args'                => $this->schema->get_endpoint_args_for_item_schema( \WP_REST_Server::EDITABLE ),
			],
			[
				'methods'             => \WP_REST_Server::DELETABLE,
				'callback'            => [ $this, 'get_response' ],
				'permission_callback' => '__return_true',
			],
			'schema'      => [ $this->schema, 'get_public_item_schema' ],
			'allow_batch' => [ 'v1' => true ],
		];
	}

	/**
	 * Get a single cart items.
	 *
	 * @throws RouteException On error.
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response
	 */
	protected function get_route_response( \WP_REST_Request $request ) {
		$cart_item = $this->cart_controller->get_cart_item( $request['key'] );

		if ( empty( $cart_item ) ) {
			throw new RouteException( 'woocommerce_rest_cart_invalid_key', __( 'Cart item does not exist.', 'woocommerce' ), 409 );
		}

		$data     = $this->prepare_item_for_response( $cart_item, $request );
		$response = rest_ensure_response( $data );

		return $response;
	}

	/**
	 * Update a single cart item.
	 *
	 * @throws RouteException On error.
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response
	 */
	protected function get_route_update_response( \WP_REST_Request $request ) {
		$cart = $this->cart_controller->get_cart_instance();

		if ( isset( $request['quantity'] ) ) {
			$this->cart_controller->set_cart_item_quantity( $request['key'], $request['quantity'] );
		}

		return rest_ensure_response( $this->prepare_item_for_response( $this->cart_controller->get_cart_item( $request['key'] ), $request ) );
	}

	/**
	 * Delete a single cart item.
	 *
	 * @throws RouteException On error.
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response
	 */
	protected function get_route_delete_response( \WP_REST_Request $request ) {
		$cart      = $this->cart_controller->get_cart_instance();
		$cart_item = $this->cart_controller->get_cart_item( $request['key'] );

		if ( empty( $cart_item ) ) {
			throw new RouteException( 'woocommerce_rest_cart_invalid_key', __( 'Cart item does not exist.', 'woocommerce' ), 409 );
		}

		$cart->remove_cart_item( $request['key'] );

		return new \WP_REST_Response( null, 204 );
	}

	/**
	 * Prepare links for the request.
	 *
	 * @param array            $cart_item Object to prepare.
	 * @param \WP_REST_Request $request Request object.
	 * @return array
	 */
	protected function prepare_links( $cart_item, $request ) {
		$base  = $this->get_namespace() . $this->get_path();
		$links = array(
			'self'       => array(
				'href' => rest_url( trailingslashit( $base ) . $cart_item['key'] ),
			),
			'collection' => array(
				'href' => rest_url( $base ),
			),
		);
		return $links;
	}
}

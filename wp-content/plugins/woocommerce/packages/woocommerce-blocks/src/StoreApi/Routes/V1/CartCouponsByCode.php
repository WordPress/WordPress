<?php
namespace Automattic\WooCommerce\StoreApi\Routes\V1;

use Automattic\WooCommerce\StoreApi\Exceptions\RouteException;

/**
 * CartCouponsByCode class.
 */
class CartCouponsByCode extends AbstractCartRoute {
	/**
	 * The route identifier.
	 *
	 * @var string
	 */
	const IDENTIFIER = 'cart-coupons-by-code';

	/**
	 * The routes schema.
	 *
	 * @var string
	 */
	const SCHEMA_TYPE = 'cart-coupon';

	/**
	 * Get the path of this REST route.
	 *
	 * @return string
	 */
	public function get_path() {
		return '/cart/coupons/(?P<code>[\w-]+)';
	}

	/**
	 * Get method arguments for this REST route.
	 *
	 * @return array An array of endpoints.
	 */
	public function get_args() {
		return [
			'args'        => [
				'code' => [
					'description' => __( 'Unique identifier for the coupon within the cart.', 'woocommerce' ),
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
				'methods'             => \WP_REST_Server::DELETABLE,
				'callback'            => [ $this, 'get_response' ],
				'permission_callback' => '__return_true',
			],
			'schema'      => [ $this->schema, 'get_public_item_schema' ],
			'allow_batch' => [ 'v1' => true ],
		];
	}

	/**
	 * Get a single cart coupon.
	 *
	 * @throws RouteException On error.
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response
	 */
	protected function get_route_response( \WP_REST_Request $request ) {
		if ( ! $this->cart_controller->has_coupon( $request['code'] ) ) {
			throw new RouteException( 'woocommerce_rest_cart_coupon_invalid_code', __( 'Coupon does not exist in the cart.', 'woocommerce' ), 404 );
		}

		return $this->prepare_item_for_response( $request['code'], $request );
	}

	/**
	 * Delete a single cart coupon.
	 *
	 * @throws RouteException On error.
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response
	 */
	protected function get_route_delete_response( \WP_REST_Request $request ) {
		if ( ! $this->cart_controller->has_coupon( $request['code'] ) ) {
			throw new RouteException( 'woocommerce_rest_cart_coupon_invalid_code', __( 'Coupon does not exist in the cart.', 'woocommerce' ), 404 );
		}

		$cart = $this->cart_controller->get_cart_instance();

		$cart->remove_coupon( $request['code'] );
		$cart->calculate_totals();

		return new \WP_REST_Response( null, 204 );
	}
}

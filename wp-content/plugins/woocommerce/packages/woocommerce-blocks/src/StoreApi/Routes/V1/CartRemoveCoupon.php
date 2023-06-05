<?php
namespace Automattic\WooCommerce\StoreApi\Routes\V1;

use Automattic\WooCommerce\StoreApi\Exceptions\RouteException;

/**
 * CartRemoveCoupon class.
 */
class CartRemoveCoupon extends AbstractCartRoute {
	/**
	 * The route identifier.
	 *
	 * @var string
	 */
	const IDENTIFIER = 'cart-remove-coupon';

	/**
	 * Get the path of this REST route.
	 *
	 * @return string
	 */
	public function get_path() {
		return '/cart/remove-coupon';
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
					'code' => [
						'description' => __( 'Unique identifier for the coupon within the cart.', 'woocommerce' ),
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
		if ( ! wc_coupons_enabled() ) {
			throw new RouteException( 'woocommerce_rest_cart_coupon_disabled', __( 'Coupons are disabled.', 'woocommerce' ), 404 );
		}

		$cart        = $this->cart_controller->get_cart_instance();
		$coupon_code = wc_format_coupon_code( $request['code'] );
		$coupon      = new \WC_Coupon( $coupon_code );

		if ( $coupon->get_code() !== $coupon_code || ! $coupon->is_valid() ) {
			throw new RouteException( 'woocommerce_rest_cart_coupon_error', __( 'Invalid coupon code.', 'woocommerce' ), 400 );
		}

		if ( ! $this->cart_controller->has_coupon( $coupon_code ) ) {
			throw new RouteException( 'woocommerce_rest_cart_coupon_invalid_code', __( 'Coupon cannot be removed because it is not already applied to the cart.', 'woocommerce' ), 409 );
		}

		$cart = $this->cart_controller->get_cart_instance();
		$cart->remove_coupon( $coupon_code );
		$cart->calculate_totals();

		return rest_ensure_response( $this->schema->get_item_response( $cart ) );
	}
}

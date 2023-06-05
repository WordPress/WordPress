<?php
namespace Automattic\WooCommerce\StoreApi\Routes\V1;

use Automattic\WooCommerce\StoreApi\Exceptions\RouteException;

/**
 * CartCoupons class.
 */
class CartCoupons extends AbstractCartRoute {
	/**
	 * The route identifier.
	 *
	 * @var string
	 */
	const IDENTIFIER = 'cart-coupons';

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
		return '/cart/coupons';
	}

	/**
	 * Get method arguments for this REST route.
	 *
	 * @return array An array of endpoints.
	 */
	public function get_args() {
		return [
			[
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_response' ],
				'permission_callback' => '__return_true',
				'args'                => [
					'context' => $this->get_context_param( [ 'default' => 'view' ] ),
				],
			],
			[
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'get_response' ],
				'permission_callback' => '__return_true',
				'args'                => $this->schema->get_endpoint_args_for_item_schema( \WP_REST_Server::CREATABLE ),
			],
			[
				'methods'             => \WP_REST_Server::DELETABLE,
				'permission_callback' => '__return_true',
				'callback'            => [ $this, 'get_response' ],
			],
			'schema'      => [ $this->schema, 'get_public_item_schema' ],
			'allow_batch' => [ 'v1' => true ],
		];
	}

	/**
	 * Get a collection of cart coupons.
	 *
	 * @throws RouteException On error.
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response
	 */
	protected function get_route_response( \WP_REST_Request $request ) {
		$cart_coupons = $this->cart_controller->get_cart_coupons();
		$items        = [];

		foreach ( $cart_coupons as $coupon_code ) {
			$response = rest_ensure_response( $this->schema->get_item_response( $coupon_code ) );
			$response->add_links( $this->prepare_links( $coupon_code, $request ) );

			$response = $this->prepare_response_for_collection( $response );
			$items[]  = $response;
		}

		$response = rest_ensure_response( $items );

		return $response;
	}

	/**
	 * Add a coupon to the cart and return the result.
	 *
	 * @throws RouteException On error.
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response
	 */
	protected function get_route_post_response( \WP_REST_Request $request ) {
		if ( ! wc_coupons_enabled() ) {
			throw new RouteException( 'woocommerce_rest_cart_coupon_disabled', __( 'Coupons are disabled.', 'woocommerce' ), 404 );
		}

		try {
			$this->cart_controller->apply_coupon( $request['code'] );
		} catch ( \WC_REST_Exception $e ) {
			throw new RouteException( $e->getErrorCode(), $e->getMessage(), $e->getCode() );
		}

		$response = $this->prepare_item_for_response( $request['code'], $request );
		$response->set_status( 201 );

		return $response;
	}

	/**
	 * Deletes all coupons in the cart.
	 *
	 * @throws RouteException On error.
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response
	 */
	protected function get_route_delete_response( \WP_REST_Request $request ) {
		$cart = $this->cart_controller->get_cart_instance();

		$cart->remove_coupons();
		$cart->calculate_totals();

		return new \WP_REST_Response( [], 200 );
	}

	/**
	 * Prepare links for the request.
	 *
	 * @param string           $coupon_code Coupon code.
	 * @param \WP_REST_Request $request Request object.
	 * @return array
	 */
	protected function prepare_links( $coupon_code, $request ) {
		$base  = $this->get_namespace() . $this->get_path();
		$links = array(
			'self'       => array(
				'href' => rest_url( trailingslashit( $base ) . $coupon_code ),
			),
			'collection' => array(
				'href' => rest_url( $base ),
			),
		);
		return $links;
	}
}

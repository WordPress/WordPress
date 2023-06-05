<?php
namespace Automattic\WooCommerce\StoreApi\Routes\V1;

use Automattic\WooCommerce\StoreApi\Exceptions\RouteException;

/**
 * CartExtensions class.
 */
class CartExtensions extends AbstractCartRoute {
	/**
	 * The route identifier.
	 *
	 * @var string
	 */
	const IDENTIFIER = 'cart-extensions';

	/**
	 * The routes schema.
	 *
	 * @var string
	 */
	const SCHEMA_TYPE = 'cart-extensions';

	/**
	 * Get the path of this REST route.
	 *
	 * @return string
	 */
	public function get_path() {
		return '/cart/extensions';
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
					'namespace' => [
						'description' => __( 'Extension\'s name - this will be used to ensure the data in the request is routed appropriately.', 'woocommerce' ),
						'type'        => 'string',
					],
					'data'      => [
						'description' => __( 'Additional data to pass to the extension', 'woocommerce' ),
						'type'        => 'object',
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
		try {
			return $this->schema->get_item_response( $request );
		} catch ( \WC_REST_Exception $e ) {
			throw new RouteException( $e->getErrorCode(), $e->getMessage(), $e->getCode() );
		}
	}
}

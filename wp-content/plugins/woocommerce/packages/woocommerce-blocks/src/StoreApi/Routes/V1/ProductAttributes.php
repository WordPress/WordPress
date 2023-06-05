<?php
namespace Automattic\WooCommerce\StoreApi\Routes\V1;

/**
 * ProductAttributes class.
 */
class ProductAttributes extends AbstractRoute {
	/**
	 * The route identifier.
	 *
	 * @var string
	 */
	const IDENTIFIER = 'product-attributes';

	/**
	 * The routes schema.
	 *
	 * @var string
	 */
	const SCHEMA_TYPE = 'product-attribute';

	/**
	 * Get the path of this REST route.
	 *
	 * @return string
	 */
	public function get_path() {
		return '/products/attributes';
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
				'args'                => $this->get_collection_params(),
			],
			'schema' => [ $this->schema, 'get_public_item_schema' ],
		];
	}

	/**
	 * Get a collection of attributes.
	 *
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response
	 */
	protected function get_route_response( \WP_REST_Request $request ) {
		$ids    = wc_get_attribute_taxonomy_ids();
		$return = [];

		foreach ( $ids as $id ) {
			$object   = wc_get_attribute( $id );
			$data     = $this->prepare_item_for_response( $object, $request );
			$return[] = $this->prepare_response_for_collection( $data );
		}

		return rest_ensure_response( $return );
	}
}

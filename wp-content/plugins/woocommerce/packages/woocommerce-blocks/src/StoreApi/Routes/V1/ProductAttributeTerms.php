<?php
namespace Automattic\WooCommerce\StoreApi\Routes\V1;

use Automattic\WooCommerce\StoreApi\Exceptions\RouteException;

/**
 * ProductAttributeTerms class.
 */
class ProductAttributeTerms extends AbstractTermsRoute {
	/**
	 * The route identifier.
	 *
	 * @var string
	 */
	const IDENTIFIER = 'product-attribute-terms';

	/**
	 * Get the path of this REST route.
	 *
	 * @return string
	 */
	public function get_path() {
		return '/products/attributes/(?P<attribute_id>[\d]+)/terms';
	}

	/**
	 * Get method arguments for this REST route.
	 *
	 * @return array An array of endpoints.
	 */
	public function get_args() {
		return [
			'args'   => array(
				'attribute_id' => array(
					'description' => __( 'Unique identifier for the attribute.', 'woocommerce' ),
					'type'        => 'integer',
				),
			),
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
	 * Get a collection of attribute terms.
	 *
	 * @throws RouteException On error.
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response
	 */
	protected function get_route_response( \WP_REST_Request $request ) {
		$attribute = wc_get_attribute( $request['attribute_id'] );

		if ( ! $attribute || ! taxonomy_exists( $attribute->slug ) ) {
			throw new RouteException( 'woocommerce_rest_taxonomy_invalid', __( 'Attribute does not exist.', 'woocommerce' ), 404 );
		}

		return $this->get_terms_response( $attribute->slug, $request );
	}
}

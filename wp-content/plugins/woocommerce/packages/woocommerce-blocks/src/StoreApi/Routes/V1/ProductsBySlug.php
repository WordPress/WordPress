<?php
namespace Automattic\WooCommerce\StoreApi\Routes\V1;

use Automattic\WooCommerce\StoreApi\Exceptions\RouteException;

/**
 * ProductsBySlug class.
 */
class ProductsBySlug extends AbstractRoute {
	/**
	 * The route identifier.
	 *
	 * @var string
	 */
	const IDENTIFIER = 'products-by-slug';

	/**
	 * The routes schema.
	 *
	 * @var string
	 */
	const SCHEMA_TYPE = 'product';

	/**
	 * Get the path of this REST route.
	 *
	 * @return string
	 */
	public function get_path() {
		return '/products/(?P<slug>[\S]+)';
	}

	/**
	 * Get method arguments for this REST route.
	 *
	 * @return array An array of endpoints.
	 */
	public function get_args() {
		return [
			'args'   => array(
				'slug' => array(
					'description' => __( 'Slug of the resource.', 'woocommerce' ),
					'type'        => 'string',
				),
			),
			[
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_response' ],
				'permission_callback' => '__return_true',
				'args'                => array(
					'context' => $this->get_context_param(
						array(
							'default' => 'view',
						)
					),
				),
			],
			'schema' => [ $this->schema, 'get_public_item_schema' ],
		];
	}

	/**
	 * Get a single item.
	 *
	 * @throws RouteException On error.
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response
	 */
	protected function get_route_response( \WP_REST_Request $request ) {
		$slug = sanitize_title( $request['slug'] );

		$object = $this->get_product_by_slug( $slug );
		if ( ! $object ) {
			$object = $this->get_product_variation_by_slug( $slug );
		}

		if ( ! $object || 0 === $object->get_id() ) {
			throw new RouteException( 'woocommerce_rest_product_invalid_slug', __( 'Invalid product slug.', 'woocommerce' ), 404 );
		}

		return rest_ensure_response( $this->schema->get_item_response( $object ) );
	}

	/**
	 * Get a product  by slug.
	 *
	 * @param string $slug The slug of the product.
	 */
	public function get_product_by_slug( $slug ) {
		return wc_get_product( get_page_by_path( $slug, OBJECT, 'product' ) );
	}

	/**
	 * Get a product variation by slug.
	 *
	 * @param string $slug The slug of the product variation.
	 */
	private function get_product_variation_by_slug( $slug ) {
		global $wpdb;

		$result = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT ID, post_name, post_parent, post_type
				FROM $wpdb->posts
				WHERE post_name = %s
				AND post_type = 'product_variation'",
				$slug
			)
		);

		if ( ! $result ) {
			return null;
		}

		return wc_get_product( $result[0]->ID );
	}
}

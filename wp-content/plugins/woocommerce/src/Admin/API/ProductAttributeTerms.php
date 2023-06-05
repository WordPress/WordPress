<?php
/**
 * REST API Product Attribute Terms Controller
 *
 * Handles requests to /products/attributes/<slug>/terms
 */

namespace Automattic\WooCommerce\Admin\API;

defined( 'ABSPATH' ) || exit;

/**
 * Product attribute terms controller.
 *
 * @internal
 * @extends WC_REST_Product_Attribute_Terms_Controller
 */
class ProductAttributeTerms extends \WC_REST_Product_Attribute_Terms_Controller {
	use CustomAttributeTraits;

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'wc-analytics';


	/**
	 * Register the routes for custom product attributes.
	 */
	public function register_routes() {
		parent::register_routes();

		register_rest_route(
			$this->namespace,
			'products/attributes/(?P<slug>[a-z0-9_\-]+)/terms',
			array(
				'args'   => array(
					'slug' => array(
						'description' => __( 'Slug identifier for the resource.', 'woocommerce' ),
						'type'        => 'string',
					),
				),
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_item_by_slug' ),
					'permission_callback' => array( $this, 'get_custom_attribute_permissions_check' ),
					'args'                => $this->get_collection_params(),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Check if a given request has access to read a custom attribute.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function get_custom_attribute_permissions_check( $request ) {
		if ( ! wc_rest_check_manager_permissions( 'attributes', 'read' ) ) {
			return new WP_Error(
				'woocommerce_rest_cannot_view',
				__( 'Sorry, you cannot view this resource.', 'woocommerce' ),
				array(
					'status' => rest_authorization_required_code(),
				)
			);
		}

		return true;
	}

	/**
	 * Get the Attribute's schema, conforming to JSON Schema.
	 *
	 * @return array
	 */
	public function get_item_schema() {
		$schema = parent::get_item_schema();
		// Custom attributes substitute slugs for numeric IDs.
		$schema['properties']['id']['type'] = array( 'integer', 'string' );

		return $schema;
	}

	/**
	 * Query custom attribute values by slug.
	 *
	 * @param string $slug Attribute slug.
	 * @return array Attribute values, formatted for response.
	 */
	protected function get_custom_attribute_values( $slug ) {
		global $wpdb;

		if ( empty( $slug ) ) {
			return array();
		}

		$attribute_values = array();

		// Get the attribute properties.
		$attribute = $this->get_custom_attribute_by_slug( $slug );

		if ( is_wp_error( $attribute ) ) {
			return $attribute;
		}

		// Find all attribute values assigned to products.
		$query_results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT meta_value, COUNT(meta_id) AS product_count
				FROM {$wpdb->postmeta}
				WHERE meta_key = %s
				AND meta_value != ''
				GROUP BY meta_value",
				'attribute_' . esc_sql( $slug )
			),
			OBJECT_K
		);

		// Ensure all defined properties are in the response.
		$defined_values = wc_get_text_attributes( $attribute[ $slug ]['value'] );

		foreach ( $defined_values as $defined_value ) {
			if ( array_key_exists( $defined_value, $query_results ) ) {
				continue;
			}

			$query_results[ $defined_value ] = (object) array(
				'meta_value'    => $defined_value, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
				'product_count' => 0,
			);
		}

		foreach ( $query_results as $term_value => $term ) {
			// Mimic the structure of a taxonomy-backed attribute values for response.
			$data = array(
				'id'          => $term_value,
				'name'        => $term_value,
				'slug'        => $term_value,
				'description' => '',
				'menu_order'  => 0,
				'count'       => (int) $term->product_count,
			);

			$response = rest_ensure_response( $data );
			$response->add_links(
				array(
					'collection' => array(
						'href' => rest_url(
							$this->namespace . '/products/attributes/' . $slug . '/terms'
						),
					),
				)
			);
			$response = $this->prepare_response_for_collection( $response );

			$attribute_values[ $term_value ] = $response;
		}

		return array_values( $attribute_values );
	}

	/**
	 * Get a single custom attribute.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Request|WP_Error
	 */
	public function get_item_by_slug( $request ) {
		return $this->get_custom_attribute_values( $request['slug'] );
	}
}

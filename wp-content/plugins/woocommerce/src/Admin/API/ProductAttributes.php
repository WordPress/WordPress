<?php
/**
 * REST API Product Attributes Controller
 *
 * Handles requests to /products/attributes.
 */

namespace Automattic\WooCommerce\Admin\API;

defined( 'ABSPATH' ) || exit;

/**
 * Product categories controller.
 *
 * @internal
 * @extends WC_REST_Product_Attributes_Controller
 */
class ProductAttributes extends \WC_REST_Product_Attributes_Controller {
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
			'products/attributes/(?P<slug>[a-z0-9_\-]+)',
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
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Get the query params for collections
	 *
	 * @return array
	 */
	public function get_collection_params() {
		$params           = parent::get_collection_params();
		$params['search'] = array(
			'description'       => __( 'Search by similar attribute name.', 'woocommerce' ),
			'type'              => 'string',
			'validate_callback' => 'rest_validate_request_arg',
		);

		return $params;
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
	 * Get a single attribute by it's slug.
	 *
	 * @param WP_REST_Request $request The API request.
	 * @return WP_REST_Response
	 */
	public function get_item_by_slug( $request ) {
		if ( empty( $request['slug'] ) ) {
			return array();
		}

		$attributes = $this->get_custom_attribute_by_slug( $request['slug'] );

		if ( is_wp_error( $attributes ) ) {
			return $attributes;
		}

		$response_items = $this->format_custom_attribute_items_for_response( $attributes );

		return reset( $response_items );
	}

	/**
	 * Format custom attribute items for response (mimic the structure of a taxonomy - backed attribute).
	 *
	 * @param array $custom_attributes - CustomAttributeTraits::get_custom_attributes().
	 * @return array
	 */
	protected function format_custom_attribute_items_for_response( $custom_attributes ) {
		$response = array();

		foreach ( $custom_attributes as $attribute_key => $attribute_value ) {
			$data = array(
				'id'           => $attribute_key,
				'name'         => $attribute_value['name'],
				'slug'         => $attribute_key,
				'type'         => 'select',
				'order_by'     => 'menu_order',
				'has_archives' => false,
			);

			$item_response = rest_ensure_response( $data );
			$item_response->add_links( $this->prepare_links( (object) array( 'attribute_id' => $attribute_key ) ) );
			$item_response = $this->prepare_response_for_collection(
				$item_response
			);

			$response[] = $item_response;
		}

		return $response;
	}

	/**
	 * Get all attributes, with support for searching (which includes custom attributes).
	 *
	 * @param WP_REST_Request $request The API request.
	 * @return WP_REST_Response
	 */
	public function get_items( $request ) {
		if ( empty( $request['search'] ) ) {
			return parent::get_items( $request );
		}

		$search_string       = $request['search'];
		$custom_attributes   = $this->get_custom_attributes( array( 'name' => $search_string ) );
		$matching_attributes = $this->format_custom_attribute_items_for_response( $custom_attributes );
		$taxonomy_attributes = wc_get_attribute_taxonomies();

		foreach ( $taxonomy_attributes as $attribute_obj ) {
			// Skip taxonomy attributes that didn't match the query.
			if ( false === stripos( $attribute_obj->attribute_label, $search_string ) ) {
				continue;
			}

			$attribute             = $this->prepare_item_for_response( $attribute_obj, $request );
			$matching_attributes[] = $this->prepare_response_for_collection( $attribute );
		}

		$response = rest_ensure_response( $matching_attributes );
		$response->header( 'X-WP-Total', count( $matching_attributes ) );
		$response->header( 'X-WP-TotalPages', 1 );

		return $response;
	}
}

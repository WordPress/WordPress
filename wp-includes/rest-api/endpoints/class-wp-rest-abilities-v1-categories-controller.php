<?php
/**
 * REST API ability categories controller for Abilities API.
 *
 * @package WordPress
 * @subpackage Abilities_API
 * @since 6.9.0
 */

declare( strict_types = 1 );

/**
 * Core controller used to access ability categories via the REST API.
 *
 * @since 6.9.0
 *
 * @see WP_REST_Controller
 */
class WP_REST_Abilities_V1_Categories_Controller extends WP_REST_Controller {

	/**
	 * REST API namespace.
	 *
	 * @since 6.9.0
	 * @var string
	 */
	protected $namespace = 'wp-abilities/v1';

	/**
	 * REST API base route.
	 *
	 * @since 6.9.0
	 * @var string
	 */
	protected $rest_base = 'categories';

	/**
	 * Registers the routes for ability categories.
	 *
	 * @since 6.9.0
	 *
	 * @see register_rest_route()
	 */
	public function register_routes(): void {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_items' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
					'args'                => $this->get_collection_params(),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<slug>[a-z0-9]+(?:-[a-z0-9]+)*)',
			array(
				'args'   => array(
					'slug' => array(
						'description' => __( 'Unique identifier for the ability category.' ),
						'type'        => 'string',
						'pattern'     => '^[a-z0-9]+(?:-[a-z0-9]+)*$',
					),
				),
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_item' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Retrieves all ability categories.
	 *
	 * @since 6.9.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response Response object on success.
	 */
	public function get_items( $request ) {
		$categories = wp_get_ability_categories();

		$page     = $request['page'];
		$per_page = $request['per_page'];
		$offset   = ( $page - 1 ) * $per_page;

		$total_categories = count( $categories );
		$max_pages        = (int) ceil( $total_categories / $per_page );

		if ( $request->get_method() === 'HEAD' ) {
			$response = new WP_REST_Response( array() );
		} else {
			$categories = array_slice( $categories, $offset, $per_page );

			$data = array();
			foreach ( $categories as $category ) {
				$item   = $this->prepare_item_for_response( $category, $request );
				$data[] = $this->prepare_response_for_collection( $item );
			}

			$response = rest_ensure_response( $data );
		}

		$response->header( 'X-WP-Total', (string) $total_categories );
		$response->header( 'X-WP-TotalPages', (string) $max_pages );

		$query_params = $request->get_query_params();
		$base         = add_query_arg(
			urlencode_deep( $query_params ),
			rest_url( sprintf( '%s/%s', $this->namespace, $this->rest_base ) )
		);

		if ( $page > 1 ) {
			$prev_page = $page - 1;
			$prev_link = add_query_arg( 'page', $prev_page, $base );
			$response->link_header( 'prev', $prev_link );
		}

		if ( $page < $max_pages ) {
			$next_page = $page + 1;
			$next_link = add_query_arg( 'page', $next_page, $base );
			$response->link_header( 'next', $next_link );
		}

		return $response;
	}

	/**
	 * Retrieves a specific ability category.
	 *
	 * @since 6.9.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_item( $request ) {
		$category = wp_get_ability_category( $request['slug'] );
		if ( ! $category ) {
			return new WP_Error(
				'rest_ability_category_not_found',
				__( 'Ability category not found.' ),
				array( 'status' => 404 )
			);
		}

		$data = $this->prepare_item_for_response( $category, $request );
		return rest_ensure_response( $data );
	}

	/**
	 * Checks if a given request has access to read ability categories.
	 *
	 * @since 6.9.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return bool True if the request has read access.
	 */
	public function get_items_permissions_check( $request ) {
		return current_user_can( 'read' );
	}

	/**
	 * Checks if a given request has access to read an ability category.
	 *
	 * @since 6.9.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return bool True if the request has read access.
	 */
	public function get_item_permissions_check( $request ) {
		return current_user_can( 'read' );
	}

	/**
	 * Prepares an ability category for response.
	 *
	 * @since 6.9.0
	 *
	 * @param WP_Ability_Category $category The ability category object.
	 * @param WP_REST_Request     $request Request object.
	 * @return WP_REST_Response Response object.
	 */
	public function prepare_item_for_response( $category, $request ) {
		$data = array(
			'slug'        => $category->get_slug(),
			'label'       => $category->get_label(),
			'description' => $category->get_description(),
			'meta'        => $category->get_meta(),
		);

		$context = $request['context'] ?? 'view';
		$data    = $this->add_additional_fields_to_object( $data, $request );
		$data    = $this->filter_response_by_context( $data, $context );

		$response = rest_ensure_response( $data );

		$fields = $this->get_fields_for_response( $request );
		if ( rest_is_field_included( '_links', $fields ) || rest_is_field_included( '_embedded', $fields ) ) {
			$links = array(
				'self'       => array(
					'href' => rest_url( sprintf( '%s/%s/%s', $this->namespace, $this->rest_base, $category->get_slug() ) ),
				),
				'collection' => array(
					'href' => rest_url( sprintf( '%s/%s', $this->namespace, $this->rest_base ) ),
				),
				'abilities'  => array(
					'href' => rest_url( sprintf( '%s/abilities?category=%s', $this->namespace, $category->get_slug() ) ),
				),
			);

			$response->add_links( $links );
		}

		return $response;
	}

	/**
	 * Retrieves the ability category's schema, conforming to JSON Schema.
	 *
	 * @since 6.9.0
	 *
	 * @return array<string, mixed> Item schema data.
	 */
	public function get_item_schema(): array {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'ability-category',
			'type'       => 'object',
			'properties' => array(
				'slug'        => array(
					'description' => __( 'Unique identifier for the ability category.' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit', 'embed' ),
					'readonly'    => true,
				),
				'label'       => array(
					'description' => __( 'Display label for the category.' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit', 'embed' ),
					'readonly'    => true,
				),
				'description' => array(
					'description' => __( 'Description of the category.' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'meta'        => array(
					'description' => __( 'Meta information about the category.' ),
					'type'        => 'object',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
			),
		);

		return $this->add_additional_fields_schema( $schema );
	}

	/**
	 * Retrieves the query params for collections.
	 *
	 * @since 6.9.0
	 *
	 * @return array<string, mixed> Collection parameters.
	 */
	public function get_collection_params(): array {
		return array(
			'context'  => $this->get_context_param( array( 'default' => 'view' ) ),
			'page'     => array(
				'description' => __( 'Current page of the collection.' ),
				'type'        => 'integer',
				'default'     => 1,
				'minimum'     => 1,
			),
			'per_page' => array(
				'description' => __( 'Maximum number of items to be returned in result set.' ),
				'type'        => 'integer',
				'default'     => 50,
				'minimum'     => 1,
				'maximum'     => 100,
			),
		);
	}
}

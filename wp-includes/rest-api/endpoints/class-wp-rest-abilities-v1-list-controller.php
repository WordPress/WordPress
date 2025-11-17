<?php
/**
 * REST API list controller for Abilities API.
 *
 * @package WordPress
 * @subpackage Abilities_API
 * @since 6.9.0
 */

declare( strict_types = 1 );

/**
 * Core controller used to access abilities via the REST API.
 *
 * @since 6.9.0
 *
 * @see WP_REST_Controller
 */
class WP_REST_Abilities_V1_List_Controller extends WP_REST_Controller {

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
	protected $rest_base = 'abilities';

	/**
	 * Registers the routes for abilities.
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
			'/' . $this->rest_base . '/(?P<name>[a-zA-Z0-9\-\/]+)',
			array(
				'args'   => array(
					'name' => array(
						'description' => __( 'Unique identifier for the ability.' ),
						'type'        => 'string',
						'pattern'     => '^[a-zA-Z0-9\-\/]+$',
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
	 * Retrieves all abilities.
	 *
	 * @since 6.9.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response Response object on success.
	 */
	public function get_items( $request ) {
		$abilities = array_filter(
			wp_get_abilities(),
			static function ( $ability ) {
				return $ability->get_meta_item( 'show_in_rest' );
			}
		);

		// Filter by ability category if specified.
		$category = $request['category'];
		if ( ! empty( $category ) ) {
			$abilities = array_filter(
				$abilities,
				static function ( $ability ) use ( $category ) {
					return $ability->get_category() === $category;
				}
			);
			// Reset array keys after filtering.
			$abilities = array_values( $abilities );
		}

		$page     = $request['page'];
		$per_page = $request['per_page'];
		$offset   = ( $page - 1 ) * $per_page;

		$total_abilities = count( $abilities );
		$max_pages       = (int) ceil( $total_abilities / $per_page );

		if ( $request->get_method() === 'HEAD' ) {
			$response = new WP_REST_Response( array() );
		} else {
			$abilities = array_slice( $abilities, $offset, $per_page );

			$data = array();
			foreach ( $abilities as $ability ) {
				$item   = $this->prepare_item_for_response( $ability, $request );
				$data[] = $this->prepare_response_for_collection( $item );
			}

			$response = rest_ensure_response( $data );
		}

		$response->header( 'X-WP-Total', (string) $total_abilities );
		$response->header( 'X-WP-TotalPages', (string) $max_pages );

		$query_params = $request->get_query_params();
		$base         = add_query_arg( urlencode_deep( $query_params ), rest_url( sprintf( '%s/%s', $this->namespace, $this->rest_base ) ) );

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
	 * Retrieves a specific ability.
	 *
	 * @since 6.9.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_item( $request ) {
		$ability = wp_get_ability( $request['name'] );
		if ( ! $ability || ! $ability->get_meta_item( 'show_in_rest' ) ) {
			return new WP_Error(
				'rest_ability_not_found',
				__( 'Ability not found.' ),
				array( 'status' => 404 )
			);
		}

		$data = $this->prepare_item_for_response( $ability, $request );
		return rest_ensure_response( $data );
	}

	/**
	 * Checks if a given request has access to read ability items.
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
	 * Checks if a given request has access to read an ability item.
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
	 * Normalizes schema empty object defaults.
	 *
	 * Converts empty array defaults to objects when the schema type is 'object'
	 * to ensure proper JSON serialization as {} instead of [].
	 *
	 * @since 6.9.0
	 *
	 * @param array<string, mixed> $schema The schema array.
	 * @return array<string, mixed> The normalized schema.
	 */
	private function normalize_schema_empty_object_defaults( array $schema ): array {
		if ( isset( $schema['type'] ) && 'object' === $schema['type'] && isset( $schema['default'] ) ) {
			$default = $schema['default'];
			if ( is_array( $default ) && empty( $default ) ) {
				$schema['default'] = (object) $default;
			}
		}
		return $schema;
	}

	/**
	 * Prepares an ability for response.
	 *
	 * @since 6.9.0
	 *
	 * @param WP_Ability      $ability The ability object.
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response Response object.
	 */
	public function prepare_item_for_response( $ability, $request ) {
		$data = array(
			'name'          => $ability->get_name(),
			'label'         => $ability->get_label(),
			'description'   => $ability->get_description(),
			'category'      => $ability->get_category(),
			'input_schema'  => $this->normalize_schema_empty_object_defaults( $ability->get_input_schema() ),
			'output_schema' => $this->normalize_schema_empty_object_defaults( $ability->get_output_schema() ),
			'meta'          => $ability->get_meta(),
		);

		$context = $request['context'] ?? 'view';
		$data    = $this->add_additional_fields_to_object( $data, $request );
		$data    = $this->filter_response_by_context( $data, $context );

		$response = rest_ensure_response( $data );

		$fields = $this->get_fields_for_response( $request );
		if ( rest_is_field_included( '_links', $fields ) || rest_is_field_included( '_embedded', $fields ) ) {
			$links = array(
				'self'       => array(
					'href' => rest_url( sprintf( '%s/%s/%s', $this->namespace, $this->rest_base, $ability->get_name() ) ),
				),
				'collection' => array(
					'href' => rest_url( sprintf( '%s/%s', $this->namespace, $this->rest_base ) ),
				),
			);

			$links['wp:action-run'] = array(
				'href' => rest_url( sprintf( '%s/%s/%s/run', $this->namespace, $this->rest_base, $ability->get_name() ) ),
			);

			$response->add_links( $links );
		}

		return $response;
	}

	/**
	 * Retrieves the ability's schema, conforming to JSON Schema.
	 *
	 * @since 6.9.0
	 *
	 * @return array<string, mixed> Item schema data.
	 */
	public function get_item_schema(): array {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'ability',
			'type'       => 'object',
			'properties' => array(
				'name'          => array(
					'description' => __( 'Unique identifier for the ability.' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit', 'embed' ),
					'readonly'    => true,
				),
				'label'         => array(
					'description' => __( 'Display label for the ability.' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit', 'embed' ),
					'readonly'    => true,
				),
				'description'   => array(
					'description' => __( 'Description of the ability.' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'category'      => array(
					'description' => __( 'Ability category this ability belongs to.' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit', 'embed' ),
					'readonly'    => true,
				),
				'input_schema'  => array(
					'description' => __( 'JSON Schema for the ability input.' ),
					'type'        => 'object',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'output_schema' => array(
					'description' => __( 'JSON Schema for the ability output.' ),
					'type'        => 'object',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'meta'          => array(
					'description' => __( 'Meta information about the ability.' ),
					'type'        => 'object',
					'properties'  => array(
						'annotations' => array(
							'description' => __( 'Annotations for the ability.' ),
							'type'        => array( 'boolean', 'null' ),
							'default'     => null,
						),
					),
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
			'category' => array(
				'description'       => __( 'Limit results to abilities in specific ability category.' ),
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_key',
				'validate_callback' => 'rest_validate_request_arg',
			),
		);
	}
}

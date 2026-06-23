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
		$query_args = array(
			'meta' => array( 'show_in_rest' => true ),
		);

		if ( ! empty( $request['category'] ) ) {
			$query_args['category'] = $request['category'];
		}

		if ( ! empty( $request['namespace'] ) ) {
			$query_args['namespace'] = $request['namespace'];
		}

		if ( ! empty( $request['meta'] ) ) {
			// Merge caller meta first so the forced show_in_rest filter wins. This keeps a caller from using meta to reveal abilities hidden from REST.
			$query_args['meta'] = array_merge( $request['meta'], $query_args['meta'] );
		}

		$abilities = wp_get_abilities( $query_args );

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
	 * Additional schema keywords to preserve in REST responses.
	 *
	 * Ability schemas are exposed to clients as JSON Schema. Preserve additional
	 * draft-04 keywords so clients can validate richer schemas, even when some
	 * of those keywords are not enforced by the server-side REST schema validator.
	 *
	 * @since 7.1.0
	 * @var string[]
	 */
	private const ADDITIONAL_ALLOWED_SCHEMA_KEYWORDS = array(
		'required',
		'allOf',
		'not',
		'$ref',
		'definitions',
		'dependencies',
		'additionalItems',
	);

	/**
	 * Determines whether the value is an associative array.
	 *
	 * @since 7.1.0
	 *
	 * @param mixed $value Value.
	 * @return bool Whether it is associative array.
	 *
	 * @phpstan-assert-if-true array<string, mixed> $value
	 */
	private function is_associative_array( $value ): bool {
		return is_array( $value ) && ! wp_is_numeric_array( $value );
	}

	/**
	 * Transforms an ability schema for REST response output.
	 *
	 * The input and output schemas are a public contract: REST clients (such as
	 * the `@wordpress/abilities` JS client) consume them as standard JSON Schema
	 * and validate ability input and output against them. The response must
	 * therefore use JSON Schema draft-04 forms that standard validators
	 * understand, not the WordPress-internal conventions that
	 * `rest_validate_value_from_schema()` also accepts on the server.
	 *
	 * Ability schemas may include WordPress-internal properties or unsupported
	 * schema keywords that should not be exposed in REST responses. This method
	 * strips keys not recognized by the REST API schema handling. It also
	 * converts empty array defaults to objects when the schema type is 'object'
	 * to ensure proper JSON serialization as {} instead of [], and normalizes
	 * the `required` keyword from the draft-03 per-property boolean form into
	 * the draft-04 array of property names.
	 *
	 * @since 7.1.0
	 *
	 * @param array<string, mixed> $schema The schema array.
	 * @return array<string, mixed> The transformed schema.
	 */
	private function prepare_schema_for_response( array $schema ): array {
		if ( isset( $schema['type'] ) && 'object' === $schema['type'] && isset( $schema['default'] ) ) {
			$default = $schema['default'];
			if ( is_array( $default ) && empty( $default ) ) {
				$schema['default'] = (object) $default;
			}
		}

		// Computed once and reused across the recursive calls for every schema node.
		static $allowed_keywords = null;
		$allowed_keywords      ??= array_fill_keys(
			array_merge(
				rest_get_allowed_schema_keywords(),
				self::ADDITIONAL_ALLOWED_SCHEMA_KEYWORDS
			),
			true
		);

		$schema = array_intersect_key( $schema, $allowed_keywords );

		// Collect draft-03 per-property `required: true` flags into a draft-04
		// `required` array of property names on the parent object schema.
		//
		// This mirrors rest_validate_object_value_from_schema(), where a draft-04
		// `required` array takes precedence: when one is present, per-property
		// booleans are ignored during validation. They are therefore left out of
		// the array here as well (but still stripped from the output) so the
		// published schema describes exactly what gets enforced.
		if ( isset( $schema['properties'] ) && is_array( $schema['properties'] ) ) {
			$has_required_array = isset( $schema['required'] ) && is_array( $schema['required'] );
			$required           = array();
			foreach ( $schema['properties'] as $property => &$property_schema ) {
				if ( $this->is_associative_array( $property_schema ) && isset( $property_schema['required'] ) && is_bool( $property_schema['required'] ) ) {
					if ( ! $has_required_array && true === $property_schema['required'] ) {
						$required[] = (string) $property;
					}
					unset( $property_schema['required'] );
				}
			}
			unset( $property_schema );

			// Property keys are unique, so the collected list needs no deduplication.
			// When a draft-04 array is already present, leave it untouched.
			if ( ! $has_required_array && count( $required ) > 0 ) {
				$schema['required'] = $required;
			}
		}

		// A boolean `required` outside of an object's property list has no draft-04
		// equivalent, so drop it rather than emit an invalid keyword.
		if ( isset( $schema['required'] ) && is_bool( $schema['required'] ) ) {
			unset( $schema['required'] );
		}

		// Sub-schema maps: keys are user-defined, values are sub-schemas.
		// Note: 'dependencies' values can also be property-dependency arrays
		// (numeric arrays of strings) which are skipped via wp_is_numeric_array().
		foreach ( array( 'properties', 'patternProperties', 'definitions', 'dependencies' ) as $keyword ) {
			if ( isset( $schema[ $keyword ] ) && is_array( $schema[ $keyword ] ) ) {
				foreach ( $schema[ $keyword ] as $key => $child_schema ) {
					if ( $this->is_associative_array( $child_schema ) ) {
						$schema[ $keyword ][ $key ] = $this->prepare_schema_for_response( $child_schema );
					}
				}
			}
		}

		// Single sub-schema keywords.
		foreach ( array( 'not', 'additionalProperties', 'additionalItems' ) as $keyword ) {
			if ( isset( $schema[ $keyword ] ) && $this->is_associative_array( $schema[ $keyword ] ) ) {
				$schema[ $keyword ] = $this->prepare_schema_for_response( $schema[ $keyword ] );
			}
		}

		// Items: single schema or tuple array of schemas.
		if ( isset( $schema['items'] ) && is_array( $schema['items'] ) ) {
			if ( $this->is_associative_array( $schema['items'] ) ) {
				$schema['items'] = $this->prepare_schema_for_response( $schema['items'] );
			} else {
				foreach ( $schema['items'] as $index => $item_schema ) {
					if ( $this->is_associative_array( $item_schema ) ) {
						$schema['items'][ $index ] = $this->prepare_schema_for_response( $item_schema );
					}
				}
			}
		}

		// Array-of-schemas keywords.
		foreach ( array( 'anyOf', 'oneOf', 'allOf' ) as $keyword ) {
			if ( isset( $schema[ $keyword ] ) && is_array( $schema[ $keyword ] ) ) {
				foreach ( $schema[ $keyword ] as $index => $sub_schema ) {
					if ( $this->is_associative_array( $sub_schema ) ) {
						$schema[ $keyword ][ $index ] = $this->prepare_schema_for_response( $sub_schema );
					}
				}
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
			'input_schema'  => $this->prepare_schema_for_response( $ability->get_input_schema() ),
			'output_schema' => $this->prepare_schema_for_response( $ability->get_output_schema() ),
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
							'description'          => __( 'Behavioral annotations for the ability.' ),
							'type'                 => 'object',
							'properties'           => array(
								'readonly'    => array(
									'description' => __( 'Whether the ability does not modify its environment.' ),
									'type'        => array( 'boolean', 'null' ),
								),
								'destructive' => array(
									'description' => __( 'Whether the ability may perform destructive updates to its environment.' ),
									'type'        => array( 'boolean', 'null' ),
								),
								'idempotent'  => array(
									'description' => __( 'Whether repeated calls with the same arguments have no additional effect.' ),
									'type'        => array( 'boolean', 'null' ),
								),
							),
							'additionalProperties' => true,
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
		$query_params = array(
			'context'   => $this->get_context_param( array( 'default' => 'view' ) ),
			'page'      => array(
				'description' => __( 'Current page of the collection.' ),
				'type'        => 'integer',
				'default'     => 1,
				'minimum'     => 1,
			),
			'per_page'  => array(
				'description' => __( 'Maximum number of items to be returned in result set.' ),
				'type'        => 'integer',
				'default'     => 50,
				'minimum'     => 1,
				'maximum'     => 100,
			),
			'category'  => array(
				'description'       => __( 'Limit results to abilities in specific ability category.' ),
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_key',
				'validate_callback' => 'rest_validate_request_arg',
			),
			'namespace' => array(
				'description'       => __( 'Limit results to abilities in a specific namespace.' ),
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_key',
				'validate_callback' => 'rest_validate_request_arg',
			),
			'meta'      => array(
				'description'          => __( 'Limit results to abilities matching all of the given meta fields.' ),
				'type'                 => 'object',
				'properties'           => array(
					// show_in_rest is omitted on purpose. It is forced on and cannot be filtered by a caller.
					'annotations' => array(
						'description'          => __( 'Limit results to abilities matching the given behavioral annotations.' ),
						'type'                 => 'object',
						'properties'           => array(
							'readonly'    => array(
								'description' => __( 'Whether the ability does not modify its environment.' ),
								'type'        => array( 'boolean', 'null' ),
							),
							'destructive' => array(
								'description' => __( 'Whether the ability may perform destructive updates to its environment.' ),
								'type'        => array( 'boolean', 'null' ),
							),
							'idempotent'  => array(
								'description' => __( 'Whether repeated calls with the same arguments have no additional effect.' ),
								'type'        => array( 'boolean', 'null' ),
							),
						),
						'additionalProperties' => true,
					),
				),
				'additionalProperties' => true,
			),
		);

		/**
		 * Filters REST API collection parameters for the abilities controller.
		 *
		 * Use this to declare the schema type of a custom meta key. A declared
		 * type lets REST coerce a query-string value, for example "true" to a
		 * boolean, before the meta filter matches it.
		 *
		 * @since 7.1.0
		 *
		 * @param array $query_params JSON Schema-formatted collection parameters.
		 */
		return apply_filters( 'rest_abilities_collection_params', $query_params );
	}
}

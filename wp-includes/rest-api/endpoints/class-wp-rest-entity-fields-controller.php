<?php
/**
 * REST API: WP_REST_Entity_Fields_Controller class
 *
 * @package    WordPress
 * @subpackage REST_API
 * @since      7.2.0
 */

/**
 * Controller which provides a REST endpoint for retrieving the fields
 * registered for a given entity type.
 *
 * The fields are the ones registered on the server on the
 * `wp_entity_fields_api_init` action: the serializable part of each field, plus
 * the script modules that provide the JavaScript parts (render callbacks,
 * components, value getters and setters), each with the ids of the fields
 * it applies to. The client merges both into the fields it derives itself.
 *
 * @since 7.2.0
 *
 * @see WP_REST_Controller
 */
class WP_REST_Entity_Fields_Controller extends WP_REST_Controller {

	/**
	 * Constructor.
	 *
	 * @since 7.2.0
	 */
	public function __construct() {
		$this->namespace = 'wp/v2';
		$this->rest_base = 'entity-fields';
	}

	/**
	 * Registers the routes for the controller.
	 *
	 * @since 7.2.0
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_items' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
					'args'                => array(
						'kind' => array(
							'description' => __( 'Entity kind.' ),
							'type'        => 'string',
							'required'    => true,
						),
						'name' => array(
							'description' => __( 'Entity name.' ),
							'type'        => 'string',
							'required'    => true,
						),
					),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Checks if a given request has access to read the fields of an entity.
	 *
	 * @since 7.2.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function get_items_permissions_check( $request ) {
		$kind = $request->get_param( 'kind' );
		$name = $request->get_param( 'name' );

		$capability = $this->get_required_capability( $kind, $name );

		if ( null === $capability ) {
			return new WP_Error(
				'rest_entity_fields_invalid_entity',
				__( 'Invalid entity kind or name.' ),
				array( 'status' => 404 )
			);
		}

		if ( ! current_user_can( $capability ) ) {
			return new WP_Error(
				'rest_cannot_read',
				__( 'Sorry, you are not allowed to read the fields of this entity.' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Resolves the capability required to read the fields of an entity.
	 *
	 * Mirrors the view config endpoint, which gates the same entities: post
	 * types use their own `edit_posts` capability (which honors custom
	 * `capability_type` registrations), taxonomies use `manage_terms`, and
	 * root-level entities use `manage_options`. A post type or taxonomy that is
	 * not registered, or not exposed to the REST API, resolves to `null` so the
	 * request is treated as referencing an unknown entity.
	 *
	 * Any other kind falls back to `edit_posts`, so fields registered for a
	 * custom entity kind stay readable behind a baseline capability.
	 *
	 * @since 7.2.0
	 *
	 * @param string $kind The entity kind (e.g. `postType`).
	 * @param string $name The entity name (e.g. `page`).
	 * @return string|null Capability required to read the fields, or null if
	 *                     the entity is not registered.
	 */
	protected function get_required_capability( $kind, $name ) {
		switch ( $kind ) {
			case 'postType':
				$post_type = get_post_type_object( $name );
				if ( $post_type && $post_type->show_in_rest ) {
					return $post_type->cap->edit_posts;
				}
				return null;

			case 'taxonomy':
				$taxonomy = get_taxonomy( $name );
				if ( $taxonomy && $taxonomy->show_in_rest ) {
					return $taxonomy->cap->manage_terms;
				}
				return null;

			case 'root':
				return 'manage_options';
		}

		return 'edit_posts';
	}

	/**
	 * Returns the fields registered for the given entity type.
	 *
	 * @since 7.2.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_items( $request ) {
		$kind = $request->get_param( 'kind' );
		$name = $request->get_param( 'name' );

		$field_schema = $this->get_field_schema();
		$fields       = array();
		foreach ( wp_get_registered_entity_fields( $kind, $name ) as $field ) {
			$fields[] = wp_prepare_json_value_for_client( $field, $field_schema );
		}

		$script_modules = array();
		foreach ( wp_get_registered_entity_field_modules( $kind, $name ) as $module => $field_ids ) {
			$script_modules[] = array(
				'id'     => $module,
				'fields' => array_values( $field_ids ),
			);
		}

		$response = array(
			'kind'           => $kind,
			'name'           => $name,
			'fields'         => $fields,
			'script_modules' => $script_modules,
		);

		return rest_ensure_response( $response );
	}

	/**
	 * Retrieves the item's schema, conforming to JSON Schema.
	 *
	 * @since 7.2.0
	 *
	 * @return array Item schema data.
	 */
	public function get_item_schema() {
		if ( $this->schema ) {
			return $this->add_additional_fields_schema( $this->schema );
		}

		$this->schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'entity-fields',
			'type'       => 'object',
			'properties' => array(
				'kind'           => array(
					'description' => __( 'Entity kind.' ),
					'type'        => 'string',
					'readonly'    => true,
				),
				'name'           => array(
					'description' => __( 'Entity name.' ),
					'type'        => 'string',
					'readonly'    => true,
				),
				'fields'         => array(
					'description' => __( 'The fields registered for the entity, in registration order.' ),
					'type'        => 'array',
					'readonly'    => true,
					'items'       => $this->get_field_schema(),
				),
				'script_modules' => array(
					'description' => __( 'The script modules providing the JavaScript parts of the fields, each with the ids of the fields it applies to.' ),
					'type'        => 'array',
					'readonly'    => true,
					'items'       => array(
						'type'       => 'object',
						'properties' => array(
							'id'     => array(
								'description' => __( 'The id of the script module.' ),
								'type'        => 'string',
							),
							'fields' => array(
								'description' => __( 'The ids of the fields the script module applies to.' ),
								'type'        => 'array',
								'items'       => array(
									'type' => 'string',
								),
							),
						),
					),
				),
			),
		);

		return $this->add_additional_fields_schema( $this->schema );
	}

	/**
	 * Returns the schema for a field definition.
	 *
	 * Describes the serializable subset of the DataViews Field API, see
	 * packages/dataviews/src/types/field-api.ts in Gutenberg. The properties
	 * that are JavaScript callbacks or components (`render`, `sort`,
	 * `getValue`, …) come from the script modules and are not part of the
	 * definition.
	 * Additional properties are allowed, so a plugin can register a field
	 * with properties of its own that its script module understands.
	 *
	 * @since 7.2.0
	 *
	 * @return array Schema for a field definition.
	 */
	protected function get_field_schema() {
		$option_schema = array(
			'type'       => 'object',
			'properties' => array(
				'value'       => array(
					'description' => __( 'The value of the option.' ),
					'type'        => array( 'string', 'integer', 'number', 'boolean', 'null' ),
				),
				'label'       => array(
					'description' => __( 'The label of the option.' ),
					'type'        => 'string',
				),
				'description' => array(
					'description' => __( 'The description of the option.' ),
					'type'        => 'string',
				),
			),
		);

		return array(
			'type'                 => 'object',
			'properties'           => array(
				'id'                 => array(
					'description' => __( 'The unique identifier of the field.' ),
					'type'        => 'string',
				),
				'origin'             => array(
					'description' => sprintf(
						/* translators: %s: core */
						__( 'Who registered and updated the field: %s, or the slug of a plugin or theme.' ),
						'core'
					),
					'type'        => 'object',
					'properties'  => array(
						'registeredBy' => array(
							'description' => __( 'Who registered the field.' ),
							'type'        => 'string',
						),
						'updatedBy'    => array(
							'description' => __( 'Who updated the field, in update order.' ),
							'type'        => 'array',
							'items'       => array(
								'type' => 'string',
							),
						),
					),
				),
				'type'               => array(
					'description' => __( 'The type of the field, one of the types DataViews provides a control for.' ),
					'type'        => 'string',
					'enum'        => array( 'array', 'boolean', 'color', 'date', 'datetime', 'email', 'integer', 'media', 'number', 'password', 'telephone', 'text', 'time', 'url' ),
				),
				'label'              => array(
					'description' => __( 'The label of the field.' ),
					'type'        => 'string',
				),
				'header'             => array(
					'description' => __( 'The header of the field. Defaults to the label.' ),
					'type'        => 'string',
				),
				'description'        => array(
					'description' => __( 'The description of the field.' ),
					'type'        => 'string',
				),
				'placeholder'        => array(
					'description' => __( 'The placeholder of the field.' ),
					'type'        => 'string',
				),
				'Edit'               => array(
					'description' => __( 'The control used to edit the field: the name of a control, or a control configuration.' ),
					'type'        => array( 'string', 'object' ),
				),
				'isValid'            => array(
					'description' => __( 'The validation rules of the field.' ),
					'type'        => 'object',
				),
				'isDisabled'         => array(
					'description' => __( 'Whether the field is disabled.' ),
					'type'        => 'boolean',
				),
				'enableSorting'      => array(
					'description' => __( 'Whether the field is sortable.' ),
					'type'        => 'boolean',
				),
				'enableGlobalSearch' => array(
					'description' => __( 'Whether the field is searchable.' ),
					'type'        => 'boolean',
				),
				'enableHiding'       => array(
					'description' => __( 'Whether the field can be hidden.' ),
					'type'        => 'boolean',
				),
				'elements'           => array(
					'description' => __( 'The options to pick from when using the field as a filter.' ),
					'type'        => 'array',
					'items'       => $option_schema,
				),
				'filterBy'           => array(
					'description' => __( 'The filter configuration of the field, or false when the field cannot be filtered.' ),
					'type'        => array( 'object', 'boolean' ),
					'properties'  => array(
						'operators' => array(
							'type'  => 'array',
							'items' => array(
								'type' => 'string',
								'enum' => array(
									'is',
									'isNot',
									'isAny',
									'isNone',
									'isAll',
									'lessThan',
									'greaterThan',
									'lessThanOrEqual',
									'greaterThanOrEqual',
									'before',
									'after',
									'beforeInc',
									'afterInc',
									'contains',
									'notContains',
									'startsWith',
									'between',
									'on',
									'notOn',
									'inThePast',
									'over',
								),
							),
						),
						'isPrimary' => array(
							'type' => 'boolean',
						),
					),
				),
				'readOnly'           => array(
					'description' => __( 'Whether the field is read only.' ),
					'type'        => 'boolean',
				),
				'format'             => array(
					'description' => __( 'The display format of the field.' ),
					'type'        => 'object',
				),
			),
			'additionalProperties' => true,
		);
	}
}

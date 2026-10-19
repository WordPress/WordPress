<?php
/**
 * REST API: WP_REST_Entity_Actions_Controller class
 *
 * @package    WordPress
 * @subpackage REST_API
 * @since      7.2.0
 */

/**
 * Controller which provides a REST endpoint for retrieving the actions
 * registered for a given entity type.
 *
 * The actions are the ones registered on the server on the
 * `wp_entity_actions_api_init` action: the serializable part of each action, plus
 * the script modules that provide the JavaScript parts (callbacks, modals,
 * eligibility checks, icons), each with the ids of the actions it applies
 * to. The client merges both into the actions it shows.
 *
 * @since 7.2.0
 *
 * @see WP_REST_Controller
 */
class WP_REST_Entity_Actions_Controller extends WP_REST_Controller {

	/**
	 * Constructor.
	 *
	 * @since 7.2.0
	 */
	public function __construct() {
		$this->namespace = 'wp/v2';
		$this->rest_base = 'entity-actions';
	}

	/**
	 * Registers the routes for the controller.
	 *
	 * @since 7.2.0
	 */
	public function register_routes() {
		if ( $this->is_route_of_entity_items() ) {
			return;
		}

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
							'minLength'   => 1,
							'required'    => true,
						),
						'name' => array(
							'description' => __( 'Entity name.' ),
							'type'        => 'string',
							'minLength'   => 1,
							'required'    => true,
						),
					),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Checks whether a post type or taxonomy has its items on the route of
	 * the controller, reporting it with _doing_it_wrong().
	 *
	 * The route is then left to the post type or taxonomy: registering it
	 * as well would not serve the actions, as the route of the items comes
	 * first, and would replace the schema of that route.
	 *
	 * @since 7.2.0
	 *
	 * @return bool Whether a post type or taxonomy has its items on the route.
	 */
	protected function is_route_of_entity_items() {
		$objects = array_merge(
			array_values( get_post_types( array( 'show_in_rest' => true ), 'objects' ) ),
			array_values( get_taxonomies( array( 'show_in_rest' => true ), 'objects' ) )
		);
		foreach ( $objects as $object ) {
			$namespace = $object->rest_namespace ? $object->rest_namespace : 'wp/v2';
			$base      = $object->rest_base ? $object->rest_base : $object->name;
			if ( $this->namespace === $namespace && $this->rest_base === $base ) {
				_doing_it_wrong(
					__METHOD__,
					sprintf(
						/* translators: 1: REST route, e.g. /wp/v2/entity-actions. 2: Post type or taxonomy name. */
						__( 'The %1$s route serves the items of the "%2$s" post type or taxonomy, so the actions of entities are not available.' ),
						'/' . $this->namespace . '/' . $this->rest_base,
						$object->name
					),
					'7.2.0'
				);
				return true;
			}
		}

		return false;
	}

	/**
	 * Checks if a given request has access to read the actions of an entity.
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
				'rest_entity_actions_invalid_entity',
				__( 'Invalid entity kind or name.' ),
				array( 'status' => 404 )
			);
		}

		if ( ! current_user_can( $capability ) ) {
			return new WP_Error(
				'rest_cannot_read',
				__( 'Sorry, you are not allowed to read the actions of this entity.' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Resolves the capability required to read the actions of an entity.
	 *
	 * Mirrors the fields endpoint, which gates the same entities: post types
	 * use their own `edit_posts` capability (which honors custom
	 * `capability_type` registrations), taxonomies use `manage_terms`, and
	 * root-level entities use `manage_options`. A post type or taxonomy that is
	 * not registered, or not exposed to the REST API, resolves to `null` so the
	 * request is treated as referencing an unknown entity.
	 *
	 * Any other kind falls back to `edit_posts`, so actions registered for a
	 * custom entity kind stay readable behind a baseline capability.
	 *
	 * @since 7.2.0
	 *
	 * @param string $kind The entity kind (e.g. `postType`).
	 * @param string $name The entity name (e.g. `page`).
	 * @return string|null Capability required to read the actions, or null if
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
	 * Returns the actions registered for the given entity type.
	 *
	 * @since 7.2.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_items( $request ) {
		$kind = $request->get_param( 'kind' );
		$name = $request->get_param( 'name' );

		$script_modules = array();
		foreach ( wp_get_registered_entity_action_modules( $kind, $name ) as $module => $action_ids ) {
			$script_modules[] = array(
				// A numeric module id is an integer as an array key.
				'id'      => (string) $module,
				'actions' => array_values( $action_ids ),
			);
		}

		$response = array(
			'kind'           => $kind,
			'name'           => $name,
			'actions'        => wp_get_registered_entity_actions( $kind, $name ),
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
			'title'      => 'entity-actions',
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
				'actions'        => array(
					'description' => __( 'The actions registered for the entity, in registration order.' ),
					'type'        => 'array',
					'readonly'    => true,
					'items'       => $this->get_action_schema(),
				),
				'script_modules' => array(
					'description' => __( 'The script modules providing the JavaScript parts of the actions, each with the ids of the actions it applies to.' ),
					'type'        => 'array',
					'readonly'    => true,
					'items'       => array(
						'type'       => 'object',
						'properties' => array(
							'id'      => array(
								'description' => __( 'The id of the script module.' ),
								'type'        => 'string',
							),
							'actions' => array(
								'description' => __( 'The ids of the actions the script module applies to.' ),
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
	 * Returns the schema for an action definition.
	 *
	 * Describes the serializable subset of the DataViews Action API, see
	 * packages/dataviews/src/types/dataviews.ts. The properties that are
	 * JavaScript callbacks, components, or elements (`callback`,
	 * `RenderModal`, `isEligible`, `icon`, …) come from the script modules and
	 * are not part of the definition. Additional properties are allowed, so a
	 * plugin can register an action with properties of its own that its script
	 * module understands.
	 *
	 * @since 7.2.0
	 *
	 * @return array Schema for an action definition.
	 */
	protected function get_action_schema() {
		return array(
			'type'                 => 'object',
			'properties'           => array(
				'id'                => array(
					'description' => __( 'The unique identifier of the action.' ),
					'type'        => 'string',
				),
				'origin'            => array(
					'description' => __( 'Who registered the action: `core`, or the slug of a plugin or theme.' ),
					'type'        => 'object',
					'properties'  => array(
						'registeredBy' => array(
							'description' => __( 'Who registered the action.' ),
							'type'        => 'string',
						),
					),
				),
				'label'             => array(
					'description' => __( 'The label of the action.' ),
					'type'        => 'string',
				),
				'isPrimary'         => array(
					'description' => __( 'Whether the action is a primary action.' ),
					'type'        => 'boolean',
				),
				'supportsBulk'      => array(
					'description' => __( 'Whether the action can be performed on several items at once.' ),
					'type'        => 'boolean',
				),
				'disabled'          => array(
					'description' => __( 'Whether the action is disabled.' ),
					'type'        => 'boolean',
				),
				'context'           => array(
					'description' => __( 'The context the action is limited to, such as `list` for the lists of items. Without it, the action shows everywhere.' ),
					'type'        => 'string',
					'enum'        => WP_Entity_Actions_Registry::SUPPORTED_VALUES['context'],
				),
				'modalHeader'       => array(
					'description' => __( 'The header of the modal of the action.' ),
					'type'        => 'string',
				),
				'hideModalHeader'   => array(
					'description' => __( 'Whether to hide the header of the modal of the action.' ),
					'type'        => 'boolean',
				),
				'modalSize'         => array(
					'description' => __( 'The size of the modal of the action.' ),
					'type'        => 'string',
					'enum'        => WP_Entity_Actions_Registry::SUPPORTED_VALUES['modalSize'],
				),
				'modalFocusOnMount' => array(
					'description' => __( 'Where the focus goes when the modal of the action opens.' ),
					'type'        => array( 'boolean', 'string' ),
				),
			),
			'additionalProperties' => true,
		);
	}
}

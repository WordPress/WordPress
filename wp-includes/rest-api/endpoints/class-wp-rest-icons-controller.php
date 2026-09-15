<?php

/**
 * REST API: WP_REST_Icons_Controller class
 *
 * @package    WordPress
 * @subpackage REST_API
 * @since      7.0.0
 */

/**
 * Controller which provides a REST endpoint for the editor to read registered
 * icons. Icons are grouped into collections (the default one being `core`).
 *
 * @since 7.0.0
 *
 * @see WP_REST_Controller
 */
class WP_REST_Icons_Controller extends WP_REST_Controller {

	/**
	 * Constructs the controller.
	 *
	 * @since 7.0.0
	 */
	public function __construct() {
		$this->namespace = 'wp/v2';
		$this->rest_base = 'icons';
	}

	/**
	 * Registers the routes for the objects of the controller.
	 *
	 * @since 7.0.0
	 * @since 7.1.0 Added the `/icons/<collection>` collection-scoped route.
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
					'args'                => $this->get_collection_params(),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<collection>[a-z0-9](?:[a-z0-9_-]*[a-z0-9])?)',
			array(
				'args'   => array(
					'collection' => array(
						'description' => __( 'Icon collection slug.' ),
						'type'        => 'string',
					),
				),
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
			'/' . $this->rest_base . '/(?P<name>[a-z0-9](?:[a-z0-9_-]*[a-z0-9])?/[a-z0-9](?:[a-z0-9_-]*[a-z0-9])?)',
			array(
				'args'   => array(
					'name' => array(
						'description' => __( 'Icon name.' ),
						'type'        => 'string',
					),
				),
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_item' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
					'args'                => array(
						'context' => $this->get_context_param( array( 'default' => 'view' ) ),
					),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Checks whether a given request has permission to read icons.
	 *
	 * @since 7.0.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function get_items_permissions_check(
		// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		$request
	) {
		if ( current_user_can( 'edit_posts' ) ) {
			return true;
		}

		foreach ( get_post_types( array( 'show_in_rest' => true ), 'objects' ) as $post_type ) {
			if ( current_user_can( $post_type->cap->edit_posts ) ) {
				return true;
			}
		}

		return new WP_Error(
			'rest_cannot_view',
			__( 'Sorry, you are not allowed to view the registered icons.' ),
			array( 'status' => rest_authorization_required_code() )
		);
	}

	/**
	 * Checks if a given request has access to read a specific icon.
	 *
	 * @since 7.0.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has read access for the item, WP_Error object otherwise.
	 */
	public function get_item_permissions_check( $request ) {
		$check = $this->get_items_permissions_check( $request );
		if ( is_wp_error( $check ) ) {
			return $check;
		}

		return true;
	}

	/**
	 * Retrieves all icons, optionally scoped to a collection.
	 *
	 * @since 7.0.0
	 * @since 7.1.0 Supports filtering by collection.
	 * @since 7.2.0 Icons registered as non-public are omitted.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_items( $request ) {
		$collection = $request->get_param( 'collection' );

		if ( null !== $collection && ! WP_Icon_Collections_Registry::get_instance()->is_registered( $collection ) ) {
			return new WP_Error(
				'rest_icon_collection_not_found',
				sprintf(
					/* translators: %s: Icon collection slug. */
					__( 'Icon collection not found: "%s".' ),
					$collection
				),
				array( 'status' => 404 )
			);
		}

		$response = array();
		$search   = $request->get_param( 'search' );
		$icons    = WP_Icons_Registry::get_instance()->get_registered_icons( $search );

		foreach ( $icons as $icon ) {
			if ( false === ( $icon['public'] ?? true ) ) {
				continue;
			}
			if ( null !== $collection && ( ! isset( $icon['collection'] ) || $icon['collection'] !== $collection ) ) {
				continue;
			}
			$prepared_icon = $this->prepare_item_for_response( $icon, $request );
			$response[]    = $this->prepare_response_for_collection( $prepared_icon );
		}
		return rest_ensure_response( $response );
	}

	/**
	 * Retrieves a specific icon.
	 *
	 * @since 7.0.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_item( $request ) {
		$icon = $this->get_icon( $request['name'] );
		if ( is_wp_error( $icon ) ) {
			return $icon;
		}

		$data = $this->prepare_item_for_response( $icon, $request );
		return rest_ensure_response( $data );
	}

	/**
	 * Retrieves a specific icon from the registry.
	 *
	 * @since 7.0.0
	 * @since 7.2.0 Icons registered as non-public are reported as not found.
	 *
	 * @param string $name Icon name.
	 * @return array|WP_Error Icon data on success, or WP_Error object on failure.
	 */
	public function get_icon( $name ) {
		$registry = WP_Icons_Registry::get_instance();
		$icon     = $registry->get_registered_icon( $name );

		if ( null === $icon || false === ( $icon['public'] ?? true ) ) {
			return new WP_Error(
				'rest_icon_not_found',
				sprintf(
					// translators: %s is the name of any user-provided name
					__( 'Icon not found: "%s".' ),
					$name
				),
				array( 'status' => 404 )
			);
		}

		return $icon;
	}

	/**
	 * Prepare a raw icon before it gets output in a REST API response.
	 *
	 * @since 7.0.0
	 * @since 7.1.0 Added the `collection` field.
	 *
	 * @param array           $item    Raw icon as registered, before any changes.
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function prepare_item_for_response( $item, $request ) {
		$fields = $this->get_fields_for_response( $request );
		$keys   = array(
			'name'       => 'name',
			'label'      => 'label',
			'content'    => 'content',
			'collection' => 'collection',
		);
		$data   = array();
		foreach ( $keys as $item_key => $rest_key ) {
			if ( isset( $item[ $item_key ] ) && rest_is_field_included( $rest_key, $fields ) ) {
				$data[ $rest_key ] = $item[ $item_key ];
			}
		}

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->add_additional_fields_to_object( $data, $request );
		$data    = $this->filter_response_by_context( $data, $context );
		return rest_ensure_response( $data );
	}

	/**
	 * Retrieves the icon schema, conforming to JSON Schema.
	 *
	 * @since 7.0.0
	 * @since 7.1.0 Added the `collection` property.
	 *
	 * @return array Item schema data.
	 */
	public function get_item_schema() {
		if ( $this->schema ) {
			return $this->add_additional_fields_schema( $this->schema );
		}

		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'icon',
			'type'       => 'object',
			'properties' => array(
				'name'       => array(
					'description' => __( 'The icon name.' ),
					'type'        => 'string',
					'readonly'    => true,
					'context'     => array( 'view', 'edit', 'embed' ),
				),
				'label'      => array(
					'description' => __( 'The icon label.' ),
					'type'        => 'string',
					'readonly'    => true,
					'context'     => array( 'view', 'edit', 'embed' ),
				),
				'content'    => array(
					'description' => __( 'The icon content (SVG markup).' ),
					'type'        => 'string',
					'readonly'    => true,
					'context'     => array( 'view', 'edit', 'embed' ),
				),
				'collection' => array(
					'description' => __( 'The slug of the collection this icon belongs to.' ),
					'type'        => 'string',
					'readonly'    => true,
					'context'     => array( 'view', 'edit', 'embed' ),
				),
			),
		);

		$this->schema = $schema;

		return $this->add_additional_fields_schema( $this->schema );
	}

	/**
	 * Retrieves the query params for the icons collection.
	 *
	 * @since 7.0.0
	 * @since 7.1.0 Added the `collection` parameter.
	 *
	 * @return array Collection parameters.
	 */
	public function get_collection_params() {
		$query_params                       = parent::get_collection_params();
		$query_params['context']['default'] = 'view';
		$query_params['collection']         = array(
			'description' => __( 'Limit results to icons belonging to the given collection slug.' ),
			'type'        => 'string',
			'pattern'     => '^[a-z0-9]([a-z0-9_-]*[a-z0-9])?$',
		);
		return $query_params;
	}
}

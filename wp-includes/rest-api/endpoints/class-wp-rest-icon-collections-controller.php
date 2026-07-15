<?php
/**
 * REST API: WP_REST_Icon_Collections_Controller class
 *
 * @package    WordPress
 * @subpackage REST_API
 * @since      7.1.0
 */

/**
 * Controller which provides a REST endpoint for the editor to read registered
 * icon collections.
 *
 * @since 7.1.0
 *
 * @see WP_REST_Controller
 */
class WP_REST_Icon_Collections_Controller extends WP_REST_Controller {

	/**
	 * Constructs the controller.
	 *
	 * @since 7.1.0
	 */
	public function __construct() {
		$this->namespace = 'wp/v2';
		$this->rest_base = 'icon-collections';
	}

	/**
	 * Registers the routes for the objects of the controller.
	 *
	 * @since 7.1.0
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
			'/' . $this->rest_base . '/(?P<slug>[a-z0-9](?:[a-z0-9_-]*[a-z0-9])?)',
			array(
				'args'   => array(
					'slug' => array(
						'description' => __( 'Icon collection slug.' ),
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
	 * Checks whether a given request has permission to read icon collections.
	 *
	 * @since 7.1.0
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
			__( 'Sorry, you are not allowed to view the registered icon collections.' ),
			array( 'status' => rest_authorization_required_code() )
		);
	}

	/**
	 * Checks if a given request has access to read a specific icon collection.
	 *
	 * @since 7.1.0
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
	 * Retrieves all icon collections.
	 *
	 * @since 7.1.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_items( $request ) {
		$response    = array();
		$collections = WP_Icon_Collections_Registry::get_instance()->get_all_registered();
		foreach ( $collections as $collection ) {
			$prepared_collection = $this->prepare_item_for_response( $collection, $request );
			$response[]          = $this->prepare_response_for_collection( $prepared_collection );
		}
		return rest_ensure_response( $response );
	}

	/**
	 * Retrieves a specific icon collection.
	 *
	 * @since 7.1.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_item( $request ) {
		$collection = $this->get_icon_collection( $request['slug'] );
		if ( is_wp_error( $collection ) ) {
			return $collection;
		}

		$data = $this->prepare_item_for_response( $collection, $request );
		return rest_ensure_response( $data );
	}

	/**
	 * Retrieves a specific icon collection from the registry.
	 *
	 * @since 7.1.0
	 *
	 * @param string $slug Icon collection slug.
	 * @return array|WP_Error Icon collection data on success, or WP_Error object on failure.
	 */
	public function get_icon_collection( $slug ) {
		$registry   = WP_Icon_Collections_Registry::get_instance();
		$collection = $registry->get_registered( $slug );

		if ( null === $collection ) {
			return new WP_Error(
				'rest_icon_collection_not_found',
				sprintf(
					/* translators: %s: Icon collection slug. */
					__( 'Icon collection not found: "%s".' ),
					$slug
				),
				array( 'status' => 404 )
			);
		}

		return $collection;
	}

	/**
	 * Prepares a raw icon collection before it gets output in a REST API response.
	 *
	 * @since 7.1.0
	 *
	 * @param array           $item    Raw icon collection as registered, before any changes.
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function prepare_item_for_response( $item, $request ) {
		$fields = $this->get_fields_for_response( $request );
		$keys   = array(
			'slug'        => 'slug',
			'label'       => 'label',
			'description' => 'description',
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
	 * Retrieves the icon collection schema, conforming to JSON Schema.
	 *
	 * @since 7.1.0
	 *
	 * @return array Item schema data.
	 */
	public function get_item_schema() {
		if ( $this->schema ) {
			return $this->add_additional_fields_schema( $this->schema );
		}

		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'icon-collection',
			'type'       => 'object',
			'properties' => array(
				'slug'        => array(
					'description' => __( 'The icon collection slug.' ),
					'type'        => 'string',
					'readonly'    => true,
					'context'     => array( 'view', 'edit', 'embed' ),
				),
				'label'       => array(
					'description' => __( 'The icon collection label.' ),
					'type'        => 'string',
					'readonly'    => true,
					'context'     => array( 'view', 'edit', 'embed' ),
				),
				'description' => array(
					'description' => __( 'The icon collection description.' ),
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
	 * Retrieves the query params for the icon collections collection.
	 *
	 * @since 7.1.0
	 *
	 * @return array Collection parameters.
	 */
	public function get_collection_params() {
		$query_params                       = parent::get_collection_params();
		$query_params['context']['default'] = 'view';
		return $query_params;
	}
}

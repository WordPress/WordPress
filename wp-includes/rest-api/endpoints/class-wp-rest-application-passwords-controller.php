<?php
/**
 * REST API: WP_REST_Application_Passwords_Controller class
 *
 * @package    WordPress
 * @subpackage REST_API
 * @since      5.6.0
 */

/**
 * Core class to access a user's application passwords via the REST API.
 *
 * @since 5.6.0
 *
 * @see   WP_REST_Controller
 */
class WP_REST_Application_Passwords_Controller extends WP_REST_Controller {

	/**
	 * Application Passwords controller constructor.
	 *
	 * @since 5.6.0
	 */
	public function __construct() {
		$this->namespace = 'wp/v2';
		$this->rest_base = 'users/(?P<user_id>(?:[\d]+|me))/application-passwords';
	}

	/**
	 * Registers the REST API routes for the application passwords controller.
	 *
	 * @since 5.6.0
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
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'create_item' ),
					'permission_callback' => array( $this, 'create_item_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema(),
				),
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete_items' ),
					'permission_callback' => array( $this, 'delete_items_permissions_check' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/introspect',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_current_item' ),
					'permission_callback' => array( $this, 'get_current_item_permissions_check' ),
					'args'                => array(
						'context' => $this->get_context_param( array( 'default' => 'view' ) ),
					),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<uuid>[\w\-]+)',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_item' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
					'args'                => array(
						'context' => $this->get_context_param( array( 'default' => 'view' ) ),
					),
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_item' ),
					'permission_callback' => array( $this, 'update_item_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
				),
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete_item' ),
					'permission_callback' => array( $this, 'delete_item_permissions_check' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Checks if a given request has access to get application passwords.
	 *
	 * @since 5.6.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function get_items_permissions_check( $request ) {
		$user = $this->get_user( $request );

		if ( is_wp_error( $user ) ) {
			return $user;
		}

		if ( ! current_user_can( 'list_app_passwords', $user->ID ) ) {
			return new WP_Error(
				'rest_cannot_list_application_passwords',
				__( 'Sorry, you are not allowed to list application passwords for this user.' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Retrieves a collection of application passwords.
	 *
	 * @since 5.6.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_items( $request ) {
		$user = $this->get_user( $request );

		if ( is_wp_error( $user ) ) {
			return $user;
		}

		$passwords = WP_Application_Passwords::get_user_application_passwords( $user->ID );
		$response  = array();

		foreach ( $passwords as $password ) {
			$response[] = $this->prepare_response_for_collection(
				$this->prepare_item_for_response( $password, $request )
			);
		}

		return new WP_REST_Response( $response );
	}

	/**
	 * Checks if a given request has access to get a specific application password.
	 *
	 * @since 5.6.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has read access for the item, WP_Error object otherwise.
	 */
	public function get_item_permissions_check( $request ) {
		$user = $this->get_user( $request );

		if ( is_wp_error( $user ) ) {
			return $user;
		}

		if ( ! current_user_can( 'read_app_password', $user->ID, $request['uuid'] ) ) {
			return new WP_Error(
				'rest_cannot_read_application_password',
				__( 'Sorry, you are not allowed to read this application password.' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Retrieves one application password from the collection.
	 *
	 * @since 5.6.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_item( $request ) {
		$password = $this->get_application_password( $request );

		if ( is_wp_error( $password ) ) {
			return $password;
		}

		return $this->prepare_item_for_response( $password, $request );
	}

	/**
	 * Checks if a given request has access to create application passwords.
	 *
	 * @since 5.6.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has access to create items, WP_Error object otherwise.
	 */
	public function create_item_permissions_check( $request ) {
		$user = $this->get_user( $request );

		if ( is_wp_error( $user ) ) {
			return $user;
		}

		if ( ! current_user_can( 'create_app_password', $user->ID ) ) {
			return new WP_Error(
				'rest_cannot_create_application_passwords',
				__( 'Sorry, you are not allowed to create application passwords for this user.' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Creates an application password.
	 *
	 * @since 5.6.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function create_item( $request ) {
		$user = $this->get_user( $request );

		if ( is_wp_error( $user ) ) {
			return $user;
		}

		$prepared = $this->prepare_item_for_database( $request );

		if ( is_wp_error( $prepared ) ) {
			return $prepared;
		}

		$created = WP_Application_Passwords::create_new_application_password( $user->ID, wp_slash( (array) $prepared ) );

		if ( is_wp_error( $created ) ) {
			return $created;
		}

		$password = $created[0];
		$item     = WP_Application_Passwords::get_user_application_password( $user->ID, $created[1]['uuid'] );

		$item['new_password'] = WP_Application_Passwords::chunk_password( $password );
		$fields_update        = $this->update_additional_fields_for_object( $item, $request );

		if ( is_wp_error( $fields_update ) ) {
			return $fields_update;
		}

		/**
		 * Fires after a single application password is completely created or updated via the REST API.
		 *
		 * @since 5.6.0
		 *
		 * @param array           $item     Inserted or updated password item.
		 * @param WP_REST_Request $request  Request object.
		 * @param bool            $creating True when creating an application password, false when updating.
		 */
		do_action( 'rest_after_insert_application_password', $item, $request, true );

		$request->set_param( 'context', 'edit' );
		$response = $this->prepare_item_for_response( $item, $request );

		$response->set_status( 201 );
		$response->header( 'Location', $response->get_links()['self'][0]['href'] );

		return $response;
	}

	/**
	 * Checks if a given request has access to update application passwords.
	 *
	 * @since 5.6.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has access to create items, WP_Error object otherwise.
	 */
	public function update_item_permissions_check( $request ) {
		$user = $this->get_user( $request );

		if ( is_wp_error( $user ) ) {
			return $user;
		}

		if ( ! current_user_can( 'edit_app_password', $user->ID, $request['uuid'] ) ) {
			return new WP_Error(
				'rest_cannot_edit_application_password',
				__( 'Sorry, you are not allowed to edit this application password.' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Updates an application password.
	 *
	 * @since 5.6.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function update_item( $request ) {
		$user = $this->get_user( $request );

		if ( is_wp_error( $user ) ) {
			return $user;
		}

		$item = $this->get_application_password( $request );

		if ( is_wp_error( $item ) ) {
			return $item;
		}

		$prepared = $this->prepare_item_for_database( $request );

		if ( is_wp_error( $prepared ) ) {
			return $prepared;
		}

		$saved = WP_Application_Passwords::update_application_password( $user->ID, $item['uuid'], wp_slash( (array) $prepared ) );

		if ( is_wp_error( $saved ) ) {
			return $saved;
		}

		$fields_update = $this->update_additional_fields_for_object( $item, $request );

		if ( is_wp_error( $fields_update ) ) {
			return $fields_update;
		}

		$item = WP_Application_Passwords::get_user_application_password( $user->ID, $item['uuid'] );

		/** This action is documented in wp-includes/rest-api/endpoints/class-wp-rest-application-passwords-controller.php */
		do_action( 'rest_after_insert_application_password', $item, $request, false );

		$request->set_param( 'context', 'edit' );
		return $this->prepare_item_for_response( $item, $request );
	}

	/**
	 * Checks if a given request has access to delete all application passwords.
	 *
	 * @since 5.6.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has access to delete the item, WP_Error object otherwise.
	 */
	public function delete_items_permissions_check( $request ) {
		$user = $this->get_user( $request );

		if ( is_wp_error( $user ) ) {
			return $user;
		}

		if ( ! current_user_can( 'delete_app_passwords', $user->ID ) ) {
			return new WP_Error(
				'rest_cannot_delete_application_passwords',
				__( 'Sorry, you are not allowed to delete application passwords for this user.' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Deletes all application passwords.
	 *
	 * @since 5.6.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function delete_items( $request ) {
		$user = $this->get_user( $request );

		if ( is_wp_error( $user ) ) {
			return $user;
		}

		$deleted = WP_Application_Passwords::delete_all_application_passwords( $user->ID );

		if ( is_wp_error( $deleted ) ) {
			return $deleted;
		}

		return new WP_REST_Response(
			array(
				'deleted' => true,
				'count'   => $deleted,
			)
		);
	}

	/**
	 * Checks if a given request has access to delete a specific application password.
	 *
	 * @since 5.6.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has access to delete the item, WP_Error object otherwise.
	 */
	public function delete_item_permissions_check( $request ) {
		$user = $this->get_user( $request );

		if ( is_wp_error( $user ) ) {
			return $user;
		}

		if ( ! current_user_can( 'delete_app_password', $user->ID, $request['uuid'] ) ) {
			return new WP_Error(
				'rest_cannot_delete_application_password',
				__( 'Sorry, you are not allowed to delete this application password.' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Deletes one application password.
	 *
	 * @since 5.6.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function delete_item( $request ) {
		$user = $this->get_user( $request );

		if ( is_wp_error( $user ) ) {
			return $user;
		}

		$password = $this->get_application_password( $request );

		if ( is_wp_error( $password ) ) {
			return $password;
		}

		$request->set_param( 'context', 'edit' );
		$previous = $this->prepare_item_for_response( $password, $request );
		$deleted  = WP_Application_Passwords::delete_application_password( $user->ID, $password['uuid'] );

		if ( is_wp_error( $deleted ) ) {
			return $deleted;
		}

		return new WP_REST_Response(
			array(
				'deleted'  => true,
				'previous' => $previous->get_data(),
			)
		);
	}

	/**
	 * Checks if a given request has access to get the currently used application password.
	 *
	 * @since 5.7.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has read access for the item, WP_Error object otherwise.
	 */
	public function get_current_item_permissions_check( $request ) {
		$user = $this->get_user( $request );

		if ( is_wp_error( $user ) ) {
			return $user;
		}

		if ( get_current_user_id() !== $user->ID ) {
			return new WP_Error(
				'rest_cannot_introspect_app_password_for_non_authenticated_user',
				__( 'The authenticated Application Password can only be introspected for the current user.' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Retrieves the application password being currently used for authentication.
	 *
	 * @since 5.7.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_current_item( $request ) {
		$user = $this->get_user( $request );

		if ( is_wp_error( $user ) ) {
			return $user;
		}

		$uuid = rest_get_authenticated_app_password();

		if ( ! $uuid ) {
			return new WP_Error(
				'rest_no_authenticated_app_password',
				__( 'Cannot introspect Application Password.' ),
				array( 'status' => 404 )
			);
		}

		$password = WP_Application_Passwords::get_user_application_password( $user->ID, $uuid );

		if ( ! $password ) {
			return new WP_Error(
				'rest_application_password_not_found',
				__( 'Application password not found.' ),
				array( 'status' => 500 )
			);
		}

		return $this->prepare_item_for_response( $password, $request );
	}

	/**
	 * Performs a permissions check for the request.
	 *
	 * @since 5.6.0
	 * @deprecated 5.7.0 Use `edit_user` directly or one of the specific meta capabilities introduced in 5.7.0.
	 *
	 * @param WP_REST_Request $request
	 * @return true|WP_Error
	 */
	protected function do_permissions_check( $request ) {
		_deprecated_function( __METHOD__, '5.7.0' );

		$user = $this->get_user( $request );

		if ( is_wp_error( $user ) ) {
			return $user;
		}

		if ( ! current_user_can( 'edit_user', $user->ID ) ) {
			return new WP_Error(
				'rest_cannot_manage_application_passwords',
				__( 'Sorry, you are not allowed to manage application passwords for this user.' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Prepares an application password for a create or update operation.
	 *
	 * @since 5.6.0
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return object|WP_Error The prepared item, or WP_Error object on failure.
	 */
	protected function prepare_item_for_database( $request ) {
		$prepared = (object) array(
			'name' => $request['name'],
		);

		if ( $request['app_id'] && ! $request['uuid'] ) {
			$prepared->app_id = $request['app_id'];
		}

		/**
		 * Filters an application password before it is inserted via the REST API.
		 *
		 * @since 5.6.0
		 *
		 * @param stdClass        $prepared An object representing a single application password prepared for inserting or updating the database.
		 * @param WP_REST_Request $request  Request object.
		 */
		return apply_filters( 'rest_pre_insert_application_password', $prepared, $request );
	}

	/**
	 * Prepares the application password for the REST response.
	 *
	 * @since 5.6.0
	 *
	 * @param array           $item    WordPress representation of the item.
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function prepare_item_for_response( $item, $request ) {
		$user = $this->get_user( $request );

		if ( is_wp_error( $user ) ) {
			return $user;
		}

		$prepared = array(
			'uuid'      => $item['uuid'],
			'app_id'    => empty( $item['app_id'] ) ? '' : $item['app_id'],
			'name'      => $item['name'],
			'created'   => gmdate( 'Y-m-d\TH:i:s', $item['created'] ),
			'last_used' => $item['last_used'] ? gmdate( 'Y-m-d\TH:i:s', $item['last_used'] ) : null,
			'last_ip'   => $item['last_ip'] ? $item['last_ip'] : null,
		);

		if ( isset( $item['new_password'] ) ) {
			$prepared['password'] = $item['new_password'];
		}

		$prepared = $this->add_additional_fields_to_object( $prepared, $request );
		$prepared = $this->filter_response_by_context( $prepared, $request['context'] );

		$response = new WP_REST_Response( $prepared );
		$response->add_links( $this->prepare_links( $user, $item ) );

		/**
		 * Filters the REST API response for an application password.
		 *
		 * @since 5.6.0
		 *
		 * @param WP_REST_Response $response The response object.
		 * @param array            $item     The application password array.
		 * @param WP_REST_Request  $request  The request object.
		 */
		return apply_filters( 'rest_prepare_application_password', $response, $item, $request );
	}

	/**
	 * Prepares links for the request.
	 *
	 * @since 5.6.0
	 *
	 * @param WP_User $user The requested user.
	 * @param array   $item The application password.
	 * @return array The list of links.
	 */
	protected function prepare_links( WP_User $user, $item ) {
		return array(
			'self' => array(
				'href' => rest_url( sprintf( '%s/users/%d/application-passwords/%s', $this->namespace, $user->ID, $item['uuid'] ) ),
			),
		);
	}

	/**
	 * Gets the requested user.
	 *
	 * @since 5.6.0
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_User|WP_Error The WordPress user associated with the request, or a WP_Error if none found.
	 */
	protected function get_user( $request ) {
		if ( ! wp_is_application_passwords_available() ) {
			return new WP_Error(
				'application_passwords_disabled',
				__( 'Application passwords are not available.' ),
				array( 'status' => 501 )
			);
		}

		$error = new WP_Error(
			'rest_user_invalid_id',
			__( 'Invalid user ID.' ),
			array( 'status' => 404 )
		);

		$id = $request['user_id'];

		if ( 'me' === $id ) {
			if ( ! is_user_logged_in() ) {
				return new WP_Error(
					'rest_not_logged_in',
					__( 'You are not currently logged in.' ),
					array( 'status' => 401 )
				);
			}

			$user = wp_get_current_user();
		} else {
			$id = (int) $id;

			if ( $id <= 0 ) {
				return $error;
			}

			$user = get_userdata( $id );
		}

		if ( empty( $user ) || ! $user->exists() ) {
			return $error;
		}

		if ( is_multisite() && ! is_user_member_of_blog( $user->ID ) ) {
			return $error;
		}

		if ( ! wp_is_application_passwords_available_for_user( $user ) ) {
			return new WP_Error(
				'application_passwords_disabled_for_user',
				__( 'Application passwords are not available for your account. Please contact the site administrator for assistance.' ),
				array( 'status' => 501 )
			);
		}

		return $user;
	}

	/**
	 * Gets the requested application password.
	 *
	 * @since 5.6.0
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return array|WP_Error The application password details if found, a WP_Error otherwise.
	 */
	protected function get_application_password( $request ) {
		$user = $this->get_user( $request );

		if ( is_wp_error( $user ) ) {
			return $user;
		}

		$password = WP_Application_Passwords::get_user_application_password( $user->ID, $request['uuid'] );

		if ( ! $password ) {
			return new WP_Error(
				'rest_application_password_not_found',
				__( 'Application password not found.' ),
				array( 'status' => 404 )
			);
		}

		return $password;
	}

	/**
	 * Retrieves the query params for the collections.
	 *
	 * @since 5.6.0
	 *
	 * @return array Query parameters for the collection.
	 */
	public function get_collection_params() {
		return array(
			'context' => $this->get_context_param( array( 'default' => 'view' ) ),
		);
	}

	/**
	 * Retrieves the application password's schema, conforming to JSON Schema.
	 *
	 * @since 5.6.0
	 *
	 * @return array Item schema data.
	 */
	public function get_item_schema() {
		if ( $this->schema ) {
			return $this->add_additional_fields_schema( $this->schema );
		}

		$this->schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'application-password',
			'type'       => 'object',
			'properties' => array(
				'uuid'      => array(
					'description' => __( 'The unique identifier for the application password.' ),
					'type'        => 'string',
					'format'      => 'uuid',
					'context'     => array( 'view', 'edit', 'embed' ),
					'readonly'    => true,
				),
				'app_id'    => array(
					'description' => __( 'A uuid provided by the application to uniquely identify it. It is recommended to use an UUID v5 with the URL or DNS namespace.' ),
					'type'        => 'string',
					'format'      => 'uuid',
					'context'     => array( 'view', 'edit', 'embed' ),
				),
				'name'      => array(
					'description' => __( 'The name of the application password.' ),
					'type'        => 'string',
					'required'    => true,
					'context'     => array( 'view', 'edit', 'embed' ),
					'minLength'   => 1,
					'pattern'     => '.*\S.*',
				),
				'password'  => array(
					'description' => __( 'The generated password. Only available after adding an application.' ),
					'type'        => 'string',
					'context'     => array( 'edit' ),
					'readonly'    => true,
				),
				'created'   => array(
					'description' => __( 'The GMT date the application password was created.' ),
					'type'        => 'string',
					'format'      => 'date-time',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'last_used' => array(
					'description' => __( 'The GMT date the application password was last used.' ),
					'type'        => array( 'string', 'null' ),
					'format'      => 'date-time',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'last_ip'   => array(
					'description' => __( 'The IP address the application password was last used by.' ),
					'type'        => array( 'string', 'null' ),
					'format'      => 'ip',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
			),
		);

		return $this->add_additional_fields_schema( $this->schema );
	}
}

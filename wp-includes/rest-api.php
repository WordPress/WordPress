<?php
/**
 * REST API functions.
 *
 * @package WordPress
 * @subpackage REST_API
 * @since 4.4.0
 */

/**
 * Version number for our API.
 *
 * @var string
 */
define( 'REST_API_VERSION', '2.0' );

/**
 * Registers a REST API route.
 *
 * Note: Do not use before the {@see 'rest_api_init'} hook.
 *
 * @since 4.4.0
 * @since 5.1.0 Added a _doing_it_wrong() notice when not called on or after the rest_api_init hook.
 *
 * @param string $namespace The first URL segment after core prefix. Should be unique to your package/plugin.
 * @param string $route     The base URL for route you are adding.
 * @param array  $args      Optional. Either an array of options for the endpoint, or an array of arrays for
 *                          multiple methods. Default empty array.
 * @param bool   $override  Optional. If the route already exists, should we override it? True overrides,
 *                          false merges (with newer overriding if duplicate keys exist). Default false.
 * @return bool True on success, false on error.
 */
function register_rest_route( $namespace, $route, $args = array(), $override = false ) {
	if ( empty( $namespace ) ) {
		/*
		 * Non-namespaced routes are not allowed, with the exception of the main
		 * and namespace indexes. If you really need to register a
		 * non-namespaced route, call `WP_REST_Server::register_route` directly.
		 */
		_doing_it_wrong( 'register_rest_route', __( 'Routes must be namespaced with plugin or theme name and version.' ), '4.4.0' );
		return false;
	} elseif ( empty( $route ) ) {
		_doing_it_wrong( 'register_rest_route', __( 'Route must be specified.' ), '4.4.0' );
		return false;
	}

	if ( ! did_action( 'rest_api_init' ) ) {
		_doing_it_wrong(
			'register_rest_route',
			sprintf(
				/* translators: %s: rest_api_init */
				__( 'REST API routes must be registered on the %s action.' ),
				'<code>rest_api_init</code>'
			),
			'5.1.0'
		);
	}

	if ( isset( $args['args'] ) ) {
		$common_args = $args['args'];
		unset( $args['args'] );
	} else {
		$common_args = array();
	}

	if ( isset( $args['callback'] ) ) {
		// Upgrade a single set to multiple.
		$args = array( $args );
	}

	$defaults = array(
		'methods'  => 'GET',
		'callback' => null,
		'args'     => array(),
	);
	foreach ( $args as $key => &$arg_group ) {
		if ( ! is_numeric( $key ) ) {
			// Route option, skip here.
			continue;
		}

		$arg_group         = array_merge( $defaults, $arg_group );
		$arg_group['args'] = array_merge( $common_args, $arg_group['args'] );
	}

	$full_route = '/' . trim( $namespace, '/' ) . '/' . trim( $route, '/' );
	rest_get_server()->register_route( $namespace, $full_route, $args, $override );
	return true;
}

/**
 * Registers a new field on an existing WordPress object type.
 *
 * @since 4.7.0
 *
 * @global array $wp_rest_additional_fields Holds registered fields, organized
 *                                          by object type.
 *
 * @param string|array $object_type Object(s) the field is being registered
 *                                  to, "post"|"term"|"comment" etc.
 * @param string $attribute         The attribute name.
 * @param array  $args {
 *     Optional. An array of arguments used to handle the registered field.
 *
 *     @type string|array|null $get_callback    Optional. The callback function used to retrieve the field
 *                                              value. Default is 'null', the field will not be returned in
 *                                              the response.
 *     @type string|array|null $update_callback Optional. The callback function used to set and update the
 *                                              field value. Default is 'null', the value cannot be set or
 *                                              updated.
 *     @type string|array|null $schema          Optional. The callback function used to create the schema for
 *                                              this field. Default is 'null', no schema entry will be returned.
 * }
 */
function register_rest_field( $object_type, $attribute, $args = array() ) {
	$defaults = array(
		'get_callback'    => null,
		'update_callback' => null,
		'schema'          => null,
	);

	$args = wp_parse_args( $args, $defaults );

	global $wp_rest_additional_fields;

	$object_types = (array) $object_type;

	foreach ( $object_types as $object_type ) {
		$wp_rest_additional_fields[ $object_type ][ $attribute ] = $args;
	}
}

/**
 * Registers rewrite rules for the API.
 *
 * @since 4.4.0
 *
 * @see rest_api_register_rewrites()
 * @global WP $wp Current WordPress environment instance.
 */
function rest_api_init() {
	rest_api_register_rewrites();

	global $wp;
	$wp->add_query_var( 'rest_route' );
}

/**
 * Adds REST rewrite rules.
 *
 * @since 4.4.0
 *
 * @see add_rewrite_rule()
 * @global WP_Rewrite $wp_rewrite
 */
function rest_api_register_rewrites() {
	global $wp_rewrite;

	add_rewrite_rule( '^' . rest_get_url_prefix() . '/?$', 'index.php?rest_route=/', 'top' );
	add_rewrite_rule( '^' . rest_get_url_prefix() . '/(.*)?', 'index.php?rest_route=/$matches[1]', 'top' );
	add_rewrite_rule( '^' . $wp_rewrite->index . '/' . rest_get_url_prefix() . '/?$', 'index.php?rest_route=/', 'top' );
	add_rewrite_rule( '^' . $wp_rewrite->index . '/' . rest_get_url_prefix() . '/(.*)?', 'index.php?rest_route=/$matches[1]', 'top' );
}

/**
 * Registers the default REST API filters.
 *
 * Attached to the {@see 'rest_api_init'} action
 * to make testing and disabling these filters easier.
 *
 * @since 4.4.0
 */
function rest_api_default_filters() {
	// Deprecated reporting.
	add_action( 'deprecated_function_run', 'rest_handle_deprecated_function', 10, 3 );
	add_filter( 'deprecated_function_trigger_error', '__return_false' );
	add_action( 'deprecated_argument_run', 'rest_handle_deprecated_argument', 10, 3 );
	add_filter( 'deprecated_argument_trigger_error', '__return_false' );

	// Default serving.
	add_filter( 'rest_pre_serve_request', 'rest_send_cors_headers' );
	add_filter( 'rest_post_dispatch', 'rest_send_allow_header', 10, 3 );
	add_filter( 'rest_post_dispatch', 'rest_filter_response_fields', 10, 3 );

	add_filter( 'rest_pre_dispatch', 'rest_handle_options_request', 10, 3 );
}

/**
 * Registers default REST API routes.
 *
 * @since 4.7.0
 */
function create_initial_rest_routes() {
	foreach ( get_post_types( array( 'show_in_rest' => true ), 'objects' ) as $post_type ) {
		$class = ! empty( $post_type->rest_controller_class ) ? $post_type->rest_controller_class : 'WP_REST_Posts_Controller';

		if ( ! class_exists( $class ) ) {
			continue;
		}
		$controller = new $class( $post_type->name );
		if ( ! is_subclass_of( $controller, 'WP_REST_Controller' ) ) {
			continue;
		}

		$controller->register_routes();

		if ( post_type_supports( $post_type->name, 'revisions' ) ) {
			$revisions_controller = new WP_REST_Revisions_Controller( $post_type->name );
			$revisions_controller->register_routes();
		}

		if ( 'attachment' !== $post_type->name ) {
			$autosaves_controller = new WP_REST_Autosaves_Controller( $post_type->name );
			$autosaves_controller->register_routes();
		}
	}

	// Post types.
	$controller = new WP_REST_Post_Types_Controller;
	$controller->register_routes();

	// Post statuses.
	$controller = new WP_REST_Post_Statuses_Controller;
	$controller->register_routes();

	// Taxonomies.
	$controller = new WP_REST_Taxonomies_Controller;
	$controller->register_routes();

	// Terms.
	foreach ( get_taxonomies( array( 'show_in_rest' => true ), 'object' ) as $taxonomy ) {
		$class = ! empty( $taxonomy->rest_controller_class ) ? $taxonomy->rest_controller_class : 'WP_REST_Terms_Controller';

		if ( ! class_exists( $class ) ) {
			continue;
		}
		$controller = new $class( $taxonomy->name );
		if ( ! is_subclass_of( $controller, 'WP_REST_Controller' ) ) {
			continue;
		}

		$controller->register_routes();
	}

	// Users.
	$controller = new WP_REST_Users_Controller;
	$controller->register_routes();

	// Comments.
	$controller = new WP_REST_Comments_Controller;
	$controller->register_routes();

	/**
	 * Filters the search handlers to use in the REST search controller.
	 *
	 * @since 5.0.0
	 *
	 * @param array $search_handlers List of search handlers to use in the controller. Each search
	 *                               handler instance must extend the `WP_REST_Search_Handler` class.
	 *                               Default is only a handler for posts.
	 */
	$search_handlers = apply_filters( 'wp_rest_search_handlers', array( new WP_REST_Post_Search_Handler() ) );

	$controller = new WP_REST_Search_Controller( $search_handlers );
	$controller->register_routes();

	// Block Renderer.
	$controller = new WP_REST_Block_Renderer_Controller;
	$controller->register_routes();

	// Settings.
	$controller = new WP_REST_Settings_Controller;
	$controller->register_routes();

	// Themes.
	$controller = new WP_REST_Themes_Controller;
	$controller->register_routes();

}

/**
 * Loads the REST API.
 *
 * @since 4.4.0
 *
 * @global WP             $wp             Current WordPress environment instance.
 */
function rest_api_loaded() {
	if ( empty( $GLOBALS['wp']->query_vars['rest_route'] ) ) {
		return;
	}

	/**
	 * Whether this is a REST Request.
	 *
	 * @since 4.4.0
	 * @var bool
	 */
	define( 'REST_REQUEST', true );

	// Initialize the server.
	$server = rest_get_server();

	// Fire off the request.
	$route = untrailingslashit( $GLOBALS['wp']->query_vars['rest_route'] );
	if ( empty( $route ) ) {
		$route = '/';
	}
	$server->serve_request( $route );

	// We're done.
	die();
}

/**
 * Retrieves the URL prefix for any API resource.
 *
 * @since 4.4.0
 *
 * @return string Prefix.
 */
function rest_get_url_prefix() {
	/**
	 * Filters the REST URL prefix.
	 *
	 * @since 4.4.0
	 *
	 * @param string $prefix URL prefix. Default 'wp-json'.
	 */
	return apply_filters( 'rest_url_prefix', 'wp-json' );
}

/**
 * Retrieves the URL to a REST endpoint on a site.
 *
 * Note: The returned URL is NOT escaped.
 *
 * @since 4.4.0
 *
 * @todo Check if this is even necessary
 * @global WP_Rewrite $wp_rewrite
 *
 * @param int    $blog_id Optional. Blog ID. Default of null returns URL for current blog.
 * @param string $path    Optional. REST route. Default '/'.
 * @param string $scheme  Optional. Sanitization scheme. Default 'rest'.
 * @return string Full URL to the endpoint.
 */
function get_rest_url( $blog_id = null, $path = '/', $scheme = 'rest' ) {
	if ( empty( $path ) ) {
		$path = '/';
	}

	$path = '/' . ltrim( $path, '/' );

	if ( is_multisite() && get_blog_option( $blog_id, 'permalink_structure' ) || get_option( 'permalink_structure' ) ) {
		global $wp_rewrite;

		if ( $wp_rewrite->using_index_permalinks() ) {
			$url = get_home_url( $blog_id, $wp_rewrite->index . '/' . rest_get_url_prefix(), $scheme );
		} else {
			$url = get_home_url( $blog_id, rest_get_url_prefix(), $scheme );
		}

		$url .= $path;
	} else {
		$url = trailingslashit( get_home_url( $blog_id, '', $scheme ) );
		// nginx only allows HTTP/1.0 methods when redirecting from / to /index.php
		// To work around this, we manually add index.php to the URL, avoiding the redirect.
		if ( 'index.php' !== substr( $url, 9 ) ) {
			$url .= 'index.php';
		}

		$url = add_query_arg( 'rest_route', $path, $url );
	}

	if ( is_ssl() ) {
		// If the current host is the same as the REST URL host, force the REST URL scheme to HTTPS.
		if ( $_SERVER['SERVER_NAME'] === parse_url( get_home_url( $blog_id ), PHP_URL_HOST ) ) {
			$url = set_url_scheme( $url, 'https' );
		}
	}

	if ( is_admin() && force_ssl_admin() ) {
		// In this situation the home URL may be http:, and `is_ssl()` may be
		// false, but the admin is served over https: (one way or another), so
		// REST API usage will be blocked by browsers unless it is also served
		// over HTTPS.
		$url = set_url_scheme( $url, 'https' );
	}

	/**
	 * Filters the REST URL.
	 *
	 * Use this filter to adjust the url returned by the get_rest_url() function.
	 *
	 * @since 4.4.0
	 *
	 * @param string $url     REST URL.
	 * @param string $path    REST route.
	 * @param int    $blog_id Blog ID.
	 * @param string $scheme  Sanitization scheme.
	 */
	return apply_filters( 'rest_url', $url, $path, $blog_id, $scheme );
}

/**
 * Retrieves the URL to a REST endpoint.
 *
 * Note: The returned URL is NOT escaped.
 *
 * @since 4.4.0
 *
 * @param string $path   Optional. REST route. Default empty.
 * @param string $scheme Optional. Sanitization scheme. Default 'json'.
 * @return string Full URL to the endpoint.
 */
function rest_url( $path = '', $scheme = 'json' ) {
	return get_rest_url( null, $path, $scheme );
}

/**
 * Do a REST request.
 *
 * Used primarily to route internal requests through WP_REST_Server.
 *
 * @since 4.4.0
 *
 * @param WP_REST_Request|string $request Request.
 * @return WP_REST_Response REST response.
 */
function rest_do_request( $request ) {
	$request = rest_ensure_request( $request );
	return rest_get_server()->dispatch( $request );
}

/**
 * Retrieves the current REST server instance.
 *
 * Instantiates a new instance if none exists already.
 *
 * @since 4.5.0
 *
 * @global WP_REST_Server $wp_rest_server REST server instance.
 *
 * @return WP_REST_Server REST server instance.
 */
function rest_get_server() {
	/* @var WP_REST_Server $wp_rest_server */
	global $wp_rest_server;

	if ( empty( $wp_rest_server ) ) {
		/**
		 * Filters the REST Server Class.
		 *
		 * This filter allows you to adjust the server class used by the API, using a
		 * different class to handle requests.
		 *
		 * @since 4.4.0
		 *
		 * @param string $class_name The name of the server class. Default 'WP_REST_Server'.
		 */
		$wp_rest_server_class = apply_filters( 'wp_rest_server_class', 'WP_REST_Server' );
		$wp_rest_server       = new $wp_rest_server_class;

		/**
		 * Fires when preparing to serve an API request.
		 *
		 * Endpoint objects should be created and register their hooks on this action rather
		 * than another action to ensure they're only loaded when needed.
		 *
		 * @since 4.4.0
		 *
		 * @param WP_REST_Server $wp_rest_server Server object.
		 */
		do_action( 'rest_api_init', $wp_rest_server );
	}

	return $wp_rest_server;
}

/**
 * Ensures request arguments are a request object (for consistency).
 *
 * @since 4.4.0
 *
 * @param array|WP_REST_Request $request Request to check.
 * @return WP_REST_Request REST request instance.
 */
function rest_ensure_request( $request ) {
	if ( $request instanceof WP_REST_Request ) {
		return $request;
	}

	return new WP_REST_Request( 'GET', '', $request );
}

/**
 * Ensures a REST response is a response object (for consistency).
 *
 * This implements WP_HTTP_Response, allowing usage of `set_status`/`header`/etc
 * without needing to double-check the object. Will also allow WP_Error to indicate error
 * responses, so users should immediately check for this value.
 *
 * @since 4.4.0
 *
 * @param WP_Error|WP_HTTP_Response|mixed $response Response to check.
 * @return WP_REST_Response|mixed If response generated an error, WP_Error, if response
 *                                is already an instance, WP_HTTP_Response, otherwise
 *                                returns a new WP_REST_Response instance.
 */
function rest_ensure_response( $response ) {
	if ( is_wp_error( $response ) ) {
		return $response;
	}

	if ( $response instanceof WP_HTTP_Response ) {
		return $response;
	}

	return new WP_REST_Response( $response );
}

/**
 * Handles _deprecated_function() errors.
 *
 * @since 4.4.0
 *
 * @param string $function    The function that was called.
 * @param string $replacement The function that should have been called.
 * @param string $version     Version.
 */
function rest_handle_deprecated_function( $function, $replacement, $version ) {
	if ( ! WP_DEBUG || headers_sent() ) {
		return;
	}
	if ( ! empty( $replacement ) ) {
		/* translators: 1: function name, 2: WordPress version number, 3: new function name */
		$string = sprintf( __( '%1$s (since %2$s; use %3$s instead)' ), $function, $version, $replacement );
	} else {
		/* translators: 1: function name, 2: WordPress version number */
		$string = sprintf( __( '%1$s (since %2$s; no alternative available)' ), $function, $version );
	}

	header( sprintf( 'X-WP-DeprecatedFunction: %s', $string ) );
}

/**
 * Handles _deprecated_argument() errors.
 *
 * @since 4.4.0
 *
 * @param string $function    The function that was called.
 * @param string $message     A message regarding the change.
 * @param string $version     Version.
 */
function rest_handle_deprecated_argument( $function, $message, $version ) {
	if ( ! WP_DEBUG || headers_sent() ) {
		return;
	}
	if ( ! empty( $message ) ) {
		/* translators: 1: function name, 2: WordPress version number, 3: error message */
		$string = sprintf( __( '%1$s (since %2$s; %3$s)' ), $function, $version, $message );
	} else {
		/* translators: 1: function name, 2: WordPress version number */
		$string = sprintf( __( '%1$s (since %2$s; no alternative available)' ), $function, $version );
	}

	header( sprintf( 'X-WP-DeprecatedParam: %s', $string ) );
}

/**
 * Sends Cross-Origin Resource Sharing headers with API requests.
 *
 * @since 4.4.0
 *
 * @param mixed $value Response data.
 * @return mixed Response data.
 */
function rest_send_cors_headers( $value ) {
	$origin = get_http_origin();

	if ( $origin ) {
		// Requests from file:// and data: URLs send "Origin: null"
		if ( 'null' !== $origin ) {
			$origin = esc_url_raw( $origin );
		}
		header( 'Access-Control-Allow-Origin: ' . $origin );
		header( 'Access-Control-Allow-Methods: OPTIONS, GET, POST, PUT, PATCH, DELETE' );
		header( 'Access-Control-Allow-Credentials: true' );
		header( 'Vary: Origin', false );
	} elseif ( ! headers_sent() && 'GET' === $_SERVER['REQUEST_METHOD'] && ! is_user_logged_in() ) {
		header( 'Vary: Origin', false );
	}

	return $value;
}

/**
 * Handles OPTIONS requests for the server.
 *
 * This is handled outside of the server code, as it doesn't obey normal route
 * mapping.
 *
 * @since 4.4.0
 *
 * @param mixed           $response Current response, either response or `null` to indicate pass-through.
 * @param WP_REST_Server  $handler  ResponseHandler instance (usually WP_REST_Server).
 * @param WP_REST_Request $request  The request that was used to make current response.
 * @return WP_REST_Response Modified response, either response or `null` to indicate pass-through.
 */
function rest_handle_options_request( $response, $handler, $request ) {
	if ( ! empty( $response ) || $request->get_method() !== 'OPTIONS' ) {
		return $response;
	}

	$response = new WP_REST_Response();
	$data     = array();

	foreach ( $handler->get_routes() as $route => $endpoints ) {
		$match = preg_match( '@^' . $route . '$@i', $request->get_route(), $matches );

		if ( ! $match ) {
			continue;
		}

		$args = array();
		foreach ( $matches as $param => $value ) {
			if ( ! is_int( $param ) ) {
				$args[ $param ] = $value;
			}
		}

		foreach ( $endpoints as $endpoint ) {
			// Remove the redundant preg_match argument.
			unset( $args[0] );

			$request->set_url_params( $args );
			$request->set_attributes( $endpoint );
		}

		$data = $handler->get_data_for_route( $route, $endpoints, 'help' );
		$response->set_matched_route( $route );
		break;
	}

	$response->set_data( $data );
	return $response;
}

/**
 * Sends the "Allow" header to state all methods that can be sent to the current route.
 *
 * @since 4.4.0
 *
 * @param WP_REST_Response $response Current response being served.
 * @param WP_REST_Server   $server   ResponseHandler instance (usually WP_REST_Server).
 * @param WP_REST_Request  $request  The request that was used to make current response.
 * @return WP_REST_Response Response to be served, with "Allow" header if route has allowed methods.
 */
function rest_send_allow_header( $response, $server, $request ) {
	$matched_route = $response->get_matched_route();

	if ( ! $matched_route ) {
		return $response;
	}

	$routes = $server->get_routes();

	$allowed_methods = array();

	// Get the allowed methods across the routes.
	foreach ( $routes[ $matched_route ] as $_handler ) {
		foreach ( $_handler['methods'] as $handler_method => $value ) {

			if ( ! empty( $_handler['permission_callback'] ) ) {

				$permission = call_user_func( $_handler['permission_callback'], $request );

				$allowed_methods[ $handler_method ] = true === $permission;
			} else {
				$allowed_methods[ $handler_method ] = true;
			}
		}
	}

	// Strip out all the methods that are not allowed (false values).
	$allowed_methods = array_filter( $allowed_methods );

	if ( $allowed_methods ) {
		$response->header( 'Allow', implode( ', ', array_map( 'strtoupper', array_keys( $allowed_methods ) ) ) );
	}

	return $response;
}

/**
 * Filter the API response to include only a white-listed set of response object fields.
 *
 * @since 4.8.0
 *
 * @param WP_REST_Response $response Current response being served.
 * @param WP_REST_Server   $server   ResponseHandler instance (usually WP_REST_Server).
 * @param WP_REST_Request  $request  The request that was used to make current response.
 *
 * @return WP_REST_Response Response to be served, trimmed down to contain a subset of fields.
 */
function rest_filter_response_fields( $response, $server, $request ) {
	if ( ! isset( $request['_fields'] ) || $response->is_error() ) {
		return $response;
	}

	$data = $response->get_data();

	$fields = wp_parse_list( $request['_fields'] );

	if ( 0 === count( $fields ) ) {
		return $response;
	}

	// Trim off outside whitespace from the comma delimited list.
	$fields = array_map( 'trim', $fields );

	$fields_as_keyed = array_combine( $fields, array_fill( 0, count( $fields ), true ) );

	if ( wp_is_numeric_array( $data ) ) {
		$new_data = array();
		foreach ( $data as $item ) {
			$new_data[] = array_intersect_key( $item, $fields_as_keyed );
		}
	} else {
		$new_data = array_intersect_key( $data, $fields_as_keyed );
	}

	$response->set_data( $new_data );

	return $response;
}

/**
 * Adds the REST API URL to the WP RSD endpoint.
 *
 * @since 4.4.0
 *
 * @see get_rest_url()
 */
function rest_output_rsd() {
	$api_root = get_rest_url();

	if ( empty( $api_root ) ) {
		return;
	}
	?>
	<api name="WP-API" blogID="1" preferred="false" apiLink="<?php echo esc_url( $api_root ); ?>" />
	<?php
}

/**
 * Outputs the REST API link tag into page header.
 *
 * @since 4.4.0
 *
 * @see get_rest_url()
 */
function rest_output_link_wp_head() {
	$api_root = get_rest_url();

	if ( empty( $api_root ) ) {
		return;
	}

	echo "<link rel='https://api.w.org/' href='" . esc_url( $api_root ) . "' />\n";
}

/**
 * Sends a Link header for the REST API.
 *
 * @since 4.4.0
 */
function rest_output_link_header() {
	if ( headers_sent() ) {
		return;
	}

	$api_root = get_rest_url();

	if ( empty( $api_root ) ) {
		return;
	}

	header( 'Link: <' . esc_url_raw( $api_root ) . '>; rel="https://api.w.org/"', false );
}

/**
 * Checks for errors when using cookie-based authentication.
 *
 * WordPress' built-in cookie authentication is always active
 * for logged in users. However, the API has to check nonces
 * for each request to ensure users are not vulnerable to CSRF.
 *
 * @since 4.4.0
 *
 * @global mixed          $wp_rest_auth_cookie
 *
 * @param WP_Error|mixed $result Error from another authentication handler,
 *                               null if we should handle it, or another value
 *                               if not.
 * @return WP_Error|mixed|bool WP_Error if the cookie is invalid, the $result, otherwise true.
 */
function rest_cookie_check_errors( $result ) {
	if ( ! empty( $result ) ) {
		return $result;
	}

	global $wp_rest_auth_cookie;

	/*
	 * Is cookie authentication being used? (If we get an auth
	 * error, but we're still logged in, another authentication
	 * must have been used).
	 */
	if ( true !== $wp_rest_auth_cookie && is_user_logged_in() ) {
		return $result;
	}

	// Determine if there is a nonce.
	$nonce = null;

	if ( isset( $_REQUEST['_wpnonce'] ) ) {
		$nonce = $_REQUEST['_wpnonce'];
	} elseif ( isset( $_SERVER['HTTP_X_WP_NONCE'] ) ) {
		$nonce = $_SERVER['HTTP_X_WP_NONCE'];
	}

	if ( null === $nonce ) {
		// No nonce at all, so act as if it's an unauthenticated request.
		wp_set_current_user( 0 );
		return true;
	}

	// Check the nonce.
	$result = wp_verify_nonce( $nonce, 'wp_rest' );

	if ( ! $result ) {
		add_filter( 'rest_send_nocache_headers', '__return_true', 20 );
		return new WP_Error( 'rest_cookie_invalid_nonce', __( 'Cookie nonce is invalid' ), array( 'status' => 403 ) );
	}

	// Send a refreshed nonce in header.
	rest_get_server()->send_header( 'X-WP-Nonce', wp_create_nonce( 'wp_rest' ) );

	return true;
}

/**
 * Collects cookie authentication status.
 *
 * Collects errors from wp_validate_auth_cookie for use by rest_cookie_check_errors.
 *
 * @since 4.4.0
 *
 * @see current_action()
 * @global mixed $wp_rest_auth_cookie
 */
function rest_cookie_collect_status() {
	global $wp_rest_auth_cookie;

	$status_type = current_action();

	if ( 'auth_cookie_valid' !== $status_type ) {
		$wp_rest_auth_cookie = substr( $status_type, 12 );
		return;
	}

	$wp_rest_auth_cookie = true;
}

/**
 * Parses an RFC3339 time into a Unix timestamp.
 *
 * @since 4.4.0
 *
 * @param string $date      RFC3339 timestamp.
 * @param bool   $force_utc Optional. Whether to force UTC timezone instead of using
 *                          the timestamp's timezone. Default false.
 * @return int Unix timestamp.
 */
function rest_parse_date( $date, $force_utc = false ) {
	if ( $force_utc ) {
		$date = preg_replace( '/[+-]\d+:?\d+$/', '+00:00', $date );
	}

	$regex = '#^\d{4}-\d{2}-\d{2}[Tt ]\d{2}:\d{2}:\d{2}(?:\.\d+)?(?:Z|[+-]\d{2}(?::\d{2})?)?$#';

	if ( ! preg_match( $regex, $date, $matches ) ) {
		return false;
	}

	return strtotime( $date );
}

/**
 * Parses a date into both its local and UTC equivalent, in MySQL datetime format.
 *
 * @since 4.4.0
 *
 * @see rest_parse_date()
 *
 * @param string $date   RFC3339 timestamp.
 * @param bool   $is_utc Whether the provided date should be interpreted as UTC. Default false.
 * @return array|null Local and UTC datetime strings, in MySQL datetime format (Y-m-d H:i:s),
 *                    null on failure.
 */
function rest_get_date_with_gmt( $date, $is_utc = false ) {
	// Whether or not the original date actually has a timezone string
	// changes the way we need to do timezone conversion.  Store this info
	// before parsing the date, and use it later.
	$has_timezone = preg_match( '#(Z|[+-]\d{2}(:\d{2})?)$#', $date );

	$date = rest_parse_date( $date );

	if ( empty( $date ) ) {
		return null;
	}

	// At this point $date could either be a local date (if we were passed a
	// *local* date without a timezone offset) or a UTC date (otherwise).
	// Timezone conversion needs to be handled differently between these two
	// cases.
	if ( ! $is_utc && ! $has_timezone ) {
		$local = date( 'Y-m-d H:i:s', $date );
		$utc   = get_gmt_from_date( $local );
	} else {
		$utc   = date( 'Y-m-d H:i:s', $date );
		$local = get_date_from_gmt( $utc );
	}

	return array( $local, $utc );
}

/**
 * Returns a contextual HTTP error code for authorization failure.
 *
 * @since 4.7.0
 *
 * @return integer 401 if the user is not logged in, 403 if the user is logged in.
 */
function rest_authorization_required_code() {
	return is_user_logged_in() ? 403 : 401;
}

/**
 * Validate a request argument based on details registered to the route.
 *
 * @since 4.7.0
 *
 * @param  mixed            $value
 * @param  WP_REST_Request  $request
 * @param  string           $param
 * @return WP_Error|boolean
 */
function rest_validate_request_arg( $value, $request, $param ) {
	$attributes = $request->get_attributes();
	if ( ! isset( $attributes['args'][ $param ] ) || ! is_array( $attributes['args'][ $param ] ) ) {
		return true;
	}
	$args = $attributes['args'][ $param ];

	return rest_validate_value_from_schema( $value, $args, $param );
}

/**
 * Sanitize a request argument based on details registered to the route.
 *
 * @since 4.7.0
 *
 * @param  mixed            $value
 * @param  WP_REST_Request  $request
 * @param  string           $param
 * @return mixed
 */
function rest_sanitize_request_arg( $value, $request, $param ) {
	$attributes = $request->get_attributes();
	if ( ! isset( $attributes['args'][ $param ] ) || ! is_array( $attributes['args'][ $param ] ) ) {
		return $value;
	}
	$args = $attributes['args'][ $param ];

	return rest_sanitize_value_from_schema( $value, $args );
}

/**
 * Parse a request argument based on details registered to the route.
 *
 * Runs a validation check and sanitizes the value, primarily to be used via
 * the `sanitize_callback` arguments in the endpoint args registration.
 *
 * @since 4.7.0
 *
 * @param  mixed            $value
 * @param  WP_REST_Request  $request
 * @param  string           $param
 * @return mixed
 */
function rest_parse_request_arg( $value, $request, $param ) {
	$is_valid = rest_validate_request_arg( $value, $request, $param );

	if ( is_wp_error( $is_valid ) ) {
		return $is_valid;
	}

	$value = rest_sanitize_request_arg( $value, $request, $param );

	return $value;
}

/**
 * Determines if an IP address is valid.
 *
 * Handles both IPv4 and IPv6 addresses.
 *
 * @since 4.7.0
 *
 * @param  string $ip IP address.
 * @return string|false The valid IP address, otherwise false.
 */
function rest_is_ip_address( $ip ) {
	$ipv4_pattern = '/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/';

	if ( ! preg_match( $ipv4_pattern, $ip ) && ! Requests_IPv6::check_ipv6( $ip ) ) {
		return false;
	}

	return $ip;
}

/**
 * Changes a boolean-like value into the proper boolean value.
 *
 * @since 4.7.0
 *
 * @param bool|string|int $value The value being evaluated.
 * @return boolean Returns the proper associated boolean value.
 */
function rest_sanitize_boolean( $value ) {
	// String values are translated to `true`; make sure 'false' is false.
	if ( is_string( $value ) ) {
		$value = strtolower( $value );
		if ( in_array( $value, array( 'false', '0' ), true ) ) {
			$value = false;
		}
	}

	// Everything else will map nicely to boolean.
	return (bool) $value;
}

/**
 * Determines if a given value is boolean-like.
 *
 * @since 4.7.0
 *
 * @param bool|string $maybe_bool The value being evaluated.
 * @return boolean True if a boolean, otherwise false.
 */
function rest_is_boolean( $maybe_bool ) {
	if ( is_bool( $maybe_bool ) ) {
		return true;
	}

	if ( is_string( $maybe_bool ) ) {
		$maybe_bool = strtolower( $maybe_bool );

		$valid_boolean_values = array(
			'false',
			'true',
			'0',
			'1',
		);

		return in_array( $maybe_bool, $valid_boolean_values, true );
	}

	if ( is_int( $maybe_bool ) ) {
		return in_array( $maybe_bool, array( 0, 1 ), true );
	}

	return false;
}

/**
 * Retrieves the avatar urls in various sizes based on a given email address.
 *
 * @since 4.7.0
 *
 * @see get_avatar_url()
 *
 * @param string $email Email address.
 * @return array $urls Gravatar url for each size.
 */
function rest_get_avatar_urls( $email ) {
	$avatar_sizes = rest_get_avatar_sizes();

	$urls = array();
	foreach ( $avatar_sizes as $size ) {
		$urls[ $size ] = get_avatar_url( $email, array( 'size' => $size ) );
	}

	return $urls;
}

/**
 * Retrieves the pixel sizes for avatars.
 *
 * @since 4.7.0
 *
 * @return array List of pixel sizes for avatars. Default `[ 24, 48, 96 ]`.
 */
function rest_get_avatar_sizes() {
	/**
	 * Filters the REST avatar sizes.
	 *
	 * Use this filter to adjust the array of sizes returned by the
	 * `rest_get_avatar_sizes` function.
	 *
	 * @since 4.4.0
	 *
	 * @param array $sizes An array of int values that are the pixel sizes for avatars.
	 *                     Default `[ 24, 48, 96 ]`.
	 */
	return apply_filters( 'rest_avatar_sizes', array( 24, 48, 96 ) );
}

/**
 * Validate a value based on a schema.
 *
 * @since 4.7.0
 *
 * @param mixed  $value The value to validate.
 * @param array  $args  Schema array to use for validation.
 * @param string $param The parameter name, used in error messages.
 * @return true|WP_Error
 */
function rest_validate_value_from_schema( $value, $args, $param = '' ) {
	if ( 'array' === $args['type'] ) {
		if ( ! is_null( $value ) ) {
			$value = wp_parse_list( $value );
		}
		if ( ! wp_is_numeric_array( $value ) ) {
			/* translators: 1: parameter, 2: type name */
			return new WP_Error( 'rest_invalid_param', sprintf( __( '%1$s is not of type %2$s.' ), $param, 'array' ) );
		}
		foreach ( $value as $index => $v ) {
			$is_valid = rest_validate_value_from_schema( $v, $args['items'], $param . '[' . $index . ']' );
			if ( is_wp_error( $is_valid ) ) {
				return $is_valid;
			}
		}
	}

	if ( 'object' === $args['type'] ) {
		if ( $value instanceof stdClass ) {
			$value = (array) $value;
		}
		if ( ! is_array( $value ) ) {
			/* translators: 1: parameter, 2: type name */
			return new WP_Error( 'rest_invalid_param', sprintf( __( '%1$s is not of type %2$s.' ), $param, 'object' ) );
		}

		foreach ( $value as $property => $v ) {
			if ( isset( $args['properties'][ $property ] ) ) {
				$is_valid = rest_validate_value_from_schema( $v, $args['properties'][ $property ], $param . '[' . $property . ']' );
				if ( is_wp_error( $is_valid ) ) {
					return $is_valid;
				}
			} elseif ( isset( $args['additionalProperties'] ) && false === $args['additionalProperties'] ) {
				return new WP_Error( 'rest_invalid_param', sprintf( __( '%1$s is not a valid property of Object.' ), $property ) );
			}
		}
	}

	if ( ! empty( $args['enum'] ) ) {
		if ( ! in_array( $value, $args['enum'], true ) ) {
			/* translators: 1: parameter, 2: list of valid values */
			return new WP_Error( 'rest_invalid_param', sprintf( __( '%1$s is not one of %2$s.' ), $param, implode( ', ', $args['enum'] ) ) );
		}
	}

	if ( in_array( $args['type'], array( 'integer', 'number' ) ) && ! is_numeric( $value ) ) {
		/* translators: 1: parameter, 2: type name */
		return new WP_Error( 'rest_invalid_param', sprintf( __( '%1$s is not of type %2$s.' ), $param, $args['type'] ) );
	}

	if ( 'integer' === $args['type'] && round( floatval( $value ) ) !== floatval( $value ) ) {
		/* translators: 1: parameter, 2: type name */
		return new WP_Error( 'rest_invalid_param', sprintf( __( '%1$s is not of type %2$s.' ), $param, 'integer' ) );
	}

	if ( 'boolean' === $args['type'] && ! rest_is_boolean( $value ) ) {
		/* translators: 1: parameter, 2: type name */
		return new WP_Error( 'rest_invalid_param', sprintf( __( '%1$s is not of type %2$s.' ), $value, 'boolean' ) );
	}

	if ( 'string' === $args['type'] && ! is_string( $value ) ) {
		/* translators: 1: parameter, 2: type name */
		return new WP_Error( 'rest_invalid_param', sprintf( __( '%1$s is not of type %2$s.' ), $param, 'string' ) );
	}

	if ( isset( $args['format'] ) ) {
		switch ( $args['format'] ) {
			case 'date-time':
				if ( ! rest_parse_date( $value ) ) {
					return new WP_Error( 'rest_invalid_date', __( 'Invalid date.' ) );
				}
				break;

			case 'email':
				if ( ! is_email( $value ) ) {
					return new WP_Error( 'rest_invalid_email', __( 'Invalid email address.' ) );
				}
				break;
			case 'ip':
				if ( ! rest_is_ip_address( $value ) ) {
					/* translators: %s: IP address */
					return new WP_Error( 'rest_invalid_param', sprintf( __( '%s is not a valid IP address.' ), $value ) );
				}
				break;
		}
	}

	if ( in_array( $args['type'], array( 'number', 'integer' ), true ) && ( isset( $args['minimum'] ) || isset( $args['maximum'] ) ) ) {
		if ( isset( $args['minimum'] ) && ! isset( $args['maximum'] ) ) {
			if ( ! empty( $args['exclusiveMinimum'] ) && $value <= $args['minimum'] ) {
				/* translators: 1: parameter, 2: minimum number */
				return new WP_Error( 'rest_invalid_param', sprintf( __( '%1$s must be greater than %2$d' ), $param, $args['minimum'] ) );
			} elseif ( empty( $args['exclusiveMinimum'] ) && $value < $args['minimum'] ) {
				/* translators: 1: parameter, 2: minimum number */
				return new WP_Error( 'rest_invalid_param', sprintf( __( '%1$s must be greater than or equal to %2$d' ), $param, $args['minimum'] ) );
			}
		} elseif ( isset( $args['maximum'] ) && ! isset( $args['minimum'] ) ) {
			if ( ! empty( $args['exclusiveMaximum'] ) && $value >= $args['maximum'] ) {
				/* translators: 1: parameter, 2: maximum number */
				return new WP_Error( 'rest_invalid_param', sprintf( __( '%1$s must be less than %2$d' ), $param, $args['maximum'] ) );
			} elseif ( empty( $args['exclusiveMaximum'] ) && $value > $args['maximum'] ) {
				/* translators: 1: parameter, 2: maximum number */
				return new WP_Error( 'rest_invalid_param', sprintf( __( '%1$s must be less than or equal to %2$d' ), $param, $args['maximum'] ) );
			}
		} elseif ( isset( $args['maximum'] ) && isset( $args['minimum'] ) ) {
			if ( ! empty( $args['exclusiveMinimum'] ) && ! empty( $args['exclusiveMaximum'] ) ) {
				if ( $value >= $args['maximum'] || $value <= $args['minimum'] ) {
					/* translators: 1: parameter, 2: minimum number, 3: maximum number */
					return new WP_Error( 'rest_invalid_param', sprintf( __( '%1$s must be between %2$d (exclusive) and %3$d (exclusive)' ), $param, $args['minimum'], $args['maximum'] ) );
				}
			} elseif ( empty( $args['exclusiveMinimum'] ) && ! empty( $args['exclusiveMaximum'] ) ) {
				if ( $value >= $args['maximum'] || $value < $args['minimum'] ) {
					/* translators: 1: parameter, 2: minimum number, 3: maximum number */
					return new WP_Error( 'rest_invalid_param', sprintf( __( '%1$s must be between %2$d (inclusive) and %3$d (exclusive)' ), $param, $args['minimum'], $args['maximum'] ) );
				}
			} elseif ( ! empty( $args['exclusiveMinimum'] ) && empty( $args['exclusiveMaximum'] ) ) {
				if ( $value > $args['maximum'] || $value <= $args['minimum'] ) {
					/* translators: 1: parameter, 2: minimum number, 3: maximum number */
					return new WP_Error( 'rest_invalid_param', sprintf( __( '%1$s must be between %2$d (exclusive) and %3$d (inclusive)' ), $param, $args['minimum'], $args['maximum'] ) );
				}
			} elseif ( empty( $args['exclusiveMinimum'] ) && empty( $args['exclusiveMaximum'] ) ) {
				if ( $value > $args['maximum'] || $value < $args['minimum'] ) {
					/* translators: 1: parameter, 2: minimum number, 3: maximum number */
					return new WP_Error( 'rest_invalid_param', sprintf( __( '%1$s must be between %2$d (inclusive) and %3$d (inclusive)' ), $param, $args['minimum'], $args['maximum'] ) );
				}
			}
		}
	}

	return true;
}

/**
 * Sanitize a value based on a schema.
 *
 * @since 4.7.0
 *
 * @param mixed $value The value to sanitize.
 * @param array $args  Schema array to use for sanitization.
 * @return true|WP_Error
 */
function rest_sanitize_value_from_schema( $value, $args ) {
	if ( 'array' === $args['type'] ) {
		if ( empty( $args['items'] ) ) {
			return (array) $value;
		}
		$value = wp_parse_list( $value );
		foreach ( $value as $index => $v ) {
			$value[ $index ] = rest_sanitize_value_from_schema( $v, $args['items'] );
		}
		// Normalize to numeric array so nothing unexpected
		// is in the keys.
		$value = array_values( $value );
		return $value;
	}

	if ( 'object' === $args['type'] ) {
		if ( $value instanceof stdClass ) {
			$value = (array) $value;
		}
		if ( ! is_array( $value ) ) {
			return array();
		}

		foreach ( $value as $property => $v ) {
			if ( isset( $args['properties'][ $property ] ) ) {
				$value[ $property ] = rest_sanitize_value_from_schema( $v, $args['properties'][ $property ] );
			} elseif ( isset( $args['additionalProperties'] ) && false === $args['additionalProperties'] ) {
				unset( $value[ $property ] );
			}
		}

		return $value;
	}

	if ( 'integer' === $args['type'] ) {
		return (int) $value;
	}

	if ( 'number' === $args['type'] ) {
		return (float) $value;
	}

	if ( 'boolean' === $args['type'] ) {
		return rest_sanitize_boolean( $value );
	}

	if ( isset( $args['format'] ) ) {
		switch ( $args['format'] ) {
			case 'date-time':
				return sanitize_text_field( $value );

			case 'email':
				/*
				 * sanitize_email() validates, which would be unexpected.
				 */
				return sanitize_text_field( $value );

			case 'uri':
				return esc_url_raw( $value );

			case 'ip':
				return sanitize_text_field( $value );
		}
	}

	if ( 'string' === $args['type'] ) {
		return strval( $value );
	}

	return $value;
}

/**
 * Append result of internal request to REST API for purpose of preloading data to be attached to a page.
 * Expected to be called in the context of `array_reduce`.
 *
 * @since 5.0.0
 *
 * @param  array  $memo Reduce accumulator.
 * @param  string $path REST API path to preload.
 * @return array        Modified reduce accumulator.
 */
function rest_preload_api_request( $memo, $path ) {
	// array_reduce() doesn't support passing an array in PHP 5.2, so we need to make sure we start with one.
	if ( ! is_array( $memo ) ) {
		$memo = array();
	}

	if ( empty( $path ) ) {
		return $memo;
	}

	$method = 'GET';
	if ( is_array( $path ) && 2 === count( $path ) ) {
		$method = end( $path );
		$path   = reset( $path );

		if ( ! in_array( $method, array( 'GET', 'OPTIONS' ), true ) ) {
			$method = 'GET';
		}
	}

	$path_parts = parse_url( $path );
	if ( false === $path_parts ) {
		return $memo;
	}

	$request = new WP_REST_Request( $method, $path_parts['path'] );
	if ( ! empty( $path_parts['query'] ) ) {
		parse_str( $path_parts['query'], $query_params );
		$request->set_query_params( $query_params );
	}

	$response = rest_do_request( $request );
	if ( 200 === $response->status ) {
		$server = rest_get_server();
		$data   = (array) $response->get_data();
		$links  = $server->get_compact_response_links( $response );
		if ( ! empty( $links ) ) {
			$data['_links'] = $links;
		}

		if ( 'OPTIONS' === $method ) {
			$response = rest_send_allow_header( $response, $server, $request );

			$memo[ $method ][ $path ] = array(
				'body'    => $data,
				'headers' => $response->headers,
			);
		} else {
			$memo[ $path ] = array(
				'body'    => $data,
				'headers' => $response->headers,
			);
		}
	}

	return $memo;
}

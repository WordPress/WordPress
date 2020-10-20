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
 * @since 5.1.0 Added a `_doing_it_wrong()` notice when not called on or after the `rest_api_init` hook.
 * @since 5.5.0 Added a `_doing_it_wrong()` notice when the required `permission_callback` argument is not set.
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

	$clean_namespace = trim( $namespace, '/' );

	if ( $clean_namespace !== $namespace ) {
		_doing_it_wrong( __FUNCTION__, __( 'Namespace must not start or end with a slash.' ), '5.4.2' );
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

		if ( ! isset( $arg_group['permission_callback'] ) ) {
			_doing_it_wrong(
				__FUNCTION__,
				sprintf(
					/* translators: 1: The REST API route being registered, 2: The argument name, 3: The suggested function name. */
					__( 'The REST API route definition for %1$s is missing the required %2$s argument. For REST API routes that are intended to be public, use %3$s as the permission callback.' ),
					'<code>' . $clean_namespace . '/' . trim( $route, '/' ) . '</code>',
					'<code>permission_callback</code>',
					'<code>__return_true</code>'
				),
				'5.5.0'
			);
		}
	}

	$full_route = '/' . $clean_namespace . '/' . trim( $route, '/' );
	rest_get_server()->register_route( $clean_namespace, $full_route, $args, $override );
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
 * @param string       $attribute   The attribute name.
 * @param array        $args {
 *     Optional. An array of arguments used to handle the registered field.
 *
 *     @type callable|null $get_callback    Optional. The callback function used to retrieve the field value. Default is
 *                                          'null', the field will not be returned in the response. The function will
 *                                          be passed the prepared object data.
 *     @type callable|null $update_callback Optional. The callback function used to set and update the field value. Default
 *                                          is 'null', the value cannot be set or updated. The function will be passed
 *                                          the model object, like WP_Post.
 *     @type array|null $schema             Optional. The callback function used to create the schema for this field.
 *                                          Default is 'null', no schema entry will be returned.
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
 * @global WP_Rewrite $wp_rewrite WordPress rewrite component.
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
	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		// Deprecated reporting.
		add_action( 'deprecated_function_run', 'rest_handle_deprecated_function', 10, 3 );
		add_filter( 'deprecated_function_trigger_error', '__return_false' );
		add_action( 'deprecated_argument_run', 'rest_handle_deprecated_argument', 10, 3 );
		add_filter( 'deprecated_argument_trigger_error', '__return_false' );
		add_action( 'doing_it_wrong_run', 'rest_handle_doing_it_wrong', 10, 3 );
		add_filter( 'doing_it_wrong_trigger_error', '__return_false' );
	}

	// Default serving.
	add_filter( 'rest_pre_serve_request', 'rest_send_cors_headers' );
	add_filter( 'rest_post_dispatch', 'rest_send_allow_header', 10, 3 );
	add_filter( 'rest_post_dispatch', 'rest_filter_response_fields', 10, 3 );

	add_filter( 'rest_pre_dispatch', 'rest_handle_options_request', 10, 3 );
	add_filter( 'rest_index', 'rest_add_application_passwords_to_index' );
}

/**
 * Registers default REST API routes.
 *
 * @since 4.7.0
 */
function create_initial_rest_routes() {
	foreach ( get_post_types( array( 'show_in_rest' => true ), 'objects' ) as $post_type ) {
		$controller = $post_type->get_rest_controller();

		if ( ! $controller ) {
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
		$controller = $taxonomy->get_rest_controller();

		if ( ! $controller ) {
			continue;
		}

		$controller->register_routes();
	}

	// Users.
	$controller = new WP_REST_Users_Controller;
	$controller->register_routes();

	// Application Passwords
	$controller = new WP_REST_Application_Passwords_Controller();
	$controller->register_routes();

	// Comments.
	$controller = new WP_REST_Comments_Controller;
	$controller->register_routes();

	$search_handlers = array(
		new WP_REST_Post_Search_Handler(),
		new WP_REST_Term_Search_Handler(),
		new WP_REST_Post_Format_Search_Handler(),
	);

	/**
	 * Filters the search handlers to use in the REST search controller.
	 *
	 * @since 5.0.0
	 *
	 * @param array $search_handlers List of search handlers to use in the controller. Each search
	 *                               handler instance must extend the `WP_REST_Search_Handler` class.
	 *                               Default is only a handler for posts.
	 */
	$search_handlers = apply_filters( 'wp_rest_search_handlers', $search_handlers );

	$controller = new WP_REST_Search_Controller( $search_handlers );
	$controller->register_routes();

	// Block Renderer.
	$controller = new WP_REST_Block_Renderer_Controller;
	$controller->register_routes();

	// Block Types.
	$controller = new WP_REST_Block_Types_Controller();
	$controller->register_routes();

	// Settings.
	$controller = new WP_REST_Settings_Controller;
	$controller->register_routes();

	// Themes.
	$controller = new WP_REST_Themes_Controller;
	$controller->register_routes();

	// Plugins.
	$controller = new WP_REST_Plugins_Controller();
	$controller->register_routes();

	// Block Directory.
	$controller = new WP_REST_Block_Directory_Controller();
	$controller->register_routes();

	// Site Health
	$site_health = WP_Site_Health::get_instance();
	$controller  = new WP_REST_Site_Health_Controller( $site_health );
	$controller->register_routes();
}

/**
 * Loads the REST API.
 *
 * @since 4.4.0
 *
 * @global WP $wp Current WordPress environment instance.
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
 * @global WP_Rewrite $wp_rewrite WordPress rewrite component.
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
		// nginx only allows HTTP/1.0 methods when redirecting from / to /index.php.
		// To work around this, we manually add index.php to the URL, avoiding the redirect.
		if ( 'index.php' !== substr( $url, 9 ) ) {
			$url .= 'index.php';
		}

		$url = add_query_arg( 'rest_route', $path, $url );
	}

	if ( is_ssl() && isset( $_SERVER['SERVER_NAME'] ) ) {
		// If the current host is the same as the REST URL host, force the REST URL scheme to HTTPS.
		if ( parse_url( get_home_url( $blog_id ), PHP_URL_HOST ) === $_SERVER['SERVER_NAME'] ) {
			$url = set_url_scheme( $url, 'https' );
		}
	}

	if ( is_admin() && force_ssl_admin() ) {
		/*
		 * In this situation the home URL may be http:, and `is_ssl()` may be false,
		 * but the admin is served over https: (one way or another), so REST API usage
		 * will be blocked by browsers unless it is also served over HTTPS.
		 */
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
 * @param string $scheme Optional. Sanitization scheme. Default 'rest'.
 * @return string Full URL to the endpoint.
 */
function rest_url( $path = '', $scheme = 'rest' ) {
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
 * @since 5.3.0 Accept string argument for the request path.
 *
 * @param array|string|WP_REST_Request $request Request to check.
 * @return WP_REST_Request REST request instance.
 */
function rest_ensure_request( $request ) {
	if ( $request instanceof WP_REST_Request ) {
		return $request;
	}

	if ( is_string( $request ) ) {
		return new WP_REST_Request( 'GET', $request );
	}

	return new WP_REST_Request( 'GET', '', $request );
}

/**
 * Ensures a REST response is a response object (for consistency).
 *
 * This implements WP_REST_Response, allowing usage of `set_status`/`header`/etc
 * without needing to double-check the object. Will also allow WP_Error to indicate error
 * responses, so users should immediately check for this value.
 *
 * @since 4.4.0
 *
 * @param WP_REST_Response|WP_Error|WP_HTTP_Response|mixed $response Response to check.
 * @return WP_REST_Response|WP_Error If response generated an error, WP_Error, if response
 *                                   is already an instance, WP_REST_Response, otherwise
 *                                   returns a new WP_REST_Response instance.
 */
function rest_ensure_response( $response ) {
	if ( is_wp_error( $response ) ) {
		return $response;
	}

	if ( $response instanceof WP_REST_Response ) {
		return $response;
	}

	// While WP_HTTP_Response is the base class of WP_REST_Response, it doesn't provide
	// all the required methods used in WP_REST_Server::dispatch().
	if ( $response instanceof WP_HTTP_Response ) {
		return new WP_REST_Response(
			$response->get_data(),
			$response->get_status(),
			$response->get_headers()
		);
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
		/* translators: 1: Function name, 2: WordPress version number, 3: New function name. */
		$string = sprintf( __( '%1$s (since %2$s; use %3$s instead)' ), $function, $version, $replacement );
	} else {
		/* translators: 1: Function name, 2: WordPress version number. */
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
	if ( $message ) {
		/* translators: 1: Function name, 2: WordPress version number, 3: Error message. */
		$string = sprintf( __( '%1$s (since %2$s; %3$s)' ), $function, $version, $message );
	} else {
		/* translators: 1: Function name, 2: WordPress version number. */
		$string = sprintf( __( '%1$s (since %2$s; no alternative available)' ), $function, $version );
	}

	header( sprintf( 'X-WP-DeprecatedParam: %s', $string ) );
}

/**
 * Handles _doing_it_wrong errors.
 *
 * @since 5.5.0
 *
 * @param string      $function The function that was called.
 * @param string      $message  A message explaining what has been done incorrectly.
 * @param string|null $version  The version of WordPress where the message was added.
 */
function rest_handle_doing_it_wrong( $function, $message, $version ) {
	if ( ! WP_DEBUG || headers_sent() ) {
		return;
	}

	if ( $version ) {
		/* translators: Developer debugging message. 1: PHP function name, 2: WordPress version number, 3: Explanatory message. */
		$string = __( '%1$s (since %2$s; %3$s)' );
		$string = sprintf( $string, $function, $version, $message );
	} else {
		/* translators: Developer debugging message. 1: PHP function name, 2: Explanatory message. */
		$string = __( '%1$s (%2$s)' );
		$string = sprintf( $string, $function, $message );
	}

	header( sprintf( 'X-WP-DoingItWrong: %s', $string ) );
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
		// Requests from file:// and data: URLs send "Origin: null".
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
			// Remove the redundant preg_match() argument.
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
 * Recursively computes the intersection of arrays using keys for comparison.
 *
 * @since 5.3.0
 *
 * @param array $array1 The array with master keys to check.
 * @param array $array2 An array to compare keys against.
 * @return array An associative array containing all the entries of array1 which have keys
 *               that are present in all arguments.
 */
function _rest_array_intersect_key_recursive( $array1, $array2 ) {
	$array1 = array_intersect_key( $array1, $array2 );
	foreach ( $array1 as $key => $value ) {
		if ( is_array( $value ) && is_array( $array2[ $key ] ) ) {
			$array1[ $key ] = _rest_array_intersect_key_recursive( $value, $array2[ $key ] );
		}
	}
	return $array1;
}

/**
 * Filters the API response to include only a white-listed set of response object fields.
 *
 * @since 4.8.0
 *
 * @param WP_REST_Response $response Current response being served.
 * @param WP_REST_Server   $server   ResponseHandler instance (usually WP_REST_Server).
 * @param WP_REST_Request  $request  The request that was used to make current response.
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

	// Create nested array of accepted field hierarchy.
	$fields_as_keyed = array();
	foreach ( $fields as $field ) {
		$parts = explode( '.', $field );
		$ref   = &$fields_as_keyed;
		while ( count( $parts ) > 1 ) {
			$next = array_shift( $parts );
			if ( isset( $ref[ $next ] ) && true === $ref[ $next ] ) {
				// Skip any sub-properties if their parent prop is already marked for inclusion.
				break 2;
			}
			$ref[ $next ] = isset( $ref[ $next ] ) ? $ref[ $next ] : array();
			$ref          = &$ref[ $next ];
		}
		$last         = array_shift( $parts );
		$ref[ $last ] = true;
	}

	if ( wp_is_numeric_array( $data ) ) {
		$new_data = array();
		foreach ( $data as $item ) {
			$new_data[] = _rest_array_intersect_key_recursive( $item, $fields_as_keyed );
		}
	} else {
		$new_data = _rest_array_intersect_key_recursive( $data, $fields_as_keyed );
	}

	$response->set_data( $new_data );

	return $response;
}

/**
 * Given an array of fields to include in a response, some of which may be
 * `nested.fields`, determine whether the provided field should be included
 * in the response body.
 *
 * If a parent field is passed in, the presence of any nested field within
 * that parent will cause the method to return `true`. For example "title"
 * will return true if any of `title`, `title.raw` or `title.rendered` is
 * provided.
 *
 * @since 5.3.0
 *
 * @param string $field  A field to test for inclusion in the response body.
 * @param array  $fields An array of string fields supported by the endpoint.
 * @return bool Whether to include the field or not.
 */
function rest_is_field_included( $field, $fields ) {
	if ( in_array( $field, $fields, true ) ) {
		return true;
	}

	foreach ( $fields as $accepted_field ) {
		// Check to see if $field is the parent of any item in $fields.
		// A field "parent" should be accepted if "parent.child" is accepted.
		if ( strpos( $accepted_field, "$field." ) === 0 ) {
			return true;
		}
		// Conversely, if "parent" is accepted, all "parent.child" fields
		// should also be accepted.
		if ( strpos( $field, "$accepted_field." ) === 0 ) {
			return true;
		}
	}

	return false;
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

	printf( '<link rel="https://api.w.org/" href="%s" />', esc_url( $api_root ) );

	$resource = rest_get_queried_resource_route();

	if ( $resource ) {
		printf( '<link rel="alternate" type="application/json" href="%s" />', esc_url( rest_url( $resource ) ) );
	}
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

	header( sprintf( 'Link: <%s>; rel="https://api.w.org/"', esc_url_raw( $api_root ) ), false );

	$resource = rest_get_queried_resource_route();

	if ( $resource ) {
		header( sprintf( 'Link: <%s>; rel="alternate"; type="application/json"', esc_url_raw( rest_url( $resource ) ) ), false );
	}
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
 *                               null if we should handle it, or another value if not.
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
 * Collects the status of authenticating with an application password.
 *
 * @since 5.6.0
 *
 * @global WP_User|WP_Error|null $wp_rest_application_password_status
 *
 * @param WP_Error $user_or_error The authenticated user or error instance.
 */
function rest_application_password_collect_status( $user_or_error ) {
	global $wp_rest_application_password_status;

	$wp_rest_application_password_status = $user_or_error;
}

/**
 * Checks for errors when using application password-based authentication.
 *
 * @since 5.6.0
 *
 * @global WP_User|WP_Error|null $wp_rest_application_password_status
 *
 * @param WP_Error|null|true $result Error from another authentication handler,
 *                                   null if we should handle it, or another value if not.
 * @return WP_Error|null|true WP_Error if the application password is invalid, the $result, otherwise true.
 */
function rest_application_password_check_errors( $result ) {
	global $wp_rest_application_password_status;

	if ( ! empty( $result ) ) {
		return $result;
	}

	if ( is_wp_error( $wp_rest_application_password_status ) ) {
		$data = $wp_rest_application_password_status->get_error_data();

		if ( ! isset( $data['status'] ) ) {
			$data['status'] = 401;
		}

		$wp_rest_application_password_status->add_data( $data );

		return $wp_rest_application_password_status;
	}

	if ( $wp_rest_application_password_status instanceof WP_User ) {
		return true;
	}

	return $result;
}

/**
 * Adds Application Passwords info to the REST API index.
 *
 * @since 5.6.0
 *
 * @param WP_REST_Response $response The index response object.
 * @return WP_REST_Response
 */
function rest_add_application_passwords_to_index( $response ) {
	if ( ! wp_is_application_passwords_available() ) {
		return $response;
	}

	$response->data['authentication']['application-passwords'] = array(
		'endpoints' => array(
			'authorization' => admin_url( 'authorize-application.php' ),
		),
	);

	return $response;
}

/**
 * Retrieves the avatar urls in various sizes.
 *
 * @since 4.7.0
 *
 * @see get_avatar_url()
 *
 * @param mixed $id_or_email The Gravatar to retrieve a URL for. Accepts a user_id, gravatar md5 hash,
 *                           user email, WP_User object, WP_Post object, or WP_Comment object.
 * @return array Avatar URLs keyed by size. Each value can be a URL string or boolean false.
 */
function rest_get_avatar_urls( $id_or_email ) {
	$avatar_sizes = rest_get_avatar_sizes();

	$urls = array();
	foreach ( $avatar_sizes as $size ) {
		$urls[ $size ] = get_avatar_url( $id_or_email, array( 'size' => $size ) );
	}

	return $urls;
}

/**
 * Retrieves the pixel sizes for avatars.
 *
 * @since 4.7.0
 *
 * @return int[] List of pixel sizes for avatars. Default `[ 24, 48, 96 ]`.
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
	 * @param int[] $sizes An array of int values that are the pixel sizes for avatars.
	 *                     Default `[ 24, 48, 96 ]`.
	 */
	return apply_filters( 'rest_avatar_sizes', array( 24, 48, 96 ) );
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
 * Parses a 3 or 6 digit hex color (with #).
 *
 * @since 5.4.0
 *
 * @param string $color 3 or 6 digit hex color (with #).
 * @return string|false
 */
function rest_parse_hex_color( $color ) {
	$regex = '|^#([A-Fa-f0-9]{3}){1,2}$|';
	if ( ! preg_match( $regex, $color, $matches ) ) {
		return false;
	}

	return $color;
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
	/*
	 * Whether or not the original date actually has a timezone string
	 * changes the way we need to do timezone conversion.
	 * Store this info before parsing the date, and use it later.
	 */
	$has_timezone = preg_match( '#(Z|[+-]\d{2}(:\d{2})?)$#', $date );

	$date = rest_parse_date( $date );

	if ( empty( $date ) ) {
		return null;
	}

	/*
	 * At this point $date could either be a local date (if we were passed
	 * a *local* date without a timezone offset) or a UTC date (otherwise).
	 * Timezone conversion needs to be handled differently between these two cases.
	 */
	if ( ! $is_utc && ! $has_timezone ) {
		$local = gmdate( 'Y-m-d H:i:s', $date );
		$utc   = get_gmt_from_date( $local );
	} else {
		$utc   = gmdate( 'Y-m-d H:i:s', $date );
		$local = get_date_from_gmt( $utc );
	}

	return array( $local, $utc );
}

/**
 * Returns a contextual HTTP error code for authorization failure.
 *
 * @since 4.7.0
 *
 * @return int 401 if the user is not logged in, 403 if the user is logged in.
 */
function rest_authorization_required_code() {
	return is_user_logged_in() ? 403 : 401;
}

/**
 * Validate a request argument based on details registered to the route.
 *
 * @since 4.7.0
 *
 * @param mixed           $value
 * @param WP_REST_Request $request
 * @param string          $param
 * @return true|WP_Error
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
 * @param mixed           $value
 * @param WP_REST_Request $request
 * @param string          $param
 * @return mixed
 */
function rest_sanitize_request_arg( $value, $request, $param ) {
	$attributes = $request->get_attributes();
	if ( ! isset( $attributes['args'][ $param ] ) || ! is_array( $attributes['args'][ $param ] ) ) {
		return $value;
	}
	$args = $attributes['args'][ $param ];

	return rest_sanitize_value_from_schema( $value, $args, $param );
}

/**
 * Parse a request argument based on details registered to the route.
 *
 * Runs a validation check and sanitizes the value, primarily to be used via
 * the `sanitize_callback` arguments in the endpoint args registration.
 *
 * @since 4.7.0
 *
 * @param mixed           $value
 * @param WP_REST_Request $request
 * @param string          $param
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
 * @param string $ip IP address.
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
 * @return bool Returns the proper associated boolean value.
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
 * @return bool True if a boolean, otherwise false.
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
 * Determines if a given value is integer-like.
 *
 * @since 5.5.0
 *
 * @param mixed $maybe_integer The value being evaluated.
 * @return bool True if an integer, otherwise false.
 */
function rest_is_integer( $maybe_integer ) {
	return is_numeric( $maybe_integer ) && round( (float) $maybe_integer ) === (float) $maybe_integer;
}

/**
 * Determines if a given value is array-like.
 *
 * @since 5.5.0
 *
 * @param mixed $maybe_array The value being evaluated.
 * @return bool
 */
function rest_is_array( $maybe_array ) {
	if ( is_scalar( $maybe_array ) ) {
		$maybe_array = wp_parse_list( $maybe_array );
	}

	return wp_is_numeric_array( $maybe_array );
}

/**
 * Converts an array-like value to an array.
 *
 * @since 5.5.0
 *
 * @param mixed $maybe_array The value being evaluated.
 * @return array Returns the array extracted from the value.
 */
function rest_sanitize_array( $maybe_array ) {
	if ( is_scalar( $maybe_array ) ) {
		return wp_parse_list( $maybe_array );
	}

	if ( ! is_array( $maybe_array ) ) {
		return array();
	}

	// Normalize to numeric array so nothing unexpected is in the keys.
	return array_values( $maybe_array );
}

/**
 * Determines if a given value is object-like.
 *
 * @since 5.5.0
 *
 * @param mixed $maybe_object The value being evaluated.
 * @return bool True if object like, otherwise false.
 */
function rest_is_object( $maybe_object ) {
	if ( '' === $maybe_object ) {
		return true;
	}

	if ( $maybe_object instanceof stdClass ) {
		return true;
	}

	if ( $maybe_object instanceof JsonSerializable ) {
		$maybe_object = $maybe_object->jsonSerialize();
	}

	return is_array( $maybe_object );
}

/**
 * Converts an object-like value to an object.
 *
 * @since 5.5.0
 *
 * @param mixed $maybe_object The value being evaluated.
 * @return array Returns the object extracted from the value.
 */
function rest_sanitize_object( $maybe_object ) {
	if ( '' === $maybe_object ) {
		return array();
	}

	if ( $maybe_object instanceof stdClass ) {
		return (array) $maybe_object;
	}

	if ( $maybe_object instanceof JsonSerializable ) {
		$maybe_object = $maybe_object->jsonSerialize();
	}

	if ( ! is_array( $maybe_object ) ) {
		return array();
	}

	return $maybe_object;
}

/**
 * Gets the best type for a value.
 *
 * @since 5.5.0
 *
 * @param mixed $value The value to check.
 * @param array $types The list of possible types.
 * @return string The best matching type, an empty string if no types match.
 */
function rest_get_best_type_for_value( $value, $types ) {
	static $checks = array(
		'array'   => 'rest_is_array',
		'object'  => 'rest_is_object',
		'integer' => 'rest_is_integer',
		'number'  => 'is_numeric',
		'boolean' => 'rest_is_boolean',
		'string'  => 'is_string',
		'null'    => 'is_null',
	);

	// Both arrays and objects allow empty strings to be converted to their types.
	// But the best answer for this type is a string.
	if ( '' === $value && in_array( 'string', $types, true ) ) {
		return 'string';
	}

	foreach ( $types as $type ) {
		if ( isset( $checks[ $type ] ) && $checks[ $type ]( $value ) ) {
			return $type;
		}
	}

	return '';
}

/**
 * Handles getting the best type for a multi-type schema.
 *
 * This is a wrapper for {@see rest_get_best_type_for_value()} that handles
 * backward compatibility for schemas that use invalid types.
 *
 * @since 5.5.0
 *
 * @param mixed  $value The value to check.
 * @param array  $args  The schema array to use.
 * @param string $param The parameter name, used in error messages.
 * @return string
 */
function rest_handle_multi_type_schema( $value, $args, $param = '' ) {
	$allowed_types = array( 'array', 'object', 'string', 'number', 'integer', 'boolean', 'null' );
	$invalid_types = array_diff( $args['type'], $allowed_types );

	if ( $invalid_types ) {
		_doing_it_wrong(
			__FUNCTION__,
			/* translators: 1: Parameter, 2: List of allowed types. */
			wp_sprintf( __( 'The "type" schema keyword for %1$s can only contain the built-in types: %2$l.' ), $param, $allowed_types ),
			'5.5.0'
		);
	}

	$best_type = rest_get_best_type_for_value( $value, $args['type'] );

	if ( ! $best_type ) {
		if ( ! $invalid_types ) {
			return '';
		}

		// Backward compatibility for previous behavior which allowed the value if there was an invalid type used.
		$best_type = reset( $invalid_types );
	}

	return $best_type;
}

/**
 * Checks if an array is made up of unique items.
 *
 * @since 5.5.0
 *
 * @param array $array The array to check.
 * @return bool True if the array contains unique items, false otherwise.
 */
function rest_validate_array_contains_unique_items( $array ) {
	$seen = array();

	foreach ( $array as $item ) {
		$stabilized = rest_stabilize_value( $item );
		$key        = serialize( $stabilized );

		if ( ! isset( $seen[ $key ] ) ) {
			$seen[ $key ] = true;

			continue;
		}

		return false;
	}

	return true;
}

/**
 * Stabilizes a value following JSON Schema semantics.
 *
 * For lists, order is preserved. For objects, properties are reordered alphabetically.
 *
 * @since 5.5.0
 *
 * @param mixed $value The value to stabilize. Must already be sanitized. Objects should have been converted to arrays.
 * @return mixed The stabilized value.
 */
function rest_stabilize_value( $value ) {
	if ( is_scalar( $value ) || is_null( $value ) ) {
		return $value;
	}

	if ( is_object( $value ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Cannot stabilize objects. Convert the object to an array first.' ), '5.5.0' );

		return $value;
	}

	ksort( $value );

	foreach ( $value as $k => $v ) {
		$value[ $k ] = rest_stabilize_value( $v );
	}

	return $value;
}

/**
 * Validates if the JSON Schema pattern matches a value.
 *
 * @since 5.6.0
 *
 * @param string $pattern The pattern to match against.
 * @param string $value   The value to check.
 * @return bool           True if the pattern matches the given value, false otherwise.
 */
function rest_validate_json_schema_pattern( $pattern, $value ) {
	$escaped_pattern = str_replace( '#', '\\#', $pattern );

	return 1 === preg_match( '#' . $escaped_pattern . '#u', $value );
}

/**
 * Finds the schema for a property using the patternProperties keyword.
 *
 * @since 5.6.0
 *
 * @param string $property The property name to check.
 * @param array  $args     The schema array to use.
 * @return array|null      The schema of matching pattern property, or null if no patterns match.
 */
function rest_find_matching_pattern_property_schema( $property, $args ) {
	if ( isset( $args['patternProperties'] ) ) {
		foreach ( $args['patternProperties'] as $pattern => $child_schema ) {
			if ( rest_validate_json_schema_pattern( $pattern, $property ) ) {
				return $child_schema;
			}
		}
	}

	return null;
}

/**
 * Formats a combining operation error into a WP_Error object.
 *
 * @since 5.6.0
 *
 * @param string $param The parameter name.
 * @param array $error  The error details.
 * @return WP_Error
 */
function rest_format_combining_operation_error( $param, $error ) {
	$position = $error['index'];
	$reason   = $error['error_object']->get_error_message();

	if ( isset( $error['schema']['title'] ) ) {
		$title = $error['schema']['title'];

		return new WP_Error(
			'rest_invalid_param',
			/* translators: 1: Parameter, 2: Schema title, 3: Reason. */
			sprintf( __( '%1$s is not a valid %2$s. Reason: %3$s' ), $param, $title, $reason ),
			array( 'position' => $position )
		);
	}

	return new WP_Error(
		'rest_invalid_param',
		/* translators: 1: Parameter, 2: Reason. */
		sprintf( __( '%1$s does not match the expected format. Reason: %2$s' ), $param, $reason ),
		array( 'position' => $position )
	);
}

/**
 * Gets the error of combining operation.
 *
 * @since 5.6.0
 *
 * @param array  $value  The value to validate.
 * @param string $param  The parameter name, used in error messages.
 * @param array  $errors The errors array, to search for possible error.
 * @return WP_Error      The combining operation error.
 */
function rest_get_combining_operation_error( $value, $param, $errors ) {
	// If there is only one error, simply return it.
	if ( 1 === count( $errors ) ) {
		return rest_format_combining_operation_error( $param, $errors[0] );
	}

	// Filter out all errors related to type validation.
	$filtered_errors = array();
	foreach ( $errors as $error ) {
		$error_code = $error['error_object']->get_error_code();
		$error_data = $error['error_object']->get_error_data();

		if ( 'rest_invalid_type' !== $error_code || ( isset( $error_data['param'] ) && $param !== $error_data['param'] ) ) {
			$filtered_errors[] = $error;
		}
	}

	// If there is only one error left, simply return it.
	if ( 1 === count( $filtered_errors ) ) {
		return rest_format_combining_operation_error( $param, $filtered_errors[0] );
	}

	// If there are only errors related to object validation, try choosing the most appropriate one.
	if ( count( $filtered_errors ) > 1 && 'object' === $filtered_errors[0]['schema']['type'] ) {
		$result = null;
		$number = 0;

		foreach ( $filtered_errors as $error ) {
			if ( isset( $error['schema']['properties'] ) ) {
				$n = count( array_intersect_key( $error['schema']['properties'], $value ) );
				if ( $n > $number ) {
					$result = $error;
					$number = $n;
				}
			}
		}

		if ( null !== $result ) {
			return rest_format_combining_operation_error( $param, $result );
		}
	}

	// If each schema has a title, include those titles in the error message.
	$schema_titles = array();
	foreach ( $errors as $error ) {
		if ( isset( $error['schema']['title'] ) ) {
			$schema_titles[] = $error['schema']['title'];
		}
	}

	if ( count( $schema_titles ) === count( $errors ) ) {
		/* translators: 1: Parameter, 2: Schema titles. */
		return new WP_Error( 'rest_invalid_param', wp_sprintf( __( '%1$s is not a valid %2$l.' ), $param, $schema_titles ) );
	}

	/* translators: 1: Parameter. */
	return new WP_Error( 'rest_invalid_param', sprintf( __( '%1$s does not match any of the expected formats.' ), $param ) );
}

/**
 * Finds the matching schema among the "anyOf" schemas.
 *
 * @since 5.6.0
 *
 * @param mixed  $value   The value to validate.
 * @param array  $args    The schema array to use.
 * @param string $param   The parameter name, used in error messages.
 * @return array|WP_Error The matching schema or WP_Error instance if all schemas do not match.
 */
function rest_find_any_matching_schema( $value, $args, $param ) {
	$errors = array();

	foreach ( $args['anyOf'] as $index => $schema ) {
		if ( ! isset( $schema['type'] ) && isset( $args['type'] ) ) {
			$schema['type'] = $args['type'];
		}

		$is_valid = rest_validate_value_from_schema( $value, $schema, $param );
		if ( ! is_wp_error( $is_valid ) ) {
			return $schema;
		}

		$errors[] = array(
			'error_object' => $is_valid,
			'schema'       => $schema,
			'index'        => $index,
		);
	}

	return rest_get_combining_operation_error( $value, $param, $errors );
}

/**
 * Finds the matching schema among the "oneOf" schemas.
 *
 * @since 5.6.0
 *
 * @param mixed  $value                  The value to validate.
 * @param array  $args                   The schema array to use.
 * @param string $param                  The parameter name, used in error messages.
 * @param bool   $stop_after_first_match Optional. Whether the process should stop after the first successful match.
 * @return array|WP_Error                The matching schema or WP_Error instance if the number of matching schemas is not equal to one.
 */
function rest_find_one_matching_schema( $value, $args, $param, $stop_after_first_match = false ) {
	$matching_schemas = array();
	$errors           = array();

	foreach ( $args['oneOf'] as $index => $schema ) {
		if ( ! isset( $schema['type'] ) && isset( $args['type'] ) ) {
			$schema['type'] = $args['type'];
		}

		$is_valid = rest_validate_value_from_schema( $value, $schema, $param );
		if ( ! is_wp_error( $is_valid ) ) {
			if ( $stop_after_first_match ) {
				return $schema;
			}

			$matching_schemas[] = array(
				'schema_object' => $schema,
				'index'         => $index,
			);
		} else {
			$errors[] = array(
				'error_object' => $is_valid,
				'schema'       => $schema,
				'index'        => $index,
			);
		}
	}

	if ( ! $matching_schemas ) {
		return rest_get_combining_operation_error( $value, $param, $errors );
	}

	if ( count( $matching_schemas ) > 1 ) {
		$schema_positions = array();
		$schema_titles    = array();

		foreach ( $matching_schemas as $schema ) {
			$schema_positions[] = $schema['index'];

			if ( isset( $schema['schema_object']['title'] ) ) {
				$schema_titles[] = $schema['schema_object']['title'];
			}
		}

		// If each schema has a title, include those titles in the error message.
		if ( count( $schema_titles ) === count( $matching_schemas ) ) {
			return new WP_Error(
				'rest_invalid_param',
				/* translators: 1: Parameter, 2: Schema titles. */
				wp_sprintf( __( '%1$s matches %2$l, but should match only one.' ), $param, $schema_titles ),
				array( 'positions' => $schema_positions )
			);
		}

		return new WP_Error(
			'rest_invalid_param',
			/* translators: 1: Parameter. */
			sprintf( __( '%1$s matches more than one of the expected formats.' ), $param ),
			array( 'positions' => $schema_positions )
		);
	}

	return $matching_schemas[0]['schema_object'];
}

/**
 * Get all valid JSON schema properties.
 *
 * @since 5.6.0
 *
 * @return string[] All valid JSON schema properties.
 */
function rest_get_allowed_schema_keywords() {
	return array(
		'title',
		'description',
		'default',
		'type',
		'format',
		'enum',
		'items',
		'properties',
		'additionalProperties',
		'patternProperties',
		'minProperties',
		'maxProperties',
		'minimum',
		'maximum',
		'exclusiveMinimum',
		'exclusiveMaximum',
		'multipleOf',
		'minLength',
		'maxLength',
		'pattern',
		'minItems',
		'maxItems',
		'uniqueItems',
		'anyOf',
		'oneOf',
	);
}

/**
 * Validate a value based on a schema.
 *
 * @since 4.7.0
 * @since 4.9.0 Support the "object" type.
 * @since 5.2.0 Support validating "additionalProperties" against a schema.
 * @since 5.3.0 Support multiple types.
 * @since 5.4.0 Convert an empty string to an empty object.
 * @since 5.5.0 Add the "uuid" and "hex-color" formats.
 *              Support the "minLength", "maxLength" and "pattern" keywords for strings.
 *              Support the "minItems", "maxItems" and "uniqueItems" keywords for arrays.
 *              Validate required properties.
 * @since 5.6.0 Support the "minProperties" and "maxProperties" keywords for objects.
 *              Support the "multipleOf" keyword for numbers and integers.
 *              Support the "patternProperties" keyword for objects.
 *              Support the "anyOf" and "oneOf" keywords.
 *
 * @param mixed  $value The value to validate.
 * @param array  $args  Schema array to use for validation.
 * @param string $param The parameter name, used in error messages.
 * @return true|WP_Error
 */
function rest_validate_value_from_schema( $value, $args, $param = '' ) {
	if ( isset( $args['anyOf'] ) ) {
		$matching_schema = rest_find_any_matching_schema( $value, $args, $param );
		if ( is_wp_error( $matching_schema ) ) {
			return $matching_schema;
		}

		if ( ! isset( $args['type'] ) && isset( $matching_schema['type'] ) ) {
			$args['type'] = $matching_schema['type'];
		}
	}

	if ( isset( $args['oneOf'] ) ) {
		$matching_schema = rest_find_one_matching_schema( $value, $args, $param );
		if ( is_wp_error( $matching_schema ) ) {
			return $matching_schema;
		}

		if ( ! isset( $args['type'] ) && isset( $matching_schema['type'] ) ) {
			$args['type'] = $matching_schema['type'];
		}
	}

	$allowed_types = array( 'array', 'object', 'string', 'number', 'integer', 'boolean', 'null' );

	if ( ! isset( $args['type'] ) ) {
		/* translators: %s: Parameter. */
		_doing_it_wrong( __FUNCTION__, sprintf( __( 'The "type" schema keyword for %s is required.' ), $param ), '5.5.0' );
	}

	if ( is_array( $args['type'] ) ) {
		$best_type = rest_handle_multi_type_schema( $value, $args, $param );

		if ( ! $best_type ) {
			return new WP_Error(
				'rest_invalid_type',
				/* translators: 1: Parameter, 2: List of types. */
				sprintf( __( '%1$s is not of type %2$s.' ), $param, implode( ',', $args['type'] ) ),
				array( 'param' => $param )
			);
		}

		$args['type'] = $best_type;
	}

	if ( ! in_array( $args['type'], $allowed_types, true ) ) {
		_doing_it_wrong(
			__FUNCTION__,
			/* translators: 1: Parameter, 2: The list of allowed types. */
			wp_sprintf( __( 'The "type" schema keyword for %1$s can only be one of the built-in types: %2$l.' ), $param, $allowed_types ),
			'5.5.0'
		);
	}

	if ( 'array' === $args['type'] ) {
		if ( ! rest_is_array( $value ) ) {
			return new WP_Error(
				'rest_invalid_type',
				/* translators: 1: Parameter, 2: Type name. */
				sprintf( __( '%1$s is not of type %2$s.' ), $param, 'array' ),
				array( 'param' => $param )
			);
		}

		$value = rest_sanitize_array( $value );

		if ( isset( $args['items'] ) ) {
			foreach ( $value as $index => $v ) {
				$is_valid = rest_validate_value_from_schema( $v, $args['items'], $param . '[' . $index . ']' );
				if ( is_wp_error( $is_valid ) ) {
					return $is_valid;
				}
			}
		}

		if ( isset( $args['minItems'] ) && count( $value ) < $args['minItems'] ) {
			/* translators: 1: Parameter, 2: Number. */
			return new WP_Error( 'rest_invalid_param', sprintf( __( '%1$s must contain at least %2$s items.' ), $param, number_format_i18n( $args['minItems'] ) ) );
		}

		if ( isset( $args['maxItems'] ) && count( $value ) > $args['maxItems'] ) {
			/* translators: 1: Parameter, 2: Number. */
			return new WP_Error( 'rest_invalid_param', sprintf( __( '%1$s must contain at most %2$s items.' ), $param, number_format_i18n( $args['maxItems'] ) ) );
		}

		if ( ! empty( $args['uniqueItems'] ) && ! rest_validate_array_contains_unique_items( $value ) ) {
			/* translators: 1: Parameter. */
			return new WP_Error( 'rest_invalid_param', sprintf( __( '%1$s has duplicate items.' ), $param ) );
		}
	}

	if ( 'object' === $args['type'] ) {
		if ( ! rest_is_object( $value ) ) {
			return new WP_Error(
				'rest_invalid_type',
				/* translators: 1: Parameter, 2: Type name. */
				sprintf( __( '%1$s is not of type %2$s.' ), $param, 'object' ),
				array( 'param' => $param )
			);
		}

		$value = rest_sanitize_object( $value );

		if ( isset( $args['required'] ) && is_array( $args['required'] ) ) { // schema version 4
			foreach ( $args['required'] as $name ) {
				if ( ! array_key_exists( $name, $value ) ) {
					/* translators: 1: Property of an object, 2: Parameter. */
					return new WP_Error( 'rest_property_required', sprintf( __( '%1$s is a required property of %2$s.' ), $name, $param ) );
				}
			}
		} elseif ( isset( $args['properties'] ) ) { // schema version 3
			foreach ( $args['properties'] as $name => $property ) {
				if ( isset( $property['required'] ) && true === $property['required'] && ! array_key_exists( $name, $value ) ) {
					/* translators: 1: Property of an object, 2: Parameter. */
					return new WP_Error( 'rest_property_required', sprintf( __( '%1$s is a required property of %2$s.' ), $name, $param ) );
				}
			}
		}

		foreach ( $value as $property => $v ) {
			if ( isset( $args['properties'][ $property ] ) ) {
				$is_valid = rest_validate_value_from_schema( $v, $args['properties'][ $property ], $param . '[' . $property . ']' );
				if ( is_wp_error( $is_valid ) ) {
					return $is_valid;
				}
				continue;
			}

			$pattern_property_schema = rest_find_matching_pattern_property_schema( $property, $args );
			if ( null !== $pattern_property_schema ) {
				$is_valid = rest_validate_value_from_schema( $v, $pattern_property_schema, $param . '[' . $property . ']' );
				if ( is_wp_error( $is_valid ) ) {
					return $is_valid;
				}
				continue;
			}

			if ( isset( $args['additionalProperties'] ) ) {
				if ( false === $args['additionalProperties'] ) {
					/* translators: %s: Property of an object. */
					return new WP_Error( 'rest_invalid_param', sprintf( __( '%1$s is not a valid property of Object.' ), $property ) );
				}

				if ( is_array( $args['additionalProperties'] ) ) {
					$is_valid = rest_validate_value_from_schema( $v, $args['additionalProperties'], $param . '[' . $property . ']' );
					if ( is_wp_error( $is_valid ) ) {
						return $is_valid;
					}
				}
			}
		}

		if ( isset( $args['minProperties'] ) && count( $value ) < $args['minProperties'] ) {
			/* translators: 1: Parameter, 2: Number. */
			return new WP_Error( 'rest_invalid_param', sprintf( __( '%1$s must contain at least %2$s properties.' ), $param, number_format_i18n( $args['minProperties'] ) ) );
		}

		if ( isset( $args['maxProperties'] ) && count( $value ) > $args['maxProperties'] ) {
			/* translators: 1: Parameter, 2: Number. */
			return new WP_Error( 'rest_invalid_param', sprintf( __( '%1$s must contain at most %2$s properties.' ), $param, number_format_i18n( $args['maxProperties'] ) ) );
		}
	}

	if ( 'null' === $args['type'] ) {
		if ( null !== $value ) {
			return new WP_Error(
				'rest_invalid_type',
				/* translators: 1: Parameter, 2: Type name. */
				sprintf( __( '%1$s is not of type %2$s.' ), $param, 'null' ),
				array( 'param' => $param )
			);
		}

		return true;
	}

	if ( ! empty( $args['enum'] ) ) {
		if ( ! in_array( $value, $args['enum'], true ) ) {
			/* translators: 1: Parameter, 2: List of valid values. */
			return new WP_Error( 'rest_invalid_param', sprintf( __( '%1$s is not one of %2$s.' ), $param, implode( ', ', $args['enum'] ) ) );
		}
	}

	if ( in_array( $args['type'], array( 'integer', 'number' ), true ) ) {
		if ( ! is_numeric( $value ) ) {
			return new WP_Error(
				'rest_invalid_type',
				/* translators: 1: Parameter, 2: Type name. */
				sprintf( __( '%1$s is not of type %2$s.' ), $param, $args['type'] ),
				array( 'param' => $param )
			);
		}

		if ( isset( $args['multipleOf'] ) && fmod( $value, $args['multipleOf'] ) !== 0.0 ) {
			/* translators: 1: Parameter, 2: Multiplier. */
			return new WP_Error( 'rest_invalid_param', sprintf( __( '%1$s must be a multiple of %2$s.' ), $param, $args['multipleOf'] ) );
		}
	}

	if ( 'integer' === $args['type'] && ! rest_is_integer( $value ) ) {
		return new WP_Error(
			'rest_invalid_type',
			/* translators: 1: Parameter, 2: Type name. */
			sprintf( __( '%1$s is not of type %2$s.' ), $param, 'integer' ),
			array( 'param' => $param )
		);
	}

	if ( 'boolean' === $args['type'] && ! rest_is_boolean( $value ) ) {
		return new WP_Error(
			'rest_invalid_type',
			/* translators: 1: Parameter, 2: Type name. */
			sprintf( __( '%1$s is not of type %2$s.' ), $param, 'boolean' ),
			array( 'param' => $param )
		);
	}

	if ( 'string' === $args['type'] ) {
		if ( ! is_string( $value ) ) {
			return new WP_Error(
				'rest_invalid_type',
				/* translators: 1: Parameter, 2: Type name. */
				sprintf( __( '%1$s is not of type %2$s.' ), $param, 'string' ),
				array( 'param' => $param )
			);
		}

		if ( isset( $args['minLength'] ) && mb_strlen( $value ) < $args['minLength'] ) {
			return new WP_Error(
				'rest_invalid_param',
				sprintf(
					/* translators: 1: Parameter, 2: Number of characters. */
					_n( '%1$s must be at least %2$s character long.', '%1$s must be at least %2$s characters long.', $args['minLength'] ),
					$param,
					number_format_i18n( $args['minLength'] )
				)
			);
		}

		if ( isset( $args['maxLength'] ) && mb_strlen( $value ) > $args['maxLength'] ) {
			return new WP_Error(
				'rest_invalid_param',
				sprintf(
					/* translators: 1: Parameter, 2: Number of characters. */
					_n( '%1$s must be at most %2$s character long.', '%1$s must be at most %2$s characters long.', $args['maxLength'] ),
					$param,
					number_format_i18n( $args['maxLength'] )
				)
			);
		}

		if ( isset( $args['pattern'] ) && ! rest_validate_json_schema_pattern( $args['pattern'], $value ) ) {
			/* translators: 1: Parameter, 2: Pattern. */
			return new WP_Error( 'rest_invalid_pattern', sprintf( __( '%1$s does not match pattern %2$s.' ), $param, $args['pattern'] ) );
		}
	}

	// The "format" keyword should only be applied to strings. However, for backward compatibility,
	// we allow the "format" keyword if the type keyword was not specified, or was set to an invalid value.
	if ( isset( $args['format'] )
		&& ( ! isset( $args['type'] ) || 'string' === $args['type'] || ! in_array( $args['type'], $allowed_types, true ) )
	) {
		switch ( $args['format'] ) {
			case 'hex-color':
				if ( ! rest_parse_hex_color( $value ) ) {
					return new WP_Error( 'rest_invalid_hex_color', __( 'Invalid hex color.' ) );
				}
				break;

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
					/* translators: %s: IP address. */
					return new WP_Error( 'rest_invalid_param', sprintf( __( '%s is not a valid IP address.' ), $param ) );
				}
				break;
			case 'uuid':
				if ( ! wp_is_uuid( $value ) ) {
					/* translators: %s: The name of a JSON field expecting a valid UUID. */
					return new WP_Error( 'rest_invalid_uuid', sprintf( __( '%s is not a valid UUID.' ), $param ) );
				}
				break;
		}
	}

	if ( in_array( $args['type'], array( 'number', 'integer' ), true ) && ( isset( $args['minimum'] ) || isset( $args['maximum'] ) ) ) {
		if ( isset( $args['minimum'] ) && ! isset( $args['maximum'] ) ) {
			if ( ! empty( $args['exclusiveMinimum'] ) && $value <= $args['minimum'] ) {
				/* translators: 1: Parameter, 2: Minimum number. */
				return new WP_Error( 'rest_invalid_param', sprintf( __( '%1$s must be greater than %2$d' ), $param, $args['minimum'] ) );
			} elseif ( empty( $args['exclusiveMinimum'] ) && $value < $args['minimum'] ) {
				/* translators: 1: Parameter, 2: Minimum number. */
				return new WP_Error( 'rest_invalid_param', sprintf( __( '%1$s must be greater than or equal to %2$d' ), $param, $args['minimum'] ) );
			}
		} elseif ( isset( $args['maximum'] ) && ! isset( $args['minimum'] ) ) {
			if ( ! empty( $args['exclusiveMaximum'] ) && $value >= $args['maximum'] ) {
				/* translators: 1: Parameter, 2: Maximum number. */
				return new WP_Error( 'rest_invalid_param', sprintf( __( '%1$s must be less than %2$d' ), $param, $args['maximum'] ) );
			} elseif ( empty( $args['exclusiveMaximum'] ) && $value > $args['maximum'] ) {
				/* translators: 1: Parameter, 2: Maximum number. */
				return new WP_Error( 'rest_invalid_param', sprintf( __( '%1$s must be less than or equal to %2$d' ), $param, $args['maximum'] ) );
			}
		} elseif ( isset( $args['maximum'] ) && isset( $args['minimum'] ) ) {
			if ( ! empty( $args['exclusiveMinimum'] ) && ! empty( $args['exclusiveMaximum'] ) ) {
				if ( $value >= $args['maximum'] || $value <= $args['minimum'] ) {
					/* translators: 1: Parameter, 2: Minimum number, 3: Maximum number. */
					return new WP_Error( 'rest_invalid_param', sprintf( __( '%1$s must be between %2$d (exclusive) and %3$d (exclusive)' ), $param, $args['minimum'], $args['maximum'] ) );
				}
			} elseif ( empty( $args['exclusiveMinimum'] ) && ! empty( $args['exclusiveMaximum'] ) ) {
				if ( $value >= $args['maximum'] || $value < $args['minimum'] ) {
					/* translators: 1: Parameter, 2: Minimum number, 3: Maximum number. */
					return new WP_Error( 'rest_invalid_param', sprintf( __( '%1$s must be between %2$d (inclusive) and %3$d (exclusive)' ), $param, $args['minimum'], $args['maximum'] ) );
				}
			} elseif ( ! empty( $args['exclusiveMinimum'] ) && empty( $args['exclusiveMaximum'] ) ) {
				if ( $value > $args['maximum'] || $value <= $args['minimum'] ) {
					/* translators: 1: Parameter, 2: Minimum number, 3: Maximum number. */
					return new WP_Error( 'rest_invalid_param', sprintf( __( '%1$s must be between %2$d (exclusive) and %3$d (inclusive)' ), $param, $args['minimum'], $args['maximum'] ) );
				}
			} elseif ( empty( $args['exclusiveMinimum'] ) && empty( $args['exclusiveMaximum'] ) ) {
				if ( $value > $args['maximum'] || $value < $args['minimum'] ) {
					/* translators: 1: Parameter, 2: Minimum number, 3: Maximum number. */
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
 * @since 5.5.0 Added the `$param` parameter.
 * @since 5.6.0 Support the "anyOf" and "oneOf" keywords.
 *
 * @param mixed  $value The value to sanitize.
 * @param array  $args  Schema array to use for sanitization.
 * @param string $param The parameter name, used in error messages.
 * @return mixed|WP_Error The sanitized value or a WP_Error instance if the value cannot be safely sanitized.
 */
function rest_sanitize_value_from_schema( $value, $args, $param = '' ) {
	if ( isset( $args['anyOf'] ) ) {
		$matching_schema = rest_find_any_matching_schema( $value, $args, $param );
		if ( is_wp_error( $matching_schema ) ) {
			return $matching_schema;
		}

		if ( ! isset( $args['type'] ) ) {
			$args['type'] = $matching_schema['type'];
		}

		$value = rest_sanitize_value_from_schema( $value, $matching_schema, $param );
	}

	if ( isset( $args['oneOf'] ) ) {
		$matching_schema = rest_find_one_matching_schema( $value, $args, $param );
		if ( is_wp_error( $matching_schema ) ) {
			return $matching_schema;
		}

		if ( ! isset( $args['type'] ) ) {
			$args['type'] = $matching_schema['type'];
		}

		$value = rest_sanitize_value_from_schema( $value, $matching_schema, $param );
	}

	$allowed_types = array( 'array', 'object', 'string', 'number', 'integer', 'boolean', 'null' );

	if ( ! isset( $args['type'] ) ) {
		/* translators: %s: Parameter. */
		_doing_it_wrong( __FUNCTION__, sprintf( __( 'The "type" schema keyword for %s is required.' ), $param ), '5.5.0' );
	}

	if ( is_array( $args['type'] ) ) {
		$best_type = rest_handle_multi_type_schema( $value, $args, $param );

		if ( ! $best_type ) {
			return null;
		}

		$args['type'] = $best_type;
	}

	if ( ! in_array( $args['type'], $allowed_types, true ) ) {
		_doing_it_wrong(
			__FUNCTION__,
			/* translators: 1: Parameter, 2: The list of allowed types. */
			wp_sprintf( __( 'The "type" schema keyword for %1$s can only be one of the built-in types: %2$l.' ), $param, $allowed_types ),
			'5.5.0'
		);
	}

	if ( 'array' === $args['type'] ) {
		$value = rest_sanitize_array( $value );

		if ( ! empty( $args['items'] ) ) {
			foreach ( $value as $index => $v ) {
				$value[ $index ] = rest_sanitize_value_from_schema( $v, $args['items'], $param . '[' . $index . ']' );
			}
		}

		if ( ! empty( $args['uniqueItems'] ) && ! rest_validate_array_contains_unique_items( $value ) ) {
			/* translators: 1: Parameter. */
			return new WP_Error( 'rest_invalid_param', sprintf( __( '%1$s has duplicate items.' ), $param ) );
		}

		return $value;
	}

	if ( 'object' === $args['type'] ) {
		$value = rest_sanitize_object( $value );

		foreach ( $value as $property => $v ) {
			if ( isset( $args['properties'][ $property ] ) ) {
				$value[ $property ] = rest_sanitize_value_from_schema( $v, $args['properties'][ $property ], $param . '[' . $property . ']' );
				continue;
			}

			$pattern_property_schema = rest_find_matching_pattern_property_schema( $property, $args );
			if ( null !== $pattern_property_schema ) {
				$value[ $property ] = rest_sanitize_value_from_schema( $v, $pattern_property_schema, $param . '[' . $property . ']' );
				continue;
			}

			if ( isset( $args['additionalProperties'] ) ) {
				if ( false === $args['additionalProperties'] ) {
					unset( $value[ $property ] );
				} elseif ( is_array( $args['additionalProperties'] ) ) {
					$value[ $property ] = rest_sanitize_value_from_schema( $v, $args['additionalProperties'], $param . '[' . $property . ']' );
				}
			}
		}

		return $value;
	}

	if ( 'null' === $args['type'] ) {
		return null;
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

	// This behavior matches rest_validate_value_from_schema().
	if ( isset( $args['format'] )
		&& ( ! isset( $args['type'] ) || 'string' === $args['type'] || ! in_array( $args['type'], $allowed_types, true ) )
	) {
		switch ( $args['format'] ) {
			case 'hex-color':
				return (string) sanitize_hex_color( $value );

			case 'date-time':
				return sanitize_text_field( $value );

			case 'email':
				// sanitize_email() validates, which would be unexpected.
				return sanitize_text_field( $value );

			case 'uri':
				return esc_url_raw( $value );

			case 'ip':
				return sanitize_text_field( $value );

			case 'uuid':
				return sanitize_text_field( $value );
		}
	}

	if ( 'string' === $args['type'] ) {
		return (string) $value;
	}

	return $value;
}

/**
 * Append result of internal request to REST API for purpose of preloading data to be attached to a page.
 * Expected to be called in the context of `array_reduce`.
 *
 * @since 5.0.0
 *
 * @param array  $memo Reduce accumulator.
 * @param string $path REST API path to preload.
 * @return array Modified reduce accumulator.
 */
function rest_preload_api_request( $memo, $path ) {
	// array_reduce() doesn't support passing an array in PHP 5.2,
	// so we need to make sure we start with one.
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
		$links  = $server::get_compact_response_links( $response );
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

/**
 * Parses the "_embed" parameter into the list of resources to embed.
 *
 * @since 5.4.0
 *
 * @param string|array $embed Raw "_embed" parameter value.
 * @return true|string[] Either true to embed all embeds, or a list of relations to embed.
 */
function rest_parse_embed_param( $embed ) {
	if ( ! $embed || 'true' === $embed || '1' === $embed ) {
		return true;
	}

	$rels = wp_parse_list( $embed );

	if ( ! $rels ) {
		return true;
	}

	return $rels;
}

/**
 * Filters the response to remove any fields not available in the given context.
 *
 * @since 5.5.0
 * @since 5.6.0 Support the "patternProperties" keyword for objects.
 *              Support the "anyOf" and "oneOf" keywords.
 *
 * @param array|object $data    The response data to modify.
 * @param array        $schema  The schema for the endpoint used to filter the response.
 * @param string       $context The requested context.
 * @return array|object The filtered response data.
 */
function rest_filter_response_by_context( $data, $schema, $context ) {
	if ( isset( $schema['anyOf'] ) ) {
		$matching_schema = rest_find_any_matching_schema( $data, $schema, '' );
		if ( ! is_wp_error( $matching_schema ) ) {
			if ( ! isset( $schema['type'] ) ) {
				$schema['type'] = $matching_schema['type'];
			}

			$data = rest_filter_response_by_context( $data, $matching_schema, $context );
		}
	}

	if ( isset( $schema['oneOf'] ) ) {
		$matching_schema = rest_find_one_matching_schema( $data, $schema, '', true );
		if ( ! is_wp_error( $matching_schema ) ) {
			if ( ! isset( $schema['type'] ) ) {
				$schema['type'] = $matching_schema['type'];
			}

			$data = rest_filter_response_by_context( $data, $matching_schema, $context );
		}
	}

	if ( ! is_array( $data ) && ! is_object( $data ) ) {
		return $data;
	}

	if ( isset( $schema['type'] ) ) {
		$type = $schema['type'];
	} elseif ( isset( $schema['properties'] ) ) {
		$type = 'object'; // Back compat if a developer accidentally omitted the type.
	} else {
		return $data;
	}

	$is_array_type  = 'array' === $type || ( is_array( $type ) && in_array( 'array', $type, true ) );
	$is_object_type = 'object' === $type || ( is_array( $type ) && in_array( 'object', $type, true ) );

	if ( $is_array_type && $is_object_type ) {
		if ( rest_is_array( $data ) ) {
			$is_object_type = false;
		} else {
			$is_array_type = false;
		}
	}

	$has_additional_properties = $is_object_type && isset( $schema['additionalProperties'] ) && is_array( $schema['additionalProperties'] );

	foreach ( $data as $key => $value ) {
		$check = array();

		if ( $is_array_type ) {
			$check = isset( $schema['items'] ) ? $schema['items'] : array();
		} elseif ( $is_object_type ) {
			if ( isset( $schema['properties'][ $key ] ) ) {
				$check = $schema['properties'][ $key ];
			} else {
				$pattern_property_schema = rest_find_matching_pattern_property_schema( $key, $schema );
				if ( null !== $pattern_property_schema ) {
					$check = $pattern_property_schema;
				} elseif ( $has_additional_properties ) {
					$check = $schema['additionalProperties'];
				}
			}
		}

		if ( ! isset( $check['context'] ) ) {
			continue;
		}

		if ( ! in_array( $context, $check['context'], true ) ) {
			if ( $is_array_type ) {
				// All array items share schema, so there's no need to check each one.
				$data = array();
				break;
			}

			if ( is_object( $data ) ) {
				unset( $data->$key );
			} else {
				unset( $data[ $key ] );
			}
		} elseif ( is_array( $value ) || is_object( $value ) ) {
			$new_value = rest_filter_response_by_context( $value, $check, $context );

			if ( is_object( $data ) ) {
				$data->$key = $new_value;
			} else {
				$data[ $key ] = $new_value;
			}
		}
	}

	return $data;
}

/**
 * Sets the "additionalProperties" to false by default for all object definitions in the schema.
 *
 * @since 5.5.0
 * @since 5.6.0 Support the "patternProperties" keyword.
 *
 * @param array $schema The schema to modify.
 * @return array The modified schema.
 */
function rest_default_additional_properties_to_false( $schema ) {
	$type = (array) $schema['type'];

	if ( in_array( 'object', $type, true ) ) {
		if ( isset( $schema['properties'] ) ) {
			foreach ( $schema['properties'] as $key => $child_schema ) {
				$schema['properties'][ $key ] = rest_default_additional_properties_to_false( $child_schema );
			}
		}

		if ( isset( $schema['patternProperties'] ) ) {
			foreach ( $schema['patternProperties'] as $key => $child_schema ) {
				$schema['patternProperties'][ $key ] = rest_default_additional_properties_to_false( $child_schema );
			}
		}

		if ( ! isset( $schema['additionalProperties'] ) ) {
			$schema['additionalProperties'] = false;
		}
	}

	if ( in_array( 'array', $type, true ) ) {
		if ( isset( $schema['items'] ) ) {
			$schema['items'] = rest_default_additional_properties_to_false( $schema['items'] );
		}
	}

	return $schema;
}

/**
 * Gets the REST API route for a post.
 *
 * @since 5.5.0
 *
 * @param int|WP_Post $post Post ID or post object.
 * @return string The route path with a leading slash for the given post, or an empty string if there is not a route.
 */
function rest_get_route_for_post( $post ) {
	$post = get_post( $post );

	if ( ! $post instanceof WP_Post ) {
		return '';
	}

	$post_type = get_post_type_object( $post->post_type );
	if ( ! $post_type ) {
		return '';
	}

	$controller = $post_type->get_rest_controller();
	if ( ! $controller ) {
		return '';
	}

	$route = '';

	// The only two controllers that we can detect are the Attachments and Posts controllers.
	if ( in_array( get_class( $controller ), array( 'WP_REST_Attachments_Controller', 'WP_REST_Posts_Controller' ), true ) ) {
		$namespace = 'wp/v2';
		$rest_base = ! empty( $post_type->rest_base ) ? $post_type->rest_base : $post_type->name;
		$route     = sprintf( '/%s/%s/%d', $namespace, $rest_base, $post->ID );
	}

	/**
	 * Filters the REST API route for a post.
	 *
	 * @since 5.5.0
	 *
	 * @param string  $route The route path.
	 * @param WP_Post $post  The post object.
	 */
	return apply_filters( 'rest_route_for_post', $route, $post );
}

/**
 * Gets the REST API route for a term.
 *
 * @since 5.5.0
 *
 * @param int|WP_Term $term Term ID or term object.
 * @return string The route path with a leading slash for the given term, or an empty string if there is not a route.
 */
function rest_get_route_for_term( $term ) {
	$term = get_term( $term );

	if ( ! $term instanceof WP_Term ) {
		return '';
	}

	$taxonomy = get_taxonomy( $term->taxonomy );
	if ( ! $taxonomy ) {
		return '';
	}

	$controller = $taxonomy->get_rest_controller();
	if ( ! $controller ) {
		return '';
	}

	$route = '';

	// The only controller that works is the Terms controller.
	if ( $controller instanceof WP_REST_Terms_Controller ) {
		$namespace = 'wp/v2';
		$rest_base = ! empty( $taxonomy->rest_base ) ? $taxonomy->rest_base : $taxonomy->name;
		$route     = sprintf( '/%s/%s/%d', $namespace, $rest_base, $term->term_id );
	}

	/**
	 * Filters the REST API route for a term.
	 *
	 * @since 5.5.0
	 *
	 * @param string  $route The route path.
	 * @param WP_Term $term  The term object.
	 */
	return apply_filters( 'rest_route_for_term', $route, $term );
}

/**
 * Gets the REST route for the currently queried object.
 *
 * @since 5.5.0
 *
 * @return string The REST route of the resource, or an empty string if no resource identified.
 */
function rest_get_queried_resource_route() {
	if ( is_singular() ) {
		$route = rest_get_route_for_post( get_queried_object() );
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$route = rest_get_route_for_term( get_queried_object() );
	} elseif ( is_author() ) {
		$route = '/wp/v2/users/' . get_queried_object_id();
	} else {
		$route = '';
	}

	/**
	 * Filters the REST route for the currently queried object.
	 *
	 * @since 5.5.0
	 *
	 * @param string $link The route with a leading slash, or an empty string.
	 */
	return apply_filters( 'rest_queried_resource_route', $route );
}

/**
 * Retrieves an array of endpoint arguments from the item schema and endpoint method.
 *
 * @since 5.6.0
 *
 * @param array  $schema The full JSON schema for the endpoint.
 * @param string $method Optional. HTTP method of the endpoint. The arguments for `CREATABLE` endpoints are
 *                       checked for required values and may fall-back to a given default, this is not done
 *                       on `EDITABLE` endpoints. Default WP_REST_Server::CREATABLE.
 * @return array The endpoint arguments.
 */
function rest_get_endpoint_args_for_schema( $schema, $method = WP_REST_Server::CREATABLE ) {

	$schema_properties       = ! empty( $schema['properties'] ) ? $schema['properties'] : array();
	$endpoint_args           = array();
	$valid_schema_properties = rest_get_allowed_schema_keywords();
	$valid_schema_properties = array_diff( $valid_schema_properties, array( 'default', 'required' ) );

	foreach ( $schema_properties as $field_id => $params ) {

		// Arguments specified as `readonly` are not allowed to be set.
		if ( ! empty( $params['readonly'] ) ) {
			continue;
		}

		$endpoint_args[ $field_id ] = array(
			'validate_callback' => 'rest_validate_request_arg',
			'sanitize_callback' => 'rest_sanitize_request_arg',
		);

		if ( WP_REST_Server::CREATABLE === $method && isset( $params['default'] ) ) {
			$endpoint_args[ $field_id ]['default'] = $params['default'];
		}

		if ( WP_REST_Server::CREATABLE === $method && ! empty( $params['required'] ) ) {
			$endpoint_args[ $field_id ]['required'] = true;
		}

		foreach ( $valid_schema_properties as $schema_prop ) {
			if ( isset( $params[ $schema_prop ] ) ) {
				$endpoint_args[ $field_id ][ $schema_prop ] = $params[ $schema_prop ];
			}
		}

		// Merge in any options provided by the schema property.
		if ( isset( $params['arg_options'] ) ) {

			// Only use required / default from arg_options on CREATABLE endpoints.
			if ( WP_REST_Server::CREATABLE !== $method ) {
				$params['arg_options'] = array_diff_key(
					$params['arg_options'],
					array(
						'required' => '',
						'default'  => '',
					)
				);
			}

			$endpoint_args[ $field_id ] = array_merge( $endpoint_args[ $field_id ], $params['arg_options'] );
		}
	}

	return $endpoint_args;
}

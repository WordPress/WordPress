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
 * @since 4.4.0
 *
 * @global WP_REST_Server $wp_rest_server ResponseHandler instance (usually WP_REST_Server).
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
	/** @var WP_REST_Server $wp_rest_server */
	global $wp_rest_server;

	if ( empty( $namespace ) ) {
		/*
		 * Non-namespaced routes are not allowed, with the exception of the main
		 * and namespace indexes. If you really need to register a
		 * non-namespaced route, call `WP_REST_Server::register_route` directly.
		 */
		_doing_it_wrong( 'register_rest_route', __( 'Routes must be namespaced with plugin or theme name and version.' ), '4.4.0' );
		return false;
	} else if ( empty( $route ) ) {
		_doing_it_wrong( 'register_rest_route', __( 'Route must be specified.' ), '4.4.0' );
		return false;
	}

	if ( isset( $args['callback'] ) ) {
		// Upgrade a single set to multiple.
		$args = array( $args );
	}

	$defaults = array(
		'methods'         => 'GET',
		'callback'        => null,
		'args'            => array(),
	);
	foreach ( $args as $key => &$arg_group ) {
		if ( ! is_numeric( $arg_group ) ) {
			// Route option, skip here.
			continue;
		}

		$arg_group = array_merge( $defaults, $arg_group );
	}

	$full_route = '/' . trim( $namespace, '/' ) . '/' . trim( $route, '/' );
	$wp_rest_server->register_route( $namespace, $full_route, $args, $override );
	return true;
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
 */
function rest_api_register_rewrites() {
	add_rewrite_rule( '^' . rest_get_url_prefix() . '/?$','index.php?rest_route=/','top' );
	add_rewrite_rule( '^' . rest_get_url_prefix() . '/(.*)?','index.php?rest_route=/$matches[1]','top' );
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

	add_filter( 'rest_pre_dispatch', 'rest_handle_options_request', 10, 3 );
}

/**
 * Loads the REST API.
 *
 * @since 4.4.0
 *
 * @global WP             $wp             Current WordPress environment instance.
 * @global WP_REST_Server $wp_rest_server ResponseHandler instance (usually WP_REST_Server).
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
	$server->serve_request( $GLOBALS['wp']->query_vars['rest_route'] );

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
	 * Filter the REST URL prefix.
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

	if ( is_multisite() && get_blog_option( $blog_id, 'permalink_structure' ) || get_option( 'permalink_structure' ) ) {
		$url = get_home_url( $blog_id, rest_get_url_prefix(), $scheme );
		$url .= '/' . ltrim( $path, '/' );
	} else {
		$url = trailingslashit( get_home_url( $blog_id, '', $scheme ) );

		$path = '/' . ltrim( $path, '/' );

		$url = add_query_arg( 'rest_route', $path, $url );
	}

	if ( is_ssl() ) {
		// If the current host is the same as the REST URL host, force the REST URL scheme to HTTPS.
		if ( $_SERVER['SERVER_NAME'] === parse_url( get_home_url( $blog_id ), PHP_URL_HOST ) ) {
			$url = set_url_scheme( $url, 'https' );
		}
	}

	/**
	 * Filter the REST URL.
	 *
	 * Use this filter to adjust the url returned by the `get_rest_url` function.
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
 * @global WP_REST_Server $wp_rest_server ResponseHandler instance (usually WP_REST_Server).
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
		 * Filter the REST Server Class.
		 *
		 * This filter allows you to adjust the server class used by the API, using a
		 * different class to handle requests.
		 *
		 * @since 4.4.0
		 *
		 * @param string $class_name The name of the server class. Default 'WP_REST_Server'.
		 */
		$wp_rest_server_class = apply_filters( 'wp_rest_server_class', 'WP_REST_Server' );
		$wp_rest_server = new $wp_rest_server_class;

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
 * @return mixed WP_Error if response generated an error, WP_HTTP_Response if response
 *               is a already an instance, otherwise returns a new WP_REST_Response instance.
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
		header( 'Access-Control-Allow-Origin: ' . esc_url_raw( $origin ) );
		header( 'Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE' );
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
	$data = array();

	$accept = array();

	foreach ( $handler->get_routes() as $route => $endpoints ) {
		$match = preg_match( '@^' . $route . '$@i', $request->get_route(), $args );

		if ( ! $match ) {
			continue;
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
 * @global mixed $wp_rest_auth_cookie
 *
 * @param WP_Error|mixed $result Error from another authentication handler, null if we should handle it,
 *                               or another value if not.
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
 * Retrieves a local date with its GMT equivalent, in MySQL datetime format.
 *
 * @since 4.4.0
 *
 * @see rest_parse_date()
 *
 * @param string $date      RFC3339 timestamp.
 * @param bool   $force_utc Whether a UTC timestamp should be forced. Default false.
 * @return array|null Local and UTC datetime strings, in MySQL datetime format (Y-m-d H:i:s),
 *                    null on failure.
 */
function rest_get_date_with_gmt( $date, $force_utc = false ) {
	$date = rest_parse_date( $date, $force_utc );

	if ( empty( $date ) ) {
		return null;
	}

	$utc = date( 'Y-m-d H:i:s', $date );
	$local = get_date_from_gmt( $utc );

	return array( $local, $utc );
}

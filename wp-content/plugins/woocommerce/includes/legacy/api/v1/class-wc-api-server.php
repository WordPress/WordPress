<?php
/**
 * WooCommerce API
 *
 * Handles REST API requests
 *
 * This class and related code (JSON response handler, resource classes) are based on WP-API v0.6 (https://github.com/WP-API/WP-API)
 * Many thanks to Ryan McCue and any other contributors!
 *
 * @author      WooThemes
 * @category    API
 * @package     WooCommerce\RestApi
 * @since       2.1
 * @version     2.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

require_once ABSPATH . 'wp-admin/includes/admin.php';

class WC_API_Server {

	const METHOD_GET    = 1;
	const METHOD_POST   = 2;
	const METHOD_PUT    = 4;
	const METHOD_PATCH  = 8;
	const METHOD_DELETE = 16;

	const READABLE   = 1;  // GET
	const CREATABLE  = 2;  // POST
	const EDITABLE   = 14; // POST | PUT | PATCH
	const DELETABLE  = 16; // DELETE
	const ALLMETHODS = 31; // GET | POST | PUT | PATCH | DELETE

	/**
	 * Does the endpoint accept a raw request body?
	 */
	const ACCEPT_RAW_DATA = 64;

	/** Does the endpoint accept a request body? (either JSON or XML) */
	const ACCEPT_DATA = 128;

	/**
	 * Should we hide this endpoint from the index?
	 */
	const HIDDEN_ENDPOINT = 256;

	/**
	 * Map of HTTP verbs to constants
	 * @var array
	 */
	public static $method_map = array(
		'HEAD'   => self::METHOD_GET,
		'GET'    => self::METHOD_GET,
		'POST'   => self::METHOD_POST,
		'PUT'    => self::METHOD_PUT,
		'PATCH'  => self::METHOD_PATCH,
		'DELETE' => self::METHOD_DELETE,
	);

	/**
	 * Requested path (relative to the API root, wp-json.php)
	 *
	 * @var string
	 */
	public $path = '';

	/**
	 * Requested method (GET/HEAD/POST/PUT/PATCH/DELETE)
	 *
	 * @var string
	 */
	public $method = 'HEAD';

	/**
	 * Request parameters
	 *
	 * This acts as an abstraction of the superglobals
	 * (GET => $_GET, POST => $_POST)
	 *
	 * @var array
	 */
	public $params = array( 'GET' => array(), 'POST' => array() );

	/**
	 * Request headers
	 *
	 * @var array
	 */
	public $headers = array();

	/**
	 * Request files (matches $_FILES)
	 *
	 * @var array
	 */
	public $files = array();

	/**
	 * Request/Response handler, either JSON by default
	 * or XML if requested by client
	 *
	 * @var WC_API_Handler
	 */
	public $handler;


	/**
	 * Setup class and set request/response handler
	 *
	 * @since 2.1
	 * @param $path
	 */
	public function __construct( $path ) {

		if ( empty( $path ) ) {
			if ( isset( $_SERVER['PATH_INFO'] ) ) {
				$path = $_SERVER['PATH_INFO'];
			} else {
				$path = '/';
			}
		}

		$this->path           = $path;
		$this->method         = $_SERVER['REQUEST_METHOD'];
		$this->params['GET']  = $_GET;
		$this->params['POST'] = $_POST;
		$this->headers        = $this->get_headers( $_SERVER );
		$this->files          = $_FILES;

		// Compatibility for clients that can't use PUT/PATCH/DELETE
		if ( isset( $_GET['_method'] ) ) {
			$this->method = strtoupper( $_GET['_method'] );
		} elseif ( isset( $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] ) ) {
			$this->method = $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'];
		}

		// determine type of request/response and load handler, JSON by default
		if ( $this->is_json_request() ) {
			$handler_class = 'WC_API_JSON_Handler';
		} elseif ( $this->is_xml_request() ) {
			$handler_class = 'WC_API_XML_Handler';
		} else {
			$handler_class = apply_filters( 'woocommerce_api_default_response_handler', 'WC_API_JSON_Handler', $this->path, $this );
		}

		$this->handler = new $handler_class();
	}

	/**
	 * Check authentication for the request
	 *
	 * @since 2.1
	 * @return WP_User|WP_Error WP_User object indicates successful login, WP_Error indicates unsuccessful login
	 */
	public function check_authentication() {

		// allow plugins to remove default authentication or add their own authentication
		$user = apply_filters( 'woocommerce_api_check_authentication', null, $this );

		// API requests run under the context of the authenticated user
		if ( is_a( $user, 'WP_User' ) ) {
			wp_set_current_user( $user->ID );
		} elseif ( ! is_wp_error( $user ) ) {
			// WP_Errors are handled in serve_request()
			$user = new WP_Error( 'woocommerce_api_authentication_error', __( 'Invalid authentication method', 'woocommerce' ), array( 'code' => 500 ) );
		}

		return $user;
	}

	/**
	 * Convert an error to an array
	 *
	 * This iterates over all error codes and messages to change it into a flat
	 * array. This enables simpler client behaviour, as it is represented as a
	 * list in JSON rather than an object/map
	 *
	 * @since 2.1
	 * @param WP_Error $error
	 * @return array List of associative arrays with code and message keys
	 */
	protected function error_to_array( $error ) {
		$errors = array();
		foreach ( (array) $error->errors as $code => $messages ) {
			foreach ( (array) $messages as $message ) {
				$errors[] = array( 'code' => $code, 'message' => $message );
			}
		}
		return array( 'errors' => $errors );
	}

	/**
	 * Handle serving an API request
	 *
	 * Matches the current server URI to a route and runs the first matching
	 * callback then outputs a JSON representation of the returned value.
	 *
	 * @since 2.1
	 * @uses WC_API_Server::dispatch()
	 */
	public function serve_request() {

		do_action( 'woocommerce_api_server_before_serve', $this );

		$this->header( 'Content-Type', $this->handler->get_content_type(), true );

		// the API is enabled by default
		if ( ! apply_filters( 'woocommerce_api_enabled', true, $this ) || ( 'no' === get_option( 'woocommerce_api_enabled' ) ) ) {

			$this->send_status( 404 );

			echo $this->handler->generate_response( array( 'errors' => array( 'code' => 'woocommerce_api_disabled', 'message' => 'The WooCommerce API is disabled on this site' ) ) );

			return;
		}

		$result = $this->check_authentication();

		// if authorization check was successful, dispatch the request
		if ( ! is_wp_error( $result ) ) {
			$result = $this->dispatch();
		}

		// handle any dispatch errors
		if ( is_wp_error( $result ) ) {
			$data = $result->get_error_data();
			if ( is_array( $data ) && isset( $data['status'] ) ) {
				$this->send_status( $data['status'] );
			}

			$result = $this->error_to_array( $result );
		}

		// This is a filter rather than an action, since this is designed to be
		// re-entrant if needed
		$served = apply_filters( 'woocommerce_api_serve_request', false, $result, $this );

		if ( ! $served ) {

			if ( 'HEAD' === $this->method ) {
				return;
			}

			echo $this->handler->generate_response( $result );
		}
	}

	/**
	 * Retrieve the route map
	 *
	 * The route map is an associative array with path regexes as the keys. The
	 * value is an indexed array with the callback function/method as the first
	 * item, and a bitmask of HTTP methods as the second item (see the class
	 * constants).
	 *
	 * Each route can be mapped to more than one callback by using an array of
	 * the indexed arrays. This allows mapping e.g. GET requests to one callback
	 * and POST requests to another.
	 *
	 * Note that the path regexes (array keys) must have @ escaped, as this is
	 * used as the delimiter with preg_match()
	 *
	 * @since 2.1
	 * @return array `'/path/regex' => array( $callback, $bitmask )` or `'/path/regex' => array( array( $callback, $bitmask ), ...)`
	 */
	public function get_routes() {

		// index added by default
		$endpoints = array(

			'/' => array( array( $this, 'get_index' ), self::READABLE ),
		);

		$endpoints = apply_filters( 'woocommerce_api_endpoints', $endpoints );

		// Normalise the endpoints
		foreach ( $endpoints as $route => &$handlers ) {
			if ( count( $handlers ) <= 2 && isset( $handlers[1] ) && ! is_array( $handlers[1] ) ) {
				$handlers = array( $handlers );
			}
		}

		return $endpoints;
	}

	/**
	 * Match the request to a callback and call it
	 *
	 * @since 2.1
	 * @return mixed The value returned by the callback, or a WP_Error instance
	 */
	public function dispatch() {

		switch ( $this->method ) {

			case 'HEAD':
			case 'GET':
				$method = self::METHOD_GET;
				break;

			case 'POST':
				$method = self::METHOD_POST;
				break;

			case 'PUT':
				$method = self::METHOD_PUT;
				break;

			case 'PATCH':
				$method = self::METHOD_PATCH;
				break;

			case 'DELETE':
				$method = self::METHOD_DELETE;
				break;

			default:
				return new WP_Error( 'woocommerce_api_unsupported_method', __( 'Unsupported request method', 'woocommerce' ), array( 'status' => 400 ) );
		}

		foreach ( $this->get_routes() as $route => $handlers ) {
			foreach ( $handlers as $handler ) {
				$callback = $handler[0];
				$supported = isset( $handler[1] ) ? $handler[1] : self::METHOD_GET;

				if ( ! ( $supported & $method ) ) {
					continue;
				}

				$match = preg_match( '@^' . $route . '$@i', urldecode( $this->path ), $args );

				if ( ! $match ) {
					continue;
				}

				if ( ! is_callable( $callback ) ) {
					return new WP_Error( 'woocommerce_api_invalid_handler', __( 'The handler for the route is invalid', 'woocommerce' ), array( 'status' => 500 ) );
				}

				$args = array_merge( $args, $this->params['GET'] );
				if ( $method & self::METHOD_POST ) {
					$args = array_merge( $args, $this->params['POST'] );
				}
				if ( $supported & self::ACCEPT_DATA ) {
					$data = $this->handler->parse_body( $this->get_raw_data() );
					$args = array_merge( $args, array( 'data' => $data ) );
				} elseif ( $supported & self::ACCEPT_RAW_DATA ) {
					$data = $this->get_raw_data();
					$args = array_merge( $args, array( 'data' => $data ) );
				}

				$args['_method']  = $method;
				$args['_route']   = $route;
				$args['_path']    = $this->path;
				$args['_headers'] = $this->headers;
				$args['_files']   = $this->files;

				$args = apply_filters( 'woocommerce_api_dispatch_args', $args, $callback );

				// Allow plugins to halt the request via this filter
				if ( is_wp_error( $args ) ) {
					return $args;
				}

				$params = $this->sort_callback_params( $callback, $args );
				if ( is_wp_error( $params ) ) {
					return $params;
				}

				return call_user_func_array( $callback, $params );
			}
		}

		return new WP_Error( 'woocommerce_api_no_route', __( 'No route was found matching the URL and request method', 'woocommerce' ), array( 'status' => 404 ) );
	}

	/**
	 * Sort parameters by order specified in method declaration
	 *
	 * Takes a callback and a list of available params, then filters and sorts
	 * by the parameters the method actually needs, using the Reflection API
	 *
	 * @since 2.1
	 *
	 * @param callable|array $callback the endpoint callback
	 * @param array $provided the provided request parameters
	 *
	 * @return array|WP_Error
	 */
	protected function sort_callback_params( $callback, $provided ) {
		if ( is_array( $callback ) ) {
			$ref_func = new ReflectionMethod( $callback[0], $callback[1] );
		} else {
			$ref_func = new ReflectionFunction( $callback );
		}

		$wanted = $ref_func->getParameters();
		$ordered_parameters = array();

		foreach ( $wanted as $param ) {
			if ( isset( $provided[ $param->getName() ] ) ) {
				// We have this parameters in the list to choose from
				$ordered_parameters[] = is_array( $provided[ $param->getName() ] ) ? array_map( 'urldecode', $provided[ $param->getName() ] ) : urldecode( $provided[ $param->getName() ] );
			} elseif ( $param->isDefaultValueAvailable() ) {
				// We don't have this parameter, but it's optional
				$ordered_parameters[] = $param->getDefaultValue();
			} else {
				// We don't have this parameter and it wasn't optional, abort!
				return new WP_Error( 'woocommerce_api_missing_callback_param', sprintf( __( 'Missing parameter %s', 'woocommerce' ), $param->getName() ), array( 'status' => 400 ) );
			}
		}
		return $ordered_parameters;
	}

	/**
	 * Get the site index.
	 *
	 * This endpoint describes the capabilities of the site.
	 *
	 * @since 2.1
	 * @return array Index entity
	 */
	public function get_index() {

		// General site data
		$available = array(
			'store' => array(
				'name'        => get_option( 'blogname' ),
				'description' => get_option( 'blogdescription' ),
				'URL'         => get_option( 'siteurl' ),
				'wc_version'  => WC()->version,
				'routes'      => array(),
				'meta'        => array(
					'timezone'			 => wc_timezone_string(),
					'currency'       	 => get_woocommerce_currency(),
					'currency_format'    => get_woocommerce_currency_symbol(),
					'tax_included'   	 => wc_prices_include_tax(),
					'weight_unit'    	 => get_option( 'woocommerce_weight_unit' ),
					'dimension_unit' 	 => get_option( 'woocommerce_dimension_unit' ),
					'ssl_enabled'    	 => ( 'yes' === get_option( 'woocommerce_force_ssl_checkout' ) ),
					'permalinks_enabled' => ( '' !== get_option( 'permalink_structure' ) ),
					'links'          	 => array(
						'help' => 'https://woocommerce.github.io/woocommerce/rest-api/',
					),
				),
			),
		);

		// Find the available routes
		foreach ( $this->get_routes() as $route => $callbacks ) {
			$data = array();

			$route = preg_replace( '#\(\?P(<\w+?>).*?\)#', '$1', $route );
			$methods = array();
			foreach ( self::$method_map as $name => $bitmask ) {
				foreach ( $callbacks as $callback ) {
					// Skip to the next route if any callback is hidden
					if ( $callback[1] & self::HIDDEN_ENDPOINT ) {
						continue 3;
					}

					if ( $callback[1] & $bitmask ) {
						$data['supports'][] = $name;
					}

					if ( $callback[1] & self::ACCEPT_DATA ) {
						$data['accepts_data'] = true;
					}

					// For non-variable routes, generate links
					if ( strpos( $route, '<' ) === false ) {
						$data['meta'] = array(
							'self' => get_woocommerce_api_url( $route ),
						);
					}
				}
			}
			$available['store']['routes'][ $route ] = apply_filters( 'woocommerce_api_endpoints_description', $data );
		}
		return apply_filters( 'woocommerce_api_index', $available );
	}

	/**
	 * Send a HTTP status code
	 *
	 * @since 2.1
	 * @param int $code HTTP status
	 */
	public function send_status( $code ) {
		status_header( $code );
	}

	/**
	 * Send a HTTP header
	 *
	 * @since 2.1
	 * @param string $key Header key
	 * @param string $value Header value
	 * @param boolean $replace Should we replace the existing header?
	 */
	public function header( $key, $value, $replace = true ) {
		header( sprintf( '%s: %s', $key, $value ), $replace );
	}

	/**
	 * Send a Link header
	 *
	 * @internal The $rel parameter is first, as this looks nicer when sending multiple
	 *
	 * @link http://tools.ietf.org/html/rfc5988
	 * @link http://www.iana.org/assignments/link-relations/link-relations.xml
	 *
	 * @since 2.1
	 * @param string $rel Link relation. Either a registered type, or an absolute URL
	 * @param string $link Target IRI for the link
	 * @param array $other Other parameters to send, as an associative array
	 */
	public function link_header( $rel, $link, $other = array() ) {

		$header = sprintf( '<%s>; rel="%s"', $link, esc_attr( $rel ) );

		foreach ( $other as $key => $value ) {

			if ( 'title' == $key ) {

				$value = '"' . $value . '"';
			}

			$header .= '; ' . $key . '=' . $value;
		}

		$this->header( 'Link', $header, false );
	}

	/**
	 * Send pagination headers for resources
	 *
	 * @since 2.1
	 * @param WP_Query|WP_User_Query $query
	 */
	public function add_pagination_headers( $query ) {

		// WP_User_Query
		if ( is_a( $query, 'WP_User_Query' ) ) {

			$page        = $query->page;
			$single      = count( $query->get_results() ) == 1;
			$total       = $query->get_total();
			$total_pages = $query->total_pages;

		// WP_Query
		} else {

			$page        = $query->get( 'paged' );
			$single      = $query->is_single();
			$total       = $query->found_posts;
			$total_pages = $query->max_num_pages;
		}

		if ( ! $page ) {
			$page = 1;
		}

		$next_page = absint( $page ) + 1;

		if ( ! $single ) {

			// first/prev
			if ( $page > 1 ) {
				$this->link_header( 'first', $this->get_paginated_url( 1 ) );
				$this->link_header( 'prev', $this->get_paginated_url( $page -1 ) );
			}

			// next
			if ( $next_page <= $total_pages ) {
				$this->link_header( 'next', $this->get_paginated_url( $next_page ) );
			}

			// last
			if ( $page != $total_pages ) {
				$this->link_header( 'last', $this->get_paginated_url( $total_pages ) );
			}
		}

		$this->header( 'X-WC-Total', $total );
		$this->header( 'X-WC-TotalPages', $total_pages );

		do_action( 'woocommerce_api_pagination_headers', $this, $query );
	}

	/**
	 * Returns the request URL with the page query parameter set to the specified page
	 *
	 * @since 2.1
	 * @param int $page
	 * @return string
	 */
	private function get_paginated_url( $page ) {

		// remove existing page query param
		$request = remove_query_arg( 'page' );

		// add provided page query param
		$request = urldecode( add_query_arg( 'page', $page, $request ) );

		// get the home host
		$host = parse_url( get_home_url(), PHP_URL_HOST );

		return set_url_scheme( "http://{$host}{$request}" );
	}

	/**
	 * Retrieve the raw request entity (body)
	 *
	 * @since 2.1
	 * @return string
	 */
	public function get_raw_data() {
		// @codingStandardsIgnoreStart
		// $HTTP_RAW_POST_DATA is deprecated on PHP 5.6.
		if ( function_exists( 'phpversion' ) && version_compare( phpversion(), '5.6', '>=' ) ) {
			return file_get_contents( 'php://input' );
		}

		global $HTTP_RAW_POST_DATA;

		// A bug in PHP < 5.2.2 makes $HTTP_RAW_POST_DATA not set by default,
		// but we can do it ourself.
		if ( ! isset( $HTTP_RAW_POST_DATA ) ) {
			$HTTP_RAW_POST_DATA = file_get_contents( 'php://input' );
		}

		return $HTTP_RAW_POST_DATA;
		// @codingStandardsIgnoreEnd
	}

	/**
	 * Parse an RFC3339 datetime into a MySQl datetime
	 *
	 * Invalid dates default to unix epoch
	 *
	 * @since 2.1
	 * @param string $datetime RFC3339 datetime
	 * @return string MySQl datetime (YYYY-MM-DD HH:MM:SS)
	 */
	public function parse_datetime( $datetime ) {

		// Strip millisecond precision (a full stop followed by one or more digits)
		if ( strpos( $datetime, '.' ) !== false ) {
			$datetime = preg_replace( '/\.\d+/', '', $datetime );
		}

		// default timezone to UTC
		$datetime = preg_replace( '/[+-]\d+:+\d+$/', '+00:00', $datetime );

		try {

			$datetime = new DateTime( $datetime, new DateTimeZone( 'UTC' ) );

		} catch ( Exception $e ) {

			$datetime = new DateTime( '@0' );

		}

		return $datetime->format( 'Y-m-d H:i:s' );
	}

	/**
	 * Format a unix timestamp or MySQL datetime into an RFC3339 datetime
	 *
	 * @since 2.1
	 * @param int|string $timestamp unix timestamp or MySQL datetime
	 * @param bool $convert_to_utc
	 * @param bool $convert_to_gmt Use GMT timezone.
	 * @return string RFC3339 datetime
	 */
	public function format_datetime( $timestamp, $convert_to_utc = false, $convert_to_gmt = false ) {
		if ( $convert_to_gmt ) {
			if ( is_numeric( $timestamp ) ) {
				$timestamp = date( 'Y-m-d H:i:s', $timestamp );
			}

			$timestamp = get_gmt_from_date( $timestamp );
		}

		if ( $convert_to_utc ) {
			$timezone = new DateTimeZone( wc_timezone_string() );
		} else {
			$timezone = new DateTimeZone( 'UTC' );
		}

		try {

			if ( is_numeric( $timestamp ) ) {
				$date = new DateTime( "@{$timestamp}" );
			} else {
				$date = new DateTime( $timestamp, $timezone );
			}

			// convert to UTC by adjusting the time based on the offset of the site's timezone
			if ( $convert_to_utc ) {
				$date->modify( -1 * $date->getOffset() . ' seconds' );
			}
		} catch ( Exception $e ) {

			$date = new DateTime( '@0' );
		}

		return $date->format( 'Y-m-d\TH:i:s\Z' );
	}

	/**
	 * Extract headers from a PHP-style $_SERVER array
	 *
	 * @since 2.1
	 * @param array $server Associative array similar to $_SERVER
	 * @return array Headers extracted from the input
	 */
	public function get_headers( $server ) {
		$headers = array();
		// CONTENT_* headers are not prefixed with HTTP_
		$additional = array( 'CONTENT_LENGTH' => true, 'CONTENT_MD5' => true, 'CONTENT_TYPE' => true );

		foreach ( $server as $key => $value ) {
			if ( strpos( $key, 'HTTP_' ) === 0 ) {
				$headers[ substr( $key, 5 ) ] = $value;
			} elseif ( isset( $additional[ $key ] ) ) {
				$headers[ $key ] = $value;
			}
		}

		return $headers;
	}

	/**
	 * Check if the current request accepts a JSON response by checking the endpoint suffix (.json) or
	 * the HTTP ACCEPT header
	 *
	 * @since 2.1
	 * @return bool
	 */
	private function is_json_request() {

		// check path
		if ( false !== stripos( $this->path, '.json' ) ) {
			return true;
		}

		// check ACCEPT header, only 'application/json' is acceptable, see RFC 4627
		if ( isset( $this->headers['ACCEPT'] ) && 'application/json' == $this->headers['ACCEPT'] ) {
			return true;
		}

		return false;
	}

	/**
	 * Check if the current request accepts an XML response by checking the endpoint suffix (.xml) or
	 * the HTTP ACCEPT header
	 *
	 * @since 2.1
	 * @return bool
	 */
	private function is_xml_request() {

		// check path
		if ( false !== stripos( $this->path, '.xml' ) ) {
			return true;
		}

		// check headers, 'application/xml' or 'text/xml' are acceptable, see RFC 2376
		if ( isset( $this->headers['ACCEPT'] ) && ( 'application/xml' == $this->headers['ACCEPT'] || 'text/xml' == $this->headers['ACCEPT'] ) ) {
			return true;
		}

		return false;
	}
}

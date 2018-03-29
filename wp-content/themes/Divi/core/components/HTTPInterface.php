<?php

/**
 * Simple object to hold HTTP request details.
 *
 * @since   1.1.0
 *
 * @package ET\Core\HTTP
 */
class ET_Core_HTTPRequest {

	/**
	 * @var array
	 */
	public $ARGS;

	/**
	 * @var bool
	 */
	public $BLOCKING = true;

	/**
	 * @var null|array
	 */
	public $BODY = null;

	/**
	 * @var bool
	 */
	public $COMPLETE = false;

	/**
	 * @var array
	 */
	public $COOKIES = array();

	/**
	 * @var array
	 */
	public $HEADERS = array();

	/**
	 * @var bool
	 */
	public $IS_AUTH = false;

	/**
	 * @var string
	 */
	public $METHOD = 'GET';

	/**
	 * @var string
	 */
	public $OWNER;

	/**
	 * @var string
	 */
	public $URL;

	/**
	 * @var string
	 */
	public $USER_AGENT;

	/**
	 * ET_Core_HTTP_Request constructor.
	 *
	 * @param string $url          The request URL.
	 * @param string $method       HTTP request method. Default is 'GET'.
	 * @param string $owner        The name of the owner of this request instance. Default is 'ET_Core'.
	 * @param bool   $is_auth      Whether or not this request is auth-related. Cache disabled if `true`. Default is `false`.
	 * @param array  $body         The request body. Default is `null`.
	 * @param bool   $is_json_body
	 */
	public function __construct( $url, $method = 'GET', $owner = 'ET_Core', $is_auth = false, $body = null, $is_json_body = false, $ssl_verify = true ) {
		$this->URL         = esc_url_raw( $url );
		$this->METHOD      = $method;
		$this->BODY        = $body;
		$this->IS_AUTH     = $is_auth;
		$this->OWNER       = $owner;
		$this->data_format = null;
		$this->JSON_BODY   = $is_json_body;
		$this->SSL_VERIFY  = $ssl_verify;

		$this->_set_user_agent();
		$this->prepare_args();
	}

	/**
	 * Only include necessary properties when printing this object using {@link var_dump}.
	 *
	 * @return array
	 */
	public function __debugInfo() {
		return array(
			'ARGS'         => $this->ARGS,
			'URL'          => $this->URL,
			'METHOD'       => $this->METHOD,
			'BODY'         => $this->BODY,
			'IS_AUTH'      => $this->IS_AUTH,
			'OWNER'        => $this->OWNER,
		);
	}

	/**
	 * Sets the user agent string.
	 */
	private function _set_user_agent() {
		global $wp_version;
		$owner   = $this->OWNER;
		$version =  'bloom' === $owner ? $GLOBALS['et_bloom']->plugin_version : ET_CORE_VERSION;

		if ( 'builder' === $owner ) {
			$owner = 'Divi Builder';
		}

		if ( '' === $owner ) {
			$this->OWNER = $owner = 'ET_Core';

		} else {
			$owner = ucfirst( $owner );
		}

		$this->USER_AGENT = "WordPress/{$wp_version}; {$owner}/{$version}; " . esc_url_raw( get_bloginfo( 'url' ) );
	}

	/**
	 * Prepares the request arguments (to be passed to wp_remote_*())
	 */
	public function prepare_args() {
		$this->ARGS = array(
			'blocking'   => $this->BLOCKING,
			'body'       => $this->BODY,
			'cookies'    => $this->COOKIES,
			'headers'    => $this->HEADERS,
			'method'     => $this->METHOD,
			'sslverify'  => $this->SSL_VERIFY,
			'user-agent' => $this->USER_AGENT,
		);
	}
}

/**
 * Simple object to hold HTTP response details.
 *
 * @since   1.1.0
 *
 * @package ET\Core\HTTP
 */
class ET_Core_HTTPResponse {

	/**
	 * @var array
	 */
	public $COOKIES;

	/**
	 * @var string|array
	 */
	public $DATA;

	/**
	 * @var bool
	 */
	public $ERROR = false;

	/**
	 * The error message if `self::$ERROR` is `true`.
	 *
	 * @var string
	 */
	public $ERROR_MESSAGE;

	/**
	 * @var array
	 */
	public $HEADERS;

	/**
	 * @var array|WP_Error
	 */
	public $RAW_RESPONSE;

	/**
	 * @var ET_Core_HTTPRequest
	 */
	public $REQUEST;

	/**
	 * The response's HTTP status code.
	 *
	 * @var int
	 */
	public $STATUS_CODE;

	/**
	 * @var string
	 */
	public $STATUS_MESSAGE;

	/**
	 * ET_Core_HTTP_Response constructor.
	 *
	 * @param ET_Core_HTTPRequest $request
	 * @param array|WP_Error      $response
	 */
	public function __construct( $request, $response ) {
		$this->REQUEST      = $request;
		$this->RAW_RESPONSE = $response;

		$this->_parse_response();
	}

	/**
	 * Parse response and save relevant details.
	 */
	private function _parse_response() {
		if ( is_wp_error( $this->RAW_RESPONSE ) ) {
			$this->ERROR          = true;
			$this->ERROR_MESSAGE  = $this->RAW_RESPONSE->get_error_message();
			$this->STATUS_CODE    = $this->RAW_RESPONSE->get_error_code();
			$this->STATUS_MESSAGE = $this->ERROR_MESSAGE;
			return;
		}

		$this->DATA           = $this->RAW_RESPONSE['body'];
		$this->HEADERS        = $this->RAW_RESPONSE['headers'];
		$this->COOKIES        = $this->RAW_RESPONSE['cookies'];
		$this->STATUS_CODE    = $this->RAW_RESPONSE['response']['code'];
		$this->STATUS_MESSAGE = $this->RAW_RESPONSE['response']['message'];

		if ( $this->STATUS_CODE >= 400 ) {
			$this->ERROR         = true;
			$this->ERROR_MESSAGE = $this->STATUS_MESSAGE;
		}
	}

	/**
	 * Only include necessary properties when printing this object using {@link var_dump}.
	 *
	 * @return array
	 */
	public function __debugInfo() {
		return array(
			'STATUS_CODE'    => $this->STATUS_CODE,
			'STATUS_MESSAGE' => $this->STATUS_MESSAGE,
			'ERROR'          => $this->ERROR,
			'ERROR_MESSAGE'  => $this->ERROR_MESSAGE,
			'DATA'           => $this->DATA,
		);
	}

	/**
	 * Only include necessary properties when serializing this object for
	 * storage in the WP Transient Cache.
	 *
	 * @return array
	 */
	public function __sleep() {
		return array( 'ERROR', 'ERROR_MESSAGE', 'STATUS_CODE', 'STATUS_MESSAGE', 'DATA' );
	}
}


/**
 * High level, generic, wrapper for making HTTP requests. It uses WordPress HTTP API under-the-hood.
 *
 * @since   1.1.0
 *
 * @package ET\Core\HTTP
 */
class ET_Core_HTTPInterface {
	/**
	 * How much time responses are cached (in seconds).
	 *
	 * @since 1.1.0
	 * @var   int
	 */
	protected $cache_timeout;

	/**
	 * @var ET_Core_HTTPRequest
	 */
	public $request;

	/**
	 * @var ET_Core_HTTPResponse
	 */
	public $response;

	/**
	 * ET_Core_API_HTTP_Interface constructor.
	 *
	 * @since 1.1.0
	 *
	 * @param string $owner           The name of the theme/plugin that created this class instance. Default: 'ET_Core'.
	 * @param array  $request_details Array of config values for the request. Optional.
	 * @param bool   $json            Whether or not json responses are expected to be received. Default is `true`.
	 */
	public function __construct( $owner = 'ET_Core', $request_details = array(), $json = true ) {
		$this->expects_json  = $json;
		$this->cache_timeout = 15 * MINUTE_IN_SECONDS;
		$this->owner         = $owner;

		if ( ! empty( $request_details ) ) {
			list( $url, $method, $is_auth, $body ) = $request_details;
			$this->prepare_request( $url, $method, $is_auth, $body );
		}
	}

	/**
	 * Only include necessary properties when printing this object using {@link var_dump}.
	 *
	 * @return array
	 */
	public function __debugInfo() {
		return array(
			'REQUEST'  => $this->request,
			'RESPONSE' => $this->response,
		);
	}

	/**
	 * Only include necessary properties when serializing this object for
	 * storage in the WP Transient Cache.
	 *
	 * @return array
	 */
	public function __sleep() {
		return array( 'request', 'response' );
	}

	/**
	 * Creates an identifier key for a request based on the URL and body content.
	 *
	 * @internal
	 * @since    1.1.0
	 *
	 * @param string          $url  The request URL.
	 * @param string|string[] $body The request body.
	 *
	 * @return string
	 */
	protected static function _get_cache_key_for_request( $url, $body ) {
		if ( is_array( $body ) ) {
			$url .= json_encode( $body );

		} else if ( ! empty( $body ) ) {
			$url .= $body;
		}

		return 'et-core-http-response-' . md5( $url );
	}

	/**
	 * Writes request/response info to the error log for failed requests.
	 *
	 * @internal
	 * @since    1.1.0
	 */
	protected function _log_failed_request() {
		ob_start();
		var_dump( $this );
		$response_details = ob_get_contents();
		ob_end_clean();

		$class_name = get_class( $this );
		$msg_part   = "{$class_name} ERROR :: Remote request failed...\n\n";
		$msg        = "{$msg_part}Details: {$response_details}";

		ET_Core_Logger::error( $msg );
	}

	/**
	 * Prepares request to send JSON data.
	 */
	protected function _setup_json_request() {
		$this->request->HEADERS['Accept'] = 'application/json';

		if ( $this->request->JSON_BODY ) {
			$this->request->BODY                    = null !== $this->request->BODY ? json_encode( $this->request->BODY ) : null;
			$this->request->HEADERS['Content-Type'] = 'application/json';
		}
	}

	/**
	 * Performs a remote HTTP request. Responses are cached for {@see self::$cache_timeout} seconds using
	 * the {@link https://goo.gl/c0FSMH WP Transients API}.
	 *
	 * @since    1.1.0
	 */
	public function make_remote_request() {
		$cache_key = self::_get_cache_key_for_request( $this->request->URL, $this->request->BODY );
		$response  = null;

		if ( ! $this->request->IS_AUTH && false !== ( $response = get_transient( $cache_key ) ) ) {
			$this->response          = $response;
			$this->request->COMPLETE = true;

			return;
		}

		if ( $this->expects_json && ! isset( $this->request->HEADERS['Content-Type'] ) ) {
			$this->_setup_json_request();
		}

		// Make sure we include any changes made after request object was instantiated.
		$this->request->prepare_args();

		if ( 'POST' === $this->request->METHOD ) {
			$response = wp_remote_post( $this->request->URL, $this->request->ARGS );

		} else if ( 'GET' === $this->request->METHOD && null === $this->request->data_format ) {
			$response = wp_remote_get( $this->request->URL, $this->request->ARGS );

		} else if ( 'GET' === $this->request->METHOD && null !== $this->request->data_format ) {
			// WordPress sends data as query args for GET and HEAD requests and provides no way
			// to alter that behavior. Thus, we need to monkey patch it for now. See the mp'd class
			// for more details.
			require_once 'lib/WPHttp.php';
			$wp_http                            = new ET_Core_LIB_WPHttp();
			$this->request->ARGS['data_format'] = $this->request->data_format;
			$response                           = $wp_http->request( $this->request->URL, $this->request->ARGS );

		} else if ( 'PUT' === $this->request->METHOD ) {
			$this->request->ARGS['method'] = 'PUT';
			$response = wp_remote_request( $this->request->URL, $this->request->ARGS );
		}

		$this->response = $response = new ET_Core_HTTPResponse( $this->request, $response );

		if ( $response->ERROR || defined( 'ET_DEBUG' ) ) {
			$this->_log_failed_request();
		}

		if ( $this->expects_json ) {
			$response->DATA = json_decode( $response->DATA, true );
		}

		if ( ! $this->request->IS_AUTH && ! $response->ERROR ) {
			set_transient( $cache_key, $response, $this->cache_timeout );
		}

		$this->request->COMPLETE = true;
	}

	/**
	 * Replaces the current request object with a new instance.
	 *
	 * @param string $url
	 * @param string $method
	 * @param bool   $is_auth
	 * @param mixed? $body
	 * @param bool   $json_body
	 * @param bool   $ssl_verify
	 */
	public function prepare_request( $url, $method = 'GET', $is_auth = false, $body = null, $json_body = false, $ssl_verify = true ) {
		$this->request = new ET_Core_HTTPRequest( $url, $method, $this->owner, $is_auth, $body, $json_body, $ssl_verify );
	}
}
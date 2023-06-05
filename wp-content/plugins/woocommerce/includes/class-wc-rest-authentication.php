<?php
/**
 * REST API Authentication
 *
 * @package  WooCommerce\RestApi
 * @since    2.6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * REST API authentication class.
 */
class WC_REST_Authentication {

	/**
	 * Authentication error.
	 *
	 * @var WP_Error
	 */
	protected $error = null;

	/**
	 * Logged in user data.
	 *
	 * @var stdClass
	 */
	protected $user = null;

	/**
	 * Current auth method.
	 *
	 * @var string
	 */
	protected $auth_method = '';

	/**
	 * Initialize authentication actions.
	 */
	public function __construct() {
		add_filter( 'determine_current_user', array( $this, 'authenticate' ), 15 );
		add_filter( 'rest_authentication_errors', array( $this, 'authentication_fallback' ) );
		add_filter( 'rest_authentication_errors', array( $this, 'check_authentication_error' ), 15 );
		add_filter( 'rest_post_dispatch', array( $this, 'send_unauthorized_headers' ), 50 );
		add_filter( 'rest_pre_dispatch', array( $this, 'check_user_permissions' ), 10, 3 );
	}

	/**
	 * Check if is request to our REST API.
	 *
	 * @return bool
	 */
	protected function is_request_to_rest_api() {
		if ( empty( $_SERVER['REQUEST_URI'] ) ) {
			return false;
		}

		$rest_prefix = trailingslashit( rest_get_url_prefix() );
		$request_uri = esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) );

		// Check if the request is to the WC API endpoints.
		$woocommerce = ( false !== strpos( $request_uri, $rest_prefix . 'wc/' ) );

		// Allow third party plugins use our authentication methods.
		$third_party = ( false !== strpos( $request_uri, $rest_prefix . 'wc-' ) );

		return apply_filters( 'woocommerce_rest_is_request_to_rest_api', $woocommerce || $third_party );
	}

	/**
	 * Authenticate user.
	 *
	 * @param int|false $user_id User ID if one has been determined, false otherwise.
	 * @return int|false
	 */
	public function authenticate( $user_id ) {
		// Do not authenticate twice and check if is a request to our endpoint in the WP REST API.
		if ( ! empty( $user_id ) || ! $this->is_request_to_rest_api() ) {
			return $user_id;
		}

		if ( is_ssl() ) {
			$user_id = $this->perform_basic_authentication();
		}

		if ( $user_id ) {
			return $user_id;
		}

		return $this->perform_oauth_authentication();
	}

	/**
	 * Authenticate the user if authentication wasn't performed during the
	 * determine_current_user action.
	 *
	 * Necessary in cases where wp_get_current_user() is called before WooCommerce is loaded.
	 *
	 * @see https://github.com/woocommerce/woocommerce/issues/26847
	 *
	 * @param WP_Error|null|bool $error Error data.
	 * @return WP_Error|null|bool
	 */
	public function authentication_fallback( $error ) {
		if ( ! empty( $error ) ) {
			// Another plugin has already declared a failure.
			return $error;
		}
		if ( empty( $this->error ) && empty( $this->auth_method ) && empty( $this->user ) && 0 === get_current_user_id() ) {
			// Authentication hasn't occurred during `determine_current_user`, so check auth.
			$user_id = $this->authenticate( false );
			if ( $user_id ) {
				wp_set_current_user( $user_id );
				return true;
			}
		}
		return $error;
	}

	/**
	 * Check for authentication error.
	 *
	 * @param WP_Error|null|bool $error Error data.
	 * @return WP_Error|null|bool
	 */
	public function check_authentication_error( $error ) {
		// Pass through other errors.
		if ( ! empty( $error ) ) {
			return $error;
		}

		return $this->get_error();
	}

	/**
	 * Set authentication error.
	 *
	 * @param WP_Error $error Authentication error data.
	 */
	protected function set_error( $error ) {
		// Reset user.
		$this->user = null;

		$this->error = $error;
	}

	/**
	 * Get authentication error.
	 *
	 * @return WP_Error|null.
	 */
	protected function get_error() {
		return $this->error;
	}

	/**
	 * Basic Authentication.
	 *
	 * SSL-encrypted requests are not subject to sniffing or man-in-the-middle
	 * attacks, so the request can be authenticated by simply looking up the user
	 * associated with the given consumer key and confirming the consumer secret
	 * provided is valid.
	 *
	 * @return int|bool
	 */
	private function perform_basic_authentication() {
		$this->auth_method = 'basic_auth';
		$consumer_key      = '';
		$consumer_secret   = '';

		// If the $_GET parameters are present, use those first.
		if ( ! empty( $_GET['consumer_key'] ) && ! empty( $_GET['consumer_secret'] ) ) { // WPCS: CSRF ok.
			$consumer_key    = $_GET['consumer_key']; // WPCS: CSRF ok, sanitization ok.
			$consumer_secret = $_GET['consumer_secret']; // WPCS: CSRF ok, sanitization ok.
		}

		// If the above is not present, we will do full basic auth.
		if ( ! $consumer_key && ! empty( $_SERVER['PHP_AUTH_USER'] ) && ! empty( $_SERVER['PHP_AUTH_PW'] ) ) {
			$consumer_key    = $_SERVER['PHP_AUTH_USER']; // WPCS: CSRF ok, sanitization ok.
			$consumer_secret = $_SERVER['PHP_AUTH_PW']; // WPCS: CSRF ok, sanitization ok.
		}

		// Stop if don't have any key.
		if ( ! $consumer_key || ! $consumer_secret ) {
			return false;
		}

		// Get user data.
		$this->user = $this->get_user_data_by_consumer_key( $consumer_key );
		if ( empty( $this->user ) ) {
			return false;
		}

		// Validate user secret.
		if ( ! hash_equals( $this->user->consumer_secret, $consumer_secret ) ) { // @codingStandardsIgnoreLine
			$this->set_error( new WP_Error( 'woocommerce_rest_authentication_error', __( 'Consumer secret is invalid.', 'woocommerce' ), array( 'status' => 401 ) ) );

			return false;
		}

		return $this->user->user_id;
	}

	/**
	 * Parse the Authorization header into parameters.
	 *
	 * @since 3.0.0
	 *
	 * @param string $header Authorization header value (not including "Authorization: " prefix).
	 *
	 * @return array Map of parameter values.
	 */
	public function parse_header( $header ) {
		if ( 'OAuth ' !== substr( $header, 0, 6 ) ) {
			return array();
		}

		// From OAuth PHP library, used under MIT license.
		$params = array();
		if ( preg_match_all( '/(oauth_[a-z_-]*)=(:?"([^"]*)"|([^,]*))/', $header, $matches ) ) {
			foreach ( $matches[1] as $i => $h ) {
				$params[ $h ] = urldecode( empty( $matches[3][ $i ] ) ? $matches[4][ $i ] : $matches[3][ $i ] );
			}
			if ( isset( $params['realm'] ) ) {
				unset( $params['realm'] );
			}
		}

		return $params;
	}

	/**
	 * Get the authorization header.
	 *
	 * On certain systems and configurations, the Authorization header will be
	 * stripped out by the server or PHP. Typically this is then used to
	 * generate `PHP_AUTH_USER`/`PHP_AUTH_PASS` but not passed on. We use
	 * `getallheaders` here to try and grab it out instead.
	 *
	 * @since 3.0.0
	 *
	 * @return string Authorization header if set.
	 */
	public function get_authorization_header() {
		if ( ! empty( $_SERVER['HTTP_AUTHORIZATION'] ) ) {
			return wp_unslash( $_SERVER['HTTP_AUTHORIZATION'] ); // WPCS: sanitization ok.
		}

		if ( function_exists( 'getallheaders' ) ) {
			$headers = getallheaders();
			// Check for the authoization header case-insensitively.
			foreach ( $headers as $key => $value ) {
				if ( 'authorization' === strtolower( $key ) ) {
					return $value;
				}
			}
		}

		return '';
	}

	/**
	 * Get oAuth parameters from $_GET, $_POST or request header.
	 *
	 * @since 3.0.0
	 *
	 * @return array|WP_Error
	 */
	public function get_oauth_parameters() {
		$params = array_merge( $_GET, $_POST ); // WPCS: CSRF ok.
		$params = wp_unslash( $params );
		$header = $this->get_authorization_header();

		if ( ! empty( $header ) ) {
			// Trim leading spaces.
			$header        = trim( $header );
			$header_params = $this->parse_header( $header );

			if ( ! empty( $header_params ) ) {
				$params = array_merge( $params, $header_params );
			}
		}

		$param_names = array(
			'oauth_consumer_key',
			'oauth_timestamp',
			'oauth_nonce',
			'oauth_signature',
			'oauth_signature_method',
		);

		$errors   = array();
		$have_one = false;

		// Check for required OAuth parameters.
		foreach ( $param_names as $param_name ) {
			if ( empty( $params[ $param_name ] ) ) {
				$errors[] = $param_name;
			} else {
				$have_one = true;
			}
		}

		// All keys are missing, so we're probably not even trying to use OAuth.
		if ( ! $have_one ) {
			return array();
		}

		// If we have at least one supplied piece of data, and we have an error,
		// then it's a failed authentication.
		if ( ! empty( $errors ) ) {
			$message = sprintf(
				/* translators: %s: amount of errors */
				_n( 'Missing OAuth parameter %s', 'Missing OAuth parameters %s', count( $errors ), 'woocommerce' ),
				implode( ', ', $errors )
			);

			$this->set_error( new WP_Error( 'woocommerce_rest_authentication_missing_parameter', $message, array( 'status' => 401 ) ) );

			return array();
		}

		return $params;
	}

	/**
	 * Perform OAuth 1.0a "one-legged" (http://oauthbible.com/#oauth-10a-one-legged) authentication for non-SSL requests.
	 *
	 * This is required so API credentials cannot be sniffed or intercepted when making API requests over plain HTTP.
	 *
	 * This follows the spec for simple OAuth 1.0a authentication (RFC 5849) as closely as possible, with two exceptions:
	 *
	 * 1) There is no token associated with request/responses, only consumer keys/secrets are used.
	 *
	 * 2) The OAuth parameters are included as part of the request query string instead of part of the Authorization header,
	 *    This is because there is no cross-OS function within PHP to get the raw Authorization header.
	 *
	 * @link http://tools.ietf.org/html/rfc5849 for the full spec.
	 *
	 * @return int|bool
	 */
	private function perform_oauth_authentication() {
		$this->auth_method = 'oauth1';

		$params = $this->get_oauth_parameters();
		if ( empty( $params ) ) {
			return false;
		}

		// Fetch WP user by consumer key.
		$this->user = $this->get_user_data_by_consumer_key( $params['oauth_consumer_key'] );

		if ( empty( $this->user ) ) {
			$this->set_error( new WP_Error( 'woocommerce_rest_authentication_error', __( 'Consumer key is invalid.', 'woocommerce' ), array( 'status' => 401 ) ) );

			return false;
		}

		// Perform OAuth validation.
		$signature = $this->check_oauth_signature( $this->user, $params );
		if ( is_wp_error( $signature ) ) {
			$this->set_error( $signature );
			return false;
		}

		$timestamp_and_nonce = $this->check_oauth_timestamp_and_nonce( $this->user, $params['oauth_timestamp'], $params['oauth_nonce'] );
		if ( is_wp_error( $timestamp_and_nonce ) ) {
			$this->set_error( $timestamp_and_nonce );
			return false;
		}

		return $this->user->user_id;
	}

	/**
	 * Verify that the consumer-provided request signature matches our generated signature,
	 * this ensures the consumer has a valid key/secret.
	 *
	 * @param stdClass $user   User data.
	 * @param array    $params The request parameters.
	 * @return true|WP_Error
	 */
	private function check_oauth_signature( $user, $params ) {
		$http_method  = isset( $_SERVER['REQUEST_METHOD'] ) ? strtoupper( $_SERVER['REQUEST_METHOD'] ) : ''; // WPCS: sanitization ok.
		$request_path = isset( $_SERVER['REQUEST_URI'] ) ? wp_parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ) : ''; // WPCS: sanitization ok.
		$wp_base      = get_home_url( null, '/', 'relative' );
		if ( substr( $request_path, 0, strlen( $wp_base ) ) === $wp_base ) {
			$request_path = substr( $request_path, strlen( $wp_base ) );
		}
		$base_request_uri = rawurlencode( get_home_url( null, $request_path, is_ssl() ? 'https' : 'http' ) );

		// Get the signature provided by the consumer and remove it from the parameters prior to checking the signature.
		$consumer_signature = rawurldecode( str_replace( ' ', '+', $params['oauth_signature'] ) );
		unset( $params['oauth_signature'] );

		// Sort parameters.
		if ( ! uksort( $params, 'strcmp' ) ) {
			return new WP_Error( 'woocommerce_rest_authentication_error', __( 'Invalid signature - failed to sort parameters.', 'woocommerce' ), array( 'status' => 401 ) );
		}

		// Normalize parameter key/values.
		$params         = $this->normalize_parameters( $params );
		$query_string   = implode( '%26', $this->join_with_equals_sign( $params ) ); // Join with ampersand.
		$string_to_sign = $http_method . '&' . $base_request_uri . '&' . $query_string;

		if ( 'HMAC-SHA1' !== $params['oauth_signature_method'] && 'HMAC-SHA256' !== $params['oauth_signature_method'] ) {
			return new WP_Error( 'woocommerce_rest_authentication_error', __( 'Invalid signature - signature method is invalid.', 'woocommerce' ), array( 'status' => 401 ) );
		}

		$hash_algorithm = strtolower( str_replace( 'HMAC-', '', $params['oauth_signature_method'] ) );
		$secret         = $user->consumer_secret . '&';
		$signature      = base64_encode( hash_hmac( $hash_algorithm, $string_to_sign, $secret, true ) );

		if ( ! hash_equals( $signature, $consumer_signature ) ) { // @codingStandardsIgnoreLine
			return new WP_Error( 'woocommerce_rest_authentication_error', __( 'Invalid signature - provided signature does not match.', 'woocommerce' ), array( 'status' => 401 ) );
		}

		return true;
	}

	/**
	 * Creates an array of urlencoded strings out of each array key/value pairs.
	 *
	 * @param  array  $params       Array of parameters to convert.
	 * @param  array  $query_params Array to extend.
	 * @param  string $key          Optional Array key to append.
	 * @return string               Array of urlencoded strings.
	 */
	private function join_with_equals_sign( $params, $query_params = array(), $key = '' ) {
		foreach ( $params as $param_key => $param_value ) {
			if ( $key ) {
				$param_key = $key . '%5B' . $param_key . '%5D'; // Handle multi-dimensional array.
			}

			if ( is_array( $param_value ) ) {
				$query_params = $this->join_with_equals_sign( $param_value, $query_params, $param_key );
			} else {
				$string         = $param_key . '=' . $param_value; // Join with equals sign.
				$query_params[] = wc_rest_urlencode_rfc3986( $string );
			}
		}

		return $query_params;
	}

	/**
	 * Normalize each parameter by assuming each parameter may have already been
	 * encoded, so attempt to decode, and then re-encode according to RFC 3986.
	 *
	 * Note both the key and value is normalized so a filter param like:
	 *
	 * 'filter[period]' => 'week'
	 *
	 * is encoded to:
	 *
	 * 'filter%255Bperiod%255D' => 'week'
	 *
	 * This conforms to the OAuth 1.0a spec which indicates the entire query string
	 * should be URL encoded.
	 *
	 * @see rawurlencode()
	 * @param array $parameters Un-normalized parameters.
	 * @return array Normalized parameters.
	 */
	private function normalize_parameters( $parameters ) {
		$keys       = wc_rest_urlencode_rfc3986( array_keys( $parameters ) );
		$values     = wc_rest_urlencode_rfc3986( array_values( $parameters ) );
		$parameters = array_combine( $keys, $values );

		return $parameters;
	}

	/**
	 * Verify that the timestamp and nonce provided with the request are valid. This prevents replay attacks where
	 * an attacker could attempt to re-send an intercepted request at a later time.
	 *
	 * - A timestamp is valid if it is within 15 minutes of now.
	 * - A nonce is valid if it has not been used within the last 15 minutes.
	 *
	 * @param stdClass $user      User data.
	 * @param int      $timestamp The unix timestamp for when the request was made.
	 * @param string   $nonce     A unique (for the given user) 32 alphanumeric string, consumer-generated.
	 * @return bool|WP_Error
	 */
	private function check_oauth_timestamp_and_nonce( $user, $timestamp, $nonce ) {
		global $wpdb;

		$valid_window = 15 * 60; // 15 minute window.

		if ( ( $timestamp < time() - $valid_window ) || ( $timestamp > time() + $valid_window ) ) {
			return new WP_Error( 'woocommerce_rest_authentication_error', __( 'Invalid timestamp.', 'woocommerce' ), array( 'status' => 401 ) );
		}

		$used_nonces = maybe_unserialize( $user->nonces );

		if ( empty( $used_nonces ) ) {
			$used_nonces = array();
		}

		if ( in_array( $nonce, $used_nonces, true ) ) {
			return new WP_Error( 'woocommerce_rest_authentication_error', __( 'Invalid nonce - nonce has already been used.', 'woocommerce' ), array( 'status' => 401 ) );
		}

		$used_nonces[ $timestamp ] = $nonce;

		// Remove expired nonces.
		foreach ( $used_nonces as $nonce_timestamp => $nonce ) {
			if ( $nonce_timestamp < ( time() - $valid_window ) ) {
				unset( $used_nonces[ $nonce_timestamp ] );
			}
		}

		$used_nonces = maybe_serialize( $used_nonces );

		$wpdb->update(
			$wpdb->prefix . 'woocommerce_api_keys',
			array( 'nonces' => $used_nonces ),
			array( 'key_id' => $user->key_id ),
			array( '%s' ),
			array( '%d' )
		);

		return true;
	}

	/**
	 * Return the user data for the given consumer_key.
	 *
	 * @param string $consumer_key Consumer key.
	 * @return array
	 */
	private function get_user_data_by_consumer_key( $consumer_key ) {
		global $wpdb;

		$consumer_key = wc_api_hash( sanitize_text_field( $consumer_key ) );
		$user         = $wpdb->get_row(
			$wpdb->prepare(
				"
			SELECT key_id, user_id, permissions, consumer_key, consumer_secret, nonces
			FROM {$wpdb->prefix}woocommerce_api_keys
			WHERE consumer_key = %s
		",
				$consumer_key
			)
		);

		return $user;
	}

	/**
	 * Check that the API keys provided have the proper key-specific permissions to either read or write API resources.
	 *
	 * @param string $method Request method.
	 * @return bool|WP_Error
	 */
	private function check_permissions( $method ) {
		$permissions = $this->user->permissions;

		switch ( $method ) {
			case 'HEAD':
			case 'GET':
				if ( 'read' !== $permissions && 'read_write' !== $permissions ) {
					return new WP_Error( 'woocommerce_rest_authentication_error', __( 'The API key provided does not have read permissions.', 'woocommerce' ), array( 'status' => 401 ) );
				}
				break;
			case 'POST':
			case 'PUT':
			case 'PATCH':
			case 'DELETE':
				if ( 'write' !== $permissions && 'read_write' !== $permissions ) {
					return new WP_Error( 'woocommerce_rest_authentication_error', __( 'The API key provided does not have write permissions.', 'woocommerce' ), array( 'status' => 401 ) );
				}
				break;
			case 'OPTIONS':
				return true;

			default:
				return new WP_Error( 'woocommerce_rest_authentication_error', __( 'Unknown request method.', 'woocommerce' ), array( 'status' => 401 ) );
		}

		return true;
	}

	/**
	 * Updated API Key last access datetime.
	 */
	private function update_last_access() {
		global $wpdb;

		/**
		 * This filter enables the exclusion of the most recent access time from being logged for REST API calls.
		 *
		 * @param bool $result  Default value.
		 * @param int  $key_id  Key ID associated with REST API request.
		 * @param int  $user_id User ID associated with REST API request.
		 *
		 * @since 7.7.0
		 */
		if ( apply_filters( 'woocommerce_disable_rest_api_access_log', false, $this->user->key_id, $this->user->user_id ) ) {
			return;
		}

		$wpdb->update(
			$wpdb->prefix . 'woocommerce_api_keys',
			array( 'last_access' => current_time( 'mysql' ) ),
			array( 'key_id' => $this->user->key_id ),
			array( '%s' ),
			array( '%d' )
		);
	}

	/**
	 * If the consumer_key and consumer_secret $_GET parameters are NOT provided
	 * and the Basic auth headers are either not present or the consumer secret does not match the consumer
	 * key provided, then return the correct Basic headers and an error message.
	 *
	 * @param WP_REST_Response $response Current response being served.
	 * @return WP_REST_Response
	 */
	public function send_unauthorized_headers( $response ) {
		if ( is_wp_error( $this->get_error() ) && 'basic_auth' === $this->auth_method ) {
			$auth_message = __( 'WooCommerce API. Use a consumer key in the username field and a consumer secret in the password field.', 'woocommerce' );
			$response->header( 'WWW-Authenticate', 'Basic realm="' . $auth_message . '"', true );
		}

		return $response;
	}

	/**
	 * Check for user permissions and register last access.
	 *
	 * @param mixed           $result  Response to replace the requested version with.
	 * @param WP_REST_Server  $server  Server instance.
	 * @param WP_REST_Request $request Request used to generate the response.
	 * @return mixed
	 */
	public function check_user_permissions( $result, $server, $request ) {
		if ( $this->user ) {
			// Check API Key permissions.
			$allowed = $this->check_permissions( $request->get_method() );
			if ( is_wp_error( $allowed ) ) {
				return $allowed;
			}

			// Register last access.
			$this->update_last_access();
		}

		return $result;
	}
}

new WC_REST_Authentication();

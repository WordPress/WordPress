<?php

/**
 * High level, generic, wrapper for interacting with a external, 3rd-party API.
 *
 * @since   1.1.0
 *
 * @package ET\Core\API
 */
abstract class ET_Core_API_Service {
	/**
	 * URL to request an OAuth access token.
	 *
	 * @since 1.1.0
	 * @var   string
	 */
	public $ACCESS_TOKEN_URL;

	/**
	 * URL to authorize an application using OAuth.
	 *
	 * @since 1.1.0
	 * @var   string
	 */
	public $AUTHORIZATION_URL;

	/**
	 * General failure message (translated & escaped).
	 *
	 * @since 1.1.0
	 * @var   string
	 */
	public $FAILURE_MESSAGE;

	/**
	 * URL to request an OAuth request token.
	 *
	 * @since 1.1.0
	 * @var   string
	 */
	public $REQUEST_TOKEN_URL;

	/**
	 * Callback URL for OAuth Authorization.
	 *
	 * @since 1.1.0
	 * @var   string
	 */
	public $REDIRECT_URL;

	/**
	 * The base url for the service.
	 *
	 * @since 1.1.0
	 * @var   string
	 */
	public $BASE_URL;

	/**
	 * Instance of the OAuth wrapper class initialized for this service.
	 *
	 * @since 1.1.0
	 * @var   ET_Core_API_OAuthHelper
	 */
	public $OAuth_Helper;

	/**
	 * The form fields (shown in the dashboard) for the service account.
	 *
	 * @since 1.1.0
	 * @var   array
	 */
	public $account_fields;

	/**
	 * Each service can have multiple sets of credentials (accounts). This identifies which
	 * account an instance of this class will provide access to.
	 *
	 * @since 1.1.0
	 * @var   string
	 */
	public $account_name;

	/**
	 * Custom HTTP headers that should be added to all requests made to this service's API.
	 *
	 * @since 1.1.0
	 * @var   array
	 */
	public $custom_headers;

	/**
	 * The service's data. Typically this will be IDs, tokens, secrets, etc used for API authentication.
	 *
	 * @since 1.1.0
	 * @var   string[]
	 */
	public $data;

	/**
	 * The mapping of the key names we use to store the service's data to the key names used by the service's API.
	 *
	 * @since 1.1.0
	 * @var   string[]
	 */
	public $data_keys;

	/**
	 * An instance of our HTTP Interface (utility class).
	 *
	 * @since 1.1.0
	 * @var ET_Core_HTTPInterface
	 */
	public $http;

	/**
	 * If service uses HTTP Basic Auth, an array with details needed to generate the auth header, false otherwise.
	 *
	 * @since 1.1.0
	 * @var   bool|array {
	 *     Details needed to generate the HTTP Auth header.
	 *
	 *     @type string $username The data key name who's value should be used as the username, or a value to use instead.
	 *     @type string $password The data key name who's value should be used as the password, or a value to use instead.
	 * }
	 */
	public $http_auth = false;

	/**
	 * The service's proper name (will be shown in the UI).
	 *
	 * @since 1.1.0
	 * @var string
	 */
	public $name;

	/**
	 * The OAuth version (if the service uses OAuth).
	 *
	 * @since 1.1.0
	 * @var   string
	 */
	public $oauth_version;

	/**
	 * The OAuth verifier key (if the service uses OAuth along with verifier keys).
	 *
	 * @since 1.1.0
	 * @var   string
	 */
	public $oauth_verifier;

	/**
	 * The name and version of the theme/plugin that created this class instance.
	 * Should be formatted like this: `Divi/3.0.23`.
	 *
	 * @since 1.1.0
	 * @var   string
	 */
	public $owner;

	/**
	 * Convenience accessor for {@link self::http->request}
	 *
	 * @since 1.1.0
	 * @var \ET_Core_HTTPRequest?
	 */
	public $request;

	/**
	 * Convenience accessor for {@link self::http->response}
	 *
	 * @since 1.1.0
	 * @var \ET_Core_HTTPResponse?
	 */
	public $response;

	/**
	 * For services that return JSON responses, this is the top-level/root key for the returned data.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	public $response_data_key;

	/**
	 * The service type (email, social, etc).
	 *
	 * @since 1.1.0
	 * @var   string
	 */
	public $service_type;

	/**
	 * The slug for this service (not shown in the UI).
	 *
	 * @since 1.1.0
	 * @var   string
	 */
	public $slug;

	/**
	 * Whether or not the service uses OAuth.
	 *
	 * @since 1.1.0
	 * @var   bool
	 */
	public $uses_oauth;

	/**
	 * ET_Core_API_Service constructor.
	 *
	 * @since 1.1.0
	 *
	 * @param string $owner        {@see self::owner}
	 * @param string $account_name The name of the service account that the instance will provide access to.
	 * @param string $api_key      The api key for the account. Optional (can be set after instantiation).
	 */
	public function __construct( $owner = 'ET_Core/1.1.0', $account_name = '', $api_key = '' ) {
		$this->account_name   = sanitize_text_field( $account_name );
		$this->owner          = sanitize_text_field( $owner );
		$this->account_fields = $this->get_account_fields();

		$this->data           = $this->_get_data();
		$this->data_keys      = $this->get_data_keymap();
		$this->oauth_verifier = '';

		$this->http       = new ET_Core_HTTPInterface( $this->owner );
		$this->data_utils = new ET_Core_Data_Utils();

		$this->FAILURE_MESSAGE  = esc_html__( 'API request failed, please try again.', 'et_core' );
		$this->API_KEY_REQUIRED = esc_html__( 'API request failed. API Key is required.', 'et_core' );

		if ( '' !== $api_key ) {
			$this->data['api_key'] = sanitize_text_field( $api_key );
			$this->save_data();
		}

		if ( $this->uses_oauth && $this->is_authenticated() ) {
			$this->_initialize_oauth_helper();
		}
	}

	/**
	 * Generates a HTTP Basic Auth header and adds it to the current request object. Uses the value
	 * of {@link self::http_auth} to determine the correct values to use for the username and password.
	 */
	protected function _add_http_auth_header_to_request() {
		$username_key = $this->http_auth['username'];
		$password_key = $this->http_auth['password'];

		$username = isset( $this->data[ $username_key ] ) ? $this->data[ $username_key ] : $username_key;
		$password = isset( $this->data[ $password_key ] ) ? $this->data[ $password_key ] : $password_key;

		$this->request->HEADERS['Authorization'] = 'Basic ' . base64_encode( "{$username}:{$password}" );
	}

	/**
	 * Exchange a request/verifier token for an access token. This is the last step in the OAuth authorization process.
	 * If successful, the access token will be saved and used for future calls to the API.
	 *
	 * @return bool Whether or not authentication was successful.
	 */
	private function _do_oauth_access_token_request() {
		if ( ! $this->_initialize_oauth_helper() ) {
			return false;
		}

		$args = array();

		if ( '' !== $this->oauth_verifier ) {
			$args['oauth_verifier'] = $this->oauth_verifier;
		}

		$this->request = $this->http->request = $this->OAuth_Helper->prepare_access_token_request( $args );

		$this->request->HEADERS['Content-Type'] = 'application/x-www-form-urlencoded';

		$this->http->make_remote_request();
		$this->response = $this->http->response;

		if ( $this->response->ERROR ) {
			return false;
		}

		$this->OAuth_Helper->process_authentication_response( $this->response, $this->http->expects_json );

		if ( null === $this->OAuth_Helper->token ) {
			return false;
		}

		$this->data['access_key']    = $this->OAuth_Helper->token->key;
		$this->data['access_secret'] = $this->OAuth_Helper->token->secret;
		$this->data['is_authorized'] = true;

		if ( ! empty( $this->OAuth_Helper->token->refresh_token ) ) {
			$this->data['refresh_token'] = $this->OAuth_Helper->token->refresh_token;
		}

		return true;
	}

	/**
	 * The service's private data.
	 *
	 * @see      self::$_data
	 * @internal
	 * @since    1.1.0
	 *
	 * @return array
	 */
	protected function _get_data() {
		return (array) get_option( "et_core_api_{$this->service_type}_options" );
	}

	/**
	 * Initializes {@link self::OAuth_Helper}
	 *
	 * @return bool `true` if successful, `false` otherwise.
	 */
	protected function _initialize_oauth_helper() {
		if ( null !== $this->OAuth_Helper ) {
			return true;
		}

		$urls = array(
			'access_token_url'  => $this->ACCESS_TOKEN_URL,
			'request_token_url' => $this->REQUEST_TOKEN_URL,
			'authorization_url' => $this->AUTHORIZATION_URL,
			'redirect_url'      => $this->REDIRECT_URL,
		);

		$this->OAuth_Helper = new ET_Core_API_OAuthHelper( $this->data, $urls, $this->owner );

		return null !== $this->OAuth_Helper;
	}

	/**
	 * Returns the currently known custom fields for this service.
	 *
	 * @return array
	 */
	protected function _get_custom_fields() {
		return empty( $this->data['custom_fields'] ) ? array() : $this->data['custom_fields'];
	}

	/**
	 * Initiate OAuth Authorization Flow
	 *
	 * @return array|bool
	 */
	public function authenticate() {
		if ( '1.0a' === $this->oauth_version || ( '2.0' === $this->oauth_version && ! empty( $_GET['code'] ) ) ) {
			$authenticated = $this->_do_oauth_access_token_request();

			if ( $authenticated ) {
				$this->save_data();
				return true;
			}
		} else if ( '2.0' === $this->oauth_version ) {
			$args = array(
				'client_id'     => $this->data['api_key'],
				'response_type' => 'code',
				'state'         => rawurlencode( "ET_Core|{$this->name}|{$this->account_name}" ),
				'redirect_uri'  => $this->REDIRECT_URL,
			);

			$this->save_data();

			return array( 'redirect_url' => add_query_arg( $args, $this->AUTHORIZATION_URL ) );
		}

		return false;
	}

	/**
	 * Remove the service account from the database. This cannot be undone. The instance
	 * will no longer be usable after a call to this method.
	 *
	 * @since  1.1.0
	 */
	abstract public function delete();

	/**
	 * Get the form fields to show in the WP Dashboard for this service's accounts.
	 *
	 * @since  1.1.0
	 *
	 * @return array
	 */
	abstract public function get_account_fields();

	/**
	 * Get an array that maps our data keys to those returned by the 3rd-party service's API.
	 *
	 * @since    1.1.0
	 *
	 * @param array  $keymap            A mapping of our data key addresses to those of the service, organized by type/category.
	 * @param string $custom_fields_key The key under which custom fields are stored.
	 *
	 * @return array[] {
	 *
	 *     @type array $key_type {
	 *
	 *         @type string $our_key_address The corresponding key address on the service.
	 *         ...
	 *    }
	 *    ...
	 * }
	 */
	abstract public function get_data_keymap( $keymap = array(), $custom_fields_key = '' );

	/**
	 * Get error message for a response that has an ERROR status. If possible the provider's
	 * error message will be returned. Otherwise the HTTP error status description will be returned.
	 *
	 * @return string
	 */
	public function get_error_message() {
		if ( ! empty( $this->data_keys['error'] ) ) {
			$data = $this->transform_data_to_our_format( $this->response->DATA, 'error' );
			return isset( $data['error_message'] ) ? $data['error_message'] : '';
		}

		return $this->response->ERROR_MESSAGE;
	}

	/**
	 * Whether or not the current account has been authenticated with the service's API.
	 *
	 * @return bool
	 */
	public function is_authenticated() {
		return isset( $this->data['is_authorized'] ) && in_array( $this->data['is_authorized'], array( true, 'true' ) );
	}

	/**
	 * Makes a remote request using the current {@link self::http->request}. Automatically
	 * handles custom headers and OAuth when applicable.
	 */
	public function make_remote_request() {
		if ( ! empty( $this->custom_headers ) ) {
			$this->http->request->HEADERS = array_merge( $this->http->request->HEADERS, $this->custom_headers );
		}

		if ( $this->uses_oauth ) {
			$oauth2 = '2.0' === $this->oauth_version;
			$this->http->request = $this->OAuth_Helper->prepare_oauth_request( $this->http->request, $oauth2 );
		} else if ( $this->http_auth ) {
			$this->_add_http_auth_header_to_request();
		}

		$this->request = $this->http->request;

		$this->http->make_remote_request();

		$this->response = $this->http->response;
	}

	/**
	 * Convenience accessor for {@link self::http->prepare_request()}
	 *
	 * @param string $url
	 * @param string $method
	 * @param bool   $is_auth
	 * @param mixed  $body
	 * @param bool   $json_body
	 * @param bool   $ssl_verify
	 */
	public function prepare_request( $url, $method = 'GET', $is_auth = false, $body = null, $json_body = false, $ssl_verify = true ) {
		$this->http->prepare_request( $url, $method, $is_auth, $body, $json_body, $ssl_verify );
		$this->request = $this->http->request;
	}

	/**
	 * Save this service's data to the database.
	 *
	 * @return mixed
	 */
	abstract public function save_data();

	/**
	 * Set the account name for the instance. Changing the accounts name affects the
	 * value of {@link self::data}.
	 *
	 * @param string $name
	 */
	abstract public function set_account_name( $name );

	/**
	 * Transforms an array from our data format to that of the service provider.
	 *
	 * @param array  $data         The data to be transformed.
	 * @param string $key_type     The type of data. This corresponds to the key_type in {@link self::data_keys}.
	 * @param array  $exclude_keys Keys that should be excluded from the transformed data.
	 *
	 * @return array
	 */
	public function transform_data_to_our_format( $data = array(), $key_type, $exclude_keys = array() ) {
		if ( ! isset( $this->data_keys[ $key_type ] ) ) {
			return array();
		}

		$data_keys_mapping = $this->data_keys[ $key_type ];

		return $this->data_utils->transform_data_to( 'our_data', $data, $data_keys_mapping, $exclude_keys );
	}

	/**
	 * Transforms an array from the service provider's data format to our data format.
	 *
	 * @param array  $data         The data to be transformed.
	 * @param string $key_type     The type of data. This corresponds to the key_type in {@link self::data_keys}.
	 * @param array  $exclude_keys Keys that should be excluded from the transformed data.
	 *
	 * @return array
	 */
	public function transform_data_to_provider_format( $data = array(), $key_type, $exclude_keys = array() ) {
		$data_keys_mapping = $this->data_keys[ $key_type ];

		return $this->data_utils->transform_data_to( 'their_data', $data, $data_keys_mapping, $exclude_keys );
	}
}

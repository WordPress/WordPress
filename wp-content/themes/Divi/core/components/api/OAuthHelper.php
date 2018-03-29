<?php

/**
 * Wrapper for the ET_Core_OAuth library.
 *
 * @since   1.1.0
 * @package ET\Core\API
 *
 * @property string consumer_key
 * @property string consumer_secret
 * @property string access_token
 * @property string access_token_secret
 */
class ET_Core_API_OAuthHelper {

	/**
	 * URL for access token requests.
	 *
	 * @since 1.1.0
	 * @var   string
	 */
	public $ACCESS_TOKEN_URL;

	/**
	 * URL for authorizing applications.
	 *
	 * @since 1.1.0
	 * @var   string
	 */
	public $AUTHORIZATION_URL;

	/**
	 * URL for request token requests.
	 *
	 * @since 1.1.0
	 * @var   string
	 */
	public $REQUEST_TOKEN_URL;

	/**
	 * The OAuth2 bearer for this instance. This is used as the value of the HTTP
	 * `Authorization` header. The format is: `Bearer {$access_token}`.
	 *
	 * @since 1.1.0
	 * @var   string
	 */
	private $bearer = null;

	/**
	 * The OAuth Consumer object for this instance.
	 *
	 * @since 1.1.0
	 * @var   ET_Core_LIB_OAuthConsumer
	 */
	public $consumer = null;

	/**
	 * The OAuth Token object for this instance.
	 *
	 * @since 1.1.0
	 * @var   ET_Core_LIB_OAuthToken
	 */
	public $token = null;

	/**
	 * ET_Core_API_OAuth_Helper constructor.
	 *
	 * @since 1.1.0
	 *
	 * @param array $data {
	 *     The consumer key/secret and access token/secret.
	 *
	 *     @type string $consumer_key      Consumer key. Required.
	 *     @type string $consumer_secret   Consumer secret. Required.
	 *     @type string $access_token      Previously obtained access token. Optional.
	 *     @type string $access_secret     Previously obtained access secret. Optional.
	 * }
	 * @param array $urls {
	 *     The service's OAuth endpoints.
	 *
	 *     @type string $request_token_url URL for request tokens. Required.
	 *     @type string $authorization_url URL for authorizations. Optional.
	 *     @type string $access_token_url  URL for access tokens. Required.
	 * }
	 */
	public function __construct( array $data, array $urls, $owner ) {
		$this->_initialize( $data, $urls );

		$this->sha1_method = new ET_Core_LIB_OAuthHMACSHA1();
		$this->consumer    = new ET_Core_LIB_OAuthConsumer( $this->consumer_key, $this->consumer_secret );

		if ( '' !== $this->access_token && '' !== $this->access_token_secret ) {
			$this->token = new ET_Core_LIB_OAuthToken( $this->access_token, $this->access_token_secret );
		} else if ( empty( $this->access_token ) && ! empty( $this->access_token_secret ) ) {
			$this->bearer = "Bearer {$this->access_token_secret}";
		}
	}

	/**
	 * @internal
	 *
	 * @param array $data {@see self::__construct()}
	 * @param array $urls {@see self::__construct()}
	 */
	private function _initialize( $data, $urls ) {
		$this->consumer_key        = isset( $data['consumer_key'] ) ? $data['consumer_key'] : '';
		$this->consumer_secret     = isset( $data['consumer_secret'] ) ? $data['consumer_secret'] : '';
		$this->access_token        = isset( $data['access_key'] ) ? $data['access_key'] : '';
		$this->access_token_secret = isset( $data['access_secret'] ) ? $data['access_secret'] : '';

		$this->REQUEST_TOKEN_URL = $urls['request_token_url'];
		$this->AUTHORIZATION_URL = $urls['authorization_url'];
		$this->ACCESS_TOKEN_URL  = $urls['access_token_url'];
		$this->REDIRECT_URL      = isset( $urls['redirect_url'] ) ? $urls['redirect_url'] : '';
	}

	protected function _get_oauth2_parameters( $args ) {
		return wp_parse_args( $args, array(
			'grant_type'    => 'authorization_code',
			'code'          => $_GET['code'],
			'client_id'     => $this->consumer_key,
			'client_secret' => $this->consumer_secret,
			'redirect_uri'  => $this->REDIRECT_URL,
		) );
	}

	/**
	 * @param \ET_Core_HTTPRequest $request
	 *
	 * @return \ET_Core_HTTPRequest
	 */
	protected function _prepare_oauth_request( $request ) {
		$parameters = array();

		if ( is_array( $request->BODY ) && ! empty( $request->BODY ) ) {
			$parameters = $request->BODY;
		}

		$oauth_request = ET_Core_LIB_OAuthRequest::from_consumer_and_token(
			$this->consumer,
			$this->token,
			$request->METHOD,
			$request->URL,
			$parameters
		);

		$oauth_request->sign_request( $this->sha1_method, $this->consumer, $this->token );

		if ( 'GET' === $request->METHOD ) {
			$request->URL = $oauth_request->to_url();
		} else if ( 'POST' === $request->METHOD ) {
			$request->URL  = $request->JSON_BODY ? $oauth_request->to_url() : $oauth_request->get_normalized_http_url();
			$request->BODY = $oauth_request->to_post_data( $request->JSON_BODY );
		}

		return $request;
	}

	/**
	 * @param \ET_Core_HTTPRequest $request
	 *
	 * @return \ET_Core_HTTPRequest
	 */
	protected function _prepare_oauth2_request( $request ) {
		if ( null !== $this->bearer ) {
			$request->HEADERS['Authorization'] = $this->bearer;
		}

		if ( $request->JSON_BODY ) {
			return $request;
		}

		if ( is_array( $request->BODY ) && ! array_key_exists( 'code', $request->BODY ) ) {
			$request->URL  = add_query_arg( $request->BODY, $request->URL );
			$request->BODY = null;
		}

		return $request;
	}

	/**
	 * Finish the OAuth2 authorization process if needed.
	 */
	public static function finish_oauth2_authorization() {
		if ( ! isset( $_GET['state'] ) || 0 !== strpos( $_GET['state'], 'ET_Core' ) ) {
			return;
		}

		list( $_, $name, $account ) = explode( '|', rawurldecode( $_GET['state'] ) );

		if ( '' === $name || '' === $account ) {
			return;
		}

		$providers = et_core_api_email_providers();
		$provider  = $providers->get( $name, $account, 'ET_Core' );

		if ( false === $provider ) {
			return;
		}

		$result = $provider->fetch_subscriber_lists();

		// Display the authorization results
		echo ET_Bloom::generate_modal_warning( $result );
	}

	/**
	 * Prepare a request for an access token.
	 *
	 * @param array $args {
	 *     For OAuth 1.0 & 1.0a:
	 *
	 *       @type string $verifier OAuth verifier token. Optional.
	 *
	 *     For OAuth 2.0:
	 *
	 *       @type string $code         The code returned when the user was redirected back to their dashboard.
	 *       @type string $grant_type   The desired grant type, as per the OAuth 2.0 spec.
	 *       @type string $redirect_uri The redirect URL from the original authorization request.
	 * }
	 *
	 * @return ET_Core_HTTPRequest
	 */
	public function prepare_access_token_request( $args = array() ) {
		$oauth2        = ! empty( $_GET['code'] );
		$request       = new ET_Core_HTTPRequest( $this->ACCESS_TOKEN_URL, 'POST', '', true );
		$request->BODY = $oauth2 ? $this->_get_oauth2_parameters( $args ) : $args;

		return $this->prepare_oauth_request( $request, $oauth2 );
	}

	/**
	 * Prepare an OAuth 1.0a or 2.0 request.
	 *
	 * @param ET_Core_HTTPRequest $request
	 * @param bool                $oauth2
	 *
	 * @return \ET_Core_HTTPRequest
	 */
	function prepare_oauth_request( $request, $oauth2 = false ) {
		return $oauth2 ? $this->_prepare_oauth2_request( $request ) : $this->_prepare_oauth_request( $request );
	}

	/**
	 * Process a response to an OAuth access token request and retrieve the access token if auth was successful.
	 *
	 * @param \ET_Core_HTTPResponse $response
	 */
	public function process_authentication_response( $response, $json = true ) {
		if ( $response->ERROR ) {
			return;
		}

		$response = $json ? $response->DATA : wp_parse_args( $response->DATA );

		if ( isset( $response['oauth_token'], $response['oauth_token_secret'] ) ) {
			// OAuth 1.0a
			$token       = sanitize_text_field( $response['oauth_token'] );
			$secret      = sanitize_text_field( $response['oauth_token_secret'] );
			$this->token = new ET_Core_LIB_OAuthToken( $token, $secret );
		} else if ( isset( $response['access_token'], $response['refresh_token'] ) ) {
			// OAuth 2.0
			$this->token                = new ET_Core_LIB_OAuthToken( '', sanitize_text_field( $response['access_token'] ) );
			$this->token->refresh_token = sanitize_text_field( $response['refresh_token'] );
		}
	}
}

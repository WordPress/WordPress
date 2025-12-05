<?php

namespace Yoast\WP\SEO\Config;

use Exception;
use Yoast\WP\SEO\Exceptions\OAuth\Authentication_Failed_Exception;
use Yoast\WP\SEO\Exceptions\OAuth\Tokens\Empty_Property_Exception;
use Yoast\WP\SEO\Exceptions\OAuth\Tokens\Empty_Token_Exception;
use Yoast\WP\SEO\Exceptions\OAuth\Tokens\Failed_Storage_Exception;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Values\OAuth\OAuth_Token;
use YoastSEO_Vendor\League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use YoastSEO_Vendor\League\OAuth2\Client\Provider\GenericProvider;

/**
 * Class OAuth_Client
 */
abstract class OAuth_Client {

	/**
	 * The option's key.
	 *
	 * @var string
	 */
	protected $token_option = null;

	/**
	 * The provider.
	 *
	 * @var Wincher_PKCE_Provider|GenericProvider
	 */
	protected $provider;

	/**
	 * The options helper.
	 *
	 * @var Options_Helper
	 */
	protected $options_helper;

	/**
	 * The token.
	 *
	 * @var OAuth_Token|null
	 */
	protected $token = null;

	/**
	 * OAuth_Client constructor.
	 *
	 * @param string                                $token_option   The option's name to save the token as.
	 * @param Wincher_PKCE_Provider|GenericProvider $provider       The provider.
	 * @param Options_Helper                        $options_helper The Options_Helper instance.
	 *
	 * @throws Empty_Property_Exception Exception thrown if a token property is empty.
	 */
	public function __construct(
		$token_option,
		$provider,
		Options_Helper $options_helper
	) {
		$this->provider       = $provider;
		$this->token_option   = $token_option;
		$this->options_helper = $options_helper;

		$tokens = $this->options_helper->get( $this->token_option );

		if ( ! empty( $tokens ) ) {
			$this->token = new OAuth_Token(
				$tokens['access_token'],
				$tokens['refresh_token'],
				$tokens['expires'],
				$tokens['has_expired'],
				$tokens['created_at'],
				( $tokens['error_count'] ?? 0 )
			);
		}
	}

	/**
	 * Requests the access token and refresh token based on the passed code.
	 *
	 * @param string $code The code to send.
	 *
	 * @return OAuth_Token The requested tokens.
	 *
	 * @throws Authentication_Failed_Exception Exception thrown if authentication has failed.
	 */
	public function request_tokens( $code ) {
		try {
			$response = $this->provider
				->getAccessToken(
					'authorization_code',
					[
						'code' => $code,
					]
				);

			$token = OAuth_Token::from_response( $response );

			return $this->store_token( $token );
		} catch ( Exception $exception ) {
			throw new Authentication_Failed_Exception( $exception );
		}
	}

	/**
	 * Performs an authenticated GET request to the desired URL.
	 *
	 * @param string $url     The URL to send the request to.
	 * @param array  $options The options to pass along to the request.
	 *
	 * @return mixed The parsed API response.
	 *
	 * @throws IdentityProviderException Exception thrown if there's something wrong with the identifying data.
	 * @throws Authentication_Failed_Exception Exception thrown if authentication has failed.
	 * @throws Empty_Token_Exception Exception thrown if the token is empty.
	 */
	public function get( $url, $options = [] ) {
		return $this->do_request( 'GET', $url, $options );
	}

	/**
	 * Performs an authenticated POST request to the desired URL.
	 *
	 * @param string $url     The URL to send the request to.
	 * @param mixed  $body    The data to send along in the request's body.
	 * @param array  $options The options to pass along to the request.
	 *
	 * @return mixed The parsed API response.
	 *
	 * @throws IdentityProviderException Exception thrown if there's something wrong with the identifying data.
	 * @throws Authentication_Failed_Exception Exception thrown if authentication has failed.
	 * @throws Empty_Token_Exception Exception thrown if the token is empty.
	 */
	public function post( $url, $body, $options = [] ) {
		$options['body'] = $body;

		return $this->do_request( 'POST', $url, $options );
	}

	/**
	 * Performs an authenticated DELETE request to the desired URL.
	 *
	 * @param string $url     The URL to send the request to.
	 * @param array  $options The options to pass along to the request.
	 *
	 * @return mixed The parsed API response.
	 *
	 * @throws IdentityProviderException Exception thrown if there's something wrong with the identifying data.
	 * @throws Authentication_Failed_Exception Exception thrown if authentication has failed.
	 * @throws Empty_Token_Exception Exception thrown if the token is empty.
	 */
	public function delete( $url, $options = [] ) {
		return $this->do_request( 'DELETE', $url, $options );
	}

	/**
	 * Determines whether there are valid tokens available.
	 *
	 * @return bool Whether there are valid tokens.
	 */
	public function has_valid_tokens() {
		return ! empty( $this->token ) && $this->token->has_expired() === false;
	}

	/**
	 * Gets the stored tokens and refreshes them if they've expired.
	 *
	 * @return OAuth_Token The stored tokens.
	 *
	 * @throws Empty_Token_Exception Exception thrown if the token is empty.
	 */
	public function get_tokens() {
		if ( empty( $this->token ) ) {
			throw new Empty_Token_Exception();
		}

		if ( $this->token->has_expired() ) {
			$this->token = $this->refresh_tokens( $this->token );
		}

		return $this->token;
	}

	/**
	 * Stores the passed token.
	 *
	 * @param OAuth_Token $token The token to store.
	 *
	 * @return OAuth_Token The stored token.
	 *
	 * @throws Failed_Storage_Exception Exception thrown if storing of the token fails.
	 */
	public function store_token( OAuth_Token $token ) {
		$saved = $this->options_helper->set( $this->token_option, $token->to_array() );

		if ( $saved === false ) {
			throw new Failed_Storage_Exception();
		}

		return $token;
	}

	/**
	 * Clears the stored token from storage.
	 *
	 * @return bool The stored token.
	 *
	 * @throws Failed_Storage_Exception Exception thrown if clearing of the token fails.
	 */
	public function clear_token() {
		$saved = $this->options_helper->set( $this->token_option, [] );

		if ( $saved === false ) {
			throw new Failed_Storage_Exception();
		}

		return true;
	}

	/**
	 * Performs the specified request.
	 *
	 * @param string $method  The HTTP method to use.
	 * @param string $url     The URL to send the request to.
	 * @param array  $options The options to pass along to the request.
	 *
	 * @return mixed The parsed API response.
	 *
	 * @throws IdentityProviderException Exception thrown if there's something wrong with the identifying data.
	 * @throws Authentication_Failed_Exception Exception thrown if authentication has failed.
	 * @throws Empty_Token_Exception Exception thrown if the token is empty.
	 */
	protected function do_request( $method, $url, array $options ) {
		$defaults = [
			'headers' => $this->provider->getHeaders( $this->get_tokens()->access_token ),
		];

		$options = \array_merge_recursive( $defaults, $options );

		if ( \array_key_exists( 'params', $options ) ) {
			$url .= '?' . \http_build_query( $options['params'] );
			unset( $options['params'] );
		}

		$request = $this->provider
			->getAuthenticatedRequest( $method, $url, null, $options );

		return $this->provider->getParsedResponse( $request );
	}

	/**
	 * Refreshes the outdated tokens.
	 *
	 * @param OAuth_Token $tokens The outdated tokens.
	 *
	 * @return OAuth_Token The refreshed tokens.
	 *
	 * @throws Authentication_Failed_Exception Exception thrown if authentication has failed.
	 */
	protected function refresh_tokens( OAuth_Token $tokens ) {
		// We do this dance with transients since we need to make sure we don't
		// delete valid tokens because of a race condition when two calls are
		// made simultaneously to this function and refresh token rotation is
		// turned on in the OAuth server. This is not 100% safe, but should at
		// least be much better than not having any lock at all.
		$lock_name = \sprintf( 'lock:%s', $this->token_option );
		$can_lock  = \get_transient( $lock_name ) === false;
		$has_lock  = $can_lock && \set_transient( $lock_name, true, 30 );

		try {
			$new_tokens = $this->provider->getAccessToken(
				'refresh_token',
				[
					'refresh_token' => $tokens->refresh_token,
				]
			);

			$token_obj = OAuth_Token::from_response( $new_tokens );

			return $this->store_token( $token_obj );
		} catch ( Exception $exception ) {
			// If we tried to refresh but the refresh token is invalid, delete
			// the tokens so that we don't try again. Only do this if we got the
			// lock at the beginning of the call.
			if ( $has_lock && $exception->getMessage() === 'invalid_grant' ) {
				try {
					// To protect from race conditions, only do this if we've
					// seen an error before with the same token.
					if ( $tokens->error_count >= 1 ) {
						$this->clear_token();
					}
					else {
						$tokens->error_count += 1;
						$this->store_token( $tokens );
					}
				} catch ( Exception $e ) {  // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
					// Pass through.
				}
			}

			throw new Authentication_Failed_Exception( $exception );
		} finally {
			\delete_transient( $lock_name );
		}
	}
}

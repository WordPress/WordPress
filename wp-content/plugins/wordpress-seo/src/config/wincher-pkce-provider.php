<?php

namespace Yoast\WP\SEO\Config;

use Exception;
use UnexpectedValueException;
use YoastSEO_Vendor\GuzzleHttp\Exception\BadResponseException;
use YoastSEO_Vendor\League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use YoastSEO_Vendor\League\OAuth2\Client\Provider\GenericProvider;
use YoastSEO_Vendor\League\OAuth2\Client\Token\AccessToken;
use YoastSEO_Vendor\League\OAuth2\Client\Token\AccessTokenInterface;
use YoastSEO_Vendor\League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use YoastSEO_Vendor\Psr\Http\Message\RequestInterface;
use YoastSEO_Vendor\Psr\Log\InvalidArgumentException;

/**
 * Class Wincher_PKCE_Provider
 *
 * @codeCoverageIgnore Ignoring as this class is purely a temporary wrapper until https://github.com/thephpleague/oauth2-client/pull/901 is merged.
 *
 * @phpcs:disable WordPress.NamingConventions.ValidVariableName.PropertyNotSnakeCase -- This class extends an external class.
 * @phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- This class extends an external class.
 */
class Wincher_PKCE_Provider extends GenericProvider {

	use BearerAuthorizationTrait;

	/**
	 * The method to use.
	 *
	 * @var string|null
	 */
	protected $pkceMethod = null;

	/**
	 * The PKCE code.
	 *
	 * @var string
	 */
	protected $pkceCode;

	/**
	 * Set the value of the pkceCode parameter.
	 *
	 * When using PKCE this should be set before requesting an access token.
	 *
	 * @param string $pkce_code The value for the pkceCode.
	 * @return self
	 */
	public function setPkceCode( $pkce_code ) {
		$this->pkceCode = $pkce_code;
		return $this;
	}

	/**
	 * Returns the current value of the pkceCode parameter.
	 *
	 * This can be accessed by the redirect handler during authorization.
	 *
	 * @return string
	 */
	public function getPkceCode() {
		return $this->pkceCode;
	}

	/**
	 * Returns a new random string to use as PKCE code_verifier and
	 * hashed as code_challenge parameters in an authorization flow.
	 * Must be between 43 and 128 characters long.
	 *
	 * @param int $length Length of the random string to be generated.
	 *
	 * @return string
	 *
	 * @throws Exception Throws exception if an invalid value is passed to random_bytes.
	 */
	protected function getRandomPkceCode( $length = 64 ) {
		return \substr(
			\strtr(
				// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
				\base64_encode( \random_bytes( $length ) ),
				'+/',
				'-_'
			),
			0,
			$length
		);
	}

	/**
	 * Returns the current value of the pkceMethod parameter.
	 *
	 * @return string|null
	 */
	protected function getPkceMethod() {
		return $this->pkceMethod;
	}

	/**
	 * Returns authorization parameters based on provided options.
	 *
	 * @param array $options The options to use in the authorization parameters.
	 *
	 * @return array The authorization parameters
	 *
	 * @throws InvalidArgumentException Throws exception if an invalid PCKE method is passed in the options.
	 * @throws Exception                When something goes wrong with generating the PKCE code.
	 */
	protected function getAuthorizationParameters( array $options ) {
		if ( empty( $options['state'] ) ) {
			$options['state'] = $this->getRandomState();
		}

		if ( empty( $options['scope'] ) ) {
			$options['scope'] = $this->getDefaultScopes();
		}

		$options += [
			'response_type'   => 'code',
		];

		if ( \is_array( $options['scope'] ) ) {
			$separator        = $this->getScopeSeparator();
			$options['scope'] = \implode( $separator, $options['scope'] );
		}

		// Store the state as it may need to be accessed later on.
		$this->state = $options['state'];

		$pkce_method = $this->getPkceMethod();
		if ( ! empty( $pkce_method ) ) {
			$this->pkceCode = $this->getRandomPkceCode();
			if ( $pkce_method === 'S256' ) {
				$options['code_challenge'] = \trim(
					\strtr(
						// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
						\base64_encode( \hash( 'sha256', $this->pkceCode, true ) ),
						'+/',
						'-_'
					),
					'='
				);
			}
			elseif ( $pkce_method === 'plain' ) {
				$options['code_challenge'] = $this->pkceCode;
			}
			else {
				throw new InvalidArgumentException( 'Unknown PKCE method "' . $pkce_method . '".' );
			}
			$options['code_challenge_method'] = $pkce_method;
		}

		// Business code layer might set a different redirect_uri parameter.
		// Depending on the context, leave it as-is.
		if ( ! isset( $options['redirect_uri'] ) ) {
			$options['redirect_uri'] = $this->redirectUri;
		}

		$options['client_id'] = $this->clientId;

		return $options;
	}

	/**
	 * Requests an access token using a specified grant and option set.
	 *
	 * @param mixed $grant   The grant to request access for.
	 * @param array $options The options to use with the current request.
	 *
	 * @return AccessToken|AccessTokenInterface The access token.
	 *
	 * @throws UnexpectedValueException Exception thrown if the provider response contains errors.
	 */
	public function getAccessToken( $grant, array $options = [] ) {
		$grant = $this->verifyGrant( $grant );

		$params = [
			'client_id'     => $this->clientId,
			'client_secret' => $this->clientSecret,
			'redirect_uri'  => $this->redirectUri,
		];

		if ( ! empty( $this->pkceCode ) ) {
			$params['code_verifier'] = $this->pkceCode;
		}

		$params   = $grant->prepareRequestParameters( $params, $options );
		$request  = $this->getAccessTokenRequest( $params );
		$response = $this->getParsedResponse( $request );

		if ( \is_array( $response ) === false ) {
			throw new UnexpectedValueException(
				'Invalid response received from Authorization Server. Expected JSON.'
			);
		}

		$prepared = $this->prepareAccessTokenResponse( $response );
		$token    = $this->createAccessToken( $prepared, $grant );

		return $token;
	}

	/**
	 * Returns all options that can be configured.
	 *
	 * @return array The configurable options.
	 */
	protected function getConfigurableOptions() {
		return \array_merge(
			$this->getRequiredOptions(),
			[
				'accessTokenMethod',
				'accessTokenResourceOwnerId',
				'scopeSeparator',
				'responseError',
				'responseCode',
				'responseResourceOwnerId',
				'scopes',
				'pkceMethod',
			]
		);
	}

	/**
	 * Parses the request response.
	 *
	 * @param RequestInterface $request The request interface.
	 *
	 * @return array The parsed response.
	 *
	 * @throws IdentityProviderException Exception thrown if there is no proper identity provider.
	 */
	public function getParsedResponse( RequestInterface $request ) {
		try {
			$response = $this->getResponse( $request );
		} catch ( BadResponseException $e ) {
			$response = $e->getResponse();
		}

		$parsed = $this->parseResponse( $response );

		$this->checkResponse( $response, $parsed );

		// We always expect an array from the API except for on DELETE requests.
		// We convert to an array here to prevent problems with array_key_exists on PHP8.
		if ( ! \is_array( $parsed ) ) {
			$parsed = [ 'data' => [] ];
		}

		// Add the response code as this is omitted from Winchers API.
		if ( ! \array_key_exists( 'status', $parsed ) ) {
			$parsed['status'] = $response->getStatusCode();
		}

		return $parsed;
	}
}

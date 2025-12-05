<?php

namespace Yoast\WP\SEO\Config;

use WPSEO_Utils;
use Yoast\WP\SEO\Exceptions\OAuth\Authentication_Failed_Exception;
use Yoast\WP\SEO\Exceptions\OAuth\Tokens\Empty_Property_Exception;
use Yoast\WP\SEO\Exceptions\OAuth\Tokens\Empty_Token_Exception;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Values\OAuth\OAuth_Token;
use Yoast\WP\SEO\Wrappers\WP_Remote_Handler;
use YoastSEO_Vendor\GuzzleHttp\Client;
use YoastSEO_Vendor\League\OAuth2\Client\Provider\Exception\IdentityProviderException;

/**
 * Class Wincher_Client
 */
class Wincher_Client extends OAuth_Client {

	/**
	 * The option's key.
	 */
	public const TOKEN_OPTION = 'wincher_tokens';

	/**
	 * Name of the temporary PKCE cookie.
	 */
	public const PKCE_TRANSIENT_NAME = 'yoast_wincher_pkce';

	/**
	 * The WP_Remote_Handler instance.
	 *
	 * @var WP_Remote_Handler
	 */
	protected $wp_remote_handler;

	/**
	 * Wincher_Client constructor.
	 *
	 * @param Options_Helper    $options_helper    The Options_Helper instance.
	 * @param WP_Remote_Handler $wp_remote_handler The request handler.
	 *
	 * @throws Empty_Property_Exception Exception thrown if a token property is empty.
	 */
	public function __construct( Options_Helper $options_helper, WP_Remote_Handler $wp_remote_handler ) {
		$provider = new Wincher_PKCE_Provider(
			[
				'clientId'                => 'yoast',
				'redirectUri'             => 'https://auth.wincher.com/yoast/setup',
				'urlAuthorize'            => 'https://auth.wincher.com/connect/authorize',
				'urlAccessToken'          => 'https://auth.wincher.com/connect/token',
				'urlResourceOwnerDetails' => 'https://api.wincher.com/beta/user',
				'scopes'                  => [ 'profile', 'account', 'websites:read', 'websites:write', 'offline_access' ],
				'scopeSeparator'          => ' ',
				'pkceMethod'              => 'S256',
			],
			[
				'httpClient' => new Client( [ 'handler' => $wp_remote_handler ] ),
			]
		);

		parent::__construct(
			self::TOKEN_OPTION,
			$provider,
			$options_helper
		);
	}

	/**
	 * Return the authorization URL.
	 *
	 * @return string The authentication URL.
	 */
	public function get_authorization_url() {
		$parsed_site_url = \wp_parse_url( \get_site_url() );

		$url = $this->provider->getAuthorizationUrl(
			[
				'state' => WPSEO_Utils::format_json_encode( [ 'domain' => $parsed_site_url['host'] ] ),
			]
		);

		$pkce_code = $this->provider->getPkceCode();

		// Store a transient value with the PKCE code that we need in order to
		// exchange the returned code for a token after authorization.
		\set_transient( self::PKCE_TRANSIENT_NAME, $pkce_code, \DAY_IN_SECONDS );

		return $url;
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
		$pkce_code = \get_transient( self::PKCE_TRANSIENT_NAME );
		if ( $pkce_code ) {
			$this->provider->setPkceCode( $pkce_code );
		}
		return parent::request_tokens( $code );
	}

	/**
	 * Performs the specified request.
	 *
	 * @codeCoverageIgnore
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
		$options['headers'] = [ 'Content-Type' => 'application/json' ];
		return parent::do_request( $method, $url, $options );
	}
}

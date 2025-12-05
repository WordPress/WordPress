<?php

namespace Yoast\WP\SEO\Config;

use Yoast\WP\SEO\Exceptions\OAuth\Authentication_Failed_Exception;
use Yoast\WP\SEO\Exceptions\OAuth\Tokens\Empty_Property_Exception;
use Yoast\WP\SEO\Exceptions\OAuth\Tokens\Empty_Token_Exception;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Wrappers\WP_Remote_Handler;
use YoastSEO_Vendor\GuzzleHttp\Client;
use YoastSEO_Vendor\League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use YoastSEO_Vendor\League\OAuth2\Client\Provider\GenericProvider;

/**
 * Class SEMrush_Client
 */
class SEMrush_Client extends OAuth_Client {

	/**
	 * The option's key.
	 */
	public const TOKEN_OPTION = 'semrush_tokens';

	/**
	 * SEMrush_Client constructor.
	 *
	 * @param Options_Helper    $options_helper    The Options_Helper instance.
	 * @param WP_Remote_Handler $wp_remote_handler The request handler.
	 *
	 * @throws Empty_Property_Exception Throws when one of the required properties is empty.
	 */
	public function __construct( Options_Helper $options_helper, WP_Remote_Handler $wp_remote_handler ) {
		$provider = new GenericProvider(
			[
				'clientId'                => 'yoast',
				'clientSecret'            => 'YdqNsWwnP4vE54WO1ugThKEjGMxMAHJt',
				'redirectUri'             => 'https://oauth.semrush.com/oauth2/yoast/success',
				'urlAuthorize'            => 'https://oauth.semrush.com/oauth2/authorize',
				'urlAccessToken'          => 'https://oauth.semrush.com/oauth2/access_token',
				'urlResourceOwnerDetails' => 'https://oauth.semrush.com/oauth2/resource',
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
	public function do_request( $method, $url, array $options ) {
		// Add the access token to the GET parameters as well since this is what
		// the SEMRush API expects.
		$options = \array_merge_recursive(
			$options,
			[
				'params' => [
					'access_token' => $this->get_tokens()->access_token,
				],
			]
		);

		return parent::do_request( $method, $url, $options );
	}
}

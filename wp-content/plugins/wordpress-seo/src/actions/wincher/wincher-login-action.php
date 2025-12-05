<?php

namespace Yoast\WP\SEO\Actions\Wincher;

use Yoast\WP\SEO\Config\Wincher_Client;
use Yoast\WP\SEO\Exceptions\OAuth\Authentication_Failed_Exception;
use Yoast\WP\SEO\Helpers\Options_Helper;

/**
 * Class Wincher_Login_Action
 */
class Wincher_Login_Action {

	/**
	 * The Wincher_Client instance.
	 *
	 * @var Wincher_Client
	 */
	protected $client;

	/**
	 * The Options_Helper instance.
	 *
	 * @var Options_Helper
	 */
	protected $options_helper;

	/**
	 * Wincher_Login_Action constructor.
	 *
	 * @param Wincher_Client $client         The API client.
	 * @param Options_Helper $options_helper The options helper.
	 */
	public function __construct( Wincher_Client $client, Options_Helper $options_helper ) {
		$this->client         = $client;
		$this->options_helper = $options_helper;
	}

	/**
	 * Returns the authorization URL.
	 *
	 * @return object The response object.
	 */
	public function get_authorization_url() {
		return (object) [
			'status'    => 200,
			'url'       => $this->client->get_authorization_url(),
		];
	}

	/**
	 * Authenticates with Wincher to request the necessary tokens.
	 *
	 * @param string $code       The authentication code to use to request a token with.
	 * @param string $website_id The website id associated with the code.
	 *
	 * @return object The response object.
	 */
	public function authenticate( $code, $website_id ) {
		// Code has already been validated at this point. No need to do that again.
		try {
			$tokens = $this->client->request_tokens( $code );
			$this->options_helper->set( 'wincher_website_id', $website_id );

			return (object) [
				'tokens' => $tokens->to_array(),
				'status' => 200,
			];
		} catch ( Authentication_Failed_Exception $e ) {
			return $e->get_response();
		}
	}
}

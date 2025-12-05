<?php

namespace Yoast\WP\SEO\Actions\SEMrush;

use Yoast\WP\SEO\Config\SEMrush_Client;
use Yoast\WP\SEO\Exceptions\OAuth\Authentication_Failed_Exception;

/**
 * Class SEMrush_Login_Action
 */
class SEMrush_Login_Action {

	/**
	 * The SEMrush_Client instance.
	 *
	 * @var SEMrush_Client
	 */
	protected $client;

	/**
	 * SEMrush_Login_Action constructor.
	 *
	 * @param SEMrush_Client $client The API client.
	 */
	public function __construct( SEMrush_Client $client ) {
		$this->client = $client;
	}

	/**
	 * Authenticates with SEMrush to request the necessary tokens.
	 *
	 * @param string $code The authentication code to use to request a token with.
	 *
	 * @return object The response object.
	 */
	public function authenticate( $code ) {
		// Code has already been validated at this point. No need to do that again.
		try {
			$tokens = $this->client->request_tokens( $code );

			return (object) [
				'tokens' => $tokens->to_array(),
				'status' => 200,
			];
		} catch ( Authentication_Failed_Exception $e ) {
			return $e->get_response();
		}
	}

	/**
	 * Performs the login request, if necessary.
	 *
	 * @return void
	 */
	public function login() {
		if ( $this->client->has_valid_tokens() ) {
			return;
		}

		// Prompt with login screen.
	}
}

<?php

namespace Yoast\WP\SEO\Conditionals;

use Yoast\WP\SEO\Config\Wincher_Client;

/**
 * Conditional that is only met when the Wincher token is set.
 */
class Wincher_Token_Conditional implements Conditional {

	/**
	 * The Wincher client.
	 *
	 * @var Wincher_Client
	 */
	private $client;

	/**
	 * Wincher_Token_Conditional constructor.
	 *
	 * @param Wincher_Client $client The Wincher client.
	 */
	public function __construct( Wincher_Client $client ) {
		$this->client = $client;
	}

	/**
	 * Returns whether this conditional is met.
	 *
	 * @return bool Whether the conditional is met.
	 */
	public function is_met() {
		return $this->client->has_valid_tokens();
	}
}

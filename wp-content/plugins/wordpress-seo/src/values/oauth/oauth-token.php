<?php

namespace Yoast\WP\SEO\Values\OAuth;

use Yoast\WP\SEO\Exceptions\OAuth\Tokens\Empty_Property_Exception;
use YoastSEO_Vendor\League\OAuth2\Client\Token\AccessTokenInterface;

/**
 * Class OAuth_Token
 */
class OAuth_Token {

	/**
	 * The access token.
	 *
	 * @var string
	 */
	public $access_token;

	/**
	 * The refresh token.
	 *
	 * @var string
	 */
	public $refresh_token;

	/**
	 * The expiration date.
	 *
	 * @var int
	 */
	public $expires;

	/**
	 * Whether or not the token has expired.
	 *
	 * @var bool
	 */
	public $has_expired;

	/**
	 * The timestamp at which the token was created.
	 *
	 * @var int
	 */
	public $created_at;

	/**
	 * The number of times we've gotten an error trying to refresh this token.
	 *
	 * @var int
	 */
	public $error_count;

	/**
	 * OAuth_Token constructor.
	 *
	 * @param string $access_token  The access token.
	 * @param string $refresh_token The refresh token.
	 * @param int    $expires       The date and time at which the token will expire.
	 * @param bool   $has_expired   Whether or not the token has expired.
	 * @param int    $created_at    The timestamp of when the token was created.
	 * @param int    $error_count   The number of times we've gotten an error trying to refresh this token.
	 *
	 * @throws Empty_Property_Exception Exception thrown if a token property is empty.
	 */
	public function __construct( $access_token, $refresh_token, $expires, $has_expired, $created_at, $error_count = 0 ) {

		if ( empty( $access_token ) ) {
			throw new Empty_Property_Exception( 'access_token' );
		}

		$this->access_token = $access_token;

		if ( empty( $refresh_token ) ) {
			throw new Empty_Property_Exception( 'refresh_token' );
		}

		$this->refresh_token = $refresh_token;

		if ( empty( $expires ) ) {
			throw new Empty_Property_Exception( 'expires' );
		}

		$this->expires = $expires;

		if ( $has_expired === null ) {
			throw new Empty_Property_Exception( 'has_expired' );
		}

		$this->has_expired = $has_expired;
		$this->created_at  = $created_at;
		$this->error_count = $error_count;
	}

	/**
	 * Creates a new instance based on the passed response.
	 *
	 * @param AccessTokenInterface $response The response object to create a new instance from.
	 *
	 * @return OAuth_Token The token object.
	 *
	 * @throws Empty_Property_Exception Exception thrown if a token property is empty.
	 */
	public static function from_response( AccessTokenInterface $response ) {
		return new self(
			$response->getToken(),
			$response->getRefreshToken(),
			$response->getExpires(),
			$response->hasExpired(),
			\time()
		);
	}

	/**
	 * Determines whether or not the token has expired.
	 *
	 * @return bool Whether or not the token has expired.
	 */
	public function has_expired() {
		return ( \time() >= $this->expires ) || $this->has_expired === true;
	}

	/**
	 * Converts the object to an array.
	 *
	 * @return array The converted object.
	 */
	public function to_array() {
		return [
			'access_token'  => $this->access_token,
			'refresh_token' => $this->refresh_token,
			'expires'       => $this->expires,
			'has_expired'   => $this->has_expired(),
			'created_at'    => $this->created_at,
			'error_count'   => $this->error_count,
		];
	}
}

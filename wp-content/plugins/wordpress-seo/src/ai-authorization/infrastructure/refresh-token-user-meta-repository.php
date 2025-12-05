<?php

namespace Yoast\WP\SEO\AI_Authorization\Infrastructure;

use RuntimeException;
use Yoast\WP\SEO\Helpers\User_Helper;

/**
 * Class Refresh_Token_Repository
 * Handles the storage and retrieval of refresh tokens for users.
 */
class Refresh_Token_User_Meta_Repository implements Refresh_Token_User_Meta_Repository_Interface {

	/**
	 * The user helper.
	 *
	 * @var User_Helper
	 */
	private $user_helper;

	/**
	 * Refresh_Token_Repository constructor.
	 *
	 * @param User_Helper $user_helper The user helper.
	 */
	public function __construct( User_Helper $user_helper ) {
		$this->user_helper = $user_helper;
	}

	/**
	 * Get the token for a user.
	 *
	 * @param int $user_id The user ID.
	 *
	 * @return string The token data.
	 *
	 * @throws RuntimeException If the token is not found or invalid.
	 */
	public function get_token( int $user_id ): string {
		$refresh_jwt = $this->user_helper->get_meta( $user_id, self::META_KEY, true );
		if ( ! \is_string( $refresh_jwt ) || $refresh_jwt === '' ) {
			throw new RuntimeException( 'Unable to retrieve the refresh token.' );
		}

		return $refresh_jwt;
	}

	/**
	 * Store the token for a user.
	 *
	 * @param int    $user_id The user ID.
	 * @param string $value   The token value.
	 *
	 * @return void
	 */
	public function store_token( int $user_id, string $value ): void {
		$this->user_helper->update_meta(
			$user_id,
			self::META_KEY,
			$value
		); }

	/**
	 * Delete the token for a user.
	 *
	 * @param int $user_id The user ID.
	 *
	 * @return void
	 */
	public function delete_token( int $user_id ): void {
		$this->user_helper->delete_meta( $user_id, self::META_KEY );
	}
}

<?php

namespace Yoast\WP\SEO\Introductions\Infrastructure;

use Yoast\WP\SEO\Helpers\User_Helper;
use Yoast\WP\SEO\Introductions\Domain\Invalid_User_Id_Exception;

/**
 * Stores and retrieves whether the user has seen certain introductions.
 *
 * @makePublic
 */
class Introductions_Seen_Repository {

	public const USER_META_KEY = '_yoast_wpseo_introductions';

	public const DEFAULT_VALUE = [];

	/**
	 * Holds the User_Helper instance.
	 *
	 * @var User_Helper
	 */
	private $user_helper;

	/**
	 * Constructs the class.
	 *
	 * @param User_Helper $user_helper The User_Helper.
	 */
	public function __construct( User_Helper $user_helper ) {
		$this->user_helper = $user_helper;
	}

	/**
	 * Retrieves the introductions.
	 *
	 * @param int $user_id User ID.
	 *
	 * @return array The introductions.
	 *
	 * @throws Invalid_User_Id_Exception If an invalid user ID is supplied.
	 */
	public function get_all_introductions( $user_id ): array {
		$seen_introductions = $this->user_helper->get_meta( $user_id, self::USER_META_KEY, true );
		if ( $seen_introductions === false ) {
			throw new Invalid_User_Id_Exception();
		}

		if ( \is_array( $seen_introductions ) ) {
			return $seen_introductions;
		}

		/**
		 * Why could $value be invalid?
		 * - When the database row does not exist yet, $value can be an empty string.
		 * - Faulty data was stored?
		 */
		return self::DEFAULT_VALUE;
	}

	/**
	 * Sets the introductions.
	 *
	 * @param int   $user_id       The user ID.
	 * @param array $introductions The introductions.
	 *
	 * @return bool True on successful update, false on failure or if the value passed to the function is the same as
	 *              the one that is already in the database.
	 */
	public function set_all_introductions( $user_id, array $introductions ): bool {
		return $this->user_helper->update_meta( $user_id, self::USER_META_KEY, $introductions ) !== false;
	}

	/**
	 * Retrieves whether an introduction is seen.
	 *
	 * @param int    $user_id         User ID.
	 * @param string $introduction_id The introduction ID.
	 *
	 * @return bool Whether the introduction is seen.
	 *
	 * @throws Invalid_User_Id_Exception If an invalid user ID is supplied.
	 */
	public function is_introduction_seen( $user_id, string $introduction_id ): bool {
		$introductions = $this->get_all_introductions( $user_id );

		if ( \array_key_exists( $introduction_id, $introductions ) ) {
			if ( \is_array( $introductions[ $introduction_id ] ) ) {
				return (bool) $introductions[ $introduction_id ]['is_seen'];
			}
			else {
				return (bool) $introductions[ $introduction_id ];
			}
		}

		return false;
	}

	/**
	 * Sets the introduction as seen.
	 *
	 * @param int    $user_id         The user ID.
	 * @param string $introduction_id The introduction ID.
	 * @param bool   $is_seen         Whether the introduction is seen. Defaults to true.
	 *
	 * @return bool False on failure. Not having to update is a success.
	 *
	 * @throws Invalid_User_Id_Exception If an invalid user ID is supplied.
	 */
	public function set_introduction( $user_id, string $introduction_id, bool $is_seen = true ): bool {
		$introductions = $this->get_all_introductions( $user_id );

		// Check if the wanted value is already set.
		if ( \array_key_exists( $introduction_id, $introductions ) ) {
			if ( \is_array( $introductions[ $introduction_id ] ) ) {
				// New format with seen_on timestamp.
				if ( $introductions[ $introduction_id ]['is_seen'] === $is_seen ) {
					return true;
				}
			}
				// Old format with just a boolean.
			elseif ( $introductions[ $introduction_id ] === $is_seen ) {
					return true;
			}
		}

		// If not, set it.
		$introductions[ $introduction_id ] = [
			'is_seen' => $is_seen,
			'seen_on' => ( $is_seen === true ) ? \time() : 0,
		];

		return $this->set_all_introductions( $user_id, $introductions );
	}
}

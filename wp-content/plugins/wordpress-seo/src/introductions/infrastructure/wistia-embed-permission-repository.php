<?php

namespace Yoast\WP\SEO\Introductions\Infrastructure;

use Exception;
use Yoast\WP\SEO\Helpers\User_Helper;

/**
 * Takes care of the get/set in the WP user meta.
 *
 * @makePublic
 */
class Wistia_Embed_Permission_Repository {

	public const USER_META_KEY = '_yoast_wpseo_wistia_embed_permission';

	public const DEFAULT_VALUE = false;

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
	 * Retrieves the current value for a user.
	 *
	 * @param int $user_id User ID.
	 *
	 * @return bool The current value.
	 *
	 * @throws Exception If an invalid user ID is supplied.
	 */
	public function get_value_for_user( $user_id ) {
		$value = $this->user_helper->get_meta( $user_id, self::USER_META_KEY, true );
		if ( $value === false ) {
			throw new Exception( 'Invalid User ID' );
		}

		if ( $value === '0' || $value === '1' ) {
			// The value is stored as a string because otherwise we can not see the difference between false and an invalid user ID.
			return $value === '1';
		}

		/**
		 * Why could $value be invalid?
		 * - When the database row does not exist yet, $value can be an empty string.
		 * - Faulty data was stored?
		 */
		return self::DEFAULT_VALUE;
	}

	/**
	 * Sets the Wistia embed permission value for the current user.
	 *
	 * @param int  $user_id The user ID.
	 * @param bool $value   The value.
	 *
	 * @return bool Whether the update was successful.
	 *
	 * @throws Exception If an invalid user ID is supplied.
	 */
	public function set_value_for_user( $user_id, $value ) {
		// The value is stored as a string because otherwise we can not see the difference between false and an invalid user ID.
		$value_as_string = ( $value === true ) ? '1' : '0';

		// Checking for only false, not interested in not having to update.
		return $this->user_helper->update_meta( $user_id, self::USER_META_KEY, $value_as_string ) !== false;
	}
}

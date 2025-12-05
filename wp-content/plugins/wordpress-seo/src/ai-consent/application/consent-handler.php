<?php

namespace Yoast\WP\SEO\AI_Consent\Application;

use Yoast\WP\SEO\Helpers\User_Helper;

/**
 * Class Consent_Handler
 * Handles the consent given or revoked by the user.
 *
 * @makePublic
 */
class Consent_Handler implements Consent_Handler_Interface {

	/**
	 * Holds the user helper instance.
	 *
	 * @var User_Helper
	 */
	private $user_helper;

	/**
	 * Class constructor.
	 *
	 * @param User_Helper $user_helper The user helper.
	 */
	public function __construct( User_Helper $user_helper ) {
		$this->user_helper = $user_helper;
	}

	/**
	 * Handles consent revoked by deleting the consent user metadata from the database.
	 *
	 * @param int $user_id The user ID.
	 *
	 * @return void
	 */
	public function revoke_consent( int $user_id ) {
		$this->user_helper->delete_meta( $user_id, '_yoast_wpseo_ai_consent' );
	}

	/**
	 * Handles consent granted by adding the consent user metadata to the database.
	 *
	 * @param int $user_id The user ID.
	 *
	 * @return void
	 */
	public function grant_consent( int $user_id ) {
		$this->user_helper->update_meta( $user_id, '_yoast_wpseo_ai_consent', true );
	}
}

<?php

namespace Yoast\WP\SEO\AI_Authorization\Application;

use RuntimeException;
use Yoast\WP\SEO\AI_Authorization\Domain\Code_Verifier;
use Yoast\WP\SEO\AI_Authorization\Infrastructure\Code_Verifier_User_Meta_Repository;
use Yoast\WP\SEO\Helpers\Date_Helper;
/**
 * Class Code_Verifier_Service
 * Handles the generation and validation of code verifiers for users.
 */
class Code_Verifier_Handler implements Code_Verifier_Handler_Interface {
	private const VALIDITY_IN_SECONDS = 300; // 5 minutes

	/**
	 * The date helper.
	 *
	 * @var Date_Helper
	 */
	private $date_helper;

	/**
	 * The code verifier repository.
	 *
	 * @var Code_Verifier_User_Meta_Repository
	 */
	private $code_verifier_repository;

	/**
	 * Code_Verifier_Service constructor.
	 *
	 * @param Date_Helper                        $date_helper              The date helper.
	 * @param Code_Verifier_User_Meta_Repository $code_verifier_repository The code verifier repository.
	 */
	public function __construct( Date_Helper $date_helper, Code_Verifier_User_Meta_Repository $code_verifier_repository ) {
		$this->date_helper              = $date_helper;
		$this->code_verifier_repository = $code_verifier_repository;
	}

	/**
	 * Generate a code verifier for a user.
	 *
	 * @param string $user_email The user email.
	 *
	 * @return Code_Verifier The generated code verifier.
	 */
	public function generate( string $user_email ): Code_Verifier {
		$random_string = \substr( \str_shuffle( '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ' ), 1, 10 );
		$code          = \hash( 'sha256', $user_email . $random_string );
		$created_at    = $this->date_helper->current_time();

		return new Code_Verifier( $code, $created_at );
	}

	/**
	 * Validate the code verifier for a user.
	 *
	 * @param int $user_id The user ID.
	 *
	 * @return string The code verifier.
	 *
	 * @throws RuntimeException If the code verifier is expired or invalid.
	 */
	public function validate( int $user_id ): string {
		$code_verifier = $this->code_verifier_repository->get_code_verifier( $user_id );

		if ( $code_verifier === null || $code_verifier->is_expired( self::VALIDITY_IN_SECONDS ) ) {
			$this->code_verifier_repository->delete_code_verifier( $user_id );
			throw new RuntimeException( 'Code verifier has expired or is invalid.' );
		}

		return $code_verifier->get_code();
	}
}

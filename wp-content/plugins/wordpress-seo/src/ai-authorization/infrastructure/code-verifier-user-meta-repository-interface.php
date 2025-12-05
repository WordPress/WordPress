<?php

namespace Yoast\WP\SEO\AI_Authorization\Infrastructure;

use RuntimeException;
use Yoast\WP\SEO\AI_Authorization\Domain\Code_Verifier;

// phpcs:disable Yoast.NamingConventions.ObjectNameDepth.MaxExceeded
/**
 * Interface for the Code Verifier User Meta Repository.
 *
 * This interface defines methods for managing code verifiers associated with users.
 */
interface Code_Verifier_User_Meta_Repository_Interface {

	/**
	 * Get the verification code for a user.
	 *
	 * @param int $user_id The user ID.
	 *
	 * @throws RuntimeException If the code verifier is not found or has expired.
	 * @return Code_Verifier The verification code or null if not found.
	 */
	public function get_code_verifier( int $user_id ): ?Code_Verifier;

	/**
	 * Store the verification code for a user.
	 *
	 * @param int    $user_id    The user ID.
	 * @param string $code       The code verifier.
	 * @param int    $created_at The time the code was created.
	 *
	 * @return void
	 */
	public function store_code_verifier( int $user_id, string $code, int $created_at ): void;

	/**
	 * Delete the verification code for a user.
	 *
	 * @param int $user_id The user ID.
	 *
	 * @return void
	 */
	public function delete_code_verifier( int $user_id ): void;
}
//phpcs:enable Yoast.NamingConventions.ObjectNameDepth.MaxExceeded

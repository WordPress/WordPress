<?php

namespace Yoast\WP\SEO\AI_Authorization\Application;

use Yoast\WP\SEO\AI_Authorization\Domain\Code_Verifier;

/**
 * Interface Code_Verifier_Handler_Interface
 *
 * This interface defines the methods for handling code verifier.
 */
interface Code_Verifier_Handler_Interface {

	/**
	 * Generate a code verifier for a user.
	 *
	 * @param string $user_email The user email.
	 *
	 * @return Code_Verifier The generated code verifier.
	 */
	public function generate( string $user_email ): Code_Verifier;

	/**
	 * Validate the code verifier for a user.
	 *
	 * @param int $user_id The user ID.
	 *
	 * @return string The code verifier.
	 *
	 * @throws RuntimeException If the code verifier is expired or invalid.
	 */
	public function validate( int $user_id ): string;
}

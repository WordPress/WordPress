<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */

namespace Piwik\Plugins\WordPress;

use Piwik\Piwik;
use Piwik\Plugins\Login\PasswordVerifier;

if (!defined( 'ABSPATH')) {
    exit; // if accessed directly
}

class WpPasswordVerifier extends PasswordVerifier
{
	public function isPasswordCorrect($userLogin, $password)
	{
		/**
		 * @ignore
		 * @internal
		 */
		Piwik::postEvent('Login.beforeLoginCheckAllowed');

		if (function_exists('is_user_logged_in')
			&& is_user_logged_in()
		) {
			$user = wp_get_current_user();
			// check if this password is the login's password
			// wp_authenticate should make sure that lockout/brute force feature by security plugins are used etc
			$authenticatedUser = wp_authenticate($user->user_login, $password);
			if ($authenticatedUser
			       && $authenticatedUser instanceof \WP_User
			       && $authenticatedUser->ID
			       && $authenticatedUser->ID === $user->ID) {
				return true;
			};
		}

		/**
		 * @ignore
		 * @internal
		 */
		Piwik::postEvent('Login.recordFailedLoginAttempt');

		return false;
	}
}

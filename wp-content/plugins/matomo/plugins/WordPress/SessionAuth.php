<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */

namespace Piwik\Plugins\WordPress;

use Piwik\AuthResult;
use Piwik\Common;
use Piwik\FrontController;
use Piwik\Plugins\UsersManager\Model;
use Piwik\Session;
use Piwik\SettingsPiwik;
use WpMatomo\Capabilities;
use WpMatomo\Settings;
use WpMatomo\Site;
use WpMatomo\User;

if (!defined( 'ABSPATH')) {
    exit; // if accessed directly
}

class SessionAuth extends \Piwik\Session\SessionAuth
{
    const MATOMO_UI_NONCE_NAME = 'matomo-ui';

    public function authenticate()
    {
        if (function_exists('is_user_logged_in') && is_user_logged_in()) {

            $user = wp_get_current_user();

            $permission = null;
            if ($user && current_user_can(Capabilities::KEY_SUPERUSER)) {
                $permission = AuthResult::SUCCESS_SUPERUSER_AUTH_CODE;
            } else if ($user && current_user_can(Capabilities::KEY_VIEW)) {
                $permission = AuthResult::SUCCESS;
            }

            if (!empty($permission)) {
                $matomo_user = $this->findMatomoUser($user->ID);
                $token = $this->makeTemporaryToken($user->ID);

                if (
                    $this->getTokenAuth() !== false
                    && $this->getTokenAuth() !== null
                    && !Common::hashEquals((string) $token, (string) $this->getTokenAuth()) // note both may be converted to empty string in worst case so still the one below needed
                    && $token !== $this->getTokenAuth()
                    // if multiple pages are opened with the same session simultaneously, a race
                    // condition may occur, and not all of the pages will end up with the same
                    // token_auth value. in this case, the token_auth/nonce will not match the
                    // session value, but will still validate as a WordPress nonce. to handle
                    // this race condition, we allow values that don't match through, as long as
                    // they are valid nonces.
                    && !wp_verify_nonce($this->getTokenAuth(), self::MATOMO_UI_NONCE_NAME)
                ) {
                    return new AuthResult(AuthResult::FAILURE, $matomo_user['login'], null);
                }

                return new AuthResult($permission, $matomo_user['login'], $token);
            }
        }

        $login = 'anonymous';
        return new AuthResult(AuthResult::FAILURE, $login, $login);
    }

    private function makeTemporaryToken($userId)
    {
        $manager = \WP_Session_Tokens::get_instance($userId);
        if (empty($manager)) {
            return null;
        }

        $sessionToken = wp_get_session_token();
        if (empty($sessionToken)) {
            return null;
        }

        $session = $manager->get($sessionToken);
        if (empty($session)) {
            return null;
        }

        $matomoToken = $session['matomo-ui-ta'] ?? null;
        if (!$matomoToken) {
            $matomoToken = wp_create_nonce(self::MATOMO_UI_NONCE_NAME);
            $session['matomo-ui-ta'] = $matomoToken;
            $manager->update($sessionToken, $session);
        }

        return $matomoToken;
    }

    private function findMatomoUser($userId, $syncIfNotFound = true)
    {
	    $login = User::get_matomo_user_login($userId);

	    if ($login) {
			// user is already synced
		    $userModel = new Model();
		    $user      = $userModel->getUser($login);
	    }

        if (empty($user['login'])) {
            if ($syncIfNotFound) {
            	$site = new Site\Sync(new Settings());
            	$site->sync_current_site();

                // user should be synced...
                $sync = new User\Sync();
                $sync->sync_current_users();

                return $this->findMatomoUser($userId, $syncIfNotFound = false);
            }
            throw new \Exception('User is not syncronized yet, please try again later');
        }
        return $user;
    }
}

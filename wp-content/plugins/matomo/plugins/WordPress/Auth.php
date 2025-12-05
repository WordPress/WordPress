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
use Piwik\Config;
use Piwik\Container\StaticContainer;
use Piwik\Log\LoggerInterface;
use Piwik\Plugins\UsersManager\Model;
use Piwik\SettingsServer;
use Piwik\Tracker\TrackerConfig;
use WpMatomo\User;

if (!defined( 'ABSPATH')) {
    exit; // if accessed directly
}

class Auth extends \Piwik\Plugins\Login\Auth
{
    public function getName()
    {
        return 'WordPress';
    }

    public function authenticate()
    {
        // authenticate app password provided via Authorization header. for tracking,
        // a dummy token_auth is still required.
        $result = $this->authWithAppPassword();
        if (!empty($result)) {
            return $result;
        }

        // UI request authentication
        $isUserLoggedIn = function_exists('is_user_logged_in') && is_user_logged_in();
        if ($isUserLoggedIn) {
	        if (is_null($this->login) && empty($this->hashedPassword)) {
	            // api authentication using token
		        return parent::authenticate();
	        }
        } else if ($this->isAppPasswordInTokenAuthAllowed()) {
            $result = $this->authApiWithTokenAuthAppPassword();
            if (!empty($result)) {
                return $result;
            }
        }

        $login = 'anonymous';
        return new AuthResult(AuthResult::FAILURE, $login, $this->token_auth);
    }

    private function authWithAppPassword()
    {
        if (!function_exists('wp_validate_application_password')) {
            return null;
        }

        $callback = function () { return true; };

        add_filter('application_password_is_api_request', $callback);
        try {
            $loggedInUserId = wp_validate_application_password(false);
            $isUserLoggedIn = $loggedInUserId !== false;
        } finally {
            remove_filter('application_password_is_api_request', $callback);
        }

        if (!$isUserLoggedIn) {
            return null;
        }

        $login = User::get_matomo_user_login($loggedInUserId);

        $userModel = new Model();
        $matomoUser = $userModel->getUser($login);
        if (empty($matomoUser)) {
            return null;
        }

        $code = ((int) $matomoUser['superuser_access']) ? AuthResult::SUCCESS_SUPERUSER_AUTH_CODE : AuthResult::SUCCESS;
        return new AuthResult($code, $login, $this->token_auth);
    }

    private function isAppPasswordInTokenAuthAllowed()
    {
        $wordPressConfig = Config::getInstance()->WordPress;
        $allowed = !empty( $wordPressConfig['allow_app_password_as_token_auth'] ) && strval( $wordPressConfig['allow_app_password_as_token_auth'] ) === '1';
        return $allowed;
    }

    private function authApiWithTokenAuthAppPassword()
    {
        $tokenAuth = $this->token_auth;
        if (empty($tokenAuth)) {
            return null;
        }

        $logger = StaticContainer::get(LoggerInterface::class);

        if (!function_exists('wp_validate_application_password')) {
            $logger->debug('WordPress\\Auth: wp_validate_application_password does not exist');
            return null;
        }

        $parts = explode(':', $tokenAuth);
        if (count($parts) !== 2) {
            $logger->debug('WordPress\\Auth: app password provided in token_auth has incorrect format, expected "username:apppassword".');
            return null;
        }

        if (
            empty($_SERVER['REQUEST_METHOD'])
            || strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST'
        ) {
            throw new \Exception('Invalid token auth or token auth was not provided as a POST parameter.');
        }

        [$user, $pass] = $parts;

        $callback = function () { return true; };

        add_filter('application_password_is_api_request', $callback);
        try {
            $authenticated = wp_authenticate_application_password(null, $user, $pass);
            if (!($authenticated instanceof \WP_User)) {
                return null;
            }
            $loggedInUserId = $authenticated->ID;
        } finally {
            remove_filter('application_password_is_api_request', $callback);
        }

        $login = User::get_matomo_user_login($loggedInUserId);

        $userModel = new Model();
        $matomoUser = $userModel->getUser($login);
        if (empty($matomoUser)) {
            return null;
        }

        $code = ((int) $matomoUser['superuser_access']) ? AuthResult::SUCCESS_SUPERUSER_AUTH_CODE : AuthResult::SUCCESS;
        return new AuthResult($code, $login, $this->token_auth);
    }
}

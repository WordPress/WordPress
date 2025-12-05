<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager\Model;

use Piwik\Common;
use Piwik\Option;
class Salt
{
    public const OPTION_TAGMANAGER_SALT = 'tagmanager_salt';
    public const SALT_LENGTH = 40;
    public function __construct($salt = '')
    {
        if ($this->isValidSalt($salt)) {
            // only used for testing
            Option::set(self::OPTION_TAGMANAGER_SALT, $salt);
        } elseif (!empty($salt)) {
            throw new \Exception('Invalid salt!');
        }
    }
    private function isValidSalt($salt)
    {
        return !empty($salt) && strlen($salt) >= self::SALT_LENGTH;
    }
    public function generateSaltIfNeeded()
    {
        $existingSalt = Option::get(self::OPTION_TAGMANAGER_SALT);
        // we do not use SettingsPiwik::getSalt() because the salt may be used in a publicly visible key
        // and we want to make sure that if someone was able to bruteforce/calculate the salt, then we do not expose
        // the original settingspiwik salt
        if ($this->isValidSalt($existingSalt)) {
            return $existingSalt;
        }
        $salt = Common::generateUniqId();
        $salt = substr($salt, 0, 20);
        $salt .= Common::getRandomString(self::SALT_LENGTH - strlen($salt), $alphabet = "abcdefghijklmnoprstuvwxyz{}[]!?-_ABCDEFGHIJKLMNOPRSTUVWXYZ0123456789");
        Option::set(self::OPTION_TAGMANAGER_SALT, $salt);
        return $salt;
    }
    public function getSalt()
    {
        $salt = Option::get(self::OPTION_TAGMANAGER_SALT);
        if (empty($salt)) {
            $salt = self::generateSaltIfNeeded();
        }
        return $salt;
    }
    public function removeSalt()
    {
        Option::delete(self::OPTION_TAGMANAGER_SALT);
    }
}

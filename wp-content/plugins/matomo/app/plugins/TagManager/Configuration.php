<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager;

use Piwik\Config;
class Configuration
{
    public static $DEFAULT_DISABLED_TAGS = [];
    public static $DEFAULT_DISABLED_TRIGGERS = [];
    public static $DEFAULT_DISABLED_VARIABLES = [];
    public const KEY_DISABLED_TAGS = 'disable_tags';
    public const KEY_DISABLED_TRIGGERS = 'disable_triggers';
    public const KEY_DISABLED_VARIABLES = 'disable_variables';
    public function install()
    {
        $config = $this->getConfig();
        if (empty($config->TagManager)) {
            $config->TagManager = array();
        }
        $tagManager = $config->TagManager;
        $values = array(self::KEY_DISABLED_TAGS => self::$DEFAULT_DISABLED_TAGS, self::KEY_DISABLED_TRIGGERS => self::$DEFAULT_DISABLED_TRIGGERS, self::KEY_DISABLED_VARIABLES => self::$DEFAULT_DISABLED_VARIABLES);
        foreach ($values as $key => $default) {
            // we make sure to set a value only if none has been configured yet, eg in common config.
            if (empty($tagManager[$key])) {
                $tagManager[$key] = $default;
            }
        }
        $config->TagManager = $tagManager;
        $config->forceSave();
    }
    public function uninstall()
    {
        $config = $this->getConfig();
        $config->TagManager = array();
        $config->forceSave();
    }
    /**
     * @return array
     */
    public function getDisabledTags()
    {
        $disabled = $this->getConfigValue(self::KEY_DISABLED_TAGS, self::$DEFAULT_DISABLED_TAGS);
        if (!is_array($disabled) || empty($disabled)) {
            $disabled = array();
        }
        return array_values(array_unique($disabled));
    }
    /**
     * @return array
     */
    public function getDisabledTriggers()
    {
        $disabled = $this->getConfigValue(self::KEY_DISABLED_TRIGGERS, self::$DEFAULT_DISABLED_TRIGGERS);
        if (!is_array($disabled) || empty($disabled)) {
            $disabled = array();
        }
        return $disabled;
    }
    /**
     * @return array
     */
    public function getDisabledVariables()
    {
        $disabled = $this->getConfigValue(self::KEY_DISABLED_VARIABLES, self::$DEFAULT_DISABLED_VARIABLES);
        if (!is_array($disabled) || empty($disabled)) {
            $disabled = array();
        }
        return array_values(array_unique($disabled));
    }
    private function getConfig()
    {
        return Config::getInstance();
    }
    private function getConfigValue($name, $default)
    {
        $config = $this->getConfig();
        $tagManager = $config->TagManager;
        if (isset($tagManager[$name])) {
            return $tagManager[$name];
        }
        return $default;
    }
}

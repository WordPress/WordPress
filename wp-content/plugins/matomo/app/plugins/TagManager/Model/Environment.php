<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager\Model;

use Piwik\Common;
use Piwik\Piwik;
use Piwik\Plugins\TagManager\Configuration;
use Exception;
use Piwik\Plugins\TagManager\SystemSettings;
class Environment
{
    public const ENVIRONMENT_LIVE = 'live';
    public const ENVIRONMENT_PREVIEW = 'preview';
    public const MAX_LENGTH = 40;
    public const MIN_LENGTH = 2;
    /**
     * @var Configuration
     */
    private $settings;
    public function __construct(SystemSettings $settings)
    {
        $this->settings = $settings;
    }
    public static function checkEnvironmentNameFormat($environment)
    {
        if (!is_string($environment) || Common::mb_strlen($environment) > self::MAX_LENGTH || Common::mb_strlen($environment) < self::MIN_LENGTH) {
            throw new Exception(Piwik::translate('TagManager_ErrorEnvironmentInvalidLength', array($environment, self::MIN_LENGTH, self::MAX_LENGTH)));
        }
        if (!preg_match('/^[a-zA-Z0-9][a-zA-Z0-9_]*[a-zA-Z0-9]$/', $environment)) {
            throw new Exception(Piwik::translate('TagManager_ErrorEnvironmentInvalidName', $environment));
        }
    }
    public function checkIsValidEnvironment($environmentId)
    {
        $environments = $this->settings->getEnvironments();
        if (in_array($environmentId, $environments, \true)) {
            return;
        }
        throw new Exception(Piwik::translate('TagManager_ErrorEnvironmentDoesNotExist', $environmentId));
    }
    /**
     * @return array
     */
    public function getEnvironments()
    {
        $environments = $this->settings->getEnvironments();
        $return = array();
        foreach ($environments as $environment) {
            $return[] = ['id' => $environment, 'name' => ucfirst(str_replace('_', ' ', $environment))];
        }
        return $return;
    }
}

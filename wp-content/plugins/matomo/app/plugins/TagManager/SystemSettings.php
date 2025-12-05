<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager;

use Piwik\Access;
use Piwik\Container\StaticContainer;
use Piwik\Date;
use Piwik\Piwik;
use Piwik\Plugins\TagManager\Context\BaseContext;
use Piwik\Plugins\TagManager\Model\Environment;
use Piwik\Settings\Plugin\SystemSetting;
use Piwik\Settings\Setting;
use Piwik\Settings\FieldConfig;
class SystemSettings extends \Piwik\Settings\Plugin\SystemSettings
{
    public const CUSTOM_TEMPLATES_DISABLED = 'disabled';
    public const CUSTOM_TEMPLATES_ADMIN = 'admin';
    public const CUSTOM_TEMPLATES_SUPERUSER = 'superuser';
    public const USER_PERMISSON_LIST = [\Piwik\Access\Role\View::ID, \Piwik\Access\Role\Write::ID, \Piwik\Access\Role\Admin::ID, self::CUSTOM_TEMPLATES_SUPERUSER];
    /** @var Setting */
    public $restrictTagManagerAccess;
    /** @var Setting */
    public $restrictCustomTemplates;
    /** @var Setting */
    public $environments;
    public static $DEFAULT_ENVIRONMENTS = [['environment' => 'dev'], ['environment' => 'staging']];
    protected function init()
    {
        $this->restrictTagManagerAccess = $this->createRestrictAccessSetting();
        $this->restrictCustomTemplates = $this->createCustomTemplatesSetting();
        $this->environments = $this->createEnvironmentsSetting();
    }
    private function createRestrictAccessSetting() : SystemSetting
    {
        return $this->makeSetting('restrictTagManagerAccess', \Piwik\Access\Role\View::ID, FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_SettingRestrictAccessTitle');
            $field->uiControl = FieldConfig::UI_CONTROL_SINGLE_SELECT;
            $field->description = Piwik::translate('TagManager_SettingRestrictAccessDescription');
            $field->availableValues = [self::USER_PERMISSON_LIST[$this->getPermissionIndex('view')] => Piwik::translate('TagManager_SettingRestrictAccessView'), self::USER_PERMISSON_LIST[$this->getPermissionIndex('write')] => Piwik::translate('TagManager_SettingRestrictAccessWrite'), self::USER_PERMISSON_LIST[$this->getPermissionIndex('admin')] => Piwik::translate('TagManager_SettingRestrictAccessAdmin'), self::USER_PERMISSON_LIST[$this->getPermissionIndex('superuser')] => Piwik::translate('TagManager_SettingRestrictAccessSuperUser')];
        });
    }
    private function getPermissionIndex(string $permission) : int
    {
        $index = array_search($permission, self::USER_PERMISSON_LIST);
        if ($index !== \false) {
            return $index;
        }
        throw new \Exception('Permission \'' . $permission . '\' not found');
    }
    /**
     * Check whether the currently logged-in user has access to MTM. This expects the site ID, but allows 0 for the
     * Administration area, where there isn't necessarily a specific site selected.
     *
     * @param int $idSite ID of the site currently being viewed. 0 or nothing should be passed if in Administration area
     * @return bool Whether the user has access to MTM
     */
    public function doesCurrentUserHaveTagManagerAccess(int $idSite = 0) : bool
    {
        // First check for superuser access, since the setting won't matter at that point
        $access = StaticContainer::get(Access::class);
        if ($access->hasSuperUserAccess()) {
            return \true;
        }
        $settingValue = $this->restrictTagManagerAccess->getValue();
        // We need to allow checks with no site ID since we might be in the Administration section
        if ($idSite === 0) {
            switch ($settingValue) {
                case self::USER_PERMISSON_LIST[$this->getPermissionIndex('view')]:
                    return !empty($access->getSitesIdWithAtLeastViewAccess());
                case self::USER_PERMISSON_LIST[$this->getPermissionIndex('write')]:
                    return $access->isUserHasSomeWriteAccess();
                case self::USER_PERMISSON_LIST[$this->getPermissionIndex('admin')]:
                    return $access->isUserHasSomeAdminAccess();
                // Those should be the only available options, since we already checked for superuser
                default:
                    return \false;
            }
        }
        $role = $access->getRoleForSite($idSite);
        $roleIndex = in_array($role, self::USER_PERMISSON_LIST) ? array_search($role, self::USER_PERMISSON_LIST) : 0;
        $settingIndex = in_array($settingValue, self::USER_PERMISSON_LIST) ? array_search($settingValue, self::USER_PERMISSON_LIST) : 0;
        return $roleIndex >= $settingIndex;
    }
    private function createCustomTemplatesSetting()
    {
        return $this->makeSetting('restrictCustomTemplates', self::CUSTOM_TEMPLATES_ADMIN, FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = Piwik::translate('TagManager_SettingCustomTemplatesTitle');
            $field->uiControl = FieldConfig::UI_CONTROL_SINGLE_SELECT;
            $field->description = Piwik::translate('TagManager_SettingCustomTemplatesDescription');
            $field->availableValues = array(self::CUSTOM_TEMPLATES_DISABLED => Piwik::translate('TagManager_SettingCustomTemplatesDisabled'), self::CUSTOM_TEMPLATES_ADMIN => Piwik::translate('TagManager_SettingCustomTemplatesAdmin'), self::CUSTOM_TEMPLATES_SUPERUSER => Piwik::translate('TagManager_SettingCustomTemplatesSuperUser'));
        });
    }
    public function transformEnvironment($environments)
    {
        if (!is_array($environments) && !empty($environments)) {
            $environments = array($environments);
        }
        $environments = array_filter($environments, function ($val) {
            if (!is_array($val) || !isset($val['environment'])) {
                throw new \Exception('Missing array key environment');
            }
            return $val['environment'] !== \false && $val['environment'] !== '' && $val['environment'] !== null;
        });
        $environments = array_map(function ($val) {
            // make sure to only keep environment but no other properties
            return array('environment' => strtolower($val['environment']));
        }, $environments);
        $environments = array_values(array_unique($environments, \SORT_REGULAR));
        return $environments;
    }
    public function getEnvironments()
    {
        $environments = $this->environments->getValue();
        if (empty($environments)) {
            $environments = array();
        }
        $flat = array();
        foreach ($environments as $environment) {
            if (!empty($environment['environment'])) {
                $flat[] = $environment['environment'];
            }
        }
        array_unshift($flat, Environment::ENVIRONMENT_LIVE);
        $flat = array_values(array_unique($flat));
        return $flat;
    }
    public function save()
    {
        parent::save();
        $environments = $this->getEnvironments();
        $environments[] = Environment::ENVIRONMENT_PREVIEW;
        $now = Date::now();
        BaseContext::removeNoLongerExistingEnvironments($environments);
        $releaseDao = StaticContainer::get('Piwik\\Plugins\\TagManager\\Dao\\ContainerReleaseDao');
        $releaseDao->deleteNoLongerExistingEnvironmentReleases($environments, $now->getDatetime());
    }
    private function createEnvironmentsSetting()
    {
        $self = $this;
        return $this->makeSetting('environments', self::$DEFAULT_ENVIRONMENTS, FieldConfig::TYPE_ARRAY, function (FieldConfig $field) use($self) {
            $field->title = Piwik::translate('TagManager_Environments');
            $field->uiControl = FieldConfig::UI_CONTROL_MULTI_TUPLE;
            $field->description = Piwik::translate('TagManager_SettingEnvironmentDescription');
            $field->uiControl = FieldConfig::UI_CONTROL_MULTI_TUPLE;
            $field1 = new FieldConfig\MultiPair(Piwik::translate('TagManager_Environment'), 'environment', FieldConfig::UI_CONTROL_TEXT);
            $field->uiControlAttributes['field1'] = $field1->toArray();
            $field->transform = function ($value) use($self) {
                return $self->transformEnvironment($value);
            };
            $field->validate = function ($value) use($self) {
                $value = $self->transformEnvironment($value);
                foreach ($value as $environment) {
                    if (!isset($environment['environment'])) {
                        continue;
                    }
                    Environment::checkEnvironmentNameFormat($environment['environment']);
                    if (strtolower($environment['environment']) === strtolower(Environment::ENVIRONMENT_PREVIEW)) {
                        throw new \Exception(Piwik::translate('TagManager_ErrorPreviewReservedEnvironment'));
                    }
                }
            };
        });
    }
}

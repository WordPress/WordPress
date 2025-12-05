<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager\Input;

use Piwik\Container\StaticContainer;
use Piwik\NoAccessException;
use Piwik\Plugins\TagManager\Access\Capability\PublishLiveContainer;
use Piwik\Plugins\TagManager\Access\Capability\TagManagerWrite;
use Piwik\Plugins\TagManager\Access\Capability\UseCustomTemplates;
use Piwik\Piwik;
use Piwik\Plugins\TagManager\SystemSettings;
use Piwik\Site;
class AccessValidator
{
    /**
     * @var SystemSettings
     */
    private $settings;
    public function __construct(SystemSettings $settings)
    {
        $this->settings = $settings;
    }
    public function checkViewPermission($idSite)
    {
        $this->checkSiteExists($idSite);
        Piwik::checkUserHasViewAccess($idSite);
        $this->checkUserHasTagManagerAccess($idSite);
    }
    public function checkWriteCapability($idSite)
    {
        $this->checkSiteExists($idSite);
        Piwik::checkUserHasCapability($idSite, TagManagerWrite::ID);
        $this->checkUserHasTagManagerAccess($idSite);
    }
    public function checkPublishLiveEnvironmentCapability($idSite)
    {
        $this->checkSiteExists($idSite);
        Piwik::checkUserHasCapability($idSite, PublishLiveContainer::ID);
        $this->checkUserHasTagManagerAccess($idSite);
    }
    public function checkUseCustomTemplatesCapability($idSite)
    {
        $this->checkSiteExists($idSite);
        if ($this->settings->restrictCustomTemplates->getValue() === SystemSettings::CUSTOM_TEMPLATES_SUPERUSER) {
            Piwik::checkUserHasSuperUserAccess();
        } else {
            // no need to check for === disabled as it will be automatically checked because in this case we remove
            // disabled tags/triggers/...
            Piwik::checkUserHasCapability($idSite, UseCustomTemplates::ID);
        }
    }
    public function checkUserHasTagManagerAccess($idSite) : void
    {
        // If the user has access, return before the exception is thrown
        if (StaticContainer::get(SystemSettings::class)->doesCurrentUserHaveTagManagerAccess(intval($idSite))) {
            return;
        }
        $minimumRole = StaticContainer::get(SystemSettings::class)->restrictTagManagerAccess->getValue();
        throw new NoAccessException(Piwik::translate('General_ExceptionPrivilegeAccessWebsite', ["'{$minimumRole}'", $idSite]));
    }
    public function hasUseCustomTemplatesCapability($idSite)
    {
        try {
            $this->checkUseCustomTemplatesCapability($idSite);
        } catch (\Exception $e) {
            return \false;
        }
        return \true;
    }
    public function hasPublishLiveEnvironmentCapability($idSite)
    {
        try {
            $this->checkPublishLiveEnvironmentCapability($idSite);
        } catch (\Exception $e) {
            return \false;
        }
        return \true;
    }
    public function hasWriteCapability($idSite)
    {
        try {
            $this->checkWriteCapability($idSite);
        } catch (\Exception $e) {
            return \false;
        }
        return \true;
    }
    public function checkSiteExists($idSite)
    {
        new Site($idSite);
    }
}

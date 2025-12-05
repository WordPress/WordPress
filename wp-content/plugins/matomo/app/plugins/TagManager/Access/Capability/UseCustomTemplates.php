<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\TagManager\Access\Capability;

use Piwik\Access\Capability;
use Piwik\Access\Role\Admin;
use Piwik\Piwik;
class UseCustomTemplates extends Capability
{
    public const ID = 'tagmanager_use_custom_templates';
    public function getId() : string
    {
        return self::ID;
    }
    public function getCategory() : string
    {
        return Piwik::translate('TagManager_TagManager');
    }
    public function getName() : string
    {
        return Piwik::translate('TagManager_CapabilityUseCustomTemplates');
    }
    public function getDescription() : string
    {
        return Piwik::translate('TagManager_CapabilityUseCustomTemplateDescription');
    }
    public function getIncludedInRoles() : array
    {
        return array(Admin::ID);
    }
}

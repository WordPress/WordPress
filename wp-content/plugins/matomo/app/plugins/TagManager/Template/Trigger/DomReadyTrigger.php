<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager\Template\Trigger;

use Piwik\Plugins\TagManager\Template\Trigger\BaseTrigger;
class DomReadyTrigger extends BaseTrigger
{
    public function getCategory()
    {
        return self::CATEGORY_PAGE_VIEW;
    }
    public function getName()
    {
        // By default, the name will be automatically fetched from the TagManager_DomReadyTriggerName translation key.
        // you can either adjust/create/remove this translation key, or return a different value here directly.
        return parent::getName();
    }
    public function getDescription()
    {
        // By default, the description will be automatically fetched from the TagManager_DomReadyTriggerDescription
        // translation key. you can either adjust/create/remove this translation key, or return a different value
        // here directly.
        return parent::getDescription();
    }
    public function getHelp()
    {
        // By default, the help will be automatically fetched from the TagManager_DomReadyTriggerHelp translation key.
        // you can either adjust/create/remove this translation key, or return a different value here directly.
        return parent::getHelp();
    }
    public function getIcon()
    {
        // You may optionally specify a path to an image icon URL, for example:
        //
        // return 'plugins/TagManager/images/MyIcon.png';
        //
        // The image should have ideally a resolution of about 64x64 pixels.
        return parent::getIcon();
    }
    public function getParameters()
    {
        return array();
    }
}

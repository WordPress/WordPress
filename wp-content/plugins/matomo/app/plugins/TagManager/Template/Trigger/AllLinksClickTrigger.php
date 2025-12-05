<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager\Template\Trigger;

class AllLinksClickTrigger extends \Piwik\Plugins\TagManager\Template\Trigger\BaseTrigger
{
    public function getCategory()
    {
        return self::CATEGORY_CLICK;
    }
    public function getIcon()
    {
        return 'plugins/TagManager/images/icons/pointer.svg';
    }
    public function getParameters()
    {
        return array();
    }
}

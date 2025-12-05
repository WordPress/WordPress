<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager\Template\Trigger;

class WindowLoadedTrigger extends \Piwik\Plugins\TagManager\Template\Trigger\BaseTrigger
{
    public const ID = 'WindowLoaded';
    public function getId()
    {
        return self::ID;
    }
    public function getCategory()
    {
        return self::CATEGORY_PAGE_VIEW;
    }
    public function getParameters()
    {
        return array();
    }
}

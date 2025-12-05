<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager\Template\Trigger;

class FormSubmitTrigger extends \Piwik\Plugins\TagManager\Template\Trigger\BaseTrigger
{
    public function getCategory()
    {
        return self::CATEGORY_USER_ENGAGEMENT;
    }
    public function getIcon()
    {
        return 'plugins/TagManager/images/icons/form.svg';
    }
    public function getParameters()
    {
        return array();
    }
}

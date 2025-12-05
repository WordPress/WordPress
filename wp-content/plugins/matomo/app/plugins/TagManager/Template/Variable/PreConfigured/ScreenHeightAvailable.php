<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager\Template\Variable\PreConfigured;

use Piwik\Plugins\TagManager\Context\WebContext;
class ScreenHeightAvailable extends \Piwik\Plugins\TagManager\Template\Variable\PreConfigured\BasePreConfiguredVariable
{
    public function getCategory()
    {
        return self::CATEGORY_DEVICE;
    }
    public function loadTemplate($context, $entity, $skipTemplate = \false)
    {
        switch ($context) {
            case WebContext::ID:
                return $this->makeReturnTemplateMethod('TagManager.window.getViewportHeight()', $skipTemplate);
        }
    }
}

<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager\Template\Variable\PreConfigured;

use Piwik\Plugins\TagManager\Context\WebContext;
class SeoNumH1 extends \Piwik\Plugins\TagManager\Template\Variable\PreConfigured\BasePreConfiguredVariable
{
    public function getCategory()
    {
        return self::CATEGORY_SEO;
    }
    public function loadTemplate($context, $entity, $skipTemplate = \false)
    {
        switch ($context) {
            case WebContext::ID:
                return $this->makeReturnTemplateMethod("TagManager.dom.byTagName('h1').length", $skipTemplate);
        }
    }
}

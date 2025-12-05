<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager\Template\Variable\PreConfigured;

class ClickTextVariable extends \Piwik\Plugins\TagManager\Template\Variable\PreConfigured\BaseDataLayerVariable
{
    public function getCategory()
    {
        return self::CATEGORY_CLICKS;
    }
    protected function getDataLayerVariableName()
    {
        return 'mtm.clickText';
    }
}

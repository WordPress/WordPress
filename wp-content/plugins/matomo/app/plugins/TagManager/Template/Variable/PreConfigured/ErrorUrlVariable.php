<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager\Template\Variable\PreConfigured;

class ErrorUrlVariable extends \Piwik\Plugins\TagManager\Template\Variable\PreConfigured\BaseDataLayerVariable
{
    public const ID = 'ErrorUrl';
    public function getId()
    {
        return self::ID;
    }
    public function getCategory()
    {
        return self::CATEGORY_ERRORS;
    }
    protected function getDataLayerVariableName()
    {
        return 'mtm.errorUrl';
    }
}

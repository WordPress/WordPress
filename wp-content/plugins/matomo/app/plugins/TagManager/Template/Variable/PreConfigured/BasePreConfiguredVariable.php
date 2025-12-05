<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager\Template\Variable\PreConfigured;

use Piwik\Common;
use Piwik\Plugins\TagManager\Template\Variable\BaseVariable;
abstract class BasePreConfiguredVariable extends BaseVariable
{
    public function isPreConfigured()
    {
        return \true;
    }
    public final function getParameters()
    {
        return [];
    }
    protected function makeReturnTemplateMethod($js, $skipTemplate = \false)
    {
        $js = trim($js);
        if (!Common::stringEndsWith($js, ';')) {
            $js .= ';';
        }
        if (strpos($js, 'return ') !== 0) {
            $js = 'return ' . $js;
        }
        if ($skipTemplate) {
            return $js;
        }
        return '(function () { return function (parameters, TagManager) { this.get = function () { ' . $js . '   }; } })();';
    }
}

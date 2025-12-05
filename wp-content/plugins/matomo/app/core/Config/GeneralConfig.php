<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Config;

class GeneralConfig extends \Piwik\Config\SectionConfig
{
    public static function getSectionName() : string
    {
        return 'General';
    }
}

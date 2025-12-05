<?php

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link https://matomo.org
 *
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */
declare (strict_types=1);
namespace DeviceDetector\Parser\Device;

/**
 * Class Mobile
 *
 * Device parser for mobile detection
 */
class Mobile extends \DeviceDetector\Parser\Device\AbstractDeviceParser
{
    /**
     * @var string
     */
    protected $fixtureFile = 'regexes/device/mobiles.yml';
    /**
     * @var string
     */
    protected $parserName = 'mobile';
}

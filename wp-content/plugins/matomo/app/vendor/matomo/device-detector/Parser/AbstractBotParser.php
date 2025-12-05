<?php

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link https://matomo.org
 *
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */
declare (strict_types=1);
namespace DeviceDetector\Parser;

/**
 * Class AbstractBotParser
 *
 * Abstract class for all bot parsers
 */
abstract class AbstractBotParser extends \DeviceDetector\Parser\AbstractParser
{
    /**
     * Enables information discarding
     */
    public abstract function discardDetails() : void;
}

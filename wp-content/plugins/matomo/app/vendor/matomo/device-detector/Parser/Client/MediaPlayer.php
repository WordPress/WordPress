<?php

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link https://matomo.org
 *
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */
declare (strict_types=1);
namespace DeviceDetector\Parser\Client;

/**
 * Class MediaPlayer
 *
 * Client parser for mediaplayer detection
 */
class MediaPlayer extends \DeviceDetector\Parser\Client\AbstractClientParser
{
    /**
     * @var string
     */
    protected $fixtureFile = 'regexes/client/mediaplayers.yml';
    /**
     * @var string
     */
    protected $parserName = 'mediaplayer';
}

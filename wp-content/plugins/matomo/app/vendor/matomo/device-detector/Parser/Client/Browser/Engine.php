<?php

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link https://matomo.org
 *
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */
declare (strict_types=1);
namespace DeviceDetector\Parser\Client\Browser;

use DeviceDetector\Parser\Client\AbstractClientParser;
/**
 * Class Engine
 *
 * Client parser for browser engine detection
 */
class Engine extends AbstractClientParser
{
    /**
     * @var string
     */
    protected $fixtureFile = 'regexes/client/browser_engine.yml';
    /**
     * @var string
     */
    protected $parserName = 'browserengine';
    /**
     * Known browser engines mapped to their internal short codes
     *
     * @var array
     */
    protected static $availableEngines = ['WebKit', 'Blink', 'Trident', 'Text-based', 'Dillo', 'iCab', 'Elektra', 'Presto', 'Clecko', 'Gecko', 'KHTML', 'NetFront', 'Edge', 'NetSurf', 'Servo', 'Goanna', 'EkiohFlow', 'Arachne', 'LibWeb', 'Maple'];
    /**
     * Returns list of all available browser engines
     * @return array
     */
    public static function getAvailableEngines() : array
    {
        return self::$availableEngines;
    }
    /**
     * @inheritdoc
     */
    public function parse() : ?array
    {
        $matches = \false;
        foreach ($this->getRegexes() as $regex) {
            $matches = $this->matchUserAgent($regex['regex']);
            if ($matches) {
                break;
            }
        }
        if (empty($matches) || empty($regex)) {
            return null;
        }
        $name = $this->buildByMatch($regex['name'], $matches);
        foreach (self::getAvailableEngines() as $engineName) {
            if (\strtolower($name) === \strtolower($engineName)) {
                return ['engine' => $engineName];
            }
        }
        // This Exception should never be thrown. If so a defined browser name is missing in $availableEngines
        throw new \Exception(\sprintf('Detected browser engine was not found in $availableEngines. Tried to parse user agent: %s', $this->userAgent));
        // @codeCoverageIgnore
    }
}

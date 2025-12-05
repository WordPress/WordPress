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

use DeviceDetector\Parser\AbstractParser;
abstract class AbstractClientParser extends AbstractParser
{
    /**
     * @var string
     */
    protected $fixtureFile = '';
    /**
     * @var string
     */
    protected $parserName = '';
    /**
     * Parses the current UA and checks whether it contains any client information
     *
     * @see $fixtureFile for file with list of detected clients
     *
     * Step 1: Build a big regex containing all regexes and match UA against it
     * -> If no matches found: return
     * -> Otherwise:
     * Step 2: Walk through the list of regexes in feed_readers.yml and try to match every one
     * -> Return the matched feed reader
     *
     * NOTE: Doing the big match before matching every single regex speeds up the detection
     *
     * @return array|null
     */
    public function parse() : ?array
    {
        $result = null;
        if ($this->preMatchOverall()) {
            foreach ($this->getRegexes() as $regex) {
                $matches = $this->matchUserAgent($regex['regex']);
                if ($matches) {
                    $result = ['type' => $this->parserName, 'name' => $this->buildByMatch($regex['name'], $matches), 'version' => $this->buildVersion((string) $regex['version'], $matches)];
                    break;
                }
            }
        }
        return $result;
    }
    /**
     * Returns all names defined in the regexes
     *
     * Attention: This method might not return all names of detected clients
     *
     * @return array
     */
    public static function getAvailableClients() : array
    {
        $instance = new static();
        // @phpstan-ignore-line
        $regexes = $instance->getRegexes();
        $names = [];
        foreach ($regexes as $regex) {
            if (\false !== \strpos($regex['name'], '$1')) {
                continue;
            }
            $names[] = $regex['name'];
        }
        if (static::class === \DeviceDetector\Parser\Client\MobileApp::class) {
            $names = \array_merge($names, [
                // Microsoft Office $1
                'Microsoft Office Access',
                'Microsoft Office Excel',
                'Microsoft Office OneDrive for Business',
                'Microsoft Office OneNote',
                'Microsoft Office PowerPoint',
                'Microsoft Office Project',
                'Microsoft Office Publisher',
                'Microsoft Office Visio',
                'Microsoft Office Word',
                // Podkicker$1
                'Podkicker',
                'Podkicker Pro',
                'Podkicker Classic',
                // radio.$1
                'radio.at',
                'radio.de',
                'radio.dk',
                'radio.es',
                'radio.fr',
                'radio.it',
                'radio.pl',
                'radio.pt',
                'radio.se',
                'radio.net',
            ]);
        }
        \natcasesort($names);
        return \array_unique($names);
    }
}

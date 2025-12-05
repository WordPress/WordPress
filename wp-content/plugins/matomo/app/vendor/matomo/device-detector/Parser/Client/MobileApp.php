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

use DeviceDetector\Cache\CacheInterface;
use DeviceDetector\ClientHints;
use DeviceDetector\Parser\Client\Hints\AppHints;
use DeviceDetector\Yaml\ParserInterface as YamlParser;
/**
 * Class MobileApp
 *
 * Client parser for mobile app detection
 */
class MobileApp extends \DeviceDetector\Parser\Client\AbstractClientParser
{
    /**
     * @var AppHints
     */
    private $appHints;
    /**
     * @var string
     */
    protected $fixtureFile = 'regexes/client/mobile_apps.yml';
    /**
     * @var string
     */
    protected $parserName = 'mobile app';
    /**
     * MobileApp constructor.
     *
     * @param string           $ua
     * @param ClientHints|null $clientHints
     */
    public function __construct(string $ua = '', ?ClientHints $clientHints = null)
    {
        $this->appHints = new AppHints($ua, $clientHints);
        parent::__construct($ua, $clientHints);
    }
    /**
     * Sets the client hints to parse
     *
     * @param ?ClientHints $clientHints client hints
     */
    public function setClientHints(?ClientHints $clientHints) : void
    {
        parent::setClientHints($clientHints);
        $this->appHints->setClientHints($clientHints);
    }
    /**
     * Sets the user agent to parse
     *
     * @param string $ua user agent
     */
    public function setUserAgent(string $ua) : void
    {
        parent::setUserAgent($ua);
        $this->appHints->setUserAgent($ua);
    }
    /**
     * Sets the Cache class
     *
     * @param CacheInterface $cache
     */
    public function setCache(CacheInterface $cache) : void
    {
        parent::setCache($cache);
        $this->appHints->setCache($cache);
    }
    /**
     * Sets the YamlParser class
     *
     * @param YamlParser $yamlParser
     */
    public function setYamlParser(YamlParser $yamlParser) : void
    {
        parent::setYamlParser($yamlParser);
        $this->appHints->setYamlParser($this->getYamlParser());
    }
    /**
     * Parses the current UA and checks whether it contains any client information
     * See parent::parse() for more details.
     *
     * @return array|null
     */
    public function parse() : ?array
    {
        $result = parent::parse();
        $name = $result['name'] ?? '';
        $version = $result['version'] ?? '';
        $appHash = $this->appHints->parse();
        if (null !== $appHash && $appHash['name'] !== $name) {
            $name = $appHash['name'];
            $version = '';
        }
        if (empty($name)) {
            return null;
        }
        return ['type' => $this->parserName, 'name' => $name, 'version' => $version];
    }
}

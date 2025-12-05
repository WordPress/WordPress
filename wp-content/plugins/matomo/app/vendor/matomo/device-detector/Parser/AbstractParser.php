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

use DeviceDetector\Cache\CacheInterface;
use DeviceDetector\Cache\StaticCache;
use DeviceDetector\ClientHints;
use DeviceDetector\DeviceDetector;
use DeviceDetector\Yaml\ParserInterface as YamlParser;
use DeviceDetector\Yaml\Spyc;
/**
 * Class AbstractParser
 */
abstract class AbstractParser
{
    /**
     * Holds the path to the yml file containing regexes
     * @var string
     */
    protected $fixtureFile;
    /**
     * Holds the internal name of the parser
     * Used for caching
     * @var string
     */
    protected $parserName;
    /**
     * Holds the user agent to be parsed
     * @var string
     */
    protected $userAgent;
    /**
     * Holds the client hints to be parsed
     * @var ?ClientHints
     */
    protected $clientHints = null;
    /**
     * Contains a list of mappings from names we use to known client hint values
     * @var array<string, array<string>>
     */
    protected static $clientHintMapping = [];
    /**
     * Holds an array with method that should be available global
     * @var array
     */
    protected $globalMethods;
    /**
     * Holds an array with regexes to parse, if already loaded
     * @var array
     */
    protected $regexList;
    /**
     * Holds the concatenated regex for all items in regex list
     * @var string
     */
    protected $overAllMatch;
    /**
     * Indicates how deep versioning will be detected
     * if $maxMinorParts is 0 only the major version will be returned
     * @var int
     */
    protected static $maxMinorParts = 1;
    /**
     * Versioning constant used to set max versioning to major version only
     * Version examples are: 3, 5, 6, 200, 123, ...
     */
    public const VERSION_TRUNCATION_MAJOR = 0;
    /**
     * Versioning constant used to set max versioning to minor version
     * Version examples are: 3.4, 5.6, 6.234, 0.200, 1.23, ...
     */
    public const VERSION_TRUNCATION_MINOR = 1;
    /**
     * Versioning constant used to set max versioning to path level
     * Version examples are: 3.4.0, 5.6.344, 6.234.2, 0.200.3, 1.2.3, ...
     */
    public const VERSION_TRUNCATION_PATCH = 2;
    /**
     * Versioning constant used to set versioning to build number
     * Version examples are: 3.4.0.12, 5.6.334.0, 6.234.2.3, 0.200.3.1, 1.2.3.0, ...
     */
    public const VERSION_TRUNCATION_BUILD = 3;
    /**
     * Versioning constant used to set versioning to unlimited (no truncation)
     */
    public const VERSION_TRUNCATION_NONE = -1;
    /**
     * @var CacheInterface|null
     */
    protected $cache = null;
    /**
     * @var YamlParser|null
     */
    protected $yamlParser = null;
    /**
     * parses the currently set useragents and returns possible results
     *
     * @return array|null
     */
    public abstract function parse() : ?array;
    /**
     * AbstractParser constructor.
     *
     * @param string       $ua
     * @param ?ClientHints $clientHints
     */
    public function __construct(string $ua = '', ?ClientHints $clientHints = null)
    {
        $this->setUserAgent($ua);
        $this->setClientHints($clientHints);
    }
    /**
     * @inheritdoc
     */
    public function restoreUserAgentFromClientHints() : void
    {
        if (null === $this->clientHints) {
            return;
        }
        $deviceModel = $this->clientHints->getModel();
        if ('' === $deviceModel) {
            return;
        }
        // Restore Android User Agent
        if ($this->hasUserAgentClientHintsFragment()) {
            $osVersion = $this->clientHints->getOperatingSystemVersion();
            $this->setUserAgent((string) \preg_replace('(Android (?:10[.\\d]*; K|1[1-5]))', \sprintf('Android %s; %s', '' !== $osVersion ? $osVersion : '10', $deviceModel), $this->userAgent));
        }
        // Restore Desktop User Agent
        if (!$this->hasDesktopFragment()) {
            return;
        }
        $this->setUserAgent((string) \preg_replace('(X11; Linux x86_64)', \sprintf('X11; Linux x86_64; %s', $deviceModel), $this->userAgent));
    }
    /**
     * Set how DeviceDetector should return versions
     * @param int $type Any of the VERSION_TRUNCATION_* constants
     */
    public static function setVersionTruncation(int $type) : void
    {
        if (!\in_array($type, [self::VERSION_TRUNCATION_BUILD, self::VERSION_TRUNCATION_NONE, self::VERSION_TRUNCATION_MAJOR, self::VERSION_TRUNCATION_MINOR, self::VERSION_TRUNCATION_PATCH])) {
            return;
        }
        static::$maxMinorParts = $type;
    }
    /**
     * Sets the user agent to parse
     *
     * @param string $ua user agent
     */
    public function setUserAgent(string $ua) : void
    {
        $this->userAgent = $ua;
    }
    /**
     * Sets the client hints to parse
     *
     * @param ?ClientHints $clientHints client hints
     */
    public function setClientHints(?ClientHints $clientHints) : void
    {
        $this->clientHints = $clientHints;
    }
    /**
     * Returns the internal name of the parser
     *
     * @return string
     */
    public function getName() : string
    {
        return $this->parserName;
    }
    /**
     * Sets the Cache class
     *
     * @param CacheInterface $cache
     */
    public function setCache(CacheInterface $cache) : void
    {
        $this->cache = $cache;
    }
    /**
     * Returns Cache object
     *
     * @return CacheInterface
     */
    public function getCache() : CacheInterface
    {
        if (!empty($this->cache)) {
            return $this->cache;
        }
        return new StaticCache();
    }
    /**
     * Sets the YamlParser class
     *
     * @param YamlParser $yamlParser
     */
    public function setYamlParser(YamlParser $yamlParser) : void
    {
        $this->yamlParser = $yamlParser;
    }
    /**
     * Returns YamlParser object
     *
     * @return YamlParser
     */
    public function getYamlParser() : YamlParser
    {
        if (!empty($this->yamlParser)) {
            return $this->yamlParser;
        }
        return new Spyc();
    }
    /**
     * Returns the result of the parsed yml file defined in $fixtureFile
     *
     * @return array
     */
    protected function getRegexes() : array
    {
        if (empty($this->regexList)) {
            $cacheKey = 'DeviceDetector-' . DeviceDetector::VERSION . 'regexes-' . $this->getName();
            $cacheKey = (string) \preg_replace('/([^a-z0-9_-]+)/i', '', $cacheKey);
            $cacheContent = $this->getCache()->fetch($cacheKey);
            if (\is_array($cacheContent)) {
                $this->regexList = $cacheContent;
            }
            if (empty($this->regexList)) {
                $parsedContent = $this->getYamlParser()->parseFile($this->getRegexesDirectory() . \DIRECTORY_SEPARATOR . $this->fixtureFile);
                if (!\is_array($parsedContent)) {
                    $parsedContent = [];
                }
                $this->regexList = $parsedContent;
                $this->getCache()->save($cacheKey, $this->regexList);
            }
        }
        return $this->regexList;
    }
    /**
     * Returns the provided name after applying client hint mappings.
     * This is used to map names provided in client hints to the names we use.
     *
     * @param string $name
     *
     * @return string
     */
    protected function applyClientHintMapping(string $name) : string
    {
        foreach (static::$clientHintMapping as $mappedName => $clientHints) {
            foreach ($clientHints as $clientHint) {
                if (\strtolower($name) === \strtolower($clientHint)) {
                    return $mappedName;
                }
            }
        }
        return $name;
    }
    /**
     * @return string
     */
    protected function getRegexesDirectory() : string
    {
        return \dirname(__DIR__);
    }
    /**
     * Returns if the parsed UA contains the 'Windows NT;' or 'X11; Linux x86_64' fragments
     *
     * @return bool
     */
    protected function hasDesktopFragment() : bool
    {
        $regexExcludeDesktopFragment = \implode('|', ['CE-HTML', ' Mozilla/|Andr[o0]id|Tablet|Mobile|iPhone|Windows Phone|ricoh|OculusBrowser', 'PicoBrowser|Lenovo|compatible; MSIE|Trident/|Tesla/|XBOX|FBMD/|ARM; ?([^)]+)']);
        return $this->matchUserAgent('(?:Windows (?:NT|IoT)|X11; Linux x86_64)') && !$this->matchUserAgent($regexExcludeDesktopFragment);
    }
    /**
     * Returns if the parsed UA contains the 'Android 10 K;' or Android 10 K Build/` fragment
     *
     * @return bool
     */
    protected function hasUserAgentClientHintsFragment() : bool
    {
        return (bool) \preg_match('~Android (?:10[.\\d]*; K(?: Build/|[;)])|1[1-5]\\)) AppleWebKit~i', $this->userAgent);
    }
    /**
     * Matches the useragent against the given regex
     *
     * @param string $regex
     *
     * @return ?array
     *
     * @throws \Exception
     */
    protected function matchUserAgent(string $regex) : ?array
    {
        $matches = [];
        // only match if useragent begins with given regex or there is no letter before it
        $regex = '/(?:^|[^A-Z0-9_-]|[^A-Z0-9-]_|sprd-|MZ-)(?:' . \str_replace('/', '\\/', $regex) . ')/i';
        try {
            if (\preg_match($regex, $this->userAgent, $matches)) {
                return $matches;
            }
        } catch (\Exception $exception) {
            throw new \Exception(\sprintf("%s\nRegex: %s", $exception->getMessage(), $regex), $exception->getCode(), $exception);
        }
        return null;
    }
    /**
     * @param string $item
     * @param array  $matches
     *
     * @return string
     */
    protected function buildByMatch(string $item, array $matches) : string
    {
        $search = [];
        $replace = [];
        for ($nb = 1; $nb <= \count($matches); $nb++) {
            $search[] = '$' . $nb;
            $replace[] = $matches[$nb] ?? '';
        }
        return \trim(\str_replace($search, $replace, $item));
    }
    /**
     * Builds the version with the given $versionString and $matches
     *
     * Example:
     * $versionString = 'v$2'
     * $matches = ['version_1_0_1', '1_0_1']
     * return value would be v1.0.1
     *
     * @param string $versionString
     * @param array  $matches
     *
     * @return string
     */
    protected function buildVersion(string $versionString, array $matches) : string
    {
        $versionString = $this->buildByMatch($versionString, $matches);
        $versionString = \str_replace('_', '.', $versionString);
        if (self::VERSION_TRUNCATION_NONE !== static::$maxMinorParts && \substr_count($versionString, '.') > static::$maxMinorParts) {
            $versionParts = \explode('.', $versionString);
            $versionParts = \array_slice($versionParts, 0, 1 + static::$maxMinorParts);
            $versionString = \implode('.', $versionParts);
        }
        return \trim($versionString, ' .');
    }
    /**
     * Tests the useragent against a combination of all regexes
     *
     * All regexes returned by getRegexes() will be reversed and concatenated with '|'
     * Afterwards the big regex will be tested against the user agent
     *
     * Method can be used to speed up detections by making a big check before doing checks for every single regex
     *
     * @return ?array
     */
    protected function preMatchOverall() : ?array
    {
        $regexes = $this->getRegexes();
        $cacheKey = $this->parserName . DeviceDetector::VERSION . '-all';
        $cacheKey = (string) \preg_replace('/([^a-z0-9_-]+)/i', '', $cacheKey);
        if (empty($this->overAllMatch)) {
            $overAllMatch = $this->getCache()->fetch($cacheKey);
            if (\is_string($overAllMatch)) {
                $this->overAllMatch = $overAllMatch;
            }
        }
        if (empty($this->overAllMatch)) {
            // reverse all regexes, so we have the generic one first, which already matches most patterns
            $this->overAllMatch = \array_reduce(\array_reverse($regexes), static function ($val1, $val2) {
                return !empty($val1) ? $val1 . '|' . $val2['regex'] : $val2['regex'];
            });
            $this->getCache()->save($cacheKey, $this->overAllMatch);
        }
        return $this->matchUserAgent($this->overAllMatch);
    }
    /**
     * Compares if two strings equals after lowering their case and removing spaces
     *
     * @param string $value1
     * @param string $value2
     *
     * @return bool
     */
    protected function fuzzyCompare(string $value1, string $value2) : bool
    {
        return \str_replace(' ', '', \strtolower($value1)) === \str_replace(' ', '', \strtolower($value2));
    }
}

<?php

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link https://matomo.org
 *
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */
declare (strict_types=1);
namespace DeviceDetector;

use DeviceDetector\Cache\CacheInterface;
use DeviceDetector\Cache\StaticCache;
use DeviceDetector\Parser\AbstractBotParser;
use DeviceDetector\Parser\Bot;
use DeviceDetector\Parser\Client\AbstractClientParser;
use DeviceDetector\Parser\Client\Browser;
use DeviceDetector\Parser\Client\FeedReader;
use DeviceDetector\Parser\Client\Library;
use DeviceDetector\Parser\Client\MediaPlayer;
use DeviceDetector\Parser\Client\MobileApp;
use DeviceDetector\Parser\Client\PIM;
use DeviceDetector\Parser\Device\AbstractDeviceParser;
use DeviceDetector\Parser\Device\Camera;
use DeviceDetector\Parser\Device\CarBrowser;
use DeviceDetector\Parser\Device\Console;
use DeviceDetector\Parser\Device\HbbTv;
use DeviceDetector\Parser\Device\Mobile;
use DeviceDetector\Parser\Device\Notebook;
use DeviceDetector\Parser\Device\PortableMediaPlayer;
use DeviceDetector\Parser\Device\ShellTv;
use DeviceDetector\Parser\OperatingSystem;
use DeviceDetector\Parser\VendorFragment;
use DeviceDetector\Yaml\ParserInterface as YamlParser;
use DeviceDetector\Yaml\Spyc;
/**
 * Class DeviceDetector
 *
 * Magic Device Type Methods:
 * @method bool isSmartphone()
 * @method bool isFeaturePhone()
 * @method bool isTablet()
 * @method bool isPhablet()
 * @method bool isConsole()
 * @method bool isPortableMediaPlayer()
 * @method bool isCarBrowser()
 * @method bool isTV()
 * @method bool isSmartDisplay()
 * @method bool isSmartSpeaker()
 * @method bool isCamera()
 * @method bool isWearable()
 * @method bool isPeripheral()
 *
 * Magic Client Type Methods:
 * @method bool isBrowser()
 * @method bool isFeedReader()
 * @method bool isMobileApp()
 * @method bool isPIM()
 * @method bool isLibrary()
 * @method bool isMediaPlayer()
 */
class DeviceDetector
{
    /**
     * Current version number of DeviceDetector
     */
    public const VERSION = '6.4.5';
    /**
     * Constant used as value for unknown browser / os
     */
    public const UNKNOWN = 'UNK';
    /**
     * Holds all registered client types
     * @var array
     */
    protected $clientTypes = [];
    /**
     * Holds the useragent that should be parsed
     * @var string
     */
    protected $userAgent = '';
    /**
     * Holds the client hints that should be parsed
     * @var ?ClientHints
     */
    protected $clientHints = null;
    /**
     * Holds the operating system data after parsing the UA
     * @var ?array
     */
    protected $os = null;
    /**
     * Holds the client data after parsing the UA
     * @var ?array
     */
    protected $client = null;
    /**
     * Holds the device type after parsing the UA
     * @var ?int
     */
    protected $device = null;
    /**
     * Holds the device brand data after parsing the UA
     * @var string
     */
    protected $brand = '';
    /**
     * Holds the device model data after parsing the UA
     * @var string
     */
    protected $model = '';
    /**
     * Holds bot information if parsing the UA results in a bot
     * (All other information attributes will stay empty in that case)
     *
     * If $discardBotInformation is set to true, this property will be set to
     * true if parsed UA is identified as bot, additional information will be not available
     *
     * If $skipBotDetection is set to true, bot detection will not be performed and isBot will
     * always be false
     *
     * @var array|bool|null
     */
    protected $bot = null;
    /**
     * @var bool
     */
    protected $discardBotInformation = \false;
    /**
     * @var bool
     */
    protected $skipBotDetection = \false;
    /**
     * Holds the cache class used for caching the parsed yml-Files
     * @var CacheInterface|null
     */
    protected $cache = null;
    /**
     * Holds the parser class used for parsing yml-Files
     * @var YamlParser|null
     */
    protected $yamlParser = null;
    /**
     * @var array<AbstractClientParser>
     */
    protected $clientParsers = [];
    /**
     * @var array<AbstractDeviceParser>
     */
    protected $deviceParsers = [];
    /**
     * @var array<AbstractBotParser>
     */
    public $botParsers = [];
    /**
     * @var bool
     */
    private $parsed = \false;
    /**
     * Constructor
     *
     * @param string      $userAgent   UA to parse
     * @param ClientHints $clientHints Browser client hints to parse
     */
    public function __construct(string $userAgent = '', ?\DeviceDetector\ClientHints $clientHints = null)
    {
        if ('' !== $userAgent) {
            $this->setUserAgent($userAgent);
        }
        if ($clientHints instanceof \DeviceDetector\ClientHints) {
            $this->setClientHints($clientHints);
        }
        $this->addClientParser(new FeedReader());
        $this->addClientParser(new MobileApp());
        $this->addClientParser(new MediaPlayer());
        $this->addClientParser(new PIM());
        $this->addClientParser(new Browser());
        $this->addClientParser(new Library());
        $this->addDeviceParser(new HbbTv());
        $this->addDeviceParser(new ShellTv());
        $this->addDeviceParser(new Notebook());
        $this->addDeviceParser(new Console());
        $this->addDeviceParser(new CarBrowser());
        $this->addDeviceParser(new Camera());
        $this->addDeviceParser(new PortableMediaPlayer());
        $this->addDeviceParser(new Mobile());
        $this->addBotParser(new Bot());
    }
    /**
     * @param string $methodName
     * @param array  $arguments
     *
     * @return bool
     */
    public function __call(string $methodName, array $arguments) : bool
    {
        foreach (AbstractDeviceParser::getAvailableDeviceTypes() as $deviceName => $deviceType) {
            if (\strtolower($methodName) === 'is' . \strtolower(\str_replace(' ', '', $deviceName))) {
                return $this->getDevice() === $deviceType;
            }
        }
        foreach ($this->clientTypes as $client) {
            if (\strtolower($methodName) === 'is' . \strtolower(\str_replace(' ', '', $client))) {
                return $this->getClient('type') === $client;
            }
        }
        throw new \BadMethodCallException("Method {$methodName} not found");
    }
    /**
     * Sets the useragent to be parsed
     *
     * @param string $userAgent
     */
    public function setUserAgent(string $userAgent) : void
    {
        if ($this->userAgent !== $userAgent) {
            $this->reset();
        }
        $this->userAgent = $userAgent;
    }
    /**
     * Sets the browser client hints to be parsed
     *
     * @param ?ClientHints $clientHints
     */
    public function setClientHints(?\DeviceDetector\ClientHints $clientHints = null) : void
    {
        if ($this->clientHints !== $clientHints) {
            $this->reset();
        }
        $this->clientHints = $clientHints;
    }
    /**
     * @param AbstractClientParser $parser
     *
     * @throws \Exception
     */
    public function addClientParser(AbstractClientParser $parser) : void
    {
        $this->clientParsers[] = $parser;
        $this->clientTypes[] = $parser->getName();
    }
    /**
     * @return array<AbstractClientParser>
     */
    public function getClientParsers() : array
    {
        return $this->clientParsers;
    }
    /**
     * @param AbstractDeviceParser $parser
     *
     * @throws \Exception
     */
    public function addDeviceParser(AbstractDeviceParser $parser) : void
    {
        $this->deviceParsers[] = $parser;
    }
    /**
     * @return array<AbstractDeviceParser>
     */
    public function getDeviceParsers() : array
    {
        return $this->deviceParsers;
    }
    /**
     * @param AbstractBotParser $parser
     */
    public function addBotParser(AbstractBotParser $parser) : void
    {
        $this->botParsers[] = $parser;
    }
    /**
     * @return array<AbstractBotParser>
     */
    public function getBotParsers() : array
    {
        return $this->botParsers;
    }
    /**
     * Sets whether to discard additional bot information
     * If information is discarded it's only possible check whether UA was detected as bot or not.
     * (Discarding information speeds up the detection a bit)
     *
     * @param bool $discard
     */
    public function discardBotInformation(bool $discard = \true) : void
    {
        $this->discardBotInformation = $discard;
    }
    /**
     * Sets whether to skip bot detection.
     * It is needed if we want bots to be processed as a simple clients. So we can detect if it is mobile client,
     * or desktop, or enything else. By default all this information is not retrieved for the bots.
     *
     * @param bool $skip
     */
    public function skipBotDetection(bool $skip = \true) : void
    {
        $this->skipBotDetection = $skip;
    }
    /**
     * Returns if the parsed UA was identified as a Bot
     *
     * @return bool
     *
     * @see bots.yml for a list of detected bots
     *
     */
    public function isBot() : bool
    {
        return !empty($this->bot);
    }
    /**
     * Returns if the parsed UA was identified as a touch enabled device
     *
     * Note: That only applies to windows 8 tablets
     *
     * @return bool
     */
    public function isTouchEnabled() : bool
    {
        $regex = 'Touch';
        return !!$this->matchUserAgent($regex);
    }
    /**
     * Returns if the parsed UA is detected as a mobile device
     *
     * @return bool
     */
    public function isMobile() : bool
    {
        // Client hints indicate a mobile device
        if ($this->clientHints instanceof \DeviceDetector\ClientHints && $this->clientHints->isMobile()) {
            return \true;
        }
        // Mobile device types
        if (\in_array($this->device, [AbstractDeviceParser::DEVICE_TYPE_FEATURE_PHONE, AbstractDeviceParser::DEVICE_TYPE_SMARTPHONE, AbstractDeviceParser::DEVICE_TYPE_TABLET, AbstractDeviceParser::DEVICE_TYPE_PHABLET, AbstractDeviceParser::DEVICE_TYPE_CAMERA, AbstractDeviceParser::DEVICE_TYPE_PORTABLE_MEDIA_PAYER])) {
            return \true;
        }
        // non mobile device types
        if (\in_array($this->device, [AbstractDeviceParser::DEVICE_TYPE_TV, AbstractDeviceParser::DEVICE_TYPE_SMART_DISPLAY, AbstractDeviceParser::DEVICE_TYPE_CONSOLE])) {
            return \false;
        }
        // Check for browsers available for mobile devices only
        if ($this->usesMobileBrowser()) {
            return \true;
        }
        $osName = $this->getOs('name');
        if (empty($osName) || self::UNKNOWN === $osName) {
            return \false;
        }
        return !$this->isBot() && !$this->isDesktop();
    }
    /**
     * Returns if the parsed UA was identified as desktop device
     * Desktop devices are all devices with an unknown type that are running a desktop os
     *
     * @return bool
     *
     * @see OperatingSystem::$desktopOsArray
     *
     */
    public function isDesktop() : bool
    {
        $osName = $this->getOsAttribute('name');
        if (empty($osName) || self::UNKNOWN === $osName) {
            return \false;
        }
        // Check for browsers available for mobile devices only
        if ($this->usesMobileBrowser()) {
            return \false;
        }
        return OperatingSystem::isDesktopOs($osName);
    }
    /**
     * Returns the operating system data extracted from the parsed UA
     *
     * If $attr is given only that property will be returned
     *
     * @param string $attr property to return(optional)
     *
     * @return array|string|null
     */
    public function getOs(string $attr = '')
    {
        if ('' === $attr) {
            return $this->os;
        }
        return $this->getOsAttribute($attr);
    }
    /**
     * Returns the client data extracted from the parsed UA
     *
     * If $attr is given only that property will be returned
     *
     * @param string $attr property to return(optional)
     *
     * @return array|string|null
     */
    public function getClient(string $attr = '')
    {
        if ('' === $attr) {
            return $this->client;
        }
        return $this->getClientAttribute($attr);
    }
    /**
     * Returns the device type extracted from the parsed UA
     *
     * @return int|null
     *
     * @see AbstractDeviceParser::$deviceTypes for available device types
     *
     */
    public function getDevice() : ?int
    {
        return $this->device;
    }
    /**
     * Returns the device type extracted from the parsed UA
     *
     * @return string
     *
     * @see AbstractDeviceParser::$deviceTypes for available device types
     *
     */
    public function getDeviceName() : string
    {
        if (null !== $this->getDevice()) {
            return AbstractDeviceParser::getDeviceName($this->getDevice());
        }
        return '';
    }
    /**
     * Returns the device brand extracted from the parsed UA
     *
     * @return string
     *
     * @see self::$deviceBrand for available device brands
     *
     * @deprecated since 4.0 - short codes might be removed in next major release
     */
    public function getBrand() : string
    {
        return AbstractDeviceParser::getShortCode($this->brand);
    }
    /**
     * Returns the full device brand name extracted from the parsed UA
     *
     * @return string
     *
     * @see self::$deviceBrand for available device brands
     *
     */
    public function getBrandName() : string
    {
        return $this->brand;
    }
    /**
     * Returns the device model extracted from the parsed UA
     *
     * @return string
     */
    public function getModel() : string
    {
        return $this->model;
    }
    /**
     * Returns the user agent that is set to be parsed
     *
     * @return string
     */
    public function getUserAgent() : string
    {
        return $this->userAgent;
    }
    /**
     * Returns the client hints that is set to be parsed
     *
     * @return ?ClientHints
     */
    public function getClientHints() : ?\DeviceDetector\ClientHints
    {
        return $this->clientHints;
    }
    /**
     * Returns the bot extracted from the parsed UA
     *
     * @return array|bool|null
     */
    public function getBot()
    {
        return $this->bot;
    }
    /**
     * Returns true, if userAgent was already parsed with parse()
     *
     * @return bool
     */
    public function isParsed() : bool
    {
        return $this->parsed;
    }
    /**
     * Triggers the parsing of the current user agent
     */
    public function parse() : void
    {
        if ($this->isParsed()) {
            return;
        }
        $this->parsed = \true;
        // skip parsing for empty useragents or those not containing any letter (if no client hints were provided)
        if ((empty($this->userAgent) || !\preg_match('/([a-z])/i', $this->userAgent)) && empty($this->clientHints)) {
            return;
        }
        $this->parseBot();
        if ($this->isBot()) {
            return;
        }
        $this->parseOs();
        /**
         * Parse Clients
         * Clients might be browsers, Feed Readers, Mobile Apps, Media Players or
         * any other application accessing with an parseable UA
         */
        $this->parseClient();
        $this->parseDevice();
    }
    /**
     * Parses a useragent and returns the detected data
     *
     * ATTENTION: Use that method only for testing or very small applications
     * To get fast results from DeviceDetector you need to make your own implementation,
     * that should use one of the caching mechanisms. See README.md for more information.
     *
     * @param string       $ua          UserAgent to parse
     * @param ?ClientHints $clientHints Client Hints to parse
     *
     * @return array
     *
     * @deprecated
     *
     * @internal
     *
     */
    public static function getInfoFromUserAgent(string $ua, ?\DeviceDetector\ClientHints $clientHints = null) : array
    {
        static $deviceDetector;
        if (!$deviceDetector instanceof \DeviceDetector\DeviceDetector) {
            $deviceDetector = new \DeviceDetector\DeviceDetector();
        }
        $deviceDetector->setUserAgent($ua);
        $deviceDetector->setClientHints($clientHints);
        $deviceDetector->parse();
        if ($deviceDetector->isBot()) {
            return ['user_agent' => $deviceDetector->getUserAgent(), 'bot' => $deviceDetector->getBot()];
        }
        /** @var array $client */
        $client = $deviceDetector->getClient();
        $browserFamily = 'Unknown';
        if ($deviceDetector->isBrowser() && \true === \is_array($client) && \true === \array_key_exists('family', $client) && null !== $client['family']) {
            $browserFamily = $client['family'];
        }
        unset($client['short_name'], $client['family']);
        /** @var array $os */
        $os = $deviceDetector->getOs();
        $osFamily = $os['family'] ?? 'Unknown';
        unset($os['short_name'], $os['family']);
        return ['user_agent' => $deviceDetector->getUserAgent(), 'os' => $os, 'client' => $client, 'device' => ['type' => $deviceDetector->getDeviceName(), 'brand' => $deviceDetector->getBrandName(), 'model' => $deviceDetector->getModel()], 'os_family' => $osFamily, 'browser_family' => $browserFamily];
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
     * Sets the Yaml Parser class
     *
     * @param YamlParser $yamlParser
     */
    public function setYamlParser(YamlParser $yamlParser) : void
    {
        $this->yamlParser = $yamlParser;
    }
    /**
     * Returns Yaml Parser object
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
     * @param string $attr
     *
     * @return string
     */
    protected function getClientAttribute(string $attr) : string
    {
        if (!isset($this->client[$attr])) {
            return self::UNKNOWN;
        }
        return $this->client[$attr];
    }
    /**
     * @param string $attr
     *
     * @return string
     */
    protected function getOsAttribute(string $attr) : string
    {
        if (!isset($this->os[$attr])) {
            return self::UNKNOWN;
        }
        return $this->os[$attr];
    }
    /**
     * Returns if the parsed UA contains the 'Android; Tablet;' fragment
     *
     * @return bool
     */
    protected function hasAndroidTableFragment() : bool
    {
        $regex = 'Android( [.0-9]+)?; Tablet;|Tablet(?! PC)|.*\\-tablet$';
        return !!$this->matchUserAgent($regex);
    }
    /**
     * Returns if the parsed UA contains the 'Android; Mobile;' fragment
     *
     * @return bool
     */
    protected function hasAndroidMobileFragment() : bool
    {
        $regex = 'Android( [.0-9]+)?; Mobile;|.*\\-mobile$';
        return !!$this->matchUserAgent($regex);
    }
    /**
     * Returns if the parsed UA contains the 'Android; Mobile VR;' fragment
     *
     * @return bool
     */
    protected function hasAndroidVRFragment() : bool
    {
        $regex = 'Android( [.0-9]+)?; Mobile VR;| VR ';
        return !!$this->matchUserAgent($regex);
    }
    /**
     * Returns if the parsed UA contains the 'Desktop;', 'Desktop x32;', 'Desktop x64;' or 'Desktop WOW64;' fragment
     *
     * @return bool
     */
    protected function hasDesktopFragment() : bool
    {
        $regex = 'Desktop(?: (x(?:32|64)|WOW64))?;';
        return !!$this->matchUserAgent($regex);
    }
    /**
     * Returns if the parsed UA contains usage of a mobile only browser
     *
     * @return bool
     */
    protected function usesMobileBrowser() : bool
    {
        return 'browser' === $this->getClient('type') && Browser::isMobileOnlyBrowser($this->getClientAttribute('name'));
    }
    /**
     * Parses the UA for bot information using the Bot parser
     */
    protected function parseBot() : void
    {
        if ($this->skipBotDetection) {
            $this->bot = \false;
            return;
        }
        $parsers = $this->getBotParsers();
        foreach ($parsers as $parser) {
            $parser->setYamlParser($this->getYamlParser());
            $parser->setCache($this->getCache());
            $parser->setUserAgent($this->getUserAgent());
            $parser->setClientHints($this->getClientHints());
            if ($this->discardBotInformation) {
                $parser->discardDetails();
            }
            $bot = $parser->parse();
            if (!empty($bot)) {
                $this->bot = $bot;
                break;
            }
        }
    }
    /**
     * Tries to detect the client (e.g. browser, mobile app, ...)
     */
    protected function parseClient() : void
    {
        $parsers = $this->getClientParsers();
        foreach ($parsers as $parser) {
            $parser->setYamlParser($this->getYamlParser());
            $parser->setCache($this->getCache());
            $parser->setUserAgent($this->getUserAgent());
            $parser->setClientHints($this->getClientHints());
            $client = $parser->parse();
            if (!empty($client)) {
                $this->client = $client;
                break;
            }
        }
    }
    /**
     * Tries to detect the device type, model and brand
     */
    protected function parseDevice() : void
    {
        $parsers = $this->getDeviceParsers();
        foreach ($parsers as $parser) {
            $parser->setYamlParser($this->getYamlParser());
            $parser->setCache($this->getCache());
            $parser->setUserAgent($this->getUserAgent());
            $parser->setClientHints($this->getClientHints());
            if ($parser->parse()) {
                $this->device = $parser->getDeviceType();
                $this->model = $parser->getModel();
                $this->brand = $parser->getBrand();
                break;
            }
        }
        /**
         * If no model could be parsed from useragent, we use the one from client hints if available
         */
        if ($this->clientHints instanceof \DeviceDetector\ClientHints && empty($this->model)) {
            $this->model = $this->clientHints->getModel();
        }
        /**
         * If no brand has been assigned try to match by known vendor fragments
         */
        if (empty($this->brand)) {
            $vendorParser = new VendorFragment($this->getUserAgent());
            $vendorParser->setYamlParser($this->getYamlParser());
            $vendorParser->setCache($this->getCache());
            $this->brand = $vendorParser->parse()['brand'] ?? '';
        }
        $osName = $this->getOsAttribute('name');
        $osFamily = $this->getOsAttribute('family');
        $osVersion = $this->getOsAttribute('version');
        $clientName = $this->getClientAttribute('name');
        $appleOsNames = ['iPadOS', 'tvOS', 'watchOS', 'iOS', 'Mac'];
        /**
         * if it's fake UA then it's best not to identify it as Apple running Android OS or GNU/Linux
         */
        if ('Apple' === $this->brand && !\in_array($osName, $appleOsNames)) {
            $this->device = null;
            $this->brand = '';
            $this->model = '';
        }
        /**
         * Assume all devices running iOS / Mac OS are from Apple
         */
        if (empty($this->brand) && \in_array($osName, $appleOsNames)) {
            $this->brand = 'Apple';
        }
        /**
         * All devices containing VR fragment are assumed to be a wearable
         */
        if (null === $this->device && $this->hasAndroidVRFragment()) {
            $this->device = AbstractDeviceParser::DEVICE_TYPE_WEARABLE;
        }
        /**
         * Chrome on Android passes the device type based on the keyword 'Mobile'
         * If it is present the device should be a smartphone, otherwise it's a tablet
         * See https://developer.chrome.com/multidevice/user-agent#chrome_for_android_user_agent
         * Note: We do not check for browser (family) here, as there might be mobile apps using Chrome, that won't have
         *       a detected browser, but can still be detected. So we check the useragent for Chrome instead.
         */
        if (null === $this->device && 'Android' === $osFamily && $this->matchUserAgent('Chrome/[.0-9]*')) {
            if ($this->matchUserAgent('(?:Mobile|eliboM)')) {
                $this->device = AbstractDeviceParser::DEVICE_TYPE_SMARTPHONE;
            } else {
                $this->device = AbstractDeviceParser::DEVICE_TYPE_TABLET;
            }
        }
        /**
         * Some UA contain the fragment 'Pad/APad', so we assume those devices as tablets
         */
        if (AbstractDeviceParser::DEVICE_TYPE_SMARTPHONE === $this->device && $this->matchUserAgent('Pad/APad')) {
            $this->device = AbstractDeviceParser::DEVICE_TYPE_TABLET;
        }
        /**
         * Some UA contain the fragment 'Android; Tablet;' or 'Opera Tablet', so we assume those devices as tablets
         */
        if (null === $this->device && ($this->hasAndroidTableFragment() || $this->matchUserAgent('Opera Tablet'))) {
            $this->device = AbstractDeviceParser::DEVICE_TYPE_TABLET;
        }
        /**
         * Some user agents simply contain the fragment 'Android; Mobile;', so we assume those devices as smartphones
         */
        if (null === $this->device && $this->hasAndroidMobileFragment()) {
            $this->device = AbstractDeviceParser::DEVICE_TYPE_SMARTPHONE;
        }
        /**
         * Android up to 3.0 was designed for smartphones only. But as 3.0, which was tablet only, was published
         * too late, there were a bunch of tablets running with 2.x
         * With 4.0 the two trees were merged and it is for smartphones and tablets
         *
         * So were are expecting that all devices running Android < 2 are smartphones
         * Devices running Android 3.X are tablets. Device type of Android 2.X and 4.X+ are unknown
         */
        if (null === $this->device && 'Android' === $osName && '' !== $osVersion) {
            if (-1 === \version_compare($osVersion, '2.0')) {
                $this->device = AbstractDeviceParser::DEVICE_TYPE_SMARTPHONE;
            } elseif (\version_compare($osVersion, '3.0') >= 0 && -1 === \version_compare($osVersion, '4.0')) {
                $this->device = AbstractDeviceParser::DEVICE_TYPE_TABLET;
            }
        }
        /**
         * All detected feature phones running android are more likely a smartphone
         */
        if (AbstractDeviceParser::DEVICE_TYPE_FEATURE_PHONE === $this->device && 'Android' === $osFamily) {
            $this->device = AbstractDeviceParser::DEVICE_TYPE_SMARTPHONE;
        }
        /**
         * All unknown devices under running Java ME are more likely features phones
         */
        if ('Java ME' === $osName && null === $this->device) {
            $this->device = AbstractDeviceParser::DEVICE_TYPE_FEATURE_PHONE;
        }
        /**
         * All devices running KaiOS are more likely features phones
         */
        if ('KaiOS' === $osName) {
            $this->device = AbstractDeviceParser::DEVICE_TYPE_FEATURE_PHONE;
        }
        /**
         * According to http://msdn.microsoft.com/en-us/library/ie/hh920767(v=vs.85).aspx
         * Internet Explorer 10 introduces the "Touch" UA string token. If this token is present at the end of the
         * UA string, the computer has touch capability, and is running Windows 8 (or later).
         * This UA string will be transmitted on a touch-enabled system running Windows 8 (RT)
         *
         * As most touch enabled devices are tablets and only a smaller part are desktops/notebooks we assume that
         * all Windows 8 touch devices are tablets.
         */
        if (null === $this->device && ('Windows RT' === $osName || 'Windows' === $osName && \version_compare($osVersion, '8') >= 0) && $this->isTouchEnabled()) {
            $this->device = AbstractDeviceParser::DEVICE_TYPE_TABLET;
        }
        /**
         * All devices running Puffin Secure Browser that contain letter 'D' are assumed to be desktops
         */
        if (null === $this->device && $this->matchUserAgent('Puffin/(?:\\d+[.\\d]+)[LMW]D')) {
            $this->device = AbstractDeviceParser::DEVICE_TYPE_DESKTOP;
        }
        /**
         * All devices running Puffin Web Browser that contain letter 'P' are assumed to be smartphones
         */
        if (null === $this->device && $this->matchUserAgent('Puffin/(?:\\d+[.\\d]+)[AIFLW]P')) {
            $this->device = AbstractDeviceParser::DEVICE_TYPE_SMARTPHONE;
        }
        /**
         * All devices running Puffin Web Browser that contain letter 'T' are assumed to be tablets
         */
        if (null === $this->device && $this->matchUserAgent('Puffin/(?:\\d+[.\\d]+)[AILW]T')) {
            $this->device = AbstractDeviceParser::DEVICE_TYPE_TABLET;
        }
        /**
         * All devices running Opera TV Store are assumed to be a tv
         */
        if ($this->matchUserAgent('Opera TV Store| OMI/')) {
            $this->device = AbstractDeviceParser::DEVICE_TYPE_TV;
        }
        /**
         * All devices running Coolita OS are assumed to be a tv
         */
        if ('Coolita OS' === $osName) {
            $this->device = AbstractDeviceParser::DEVICE_TYPE_TV;
        }
        /**
         * All devices that contain Andr0id in string are assumed to be a tv
         */
        $hasDeviceTvType = \false === \in_array($this->device, [AbstractDeviceParser::DEVICE_TYPE_TV, AbstractDeviceParser::DEVICE_TYPE_PERIPHERAL]) && $this->matchUserAgent('Andr0id|(?:Android(?: UHD)?|Google) TV|\\(lite\\) TV|BRAVIA| TV$');
        if ($hasDeviceTvType) {
            $this->device = AbstractDeviceParser::DEVICE_TYPE_TV;
        }
        /**
         * All devices running Tizen TV or SmartTV are assumed to be a tv
         */
        if (null === $this->device && $this->matchUserAgent('SmartTV|Tizen.+ TV .+$')) {
            $this->device = AbstractDeviceParser::DEVICE_TYPE_TV;
        }
        /**
         * Devices running those clients are assumed to be a TV
         */
        if (\in_array($clientName, ['Kylo', 'Espial TV Browser', 'LUJO TV Browser', 'LogicUI TV Browser', 'Open TV Browser', 'Seraphic Sraf', 'Opera Devices', 'Crow Browser', 'Vewd Browser', 'TiviMate', 'Quick Search TV', 'QJY TV Browser', 'TV Bro'])) {
            $this->device = AbstractDeviceParser::DEVICE_TYPE_TV;
        }
        /**
         * All devices containing TV fragment are assumed to be a tv
         */
        if (null === $this->device && $this->matchUserAgent('\\(TV;')) {
            $this->device = AbstractDeviceParser::DEVICE_TYPE_TV;
        }
        /**
         * Set device type desktop if string ua contains desktop
         */
        $hasDesktop = AbstractDeviceParser::DEVICE_TYPE_DESKTOP !== $this->device && \false !== \strpos($this->userAgent, 'Desktop') && $this->hasDesktopFragment();
        if ($hasDesktop) {
            $this->device = AbstractDeviceParser::DEVICE_TYPE_DESKTOP;
        }
        // set device type to desktop for all devices running a desktop os that were not detected as another device type
        if (null !== $this->device || !$this->isDesktop()) {
            return;
        }
        $this->device = AbstractDeviceParser::DEVICE_TYPE_DESKTOP;
    }
    /**
     * Tries to detect the operating system
     */
    protected function parseOs() : void
    {
        $osParser = new OperatingSystem();
        $osParser->setUserAgent($this->getUserAgent());
        $osParser->setClientHints($this->getClientHints());
        $osParser->setYamlParser($this->getYamlParser());
        $osParser->setCache($this->getCache());
        $this->os = $osParser->parse();
    }
    /**
     * @param string $regex
     *
     * @return array|null
     */
    protected function matchUserAgent(string $regex) : ?array
    {
        $regex = '/(?:^|[^A-Z_-])(?:' . \str_replace('/', '\\/', $regex) . ')/i';
        if (\preg_match($regex, $this->userAgent, $matches)) {
            return $matches;
        }
        return null;
    }
    /**
     * Resets all detected data
     */
    protected function reset() : void
    {
        $this->bot = null;
        $this->client = null;
        $this->device = null;
        $this->os = null;
        $this->brand = '';
        $this->model = '';
        $this->parsed = \false;
    }
}

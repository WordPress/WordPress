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

class ClientHints
{
    /**
     * Represents `Sec-CH-UA-Arch` header field: The underlying architecture's instruction set
     *
     * @var string
     */
    protected $architecture = '';
    /**
     * Represents `Sec-CH-UA-Bitness` header field: The underlying architecture's bitness
     *
     * @var string
     */
    protected $bitness = '';
    /**
     * Represents `Sec-CH-UA-Mobile` header field: whether the user agent should receive a specifically "mobile" UX
     *
     * @var bool
     */
    protected $mobile = \false;
    /**
     * Represents `Sec-CH-UA-Model` header field: the user agent's underlying device model
     *
     * @var string
     */
    protected $model = '';
    /**
     * Represents `Sec-CH-UA-Platform` header field: the platform's brand
     *
     * @var string
     */
    protected $platform = '';
    /**
     * Represents `Sec-CH-UA-Platform-Version` header field: the platform's major version
     *
     * @var string
     */
    protected $platformVersion = '';
    /**
     * Represents `Sec-CH-UA-Full-Version` header field: the platform's major version
     *
     * @var string
     */
    protected $uaFullVersion = '';
    /**
     * Represents `Sec-CH-UA-Full-Version-List` header field: the full version for each brand in its brand list
     *
     * @var array
     */
    protected $fullVersionList = [];
    /**
     * Represents `x-requested-with` header field: Android app id
     * @var string
     */
    protected $app = '';
    /**
     * Represents `Sec-CH-UA-Form-Factors` header field: form factor device type name
     *
     * @var array
     */
    protected $formFactors = [];
    /**
     * Constructor
     *
     * @param string $model           `Sec-CH-UA-Model` header field
     * @param string $platform        `Sec-CH-UA-Platform` header field
     * @param string $platformVersion `Sec-CH-UA-Platform-Version` header field
     * @param string $uaFullVersion   `Sec-CH-UA-Full-Version` header field
     * @param array  $fullVersionList `Sec-CH-UA-Full-Version-List` header field
     * @param bool   $mobile          `Sec-CH-UA-Mobile` header field
     * @param string $architecture    `Sec-CH-UA-Arch` header field
     * @param string $bitness         `Sec-CH-UA-Bitness`
     * @param string $app             `HTTP_X-REQUESTED-WITH`
     * @param array  $formFactors     `Sec-CH-UA-Form-Factors` header field
     */
    public function __construct(string $model = '', string $platform = '', string $platformVersion = '', string $uaFullVersion = '', array $fullVersionList = [], bool $mobile = \false, string $architecture = '', string $bitness = '', string $app = '', array $formFactors = [])
    {
        $this->model = $model;
        $this->platform = $platform;
        $this->platformVersion = $platformVersion;
        $this->uaFullVersion = $uaFullVersion;
        $this->fullVersionList = $fullVersionList;
        $this->mobile = $mobile;
        $this->architecture = $architecture;
        $this->bitness = $bitness;
        $this->app = $app;
        $this->formFactors = $formFactors;
    }
    /**
     * Magic method to directly allow accessing the protected properties
     *
     * @param string $variable
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function __get(string $variable)
    {
        if (\property_exists($this, $variable)) {
            return $this->{$variable};
        }
        throw new \Exception('Invalid ClientHint property requested.');
    }
    /**
     * Returns if the client hints
     *
     * @return bool
     */
    public function isMobile() : bool
    {
        return $this->mobile;
    }
    /**
     * Returns the Architecture
     *
     * @return string
     */
    public function getArchitecture() : string
    {
        return $this->architecture;
    }
    /**
     * Returns the Bitness
     *
     * @return string
     */
    public function getBitness() : string
    {
        return $this->bitness;
    }
    /**
     * Returns the device model
     *
     * @return string
     */
    public function getModel() : string
    {
        return $this->model;
    }
    /**
     * Returns the Operating System
     *
     * @return string
     */
    public function getOperatingSystem() : string
    {
        return $this->platform;
    }
    /**
     * Returns the Operating System version
     *
     * @return string
     */
    public function getOperatingSystemVersion() : string
    {
        return $this->platformVersion;
    }
    /**
     * Returns the Browser name
     *
     * @return array<string, string>
     */
    public function getBrandList() : array
    {
        if (\is_array($this->fullVersionList) && \count($this->fullVersionList)) {
            $brands = \array_column($this->fullVersionList, 'brand');
            $versions = \array_column($this->fullVersionList, 'version');
            if (\count($brands) === \count($versions)) {
                // @phpstan-ignore-next-line
                return \array_combine($brands, $versions);
            }
        }
        return [];
    }
    /**
     * Returns the Browser version
     *
     * @return string
     */
    public function getBrandVersion() : string
    {
        if (!empty($this->uaFullVersion)) {
            return $this->uaFullVersion;
        }
        return '';
    }
    /**
     * Returns the Android app id
     *
     * @return string
     */
    public function getApp() : string
    {
        return $this->app;
    }
    /**
     * Returns the formFactor device type name
     *
     * @return array
     */
    public function getFormFactors() : array
    {
        return $this->formFactors;
    }
    /**
     * Factory method to easily instantiate this class using an array containing all available (client hint) headers
     *
     * @param array $headers
     *
     * @return ClientHints
     */
    public static function factory(array $headers) : \DeviceDetector\ClientHints
    {
        $model = $platform = $platformVersion = $uaFullVersion = $architecture = $bitness = '';
        $app = '';
        $mobile = \false;
        $fullVersionList = [];
        $formFactors = [];
        foreach ($headers as $name => $value) {
            if (empty($value)) {
                continue;
            }
            switch (\str_replace('_', '-', \strtolower((string) $name))) {
                case 'http-sec-ch-ua-arch':
                case 'sec-ch-ua-arch':
                case 'arch':
                case 'architecture':
                    $architecture = \trim($value, '"');
                    break;
                case 'http-sec-ch-ua-bitness':
                case 'sec-ch-ua-bitness':
                case 'bitness':
                    $bitness = \trim($value, '"');
                    break;
                case 'http-sec-ch-ua-mobile':
                case 'sec-ch-ua-mobile':
                case 'mobile':
                    $mobile = \true === $value || '1' === $value || '?1' === $value;
                    break;
                case 'http-sec-ch-ua-model':
                case 'sec-ch-ua-model':
                case 'model':
                    $model = \trim($value, '"');
                    break;
                case 'http-sec-ch-ua-full-version':
                case 'sec-ch-ua-full-version':
                case 'uafullversion':
                    $uaFullVersion = \trim($value, '"');
                    break;
                case 'http-sec-ch-ua-platform':
                case 'sec-ch-ua-platform':
                case 'platform':
                    $platform = \trim($value, '"');
                    break;
                case 'http-sec-ch-ua-platform-version':
                case 'sec-ch-ua-platform-version':
                case 'platformversion':
                    $platformVersion = \trim($value, '"');
                    break;
                case 'brands':
                    if (!empty($fullVersionList)) {
                        break;
                    }
                // use this only if no other header already set the list
                case 'fullversionlist':
                    $fullVersionList = \is_array($value) ? $value : $fullVersionList;
                    break;
                case 'http-sec-ch-ua':
                case 'sec-ch-ua':
                    if (!empty($fullVersionList)) {
                        break;
                    }
                // use this only if no other header already set the list
                case 'http-sec-ch-ua-full-version-list':
                case 'sec-ch-ua-full-version-list':
                    $reg = '/^"([^"]+)"; ?v="([^"]+)"(?:, )?/';
                    $list = [];
                    while (\preg_match($reg, $value, $matches)) {
                        $list[] = ['brand' => $matches[1], 'version' => $matches[2]];
                        $value = \substr($value, \strlen($matches[0]));
                    }
                    if (\count($list)) {
                        $fullVersionList = $list;
                    }
                    break;
                case 'http-x-requested-with':
                case 'x-requested-with':
                    if ('xmlhttprequest' !== \strtolower($value)) {
                        $app = $value;
                    }
                    break;
                case 'formfactors':
                case 'http-sec-ch-ua-form-factors':
                case 'sec-ch-ua-form-factors':
                    if (\is_array($value)) {
                        $formFactors = \array_map('\\strtolower', $value);
                    } elseif (\preg_match_all('~"([a-z]+)"~i', \strtolower($value), $matches)) {
                        $formFactors = $matches[1];
                    }
                    break;
            }
        }
        return new self($model, $platform, $platformVersion, $uaFullVersion, $fullVersionList, $mobile, $architecture, $bitness, $app, $formFactors);
    }
}

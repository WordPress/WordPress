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
 * Class HbbTv
 *
 * Device parser for hbbtv detection
 */
class HbbTv extends \DeviceDetector\Parser\Device\AbstractDeviceParser
{
    /**
     * @var string
     */
    protected $fixtureFile = 'regexes/device/televisions.yml';
    /**
     * @var string
     */
    protected $parserName = 'tv';
    /**
     * Parses the current UA and checks whether it contains HbbTv or SmartTvA information
     *
     * @see televisions.yml for list of detected televisions
     *
     * @return array|null
     */
    public function parse() : ?array
    {
        // only parse user agents containing fragments: hbbtv or SmartTvA
        if (null === $this->isHbbTv()) {
            return null;
        }
        parent::parse();
        // always set device type to tv, even if no model/brand could be found
        if (null === $this->deviceType) {
            $this->deviceType = self::DEVICE_TYPE_TV;
        }
        return $this->getResult();
    }
    /**
     * Returns if the parsed UA was identified as a HbbTV device
     *
     * @return string|null
     */
    public function isHbbTv() : ?string
    {
        $regex = '(?:HbbTV|SmartTvA)/([1-9]{1}(?:\\.[0-9]{1}){1,2})';
        $match = $this->matchUserAgent($regex);
        return $match[1] ?? null;
    }
}

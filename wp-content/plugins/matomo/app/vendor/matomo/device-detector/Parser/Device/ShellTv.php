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
 * Class ShellTv
 */
class ShellTv extends \DeviceDetector\Parser\Device\AbstractDeviceParser
{
    /**
     * @var string
     */
    protected $fixtureFile = 'regexes/device/shell_tv.yml';
    /**
     * @var string
     */
    protected $parserName = 'shelltv';
    /**
     * Returns if the parsed UA was identified as ShellTv device
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function isShellTv() : bool
    {
        $regex = '[a-z]+[ _]Shell[ _]\\w{6}|tclwebkit(\\d+[.\\d]*)';
        $match = $this->matchUserAgent($regex);
        return null !== $match;
    }
    /**
     * Parses the current UA and checks whether it contains ShellTv information
     *
     * @see shell_tv.yml for list of detected televisions
     *
     * @return array|null
     */
    public function parse() : ?array
    {
        // only parse user agents containing fragments: {brand} shell
        if (\false === $this->isShellTv()) {
            return null;
        }
        parent::parse();
        // always set device type to tv, even if no model/brand could be found
        $this->deviceType = self::DEVICE_TYPE_TV;
        return $this->getResult();
    }
}

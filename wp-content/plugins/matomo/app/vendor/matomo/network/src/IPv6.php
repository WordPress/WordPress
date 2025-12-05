<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL v3 or later
 */
namespace Matomo\Network;

/**
 * IP v6 address.
 *
 * This class is immutable, i.e. once created it can't be changed. Methods that modify it
 * will always return a new instance.
 */
class IPv6 extends \Matomo\Network\IP
{
    const MAPPED_IPv4_START = '::ffff:';
    /**
     * {@inheritdoc}
     */
    public function anonymize($byteCount)
    {
        $newBinaryIp = $this->ip;
        if ($this->isMappedIPv4()) {
            $i = strlen($newBinaryIp);
            if ($byteCount > $i) {
                $byteCount = $i;
            }
            while ($byteCount-- > 0) {
                $newBinaryIp[--$i] = chr(0);
            }
            return self::fromBinaryIP($newBinaryIp);
        }
        $masks = array('ffff:ffff:ffff:ffff:ffff:ffff:ffff:ffff', 'ffff:ffff:ffff:ffff::', 'ffff:ffff:ffff:0000::', 'ffff:ff00:0000:0000::', '0000::');
        $newBinaryIp = $newBinaryIp & pack('a16', inet_pton($masks[$byteCount]));
        return self::fromBinaryIP($newBinaryIp);
    }
    /**
     * {@inheritdoc}
     */
    public function toIPv4String()
    {
        $str = $this->toString();
        if ($this->isMappedIPv4()) {
            return substr($str, strlen(self::MAPPED_IPv4_START));
        }
        return null;
    }
    /**
     * Returns true if this is a IPv4 mapped address, false otherwise.
     *
     * @return bool
     */
    public function isMappedIPv4()
    {
        return substr_compare($this->ip, "\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\xff\xff", 0, 12) === 0 || substr_compare($this->ip, "\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00", 0, 12) === 0;
    }
}

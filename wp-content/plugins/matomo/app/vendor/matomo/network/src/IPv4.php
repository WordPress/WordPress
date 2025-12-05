<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL v3 or later
 */
namespace Matomo\Network;

/**
 * IP v4 address.
 *
 * This class is immutable, i.e. once created it can't be changed. Methods that modify it
 * will always return a new instance.
 */
class IPv4 extends \Matomo\Network\IP
{
    /**
     * {@inheritdoc}
     */
    public function toIPv4String()
    {
        return $this->toString();
    }
    /**
     * {@inheritdoc}
     */
    public function anonymize($byteCount)
    {
        $newBinaryIp = $this->ip;
        $i = strlen($newBinaryIp);
        if ($byteCount > $i) {
            $byteCount = $i;
        }
        while ($byteCount-- > 0) {
            $newBinaryIp[--$i] = chr(0);
        }
        return self::fromBinaryIP($newBinaryIp);
    }
}

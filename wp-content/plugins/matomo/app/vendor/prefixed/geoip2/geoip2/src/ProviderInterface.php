<?php

declare (strict_types=1);
namespace Matomo\Dependencies\GeoIp2;

interface ProviderInterface
{
    /**
     * @param string $ipAddress an IPv4 or IPv6 address to lookup
     *
     * @return \GeoIp2\Model\Country a Country model for the requested IP address
     */
    public function country(string $ipAddress) : Model\Country;
    /**
     * @param string $ipAddress an IPv4 or IPv6 address to lookup
     *
     * @return \GeoIp2\Model\City a City model for the requested IP address
     */
    public function city(string $ipAddress) : Model\City;
}

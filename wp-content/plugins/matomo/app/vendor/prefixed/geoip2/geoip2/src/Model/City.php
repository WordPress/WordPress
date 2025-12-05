<?php

declare (strict_types=1);
namespace Matomo\Dependencies\GeoIp2\Model;

/**
 * Model class for the data returned by City Plus web service and City
 * database.
 *
 * See https://dev.maxmind.com/geoip/docs/web-services?lang=en for more
 * details.
 *
 * @property-read \GeoIp2\Record\City $city City data for the requested IP
 * address.
 * @property-read \GeoIp2\Record\Location $location Location data for the
 * requested IP address.
 * @property-read \GeoIp2\Record\Postal $postal Postal data for the
 * requested IP address.
 * @property-read array $subdivisions An array \GeoIp2\Record\Subdivision
 * objects representing the country subdivisions for the requested IP
 * address. The number and type of subdivisions varies by country, but a
 * subdivision is typically a state, province, county, etc. Subdivisions
 * are ordered from most general (largest) to most specific (smallest).
 * If the response did not contain any subdivisions, this method returns
 * an empty array.
 * @property-read \GeoIp2\Record\Subdivision $mostSpecificSubdivision An object
 * representing the most specific subdivision returned. If the response
 * did not contain any subdivisions, this method returns an empty
 * \GeoIp2\Record\Subdivision object.
 */
class City extends Country
{
    /**
     * @ignore
     *
     * @var \GeoIp2\Record\City
     */
    protected $city;
    /**
     * @ignore
     *
     * @var \GeoIp2\Record\Location
     */
    protected $location;
    /**
     * @ignore
     *
     * @var \GeoIp2\Record\Postal
     */
    protected $postal;
    /**
     * @ignore
     *
     * @var array<\GeoIp2\Record\Subdivision>
     */
    protected $subdivisions = [];
    /**
     * @ignore
     */
    public function __construct(array $raw, array $locales = ['en'])
    {
        parent::__construct($raw, $locales);
        $this->city = new \Matomo\Dependencies\GeoIp2\Record\City($this->get('city'), $locales);
        $this->location = new \Matomo\Dependencies\GeoIp2\Record\Location($this->get('location'));
        $this->postal = new \Matomo\Dependencies\GeoIp2\Record\Postal($this->get('postal'));
        $this->createSubdivisions($raw, $locales);
    }
    private function createSubdivisions(array $raw, array $locales) : void
    {
        if (!isset($raw['subdivisions'])) {
            return;
        }
        foreach ($raw['subdivisions'] as $sub) {
            $this->subdivisions[] = new \Matomo\Dependencies\GeoIp2\Record\Subdivision($sub, $locales);
        }
    }
    /**
     * @ignore
     *
     * @return mixed
     */
    public function __get(string $attr)
    {
        if ($attr === 'mostSpecificSubdivision') {
            return $this->{$attr}();
        }
        return parent::__get($attr);
    }
    /**
     * @ignore
     */
    public function __isset(string $attr) : bool
    {
        if ($attr === 'mostSpecificSubdivision') {
            // We always return a mostSpecificSubdivision, even if it is the
            // empty subdivision
            return \true;
        }
        return parent::__isset($attr);
    }
    private function mostSpecificSubdivision() : \Matomo\Dependencies\GeoIp2\Record\Subdivision
    {
        return empty($this->subdivisions) ? new \Matomo\Dependencies\GeoIp2\Record\Subdivision([], $this->locales) : end($this->subdivisions);
    }
}

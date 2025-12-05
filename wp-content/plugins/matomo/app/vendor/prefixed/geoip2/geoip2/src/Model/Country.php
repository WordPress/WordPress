<?php

declare (strict_types=1);
namespace Matomo\Dependencies\GeoIp2\Model;

/**
 * Model class for the data returned by GeoIP2 Country web service and database.
 *
 * See https://dev.maxmind.com/geoip/docs/web-services?lang=en for more details.
 *
 * @property-read \GeoIp2\Record\Continent $continent Continent data for the
 * requested IP address.
 * @property-read \GeoIp2\Record\Country $country Country data for the requested
 * IP address. This object represents the country where MaxMind believes the
 * end user is located.
 * @property-read \GeoIp2\Record\MaxMind $maxmind Data related to your MaxMind
 * account.
 * @property-read \GeoIp2\Record\Country $registeredCountry Registered country
 * data for the requested IP address. This record represents the country
 * where the ISP has registered a given IP block and may differ from the
 * user's country.
 * @property-read \GeoIp2\Record\RepresentedCountry $representedCountry
 * Represented country data for the requested IP address. The represented
 * country is used for things like military bases. It is only present when
 * the represented country differs from the country.
 * @property-read \GeoIp2\Record\Traits $traits Data for the traits of the
 * requested IP address.
 * @property-read array $raw The raw data from the web service.
 */
class Country extends AbstractModel
{
    /**
     * @var \GeoIp2\Record\Continent
     */
    protected $continent;
    /**
     * @var \GeoIp2\Record\Country
     */
    protected $country;
    /**
     * @var array<string>
     */
    protected $locales;
    /**
     * @var \GeoIp2\Record\MaxMind
     */
    protected $maxmind;
    /**
     * @var \GeoIp2\Record\Country
     */
    protected $registeredCountry;
    /**
     * @var \GeoIp2\Record\RepresentedCountry
     */
    protected $representedCountry;
    /**
     * @var \GeoIp2\Record\Traits
     */
    protected $traits;
    /**
     * @ignore
     */
    public function __construct(array $raw, array $locales = ['en'])
    {
        parent::__construct($raw);
        $this->continent = new \Matomo\Dependencies\GeoIp2\Record\Continent($this->get('continent'), $locales);
        $this->country = new \Matomo\Dependencies\GeoIp2\Record\Country($this->get('country'), $locales);
        $this->maxmind = new \Matomo\Dependencies\GeoIp2\Record\MaxMind($this->get('maxmind'));
        $this->registeredCountry = new \Matomo\Dependencies\GeoIp2\Record\Country($this->get('registered_country'), $locales);
        $this->representedCountry = new \Matomo\Dependencies\GeoIp2\Record\RepresentedCountry($this->get('represented_country'), $locales);
        $this->traits = new \Matomo\Dependencies\GeoIp2\Record\Traits($this->get('traits'));
        $this->locales = $locales;
    }
}

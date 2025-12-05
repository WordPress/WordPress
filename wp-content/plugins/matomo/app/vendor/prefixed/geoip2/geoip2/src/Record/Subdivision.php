<?php

declare (strict_types=1);
namespace Matomo\Dependencies\GeoIp2\Record;

/**
 * Contains data for the subdivisions associated with an IP address.
 *
 * This record is returned by all location databases and services besides
 * Country.
 *
 * @property-read int|null $confidence This is a value from 0-100 indicating
 * MaxMind's confidence that the subdivision is correct. This attribute is
 * only available from the Insights service and the GeoIP2 Enterprise
 * database.
 * @property-read int|null $geonameId This is a GeoName ID for the subdivision.
 * This attribute is returned by all location databases and services besides
 * Country.
 * @property-read string|null $isoCode This is a string up to three characters long
 * contain the subdivision portion of the ISO 3166-2 code. See
 * https://en.wikipedia.org/wiki/ISO_3166-2. This attribute is returned by all
 * location databases and services except Country.
 * @property-read string|null $name The name of the subdivision based on the
 * locales list passed to the constructor. This attribute is returned by all
 * location databases and services besides Country.
 * @property-read array|null $names An array map where the keys are locale codes
 * and the values are names. This attribute is returned by all location
 * databases and services besides Country.
 */
class Subdivision extends AbstractPlaceRecord
{
    /**
     * @ignore
     *
     * @var array<string>
     */
    protected $validAttributes = ['confidence', 'geonameId', 'isoCode', 'names'];
}

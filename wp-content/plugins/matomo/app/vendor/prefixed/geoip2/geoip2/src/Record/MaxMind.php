<?php

declare (strict_types=1);
namespace Matomo\Dependencies\GeoIp2\Record;

/**
 * Contains data about your account.
 *
 * This record is returned by all location services and databases.
 *
 * @property-read int|null $queriesRemaining The number of remaining queries you
 * have for the service you are calling.
 */
class MaxMind extends AbstractRecord
{
    /**
     * @ignore
     *
     * @var array<string>
     */
    protected $validAttributes = ['queriesRemaining'];
}

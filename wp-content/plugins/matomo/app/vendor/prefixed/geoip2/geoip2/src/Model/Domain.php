<?php

declare (strict_types=1);
namespace Matomo\Dependencies\GeoIp2\Model;

use Matomo\Dependencies\GeoIp2\Util;
/**
 * This class provides the GeoIP2 Domain model.
 *
 * @property-read string|null $domain The second level domain associated with the
 *     IP address. This will be something like "example.com" or
 *     "example.co.uk", not "foo.example.com".
 * @property-read string $ipAddress The IP address that the data in the model is
 *     for.
 * @property-read string $network The network in CIDR notation associated with
 *      the record. In particular, this is the largest network where all of the
 *      fields besides $ipAddress have the same value.
 */
class Domain extends AbstractModel
{
    /**
     * @var string|null
     */
    protected $domain;
    /**
     * @var string
     */
    protected $ipAddress;
    /**
     * @var string
     */
    protected $network;
    /**
     * @ignore
     */
    public function __construct(array $raw)
    {
        parent::__construct($raw);
        $this->domain = $this->get('domain');
        $ipAddress = $this->get('ip_address');
        $this->ipAddress = $ipAddress;
        $this->network = Util::cidr($ipAddress, $this->get('prefix_len'));
    }
}

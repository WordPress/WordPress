<?php

declare (strict_types=1);
namespace Matomo\Dependencies\GeoIp2\Model;

use Matomo\Dependencies\GeoIp2\Util;
/**
 * This class provides the GeoIP2 ISP model.
 *
 * @property-read int|null $autonomousSystemNumber The autonomous system number
 *     associated with the IP address.
 * @property-read string|null $autonomousSystemOrganization The organization
 *     associated with the registered autonomous system number for the IP
 *     address.
 * @property-read string|null $isp The name of the ISP associated with the IP
 *     address.
 * @property-read string|null $mobileCountryCode The [mobile country code
 *     (MCC)](https://en.wikipedia.org/wiki/Mobile_country_code) associated with
 *     the IP address and ISP.
 * @property-read string|null $mobileNetworkCode The [mobile network code
 *     (MNC)](https://en.wikipedia.org/wiki/Mobile_country_code) associated with
 *     the IP address and ISP.
 * @property-read string|null $organization The name of the organization associated
 *     with the IP address.
 * @property-read string $ipAddress The IP address that the data in the model is
 *     for.
 * @property-read string $network The network in CIDR notation associated with
 *      the record. In particular, this is the largest network where all of the
 *      fields besides $ipAddress have the same value.
 */
class Isp extends AbstractModel
{
    /**
     * @var int|null
     */
    protected $autonomousSystemNumber;
    /**
     * @var string|null
     */
    protected $autonomousSystemOrganization;
    /**
     * @var string|null
     */
    protected $isp;
    /**
     * @var string|null
     */
    protected $mobileCountryCode;
    /**
     * @var string|null
     */
    protected $mobileNetworkCode;
    /**
     * @var string|null
     */
    protected $organization;
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
        $this->autonomousSystemNumber = $this->get('autonomous_system_number');
        $this->autonomousSystemOrganization = $this->get('autonomous_system_organization');
        $this->isp = $this->get('isp');
        $this->mobileCountryCode = $this->get('mobile_country_code');
        $this->mobileNetworkCode = $this->get('mobile_network_code');
        $this->organization = $this->get('organization');
        $ipAddress = $this->get('ip_address');
        $this->ipAddress = $ipAddress;
        $this->network = Util::cidr($ipAddress, $this->get('prefix_len'));
    }
}

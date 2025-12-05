<?php

declare (strict_types=1);
namespace Matomo\Dependencies\GeoIp2\Model;

use Matomo\Dependencies\GeoIp2\Util;
/**
 * This class provides the GeoIP2 Anonymous IP model.
 *
 * @property-read bool $isAnonymous This is true if the IP address belongs to
 *     any sort of anonymous network.
 * @property-read bool $isAnonymousVpn This is true if the IP address is
 *     registered to an anonymous VPN provider. If a VPN provider does not
 *     register subnets under names associated with them, we will likely only
 *     flag their IP ranges using the isHostingProvider property.
 * @property-read bool $isHostingProvider This is true if the IP address belongs
 *     to a hosting or VPN provider (see description of isAnonymousVpn property).
 * @property-read bool $isPublicProxy This is true if the IP address belongs to
 *     a public proxy.
 * @property-read bool $isResidentialProxy This is true if the IP address is
 *     on a suspected anonymizing network and belongs to a residential ISP.
 * @property-read bool $isTorExitNode This is true if the IP address is a Tor
 *     exit node.
 * @property-read string $ipAddress The IP address that the data in the model is
 *     for.
 * @property-read string $network The network in CIDR notation associated with
 *      the record. In particular, this is the largest network where all of the
 *      fields besides $ipAddress have the same value.
 */
class AnonymousIp extends AbstractModel
{
    /**
     * @var bool
     */
    protected $isAnonymous;
    /**
     * @var bool
     */
    protected $isAnonymousVpn;
    /**
     * @var bool
     */
    protected $isHostingProvider;
    /**
     * @var bool
     */
    protected $isPublicProxy;
    /**
     * @var bool
     */
    protected $isResidentialProxy;
    /**
     * @var bool
     */
    protected $isTorExitNode;
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
        $this->isAnonymous = $this->get('is_anonymous');
        $this->isAnonymousVpn = $this->get('is_anonymous_vpn');
        $this->isHostingProvider = $this->get('is_hosting_provider');
        $this->isPublicProxy = $this->get('is_public_proxy');
        $this->isResidentialProxy = $this->get('is_residential_proxy');
        $this->isTorExitNode = $this->get('is_tor_exit_node');
        $ipAddress = $this->get('ip_address');
        $this->ipAddress = $ipAddress;
        $this->network = Util::cidr($ipAddress, $this->get('prefix_len'));
    }
}

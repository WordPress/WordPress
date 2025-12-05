<?php

declare (strict_types=1);
namespace Matomo\Dependencies\GeoIp2\Model;

use Matomo\Dependencies\GeoIp2\Util;
/**
 * This class provides the GeoIP2 Connection-Type model.
 *
 * @property-read string|null $connectionType The connection type may take the
 *     following values: "Dialup", "Cable/DSL", "Corporate", "Cellular".
 *     Additional values may be added in the future.
 * @property-read string $ipAddress The IP address that the data in the model is
 *     for.
 * @property-read string $network The network in CIDR notation associated with
 *      the record. In particular, this is the largest network where all of the
 *      fields besides $ipAddress have the same value.
 */
class ConnectionType extends AbstractModel
{
    /**
     * @var string|null
     */
    protected $connectionType;
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
        $this->connectionType = $this->get('connection_type');
        $ipAddress = $this->get('ip_address');
        $this->ipAddress = $ipAddress;
        $this->network = Util::cidr($ipAddress, $this->get('prefix_len'));
    }
}

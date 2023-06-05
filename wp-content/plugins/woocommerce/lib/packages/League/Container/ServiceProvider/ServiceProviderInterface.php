<?php declare(strict_types=1);

namespace Automattic\WooCommerce\Vendor\League\Container\ServiceProvider;

use Automattic\WooCommerce\Vendor\League\Container\ContainerAwareInterface;

interface ServiceProviderInterface extends ContainerAwareInterface
{
    /**
     * Returns a boolean if checking whether this provider provides a specific
     * service or returns an array of provided services if no argument passed.
     *
     * @param string $service
     *
     * @return boolean
     */
    public function provides(string $service) : bool;

    /**
     * Use the register method to register items with the container via the
     * protected $this->leagueContainer property or the `getLeagueContainer` method
     * from the ContainerAwareTrait.
     *
     * @return void
     */
    public function register();

    /**
     * Set a custom id for the service provider. This enables
     * registering the same service provider multiple times.
     *
     * @param string $id
     *
     * @return self
     */
    public function setIdentifier(string $id) : ServiceProviderInterface;

    /**
     * The id of the service provider uniquely identifies it, so
     * that we can quickly determine if it has already been registered.
     * Defaults to get_class($provider).
     *
     * @return string
     */
    public function getIdentifier() : string;
}

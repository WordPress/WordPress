<?php declare(strict_types=1);

namespace Automattic\WooCommerce\Vendor\League\Container\ServiceProvider;

use Generator;
use Automattic\WooCommerce\Vendor\League\Container\{ContainerAwareInterface, ContainerAwareTrait};
use Automattic\WooCommerce\Vendor\League\Container\Exception\ContainerException;

class ServiceProviderAggregate implements ServiceProviderAggregateInterface
{
    use ContainerAwareTrait;

    /**
     * @var ServiceProviderInterface[]
     */
    protected $providers = [];

    /**
     * @var array
     */
    protected $registered = [];

    /**
     * {@inheritdoc}
     */
    public function add($provider) : ServiceProviderAggregateInterface
    {
        if (is_string($provider) && $this->getContainer()->has($provider)) {
            $provider = $this->getContainer()->get($provider);
        } elseif (is_string($provider) && class_exists($provider)) {
            $provider = new $provider;
        }

        if (in_array($provider, $this->providers, true)) {
            return $this;
        }

        if ($provider instanceof ContainerAwareInterface) {
            $provider->setLeagueContainer($this->getLeagueContainer());
        }

        if ($provider instanceof BootableServiceProviderInterface) {
            $provider->boot();
        }

        if ($provider instanceof ServiceProviderInterface) {
            $this->providers[] = $provider;

            return $this;
        }

        throw new ContainerException(
            'A service provider must be a fully qualified class name or instance ' .
            'of (\Automattic\WooCommerce\Vendor\League\Container\ServiceProvider\ServiceProviderInterface)'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function provides(string $service) : bool
    {
        foreach ($this->getIterator() as $provider) {
            if ($provider->provides($service)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator() : Generator
    {
        $count = count($this->providers);

        for ($i = 0; $i < $count; $i++) {
            yield $this->providers[$i];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function register(string $service)
    {
        if (false === $this->provides($service)) {
            throw new ContainerException(
                sprintf('(%s) is not provided by a service provider', $service)
            );
        }

        foreach ($this->getIterator() as $provider) {
            if (in_array($provider->getIdentifier(), $this->registered, true)) {
                continue;
            }

            if ($provider->provides($service)) {
                $this->registered[] = $provider->getIdentifier();
                $provider->register();
            }
        }
    }
}

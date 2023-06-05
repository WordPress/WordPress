<?php declare(strict_types=1);

namespace Automattic\WooCommerce\Vendor\League\Container;

use Automattic\WooCommerce\Vendor\League\Container\Definition\{DefinitionAggregate, DefinitionInterface, DefinitionAggregateInterface};
use Automattic\WooCommerce\Vendor\League\Container\Exception\{NotFoundException, ContainerException};
use Automattic\WooCommerce\Vendor\League\Container\Inflector\{InflectorAggregate, InflectorInterface, InflectorAggregateInterface};
use Automattic\WooCommerce\Vendor\League\Container\ServiceProvider\{
    ServiceProviderAggregate,
    ServiceProviderAggregateInterface,
    ServiceProviderInterface
};
use Automattic\WooCommerce\Vendor\Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    /**
     * @var boolean
     */
    protected $defaultToShared = false;

    /**
     * @var DefinitionAggregateInterface
     */
    protected $definitions;

    /**
     * @var ServiceProviderAggregateInterface
     */
    protected $providers;

    /**
     * @var InflectorAggregateInterface
     */
    protected $inflectors;

    /**
     * @var ContainerInterface[]
     */
    protected $delegates = [];

    /**
     * Construct.
     *
     * @param DefinitionAggregateInterface|null      $definitions
     * @param ServiceProviderAggregateInterface|null $providers
     * @param InflectorAggregateInterface|null       $inflectors
     */
    public function __construct(
        DefinitionAggregateInterface      $definitions = null,
        ServiceProviderAggregateInterface $providers = null,
        InflectorAggregateInterface       $inflectors = null
    ) {
        $this->definitions = $definitions ?? new DefinitionAggregate;
        $this->providers   = $providers   ?? new ServiceProviderAggregate;
        $this->inflectors  = $inflectors  ?? new InflectorAggregate;

        if ($this->definitions instanceof ContainerAwareInterface) {
            $this->definitions->setLeagueContainer($this);
        }

        if ($this->providers instanceof ContainerAwareInterface) {
            $this->providers->setLeagueContainer($this);
        }

        if ($this->inflectors instanceof ContainerAwareInterface) {
            $this->inflectors->setLeagueContainer($this);
        }
    }

    /**
     * Add an item to the container.
     *
     * @param string  $id
     * @param mixed   $concrete
     * @param boolean $shared
     *
     * @return DefinitionInterface
     */
    public function add(string $id, $concrete = null, bool $shared = null) : DefinitionInterface
    {
        $concrete = $concrete ?? $id;
        $shared = $shared ?? $this->defaultToShared;

        return $this->definitions->add($id, $concrete, $shared);
    }

    /**
     * Proxy to add with shared as true.
     *
     * @param string $id
     * @param mixed  $concrete
     *
     * @return DefinitionInterface
     */
    public function share(string $id, $concrete = null) : DefinitionInterface
    {
        return $this->add($id, $concrete, true);
    }

    /**
     * Whether the container should default to defining shared definitions.
     *
     * @param boolean $shared
     *
     * @return self
     */
    public function defaultToShared(bool $shared = true) : ContainerInterface
    {
        $this->defaultToShared = $shared;

        return $this;
    }

    /**
     * Get a definition to extend.
     *
     * @param string $id [description]
     *
     * @return DefinitionInterface
     */
    public function extend(string $id) : DefinitionInterface
    {
        if ($this->providers->provides($id)) {
            $this->providers->register($id);
        }

        if ($this->definitions->has($id)) {
            return $this->definitions->getDefinition($id);
        }

        throw new NotFoundException(
            sprintf('Unable to extend alias (%s) as it is not being managed as a definition', $id)
        );
    }

    /**
     * Add a service provider.
     *
     * @param ServiceProviderInterface|string $provider
     *
     * @return self
     */
    public function addServiceProvider($provider) : self
    {
        $this->providers->add($provider);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id, bool $new = false)
    {
        if ($this->definitions->has($id)) {
            $resolved = $this->definitions->resolve($id, $new);
            return $this->inflectors->inflect($resolved);
        }

        if ($this->definitions->hasTag($id)) {
            $arrayOf = $this->definitions->resolveTagged($id, $new);

            array_walk($arrayOf, function (&$resolved) {
                $resolved = $this->inflectors->inflect($resolved);
            });

            return $arrayOf;
        }

        if ($this->providers->provides($id)) {
            $this->providers->register($id);

            if (!$this->definitions->has($id) && !$this->definitions->hasTag($id)) {
                throw new ContainerException(sprintf('Service provider lied about providing (%s) service', $id));
            }

            return $this->get($id, $new);
        }

        foreach ($this->delegates as $delegate) {
            if ($delegate->has($id)) {
                $resolved = $delegate->get($id);
                return $this->inflectors->inflect($resolved);
            }
        }

        throw new NotFoundException(sprintf('Alias (%s) is not being managed by the container or delegates', $id));
    }

    /**
     * {@inheritdoc}
     */
    public function has($id) : bool
    {
        if ($this->definitions->has($id)) {
            return true;
        }

        if ($this->definitions->hasTag($id)) {
            return true;
        }

        if ($this->providers->provides($id)) {
            return true;
        }

        foreach ($this->delegates as $delegate) {
            if ($delegate->has($id)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Allows for manipulation of specific types on resolution.
     *
     * @param string        $type
     * @param callable|null $callback
     *
     * @return InflectorInterface
     */
    public function inflector(string $type, callable $callback = null) : InflectorInterface
    {
        return $this->inflectors->add($type, $callback);
    }

    /**
     * Delegate a backup container to be checked for services if it
     * cannot be resolved via this container.
     *
     * @param ContainerInterface $container
     *
     * @return self
     */
    public function delegate(ContainerInterface $container) : self
    {
        $this->delegates[] = $container;

        if ($container instanceof ContainerAwareInterface) {
            $container->setLeagueContainer($this);
        }

        return $this;
    }
}

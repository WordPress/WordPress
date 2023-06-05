<?php declare(strict_types=1);

namespace Automattic\WooCommerce\Vendor\League\Container;

use Automattic\WooCommerce\Vendor\League\Container\Argument\{ArgumentResolverInterface, ArgumentResolverTrait};
use Automattic\WooCommerce\Vendor\League\Container\Exception\NotFoundException;
use Automattic\WooCommerce\Vendor\Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionFunction;
use ReflectionMethod;

class ReflectionContainer implements ArgumentResolverInterface, ContainerInterface
{
    use ArgumentResolverTrait;
    use ContainerAwareTrait;

    /**
     * @var boolean
     */
    protected $cacheResolutions = false;

    /**
     * Cache of resolutions.
     *
     * @var array
     */
    protected $cache = [];

    /**
     * {@inheritdoc}
     *
     * @throws ReflectionException
     */
    public function get($id, array $args = [])
    {
        if ($this->cacheResolutions === true && array_key_exists($id, $this->cache)) {
            return $this->cache[$id];
        }

        if (! $this->has($id)) {
            throw new NotFoundException(
                sprintf('Alias (%s) is not an existing class and therefore cannot be resolved', $id)
            );
        }

        $reflector = new ReflectionClass($id);
        $construct = $reflector->getConstructor();

        $resolution = $construct === null
            ? new $id
            : $resolution = $reflector->newInstanceArgs($this->reflectArguments($construct, $args))
        ;

        if ($this->cacheResolutions === true) {
            $this->cache[$id] = $resolution;
        }

        return $resolution;
    }

    /**
     * {@inheritdoc}
     */
    public function has($id) : bool
    {
        return class_exists($id);
    }

    /**
     * Invoke a callable via the container.
     *
     * @param callable $callable
     * @param array    $args
     *
     * @return mixed
     *
     * @throws ReflectionException
     */
    public function call(callable $callable, array $args = [])
    {
        if (is_string($callable) && strpos($callable, '::') !== false) {
            $callable = explode('::', $callable);
        }

        if (is_array($callable)) {
            if (is_string($callable[0])) {
                $callable[0] = $this->getContainer()->get($callable[0]);
            }

            $reflection = new ReflectionMethod($callable[0], $callable[1]);

            if ($reflection->isStatic()) {
                $callable[0] = null;
            }

            return $reflection->invokeArgs($callable[0], $this->reflectArguments($reflection, $args));
        }

        if (is_object($callable)) {
            $reflection = new ReflectionMethod($callable, '__invoke');

            return $reflection->invokeArgs($callable, $this->reflectArguments($reflection, $args));
        }

        $reflection = new ReflectionFunction(\Closure::fromCallable($callable));

        return $reflection->invokeArgs($this->reflectArguments($reflection, $args));
    }

    /**
     * Whether the container should default to caching resolutions and returning
     * the cache on following calls.
     *
     * @param boolean $option
     *
     * @return self
     */
    public function cacheResolutions(bool $option = true) : ContainerInterface
    {
        $this->cacheResolutions = $option;

        return $this;
    }
}

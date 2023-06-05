<?php declare(strict_types=1);

namespace Automattic\WooCommerce\Vendor\League\Container\Argument;

use Automattic\WooCommerce\Vendor\League\Container\Container;
use Automattic\WooCommerce\Vendor\League\Container\Exception\{ContainerException, NotFoundException};
use Automattic\WooCommerce\Vendor\League\Container\ReflectionContainer;
use Automattic\WooCommerce\Vendor\Psr\Container\ContainerInterface;
use ReflectionFunctionAbstract;
use ReflectionParameter;

trait ArgumentResolverTrait
{
    /**
     * {@inheritdoc}
     */
    public function resolveArguments(array $arguments) : array
    {
        return array_map(function ($argument) {
            $justStringValue = false;

            if ($argument instanceof RawArgumentInterface) {
                return $argument->getValue();
            } elseif ($argument instanceof ClassNameInterface) {
                $id = $argument->getClassName();
            } elseif (!is_string($argument)) {
                return $argument;
            } else {
                $justStringValue = true;
                $id = $argument;
            }

            $container = null;

            try {
                $container = $this->getLeagueContainer();
            } catch (ContainerException $e) {
                if ($this instanceof ReflectionContainer) {
                    $container = $this;
                }
            }

            if ($container !== null) {
                try {
                    return $container->get($id);
                } catch (NotFoundException $exception) {
                    if ($argument instanceof ClassNameWithOptionalValue) {
                        return $argument->getOptionalValue();
                    }

                    if ($justStringValue) {
                        return $id;
                    }

                    throw $exception;
                }
            }

            if ($argument instanceof ClassNameWithOptionalValue) {
                return $argument->getOptionalValue();
            }

            // Just a string value.
            return $id;
        }, $arguments);
    }

    /**
     * {@inheritdoc}
     */
    public function reflectArguments(ReflectionFunctionAbstract $method, array $args = []) : array
    {
        $arguments = array_map(function (ReflectionParameter $param) use ($method, $args) {
            $name = $param->getName();
            $type = $param->getType();

            if (array_key_exists($name, $args)) {
                return new RawArgument($args[$name]);
            }

            if ($type) {
                if (PHP_VERSION_ID >= 70100) {
                    $typeName = $type->getName();
                } else {
                    $typeName = (string) $type;
                }

                $typeName = ltrim($typeName, '?');

                if ($param->isDefaultValueAvailable()) {
                    return new ClassNameWithOptionalValue($typeName, $param->getDefaultValue());
                }

                return new ClassName($typeName);
            }

            if ($param->isDefaultValueAvailable()) {
                return new RawArgument($param->getDefaultValue());
            }

            throw new NotFoundException(sprintf(
                'Unable to resolve a value for parameter (%s) in the function/method (%s)',
                $name,
                $method->getName()
            ));
        }, $method->getParameters());

        return $this->resolveArguments($arguments);
    }

    /**
     * @return ContainerInterface
     */
    abstract public function getContainer() : ContainerInterface;

    /**
     * @return Container
     */
    abstract public function getLeagueContainer() : Container;
}

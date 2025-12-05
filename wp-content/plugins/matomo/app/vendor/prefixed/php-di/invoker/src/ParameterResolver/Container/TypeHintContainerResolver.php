<?php

namespace Matomo\Dependencies\Invoker\ParameterResolver\Container;

use Matomo\Dependencies\Invoker\ParameterResolver\ParameterResolver;
use Matomo\Dependencies\Psr\Container\ContainerInterface;
use ReflectionFunctionAbstract;
/**
 * Inject entries from a DI container using the type-hints.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class TypeHintContainerResolver implements ParameterResolver
{
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @param ContainerInterface $container The container to get entries from.
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    public function getParameters(ReflectionFunctionAbstract $reflection, array $providedParameters, array $resolvedParameters)
    {
        $parameters = $reflection->getParameters();
        // Skip parameters already resolved
        if (!empty($resolvedParameters)) {
            $parameters = array_diff_key($parameters, $resolvedParameters);
        }
        foreach ($parameters as $index => $parameter) {
            $parameterClass = $parameter->getClass();
            if ($parameterClass && $this->container->has($parameterClass->name)) {
                $resolvedParameters[$index] = $this->container->get($parameterClass->name);
            }
        }
        return $resolvedParameters;
    }
}

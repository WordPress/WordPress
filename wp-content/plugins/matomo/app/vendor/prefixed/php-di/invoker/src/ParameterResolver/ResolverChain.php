<?php

namespace Matomo\Dependencies\Invoker\ParameterResolver;

use ReflectionFunctionAbstract;
/**
 * Dispatches the call to other resolvers until all parameters are resolved.
 *
 * Chain of responsibility pattern.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class ResolverChain implements ParameterResolver
{
    /**
     * @var ParameterResolver[]
     */
    private $resolvers = array();
    public function __construct(array $resolvers = array())
    {
        $this->resolvers = $resolvers;
    }
    public function getParameters(ReflectionFunctionAbstract $reflection, array $providedParameters, array $resolvedParameters)
    {
        $reflectionParameters = $reflection->getParameters();
        foreach ($this->resolvers as $resolver) {
            $resolvedParameters = $resolver->getParameters($reflection, $providedParameters, $resolvedParameters);
            $diff = array_diff_key($reflectionParameters, $resolvedParameters);
            if (empty($diff)) {
                // Stop traversing: all parameters are resolved
                return $resolvedParameters;
            }
        }
        return $resolvedParameters;
    }
    /**
     * Push a parameter resolver after the ones already registered.
     *
     * @param ParameterResolver $resolver
     */
    public function appendResolver(ParameterResolver $resolver)
    {
        $this->resolvers[] = $resolver;
    }
    /**
     * Insert a parameter resolver before the ones already registered.
     *
     * @param ParameterResolver $resolver
     */
    public function prependResolver(ParameterResolver $resolver)
    {
        array_unshift($this->resolvers, $resolver);
    }
}

<?php

namespace Matomo\Dependencies\Invoker\ParameterResolver;

use ReflectionFunctionAbstract;
/**
 * Resolves the parameters to use to call the callable.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
interface ParameterResolver
{
    /**
     * Resolves the parameters to use to call the callable.
     *
     * `$resolvedParameters` contains parameters that have already been resolved.
     *
     * Each ParameterResolver must resolve parameters that are not already
     * in `$resolvedParameters`. That allows to chain multiple ParameterResolver.
     *
     * @param ReflectionFunctionAbstract $reflection Reflection object for the callable.
     * @param array $providedParameters Parameters provided by the caller.
     * @param array $resolvedParameters Parameters resolved (indexed by parameter position).
     *
     * @return array
     */
    public function getParameters(ReflectionFunctionAbstract $reflection, array $providedParameters, array $resolvedParameters);
}

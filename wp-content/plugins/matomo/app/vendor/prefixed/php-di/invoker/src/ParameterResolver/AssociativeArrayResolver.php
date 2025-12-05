<?php

namespace Matomo\Dependencies\Invoker\ParameterResolver;

use ReflectionFunctionAbstract;
/**
 * Tries to map an associative array (string-indexed) to the parameter names.
 *
 * E.g. `->call($callable, ['foo' => 'bar'])` will inject the string `'bar'`
 * in the parameter named `$foo`.
 *
 * Parameters that are not indexed by a string are ignored.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class AssociativeArrayResolver implements ParameterResolver
{
    public function getParameters(ReflectionFunctionAbstract $reflection, array $providedParameters, array $resolvedParameters)
    {
        $parameters = $reflection->getParameters();
        // Skip parameters already resolved
        if (!empty($resolvedParameters)) {
            $parameters = array_diff_key($parameters, $resolvedParameters);
        }
        foreach ($parameters as $index => $parameter) {
            if (array_key_exists($parameter->name, $providedParameters)) {
                $resolvedParameters[$index] = $providedParameters[$parameter->name];
            }
        }
        return $resolvedParameters;
    }
}

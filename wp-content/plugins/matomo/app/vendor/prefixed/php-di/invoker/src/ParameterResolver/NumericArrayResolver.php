<?php

namespace Matomo\Dependencies\Invoker\ParameterResolver;

use ReflectionFunctionAbstract;
/**
 * Simply returns all the values of the $providedParameters array that are
 * indexed by the parameter position (i.e. a number).
 *
 * E.g. `->call($callable, ['foo', 'bar'])` will simply resolve the parameters
 * to `['foo', 'bar']`.
 *
 * Parameters that are not indexed by a number (i.e. parameter position)
 * will be ignored.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class NumericArrayResolver implements ParameterResolver
{
    public function getParameters(ReflectionFunctionAbstract $reflection, array $providedParameters, array $resolvedParameters)
    {
        // Skip parameters already resolved
        if (!empty($resolvedParameters)) {
            $providedParameters = array_diff_key($providedParameters, $resolvedParameters);
        }
        foreach ($providedParameters as $key => $value) {
            if (is_int($key)) {
                $resolvedParameters[$key] = $value;
            }
        }
        return $resolvedParameters;
    }
}

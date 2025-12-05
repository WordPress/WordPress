<?php

namespace Matomo\Dependencies\Invoker\ParameterResolver;

use ReflectionException;
use ReflectionFunctionAbstract;
/**
 * Finds the default value for a parameter, *if it exists*.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class DefaultValueResolver implements ParameterResolver
{
    public function getParameters(ReflectionFunctionAbstract $reflection, array $providedParameters, array $resolvedParameters)
    {
        $parameters = $reflection->getParameters();
        // Skip parameters already resolved
        if (!empty($resolvedParameters)) {
            $parameters = array_diff_key($parameters, $resolvedParameters);
        }
        foreach ($parameters as $index => $parameter) {
            /** @var \ReflectionParameter $parameter */
            if ($parameter->isOptional()) {
                try {
                    $resolvedParameters[$index] = $parameter->getDefaultValue();
                } catch (ReflectionException $e) {
                    // Can't get default values from PHP internal classes and functions
                }
            }
        }
        return $resolvedParameters;
    }
}

<?php

namespace Matomo\Dependencies\Invoker\ParameterResolver;

use Matomo\Dependencies\Invoker\ParameterResolver\ParameterResolver;
use ReflectionFunctionAbstract;
/**
 * Inject entries using type-hints.
 *
 * Tries to match type-hints with the parameters provided.
 *
 * @author Felix Becker <f.becker@outlook.com>
 */
class TypeHintResolver implements ParameterResolver
{
    public function getParameters(ReflectionFunctionAbstract $reflection, array $providedParameters, array $resolvedParameters)
    {
        $parameters = $reflection->getParameters();
        // Skip parameters already resolved
        if (!empty($resolvedParameters)) {
            $parameters = array_diff_key($parameters, $resolvedParameters);
        }
        foreach ($parameters as $index => $parameter) {
            $parameterClass = $parameter->getClass();
            if ($parameterClass && array_key_exists($parameterClass->name, $providedParameters)) {
                $resolvedParameters[$index] = $providedParameters[$parameterClass->name];
            }
        }
        return $resolvedParameters;
    }
}

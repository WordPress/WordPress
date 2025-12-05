<?php

declare (strict_types=1);
namespace Matomo\Dependencies\DI\Definition\Resolver;

use Matomo\Dependencies\DI\Definition\Definition;
use Matomo\Dependencies\DI\Definition\Exception\InvalidDefinition;
use Matomo\Dependencies\DI\Definition\ObjectDefinition\MethodInjection;
use ReflectionMethod;
use ReflectionParameter;
/**
 * Resolves parameters for a function call.
 *
 * @since  4.2
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class ParameterResolver
{
    /**
     * @var DefinitionResolver
     */
    private $definitionResolver;
    /**
     * @param DefinitionResolver $definitionResolver Will be used to resolve nested definitions.
     */
    public function __construct(DefinitionResolver $definitionResolver)
    {
        $this->definitionResolver = $definitionResolver;
    }
    /**
     * @throws InvalidDefinition A parameter has no value defined or guessable.
     * @return array Parameters to use to call the function.
     */
    public function resolveParameters(MethodInjection $definition = null, ReflectionMethod $method = null, array $parameters = [])
    {
        $args = [];
        if (!$method) {
            return $args;
        }
        $definitionParameters = $definition ? $definition->getParameters() : [];
        foreach ($method->getParameters() as $index => $parameter) {
            if (array_key_exists($parameter->getName(), $parameters)) {
                // Look in the $parameters array
                $value =& $parameters[$parameter->getName()];
            } elseif (array_key_exists($index, $definitionParameters)) {
                // Look in the definition
                $value =& $definitionParameters[$index];
            } else {
                // If the parameter is optional and wasn't specified, we take its default value
                if ($parameter->isDefaultValueAvailable() || $parameter->isOptional()) {
                    $args[] = $this->getParameterDefaultValue($parameter, $method);
                    continue;
                }
                throw new InvalidDefinition(sprintf('Parameter $%s of %s has no value defined or guessable', $parameter->getName(), $this->getFunctionName($method)));
            }
            // Nested definitions
            if ($value instanceof Definition) {
                // If the container cannot produce the entry, we can use the default parameter value
                if ($parameter->isOptional() && !$this->definitionResolver->isResolvable($value)) {
                    $value = $this->getParameterDefaultValue($parameter, $method);
                } else {
                    $value = $this->definitionResolver->resolve($value);
                }
            }
            $args[] =& $value;
        }
        return $args;
    }
    /**
     * Returns the default value of a function parameter.
     *
     * @throws InvalidDefinition Can't get default values from PHP internal classes and functions
     * @return mixed
     */
    private function getParameterDefaultValue(ReflectionParameter $parameter, ReflectionMethod $function)
    {
        try {
            return $parameter->getDefaultValue();
        } catch (\ReflectionException $e) {
            throw new InvalidDefinition(sprintf('The parameter "%s" of %s has no type defined or guessable. It has a default value, ' . 'but the default value can\'t be read through Reflection because it is a PHP internal class.', $parameter->getName(), $this->getFunctionName($function)));
        }
    }
    private function getFunctionName(ReflectionMethod $method) : string
    {
        return $method->getName() . '()';
    }
}

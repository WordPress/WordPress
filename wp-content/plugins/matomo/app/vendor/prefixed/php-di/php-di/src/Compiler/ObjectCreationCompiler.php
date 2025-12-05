<?php

declare (strict_types=1);
namespace Matomo\Dependencies\DI\Compiler;

use Matomo\Dependencies\DI\Definition\Exception\InvalidDefinition;
use Matomo\Dependencies\DI\Definition\ObjectDefinition;
use Matomo\Dependencies\DI\Definition\ObjectDefinition\MethodInjection;
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionProperty;
/**
 * Compiles an object definition into native PHP code that, when executed, creates the object.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class ObjectCreationCompiler
{
    /**
     * @var Compiler
     */
    private $compiler;
    public function __construct(Compiler $compiler)
    {
        $this->compiler = $compiler;
    }
    public function compile(ObjectDefinition $definition) : string
    {
        $this->assertClassIsNotAnonymous($definition);
        $this->assertClassIsInstantiable($definition);
        // Lazy?
        if ($definition->isLazy()) {
            return $this->compileLazyDefinition($definition);
        }
        try {
            $classReflection = new ReflectionClass($definition->getClassName());
            $constructorArguments = $this->resolveParameters($definition->getConstructorInjection(), $classReflection->getConstructor());
            $dumpedConstructorArguments = array_map(function ($value) {
                return $this->compiler->compileValue($value);
            }, $constructorArguments);
            $code = [];
            $code[] = sprintf('$object = new %s(%s);', $definition->getClassName(), implode(', ', $dumpedConstructorArguments));
            // Property injections
            foreach ($definition->getPropertyInjections() as $propertyInjection) {
                $value = $propertyInjection->getValue();
                $value = $this->compiler->compileValue($value);
                $className = $propertyInjection->getClassName() ?: $definition->getClassName();
                $property = new ReflectionProperty($className, $propertyInjection->getPropertyName());
                if ($property->isPublic()) {
                    $code[] = sprintf('$object->%s = %s;', $propertyInjection->getPropertyName(), $value);
                } else {
                    // Private/protected property
                    $code[] = sprintf('\\DI\\Definition\\Resolver\\ObjectCreator::setPrivatePropertyValue(%s, $object, \'%s\', %s);', var_export($propertyInjection->getClassName(), \true), $propertyInjection->getPropertyName(), $value);
                }
            }
            // Method injections
            foreach ($definition->getMethodInjections() as $methodInjection) {
                $methodReflection = new \ReflectionMethod($definition->getClassName(), $methodInjection->getMethodName());
                $parameters = $this->resolveParameters($methodInjection, $methodReflection);
                $dumpedParameters = array_map(function ($value) {
                    return $this->compiler->compileValue($value);
                }, $parameters);
                $code[] = sprintf('$object->%s(%s);', $methodInjection->getMethodName(), implode(', ', $dumpedParameters));
            }
        } catch (InvalidDefinition $e) {
            throw InvalidDefinition::create($definition, sprintf('Entry "%s" cannot be compiled: %s', $definition->getName(), $e->getMessage()));
        }
        return implode("\n        ", $code);
    }
    public function resolveParameters(MethodInjection $definition = null, ReflectionMethod $method = null) : array
    {
        $args = [];
        if (!$method) {
            return $args;
        }
        $definitionParameters = $definition ? $definition->getParameters() : [];
        foreach ($method->getParameters() as $index => $parameter) {
            if (array_key_exists($index, $definitionParameters)) {
                // Look in the definition
                $value =& $definitionParameters[$index];
            } elseif ($parameter->isOptional()) {
                // If the parameter is optional and wasn't specified, we take its default value
                $args[] = $this->getParameterDefaultValue($parameter, $method);
                continue;
            } else {
                throw new InvalidDefinition(sprintf('Parameter $%s of %s has no value defined or guessable', $parameter->getName(), $this->getFunctionName($method)));
            }
            $args[] =& $value;
        }
        return $args;
    }
    private function compileLazyDefinition(ObjectDefinition $definition) : string
    {
        $subDefinition = clone $definition;
        $subDefinition->setLazy(\false);
        $subDefinition = $this->compiler->compileValue($subDefinition);
        $this->compiler->getProxyFactory()->generateProxyClass($definition->getClassName());
        return <<<PHP
        \$object = \$this->proxyFactory->createProxy(
            '{$definition->getClassName()}',
            function (&\$wrappedObject, \$proxy, \$method, \$params, &\$initializer) {
                \$wrappedObject = {$subDefinition};
                \$initializer = null; // turning off further lazy initialization
                return true;
            }
        );
PHP;
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
    private function assertClassIsNotAnonymous(ObjectDefinition $definition)
    {
        if (strpos($definition->getClassName(), '@') !== \false) {
            throw InvalidDefinition::create($definition, sprintf('Entry "%s" cannot be compiled: anonymous classes cannot be compiled', $definition->getName()));
        }
    }
    private function assertClassIsInstantiable(ObjectDefinition $definition)
    {
        if ($definition->isInstantiable()) {
            return;
        }
        $message = !$definition->classExists() ? 'Entry "%s" cannot be compiled: the class doesn\'t exist' : 'Entry "%s" cannot be compiled: the class is not instantiable';
        throw InvalidDefinition::create($definition, sprintf($message, $definition->getName()));
    }
}

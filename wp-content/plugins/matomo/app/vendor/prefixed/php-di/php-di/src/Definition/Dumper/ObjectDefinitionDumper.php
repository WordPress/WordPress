<?php

declare (strict_types=1);
namespace Matomo\Dependencies\DI\Definition\Dumper;

use Matomo\Dependencies\DI\Definition\Definition;
use Matomo\Dependencies\DI\Definition\ObjectDefinition;
use Matomo\Dependencies\DI\Definition\ObjectDefinition\MethodInjection;
use ReflectionException;
/**
 * Dumps object definitions to string for debugging purposes.
 *
 * @since 4.1
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class ObjectDefinitionDumper
{
    /**
     * Returns the definition as string representation.
     */
    public function dump(ObjectDefinition $definition) : string
    {
        $className = $definition->getClassName();
        $classExist = class_exists($className) || interface_exists($className);
        // Class
        if (!$classExist) {
            $warning = '#UNKNOWN# ';
        } else {
            $class = new \ReflectionClass($className);
            $warning = $class->isInstantiable() ? '' : '#NOT INSTANTIABLE# ';
        }
        $str = sprintf('    class = %s%s', $warning, $className);
        // Lazy
        $str .= \PHP_EOL . '    lazy = ' . var_export($definition->isLazy(), \true);
        if ($classExist) {
            // Constructor
            $str .= $this->dumpConstructor($className, $definition);
            // Properties
            $str .= $this->dumpProperties($definition);
            // Methods
            $str .= $this->dumpMethods($className, $definition);
        }
        return sprintf('Object (' . \PHP_EOL . '%s' . \PHP_EOL . ')', $str);
    }
    private function dumpConstructor(string $className, ObjectDefinition $definition) : string
    {
        $str = '';
        $constructorInjection = $definition->getConstructorInjection();
        if ($constructorInjection !== null) {
            $parameters = $this->dumpMethodParameters($className, $constructorInjection);
            $str .= sprintf(\PHP_EOL . '    __construct(' . \PHP_EOL . '        %s' . \PHP_EOL . '    )', $parameters);
        }
        return $str;
    }
    private function dumpProperties(ObjectDefinition $definition) : string
    {
        $str = '';
        foreach ($definition->getPropertyInjections() as $propertyInjection) {
            $value = $propertyInjection->getValue();
            $valueStr = $value instanceof Definition ? (string) $value : var_export($value, \true);
            $str .= sprintf(\PHP_EOL . '    $%s = %s', $propertyInjection->getPropertyName(), $valueStr);
        }
        return $str;
    }
    private function dumpMethods(string $className, ObjectDefinition $definition) : string
    {
        $str = '';
        foreach ($definition->getMethodInjections() as $methodInjection) {
            $parameters = $this->dumpMethodParameters($className, $methodInjection);
            $str .= sprintf(\PHP_EOL . '    %s(' . \PHP_EOL . '        %s' . \PHP_EOL . '    )', $methodInjection->getMethodName(), $parameters);
        }
        return $str;
    }
    private function dumpMethodParameters(string $className, MethodInjection $methodInjection) : string
    {
        $methodReflection = new \ReflectionMethod($className, $methodInjection->getMethodName());
        $args = [];
        $definitionParameters = $methodInjection->getParameters();
        foreach ($methodReflection->getParameters() as $index => $parameter) {
            if (array_key_exists($index, $definitionParameters)) {
                $value = $definitionParameters[$index];
                $valueStr = $value instanceof Definition ? (string) $value : var_export($value, \true);
                $args[] = sprintf('$%s = %s', $parameter->getName(), $valueStr);
                continue;
            }
            // If the parameter is optional and wasn't specified, we take its default value
            if ($parameter->isOptional()) {
                try {
                    $value = $parameter->getDefaultValue();
                    $args[] = sprintf('$%s = (default value) %s', $parameter->getName(), var_export($value, \true));
                    continue;
                } catch (ReflectionException $e) {
                    // The default value can't be read through Reflection because it is a PHP internal class
                }
            }
            $args[] = sprintf('$%s = #UNDEFINED#', $parameter->getName());
        }
        return implode(\PHP_EOL . '        ', $args);
    }
}

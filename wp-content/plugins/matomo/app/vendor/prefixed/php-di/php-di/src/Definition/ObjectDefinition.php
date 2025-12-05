<?php

declare (strict_types=1);
namespace Matomo\Dependencies\DI\Definition;

use Matomo\Dependencies\DI\Definition\Dumper\ObjectDefinitionDumper;
use Matomo\Dependencies\DI\Definition\ObjectDefinition\MethodInjection;
use Matomo\Dependencies\DI\Definition\ObjectDefinition\PropertyInjection;
use Matomo\Dependencies\DI\Definition\Source\DefinitionArray;
use ReflectionClass;
/**
 * Defines how an object can be instantiated.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class ObjectDefinition implements Definition
{
    /**
     * Entry name (most of the time, same as $classname).
     * @var string
     */
    private $name;
    /**
     * Class name (if null, then the class name is $name).
     * @var string|null
     */
    protected $className;
    /**
     * Constructor parameter injection.
     * @var MethodInjection|null
     */
    protected $constructorInjection;
    /**
     * Property injections.
     * @var PropertyInjection[]
     */
    protected $propertyInjections = [];
    /**
     * Method calls.
     * @var MethodInjection[][]
     */
    protected $methodInjections = [];
    /**
     * @var bool|null
     */
    protected $lazy;
    /**
     * Store if the class exists. Storing it (in cache) avoids recomputing this.
     *
     * @var bool
     */
    private $classExists;
    /**
     * Store if the class is instantiable. Storing it (in cache) avoids recomputing this.
     *
     * @var bool
     */
    private $isInstantiable;
    /**
     * @param string $name Entry name
     */
    public function __construct(string $name, string $className = null)
    {
        $this->name = $name;
        $this->setClassName($className);
    }
    public function getName() : string
    {
        return $this->name;
    }
    public function setName(string $name)
    {
        $this->name = $name;
    }
    public function setClassName(string $className = null)
    {
        $this->className = $className;
        $this->updateCache();
    }
    public function getClassName() : string
    {
        if ($this->className !== null) {
            return $this->className;
        }
        return $this->name;
    }
    /**
     * @return MethodInjection|null
     */
    public function getConstructorInjection()
    {
        return $this->constructorInjection;
    }
    public function setConstructorInjection(MethodInjection $constructorInjection)
    {
        $this->constructorInjection = $constructorInjection;
    }
    public function completeConstructorInjection(MethodInjection $injection)
    {
        if ($this->constructorInjection !== null) {
            // Merge
            $this->constructorInjection->merge($injection);
        } else {
            // Set
            $this->constructorInjection = $injection;
        }
    }
    /**
     * @return PropertyInjection[] Property injections
     */
    public function getPropertyInjections() : array
    {
        return $this->propertyInjections;
    }
    public function addPropertyInjection(PropertyInjection $propertyInjection)
    {
        $className = $propertyInjection->getClassName();
        if ($className) {
            // Index with the class name to avoid collisions between parent and
            // child private properties with the same name
            $key = $className . '::' . $propertyInjection->getPropertyName();
        } else {
            $key = $propertyInjection->getPropertyName();
        }
        $this->propertyInjections[$key] = $propertyInjection;
    }
    /**
     * @return MethodInjection[] Method injections
     */
    public function getMethodInjections() : array
    {
        // Return array leafs
        $injections = [];
        array_walk_recursive($this->methodInjections, function ($injection) use(&$injections) {
            $injections[] = $injection;
        });
        return $injections;
    }
    public function addMethodInjection(MethodInjection $methodInjection)
    {
        $method = $methodInjection->getMethodName();
        if (!isset($this->methodInjections[$method])) {
            $this->methodInjections[$method] = [];
        }
        $this->methodInjections[$method][] = $methodInjection;
    }
    public function completeFirstMethodInjection(MethodInjection $injection)
    {
        $method = $injection->getMethodName();
        if (isset($this->methodInjections[$method][0])) {
            // Merge
            $this->methodInjections[$method][0]->merge($injection);
        } else {
            // Set
            $this->addMethodInjection($injection);
        }
    }
    public function setLazy(bool $lazy = null)
    {
        $this->lazy = $lazy;
    }
    public function isLazy() : bool
    {
        if ($this->lazy !== null) {
            return $this->lazy;
        }
        // Default value
        return \false;
    }
    public function classExists() : bool
    {
        return $this->classExists;
    }
    public function isInstantiable() : bool
    {
        return $this->isInstantiable;
    }
    public function replaceNestedDefinitions(callable $replacer)
    {
        array_walk($this->propertyInjections, function (PropertyInjection $propertyInjection) use($replacer) {
            $propertyInjection->replaceNestedDefinition($replacer);
        });
        if ($this->constructorInjection) {
            $this->constructorInjection->replaceNestedDefinitions($replacer);
        }
        array_walk($this->methodInjections, function ($injectionArray) use($replacer) {
            array_walk($injectionArray, function (MethodInjection $methodInjection) use($replacer) {
                $methodInjection->replaceNestedDefinitions($replacer);
            });
        });
    }
    /**
     * Replaces all the wildcards in the string with the given replacements.
     *
     * @param string[] $replacements
     */
    public function replaceWildcards(array $replacements)
    {
        $className = $this->getClassName();
        foreach ($replacements as $replacement) {
            $pos = strpos($className, DefinitionArray::WILDCARD);
            if ($pos !== \false) {
                $className = substr_replace($className, $replacement, $pos, 1);
            }
        }
        $this->setClassName($className);
    }
    public function __toString()
    {
        return (new ObjectDefinitionDumper())->dump($this);
    }
    private function updateCache()
    {
        $className = $this->getClassName();
        $this->classExists = class_exists($className) || interface_exists($className);
        if (!$this->classExists) {
            $this->isInstantiable = \false;
            return;
        }
        $class = new ReflectionClass($className);
        $this->isInstantiable = $class->isInstantiable();
    }
}

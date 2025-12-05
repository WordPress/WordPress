<?php

declare (strict_types=1);
namespace Matomo\Dependencies\DI\Definition\ObjectDefinition;

/**
 * Describe an injection in a class property.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class PropertyInjection
{
    /**
     * Property name.
     * @var string
     */
    private $propertyName;
    /**
     * Value that should be injected in the property.
     * @var mixed
     */
    private $value;
    /**
     * Use for injecting in properties of parent classes: the class name
     * must be the name of the parent class because private properties
     * can be attached to the parent classes, not the one we are resolving.
     * @var string|null
     */
    private $className;
    /**
     * @param string $propertyName Property name
     * @param mixed $value Value that should be injected in the property
     */
    public function __construct(string $propertyName, $value, string $className = null)
    {
        $this->propertyName = $propertyName;
        $this->value = $value;
        $this->className = $className;
    }
    public function getPropertyName() : string
    {
        return $this->propertyName;
    }
    /**
     * @return mixed Value that should be injected in the property
     */
    public function getValue()
    {
        return $this->value;
    }
    /**
     * @return string|null
     */
    public function getClassName()
    {
        return $this->className;
    }
    public function replaceNestedDefinition(callable $replacer)
    {
        $this->value = $replacer($this->value);
    }
}

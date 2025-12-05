<?php

declare (strict_types=1);
namespace Matomo\Dependencies\DI\Definition;

/**
 * Definition of a value or class with a factory.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class FactoryDefinition implements Definition
{
    /**
     * Entry name.
     * @var string
     */
    private $name;
    /**
     * Callable that returns the value.
     * @var callable
     */
    private $factory;
    /**
     * Factory parameters.
     * @var mixed[]
     */
    private $parameters = [];
    /**
     * @param string $name Entry name
     * @param callable $factory Callable that returns the value associated to the entry name.
     * @param array $parameters Parameters to be passed to the callable
     */
    public function __construct(string $name, $factory, array $parameters = [])
    {
        $this->name = $name;
        $this->factory = $factory;
        $this->parameters = $parameters;
    }
    public function getName() : string
    {
        return $this->name;
    }
    public function setName(string $name)
    {
        $this->name = $name;
    }
    /**
     * @return callable Callable that returns the value associated to the entry name.
     */
    public function getCallable()
    {
        return $this->factory;
    }
    /**
     * @return array Array containing the parameters to be passed to the callable, indexed by name.
     */
    public function getParameters() : array
    {
        return $this->parameters;
    }
    public function replaceNestedDefinitions(callable $replacer)
    {
        $this->parameters = array_map($replacer, $this->parameters);
    }
    public function __toString()
    {
        return 'Factory';
    }
}

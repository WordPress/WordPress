<?php

declare (strict_types=1);
namespace Matomo\Dependencies\DI\Definition\Helper;

use Matomo\Dependencies\DI\Definition\DecoratorDefinition;
use Matomo\Dependencies\DI\Definition\Definition;
use Matomo\Dependencies\DI\Definition\FactoryDefinition;
/**
 * Helps defining how to create an instance of a class using a factory (callable).
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class FactoryDefinitionHelper implements DefinitionHelper
{
    /**
     * @var callable
     */
    private $factory;
    /**
     * @var bool
     */
    private $decorate;
    /**
     * @var array
     */
    private $parameters = [];
    /**
     * @param callable $factory
     * @param bool $decorate Is the factory decorating a previous definition?
     */
    public function __construct($factory, bool $decorate = \false)
    {
        $this->factory = $factory;
        $this->decorate = $decorate;
    }
    /**
     * @param string $entryName Container entry name
     * @return FactoryDefinition
     */
    public function getDefinition(string $entryName) : Definition
    {
        if ($this->decorate) {
            return new DecoratorDefinition($entryName, $this->factory, $this->parameters);
        }
        return new FactoryDefinition($entryName, $this->factory, $this->parameters);
    }
    /**
     * Defines arguments to pass to the factory.
     *
     * Because factory methods do not yet support annotations or autowiring, this method
     * should be used to define all parameters except the ContainerInterface and RequestedEntry.
     *
     * Multiple calls can be made to the method to override individual values.
     *
     * @param string $parameter Name or index of the parameter for which the value will be given.
     * @param mixed  $value     Value to give to this parameter.
     *
     * @return $this
     */
    public function parameter(string $parameter, $value)
    {
        $this->parameters[$parameter] = $value;
        return $this;
    }
}

<?php

declare (strict_types=1);
namespace Matomo\Dependencies\DI\Definition\ObjectDefinition;

use Matomo\Dependencies\DI\Definition\Definition;
/**
 * Describe an injection in an object method.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class MethodInjection implements Definition
{
    /**
     * @var string
     */
    private $methodName;
    /**
     * @var mixed[]
     */
    private $parameters = [];
    public function __construct(string $methodName, array $parameters = [])
    {
        $this->methodName = $methodName;
        $this->parameters = $parameters;
    }
    public static function constructor(array $parameters = []) : self
    {
        return new self('__construct', $parameters);
    }
    public function getMethodName() : string
    {
        return $this->methodName;
    }
    /**
     * @return mixed[]
     */
    public function getParameters() : array
    {
        return $this->parameters;
    }
    /**
     * Replace the parameters of the definition by a new array of parameters.
     */
    public function replaceParameters(array $parameters)
    {
        $this->parameters = $parameters;
    }
    public function merge(self $definition)
    {
        // In case of conflicts, the current definition prevails.
        $this->parameters = $this->parameters + $definition->parameters;
    }
    public function getName() : string
    {
        return '';
    }
    public function setName(string $name)
    {
        // The name does not matter for method injections
    }
    public function replaceNestedDefinitions(callable $replacer)
    {
        $this->parameters = array_map($replacer, $this->parameters);
    }
    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return sprintf('method(%s)', $this->methodName);
    }
}

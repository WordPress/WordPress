<?php

declare (strict_types=1);
namespace Matomo\Dependencies\DI\Definition\Source;

use Matomo\Dependencies\DI\Definition\ArrayDefinition;
use Matomo\Dependencies\DI\Definition\AutowireDefinition;
use Matomo\Dependencies\DI\Definition\DecoratorDefinition;
use Matomo\Dependencies\DI\Definition\Definition;
use Matomo\Dependencies\DI\Definition\Exception\InvalidDefinition;
use Matomo\Dependencies\DI\Definition\FactoryDefinition;
use Matomo\Dependencies\DI\Definition\Helper\DefinitionHelper;
use Matomo\Dependencies\DI\Definition\ObjectDefinition;
use Matomo\Dependencies\DI\Definition\ValueDefinition;
/**
 * Turns raw definitions/definition helpers into definitions ready
 * to be resolved or compiled.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class DefinitionNormalizer
{
    /**
     * @var Autowiring
     */
    private $autowiring;
    public function __construct(Autowiring $autowiring)
    {
        $this->autowiring = $autowiring;
    }
    /**
     * Normalize a definition that is *not* nested in another one.
     *
     * This is usually a definition declared at the root of a definition array.
     *
     * @param mixed $definition
     * @param string $name The definition name.
     * @param string[] $wildcardsReplacements Replacements for wildcard definitions.
     *
     * @throws InvalidDefinition
     */
    public function normalizeRootDefinition($definition, string $name, array $wildcardsReplacements = null) : Definition
    {
        if ($definition instanceof DefinitionHelper) {
            $definition = $definition->getDefinition($name);
        } elseif (is_array($definition)) {
            $definition = new ArrayDefinition($definition);
        } elseif ($definition instanceof \Closure) {
            $definition = new FactoryDefinition($name, $definition);
        } elseif (!$definition instanceof Definition) {
            $definition = new ValueDefinition($definition);
        }
        // For a class definition, we replace * in the class name with the matches
        // *Interface -> *Impl => FooInterface -> FooImpl
        if ($wildcardsReplacements && $definition instanceof ObjectDefinition) {
            $definition->replaceWildcards($wildcardsReplacements);
        }
        if ($definition instanceof AutowireDefinition) {
            $definition = $this->autowiring->autowire($name, $definition);
        }
        $definition->setName($name);
        try {
            $definition->replaceNestedDefinitions([$this, 'normalizeNestedDefinition']);
        } catch (InvalidDefinition $e) {
            throw InvalidDefinition::create($definition, sprintf('Definition "%s" contains an error: %s', $definition->getName(), $e->getMessage()), $e);
        }
        return $definition;
    }
    /**
     * Normalize a definition that is nested in another one.
     *
     * @param mixed $definition
     * @return mixed
     *
     * @throws InvalidDefinition
     */
    public function normalizeNestedDefinition($definition)
    {
        $name = '<nested definition>';
        if ($definition instanceof DefinitionHelper) {
            $definition = $definition->getDefinition($name);
        } elseif (is_array($definition)) {
            $definition = new ArrayDefinition($definition);
        } elseif ($definition instanceof \Closure) {
            $definition = new FactoryDefinition($name, $definition);
        }
        if ($definition instanceof DecoratorDefinition) {
            throw new InvalidDefinition('Decorators cannot be nested in another definition');
        }
        if ($definition instanceof AutowireDefinition) {
            $definition = $this->autowiring->autowire($name, $definition);
        }
        if ($definition instanceof Definition) {
            $definition->setName($name);
            // Recursively traverse nested definitions
            $definition->replaceNestedDefinitions([$this, 'normalizeNestedDefinition']);
        }
        return $definition;
    }
}

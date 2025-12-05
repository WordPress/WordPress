<?php

declare (strict_types=1);
namespace Matomo\Dependencies\DI\Definition;

/**
 * Factory that decorates a sub-definition.
 *
 * @since 5.0
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class DecoratorDefinition extends FactoryDefinition implements Definition, ExtendsPreviousDefinition
{
    /**
     * @var Definition|null
     */
    private $decorated;
    public function setExtendedDefinition(Definition $definition)
    {
        $this->decorated = $definition;
    }
    /**
     * @return Definition|null
     */
    public function getDecoratedDefinition()
    {
        return $this->decorated;
    }
    public function replaceNestedDefinitions(callable $replacer)
    {
        // no nested definitions
    }
    public function __toString()
    {
        return 'Decorate(' . $this->getName() . ')';
    }
}

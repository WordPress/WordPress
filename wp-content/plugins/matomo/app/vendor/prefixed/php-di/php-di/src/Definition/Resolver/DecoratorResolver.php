<?php

declare (strict_types=1);
namespace Matomo\Dependencies\DI\Definition\Resolver;

use Matomo\Dependencies\DI\Definition\DecoratorDefinition;
use Matomo\Dependencies\DI\Definition\Definition;
use Matomo\Dependencies\DI\Definition\Exception\InvalidDefinition;
use Matomo\Dependencies\Psr\Container\ContainerInterface;
/**
 * Resolves a decorator definition to a value.
 *
 * @since 5.0
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class DecoratorResolver implements DefinitionResolver
{
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var DefinitionResolver
     */
    private $definitionResolver;
    /**
     * The resolver needs a container. This container will be passed to the factory as a parameter
     * so that the factory can access other entries of the container.
     *
     * @param DefinitionResolver $definitionResolver Used to resolve nested definitions.
     */
    public function __construct(ContainerInterface $container, DefinitionResolver $definitionResolver)
    {
        $this->container = $container;
        $this->definitionResolver = $definitionResolver;
    }
    /**
     * Resolve a decorator definition to a value.
     *
     * This will call the callable of the definition and pass it the decorated entry.
     *
     * @param DecoratorDefinition $definition
     */
    public function resolve(Definition $definition, array $parameters = [])
    {
        $callable = $definition->getCallable();
        if (!is_callable($callable)) {
            throw new InvalidDefinition(sprintf('The decorator "%s" is not callable', $definition->getName()));
        }
        $decoratedDefinition = $definition->getDecoratedDefinition();
        if (!$decoratedDefinition instanceof Definition) {
            if (!$definition->getName()) {
                throw new InvalidDefinition('Decorators cannot be nested in another definition');
            }
            throw new InvalidDefinition(sprintf('Entry "%s" decorates nothing: no previous definition with the same name was found', $definition->getName()));
        }
        $decorated = $this->definitionResolver->resolve($decoratedDefinition, $parameters);
        return call_user_func($callable, $decorated, $this->container);
    }
    public function isResolvable(Definition $definition, array $parameters = []) : bool
    {
        return \true;
    }
}

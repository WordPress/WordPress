<?php

declare (strict_types=1);
namespace Matomo\Dependencies\DI\Definition\Resolver;

use Matomo\Dependencies\DI\Definition\Definition;
use Matomo\Dependencies\DI\Definition\Exception\InvalidDefinition;
use Matomo\Dependencies\DI\Definition\ObjectDefinition;
use Matomo\Dependencies\DI\Definition\ObjectDefinition\PropertyInjection;
use Matomo\Dependencies\DI\DependencyException;
use Matomo\Dependencies\DI\Proxy\ProxyFactory;
use Exception;
use ProxyManager\Proxy\LazyLoadingInterface;
use Matomo\Dependencies\Psr\Container\NotFoundExceptionInterface;
use ReflectionClass;
use ReflectionProperty;
/**
 * Create objects based on an object definition.
 *
 * @since 4.0
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class ObjectCreator implements DefinitionResolver
{
    /**
     * @var ProxyFactory
     */
    private $proxyFactory;
    /**
     * @var ParameterResolver
     */
    private $parameterResolver;
    /**
     * @var DefinitionResolver
     */
    private $definitionResolver;
    /**
     * @param DefinitionResolver $definitionResolver Used to resolve nested definitions.
     * @param ProxyFactory       $proxyFactory       Used to create proxies for lazy injections.
     */
    public function __construct(DefinitionResolver $definitionResolver, ProxyFactory $proxyFactory)
    {
        $this->definitionResolver = $definitionResolver;
        $this->proxyFactory = $proxyFactory;
        $this->parameterResolver = new ParameterResolver($definitionResolver);
    }
    /**
     * Resolve a class definition to a value.
     *
     * This will create a new instance of the class using the injections points defined.
     *
     * @param ObjectDefinition $definition
     *
     * @return object|null
     */
    public function resolve(Definition $definition, array $parameters = [])
    {
        // Lazy?
        if ($definition->isLazy()) {
            return $this->createProxy($definition, $parameters);
        }
        return $this->createInstance($definition, $parameters);
    }
    /**
     * The definition is not resolvable if the class is not instantiable (interface or abstract)
     * or if the class doesn't exist.
     *
     * @param ObjectDefinition $definition
     */
    public function isResolvable(Definition $definition, array $parameters = []) : bool
    {
        return $definition->isInstantiable();
    }
    /**
     * Returns a proxy instance.
     */
    private function createProxy(ObjectDefinition $definition, array $parameters) : LazyLoadingInterface
    {
        /** @noinspection PhpUnusedParameterInspection */
        $proxy = $this->proxyFactory->createProxy($definition->getClassName(), function (&$wrappedObject, $proxy, $method, $params, &$initializer) use($definition, $parameters) {
            $wrappedObject = $this->createInstance($definition, $parameters);
            $initializer = null;
            // turning off further lazy initialization
            return \true;
        });
        return $proxy;
    }
    /**
     * Creates an instance of the class and injects dependencies..
     *
     * @param array            $parameters      Optional parameters to use to create the instance.
     *
     * @throws InvalidDefinition
     * @throws DependencyException
     * @return object
     */
    private function createInstance(ObjectDefinition $definition, array $parameters)
    {
        // Check that the class is instantiable
        if (!$definition->isInstantiable()) {
            // Check that the class exists
            if (!$definition->classExists()) {
                throw InvalidDefinition::create($definition, sprintf('Entry "%s" cannot be resolved: the class doesn\'t exist', $definition->getName()));
            }
            throw InvalidDefinition::create($definition, sprintf('Entry "%s" cannot be resolved: the class is not instantiable', $definition->getName()));
        }
        $classname = $definition->getClassName();
        $classReflection = new ReflectionClass($classname);
        $constructorInjection = $definition->getConstructorInjection();
        try {
            $args = $this->parameterResolver->resolveParameters($constructorInjection, $classReflection->getConstructor(), $parameters);
            $object = new $classname(...$args);
            $this->injectMethodsAndProperties($object, $definition);
        } catch (NotFoundExceptionInterface $e) {
            throw new DependencyException(sprintf('Error while injecting dependencies into %s: %s', $classReflection->getName(), $e->getMessage()), 0, $e);
        } catch (InvalidDefinition $e) {
            throw InvalidDefinition::create($definition, sprintf('Entry "%s" cannot be resolved: %s', $definition->getName(), $e->getMessage()));
        }
        return $object;
    }
    protected function injectMethodsAndProperties($object, ObjectDefinition $objectDefinition)
    {
        // Property injections
        foreach ($objectDefinition->getPropertyInjections() as $propertyInjection) {
            $this->injectProperty($object, $propertyInjection);
        }
        // Method injections
        foreach ($objectDefinition->getMethodInjections() as $methodInjection) {
            $methodReflection = new \ReflectionMethod($object, $methodInjection->getMethodName());
            $args = $this->parameterResolver->resolveParameters($methodInjection, $methodReflection);
            $methodReflection->invokeArgs($object, $args);
        }
    }
    /**
     * Inject dependencies into properties.
     *
     * @param object            $object            Object to inject dependencies into
     * @param PropertyInjection $propertyInjection Property injection definition
     *
     * @throws DependencyException
     * @throws InvalidDefinition
     */
    private function injectProperty($object, PropertyInjection $propertyInjection)
    {
        $propertyName = $propertyInjection->getPropertyName();
        $value = $propertyInjection->getValue();
        if ($value instanceof Definition) {
            try {
                $value = $this->definitionResolver->resolve($value);
            } catch (DependencyException $e) {
                throw $e;
            } catch (Exception $e) {
                throw new DependencyException(sprintf('Error while injecting in %s::%s. %s', get_class($object), $propertyName, $e->getMessage()), 0, $e);
            }
        }
        self::setPrivatePropertyValue($propertyInjection->getClassName(), $object, $propertyName, $value);
    }
    public static function setPrivatePropertyValue(string $className = null, $object, string $propertyName, $propertyValue)
    {
        $className = $className ?: get_class($object);
        $property = new ReflectionProperty($className, $propertyName);
        if (!$property->isPublic()) {
            $property->setAccessible(\true);
        }
        $property->setValue($object, $propertyValue);
    }
}

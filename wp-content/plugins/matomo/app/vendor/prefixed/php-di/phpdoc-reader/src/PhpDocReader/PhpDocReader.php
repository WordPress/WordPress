<?php

declare (strict_types=1);
namespace Matomo\Dependencies\PhpDocReader;

use Matomo\Dependencies\PhpDocReader\PhpParser\UseStatementParser;
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionProperty;
use Reflector;
/**
 * PhpDoc reader
 */
class PhpDocReader
{
    /** @var UseStatementParser */
    private $parser;
    private const PRIMITIVE_TYPES = ['bool' => 'bool', 'boolean' => 'bool', 'string' => 'string', 'int' => 'int', 'integer' => 'int', 'float' => 'float', 'double' => 'float', 'array' => 'array', 'object' => 'object', 'callable' => 'callable', 'resource' => 'resource', 'mixed' => 'mixed', 'iterable' => 'iterable'];
    /** @var bool */
    private $ignorePhpDocErrors;
    /**
     * @param bool $ignorePhpDocErrors Enable or disable throwing errors when PhpDoc errors occur (when parsing annotations).
     */
    public function __construct(bool $ignorePhpDocErrors = \false)
    {
        $this->parser = new UseStatementParser();
        $this->ignorePhpDocErrors = $ignorePhpDocErrors;
    }
    /**
     * Parse the docblock of the property to get the type (class or primitive type) of the var annotation.
     *
     * @return string|null Type of the property (content of var annotation)
     * @throws AnnotationException
     */
    public function getPropertyType(ReflectionProperty $property) : ?string
    {
        return $this->readPropertyType($property, \true);
    }
    /**
     * Parse the docblock of the property to get the class of the var annotation.
     *
     * @return string|null Type of the property (content of var annotation)
     * @throws AnnotationException
     */
    public function getPropertyClass(ReflectionProperty $property) : ?string
    {
        return $this->readPropertyType($property, \false);
    }
    private function readPropertyType(ReflectionProperty $property, bool $allowPrimitiveTypes) : ?string
    {
        // Get the content of the @var annotation
        $docComment = $property->getDocComment();
        if (!$docComment) {
            return null;
        }
        if (preg_match('/@var\\s+([^\\s]+)/', $docComment, $matches)) {
            [, $type] = $matches;
        } else {
            return null;
        }
        // Ignore primitive types
        if (isset(self::PRIMITIVE_TYPES[$type])) {
            if ($allowPrimitiveTypes) {
                return self::PRIMITIVE_TYPES[$type];
            }
            return null;
        }
        // Ignore types containing special characters ([], <> ...)
        if (!preg_match('/^[a-zA-Z0-9\\\\_]+$/', $type)) {
            return null;
        }
        $class = $property->getDeclaringClass();
        // If the class name is not fully qualified (i.e. doesn't start with a \)
        if ($type[0] !== '\\') {
            // Try to resolve the FQN using the class context
            $resolvedType = $this->tryResolveFqn($type, $class, $property);
            if (!$resolvedType && !$this->ignorePhpDocErrors) {
                throw new AnnotationException(sprintf('The @var annotation on %s::%s contains a non existent class "%s". ' . 'Did you maybe forget to add a "use" statement for this annotation?', $class->name, $property->getName(), $type));
            }
            $type = $resolvedType;
        }
        if (!$this->ignorePhpDocErrors && !$this->classExists($type)) {
            throw new AnnotationException(sprintf('The @var annotation on %s::%s contains a non existent class "%s"', $class->name, $property->getName(), $type));
        }
        // Remove the leading \ (FQN shouldn't contain it)
        $type = is_string($type) ? ltrim($type, '\\') : null;
        return $type;
    }
    /**
     * Parse the docblock of the property to get the type (class or primitive type) of the param annotation.
     *
     * @return string|null Type of the property (content of var annotation)
     * @throws AnnotationException
     */
    public function getParameterType(ReflectionParameter $parameter) : ?string
    {
        return $this->readParameterClass($parameter, \true);
    }
    /**
     * Parse the docblock of the property to get the class of the param annotation.
     *
     * @return string|null Type of the property (content of var annotation)
     * @throws AnnotationException
     */
    public function getParameterClass(ReflectionParameter $parameter) : ?string
    {
        return $this->readParameterClass($parameter, \false);
    }
    private function readParameterClass(ReflectionParameter $parameter, bool $allowPrimitiveTypes) : ?string
    {
        // Use reflection
        $parameterType = $parameter->getType();
        if ($parameterType && $parameterType instanceof \ReflectionNamedType && !$parameterType->isBuiltin()) {
            return $parameterType->getName();
        }
        $parameterName = $parameter->name;
        // Get the content of the @param annotation
        $method = $parameter->getDeclaringFunction();
        $docComment = $method->getDocComment();
        if (!$docComment) {
            return null;
        }
        if (preg_match('/@param\\s+([^\\s]+)\\s+\\$' . $parameterName . '/', $docComment, $matches)) {
            [, $type] = $matches;
        } else {
            return null;
        }
        // Ignore primitive types
        if (isset(self::PRIMITIVE_TYPES[$type])) {
            if ($allowPrimitiveTypes) {
                return self::PRIMITIVE_TYPES[$type];
            }
            return null;
        }
        // Ignore types containing special characters ([], <> ...)
        if (!preg_match('/^[a-zA-Z0-9\\\\_]+$/', $type)) {
            return null;
        }
        $class = $parameter->getDeclaringClass();
        // If the class name is not fully qualified (i.e. doesn't start with a \)
        if ($type[0] !== '\\') {
            // Try to resolve the FQN using the class context
            $resolvedType = $this->tryResolveFqn($type, $class, $parameter);
            if (!$resolvedType && !$this->ignorePhpDocErrors) {
                throw new AnnotationException(sprintf('The @param annotation for parameter "%s" of %s::%s contains a non existent class "%s". ' . 'Did you maybe forget to add a "use" statement for this annotation?', $parameterName, $class->name, $method->name, $type));
            }
            $type = $resolvedType;
        }
        if (!$this->ignorePhpDocErrors && !$this->classExists($type)) {
            throw new AnnotationException(sprintf('The @param annotation for parameter "%s" of %s::%s contains a non existent class "%s"', $parameterName, $class->name, $method->name, $type));
        }
        // Remove the leading \ (FQN shouldn't contain it)
        $type = is_string($type) ? ltrim($type, '\\') : null;
        return $type;
    }
    /**
     * Attempts to resolve the FQN of the provided $type based on the $class and $member context.
     *
     * @return string|null Fully qualified name of the type, or null if it could not be resolved
     */
    private function tryResolveFqn(string $type, ReflectionClass $class, Reflector $member) : ?string
    {
        $alias = ($pos = strpos($type, '\\')) === \false ? $type : substr($type, 0, $pos);
        $loweredAlias = strtolower($alias);
        // Retrieve "use" statements
        $uses = $this->parser->parseUseStatements($class);
        if (isset($uses[$loweredAlias])) {
            // Imported classes
            if ($pos !== \false) {
                return $uses[$loweredAlias] . substr($type, $pos);
            }
            return $uses[$loweredAlias];
        }
        if ($this->classExists($class->getNamespaceName() . '\\' . $type)) {
            return $class->getNamespaceName() . '\\' . $type;
        }
        if (isset($uses['__NAMESPACE__']) && $this->classExists($uses['__NAMESPACE__'] . '\\' . $type)) {
            // Class namespace
            return $uses['__NAMESPACE__'] . '\\' . $type;
        }
        if ($this->classExists($type)) {
            // No namespace
            return $type;
        }
        // If all fail, try resolving through related traits
        return $this->tryResolveFqnInTraits($type, $class, $member);
    }
    /**
     * Attempts to resolve the FQN of the provided $type based on the $class and $member context, specifically searching
     * through the traits that are used by the provided $class.
     *
     * @return string|null Fully qualified name of the type, or null if it could not be resolved
     */
    private function tryResolveFqnInTraits(string $type, ReflectionClass $class, Reflector $member) : ?string
    {
        /** @var ReflectionClass[] $traits */
        $traits = [];
        // Get traits for the class and its parents
        while ($class) {
            $traits = array_merge($traits, $class->getTraits());
            $class = $class->getParentClass();
        }
        foreach ($traits as $trait) {
            // Eliminate traits that don't have the property/method/parameter
            if ($member instanceof ReflectionProperty && !$trait->hasProperty($member->name)) {
                continue;
            }
            if ($member instanceof ReflectionMethod && !$trait->hasMethod($member->name)) {
                continue;
            }
            if ($member instanceof ReflectionParameter && !$trait->hasMethod($member->getDeclaringFunction()->name)) {
                continue;
            }
            // Run the resolver again with the ReflectionClass instance for the trait
            $resolvedType = $this->tryResolveFqn($type, $trait, $member);
            if ($resolvedType) {
                return $resolvedType;
            }
        }
        return null;
    }
    private function classExists(string $class) : bool
    {
        return class_exists($class) || interface_exists($class);
    }
}

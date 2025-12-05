<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ElementorDeps\Twig\Node\Expression;

use ElementorDeps\Twig\Compiler;
use ElementorDeps\Twig\Error\SyntaxError;
use ElementorDeps\Twig\Extension\ExtensionInterface;
use ElementorDeps\Twig\Node\Node;
use ElementorDeps\Twig\Util\ReflectionCallable;
abstract class CallExpression extends AbstractExpression
{
    private $reflector = null;
    protected function compileCallable(Compiler $compiler)
    {
        $callable = $this->getAttribute('callable');
        if (\is_string($callable) && !\str_contains($callable, '::')) {
            $compiler->raw($callable);
        } else {
            $rc = $this->reflectCallable($callable);
            $r = $rc->getReflector();
            $callable = $rc->getCallable();
            if (\is_string($callable)) {
                $compiler->raw($callable);
            } elseif (\is_array($callable) && \is_string($callable[0])) {
                if (!$r instanceof \ReflectionMethod || $r->isStatic()) {
                    $compiler->raw(\sprintf('%s::%s', $callable[0], $callable[1]));
                } else {
                    $compiler->raw(\sprintf('$this->env->getRuntime(\'%s\')->%s', $callable[0], $callable[1]));
                }
            } elseif (\is_array($callable) && $callable[0] instanceof ExtensionInterface) {
                $class = \get_class($callable[0]);
                if (!$compiler->getEnvironment()->hasExtension($class)) {
                    // Compile a non-optimized call to trigger a \Twig\Error\RuntimeError, which cannot be a compile-time error
                    $compiler->raw(\sprintf('$this->env->getExtension(\'%s\')', $class));
                } else {
                    $compiler->raw(\sprintf('$this->extensions[\'%s\']', \ltrim($class, '\\')));
                }
                $compiler->raw(\sprintf('->%s', $callable[1]));
            } else {
                $compiler->raw(\sprintf('$this->env->get%s(\'%s\')->getCallable()', \ucfirst($this->getAttribute('type')), $this->getAttribute('name')));
            }
        }
        $this->compileArguments($compiler);
    }
    protected function compileArguments(Compiler $compiler, $isArray = \false) : void
    {
        if (\func_num_args() >= 2) {
            trigger_deprecation('twig/twig', '3.11', 'Passing a second argument to "%s()" is deprecated.', __METHOD__);
        }
        $compiler->raw($isArray ? '[' : '(');
        $first = \true;
        if ($this->hasAttribute('needs_charset') && $this->getAttribute('needs_charset')) {
            $compiler->raw('$this->env->getCharset()');
            $first = \false;
        }
        if ($this->hasAttribute('needs_environment') && $this->getAttribute('needs_environment')) {
            if (!$first) {
                $compiler->raw(', ');
            }
            $compiler->raw('$this->env');
            $first = \false;
        }
        if ($this->hasAttribute('needs_context') && $this->getAttribute('needs_context')) {
            if (!$first) {
                $compiler->raw(', ');
            }
            $compiler->raw('$context');
            $first = \false;
        }
        if ($this->hasAttribute('arguments')) {
            foreach ($this->getAttribute('arguments') as $argument) {
                if (!$first) {
                    $compiler->raw(', ');
                }
                $compiler->string($argument);
                $first = \false;
            }
        }
        if ($this->hasNode('node')) {
            if (!$first) {
                $compiler->raw(', ');
            }
            $compiler->subcompile($this->getNode('node'));
            $first = \false;
        }
        if ($this->hasNode('arguments')) {
            $callable = $this->getAttribute('callable');
            $arguments = $this->getArguments($callable, $this->getNode('arguments'));
            foreach ($arguments as $node) {
                if (!$first) {
                    $compiler->raw(', ');
                }
                $compiler->subcompile($node);
                $first = \false;
            }
        }
        $compiler->raw($isArray ? ']' : ')');
    }
    protected function getArguments($callable, $arguments)
    {
        $callType = $this->getAttribute('type');
        $callName = $this->getAttribute('name');
        $parameters = [];
        $named = \false;
        foreach ($arguments as $name => $node) {
            if (!\is_int($name)) {
                $named = \true;
                $name = $this->normalizeName($name);
            } elseif ($named) {
                throw new SyntaxError(\sprintf('Positional arguments cannot be used after named arguments for %s "%s".', $callType, $callName), $this->getTemplateLine(), $this->getSourceContext());
            }
            $parameters[$name] = $node;
        }
        $isVariadic = $this->hasAttribute('is_variadic') && $this->getAttribute('is_variadic');
        if (!$named && !$isVariadic) {
            return $parameters;
        }
        if (!$callable) {
            if ($named) {
                $message = \sprintf('Named arguments are not supported for %s "%s".', $callType, $callName);
            } else {
                $message = \sprintf('Arbitrary positional arguments are not supported for %s "%s".', $callType, $callName);
            }
            throw new \LogicException($message);
        }
        [$callableParameters, $isPhpVariadic] = $this->getCallableParameters($callable, $isVariadic);
        $arguments = [];
        $names = [];
        $missingArguments = [];
        $optionalArguments = [];
        $pos = 0;
        foreach ($callableParameters as $callableParameter) {
            $name = $this->normalizeName($callableParameter->name);
            if (\PHP_VERSION_ID >= 80000 && 'range' === $callable) {
                if ('start' === $name) {
                    $name = 'low';
                } elseif ('end' === $name) {
                    $name = 'high';
                }
            }
            $names[] = $name;
            if (\array_key_exists($name, $parameters)) {
                if (\array_key_exists($pos, $parameters)) {
                    throw new SyntaxError(\sprintf('Argument "%s" is defined twice for %s "%s".', $name, $callType, $callName), $this->getTemplateLine(), $this->getSourceContext());
                }
                if (\count($missingArguments)) {
                    throw new SyntaxError(\sprintf('Argument "%s" could not be assigned for %s "%s(%s)" because it is mapped to an internal PHP function which cannot determine default value for optional argument%s "%s".', $name, $callType, $callName, \implode(', ', $names), \count($missingArguments) > 1 ? 's' : '', \implode('", "', $missingArguments)), $this->getTemplateLine(), $this->getSourceContext());
                }
                $arguments = \array_merge($arguments, $optionalArguments);
                $arguments[] = $parameters[$name];
                unset($parameters[$name]);
                $optionalArguments = [];
            } elseif (\array_key_exists($pos, $parameters)) {
                $arguments = \array_merge($arguments, $optionalArguments);
                $arguments[] = $parameters[$pos];
                unset($parameters[$pos]);
                $optionalArguments = [];
                ++$pos;
            } elseif ($callableParameter->isDefaultValueAvailable()) {
                $optionalArguments[] = new ConstantExpression($callableParameter->getDefaultValue(), -1);
            } elseif ($callableParameter->isOptional()) {
                if (empty($parameters)) {
                    break;
                } else {
                    $missingArguments[] = $name;
                }
            } else {
                throw new SyntaxError(\sprintf('Value for argument "%s" is required for %s "%s".', $name, $callType, $callName), $this->getTemplateLine(), $this->getSourceContext());
            }
        }
        if ($isVariadic) {
            $arbitraryArguments = $isPhpVariadic ? new VariadicExpression([], -1) : new ArrayExpression([], -1);
            foreach ($parameters as $key => $value) {
                if (\is_int($key)) {
                    $arbitraryArguments->addElement($value);
                } else {
                    $arbitraryArguments->addElement($value, new ConstantExpression($key, -1));
                }
                unset($parameters[$key]);
            }
            if ($arbitraryArguments->count()) {
                $arguments = \array_merge($arguments, $optionalArguments);
                $arguments[] = $arbitraryArguments;
            }
        }
        if (!empty($parameters)) {
            $unknownParameter = null;
            foreach ($parameters as $parameter) {
                if ($parameter instanceof Node) {
                    $unknownParameter = $parameter;
                    break;
                }
            }
            throw new SyntaxError(\sprintf('Unknown argument%s "%s" for %s "%s(%s)".', \count($parameters) > 1 ? 's' : '', \implode('", "', \array_keys($parameters)), $callType, $callName, \implode(', ', $names)), $unknownParameter ? $unknownParameter->getTemplateLine() : $this->getTemplateLine(), $unknownParameter ? $unknownParameter->getSourceContext() : $this->getSourceContext());
        }
        return $arguments;
    }
    protected function normalizeName(string $name) : string
    {
        return \strtolower(\preg_replace(['/([A-Z]+)([A-Z][a-z])/', '/([a-z\\d])([A-Z])/'], ['\\1_\\2', '\\1_\\2'], $name));
    }
    private function getCallableParameters($callable, bool $isVariadic) : array
    {
        $rc = $this->reflectCallable($callable);
        $r = $rc->getReflector();
        $callableName = $rc->getName();
        $parameters = $r->getParameters();
        if ($this->hasNode('node')) {
            \array_shift($parameters);
        }
        if ($this->hasAttribute('needs_charset') && $this->getAttribute('needs_charset')) {
            \array_shift($parameters);
        }
        if ($this->hasAttribute('needs_environment') && $this->getAttribute('needs_environment')) {
            \array_shift($parameters);
        }
        if ($this->hasAttribute('needs_context') && $this->getAttribute('needs_context')) {
            \array_shift($parameters);
        }
        if ($this->hasAttribute('arguments') && null !== $this->getAttribute('arguments')) {
            foreach ($this->getAttribute('arguments') as $argument) {
                \array_shift($parameters);
            }
        }
        $isPhpVariadic = \false;
        if ($isVariadic) {
            $argument = \end($parameters);
            $isArray = $argument && $argument->hasType() && $argument->getType() instanceof \ReflectionNamedType && 'array' === $argument->getType()->getName();
            if ($isArray && $argument->isDefaultValueAvailable() && [] === $argument->getDefaultValue()) {
                \array_pop($parameters);
            } elseif ($argument && $argument->isVariadic()) {
                \array_pop($parameters);
                $isPhpVariadic = \true;
            } else {
                throw new \LogicException(\sprintf('The last parameter of "%s" for %s "%s" must be an array with default value, eg. "array $arg = []".', $callableName, $this->getAttribute('type'), $this->getAttribute('name')));
            }
        }
        return [$parameters, $isPhpVariadic];
    }
    private function reflectCallable($callable) : ReflectionCallable
    {
        if (!$this->reflector) {
            $this->reflector = new ReflectionCallable($callable, $this->getAttribute('type'), $this->getAttribute('name'));
        }
        return $this->reflector;
    }
}

<?php

namespace Matomo\Dependencies\Invoker\Reflection;

use Matomo\Dependencies\Invoker\Exception\NotCallableException;
/**
 * Create a reflection object from a callable.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class CallableReflection
{
    /**
     * @param callable $callable
     *
     * @return \ReflectionFunctionAbstract
     *
     * @throws NotCallableException
     *
     * TODO Use the `callable` type-hint once support for PHP 5.4 and up.
     */
    public static function create($callable)
    {
        // Closure
        if ($callable instanceof \Closure) {
            return new \ReflectionFunction($callable);
        }
        // Array callable
        if (is_array($callable)) {
            list($class, $method) = $callable;
            if (!method_exists($class, $method)) {
                throw NotCallableException::fromInvalidCallable($callable);
            }
            return new \ReflectionMethod($class, $method);
        }
        // Callable object (i.e. implementing __invoke())
        if (is_object($callable) && method_exists($callable, '__invoke')) {
            return new \ReflectionMethod($callable, '__invoke');
        }
        // Callable class (i.e. implementing __invoke())
        if (is_string($callable) && class_exists($callable) && method_exists($callable, '__invoke')) {
            return new \ReflectionMethod($callable, '__invoke');
        }
        // Standard function
        if (is_string($callable) && function_exists($callable)) {
            return new \ReflectionFunction($callable);
        }
        throw new NotCallableException(sprintf('%s is not a callable', is_string($callable) ? $callable : 'Instance of ' . get_class($callable)));
    }
}

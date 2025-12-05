<?php

namespace Matomo\Dependencies\Invoker\Exception;

/**
 * The given callable is not actually callable.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class NotCallableException extends InvocationException
{
    /**
     * @param string $value
     * @param bool $containerEntry
     * @return self
     */
    public static function fromInvalidCallable($value, $containerEntry = \false)
    {
        if (is_object($value)) {
            $message = sprintf('Instance of %s is not a callable', get_class($value));
        } elseif (is_array($value) && isset($value[0]) && isset($value[1])) {
            $class = is_object($value[0]) ? get_class($value[0]) : $value[0];
            $extra = method_exists($class, '__call') ? ' A __call() method exists but magic methods are not supported.' : '';
            $message = sprintf('%s::%s() is not a callable.%s', $class, $value[1], $extra);
        } else {
            if ($containerEntry) {
                $message = var_export($value, \true) . ' is neither a callable nor a valid container entry';
            } else {
                $message = var_export($value, \true) . ' is not a callable';
            }
        }
        return new self($message);
    }
}

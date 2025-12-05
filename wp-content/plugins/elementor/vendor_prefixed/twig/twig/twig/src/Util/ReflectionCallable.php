<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ElementorDeps\Twig\Util;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @internal
 */
final class ReflectionCallable
{
    private $reflector;
    private $callable = null;
    private $name;
    public function __construct($callable, string $debugType = 'unknown', string $debugName = 'unknown')
    {
        if (\is_string($callable) && \false !== ($pos = \strpos($callable, '::'))) {
            $callable = [\substr($callable, 0, $pos), \substr($callable, 2 + $pos)];
        }
        if (\is_array($callable) && \method_exists($callable[0], $callable[1])) {
            $this->reflector = $r = new \ReflectionMethod($callable[0], $callable[1]);
            $this->callable = $callable;
            $this->name = $r->class . '::' . $r->name;
            return;
        }
        $checkVisibility = $callable instanceof \Closure;
        try {
            $closure = \Closure::fromCallable($callable);
        } catch (\TypeError $e) {
            throw new \LogicException(\sprintf('Callback for %s "%s" is not callable in the current scope.', $debugType, $debugName), 0, $e);
        }
        $this->reflector = $r = new \ReflectionFunction($closure);
        if (\str_contains($r->name, '{closure')) {
            $this->callable = $callable;
            $this->name = 'Closure';
            return;
        }
        if ($object = $r->getClosureThis()) {
            $callable = [$object, $r->name];
            $this->name = \get_debug_type($object) . '::' . $r->name;
        } elseif (\PHP_VERSION_ID >= 80111 && ($class = $r->getClosureCalledClass())) {
            $callable = [$class->name, $r->name];
            $this->name = $class->name . '::' . $r->name;
        } elseif (\PHP_VERSION_ID < 80111 && ($class = $r->getClosureScopeClass())) {
            $callable = [\is_array($callable) ? $callable[0] : $class->name, $r->name];
            $this->name = (\is_array($callable) ? $callable[0] : $class->name) . '::' . $r->name;
        } else {
            $callable = $this->name = $r->name;
        }
        if ($checkVisibility && \is_array($callable) && \method_exists(...$callable) && !(new \ReflectionMethod(...$callable))->isPublic()) {
            $callable = $r->getClosure();
        }
        $this->callable = $callable;
    }
    public function getReflector() : \ReflectionFunctionAbstract
    {
        return $this->reflector;
    }
    public function getCallable()
    {
        return $this->callable;
    }
    public function getName() : string
    {
        return $this->name;
    }
}

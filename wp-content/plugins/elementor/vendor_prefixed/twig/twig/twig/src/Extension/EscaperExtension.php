<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ElementorDeps\Twig\Extension;

use ElementorDeps\Twig\Environment;
use ElementorDeps\Twig\FileExtensionEscapingStrategy;
use ElementorDeps\Twig\Node\Expression\ConstantExpression;
use ElementorDeps\Twig\Node\Expression\Filter\RawFilter;
use ElementorDeps\Twig\Node\Node;
use ElementorDeps\Twig\NodeVisitor\EscaperNodeVisitor;
use ElementorDeps\Twig\Runtime\EscaperRuntime;
use ElementorDeps\Twig\TokenParser\AutoEscapeTokenParser;
use ElementorDeps\Twig\TwigFilter;
final class EscaperExtension extends AbstractExtension
{
    private $environment;
    private $escapers = [];
    private $escaper;
    private $defaultStrategy;
    /**
     * @param string|false|callable $defaultStrategy An escaping strategy
     *
     * @see setDefaultStrategy()
     */
    public function __construct($defaultStrategy = 'html')
    {
        $this->setDefaultStrategy($defaultStrategy);
    }
    public function getTokenParsers() : array
    {
        return [new AutoEscapeTokenParser()];
    }
    public function getNodeVisitors() : array
    {
        return [new EscaperNodeVisitor()];
    }
    public function getFilters() : array
    {
        return [new TwigFilter('escape', [EscaperRuntime::class, 'escape'], ['is_safe_callback' => [self::class, 'escapeFilterIsSafe']]), new TwigFilter('e', [EscaperRuntime::class, 'escape'], ['is_safe_callback' => [self::class, 'escapeFilterIsSafe']]), new TwigFilter('raw', null, ['is_safe' => ['all'], 'node_class' => RawFilter::class])];
    }
    /**
     * @deprecated since Twig 3.10
     */
    public function setEnvironment(Environment $environment, bool $triggerDeprecation = \true) : void
    {
        if ($triggerDeprecation) {
            trigger_deprecation('twig/twig', '3.10', 'The "%s()" method is deprecated and not needed if you are using methods from "Twig\\Runtime\\EscaperRuntime".', __METHOD__);
        }
        $this->environment = $environment;
        $this->escaper = $environment->getRuntime(EscaperRuntime::class);
    }
    /**
     * @deprecated since Twig 3.10
     */
    public function setEscaperRuntime(EscaperRuntime $escaper)
    {
        trigger_deprecation('twig/twig', '3.10', 'The "%s()" method is deprecated and not needed if you are using methods from "Twig\\Runtime\\EscaperRuntime".', __METHOD__);
        $this->escaper = $escaper;
    }
    /**
     * Sets the default strategy to use when not defined by the user.
     *
     * The strategy can be a valid PHP callback that takes the template
     * name as an argument and returns the strategy to use.
     *
     * @param string|false|callable(string $templateName): string $defaultStrategy An escaping strategy
     */
    public function setDefaultStrategy($defaultStrategy) : void
    {
        if ('name' === $defaultStrategy) {
            $defaultStrategy = [FileExtensionEscapingStrategy::class, 'guess'];
        }
        $this->defaultStrategy = $defaultStrategy;
    }
    /**
     * Gets the default strategy to use when not defined by the user.
     *
     * @param string $name The template name
     *
     * @return string|false The default strategy to use for the template
     */
    public function getDefaultStrategy(string $name)
    {
        // disable string callables to avoid calling a function named html or js,
        // or any other upcoming escaping strategy
        if (!\is_string($this->defaultStrategy) && \false !== $this->defaultStrategy) {
            return \call_user_func($this->defaultStrategy, $name);
        }
        return $this->defaultStrategy;
    }
    /**
     * Defines a new escaper to be used via the escape filter.
     *
     * @param string                                        $strategy The strategy name that should be used as a strategy in the escape call
     * @param callable(Environment, string, string): string $callable A valid PHP callable
     *
     * @deprecated since Twig 3.10
     */
    public function setEscaper($strategy, callable $callable)
    {
        trigger_deprecation('twig/twig', '3.10', 'The "%s()" method is deprecated, use the "Twig\\Runtime\\EscaperRuntime::setEscaper()" method instead (be warned that Environment is not passed anymore to the callable).', __METHOD__);
        if (!isset($this->environment)) {
            throw new \LogicException(\sprintf('You must call "setEnvironment()" before calling "%s()".', __METHOD__));
        }
        $this->escapers[$strategy] = $callable;
        $callable = function ($string, $charset) use($callable) {
            return $callable($this->environment, $string, $charset);
        };
        $this->escaper->setEscaper($strategy, $callable);
    }
    /**
     * Gets all defined escapers.
     *
     * @return array<string, callable(Environment, string, string): string> An array of escapers
     *
     * @deprecated since Twig 3.10
     */
    public function getEscapers()
    {
        trigger_deprecation('twig/twig', '3.10', 'The "%s()" method is deprecated, use the "Twig\\Runtime\\EscaperRuntime::getEscaper()" method instead.', __METHOD__);
        return $this->escapers;
    }
    /**
     * @deprecated since Twig 3.10
     */
    public function setSafeClasses(array $safeClasses = [])
    {
        trigger_deprecation('twig/twig', '3.10', 'The "%s()" method is deprecated, use the "Twig\\Runtime\\EscaperRuntime::setSafeClasses()" method instead.', __METHOD__);
        if (!isset($this->escaper)) {
            throw new \LogicException(\sprintf('You must call "setEnvironment()" before calling "%s()".', __METHOD__));
        }
        $this->escaper->setSafeClasses($safeClasses);
    }
    /**
     * @deprecated since Twig 3.10
     */
    public function addSafeClass(string $class, array $strategies)
    {
        trigger_deprecation('twig/twig', '3.10', 'The "%s()" method is deprecated, use the "Twig\\Runtime\\EscaperRuntime::addSafeClass()" method instead.', __METHOD__);
        if (!isset($this->escaper)) {
            throw new \LogicException(\sprintf('You must call "setEnvironment()" before calling "%s()".', __METHOD__));
        }
        $this->escaper->addSafeClass($class, $strategies);
    }
    /**
     * @internal
     */
    public static function escapeFilterIsSafe(Node $filterArgs)
    {
        foreach ($filterArgs as $arg) {
            if ($arg instanceof ConstantExpression) {
                return [$arg->getAttribute('value')];
            }
            return [];
        }
        return ['html'];
    }
}

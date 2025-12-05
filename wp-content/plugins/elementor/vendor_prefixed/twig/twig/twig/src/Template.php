<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 * (c) Armin Ronacher
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ElementorDeps\Twig;

use ElementorDeps\Twig\Error\Error;
use ElementorDeps\Twig\Error\LoaderError;
use ElementorDeps\Twig\Error\RuntimeError;
/**
 * Default base class for compiled templates.
 *
 * This class is an implementation detail of how template compilation currently
 * works, which might change. It should never be used directly. Use $twig->load()
 * instead, which returns an instance of \Twig\TemplateWrapper.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @internal
 */
abstract class Template
{
    public const ANY_CALL = 'any';
    public const ARRAY_CALL = 'array';
    public const METHOD_CALL = 'method';
    protected $parent;
    protected $parents = [];
    protected $env;
    protected $blocks = [];
    protected $traits = [];
    protected $extensions = [];
    protected $sandbox;
    private $useYield;
    public function __construct(Environment $env)
    {
        $this->env = $env;
        $this->useYield = $env->useYield();
        $this->extensions = $env->getExtensions();
    }
    /**
     * Returns the template name.
     *
     * @return string The template name
     */
    public abstract function getTemplateName();
    /**
     * Returns debug information about the template.
     *
     * @return array Debug information
     */
    public abstract function getDebugInfo();
    /**
     * Returns information about the original template source code.
     *
     * @return Source
     */
    public abstract function getSourceContext();
    /**
     * Returns the parent template.
     *
     * This method is for internal use only and should never be called
     * directly.
     *
     * @return self|TemplateWrapper|false The parent template or false if there is no parent
     */
    public function getParent(array $context)
    {
        if (null !== $this->parent) {
            return $this->parent;
        }
        try {
            if (!($parent = $this->doGetParent($context))) {
                return \false;
            }
            if ($parent instanceof self || $parent instanceof TemplateWrapper) {
                return $this->parents[$parent->getSourceContext()->getName()] = $parent;
            }
            if (!isset($this->parents[$parent])) {
                $this->parents[$parent] = $this->loadTemplate($parent);
            }
        } catch (LoaderError $e) {
            $e->setSourceContext(null);
            $e->guess();
            throw $e;
        }
        return $this->parents[$parent];
    }
    protected function doGetParent(array $context)
    {
        return \false;
    }
    public function isTraitable()
    {
        return \true;
    }
    /**
     * Displays a parent block.
     *
     * This method is for internal use only and should never be called
     * directly.
     *
     * @param string $name    The block name to display from the parent
     * @param array  $context The context
     * @param array  $blocks  The current set of blocks
     */
    public function displayParentBlock($name, array $context, array $blocks = [])
    {
        foreach ($this->yieldParentBlock($name, $context, $blocks) as $data) {
            echo $data;
        }
    }
    /**
     * Displays a block.
     *
     * This method is for internal use only and should never be called
     * directly.
     *
     * @param string $name      The block name to display
     * @param array  $context   The context
     * @param array  $blocks    The current set of blocks
     * @param bool   $useBlocks Whether to use the current set of blocks
     */
    public function displayBlock($name, array $context, array $blocks = [], $useBlocks = \true, ?self $templateContext = null)
    {
        foreach ($this->yieldBlock($name, $context, $blocks, $useBlocks, $templateContext) as $data) {
            echo $data;
        }
    }
    /**
     * Renders a parent block.
     *
     * This method is for internal use only and should never be called
     * directly.
     *
     * @param string $name    The block name to render from the parent
     * @param array  $context The context
     * @param array  $blocks  The current set of blocks
     *
     * @return string The rendered block
     */
    public function renderParentBlock($name, array $context, array $blocks = [])
    {
        $content = '';
        foreach ($this->yieldParentBlock($name, $context, $blocks) as $data) {
            $content .= $data;
        }
        return $content;
    }
    /**
     * Renders a block.
     *
     * This method is for internal use only and should never be called
     * directly.
     *
     * @param string $name      The block name to render
     * @param array  $context   The context
     * @param array  $blocks    The current set of blocks
     * @param bool   $useBlocks Whether to use the current set of blocks
     *
     * @return string The rendered block
     */
    public function renderBlock($name, array $context, array $blocks = [], $useBlocks = \true)
    {
        $content = '';
        foreach ($this->yieldBlock($name, $context, $blocks, $useBlocks) as $data) {
            $content .= $data;
        }
        return $content;
    }
    /**
     * Returns whether a block exists or not in the current context of the template.
     *
     * This method checks blocks defined in the current template
     * or defined in "used" traits or defined in parent templates.
     *
     * @param string $name    The block name
     * @param array  $context The context
     * @param array  $blocks  The current set of blocks
     *
     * @return bool true if the block exists, false otherwise
     */
    public function hasBlock($name, array $context, array $blocks = [])
    {
        if (isset($blocks[$name])) {
            return $blocks[$name][0] instanceof self;
        }
        if (isset($this->blocks[$name])) {
            return \true;
        }
        if ($parent = $this->getParent($context)) {
            return $parent->hasBlock($name, $context);
        }
        return \false;
    }
    /**
     * Returns all block names in the current context of the template.
     *
     * This method checks blocks defined in the current template
     * or defined in "used" traits or defined in parent templates.
     *
     * @param array $context The context
     * @param array $blocks  The current set of blocks
     *
     * @return array An array of block names
     */
    public function getBlockNames(array $context, array $blocks = [])
    {
        $names = \array_merge(\array_keys($blocks), \array_keys($this->blocks));
        if ($parent = $this->getParent($context)) {
            $names = \array_merge($names, $parent->getBlockNames($context));
        }
        return \array_unique($names);
    }
    /**
     * @param string|TemplateWrapper|array<string|TemplateWrapper> $template
     *
     * @return self|TemplateWrapper
     */
    protected function loadTemplate($template, $templateName = null, $line = null, $index = null)
    {
        try {
            if (\is_array($template)) {
                return $this->env->resolveTemplate($template);
            }
            if ($template instanceof TemplateWrapper) {
                return $template;
            }
            if ($template instanceof self) {
                trigger_deprecation('twig/twig', '3.9', 'Passing a "%s" instance to "%s" is deprecated.', self::class, __METHOD__);
                return $template;
            }
            if ($template === $this->getTemplateName()) {
                $class = static::class;
                if (\false !== ($pos = \strrpos($class, '___', -1))) {
                    $class = \substr($class, 0, $pos);
                }
            } else {
                $class = $this->env->getTemplateClass($template);
            }
            return $this->env->loadTemplate($class, $template, $index);
        } catch (Error $e) {
            if (!$e->getSourceContext()) {
                $e->setSourceContext($templateName ? new Source('', $templateName) : $this->getSourceContext());
            }
            if ($e->getTemplateLine() > 0) {
                throw $e;
            }
            if (!$line) {
                $e->guess();
            } else {
                $e->setTemplateLine($line);
            }
            throw $e;
        }
    }
    /**
     * @internal
     *
     * @return self
     */
    public function unwrap()
    {
        return $this;
    }
    /**
     * Returns all blocks.
     *
     * This method is for internal use only and should never be called
     * directly.
     *
     * @return array An array of blocks
     */
    public function getBlocks()
    {
        return $this->blocks;
    }
    public function display(array $context, array $blocks = []) : void
    {
        foreach ($this->yield($context, $blocks) as $data) {
            echo $data;
        }
    }
    public function render(array $context) : string
    {
        $content = '';
        foreach ($this->yield($context) as $data) {
            $content .= $data;
        }
        return $content;
    }
    /**
     * @return iterable<string>
     */
    public function yield(array $context, array $blocks = []) : iterable
    {
        $context = $this->env->mergeGlobals($context);
        $blocks = \array_merge($this->blocks, $blocks);
        try {
            if ($this->useYield) {
                yield from $this->doDisplay($context, $blocks);
                return;
            }
            $level = \ob_get_level();
            \ob_start();
            foreach ($this->doDisplay($context, $blocks) as $data) {
                if (\ob_get_length()) {
                    $data = \ob_get_clean() . $data;
                    \ob_start();
                }
                (yield $data);
            }
            if (\ob_get_length()) {
                (yield \ob_get_clean());
            }
        } catch (Error $e) {
            if (!$e->getSourceContext()) {
                $e->setSourceContext($this->getSourceContext());
            }
            // this is mostly useful for \Twig\Error\LoaderError exceptions
            // see \Twig\Error\LoaderError
            if (-1 === $e->getTemplateLine()) {
                $e->guess();
            }
            throw $e;
        } catch (\Throwable $e) {
            $e = new RuntimeError(\sprintf('An exception has been thrown during the rendering of a template ("%s").', $e->getMessage()), -1, $this->getSourceContext(), $e);
            $e->guess();
            throw $e;
        } finally {
            if (!$this->useYield) {
                while (\ob_get_level() > $level) {
                    \ob_end_clean();
                }
            }
        }
    }
    /**
     * @return iterable<string>
     */
    public function yieldBlock($name, array $context, array $blocks = [], $useBlocks = \true, ?self $templateContext = null)
    {
        if ($useBlocks && isset($blocks[$name])) {
            $template = $blocks[$name][0];
            $block = $blocks[$name][1];
        } elseif (isset($this->blocks[$name])) {
            $template = $this->blocks[$name][0];
            $block = $this->blocks[$name][1];
        } else {
            $template = null;
            $block = null;
        }
        // avoid RCEs when sandbox is enabled
        if (null !== $template && !$template instanceof self) {
            throw new \LogicException('A block must be a method on a \\Twig\\Template instance.');
        }
        if (null !== $template) {
            try {
                if ($this->useYield) {
                    yield from $template->{$block}($context, $blocks);
                    return;
                }
                $level = \ob_get_level();
                \ob_start();
                foreach ($template->{$block}($context, $blocks) as $data) {
                    if (\ob_get_length()) {
                        $data = \ob_get_clean() . $data;
                        \ob_start();
                    }
                    (yield $data);
                }
                if (\ob_get_length()) {
                    (yield \ob_get_clean());
                }
            } catch (Error $e) {
                if (!$e->getSourceContext()) {
                    $e->setSourceContext($template->getSourceContext());
                }
                // this is mostly useful for \Twig\Error\LoaderError exceptions
                // see \Twig\Error\LoaderError
                if (-1 === $e->getTemplateLine()) {
                    $e->guess();
                }
                throw $e;
            } catch (\Throwable $e) {
                $e = new RuntimeError(\sprintf('An exception has been thrown during the rendering of a template ("%s").', $e->getMessage()), -1, $template->getSourceContext(), $e);
                $e->guess();
                throw $e;
            } finally {
                if (!$this->useYield) {
                    while (\ob_get_level() > $level) {
                        \ob_end_clean();
                    }
                }
            }
        } elseif ($parent = $this->getParent($context)) {
            yield from $parent->unwrap()->yieldBlock($name, $context, \array_merge($this->blocks, $blocks), \false, $templateContext ?? $this);
        } elseif (isset($blocks[$name])) {
            throw new RuntimeError(\sprintf('Block "%s" should not call parent() in "%s" as the block does not exist in the parent template "%s".', $name, $blocks[$name][0]->getTemplateName(), $this->getTemplateName()), -1, $blocks[$name][0]->getSourceContext());
        } else {
            throw new RuntimeError(\sprintf('Block "%s" on template "%s" does not exist.', $name, $this->getTemplateName()), -1, ($templateContext ?? $this)->getSourceContext());
        }
    }
    /**
     * Yields a parent block.
     *
     * This method is for internal use only and should never be called
     * directly.
     *
     * @param string $name    The block name to display from the parent
     * @param array  $context The context
     * @param array  $blocks  The current set of blocks
     *
     * @return iterable<string>
     */
    public function yieldParentBlock($name, array $context, array $blocks = [])
    {
        if (isset($this->traits[$name])) {
            yield from $this->traits[$name][0]->yieldBlock($name, $context, $blocks, \false);
        } elseif ($parent = $this->getParent($context)) {
            yield from $parent->unwrap()->yieldBlock($name, $context, $blocks, \false);
        } else {
            throw new RuntimeError(\sprintf('The template has no parent and no traits defining the "%s" block.', $name), -1, $this->getSourceContext());
        }
    }
    /**
     * Auto-generated method to display the template with the given context.
     *
     * @param array $context An array of parameters to pass to the template
     * @param array $blocks  An array of blocks to pass to the template
     */
    protected abstract function doDisplay(array $context, array $blocks = []);
}

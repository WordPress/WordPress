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
namespace ElementorDeps\Twig\Node;

use ElementorDeps\Twig\Attribute\YieldReady;
use ElementorDeps\Twig\Compiler;
use ElementorDeps\Twig\Source;
/**
 * Represents a node in the AST.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
#[YieldReady]
class Node implements \Countable, \IteratorAggregate
{
    protected $nodes;
    protected $attributes;
    protected $lineno;
    protected $tag;
    private $sourceContext;
    /** @var array<string, NameDeprecation> */
    private $nodeNameDeprecations = [];
    /** @var array<string, NameDeprecation> */
    private $attributeNameDeprecations = [];
    /**
     * @param array  $nodes      An array of named nodes
     * @param array  $attributes An array of attributes (should not be nodes)
     * @param int    $lineno     The line number
     * @param string $tag        The tag name associated with the Node
     */
    public function __construct(array $nodes = [], array $attributes = [], int $lineno = 0, ?string $tag = null)
    {
        foreach ($nodes as $name => $node) {
            if (!$node instanceof self) {
                throw new \InvalidArgumentException(\sprintf('Using "%s" for the value of node "%s" of "%s" is not supported. You must pass a \\Twig\\Node\\Node instance.', \is_object($node) ? \get_class($node) : (null === $node ? 'null' : \gettype($node)), $name, static::class));
            }
        }
        $this->nodes = $nodes;
        $this->attributes = $attributes;
        $this->lineno = $lineno;
        $this->tag = $tag;
    }
    public function __toString()
    {
        $attributes = [];
        foreach ($this->attributes as $name => $value) {
            $attributes[] = \sprintf('%s: %s', $name, \is_callable($value) ? '\\Closure' : \str_replace("\n", '', \var_export($value, \true)));
        }
        $repr = [static::class . '(' . \implode(', ', $attributes)];
        if (\count($this->nodes)) {
            foreach ($this->nodes as $name => $node) {
                $len = \strlen($name) + 4;
                $noderepr = [];
                foreach (\explode("\n", (string) $node) as $line) {
                    $noderepr[] = \str_repeat(' ', $len) . $line;
                }
                $repr[] = \sprintf('  %s: %s', $name, \ltrim(\implode("\n", $noderepr)));
            }
            $repr[] = ')';
        } else {
            $repr[0] .= ')';
        }
        return \implode("\n", $repr);
    }
    /**
     * @return void
     */
    public function compile(Compiler $compiler)
    {
        foreach ($this->nodes as $node) {
            $compiler->subcompile($node);
        }
    }
    public function getTemplateLine() : int
    {
        return $this->lineno;
    }
    public function getNodeTag() : ?string
    {
        return $this->tag;
    }
    public function hasAttribute(string $name) : bool
    {
        return \array_key_exists($name, $this->attributes);
    }
    public function getAttribute(string $name)
    {
        if (!\array_key_exists($name, $this->attributes)) {
            throw new \LogicException(\sprintf('Attribute "%s" does not exist for Node "%s".', $name, static::class));
        }
        $triggerDeprecation = \func_num_args() > 1 ? \func_get_arg(1) : \true;
        if ($triggerDeprecation && isset($this->attributeNameDeprecations[$name])) {
            $dep = $this->attributeNameDeprecations[$name];
            if ($dep->getNewName()) {
                trigger_deprecation($dep->getPackage(), $dep->getVersion(), 'Getting attribute "%s" on a "%s" class is deprecated, get the "%s" attribute instead.', $name, static::class, $dep->getNewName());
            } else {
                trigger_deprecation($dep->getPackage(), $dep->getVersion(), 'Getting attribute "%s" on a "%s" class is deprecated.', $name, static::class);
            }
        }
        return $this->attributes[$name];
    }
    public function setAttribute(string $name, $value) : void
    {
        $triggerDeprecation = \func_num_args() > 2 ? \func_get_arg(2) : \true;
        if ($triggerDeprecation && isset($this->attributeNameDeprecations[$name])) {
            $dep = $this->attributeNameDeprecations[$name];
            if ($dep->getNewName()) {
                trigger_deprecation($dep->getPackage(), $dep->getVersion(), 'Setting attribute "%s" on a "%s" class is deprecated, set the "%s" attribute instead.', $name, static::class, $dep->getNewName());
            } else {
                trigger_deprecation($dep->getPackage(), $dep->getVersion(), 'Setting attribute "%s" on a "%s" class is deprecated.', $name, static::class);
            }
        }
        $this->attributes[$name] = $value;
    }
    public function deprecateAttribute(string $name, NameDeprecation $dep) : void
    {
        $this->attributeNameDeprecations[$name] = $dep;
    }
    public function removeAttribute(string $name) : void
    {
        unset($this->attributes[$name]);
    }
    public function hasNode(string $name) : bool
    {
        return isset($this->nodes[$name]);
    }
    public function getNode(string $name) : self
    {
        if (!isset($this->nodes[$name])) {
            throw new \LogicException(\sprintf('Node "%s" does not exist for Node "%s".', $name, static::class));
        }
        $triggerDeprecation = \func_num_args() > 1 ? \func_get_arg(1) : \true;
        if ($triggerDeprecation && isset($this->nodeNameDeprecations[$name])) {
            $dep = $this->nodeNameDeprecations[$name];
            if ($dep->getNewName()) {
                trigger_deprecation($dep->getPackage(), $dep->getVersion(), 'Getting node "%s" on a "%s" class is deprecated, get the "%s" node instead.', $name, static::class, $dep->getNewName());
            } else {
                trigger_deprecation($dep->getPackage(), $dep->getVersion(), 'Getting node "%s" on a "%s" class is deprecated.', $name, static::class);
            }
        }
        return $this->nodes[$name];
    }
    public function setNode(string $name, self $node) : void
    {
        $triggerDeprecation = \func_num_args() > 2 ? \func_get_arg(2) : \true;
        if ($triggerDeprecation && isset($this->nodeNameDeprecations[$name])) {
            $dep = $this->nodeNameDeprecations[$name];
            if ($dep->getNewName()) {
                trigger_deprecation($dep->getPackage(), $dep->getVersion(), 'Setting node "%s" on a "%s" class is deprecated, set the "%s" node instead.', $name, static::class, $dep->getNewName());
            } else {
                trigger_deprecation($dep->getPackage(), $dep->getVersion(), 'Setting node "%s" on a "%s" class is deprecated.', $name, static::class);
            }
        }
        if (null !== $this->sourceContext) {
            $node->setSourceContext($this->sourceContext);
        }
        $this->nodes[$name] = $node;
    }
    public function removeNode(string $name) : void
    {
        unset($this->nodes[$name]);
    }
    public function deprecateNode(string $name, NameDeprecation $dep) : void
    {
        $this->nodeNameDeprecations[$name] = $dep;
    }
    /**
     * @return int
     */
    #[\ReturnTypeWillChange]
    public function count()
    {
        return \count($this->nodes);
    }
    public function getIterator() : \Traversable
    {
        return new \ArrayIterator($this->nodes);
    }
    public function getTemplateName() : ?string
    {
        return $this->sourceContext ? $this->sourceContext->getName() : null;
    }
    public function setSourceContext(Source $source) : void
    {
        $this->sourceContext = $source;
        foreach ($this->nodes as $node) {
            $node->setSourceContext($source);
        }
    }
    public function getSourceContext() : ?Source
    {
        return $this->sourceContext;
    }
}

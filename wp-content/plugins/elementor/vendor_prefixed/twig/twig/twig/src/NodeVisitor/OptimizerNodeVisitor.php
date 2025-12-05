<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ElementorDeps\Twig\NodeVisitor;

use ElementorDeps\Twig\Environment;
use ElementorDeps\Twig\Node\BlockReferenceNode;
use ElementorDeps\Twig\Node\Expression\BlockReferenceExpression;
use ElementorDeps\Twig\Node\Expression\ConstantExpression;
use ElementorDeps\Twig\Node\Expression\FunctionExpression;
use ElementorDeps\Twig\Node\Expression\GetAttrExpression;
use ElementorDeps\Twig\Node\Expression\NameExpression;
use ElementorDeps\Twig\Node\Expression\ParentExpression;
use ElementorDeps\Twig\Node\ForNode;
use ElementorDeps\Twig\Node\IncludeNode;
use ElementorDeps\Twig\Node\Node;
use ElementorDeps\Twig\Node\PrintNode;
use ElementorDeps\Twig\Node\TextNode;
/**
 * Tries to optimize the AST.
 *
 * This visitor is always the last registered one.
 *
 * You can configure which optimizations you want to activate via the
 * optimizer mode.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @internal
 */
final class OptimizerNodeVisitor implements NodeVisitorInterface
{
    public const OPTIMIZE_ALL = -1;
    public const OPTIMIZE_NONE = 0;
    public const OPTIMIZE_FOR = 2;
    public const OPTIMIZE_RAW_FILTER = 4;
    public const OPTIMIZE_TEXT_NODES = 8;
    private $loops = [];
    private $loopsTargets = [];
    private $optimizers;
    /**
     * @param int $optimizers The optimizer mode
     */
    public function __construct(int $optimizers = -1)
    {
        if ($optimizers > (self::OPTIMIZE_FOR | self::OPTIMIZE_RAW_FILTER | self::OPTIMIZE_TEXT_NODES)) {
            throw new \InvalidArgumentException(\sprintf('Optimizer mode "%s" is not valid.', $optimizers));
        }
        if (-1 !== $optimizers && self::OPTIMIZE_RAW_FILTER === (self::OPTIMIZE_RAW_FILTER & $optimizers)) {
            trigger_deprecation('twig/twig', '3.11', 'The "Twig\\NodeVisitor\\OptimizerNodeVisitor::OPTIMIZE_RAW_FILTER" option is deprecated and does nothing.');
        }
        $this->optimizers = $optimizers;
    }
    public function enterNode(Node $node, Environment $env) : Node
    {
        if (self::OPTIMIZE_FOR === (self::OPTIMIZE_FOR & $this->optimizers)) {
            $this->enterOptimizeFor($node);
        }
        return $node;
    }
    public function leaveNode(Node $node, Environment $env) : ?Node
    {
        if (self::OPTIMIZE_FOR === (self::OPTIMIZE_FOR & $this->optimizers)) {
            $this->leaveOptimizeFor($node);
        }
        $node = $this->optimizePrintNode($node);
        if (self::OPTIMIZE_TEXT_NODES === (self::OPTIMIZE_TEXT_NODES & $this->optimizers)) {
            $node = $this->mergeTextNodeCalls($node);
        }
        return $node;
    }
    private function mergeTextNodeCalls(Node $node) : Node
    {
        $text = '';
        $names = [];
        foreach ($node as $k => $n) {
            if (!$n instanceof TextNode) {
                return $node;
            }
            $text .= $n->getAttribute('data');
            $names[] = $k;
        }
        if (!$text) {
            return $node;
        }
        if (Node::class === \get_class($node)) {
            return new TextNode($text, $node->getTemplateLine());
        }
        foreach ($names as $i => $name) {
            if (0 === $i) {
                $node->setNode($name, new TextNode($text, $node->getTemplateLine()));
            } else {
                $node->removeNode($name);
            }
        }
        return $node;
    }
    /**
     * Optimizes print nodes.
     *
     * It replaces:
     *
     *   * "echo $this->render(Parent)Block()" with "$this->display(Parent)Block()"
     */
    private function optimizePrintNode(Node $node) : Node
    {
        if (!$node instanceof PrintNode) {
            return $node;
        }
        $exprNode = $node->getNode('expr');
        if ($exprNode instanceof ConstantExpression && \is_string($exprNode->getAttribute('value'))) {
            return new TextNode($exprNode->getAttribute('value'), $exprNode->getTemplateLine());
        }
        if ($exprNode instanceof BlockReferenceExpression || $exprNode instanceof ParentExpression) {
            $exprNode->setAttribute('output', \true);
            return $exprNode;
        }
        return $node;
    }
    /**
     * Optimizes "for" tag by removing the "loop" variable creation whenever possible.
     */
    private function enterOptimizeFor(Node $node) : void
    {
        if ($node instanceof ForNode) {
            // disable the loop variable by default
            $node->setAttribute('with_loop', \false);
            \array_unshift($this->loops, $node);
            \array_unshift($this->loopsTargets, $node->getNode('value_target')->getAttribute('name'));
            \array_unshift($this->loopsTargets, $node->getNode('key_target')->getAttribute('name'));
        } elseif (!$this->loops) {
            // we are outside a loop
            return;
        } elseif ($node instanceof NameExpression && 'loop' === $node->getAttribute('name')) {
            $node->setAttribute('always_defined', \true);
            $this->addLoopToCurrent();
        } elseif ($node instanceof NameExpression && \in_array($node->getAttribute('name'), $this->loopsTargets)) {
            $node->setAttribute('always_defined', \true);
        } elseif ($node instanceof BlockReferenceNode || $node instanceof BlockReferenceExpression) {
            $this->addLoopToCurrent();
        } elseif ($node instanceof IncludeNode && !$node->getAttribute('only')) {
            $this->addLoopToAll();
        } elseif ($node instanceof FunctionExpression && 'include' === $node->getAttribute('name') && (!$node->getNode('arguments')->hasNode('with_context') || \false !== $node->getNode('arguments')->getNode('with_context')->getAttribute('value'))) {
            $this->addLoopToAll();
        } elseif ($node instanceof GetAttrExpression && (!$node->getNode('attribute') instanceof ConstantExpression || 'parent' === $node->getNode('attribute')->getAttribute('value')) && (\true === $this->loops[0]->getAttribute('with_loop') || $node->getNode('node') instanceof NameExpression && 'loop' === $node->getNode('node')->getAttribute('name'))) {
            $this->addLoopToAll();
        }
    }
    /**
     * Optimizes "for" tag by removing the "loop" variable creation whenever possible.
     */
    private function leaveOptimizeFor(Node $node) : void
    {
        if ($node instanceof ForNode) {
            \array_shift($this->loops);
            \array_shift($this->loopsTargets);
            \array_shift($this->loopsTargets);
        }
    }
    private function addLoopToCurrent() : void
    {
        $this->loops[0]->setAttribute('with_loop', \true);
    }
    private function addLoopToAll() : void
    {
        foreach ($this->loops as $loop) {
            $loop->setAttribute('with_loop', \true);
        }
    }
    public function getPriority() : int
    {
        return 255;
    }
}

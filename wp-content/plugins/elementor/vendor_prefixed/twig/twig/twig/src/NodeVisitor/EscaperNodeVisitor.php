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
use ElementorDeps\Twig\Extension\EscaperExtension;
use ElementorDeps\Twig\Node\AutoEscapeNode;
use ElementorDeps\Twig\Node\BlockNode;
use ElementorDeps\Twig\Node\BlockReferenceNode;
use ElementorDeps\Twig\Node\DoNode;
use ElementorDeps\Twig\Node\Expression\ConditionalExpression;
use ElementorDeps\Twig\Node\Expression\ConstantExpression;
use ElementorDeps\Twig\Node\Expression\FilterExpression;
use ElementorDeps\Twig\Node\Expression\InlinePrint;
use ElementorDeps\Twig\Node\ImportNode;
use ElementorDeps\Twig\Node\ModuleNode;
use ElementorDeps\Twig\Node\Node;
use ElementorDeps\Twig\Node\PrintNode;
use ElementorDeps\Twig\NodeTraverser;
/**
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @internal
 */
final class EscaperNodeVisitor implements NodeVisitorInterface
{
    private $statusStack = [];
    private $blocks = [];
    private $safeAnalysis;
    private $traverser;
    private $defaultStrategy = \false;
    private $safeVars = [];
    public function __construct()
    {
        $this->safeAnalysis = new SafeAnalysisNodeVisitor();
    }
    public function enterNode(Node $node, Environment $env) : Node
    {
        if ($node instanceof ModuleNode) {
            if ($env->hasExtension(EscaperExtension::class) && ($defaultStrategy = $env->getExtension(EscaperExtension::class)->getDefaultStrategy($node->getTemplateName()))) {
                $this->defaultStrategy = $defaultStrategy;
            }
            $this->safeVars = [];
            $this->blocks = [];
        } elseif ($node instanceof AutoEscapeNode) {
            $this->statusStack[] = $node->getAttribute('value');
        } elseif ($node instanceof BlockNode) {
            $this->statusStack[] = $this->blocks[$node->getAttribute('name')] ?? $this->needEscaping();
        } elseif ($node instanceof ImportNode) {
            $this->safeVars[] = $node->getNode('var')->getAttribute('name');
        }
        return $node;
    }
    public function leaveNode(Node $node, Environment $env) : ?Node
    {
        if ($node instanceof ModuleNode) {
            $this->defaultStrategy = \false;
            $this->safeVars = [];
            $this->blocks = [];
        } elseif ($node instanceof FilterExpression) {
            return $this->preEscapeFilterNode($node, $env);
        } elseif ($node instanceof PrintNode && \false !== ($type = $this->needEscaping())) {
            $expression = $node->getNode('expr');
            if ($expression instanceof ConditionalExpression && $this->shouldUnwrapConditional($expression, $env, $type)) {
                return new DoNode($this->unwrapConditional($expression, $env, $type), $expression->getTemplateLine());
            }
            return $this->escapePrintNode($node, $env, $type);
        }
        if ($node instanceof AutoEscapeNode || $node instanceof BlockNode) {
            \array_pop($this->statusStack);
        } elseif ($node instanceof BlockReferenceNode) {
            $this->blocks[$node->getAttribute('name')] = $this->needEscaping();
        }
        return $node;
    }
    private function shouldUnwrapConditional(ConditionalExpression $expression, Environment $env, string $type) : bool
    {
        $expr2Safe = $this->isSafeFor($type, $expression->getNode('expr2'), $env);
        $expr3Safe = $this->isSafeFor($type, $expression->getNode('expr3'), $env);
        return $expr2Safe !== $expr3Safe;
    }
    private function unwrapConditional(ConditionalExpression $expression, Environment $env, string $type) : ConditionalExpression
    {
        // convert "echo a ? b : c" to "a ? echo b : echo c" recursively
        $expr2 = $expression->getNode('expr2');
        if ($expr2 instanceof ConditionalExpression && $this->shouldUnwrapConditional($expr2, $env, $type)) {
            $expr2 = $this->unwrapConditional($expr2, $env, $type);
        } else {
            $expr2 = $this->escapeInlinePrintNode(new InlinePrint($expr2, $expr2->getTemplateLine()), $env, $type);
        }
        $expr3 = $expression->getNode('expr3');
        if ($expr3 instanceof ConditionalExpression && $this->shouldUnwrapConditional($expr3, $env, $type)) {
            $expr3 = $this->unwrapConditional($expr3, $env, $type);
        } else {
            $expr3 = $this->escapeInlinePrintNode(new InlinePrint($expr3, $expr3->getTemplateLine()), $env, $type);
        }
        return new ConditionalExpression($expression->getNode('expr1'), $expr2, $expr3, $expression->getTemplateLine());
    }
    private function escapeInlinePrintNode(InlinePrint $node, Environment $env, string $type) : Node
    {
        $expression = $node->getNode('node');
        if ($this->isSafeFor($type, $expression, $env)) {
            return $node;
        }
        return new InlinePrint($this->getEscaperFilter($type, $expression), $node->getTemplateLine());
    }
    private function escapePrintNode(PrintNode $node, Environment $env, string $type) : Node
    {
        $expression = $node->getNode('expr');
        if ($this->isSafeFor($type, $expression, $env)) {
            return $node;
        }
        $class = \get_class($node);
        return new $class($this->getEscaperFilter($type, $expression), $node->getTemplateLine());
    }
    private function preEscapeFilterNode(FilterExpression $filter, Environment $env) : FilterExpression
    {
        $name = $filter->getNode('filter')->getAttribute('value');
        $type = $env->getFilter($name)->getPreEscape();
        if (null === $type) {
            return $filter;
        }
        $node = $filter->getNode('node');
        if ($this->isSafeFor($type, $node, $env)) {
            return $filter;
        }
        $filter->setNode('node', $this->getEscaperFilter($type, $node));
        return $filter;
    }
    private function isSafeFor(string $type, Node $expression, Environment $env) : bool
    {
        $safe = $this->safeAnalysis->getSafe($expression);
        if (null === $safe) {
            if (null === $this->traverser) {
                $this->traverser = new NodeTraverser($env, [$this->safeAnalysis]);
            }
            $this->safeAnalysis->setSafeVars($this->safeVars);
            $this->traverser->traverse($expression);
            $safe = $this->safeAnalysis->getSafe($expression);
        }
        return \in_array($type, $safe) || \in_array('all', $safe);
    }
    private function needEscaping()
    {
        if (\count($this->statusStack)) {
            return $this->statusStack[\count($this->statusStack) - 1];
        }
        return $this->defaultStrategy ?: \false;
    }
    private function getEscaperFilter(string $type, Node $node) : FilterExpression
    {
        $line = $node->getTemplateLine();
        $name = new ConstantExpression('escape', $line);
        $args = new Node([new ConstantExpression($type, $line), new ConstantExpression(null, $line), new ConstantExpression(\true, $line)]);
        return new FilterExpression($node, $name, $args, $line);
    }
    public function getPriority() : int
    {
        return 0;
    }
}

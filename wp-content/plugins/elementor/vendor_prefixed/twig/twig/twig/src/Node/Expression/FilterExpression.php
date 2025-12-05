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
namespace ElementorDeps\Twig\Node\Expression;

use ElementorDeps\Twig\Compiler;
use ElementorDeps\Twig\Node\Node;
class FilterExpression extends CallExpression
{
    public function __construct(Node $node, ConstantExpression $filterName, Node $arguments, int $lineno, ?string $tag = null)
    {
        parent::__construct(['node' => $node, 'filter' => $filterName, 'arguments' => $arguments], ['name' => $filterName->getAttribute('value'), 'type' => 'filter'], $lineno, $tag);
    }
    public function compile(Compiler $compiler) : void
    {
        $name = $this->getNode('filter')->getAttribute('value');
        if ($name !== $this->getAttribute('name')) {
            trigger_deprecation('twig/twig', '3.11', 'Changing the value of a "filter" node in a NodeVisitor class is not supported anymore.');
            $this->setAttribute('name', $name);
        }
        if ('raw' === $name) {
            trigger_deprecation('twig/twig', '3.11', 'Creating the "raw" filter via "FilterExpression" is deprecated; use "RawFilter" instead.');
            $compiler->subcompile($this->getNode('node'));
            return;
        }
        $filter = $compiler->getEnvironment()->getFilter($name);
        $this->setAttribute('needs_charset', $filter->needsCharset());
        $this->setAttribute('needs_environment', $filter->needsEnvironment());
        $this->setAttribute('needs_context', $filter->needsContext());
        $this->setAttribute('arguments', $filter->getArguments());
        $this->setAttribute('callable', $filter->getCallable());
        $this->setAttribute('is_variadic', $filter->isVariadic());
        $this->compileCallable($compiler);
    }
}

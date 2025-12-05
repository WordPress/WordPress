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
use ElementorDeps\Twig\Node\Expression\AbstractExpression;
/**
 * Represents a node that outputs an expression.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
#[YieldReady]
class PrintNode extends Node implements NodeOutputInterface
{
    public function __construct(AbstractExpression $expr, int $lineno, ?string $tag = null)
    {
        parent::__construct(['expr' => $expr], [], $lineno, $tag);
    }
    public function compile(Compiler $compiler) : void
    {
        /** @var AbstractExpression */
        $expr = $this->getNode('expr');
        $compiler->addDebugInfo($this)->write($expr->isGenerator() ? 'yield from ' : 'yield ')->subcompile($expr)->raw(";\n");
    }
}

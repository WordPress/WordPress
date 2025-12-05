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
class ConditionalExpression extends AbstractExpression
{
    public function __construct(AbstractExpression $expr1, AbstractExpression $expr2, AbstractExpression $expr3, int $lineno)
    {
        parent::__construct(['expr1' => $expr1, 'expr2' => $expr2, 'expr3' => $expr3], [], $lineno);
    }
    public function compile(Compiler $compiler) : void
    {
        // Ternary with no then uses Elvis operator
        if ($this->getNode('expr1') === $this->getNode('expr2')) {
            $compiler->raw('((')->subcompile($this->getNode('expr1'))->raw(') ?: (')->subcompile($this->getNode('expr3'))->raw('))');
        } else {
            $compiler->raw('((')->subcompile($this->getNode('expr1'))->raw(') ? (')->subcompile($this->getNode('expr2'))->raw(') : (')->subcompile($this->getNode('expr3'))->raw('))');
        }
    }
}

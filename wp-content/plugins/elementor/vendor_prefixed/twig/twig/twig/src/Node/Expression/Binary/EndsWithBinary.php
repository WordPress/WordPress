<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ElementorDeps\Twig\Node\Expression\Binary;

use ElementorDeps\Twig\Compiler;
class EndsWithBinary extends AbstractBinary
{
    public function compile(Compiler $compiler) : void
    {
        $left = $compiler->getVarName();
        $right = $compiler->getVarName();
        $compiler->raw(\sprintf('(is_string($%s = ', $left))->subcompile($this->getNode('left'))->raw(\sprintf(') && is_string($%s = ', $right))->subcompile($this->getNode('right'))->raw(\sprintf(') && str_ends_with($%1$s, $%2$s))', $left, $right));
    }
    public function operator(Compiler $compiler) : Compiler
    {
        return $compiler->raw('');
    }
}

<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ElementorDeps\Twig\Node\Expression\Test;

use ElementorDeps\Twig\Compiler;
use ElementorDeps\Twig\Node\Expression\TestExpression;
/**
 * Checks if a variable is divisible by a number.
 *
 *  {% if loop.index is divisible by(3) %}
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class DivisiblebyTest extends TestExpression
{
    public function compile(Compiler $compiler) : void
    {
        $compiler->raw('(0 == ')->subcompile($this->getNode('node'))->raw(' % ')->subcompile($this->getNode('arguments')->getNode('0'))->raw(')');
    }
}

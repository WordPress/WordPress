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
namespace Matomo\Dependencies\Twig\Node\Expression;

use Matomo\Dependencies\Twig\Compiler;
class AssignNameExpression extends NameExpression
{
    public function compile(Compiler $compiler) : void
    {
        $compiler->raw('$context[')->string($this->getAttribute('name'))->raw(']');
    }
}

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
namespace Matomo\Dependencies\Twig\Node;

use Matomo\Dependencies\Twig\Attribute\YieldReady;
use Matomo\Dependencies\Twig\Compiler;
/**
 * Represents a text node.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
#[YieldReady]
class TextNode extends Node implements NodeOutputInterface
{
    public function __construct(string $data, int $lineno)
    {
        parent::__construct([], ['data' => $data], $lineno);
    }
    public function compile(Compiler $compiler) : void
    {
        $compiler->addDebugInfo($this);
        $compiler->write('yield ')->string($this->getAttribute('data'))->raw(";\n");
    }
}

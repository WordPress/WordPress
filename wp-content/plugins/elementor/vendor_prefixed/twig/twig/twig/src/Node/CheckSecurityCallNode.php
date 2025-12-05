<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ElementorDeps\Twig\Node;

use ElementorDeps\Twig\Attribute\YieldReady;
use ElementorDeps\Twig\Compiler;
/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
#[YieldReady]
class CheckSecurityCallNode extends Node
{
    public function compile(Compiler $compiler)
    {
        $compiler->write("\$this->sandbox = \$this->extensions[SandboxExtension::class];\n")->write("\$this->checkSecurity();\n");
    }
}

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
use ElementorDeps\Twig\Node\Node;
/**
 * Used to make node visitors compatible with Twig 1.x and 2.x.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @deprecated since 3.9 (to be removed in 4.0)
 */
abstract class AbstractNodeVisitor implements NodeVisitorInterface
{
    public final function enterNode(Node $node, Environment $env) : Node
    {
        return $this->doEnterNode($node, $env);
    }
    public final function leaveNode(Node $node, Environment $env) : ?Node
    {
        return $this->doLeaveNode($node, $env);
    }
    /**
     * Called before child nodes are visited.
     *
     * @return Node The modified node
     */
    protected abstract function doEnterNode(Node $node, Environment $env);
    /**
     * Called after child nodes are visited.
     *
     * @return Node|null The modified node or null if the node must be removed
     */
    protected abstract function doLeaveNode(Node $node, Environment $env);
}

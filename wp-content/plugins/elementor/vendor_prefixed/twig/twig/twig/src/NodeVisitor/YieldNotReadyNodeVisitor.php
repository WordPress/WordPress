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

use ElementorDeps\Twig\Attribute\YieldReady;
use ElementorDeps\Twig\Environment;
use ElementorDeps\Twig\Node\Expression\AbstractExpression;
use ElementorDeps\Twig\Node\Node;
/**
 * @internal to be removed in Twig 4
 */
final class YieldNotReadyNodeVisitor implements NodeVisitorInterface
{
    private $useYield;
    private $yieldReadyNodes = [];
    public function __construct(bool $useYield)
    {
        $this->useYield = $useYield;
    }
    public function enterNode(Node $node, Environment $env) : Node
    {
        $class = \get_class($node);
        if ($node instanceof AbstractExpression || isset($this->yieldReadyNodes[$class])) {
            return $node;
        }
        if (!($this->yieldReadyNodes[$class] = (bool) (new \ReflectionClass($class))->getAttributes(YieldReady::class))) {
            if ($this->useYield) {
                throw new \LogicException(\sprintf('You cannot enable the "use_yield" option of Twig as node "%s" is not marked as ready for it; please make it ready and then flag it with the #[YieldReady] attribute.', $class));
            }
            trigger_deprecation('twig/twig', '3.9', 'Twig node "%s" is not marked as ready for using "yield" instead of "echo"; please make it ready and then flag it with the #[YieldReady] attribute.', $class);
        }
        return $node;
    }
    public function leaveNode(Node $node, Environment $env) : ?Node
    {
        return $node;
    }
    public function getPriority() : int
    {
        return 255;
    }
}

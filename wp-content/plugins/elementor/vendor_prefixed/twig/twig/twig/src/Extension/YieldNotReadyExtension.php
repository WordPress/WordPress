<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ElementorDeps\Twig\Extension;

use ElementorDeps\Twig\NodeVisitor\YieldNotReadyNodeVisitor;
/**
 * @internal to be removed in Twig 4
 */
final class YieldNotReadyExtension extends AbstractExtension
{
    private $useYield;
    public function __construct(bool $useYield)
    {
        $this->useYield = $useYield;
    }
    public function getNodeVisitors() : array
    {
        return [new YieldNotReadyNodeVisitor($this->useYield)];
    }
}

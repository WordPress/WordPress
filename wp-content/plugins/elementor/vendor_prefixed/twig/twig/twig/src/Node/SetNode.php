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
use ElementorDeps\Twig\Node\Expression\ConstantExpression;
/**
 * Represents a set node.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
#[YieldReady]
class SetNode extends Node implements NodeCaptureInterface
{
    public function __construct(bool $capture, Node $names, Node $values, int $lineno, ?string $tag = null)
    {
        /*
         * Optimizes the node when capture is used for a large block of text.
         *
         * {% set foo %}foo{% endset %} is compiled to $context['foo'] = new Twig\Markup("foo");
         */
        $safe = \false;
        if ($capture) {
            $safe = \true;
            if ($values instanceof TextNode) {
                $values = new ConstantExpression($values->getAttribute('data'), $values->getTemplateLine());
                $capture = \false;
            } else {
                $values = new CaptureNode($values, $values->getTemplateLine());
            }
        }
        parent::__construct(['names' => $names, 'values' => $values], ['capture' => $capture, 'safe' => $safe], $lineno, $tag);
    }
    public function compile(Compiler $compiler) : void
    {
        $compiler->addDebugInfo($this);
        if (\count($this->getNode('names')) > 1) {
            $compiler->write('[');
            foreach ($this->getNode('names') as $idx => $node) {
                if ($idx) {
                    $compiler->raw(', ');
                }
                $compiler->subcompile($node);
            }
            $compiler->raw(']');
        } else {
            $compiler->subcompile($this->getNode('names'), \false);
        }
        $compiler->raw(' = ');
        if ($this->getAttribute('capture')) {
            $compiler->subcompile($this->getNode('values'));
        } else {
            if (\count($this->getNode('names')) > 1) {
                $compiler->write('[');
                foreach ($this->getNode('values') as $idx => $value) {
                    if ($idx) {
                        $compiler->raw(', ');
                    }
                    $compiler->subcompile($value);
                }
                $compiler->raw(']');
            } else {
                if ($this->getAttribute('safe')) {
                    $compiler->raw("('' === \$tmp = ")->subcompile($this->getNode('values'))->raw(") ? '' : new Markup(\$tmp, \$this->env->getCharset())");
                } else {
                    $compiler->subcompile($this->getNode('values'));
                }
            }
            $compiler->raw(';');
        }
        $compiler->raw("\n");
    }
}

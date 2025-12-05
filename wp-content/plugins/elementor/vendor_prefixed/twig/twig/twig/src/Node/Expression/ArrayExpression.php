<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ElementorDeps\Twig\Node\Expression;

use ElementorDeps\Twig\Compiler;
class ArrayExpression extends AbstractExpression
{
    private $index;
    public function __construct(array $elements, int $lineno)
    {
        parent::__construct($elements, [], $lineno);
        $this->index = -1;
        foreach ($this->getKeyValuePairs() as $pair) {
            if ($pair['key'] instanceof ConstantExpression && \ctype_digit((string) $pair['key']->getAttribute('value')) && $pair['key']->getAttribute('value') > $this->index) {
                $this->index = $pair['key']->getAttribute('value');
            }
        }
    }
    public function getKeyValuePairs() : array
    {
        $pairs = [];
        foreach (\array_chunk($this->nodes, 2) as $pair) {
            $pairs[] = ['key' => $pair[0], 'value' => $pair[1]];
        }
        return $pairs;
    }
    public function hasElement(AbstractExpression $key) : bool
    {
        foreach ($this->getKeyValuePairs() as $pair) {
            // we compare the string representation of the keys
            // to avoid comparing the line numbers which are not relevant here.
            if ((string) $key === (string) $pair['key']) {
                return \true;
            }
        }
        return \false;
    }
    public function addElement(AbstractExpression $value, ?AbstractExpression $key = null) : void
    {
        if (null === $key) {
            $key = new ConstantExpression(++$this->index, $value->getTemplateLine());
        }
        \array_push($this->nodes, $key, $value);
    }
    public function compile(Compiler $compiler) : void
    {
        $keyValuePairs = $this->getKeyValuePairs();
        $needsArrayMergeSpread = \PHP_VERSION_ID < 80100 && $this->hasSpreadItem($keyValuePairs);
        if ($needsArrayMergeSpread) {
            $compiler->raw('CoreExtension::merge(');
        }
        $compiler->raw('[');
        $first = \true;
        $reopenAfterMergeSpread = \false;
        $nextIndex = 0;
        foreach ($keyValuePairs as $pair) {
            if ($reopenAfterMergeSpread) {
                $compiler->raw(', [');
                $reopenAfterMergeSpread = \false;
            }
            if ($needsArrayMergeSpread && $pair['value']->hasAttribute('spread')) {
                $compiler->raw('], ')->subcompile($pair['value']);
                $first = \true;
                $reopenAfterMergeSpread = \true;
                continue;
            }
            if (!$first) {
                $compiler->raw(', ');
            }
            $first = \false;
            if ($pair['value']->hasAttribute('spread') && !$needsArrayMergeSpread) {
                $compiler->raw('...')->subcompile($pair['value']);
                ++$nextIndex;
            } else {
                $key = $pair['key'] instanceof ConstantExpression ? $pair['key']->getAttribute('value') : null;
                if ($nextIndex !== $key) {
                    if (\is_int($key)) {
                        $nextIndex = $key + 1;
                    }
                    $compiler->subcompile($pair['key'])->raw(' => ');
                } else {
                    ++$nextIndex;
                }
                $compiler->subcompile($pair['value']);
            }
        }
        if (!$reopenAfterMergeSpread) {
            $compiler->raw(']');
        }
        if ($needsArrayMergeSpread) {
            $compiler->raw(')');
        }
    }
    private function hasSpreadItem(array $pairs) : bool
    {
        foreach ($pairs as $pair) {
            if ($pair['value']->hasAttribute('spread')) {
                return \true;
            }
        }
        return \false;
    }
}

<?php

declare(strict_types=1);

namespace Pelago\Emogrifier\Css;

use Sabberworm\CSS\Property\Selector;
use Sabberworm\CSS\RuleSet\DeclarationBlock;

/**
 * This class represents a CSS style rule, including selectors, a declaration block, and an optional containing at-rule.
 *
 * @internal
 */
class StyleRule
{
    /**
     * @var DeclarationBlock
     */
    private $declarationBlock;

    /**
     * @var string
     */
    private $containingAtRule;

    /**
     * @param DeclarationBlock $declarationBlock
     * @param string $containingAtRule e.g. `@media screen and (max-width: 480px)`
     */
    public function __construct(DeclarationBlock $declarationBlock, string $containingAtRule = '')
    {
        $this->declarationBlock = $declarationBlock;
        $this->containingAtRule = \trim($containingAtRule);
    }

    /**
     * @return array<int, string> the selectors, e.g. `["h1", "p"]`
     */
    public function getSelectors(): array
    {
        /** @var array<int, Selector> $selectors */
        $selectors = $this->declarationBlock->getSelectors();
        return \array_map(
            static function (Selector $selector): string {
                return (string)$selector;
            },
            $selectors
        );
    }

    /**
     * @return string the CSS declarations, separated and followed by a semicolon, e.g., `color: red; height: 4px;`
     */
    public function getDeclarationAsText(): string
    {
        return \implode(' ', $this->declarationBlock->getRules());
    }

    /**
     * Checks whether the declaration block has at least one declaration.
     */
    public function hasAtLeastOneDeclaration(): bool
    {
        return $this->declarationBlock->getRules() !== [];
    }

    /**
     * @returns string e.g. `@media screen and (max-width: 480px)`, or an empty string
     */
    public function getContainingAtRule(): string
    {
        return $this->containingAtRule;
    }

    /**
     * Checks whether the containing at-rule is non-empty and has any non-whitespace characters.
     */
    public function hasContainingAtRule(): bool
    {
        return $this->getContainingAtRule() !== '';
    }
}

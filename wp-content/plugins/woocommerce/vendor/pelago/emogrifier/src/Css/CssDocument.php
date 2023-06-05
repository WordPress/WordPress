<?php

declare(strict_types=1);

namespace Pelago\Emogrifier\Css;

use Sabberworm\CSS\CSSList\AtRuleBlockList as CssAtRuleBlockList;
use Sabberworm\CSS\CSSList\Document as SabberwormCssDocument;
use Sabberworm\CSS\Parser as CssParser;
use Sabberworm\CSS\Property\AtRule as CssAtRule;
use Sabberworm\CSS\Property\Charset as CssCharset;
use Sabberworm\CSS\Property\Import as CssImport;
use Sabberworm\CSS\Renderable as CssRenderable;
use Sabberworm\CSS\RuleSet\DeclarationBlock as CssDeclarationBlock;
use Sabberworm\CSS\RuleSet\RuleSet as CssRuleSet;

/**
 * Parses and stores a CSS document from a string of CSS, and provides methods to obtain the CSS in parts or as data
 * structures.
 *
 * @internal
 */
class CssDocument
{
    /**
     * @var SabberwormCssDocument
     */
    private $sabberwormCssDocument;

    /**
     * `@import` rules must precede all other types of rules, except `@charset` rules.  This property is used while
     * rendering at-rules to enforce that.
     *
     * @var bool
     */
    private $isImportRuleAllowed = true;

    /**
     * @param string $css
     */
    public function __construct(string $css)
    {
        $cssParser = new CssParser($css);
        /** @var SabberwormCssDocument $sabberwormCssDocument */
        $sabberwormCssDocument = $cssParser->parse();
        $this->sabberwormCssDocument = $sabberwormCssDocument;
    }

    /**
     * Collates the media query, selectors and declarations for individual rules from the parsed CSS, in order.
     *
     * @param array<array-key, string> $allowedMediaTypes
     *
     * @return array<int, StyleRule>
     */
    public function getStyleRulesData(array $allowedMediaTypes): array
    {
        $ruleMatches = [];
        /** @var CssRenderable $rule */
        foreach ($this->sabberwormCssDocument->getContents() as $rule) {
            if ($rule instanceof CssAtRuleBlockList) {
                $containingAtRule = $this->getFilteredAtIdentifierAndRule($rule, $allowedMediaTypes);
                if (\is_string($containingAtRule)) {
                    /** @var CssRenderable $nestedRule */
                    foreach ($rule->getContents() as $nestedRule) {
                        if ($nestedRule instanceof CssDeclarationBlock) {
                            $ruleMatches[] = new StyleRule($nestedRule, $containingAtRule);
                        }
                    }
                }
            } elseif ($rule instanceof CssDeclarationBlock) {
                $ruleMatches[] = new StyleRule($rule);
            }
        }

        return $ruleMatches;
    }

    /**
     * Renders at-rules from the parsed CSS that are valid and not conditional group rules (i.e. not rules such as
     * `@media` which contain style rules whose data is returned by {@see getStyleRulesData}).  Also does not render
     * `@charset` rules; these are discarded (only UTF-8 is supported).
     *
     * @return string
     */
    public function renderNonConditionalAtRules(): string
    {
        $this->isImportRuleAllowed = true;
        /** @var array<int, CssRenderable> $cssContents */
        $cssContents = $this->sabberwormCssDocument->getContents();
        $atRules = \array_filter($cssContents, [$this, 'isValidAtRuleToRender']);

        if ($atRules === []) {
            return '';
        }

        $atRulesDocument = new SabberwormCssDocument();
        $atRulesDocument->setContents($atRules);

        /** @var string $renderedRules */
        $renderedRules = $atRulesDocument->render();
        return $renderedRules;
    }

    /**
     * @param CssAtRuleBlockList $rule
     * @param array<array-key, string> $allowedMediaTypes
     *
     * @return ?string
     *         If the nested at-rule is supported, it's opening declaration (e.g. "@media (max-width: 768px)") is
     *         returned; otherwise the return value is null.
     */
    private function getFilteredAtIdentifierAndRule(CssAtRuleBlockList $rule, array $allowedMediaTypes): ?string
    {
        $result = null;

        if ($rule->atRuleName() === 'media') {
            /** @var string $mediaQueryList */
            $mediaQueryList = $rule->atRuleArgs();
            [$mediaType] = \explode('(', $mediaQueryList, 2);
            if (\trim($mediaType) !== '') {
                $escapedAllowedMediaTypes = \array_map(
                    static function (string $allowedMediaType): string {
                        return \preg_quote($allowedMediaType, '/');
                    },
                    $allowedMediaTypes
                );
                $mediaTypesMatcher = \implode('|', $escapedAllowedMediaTypes);
                $isAllowed = \preg_match('/^\\s*+(?:only\\s++)?+(?:' . $mediaTypesMatcher . ')/i', $mediaType) > 0;
            } else {
                $isAllowed = true;
            }

            if ($isAllowed) {
                $result = '@media ' . $mediaQueryList;
            }
        }

        return $result;
    }

    /**
     * Tests if a CSS rule is an at-rule that should be passed though and copied to a `<style>` element unmodified:
     * - `@charset` rules are discarded - only UTF-8 is supported - `false` is returned;
     * - `@import` rules are passed through only if they satisfy the specification ("user agents must ignore any
     *   '@import' rule that occurs inside a block or after any non-ignored statement other than an '@charset' or an
     *   '@import' rule");
     * - `@media` rules are processed separately to see if their nested rules apply - `false` is returned;
     * - `@font-face` rules are checked for validity - they must contain both a `src` and `font-family` property;
     * - other at-rules are assumed to be valid and treated as a black box - `true` is returned.
     *
     * @param CssRenderable $rule
     *
     * @return bool
     */
    private function isValidAtRuleToRender(CssRenderable $rule): bool
    {
        if ($rule instanceof CssCharset) {
            return false;
        }

        if ($rule instanceof CssImport) {
            return $this->isImportRuleAllowed;
        }

        $this->isImportRuleAllowed = false;

        if (!$rule instanceof CssAtRule) {
            return false;
        }

        switch ($rule->atRuleName()) {
            case 'media':
                $result = false;
                break;
            case 'font-face':
                $result = $rule instanceof CssRuleSet
                    && $rule->getRules('font-family') !== []
                    && $rule->getRules('src') !== [];
                break;
            default:
                $result = true;
        }

        return $result;
    }
}

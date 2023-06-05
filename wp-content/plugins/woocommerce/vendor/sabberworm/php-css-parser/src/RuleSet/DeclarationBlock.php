<?php

namespace Sabberworm\CSS\RuleSet;

use Sabberworm\CSS\CSSList\CSSList;
use Sabberworm\CSS\CSSList\KeyFrame;
use Sabberworm\CSS\OutputFormat;
use Sabberworm\CSS\Parsing\OutputException;
use Sabberworm\CSS\Parsing\ParserState;
use Sabberworm\CSS\Parsing\UnexpectedEOFException;
use Sabberworm\CSS\Parsing\UnexpectedTokenException;
use Sabberworm\CSS\Property\KeyframeSelector;
use Sabberworm\CSS\Property\Selector;
use Sabberworm\CSS\Rule\Rule;
use Sabberworm\CSS\Value\Color;
use Sabberworm\CSS\Value\RuleValueList;
use Sabberworm\CSS\Value\Size;
use Sabberworm\CSS\Value\URL;
use Sabberworm\CSS\Value\Value;

/**
 * Declaration blocks are the parts of a CSS file which denote the rules belonging to a selector.
 *
 * Declaration blocks usually appear directly inside a `Document` or another `CSSList` (mostly a `MediaQuery`).
 */
class DeclarationBlock extends RuleSet
{
    /**
     * @var array<int, Selector|string>
     */
    private $aSelectors;

    /**
     * @param int $iLineNo
     */
    public function __construct($iLineNo = 0)
    {
        parent::__construct($iLineNo);
        $this->aSelectors = [];
    }

    /**
     * @param CSSList|null $oList
     *
     * @return DeclarationBlock|false
     *
     * @throws UnexpectedTokenException
     * @throws UnexpectedEOFException
     */
    public static function parse(ParserState $oParserState, $oList = null)
    {
        $aComments = [];
        $oResult = new DeclarationBlock($oParserState->currentLine());
        try {
            $aSelectorParts = [];
            $sStringWrapperChar = false;
            do {
                $aSelectorParts[] = $oParserState->consume(1)
                    . $oParserState->consumeUntil(['{', '}', '\'', '"'], false, false, $aComments);
                if (in_array($oParserState->peek(), ['\'', '"']) && substr(end($aSelectorParts), -1) != "\\") {
                    if ($sStringWrapperChar === false) {
                        $sStringWrapperChar = $oParserState->peek();
                    } elseif ($sStringWrapperChar == $oParserState->peek()) {
                        $sStringWrapperChar = false;
                    }
                }
            } while (!in_array($oParserState->peek(), ['{', '}']) || $sStringWrapperChar !== false);
            $oResult->setSelectors(implode('', $aSelectorParts), $oList);
            if ($oParserState->comes('{')) {
                $oParserState->consume(1);
            }
        } catch (UnexpectedTokenException $e) {
            if ($oParserState->getSettings()->bLenientParsing) {
                if (!$oParserState->comes('}')) {
                    $oParserState->consumeUntil('}', false, true);
                }
                return false;
            } else {
                throw $e;
            }
        }
        $oResult->setComments($aComments);
        RuleSet::parseRuleSet($oParserState, $oResult);
        return $oResult;
    }

    /**
     * @param array<int, Selector|string>|string $mSelector
     * @param CSSList|null $oList
     *
     * @throws UnexpectedTokenException
     */
    public function setSelectors($mSelector, $oList = null)
    {
        if (is_array($mSelector)) {
            $this->aSelectors = $mSelector;
        } else {
            $this->aSelectors = explode(',', $mSelector);
        }
        foreach ($this->aSelectors as $iKey => $mSelector) {
            if (!($mSelector instanceof Selector)) {
                if ($oList === null || !($oList instanceof KeyFrame)) {
                    if (!Selector::isValid($mSelector)) {
                        throw new UnexpectedTokenException(
                            "Selector did not match '" . Selector::SELECTOR_VALIDATION_RX . "'.",
                            $mSelector,
                            "custom"
                        );
                    }
                    $this->aSelectors[$iKey] = new Selector($mSelector);
                } else {
                    if (!KeyframeSelector::isValid($mSelector)) {
                        throw new UnexpectedTokenException(
                            "Selector did not match '" . KeyframeSelector::SELECTOR_VALIDATION_RX . "'.",
                            $mSelector,
                            "custom"
                        );
                    }
                    $this->aSelectors[$iKey] = new KeyframeSelector($mSelector);
                }
            }
        }
    }

    /**
     * Remove one of the selectors of the block.
     *
     * @param Selector|string $mSelector
     *
     * @return bool
     */
    public function removeSelector($mSelector)
    {
        if ($mSelector instanceof Selector) {
            $mSelector = $mSelector->getSelector();
        }
        foreach ($this->aSelectors as $iKey => $oSelector) {
            if ($oSelector->getSelector() === $mSelector) {
                unset($this->aSelectors[$iKey]);
                return true;
            }
        }
        return false;
    }

    /**
     * @return array<int, Selector|string>
     *
     * @deprecated will be removed in version 9.0; use `getSelectors()` instead
     */
    public function getSelector()
    {
        return $this->getSelectors();
    }

    /**
     * @param Selector|string $mSelector
     * @param CSSList|null $oList
     *
     * @return void
     *
     * @deprecated will be removed in version 9.0; use `setSelectors()` instead
     */
    public function setSelector($mSelector, $oList = null)
    {
        $this->setSelectors($mSelector, $oList);
    }

    /**
     * @return array<int, Selector|string>
     */
    public function getSelectors()
    {
        return $this->aSelectors;
    }

    /**
     * Splits shorthand declarations (e.g. `margin` or `font`) into their constituent parts.
     *
     * @return void
     */
    public function expandShorthands()
    {
        // border must be expanded before dimensions
        $this->expandBorderShorthand();
        $this->expandDimensionsShorthand();
        $this->expandFontShorthand();
        $this->expandBackgroundShorthand();
        $this->expandListStyleShorthand();
    }

    /**
     * Creates shorthand declarations (e.g. `margin` or `font`) whenever possible.
     *
     * @return void
     */
    public function createShorthands()
    {
        $this->createBackgroundShorthand();
        $this->createDimensionsShorthand();
        // border must be shortened after dimensions
        $this->createBorderShorthand();
        $this->createFontShorthand();
        $this->createListStyleShorthand();
    }

    /**
     * Splits shorthand border declarations (e.g. `border: 1px red;`).
     *
     * Additional splitting happens in expandDimensionsShorthand.
     *
     * Multiple borders are not yet supported as of 3.
     *
     * @return void
     */
    public function expandBorderShorthand()
    {
        $aBorderRules = [
            'border',
            'border-left',
            'border-right',
            'border-top',
            'border-bottom',
        ];
        $aBorderSizes = [
            'thin',
            'medium',
            'thick',
        ];
        $aRules = $this->getRulesAssoc();
        foreach ($aBorderRules as $sBorderRule) {
            if (!isset($aRules[$sBorderRule])) {
                continue;
            }
            $oRule = $aRules[$sBorderRule];
            $mRuleValue = $oRule->getValue();
            $aValues = [];
            if (!$mRuleValue instanceof RuleValueList) {
                $aValues[] = $mRuleValue;
            } else {
                $aValues = $mRuleValue->getListComponents();
            }
            foreach ($aValues as $mValue) {
                if ($mValue instanceof Value) {
                    $mNewValue = clone $mValue;
                } else {
                    $mNewValue = $mValue;
                }
                if ($mValue instanceof Size) {
                    $sNewRuleName = $sBorderRule . "-width";
                } elseif ($mValue instanceof Color) {
                    $sNewRuleName = $sBorderRule . "-color";
                } else {
                    if (in_array($mValue, $aBorderSizes)) {
                        $sNewRuleName = $sBorderRule . "-width";
                    } else {
                        $sNewRuleName = $sBorderRule . "-style";
                    }
                }
                $oNewRule = new Rule($sNewRuleName, $oRule->getLineNo(), $oRule->getColNo());
                $oNewRule->setIsImportant($oRule->getIsImportant());
                $oNewRule->addValue([$mNewValue]);
                $this->addRule($oNewRule);
            }
            $this->removeRule($sBorderRule);
        }
    }

    /**
     * Splits shorthand dimensional declarations (e.g. `margin: 0px auto;`)
     * into their constituent parts.
     *
     * Handles `margin`, `padding`, `border-color`, `border-style` and `border-width`.
     *
     * @return void
     */
    public function expandDimensionsShorthand()
    {
        $aExpansions = [
            'margin' => 'margin-%s',
            'padding' => 'padding-%s',
            'border-color' => 'border-%s-color',
            'border-style' => 'border-%s-style',
            'border-width' => 'border-%s-width',
        ];
        $aRules = $this->getRulesAssoc();
        foreach ($aExpansions as $sProperty => $sExpanded) {
            if (!isset($aRules[$sProperty])) {
                continue;
            }
            $oRule = $aRules[$sProperty];
            $mRuleValue = $oRule->getValue();
            $aValues = [];
            if (!$mRuleValue instanceof RuleValueList) {
                $aValues[] = $mRuleValue;
            } else {
                $aValues = $mRuleValue->getListComponents();
            }
            $top = $right = $bottom = $left = null;
            switch (count($aValues)) {
                case 1:
                    $top = $right = $bottom = $left = $aValues[0];
                    break;
                case 2:
                    $top = $bottom = $aValues[0];
                    $left = $right = $aValues[1];
                    break;
                case 3:
                    $top = $aValues[0];
                    $left = $right = $aValues[1];
                    $bottom = $aValues[2];
                    break;
                case 4:
                    $top = $aValues[0];
                    $right = $aValues[1];
                    $bottom = $aValues[2];
                    $left = $aValues[3];
                    break;
            }
            foreach (['top', 'right', 'bottom', 'left'] as $sPosition) {
                $oNewRule = new Rule(sprintf($sExpanded, $sPosition), $oRule->getLineNo(), $oRule->getColNo());
                $oNewRule->setIsImportant($oRule->getIsImportant());
                $oNewRule->addValue(${$sPosition});
                $this->addRule($oNewRule);
            }
            $this->removeRule($sProperty);
        }
    }

    /**
     * Converts shorthand font declarations
     * (e.g. `font: 300 italic 11px/14px verdana, helvetica, sans-serif;`)
     * into their constituent parts.
     *
     * @return void
     */
    public function expandFontShorthand()
    {
        $aRules = $this->getRulesAssoc();
        if (!isset($aRules['font'])) {
            return;
        }
        $oRule = $aRules['font'];
        // reset properties to 'normal' per http://www.w3.org/TR/21/fonts.html#font-shorthand
        $aFontProperties = [
            'font-style' => 'normal',
            'font-variant' => 'normal',
            'font-weight' => 'normal',
            'font-size' => 'normal',
            'line-height' => 'normal',
        ];
        $mRuleValue = $oRule->getValue();
        $aValues = [];
        if (!$mRuleValue instanceof RuleValueList) {
            $aValues[] = $mRuleValue;
        } else {
            $aValues = $mRuleValue->getListComponents();
        }
        foreach ($aValues as $mValue) {
            if (!$mValue instanceof Value) {
                $mValue = mb_strtolower($mValue);
            }
            if (in_array($mValue, ['normal', 'inherit'])) {
                foreach (['font-style', 'font-weight', 'font-variant'] as $sProperty) {
                    if (!isset($aFontProperties[$sProperty])) {
                        $aFontProperties[$sProperty] = $mValue;
                    }
                }
            } elseif (in_array($mValue, ['italic', 'oblique'])) {
                $aFontProperties['font-style'] = $mValue;
            } elseif ($mValue == 'small-caps') {
                $aFontProperties['font-variant'] = $mValue;
            } elseif (
                in_array($mValue, ['bold', 'bolder', 'lighter'])
                || ($mValue instanceof Size
                    && in_array($mValue->getSize(), range(100, 900, 100)))
            ) {
                $aFontProperties['font-weight'] = $mValue;
            } elseif ($mValue instanceof RuleValueList && $mValue->getListSeparator() == '/') {
                list($oSize, $oHeight) = $mValue->getListComponents();
                $aFontProperties['font-size'] = $oSize;
                $aFontProperties['line-height'] = $oHeight;
            } elseif ($mValue instanceof Size && $mValue->getUnit() !== null) {
                $aFontProperties['font-size'] = $mValue;
            } else {
                $aFontProperties['font-family'] = $mValue;
            }
        }
        foreach ($aFontProperties as $sProperty => $mValue) {
            $oNewRule = new Rule($sProperty, $oRule->getLineNo(), $oRule->getColNo());
            $oNewRule->addValue($mValue);
            $oNewRule->setIsImportant($oRule->getIsImportant());
            $this->addRule($oNewRule);
        }
        $this->removeRule('font');
    }

    /**
     * Converts shorthand background declarations
     * (e.g. `background: url("chess.png") gray 50% repeat fixed;`)
     * into their constituent parts.
     *
     * @see http://www.w3.org/TR/21/colors.html#propdef-background
     *
     * @return void
     */
    public function expandBackgroundShorthand()
    {
        $aRules = $this->getRulesAssoc();
        if (!isset($aRules['background'])) {
            return;
        }
        $oRule = $aRules['background'];
        $aBgProperties = [
            'background-color' => ['transparent'],
            'background-image' => ['none'],
            'background-repeat' => ['repeat'],
            'background-attachment' => ['scroll'],
            'background-position' => [
                new Size(0, '%', null, false, $this->iLineNo),
                new Size(0, '%', null, false, $this->iLineNo),
            ],
        ];
        $mRuleValue = $oRule->getValue();
        $aValues = [];
        if (!$mRuleValue instanceof RuleValueList) {
            $aValues[] = $mRuleValue;
        } else {
            $aValues = $mRuleValue->getListComponents();
        }
        if (count($aValues) == 1 && $aValues[0] == 'inherit') {
            foreach ($aBgProperties as $sProperty => $mValue) {
                $oNewRule = new Rule($sProperty, $oRule->getLineNo(), $oRule->getColNo());
                $oNewRule->addValue('inherit');
                $oNewRule->setIsImportant($oRule->getIsImportant());
                $this->addRule($oNewRule);
            }
            $this->removeRule('background');
            return;
        }
        $iNumBgPos = 0;
        foreach ($aValues as $mValue) {
            if (!$mValue instanceof Value) {
                $mValue = mb_strtolower($mValue);
            }
            if ($mValue instanceof URL) {
                $aBgProperties['background-image'] = $mValue;
            } elseif ($mValue instanceof Color) {
                $aBgProperties['background-color'] = $mValue;
            } elseif (in_array($mValue, ['scroll', 'fixed'])) {
                $aBgProperties['background-attachment'] = $mValue;
            } elseif (in_array($mValue, ['repeat', 'no-repeat', 'repeat-x', 'repeat-y'])) {
                $aBgProperties['background-repeat'] = $mValue;
            } elseif (
                in_array($mValue, ['left', 'center', 'right', 'top', 'bottom'])
                || $mValue instanceof Size
            ) {
                if ($iNumBgPos == 0) {
                    $aBgProperties['background-position'][0] = $mValue;
                    $aBgProperties['background-position'][1] = 'center';
                } else {
                    $aBgProperties['background-position'][$iNumBgPos] = $mValue;
                }
                $iNumBgPos++;
            }
        }
        foreach ($aBgProperties as $sProperty => $mValue) {
            $oNewRule = new Rule($sProperty, $oRule->getLineNo(), $oRule->getColNo());
            $oNewRule->setIsImportant($oRule->getIsImportant());
            $oNewRule->addValue($mValue);
            $this->addRule($oNewRule);
        }
        $this->removeRule('background');
    }

    /**
     * @return void
     */
    public function expandListStyleShorthand()
    {
        $aListProperties = [
            'list-style-type' => 'disc',
            'list-style-position' => 'outside',
            'list-style-image' => 'none',
        ];
        $aListStyleTypes = [
            'none',
            'disc',
            'circle',
            'square',
            'decimal-leading-zero',
            'decimal',
            'lower-roman',
            'upper-roman',
            'lower-greek',
            'lower-alpha',
            'lower-latin',
            'upper-alpha',
            'upper-latin',
            'hebrew',
            'armenian',
            'georgian',
            'cjk-ideographic',
            'hiragana',
            'hira-gana-iroha',
            'katakana-iroha',
            'katakana',
        ];
        $aListStylePositions = [
            'inside',
            'outside',
        ];
        $aRules = $this->getRulesAssoc();
        if (!isset($aRules['list-style'])) {
            return;
        }
        $oRule = $aRules['list-style'];
        $mRuleValue = $oRule->getValue();
        $aValues = [];
        if (!$mRuleValue instanceof RuleValueList) {
            $aValues[] = $mRuleValue;
        } else {
            $aValues = $mRuleValue->getListComponents();
        }
        if (count($aValues) == 1 && $aValues[0] == 'inherit') {
            foreach ($aListProperties as $sProperty => $mValue) {
                $oNewRule = new Rule($sProperty, $oRule->getLineNo(), $oRule->getColNo());
                $oNewRule->addValue('inherit');
                $oNewRule->setIsImportant($oRule->getIsImportant());
                $this->addRule($oNewRule);
            }
            $this->removeRule('list-style');
            return;
        }
        foreach ($aValues as $mValue) {
            if (!$mValue instanceof Value) {
                $mValue = mb_strtolower($mValue);
            }
            if ($mValue instanceof Url) {
                $aListProperties['list-style-image'] = $mValue;
            } elseif (in_array($mValue, $aListStyleTypes)) {
                $aListProperties['list-style-types'] = $mValue;
            } elseif (in_array($mValue, $aListStylePositions)) {
                $aListProperties['list-style-position'] = $mValue;
            }
        }
        foreach ($aListProperties as $sProperty => $mValue) {
            $oNewRule = new Rule($sProperty, $oRule->getLineNo(), $oRule->getColNo());
            $oNewRule->setIsImportant($oRule->getIsImportant());
            $oNewRule->addValue($mValue);
            $this->addRule($oNewRule);
        }
        $this->removeRule('list-style');
    }

    /**
     * @param array<array-key, string> $aProperties
     * @param string $sShorthand
     *
     * @return void
     */
    public function createShorthandProperties(array $aProperties, $sShorthand)
    {
        $aRules = $this->getRulesAssoc();
        $aNewValues = [];
        foreach ($aProperties as $sProperty) {
            if (!isset($aRules[$sProperty])) {
                continue;
            }
            $oRule = $aRules[$sProperty];
            if (!$oRule->getIsImportant()) {
                $mRuleValue = $oRule->getValue();
                $aValues = [];
                if (!$mRuleValue instanceof RuleValueList) {
                    $aValues[] = $mRuleValue;
                } else {
                    $aValues = $mRuleValue->getListComponents();
                }
                foreach ($aValues as $mValue) {
                    $aNewValues[] = $mValue;
                }
                $this->removeRule($sProperty);
            }
        }
        if (count($aNewValues)) {
            $oNewRule = new Rule($sShorthand, $oRule->getLineNo(), $oRule->getColNo());
            foreach ($aNewValues as $mValue) {
                $oNewRule->addValue($mValue);
            }
            $this->addRule($oNewRule);
        }
    }

    /**
     * @return void
     */
    public function createBackgroundShorthand()
    {
        $aProperties = [
            'background-color',
            'background-image',
            'background-repeat',
            'background-position',
            'background-attachment',
        ];
        $this->createShorthandProperties($aProperties, 'background');
    }

    /**
     * @return void
     */
    public function createListStyleShorthand()
    {
        $aProperties = [
            'list-style-type',
            'list-style-position',
            'list-style-image',
        ];
        $this->createShorthandProperties($aProperties, 'list-style');
    }

    /**
     * Combines `border-color`, `border-style` and `border-width` into `border`.
     *
     * Should be run after `create_dimensions_shorthand`!
     *
     * @return void
     */
    public function createBorderShorthand()
    {
        $aProperties = [
            'border-width',
            'border-style',
            'border-color',
        ];
        $this->createShorthandProperties($aProperties, 'border');
    }

    /**
     * Looks for long format CSS dimensional properties
     * (margin, padding, border-color, border-style and border-width)
     * and converts them into shorthand CSS properties.
     *
     * @return void
     */
    public function createDimensionsShorthand()
    {
        $aPositions = ['top', 'right', 'bottom', 'left'];
        $aExpansions = [
            'margin' => 'margin-%s',
            'padding' => 'padding-%s',
            'border-color' => 'border-%s-color',
            'border-style' => 'border-%s-style',
            'border-width' => 'border-%s-width',
        ];
        $aRules = $this->getRulesAssoc();
        foreach ($aExpansions as $sProperty => $sExpanded) {
            $aFoldable = [];
            foreach ($aRules as $sRuleName => $oRule) {
                foreach ($aPositions as $sPosition) {
                    if ($sRuleName == sprintf($sExpanded, $sPosition)) {
                        $aFoldable[$sRuleName] = $oRule;
                    }
                }
            }
            // All four dimensions must be present
            if (count($aFoldable) == 4) {
                $aValues = [];
                foreach ($aPositions as $sPosition) {
                    $oRule = $aRules[sprintf($sExpanded, $sPosition)];
                    $mRuleValue = $oRule->getValue();
                    $aRuleValues = [];
                    if (!$mRuleValue instanceof RuleValueList) {
                        $aRuleValues[] = $mRuleValue;
                    } else {
                        $aRuleValues = $mRuleValue->getListComponents();
                    }
                    $aValues[$sPosition] = $aRuleValues;
                }
                $oNewRule = new Rule($sProperty, $oRule->getLineNo(), $oRule->getColNo());
                if ((string)$aValues['left'][0] == (string)$aValues['right'][0]) {
                    if ((string)$aValues['top'][0] == (string)$aValues['bottom'][0]) {
                        if ((string)$aValues['top'][0] == (string)$aValues['left'][0]) {
                            // All 4 sides are equal
                            $oNewRule->addValue($aValues['top']);
                        } else {
                            // Top and bottom are equal, left and right are equal
                            $oNewRule->addValue($aValues['top']);
                            $oNewRule->addValue($aValues['left']);
                        }
                    } else {
                        // Only left and right are equal
                        $oNewRule->addValue($aValues['top']);
                        $oNewRule->addValue($aValues['left']);
                        $oNewRule->addValue($aValues['bottom']);
                    }
                } else {
                    // No sides are equal
                    $oNewRule->addValue($aValues['top']);
                    $oNewRule->addValue($aValues['left']);
                    $oNewRule->addValue($aValues['bottom']);
                    $oNewRule->addValue($aValues['right']);
                }
                $this->addRule($oNewRule);
                foreach ($aPositions as $sPosition) {
                    $this->removeRule(sprintf($sExpanded, $sPosition));
                }
            }
        }
    }

    /**
     * Looks for long format CSS font properties (e.g. `font-weight`) and
     * tries to convert them into a shorthand CSS `font` property.
     *
     * At least `font-size` AND `font-family` must be present in order to create a shorthand declaration.
     *
     * @return void
     */
    public function createFontShorthand()
    {
        $aFontProperties = [
            'font-style',
            'font-variant',
            'font-weight',
            'font-size',
            'line-height',
            'font-family',
        ];
        $aRules = $this->getRulesAssoc();
        if (!isset($aRules['font-size']) || !isset($aRules['font-family'])) {
            return;
        }
        $oOldRule = isset($aRules['font-size']) ? $aRules['font-size'] : $aRules['font-family'];
        $oNewRule = new Rule('font', $oOldRule->getLineNo(), $oOldRule->getColNo());
        unset($oOldRule);
        foreach (['font-style', 'font-variant', 'font-weight'] as $sProperty) {
            if (isset($aRules[$sProperty])) {
                $oRule = $aRules[$sProperty];
                $mRuleValue = $oRule->getValue();
                $aValues = [];
                if (!$mRuleValue instanceof RuleValueList) {
                    $aValues[] = $mRuleValue;
                } else {
                    $aValues = $mRuleValue->getListComponents();
                }
                if ($aValues[0] !== 'normal') {
                    $oNewRule->addValue($aValues[0]);
                }
            }
        }
        // Get the font-size value
        $oRule = $aRules['font-size'];
        $mRuleValue = $oRule->getValue();
        $aFSValues = [];
        if (!$mRuleValue instanceof RuleValueList) {
            $aFSValues[] = $mRuleValue;
        } else {
            $aFSValues = $mRuleValue->getListComponents();
        }
        // But wait to know if we have line-height to add it
        if (isset($aRules['line-height'])) {
            $oRule = $aRules['line-height'];
            $mRuleValue = $oRule->getValue();
            $aLHValues = [];
            if (!$mRuleValue instanceof RuleValueList) {
                $aLHValues[] = $mRuleValue;
            } else {
                $aLHValues = $mRuleValue->getListComponents();
            }
            if ($aLHValues[0] !== 'normal') {
                $val = new RuleValueList('/', $this->iLineNo);
                $val->addListComponent($aFSValues[0]);
                $val->addListComponent($aLHValues[0]);
                $oNewRule->addValue($val);
            }
        } else {
            $oNewRule->addValue($aFSValues[0]);
        }
        $oRule = $aRules['font-family'];
        $mRuleValue = $oRule->getValue();
        $aFFValues = [];
        if (!$mRuleValue instanceof RuleValueList) {
            $aFFValues[] = $mRuleValue;
        } else {
            $aFFValues = $mRuleValue->getListComponents();
        }
        $oFFValue = new RuleValueList(',', $this->iLineNo);
        $oFFValue->setListComponents($aFFValues);
        $oNewRule->addValue($oFFValue);

        $this->addRule($oNewRule);
        foreach ($aFontProperties as $sProperty) {
            $this->removeRule($sProperty);
        }
    }

    /**
     * @return string
     *
     * @throws OutputException
     */
    public function __toString()
    {
        return $this->render(new OutputFormat());
    }

    /**
     * @return string
     *
     * @throws OutputException
     */
    public function render(OutputFormat $oOutputFormat)
    {
        if (count($this->aSelectors) === 0) {
            // If all the selectors have been removed, this declaration block becomes invalid
            throw new OutputException("Attempt to print declaration block with missing selector", $this->iLineNo);
        }
        $sResult = $oOutputFormat->sBeforeDeclarationBlock;
        $sResult .= $oOutputFormat->implode(
            $oOutputFormat->spaceBeforeSelectorSeparator() . ',' . $oOutputFormat->spaceAfterSelectorSeparator(),
            $this->aSelectors
        );
        $sResult .= $oOutputFormat->sAfterDeclarationBlockSelectors;
        $sResult .= $oOutputFormat->spaceBeforeOpeningBrace() . '{';
        $sResult .= parent::render($oOutputFormat);
        $sResult .= '}';
        $sResult .= $oOutputFormat->sAfterDeclarationBlock;
        return $sResult;
    }
}

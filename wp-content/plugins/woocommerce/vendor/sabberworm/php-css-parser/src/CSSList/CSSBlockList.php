<?php

namespace Sabberworm\CSS\CSSList;

use Sabberworm\CSS\Property\Selector;
use Sabberworm\CSS\Rule\Rule;
use Sabberworm\CSS\RuleSet\DeclarationBlock;
use Sabberworm\CSS\RuleSet\RuleSet;
use Sabberworm\CSS\Value\CSSFunction;
use Sabberworm\CSS\Value\Value;
use Sabberworm\CSS\Value\ValueList;

/**
 * A `CSSBlockList` is a `CSSList` whose `DeclarationBlock`s are guaranteed to contain valid declaration blocks or
 * at-rules.
 *
 * Most `CSSList`s conform to this category but some at-rules (such as `@keyframes`) do not.
 */
abstract class CSSBlockList extends CSSList
{
    /**
     * @param int $iLineNo
     */
    public function __construct($iLineNo = 0)
    {
        parent::__construct($iLineNo);
    }

    /**
     * @param array<int, DeclarationBlock> $aResult
     *
     * @return void
     */
    protected function allDeclarationBlocks(array &$aResult)
    {
        foreach ($this->aContents as $mContent) {
            if ($mContent instanceof DeclarationBlock) {
                $aResult[] = $mContent;
            } elseif ($mContent instanceof CSSBlockList) {
                $mContent->allDeclarationBlocks($aResult);
            }
        }
    }

    /**
     * @param array<int, RuleSet> $aResult
     *
     * @return void
     */
    protected function allRuleSets(array &$aResult)
    {
        foreach ($this->aContents as $mContent) {
            if ($mContent instanceof RuleSet) {
                $aResult[] = $mContent;
            } elseif ($mContent instanceof CSSBlockList) {
                $mContent->allRuleSets($aResult);
            }
        }
    }

    /**
     * @param CSSList|Rule|RuleSet|Value $oElement
     * @param array<int, Value> $aResult
     * @param string|null $sSearchString
     * @param bool $bSearchInFunctionArguments
     *
     * @return void
     */
    protected function allValues($oElement, array &$aResult, $sSearchString = null, $bSearchInFunctionArguments = false)
    {
        if ($oElement instanceof CSSBlockList) {
            foreach ($oElement->getContents() as $oContent) {
                $this->allValues($oContent, $aResult, $sSearchString, $bSearchInFunctionArguments);
            }
        } elseif ($oElement instanceof RuleSet) {
            foreach ($oElement->getRules($sSearchString) as $oRule) {
                $this->allValues($oRule, $aResult, $sSearchString, $bSearchInFunctionArguments);
            }
        } elseif ($oElement instanceof Rule) {
            $this->allValues($oElement->getValue(), $aResult, $sSearchString, $bSearchInFunctionArguments);
        } elseif ($oElement instanceof ValueList) {
            if ($bSearchInFunctionArguments || !($oElement instanceof CSSFunction)) {
                foreach ($oElement->getListComponents() as $mComponent) {
                    $this->allValues($mComponent, $aResult, $sSearchString, $bSearchInFunctionArguments);
                }
            }
        } else {
            // Non-List `Value` or `CSSString` (CSS identifier)
            $aResult[] = $oElement;
        }
    }

    /**
     * @param array<int, Selector> $aResult
     * @param string|null $sSpecificitySearch
     *
     * @return void
     */
    protected function allSelectors(array &$aResult, $sSpecificitySearch = null)
    {
        /** @var array<int, DeclarationBlock> $aDeclarationBlocks */
        $aDeclarationBlocks = [];
        $this->allDeclarationBlocks($aDeclarationBlocks);
        foreach ($aDeclarationBlocks as $oBlock) {
            foreach ($oBlock->getSelectors() as $oSelector) {
                if ($sSpecificitySearch === null) {
                    $aResult[] = $oSelector;
                } else {
                    $sComparator = '===';
                    $aSpecificitySearch = explode(' ', $sSpecificitySearch);
                    $iTargetSpecificity = $aSpecificitySearch[0];
                    if (count($aSpecificitySearch) > 1) {
                        $sComparator = $aSpecificitySearch[0];
                        $iTargetSpecificity = $aSpecificitySearch[1];
                    }
                    $iTargetSpecificity = (int)$iTargetSpecificity;
                    $iSelectorSpecificity = $oSelector->getSpecificity();
                    $bMatches = false;
                    switch ($sComparator) {
                        case '<=':
                            $bMatches = $iSelectorSpecificity <= $iTargetSpecificity;
                            break;
                        case '<':
                            $bMatches = $iSelectorSpecificity < $iTargetSpecificity;
                            break;
                        case '>=':
                            $bMatches = $iSelectorSpecificity >= $iTargetSpecificity;
                            break;
                        case '>':
                            $bMatches = $iSelectorSpecificity > $iTargetSpecificity;
                            break;
                        default:
                            $bMatches = $iSelectorSpecificity === $iTargetSpecificity;
                            break;
                    }
                    if ($bMatches) {
                        $aResult[] = $oSelector;
                    }
                }
            }
        }
    }
}

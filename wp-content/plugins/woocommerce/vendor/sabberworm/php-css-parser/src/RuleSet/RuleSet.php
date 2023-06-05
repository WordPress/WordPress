<?php

namespace Sabberworm\CSS\RuleSet;

use Sabberworm\CSS\Comment\Comment;
use Sabberworm\CSS\Comment\Commentable;
use Sabberworm\CSS\OutputFormat;
use Sabberworm\CSS\Parsing\ParserState;
use Sabberworm\CSS\Parsing\UnexpectedEOFException;
use Sabberworm\CSS\Parsing\UnexpectedTokenException;
use Sabberworm\CSS\Renderable;
use Sabberworm\CSS\Rule\Rule;

/**
 * RuleSet is a generic superclass denoting rules. The typical example for rule sets are declaration block.
 * However, unknown At-Rules (like `@font-face`) are also rule sets.
 */
abstract class RuleSet implements Renderable, Commentable
{
    /**
     * @var array<string, Rule>
     */
    private $aRules;

    /**
     * @var int
     */
    protected $iLineNo;

    /**
     * @var array<array-key, Comment>
     */
    protected $aComments;

    /**
     * @param int $iLineNo
     */
    public function __construct($iLineNo = 0)
    {
        $this->aRules = [];
        $this->iLineNo = $iLineNo;
        $this->aComments = [];
    }

    /**
     * @return void
     *
     * @throws UnexpectedTokenException
     * @throws UnexpectedEOFException
     */
    public static function parseRuleSet(ParserState $oParserState, RuleSet $oRuleSet)
    {
        while ($oParserState->comes(';')) {
            $oParserState->consume(';');
        }
        while (!$oParserState->comes('}')) {
            $oRule = null;
            if ($oParserState->getSettings()->bLenientParsing) {
                try {
                    $oRule = Rule::parse($oParserState);
                } catch (UnexpectedTokenException $e) {
                    try {
                        $sConsume = $oParserState->consumeUntil(["\n", ";", '}'], true);
                        // We need to “unfind” the matches to the end of the ruleSet as this will be matched later
                        if ($oParserState->streql(substr($sConsume, -1), '}')) {
                            $oParserState->backtrack(1);
                        } else {
                            while ($oParserState->comes(';')) {
                                $oParserState->consume(';');
                            }
                        }
                    } catch (UnexpectedTokenException $e) {
                        // We’ve reached the end of the document. Just close the RuleSet.
                        return;
                    }
                }
            } else {
                $oRule = Rule::parse($oParserState);
            }
            if ($oRule) {
                $oRuleSet->addRule($oRule);
            }
        }
        $oParserState->consume('}');
    }

    /**
     * @return int
     */
    public function getLineNo()
    {
        return $this->iLineNo;
    }

    /**
     * @param Rule|null $oSibling
     *
     * @return void
     */
    public function addRule(Rule $oRule, Rule $oSibling = null)
    {
        $sRule = $oRule->getRule();
        if (!isset($this->aRules[$sRule])) {
            $this->aRules[$sRule] = [];
        }

        $iPosition = count($this->aRules[$sRule]);

        if ($oSibling !== null) {
            $iSiblingPos = array_search($oSibling, $this->aRules[$sRule], true);
            if ($iSiblingPos !== false) {
                $iPosition = $iSiblingPos;
                $oRule->setPosition($oSibling->getLineNo(), $oSibling->getColNo() - 1);
            }
        }
        if ($oRule->getLineNo() === 0 && $oRule->getColNo() === 0) {
            //this node is added manually, give it the next best line
            $rules = $this->getRules();
            $pos = count($rules);
            if ($pos > 0) {
                $last = $rules[$pos - 1];
                $oRule->setPosition($last->getLineNo() + 1, 0);
            }
        }

        array_splice($this->aRules[$sRule], $iPosition, 0, [$oRule]);
    }

    /**
     * Returns all rules matching the given rule name
     *
     * @example $oRuleSet->getRules('font') // returns array(0 => $oRule, …) or array().
     *
     * @example $oRuleSet->getRules('font-')
     *          //returns an array of all rules either beginning with font- or matching font.
     *
     * @param Rule|string|null $mRule
     *        Pattern to search for. If null, returns all rules.
     *        If the pattern ends with a dash, all rules starting with the pattern are returned
     *        as well as one matching the pattern with the dash excluded.
     *        Passing a Rule behaves like calling `getRules($mRule->getRule())`.
     *
     * @return array<int, Rule>
     */
    public function getRules($mRule = null)
    {
        if ($mRule instanceof Rule) {
            $mRule = $mRule->getRule();
        }
        /** @var array<int, Rule> $aResult */
        $aResult = [];
        foreach ($this->aRules as $sName => $aRules) {
            // Either no search rule is given or the search rule matches the found rule exactly
            // or the search rule ends in “-” and the found rule starts with the search rule.
            if (
                !$mRule || $sName === $mRule
                || (
                    strrpos($mRule, '-') === strlen($mRule) - strlen('-')
                    && (strpos($sName, $mRule) === 0 || $sName === substr($mRule, 0, -1))
                )
            ) {
                $aResult = array_merge($aResult, $aRules);
            }
        }
        usort($aResult, function (Rule $first, Rule $second) {
            if ($first->getLineNo() === $second->getLineNo()) {
                return $first->getColNo() - $second->getColNo();
            }
            return $first->getLineNo() - $second->getLineNo();
        });
        return $aResult;
    }

    /**
     * Overrides all the rules of this set.
     *
     * @param array<array-key, Rule> $aRules The rules to override with.
     *
     * @return void
     */
    public function setRules(array $aRules)
    {
        $this->aRules = [];
        foreach ($aRules as $rule) {
            $this->addRule($rule);
        }
    }

    /**
     * Returns all rules matching the given pattern and returns them in an associative array with the rule’s name
     * as keys. This method exists mainly for backwards-compatibility and is really only partially useful.
     *
     * Note: This method loses some information: Calling this (with an argument of `background-`) on a declaration block
     * like `{ background-color: green; background-color; rgba(0, 127, 0, 0.7); }` will only yield an associative array
     * containing the rgba-valued rule while `getRules()` would yield an indexed array containing both.
     *
     * @param Rule|string|null $mRule $mRule
     *        Pattern to search for. If null, returns all rules. If the pattern ends with a dash,
     *        all rules starting with the pattern are returned as well as one matching the pattern with the dash
     *        excluded. Passing a Rule behaves like calling `getRules($mRule->getRule())`.
     *
     * @return array<string, Rule>
     */
    public function getRulesAssoc($mRule = null)
    {
        /** @var array<string, Rule> $aResult */
        $aResult = [];
        foreach ($this->getRules($mRule) as $oRule) {
            $aResult[$oRule->getRule()] = $oRule;
        }
        return $aResult;
    }

    /**
     * Removes a rule from this RuleSet. This accepts all the possible values that `getRules()` accepts.
     *
     * If given a Rule, it will only remove this particular rule (by identity).
     * If given a name, it will remove all rules by that name.
     *
     * Note: this is different from pre-v.2.0 behaviour of PHP-CSS-Parser, where passing a Rule instance would
     * remove all rules with the same name. To get the old behaviour, use `removeRule($oRule->getRule())`.
     *
     * @param Rule|string|null $mRule
     *        pattern to remove. If $mRule is null, all rules are removed. If the pattern ends in a dash,
     *        all rules starting with the pattern are removed as well as one matching the pattern with the dash
     *        excluded. Passing a Rule behaves matches by identity.
     *
     * @return void
     */
    public function removeRule($mRule)
    {
        if ($mRule instanceof Rule) {
            $sRule = $mRule->getRule();
            if (!isset($this->aRules[$sRule])) {
                return;
            }
            foreach ($this->aRules[$sRule] as $iKey => $oRule) {
                if ($oRule === $mRule) {
                    unset($this->aRules[$sRule][$iKey]);
                }
            }
        } else {
            foreach ($this->aRules as $sName => $aRules) {
                // Either no search rule is given or the search rule matches the found rule exactly
                // or the search rule ends in “-” and the found rule starts with the search rule or equals it
                // (without the trailing dash).
                if (
                    !$mRule || $sName === $mRule
                    || (strrpos($mRule, '-') === strlen($mRule) - strlen('-')
                        && (strpos($sName, $mRule) === 0 || $sName === substr($mRule, 0, -1)))
                ) {
                    unset($this->aRules[$sName]);
                }
            }
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->render(new OutputFormat());
    }

    /**
     * @return string
     */
    public function render(OutputFormat $oOutputFormat)
    {
        $sResult = '';
        $bIsFirst = true;
        foreach ($this->aRules as $aRules) {
            foreach ($aRules as $oRule) {
                $sRendered = $oOutputFormat->safely(function () use ($oRule, $oOutputFormat) {
                    return $oRule->render($oOutputFormat->nextLevel());
                });
                if ($sRendered === null) {
                    continue;
                }
                if ($bIsFirst) {
                    $bIsFirst = false;
                    $sResult .= $oOutputFormat->nextLevel()->spaceBeforeRules();
                } else {
                    $sResult .= $oOutputFormat->nextLevel()->spaceBetweenRules();
                }
                $sResult .= $sRendered;
            }
        }

        if (!$bIsFirst) {
            // Had some output
            $sResult .= $oOutputFormat->spaceAfterRules();
        }

        return $oOutputFormat->removeLastSemicolon($sResult);
    }

    /**
     * @param array<string, Comment> $aComments
     *
     * @return void
     */
    public function addComments(array $aComments)
    {
        $this->aComments = array_merge($this->aComments, $aComments);
    }

    /**
     * @return array<string, Comment>
     */
    public function getComments()
    {
        return $this->aComments;
    }

    /**
     * @param array<string, Comment> $aComments
     *
     * @return void
     */
    public function setComments(array $aComments)
    {
        $this->aComments = $aComments;
    }
}

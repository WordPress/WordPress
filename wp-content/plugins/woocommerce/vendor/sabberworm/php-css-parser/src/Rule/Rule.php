<?php

namespace Sabberworm\CSS\Rule;

use Sabberworm\CSS\Comment\Comment;
use Sabberworm\CSS\Comment\Commentable;
use Sabberworm\CSS\OutputFormat;
use Sabberworm\CSS\Parsing\ParserState;
use Sabberworm\CSS\Parsing\UnexpectedEOFException;
use Sabberworm\CSS\Parsing\UnexpectedTokenException;
use Sabberworm\CSS\Renderable;
use Sabberworm\CSS\Value\RuleValueList;
use Sabberworm\CSS\Value\Value;

/**
 * RuleSets contains Rule objects which always have a key and a value.
 * In CSS, Rules are expressed as follows: “key: value[0][0] value[0][1], value[1][0] value[1][1];”
 */
class Rule implements Renderable, Commentable
{
    /**
     * @var string
     */
    private $sRule;

    /**
     * @var RuleValueList|null
     */
    private $mValue;

    /**
     * @var bool
     */
    private $bIsImportant;

    /**
     * @var array<int, int>
     */
    private $aIeHack;

    /**
     * @var int
     */
    protected $iLineNo;

    /**
     * @var int
     */
    protected $iColNo;

    /**
     * @var array<array-key, Comment>
     */
    protected $aComments;

    /**
     * @param string $sRule
     * @param int $iLineNo
     * @param int $iColNo
     */
    public function __construct($sRule, $iLineNo = 0, $iColNo = 0)
    {
        $this->sRule = $sRule;
        $this->mValue = null;
        $this->bIsImportant = false;
        $this->aIeHack = [];
        $this->iLineNo = $iLineNo;
        $this->iColNo = $iColNo;
        $this->aComments = [];
    }

    /**
     * @return Rule
     *
     * @throws UnexpectedEOFException
     * @throws UnexpectedTokenException
     */
    public static function parse(ParserState $oParserState)
    {
        $aComments = $oParserState->consumeWhiteSpace();
        $oRule = new Rule(
            $oParserState->parseIdentifier(!$oParserState->comes("--")),
            $oParserState->currentLine(),
            $oParserState->currentColumn()
        );
        $oRule->setComments($aComments);
        $oRule->addComments($oParserState->consumeWhiteSpace());
        $oParserState->consume(':');
        $oValue = Value::parseValue($oParserState, self::listDelimiterForRule($oRule->getRule()));
        $oRule->setValue($oValue);
        if ($oParserState->getSettings()->bLenientParsing) {
            while ($oParserState->comes('\\')) {
                $oParserState->consume('\\');
                $oRule->addIeHack($oParserState->consume());
                $oParserState->consumeWhiteSpace();
            }
        }
        $oParserState->consumeWhiteSpace();
        if ($oParserState->comes('!')) {
            $oParserState->consume('!');
            $oParserState->consumeWhiteSpace();
            $oParserState->consume('important');
            $oRule->setIsImportant(true);
        }
        $oParserState->consumeWhiteSpace();
        while ($oParserState->comes(';')) {
            $oParserState->consume(';');
        }
        $oParserState->consumeWhiteSpace();

        return $oRule;
    }

    /**
     * @param string $sRule
     *
     * @return array<int, string>
     */
    private static function listDelimiterForRule($sRule)
    {
        if (preg_match('/^font($|-)/', $sRule)) {
            return [',', '/', ' '];
        }
        return [',', ' ', '/'];
    }

    /**
     * @return int
     */
    public function getLineNo()
    {
        return $this->iLineNo;
    }

    /**
     * @return int
     */
    public function getColNo()
    {
        return $this->iColNo;
    }

    /**
     * @param int $iLine
     * @param int $iColumn
     *
     * @return void
     */
    public function setPosition($iLine, $iColumn)
    {
        $this->iColNo = $iColumn;
        $this->iLineNo = $iLine;
    }

    /**
     * @param string $sRule
     *
     * @return void
     */
    public function setRule($sRule)
    {
        $this->sRule = $sRule;
    }

    /**
     * @return string
     */
    public function getRule()
    {
        return $this->sRule;
    }

    /**
     * @return RuleValueList|null
     */
    public function getValue()
    {
        return $this->mValue;
    }

    /**
     * @param RuleValueList|null $mValue
     *
     * @return void
     */
    public function setValue($mValue)
    {
        $this->mValue = $mValue;
    }

    /**
     * @param array<array-key, array<array-key, RuleValueList>> $aSpaceSeparatedValues
     *
     * @return RuleValueList
     *
     * @deprecated will be removed in version 9.0
     *             Old-Style 2-dimensional array given. Retained for (some) backwards-compatibility.
     *             Use `setValue()` instead and wrap the value inside a RuleValueList if necessary.
     */
    public function setValues(array $aSpaceSeparatedValues)
    {
        $oSpaceSeparatedList = null;
        if (count($aSpaceSeparatedValues) > 1) {
            $oSpaceSeparatedList = new RuleValueList(' ', $this->iLineNo);
        }
        foreach ($aSpaceSeparatedValues as $aCommaSeparatedValues) {
            $oCommaSeparatedList = null;
            if (count($aCommaSeparatedValues) > 1) {
                $oCommaSeparatedList = new RuleValueList(',', $this->iLineNo);
            }
            foreach ($aCommaSeparatedValues as $mValue) {
                if (!$oSpaceSeparatedList && !$oCommaSeparatedList) {
                    $this->mValue = $mValue;
                    return $mValue;
                }
                if ($oCommaSeparatedList) {
                    $oCommaSeparatedList->addListComponent($mValue);
                } else {
                    $oSpaceSeparatedList->addListComponent($mValue);
                }
            }
            if (!$oSpaceSeparatedList) {
                $this->mValue = $oCommaSeparatedList;
                return $oCommaSeparatedList;
            } else {
                $oSpaceSeparatedList->addListComponent($oCommaSeparatedList);
            }
        }
        $this->mValue = $oSpaceSeparatedList;
        return $oSpaceSeparatedList;
    }

    /**
     * @return array<int, array<int, RuleValueList>>
     *
     * @deprecated will be removed in version 9.0
     *             Old-Style 2-dimensional array returned. Retained for (some) backwards-compatibility.
     *             Use `getValue()` instead and check for the existence of a (nested set of) ValueList object(s).
     */
    public function getValues()
    {
        if (!$this->mValue instanceof RuleValueList) {
            return [[$this->mValue]];
        }
        if ($this->mValue->getListSeparator() === ',') {
            return [$this->mValue->getListComponents()];
        }
        $aResult = [];
        foreach ($this->mValue->getListComponents() as $mValue) {
            if (!$mValue instanceof RuleValueList || $mValue->getListSeparator() !== ',') {
                $aResult[] = [$mValue];
                continue;
            }
            if ($this->mValue->getListSeparator() === ' ' || count($aResult) === 0) {
                $aResult[] = [];
            }
            foreach ($mValue->getListComponents() as $mValue) {
                $aResult[count($aResult) - 1][] = $mValue;
            }
        }
        return $aResult;
    }

    /**
     * Adds a value to the existing value. Value will be appended if a `RuleValueList` exists of the given type.
     * Otherwise, the existing value will be wrapped by one.
     *
     * @param RuleValueList|array<int, RuleValueList> $mValue
     * @param string $sType
     *
     * @return void
     */
    public function addValue($mValue, $sType = ' ')
    {
        if (!is_array($mValue)) {
            $mValue = [$mValue];
        }
        if (!$this->mValue instanceof RuleValueList || $this->mValue->getListSeparator() !== $sType) {
            $mCurrentValue = $this->mValue;
            $this->mValue = new RuleValueList($sType, $this->iLineNo);
            if ($mCurrentValue) {
                $this->mValue->addListComponent($mCurrentValue);
            }
        }
        foreach ($mValue as $mValueItem) {
            $this->mValue->addListComponent($mValueItem);
        }
    }

    /**
     * @param int $iModifier
     *
     * @return void
     */
    public function addIeHack($iModifier)
    {
        $this->aIeHack[] = $iModifier;
    }

    /**
     * @param array<int, int> $aModifiers
     *
     * @return void
     */
    public function setIeHack(array $aModifiers)
    {
        $this->aIeHack = $aModifiers;
    }

    /**
     * @return array<int, int>
     */
    public function getIeHack()
    {
        return $this->aIeHack;
    }

    /**
     * @param bool $bIsImportant
     *
     * @return void
     */
    public function setIsImportant($bIsImportant)
    {
        $this->bIsImportant = $bIsImportant;
    }

    /**
     * @return bool
     */
    public function getIsImportant()
    {
        return $this->bIsImportant;
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
        $sResult = "{$this->sRule}:{$oOutputFormat->spaceAfterRuleName()}";
        if ($this->mValue instanceof Value) { //Can also be a ValueList
            $sResult .= $this->mValue->render($oOutputFormat);
        } else {
            $sResult .= $this->mValue;
        }
        if (!empty($this->aIeHack)) {
            $sResult .= ' \\' . implode('\\', $this->aIeHack);
        }
        if ($this->bIsImportant) {
            $sResult .= ' !important';
        }
        $sResult .= ';';
        return $sResult;
    }

    /**
     * @param array<array-key, Comment> $aComments
     *
     * @return void
     */
    public function addComments(array $aComments)
    {
        $this->aComments = array_merge($this->aComments, $aComments);
    }

    /**
     * @return array<array-key, Comment>
     */
    public function getComments()
    {
        return $this->aComments;
    }

    /**
     * @param array<array-key, Comment> $aComments
     *
     * @return void
     */
    public function setComments(array $aComments)
    {
        $this->aComments = $aComments;
    }
}

<?php

namespace Sabberworm\CSS\CSSList;

use Sabberworm\CSS\OutputFormat;
use Sabberworm\CSS\Parsing\ParserState;
use Sabberworm\CSS\Parsing\SourceException;
use Sabberworm\CSS\Property\Selector;
use Sabberworm\CSS\RuleSet\DeclarationBlock;
use Sabberworm\CSS\RuleSet\RuleSet;
use Sabberworm\CSS\Value\Value;

/**
 * The root `CSSList` of a parsed file. Contains all top-level CSS contents, mostly declaration blocks,
 * but also any at-rules encountered.
 */
class Document extends CSSBlockList
{
    /**
     * @param int $iLineNo
     */
    public function __construct($iLineNo = 0)
    {
        parent::__construct($iLineNo);
    }

    /**
     * @return Document
     *
     * @throws SourceException
     */
    public static function parse(ParserState $oParserState)
    {
        $oDocument = new Document($oParserState->currentLine());
        CSSList::parseList($oParserState, $oDocument);
        return $oDocument;
    }

    /**
     * Gets all `DeclarationBlock` objects recursively.
     *
     * @return array<int, DeclarationBlock>
     */
    public function getAllDeclarationBlocks()
    {
        /** @var array<int, DeclarationBlock> $aResult */
        $aResult = [];
        $this->allDeclarationBlocks($aResult);
        return $aResult;
    }

    /**
     * Gets all `DeclarationBlock` objects recursively.
     *
     * @return array<int, DeclarationBlock>
     *
     * @deprecated will be removed in version 9.0; use `getAllDeclarationBlocks()` instead
     */
    public function getAllSelectors()
    {
        return $this->getAllDeclarationBlocks();
    }

    /**
     * Returns all `RuleSet` objects found recursively in the tree.
     *
     * @return array<int, RuleSet>
     */
    public function getAllRuleSets()
    {
        /** @var array<int, RuleSet> $aResult */
        $aResult = [];
        $this->allRuleSets($aResult);
        return $aResult;
    }

    /**
     * Returns all `Value` objects found recursively in the tree.
     *
     * @param CSSList|RuleSet|string $mElement
     *        the `CSSList` or `RuleSet` to start the search from (defaults to the whole document).
     *        If a string is given, it is used as rule name filter.
     * @param bool $bSearchInFunctionArguments whether to also return Value objects used as Function arguments.
     *
     * @return array<int, Value>
     *
     * @see RuleSet->getRules()
     */
    public function getAllValues($mElement = null, $bSearchInFunctionArguments = false)
    {
        $sSearchString = null;
        if ($mElement === null) {
            $mElement = $this;
        } elseif (is_string($mElement)) {
            $sSearchString = $mElement;
            $mElement = $this;
        }
        /** @var array<int, Value> $aResult */
        $aResult = [];
        $this->allValues($mElement, $aResult, $sSearchString, $bSearchInFunctionArguments);
        return $aResult;
    }

    /**
     * Returns all `Selector` objects found recursively in the tree.
     *
     * Note that this does not yield the full `DeclarationBlock` that the selector belongs to
     * (and, currently, there is no way to get to that).
     *
     * @param string|null $sSpecificitySearch
     *        An optional filter by specificity.
     *        May contain a comparison operator and a number or just a number (defaults to "==").
     *
     * @return array<int, Selector>
     * @example `getSelectorsBySpecificity('>= 100')`
     *
     */
    public function getSelectorsBySpecificity($sSpecificitySearch = null)
    {
        /** @var array<int, Selector> $aResult */
        $aResult = [];
        $this->allSelectors($aResult, $sSpecificitySearch);
        return $aResult;
    }

    /**
     * Expands all shorthand properties to their long value.
     *
     * @return void
     */
    public function expandShorthands()
    {
        foreach ($this->getAllDeclarationBlocks() as $oDeclaration) {
            $oDeclaration->expandShorthands();
        }
    }

    /**
     * Create shorthands properties whenever possible.
     *
     * @return void
     */
    public function createShorthands()
    {
        foreach ($this->getAllDeclarationBlocks() as $oDeclaration) {
            $oDeclaration->createShorthands();
        }
    }

    /**
     * Overrides `render()` to make format argument optional.
     *
     * @param OutputFormat|null $oOutputFormat
     *
     * @return string
     */
    public function render(OutputFormat $oOutputFormat = null)
    {
        if ($oOutputFormat === null) {
            $oOutputFormat = new OutputFormat();
        }
        return parent::render($oOutputFormat);
    }

    /**
     * @return bool
     */
    public function isRootList()
    {
        return true;
    }
}

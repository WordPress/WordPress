<?php

namespace Sabberworm\CSS;

use Sabberworm\CSS\CSSList\Document;
use Sabberworm\CSS\Parsing\ParserState;
use Sabberworm\CSS\Parsing\SourceException;

/**
 * This class parses CSS from text into a data structure.
 */
class Parser
{
    /**
     * @var ParserState
     */
    private $oParserState;

    /**
     * @param string $sText
     * @param Settings|null $oParserSettings
     * @param int $iLineNo the line number (starting from 1, not from 0)
     */
    public function __construct($sText, Settings $oParserSettings = null, $iLineNo = 1)
    {
        if ($oParserSettings === null) {
            $oParserSettings = Settings::create();
        }
        $this->oParserState = new ParserState($sText, $oParserSettings, $iLineNo);
    }

    /**
     * @param string $sCharset
     *
     * @return void
     */
    public function setCharset($sCharset)
    {
        $this->oParserState->setCharset($sCharset);
    }

    /**
     * @return void
     */
    public function getCharset()
    {
        // Note: The `return` statement is missing here. This is a bug that needs to be fixed.
        $this->oParserState->getCharset();
    }

    /**
     * @return Document
     *
     * @throws SourceException
     */
    public function parse()
    {
        return Document::parse($this->oParserState);
    }
}

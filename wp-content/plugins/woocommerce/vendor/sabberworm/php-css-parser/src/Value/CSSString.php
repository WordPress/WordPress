<?php

namespace Sabberworm\CSS\Value;

use Sabberworm\CSS\OutputFormat;
use Sabberworm\CSS\Parsing\ParserState;
use Sabberworm\CSS\Parsing\SourceException;
use Sabberworm\CSS\Parsing\UnexpectedEOFException;
use Sabberworm\CSS\Parsing\UnexpectedTokenException;

class CSSString extends PrimitiveValue
{
    /**
     * @var string
     */
    private $sString;

    /**
     * @param string $sString
     * @param int $iLineNo
     */
    public function __construct($sString, $iLineNo = 0)
    {
        $this->sString = $sString;
        parent::__construct($iLineNo);
    }

    /**
     * @return CSSString
     *
     * @throws SourceException
     * @throws UnexpectedEOFException
     * @throws UnexpectedTokenException
     */
    public static function parse(ParserState $oParserState)
    {
        $sBegin = $oParserState->peek();
        $sQuote = null;
        if ($sBegin === "'") {
            $sQuote = "'";
        } elseif ($sBegin === '"') {
            $sQuote = '"';
        }
        if ($sQuote !== null) {
            $oParserState->consume($sQuote);
        }
        $sResult = "";
        $sContent = null;
        if ($sQuote === null) {
            // Unquoted strings end in whitespace or with braces, brackets, parentheses
            while (!preg_match('/[\\s{}()<>\\[\\]]/isu', $oParserState->peek())) {
                $sResult .= $oParserState->parseCharacter(false);
            }
        } else {
            while (!$oParserState->comes($sQuote)) {
                $sContent = $oParserState->parseCharacter(false);
                if ($sContent === null) {
                    throw new SourceException(
                        "Non-well-formed quoted string {$oParserState->peek(3)}",
                        $oParserState->currentLine()
                    );
                }
                $sResult .= $sContent;
            }
            $oParserState->consume($sQuote);
        }
        return new CSSString($sResult, $oParserState->currentLine());
    }

    /**
     * @param string $sString
     *
     * @return void
     */
    public function setString($sString)
    {
        $this->sString = $sString;
    }

    /**
     * @return string
     */
    public function getString()
    {
        return $this->sString;
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
        $sString = addslashes($this->sString);
        $sString = str_replace("\n", '\A', $sString);
        return $oOutputFormat->getStringQuotingType() . $sString . $oOutputFormat->getStringQuotingType();
    }
}

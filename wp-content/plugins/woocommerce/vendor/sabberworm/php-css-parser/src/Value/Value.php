<?php

namespace Sabberworm\CSS\Value;

use Sabberworm\CSS\Parsing\ParserState;
use Sabberworm\CSS\Parsing\SourceException;
use Sabberworm\CSS\Parsing\UnexpectedEOFException;
use Sabberworm\CSS\Parsing\UnexpectedTokenException;
use Sabberworm\CSS\Renderable;

abstract class Value implements Renderable
{
    /**
     * @var int
     */
    protected $iLineNo;

    /**
     * @param int $iLineNo
     */
    public function __construct($iLineNo = 0)
    {
        $this->iLineNo = $iLineNo;
    }

    /**
     * @param array<array-key, string> $aListDelimiters
     *
     * @return RuleValueList|CSSFunction|CSSString|LineName|Size|URL|string
     *
     * @throws UnexpectedTokenException
     * @throws UnexpectedEOFException
     */
    public static function parseValue(ParserState $oParserState, array $aListDelimiters = [])
    {
        /** @var array<int, RuleValueList|CSSFunction|CSSString|LineName|Size|URL|string> $aStack */
        $aStack = [];
        $oParserState->consumeWhiteSpace();
        //Build a list of delimiters and parsed values
        while (
            !($oParserState->comes('}') || $oParserState->comes(';') || $oParserState->comes('!')
            || $oParserState->comes(')')
            || $oParserState->comes('\\'))
        ) {
            if (count($aStack) > 0) {
                $bFoundDelimiter = false;
                foreach ($aListDelimiters as $sDelimiter) {
                    if ($oParserState->comes($sDelimiter)) {
                        array_push($aStack, $oParserState->consume($sDelimiter));
                        $oParserState->consumeWhiteSpace();
                        $bFoundDelimiter = true;
                        break;
                    }
                }
                if (!$bFoundDelimiter) {
                    //Whitespace was the list delimiter
                    array_push($aStack, ' ');
                }
            }
            array_push($aStack, self::parsePrimitiveValue($oParserState));
            $oParserState->consumeWhiteSpace();
        }
        // Convert the list to list objects
        foreach ($aListDelimiters as $sDelimiter) {
            if (count($aStack) === 1) {
                return $aStack[0];
            }
            $iStartPosition = null;
            while (($iStartPosition = array_search($sDelimiter, $aStack, true)) !== false) {
                $iLength = 2; //Number of elements to be joined
                for ($i = $iStartPosition + 2; $i < count($aStack); $i += 2, ++$iLength) {
                    if ($sDelimiter !== $aStack[$i]) {
                        break;
                    }
                }
                $oList = new RuleValueList($sDelimiter, $oParserState->currentLine());
                for ($i = $iStartPosition - 1; $i - $iStartPosition + 1 < $iLength * 2; $i += 2) {
                    $oList->addListComponent($aStack[$i]);
                }
                array_splice($aStack, $iStartPosition - 1, $iLength * 2 - 1, [$oList]);
            }
        }
        if (!isset($aStack[0])) {
            throw new UnexpectedTokenException(
                " {$oParserState->peek()} ",
                $oParserState->peek(1, -1) . $oParserState->peek(2),
                'literal',
                $oParserState->currentLine()
            );
        }
        return $aStack[0];
    }

    /**
     * @param bool $bIgnoreCase
     *
     * @return CSSFunction|string
     *
     * @throws UnexpectedEOFException
     * @throws UnexpectedTokenException
     */
    public static function parseIdentifierOrFunction(ParserState $oParserState, $bIgnoreCase = false)
    {
        $sResult = $oParserState->parseIdentifier($bIgnoreCase);

        if ($oParserState->comes('(')) {
            $oParserState->consume('(');
            $aArguments = Value::parseValue($oParserState, ['=', ' ', ',']);
            $sResult = new CSSFunction($sResult, $aArguments, ',', $oParserState->currentLine());
            $oParserState->consume(')');
        }

        return $sResult;
    }

    /**
     * @return CSSFunction|CSSString|LineName|Size|URL|string
     *
     * @throws UnexpectedEOFException
     * @throws UnexpectedTokenException
     * @throws SourceException
     */
    public static function parsePrimitiveValue(ParserState $oParserState)
    {
        $oValue = null;
        $oParserState->consumeWhiteSpace();
        if (
            is_numeric($oParserState->peek())
            || ($oParserState->comes('-.')
                && is_numeric($oParserState->peek(1, 2)))
            || (($oParserState->comes('-') || $oParserState->comes('.')) && is_numeric($oParserState->peek(1, 1)))
        ) {
            $oValue = Size::parse($oParserState);
        } elseif ($oParserState->comes('#') || $oParserState->comes('rgb', true) || $oParserState->comes('hsl', true)) {
            $oValue = Color::parse($oParserState);
        } elseif ($oParserState->comes('url', true)) {
            $oValue = URL::parse($oParserState);
        } elseif (
            $oParserState->comes('calc', true) || $oParserState->comes('-webkit-calc', true)
            || $oParserState->comes('-moz-calc', true)
        ) {
            $oValue = CalcFunction::parse($oParserState);
        } elseif ($oParserState->comes("'") || $oParserState->comes('"')) {
            $oValue = CSSString::parse($oParserState);
        } elseif ($oParserState->comes("progid:") && $oParserState->getSettings()->bLenientParsing) {
            $oValue = self::parseMicrosoftFilter($oParserState);
        } elseif ($oParserState->comes("[")) {
            $oValue = LineName::parse($oParserState);
        } elseif ($oParserState->comes("U+")) {
            $oValue = self::parseUnicodeRangeValue($oParserState);
        } else {
            $oValue = self::parseIdentifierOrFunction($oParserState);
        }
        $oParserState->consumeWhiteSpace();
        return $oValue;
    }

    /**
     * @return CSSFunction
     *
     * @throws UnexpectedEOFException
     * @throws UnexpectedTokenException
     */
    private static function parseMicrosoftFilter(ParserState $oParserState)
    {
        $sFunction = $oParserState->consumeUntil('(', false, true);
        $aArguments = Value::parseValue($oParserState, [',', '=']);
        return new CSSFunction($sFunction, $aArguments, ',', $oParserState->currentLine());
    }

    /**
     * @return string
     *
     * @throws UnexpectedEOFException
     * @throws UnexpectedTokenException
     */
    private static function parseUnicodeRangeValue(ParserState $oParserState)
    {
        $iCodepointMaxLength = 6; // Code points outside BMP can use up to six digits
        $sRange = "";
        $oParserState->consume("U+");
        do {
            if ($oParserState->comes('-')) {
                $iCodepointMaxLength = 13; // Max length is 2 six digit code points + the dash(-) between them
            }
            $sRange .= $oParserState->consume(1);
        } while (strlen($sRange) < $iCodepointMaxLength && preg_match("/[A-Fa-f0-9\?-]/", $oParserState->peek()));
        return "U+{$sRange}";
    }

    /**
     * @return int
     */
    public function getLineNo()
    {
        return $this->iLineNo;
    }
}

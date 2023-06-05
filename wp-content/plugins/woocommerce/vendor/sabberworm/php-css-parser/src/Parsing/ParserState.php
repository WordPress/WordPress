<?php

namespace Sabberworm\CSS\Parsing;

use Sabberworm\CSS\Comment\Comment;
use Sabberworm\CSS\Settings;

class ParserState
{
    /**
     * @var null
     */
    const EOF = null;

    /**
     * @var Settings
     */
    private $oParserSettings;

    /**
     * @var string
     */
    private $sText;

    /**
     * @var array<int, string>
     */
    private $aText;

    /**
     * @var int
     */
    private $iCurrentPosition;

    /**
     * @var string
     */
    private $sCharset;

    /**
     * @var int
     */
    private $iLength;

    /**
     * @var int
     */
    private $iLineNo;

    /**
     * @param string $sText
     * @param int $iLineNo
     */
    public function __construct($sText, Settings $oParserSettings, $iLineNo = 1)
    {
        $this->oParserSettings = $oParserSettings;
        $this->sText = $sText;
        $this->iCurrentPosition = 0;
        $this->iLineNo = $iLineNo;
        $this->setCharset($this->oParserSettings->sDefaultCharset);
    }

    /**
     * @param string $sCharset
     *
     * @return void
     */
    public function setCharset($sCharset)
    {
        $this->sCharset = $sCharset;
        $this->aText = $this->strsplit($this->sText);
        if (is_array($this->aText)) {
            $this->iLength = count($this->aText);
        }
    }

    /**
     * @return string
     */
    public function getCharset()
    {
        return $this->sCharset;
    }

    /**
     * @return int
     */
    public function currentLine()
    {
        return $this->iLineNo;
    }

    /**
     * @return int
     */
    public function currentColumn()
    {
        return $this->iCurrentPosition;
    }

    /**
     * @return Settings
     */
    public function getSettings()
    {
        return $this->oParserSettings;
    }

    /**
     * @param bool $bIgnoreCase
     *
     * @return string
     *
     * @throws UnexpectedTokenException
     */
    public function parseIdentifier($bIgnoreCase = true)
    {
        $sResult = $this->parseCharacter(true);
        if ($sResult === null) {
            throw new UnexpectedTokenException($sResult, $this->peek(5), 'identifier', $this->iLineNo);
        }
        $sCharacter = null;
        while (($sCharacter = $this->parseCharacter(true)) !== null) {
            if (preg_match('/[a-zA-Z0-9\x{00A0}-\x{FFFF}_-]/Sux', $sCharacter)) {
                $sResult .= $sCharacter;
            } else {
                $sResult .= '\\' . $sCharacter;
            }
        }
        if ($bIgnoreCase) {
            $sResult = $this->strtolower($sResult);
        }
        return $sResult;
    }

    /**
     * @param bool $bIsForIdentifier
     *
     * @return string|null
     *
     * @throws UnexpectedEOFException
     * @throws UnexpectedTokenException
     */
    public function parseCharacter($bIsForIdentifier)
    {
        if ($this->peek() === '\\') {
            if (
                $bIsForIdentifier && $this->oParserSettings->bLenientParsing
                && ($this->comes('\0') || $this->comes('\9'))
            ) {
                // Non-strings can contain \0 or \9 which is an IE hack supported in lenient parsing.
                return null;
            }
            $this->consume('\\');
            if ($this->comes('\n') || $this->comes('\r')) {
                return '';
            }
            if (preg_match('/[0-9a-fA-F]/Su', $this->peek()) === 0) {
                return $this->consume(1);
            }
            $sUnicode = $this->consumeExpression('/^[0-9a-fA-F]{1,6}/u', 6);
            if ($this->strlen($sUnicode) < 6) {
                // Consume whitespace after incomplete unicode escape
                if (preg_match('/\\s/isSu', $this->peek())) {
                    if ($this->comes('\r\n')) {
                        $this->consume(2);
                    } else {
                        $this->consume(1);
                    }
                }
            }
            $iUnicode = intval($sUnicode, 16);
            $sUtf32 = "";
            for ($i = 0; $i < 4; ++$i) {
                $sUtf32 .= chr($iUnicode & 0xff);
                $iUnicode = $iUnicode >> 8;
            }
            return iconv('utf-32le', $this->sCharset, $sUtf32);
        }
        if ($bIsForIdentifier) {
            $peek = ord($this->peek());
            // Ranges: a-z A-Z 0-9 - _
            if (
                ($peek >= 97 && $peek <= 122)
                || ($peek >= 65 && $peek <= 90)
                || ($peek >= 48 && $peek <= 57)
                || ($peek === 45)
                || ($peek === 95)
                || ($peek > 0xa1)
            ) {
                return $this->consume(1);
            }
        } else {
            return $this->consume(1);
        }
        return null;
    }

    /**
     * @return array<int, Comment>|void
     *
     * @throws UnexpectedEOFException
     * @throws UnexpectedTokenException
     */
    public function consumeWhiteSpace()
    {
        $comments = [];
        do {
            while (preg_match('/\\s/isSu', $this->peek()) === 1) {
                $this->consume(1);
            }
            if ($this->oParserSettings->bLenientParsing) {
                try {
                    $oComment = $this->consumeComment();
                } catch (UnexpectedEOFException $e) {
                    $this->iCurrentPosition = $this->iLength;
                    return;
                }
            } else {
                $oComment = $this->consumeComment();
            }
            if ($oComment !== false) {
                $comments[] = $oComment;
            }
        } while ($oComment !== false);
        return $comments;
    }

    /**
     * @param string $sString
     * @param bool $bCaseInsensitive
     *
     * @return bool
     */
    public function comes($sString, $bCaseInsensitive = false)
    {
        $sPeek = $this->peek(strlen($sString));
        return ($sPeek == '')
            ? false
            : $this->streql($sPeek, $sString, $bCaseInsensitive);
    }

    /**
     * @param int $iLength
     * @param int $iOffset
     *
     * @return string
     */
    public function peek($iLength = 1, $iOffset = 0)
    {
        $iOffset += $this->iCurrentPosition;
        if ($iOffset >= $this->iLength) {
            return '';
        }
        return $this->substr($iOffset, $iLength);
    }

    /**
     * @param int $mValue
     *
     * @return string
     *
     * @throws UnexpectedEOFException
     * @throws UnexpectedTokenException
     */
    public function consume($mValue = 1)
    {
        if (is_string($mValue)) {
            $iLineCount = substr_count($mValue, "\n");
            $iLength = $this->strlen($mValue);
            if (!$this->streql($this->substr($this->iCurrentPosition, $iLength), $mValue)) {
                throw new UnexpectedTokenException($mValue, $this->peek(max($iLength, 5)), $this->iLineNo);
            }
            $this->iLineNo += $iLineCount;
            $this->iCurrentPosition += $this->strlen($mValue);
            return $mValue;
        } else {
            if ($this->iCurrentPosition + $mValue > $this->iLength) {
                throw new UnexpectedEOFException($mValue, $this->peek(5), 'count', $this->iLineNo);
            }
            $sResult = $this->substr($this->iCurrentPosition, $mValue);
            $iLineCount = substr_count($sResult, "\n");
            $this->iLineNo += $iLineCount;
            $this->iCurrentPosition += $mValue;
            return $sResult;
        }
    }

    /**
     * @param string $mExpression
     * @param int|null $iMaxLength
     *
     * @return string
     *
     * @throws UnexpectedEOFException
     * @throws UnexpectedTokenException
     */
    public function consumeExpression($mExpression, $iMaxLength = null)
    {
        $aMatches = null;
        $sInput = $iMaxLength !== null ? $this->peek($iMaxLength) : $this->inputLeft();
        if (preg_match($mExpression, $sInput, $aMatches, PREG_OFFSET_CAPTURE) === 1) {
            return $this->consume($aMatches[0][0]);
        }
        throw new UnexpectedTokenException($mExpression, $this->peek(5), 'expression', $this->iLineNo);
    }

    /**
     * @return Comment|false
     */
    public function consumeComment()
    {
        $mComment = false;
        if ($this->comes('/*')) {
            $iLineNo = $this->iLineNo;
            $this->consume(1);
            $mComment = '';
            while (($char = $this->consume(1)) !== '') {
                $mComment .= $char;
                if ($this->comes('*/')) {
                    $this->consume(2);
                    break;
                }
            }
        }

        if ($mComment !== false) {
            // We skip the * which was included in the comment.
            return new Comment(substr($mComment, 1), $iLineNo);
        }

        return $mComment;
    }

    /**
     * @return bool
     */
    public function isEnd()
    {
        return $this->iCurrentPosition >= $this->iLength;
    }

    /**
     * @param array<array-key, string>|string $aEnd
     * @param string $bIncludeEnd
     * @param string $consumeEnd
     * @param array<int, Comment> $comments
     *
     * @return string
     *
     * @throws UnexpectedEOFException
     * @throws UnexpectedTokenException
     */
    public function consumeUntil($aEnd, $bIncludeEnd = false, $consumeEnd = false, array &$comments = [])
    {
        $aEnd = is_array($aEnd) ? $aEnd : [$aEnd];
        $out = '';
        $start = $this->iCurrentPosition;

        while (!$this->isEnd()) {
            $char = $this->consume(1);
            if (in_array($char, $aEnd)) {
                if ($bIncludeEnd) {
                    $out .= $char;
                } elseif (!$consumeEnd) {
                    $this->iCurrentPosition -= $this->strlen($char);
                }
                return $out;
            }
            $out .= $char;
            if ($comment = $this->consumeComment()) {
                $comments[] = $comment;
            }
        }

        if (in_array(self::EOF, $aEnd)) {
            return $out;
        }

        $this->iCurrentPosition = $start;
        throw new UnexpectedEOFException(
            'One of ("' . implode('","', $aEnd) . '")',
            $this->peek(5),
            'search',
            $this->iLineNo
        );
    }

    /**
     * @return string
     */
    private function inputLeft()
    {
        return $this->substr($this->iCurrentPosition, -1);
    }

    /**
     * @param string $sString1
     * @param string $sString2
     * @param bool $bCaseInsensitive
     *
     * @return bool
     */
    public function streql($sString1, $sString2, $bCaseInsensitive = true)
    {
        if ($bCaseInsensitive) {
            return $this->strtolower($sString1) === $this->strtolower($sString2);
        } else {
            return $sString1 === $sString2;
        }
    }

    /**
     * @param int $iAmount
     *
     * @return void
     */
    public function backtrack($iAmount)
    {
        $this->iCurrentPosition -= $iAmount;
    }

    /**
     * @param string $sString
     *
     * @return int
     */
    public function strlen($sString)
    {
        if ($this->oParserSettings->bMultibyteSupport) {
            return mb_strlen($sString, $this->sCharset);
        } else {
            return strlen($sString);
        }
    }

    /**
     * @param int $iStart
     * @param int $iLength
     *
     * @return string
     */
    private function substr($iStart, $iLength)
    {
        if ($iLength < 0) {
            $iLength = $this->iLength - $iStart + $iLength;
        }
        if ($iStart + $iLength > $this->iLength) {
            $iLength = $this->iLength - $iStart;
        }
        $sResult = '';
        while ($iLength > 0) {
            $sResult .= $this->aText[$iStart];
            $iStart++;
            $iLength--;
        }
        return $sResult;
    }

    /**
     * @param string $sString
     *
     * @return string
     */
    private function strtolower($sString)
    {
        if ($this->oParserSettings->bMultibyteSupport) {
            return mb_strtolower($sString, $this->sCharset);
        } else {
            return strtolower($sString);
        }
    }

    /**
     * @param string $sString
     *
     * @return array<int, string>
     */
    private function strsplit($sString)
    {
        if ($this->oParserSettings->bMultibyteSupport) {
            if ($this->streql($this->sCharset, 'utf-8')) {
                return preg_split('//u', $sString, -1, PREG_SPLIT_NO_EMPTY);
            } else {
                $iLength = mb_strlen($sString, $this->sCharset);
                $aResult = [];
                for ($i = 0; $i < $iLength; ++$i) {
                    $aResult[] = mb_substr($sString, $i, 1, $this->sCharset);
                }
                return $aResult;
            }
        } else {
            if ($sString === '') {
                return [];
            } else {
                return str_split($sString);
            }
        }
    }

    /**
     * @param string $sString
     * @param string $sNeedle
     * @param int $iOffset
     *
     * @return int|false
     */
    private function strpos($sString, $sNeedle, $iOffset)
    {
        if ($this->oParserSettings->bMultibyteSupport) {
            return mb_strpos($sString, $sNeedle, $iOffset, $this->sCharset);
        } else {
            return strpos($sString, $sNeedle, $iOffset);
        }
    }
}

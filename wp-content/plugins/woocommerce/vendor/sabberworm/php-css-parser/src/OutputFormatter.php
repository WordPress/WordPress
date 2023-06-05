<?php

namespace Sabberworm\CSS;

use Sabberworm\CSS\Parsing\OutputException;

class OutputFormatter
{
    /**
     * @var OutputFormat
     */
    private $oFormat;

    public function __construct(OutputFormat $oFormat)
    {
        $this->oFormat = $oFormat;
    }

    /**
     * @param string $sName
     * @param string|null $sType
     *
     * @return string
     */
    public function space($sName, $sType = null)
    {
        $sSpaceString = $this->oFormat->get("Space$sName");
        // If $sSpaceString is an array, we have multiple values configured
        // depending on the type of object the space applies to
        if (is_array($sSpaceString)) {
            if ($sType !== null && isset($sSpaceString[$sType])) {
                $sSpaceString = $sSpaceString[$sType];
            } else {
                $sSpaceString = reset($sSpaceString);
            }
        }
        return $this->prepareSpace($sSpaceString);
    }

    /**
     * @return string
     */
    public function spaceAfterRuleName()
    {
        return $this->space('AfterRuleName');
    }

    /**
     * @return string
     */
    public function spaceBeforeRules()
    {
        return $this->space('BeforeRules');
    }

    /**
     * @return string
     */
    public function spaceAfterRules()
    {
        return $this->space('AfterRules');
    }

    /**
     * @return string
     */
    public function spaceBetweenRules()
    {
        return $this->space('BetweenRules');
    }

    /**
     * @return string
     */
    public function spaceBeforeBlocks()
    {
        return $this->space('BeforeBlocks');
    }

    /**
     * @return string
     */
    public function spaceAfterBlocks()
    {
        return $this->space('AfterBlocks');
    }

    /**
     * @return string
     */
    public function spaceBetweenBlocks()
    {
        return $this->space('BetweenBlocks');
    }

    /**
     * @return string
     */
    public function spaceBeforeSelectorSeparator()
    {
        return $this->space('BeforeSelectorSeparator');
    }

    /**
     * @return string
     */
    public function spaceAfterSelectorSeparator()
    {
        return $this->space('AfterSelectorSeparator');
    }

    /**
     * @param string $sSeparator
     *
     * @return string
     */
    public function spaceBeforeListArgumentSeparator($sSeparator)
    {
        return $this->space('BeforeListArgumentSeparator', $sSeparator);
    }

    /**
     * @param string $sSeparator
     *
     * @return string
     */
    public function spaceAfterListArgumentSeparator($sSeparator)
    {
        return $this->space('AfterListArgumentSeparator', $sSeparator);
    }

    /**
     * @return string
     */
    public function spaceBeforeOpeningBrace()
    {
        return $this->space('BeforeOpeningBrace');
    }

    /**
     * Runs the given code, either swallowing or passing exceptions, depending on the `bIgnoreExceptions` setting.
     *
     * @param string $cCode the name of the function to call
     *
     * @return string|null
     */
    public function safely($cCode)
    {
        if ($this->oFormat->get('IgnoreExceptions')) {
            // If output exceptions are ignored, run the code with exception guards
            try {
                return $cCode();
            } catch (OutputException $e) {
                return null;
            } // Do nothing
        } else {
            // Run the code as-is
            return $cCode();
        }
    }

    /**
     * Clone of the `implode` function, but calls `render` with the current output format instead of `__toString()`.
     *
     * @param string $sSeparator
     * @param array<array-key, Renderable|string> $aValues
     * @param bool $bIncreaseLevel
     *
     * @return string
     */
    public function implode($sSeparator, array $aValues, $bIncreaseLevel = false)
    {
        $sResult = '';
        $oFormat = $this->oFormat;
        if ($bIncreaseLevel) {
            $oFormat = $oFormat->nextLevel();
        }
        $bIsFirst = true;
        foreach ($aValues as $mValue) {
            if ($bIsFirst) {
                $bIsFirst = false;
            } else {
                $sResult .= $sSeparator;
            }
            if ($mValue instanceof Renderable) {
                $sResult .= $mValue->render($oFormat);
            } else {
                $sResult .= $mValue;
            }
        }
        return $sResult;
    }

    /**
     * @param string $sString
     *
     * @return string
     */
    public function removeLastSemicolon($sString)
    {
        if ($this->oFormat->get('SemicolonAfterLastRule')) {
            return $sString;
        }
        $sString = explode(';', $sString);
        if (count($sString) < 2) {
            return $sString[0];
        }
        $sLast = array_pop($sString);
        $sNextToLast = array_pop($sString);
        array_push($sString, $sNextToLast . $sLast);
        return implode(';', $sString);
    }

    /**
     * @param string $sSpaceString
     *
     * @return string
     */
    private function prepareSpace($sSpaceString)
    {
        return str_replace("\n", "\n" . $this->indent(), $sSpaceString);
    }

    /**
     * @return string
     */
    private function indent()
    {
        return str_repeat($this->oFormat->sIndentation, $this->oFormat->level());
    }
}

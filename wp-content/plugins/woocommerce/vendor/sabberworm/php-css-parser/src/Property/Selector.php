<?php

namespace Sabberworm\CSS\Property;

/**
 * Class representing a single CSS selector. Selectors have to be split by the comma prior to being passed into this
 * class.
 */
class Selector
{
    /**
     * regexp for specificity calculations
     *
     * @var string
     */
    const NON_ID_ATTRIBUTES_AND_PSEUDO_CLASSES_RX = '/
        (\.[\w]+)                   # classes
        |
        \[(\w+)                     # attributes
        |
        (\:(                        # pseudo classes
            link|visited|active
            |hover|focus
            |lang
            |target
            |enabled|disabled|checked|indeterminate
            |root
            |nth-child|nth-last-child|nth-of-type|nth-last-of-type
            |first-child|last-child|first-of-type|last-of-type
            |only-child|only-of-type
            |empty|contains
        ))
        /ix';

    /**
     * regexp for specificity calculations
     *
     * @var string
     */
    const ELEMENTS_AND_PSEUDO_ELEMENTS_RX = '/
        ((^|[\s\+\>\~]+)[\w]+   # elements
        |
        \:{1,2}(                # pseudo-elements
            after|before|first-letter|first-line|selection
        ))
        /ix';

    /**
     * regexp for specificity calculations
     *
     * @var string
     */
    const SELECTOR_VALIDATION_RX = '/
        ^(
            (?:
                [a-zA-Z0-9\x{00A0}-\x{FFFF}_^$|*="\'~\[\]()\-\s\.:#+>]* # any sequence of valid unescaped characters
                (?:\\\\.)?                                              # a single escaped character
                (?:([\'"]).*?(?<!\\\\)\2)?                              # a quoted text like [id="example"]
            )*
        )$
        /ux';

    /**
     * @var string
     */
    private $sSelector;

    /**
     * @var int|null
     */
    private $iSpecificity;

    /**
     * @param string $sSelector
     *
     * @return bool
     */
    public static function isValid($sSelector)
    {
        return preg_match(static::SELECTOR_VALIDATION_RX, $sSelector);
    }

    /**
     * @param string $sSelector
     * @param bool $bCalculateSpecificity
     */
    public function __construct($sSelector, $bCalculateSpecificity = false)
    {
        $this->setSelector($sSelector);
        if ($bCalculateSpecificity) {
            $this->getSpecificity();
        }
    }

    /**
     * @return string
     */
    public function getSelector()
    {
        return $this->sSelector;
    }

    /**
     * @param string $sSelector
     *
     * @return void
     */
    public function setSelector($sSelector)
    {
        $this->sSelector = trim($sSelector);
        $this->iSpecificity = null;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getSelector();
    }

    /**
     * @return int
     */
    public function getSpecificity()
    {
        if ($this->iSpecificity === null) {
            $a = 0;
            /// @todo should exclude \# as well as "#"
            $aMatches = null;
            $b = substr_count($this->sSelector, '#');
            $c = preg_match_all(self::NON_ID_ATTRIBUTES_AND_PSEUDO_CLASSES_RX, $this->sSelector, $aMatches);
            $d = preg_match_all(self::ELEMENTS_AND_PSEUDO_ELEMENTS_RX, $this->sSelector, $aMatches);
            $this->iSpecificity = ($a * 1000) + ($b * 100) + ($c * 10) + $d;
        }
        return $this->iSpecificity;
    }
}

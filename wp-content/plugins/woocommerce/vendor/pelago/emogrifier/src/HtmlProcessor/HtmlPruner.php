<?php

declare(strict_types=1);

namespace Pelago\Emogrifier\HtmlProcessor;

use Pelago\Emogrifier\CssInliner;
use Pelago\Emogrifier\Utilities\ArrayIntersector;

/**
 * This class can remove things from HTML.
 */
class HtmlPruner extends AbstractHtmlProcessor
{
    /**
     * We need to look for display:none, but we need to do a case-insensitive search. Since DOMDocument only
     * supports XPath 1.0, lower-case() isn't available to us. We've thus far only set attributes to lowercase,
     * not attribute values. Consequently, we need to translate() the letters that would be in 'NONE' ("NOE")
     * to lowercase.
     *
     * @var string
     */
    private const DISPLAY_NONE_MATCHER
        = '//*[@style and contains(translate(translate(@style," ",""),"NOE","noe"),"display:none")'
        . ' and not(@class and contains(concat(" ", normalize-space(@class), " "), " -emogrifier-keep "))]';

    /**
     * Removes elements that have a "display: none;" style.
     *
     * @return self fluent interface
     */
    public function removeElementsWithDisplayNone(): self
    {
        $elementsWithStyleDisplayNone = $this->getXPath()->query(self::DISPLAY_NONE_MATCHER);
        if ($elementsWithStyleDisplayNone->length === 0) {
            return $this;
        }

        foreach ($elementsWithStyleDisplayNone as $element) {
            $parentNode = $element->parentNode;
            if ($parentNode !== null) {
                $parentNode->removeChild($element);
            }
        }

        return $this;
    }

    /**
     * Removes classes that are no longer required (e.g. because there are no longer any CSS rules that reference them)
     * from `class` attributes.
     *
     * Note that this does not inspect the CSS, but expects to be provided with a list of classes that are still in use.
     *
     * This method also has the (presumably beneficial) side-effect of minifying (removing superfluous whitespace from)
     * `class` attributes.
     *
     * @param array<array-key, string> $classesToKeep names of classes that should not be removed
     *
     * @return self fluent interface
     */
    public function removeRedundantClasses(array $classesToKeep = []): self
    {
        $elementsWithClassAttribute = $this->getXPath()->query('//*[@class]');

        if ($classesToKeep !== []) {
            $this->removeClassesFromElements($elementsWithClassAttribute, $classesToKeep);
        } else {
            // Avoid unnecessary processing if there are no classes to keep.
            $this->removeClassAttributeFromElements($elementsWithClassAttribute);
        }

        return $this;
    }

    /**
     * Removes classes from the `class` attribute of each element in `$elements`, except any in `$classesToKeep`,
     * removing the `class` attribute itself if the resultant list is empty.
     *
     * @param \DOMNodeList $elements
     * @param array<array-key, string> $classesToKeep
     */
    private function removeClassesFromElements(\DOMNodeList $elements, array $classesToKeep): void
    {
        $classesToKeepIntersector = new ArrayIntersector($classesToKeep);

        /** @var \DOMElement $element */
        foreach ($elements as $element) {
            $elementClasses = \preg_split('/\\s++/', \trim($element->getAttribute('class')));
            $elementClassesToKeep = $classesToKeepIntersector->intersectWith($elementClasses);
            if ($elementClassesToKeep !== []) {
                $element->setAttribute('class', \implode(' ', $elementClassesToKeep));
            } else {
                $element->removeAttribute('class');
            }
        }
    }

    /**
     * Removes the `class` attribute from each element in `$elements`.
     *
     * @param \DOMNodeList $elements
     */
    private function removeClassAttributeFromElements(\DOMNodeList $elements): void
    {
        /** @var \DOMElement $element */
        foreach ($elements as $element) {
            $element->removeAttribute('class');
        }
    }

    /**
     * After CSS has been inlined, there will likely be some classes in `class` attributes that are no longer referenced
     * by any remaining (uninlinable) CSS.  This method removes such classes.
     *
     * Note that it does not inspect the remaining CSS, but uses information readily available from the `CssInliner`
     * instance about the CSS rules that could not be inlined.
     *
     * @param CssInliner $cssInliner object instance that performed the CSS inlining
     *
     * @return self fluent interface
     *
     * @throws \BadMethodCallException if `inlineCss` has not first been called on `$cssInliner`
     */
    public function removeRedundantClassesAfterCssInlined(CssInliner $cssInliner): self
    {
        $classesToKeepAsKeys = [];
        foreach ($cssInliner->getMatchingUninlinableSelectors() as $selector) {
            \preg_match_all('/\\.(-?+[_a-zA-Z][\\w\\-]*+)/', $selector, $matches);
            $classesToKeepAsKeys += \array_fill_keys($matches[1], true);
        }

        $this->removeRedundantClasses(\array_keys($classesToKeepAsKeys));

        return $this;
    }
}

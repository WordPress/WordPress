<?php

declare(strict_types=1);

namespace Pelago\Emogrifier\HtmlProcessor;

/**
 * This HtmlProcessor can convert style HTML attributes to the corresponding other visual HTML attributes,
 * e.g. it converts style="width: 100px" to width="100".
 *
 * It will only add attributes, but leaves the style attribute untouched.
 *
 * To trigger the conversion, call the convertCssToVisualAttributes method.
 */
class CssToAttributeConverter extends AbstractHtmlProcessor
{
    /**
     * This multi-level array contains simple mappings of CSS properties to
     * HTML attributes. If a mapping only applies to certain HTML nodes or
     * only for certain values, the mapping is an object with a whitelist
     * of nodes and values.
     *
     * @var array<string, array{attribute: string, nodes?: array<int, string>, values?: array<int, string>}>
     */
    private $cssToHtmlMap = [
        'background-color' => [
            'attribute' => 'bgcolor',
        ],
        'text-align' => [
            'attribute' => 'align',
            'nodes' => ['p', 'div', 'td', 'th'],
            'values' => ['left', 'right', 'center', 'justify'],
        ],
        'float' => [
            'attribute' => 'align',
            'nodes' => ['table', 'img'],
            'values' => ['left', 'right'],
        ],
        'border-spacing' => [
            'attribute' => 'cellspacing',
            'nodes' => ['table'],
        ],
    ];

    /**
     * @var array<string, array<string, string>>
     */
    private static $parsedCssCache = [];

    /**
     * Maps the CSS from the style nodes to visual HTML attributes.
     *
     * @return self fluent interface
     */
    public function convertCssToVisualAttributes(): self
    {
        /** @var \DOMElement $node */
        foreach ($this->getAllNodesWithStyleAttribute() as $node) {
            $inlineStyleDeclarations = $this->parseCssDeclarationsBlock($node->getAttribute('style'));
            $this->mapCssToHtmlAttributes($inlineStyleDeclarations, $node);
        }

        return $this;
    }

    /**
     * Returns a list with all DOM nodes that have a style attribute.
     *
     * @return \DOMNodeList
     */
    private function getAllNodesWithStyleAttribute(): \DOMNodeList
    {
        return $this->getXPath()->query('//*[@style]');
    }

    /**
     * Parses a CSS declaration block into property name/value pairs.
     *
     * Example:
     *
     * The declaration block
     *
     *   "color: #000; font-weight: bold;"
     *
     * will be parsed into the following array:
     *
     *   "color" => "#000"
     *   "font-weight" => "bold"
     *
     * @param string $cssDeclarationsBlock the CSS declarations block without the curly braces, may be empty
     *
     * @return array<string, string>
     *         the CSS declarations with the property names as array keys and the property values as array values
     */
    private function parseCssDeclarationsBlock(string $cssDeclarationsBlock): array
    {
        if (isset(self::$parsedCssCache[$cssDeclarationsBlock])) {
            return self::$parsedCssCache[$cssDeclarationsBlock];
        }

        $properties = [];
        foreach (\preg_split('/;(?!base64|charset)/', $cssDeclarationsBlock) as $declaration) {
            /** @var array<int, string> $matches */
            $matches = [];
            if (!\preg_match('/^([A-Za-z\\-]+)\\s*:\\s*(.+)$/s', \trim($declaration), $matches)) {
                continue;
            }

            $propertyName = \strtolower($matches[1]);
            $propertyValue = $matches[2];
            $properties[$propertyName] = $propertyValue;
        }
        self::$parsedCssCache[$cssDeclarationsBlock] = $properties;

        return $properties;
    }

    /**
     * Applies $styles to $node.
     *
     * This method maps CSS styles to HTML attributes and adds those to the
     * node.
     *
     * @param array<string, string> $styles the new CSS styles taken from the global styles to be applied to this node
     * @param \DOMElement $node node to apply styles to
     */
    private function mapCssToHtmlAttributes(array $styles, \DOMElement $node): void
    {
        foreach ($styles as $property => $value) {
            // Strip !important indicator
            $value = \trim(\str_replace('!important', '', $value));
            $this->mapCssToHtmlAttribute($property, $value, $node);
        }
    }

    /**
     * Tries to apply the CSS style to $node as an attribute.
     *
     * This method maps a CSS rule to HTML attributes and adds those to the node.
     *
     * @param string $property the name of the CSS property to map
     * @param string $value the value of the style rule to map
     * @param \DOMElement $node node to apply styles to
     */
    private function mapCssToHtmlAttribute(string $property, string $value, \DOMElement $node): void
    {
        if (!$this->mapSimpleCssProperty($property, $value, $node)) {
            $this->mapComplexCssProperty($property, $value, $node);
        }
    }

    /**
     * Looks up the CSS property in the mapping table and maps it if it matches the conditions.
     *
     * @param string $property the name of the CSS property to map
     * @param string $value the value of the style rule to map
     * @param \DOMElement $node node to apply styles to
     *
     * @return bool true if the property can be mapped using the simple mapping table
     */
    private function mapSimpleCssProperty(string $property, string $value, \DOMElement $node): bool
    {
        if (!isset($this->cssToHtmlMap[$property])) {
            return false;
        }

        $mapping = $this->cssToHtmlMap[$property];
        $nodesMatch = !isset($mapping['nodes']) || \in_array($node->nodeName, $mapping['nodes'], true);
        $valuesMatch = !isset($mapping['values']) || \in_array($value, $mapping['values'], true);
        $canBeMapped = $nodesMatch && $valuesMatch;
        if ($canBeMapped) {
            $node->setAttribute($mapping['attribute'], $value);
        }

        return $canBeMapped;
    }

    /**
     * Maps CSS properties that need special transformation to an HTML attribute.
     *
     * @param string $property the name of the CSS property to map
     * @param string $value the value of the style rule to map
     * @param \DOMElement $node node to apply styles to
     */
    private function mapComplexCssProperty(string $property, string $value, \DOMElement $node): void
    {
        switch ($property) {
            case 'background':
                $this->mapBackgroundProperty($node, $value);
                break;
            case 'width':
                // intentional fall-through
            case 'height':
                $this->mapWidthOrHeightProperty($node, $value, $property);
                break;
            case 'margin':
                $this->mapMarginProperty($node, $value);
                break;
            case 'border':
                $this->mapBorderProperty($node, $value);
                break;
            default:
        }
    }

    /**
     * @param \DOMElement $node node to apply styles to
     * @param string $value the value of the style rule to map
     */
    private function mapBackgroundProperty(\DOMElement $node, string $value): void
    {
        // parse out the color, if any
        /** @var array<int, string> $styles */
        $styles = \explode(' ', $value, 2);
        $first = $styles[0];
        if (\is_numeric($first[0]) || \strncmp($first, 'url', 3) === 0) {
            return;
        }

        // as this is not a position or image, assume it's a color
        $node->setAttribute('bgcolor', $first);
    }

    /**
     * @param \DOMElement $node node to apply styles to
     * @param string $value the value of the style rule to map
     * @param string $property the name of the CSS property to map
     */
    private function mapWidthOrHeightProperty(\DOMElement $node, string $value, string $property): void
    {
        // only parse values in px and %, but not values like "auto"
        if (!\preg_match('/^(\\d+)(\\.(\\d+))?(px|%)$/', $value)) {
            return;
        }

        $number = \preg_replace('/[^0-9.%]/', '', $value);
        $node->setAttribute($property, $number);
    }

    /**
     * @param \DOMElement $node node to apply styles to
     * @param string $value the value of the style rule to map
     */
    private function mapMarginProperty(\DOMElement $node, string $value): void
    {
        if (!$this->isTableOrImageNode($node)) {
            return;
        }

        $margins = $this->parseCssShorthandValue($value);
        if ($margins['left'] === 'auto' && $margins['right'] === 'auto') {
            $node->setAttribute('align', 'center');
        }
    }

    /**
     * @param \DOMElement $node node to apply styles to
     * @param string $value the value of the style rule to map
     */
    private function mapBorderProperty(\DOMElement $node, string $value): void
    {
        if (!$this->isTableOrImageNode($node)) {
            return;
        }

        if ($value === 'none' || $value === '0') {
            $node->setAttribute('border', '0');
        }
    }

    /**
     * @param \DOMElement $node
     *
     * @return bool
     */
    private function isTableOrImageNode(\DOMElement $node): bool
    {
        return $node->nodeName === 'table' || $node->nodeName === 'img';
    }

    /**
     * Parses a shorthand CSS value and splits it into individual values.  For example: `padding: 0 auto;` - `0 auto` is
     * split into top: 0, left: auto, bottom: 0, right: auto.
     *
     * @param string $value a CSS property value with 1, 2, 3 or 4 sizes
     *
     * @return array<string, string>
     *         an array of values for top, right, bottom and left (using these as associative array keys)
     */
    private function parseCssShorthandValue(string $value): array
    {
        /** @var array<int, string> $values */
        $values = \preg_split('/\\s+/', $value);

        $css = [];
        $css['top'] = $values[0];
        $css['right'] = (\count($values) > 1) ? $values[1] : $css['top'];
        $css['bottom'] = (\count($values) > 2) ? $values[2] : $css['top'];
        $css['left'] = (\count($values) > 3) ? $values[3] : $css['right'];

        return $css;
    }
}

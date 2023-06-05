<?php

declare(strict_types=1);

namespace Pelago\Emogrifier\HtmlProcessor;

/**
 * Base class for HTML processor that e.g., can remove, add or modify nodes or attributes.
 *
 * The "vanilla" subclass is the HtmlNormalizer.
 *
 * @psalm-consistent-constructor
 */
abstract class AbstractHtmlProcessor
{
    /**
     * @var string
     */
    protected const DEFAULT_DOCUMENT_TYPE = '<!DOCTYPE html>';

    /**
     * @var string
     */
    protected const CONTENT_TYPE_META_TAG = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';

    /**
     * @var string Regular expression part to match tag names that PHP's DOMDocument implementation is not aware are
     *      self-closing. These are mostly HTML5 elements, but for completeness <command> (obsolete) and <keygen>
     *      (deprecated) are also included.
     *
     * @see https://bugs.php.net/bug.php?id=73175
     */
    protected const PHP_UNRECOGNIZED_VOID_TAGNAME_MATCHER = '(?:command|embed|keygen|source|track|wbr)';

    /**
     * Regular expression part to match tag names that may appear before the start of the `<body>` element.  A start tag
     * for any other element would implicitly start the `<body>` element due to tag omission rules.
     *
     * @var string
     */
    protected const TAGNAME_ALLOWED_BEFORE_BODY_MATCHER
        = '(?:html|head|base|command|link|meta|noscript|script|style|template|title)';

    /**
     * regular expression pattern to match an HTML comment, including delimiters and modifiers
     *
     * @var string
     */
    protected const HTML_COMMENT_PATTERN = '/<!--[^-]*+(?:-(?!->)[^-]*+)*+(?:-->|$)/';

    /**
     * regular expression pattern to match an HTML `<template>` element, including delimiters and modifiers
     *
     * @var string
     */
    protected const HTML_TEMPLATE_ELEMENT_PATTERN
        = '%<template[\\s>][^<]*+(?:<(?!/template>)[^<]*+)*+(?:</template>|$)%i';

    /**
     * @var ?\DOMDocument
     */
    protected $domDocument = null;

    /**
     * @var ?\DOMXPath
     */
    private $xPath = null;

    /**
     * The constructor.
     *
     * Please use `::fromHtml` or `::fromDomDocument` instead.
     */
    private function __construct()
    {
    }

    /**
     * Builds a new instance from the given HTML.
     *
     * @param string $unprocessedHtml raw HTML, must be UTF-encoded, must not be empty
     *
     * @return static
     *
     * @throws \InvalidArgumentException if $unprocessedHtml is anything other than a non-empty string
     */
    public static function fromHtml(string $unprocessedHtml): self
    {
        if ($unprocessedHtml === '') {
            throw new \InvalidArgumentException('The provided HTML must not be empty.', 1515763647);
        }

        $instance = new static();
        $instance->setHtml($unprocessedHtml);

        return $instance;
    }

    /**
     * Builds a new instance from the given DOM document.
     *
     * @param \DOMDocument $document a DOM document returned by getDomDocument() of another instance
     *
     * @return static
     */
    public static function fromDomDocument(\DOMDocument $document): self
    {
        $instance = new static();
        $instance->setDomDocument($document);

        return $instance;
    }

    /**
     * Sets the HTML to process.
     *
     * @param string $html the HTML to process, must be UTF-8-encoded
     */
    private function setHtml(string $html): void
    {
        $this->createUnifiedDomDocument($html);
    }

    /**
     * Provides access to the internal DOMDocument representation of the HTML in its current state.
     *
     * @return \DOMDocument
     *
     * @throws \UnexpectedValueException
     */
    public function getDomDocument(): \DOMDocument
    {
        if (!$this->domDocument instanceof \DOMDocument) {
            $message = self::class . '::setDomDocument() has not yet been called on ' . static::class;
            throw new \UnexpectedValueException($message, 1570472239);
        }

        return $this->domDocument;
    }

    /**
     * @param \DOMDocument $domDocument
     */
    private function setDomDocument(\DOMDocument $domDocument): void
    {
        $this->domDocument = $domDocument;
        $this->xPath = new \DOMXPath($this->domDocument);
    }

    /**
     * @return \DOMXPath
     *
     * @throws \UnexpectedValueException
     */
    protected function getXPath(): \DOMXPath
    {
        if (!$this->xPath instanceof \DOMXPath) {
            $message = self::class . '::setDomDocument() has not yet been called on ' . static::class;
            throw new \UnexpectedValueException($message, 1617819086);
        }

        return $this->xPath;
    }

    /**
     * Renders the normalized and processed HTML.
     *
     * @return string
     */
    public function render(): string
    {
        $htmlWithPossibleErroneousClosingTags = $this->getDomDocument()->saveHTML();

        return $this->removeSelfClosingTagsClosingTags($htmlWithPossibleErroneousClosingTags);
    }

    /**
     * Renders the content of the BODY element of the normalized and processed HTML.
     *
     * @return string
     */
    public function renderBodyContent(): string
    {
        $htmlWithPossibleErroneousClosingTags = $this->getDomDocument()->saveHTML($this->getBodyElement());
        $bodyNodeHtml = $this->removeSelfClosingTagsClosingTags($htmlWithPossibleErroneousClosingTags);

        return \preg_replace('%</?+body(?:\\s[^>]*+)?+>%', '', $bodyNodeHtml);
    }

    /**
     * Eliminates any invalid closing tags for void elements from the given HTML.
     *
     * @param string $html
     *
     * @return string
     */
    private function removeSelfClosingTagsClosingTags(string $html): string
    {
        return \preg_replace('%</' . self::PHP_UNRECOGNIZED_VOID_TAGNAME_MATCHER . '>%', '', $html);
    }

    /**
     * Returns the BODY element.
     *
     * This method assumes that there always is a BODY element.
     *
     * @return \DOMElement
     *
     * @throws \RuntimeException
     */
    private function getBodyElement(): \DOMElement
    {
        $node = $this->getDomDocument()->getElementsByTagName('body')->item(0);
        if (!$node instanceof \DOMElement) {
            throw new \RuntimeException('There is no body element.', 1617922607);
        }

        return $node;
    }

    /**
     * Creates a DOM document from the given HTML and stores it in $this->domDocument.
     *
     * The DOM document will always have a BODY element and a document type.
     *
     * @param string $html
     */
    private function createUnifiedDomDocument(string $html): void
    {
        $this->createRawDomDocument($html);
        $this->ensureExistenceOfBodyElement();
    }

    /**
     * Creates a DOMDocument instance from the given HTML and stores it in $this->domDocument.
     *
     * @param string $html
     */
    private function createRawDomDocument(string $html): void
    {
        $domDocument = new \DOMDocument();
        $domDocument->strictErrorChecking = false;
        $domDocument->formatOutput = true;
        $libXmlState = \libxml_use_internal_errors(true);
        $domDocument->loadHTML($this->prepareHtmlForDomConversion($html));
        \libxml_clear_errors();
        \libxml_use_internal_errors($libXmlState);

        $this->setDomDocument($domDocument);
    }

    /**
     * Returns the HTML with added document type, Content-Type meta tag, and self-closing slashes, if needed,
     * ensuring that the HTML will be good for creating a DOM document from it.
     *
     * @param string $html
     *
     * @return string the unified HTML
     */
    private function prepareHtmlForDomConversion(string $html): string
    {
        $htmlWithSelfClosingSlashes = $this->ensurePhpUnrecognizedSelfClosingTagsAreXml($html);
        $htmlWithDocumentType = $this->ensureDocumentType($htmlWithSelfClosingSlashes);

        return $this->addContentTypeMetaTag($htmlWithDocumentType);
    }

    /**
     * Makes sure that the passed HTML has a document type, with lowercase "html".
     *
     * @param string $html
     *
     * @return string HTML with document type
     */
    private function ensureDocumentType(string $html): string
    {
        $hasDocumentType = \stripos($html, '<!DOCTYPE') !== false;
        if ($hasDocumentType) {
            return $this->normalizeDocumentType($html);
        }

        return self::DEFAULT_DOCUMENT_TYPE . $html;
    }

    /**
     * Makes sure the document type in the passed HTML has lowercase "html".
     *
     * @param string $html
     *
     * @return string HTML with normalized document type
     */
    private function normalizeDocumentType(string $html): string
    {
        // Limit to replacing the first occurrence: as an optimization; and in case an example exists as unescaped text.
        return \preg_replace(
            '/<!DOCTYPE\\s++html(?=[\\s>])/i',
            '<!DOCTYPE html',
            $html,
            1
        );
    }

    /**
     * Adds a Content-Type meta tag for the charset.
     *
     * This method also ensures that there is a HEAD element.
     *
     * @param string $html
     *
     * @return string the HTML with the meta tag added
     */
    private function addContentTypeMetaTag(string $html): string
    {
        if ($this->hasContentTypeMetaTagInHead($html)) {
            return $html;
        }

        // We are trying to insert the meta tag to the right spot in the DOM.
        // If we just prepended it to the HTML, we would lose attributes set to the HTML tag.
        $hasHeadTag = \preg_match('/<head[\\s>]/i', $html);
        $hasHtmlTag = \stripos($html, '<html') !== false;

        if ($hasHeadTag) {
            $reworkedHtml = \preg_replace(
                '/<head(?=[\\s>])([^>]*+)>/i',
                '<head$1>' . self::CONTENT_TYPE_META_TAG,
                $html
            );
        } elseif ($hasHtmlTag) {
            $reworkedHtml = \preg_replace(
                '/<html(.*?)>/is',
                '<html$1><head>' . self::CONTENT_TYPE_META_TAG . '</head>',
                $html
            );
        } else {
            $reworkedHtml = self::CONTENT_TYPE_META_TAG . $html;
        }

        return $reworkedHtml;
    }

    /**
     * Tests whether the given HTML has a valid `Content-Type` metadata element within the `<head>` element.  Due to tag
     * omission rules, HTML parsers are expected to end the `<head>` element and start the `<body>` element upon
     * encountering a start tag for any element which is permitted only within the `<body>`.
     *
     * @param string $html
     *
     * @return bool
     */
    private function hasContentTypeMetaTagInHead(string $html): bool
    {
        \preg_match('%^.*?(?=<meta(?=\\s)[^>]*\\shttp-equiv=(["\']?+)Content-Type\\g{-1}[\\s/>])%is', $html, $matches);
        if (isset($matches[0])) {
            $htmlBefore = $matches[0];
            try {
                $hasContentTypeMetaTagInHead = !$this->hasEndOfHeadElement($htmlBefore);
            } catch (\RuntimeException $exception) {
                // If something unexpected occurs, assume the `Content-Type` that was found is valid.
                \trigger_error($exception->getMessage());
                $hasContentTypeMetaTagInHead = true;
            }
        } else {
            $hasContentTypeMetaTagInHead = false;
        }

        return $hasContentTypeMetaTagInHead;
    }

    /**
     * Tests whether the `<head>` element ends within the given HTML.  Due to tag omission rules, HTML parsers are
     * expected to end the `<head>` element and start the `<body>` element upon encountering a start tag for any element
     * which is permitted only within the `<body>`.
     *
     * @param string $html
     *
     * @return bool
     *
     * @throws \RuntimeException
     */
    private function hasEndOfHeadElement(string $html): bool
    {
        $headEndTagMatchCount
            = \preg_match('%<(?!' . self::TAGNAME_ALLOWED_BEFORE_BODY_MATCHER . '[\\s/>])\\w|</head>%i', $html);
        if (\is_int($headEndTagMatchCount) && $headEndTagMatchCount > 0) {
            // An exception to the implicit end of the `<head>` is any content within a `<template>` element, as well in
            // comments.  As an optimization, this is only checked for if a potential `<head>` end tag is found.
            $htmlWithoutCommentsOrTemplates = $this->removeHtmlTemplateElements($this->removeHtmlComments($html));
            $hasEndOfHeadElement = $htmlWithoutCommentsOrTemplates === $html
                || $this->hasEndOfHeadElement($htmlWithoutCommentsOrTemplates);
        } else {
            $hasEndOfHeadElement = false;
        }

        return $hasEndOfHeadElement;
    }

    /**
     * Removes comments from the given HTML, including any which are unterminated, for which the remainder of the string
     * is removed.
     *
     * @param string $html
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    private function removeHtmlComments(string $html): string
    {
        $result = \preg_replace(self::HTML_COMMENT_PATTERN, '', $html);
        if (!\is_string($result)) {
            throw new \RuntimeException('Internal PCRE error', 1616521475);
        }

        return $result;
    }

    /**
     * Removes `<template>` elements from the given HTML, including any without an end tag, for which the remainder of
     * the string is removed.
     *
     * @param string $html
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    private function removeHtmlTemplateElements(string $html): string
    {
        $result = \preg_replace(self::HTML_TEMPLATE_ELEMENT_PATTERN, '', $html);
        if (!\is_string($result)) {
            throw new \RuntimeException('Internal PCRE error', 1616519652);
        }

        return $result;
    }

    /**
     * Makes sure that any self-closing tags not recognized as such by PHP's DOMDocument implementation have a
     * self-closing slash.
     *
     * @param string $html
     *
     * @return string HTML with problematic tags converted.
     */
    private function ensurePhpUnrecognizedSelfClosingTagsAreXml(string $html): string
    {
        return \preg_replace(
            '%<' . self::PHP_UNRECOGNIZED_VOID_TAGNAME_MATCHER . '\\b[^>]*+(?<!/)(?=>)%',
            '$0/',
            $html
        );
    }

    /**
     * Checks that $this->domDocument has a BODY element and adds it if it is missing.
     *
     * @throws \UnexpectedValueException
     */
    private function ensureExistenceOfBodyElement(): void
    {
        if ($this->getDomDocument()->getElementsByTagName('body')->item(0) instanceof \DOMElement) {
            return;
        }

        $htmlElement = $this->getDomDocument()->getElementsByTagName('html')->item(0);
        if (!$htmlElement instanceof \DOMElement) {
            throw new \UnexpectedValueException('There is no HTML element although there should be one.', 1569930853);
        }
        $htmlElement->appendChild($this->getDomDocument()->createElement('body'));
    }
}

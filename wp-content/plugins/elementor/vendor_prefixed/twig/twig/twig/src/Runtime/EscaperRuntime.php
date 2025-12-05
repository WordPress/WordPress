<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ElementorDeps\Twig\Runtime;

use ElementorDeps\Twig\Error\RuntimeError;
use ElementorDeps\Twig\Extension\RuntimeExtensionInterface;
use ElementorDeps\Twig\Markup;
final class EscaperRuntime implements RuntimeExtensionInterface
{
    /** @var array<string, callable(string $string, string $charset): string> */
    private $escapers = [];
    /** @internal */
    public $safeClasses = [];
    /** @internal */
    public $safeLookup = [];
    private $charset;
    public function __construct($charset = 'UTF-8')
    {
        $this->charset = $charset;
    }
    /**
     * Defines a new escaper to be used via the escape filter.
     *
     * @param string                                            $strategy The strategy name that should be used as a strategy in the escape call
     * @param callable(string $string, string $charset): string $callable A valid PHP callable
     */
    public function setEscaper($strategy, callable $callable)
    {
        $this->escapers[$strategy] = $callable;
    }
    /**
     * Gets all defined escapers.
     *
     * @return array<string, callable(string $string, string $charset): string> An array of escapers
     */
    public function getEscapers()
    {
        return $this->escapers;
    }
    public function setSafeClasses(array $safeClasses = [])
    {
        $this->safeClasses = [];
        $this->safeLookup = [];
        foreach ($safeClasses as $class => $strategies) {
            $this->addSafeClass($class, $strategies);
        }
    }
    public function addSafeClass(string $class, array $strategies)
    {
        $class = \ltrim($class, '\\');
        if (!isset($this->safeClasses[$class])) {
            $this->safeClasses[$class] = [];
        }
        $this->safeClasses[$class] = \array_merge($this->safeClasses[$class], $strategies);
        foreach ($strategies as $strategy) {
            $this->safeLookup[$strategy][$class] = \true;
        }
    }
    /**
     * Escapes a string.
     *
     * @param mixed       $string     The value to be escaped
     * @param string      $strategy   The escaping strategy
     * @param string|null $charset    The charset
     * @param bool        $autoescape Whether the function is called by the auto-escaping feature (true) or by the developer (false)
     *
     * @throws RuntimeError
     */
    public function escape($string, string $strategy = 'html', ?string $charset = null, bool $autoescape = \false)
    {
        if ($autoescape && $string instanceof Markup) {
            return $string;
        }
        if (!\is_string($string)) {
            if (\is_object($string) && \method_exists($string, '__toString')) {
                if ($autoescape) {
                    $c = \get_class($string);
                    if (!isset($this->safeClasses[$c])) {
                        $this->safeClasses[$c] = [];
                        foreach (\class_parents($string) + \class_implements($string) as $class) {
                            if (isset($this->safeClasses[$class])) {
                                $this->safeClasses[$c] = \array_unique(\array_merge($this->safeClasses[$c], $this->safeClasses[$class]));
                                foreach ($this->safeClasses[$class] as $s) {
                                    $this->safeLookup[$s][$c] = \true;
                                }
                            }
                        }
                    }
                    if (isset($this->safeLookup[$strategy][$c]) || isset($this->safeLookup['all'][$c])) {
                        return (string) $string;
                    }
                }
                $string = (string) $string;
            } elseif (\in_array($strategy, ['html', 'js', 'css', 'html_attr', 'url'])) {
                // we return the input as is (which can be of any type)
                return $string;
            }
        }
        if ('' === $string) {
            return '';
        }
        $charset = $charset ?: $this->charset;
        switch ($strategy) {
            case 'html':
                // see https://www.php.net/htmlspecialchars
                // Using a static variable to avoid initializing the array
                // each time the function is called. Moving the declaration on the
                // top of the function slow downs other escaping strategies.
                static $htmlspecialcharsCharsets = ['ISO-8859-1' => \true, 'ISO8859-1' => \true, 'ISO-8859-15' => \true, 'ISO8859-15' => \true, 'utf-8' => \true, 'UTF-8' => \true, 'CP866' => \true, 'IBM866' => \true, '866' => \true, 'CP1251' => \true, 'WINDOWS-1251' => \true, 'WIN-1251' => \true, '1251' => \true, 'CP1252' => \true, 'WINDOWS-1252' => \true, '1252' => \true, 'KOI8-R' => \true, 'KOI8-RU' => \true, 'KOI8R' => \true, 'BIG5' => \true, '950' => \true, 'GB2312' => \true, '936' => \true, 'BIG5-HKSCS' => \true, 'SHIFT_JIS' => \true, 'SJIS' => \true, '932' => \true, 'EUC-JP' => \true, 'EUCJP' => \true, 'ISO8859-5' => \true, 'ISO-8859-5' => \true, 'MACROMAN' => \true];
                if (isset($htmlspecialcharsCharsets[$charset])) {
                    return \htmlspecialchars($string, \ENT_QUOTES | \ENT_SUBSTITUTE, $charset);
                }
                if (isset($htmlspecialcharsCharsets[\strtoupper($charset)])) {
                    // cache the lowercase variant for future iterations
                    $htmlspecialcharsCharsets[$charset] = \true;
                    return \htmlspecialchars($string, \ENT_QUOTES | \ENT_SUBSTITUTE, $charset);
                }
                $string = $this->convertEncoding($string, 'UTF-8', $charset);
                $string = \htmlspecialchars($string, \ENT_QUOTES | \ENT_SUBSTITUTE, 'UTF-8');
                return \iconv('UTF-8', $charset, $string);
            case 'js':
                // escape all non-alphanumeric characters
                // into their \x or \uHHHH representations
                if ('UTF-8' !== $charset) {
                    $string = $this->convertEncoding($string, 'UTF-8', $charset);
                }
                if (!\preg_match('//u', $string)) {
                    throw new RuntimeError('The string to escape is not a valid UTF-8 string.');
                }
                $string = \preg_replace_callback('#[^a-zA-Z0-9,\\._]#Su', function ($matches) {
                    $char = $matches[0];
                    /*
                     * A few characters have short escape sequences in JSON and JavaScript.
                     * Escape sequences supported only by JavaScript, not JSON, are omitted.
                     * \" is also supported but omitted, because the resulting string is not HTML safe.
                     */
                    static $shortMap = ['\\' => '\\\\', '/' => '\\/', "\x08" => '\\b', "\f" => '\\f', "\n" => '\\n', "\r" => '\\r', "\t" => '\\t'];
                    if (isset($shortMap[$char])) {
                        return $shortMap[$char];
                    }
                    $codepoint = \mb_ord($char, 'UTF-8');
                    if (0x10000 > $codepoint) {
                        return \sprintf('\\u%04X', $codepoint);
                    }
                    // Split characters outside the BMP into surrogate pairs
                    // https://tools.ietf.org/html/rfc2781.html#section-2.1
                    $u = $codepoint - 0x10000;
                    $high = 0xd800 | $u >> 10;
                    $low = 0xdc00 | $u & 0x3ff;
                    return \sprintf('\\u%04X\\u%04X', $high, $low);
                }, $string);
                if ('UTF-8' !== $charset) {
                    $string = \iconv('UTF-8', $charset, $string);
                }
                return $string;
            case 'css':
                if ('UTF-8' !== $charset) {
                    $string = $this->convertEncoding($string, 'UTF-8', $charset);
                }
                if (!\preg_match('//u', $string)) {
                    throw new RuntimeError('The string to escape is not a valid UTF-8 string.');
                }
                $string = \preg_replace_callback('#[^a-zA-Z0-9]#Su', function ($matches) {
                    $char = $matches[0];
                    return \sprintf('\\%X ', 1 === \strlen($char) ? \ord($char) : \mb_ord($char, 'UTF-8'));
                }, $string);
                if ('UTF-8' !== $charset) {
                    $string = \iconv('UTF-8', $charset, $string);
                }
                return $string;
            case 'html_attr':
                if ('UTF-8' !== $charset) {
                    $string = $this->convertEncoding($string, 'UTF-8', $charset);
                }
                if (!\preg_match('//u', $string)) {
                    throw new RuntimeError('The string to escape is not a valid UTF-8 string.');
                }
                $string = \preg_replace_callback('#[^a-zA-Z0-9,\\.\\-_]#Su', function ($matches) {
                    /**
                     * This function is adapted from code coming from Zend Framework.
                     *
                     * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (https://www.zend.com)
                     * @license   https://framework.zend.com/license/new-bsd New BSD License
                     */
                    $chr = $matches[0];
                    $ord = \ord($chr);
                    /*
                     * The following replaces characters undefined in HTML with the
                     * hex entity for the Unicode replacement character.
                     */
                    if ($ord <= 0x1f && "\t" != $chr && "\n" != $chr && "\r" != $chr || $ord >= 0x7f && $ord <= 0x9f) {
                        return '&#xFFFD;';
                    }
                    /*
                     * Check if the current character to escape has a name entity we should
                     * replace it with while grabbing the hex value of the character.
                     */
                    if (1 === \strlen($chr)) {
                        /*
                         * While HTML supports far more named entities, the lowest common denominator
                         * has become HTML5's XML Serialisation which is restricted to the those named
                         * entities that XML supports. Using HTML entities would result in this error:
                         *     XML Parsing Error: undefined entity
                         */
                        static $entityMap = [
                            34 => '&quot;',
                            /* quotation mark */
                            38 => '&amp;',
                            /* ampersand */
                            60 => '&lt;',
                            /* less-than sign */
                            62 => '&gt;',
                        ];
                        if (isset($entityMap[$ord])) {
                            return $entityMap[$ord];
                        }
                        return \sprintf('&#x%02X;', $ord);
                    }
                    /*
                     * Per OWASP recommendations, we'll use hex entities for any other
                     * characters where a named entity does not exist.
                     */
                    return \sprintf('&#x%04X;', \mb_ord($chr, 'UTF-8'));
                }, $string);
                if ('UTF-8' !== $charset) {
                    $string = \iconv('UTF-8', $charset, $string);
                }
                return $string;
            case 'url':
                return \rawurlencode($string);
            default:
                if (\array_key_exists($strategy, $this->escapers)) {
                    return $this->escapers[$strategy]($string, $charset);
                }
                $validStrategies = \implode('", "', \array_merge(['html', 'js', 'url', 'css', 'html_attr'], \array_keys($this->escapers)));
                throw new RuntimeError(\sprintf('Invalid escaping strategy "%s" (valid ones: "%s").', $strategy, $validStrategies));
        }
    }
    private function convertEncoding(string $string, string $to, string $from)
    {
        if (!\function_exists('iconv')) {
            throw new RuntimeError('Unable to convert encoding: required function iconv() does not exist. You should install ext-iconv or symfony/polyfill-iconv.');
        }
        return \iconv($from, $to, $string);
    }
}

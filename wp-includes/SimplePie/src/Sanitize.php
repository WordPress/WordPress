<?php

// SPDX-FileCopyrightText: 2004-2023 Ryan Parman, Sam Sneddon, Ryan McCue
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace SimplePie;

use DOMDocument;
use DOMXPath;
use InvalidArgumentException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use SimplePie\Cache\Base;
use SimplePie\Cache\BaseDataCache;
use SimplePie\Cache\CallableNameFilter;
use SimplePie\Cache\DataCache;
use SimplePie\Cache\NameFilter;
use SimplePie\HTTP\Client;
use SimplePie\HTTP\ClientException;
use SimplePie\HTTP\FileClient;
use SimplePie\HTTP\Psr18Client;

/**
 * Used for data cleanup and post-processing
 *
 *
 * This class can be overloaded with {@see \SimplePie\SimplePie::set_sanitize_class()}
 *
 * @todo Move to using an actual HTML parser (this will allow tags to be properly stripped, and to switch between HTML and XHTML), this will also make it easier to shorten a string while preserving HTML tags
 */
class Sanitize implements RegistryAware
{
    // Private vars
    /** @var string */
    public $base = '';

    // Options
    /** @var bool */
    public $remove_div = true;
    /** @var string */
    public $image_handler = '';
    /** @var string[] */
    public $strip_htmltags = ['base', 'blink', 'body', 'doctype', 'embed', 'font', 'form', 'frame', 'frameset', 'html', 'iframe', 'input', 'marquee', 'meta', 'noscript', 'object', 'param', 'script', 'style'];
    /** @var bool */
    public $encode_instead_of_strip = false;
    /** @var string[] */
    public $strip_attributes = ['bgsound', 'expr', 'id', 'style', 'onclick', 'onerror', 'onfinish', 'onmouseover', 'onmouseout', 'onfocus', 'onblur', 'lowsrc', 'dynsrc'];
    /** @var string[] */
    public $rename_attributes = [];
    /** @var array<string, array<string, string>> */
    public $add_attributes = ['audio' => ['preload' => 'none'], 'iframe' => ['sandbox' => 'allow-scripts allow-same-origin'], 'video' => ['preload' => 'none']];
    /** @var bool */
    public $strip_comments = false;
    /** @var string */
    public $output_encoding = 'UTF-8';
    /** @var bool */
    public $enable_cache = true;
    /** @var string */
    public $cache_location = './cache';
    /** @var string&(callable(string): string) */
    public $cache_name_function = 'md5';

    /**
     * @var NameFilter
     */
    private $cache_namefilter;
    /** @var int */
    public $timeout = 10;
    /** @var string */
    public $useragent = '';
    /** @var bool */
    public $force_fsockopen = false;
    /** @var array<string, string|string[]> */
    public $replace_url_attributes = [];
    /**
     * @var array<int, mixed> Custom curl options
     * @see SimplePie::set_curl_options()
     */
    private $curl_options = [];

    /** @var Registry */
    public $registry;

    /**
     * @var DataCache|null
     */
    private $cache = null;

    /**
     * @var int Cache duration (in seconds)
     */
    private $cache_duration = 3600;

    /**
     * List of domains for which to force HTTPS.
     * @see \SimplePie\Sanitize::set_https_domains()
     * Array is a tree split at DNS levels. Example:
     * array('biz' => true, 'com' => array('example' => true), 'net' => array('example' => array('www' => true)))
     * @var true|array<string, true|array<string, true|array<string, array<string, true|array<string, true|array<string, true>>>>>>
     */
    public $https_domains = [];

    /**
     * @var Client|null
     */
    private $http_client = null;

    public function __construct()
    {
        // Set defaults
        $this->set_url_replacements(null);
    }

    /**
     * @return void
     */
    public function remove_div(bool $enable = true)
    {
        $this->remove_div = (bool) $enable;
    }

    /**
     * @param string|false $page
     * @return void
     */
    public function set_image_handler($page = false)
    {
        if ($page) {
            $this->image_handler = (string) $page;
        } else {
            $this->image_handler = '';
        }
    }

    /**
     * @return void
     */
    public function set_registry(\SimplePie\Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param (string&(callable(string): string))|NameFilter $cache_name_function
     * @param class-string<Cache> $cache_class
     * @return void
     */
    public function pass_cache_data(bool $enable_cache = true, string $cache_location = './cache', $cache_name_function = 'md5', string $cache_class = Cache::class, ?DataCache $cache = null)
    {
        $this->enable_cache = $enable_cache;

        if ($cache_location) {
            $this->cache_location = $cache_location;
        }

        // @phpstan-ignore-next-line Enforce PHPDoc type.
        if (!is_string($cache_name_function) && !$cache_name_function instanceof NameFilter) {
            throw new InvalidArgumentException(sprintf(
                '%s(): Argument #3 ($cache_name_function) must be of type %s',
                __METHOD__,
                NameFilter::class
            ), 1);
        }

        // BC: $cache_name_function could be a callable as string
        if (is_string($cache_name_function)) {
            // trigger_error(sprintf('Providing $cache_name_function as string in "%s()" is deprecated since SimplePie 1.8.0, provide as "%s" instead.', __METHOD__, NameFilter::class), \E_USER_DEPRECATED);
            $this->cache_name_function = $cache_name_function;

            $cache_name_function = new CallableNameFilter($cache_name_function);
        }

        $this->cache_namefilter = $cache_name_function;

        if ($cache !== null) {
            $this->cache = $cache;
        }
    }

    /**
     * Set a PSR-18 client and PSR-17 factories
     *
     * Allows you to use your own HTTP client implementations.
     */
    final public function set_http_client(
        ClientInterface $http_client,
        RequestFactoryInterface $request_factory,
        UriFactoryInterface $uri_factory
    ): void {
        $this->http_client = new Psr18Client($http_client, $request_factory, $uri_factory);
    }

    /**
     * @deprecated since SimplePie 1.9.0, use \SimplePie\Sanitize::set_http_client() instead.
     * @param class-string<File> $file_class
     * @param array<int, mixed> $curl_options
     * @return void
     */
    public function pass_file_data(string $file_class = File::class, int $timeout = 10, string $useragent = '', bool $force_fsockopen = false, array $curl_options = [])
    {
        // trigger_error(sprintf('SimplePie\Sanitize::pass_file_data() is deprecated since SimplePie 1.9.0, please use "SimplePie\Sanitize::set_http_client()" instead.'), \E_USER_DEPRECATED);
        if ($timeout) {
            $this->timeout = $timeout;
        }

        if ($useragent) {
            $this->useragent = $useragent;
        }

        if ($force_fsockopen) {
            $this->force_fsockopen = $force_fsockopen;
        }

        $this->curl_options = $curl_options;
        // Invalidate the registered client.
        $this->http_client = null;
    }

    /**
     * @param string[]|string|false $tags Set a list of tags to strip, or set empty string to use default tags, or false to strip nothing.
     * @return void
     */
    public function strip_htmltags($tags = ['base', 'blink', 'body', 'doctype', 'embed', 'font', 'form', 'frame', 'frameset', 'html', 'iframe', 'input', 'marquee', 'meta', 'noscript', 'object', 'param', 'script', 'style'])
    {
        if ($tags) {
            if (is_array($tags)) {
                $this->strip_htmltags = $tags;
            } else {
                $this->strip_htmltags = explode(',', $tags);
            }
        } else {
            $this->strip_htmltags = [];
        }
    }

    /**
     * @return void
     */
    public function encode_instead_of_strip(bool $encode = false)
    {
        $this->encode_instead_of_strip = $encode;
    }

    /**
     * @param string[]|string $attribs
     * @return void
     */
    public function rename_attributes($attribs = [])
    {
        if ($attribs) {
            if (is_array($attribs)) {
                $this->rename_attributes = $attribs;
            } else {
                $this->rename_attributes = explode(',', $attribs);
            }
        } else {
            $this->rename_attributes = [];
        }
    }

    /**
     * @param string[]|string $attribs
     * @return void
     */
    public function strip_attributes($attribs = ['bgsound', 'expr', 'id', 'style', 'onclick', 'onerror', 'onfinish', 'onmouseover', 'onmouseout', 'onfocus', 'onblur', 'lowsrc', 'dynsrc'])
    {
        if ($attribs) {
            if (is_array($attribs)) {
                $this->strip_attributes = $attribs;
            } else {
                $this->strip_attributes = explode(',', $attribs);
            }
        } else {
            $this->strip_attributes = [];
        }
    }

    /**
     * @param array<string, array<string, string>> $attribs
     * @return void
     */
    public function add_attributes(array $attribs = ['audio' => ['preload' => 'none'], 'iframe' => ['sandbox' => 'allow-scripts allow-same-origin'], 'video' => ['preload' => 'none']])
    {
        $this->add_attributes = $attribs;
    }

    /**
     * @return void
     */
    public function strip_comments(bool $strip = false)
    {
        $this->strip_comments = $strip;
    }

    /**
     * @return void
     */
    public function set_output_encoding(string $encoding = 'UTF-8')
    {
        $this->output_encoding = $encoding;
    }

    /**
     * Set element/attribute key/value pairs of HTML attributes
     * containing URLs that need to be resolved relative to the feed
     *
     * Defaults to |a|@href, |area|@href, |audio|@src, |blockquote|@cite,
     * |del|@cite, |form|@action, |img|@longdesc, |img|@src, |input|@src,
     * |ins|@cite, |q|@cite, |source|@src, |video|@src
     *
     * @since 1.0
     * @param array<string, string|string[]>|null $element_attribute Element/attribute key/value pairs, null for default
     * @return void
     */
    public function set_url_replacements(?array $element_attribute = null)
    {
        if ($element_attribute === null) {
            $element_attribute = [
                'a' => 'href',
                'area' => 'href',
                'audio' => 'src',
                'blockquote' => 'cite',
                'del' => 'cite',
                'form' => 'action',
                'img' => [
                    'longdesc',
                    'src'
                ],
                'input' => 'src',
                'ins' => 'cite',
                'q' => 'cite',
                'source' => 'src',
                'video' => [
                    'poster',
                    'src'
                ]
            ];
        }
        $this->replace_url_attributes = $element_attribute;
    }

    /**
     * Set the list of domains for which to force HTTPS.
     * @see \SimplePie\Misc::https_url()
     * Example array('biz', 'example.com', 'example.org', 'www.example.net');
     *
     * @param string[] $domains list of domain names ['biz', 'example.com', 'example.org', 'www.example.net']
     *
     * @return void
     */
    public function set_https_domains(array $domains)
    {
        $this->https_domains = [];
        foreach ($domains as $domain) {
            $domain = trim($domain, ". \t\n\r\0\x0B");
            $segments = array_reverse(explode('.', $domain));
            /** @var true|array<string, true|array<string, true|array<string, array<string, true|array<string, true|array<string, true>>>>>> */ // Needed for PHPStan.
            $node = &$this->https_domains;
            foreach ($segments as $segment) {//Build a tree
                if ($node === true) {
                    break;
                }
                if (!isset($node[$segment])) {
                    $node[$segment] = [];
                }
                $node = &$node[$segment];
            }
            $node = true;
        }
    }

    /**
     * Check if the domain is in the list of forced HTTPS.
     *
     * @return bool
     */
    protected function is_https_domain(string $domain)
    {
        $domain = trim($domain, '. ');
        $segments = array_reverse(explode('.', $domain));
        $node = &$this->https_domains;
        foreach ($segments as $segment) {//Explore the tree
            if (isset($node[$segment])) {
                $node = &$node[$segment];
            } else {
                break;
            }
        }
        return $node === true;
    }

    /**
     * Force HTTPS for selected Web sites.
     *
     * @return string
     */
    public function https_url(string $url)
    {
        return (
            strtolower(substr($url, 0, 7)) === 'http://'
            && ($parsed = parse_url($url, PHP_URL_HOST)) !== false // Malformed URL
            && $parsed !== null // Missing host
            && $this->is_https_domain($parsed) // Should be forced?
        ) ? substr_replace($url, 's', 4, 0) // Add the 's' to HTTPS
        : $url;
    }

    /**
     * @param int-mask-of<SimplePie::CONSTRUCT_*> $type
     * @param string $base
     * @return string Sanitized data; false if output encoding is changed to something other than UTF-8 and conversion fails
     */
    public function sanitize(string $data, int $type, string $base = '')
    {
        $data = trim($data);
        if ($data !== '' || $type & \SimplePie\SimplePie::CONSTRUCT_IRI) {
            if ($type & \SimplePie\SimplePie::CONSTRUCT_MAYBE_HTML) {
                if (preg_match('/(&(#(x[0-9a-fA-F]+|[0-9]+)|[a-zA-Z0-9]+)|<\/[A-Za-z][^\x09\x0A\x0B\x0C\x0D\x20\x2F\x3E]*' . \SimplePie\SimplePie::PCRE_HTML_ATTRIBUTE . '>)/', $data)) {
                    $type |= \SimplePie\SimplePie::CONSTRUCT_HTML;
                } else {
                    $type |= \SimplePie\SimplePie::CONSTRUCT_TEXT;
                }
            }

            if ($type & \SimplePie\SimplePie::CONSTRUCT_BASE64) {
                $data = base64_decode($data);
            }

            if ($type & (\SimplePie\SimplePie::CONSTRUCT_HTML | \SimplePie\SimplePie::CONSTRUCT_XHTML)) {
                if (!class_exists('DOMDocument')) {
                    throw new \SimplePie\Exception('DOMDocument not found, unable to use sanitizer');
                }
                $document = new \DOMDocument();
                $document->encoding = 'UTF-8';

                // PHPStan seems to have trouble resolving int-mask because bitwise
                // operators are used when operators are used when passing this parameter.
                // https://github.com/phpstan/phpstan/issues/9384
                /** @var int-mask-of<SimplePie::CONSTRUCT_*> $type */
                $data = $this->preprocess($data, $type);

                set_error_handler([Misc::class, 'silence_errors']);
                $document->loadHTML($data);
                restore_error_handler();

                $xpath = new \DOMXPath($document);

                // Strip comments
                if ($this->strip_comments) {
                    /** @var \DOMNodeList<\DOMComment> */
                    $comments = $xpath->query('//comment()');

                    foreach ($comments as $comment) {
                        $parentNode = $comment->parentNode;
                        assert($parentNode !== null, 'For PHPStan, comment must have a parent');
                        $parentNode->removeChild($comment);
                    }
                }

                // Strip out HTML tags and attributes that might cause various security problems.
                // Based on recommendations by Mark Pilgrim at:
                // https://web.archive.org/web/20110902041826/http://diveintomark.org:80/archives/2003/06/12/how_to_consume_rss_safely
                if ($this->strip_htmltags) {
                    foreach ($this->strip_htmltags as $tag) {
                        $this->strip_tag($tag, $document, $xpath, $type);
                    }
                }

                if ($this->rename_attributes) {
                    foreach ($this->rename_attributes as $attrib) {
                        $this->rename_attr($attrib, $xpath);
                    }
                }

                if ($this->strip_attributes) {
                    foreach ($this->strip_attributes as $attrib) {
                        $this->strip_attr($attrib, $xpath);
                    }
                }

                if ($this->add_attributes) {
                    foreach ($this->add_attributes as $tag => $valuePairs) {
                        $this->add_attr($tag, $valuePairs, $document);
                    }
                }

                // Replace relative URLs
                $this->base = $base;
                foreach ($this->replace_url_attributes as $element => $attributes) {
                    $this->replace_urls($document, $element, $attributes);
                }

                // If image handling (caching, etc.) is enabled, cache and rewrite all the image tags.
                if ($this->image_handler !== '' && $this->enable_cache) {
                    $images = $document->getElementsByTagName('img');

                    foreach ($images as $img) {
                        if ($img->hasAttribute('src')) {
                            $image_url = $this->cache_namefilter->filter($img->getAttribute('src'));
                            $cache = $this->get_cache($image_url);

                            if ($cache->get_data($image_url, false)) {
                                $img->setAttribute('src', $this->image_handler . $image_url);
                            } else {
                                try {
                                    $file = $this->get_http_client()->request(
                                        Client::METHOD_GET,
                                        $img->getAttribute('src'),
                                        ['X-FORWARDED-FOR' => $_SERVER['REMOTE_ADDR']]
                                    );
                                } catch (ClientException $th) {
                                    continue;
                                }

                                if ((!Misc::is_remote_uri($file->get_final_requested_uri()) || ($file->get_status_code() === 200 || $file->get_status_code() > 206 && $file->get_status_code() < 300))) {
                                    if ($cache->set_data($image_url, ['headers' => $file->get_headers(), 'body' => $file->get_body_content()], $this->cache_duration)) {
                                        $img->setAttribute('src', $this->image_handler . $image_url);
                                    } else {
                                        trigger_error("$this->cache_location is not writable. Make sure you've set the correct relative or absolute path, and that the location is server-writable.", E_USER_WARNING);
                                    }
                                }
                            }
                        }
                    }
                }

                // Get content node
                $div = null;
                if (($item = $document->getElementsByTagName('body')->item(0)) !== null) {
                    $div = $item->firstChild;
                }
                // Finally, convert to a HTML string
                $data = trim((string) $document->saveHTML($div));

                if ($this->remove_div) {
                    $data = preg_replace('/^<div' . \SimplePie\SimplePie::PCRE_XML_ATTRIBUTE . '>/', '', $data);
                    // Cast for PHPStan, it is unable to validate a non-literal regex above.
                    $data = preg_replace('/<\/div>$/', '', (string) $data);
                } else {
                    $data = preg_replace('/^<div' . \SimplePie\SimplePie::PCRE_XML_ATTRIBUTE . '>/', '<div>', $data);
                }

                // Cast for PHPStan, it is unable to validate a non-literal regex above.
                $data = str_replace('</source>', '', (string) $data);
            }

            if ($type & \SimplePie\SimplePie::CONSTRUCT_IRI) {
                $absolute = $this->registry->call(Misc::class, 'absolutize_url', [$data, $base]);
                if ($absolute !== false) {
                    $data = $absolute;
                }
            }

            if ($type & (\SimplePie\SimplePie::CONSTRUCT_TEXT | \SimplePie\SimplePie::CONSTRUCT_IRI)) {
                $data = htmlspecialchars($data, ENT_COMPAT, 'UTF-8');
            }

            if ($this->output_encoding !== 'UTF-8') {
                // This really returns string|false but changing encoding is uncommon and we are going to deprecate it, so let’s just lie to PHPStan in the interest of cleaner annotations.
                /** @var string */
                $data = $this->registry->call(Misc::class, 'change_encoding', [$data, 'UTF-8', $this->output_encoding]);
            }
        }
        return $data;
    }

    /**
     * @param int-mask-of<SimplePie::CONSTRUCT_*> $type
     * @return string
     */
    protected function preprocess(string $html, int $type)
    {
        $ret = '';
        $html = preg_replace('%</?(?:html|body)[^>]*?'.'>%is', '', $html);
        if ($type & ~\SimplePie\SimplePie::CONSTRUCT_XHTML) {
            // Atom XHTML constructs are wrapped with a div by default
            // Note: No protection if $html contains a stray </div>!
            $html = '<div>' . $html . '</div>';
            $ret .= '<!DOCTYPE html>';
            $content_type = 'text/html';
        } else {
            $ret .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
            $content_type = 'application/xhtml+xml';
        }

        $ret .= '<html><head>';
        $ret .= '<meta http-equiv="Content-Type" content="' . $content_type . '; charset=utf-8" />';
        $ret .= '</head><body>' . $html . '</body></html>';
        return $ret;
    }

    /**
     * @param array<string>|string $attributes
     * @return void
     */
    public function replace_urls(DOMDocument $document, string $tag, $attributes)
    {
        if (!is_array($attributes)) {
            $attributes = [$attributes];
        }

        if (!is_array($this->strip_htmltags) || !in_array($tag, $this->strip_htmltags)) {
            $elements = $document->getElementsByTagName($tag);
            foreach ($elements as $element) {
                foreach ($attributes as $attribute) {
                    if ($element->hasAttribute($attribute)) {
                        $value = $this->registry->call(Misc::class, 'absolutize_url', [$element->getAttribute($attribute), $this->base]);
                        if ($value !== false) {
                            $value = $this->https_url($value);
                            $element->setAttribute($attribute, $value);
                        }
                    }
                }
            }
        }
    }

    /**
     * @param array<int, string> $match
     * @return string
     */
    public function do_strip_htmltags(array $match)
    {
        if ($this->encode_instead_of_strip) {
            if (isset($match[4]) && !in_array(strtolower($match[1]), ['script', 'style'])) {
                $match[1] = htmlspecialchars($match[1], ENT_COMPAT, 'UTF-8');
                $match[2] = htmlspecialchars($match[2], ENT_COMPAT, 'UTF-8');
                return "&lt;$match[1]$match[2]&gt;$match[3]&lt;/$match[1]&gt;";
            } else {
                return htmlspecialchars($match[0], ENT_COMPAT, 'UTF-8');
            }
        } elseif (isset($match[4]) && !in_array(strtolower($match[1]), ['script', 'style'])) {
            return $match[4];
        } else {
            return '';
        }
    }

    /**
     * @param int-mask-of<SimplePie::CONSTRUCT_*> $type
     * @return void
     */
    protected function strip_tag(string $tag, DOMDocument $document, DOMXPath $xpath, int $type)
    {
        $elements = $xpath->query('body//' . $tag);

        if ($elements === false) {
            throw new \SimplePie\Exception(sprintf(
                '%s(): Possibly malformed expression, check argument #1 ($tag)',
                __METHOD__
            ), 1);
        }

        if ($this->encode_instead_of_strip) {
            foreach ($elements as $element) {
                $fragment = $document->createDocumentFragment();

                // For elements which aren't script or style, include the tag itself
                if (!in_array($tag, ['script', 'style'])) {
                    $text = '<' . $tag;
                    if ($element->attributes !== null) {
                        $attrs = [];
                        foreach ($element->attributes as $name => $attr) {
                            $value = $attr->value;

                            // In XHTML, empty values should never exist, so we repeat the value
                            if (empty($value) && ($type & \SimplePie\SimplePie::CONSTRUCT_XHTML)) {
                                $value = $name;
                            }
                            // For HTML, empty is fine
                            elseif (empty($value) && ($type & \SimplePie\SimplePie::CONSTRUCT_HTML)) {
                                $attrs[] = $name;
                                continue;
                            }

                            // Standard attribute text
                            $attrs[] = $name . '="' . $attr->value . '"';
                        }
                        $text .= ' ' . implode(' ', $attrs);
                    }
                    $text .= '>';
                    $fragment->appendChild(new \DOMText($text));
                }

                $number = $element->childNodes->length;
                for ($i = $number; $i > 0; $i--) {
                    if (($child = $element->childNodes->item(0)) !== null) {
                        $fragment->appendChild($child);
                    }
                }

                if (!in_array($tag, ['script', 'style'])) {
                    $fragment->appendChild(new \DOMText('</' . $tag . '>'));
                }

                if (($parentNode = $element->parentNode) !== null) {
                    $parentNode->replaceChild($fragment, $element);
                }
            }

            return;
        } elseif (in_array($tag, ['script', 'style'])) {
            foreach ($elements as $element) {
                if (($parentNode = $element->parentNode) !== null) {
                    $parentNode->removeChild($element);
                }
            }

            return;
        } else {
            foreach ($elements as $element) {
                $fragment = $document->createDocumentFragment();
                $number = $element->childNodes->length;
                for ($i = $number; $i > 0; $i--) {
                    if (($child = $element->childNodes->item(0)) !== null) {
                        $fragment->appendChild($child);
                    }
                }

                if (($parentNode = $element->parentNode) !== null) {
                    $parentNode->replaceChild($fragment, $element);
                }
            }
        }
    }

    /**
     * @return void
     */
    protected function strip_attr(string $attrib, DOMXPath $xpath)
    {
        $elements = $xpath->query('//*[@' . $attrib . ']');

        if ($elements === false) {
            throw new \SimplePie\Exception(sprintf(
                '%s(): Possibly malformed expression, check argument #1 ($attrib)',
                __METHOD__
            ), 1);
        }

        /** @var \DOMElement $element */
        foreach ($elements as $element) {
            $element->removeAttribute($attrib);
        }
    }

    /**
     * @return void
     */
    protected function rename_attr(string $attrib, DOMXPath $xpath)
    {
        $elements = $xpath->query('//*[@' . $attrib . ']');

        if ($elements === false) {
            throw new \SimplePie\Exception(sprintf(
                '%s(): Possibly malformed expression, check argument #1 ($attrib)',
                __METHOD__
            ), 1);
        }

        /** @var \DOMElement $element */
        foreach ($elements as $element) {
            $element->setAttribute('data-sanitized-' . $attrib, $element->getAttribute($attrib));
            $element->removeAttribute($attrib);
        }
    }

    /**
     * @param array<string, string> $valuePairs
     * @return void
     */
    protected function add_attr(string $tag, array $valuePairs, DOMDocument $document)
    {
        $elements = $document->getElementsByTagName($tag);
        /** @var \DOMElement $element */
        foreach ($elements as $element) {
            foreach ($valuePairs as $attrib => $value) {
                $element->setAttribute($attrib, $value);
            }
        }
    }

    /**
     * Get a DataCache
     *
     * @param string $image_url Only needed for BC, can be removed in SimplePie 2.0.0
     *
     * @return DataCache
     */
    private function get_cache(string $image_url = ''): DataCache
    {
        if ($this->cache === null) {
            // @trigger_error(sprintf('Not providing as PSR-16 cache implementation is deprecated since SimplePie 1.8.0, please use "SimplePie\SimplePie::set_cache()".'), \E_USER_DEPRECATED);
            $cache = $this->registry->call(Cache::class, 'get_handler', [
                $this->cache_location,
                $image_url,
                Base::TYPE_IMAGE
            ]);

            return new BaseDataCache($cache);
        }

        return $this->cache;
    }

    /**
     * Get a HTTP client
     */
    private function get_http_client(): Client
    {
        if ($this->http_client === null) {
            $this->http_client = new FileClient(
                $this->registry,
                [
                    'timeout' => $this->timeout,
                    'redirects' => 5,
                    'useragent' => $this->useragent,
                    'force_fsockopen' => $this->force_fsockopen,
                    'curl_options' => $this->curl_options,
                ]
            );
        }

        return $this->http_client;
    }
}

class_alias('SimplePie\Sanitize', 'SimplePie_Sanitize');

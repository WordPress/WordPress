<?php

// SPDX-FileCopyrightText: 2004-2023 Ryan Parman, Sam Sneddon, Ryan McCue
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace SimplePie;

use DomDocument;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use SimplePie\HTTP\Client;
use SimplePie\HTTP\ClientException;
use SimplePie\HTTP\FileClient;
use SimplePie\HTTP\Psr18Client;
use SimplePie\HTTP\Response;

/**
 * Used for feed auto-discovery
 *
 *
 * This class can be overloaded with {@see \SimplePie\SimplePie::set_locator_class()}
 */
class Locator implements RegistryAware
{
    /** @var ?string */
    public $useragent = null;
    /** @var int */
    public $timeout = 10;
    /** @var File */
    public $file;
    /** @var string[] */
    public $local = [];
    /** @var string[] */
    public $elsewhere = [];
    /** @var array<mixed> */
    public $cached_entities = [];
    /** @var string */
    public $http_base;
    /** @var string */
    public $base;
    /** @var int */
    public $base_location = 0;
    /** @var int */
    public $checked_feeds = 0;
    /** @var int */
    public $max_checked_feeds = 10;
    /** @var bool */
    public $force_fsockopen = false;
    /** @var array<int, mixed> */
    public $curl_options = [];
    /** @var ?\DomDocument */
    public $dom;
    /** @var ?Registry */
    protected $registry;

    /**
     * @var Client|null
     */
    private $http_client = null;

    /**
     * @param array<int, mixed> $curl_options
     */
    public function __construct(File $file, int $timeout = 10, ?string $useragent = null, int $max_checked_feeds = 10, bool $force_fsockopen = false, array $curl_options = [])
    {
        $this->file = $file;
        $this->useragent = $useragent;
        $this->timeout = $timeout;
        $this->max_checked_feeds = $max_checked_feeds;
        $this->force_fsockopen = $force_fsockopen;
        $this->curl_options = $curl_options;

        $body = $this->file->get_body_content();

        if (class_exists('DOMDocument') && $body != '') {
            $this->dom = new \DOMDocument();

            set_error_handler([Misc::class, 'silence_errors']);
            try {
                $this->dom->loadHTML($body);
            } catch (\Throwable $ex) {
                $this->dom = null;
            }
            restore_error_handler();
        } else {
            $this->dom = null;
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
     * @return void
     */
    public function set_registry(\SimplePie\Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param SimplePie::LOCATOR_* $type
     * @param array<Response>|null $working
     * @return Response|null
     */
    public function find(int $type = \SimplePie\SimplePie::LOCATOR_ALL, ?array &$working = null)
    {
        assert($this->registry !== null);

        if ($this->is_feed($this->file)) {
            return $this->file;
        }

        if (Misc::is_remote_uri($this->file->get_final_requested_uri())) {
            $sniffer = $this->registry->create(Content\Type\Sniffer::class, [$this->file]);
            if ($sniffer->get_type() !== 'text/html') {
                return null;
            }
        }

        if ($type & ~\SimplePie\SimplePie::LOCATOR_NONE) {
            $this->get_base();
        }

        if ($type & \SimplePie\SimplePie::LOCATOR_AUTODISCOVERY && $working = $this->autodiscovery()) {
            return $working[0];
        }

        if ($type & (\SimplePie\SimplePie::LOCATOR_LOCAL_EXTENSION | \SimplePie\SimplePie::LOCATOR_LOCAL_BODY | \SimplePie\SimplePie::LOCATOR_REMOTE_EXTENSION | \SimplePie\SimplePie::LOCATOR_REMOTE_BODY) && $this->get_links()) {
            if ($type & \SimplePie\SimplePie::LOCATOR_LOCAL_EXTENSION && $working = $this->extension($this->local)) {
                return $working[0];
            }

            if ($type & \SimplePie\SimplePie::LOCATOR_LOCAL_BODY && $working = $this->body($this->local)) {
                return $working[0];
            }

            if ($type & \SimplePie\SimplePie::LOCATOR_REMOTE_EXTENSION && $working = $this->extension($this->elsewhere)) {
                return $working[0];
            }

            if ($type & \SimplePie\SimplePie::LOCATOR_REMOTE_BODY && $working = $this->body($this->elsewhere)) {
                return $working[0];
            }
        }
        return null;
    }

    /**
     * @return bool
     */
    public function is_feed(Response $file, bool $check_html = false)
    {
        assert($this->registry !== null);

        if (Misc::is_remote_uri($file->get_final_requested_uri())) {
            $sniffer = $this->registry->create(Content\Type\Sniffer::class, [$file]);
            $sniffed = $sniffer->get_type();
            $mime_types = ['application/rss+xml', 'application/rdf+xml',
                                'text/rdf', 'application/atom+xml', 'text/xml',
                                'application/xml', 'application/x-rss+xml'];
            if ($check_html) {
                $mime_types[] = 'text/html';
            }

            return in_array($sniffed, $mime_types);
        } elseif (is_file($file->get_final_requested_uri())) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return void
     */
    public function get_base()
    {
        assert($this->registry !== null);

        if ($this->dom === null) {
            throw new \SimplePie\Exception('DOMDocument not found, unable to use locator');
        }
        $this->http_base = $this->file->get_final_requested_uri();
        $this->base = $this->http_base;
        $elements = $this->dom->getElementsByTagName('base');
        foreach ($elements as $element) {
            if ($element->hasAttribute('href')) {
                $base = $this->registry->call(Misc::class, 'absolutize_url', [trim($element->getAttribute('href')), $this->http_base]);
                if ($base === false) {
                    continue;
                }
                $this->base = $base;
                $this->base_location = method_exists($element, 'getLineNo') ? $element->getLineNo() : 0;
                break;
            }
        }
    }

    /**
     * @return array<Response>|null
     */
    public function autodiscovery()
    {
        $done = [];
        $feeds = [];
        $feeds = array_merge($feeds, $this->search_elements_by_tag('link', $done, $feeds));
        $feeds = array_merge($feeds, $this->search_elements_by_tag('a', $done, $feeds));
        $feeds = array_merge($feeds, $this->search_elements_by_tag('area', $done, $feeds));

        if (!empty($feeds)) {
            return array_values($feeds);
        }

        return null;
    }

    /**
     * @param string[] $done
     * @param array<string, Response> $feeds
     * @return array<string, Response>
     */
    protected function search_elements_by_tag(string $name, array &$done, array $feeds)
    {
        assert($this->registry !== null);

        if ($this->dom === null) {
            throw new \SimplePie\Exception('DOMDocument not found, unable to use locator');
        }

        $links = $this->dom->getElementsByTagName($name);
        foreach ($links as $link) {
            if ($this->checked_feeds === $this->max_checked_feeds) {
                break;
            }
            if ($link->hasAttribute('href') && $link->hasAttribute('rel')) {
                $rel = array_unique($this->registry->call(Misc::class, 'space_separated_tokens', [strtolower($link->getAttribute('rel'))]));
                $line = method_exists($link, 'getLineNo') ? $link->getLineNo() : 1;

                if ($this->base_location < $line) {
                    $href = $this->registry->call(Misc::class, 'absolutize_url', [trim($link->getAttribute('href')), $this->base]);
                } else {
                    $href = $this->registry->call(Misc::class, 'absolutize_url', [trim($link->getAttribute('href')), $this->http_base]);
                }
                if ($href === false) {
                    continue;
                }

                if (!in_array($href, $done) && in_array('feed', $rel) || (in_array('alternate', $rel) && !in_array('stylesheet', $rel) && $link->hasAttribute('type') && in_array(strtolower($this->registry->call(Misc::class, 'parse_mime', [$link->getAttribute('type')])), ['text/html', 'application/rss+xml', 'application/atom+xml'])) && !isset($feeds[$href])) {
                    $this->checked_feeds++;
                    $headers = [
                        'Accept' => SimplePie::DEFAULT_HTTP_ACCEPT_HEADER,
                    ];

                    try {
                        $feed = $this->get_http_client()->request(Client::METHOD_GET, $href, $headers);

                        if ((!Misc::is_remote_uri($feed->get_final_requested_uri()) || ($feed->get_status_code() === 200 || $feed->get_status_code() > 206 && $feed->get_status_code() < 300)) && $this->is_feed($feed, true)) {
                            $feeds[$href] = $feed;
                        }
                    } catch (ClientException $th) {
                        // Just mark it as done and continue.
                    }
                }
                $done[] = $href;
            }
        }

        return $feeds;
    }

    /**
     * @return true|null
     */
    public function get_links()
    {
        assert($this->registry !== null);

        if ($this->dom === null) {
            throw new \SimplePie\Exception('DOMDocument not found, unable to use locator');
        }

        $links = $this->dom->getElementsByTagName('a');
        foreach ($links as $link) {
            if ($link->hasAttribute('href')) {
                $href = trim($link->getAttribute('href'));
                $parsed = $this->registry->call(Misc::class, 'parse_url', [$href]);
                if ($parsed['scheme'] === '' || preg_match('/^(https?|feed)?$/i', $parsed['scheme'])) {
                    if (method_exists($link, 'getLineNo') && $this->base_location < $link->getLineNo()) {
                        $href = $this->registry->call(Misc::class, 'absolutize_url', [trim($link->getAttribute('href')), $this->base]);
                    } else {
                        $href = $this->registry->call(Misc::class, 'absolutize_url', [trim($link->getAttribute('href')), $this->http_base]);
                    }
                    if ($href === false) {
                        continue;
                    }

                    $current = $this->registry->call(Misc::class, 'parse_url', [$this->file->get_final_requested_uri()]);

                    if ($parsed['authority'] === '' || $parsed['authority'] === $current['authority']) {
                        $this->local[] = $href;
                    } else {
                        $this->elsewhere[] = $href;
                    }
                }
            }
        }
        $this->local = array_unique($this->local);
        $this->elsewhere = array_unique($this->elsewhere);
        if (!empty($this->local) || !empty($this->elsewhere)) {
            return true;
        }
        return null;
    }

    /**
     * Extracts first `link` element with given `rel` attribute inside the `head` element.
     *
     * @return string|null
     */
    public function get_rel_link(string $rel)
    {
        assert($this->registry !== null);

        if ($this->dom === null) {
            throw new \SimplePie\Exception('DOMDocument not found, unable to use '.
                                          'locator');
        }
        if (!class_exists('DOMXpath')) {
            throw new \SimplePie\Exception('DOMXpath not found, unable to use '.
                                          'get_rel_link');
        }

        $xpath = new \DOMXpath($this->dom);
        $query = '(//head)[1]/link[@rel and @href]';
        /** @var \DOMNodeList<\DOMElement> */
        $queryResult = $xpath->query($query);
        foreach ($queryResult as $link) {
            $href = trim($link->getAttribute('href'));
            $parsed = $this->registry->call(Misc::class, 'parse_url', [$href]);
            if ($parsed['scheme'] === '' ||
                preg_match('/^https?$/i', $parsed['scheme'])) {
                if (method_exists($link, 'getLineNo') &&
                    $this->base_location < $link->getLineNo()) {
                    $href = $this->registry->call(
                        Misc::class,
                        'absolutize_url',
                        [trim($link->getAttribute('href')), $this->base]
                    );
                } else {
                    $href = $this->registry->call(
                        Misc::class,
                        'absolutize_url',
                        [trim($link->getAttribute('href')), $this->http_base]
                    );
                }
                if ($href === false) {
                    return null;
                }
                $rel_values = explode(' ', strtolower($link->getAttribute('rel')));
                if (in_array($rel, $rel_values)) {
                    return $href;
                }
            }
        }

        return null;
    }

    /**
     * @param string[] $array
     * @return array<Response>|null
     */
    public function extension(array &$array)
    {
        foreach ($array as $key => $value) {
            if ($this->checked_feeds === $this->max_checked_feeds) {
                break;
            }
            $extension = strrchr($value, '.');
            if ($extension !== false && in_array(strtolower($extension), ['.rss', '.rdf', '.atom', '.xml'])) {
                $this->checked_feeds++;

                $headers = [
                    'Accept' => SimplePie::DEFAULT_HTTP_ACCEPT_HEADER,
                ];

                try {
                    $feed = $this->get_http_client()->request(Client::METHOD_GET, $value, $headers);

                    if ((!Misc::is_remote_uri($feed->get_final_requested_uri()) || ($feed->get_status_code() === 200 || $feed->get_status_code() > 206 && $feed->get_status_code() < 300)) && $this->is_feed($feed)) {
                        return [$feed];
                    }
                } catch (ClientException $th) {
                    // Just unset and continue.
                }

                unset($array[$key]);
            }
        }
        return null;
    }

    /**
     * @param string[] $array
     * @return array<Response>|null
     */
    public function body(array &$array)
    {
        foreach ($array as $key => $value) {
            if ($this->checked_feeds === $this->max_checked_feeds) {
                break;
            }
            if (preg_match('/(feed|rss|rdf|atom|xml)/i', $value)) {
                $this->checked_feeds++;
                $headers = [
                    'Accept' => SimplePie::DEFAULT_HTTP_ACCEPT_HEADER,
                ];

                try {
                    $feed = $this->get_http_client()->request(Client::METHOD_GET, $value, $headers);

                    if ((!Misc::is_remote_uri($feed->get_final_requested_uri()) || ($feed->get_status_code() === 200 || $feed->get_status_code() > 206 && $feed->get_status_code() < 300)) && $this->is_feed($feed)) {
                        return [$feed];
                    }
                } catch (ClientException $th) {
                    // Just unset and continue.
                }

                unset($array[$key]);
            }
        }
        return null;
    }

    /**
     * Get a HTTP client
     */
    private function get_http_client(): Client
    {
        assert($this->registry !== null);

        if ($this->http_client === null) {
            $options = [
                'timeout' => $this->timeout,
                'redirects' => 5,
                'force_fsockopen' => $this->force_fsockopen,
                'curl_options' => $this->curl_options,
            ];

            if ($this->useragent !== null) {
                $options['useragent'] = $this->useragent;
            }

            return new FileClient(
                $this->registry,
                $options
            );
        }

        return $this->http_client;
    }
}

class_alias('SimplePie\Locator', 'SimplePie_Locator', false);

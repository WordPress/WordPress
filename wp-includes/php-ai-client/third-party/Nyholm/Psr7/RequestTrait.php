<?php

declare (strict_types=1);
namespace WordPress\AiClientDependencies\Nyholm\Psr7;

use WordPress\AiClientDependencies\Psr\Http\Message\RequestInterface;
use WordPress\AiClientDependencies\Psr\Http\Message\UriInterface;
/**
 * @author Michael Dowling and contributors to guzzlehttp/psr7
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 * @author Martijn van der Ven <martijn@vanderven.se>
 *
 * @internal should not be used outside of Nyholm/Psr7 as it does not fall under our BC promise
 */
trait RequestTrait
{
    /** @var string */
    private $method;
    /** @var string|null */
    private $requestTarget;
    /** @var UriInterface|null */
    private $uri;
    public function getRequestTarget(): string
    {
        if (null !== $this->requestTarget) {
            return $this->requestTarget;
        }
        if ('' === $target = $this->uri->getPath()) {
            $target = '/';
        }
        if ('' !== $this->uri->getQuery()) {
            $target .= '?' . $this->uri->getQuery();
        }
        return $target;
    }
    /**
     * @return static
     */
    public function withRequestTarget($requestTarget): RequestInterface
    {
        if (!\is_string($requestTarget)) {
            throw new \InvalidArgumentException('Request target must be a string');
        }
        if (\preg_match('#\s#', $requestTarget)) {
            throw new \InvalidArgumentException('Invalid request target provided; cannot contain whitespace');
        }
        $new = clone $this;
        $new->requestTarget = $requestTarget;
        return $new;
    }
    public function getMethod(): string
    {
        return $this->method;
    }
    /**
     * @return static
     */
    public function withMethod($method): RequestInterface
    {
        if (!\is_string($method)) {
            throw new \InvalidArgumentException('Method must be a string');
        }
        $new = clone $this;
        $new->method = $method;
        return $new;
    }
    public function getUri(): UriInterface
    {
        return $this->uri;
    }
    /**
     * @return static
     */
    public function withUri(UriInterface $uri, $preserveHost = \false): RequestInterface
    {
        if ($uri === $this->uri) {
            return $this;
        }
        $new = clone $this;
        $new->uri = $uri;
        if (!$preserveHost || !$this->hasHeader('Host')) {
            $new->updateHostFromUri();
        }
        return $new;
    }
    private function updateHostFromUri(): void
    {
        if ('' === $host = $this->uri->getHost()) {
            return;
        }
        if (null !== $port = $this->uri->getPort()) {
            $host .= ':' . $port;
        }
        if (isset($this->headerNames['host'])) {
            $header = $this->headerNames['host'];
        } else {
            $this->headerNames['host'] = $header = 'Host';
        }
        // Ensure Host is the first header.
        // See: http://tools.ietf.org/html/rfc7230#section-5.4
        $this->headers = [$header => [$host]] + $this->headers;
    }
}

<?php

declare (strict_types=1);
namespace WordPress\AiClientDependencies\Nyholm\Psr7;

use WordPress\AiClientDependencies\Psr\Http\Message\ServerRequestInterface;
use WordPress\AiClientDependencies\Psr\Http\Message\StreamInterface;
use WordPress\AiClientDependencies\Psr\Http\Message\UploadedFileInterface;
use WordPress\AiClientDependencies\Psr\Http\Message\UriInterface;
/**
 * @author Michael Dowling and contributors to guzzlehttp/psr7
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 * @author Martijn van der Ven <martijn@vanderven.se>
 *
 * @final This class should never be extended. See https://github.com/Nyholm/psr7/blob/master/doc/final.md
 */
class ServerRequest implements ServerRequestInterface
{
    use MessageTrait;
    use RequestTrait;
    /** @var array */
    private $attributes = [];
    /** @var array */
    private $cookieParams = [];
    /** @var array|object|null */
    private $parsedBody;
    /** @var array */
    private $queryParams = [];
    /** @var array */
    private $serverParams;
    /** @var UploadedFileInterface[] */
    private $uploadedFiles = [];
    /**
     * @param string $method HTTP method
     * @param string|UriInterface $uri URI
     * @param array $headers Request headers
     * @param string|resource|StreamInterface|null $body Request body
     * @param string $version Protocol version
     * @param array $serverParams Typically the $_SERVER superglobal
     */
    public function __construct(string $method, $uri, array $headers = [], $body = null, string $version = '1.1', array $serverParams = [])
    {
        $this->serverParams = $serverParams;
        if (!$uri instanceof UriInterface) {
            $uri = new Uri($uri);
        }
        $this->method = $method;
        $this->uri = $uri;
        $this->setHeaders($headers);
        $this->protocol = $version;
        \parse_str($uri->getQuery(), $this->queryParams);
        if (!$this->hasHeader('Host')) {
            $this->updateHostFromUri();
        }
        // If we got no body, defer initialization of the stream until ServerRequest::getBody()
        if ('' !== $body && null !== $body) {
            $this->stream = Stream::create($body);
        }
    }
    public function getServerParams(): array
    {
        return $this->serverParams;
    }
    public function getUploadedFiles(): array
    {
        return $this->uploadedFiles;
    }
    /**
     * @return static
     */
    public function withUploadedFiles(array $uploadedFiles): ServerRequestInterface
    {
        $new = clone $this;
        $new->uploadedFiles = $uploadedFiles;
        return $new;
    }
    public function getCookieParams(): array
    {
        return $this->cookieParams;
    }
    /**
     * @return static
     */
    public function withCookieParams(array $cookies): ServerRequestInterface
    {
        $new = clone $this;
        $new->cookieParams = $cookies;
        return $new;
    }
    public function getQueryParams(): array
    {
        return $this->queryParams;
    }
    /**
     * @return static
     */
    public function withQueryParams(array $query): ServerRequestInterface
    {
        $new = clone $this;
        $new->queryParams = $query;
        return $new;
    }
    /**
     * @return array|object|null
     */
    public function getParsedBody()
    {
        return $this->parsedBody;
    }
    /**
     * @return static
     */
    public function withParsedBody($data): ServerRequestInterface
    {
        if (!\is_array($data) && !\is_object($data) && null !== $data) {
            throw new \InvalidArgumentException('First parameter to withParsedBody MUST be object, array or null');
        }
        $new = clone $this;
        $new->parsedBody = $data;
        return $new;
    }
    public function getAttributes(): array
    {
        return $this->attributes;
    }
    /**
     * @return mixed
     */
    public function getAttribute($attribute, $default = null)
    {
        if (!\is_string($attribute)) {
            throw new \InvalidArgumentException('Attribute name must be a string');
        }
        if (\false === \array_key_exists($attribute, $this->attributes)) {
            return $default;
        }
        return $this->attributes[$attribute];
    }
    /**
     * @return static
     */
    public function withAttribute($attribute, $value): ServerRequestInterface
    {
        if (!\is_string($attribute)) {
            throw new \InvalidArgumentException('Attribute name must be a string');
        }
        $new = clone $this;
        $new->attributes[$attribute] = $value;
        return $new;
    }
    /**
     * @return static
     */
    public function withoutAttribute($attribute): ServerRequestInterface
    {
        if (!\is_string($attribute)) {
            throw new \InvalidArgumentException('Attribute name must be a string');
        }
        if (\false === \array_key_exists($attribute, $this->attributes)) {
            return $this;
        }
        $new = clone $this;
        unset($new->attributes[$attribute]);
        return $new;
    }
}

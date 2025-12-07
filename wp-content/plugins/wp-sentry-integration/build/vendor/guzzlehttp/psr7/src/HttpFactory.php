<?php

declare (strict_types=1);
namespace WPSentry\ScopedVendor\GuzzleHttp\Psr7;

use WPSentry\ScopedVendor\Psr\Http\Message\RequestFactoryInterface;
use WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface;
use WPSentry\ScopedVendor\Psr\Http\Message\ResponseFactoryInterface;
use WPSentry\ScopedVendor\Psr\Http\Message\ResponseInterface;
use WPSentry\ScopedVendor\Psr\Http\Message\ServerRequestFactoryInterface;
use WPSentry\ScopedVendor\Psr\Http\Message\ServerRequestInterface;
use WPSentry\ScopedVendor\Psr\Http\Message\StreamFactoryInterface;
use WPSentry\ScopedVendor\Psr\Http\Message\StreamInterface;
use WPSentry\ScopedVendor\Psr\Http\Message\UploadedFileFactoryInterface;
use WPSentry\ScopedVendor\Psr\Http\Message\UploadedFileInterface;
use WPSentry\ScopedVendor\Psr\Http\Message\UriFactoryInterface;
use WPSentry\ScopedVendor\Psr\Http\Message\UriInterface;
/**
 * Implements all of the PSR-17 interfaces.
 *
 * Note: in consuming code it is recommended to require the implemented interfaces
 * and inject the instance of this class multiple times.
 */
final class HttpFactory implements \WPSentry\ScopedVendor\Psr\Http\Message\RequestFactoryInterface, \WPSentry\ScopedVendor\Psr\Http\Message\ResponseFactoryInterface, \WPSentry\ScopedVendor\Psr\Http\Message\ServerRequestFactoryInterface, \WPSentry\ScopedVendor\Psr\Http\Message\StreamFactoryInterface, \WPSentry\ScopedVendor\Psr\Http\Message\UploadedFileFactoryInterface, \WPSentry\ScopedVendor\Psr\Http\Message\UriFactoryInterface
{
    public function createUploadedFile(\WPSentry\ScopedVendor\Psr\Http\Message\StreamInterface $stream, ?int $size = null, int $error = \UPLOAD_ERR_OK, ?string $clientFilename = null, ?string $clientMediaType = null) : \WPSentry\ScopedVendor\Psr\Http\Message\UploadedFileInterface
    {
        if ($size === null) {
            $size = $stream->getSize();
        }
        return new \WPSentry\ScopedVendor\GuzzleHttp\Psr7\UploadedFile($stream, $size, $error, $clientFilename, $clientMediaType);
    }
    public function createStream(string $content = '') : \WPSentry\ScopedVendor\Psr\Http\Message\StreamInterface
    {
        return \WPSentry\ScopedVendor\GuzzleHttp\Psr7\Utils::streamFor($content);
    }
    public function createStreamFromFile(string $file, string $mode = 'r') : \WPSentry\ScopedVendor\Psr\Http\Message\StreamInterface
    {
        try {
            $resource = \WPSentry\ScopedVendor\GuzzleHttp\Psr7\Utils::tryFopen($file, $mode);
        } catch (\RuntimeException $e) {
            if ('' === $mode || \false === \in_array($mode[0], ['r', 'w', 'a', 'x', 'c'], \true)) {
                throw new \InvalidArgumentException(\sprintf('Invalid file opening mode "%s"', $mode), 0, $e);
            }
            throw $e;
        }
        return \WPSentry\ScopedVendor\GuzzleHttp\Psr7\Utils::streamFor($resource);
    }
    public function createStreamFromResource($resource) : \WPSentry\ScopedVendor\Psr\Http\Message\StreamInterface
    {
        return \WPSentry\ScopedVendor\GuzzleHttp\Psr7\Utils::streamFor($resource);
    }
    public function createServerRequest(string $method, $uri, array $serverParams = []) : \WPSentry\ScopedVendor\Psr\Http\Message\ServerRequestInterface
    {
        if (empty($method)) {
            if (!empty($serverParams['REQUEST_METHOD'])) {
                $method = $serverParams['REQUEST_METHOD'];
            } else {
                throw new \InvalidArgumentException('Cannot determine HTTP method');
            }
        }
        return new \WPSentry\ScopedVendor\GuzzleHttp\Psr7\ServerRequest($method, $uri, [], null, '1.1', $serverParams);
    }
    public function createResponse(int $code = 200, string $reasonPhrase = '') : \WPSentry\ScopedVendor\Psr\Http\Message\ResponseInterface
    {
        return new \WPSentry\ScopedVendor\GuzzleHttp\Psr7\Response($code, [], null, '1.1', $reasonPhrase);
    }
    public function createRequest(string $method, $uri) : \WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface
    {
        return new \WPSentry\ScopedVendor\GuzzleHttp\Psr7\Request($method, $uri);
    }
    public function createUri(string $uri = '') : \WPSentry\ScopedVendor\Psr\Http\Message\UriInterface
    {
        return new \WPSentry\ScopedVendor\GuzzleHttp\Psr7\Uri($uri);
    }
}

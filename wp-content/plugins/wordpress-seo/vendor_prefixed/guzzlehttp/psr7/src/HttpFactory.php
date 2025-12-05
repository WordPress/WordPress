<?php

declare (strict_types=1);
namespace YoastSEO_Vendor\GuzzleHttp\Psr7;

use YoastSEO_Vendor\Psr\Http\Message\RequestFactoryInterface;
use YoastSEO_Vendor\Psr\Http\Message\RequestInterface;
use YoastSEO_Vendor\Psr\Http\Message\ResponseFactoryInterface;
use YoastSEO_Vendor\Psr\Http\Message\ResponseInterface;
use YoastSEO_Vendor\Psr\Http\Message\ServerRequestFactoryInterface;
use YoastSEO_Vendor\Psr\Http\Message\ServerRequestInterface;
use YoastSEO_Vendor\Psr\Http\Message\StreamFactoryInterface;
use YoastSEO_Vendor\Psr\Http\Message\StreamInterface;
use YoastSEO_Vendor\Psr\Http\Message\UploadedFileFactoryInterface;
use YoastSEO_Vendor\Psr\Http\Message\UploadedFileInterface;
use YoastSEO_Vendor\Psr\Http\Message\UriFactoryInterface;
use YoastSEO_Vendor\Psr\Http\Message\UriInterface;
/**
 * Implements all of the PSR-17 interfaces.
 *
 * Note: in consuming code it is recommended to require the implemented interfaces
 * and inject the instance of this class multiple times.
 */
final class HttpFactory implements \YoastSEO_Vendor\Psr\Http\Message\RequestFactoryInterface, \YoastSEO_Vendor\Psr\Http\Message\ResponseFactoryInterface, \YoastSEO_Vendor\Psr\Http\Message\ServerRequestFactoryInterface, \YoastSEO_Vendor\Psr\Http\Message\StreamFactoryInterface, \YoastSEO_Vendor\Psr\Http\Message\UploadedFileFactoryInterface, \YoastSEO_Vendor\Psr\Http\Message\UriFactoryInterface
{
    public function createUploadedFile(\YoastSEO_Vendor\Psr\Http\Message\StreamInterface $stream, int $size = null, int $error = \UPLOAD_ERR_OK, string $clientFilename = null, string $clientMediaType = null) : \YoastSEO_Vendor\Psr\Http\Message\UploadedFileInterface
    {
        if ($size === null) {
            $size = $stream->getSize();
        }
        return new \YoastSEO_Vendor\GuzzleHttp\Psr7\UploadedFile($stream, $size, $error, $clientFilename, $clientMediaType);
    }
    public function createStream(string $content = '') : \YoastSEO_Vendor\Psr\Http\Message\StreamInterface
    {
        return \YoastSEO_Vendor\GuzzleHttp\Psr7\Utils::streamFor($content);
    }
    public function createStreamFromFile(string $file, string $mode = 'r') : \YoastSEO_Vendor\Psr\Http\Message\StreamInterface
    {
        try {
            $resource = \YoastSEO_Vendor\GuzzleHttp\Psr7\Utils::tryFopen($file, $mode);
        } catch (\RuntimeException $e) {
            if ('' === $mode || \false === \in_array($mode[0], ['r', 'w', 'a', 'x', 'c'], \true)) {
                throw new \InvalidArgumentException(\sprintf('Invalid file opening mode "%s"', $mode), 0, $e);
            }
            throw $e;
        }
        return \YoastSEO_Vendor\GuzzleHttp\Psr7\Utils::streamFor($resource);
    }
    public function createStreamFromResource($resource) : \YoastSEO_Vendor\Psr\Http\Message\StreamInterface
    {
        return \YoastSEO_Vendor\GuzzleHttp\Psr7\Utils::streamFor($resource);
    }
    public function createServerRequest(string $method, $uri, array $serverParams = []) : \YoastSEO_Vendor\Psr\Http\Message\ServerRequestInterface
    {
        if (empty($method)) {
            if (!empty($serverParams['REQUEST_METHOD'])) {
                $method = $serverParams['REQUEST_METHOD'];
            } else {
                throw new \InvalidArgumentException('Cannot determine HTTP method');
            }
        }
        return new \YoastSEO_Vendor\GuzzleHttp\Psr7\ServerRequest($method, $uri, [], null, '1.1', $serverParams);
    }
    public function createResponse(int $code = 200, string $reasonPhrase = '') : \YoastSEO_Vendor\Psr\Http\Message\ResponseInterface
    {
        return new \YoastSEO_Vendor\GuzzleHttp\Psr7\Response($code, [], null, '1.1', $reasonPhrase);
    }
    public function createRequest(string $method, $uri) : \YoastSEO_Vendor\Psr\Http\Message\RequestInterface
    {
        return new \YoastSEO_Vendor\GuzzleHttp\Psr7\Request($method, $uri);
    }
    public function createUri(string $uri = '') : \YoastSEO_Vendor\Psr\Http\Message\UriInterface
    {
        return new \YoastSEO_Vendor\GuzzleHttp\Psr7\Uri($uri);
    }
}

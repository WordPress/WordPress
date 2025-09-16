<?php

// SPDX-FileCopyrightText: 2004-2023 Ryan Parman, Sam Sneddon, Ryan McCue
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace SimplePie\HTTP;

use InvalidArgumentException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use Throwable;

/**
 * HTTP Client based on PSR-18 and PSR-17 implementations
 *
 * @internal
 */
final class Psr18Client implements Client
{
    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * @var RequestFactoryInterface
     */
    private $requestFactory;

    /**
     * @var UriFactoryInterface
     */
    private $uriFactory;

    /**
     * @var int
     */
    private $allowedRedirects = 5;

    public function __construct(ClientInterface $httpClient, RequestFactoryInterface $requestFactory, UriFactoryInterface $uriFactory)
    {
        $this->httpClient = $httpClient;
        $this->requestFactory = $requestFactory;
        $this->uriFactory = $uriFactory;
    }

    public function getHttpClient(): ClientInterface
    {
        return $this->httpClient;
    }

    public function getRequestFactory(): RequestFactoryInterface
    {
        return $this->requestFactory;
    }

    public function getUriFactory(): UriFactoryInterface
    {
        return $this->uriFactory;
    }

    /**
     * send a request and return the response
     *
     * @param Client::METHOD_* $method
     * @param string $url
     * @param array<string,string|string[]> $headers
     *
     * @throws ClientException if anything goes wrong requesting the data
     */
    public function request(string $method, string $url, array $headers = []): Response
    {
        if ($method !== self::METHOD_GET) {
            throw new InvalidArgumentException(sprintf(
                '%s(): Argument #1 ($method) only supports method "%s".',
                __METHOD__,
                self::METHOD_GET
            ), 1);
        }

        if (preg_match('/^http(s)?:\/\//i', $url)) {
            return $this->requestUrl($method, $url, $headers);
        }

        return $this->requestLocalFile($url);
    }

    /**
     * @param array<string,string|string[]> $headers
     */
    private function requestUrl(string $method, string $url, array $headers): Response
    {
        $permanentUrl = $url;
        $requestedUrl = $url;
        $remainingRedirects = $this->allowedRedirects;

        $request = $this->requestFactory->createRequest(
            $method,
            $this->uriFactory->createUri($requestedUrl)
        );

        foreach ($headers as $name => $value) {
            $request = $request->withHeader($name, $value);
        }

        do {
            $followRedirect = false;

            try {
                $response = $this->httpClient->sendRequest($request);
            } catch (ClientExceptionInterface $th) {
                throw new ClientException($th->getMessage(), $th->getCode(), $th);
            }

            $statusCode = $response->getStatusCode();

            // If we have a redirect
            if (in_array($statusCode, [300, 301, 302, 303, 307]) && $response->hasHeader('Location')) {
                // Prevent infinity redirect loops
                if ($remainingRedirects <= 0) {
                    break;
                }

                $remainingRedirects--;
                $followRedirect = true;

                $requestedUrl = $response->getHeaderLine('Location');

                if ($statusCode === 301) {
                    $permanentUrl = $requestedUrl;
                }

                $request = $request->withUri($this->uriFactory->createUri($requestedUrl));
            }
        } while ($followRedirect);

        return new Psr7Response($response, $permanentUrl, $requestedUrl);
    }

    private function requestLocalFile(string $path): Response
    {
        if (!is_readable($path)) {
            throw new ClientException(sprintf('file "%s" is not readable', $path));
        }

        try {
            $raw = file_get_contents($path);
        } catch (Throwable $th) {
            throw new ClientException($th->getMessage(), $th->getCode(), $th);
        }

        if ($raw === false) {
            throw new ClientException('file_get_contents() could not read the file', 1);
        }

        return new RawTextResponse($raw, $path);
    }
}

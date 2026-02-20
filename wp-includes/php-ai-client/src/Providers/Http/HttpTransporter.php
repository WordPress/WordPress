<?php

declare (strict_types=1);
namespace WordPress\AiClient\Providers\Http;

use WordPress\AiClientDependencies\Http\Discovery\Psr17FactoryDiscovery;
use WordPress\AiClientDependencies\Http\Discovery\Psr18ClientDiscovery;
use WordPress\AiClientDependencies\Psr\Http\Client\ClientInterface;
use WordPress\AiClientDependencies\Psr\Http\Message\RequestFactoryInterface;
use WordPress\AiClientDependencies\Psr\Http\Message\RequestInterface;
use WordPress\AiClientDependencies\Psr\Http\Message\ResponseInterface;
use WordPress\AiClientDependencies\Psr\Http\Message\StreamFactoryInterface;
use WordPress\AiClient\Common\Exception\RuntimeException;
use WordPress\AiClient\Providers\Http\Contracts\ClientWithOptionsInterface;
use WordPress\AiClient\Providers\Http\Contracts\HttpTransporterInterface;
use WordPress\AiClient\Providers\Http\DTO\Request;
use WordPress\AiClient\Providers\Http\DTO\RequestOptions;
use WordPress\AiClient\Providers\Http\DTO\Response;
use WordPress\AiClient\Providers\Http\Exception\NetworkException;
/**
 * HTTP transporter implementation using HTTPlug.
 *
 * This class handles the conversion between custom Request/Response
 * objects and PSR-7 messages, using HTTPlug for client abstraction
 * and PSR-17 factories for message creation.
 *
 * @since 0.1.0
 */
class HttpTransporter implements HttpTransporterInterface
{
    /**
     * @var RequestFactoryInterface PSR-17 request factory.
     */
    private RequestFactoryInterface $requestFactory;
    /**
     * @var StreamFactoryInterface PSR-17 stream factory.
     */
    private StreamFactoryInterface $streamFactory;
    /**
     * @var ClientInterface PSR-18 HTTP client.
     */
    private ClientInterface $client;
    /**
     * Constructor.
     *
     * @since 0.1.0
     *
     * @param ClientInterface|null $client PSR-18 HTTP client.
     * @param RequestFactoryInterface|null $requestFactory PSR-17 request factory.
     * @param StreamFactoryInterface|null $streamFactory PSR-17 stream factory.
     */
    public function __construct(?ClientInterface $client = null, ?RequestFactoryInterface $requestFactory = null, ?StreamFactoryInterface $streamFactory = null)
    {
        $this->client = $client ?: Psr18ClientDiscovery::find();
        $this->requestFactory = $requestFactory ?: Psr17FactoryDiscovery::findRequestFactory();
        $this->streamFactory = $streamFactory ?: Psr17FactoryDiscovery::findStreamFactory();
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     * @since 0.2.0 Added optional RequestOptions parameter and ClientWithOptions support.
     */
    public function send(Request $request, ?RequestOptions $options = null): Response
    {
        $psr7Request = $this->convertToPsr7Request($request);
        // Merge request options with parameter options, with parameter options taking precedence
        $mergedOptions = $this->mergeOptions($request->getOptions(), $options);
        try {
            $hasOptions = $mergedOptions !== null;
            if ($hasOptions && $this->client instanceof ClientWithOptionsInterface) {
                $psr7Response = $this->client->sendRequestWithOptions($psr7Request, $mergedOptions);
            } elseif ($hasOptions && $this->isGuzzleClient($this->client)) {
                $psr7Response = $this->sendWithGuzzle($psr7Request, $mergedOptions);
            } else {
                $psr7Response = $this->client->sendRequest($psr7Request);
            }
        } catch (\WordPress\AiClientDependencies\Psr\Http\Client\NetworkExceptionInterface $e) {
            throw NetworkException::fromPsr18NetworkException($psr7Request, $e);
        } catch (\WordPress\AiClientDependencies\Psr\Http\Client\ClientExceptionInterface $e) {
            // Handle other PSR-18 client exceptions that are not network-related
            throw new RuntimeException(sprintf('HTTP client error occurred while sending request to %s: %s', $request->getUri(), $e->getMessage()), 0, $e);
        }
        return $this->convertFromPsr7Response($psr7Response);
    }
    /**
     * Merges request options with parameter options taking precedence.
     *
     * @since 0.2.0
     *
     * @param RequestOptions|null $requestOptions Options from the Request object.
     * @param RequestOptions|null $parameterOptions Options passed as method parameter.
     * @return RequestOptions|null Merged options, or null if both are null.
     */
    private function mergeOptions(?RequestOptions $requestOptions, ?RequestOptions $parameterOptions): ?RequestOptions
    {
        // If no options at all, return null
        if ($requestOptions === null && $parameterOptions === null) {
            return null;
        }
        // If only one set of options exists, return it
        if ($requestOptions === null) {
            return $parameterOptions;
        }
        if ($parameterOptions === null) {
            return $requestOptions;
        }
        // Both exist, merge them with parameter options taking precedence
        $merged = new RequestOptions();
        // Start with request options (lower precedence)
        if ($requestOptions->getTimeout() !== null) {
            $merged->setTimeout($requestOptions->getTimeout());
        }
        if ($requestOptions->getConnectTimeout() !== null) {
            $merged->setConnectTimeout($requestOptions->getConnectTimeout());
        }
        if ($requestOptions->getMaxRedirects() !== null) {
            $merged->setMaxRedirects($requestOptions->getMaxRedirects());
        }
        // Override with parameter options (higher precedence)
        if ($parameterOptions->getTimeout() !== null) {
            $merged->setTimeout($parameterOptions->getTimeout());
        }
        if ($parameterOptions->getConnectTimeout() !== null) {
            $merged->setConnectTimeout($parameterOptions->getConnectTimeout());
        }
        if ($parameterOptions->getMaxRedirects() !== null) {
            $merged->setMaxRedirects($parameterOptions->getMaxRedirects());
        }
        return $merged;
    }
    /**
     * Determines if the underlying client matches the Guzzle client shape.
     *
     * @since 0.2.0
     *
     * @param ClientInterface $client The HTTP client instance.
     * @return bool True when the client exposes Guzzle's send signature.
     */
    private function isGuzzleClient(ClientInterface $client): bool
    {
        $reflection = new \ReflectionObject($client);
        if (!is_callable([$client, 'send'])) {
            return \false;
        }
        if (!$reflection->hasMethod('send')) {
            return \false;
        }
        $method = $reflection->getMethod('send');
        if (!$method->isPublic() || $method->isStatic()) {
            return \false;
        }
        $parameters = $method->getParameters();
        if (count($parameters) < 2) {
            return \false;
        }
        $firstParameter = $parameters[0]->getType();
        if (!$firstParameter instanceof \ReflectionNamedType || $firstParameter->isBuiltin()) {
            return \false;
        }
        if (!is_a($firstParameter->getName(), RequestInterface::class, \true)) {
            return \false;
        }
        $secondParameter = $parameters[1];
        $secondType = $secondParameter->getType();
        if (!$secondType instanceof \ReflectionNamedType || $secondType->getName() !== 'array') {
            return \false;
        }
        return \true;
    }
    /**
     * Sends a request using a Guzzle-compatible client.
     *
     * @since 0.2.0
     *
     * @param RequestInterface $request The PSR-7 request to send.
     * @param RequestOptions $options The request options.
     * @return ResponseInterface The PSR-7 response received.
     */
    private function sendWithGuzzle(RequestInterface $request, RequestOptions $options): ResponseInterface
    {
        $guzzleOptions = $this->buildGuzzleOptions($options);
        /** @var callable $callable */
        $callable = [$this->client, 'send'];
        /** @var ResponseInterface $response */
        $response = $callable($request, $guzzleOptions);
        return $response;
    }
    /**
     * Converts request options to a Guzzle-compatible options array.
     *
     * @since 0.2.0
     *
     * @param RequestOptions $options The request options.
     * @return array<string, mixed> Guzzle-compatible options.
     */
    private function buildGuzzleOptions(RequestOptions $options): array
    {
        $guzzleOptions = [];
        $timeout = $options->getTimeout();
        if ($timeout !== null) {
            $guzzleOptions['timeout'] = $timeout;
        }
        $connectTimeout = $options->getConnectTimeout();
        if ($connectTimeout !== null) {
            $guzzleOptions['connect_timeout'] = $connectTimeout;
        }
        $allowRedirects = $options->allowsRedirects();
        if ($allowRedirects !== null) {
            if ($allowRedirects) {
                $redirectOptions = [];
                $maxRedirects = $options->getMaxRedirects();
                if ($maxRedirects !== null) {
                    $redirectOptions['max'] = $maxRedirects;
                }
                $guzzleOptions['allow_redirects'] = !empty($redirectOptions) ? $redirectOptions : \true;
            } else {
                $guzzleOptions['allow_redirects'] = \false;
            }
        }
        return $guzzleOptions;
    }
    /**
     * Converts a custom Request to a PSR-7 request.
     *
     * @since 0.1.0
     *
     * @param Request $request The custom request.
     * @return RequestInterface The PSR-7 request.
     */
    private function convertToPsr7Request(Request $request): RequestInterface
    {
        $psr7Request = $this->requestFactory->createRequest($request->getMethod()->value, $request->getUri());
        // Add headers
        foreach ($request->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                $psr7Request = $psr7Request->withAddedHeader($name, $value);
            }
        }
        // Add body if present
        $body = $request->getBody();
        if ($body !== null) {
            $stream = $this->streamFactory->createStream($body);
            $psr7Request = $psr7Request->withBody($stream);
        }
        return $psr7Request;
    }
    /**
     * Converts a PSR-7 response to a custom Response.
     *
     * @since 0.1.0
     *
     * @param ResponseInterface $psr7Response The PSR-7 response.
     * @return Response The custom response.
     */
    private function convertFromPsr7Response(ResponseInterface $psr7Response): Response
    {
        $body = (string) $psr7Response->getBody();
        // PSR-7 always returns headers as arrays, but HeadersCollection handles this
        return new Response(
            $psr7Response->getStatusCode(),
            $psr7Response->getHeaders(),
            // @phpstan-ignore-line
            $body === '' ? null : $body
        );
    }
}

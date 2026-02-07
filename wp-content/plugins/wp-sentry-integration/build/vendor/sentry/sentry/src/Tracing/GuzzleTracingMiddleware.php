<?php

declare (strict_types=1);
namespace Sentry\Tracing;

use WPSentry\ScopedVendor\GuzzleHttp\Exception\RequestException as GuzzleRequestException;
use WPSentry\ScopedVendor\GuzzleHttp\Psr7\Uri;
use WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface;
use WPSentry\ScopedVendor\Psr\Http\Message\ResponseInterface;
use Sentry\Breadcrumb;
use Sentry\ClientInterface;
use Sentry\SentrySdk;
use Sentry\State\HubInterface;
use function Sentry\getBaggage;
use function Sentry\getTraceparent;
/**
 * This handler traces each outgoing HTTP request by recording performance data.
 */
final class GuzzleTracingMiddleware
{
    public static function trace(?\Sentry\State\HubInterface $hub = null) : \Closure
    {
        return static function (callable $handler) use($hub) : \Closure {
            return static function (\WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface $request, array $options) use($hub, $handler) {
                $hub = $hub ?? \Sentry\SentrySdk::getCurrentHub();
                $client = $hub->getClient();
                $parentSpan = $hub->getSpan();
                $partialUri = \WPSentry\ScopedVendor\GuzzleHttp\Psr7\Uri::fromParts(['scheme' => $request->getUri()->getScheme(), 'host' => $request->getUri()->getHost(), 'port' => $request->getUri()->getPort(), 'path' => $request->getUri()->getPath()]);
                $spanAndBreadcrumbData = ['http.request.method' => $request->getMethod(), 'http.request.body.size' => $request->getBody()->getSize()];
                if ($request->getUri()->getQuery() !== '') {
                    $spanAndBreadcrumbData['http.query'] = $request->getUri()->getQuery();
                }
                if ($request->getUri()->getFragment() !== '') {
                    $spanAndBreadcrumbData['http.fragment'] = $request->getUri()->getFragment();
                }
                $childSpan = null;
                if ($parentSpan !== null && $parentSpan->getSampled()) {
                    $spanContext = new \Sentry\Tracing\SpanContext();
                    $spanContext->setOp('http.client');
                    $spanContext->setData($spanAndBreadcrumbData);
                    $spanContext->setOrigin('auto.http.guzzle');
                    $spanContext->setDescription($request->getMethod() . ' ' . $partialUri);
                    $childSpan = $parentSpan->startChild($spanContext);
                    $hub->setSpan($childSpan);
                }
                if (self::shouldAttachTracingHeaders($client, $request)) {
                    $request = $request->withHeader('sentry-trace', \Sentry\getTraceparent())->withHeader('baggage', \Sentry\getBaggage());
                }
                $handlerPromiseCallback = static function ($responseOrException) use($hub, $spanAndBreadcrumbData, $childSpan, $parentSpan, $partialUri) {
                    if ($childSpan !== null) {
                        // We finish the span (which means setting the span end timestamp) first to ensure the measured time
                        // the span spans is as close to only the HTTP request time and do the data collection afterwards
                        $childSpan->finish();
                        $hub->setSpan($parentSpan);
                    }
                    $response = null;
                    /** @psalm-suppress UndefinedClass */
                    if ($responseOrException instanceof \WPSentry\ScopedVendor\Psr\Http\Message\ResponseInterface) {
                        $response = $responseOrException;
                    } elseif ($responseOrException instanceof \WPSentry\ScopedVendor\GuzzleHttp\Exception\RequestException) {
                        $response = $responseOrException->getResponse();
                    }
                    $breadcrumbLevel = \Sentry\Breadcrumb::LEVEL_INFO;
                    if ($response !== null) {
                        $spanAndBreadcrumbData['http.response.body.size'] = $response->getBody()->getSize();
                        $spanAndBreadcrumbData['http.response.status_code'] = $response->getStatusCode();
                        if ($response->getStatusCode() >= 400 && $response->getStatusCode() < 500) {
                            $breadcrumbLevel = \Sentry\Breadcrumb::LEVEL_WARNING;
                        } elseif ($response->getStatusCode() >= 500) {
                            $breadcrumbLevel = \Sentry\Breadcrumb::LEVEL_ERROR;
                        }
                    }
                    if ($childSpan !== null) {
                        if ($response !== null) {
                            $childSpan->setStatus(\Sentry\Tracing\SpanStatus::createFromHttpStatusCode($response->getStatusCode()));
                            $childSpan->setData($spanAndBreadcrumbData);
                        } else {
                            $childSpan->setStatus(\Sentry\Tracing\SpanStatus::internalError());
                        }
                    }
                    $hub->addBreadcrumb(new \Sentry\Breadcrumb($breadcrumbLevel, \Sentry\Breadcrumb::TYPE_HTTP, 'http', null, \array_merge(['url' => (string) $partialUri], $spanAndBreadcrumbData)));
                    if ($responseOrException instanceof \Throwable) {
                        throw $responseOrException;
                    }
                    return $responseOrException;
                };
                return $handler($request, $options)->then($handlerPromiseCallback, $handlerPromiseCallback);
            };
        };
    }
    private static function shouldAttachTracingHeaders(?\Sentry\ClientInterface $client, \WPSentry\ScopedVendor\Psr\Http\Message\RequestInterface $request) : bool
    {
        if ($client === null) {
            return \false;
        }
        $sdkOptions = $client->getOptions();
        // Check if the request destination is allow listed in the trace_propagation_targets option.
        return $sdkOptions->getTracePropagationTargets() === null || \in_array($request->getUri()->getHost(), $sdkOptions->getTracePropagationTargets());
    }
}

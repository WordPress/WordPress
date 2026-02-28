<?php

declare(strict_types=1);

namespace Sentry\Tracing\Traits;

use Sentry\Tracing\DynamicSamplingContext;
use Sentry\Tracing\SpanId;
use Sentry\Tracing\TraceId;

/**
 * @internal
 */
trait TraceHeaderParserTrait
{
    /**
     * @var string The regex for parsing the sentry-trace header
     */
    private static $sentryTraceparentHeaderRegex = '/^[ \\t]*(?<trace_id>[0-9a-f]{32})?-?(?<span_id>[0-9a-f]{16})?-?(?<sampled>[01])?[ \\t]*$/i';

    /**
     * Parses the sentry-trace and baggage headers and returns the extracted data.
     *
     * @param string $sentryTrace The sentry-trace header value
     * @param string $baggage     The baggage header value
     *
     * @return array{
     *     traceId: TraceId|null,
     *     parentSpanId: SpanId|null,
     *     parentSampled: bool|null,
     *     dynamicSamplingContext: DynamicSamplingContext|null,
     *     sampleRand: float|null,
     *     parentSamplingRate: float|null
     * }
     */
    protected static function parseTraceAndBaggageHeaders(string $sentryTrace, string $baggage): array
    {
        $result = [
            'traceId' => null,
            'parentSpanId' => null,
            'parentSampled' => null,
            'dynamicSamplingContext' => null,
            'sampleRand' => null,
            'parentSamplingRate' => null,
        ];

        $hasSentryTrace = false;

        if (preg_match(self::$sentryTraceparentHeaderRegex, $sentryTrace, $matches)) {
            if (!empty($matches['trace_id'])) {
                $result['traceId'] = new TraceId($matches['trace_id']);
                $hasSentryTrace = true;
            }

            if (!empty($matches['span_id'])) {
                $result['parentSpanId'] = new SpanId($matches['span_id']);
                $hasSentryTrace = true;
            }

            if (isset($matches['sampled'])) {
                $result['parentSampled'] = $matches['sampled'] === '1';
                $hasSentryTrace = true;
            }
        }

        $samplingContext = DynamicSamplingContext::fromHeader($baggage);

        if ($hasSentryTrace && !$samplingContext->hasEntries()) {
            // The request comes from an old SDK which does not support Dynamic Sampling.
            // Propagate the Dynamic Sampling Context as is, but frozen, even without sentry-* entries.
            $samplingContext->freeze();
            $result['dynamicSamplingContext'] = $samplingContext;
        }

        if ($hasSentryTrace && $samplingContext->hasEntries()) {
            // The baggage header contains Dynamic Sampling Context data from an upstream SDK.
            // Propagate this Dynamic Sampling Context.
            $result['dynamicSamplingContext'] = $samplingContext;
        }

        // Store the propagated traces sample rate
        if ($samplingContext->has('sample_rate')) {
            $result['parentSamplingRate'] = (float) $samplingContext->get('sample_rate');
        }

        // Store the propagated trace sample rand or generate a new one
        if ($samplingContext->has('sample_rand')) {
            $result['sampleRand'] = (float) $samplingContext->get('sample_rand');
        } else {
            if ($samplingContext->has('sample_rate') && $result['parentSampled'] !== null) {
                if ($result['parentSampled'] === true) {
                    // [0, rate)
                    $result['sampleRand'] = round(mt_rand(0, mt_getrandmax() - 1) / mt_getrandmax() * (float) $samplingContext->get('sample_rate'), 6);
                } else {
                    // [rate, 1)
                    $result['sampleRand'] = round(mt_rand(0, mt_getrandmax() - 1) / mt_getrandmax() * (1 - (float) $samplingContext->get('sample_rate')) + (float) $samplingContext->get('sample_rate'), 6);
                }
            } elseif ($result['parentSampled'] !== null) {
                // [0, 1)
                $result['sampleRand'] = round(mt_rand(0, mt_getrandmax() - 1) / mt_getrandmax(), 6);
            }
        }

        return $result;
    }
}

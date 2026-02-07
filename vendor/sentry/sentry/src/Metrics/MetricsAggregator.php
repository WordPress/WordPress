<?php

declare(strict_types=1);

namespace Sentry\Metrics;

use Sentry\Client;
use Sentry\Event;
use Sentry\EventId;
use Sentry\Metrics\Types\CounterMetric;
use Sentry\Metrics\Types\DistributionMetric;
use Sentry\Metrics\Types\GaugeMetric;
use Sentry\Metrics\Types\Metric;
use Sentry\SentrySdk;
use Sentry\State\Scope;
use Sentry\Unit;
use Sentry\Util\RingBuffer;

/**
 * @internal
 */
final class MetricsAggregator
{
    /**
     * @var int
     */
    public const METRICS_BUFFER_SIZE = 1000;

    /**
     * @var RingBuffer<Metric>
     */
    private $metrics;

    public function __construct()
    {
        $this->metrics = new RingBuffer(self::METRICS_BUFFER_SIZE);
    }

    private const METRIC_TYPES = [
        CounterMetric::TYPE => CounterMetric::class,
        DistributionMetric::TYPE => DistributionMetric::class,
        GaugeMetric::TYPE => GaugeMetric::class,
    ];

    /**
     * @param int|float                            $value
     * @param array<string, int|float|string|bool> $attributes
     */
    public function add(
        string $type,
        string $name,
        $value,
        array $attributes,
        ?Unit $unit
    ): void {
        $hub = SentrySdk::getCurrentHub();
        $client = $hub->getClient();

        if (!\is_int($value) && !\is_float($value)) {
            if ($client !== null) {
                $client->getOptions()->getLoggerOrNullLogger()->debug('Metrics value is neither int nor float. Metric will be discarded');
            }

            return;
        }

        if ($client instanceof Client) {
            $options = $client->getOptions();

            if ($options->getEnableMetrics() === false) {
                return;
            }

            $defaultAttributes = [
                'sentry.sdk.name' => $client->getSdkIdentifier(),
                'sentry.sdk.version' => $client->getSdkVersion(),
                'sentry.environment' => $options->getEnvironment() ?? Event::DEFAULT_ENVIRONMENT,
                'server.address' => $options->getServerName(),
            ];

            if ($options->shouldSendDefaultPii()) {
                $hub->configureScope(function (Scope $scope) use (&$defaultAttributes) {
                    $user = $scope->getUser();
                    if ($user !== null) {
                        if ($user->getId() !== null) {
                            $defaultAttributes['user.id'] = $user->getId();
                        }
                        if ($user->getEmail() !== null) {
                            $defaultAttributes['user.email'] = $user->getEmail();
                        }
                        if ($user->getUsername() !== null) {
                            $defaultAttributes['user.name'] = $user->getUsername();
                        }
                    }
                });
            }

            $release = $options->getRelease();
            if ($release !== null) {
                $defaultAttributes['sentry.release'] = $release;
            }

            $attributes += $defaultAttributes;
        }

        $spanId = null;
        $traceId = null;

        $span = $hub->getSpan();
        if ($span !== null) {
            $spanId = $span->getSpanId();
            $traceId = $span->getTraceId();
        } else {
            $hub->configureScope(function (Scope $scope) use (&$traceId, &$spanId) {
                $propagationContext = $scope->getPropagationContext();
                $traceId = $propagationContext->getTraceId();
                $spanId = $propagationContext->getSpanId();
            });
        }

        $metricTypeClass = self::METRIC_TYPES[$type];
        /** @var Metric $metric */
        /** @phpstan-ignore-next-line */
        $metric = new $metricTypeClass($name, $value, $traceId, $spanId, $attributes, microtime(true), $unit);

        if ($client !== null) {
            $beforeSendMetric = $client->getOptions()->getBeforeSendMetricCallback();
            $metric = $beforeSendMetric($metric);
            if ($metric === null) {
                return;
            }
        }

        $this->metrics->push($metric);
    }

    public function flush(): ?EventId
    {
        if ($this->metrics->isEmpty()) {
            return null;
        }

        $hub = SentrySdk::getCurrentHub();
        $event = Event::createMetrics()->setMetrics($this->metrics->drain());

        return $hub->captureEvent($event);
    }
}

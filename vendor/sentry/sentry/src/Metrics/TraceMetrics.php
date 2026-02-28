<?php

declare(strict_types=1);

namespace Sentry\Metrics;

use Sentry\EventId;
use Sentry\Metrics\Types\CounterMetric;
use Sentry\Metrics\Types\DistributionMetric;
use Sentry\Metrics\Types\GaugeMetric;
use Sentry\Unit;

class TraceMetrics
{
    /**
     * @var self|null
     */
    private static $instance;

    /**
     * @var MetricsAggregator
     */
    private $aggregator;

    public function __construct()
    {
        $this->aggregator = new MetricsAggregator();
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new TraceMetrics();
        }

        return self::$instance;
    }

    /**
     * @param int|float                            $value
     * @param array<string, int|float|string|bool> $attributes
     */
    public function count(
        string $name,
        $value,
        array $attributes = [],
        ?Unit $unit = null
    ): void {
        $this->aggregator->add(
            CounterMetric::TYPE,
            $name,
            $value,
            $attributes,
            $unit
        );
    }

    /**
     * @param int|float                            $value
     * @param array<string, int|float|string|bool> $attributes
     */
    public function distribution(
        string $name,
        $value,
        array $attributes = [],
        ?Unit $unit = null
    ): void {
        $this->aggregator->add(
            DistributionMetric::TYPE,
            $name,
            $value,
            $attributes,
            $unit
        );
    }

    /**
     * @param int|float                            $value
     * @param array<string, int|float|string|bool> $attributes
     */
    public function gauge(
        string $name,
        $value,
        array $attributes = [],
        ?Unit $unit = null
    ): void {
        $this->aggregator->add(
            GaugeMetric::TYPE,
            $name,
            $value,
            $attributes,
            $unit
        );
    }

    public function flush(): ?EventId
    {
        return $this->aggregator->flush();
    }
}

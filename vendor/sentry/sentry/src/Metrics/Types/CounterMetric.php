<?php

declare(strict_types=1);

namespace Sentry\Metrics\Types;

use Sentry\Tracing\SpanId;
use Sentry\Tracing\TraceId;
use Sentry\Unit;

/**
 * @internal
 */
final class CounterMetric extends Metric
{
    /**
     * @var string
     */
    public const TYPE = 'counter';

    /**
     * @var int|float
     */
    private $value;

    /**
     * @param int|float                            $value
     * @param array<string, int|float|string|bool> $attributes
     */
    public function __construct(
        string $name,
        $value,
        TraceId $traceId,
        SpanId $spanId,
        array $attributes,
        float $timestamp,
        ?Unit $unit
    ) {
        parent::__construct($name, $traceId, $spanId, $timestamp, $attributes, $unit);

        $this->value = $value;
    }

    /**
     * @param int|float $value
     */
    public function setValue($value): void
    {
        $this->value = $value;
    }

    /**
     * @return int|float
     */
    public function getValue()
    {
        return $this->value;
    }

    public function getType(): string
    {
        return self::TYPE;
    }
}

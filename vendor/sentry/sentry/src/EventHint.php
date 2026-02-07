<?php

declare(strict_types=1);

namespace Sentry;

/**
 * This class represents hints on how to process an event.
 */
final class EventHint
{
    /**
     * The original exception to add to the event.
     *
     * @var \Throwable|null
     */
    public $exception;

    /**
     * An object describing the mechanism of the original exception.
     *
     * @var ExceptionMechanism|null
     */
    public $mechanism;

    /**
     * The stacktrace to set on the event.
     *
     * @var Stacktrace|null
     */
    public $stacktrace;

    /**
     * Any extra data that might be needed to process the event.
     *
     * @var array<string, mixed>
     */
    public $extra = [];

    /**
     * Create a EventHint instance from an array of values.
     *
     * @psalm-param array{
     *     exception?: \Throwable|null,
     *     mechanism?: ExceptionMechanism|null,
     *     stacktrace?: Stacktrace|null,
     *     extra?: array<string, mixed>
     * } $hintData
     */
    public static function fromArray(array $hintData): self
    {
        $hint = new self();
        $exception = $hintData['exception'] ?? null;
        $mechanism = $hintData['mechanism'] ?? null;
        $stacktrace = $hintData['stacktrace'] ?? null;
        $extra = $hintData['extra'] ?? [];

        if ($exception !== null && !$exception instanceof \Throwable) {
            throw new \InvalidArgumentException(\sprintf('The value of the "exception" field must be an instance of a class implementing the "%s" interface. Got: "%s".', \Throwable::class, get_debug_type($exception)));
        }

        if ($mechanism !== null && !$mechanism instanceof ExceptionMechanism) {
            throw new \InvalidArgumentException(\sprintf('The value of the "mechanism" field must be an instance of the "%s" class. Got: "%s".', ExceptionMechanism::class, get_debug_type($mechanism)));
        }

        if ($stacktrace !== null && !$stacktrace instanceof Stacktrace) {
            throw new \InvalidArgumentException(\sprintf('The value of the "stacktrace" field must be an instance of the "%s" class. Got: "%s".', Stacktrace::class, get_debug_type($stacktrace)));
        }

        if (!\is_array($extra)) {
            throw new \InvalidArgumentException(\sprintf('The value of the "extra" field must be an array. Got: "%s".', get_debug_type($extra)));
        }

        $hint->exception = $exception;
        $hint->mechanism = $mechanism;
        $hint->stacktrace = $stacktrace;
        $hint->extra = $extra;

        return $hint;
    }
}

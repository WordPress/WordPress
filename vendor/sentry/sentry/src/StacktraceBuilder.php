<?php

declare(strict_types=1);

namespace Sentry;

use Sentry\Serializer\RepresentationSerializerInterface;
use Sentry\Util\PHPConfiguration;

/**
 * This class builds {@see Stacktrace} objects from an instance of an exception
 * or from a backtrace.
 *
 * @psalm-import-type StacktraceFrame from FrameBuilder
 */
final class StacktraceBuilder
{
    /**
     * @var FrameBuilder An instance of the builder of {@see Frame} objects
     */
    private $frameBuilder;

    /**
     * Constructor.
     *
     * @param Options                           $options                  The SDK client options
     * @param RepresentationSerializerInterface $representationSerializer The representation serializer
     */
    public function __construct(Options $options, RepresentationSerializerInterface $representationSerializer)
    {
        $this->frameBuilder = new FrameBuilder($options, $representationSerializer);

        if (PHPConfiguration::isBooleanIniOptionEnabled('zend.exception_ignore_args')) {
            $options->getLoggerOrNullLogger()->warning('The "zend.exception_ignore_args" PHP setting is enabled which results in missing stack trace arguments, see: https://docs.sentry.io/platforms/php/troubleshooting/#missing-variables-in-stack-traces.');
        }
    }

    /**
     * Builds a {@see Stacktrace} object from the given exception.
     *
     * @param \Throwable $exception The exception object
     */
    public function buildFromException(\Throwable $exception): Stacktrace
    {
        return $this->buildFromBacktrace($exception->getTrace(), $exception->getFile(), $exception->getLine());
    }

    /**
     * Builds a {@see Stacktrace} object from the given backtrace.
     *
     * @param array<int, array<string, mixed>> $backtrace The backtrace
     * @param string                           $file      The file where the backtrace originated from
     * @param int                              $line      The line from which the backtrace originated from
     *
     * @psalm-param list<StacktraceFrame> $backtrace
     */
    public function buildFromBacktrace(array $backtrace, string $file, int $line): Stacktrace
    {
        $frames = [];

        foreach ($backtrace as $backtraceFrame) {
            array_unshift($frames, $this->frameBuilder->buildFromBacktraceFrame($file, $line, $backtraceFrame));

            $file = $backtraceFrame['file'] ?? Frame::INTERNAL_FRAME_FILENAME;
            $line = $backtraceFrame['line'] ?? 0;
        }

        // Add a final stackframe for the first method ever of this stacktrace
        array_unshift($frames, $this->frameBuilder->buildFromBacktraceFrame($file, $line, []));

        return new Stacktrace($frames);
    }
}

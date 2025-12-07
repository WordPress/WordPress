<?php

declare(strict_types=1);

namespace Sentry;

use Sentry\Serializer\RepresentationSerializerInterface;
use Sentry\Util\PrefixStripper;

/**
 * This class builds a {@see Frame} object out of a backtrace's raw frame.
 *
 * @internal
 *
 * @psalm-type StacktraceFrame array{
 *     function?: string,
 *     line?: int,
 *     file?: string,
 *     class?: class-string,
 *     type?: string,
 *     args?: mixed[]
 * }
 */
final class FrameBuilder
{
    use PrefixStripper;

    /**
     * @var Options The SDK client options
     */
    private $options;

    /**
     * @var RepresentationSerializerInterface The representation serializer
     */
    private $representationSerializer;

    /**
     * Constructor.
     *
     * @param Options                           $options                  The SDK client options
     * @param RepresentationSerializerInterface $representationSerializer The representation serializer
     */
    public function __construct(Options $options, RepresentationSerializerInterface $representationSerializer)
    {
        $this->options = $options;
        $this->representationSerializer = $representationSerializer;
    }

    /**
     * Builds a {@see Frame} object from the given backtrace's raw frame.
     *
     * @param string               $file           The file where the frame originated
     * @param int                  $line           The line at which the frame originated
     * @param array<string, mixed> $backtraceFrame The raw frame
     *
     * @psalm-param StacktraceFrame $backtraceFrame
     */
    public function buildFromBacktraceFrame(string $file, int $line, array $backtraceFrame): Frame
    {
        // The filename can be in any of these formats:
        //   - </path/to/filename>
        //   - </path/to/filename>(<line number>) : eval()'d code
        //   - </path/to/filename>(<line number>) : runtime-created function
        if (preg_match('/^(.*)\((\d+)\) : (?:eval\(\)\'d code|runtime-created function)$/', $file, $matches)) {
            $file = $matches[1];
            $line = (int) $matches[2];
        }

        $functionName = null;
        $rawFunctionName = null;
        $strippedFilePath = $this->stripPrefixFromFilePath($this->options, $file);

        if (isset($backtraceFrame['class']) && isset($backtraceFrame['function'])) {
            $functionName = $backtraceFrame['class'];

            // Skip if no prefixes are set
            if ($this->options->getPrefixes()) {
                $prefixStrippedFunctionName = preg_replace_callback('/@anonymous\\x00([^:]+)(:.*)?/', function (array $matches) {
                    return "@anonymous\x00" . $this->stripPrefixFromFilePath($this->options, $matches[1]) . ($matches[2] ?? '');
                }, $functionName);

                if ($prefixStrippedFunctionName) {
                    $functionName = $prefixStrippedFunctionName;
                }
            }

            $rawFunctionName = \sprintf('%s::%s', $backtraceFrame['class'], $backtraceFrame['function']);
            $functionName = \sprintf('%s::%s', preg_replace('/(?::\d+\$|0x)[a-fA-F0-9]+$/', '', $functionName), $backtraceFrame['function']);
        } elseif (isset($backtraceFrame['function'])) {
            $functionName = $backtraceFrame['function'];
        }

        // Starting with PHP 8.4 a closure function call is reported as "{closure:filename:line}" instead of just "{closure}", properly strip the prefixes from that format
        if (\PHP_VERSION_ID >= 80400 && $functionName !== null && $this->options->getPrefixes()) {
            $prefixStrippedFunctionName = preg_replace_callback('/^\{closure:(.*?):(\d+)}$/', function (array $matches) {
                return '{closure:' . $this->stripPrefixFromFilePath($this->options, $matches[1]) . ':' . $matches[2] . '}';
            }, $functionName);

            if ($prefixStrippedFunctionName) {
                $functionName = $prefixStrippedFunctionName;
            }
        }

        return new Frame(
            $functionName,
            $strippedFilePath,
            $line,
            $rawFunctionName,
            $file !== Frame::INTERNAL_FRAME_FILENAME ? $file : null,
            $this->getFunctionArguments($backtraceFrame),
            $this->isFrameInApp($file, $functionName)
        );
    }

    /**
     * Checks whether a certain frame should be marked as "in app" or not.
     *
     * @param string      $file         The file to check
     * @param string|null $functionName The name of the function
     */
    private function isFrameInApp(string $file, ?string $functionName): bool
    {
        if ($file === Frame::INTERNAL_FRAME_FILENAME) {
            return false;
        }

        if ($functionName !== null && substr($functionName, 0, \strlen('Sentry\\')) === 'Sentry\\') {
            return false;
        }

        $excludedAppPaths = $this->options->getInAppExcludedPaths();
        $includedAppPaths = $this->options->getInAppIncludedPaths();
        $absoluteFilePath = @realpath($file) ?: $file;
        $isInApp = true;

        foreach ($excludedAppPaths as $excludedAppPath) {
            if (mb_substr($absoluteFilePath, 0, mb_strlen($excludedAppPath)) === $excludedAppPath) {
                $isInApp = false;

                break;
            }
        }

        foreach ($includedAppPaths as $includedAppPath) {
            if (mb_substr($absoluteFilePath, 0, mb_strlen($includedAppPath)) === $includedAppPath) {
                $isInApp = true;

                break;
            }
        }

        return $isInApp;
    }

    /**
     * Gets the arguments of the function called in the given frame.
     *
     * @param array<string, mixed> $backtraceFrame The frame data
     *
     * @psalm-param StacktraceFrame $backtraceFrame
     *
     * @return array<string, mixed>
     */
    private function getFunctionArguments(array $backtraceFrame): array
    {
        if (!isset($backtraceFrame['function'], $backtraceFrame['args'])) {
            return [];
        }

        $reflectionFunction = null;

        try {
            if (isset($backtraceFrame['class'])) {
                if (method_exists($backtraceFrame['class'], $backtraceFrame['function'])) {
                    $reflectionFunction = new \ReflectionMethod($backtraceFrame['class'], $backtraceFrame['function']);
                } elseif (isset($backtraceFrame['type']) && $backtraceFrame['type'] === '::') {
                    $reflectionFunction = new \ReflectionMethod($backtraceFrame['class'], '__callStatic');
                } else {
                    $reflectionFunction = new \ReflectionMethod($backtraceFrame['class'], '__call');
                }
            } elseif ($backtraceFrame['function'] !== '__lambda_func' && !str_starts_with($backtraceFrame['function'], '{closure') && \function_exists($backtraceFrame['function'])) {
                $reflectionFunction = new \ReflectionFunction($backtraceFrame['function']);
            }
        } catch (\ReflectionException $e) {
            // Reflection failed, we do nothing instead
        }

        $argumentValues = [];

        if ($reflectionFunction !== null) {
            $argumentValues = $this->getFunctionArgumentValues($reflectionFunction, $backtraceFrame['args']);
        } else {
            foreach ($backtraceFrame['args'] as $parameterPosition => $parameterValue) {
                $argumentValues['param' . $parameterPosition] = $parameterValue;
            }
        }

        foreach ($argumentValues as $argumentName => $argumentValue) {
            $argumentValues[$argumentName] = $this->representationSerializer->representationSerialize($argumentValue);
        }

        return $argumentValues;
    }

    /**
     * Gets an hashmap indexed by argument name containing all the arguments
     * passed to the function called in the given frame of the stacktrace.
     *
     * @param \ReflectionFunctionAbstract $reflectionFunction A reflection object
     * @param mixed[]                     $backtraceFrameArgs The arguments of the frame
     *
     * @return array<string, mixed>
     */
    private function getFunctionArgumentValues(\ReflectionFunctionAbstract $reflectionFunction, array $backtraceFrameArgs): array
    {
        $argumentValues = [];

        foreach ($reflectionFunction->getParameters() as $reflectionParameter) {
            $parameterPosition = $reflectionParameter->getPosition();

            if ($reflectionParameter->isVariadic()) {
                // For variadic parameters, collect all remaining arguments into an array
                $variadicArgs = \array_slice($backtraceFrameArgs, $parameterPosition);
                $argumentValues[$reflectionParameter->getName()] = array_values($variadicArgs);
                // Variadic parameter is always the last one, so we can break
                break;
            }

            if (!\array_key_exists($parameterPosition, $backtraceFrameArgs)) {
                continue;
            }

            $argumentValues[$reflectionParameter->getName()] = $backtraceFrameArgs[$parameterPosition];
        }

        return $argumentValues;
    }
}

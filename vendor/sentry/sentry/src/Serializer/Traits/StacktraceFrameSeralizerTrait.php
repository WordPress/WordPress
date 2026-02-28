<?php

declare(strict_types=1);

namespace Sentry\Serializer\Traits;

use Sentry\Frame;

/**
 * @internal
 */
trait StacktraceFrameSeralizerTrait
{
    /**
     * @return array<string, mixed>
     *
     * @psalm-return array{
     *     filename: string,
     *     lineno: int,
     *     in_app: bool,
     *     abs_path?: string,
     *     function?: string,
     *     raw_function?: string,
     *     pre_context?: string[],
     *     context_line?: string,
     *     post_context?: string[],
     *     vars?: array<string, mixed>
     * }
     */
    protected static function serializeStacktraceFrame(Frame $frame): array
    {
        $result = [
            'filename' => $frame->getFile(),
            'lineno' => $frame->getLine(),
            'in_app' => $frame->isInApp(),
        ];

        if ($frame->getAbsoluteFilePath() !== null) {
            $result['abs_path'] = $frame->getAbsoluteFilePath();
        }

        if ($frame->getFunctionName() !== null) {
            $result['function'] = $frame->getFunctionName();
        }

        if ($frame->getRawFunctionName() !== null) {
            $result['raw_function'] = $frame->getRawFunctionName();
        }

        if (!empty($frame->getPreContext())) {
            $result['pre_context'] = $frame->getPreContext();
        }

        if ($frame->getContextLine() !== null) {
            $result['context_line'] = $frame->getContextLine();
        }

        if (!empty($frame->getPostContext())) {
            $result['post_context'] = $frame->getPostContext();
        }

        if (!empty($frame->getVars())) {
            $result['vars'] = $frame->getVars();
        }

        return $result;
    }
}

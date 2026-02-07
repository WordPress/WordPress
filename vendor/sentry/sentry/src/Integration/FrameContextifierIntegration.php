<?php

declare(strict_types=1);

namespace Sentry\Integration;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Sentry\Event;
use Sentry\Frame;
use Sentry\SentrySdk;
use Sentry\Stacktrace;
use Sentry\State\Scope;

/**
 * This integration reads excerpts of code around the line that originated an
 * error.
 *
 * @author Stefano Arlandini <sarlandini@alice.it>
 */
final class FrameContextifierIntegration implements IntegrationInterface
{
    /**
     * @var LoggerInterface A PSR-3 logger
     */
    private $logger;

    /**
     * Creates a new instance of this integration.
     *
     * @param LoggerInterface|null $logger A PSR-3 logger
     */
    public function __construct(?LoggerInterface $logger = null)
    {
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * {@inheritdoc}
     */
    public function setupOnce(): void
    {
        Scope::addGlobalEventProcessor(static function (Event $event): Event {
            $client = SentrySdk::getCurrentHub()->getClient();

            if ($client === null) {
                return $event;
            }

            $maxContextLines = $client->getOptions()->getContextLines();
            $integration = $client->getIntegration(self::class);

            if ($integration === null || $maxContextLines === null) {
                return $event;
            }

            $stacktrace = $event->getStacktrace();

            if ($stacktrace !== null) {
                $integration->addContextToStacktraceFrames($maxContextLines, $stacktrace);
            }

            foreach ($event->getExceptions() as $exception) {
                if ($exception->getStacktrace() !== null) {
                    $integration->addContextToStacktraceFrames($maxContextLines, $exception->getStacktrace());
                }
            }

            return $event;
        });
    }

    /**
     * Contextifies the frames of the given stacktrace.
     *
     * @param int        $maxContextLines The maximum number of lines of code to read
     * @param Stacktrace $stacktrace      The stacktrace object
     */
    private function addContextToStacktraceFrames(int $maxContextLines, Stacktrace $stacktrace): void
    {
        foreach ($stacktrace->getFrames() as $frame) {
            if ($frame->isInternal()) {
                continue;
            }

            $this->addContextToStacktraceFrame($maxContextLines, $frame);
        }
    }

    /**
     * Contextifies the given frame.
     *
     * @param int $maxContextLines The maximum number of lines of code to read
     */
    private function addContextToStacktraceFrame(int $maxContextLines, Frame $frame): void
    {
        if ($frame->getAbsoluteFilePath() === null) {
            return;
        }

        $sourceCodeExcerpt = $this->getSourceCodeExcerpt($maxContextLines, $frame->getAbsoluteFilePath(), $frame->getLine());

        $frame->setPreContext($sourceCodeExcerpt['pre_context']);
        $frame->setContextLine($sourceCodeExcerpt['context_line']);
        $frame->setPostContext($sourceCodeExcerpt['post_context']);
    }

    /**
     * Gets an excerpt of the source code around a given line.
     *
     * @param int    $maxContextLines The maximum number of lines of code to read
     * @param string $filePath        The file path
     * @param int    $lineNumber      The line to centre about
     *
     * @return array<string, mixed>
     *
     * @psalm-return array{
     *     pre_context: string[],
     *     context_line: string|null,
     *     post_context: string[]
     * }
     */
    private function getSourceCodeExcerpt(int $maxContextLines, string $filePath, int $lineNumber): array
    {
        $frame = [
            'pre_context' => [],
            'context_line' => null,
            'post_context' => [],
        ];

        $target = max(0, $lineNumber - ($maxContextLines + 1));
        $currentLineNumber = $target + 1;

        try {
            $file = new \SplFileObject($filePath);
            $file->seek($target);

            while (!$file->eof()) {
                /** @var string $line */
                $line = $file->current();
                $line = rtrim($line, "\r\n");

                if ($currentLineNumber === $lineNumber) {
                    $frame['context_line'] = $line;
                } elseif ($currentLineNumber < $lineNumber) {
                    $frame['pre_context'][] = $line;
                } elseif ($currentLineNumber > $lineNumber) {
                    $frame['post_context'][] = $line;
                }

                ++$currentLineNumber;

                if ($currentLineNumber > $lineNumber + $maxContextLines) {
                    break;
                }

                $file->next();
            }
        } catch (\Throwable $exception) {
            $this->logger->warning(
                \sprintf('Failed to get the source code excerpt for the file "%s".', $filePath),
                ['exception' => $exception]
            );
        }

        return $frame;
    }
}

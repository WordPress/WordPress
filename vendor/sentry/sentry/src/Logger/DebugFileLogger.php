<?php

declare(strict_types=1);

namespace Sentry\Logger;

class DebugFileLogger extends DebugLogger
{
    /**
     * @var string
     */
    private $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    public function write(string $message): void
    {
        file_put_contents($this->filePath, $message, \FILE_APPEND);
    }
}

<?php

declare(strict_types=1);

namespace Sentry\Logger;

class DebugStdOutLogger extends DebugLogger
{
    public function write(string $message): void
    {
        file_put_contents('php://stdout', $message);
    }
}

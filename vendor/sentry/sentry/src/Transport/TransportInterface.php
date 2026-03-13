<?php

declare(strict_types=1);

namespace Sentry\Transport;

use Sentry\Event;

interface TransportInterface
{
    public function send(Event $event): Result;

    public function close(?int $timeout = null): Result;
}

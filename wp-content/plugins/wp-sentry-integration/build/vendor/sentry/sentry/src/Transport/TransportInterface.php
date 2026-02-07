<?php

declare (strict_types=1);
namespace Sentry\Transport;

use Sentry\Event;
interface TransportInterface
{
    public function send(\Sentry\Event $event) : \Sentry\Transport\Result;
    public function close(?int $timeout = null) : \Sentry\Transport\Result;
}

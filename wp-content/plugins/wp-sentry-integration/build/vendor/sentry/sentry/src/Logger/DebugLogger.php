<?php

declare (strict_types=1);
namespace Sentry\Logger;

use WPSentry\ScopedVendor\Psr\Log\AbstractLogger;
abstract class DebugLogger extends \WPSentry\ScopedVendor\Psr\Log\AbstractLogger
{
    /**
     * @param mixed              $level
     * @param string|\Stringable $message
     * @param mixed[]            $context
     */
    public function log($level, $message, array $context = []) : void
    {
        $formattedMessageAndContext = \implode(' ', \array_filter([(string) $message, \json_encode($context)]));
        $this->write(\sprintf("sentry/sentry: [%s] %s\n", $level, $formattedMessageAndContext));
    }
    public abstract function write(string $message) : void;
}

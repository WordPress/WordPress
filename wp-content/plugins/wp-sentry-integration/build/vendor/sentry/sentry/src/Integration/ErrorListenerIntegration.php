<?php

declare (strict_types=1);
namespace Sentry\Integration;

use Sentry\ErrorHandler;
use Sentry\Exception\SilencedErrorException;
use Sentry\Options;
use Sentry\SentrySdk;
/**
 * This integration hooks into the global error handlers and emits events to
 * Sentry.
 */
final class ErrorListenerIntegration extends \Sentry\Integration\AbstractErrorListenerIntegration implements \Sentry\Integration\OptionAwareIntegrationInterface
{
    /**
     * @var Options
     */
    private $options;
    public function setOptions(\Sentry\Options $options) : void
    {
        $this->options = $options;
    }
    /**
     * {@inheritdoc}
     */
    public function setupOnce() : void
    {
        \Sentry\ErrorHandler::registerOnceErrorHandler($this->options)->addErrorHandlerListener(static function (\ErrorException $exception) : void {
            $currentHub = \Sentry\SentrySdk::getCurrentHub();
            $integration = $currentHub->getIntegration(self::class);
            $client = $currentHub->getClient();
            // The client bound to the current hub, if any, could not have this
            // integration enabled. If this is the case, bail out
            if ($integration === null || $client === null) {
                return;
            }
            if ($exception instanceof \Sentry\Exception\SilencedErrorException && !$client->getOptions()->shouldCaptureSilencedErrors()) {
                return;
            }
            if (!$exception instanceof \Sentry\Exception\SilencedErrorException && !($client->getOptions()->getErrorTypes() & $exception->getSeverity())) {
                return;
            }
            $integration->captureException($currentHub, $exception);
        });
    }
    /**
     * @internal this is a convenience method to create an instance of this integration for tests
     */
    public static function make(\Sentry\Options $options) : self
    {
        $integration = new self();
        $integration->setOptions($options);
        return $integration;
    }
}

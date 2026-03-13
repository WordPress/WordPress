<?php

declare (strict_types=1);
namespace Sentry\Integration;

use Sentry\ErrorHandler;
use Sentry\Exception\FatalErrorException;
use Sentry\SentrySdk;
/**
 * This integration hooks into the error handler and captures fatal errors.
 *
 * @author Stefano Arlandini <sarlandini@alice.it>
 */
final class FatalErrorListenerIntegration extends \Sentry\Integration\AbstractErrorListenerIntegration
{
    /**
     * {@inheritdoc}
     */
    public function setupOnce() : void
    {
        $errorHandler = \Sentry\ErrorHandler::registerOnceFatalErrorHandler();
        $errorHandler->addFatalErrorHandlerListener(static function (\Sentry\Exception\FatalErrorException $exception) : void {
            $currentHub = \Sentry\SentrySdk::getCurrentHub();
            $integration = $currentHub->getIntegration(self::class);
            $client = $currentHub->getClient();
            // The client bound to the current hub, if any, could not have this
            // integration enabled. If this is the case, bail out
            if ($integration === null || $client === null) {
                return;
            }
            if (!($client->getOptions()->getErrorTypes() & $exception->getSeverity())) {
                return;
            }
            $integration->captureException($currentHub, $exception);
        });
    }
}

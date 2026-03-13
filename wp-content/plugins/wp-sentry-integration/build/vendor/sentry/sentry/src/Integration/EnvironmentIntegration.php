<?php

declare (strict_types=1);
namespace Sentry\Integration;

use Sentry\Context\OsContext;
use Sentry\Context\RuntimeContext;
use Sentry\Event;
use Sentry\SentrySdk;
use Sentry\State\Scope;
use Sentry\Util\PHPVersion;
/**
 * This integration fills the event data with runtime and server OS information.
 *
 * @author Stefano Arlandini <sarlandini@alice.it>
 */
final class EnvironmentIntegration implements \Sentry\Integration\IntegrationInterface
{
    /**
     * {@inheritdoc}
     */
    public function setupOnce() : void
    {
        \Sentry\State\Scope::addGlobalEventProcessor(static function (\Sentry\Event $event) : Event {
            $integration = \Sentry\SentrySdk::getCurrentHub()->getIntegration(self::class);
            if ($integration !== null) {
                $event->setRuntimeContext($integration->updateRuntimeContext($event->getRuntimeContext()));
                $event->setOsContext($integration->updateServerOsContext($event->getOsContext()));
            }
            return $event;
        });
    }
    private function updateRuntimeContext(?\Sentry\Context\RuntimeContext $runtimeContext) : \Sentry\Context\RuntimeContext
    {
        if ($runtimeContext === null) {
            $runtimeContext = new \Sentry\Context\RuntimeContext('php');
        }
        if ($runtimeContext->getVersion() === null) {
            $runtimeContext->setVersion(\Sentry\Util\PHPVersion::parseVersion());
        }
        if ($runtimeContext->getSAPI() === null) {
            $runtimeContext->setSAPI(\PHP_SAPI);
        }
        return $runtimeContext;
    }
    private function updateServerOsContext(?\Sentry\Context\OsContext $osContext) : ?\Sentry\Context\OsContext
    {
        if (!\function_exists('php_uname')) {
            return $osContext;
        }
        if ($osContext === null) {
            $osContext = new \Sentry\Context\OsContext(\php_uname('s'));
        }
        if ($osContext->getVersion() === null) {
            $osContext->setVersion(\php_uname('r'));
        }
        if ($osContext->getBuild() === null) {
            $osContext->setBuild(\php_uname('v'));
        }
        if ($osContext->getKernelVersion() === null) {
            $osContext->setKernelVersion(\php_uname('a'));
        }
        if ($osContext->getMachineType() === null) {
            $osContext->setMachineType(\php_uname('m'));
        }
        return $osContext;
    }
}

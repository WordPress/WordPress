<?php

declare (strict_types=1);
namespace Sentry\Integration;

use WPSentry\ScopedVendor\Composer\InstalledVersions;
use WPSentry\ScopedVendor\Jean85\PrettyVersions;
use WPSentry\ScopedVendor\PackageVersions\Versions;
use Sentry\Event;
use Sentry\SentrySdk;
use Sentry\State\Scope;
/**
 * This integration logs with the event details all the versions of the packages
 * installed with Composer; the root project is included too.
 */
final class ModulesIntegration implements \Sentry\Integration\IntegrationInterface
{
    /**
     * @var array<string, string> The list of installed vendors
     */
    private static $packages = [];
    /**
     * {@inheritdoc}
     */
    public function setupOnce() : void
    {
        \Sentry\State\Scope::addGlobalEventProcessor(static function (\Sentry\Event $event) : Event {
            $integration = \Sentry\SentrySdk::getCurrentHub()->getIntegration(self::class);
            // The integration could be bound to a client that is not the one
            // attached to the current hub. If this is the case, bail out
            if ($integration !== null) {
                $event->setModules(self::getComposerPackages());
            }
            return $event;
        });
    }
    /**
     * @return array<string, string>
     */
    private static function getComposerPackages() : array
    {
        if (empty(self::$packages)) {
            foreach (self::getInstalledPackages() as $package) {
                try {
                    self::$packages[$package] = \WPSentry\ScopedVendor\Jean85\PrettyVersions::getVersion($package)->getPrettyVersion();
                } catch (\Throwable $exception) {
                    continue;
                }
            }
        }
        return self::$packages;
    }
    /**
     * @return string[]
     */
    private static function getInstalledPackages() : array
    {
        if (\class_exists(\WPSentry\ScopedVendor\Composer\InstalledVersions::class)) {
            return \WPSentry\ScopedVendor\Composer\InstalledVersions::getInstalledPackages();
        }
        if (\class_exists(\WPSentry\ScopedVendor\PackageVersions\Versions::class)) {
            // BC layer for Composer 1, using a transient dependency
            return \array_keys(\WPSentry\ScopedVendor\PackageVersions\Versions::VERSIONS);
        }
        // this should not happen
        return ['sentry/sentry'];
    }
}

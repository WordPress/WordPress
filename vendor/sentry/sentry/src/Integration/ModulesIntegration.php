<?php

declare(strict_types=1);

namespace Sentry\Integration;

use Composer\InstalledVersions;
use Jean85\PrettyVersions;
use PackageVersions\Versions;
use Sentry\Event;
use Sentry\SentrySdk;
use Sentry\State\Scope;

/**
 * This integration logs with the event details all the versions of the packages
 * installed with Composer; the root project is included too.
 */
final class ModulesIntegration implements IntegrationInterface
{
    /**
     * @var array<string, string> The list of installed vendors
     */
    private static $packages = [];

    /**
     * {@inheritdoc}
     */
    public function setupOnce(): void
    {
        Scope::addGlobalEventProcessor(static function (Event $event): Event {
            $integration = SentrySdk::getCurrentHub()->getIntegration(self::class);

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
    private static function getComposerPackages(): array
    {
        if (empty(self::$packages)) {
            foreach (self::getInstalledPackages() as $package) {
                try {
                    self::$packages[$package] = PrettyVersions::getVersion($package)->getPrettyVersion();
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
    private static function getInstalledPackages(): array
    {
        if (class_exists(InstalledVersions::class)) {
            return InstalledVersions::getInstalledPackages();
        }

        if (class_exists(Versions::class)) {
            // BC layer for Composer 1, using a transient dependency
            return array_keys(Versions::VERSIONS);
        }

        // this should not happen
        return ['sentry/sentry'];
    }
}

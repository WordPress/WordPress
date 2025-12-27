<?php

declare(strict_types=1);

namespace Sentry;

use Sentry\State\Hub;
use Sentry\State\HubInterface;

/**
 * This class is the main entry point for all the most common SDK features.
 *
 * @author Stefano Arlandini <sarlandini@alice.it>
 */
final class SentrySdk
{
    /**
     * @var HubInterface|null The current hub
     */
    private static $currentHub;

    /**
     * Constructor.
     */
    private function __construct()
    {
    }

    /**
     * Initializes the SDK by creating a new hub instance each time this method
     * gets called.
     */
    public static function init(): HubInterface
    {
        self::$currentHub = new Hub();

        return self::$currentHub;
    }

    /**
     * Gets the current hub. If it's not initialized then creates a new instance
     * and sets it as current hub.
     */
    public static function getCurrentHub(): HubInterface
    {
        if (self::$currentHub === null) {
            self::$currentHub = new Hub();
        }

        return self::$currentHub;
    }

    /**
     * Sets the current hub.
     *
     * @param HubInterface $hub The hub to set
     */
    public static function setCurrentHub(HubInterface $hub): HubInterface
    {
        self::$currentHub = $hub;

        return $hub;
    }
}

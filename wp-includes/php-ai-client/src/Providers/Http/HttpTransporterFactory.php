<?php

declare (strict_types=1);
namespace WordPress\AiClient\Providers\Http;

use WordPress\AiClientDependencies\Http\Discovery\Psr17FactoryDiscovery;
use WordPress\AiClientDependencies\Http\Discovery\Psr18ClientDiscovery;
use WordPress\AiClient\Providers\Http\Contracts\HttpTransporterInterface;
/**
 * Factory for creating HTTP transporters.
 *
 * Uses HTTPlug's Discovery component to automatically find
 * available HTTP clients and factories.
 *
 * @since 0.1.0
 */
class HttpTransporterFactory
{
    /**
     * Creates an HTTP transporter.
     *
     * Uses HTTPlug Discovery to automatically find PSR-18 client
     * and PSR-17 factories if not provided.
     *
     * @since 0.1.0
     *
     * @return HttpTransporterInterface The HTTP transporter.
     */
    public static function createTransporter(): HttpTransporterInterface
    {
        return new \WordPress\AiClient\Providers\Http\HttpTransporter(Psr18ClientDiscovery::find(), Psr17FactoryDiscovery::findRequestFactory(), Psr17FactoryDiscovery::findStreamFactory());
    }
}

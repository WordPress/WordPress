<?php

declare (strict_types=1);
namespace WordPress\AiClient\Providers\Http\Contracts;

/**
 * Interface for models that require HTTP transport capabilities.
 *
 * @since 0.1.0
 */
interface WithHttpTransporterInterface
{
    /**
     * Sets the HTTP transporter.
     *
     * @since 0.1.0
     *
     * @param HttpTransporterInterface $transporter The HTTP transporter instance.
     * @return void
     */
    public function setHttpTransporter(\WordPress\AiClient\Providers\Http\Contracts\HttpTransporterInterface $transporter): void;
    /**
     * Returns the HTTP transporter.
     *
     * @since 0.1.0
     *
     * @return HttpTransporterInterface The HTTP transporter instance.
     */
    public function getHttpTransporter(): \WordPress\AiClient\Providers\Http\Contracts\HttpTransporterInterface;
}

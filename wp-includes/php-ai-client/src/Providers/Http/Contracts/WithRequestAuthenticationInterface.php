<?php

declare (strict_types=1);
namespace WordPress\AiClient\Providers\Http\Contracts;

/**
 * Interface for models that support request authentication.
 *
 * @since 0.1.0
 */
interface WithRequestAuthenticationInterface
{
    /**
     * Sets the request authentication.
     *
     * @since 0.1.0
     *
     * @param RequestAuthenticationInterface $authentication The authentication instance.
     * @return void
     */
    public function setRequestAuthentication(\WordPress\AiClient\Providers\Http\Contracts\RequestAuthenticationInterface $authentication): void;
    /**
     * Returns the request authentication.
     *
     * @since 0.1.0
     *
     * @return RequestAuthenticationInterface The authentication instance.
     */
    public function getRequestAuthentication(): \WordPress\AiClient\Providers\Http\Contracts\RequestAuthenticationInterface;
}

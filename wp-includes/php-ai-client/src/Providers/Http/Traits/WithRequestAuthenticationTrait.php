<?php

declare (strict_types=1);
namespace WordPress\AiClient\Providers\Http\Traits;

use WordPress\AiClient\Common\Exception\RuntimeException;
use WordPress\AiClient\Providers\Http\Contracts\RequestAuthenticationInterface;
/**
 * Trait for a class that implements WithRequestAuthenticationInterface.
 *
 * @since 0.1.0
 */
trait WithRequestAuthenticationTrait
{
    /**
     * @var RequestAuthenticationInterface|null The request authentication instance.
     */
    private ?RequestAuthenticationInterface $requestAuthentication = null;
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     */
    public function setRequestAuthentication(RequestAuthenticationInterface $requestAuthentication): void
    {
        $this->requestAuthentication = $requestAuthentication;
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     */
    public function getRequestAuthentication(): RequestAuthenticationInterface
    {
        if ($this->requestAuthentication === null) {
            throw new RuntimeException('RequestAuthenticationInterface instance not set. ' . 'Make sure you use the AiClient class for all requests.');
        }
        return $this->requestAuthentication;
    }
}

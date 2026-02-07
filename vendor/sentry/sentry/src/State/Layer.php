<?php

declare(strict_types=1);

namespace Sentry\State;

use Sentry\ClientInterface;

/**
 * This class holds a pair of client and scope instances for each element in the
 * stack of a {@see Hub}.
 *
 * @internal
 */
final class Layer
{
    /**
     * @var ClientInterface|null The client held by this layer
     */
    private $client;

    /**
     * @var Scope The scope held by this layer
     */
    private $scope;

    /**
     * Constructor.
     *
     * @param ClientInterface|null $client The client held by this layer
     * @param Scope                $scope  The scope held by this layer
     */
    public function __construct(?ClientInterface $client, Scope $scope)
    {
        $this->client = $client;
        $this->scope = $scope;
    }

    /**
     * Gets the client held by this layer.
     */
    public function getClient(): ?ClientInterface
    {
        return $this->client;
    }

    /**
     * Sets the client held by this layer.
     *
     * @param ClientInterface|null $client The client instance
     *
     * @return $this
     */
    public function setClient(?ClientInterface $client): self
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Gets the scope held by this layer.
     */
    public function getScope(): Scope
    {
        return $this->scope;
    }

    /**
     * Sets the scope held by this layer.
     *
     * @param Scope $scope The scope instance
     *
     * @return $this
     */
    public function setScope(Scope $scope): self
    {
        $this->scope = $scope;

        return $this;
    }
}

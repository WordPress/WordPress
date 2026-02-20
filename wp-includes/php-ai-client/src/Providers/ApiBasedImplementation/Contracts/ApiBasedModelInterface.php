<?php

declare (strict_types=1);
namespace WordPress\AiClient\Providers\ApiBasedImplementation\Contracts;

use WordPress\AiClient\Providers\Http\DTO\RequestOptions;
use WordPress\AiClient\Providers\Models\Contracts\ModelInterface;
/**
 * Interface for API-based AI models that support HTTP transport configuration.
 *
 * This interface extends ModelInterface to add request options support
 * for models that communicate with external APIs via HTTP.
 *
 * @since 0.3.0
 */
interface ApiBasedModelInterface extends ModelInterface
{
    /**
     * Sets the request options for HTTP transport.
     *
     * @since 0.3.0
     *
     * @param RequestOptions $requestOptions The request options to use.
     * @return void
     */
    public function setRequestOptions(RequestOptions $requestOptions): void;
    /**
     * Gets the request options for HTTP transport.
     *
     * @since 0.3.0
     *
     * @return RequestOptions|null The request options, or null if not set.
     */
    public function getRequestOptions(): ?RequestOptions;
}

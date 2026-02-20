<?php

declare (strict_types=1);
namespace WordPress\AiClient\Providers\Http\DTO;

use WordPress\AiClient\Common\AbstractDataTransferObject;
use WordPress\AiClient\Common\Exception\InvalidArgumentException;
/**
 * Represents optional HTTP transport configuration for a single request.
 *
 * Provides mutable setters for working with timeouts and redirect handling.
 *
 * @since 0.2.0
 *
 * @phpstan-type RequestOptionsArrayShape array{
 *     timeout?: float|null,
 *     connectTimeout?: float|null,
 *     maxRedirects?: int|null
 * }
 *
 * @extends AbstractDataTransferObject<RequestOptionsArrayShape>
 */
class RequestOptions extends AbstractDataTransferObject
{
    public const KEY_TIMEOUT = 'timeout';
    public const KEY_CONNECT_TIMEOUT = 'connectTimeout';
    public const KEY_MAX_REDIRECTS = 'maxRedirects';
    /**
     * @var float|null Maximum duration in seconds to wait for the full response.
     */
    protected ?float $timeout = null;
    /**
     * @var float|null Maximum duration in seconds to wait for the initial connection.
     */
    protected ?float $connectTimeout = null;
    /**
     * @var int|null Maximum number of redirects to follow. 0 disables redirects, null is unspecified.
     */
    protected ?int $maxRedirects = null;
    /**
     * Sets the request timeout in seconds.
     *
     * @since 0.2.0
     *
     * @param float|null $timeout Timeout in seconds.
     * @return void
     *
     * @throws InvalidArgumentException When timeout is negative.
     */
    public function setTimeout(?float $timeout): void
    {
        $this->validateTimeout($timeout, self::KEY_TIMEOUT);
        $this->timeout = $timeout;
    }
    /**
     * Sets the connection timeout in seconds.
     *
     * @since 0.2.0
     *
     * @param float|null $timeout Connection timeout in seconds.
     * @return void
     *
     * @throws InvalidArgumentException When timeout is negative.
     */
    public function setConnectTimeout(?float $timeout): void
    {
        $this->validateTimeout($timeout, self::KEY_CONNECT_TIMEOUT);
        $this->connectTimeout = $timeout;
    }
    /**
     * Sets the maximum number of redirects to follow.
     *
     * Set to 0 to disable redirects, null for unspecified, or a positive integer
     * to enable redirects with a maximum count.
     *
     * @since 0.2.0
     *
     * @param int|null $maxRedirects Maximum redirects to follow, or 0 to disable, or null for unspecified.
     * @return void
     *
     * @throws InvalidArgumentException When redirect count is negative.
     */
    public function setMaxRedirects(?int $maxRedirects): void
    {
        if ($maxRedirects !== null && $maxRedirects < 0) {
            throw new InvalidArgumentException('Request option "maxRedirects" must be greater than or equal to 0.');
        }
        $this->maxRedirects = $maxRedirects;
    }
    /**
     * Gets the request timeout in seconds.
     *
     * @since 0.2.0
     *
     * @return float|null Timeout in seconds.
     */
    public function getTimeout(): ?float
    {
        return $this->timeout;
    }
    /**
     * Gets the connection timeout in seconds.
     *
     * @since 0.2.0
     *
     * @return float|null Connection timeout in seconds.
     */
    public function getConnectTimeout(): ?float
    {
        return $this->connectTimeout;
    }
    /**
     * Checks whether redirects are allowed.
     *
     * @since 0.2.0
     *
     * @return bool|null True when redirects are allowed (maxRedirects > 0),
     *                   false when disabled (maxRedirects = 0),
     *                   null when unspecified (maxRedirects = null).
     */
    public function allowsRedirects(): ?bool
    {
        if ($this->maxRedirects === null) {
            return null;
        }
        return $this->maxRedirects > 0;
    }
    /**
     * Gets the maximum number of redirects to follow.
     *
     * @since 0.2.0
     *
     * @return int|null Maximum redirects or null when not specified.
     */
    public function getMaxRedirects(): ?int
    {
        return $this->maxRedirects;
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.2.0
     *
     * @return RequestOptionsArrayShape
     */
    public function toArray(): array
    {
        $data = [];
        if ($this->timeout !== null) {
            $data[self::KEY_TIMEOUT] = $this->timeout;
        }
        if ($this->connectTimeout !== null) {
            $data[self::KEY_CONNECT_TIMEOUT] = $this->connectTimeout;
        }
        if ($this->maxRedirects !== null) {
            $data[self::KEY_MAX_REDIRECTS] = $this->maxRedirects;
        }
        return $data;
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.2.0
     */
    public static function fromArray(array $array): self
    {
        $instance = new self();
        if (isset($array[self::KEY_TIMEOUT])) {
            $instance->setTimeout((float) $array[self::KEY_TIMEOUT]);
        }
        if (isset($array[self::KEY_CONNECT_TIMEOUT])) {
            $instance->setConnectTimeout((float) $array[self::KEY_CONNECT_TIMEOUT]);
        }
        if (isset($array[self::KEY_MAX_REDIRECTS])) {
            $instance->setMaxRedirects((int) $array[self::KEY_MAX_REDIRECTS]);
        }
        return $instance;
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.2.0
     */
    public static function getJsonSchema(): array
    {
        return ['type' => 'object', 'properties' => [self::KEY_TIMEOUT => ['type' => ['number', 'null'], 'minimum' => 0, 'description' => 'Maximum duration in seconds to wait for the full response.'], self::KEY_CONNECT_TIMEOUT => ['type' => ['number', 'null'], 'minimum' => 0, 'description' => 'Maximum duration in seconds to wait for the initial connection.'], self::KEY_MAX_REDIRECTS => ['type' => ['integer', 'null'], 'minimum' => 0, 'description' => 'Maximum redirects to follow. 0 disables, null is unspecified.']], 'additionalProperties' => \false];
    }
    /**
     * Validates timeout values.
     *
     * @since 0.2.0
     *
     * @param float|null $value Timeout to validate.
     * @param string $fieldName Field name for the error message.
     *
     * @throws InvalidArgumentException When timeout is negative.
     */
    private function validateTimeout(?float $value, string $fieldName): void
    {
        if ($value !== null && $value < 0) {
            throw new InvalidArgumentException(sprintf('Request option "%s" must be greater than or equal to 0.', $fieldName));
        }
    }
}

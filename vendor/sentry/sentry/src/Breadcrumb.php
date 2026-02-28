<?php

declare(strict_types=1);

namespace Sentry;

/**
 * This class stores all the information about a breadcrumb.
 *
 * @author Stefano Arlandini <sarlandini@alice.it>
 */
final class Breadcrumb
{
    /**
     * This constant defines the default breadcrumb type.
     */
    public const TYPE_DEFAULT = 'default';

    /**
     * This constant defines the http breadcrumb type.
     */
    public const TYPE_HTTP = 'http';

    /**
     * This constant defines the user breadcrumb type.
     */
    public const TYPE_USER = 'user';

    /**
     * This constant defines the navigation breadcrumb type.
     */
    public const TYPE_NAVIGATION = 'navigation';

    /**
     * This constant defines the error breadcrumb type.
     */
    public const TYPE_ERROR = 'error';

    /**
     * This constant defines the debug level for a breadcrumb.
     */
    public const LEVEL_DEBUG = 'debug';

    /**
     * This constant defines the info level for a breadcrumb.
     */
    public const LEVEL_INFO = 'info';

    /**
     * This constant defines the warning level for a breadcrumb.
     */
    public const LEVEL_WARNING = 'warning';

    /**
     * This constant defines the error level for a breadcrumb.
     */
    public const LEVEL_ERROR = 'error';

    /**
     * This constant defines the fatal level for a breadcrumb.
     */
    public const LEVEL_FATAL = 'fatal';

    /**
     * This constant defines the list of values allowed to be set as severity
     * level of the breadcrumb.
     */
    private const ALLOWED_LEVELS = [
        self::LEVEL_DEBUG,
        self::LEVEL_INFO,
        self::LEVEL_WARNING,
        self::LEVEL_ERROR,
        self::LEVEL_FATAL,
    ];

    /**
     * @var string The category of the breadcrumb
     */
    private $category;

    /**
     * @var string The type of breadcrumb
     */
    private $type;

    /**
     * @var string|null The message of the breadcrumb
     */
    private $message;

    /**
     * @var string The level of the breadcrumb
     */
    private $level;

    /**
     * @var array<string, mixed> The meta data of the breadcrumb
     */
    private $metadata;

    /**
     * @var float The timestamp of the breadcrumb
     */
    private $timestamp;

    /**
     * Constructor.
     *
     * @param string               $level     The error level of the breadcrumb
     * @param string               $type      The type of the breadcrumb
     * @param string               $category  The category of the breadcrumb
     * @param string|null          $message   Optional text message
     * @param array<string, mixed> $metadata  Additional information about the breadcrumb
     * @param float|null           $timestamp Optional timestamp of the breadcrumb
     */
    public function __construct(string $level, string $type, string $category, ?string $message = null, array $metadata = [], ?float $timestamp = null)
    {
        if (!\in_array($level, self::ALLOWED_LEVELS, true)) {
            throw new \InvalidArgumentException('The value of the $level argument must be one of the Breadcrumb::LEVEL_* constants.');
        }

        $this->type = $type;
        $this->level = $level;
        $this->category = $category;
        $this->message = $message;
        $this->metadata = $metadata;
        $this->timestamp = $timestamp ?? microtime(true);
    }

    /**
     * Gets the breadcrumb type.
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Sets the type of the breadcrumb.
     *
     * @param string $type The type
     *
     * @return static
     */
    public function withType(string $type): self
    {
        if ($type === $this->type) {
            return $this;
        }

        $new = clone $this;
        $new->type = $type;

        return $new;
    }

    /**
     * Gets the breadcrumb level.
     */
    public function getLevel(): string
    {
        return $this->level;
    }

    /**
     * Sets the error level of the breadcrumb.
     *
     * @param string $level The level
     *
     * @return static
     */
    public function withLevel(string $level): self
    {
        if (!\in_array($level, self::ALLOWED_LEVELS, true)) {
            throw new \InvalidArgumentException('The value of the $level argument must be one of the Breadcrumb::LEVEL_* constants.');
        }

        if ($level === $this->level) {
            return $this;
        }

        $new = clone $this;
        $new->level = $level;

        return $new;
    }

    /**
     * Gets the breadcrumb category.
     */
    public function getCategory(): string
    {
        return $this->category;
    }

    /**
     * Sets the breadcrumb category.
     *
     * @param string $category The category
     *
     * @return static
     */
    public function withCategory(string $category): self
    {
        if ($category === $this->category) {
            return $this;
        }

        $new = clone $this;
        $new->category = $category;

        return $new;
    }

    /**
     * Gets the breadcrumb message.
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * Sets the breadcrumb message.
     *
     * @param string $message The message
     *
     * @return static
     */
    public function withMessage(string $message): self
    {
        if ($message === $this->message) {
            return $this;
        }

        $new = clone $this;
        $new->message = $message;

        return $new;
    }

    /**
     * Gets the breadcrumb meta data.
     *
     * @return array<string, mixed>
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * Returns an instance of this class with the provided metadata, replacing
     * any existing values of any metadata with the same name.
     *
     * @param string $name  The name of the metadata
     * @param mixed  $value The value
     *
     * @return static
     */
    public function withMetadata(string $name, $value): self
    {
        if (isset($this->metadata[$name]) && $value === $this->metadata[$name]) {
            return $this;
        }

        $new = clone $this;
        $new->metadata[$name] = $value;

        return $new;
    }

    /**
     * Returns an instance of this class without the specified metadata
     * information.
     *
     * @param string $name The name of the metadata
     *
     * @return static|Breadcrumb
     */
    public function withoutMetadata(string $name): self
    {
        if (!isset($this->metadata[$name])) {
            return $this;
        }

        $new = clone $this;

        unset($new->metadata[$name]);

        return $new;
    }

    /**
     * Gets the breadcrumb timestamp.
     */
    public function getTimestamp(): float
    {
        return $this->timestamp;
    }

    /**
     * Sets the breadcrumb timestamp.
     *
     * @param float $timestamp The timestamp
     *
     * @return static
     */
    public function withTimestamp(float $timestamp): self
    {
        if ($timestamp === $this->timestamp) {
            return $this;
        }

        $new = clone $this;
        $new->timestamp = $timestamp;

        return $new;
    }

    /**
     * Helper method to create an instance of this class from an array of data.
     *
     * @param array $data Data used to populate the breadcrumb
     *
     * @psalm-param array{
     *     level: string,
     *     type?: string,
     *     category: string,
     *     message?: string|null,
     *     data?: array<string, mixed>,
     *     timestamp?: float|null
     * } $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['level'],
            $data['type'] ?? self::TYPE_DEFAULT,
            $data['category'],
            $data['message'] ?? null,
            $data['data'] ?? [],
            $data['timestamp'] ?? null
        );
    }
}

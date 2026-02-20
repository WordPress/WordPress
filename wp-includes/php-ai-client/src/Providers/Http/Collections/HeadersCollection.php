<?php

declare (strict_types=1);
namespace WordPress\AiClient\Providers\Http\Collections;

/**
 * Simple collection for managing HTTP headers with case-insensitive access.
 *
 * This class stores HTTP headers while preserving their original casing
 * and provides efficient case-insensitive lookups.
 *
 * @since 0.1.0
 */
class HeadersCollection
{
    /**
     * @var array<string, list<string>> The headers with original casing.
     */
    private array $headers = [];
    /**
     * @var array<string, string> Map of lowercase header names to actual header names.
     */
    private array $headersMap = [];
    /**
     * Constructor.
     *
     * @since 0.1.0
     *
     * @param array<string, string|list<string>> $headers Initial headers.
     */
    public function __construct(array $headers = [])
    {
        foreach ($headers as $name => $value) {
            $this->set($name, $value);
        }
    }
    /**
     * Gets a specific header value.
     *
     * @since 0.1.0
     *
     * @param string $name The header name (case-insensitive).
     * @return list<string>|null The header value(s) or null if not found.
     */
    public function get(string $name): ?array
    {
        $lowerName = strtolower($name);
        if (!isset($this->headersMap[$lowerName])) {
            return null;
        }
        $actualName = $this->headersMap[$lowerName];
        return $this->headers[$actualName];
    }
    /**
     * Gets all headers.
     *
     * @since 0.1.0
     *
     * @return array<string, list<string>> All headers with their original casing.
     */
    public function getAll(): array
    {
        return $this->headers;
    }
    /**
     * Gets header values as a comma-separated string.
     *
     * @since 0.1.0
     *
     * @param string $name The header name (case-insensitive).
     * @return string|null The header values as a comma-separated string or null if not found.
     */
    public function getAsString(string $name): ?string
    {
        $values = $this->get($name);
        return $values !== null ? implode(', ', $values) : null;
    }
    /**
     * Checks if a header exists.
     *
     * @since 0.1.0
     *
     * @param string $name The header name (case-insensitive).
     * @return bool True if the header exists, false otherwise.
     */
    public function has(string $name): bool
    {
        return isset($this->headersMap[strtolower($name)]);
    }
    /**
     * Sets a header value, replacing any existing value.
     *
     * @since 0.1.0
     *
     * @param string $name The header name.
     * @param string|list<string> $value The header value(s).
     * @return void
     */
    private function set(string $name, $value): void
    {
        if (is_array($value)) {
            $normalizedValues = array_values($value);
        } else {
            // Split comma-separated string into array
            $normalizedValues = array_map('trim', explode(',', $value));
        }
        $lowerName = strtolower($name);
        // If header exists with different casing, remove the old casing
        if (isset($this->headersMap[$lowerName])) {
            $oldName = $this->headersMap[$lowerName];
            if ($oldName !== $name) {
                unset($this->headers[$oldName]);
            }
        }
        // Always use the new casing
        $this->headers[$name] = $normalizedValues;
        $this->headersMap[$lowerName] = $name;
    }
    /**
     * Returns a new instance with the specified header.
     *
     * @since 0.1.0
     *
     * @param string $name The header name.
     * @param string|list<string> $value The header value(s).
     * @return self A new instance with the header.
     */
    public function withHeader(string $name, $value): self
    {
        $new = clone $this;
        $new->set($name, $value);
        return $new;
    }
}

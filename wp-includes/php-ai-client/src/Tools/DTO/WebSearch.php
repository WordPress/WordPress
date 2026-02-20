<?php

declare (strict_types=1);
namespace WordPress\AiClient\Tools\DTO;

use WordPress\AiClient\Common\AbstractDataTransferObject;
/**
 * Represents web search configuration for AI models.
 *
 * This DTO defines constraints for web searches that AI models can perform,
 * including allowed and disallowed domains.
 *
 * @since 0.1.0
 *
 * @phpstan-type WebSearchArrayShape array{allowedDomains?: string[], disallowedDomains?: string[]}
 *
 * @extends AbstractDataTransferObject<WebSearchArrayShape>
 */
class WebSearch extends AbstractDataTransferObject
{
    public const KEY_ALLOWED_DOMAINS = 'allowedDomains';
    public const KEY_DISALLOWED_DOMAINS = 'disallowedDomains';
    /**
     * @var string[] List of domains that are allowed for web search.
     */
    private array $allowedDomains;
    /**
     * @var string[] List of domains that are disallowed for web search.
     */
    private array $disallowedDomains;
    /**
     * Constructor.
     *
     * @since 0.1.0
     *
     * @param string[] $allowedDomains List of domains that are allowed for web search.
     * @param string[] $disallowedDomains List of domains that are disallowed for web search.
     */
    public function __construct(array $allowedDomains = [], array $disallowedDomains = [])
    {
        $this->allowedDomains = $allowedDomains;
        $this->disallowedDomains = $disallowedDomains;
    }
    /**
     * Gets the allowed domains.
     *
     * @since 0.1.0
     *
     * @return string[] The allowed domains.
     */
    public function getAllowedDomains(): array
    {
        return $this->allowedDomains;
    }
    /**
     * Gets the disallowed domains.
     *
     * @since 0.1.0
     *
     * @return string[] The disallowed domains.
     */
    public function getDisallowedDomains(): array
    {
        return $this->disallowedDomains;
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     */
    public static function getJsonSchema(): array
    {
        return ['type' => 'object', 'properties' => [self::KEY_ALLOWED_DOMAINS => ['type' => 'array', 'items' => ['type' => 'string'], 'description' => 'List of domains that are allowed for web search.'], self::KEY_DISALLOWED_DOMAINS => ['type' => 'array', 'items' => ['type' => 'string'], 'description' => 'List of domains that are disallowed for web search.']], 'required' => []];
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     *
     * @return WebSearchArrayShape
     */
    public function toArray(): array
    {
        return [self::KEY_ALLOWED_DOMAINS => $this->allowedDomains, self::KEY_DISALLOWED_DOMAINS => $this->disallowedDomains];
    }
    /**
     * {@inheritDoc}
     *
     * @since 0.1.0
     */
    public static function fromArray(array $array): self
    {
        return new self($array[self::KEY_ALLOWED_DOMAINS] ?? [], $array[self::KEY_DISALLOWED_DOMAINS] ?? []);
    }
}

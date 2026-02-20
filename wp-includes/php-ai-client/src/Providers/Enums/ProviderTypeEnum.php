<?php

declare (strict_types=1);
namespace WordPress\AiClient\Providers\Enums;

use WordPress\AiClient\Common\AbstractEnum;
/**
 * Enum for provider types.
 *
 * @since 0.1.0
 *
 * @method static self cloud() Creates an instance for CLOUD type.
 * @method static self server() Creates an instance for SERVER type.
 * @method static self client() Creates an instance for CLIENT type.
 * @method bool isCloud() Checks if the type is CLOUD.
 * @method bool isServer() Checks if the type is SERVER.
 * @method bool isClient() Checks if the type is CLIENT.
 */
class ProviderTypeEnum extends AbstractEnum
{
    /**
     * Cloud-based AI provider (e.g. models available via external REST APIs).
     */
    public const CLOUD = 'cloud';
    /**
     * Server-side AI provider (e.g. self-hosted models).
     */
    public const SERVER = 'server';
    /**
     * Client-side AI provider (e.g. browser-based models).
     */
    public const CLIENT = 'client';
}

<?php

declare (strict_types=1);
namespace Sentry\Integration;

use Sentry\Options;
interface OptionAwareIntegrationInterface extends \Sentry\Integration\IntegrationInterface
{
    /**
     * Sets the options for the integration, is called before `setupOnce()`.
     */
    public function setOptions(\Sentry\Options $options) : void;
}

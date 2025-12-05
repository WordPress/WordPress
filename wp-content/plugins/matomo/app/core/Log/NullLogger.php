<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Log;

/**
 * Proxy class for \Psr\Log\NullLogger
 * @see \Psr\Log\NullLogger
 */
class NullLogger extends \Matomo\Dependencies\Psr\Log\NullLogger implements \Piwik\Log\LoggerInterface
{
}

<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\SitesManager\SiteContentDetection;

abstract class ConsentManagerDetectionAbstract extends \Piwik\Plugins\SitesManager\SiteContentDetection\SiteContentDetectionAbstract
{
    public static final function getContentType() : int
    {
        return self::TYPE_CONSENT_MANAGER;
    }
    /**
     * Returns if the consent manager was already connected to Matomo
     *
     * @param string|null $data
     * @param array|null $headers
     * @return bool
     */
    public abstract function checkIsConnected(?string $data = null, ?array $headers = null) : bool;
}

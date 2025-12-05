<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager\API;

use Piwik\Cookie;
class PreviewCookie extends Cookie
{
    public const COOKIE_NAME = 'mtmPreviewMode';
    public const DEBUG_SITE_URL_COOKIE_NAME = 'mtmPreviewSiteURL';
    public function __construct()
    {
        $oneWeekInSeconds = 604800;
        $expire = time() + $oneWeekInSeconds;
        parent::__construct(self::COOKIE_NAME, $expire);
    }
    public function getCookieValueName($idSite, $idContainer)
    {
        return 'mtmPreview' . $idSite . '_' . $idContainer;
    }
    public function enable($idSite, $idContainer)
    {
        $this->set($this->getCookieValueName($idSite, $idContainer), '1');
        $this->save('Lax');
    }
    public function disable($idSite, $idContainer)
    {
        $this->set($this->getCookieValueName($idSite, $idContainer), null);
        $this->save('Lax');
    }
    public function enableDebugSiteUrl($url)
    {
        $this->set(self::DEBUG_SITE_URL_COOKIE_NAME, $url);
        $this->save('Lax');
    }
    public function disableDebugSiteUrl()
    {
        $this->set(self::DEBUG_SITE_URL_COOKIE_NAME, null);
        $this->save('Lax');
    }
    public function getDebugSiteUrl()
    {
        return $this->get(self::DEBUG_SITE_URL_COOKIE_NAME);
    }
}

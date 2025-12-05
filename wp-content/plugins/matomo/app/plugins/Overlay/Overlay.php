<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\Overlay;

use Piwik\Url;
use Piwik\UrlHelper;
class Overlay extends \Piwik\Plugin
{
    /**
     * @see \Piwik\Plugin::registerEvents
     */
    public function registerEvents()
    {
        return array('AssetManager.getJavaScriptFiles' => 'getJsFiles', 'Translate.getClientSideTranslationKeys' => 'getClientSideTranslationKeys');
    }
    /**
     * Returns required Js Files
     * @param $jsFiles
     */
    public function getJsFiles(&$jsFiles)
    {
        $jsFiles[] = 'plugins/Overlay/javascripts/rowaction.js';
        $jsFiles[] = 'plugins/Overlay/javascripts/Overlay_Helper.js';
    }
    public function getClientSideTranslationKeys(&$translationKeys)
    {
        $translationKeys[] = 'General_OverlayRowActionTooltipTitle';
        $translationKeys[] = 'General_OverlayRowActionTooltip';
    }
    /**
     * Returns if a request belongs to the Overlay page
     *
     * Whenever we change the Overlay, or any feature that is available on that page, this list needs to be adjusted
     * Otherwise it can happen, that the session cookie is sent with samesite=lax, which might break the session in Overlay
     * See https://github.com/matomo-org/matomo/pull/18648
     */
    public static function isOverlayRequest($module, $action, $method, $referer)
    {
        $isOverlay = $module == 'Overlay';
        $referrerUrlQuery = parse_url($referer ?? '', \PHP_URL_QUERY);
        $referrerUrlQueryParams = UrlHelper::getArrayFromQueryString($referrerUrlQuery);
        $referrerUrlHost = parse_url($referer ?? '', \PHP_URL_HOST);
        $comingFromOverlay = Url::isValidHost($referrerUrlHost) && !empty($referrerUrlQueryParams['module']) && $referrerUrlQueryParams['module'] === 'Overlay';
        $isPossibleOverlayRequest = $module === 'Proxy' || $module === 'API' && 0 === strpos($method, 'Overlay.') || $module === 'CoreHome' && $action === 'getRowEvolutionPopover' || $module === 'CoreHome' && $action === 'getRowEvolutionGraph' || $module === 'CoreHome' && $action === 'saveViewDataTableParameters' || $module === 'Annotations' || $module === 'Transitions' && $action === 'renderPopover' || $module === 'API' && 0 === strpos($method, 'Transitions.') || $module === 'Live' && $action === 'indexVisitorLog' || $module === 'Live' && $action === 'getLastVisitsDetails' || $module === 'Live' && $action === 'getVisitorProfilePopup' || $module === 'Live' && $action === 'getVisitList' || $module === 'UserCountryMap' && $action === 'realtimeMap';
        return $isOverlay || $comingFromOverlay && $isPossibleOverlayRequest;
    }
}

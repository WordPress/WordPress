<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager\Context\WebContext;

use Piwik\Context;
use Piwik\Development;
use Piwik\FrontController;
use Piwik\Piwik;
use Piwik\Plugins\TagManager\API\PreviewCookie;
class JavaScriptTagManagerLoader
{
    public function getJavaScriptContent()
    {
        $basePath = PIWIK_DOCUMENT_ROOT . '/plugins/TagManager/javascripts/';
        $tagManagerJs = $basePath . 'tagmanager.js';
        $tagManagerMinJs = $basePath . 'tagmanager.min.js';
        if (Development::isEnabled() || !file_exists($tagManagerMinJs)) {
            $baseJs = file_get_contents($tagManagerJs);
        } else {
            $baseJs = file_get_contents($tagManagerMinJs);
        }
        return $baseJs;
    }
    public function getDetectPreviewModeContent($previewUrl, $idSite, $idContainer)
    {
        $previewCookie = new PreviewCookie();
        $id = $previewCookie->getCookieValueName($idSite, $idContainer);
        $cookieId = $id . urlencode('=') . '1';
        $urlParamEnabledId = PreviewCookie::COOKIE_NAME . '=' . $idContainer;
        $urlParamDisableId = PreviewCookie::COOKIE_NAME . '=0';
        $path = PIWIK_DOCUMENT_ROOT . '/plugins/TagManager/javascripts/previewmodedetection.js';
        $previewJs = file_get_contents($path);
        $previewJs = str_replace('$cookieId', $cookieId, $previewJs);
        $previewJs = str_replace('$urlParamDisableId', $urlParamDisableId, $previewJs);
        $previewJs = str_replace('$urlParamEnabledId', $urlParamEnabledId, $previewJs);
        $previewJs = str_replace('$previewUrl', $previewUrl, $previewJs);
        return $previewJs;
    }
    public function getPreviewJsContent()
    {
        $unsetGet = \false;
        $unsetPost = \false;
        if (!isset($_GET)) {
            $_GET = array();
            $unsetGet = \true;
        }
        if (!isset($_POST)) {
            $_POST = array();
            $unsetPost = \true;
        }
        $path = PIWIK_DOCUMENT_ROOT . '/plugins/TagManager/javascripts/previewmode.js';
        $previewJs = file_get_contents($path);
        $debugContent = '';
        Context::executeWithQueryParameters(array('period' => 'day', 'date' => 'today'), function () use(&$debugContent) {
            $debugContent = FrontController::getInstance()->dispatch('TagManager', 'debug');
        });
        $debugContent = str_replace(Piwik::getCurrentUserTokenAuth() ?? '', 'anonymous', $debugContent);
        // make sure to not expose somehow the token
        $debugContent = json_encode($debugContent);
        $previewJs = str_replace(array('/*!! previewContent */', '/*!!! previewContent */'), $debugContent, $previewJs);
        if ($unsetGet) {
            unset($_GET);
        }
        if ($unsetPost) {
            unset($_POST);
        }
        return $previewJs;
    }
}

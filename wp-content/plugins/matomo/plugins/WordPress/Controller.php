<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */

namespace Piwik\Plugins\WordPress;

use Piwik\Access;
use Piwik\NoAccessException;
use Piwik\Piwik;
use Piwik\Plugin\Manager;
use Piwik\Request;
use Piwik\View;

if (!defined( 'ABSPATH')) {
	exit; // if accessed directly
}

class Controller extends \Piwik\Plugin\ControllerAdmin
{
    public function index()
    {
        if (!is_user_logged_in()) {
        	$redirect_url = WordPress::getWpLoginUrl();
	        wp_safe_redirect($redirect_url);
        }
    }

    public function logout()
    {
        if (is_user_logged_in()) {
	        wp_safe_redirect(wp_logout_url());
        }
        exit;
    }

    public function goToWordPress()
    {
        if (is_user_logged_in()) {
	        wp_safe_redirect(admin_url());
        }
        exit;
    }

    public function showMeasurableSettings()
    {
        Piwik::checkUserIsNotAnonymous();

        $idSite = Request::fromRequest()->getIntegerParameter('idSite', 0);
        $pluginName = Request::fromRequest()->getStringParameter('plugin', '');
        if (!$idSite || !$pluginName || !Manager::getInstance()->isPluginActivated($pluginName)) {
            return '';
        }

        if (!is_user_logged_in()
            || !$this->doesUserHaveAdminAccessTo($idSite)
        ) {
            $view = new View('@WordPress/measurableSettingsNoAccess.twig');
            $this->setBasicVariablesNoneAdminView($view);
            $view->setXFrameOptions('same-origin');
            $view->adminPhpUrl = $this->getAdminPhpUrl();
            return $view->render();
        }

        $view = new View('@WordPress/measurableSettings.twig');
        $view->idSite = $idSite;
        $view->pluginName = $pluginName;
        $view->adminPhpUrl = $this->getAdminPhpUrl();
        $this->setBasicVariablesView($view);
        $view->setXFrameOptions('same-origin');
        return $view->render();
    }

    private function doesUserHaveAdminAccessTo($idSite)
    {
        try {
            Access::getInstance()->checkUserHasAdminAccess($idSite);
            return true;
        } catch (NoAccessException $ex) {
            return false;
        }
    }

    private function getAdminPhpUrl()
    {
        return home_url('/wp-admin/admin.php');
    }
}

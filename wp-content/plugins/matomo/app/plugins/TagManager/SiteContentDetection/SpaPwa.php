<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\TagManager\SiteContentDetection;

use Piwik\Container\StaticContainer;
use Piwik\Piwik;
use Piwik\Plugins\SitesManager\SiteContentDetection\SiteContentDetectionAbstract;
use Piwik\SiteContentDetector;
use Piwik\View;
class SpaPwa extends SiteContentDetectionAbstract
{
    public static function getName() : string
    {
        return 'Single Page Application';
    }
    public static function getIcon() : string
    {
        return './plugins/TagManager/images/spa.png';
    }
    public static function getContentType() : int
    {
        return self::TYPE_JS_FRAMEWORK;
    }
    public static function getPriority() : int
    {
        return 70;
    }
    public function isDetected(?string $data = null, ?array $headers = null) : bool
    {
        return \false;
    }
    public function renderInstructionsTab(SiteContentDetector $detector) : string
    {
        $model = StaticContainer::get('Piwik\\Plugins\\TagManager\\Model\\Container');
        $view = new View("@TagManager/trackingSPA");
        $view->action = Piwik::getAction();
        $view->showContainerRow = $model->getNumContainersTotal() > 1;
        return $view->render();
    }
}

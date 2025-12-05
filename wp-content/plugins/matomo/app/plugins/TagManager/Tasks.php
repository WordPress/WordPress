<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager;

use Piwik\Plugin;
use Piwik\Plugins\TagManager\Dao\ContainersDao;
use Piwik\Site;
use Piwik\Exception\UnexpectedWebsiteFoundException;
class Tasks extends \Piwik\Plugin\Tasks
{
    /**
     * @var Plugin\Manager
     */
    private $pluginManager;
    public function __construct(Plugin\Manager $pluginManager)
    {
        $this->pluginManager = $pluginManager;
    }
    public function schedule()
    {
        $this->hourly('regenerateReleasedContainers');
        $this->daily('deleteContainersForNonExistingSite');
    }
    public function regenerateReleasedContainers()
    {
        /** @var TagManager $tagManager */
        $tagManager = $this->pluginManager->getLoadedPlugin('TagManager');
        if ($tagManager) {
            $tagManager->regenerateReleasedContainers();
        }
    }
    public function deleteContainersForNonExistingSite()
    {
        /** @var TagManager $tagManager */
        $tagManager = $this->pluginManager->getLoadedPlugin('TagManager');
        if (!$tagManager) {
            return;
        }
        $containerDao = new ContainersDao();
        $containers = $containerDao->getActiveContainersInfo();
        $siteIdsDeleted = [];
        foreach ($containers as $container) {
            $idSite = $container['idsite'];
            if (!isset($siteIdsDeleted[$idSite]) && !$this->siteExists($idSite)) {
                $siteIdsDeleted[$idSite] = $idSite;
            }
        }
        if (!empty($siteIdsDeleted)) {
            $tagManager = new \Piwik\Plugins\TagManager\TagManager();
            foreach ($siteIdsDeleted as $siteId) {
                $tagManager->onSiteDeleted($siteId);
            }
        }
    }
    private function siteExists($idSite)
    {
        try {
            new Site($idSite);
            return \true;
        } catch (UnexpectedWebsiteFoundException $ex) {
            return \false;
        }
    }
}

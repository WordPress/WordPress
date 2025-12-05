<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager\API;

use Piwik\Plugins\TagManager\Model\Container;
use Piwik\Plugins\TagManager\Model\Tag;
use Piwik\Plugins\TagManager\Model\Trigger;
use Piwik\Plugins\TagManager\Model\Variable;
class Export
{
    /**
     * @var Tag
     */
    private $tags;
    /**
     * @var Trigger
     */
    private $triggers;
    /**
     * @var Variable
     */
    private $variables;
    /**
     * @var Container
     */
    private $containers;
    public function __construct(Tag $tags, Trigger $triggers, Variable $variables, Container $containers)
    {
        $this->tags = $tags;
        $this->triggers = $triggers;
        $this->variables = $variables;
        $this->containers = $containers;
    }
    public function exportContainerVersion($idSite, $idContainer, $idContainerVersion)
    {
        $container = $this->containers->getContainer($idSite, $idContainer);
        $version = $this->containers->getContainerVersion($idSite, $idContainer, $idContainerVersion);
        $container['revision'] = $version['revision'];
        $container['version'] = array('name' => $version['name'], 'description' => $version['description'], 'revision' => $version['revision'], 'created_date' => $version['created_date'], 'created_date_pretty' => $version['created_date_pretty'], 'updated_date' => $version['updated_date'], 'updated_date_pretty' => $version['updated_date_pretty']);
        $container['tags'] = $this->exportTags($idSite, $idContainerVersion);
        $container['triggers'] = $this->exportTriggers($idSite, $idContainerVersion);
        $container['variables'] = $this->exportVariables($idSite, $idContainerVersion);
        unset($container['status']);
        unset($container['deleted_date']);
        unset($container['draft']);
        unset($container['versions']);
        unset($container['releases']);
        return $container;
    }
    public function exportTags($idSite, $idContainerVersion)
    {
        $tags = $this->tags->getContainerTags($idSite, $idContainerVersion);
        foreach ($tags as $index => &$tag) {
            unset($tag['idcontainerversion']);
            unset($tag['idsite']);
            unset($tag['typeMetadata']);
            unset($tag['deleted_date']);
        }
        return $tags;
    }
    public function exportTriggers($idSite, $idContainerVersion)
    {
        $triggers = $this->triggers->getContainerTriggers($idSite, $idContainerVersion);
        foreach ($triggers as $index => &$trigger) {
            unset($trigger['idcontainerversion']);
            unset($trigger['status']);
            unset($trigger['idsite']);
            unset($trigger['typeMetadata']);
            unset($trigger['deleted_date']);
        }
        return $triggers;
    }
    public function exportVariables($idSite, $idContainerVersion)
    {
        $variables = $this->variables->getContainerVariables($idSite, $idContainerVersion);
        foreach ($variables as $index => &$variable) {
            unset($variable['idcontainerversion']);
            unset($variable['status']);
            unset($variable['idsite']);
            unset($variable['typeMetadata']);
            unset($variable['deleted_date']);
        }
        return $variables;
    }
}

<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager\Context;

use Piwik\Container\StaticContainer;
use Piwik\Plugin\Manager;
class ContextProvider
{
    /**
     * @var BaseContext[]
     */
    private $cached;
    /**
     * @var Manager
     */
    private $pluginManager;
    public function __construct(Manager $pluginManager)
    {
        $this->pluginManager = $pluginManager;
    }
    public function checkIsValidContext($contextId)
    {
        if (!$this->getContext($contextId)) {
            throw new \Exception(sprintf('The context "%s" is not supported', $contextId));
        }
    }
    /**
     * @param string $contextId  eg "web"
     * @return BaseContext|null
     */
    public function getContext($contextId)
    {
        foreach ($this->getAllContexts() as $context) {
            if ($context->getId() === $contextId) {
                return $context;
            }
        }
    }
    /**
     * @return BaseContext[]
     */
    public function getAllContexts()
    {
        if (!isset($this->cached)) {
            $tags = $this->pluginManager->findMultipleComponents('Context', 'Piwik\\Plugins\\TagManager\\Context\\BaseContext');
            $this->cached = array();
            foreach ($tags as $tag) {
                $this->cached[] = StaticContainer::get($tag);
            }
            usort($this->cached, function ($a, $b) {
                /** @var $a baseContext */
                /** @var $b baseContext */
                return $a->getOrder() - $b->getOrder();
            });
        }
        return $this->cached;
    }
}

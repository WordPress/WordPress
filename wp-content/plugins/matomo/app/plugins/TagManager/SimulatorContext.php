<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager;

use Piwik\Plugins\TagManager\Context\BaseContext;
// we don't have this context in the "Context" directory of this plugin because we don't want it to be picked up as a component
// we only want to use it to detect whether a variable references itself or if there is any recursion
class SimulatorContext extends BaseContext
{
    public const ID = 'simulator';
    public function getId()
    {
        return self::ID;
    }
    public function getName()
    {
        return 'Simulator';
    }
    public function getOrder()
    {
        return 15;
    }
    public function generate($container)
    {
        foreach ($container['releases'] as $release) {
            // we don't actually look at the output and we also don't save anything
            // we only simulate the core logic of any container generation
            $this->generatePublicContainer($container, $release);
        }
        return [];
    }
    public function getInstallInstructions($container, $environment)
    {
        return [];
    }
    public function getInstallInstructionsReact($container, $environment)
    {
        return [];
    }
}

<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager\Context;

use Piwik\Common;
use Piwik\SettingsPiwik;
class iOSContext extends \Piwik\Plugins\TagManager\Context\BaseContext
{
    public const ID = 'ios';
    public function getId()
    {
        return self::ID;
    }
    public function getName()
    {
        return 'iOS';
    }
    public function getOrder()
    {
        return 10;
    }
    public function generate($container)
    {
        $filesCreated = array();
        foreach ($container['releases'] as $release) {
            $containerJs = $this->generatePublicContainer($container, $release);
            $path = $this->getJsTargetPath($container['idsite'], $container['idcontainer'], $release['environment'], $container['created_date']);
            $filesCreated[$path] = json_encode($containerJs);
            $this->storage->save(PIWIK_DOCUMENT_ROOT . $path, $filesCreated[$path]);
        }
        return $filesCreated;
    }
    public function getJsTargetPath($idSite, $idContainer, $environment, $containerCreatedDate)
    {
        return parent::getJsTargetPath($idSite, $idContainer, $environment, $containerCreatedDate) . '.json';
    }
    public function getInstallInstructions($container, $environment)
    {
        $domain = SettingsPiwik::getPiwikUrl();
        if (Common::stringEndsWith($domain, '/')) {
            $domain = Common::mb_substr($domain, 0, -1);
        }
        $path = $domain . $this->getJsTargetPath($container['idsite'], $container['idcontainer'], $environment, $container['created_date']);
        return [['description' => 'The JSON to embed in your mobile app is available at: ' . $path, 'embedCode' => '', 'helpUrl' => '']];
    }
    public function getInstallInstructionsReact($container, $environment)
    {
        return $this->getInstallInstructions($container, $environment);
    }
}

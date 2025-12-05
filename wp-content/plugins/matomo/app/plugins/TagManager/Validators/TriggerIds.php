<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager\Validators;

use Piwik\Container\StaticContainer;
use Piwik\Piwik;
use Piwik\Validators\BaseValidator;
use Piwik\Validators\Exception;
class TriggerIds extends BaseValidator
{
    private $idSite;
    private $idContainerVersion;
    /**
     * @var \Piwik\Plugins\TagManager\Model\Trigger
     */
    private $trigger;
    public function __construct($idSite, $idContainerVersion)
    {
        $this->idSite = $idSite;
        $this->idContainerVersion = $idContainerVersion;
        $this->trigger = StaticContainer::get('Piwik\\Plugins\\TagManager\\Model\\Trigger');
    }
    public function validate($value)
    {
        if (!is_array($value)) {
            throw new Exception(Piwik::translate('TagManager_ErrorNotAnArray', Piwik::translate('TagManager_Triggers')));
        }
        foreach ($value as $index => $triggerId) {
            $trigger = $this->trigger->getContainerTrigger($this->idSite, $this->idContainerVersion, $triggerId);
            if (empty($trigger)) {
                throw new Exception(Piwik::translate('TagManager_ErrorTriggerAtPositionXDoesNotExist', array($triggerId, $index)));
            }
        }
    }
}

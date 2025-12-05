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
use Piwik\Plugins\TagManager\Template\Variable\CustomRequestProcessingVariable;
class CustomRequestProcessing extends BaseValidator
{
    private $idSite;
    private $idContainer;
    /**
     * @var \Piwik\Plugins\TagManager\Model\Container
     */
    private $container;
    /**
     * @var \Piwik\Plugins\TagManager\Model\Variable
     */
    private $variable;
    public function __construct($idSite, $idContainer)
    {
        $this->idSite = $idSite;
        $this->idContainer = $idContainer;
        $this->container = StaticContainer::get('Piwik\\Plugins\\TagManager\\Model\\Container');
        $this->variable = StaticContainer::get('Piwik\\Plugins\\TagManager\\Model\\Variable');
    }
    public function validate($value)
    {
        if (empty($value)) {
            return;
        }
        $customRequestProcessingVariableName = Piwik::translate('TagManager_CustomRequestProcessingVariableName');
        if (substr($value, 0, 2) != '{{' || substr($value, -2, 2) != '}}' || strlen($value) < 5) {
            throw new Exception(Piwik::translate('TagManager_ErrorNotAnVariableOfTypeException', $customRequestProcessingVariableName));
        }
        $idContainerVersion = $this->container->getContainer($this->idSite, $this->idContainer)['draft']['idcontainerversion'];
        $name = substr($value, 2, -2);
        $target = $this->variable->findVariableByName($this->idSite, $idContainerVersion, $name);
        if (!is_array($target) || $target['type'] != CustomRequestProcessingVariable::ID) {
            throw new Exception(Piwik::translate('TagManager_ErrorNotAnVariableOfTypeException', $customRequestProcessingVariableName));
        }
    }
}

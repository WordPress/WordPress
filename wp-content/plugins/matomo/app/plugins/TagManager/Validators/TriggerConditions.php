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
use Piwik\Plugins\TagManager\Model\Variable;
use Piwik\Plugins\TagManager\Template\Variable\VariablesProvider;
use Piwik\Validators\BaseValidator;
use Piwik\Validators\Exception;
class TriggerConditions extends BaseValidator
{
    private $idSite;
    private $idContainerVersion;
    /**
     * @var \Piwik\Plugins\TagManager\Model\Comparison
     */
    private $comparisons;
    /**
     * @var VariablesProvider
     */
    private $variablesProvider;
    /**
     * @var Variable
     */
    private $variable;
    public function __construct($idSite, $idContainerVersion)
    {
        $this->idSite = $idSite;
        $this->idContainerVersion = $idContainerVersion;
        $this->comparisons = StaticContainer::get('Piwik\\Plugins\\TagManager\\Model\\Comparison');
        $this->variable = StaticContainer::get('Piwik\\Plugins\\TagManager\\Model\\Variable');
        $this->variablesProvider = StaticContainer::get('Piwik\\Plugins\\TagManager\\Template\\Variable\\VariablesProvider');
    }
    public function validate($value)
    {
        $titlePlural = Piwik::translate('TagManager_Conditions');
        if (!is_array($value)) {
            throw new Exception(Piwik::translate('TagManager_ErrorNotAnArray', $titlePlural));
        }
        foreach ($value as $index => $condition) {
            if (!is_array($condition)) {
                $titleSingular = Piwik::translate('TagManager_Condition');
                throw new Exception(Piwik::translate('TagManager_ErrorInnerIsNotAnArray', array($titleSingular, $titlePlural)));
            }
            if (empty($condition['actual'])) {
                throw new Exception(Piwik::translate('TagManager_ErrorArrayMissingValue', array('actual', $titlePlural, $index)));
            }
            if (empty($condition['comparison'])) {
                throw new Exception(Piwik::translate('TagManager_ErrorArrayMissingValue', array('comparison', $titlePlural, $index)));
            }
            if (!isset($condition['expected']) || !is_scalar($condition['expected']) || 0 === strlen($condition['expected'])) {
                throw new Exception(Piwik::translate('TagManager_ErrorArrayMissingValue', array('expected', $titlePlural, $index)));
            }
            if (!$this->variablesProvider->getPreConfiguredVariable($condition['actual']) && !$this->variable->findVariableByName($this->idSite, $this->idContainerVersion, $condition['actual'])) {
                throw new Exception(Piwik::translate('TagManager_ErrorVariableInConditionAtPositionNotFound', array($condition['actual'], $index)));
            }
            try {
                $this->comparisons->checkIsValidComparison($condition['comparison']);
            } catch (\Exception $e) {
                throw new Exception($e->getMessage(), $e->getCode(), $e);
            }
        }
    }
}

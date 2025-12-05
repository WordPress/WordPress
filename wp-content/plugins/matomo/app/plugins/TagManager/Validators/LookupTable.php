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
class LookupTable extends BaseValidator
{
    /**
     * @var \Piwik\Plugins\TagManager\Model\Comparison
     */
    private $comparisons;
    public function __construct()
    {
        $this->comparisons = StaticContainer::get('Piwik\\Plugins\\TagManager\\Model\\Comparison');
    }
    public function validate($value)
    {
        $titlePlural = Piwik::translate('TagManager_LookupTable');
        if (!is_array($value)) {
            throw new Exception(Piwik::translate('TagManager_ErrorNotAnArray', $titlePlural));
        }
        foreach ($value as $index => $variable) {
            if (!is_array($variable)) {
                $titleSingular = Piwik::translate('TagManager_Entry');
                throw new Exception(Piwik::translate('TagManager_ErrorInnerIsNotAnArray', array($titleSingular, $titlePlural)));
            }
            if (!isset($variable['match_value']) || $variable['match_value'] === \false || $variable['match_value'] === null) {
                throw new Exception(Piwik::translate('TagManager_ErrorArrayMissingValue', array('match_value', $titlePlural, $index)));
            }
            if (empty($variable['comparison'])) {
                throw new Exception(Piwik::translate('TagManager_ErrorArrayMissingValue', array('comparison', $titlePlural, $index)));
            }
            if (empty($variable['out_value'])) {
                throw new Exception(Piwik::translate('TagManager_ErrorArrayMissingValue', array('out_value', $titlePlural, $index)));
            }
            try {
                $this->comparisons->checkIsValidComparison($variable['comparison']);
            } catch (\Exception $e) {
                throw new Exception($e->getMessage(), $e->getCode(), $e);
            }
        }
    }
}

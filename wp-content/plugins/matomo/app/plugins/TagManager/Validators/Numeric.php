<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager\Validators;

use Piwik\Piwik;
use Piwik\Validators\BaseValidator;
use Piwik\Validators\Exception;
class Numeric extends BaseValidator
{
    /**
     * @var bool
     */
    private $isOptional;
    /**
     * @var bool
     */
    private $isVariableAllowed;
    /**
     * @param bool $isOptional Indicates whether the field is optional or not. The default is false.
     * @param bool $isVariableAllowed Indicates whether variables are allowed. The default is false.
     */
    public function __construct($isOptional = \false, $isVariableAllowed = \false)
    {
        $this->isOptional = $isOptional;
        $this->isVariableAllowed = $isVariableAllowed;
    }
    public function validate($value)
    {
        if ($this->isOptional && empty($value)) {
            return;
        }
        if (is_numeric($value)) {
            return;
            // valid
        }
        // Since it's not numeric and variables aren't allowed, return the general not a number error.
        if (!$this->isVariableAllowed) {
            throw new Exception(Piwik::translate('General_ValidatorErrorNotANumber'));
        }
        // Since it's not numeric and variables are allowed, check if the value references a variable.
        $posBracket = strpos($value, '{{');
        if ($posBracket === \false || strpos($value, '}}', $posBracket) === \false) {
            throw new Exception(Piwik::translate('TagManager_ValidatorErrorNotNumericOrVariable'));
        }
    }
}

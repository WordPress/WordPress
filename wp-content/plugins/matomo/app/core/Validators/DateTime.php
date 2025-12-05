<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Validators;

use Piwik\Date;
use Piwik\Piwik;
class DateTime extends \Piwik\Validators\BaseValidator
{
    public function validate($value)
    {
        if ($this->isValueBare($value)) {
            return;
        }
        if (!preg_match('/^(\\d{4})-(\\d{2})-(\\d{2})[ T](\\d{2}):(\\d{2}):(\\d{2})Z?$/', $value)) {
            throw new \Piwik\Validators\Exception(Piwik::translate('General_ValidatorErrorInvalidDateTimeFormat', array($value, 'YYYY-MM-DD HH:MM:SS')));
        }
        try {
            Date::factory($value);
        } catch (\Exception $e) {
            throw new \Piwik\Validators\Exception($e->getMessage(), $e->getCode(), $e);
        }
    }
}

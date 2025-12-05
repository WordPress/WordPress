<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager\Input;

use Piwik\Piwik;
use Piwik\Validators\BaseValidator;
use Piwik\Validators\CharacterLength;
use Piwik\Validators\NotEmpty;
class Name
{
    public const MAX_LENGTH = 255;
    /**
     * @var string
     */
    private $name;
    public function __construct($name)
    {
        $this->name = $name;
    }
    public function check()
    {
        $title = Piwik::translate('General_Name');
        BaseValidator::check($title, $this->name, [new NotEmpty(), new CharacterLength(1, self::MAX_LENGTH)]);
    }
}

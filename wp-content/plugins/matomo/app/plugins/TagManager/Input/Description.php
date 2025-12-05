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
class Description
{
    public const MAX_LENGTH = 1000;
    /**
     * @var string
     */
    private $description;
    public function __construct($description)
    {
        $this->description = $description;
    }
    public function check()
    {
        $title = Piwik::translate('General_Description');
        BaseValidator::check($title, $this->description, [new CharacterLength(0, self::MAX_LENGTH)]);
    }
}

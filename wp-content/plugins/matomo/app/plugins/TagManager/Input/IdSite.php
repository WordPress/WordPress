<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager\Input;

use Piwik\Validators\BaseValidator;
use Piwik\Validators\NotEmpty;
class IdSite
{
    /**
     * @var string|int
     */
    private $idSite;
    public function __construct($idSite)
    {
        $this->idSite = $idSite;
    }
    public function check()
    {
        BaseValidator::check('idSite', $this->idSite, [new NotEmpty(), new \Piwik\Validators\IdSite()]);
    }
}

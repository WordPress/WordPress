<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Access;

abstract class Role
{
    public abstract function getName() : string;
    public abstract function getId() : string;
    public abstract function getDescription() : string;
    public function getHelpUrl() : string
    {
        return '';
    }
}

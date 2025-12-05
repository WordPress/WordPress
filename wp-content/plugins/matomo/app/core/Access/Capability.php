<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Access;

abstract class Capability
{
    public abstract function getId() : string;
    public abstract function getName() : string;
    public abstract function getCategory() : string;
    public abstract function getDescription() : string;
    public abstract function getIncludedInRoles() : array;
    public function getHelpUrl() : string
    {
        return '';
    }
    public function hasRoleCapability(string $idRole) : bool
    {
        return \in_array($idRole, $this->getIncludedInRoles(), \true);
    }
}

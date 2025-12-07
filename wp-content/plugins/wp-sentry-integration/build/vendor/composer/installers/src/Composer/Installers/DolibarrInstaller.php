<?php

namespace WPSentry\ScopedVendor\Composer\Installers;

/**
 * Class DolibarrInstaller
 *
 * @package Composer\Installers
 * @author  Raphaël Doursenaud <rdoursenaud@gpcsolutions.fr>
 */
class DolibarrInstaller extends \WPSentry\ScopedVendor\Composer\Installers\BaseInstaller
{
    //TODO: Add support for scripts and themes
    /** @var array<string, string> */
    protected $locations = array('module' => 'htdocs/custom/{$name}/');
}

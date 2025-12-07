<?php

namespace WPSentry\ScopedVendor\Composer\Installers;

class FuelphpInstaller extends \WPSentry\ScopedVendor\Composer\Installers\BaseInstaller
{
    /** @var array<string, string> */
    protected $locations = array('component' => 'components/{$name}/');
}

<?php

namespace WPSentry\ScopedVendor\Composer\Installers;

class BonefishInstaller extends \WPSentry\ScopedVendor\Composer\Installers\BaseInstaller
{
    /** @var array<string, string> */
    protected $locations = array('package' => 'Packages/{$vendor}/{$name}/');
}

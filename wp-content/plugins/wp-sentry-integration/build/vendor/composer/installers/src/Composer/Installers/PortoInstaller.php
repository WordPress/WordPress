<?php

namespace WPSentry\ScopedVendor\Composer\Installers;

class PortoInstaller extends \WPSentry\ScopedVendor\Composer\Installers\BaseInstaller
{
    /** @var array<string, string> */
    protected $locations = array('container' => 'app/Containers/{$name}/');
}

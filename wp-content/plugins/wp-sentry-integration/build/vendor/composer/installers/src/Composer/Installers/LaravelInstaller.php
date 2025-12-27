<?php

namespace WPSentry\ScopedVendor\Composer\Installers;

class LaravelInstaller extends \WPSentry\ScopedVendor\Composer\Installers\BaseInstaller
{
    /** @var array<string, string> */
    protected $locations = array('library' => 'libraries/{$name}/');
}

<?php

namespace WPSentry\ScopedVendor\Composer\Installers;

class ElggInstaller extends \WPSentry\ScopedVendor\Composer\Installers\BaseInstaller
{
    /** @var array<string, string> */
    protected $locations = array('plugin' => 'mod/{$name}/');
}

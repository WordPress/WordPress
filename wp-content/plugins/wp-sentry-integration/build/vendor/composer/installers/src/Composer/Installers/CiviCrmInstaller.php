<?php

namespace WPSentry\ScopedVendor\Composer\Installers;

class CiviCrmInstaller extends \WPSentry\ScopedVendor\Composer\Installers\BaseInstaller
{
    /** @var array<string, string> */
    protected $locations = array('ext' => 'ext/{$name}/');
}

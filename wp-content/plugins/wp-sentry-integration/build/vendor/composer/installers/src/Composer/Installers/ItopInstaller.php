<?php

namespace WPSentry\ScopedVendor\Composer\Installers;

class ItopInstaller extends \WPSentry\ScopedVendor\Composer\Installers\BaseInstaller
{
    /** @var array<string, string> */
    protected $locations = array('extension' => 'extensions/{$name}/');
}

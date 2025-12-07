<?php

namespace WPSentry\ScopedVendor\Composer\Installers;

class SyliusInstaller extends \WPSentry\ScopedVendor\Composer\Installers\BaseInstaller
{
    /** @var array<string, string> */
    protected $locations = array('theme' => 'themes/{$name}/');
}

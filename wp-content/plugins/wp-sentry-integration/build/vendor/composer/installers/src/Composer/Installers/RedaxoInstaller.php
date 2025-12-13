<?php

namespace WPSentry\ScopedVendor\Composer\Installers;

class RedaxoInstaller extends \WPSentry\ScopedVendor\Composer\Installers\BaseInstaller
{
    /** @var array<string, string> */
    protected $locations = array('addon' => 'redaxo/include/addons/{$name}/', 'bestyle-plugin' => 'redaxo/include/addons/be_style/plugins/{$name}/');
}

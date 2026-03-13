<?php

namespace WPSentry\ScopedVendor\Composer\Installers;

class WolfCMSInstaller extends \WPSentry\ScopedVendor\Composer\Installers\BaseInstaller
{
    /** @var array<string, string> */
    protected $locations = array('plugin' => 'wolf/plugins/{$name}/');
}
